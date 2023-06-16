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
                    <h1>Manage Leave Information</h1>
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
                                    
                                    // Add Leave Information
                                    if (isset($_POST['add'])) {
                                        $leaveID = $_POST['leave_id'];
                                        $soldierID = $_POST['soldier_id'];
                                        $leaveType = $_POST['leave_type'];
                                        $leaveStartDate = $_POST['leave_start_date'];
                                        $leaveEndDate = $_POST['leave_end_date'];

                                        $query = "INSERT INTO LeaveModule (LeaveID, SoldierID, LeaveType, LeaveStartDate, LeaveEndDate)
                                                  VALUES (:leave_id, :soldier_id, :leave_type, TO_DATE(:leave_start_date, 'YYYY-MM-DD'), TO_DATE(:leave_end_date, 'YYYY-MM-DD'))";
                                        $stmt = oci_parse($conn, $query);

                                        oci_bind_by_name($stmt, ':leave_id', $leaveID);
                                        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
                                        oci_bind_by_name($stmt, ':leave_type', $leaveType);
                                        oci_bind_by_name($stmt, ':leave_start_date', $leaveStartDate);
                                        oci_bind_by_name($stmt, ':leave_end_date', $leaveEndDate);

                                        $result = oci_execute($stmt);
                                        if ($result) {
                                            echo "Leave information added successfully.";
                                        } else {
                                            $e = oci_error($stmt);
                                            echo "Failed to add leave information: " . $e['message'];
                                        }

                                        oci_free_statement($stmt);
                                    }

                                    // Delete Leave Information
                                    if (isset($_GET['delete'])) {
                                        $leaveID = $_GET['delete'];

                                        $query = "DELETE FROM LeaveModule WHERE LeaveID = :leave_id";
                                        $stmt = oci_parse($conn, $query);

                                        oci_bind_by_name($stmt, ':leave_id', $leaveID);

                                        $result = oci_execute($stmt);
                                        if ($result) {
                                            echo "Leave information deleted successfully.";
                                        } else {
                                            $e = oci_error($stmt);
                                            echo "Failed to delete leave information: " . $e['message'];
                                        }

                                        oci_free_statement($stmt);
                                    }

                                    // Update Leave Information
                                    if (isset($_POST['update'])) {
                                        $leaveID = $_POST['leave_id'];
                                        $soldierID = $_POST['soldier_id'];
                                        $leaveType = $_POST['leave_type'];
                                        $leaveStartDate = $_POST['leave_start_date'];
                                        $leaveEndDate = $_POST['leave_end_date'];

                                        $query = "UPDATE LeaveModule 
                                                  SET SoldierID = :soldier_id, LeaveType = :leave_type, LeaveStartDate = TO_DATE(:leave_start_date, 'YYYY-MM-DD'), LeaveEndDate = TO_DATE(:leave_end_date, 'YYYY-MM-DD')
                                                  WHERE LeaveID = :leave_id";
                                        $stmt = oci_parse($conn, $query);

                                        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
                                        oci_bind_by_name($stmt, ':leave_type', $leaveType);
                                        oci_bind_by_name($stmt, ':leave_start_date', $leaveStartDate);
                                        oci_bind_by_name($stmt, ':leave_end_date', $leaveEndDate);
                                        oci_bind_by_name($stmt, ':leave_id', $leaveID);

                                        $result = oci_execute($stmt);
                                        if ($result) {
                                            echo "Leave information updated successfully.";
                                        } else {
                                            $e = oci_error($stmt);
                                            echo "Failed to update leave information: " . $e['message'];
                                        }

                                        oci_free_statement($stmt);
                                    }
                                    ?>

                                    <!-- Add/Update Leave Information Form -->
                                    <form method="post" action="">
                                        <div class="form-group">
                                            <label for="leave_id">Leave ID:</label>
                                            <input type="text" name="leave_id" id="leave_id" class="form-control"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label for="soldier_id">Soldier ID:</label>
                                            <input type="text" name="soldier_id" id="soldier_id" class="form-control"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label for="leave_type">Leave Type:</label>
                                            <select name="leave_type" id="leave_type" class="form-control" required>
                                                <option value="Weekend">Weekend</option>
                                                <option value="C Leave">C Leave</option>
                                                <option value="P Leave">P Leave</option>
                                                <option value="R Leave">R Leave</option>
                                                <option value="Sick Leave">Sick Leave</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="leave_start_date">Leave Start Date:</label>
                                            <input type="date" name="leave_start_date" id="leave_start_date"
                                                class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="leave_end_date">Leave End Date:</label>
                                            <input type="date" name="leave_end_date" id="leave_end_date"
                                                class="form-control" required>
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
                                                <th>Leave ID</th>
                                                <th>Soldier ID</th>
                                                <th>Leave Type</th>
                                                <th>Leave Start Date</th>
                                                <th>Leave End Date</th>
                                                <th>Duration</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT * FROM LeaveModule";
                                            $stmt = oci_parse($conn, $query);
                                            oci_execute($stmt);

                                            while ($row = oci_fetch_assoc($stmt)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['LEAVEID'] . "</td>";
                                                echo "<td>" . $row['SOLDIERID'] . "</td>";
                                                echo "<td>" . $row['LEAVETYPE'] . "</td>";
                                                echo "<td>" . $row['LEAVESTARTDATE'] . "</td>";
                                                echo "<td>" . $row['LEAVEENDDATE'] . "</td>";

                                                // Calculate duration
                                                $startDate = strtotime($row['LEAVESTARTDATE']);
                                                $endDate = strtotime($row['LEAVEENDDATE']);
                                                $duration = round(($endDate - $startDate) / (60 * 60 * 24)); // Duration in days
                                            
                                                echo "<td>" . $duration . " days</td>";
                                                echo "<td><a href='?delete=" . $row['LEAVEID'] . "' class='btn btn-danger btn-sm'>Delete</a></td>";
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