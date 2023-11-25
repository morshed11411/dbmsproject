<?php
function getManpowerByCompany($conn) {
    $manpowerByCompany = array();

    // Query to retrieve manpower and company information
    $queryAuthorization = "SELECT MANPOWER, COMPANYID FROM AUTHORIZATION";
    $stmtAuthorization = oci_parse($conn, $queryAuthorization);
    oci_execute($stmtAuthorization);

    // Fetch the results
    while ($row = oci_fetch_assoc($stmtAuthorization)) {
        $companyId = $row['COMPANYID'];
        $manpower = $row['MANPOWER'];

        // Store the manpower information in the array
        $manpowerByCompany[$companyId] = $manpower;
    }

    // Close the statement
    oci_free_statement($stmtAuthorization);

    return $manpowerByCompany;
}

// Example usage
$manpowerData = getManpowerByCompany($conn);

// Print or use the data as needed
print_r($manpowerData);
?>
