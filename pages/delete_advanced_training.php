<?php
$conn = oci_connect('UMS', '12345', 'localhost/XE');

if (!$conn) {
    $e = oci_error();
    echo "Failed to connect to Oracle: " . $e['message'];
} else {
    if (isset($_GET['cadreid']) && isset($_GET['soldierid'])) {
        $cadreID = $_GET['cadreid'];
        $soldierID = $_GET['soldierid'];

        $queryDelete = "DELETE FROM SOLDIERADVANCEDTRAINING WHERE CADREID = :cadreID AND SOLDIERID = :soldierID";
        $stmtDelete = oci_parse($conn, $queryDelete);
        oci_bind_by_name($stmtDelete, ':cadreID', $cadreID);
        oci_bind_by_name($stmtDelete, ':soldierID', $soldierID);

        $result = oci_execute($stmtDelete);
        if ($result) {
            echo "Record deleted successfully.";
            header("Location: soldier_advanced_training.php");
        } else {
            $e = oci_error($stmtDelete);
            echo "Failed to delete record: " . $e['message'];
        }

        oci_free_statement($stmtDelete);
    }

    oci_close($conn);
}
?>
