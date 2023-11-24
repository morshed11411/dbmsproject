<?php
session_start();

include '../includes/connection.php';

// Add Duty
if (isset($_POST['add_duty_submit'])) {
    $postName = $_POST['post_name'];
    $numPersons = $_POST['num_persons'];

    $addDutyQuery = "INSERT INTO DUTIES (DUTYTYPEID, POSTNAME, NUMPERSONS) VALUES (:dutytypeid, :postname, :numpersons)";
    $addDutyStmt = oci_parse($conn, $addDutyQuery);
    oci_bind_by_name($addDutyStmt, ':dutytypeid', $dutyTypeID);
    oci_bind_by_name($addDutyStmt, ':postname', $postName);
    oci_bind_by_name($addDutyStmt, ':numpersons', $numPersons);

    $result = oci_execute($addDutyStmt);
    if ($result) {
        $_SESSION['success'] = "Duty added successfully.";
    } else {
        $error = oci_error($addDutyStmt);
        $_SESSION['error'] = "Failed to add duty: " . $error['message'];
    }

    oci_free_statement($addDutyStmt);
    header("Location: {$_SERVER['PHP_SELF']}?dutytypeid={$dutyTypeID}");
    exit();
}

// Fetch duties for the given duty type
$dutiesQuery = "SELECT * FROM DUTIES WHERE DUTYTYPEID = :dutytypeid";
$dutiesStmt = oci_parse($conn, $dutiesQuery);
oci_bind_by_name($dutiesStmt, ':dutytypeid', $dutyTypeID);
oci_execute($dutiesStmt);
$duties = [];
while ($row = oci_fetch_assoc($dutiesStmt)) {
    $duties[] = $row;
}
oci_free_statement($dutiesStmt);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Manage Duties
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addDutyModal">Add Duty</button>
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
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Duty ID</th>
                                    <th>Duty Type ID</th>
                                    <th>Post ID</th>
                                    <th>Num Persons</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($duties as $duty): ?>
                                    <tr>
                                        <td><?php echo $duty['DUTYID']; ?></td>
                                        <td><?php echo $duty['DUTYTYPEID']; ?></td>
                                        <td><?php echo $duty['POSTID']; ?></td>
                                        <td><?php echo $duty['NUMPERSONS']; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteDutyModal-<?php echo $duty['DUTYID']; ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Delete Duty Modal -->
                                    <div class="modal fade" id="deleteDutyModal-<?php echo $duty['DUTYID']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteDutyModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteDutyModalLabel">Delete Duty</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this Duty?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_duty_id" value="<?php echo $duty['DUTYID']; ?>">
                                                        <button type="submit" name="delete_duty_submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Add Duty Modal -->
<div class="modal fade" id="addDutyModal" tabindex="-1" role="dialog" aria-labelledby="addDutyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDutyModalLabel">Add Duty</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="post_name">Post Name:</label>
                        <input type="text" name="post_name" id="post_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="num_persons">Number of Persons:</label>
                        <input type="text" name="num_persons" id="num_persons" class="form-control" required>
                    </div>
                    <input type="submit" name="add_duty_submit" value="Add Duty" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
