<!DOCTYPE html>
<?php
include 'conn.php';
include 'views/auth.php';
?>
<html lang="en">
<?php include 'views/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Soldier Options</h1>
                    
                </div>
            </div>


            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <form method="post" action="">
                                        <div class="form-group">
                                            <label for="soldier_id">Soldier ID:</label>
                                            <input type="text" name="soldier_id" id="soldier_id" class="form-control"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="temporary_command">Temporary Command:</label>
                                            <select name="temporary_command" id="temporary_command" class="form-control"
                                                required>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="ere">ERE:</label>
                                            <select name="ere" id="ere" class="form-control" required>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="serving_status">Serving Status:</label>
                                            <select name="serving_status" id="serving_status" class="form-control"
                                                required>
                                                <option value="Serving">Serving</option>
                                                <option value="AWOL">AWOL</option>
                                                <option value="Retired">Retired</option>
                                            </select>
                                        </div>
                                        <input type="submit" value="Update" class="btn btn-primary">
                                    </form>
                                    <br>
                                    <?php
                    // Check if the form is submitted
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        // Get the form values
                        $soldierId = $_POST['soldier_id'];
                        $temporaryCommand = $_POST['temporary_command'];
                        $ere = $_POST['ere'];
                        $servingStatus = $_POST['serving_status'];

                        // Update the soldier options in the database
                        $query = "UPDATE Soldier SET TemporaryCommand = :temporary_command, ERE = :ere, ServingStatus = :serving_status WHERE SoldierID = :soldier_id";
                        $stmt = oci_parse($conn, $query);
                        oci_bind_by_name($stmt, ':temporary_command', $temporaryCommand);
                        oci_bind_by_name($stmt, ':ere', $ere);
                        oci_bind_by_name($stmt, ':serving_status', $servingStatus);
                        oci_bind_by_name($stmt, ':soldier_id', $soldierId);
                        $result = oci_execute($stmt);

                        if ($result) {
                            echo '<div class="alert alert-success" role="alert">
                                    Soldier data inserted successfully.
                                </div>';
                            } else {
                                $e = oci_error($stmt);
                                echo '<div class="alert alert-danger" role="alert">
                                    Failed to insert soldier data: ' . $e['message'] . '
                                </div>';

                            oci_free_statement($stmt);
                        }
                    }

                    oci_close($conn);
                    ?>
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