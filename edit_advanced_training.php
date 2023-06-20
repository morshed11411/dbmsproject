<?php
$conn = oci_connect('UMS', '12345', 'localhost/XE');

if (!$conn) {
    $e = oci_error();
    echo "Failed to connect to Oracle: " . $e['message'];
} else {
    if (isset($_GET['cadreid']) && isset($_GET['soldierid'])) {
        $cadreID = $_GET['cadreid'];
        $soldierID = $_GET['soldierid'];

        if (isset($_POST['submit'])) {
            $newCadreID = $_POST['new_cadreid'];
            $newSoldierID = $_POST['new_soldierid'];
            $newCadreName = $_POST['new_cadrename'];
            $newRemark = $_POST['new_remark'];

            $queryUpdate = "UPDATE SOLDIERADVANCEDTRAINING SET CADREID = :newCadreID, SOLDIERID = :newSoldierID, CADRENAME = :newCadreName, REMARK = :newRemark WHERE CADREID = :cadreID AND SOLDIERID = :soldierID";
            $stmtUpdate = oci_parse($conn, $queryUpdate);
            oci_bind_by_name($stmtUpdate, ':newCadreID', $newCadreID);
            oci_bind_by_name($stmtUpdate, ':newSoldierID', $newSoldierID);
            oci_bind_by_name($stmtUpdate, ':newCadreName', $newCadreName);
            oci_bind_by_name($stmtUpdate, ':newRemark', $newRemark);
            oci_bind_by_name($stmtUpdate, ':cadreID', $cadreID);
            oci_bind_by_name($stmtUpdate, ':soldierID', $soldierID);

            $result = oci_execute($stmtUpdate);
            if ($result) {
                echo "Record updated successfully.";
                $cadreID = $newCadreID; // Update the displayed Cadre ID if it was changed
                $soldierID = $newSoldierID; // Update the displayed Soldier ID if it was changed
                header("Location: soldier_advanced_training.php");
            } else {
                $e = oci_error($stmtUpdate);
                echo "Failed to update record: " . $e['message'];
            }

            oci_free_statement($stmtUpdate);
        }

        // Retrieve the current record for display
        $querySelect = "SELECT * FROM SOLDIERADVANCEDTRAINING WHERE CADREID = :cadreID AND SOLDIERID = :soldierID";
        $stmtSelect = oci_parse($conn, $querySelect);
        oci_bind_by_name($stmtSelect, ':cadreID', $cadreID);
        oci_bind_by_name($stmtSelect, ':soldierID', $soldierID);
        oci_execute($stmtSelect);
        $row = oci_fetch_assoc($stmtSelect);
        oci_free_statement($stmtSelect);
    }

    oci_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Edit Advanced Training</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <form method="post" action="">
                                        <div class="form-group">
                                            <label for="new_cadreid">Cadre ID:</label>
                                            <input type="text" name="new_cadreid" id="new_cadreid" class="form-control" value="<?php echo $row['CADREID']; ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="new_soldierid">Soldier ID:</label>
                                            <input type="text" name="new_soldierid" id="new_soldierid" class="form-control" value="<?php echo $row['SOLDIERID']; ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="new_cadrename">Cadre Name:</label>
                                            <input type="text" name="new_cadrename" id="new_cadrename" class="form-control" value="<?php echo $row['CADRENAME']; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="new_remark">Remark:</label>
                                            <textarea name="new_remark" id="new_remark" class="form-control"><?php echo $row['REMARK']; ?></textarea>
                                        </div>

                                        <button type="submit" name="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include 'views/footer.php'; ?>
    </div>
</body>

</html>
