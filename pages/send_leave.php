<?php
session_start();

include '../includes/connection.php';

// Fetch soldier ID and name from the query parameter
if (isset($_GET['soldier'])) {
    $soldierID = $_GET['soldier'];

    // Fetch soldier details from the database
    $query = "SELECT SOLDIERID, NAME, COMPANYNAME FROM SOLDIER JOIN COMPANY USING (COMPANYID) WHERE SOLDIERID = :soldier_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_execute($stmt);

    $soldier = oci_fetch_assoc($stmt);

    // Redirect if soldier not found
    if (!$soldier) {
        header("Location: soldiers.php");
        exit();
    }

    oci_free_statement($stmt);
} else {
    header("Location: soldiers.php");
    exit();
}

// Process the form submission to send a leave request
if (isset($_POST['send_leave_submit'])) {
    $leaveType = $_POST['leave_type'];
    $leaveStartDate = date('Y-m-d'); // System date
    $leaveEndDate = $_POST['leave_end_date'];

    // Insert leave request into the database
    $query = "INSERT INTO LEAVEMODULE (SOLDIERID, LEAVETYPE, LEAVESTARTDATE, LEAVEENDDATE) VALUES (:soldier_id, :leave_type, TO_DATE(:leave_start_date, 'YYYY-MM-DD'), TO_DATE(:leave_end_date, 'YYYY-MM-DD'))";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_bind_by_name($stmt, ':leave_type', $leaveType);
    oci_bind_by_name($stmt, ':leave_start_date', $leaveStartDate);
    oci_bind_by_name($stmt, ':leave_end_date', $leaveEndDate);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Leave request sent successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to send leave request: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: soldiers.php");
    exit();
}

// Fetch leave history of the soldier
$query = "SELECT * FROM LEAVEMODULE WHERE SOLDIERID = :soldier_id ORDER BY LEAVESTARTDATE DESC";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierID);
oci_execute($stmt);

$leaveHistory = array();
while ($row = oci_fetch_assoc($stmt)) {
    $leave = new stdClass();
    $leave->LeaveID = $row['LEAVEID'];
    $leave->LeaveType = $row['LEAVETYPE'];
    $leave->LeaveStartDate = $row['LEAVESTARTDATE'];
    $leave->LeaveEndDate = $row['LEAVEENDDATE'];
    $leaveHistory[] = $leave;
}

oci_free_statement($stmt);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Send Leave Request</h3>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
           <?php include '../includes/alert.php'; ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Soldier Information</h5>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Soldier ID</th>
                                    <td><?php echo $soldier['SOLDIERID']; ?></td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td><?php echo $soldier['NAME']; ?></td>
                                </tr>
                                <tr>
                                    <th>Company</th>
                                    <td><?php echo $soldier['COMPANYNAME']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Leave Request</h5>
                        <form method="post" action="">
                            <input type="hidden" name="soldier_id" value="<?php echo $soldierID; ?>">
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
                                <label for="leave_end_date">Leave End Date:</label>
                                <input type="date" name="leave_end_date" id="leave_end_date" class="form-control" required>
                            </div>
                            <button type="submit" name="send_leave_submit" class="btn btn-primary">Send Leave Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Leave History</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Leave ID</th>
                                        <th>Leave Type</th>
                                        <th>Leave Start Date</th>
                                        <th>Leave End Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leaveHistory as $leave): ?>
                                        <tr>
                                            <td><?php echo $leave->LeaveID; ?></td>
                                            <td><?php echo $leave->LeaveType; ?></td>
                                            <td><?php echo $leave->LeaveStartDate; ?></td>
                                            <td><?php echo $leave->LeaveEndDate; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
