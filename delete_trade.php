<?php
// delete_trade.php

if (isset($_GET['trade_id'])) {
    $trade_id = $_GET['trade_id'];

    // Perform the delete operation
    $conn = oci_connect('UMS', '12345', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        echo "Failed to connect to Oracle: " . $e['message'];
        exit;
    }

    $query = "DELETE FROM TRADE WHERE TRADEID = :trade_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':trade_id', $trade_id);

    $result = oci_execute($stmt);
    if ($result) {
        echo "Trade deleted successfully.";
        header("Location: trade.php"); // Redirect back to trade.php

    } else {
        $e = oci_error($stmt);
        echo "Failed to delete trade: " . $e['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);
} else {
    echo "Invalid request. Trade ID not provided.";
    exit;
}
?>
