<?php
/**
 * Logout - Clears the session and logs out the user
 */

session_start();

// Clear all session data
session_destroy();

// Redirect to home page
header('Location: index.php');
exit;

