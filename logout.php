<?php

$_SESSION = [];
// Destroy the session and clear all session variables
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Optionally, clear the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to index.php
header("Location: index.php");
exit();
?>