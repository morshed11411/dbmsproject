<?php
session_start();

include 'conn.php'; // Include the conn.php file for database connection

if (isset($_GET['leave_id'])) {
    $leaveID = $_GET['leave_id'];

    // Fetch leave information based on leave ID
    $query = "SELECT * FROM LeaveModule WHERE LeaveID = :leave_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':leave_id', $leaveID);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);

    if ($row) {
        $soldierID = $row['SOLDIERID'];
        $leaveType = $row['LEAVETYPE'];
        $leaveEndDate = $row['LEAVEENDDATE'];

        // Update Leave Information
        if (isset($_POST['update'])) {
            $newLeaveType = $_POST['leave_type'];
            $newLeaveEndDate = $_POST['leave_end_date'];

            $updateQuery = "UPDATE LeaveModule SET LeaveType = :leave_type, LeaveEndDate = TO_DATE(:leave_end_date, 'YYYY-MM-DD')
                WHERE LeaveID = :leave_id";

            $updateStmt = oci_parse($conn, $updateQuery);

            oci_bind_by_name($updateStmt, ':leave_type', $newLeaveType);
            oci_bind_by_name($updateStmt, ':leave_end_date', $newLeaveEndDate);
            oci_bind_by_name($updateStmt, ':leave_id', $leaveID);

            $updateResult = oci_execute($updateStmt);
            if ($updateResult) {
                $_SESSION['success'] = "Leave information updated successfully.";
                oci_commit($conn); // Commit the transaction
                oci_close($conn); // Close the connection before redirecting
                header("Location: approve_leave.php"); // Redirect to the approve_leave.php page after update
                exit;
            } else {
                $error = oci_error($updateStmt);
                $_SESSION['error'] = "Failed to update leave information: " . $error['message'];
                oci_rollback($conn); // Roll back the transaction
                oci_close($conn); // Close the connection before redirecting
                header("Location: approve_leave.php"); // Redirect to the approve_leave.php page after update
                exit;
            }

            oci_free_statement($updateStmt);
        }
    } else {
        $_SESSION['error'] = "Leave information not found.";
        oci_close($conn); // Close the connection before redirecting
        header("Location: approve_leave.php"); // Redirect to the approve_leave.php page if leave information not found
        exit;
    }

    oci_free_statement($stmt);
} else {
    $_SESSION['error'] = "Leave ID not provided.";
    oci_close($conn); // Close the connection before redirecting
    header("Location: approve_leave.php"); // Redirect to the approve_leave.php page if leave ID is not provided
    exit;
}
?>

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
                    <h1>Edit Leave Information</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="soldier_id">Soldier ID:</label>
                                            <input type="text" name="soldier_id" id="soldier_id" class="form-control"
                                                value="<?php echo $soldierID; ?>" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="leave_type">Leave Type:</label>
                                            <select name="leave_type" id="leave_type" class="form-control" required>
                                                <option value="Weekend" <?php if ($leaveType == 'Weekend')
                                                    echo 'selected'; ?>>Weekend</option>
                                                <option value="C Leave" <?php if ($leaveType == 'C Leave')
                                                    echo 'selected'; ?>>C Leave</option>
                                                <option value="P Leave" <?php if ($leaveType == 'P Leave')
                                                    echo 'selected'; ?>>P Leave</option>
                                                <option value="R Leave" <?php if ($leaveType == 'R Leave')
                                                    echo 'selected'; ?>>R Leave</option>
                                                <option value="Sick Leave" <?php if ($leaveType == 'Sick Leave')
                                                    echo 'selected'; ?>>Sick Leave</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="leave_end_date">Leave End Date:</label>
                                            <input type="date" name="leave_end_date" id="leave_end_date"
                                                class="form-control" value="<?php echo $leaveEndDate; ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <input type="submit" name="update" value="Update" class="btn btn-primary">
                                <a href="index.php" class="btn btn-default">Cancel</a>
                            </form>

                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include 'views/footer.php'; ?>

    </div>

</body>

</html>