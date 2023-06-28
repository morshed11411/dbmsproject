<?php
// update_company.php

if (isset($_POST['submit'])) {
    $company_id = $_POST['company_id'];
    $company_name = $_POST['company_name'];

    // Perform the update operation
    include 'conn.php'; // Include the conn.php file for database connection

    $query = "UPDATE Company SET COMPANYNAME = :company_name WHERE COMPANYID = :company_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':company_name', $company_name);
    oci_bind_by_name($stmt, ':company_id', $company_id);

    $result = oci_execute($stmt);
    if ($result) {
        oci_commit($conn); // Commit the transaction
        oci_free_statement($stmt);
        oci_close($conn);
        header("Location: add_company.php"); // Redirect back to company.php
        exit;
    } else {
        $e = oci_error($stmt);
        echo "Failed to update company: " . $e['message'];
    }

    oci_free_statement($stmt);
    oci_rollback($conn); // Rollback the transaction
    oci_close($conn);
} else {
    echo "Invalid request. Please submit the form.";
    exit;
}
?>
