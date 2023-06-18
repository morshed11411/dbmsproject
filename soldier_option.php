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
                                    <?php
                                    // Check if the soldier ID is provided
                                    if (isset($_GET['soldier_id'])) {
                                        // Fetch the soldier ID from the previous page
                                        $soldierId = $_GET['soldier_id'];

                                        // Fetch the existing values for the soldier
                                        $query = "SELECT TemporaryCommand, ERE, ServingStatus FROM Soldier WHERE SoldierID = :soldier_id";
                                        $stmt = oci_parse($conn, $query);
                                        oci_bind_by_name($stmt, ':soldier_id', $soldierId);
                                        oci_execute($stmt);

                                        // Fetch the row
                                        $row = oci_fetch_assoc($stmt);

                                        // Extract the values
                                        $temporaryCommand = $row['TEMPORARYCOMMAND'];
                                        $ere = $row['ERE'];
                                        $servingStatus = $row['SERVINGSTATUS'];

                                        // Free the statement
                                        oci_free_statement($stmt);
                                    }
                                    ?>

                                    <form method="post" action="">
                                        <div class="form-group">
                                            <label for="soldier_id">Soldier ID:</label>
                                            <input type="text" name="soldier_id" id="soldier_id" class="form-control"
                                                value="<?php echo $soldierId; ?>" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="temporary_command">Temporary Command:</label>
                                            <select name="temporary_command" id="temporary_command" class="form-control"
                                                required>
                                                <option value="Yes" <?php if ($temporaryCommand == 'Yes') echo 'selected'; ?>>
                                                    Yes</option>
                                                <option value="No" <?php if ($temporaryCommand == 'No') echo 'selected'; ?>>
                                                    No</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="ere">ERE:</label>
                                            <select name="ere" id="ere" class="form-control" required>
                                                <option value="Yes" <?php if ($ere == 'Yes') echo 'selected'; ?>>Yes
                                                </option>
                                                <option value="No" <?php if ($ere == 'No') echo 'selected'; ?>>No
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="serving_status">Serving Status:</label>
                                            <select name="serving_status" id="serving_status" class="form-control"
                                                required>
                                                <option value="Serving"
                                                    <?php if ($servingStatus == 'Serving') echo 'selected'; ?>>Serving
                                                </option>
                                                <option value="AWOL"
                                                    <?php if ($servingStatus == 'AWOL') echo 'selected'; ?>>AWOL
                                                </option>
                                                <option value="Retired"
                                                    <?php if ($servingStatus == 'Retired') echo 'selected'; ?>>Retired
                                                </option>
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
                                                    Soldier data updated successfully.
                                                </div>';
                                        } else {
                                            $e = oci_error($stmt);
                                            echo '<div class="alert alert-danger" role="alert">
                                                    Failed to update soldier data: ' . $e['message'] . '
                                                </div>';
                                        }

                                        oci_free_statement($stmt);
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
