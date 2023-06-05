<?php
// delete_company.php

if (isset($_GET['company_id'])) {
    $company_id = $_GET['company_id'];

    // Perform the delete operation
    include 'conn.php'; // Include the conn.php file for database connection

    $query = "DELETE FROM COMPANY WHERE COMPANYID = :company_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':company_id', $company_id);

    $result = oci_execute($stmt);
    if ($result) {
        echo "Company deleted successfully.";
        header("Location: company.php"); // Redirect back to company.php

    } else {
        $e = oci_error($stmt);
        echo "Failed to delete company: " . $e['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);
} else {
    echo "Invalid request. Company ID not provided.";
    exit;
}
?>
