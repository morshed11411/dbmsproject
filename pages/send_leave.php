<?php
session_start();

include '../includes/connection.php';

function getLeaveTypes($conn)
{
    $leaveTypes = [];

    $leaveQuery = "SELECT LEAVETYPEID, LEAVETYPE FROM LEAVETYPE";
    $leaveStmt = oci_parse($conn, $leaveQuery);
    oci_execute($leaveStmt);

    while ($leaveRow = oci_fetch_assoc($leaveStmt)) {
        $leaveTypes[$leaveRow['LEAVETYPEID']] = $leaveRow['LEAVETYPE'];
    }

    oci_free_statement($leaveStmt);

    return $leaveTypes;
}

$leaveTypes = getLeaveTypes($conn);



// Fetch soldier ID and name from the query parameter
if (isset($_GET['soldier'])) {
    $soldierID = $_GET['soldier'];

    // Fetch soldier details from the database
    $query = "SELECT SOLDIERID, NAME, COMPANYNAME FROM SOLDIER JOIN COMPANY USING (COMPANYID) WHERE SOLDIERID = :soldier_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_execute($stmt);

    $soldier = oci_fetch_assoc($stmt);

    oci_free_statement($stmt);
}


// Process the form submission to send a leave request
if (isset($_POST['send_leave_submit'])) {
    $leaveType = $_POST['leave_type'];
    $leaveStartDate = $_POST['leave_start_date'];
    $leaveEndDate = $_POST['leave_end_date'];
    $leaveRequestDate = date('Y-m-d'); // System date
    $status = 'Pending';

    if ($leaveStartDate < date('Y-m-d')) {
        // Display error message
        $_SESSION['error'] = "Error: Leave start date cannot be in the past.";
        header("Location: send_leave.php?soldier=$soldierID");
        exit();
    } elseif ($leaveEndDate < $leaveStartDate) {
        // Display error message
        $_SESSION['error'] = "Error: Leave end date cannot be before the start date.";
        header("Location: send_leave.php?soldier=$soldierID");
        exit();
    } else {
        // Insert leave request into the database

        $query = "INSERT INTO LEAVEMODULE (SOLDIERID, LEAVETYPEID, LEAVESTARTDATE, LEAVEENDDATE, REQUESTDATE, STATUS) VALUES (:soldier_id, :leave_type, TO_DATE(:leave_start_date, 'YYYY-MM-DD'), TO_DATE(:leave_end_date, 'YYYY-MM-DD'), TO_DATE(:leave_request_date, 'YYYY-MM-DD'), :status)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
        oci_bind_by_name($stmt, ':leave_type', $leaveType);
        oci_bind_by_name($stmt, ':leave_start_date', $leaveStartDate);
        oci_bind_by_name($stmt, ':leave_end_date', $leaveEndDate);
        oci_bind_by_name($stmt, ':leave_request_date', $leaveRequestDate);
        oci_bind_by_name($stmt, ':status', $status);

        $result = oci_execute($stmt);

        if ($result) {
            $_SESSION['success'] = "Leave request sent successfully.";

            // Open leave certificate in a popup window
            header("Location: send_leave.php?soldier=$soldierID");
            exit();
        } else {
            $error = oci_error($stmt);
            $_SESSION['error'] = "Failed to send leave request: " . $error['message'];
            header("Location: send_leave.php?soldier=$soldierID");
            exit();
        }
    }

    oci_free_statement($stmt);
    oci_close($conn);
}

