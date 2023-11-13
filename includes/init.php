<?php
// Set up the include path to include the current directory and the "includes" folder
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . DIRECTORY_SEPARATOR);
echo "Include path: " . get_include_path();

error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Define your database connection parameters
$db_user = 'umsv2';
$db_password = '12345';
$db_host = 'localhost/XEPDB1';

// Establish the Oracle database connection
$conn = oci_connect($db_user, $db_password, $db_host);
if (!$conn) {
    $e = oci_error();
    echo "Failed to connect to Oracle: " . $e['message'];
    exit;
}

// You can add more common initialization tasks here if needed.
?>
