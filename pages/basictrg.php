<?php
session_start();

include '../includes/connection.php';

// Process the form submission to add a basic training
if (isset($_POST['add_training_submit'])) {
    $trainingCode = $_POST['training_code'];
    $trainingName = $_POST['training_name'];

    $query = "INSERT INTO BASICTRAINING (TRAININGCODE, TRAININGNAME) VALUES (:training_code, :training_name)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':training_code', $trainingCode);
    oci_bind_by_name($stmt, ':training_name', $trainingName);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Basic training added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add basic training: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: basictrg.php");
    exit();
}

// Process the form submission to edit a basic training
if (isset($_POST['edit_training_submit'])) {
    $trainingID = $_POST['edit_training_id'];
    $trainingCode = $_POST['edit_training_code'];
    $trainingName = $_POST['edit_training_name'];

    $query = "UPDATE BASICTRAINING SET TRAININGCODE = :training_code, TRAININGNAME = :training_name WHERE TRAININGID = :training_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':training_code', $trainingCode);
    oci_bind_by_name($stmt, ':training_name', $trainingName);
    oci_bind_by_name($stmt, ':training_id', $trainingID);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Basic training updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update basic training: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: basictrg.php");
    exit();
}

// Process the form submission to delete a basic training
if (isset($_POST['delete_training_submit'])) {
    $trainingID = $_POST['delete_training_id'];

    $query = "DELETE FROM BASICTRAINING WHERE TRAININGID = :training_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':training_id', $trainingID);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Basic training deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete basic training: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: basictrg.php");
    exit();
}

// Fetch data from the basictraining table
$query = "SELECT * FROM BASICTRAINING ORDER BY TRAININGID";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$basicTrainingList = array();
while ($row = oci_fetch_assoc($stmt)) {
    $basicTraining = new stdClass();
    $basicTraining->TrainingID = $row['TRAININGID'];
    $basicTraining->TrainingCode = $row['TRAININGCODE'];
    $basicTraining->TrainingName = $row['TRAININGNAME'];
    $basicTrainingList[] = $basicTraining;
}

oci_free_statement($stmt);
oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Basic Training Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addBasicTrainingModal">Add Basic Training</button>
        </div>
    </div>
</div>
<?php 
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Training ID</th>
                                    <th>Training Code</th>
                                    <th>Training Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($basicTrainingList as $basicTraining): ?>
                                    <tr>
                                        <td><?php echo $basicTraining->TrainingID; ?></td>
                                        <td><?php echo $basicTraining->TrainingCode; ?></td>
                                        <td><?php echo $basicTraining->TrainingName; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editBasicTrainingModal-<?php echo $basicTraining->TrainingID; ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteBasicTrainingModal-<?php echo $basicTraining->TrainingID; ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Edit Basic Training Modal -->
                                    <div class="modal fade" id="editBasicTrainingModal-<?php echo $basicTraining->TrainingID; ?>" tabindex="-1" role="dialog" aria-labelledby="editBasicTrainingModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editBasicTrainingModalLabel">Edit Basic Training</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_training_id" value="<?php echo $basicTraining->TrainingID; ?>">
                                                        <div class="form-group">
                                                            <label for="edit_training_code">Training Code:</label>
                                                            <input type="text" name="edit_training_code" id="edit_training_code" class="form-control" value="<?php echo $basicTraining->TrainingCode; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="edit_training_name">Training Name:</label>
                                                            <input type="text" name="edit_training_name" id="edit_training_name" class="form-control" value="<?php echo $basicTraining->TrainingName; ?>" required>
                                                        </div>
                                                        <button type="submit" name="edit_training_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Delete Basic Training Modal -->
                                    <div class="modal fade" id="deleteBasicTrainingModal-<?php echo $basicTraining->TrainingID; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteBasicTrainingModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteBasicTrainingModalLabel">Delete Basic Training</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this basic training?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_training_id" value="<?php echo $basicTraining->TrainingID; ?>">
                                                        <button type="submit" name="delete_training_submit" class="btn btn-danger">Delete</button>
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

<!-- Add Basic Training Modal -->
<div class="modal fade" id="addBasicTrainingModal" tabindex="-1" role="dialog" aria-labelledby="addBasicTrainingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBasicTrainingModalLabel">Add Basic Training</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="training_code">Training Code:</label>
                        <input type="text" name="training_code" id="training_code" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="training_name">Training Name:</label>
                        <input type="text" name="training_name" id="training_name" class="form-control" required>
                    </div>
                    <input type="submit" name="add_training_submit" value="Add Training" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