// Handle cancel leave request or delete leave request
if (isset($_GET['leave_id'])) {
    $leaveIDToCancel = $_GET['leave_id'];

    // Delete the leave record
    $query = "DELETE FROM LEAVEMODULE WHERE LEAVEID = :leave_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':leave_id', $leaveIDToCancel);

    $result = oci_execute($stmt);

    if ($result) {
        $_SESSION['success'] = "Leave request has been canceled and deleted.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to cancel and delete leave request: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    // Redirect back to the same page
    header("Location: send_leave.php?soldier=$soldierID");
    exit();
}

// Fetch leave history of the soldier
$query = "SELECT LM.LEAVEID, LT.LEAVETYPE, LM.LEAVESTARTDATE, LM.LEAVEENDDATE, LM.REQUESTDATE, LM.STATUS 
          FROM LEAVEMODULE LM 
          JOIN LEAVETYPE LT ON LM.LEAVETYPEID = LT.LEAVETYPEID
          WHERE LM.SOLDIERID = :soldier_id 
          ORDER BY LM.LEAVEID DESC";
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
    $leave->RequestDate = $row['REQUESTDATE'];
    $leave->Status = $row['STATUS'];
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
            <div class="col col-md-4">
                <?php include '../includes/soldier_info.php'; ?>

            </div>
            <div class="col col-md-8">


                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="post" action="">
                                    <input type="hidden" name="soldier_id" value="<?php echo $soldierID; ?>">
                                    <div class="form-group">
                                        <label for="leave_type">Leave Type:</label>
                                        <select name="leave_type" id="leave_type" class="form-control" required>
                                            <?php foreach ($leaveTypes as $leaveId => $leaveType): ?>
                                                <option value="<?php echo $leaveId; ?>">
                                                    <?php echo $leaveType; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="leave_start_date">Leave Start Date:</label>
                                            <input type="date" name="leave_start_date" id="leave_start_date"
                                                class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="leave_end_date">Leave End Date:</label>
                                            <input type="date" name="leave_end_date" id="leave_end_date"
                                                class="form-control" required>
                                        </div>
                                    </div>

                                    <button type="submit" name="send_leave_submit" class="btn btn-primary">Send Leave
                                        Request</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave History Card -->
        <div class="row mt-2">
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
                                        <th>Total Days</th>
                                        <th>Request Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $id = 1;
                                    foreach ($leaveHistory as $leave): ?>
                                        <tr>
                                            <td>
                                                <?php echo $id;
                                                $id++; ?>
                                            </td>
                                            <td>
                                                <?php echo $leave->LeaveType; ?>
                                            </td>
                                            <td>
                                                <?php echo $leave->LeaveStartDate; ?>
                                            </td>
                                            <td>
                                                <?php echo $leave->LeaveEndDate; ?>
                                            </td>
                                            <!-- Calculate and display total days -->
                                            <td>
                                                <?php echo date_diff(date_create($leave->LeaveStartDate), date_create($leave->LeaveEndDate))->format('%a') + 1; ?>
                                            </td>
                                            <td>
                                                <?php echo $leave->RequestDate; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status = $leave->Status;
                                                if ($status === 'Pending') {
                                                    echo '<span class="btn btn-warning">' . $status . '</span>';
                                                } elseif ($status === 'Approved') {
                                                    echo '<span class="btn btn-success">' . $status . '</span>';
                                                } elseif ($status === 'Expired') {
                                                    echo '<span class="btn btn-secondary">' . $status . '</span>';
                                                } elseif ($status === 'Rejected') {
                                                    echo '<span class="btn btn-danger">' . $status . '</span>';
                                                } elseif ($status === 'On Leave') {
                                                    echo '<span class="btn btn-info">' . $status . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if ($status === 'Pending') {
                                                    echo '<a href="send_leave.php?soldier=' . $soldierID . '&leave_id=' . $leave->LeaveID . '" class="btn btn-danger">Cancel</a>';
                                                } elseif ($status === 'Approved') {
                                                    echo '<a href="leavecard.php?leaveid=' . $leave->LeaveID . '" class="btn btn-primary" target="_blank">Leave Card</a>';
                                                } elseif (($status === 'Expired') || ($status === 'On Leave')) {
                                                    echo '<a href="leavecard.php?leaveid=' . $leave->LeaveID . '" class="btn btn-light" target="_blank">Leave Card</a>';
                                                }

                                                ?>
                                                <!-- Additional actions if needed -->
                                            </td>
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