<?php
// delete_soldier.php

if (isset($_GET['soldierId'])) {
    $soldierId = $_GET['soldierId'];

    // Perform the delete operation
    $conn = oci_connect('UMS', '12345', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        echo "Failed to connect to Oracle: " . $e['message'];
        exit;
    }

    $query = "DELETE FROM Soldier WHERE SOLDIERID = :soldierId";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldierId', $soldierId);

    $result = oci_execute($stmt);
    if ($result) {
        echo "Soldier deleted successfully.";
        header("Location: dashboard.php"); // Redirect back to the soldiers.php page
    } else {
        $e = oci_error($stmt);
        echo "Failed to delete soldier: " . $e['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);
} else {
    echo "Invalid request. Soldier ID not provided.";
    exit;
}
?>
