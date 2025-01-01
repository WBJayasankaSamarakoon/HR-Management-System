<?php
session_start();

// Set the session lifetime to 1 hour (3600 seconds)
$timeout = 604800; // Session timeout in seconds
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
    session_unset();     // Unset $_SESSION variables
    session_destroy();   // Destroy the session
    header("Location: login.php"); // Redirect to login
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity timestamp

// Security function for input sanitization
function clean_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Redirect if user is not logged in
function check_login()
{
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
}

// Logout function
function logout()
{
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// CSRF Token Generation
function generate_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF Token Validation
function validate_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Password hashing function
function hash_password($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}

// Password verification function
function verify_password($input_password, $hashed_password)
{
    return password_verify($input_password, $hashed_password);
}
?>
