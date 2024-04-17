<?php

return [
    'TELEGRAM_BOTS' => [
        'ADMIN' => [
            'token' => '6278639566:AAFOIuW6Gjd53XTJJSQUoWu83j9bQ540Th8',
            'chatId' => '-1002113271006'
        ],
    ],
    'USERS' => [
        'cgi-admin1' => [
            'password' => 'password1',
            'location' => 'cgi-admin1/dashboard.php'
        ],
        'cgi-admin2' => [
            'password' => 'password2',
            'location' => 'cgi-admin2/dashboard.php'
        ],
        'cgi-admin3' => [
            'password' => 'password3',
            'location' => 'cgi-admin3/dashboard.php'
        ],
        'admin' => [
            'password' => 'admin',
            'location' => 'dashboard.php'
        ]
    ],
    'MAILER' => [
        'host' => 'your_smtp_host',
        'port' => 587,
        'username' => 'your_smtp_username',
        'password' => 'your_smtp_password',
        'encryption' => 'tls',
        'fromEmail' => 'your_email@example.com',
        'fromName' => 'Your Name'
    ],
    'LOG_FILES' => [
        'errorLog' => '../logs/error.log',
        'infoLog' => '../logs/info.log',
        'debugLog' => '../logs/debug.log'
    ],
];
