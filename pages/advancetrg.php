<?php
session_start();

include '../includes/connection.php';

// Process the form submission to add an advanced training
if (isset($_POST['add_training_submit'])) {
    $name = $_POST['training_name'];
    $startDate = $_POST['training_start_date'];
    $endDate = $_POST['training_end_date'];
    $trainingOIC = $_POST['training_oic'];
    $instructor = $_POST['instructor'];

    $query = "INSERT INTO ADVANCETRAINING (NAME, TRAININGSTARTDATE, TRAININGENDDATE, TRAININGOIC, INSTRUCTOR) 
              VALUES (:name, TO_DATE(:start_date, 'YYYY-MM-DD'), TO_DATE(:end_date, 'YYYY-MM-DD'), :training_oic, :instructor)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':name', $name);
    oci_bind_by_name($stmt, ':start_date', $startDate);
    oci_bind_by_name($stmt, ':end_date', $endDate);
    oci_bind_by_name($stmt, ':training_oic', $trainingOIC);
    oci_bind_by_name($stmt, ':instructor', $instructor);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Advanced training added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add advanced training: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: advancetrg.php");
    exit();
}

// Process the form submission to edit an advanced training
if (isset($_POST['edit_training_submit'])) {
    $trainingID = $_POST['edit_training_id'];
    $name = $_POST['edit_training_name'];
    $startDate = $_POST['edit_training_start_date'];
    $endDate = $_POST['edit_training_end_date'];
    $trainingOIC = $_POST['edit_training_oic'];
    $instructor = $_POST['edit_instructor'];

    $query = "UPDATE ADVANCETRAINING 
              SET NAME = :name, TRAININGSTARTDATE = TO_DATE(:start_date, 'YYYY-MM-DD'), 
                  TRAININGENDDATE = TO_DATE(:end_date, 'YYYY-MM-DD'), TRAININGOIC = :training_oic, 
                  INSTRUCTOR = :instructor 
              WHERE CADREID = :training_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':name', $name);
    oci_bind_by_name($stmt, ':start_date', $startDate);
    oci_bind_by_name($stmt, ':end_date', $endDate);
    oci_bind_by_name($stmt, ':training_oic', $trainingOIC);
    oci_bind_by_name($stmt, ':instructor', $instructor);
    oci_bind_by_name($stmt, ':training_id', $trainingID);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Advanced training updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update advanced training: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: advancetrg.php");
    exit();
}

// Process the form submission to delete an advanced training
if (isset($_POST['delete_training_submit'])) {
    $trainingID = $_POST['delete_training_id'];

    $query = "DELETE FROM ADVANCETRAINING WHERE CADREID = :training_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':training_id', $trainingID);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Advanced training deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete advanced training: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: advancetrg.php");
    exit();
}

// Fetch data from the ADVANCETRAINING table
$query = "SELECT * FROM ADVANCETRAINING";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$advancedTrainingList = array();
while ($row = oci_fetch_assoc($stmt)) {
    $advancedTraining = new stdClass();
    $advancedTraining->CadreID = $row['CADREID'];
    $advancedTraining->Name = $row['NAME'];
    $advancedTraining->StartDate = $row['TRAININGSTARTDATE'];
    $advancedTraining->EndDate = $row['TRAININGENDDATE'];
    $advancedTraining->TrainingOIC = $row['TRAININGOIC'];
    $advancedTraining->Instructor = $row['INSTRUCTOR'];
    $advancedTrainingList[] = $advancedTraining;
}

oci_free_statement($stmt);
oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Advanced Training Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addAdvancedTrainingModal">Add Advanced Training</button>
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
           <?php include '../includes/alert.php'; ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Cadre ID</th>
                                    <th>Name</th>
                                    <th>Training Start Date</th>
                                    <th>Training End Date</th>
                                    <th>Training OIC</th>
                                    <th>Instructor</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($advancedTrainingList as $advancedTraining): ?>
                                    <tr>
                                        <td><?php echo $advancedTraining->CadreID; ?></td>
                                        <td><?php echo $advancedTraining->Name; ?></td>
                                        <td><?php echo $advancedTraining->StartDate; ?></td>
                                        <td><?php echo $advancedTraining->EndDate; ?></td>
                                        <td><?php echo $advancedTraining->TrainingOIC; ?></td>
                                        <td><?php echo $advancedTraining->Instructor; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editAdvancedTrainingModal-<?php echo $advancedTraining->CadreID; ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAdvancedTrainingModal-<?php echo $advancedTraining->CadreID; ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Edit Advanced Training Modal -->
                                    <div class="modal fade" id="editAdvancedTrainingModal-<?php echo $advancedTraining->CadreID; ?>" tabindex="-1" role="dialog" aria-labelledby="editAdvancedTrainingModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editAdvancedTrainingModalLabel">Edit Advanced Training</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_training_id" value="<?php echo $advancedTraining->CadreID; ?>">
                                                        <div class="form-group">
                                                            <label for="edit_training_name">Name:</label>
                                                            <input type="text" name="edit_training_name" id="edit_training_name" class="form-control" value="<?php echo $advancedTraining->Name; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="edit_training_start_date">Training Start Date:</label>
                                                            <input type="date" name="edit_training_start_date" id="edit_training_start_date" class="form-control" value="<?php echo $advancedTraining->StartDate; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="edit_training_end_date">Training End Date:</label>
                                                            <input type="date" name="edit_training_end_date" id="edit_training_end_date" class="form-control" value="<?php echo $advancedTraining->EndDate; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="edit_training_oic">Training OIC:</label>
                                                            <input type="text" name="edit_training_oic" id="edit_training_oic" class="form-control" value="<?php echo $advancedTraining->TrainingOIC; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="edit_instructor">Instructor:</label>
                                                            <input type="text" name="edit_instructor" id="edit_instructor" class="form-control" value="<?php echo $advancedTraining->Instructor; ?>" required>
                                                        </div>
                                                        <button type="submit" name="edit_training_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Delete Advanced Training Modal -->
                                    <div class="modal fade" id="deleteAdvancedTrainingModal-<?php echo $advancedTraining->CadreID; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteAdvancedTrainingModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteAdvancedTrainingModalLabel">Delete Advanced Training</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this advanced training?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_training_id" value="<?php echo $advancedTraining->CadreID; ?>">
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

<!-- Add Advanced Training Modal -->
<div class="modal fade" id="addAdvancedTrainingModal" tabindex="-1" role="dialog" aria-labelledby="addAdvancedTrainingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAdvancedTrainingModalLabel">Add Advanced Training</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="training_name">Name:</label>
                        <input type="text" name="training_name" id="training_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="training_start_date">Training Start Date:</label>
                        <input type="date" name="training_start_date" id="training_start_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="training_end_date">Training End Date:</label>
                        <input type="date" name="training_end_date" id="training_end_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="training_oic">Training OIC:</label>
                        <input type="text" name="training_oic" id="training_oic" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="instructor">Instructor:</label>
                        <input type="text" name="instructor" id="instructor" class="form-control" required>
                    </div>
                    <input type="submit" name="add_training_submit" value="Add Training" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
