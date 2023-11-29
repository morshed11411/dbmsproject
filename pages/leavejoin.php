<?php
session_start();
include '../includes/connection.php';

// Check if the form is submitted to mark a soldier as "in for leave"
if (isset($_POST['mark_in_for_leave'])) {
    $searchSoldierID = $_POST['soldier_id'];

    // Check if the soldier ID matches any leave record
    $query = "SELECT LEAVEID FROM LEAVEMODULE WHERE STATUS = 'On Leave' AND ONLEAVE = 1 AND SOLDIERID = :search_soldier";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':search_soldier', $searchSoldierID);
    oci_execute($stmt);

    if ($row = oci_fetch_assoc($stmt)) {
        // Soldier ID matches a leave record, mark the soldier as "in for leave"
        $leaveIDToMarkIn = $row['LEAVEID'];
        $query = "UPDATE LEAVEMODULE SET INTIME = CURRENT_TIMESTAMP, STATUS='Expired', ONLEAVE = 0, LEAVEENDDATE = CURRENT_TIMESTAMP WHERE LEAVEID = :leave_id";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':leave_id', $leaveIDToMarkIn);
        $result = oci_execute($stmt);

        if ($result) {
            $_SESSION['success'] = "Soldier marked as 'In for Leave.'";
        } else {
            $error = oci_error($stmt);
            $_SESSION['error'] = "Failed to mark soldier as 'In for Leave': " . $error['message'];
        }
    } else {
        $_SESSION['error'] = "Soldier ID not found in approved leave records.";
    }

    oci_free_statement($stmt);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch the list of approved leaves with onleave=1 (marked as out)
$query = "SELECT LEAVEMODULE.LEAVEID, SOLDIER.SOLDIERID, SOLDIER.NAME AS SOLDIER_NAME, LEAVETYPE.LEAVETYPE AS LEAVETYPE, LEAVEMODULE.LEAVESTARTDATE, LEAVEMODULE.LEAVEENDDATE
FROM LEAVEMODULE
JOIN SOLDIER ON LEAVEMODULE.SOLDIERID = SOLDIER.SOLDIERID
JOIN LEAVETYPE ON LEAVEMODULE.LEAVETYPEID = LEAVETYPE.LEAVETYPEID
WHERE LEAVEMODULE.STATUS = 'On Leave' AND LEAVEMODULE.ONLEAVE = 1
ORDER BY LEAVEMODULE.LEAVEID";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$approvedLeaves = array();
while ($row = oci_fetch_assoc($stmt)) {
    $approvedLeaves[] = $row;
}

oci_free_statement($stmt);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Mark Soldiers In for Leave</h3>
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
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="soldier_id">Enter Soldier ID:</label>
                                <input type="text" name="soldier_id" id="soldier_id" class="form-control" required>
                            </div>
                            <button type="submit" name="mark_in_for_leave" class="btn btn-success">Mark Out for
                                Leave</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Leave ID</th>
                                    <th>Soldier ID</th>
                                    <th>Soldier Name</th>
                                    <th>Leave Type</th>
                                    <th>Leave Start Date</th>
                                    <th>Leave End Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($approvedLeaves)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <h4>No approved leaves to mark soldiers in for leave.</h4>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($approvedLeaves as $leave): ?>
                                        <tr>
                                            <td>
                                                <?php echo $leave['LEAVEID']; ?>
                                            </td>
                                            <td>
                                                <?php echo $leave['SOLDIERID']; ?>
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
                                                <form method="POST" action="">
                                                    <input type="hidden" name="soldier_id"
                                                        value="<?php echo $leave['SOLDIERID']; ?>">
                                                    <button type="submit" name="mark_in_for_leave"
                                                        class="btn btn-success">Joined From Leave</button>
                                                </form>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- JavaScript to focus on the search box when the page loads -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('soldier_id').focus();
    });
</script>

<?php include '../includes/footer.php'; ?>