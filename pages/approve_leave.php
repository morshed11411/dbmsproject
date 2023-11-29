<?php


session_start();

// Include your database connection code here
include '../includes/connection.php';
require_once '../includes/create_notification.php';
require_once '../includes/leave_controller.php';


$query = "SELECT LM.LEAVEID,SOLDIER.SOLDIERID,SOLDIER.NAME AS SOLDIER_NAME, LT.LEAVETYPE, LM.LEAVESTARTDATE, LM.LEAVEENDDATE, LM.REQUESTDATE, LM.STATUS 
          FROM LEAVEMODULE LM 
          JOIN LEAVETYPE LT ON LM.LEAVETYPEID = LT.LEAVETYPEID
          JOIN SOLDIER ON LM.SOLDIERID = SOLDIER.SOLDIERID
          WHERE LM.STATUS='Pending'
          ORDER BY LM.LEAVEID DESC";

$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$leaveRequests = array();
while ($row = oci_fetch_assoc($stmt)) {
    $leaveRequests[] = $row;
}

oci_free_statement($stmt);

if (isset($_POST['approve_leave'])) {
    $leaveIDToApprove = $_POST['leave_id'];
    $newStartDate = $_POST['new_start_date'];
    $newEndDate = $_POST['new_end_date'];

    // Validate leave start and end dates
    $leaveStartDate = date('Y-m-d', strtotime($newStartDate));
    $leaveEndDate = date('Y-m-d', strtotime($newEndDate));

    if ($leaveStartDate < date('Y-m-d')) {
        $_SESSION['error'] = "Error: Leave start date cannot be in the past.";
    } elseif ($leaveEndDate < $leaveStartDate) {
        $_SESSION['error'] = "Error: Leave end date cannot be before the start date.";
    } else {
        // Update the leave record with the new start and end dates and set status to 'Approved'
        $query = "UPDATE LEAVEMODULE 
          SET LEAVESTARTDATE = TO_DATE(:new_start_date, 'YYYY-MM-DD'), 
              LEAVEENDDATE = TO_DATE(:new_end_date, 'YYYY-MM-DD'), 
              STATUS = 'Approved', AUTHBY=:authid
          WHERE LEAVEID = :leave_id";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':new_start_date', $newStartDate);
        oci_bind_by_name($stmt, ':new_end_date', $newEndDate);
        oci_bind_by_name($stmt, ':leave_id', $leaveIDToApprove);
        oci_bind_by_name($stmt, ':authid', $_SESSION['userid']);

        $result = oci_execute($stmt);

        $_SESSION['success'] = "Leave approved successfully.";
        // Fetch the last inserted leave ID
        $query = "SELECT SOLDIERID, LEAVEID FROM LEAVEMODULE ORDER BY LEAVEID DESC FETCH FIRST 1 ROW ONLY";
        $stmtLeaveId = oci_parse($conn, $query);
        oci_execute($stmtLeaveId);

        // Fetch the result
        $notified = oci_fetch_assoc($stmtLeaveId);

        // Check if a result is found
        if ($notified) {
            $notifiedSoldierId = $notified['SOLDIERID'];
            $leaveIDToApprove = $notified['LEAVEID'];

            // Free the statement for fetching leave ID
            oci_free_statement($stmtLeaveId);

            $notifiedGroup = ''; // Assuming 'all' represents all users
            $message = "Your leave request is approved. Download leave card: <a href='leavecard.php?leaveid=$leaveIDToApprove'>Download</a>";
            $notifierSoldierId = $_SESSION['userid'];

            // Call the createNotification function
            $result = createNotification($notifiedSoldierId, $notifierSoldierId, $notifiedGroup, $message);
        } else {
            $error = oci_error($stmt);
            $_SESSION['error'] = "Failed to approve leave request: " . $error['message'];
        }

        // Close the connection
        oci_close($conn);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

}


if (isset($_POST['reject_leave'])) {
    $leaveIDToReject = $_POST['leave_id'];

    // Update the leave record to set the status as "Rejected"
    $query = "UPDATE LEAVEMODULE SET STATUS = 'Rejected' WHERE LEAVEID = :leave_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':leave_id', $leaveIDToReject);

    $result = oci_execute($stmt);

    if ($result) {
        $_SESSION['success'] = "Leave request has been rejected.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to reject leave request: " . $error['message'];
    }

    oci_free_statement($stmt);

    // Redirect back to the current page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
global $notified;

// Process the form submission to send a quick leave request
if (isset($_POST['quick_leave'])) {
    $leaveType = $_POST['leave_type'];
    $leaveStartDate = $_POST['leave_start_date'];
    $leaveEndDate = $_POST['leave_end_date'];
    $leaveRequestDate = date('Y-m-d'); // System date
    $status = 'Approved'; // Set the status to "Approved"

    // Get the soldier ID from the input field
    $soldierID = $_POST['soldier_id'];

    if ($leaveStartDate < date('Y-m-d')) {
        // Display error message
        $_SESSION['error'] = "Error: Leave start date cannot be in the past.";
    } elseif ($leaveEndDate < $leaveStartDate) {
        // Display error message
        $_SESSION['error'] = "Error: Leave end date cannot be before the start date.";
    } else {
        // Insert leave request into the database
        $query = "INSERT INTO LEAVEMODULE (SOLDIERID, LEAVETYPEID, LEAVESTARTDATE, LEAVEENDDATE, REQUESTDATE, STATUS, AUTHBY) VALUES (:soldier_id, :leave_type, TO_DATE(:leave_start_date, 'YYYY-MM-DD'), TO_DATE(:leave_end_date, 'YYYY-MM-DD'), TO_DATE(:leave_request_date, 'YYYY-MM-DD'), :status, :authid)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
        oci_bind_by_name($stmt, ':leave_type', $leaveType);
        oci_bind_by_name($stmt, ':leave_start_date', $leaveStartDate);
        oci_bind_by_name($stmt, ':leave_end_date', $leaveEndDate);
        oci_bind_by_name($stmt, ':leave_request_date', $leaveRequestDate);
        oci_bind_by_name($stmt, ':status', $status);
        oci_bind_by_name($stmt, ':authid', $_SESSION['userid']);

        $result = oci_execute($stmt);

        if ($result) {
            $_SESSION['success'] = "Leave approved successfully.";
            // Fetch the last inserted leave ID
            $query = "SELECT SOLDIERID, LEAVEID FROM LEAVEMODULE ORDER BY LEAVEID DESC FETCH FIRST 1 ROW ONLY";
            $stmtLeaveId = oci_parse($conn, $query);
            oci_execute($stmtLeaveId);

            // Fetch the result
            $notified = oci_fetch_assoc($stmtLeaveId);

            // Check if a result is found
            if ($notified) {
                $notifiedSoldierId = $notified['SOLDIERID'];
                $leaveIDToApprove = $notified['LEAVEID'];

                // Free the statement for fetching leave ID
                oci_free_statement($stmtLeaveId);

                $notifiedGroup = ''; // Assuming 'all' represents all users
                $message = "Your leave request is approved. Download leave card: <a href='leavecard.php?leaveid=$leaveIDToApprove'>Download</a>";
                $notifierSoldierId = $_SESSION['userid'];

                // Call the createNotification function
                $result = createNotification($notifiedSoldierId, $notifierSoldierId, $notifiedGroup, $message);
            } else {
                // Handle the case where no result is found
                $_SESSION['error'] = "Failed to send leave notification: No result found.";
            }

            // Close the connection
            oci_close($conn);

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}


include '../includes/header.php';
print_r($notified);
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Leave Approval</h3>
        </div>
        <div class="text-right">

            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#quickLeaveModal">
                Quick Leave
            </button>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php
        include '../includes/alert.php'; ?>


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Leave ID</th>
                                    <th>Soldier Name</th>
                                    <th>Leave Type</th>
                                    <th>Leave Start Date</th>
                                    <th>Leave End Date</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($leaveRequests)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <h4>No Leave Request</h4>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($leaveRequests as $leave):
                                        $soldierID = $leave['SOLDIERID'];
                                        ?>

                                        <tr>
                                            <td>
                                                <?php echo $leave['LEAVEID']; ?>
                                            </td>
                                            <td>
                                                <?php echo $leave['SOLDIER_NAME']; ?>
                                            </td>
                                            <td>
                                                <?php echo $leave['LEAVETYPE']; ?>
                                            </td>
                                            <td>
                                                <?php echo $leave['LEAVESTARTDATE']; ?>
                                            </td>
                                            <td>
                                                <?php echo $leave['LEAVEENDDATE']; ?>
                                            </td>
                                            <td>
                                                <?php echo $leave['REQUESTDATE']; ?>
                                            </td>
                                            <td>
                                                <?php echo $leave['STATUS']; ?>
                                            </td>
                                            <td>
                                                <!-- Approve Button (Open Approve Modal) -->
                                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                                    data-target="#approveLeaveModal_<?php echo $leave['LEAVEID']; ?>">
                                                    Approve
                                                </button>

                                                <!-- Reject Button (Open Reject Modal) -->
                                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                                    data-target="#rejectLeaveModal_<?php echo $leave['LEAVEID']; ?>">
                                                    Reject
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Approve Leave Modal -->
                                        <div class="modal fade" id="approveLeaveModal_<?php echo $leave['LEAVEID']; ?>"
                                            tabindex="-1" role="dialog"
                                            aria-labelledby="approveLeaveModalLabel_<?php echo $leave['LEAVEID']; ?>"
                                            aria-hidden="true">
                                            <!-- Modal content for approving leave request -->
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="approveLeaveModalLabel_<?php echo $leave['LEAVEID']; ?>">Approve
                                                            Leave Request</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">


                                                        <?php
                                                        include '../includes/soldier_info.php';
                                                        $totalDays = calculateLeaveCount($conn, $leaveTypes, $soldierID);
                                                        ?>

                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <?php foreach ($leaveTypes as $leaveType): ?>
                                                                                <div class="col text-center">
                                                                                    <strong>
                                                                                        <?php echo $leaveType; ?>
                                                                                    </strong>
                                                                                </div>
                                                                            <?php endforeach; ?>
                                                                            <div class="col text-center">
                                                                                <strong>Total</strong>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <?php foreach ($leaveTypes as $leaveType): ?>
                                                                                <div class="col text-center">
                                                                                    <?php echo $totalDays[$leaveType]; ?> Days
                                                                                </div>
                                                                            <?php endforeach; ?>
                                                                            <div class="col text-center">
                                                                                <?php echo array_sum($totalDays); ?> Days
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <form method="POST" action="">
                                                            <!-- Leave ID for reference -->
                                                            <input type="hidden" name="leave_id"
                                                                value="<?php echo $leave['LEAVEID']; ?>">

                                                            <!-- Leave Start Date (Editable) -->
                                                            <div class="form-row">
                                                                <!-- Leave Start Date (Editable) -->
                                                                <div class="form-group col-md-4">
                                                                    <label for="new_start_date">Start Date:</label>
                                                                    <input type="date" name="new_start_date"
                                                                        id="new_start_date_<?php echo $leave['LEAVEID']; ?>"
                                                                        class="form-control" required
                                                                        value="<?php echo date('Y-m-d', strtotime($leave['LEAVESTARTDATE'])); ?>"
                                                                        onchange="updateTotalDays('<?php echo $leave['LEAVEID']; ?>')">
                                                                </div>

                                                                <!-- Leave End Date (Editable) -->
                                                                <div class="form-group col-md-4">
                                                                    <label for="new_end_date">End Date:</label>
                                                                    <input type="date" name="new_end_date"
                                                                        id="new_end_date_<?php echo $leave['LEAVEID']; ?>"
                                                                        class="form-control" required
                                                                        value="<?php echo date('Y-m-d', strtotime($leave['LEAVEENDDATE'])); ?>"
                                                                        onchange="updateTotalDays('<?php echo $leave['LEAVEID']; ?>')">
                                                                </div>

                                                                <!-- Total Days (Readonly) -->
                                                                <div class="form-group col-md-4">
                                                                    <label for="total_days">Total Days:</label>
                                                                    <input type="text" name="total_days"
                                                                        id="total_days_<?php echo $leave['LEAVEID']; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo date_diff(date_create($leave['LEAVESTARTDATE']), date_create($leave['LEAVEENDDATE']))->format('%a') + 1; ?>"
                                                                        readonly>
                                                                </div>
                                                            </div>

                                                            <!-- Submit Button for Approving Leave -->
                                                            <button type="submit" name="approve_leave"
                                                                class="btn btn-success">Approve Leave</button>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Approve Leave Modal -->

                                        <!-- Reject Leave Modal -->
                                        <div class="modal fade" id="rejectLeaveModal_<?php echo $leave['LEAVEID']; ?>"
                                            tabindex="-1" role="dialog"
                                            aria-labelledby="rejectLeaveModalLabel_<?php echo $leave['LEAVEID']; ?>"
                                            aria-hidden="true">
                                            <!-- Modal content for rejecting leave request -->
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="rejectLeaveModalLabel_<?php echo $leave['LEAVEID']; ?>">Reject
                                                            Leave Request</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Confirmation message for rejecting leave -->
                                                        <p>Are you sure you want to reject this leave request?</p>
                                                        <!-- Replace with your form fields for rejecting -->
                                                        <form method="POST" action="">
                                                            <!-- Leave ID for reference -->
                                                            <input type="hidden" name="leave_id"
                                                                value="<?php echo $leave['LEAVEID']; ?>">
                                                            <!-- Submit Button for Rejecting Leave -->
                                                            <button type="submit" name="reject_leave"
                                                                class="btn btn-danger">Reject Leave</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Leave Modal -->
        <div class="modal fade" id="quickLeaveModal" tabindex="-1" role="dialog" aria-labelledby="quickLeaveModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="quickLeaveModalLabel">Quick Leave Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <!-- Soldier ID (Editable) -->
                            <div class="form-group">
                                <label for="soldier_id">Soldier ID:</label>
                                <input type="text" name="soldier_id" id="soldier_id" class="form-control" required>
                            </div>

                            <!-- Leave Type (Editable) -->
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

                            <!-- Leave Start Date (Editable, default to sysdate) -->
                            <div class="form-group">
                                <label for="leave_start_date">Start Date:</label>
                                <input type="date" name="leave_start_date" id="leave_start_date" class="form-control"
                                    required value="<?php echo date('Y-m-d'); ?>" onchange="updateTotalDays()">
                            </div>

                            <!-- Leave End Date (Editable) -->
                            <div class="form-group">
                                <label for="leave_end_date">End Date:</label>
                                <input type="date" name="leave_end_date" id="leave_end_date" class="form-control"
                                    required onchange="updateTotalDays()">
                            </div>

                            <!-- Total Days (Read-only) -->
                            <div class="form-group">
                                <label for="total_days">Total Days:</label>
                                <input type="text" name="total_days" id="total_days" class="form-control" readonly>
                            </div>

                            <!-- Submit Button for Quick Leave Request -->
                            <button type="submit" name="quick_leave" class="btn btn-success">Send Quick Leave</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- JavaScript to update total days when the date inputs change -->
<!-- JavaScript for Both Modals -->
<script>
    // JavaScript function to update the total days
    function updateTotalDays(leaveID) {
        var startDateInput = document.getElementById("new_start_date_" + leaveID) || document.getElementById("leave_start_date");
        var endDateInput = document.getElementById("new_end_date_" + leaveID) || document.getElementById("leave_end_date");
        var totalDaysInput = document.getElementById("total_days_" + leaveID) || document.getElementById("total_days");

        // Calculate and update the total days
        if (startDateInput && endDateInput && totalDaysInput) {
            if (startDateInput.value && endDateInput.value) {
                var startDate = new Date(startDateInput.value);
                var endDate = new Date(endDateInput.value);
                var totalDays = Math.floor((endDate - startDate) / (24 * 60 * 60 * 1000)) + 1;
                totalDaysInput.value = totalDays;
            } else {
                totalDaysInput.value = "";
            }
        }
    }

    // Add event listeners to the date inputs to update total days when they change
    var startDateInputs = document.querySelectorAll("[id^='new_start_date_']");
    var endDateInputs = document.querySelectorAll("[id^='new_end_date_']");
    var totalDaysInputs = document.querySelectorAll("[id^='total_days_']");
    var leaveStartInput = document.getElementById("leave_start_date");
    var leaveEndInput = document.getElementById("leave_end_date");
    var totalDaysInputQuick = document.getElementById("total_days");

    startDateInputs.forEach(function (element) {
        element.addEventListener('input', function () {
            updateTotalDays(element.getAttribute("data-leave-id"));
        });
    });

    endDateInputs.forEach(function (element) {
        element.addEventListener('input', function () {
            updateTotalDays(element.getAttribute("data-leave-id"));
        });
    });

    if (leaveStartInput && leaveEndInput && totalDaysInputQuick) {
        leaveStartInput.addEventListener('input', function () {
            updateTotalDays();
        });

        leaveEndInput.addEventListener('input', function () {
            updateTotalDays();
        });
    }

    // Calculate and display total days when the modal is shown
    $('[id^="approveLeaveModal_"]').on('show.bs.modal', function (event) {
        var leaveID = event.relatedTarget.getAttribute("data-leave-id");
        updateTotalDays(leaveID);
    });

    // Calculate and display total days when the "Quick Leave" modal is shown
    $('#quickLeaveModal').on('show.bs.modal', function () {
        updateTotalDays();
    });
</script>

<?php
include '../includes/footer.php';
?>