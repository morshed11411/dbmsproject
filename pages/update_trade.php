<?php
// update_trade.php

if (isset($_POST['submit'])) {
    $trade_id = $_POST['trade_id'];
    $trade_name = $_POST['trade_name'];

    // Perform the update operation
    $conn = oci_connect('UMS', '12345', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        echo "Failed to connect to Oracle: " . $e['message'];
        exit;
    }

    $query = "UPDATE TRADE SET TRADE = :trade_name WHERE TRADEID = :trade_id ";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':trade_name', $trade_name);
    oci_bind_by_name($stmt, ':trade_id', $trade_id);

    $result = oci_execute($stmt);
    if ($result) {
        oci_commit($conn); // Commit the transaction
        oci_free_statement($stmt);
        oci_close($conn);
        header("Location: manage_trade.php"); // Redirect back to trade.php
        exit;
    } else {
        $e = oci_error($stmt);
        echo "Failed to update trade: " . $e['message'];
    }

    oci_free_statement($stmt);
    oci_rollback($conn); // Rollback the transaction
    oci_close($conn);
} else {
    echo "Invalid request. Please submit the form.";
    exit;
}
?>
