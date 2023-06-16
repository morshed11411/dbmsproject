<?php
// remove_access.php

include 'conn.php'; // Include the conn.php file for database connection

if (isset($_GET['soldier_id'])) {
    $soldier_id = $_GET['soldier_id'];

    // Perform the necessary database operations to remove user access
    // For example, you can execute an UPDATE query on the Soldier table to remove the access role

    $query = "UPDATE Soldier SET AccessRole = NULL WHERE SoldierID = :soldier_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
    oci_execute($stmt);

    oci_free_statement($stmt);
    oci_close($conn);

    echo "User access removed successfully.";
} else {
    echo "Invalid request. Please provide a valid soldier ID.";
}
?>
