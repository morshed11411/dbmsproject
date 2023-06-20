<?php
$conn = oci_connect('UMS', '12345', 'localhost/XE');

if (!$conn) {
    $e = oci_error();
    echo "Failed to connect to Oracle: " . $e['message'];
} else {
    if (isset($_GET['cadreid'])) {
        $cadreID = $_GET['cadreid'];

        $queryDelete = "DELETE FROM ADVANCETRAINING WHERE CADREID = :cadreID";
        $stmtDelete = oci_parse($conn, $queryDelete);
        oci_bind_by_name($stmtDelete, ':cadreID', $cadreID);

        $result = oci_execute($stmtDelete);
        if ($result) {
            echo "Cadre deleted successfully.";
            header("Location: advanced_training.php");
        } else {
            $e = oci_error($stmtDelete);
            echo "Failed to delete cadre: " . $e['message'];
        }

        oci_free_statement($stmtDelete);
    }

    oci_close($conn);
}
?>
