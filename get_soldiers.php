<?php
// get_soldiers.php

include 'conn.php';

if (isset($_GET['company_id'])) {
    $selected_company_id = $_GET['company_id'];

    $query = "SELECT SOLDIERID, NAME FROM Soldier WHERE COMPANYID = :company_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':company_id', $selected_company_id);
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $selected = ($_POST['soldier_id'] == $row['SOLDIERID']) ? 'selected' : '';
        echo "<option value='" . $row['SOLDIERID'] . "' $selected>" . $row['NAME'] . "</option>";
    }

    oci_free_statement($stmt);
    oci_close($conn);
}
?>
