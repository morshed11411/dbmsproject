<?php

error_reporting(0);
ini_set('display_errors', 0);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$db_user = 'upcs';
$db_password = '12345';
$db_host = 'localhost/XEPDB1';



// Establish Oracle database connection
$conn = oci_connect($db_user, $db_password, $db_host);
if (!$conn) {
    $e = oci_error();
    echo "Failed to connect to Oracle: " . $e['message'];
    exit;
}


?>