<!DOCTYPE html>
<html lang="en">
<?php
include 'views/head.php';
include 'views/auth.php';
?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Manage Medical Information</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <?php
                                    include 'conn.php'; // Include the conn.php file for database connection

                                    // Add Medical Information
                                    if (isset($_POST['add'])) {
                                        $medicalID = $_POST['medical_id'];
                                        $soldierID = $_POST['soldier_id'];
                                        $disposalType = $_POST['disposal_type'];
                                        $startDate = $_POST['start_date'];
                                        $endDate = $_POST['end_date'];
                                        $reason = $_POST['reason'];

                                        $query = "INSERT INTO MedicalInfo (MedicalID, SoldierID, DisposalType, StartDate, EndDate, Reason)
                                                  VALUES (:medical_id, :soldier_id, :disposal_type, TO_DATE(:start_date, 'YYYY-MM-DD'), TO_DATE(:end_date, 'YYYY-MM-DD'), :reason)";
                                        $stmt = oci_parse($conn, $query);

                                        oci_bind_by_name($stmt, ':medical_id', $medicalID);
                                        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
                                        oci_bind_by_name($stmt, ':disposal_type', $disposalType);
                                        oci_bind_by_name($stmt, ':start_date', $startDate);
                                        oci_bind_by_name($stmt, ':end_date', $endDate);
                                        oci_bind_by_name($stmt, ':reason', $reason);

                                        $result = oci_execute($stmt);
                                        if ($result) {
                                            echo "Medical information added successfully.";
                                        } else {
                                            $e = oci_error($stmt);
                                            echo "Failed to add medical information: " . $e['message'];
                                        }

                                        oci_free_statement($stmt);
                                    }

                                    // Delete Medical Information
                                    if (isset($_GET['delete'])) {
                                        $medicalID = $_GET['delete'];

                                        $query = "DELETE FROM MedicalInfo WHERE MedicalID = :medical_id";
                                        $stmt = oci_parse($conn, $query);

                                        oci_bind_by_name($stmt, ':medical_id', $medicalID);

                                        $result = oci_execute($stmt);
                                        if ($result) {
                                            echo "Medical information deleted successfully.";
                                        } else {
                                            $e = oci_error($stmt);
                                            echo "Failed to delete medical information: " . $e['message'];
                                        }

                                        oci_free_statement($stmt);
                                    }

                                    // Update Medical Information
                                    if (isset($_POST['update'])) {
                                        $medicalID = $_POST['medical_id'];
                                        $soldierID = $_POST['soldier_id'];
                                        $disposalType = $_POST['disposal_type'];
                                        $startDate = $_POST['start_date'];
                                        $endDate = $_POST['end_date'];
                                        $reason = $_POST['reason'];

                                        $query = "UPDATE MedicalInfo 
                                                  SET SoldierID = :soldier_id, DisposalType = :disposal_type, StartDate = TO_DATE(:start_date, 'YYYY-MM-DD'), 
                                                      EndDate = TO_DATE(:end_date, 'YYYY-MM-DD'), Reason = :reason
                                                  WHERE MedicalID = :medical_id";
                                        $stmt = oci_parse($conn, $query);

                                        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
                                        oci_bind_by_name($stmt, ':disposal_type', $disposalType);
                                        oci_bind_by_name($stmt, ':start_date', $startDate);
                                        oci_bind_by_name($stmt, ':end_date', $endDate);
                                        oci_bind_by_name($stmt, ':reason', $reason);
                                        oci_bind_by_name($stmt, ':medical_id', $medicalID);

                                        $result = oci_execute($stmt);
                                        if ($result) {
                                            echo "Medical information updated successfully.";
                                        } else {
                                            $e = oci_error($stmt);
                                            echo "Failed to update medical information: " . $e['message'];
                                        }

                                        oci_free_statement($stmt);
                                    }
                                    ?>

                                    <!-- Add/Update Medical Information Form -->
                                    <form method="post" action="">
                                        <div class="form-group">
                                            <label for="medical_id">Medical ID:</label>
                                            <input type="text" name="medical_id" id="medical_id" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="soldier_id">Soldier ID:</label>
                                            <input type="text" name="soldier_id" id="soldier_id" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="disposal_type">Disposal Type:</label>
                                            <input type="text" name="disposal_type" id="disposal_type" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="start_date">Start Date:</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="end_date">End Date:</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="reason">Reason:</label>
                                            <textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>
                                        </div>

                                        <input type="submit" name="add" value="Add" class="btn btn-primary">
                                        <input type="submit" name="update" value="Update" class="btn btn-success">
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Medical ID</th>
                                                <th>Soldier ID</th>
                                                <th>Disposal Type</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Reason</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT * FROM MedicalInfo";
                                            $stmt = oci_parse($conn, $query);
                                            oci_execute($stmt);

                                            while ($row = oci_fetch_assoc($stmt)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['MEDICALID'] . "</td>";
                                                echo "<td>" . $row['SOLDIERID'] . "</td>";
                                                echo "<td>" . $row['DISPOSALTYPE'] . "</td>";
                                                echo "<td>" . $row['STARTDATE'] . "</td>";
                                                echo "<td>" . $row['ENDDATE'] . "</td>";
                                                echo "<td>" . $row['REASON'] . "</td>";
                                                echo "<td><a href='?delete=" . $row['MEDICALID'] . "' class='btn btn-danger btn-sm'>Delete</a></td>";
                                                echo "</tr>";
                                            }
                                            oci_free_statement($stmt);
                                            oci_close($conn);
                                            ?>
                                        </tbody>
                                    </table>
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
