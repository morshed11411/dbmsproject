<?php
session_start();
include '../includes/connection.php';

// Handle approval of password reset request
if (isset($_POST['approve_reset'])) {
    $request_id = $_POST['request_id'];

    // Update the request to mark it as approved
    $query = "UPDATE pwd_reset_req SET is_approved = 1 WHERE req_id = :request_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':request_id', $request_id);

    if (oci_execute($stmt)) {
        // Fetch the reset code for this request
        $query = "SELECT reset_code FROM pwd_reset_req WHERE req_id = :request_id";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':request_id', $request_id);
        oci_execute($stmt);
        $row = oci_fetch_assoc($stmt);
        $reset_code = $row['RESET_CODE'];
    } else {
        $_SESSION['error'] = 'Error approving password reset request. Please try again.';
    }
} elseif (isset($_POST['decline_reset'])) {
    $request_id = $_POST['request_id'];

    // Delete the request upon decline
    $query = "DELETE FROM pwd_reset_req WHERE req_id = :request_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':request_id', $request_id);

    if (oci_execute($stmt)) {
        $_SESSION['success'] = 'Password reset request declined and deleted.';
    } else {
        $_SESSION['error'] = 'Error declining password reset request. Please try again.';
    }
}

// Fetch the list of password reset requests
$query = "SELECT pwd_reset_req.req_id, pwd_reset_req.username, SOLDIER.NAME, TO_CHAR(pwd_reset_req.req_time, 'YYYY-MM-DD HH24:MI') AS REQ_TIME, pwd_reset_req.reset_code
FROM pwd_reset_req
JOIN SOLDIER ON pwd_reset_req.username = SOLDIER.SOLDIERID
ORDER BY pwd_reset_req.req_time";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$resetRequests = array();
while ($row = oci_fetch_assoc($stmt)) {
    $resetRequests[] = $row;
}

oci_free_statement($stmt);

include '../includes/header.php'; // Include your header
?>
<style>
    .reset-code {
        font-size: 20px;
        color: green;

        /* Set the initial font size */
        transition: font-size 0.2s;
        /* Add a smooth transition effect */
    }

    .reset-code:hover {
        font-size: 45px;
        /* Increase font size on hover */
        color: red;
        font-weight: bold;
        /* You can change the color or other styles as desired */
    }
</style>
<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Password Reset Requests</h3>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php include '../includes/alert.php'; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Ser</th>
                                    <th>Soldier No</th>
                                    <th>Name</th>
                                    <th>Request Time</th>
                                    <th>Reset Code</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($resetRequests)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <h4>No pending password reset requests.</h4>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $i = 1;
                                    foreach ($resetRequests as $request): ?>
                                        <tr>
                                            <td>
                                                <?php echo $i; ?>
                                            </td>
                                            <td>
                                                <?php echo $request['USERNAME']; ?>
                                            </td>
                                            <td>
                                                <?php echo $request['NAME']; ?>
                                            </td>
                                            <td>
                                                <?php echo $request['REQ_TIME']; ?>
                                            </td>
                                            <td class="reset-code">
                                                <span>
                                                    <?php echo isset($reset_code) ? $reset_code : ''; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <form method="POST" action="">
                                                    <input type="hidden" name="request_id"
                                                        value="<?php echo $request['REQ_ID']; ?>">
                                                    <button type="submit" name="approve_reset" class="btn btn-success">Show
                                                        Code</button>
                                                    <button type="submit" name="decline_reset"
                                                        class="btn btn-danger">Decline</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php $i++; endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>