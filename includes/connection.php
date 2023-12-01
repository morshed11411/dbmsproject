<?php
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);

if (isset($_SESSION['default_password']) && $_SESSION['default_password'] == true) {

    if ($current_page != 'change_password.php') {
        $_SESSION['error']="Please change your password first!!!";
        header('Location: change_password.php');
        exit(); // Ensure that the script stops after the redirect
    }

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