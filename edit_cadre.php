<?php
$conn = oci_connect('UMS', '12345', 'localhost/XE');

if (!$conn) {
    $e = oci_error();
    echo "Failed to connect to Oracle: " . $e['message'];
} else {
    if (isset($_GET['cadreid'])) {
        $cadreID = $_GET['cadreid'];

        // Fetch the existing cadre details
        $querySelect = "SELECT * FROM ADVANCETRAINING WHERE CADREID = :cadreID";
        $stmtSelect = oci_parse($conn, $querySelect);
        oci_bind_by_name($stmtSelect, ':cadreID', $cadreID);
        oci_execute($stmtSelect);

        $cadre = oci_fetch_assoc($stmtSelect);

        oci_free_statement($stmtSelect);
    }

    // Update the cadre details
    if (isset($_POST['submit'])) {
        $newCadreID = $_POST['cadre_id'];
        $newName = $_POST['name'];
        $newTrainingStartDate = $_POST['training_startdate'];
        $newTrainingEndDate = $_POST['training_enddate'];
        $newTrainingOIC = $_POST['training_oic'];
        $newInstructor = $_POST['instructor'];

        $queryUpdate = "UPDATE ADVANCETRAINING SET CADREID = :newCadreID, NAME = :newName, TRAININGSTARTDATE = TO_DATE(:newTrainingStartDate, 'YYYY-MM-DD'), TRAININGENDDATE = TO_DATE(:newTrainingEndDate, 'YYYY-MM-DD'), TRAININGOIC = :newTrainingOIC, INSTRUCTOR = :newInstructor WHERE CADREID = :cadreID";
        $stmtUpdate = oci_parse($conn, $queryUpdate);
        oci_bind_by_name($stmtUpdate, ':newCadreID', $newCadreID);
        oci_bind_by_name($stmtUpdate, ':newName', $newName);
        oci_bind_by_name($stmtUpdate, ':newTrainingStartDate', $newTrainingStartDate);
        oci_bind_by_name($stmtUpdate, ':newTrainingEndDate', $newTrainingEndDate);
        oci_bind_by_name($stmtUpdate, ':newTrainingOIC', $newTrainingOIC);
        oci_bind_by_name($stmtUpdate, ':newInstructor', $newInstructor);
        oci_bind_by_name($stmtUpdate, ':cadreID', $cadreID);

        $result = oci_execute($stmtUpdate);
        if ($result) {
            echo "Cadre details updated successfully.";
            header("Location: advanced_training.php");
        } else {
            $e = oci_error($stmtUpdate);
            echo "Failed to update cadre details: " . $e['message'];
        }

        oci_free_statement($stmtUpdate);
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
                    <h1>Edit Cadre</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="post" action="">
                                        <div class="form-group">
                                            <label for="cadre_id">Cadre ID:</label>
                                            <input type="number" name="cadre_id" id="cadre_id" class="form-control" value="<?php echo $cadre['CADREID']; ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="name">Name:</label>
                                            <input type="text" name="name" id="name" class="form-control" value="<?php echo $cadre['NAME']; ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="training_startdate">Training Start Date:</label>
                                            <input type="date" name="training_startdate" id="training_startdate" class="form-control" value="<?php echo $cadre['TRAININGSTARTDATE']; ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="training_enddate">Training End Date:</label>
                                            <input type="date" name="training_enddate" id="training_enddate" class="form-control" value="<?php echo $cadre['TRAININGENDDATE']; ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="training_oic">Training OIC:</label>
                                            <input type="text" name="training_oic" id="training_oic" class="form-control" value="<?php echo $cadre['TRAININGOIC']; ?>">
                                        </div>

                                        <div class="form-group">
                                            <label for="instructor">Instructor:</label>
                                            <input type="text" name="instructor" id="instructor" class="form-control" value="<?php echo $cadre['INSTRUCTOR']; ?>">
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
