<?php
// Include the config.php file
include_once 'config.php';

// Function to get the current value of a configuration field
function getConfigValue($fieldName) {
    global ${$fieldName}; // Get the variable with the same name as the field
    return ${$fieldName}; // Return its current value
}

// Function to update the config.php file with the modified variables
function updateConfigFile($configData) {
    $configContent = '<?php' . PHP_EOL;
    foreach ($configData as $key => $value) {
        $configContent .= '$' . $key . ' = ' . var_export($value, true) . ';' . PHP_EOL;
    }
    $configContent .= '?>';
    file_put_contents('config.php', $configContent);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and update config variables
    $configData = array(
        'adminUsername' => $_POST['adminUsername'],
        'adminPassword' => $_POST['adminPassword'],
        'telegramBots' => array(
            'bot1' => array(
                'token' => $_POST['botToken1'],
                'chatId' => $_POST['chatId1']
            ),
            'bot2' => array(
                'token' => $_POST['botToken2'],
                'chatId' => $_POST['chatId2']
            )
        ),
        'mailerConfig' => array(
            'host' => $_POST['mailerHost'],
            'port' => $_POST['mailerPort'],
            'username' => $_POST['mailerUsername'],
            'password' => $_POST['mailerPassword'],
            'encryption' => $_POST['mailerEncryption'],
            'fromEmail' => $_POST['mailerFromEmail'],
            'fromName' => $_POST['mailerFromName']
        ),
        'logFileLocations' => array(
            'errorLog' => $_POST['errorLog'],
            'infoLog' => $_POST['infoLog'],
            'debugLog' => $_POST['debugLog']
        )
    );

    // Update config file
    updateConfigFile($configData);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Configuration</title>
    <style>
        /* Add your CSS styles here */
    </style>
</head>
<body>
    <h2>Edit Configuration</h2>
    <form method="post">
        <label for="adminUsername">Admin Username:</label>
        <input type="text" id="adminUsername" name="adminUsername" value="<?php echo getConfigValue('adminUsername'); ?>"><br><br>

        <label for="adminPassword">Admin Password:</label>
        <input type="password" id="adminPassword" name="adminPassword" value="<?php echo getConfigValue('adminPassword'); ?>"><br><br>

        <!-- Telegram Bot Configurations -->
        <label for="botToken1">Bot 1 Token:</label>
        <input type="text" id="botToken1" name="botToken1" value="<?php echo getConfigValue('telegramBots')['bot1']['token']; ?>"><br><br>
        <label for="chatId1">Bot 1 Chat ID:</label>
        <input type="text" id="chatId1" name="chatId1" value="<?php echo getConfigValue('telegramBots')['bot1']['chatId']; ?>"><br><br>
        
        <label for="botToken2">Bot 2 Token:</label>
        <input type="text" id="botToken2" name="botToken2" value="<?php echo getConfigValue('telegramBots')['bot2']['token']; ?>"><br><br>
        <label for="chatId2">Bot 2 Chat ID:</label>
        <input type="text" id="chatId2" name="chatId2" value="<?php echo getConfigValue('telegramBots')['bot2']['chatId']; ?>"><br><br>
        
        <!-- Mailer Configuration -->
        <label for="mailerHost">Mailer Host:</label>
        <input type="text" id="mailerHost" name="mailerHost" value="<?php echo getConfigValue('mailerConfig')['host']; ?>"><br><br>
        <label for="mailerPort">Mailer Port:</label>
        <input type="text" id="mailerPort" name="mailerPort" value="<?php echo getConfigValue('mailerConfig')['port']; ?>"><br><br>
        <!-- Add more fields for mailer configuration as needed -->

        <!-- Log File Locations -->
        <label for="errorLog">Error Log File:</label>
        <input type="text" id="errorLog" name="errorLog" value="<?php echo getConfigValue('logFileLocations')['errorLog']; ?>"><br><br>
        <!-- Add more fields for log file locations as needed -->

        <input type="submit" value="Save Changes">
    </form>
</body>
</html>
