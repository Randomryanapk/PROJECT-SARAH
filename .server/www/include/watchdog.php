<?php
// Include the config.php file
$config = include('config.php');

// Function to send a message to Telegram
function sendTelegramMessage($message, $botToken, $chatId) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $data = array(
        'chat_id' => $chatId,
        'text' => $message
    );

    // Use cURL to send the message
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Check for errors
    if (!$response) {
        error_log("Failed to send message to Telegram: " . curl_error($ch));
    }
}

// Get Telegram API token and chat ID from config
$telegramConfig = $config['TELEGRAM_BOTS']['ADMIN'] ?? [];
$botToken = $telegramConfig['token'] ?? '';
$chatId = $telegramConfig['chatId'] ?? '';

// Check if Telegram API token or chat ID is missing or empty
if (empty($botToken) || empty($chatId)) {
    die("Telegram API token or chat ID is missing or empty in config.php");
}

// Function to recursively get all files in a directory
function getFilesInDirectory($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

// Get initial list of files
$initialFiles = getFilesInDirectory('/path/to/your/directory');

// Infinite loop to continuously monitor for changes
while (true) {
    sleep(10); // Wait for 10 seconds before checking again

    // Get current list of files
    $currentFiles = getFilesInDirectory('/path/to/your/directory');

    // Compare the current list with the initial list
    $changedFiles = array_diff($currentFiles, $initialFiles);

    // Check if any files have changed
    if (!empty($changedFiles)) {
        // Send a notification to Telegram
        $message = "Changes detected in the following files:\n";
        foreach ($changedFiles as $file) {
            $message .= basename($file) . "\n";
        }
        sendTelegramMessage($message, $botToken, $chatId);

        // Update the initial list of files
        $initialFiles = $currentFiles;
    }
}
?>
