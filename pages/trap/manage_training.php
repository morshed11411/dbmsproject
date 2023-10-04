<?php
session_start();

include '../includes/connection.php';

// Process the form submission to update basic training
if (isset($_POST['update_basic_training_submit'])) {
    $soldierID = $_POST['soldier_id'];
    $trainingID = $_POST['training_id'];
    $trainingDate = $_POST['training_date'];
    $trainingResult = $_POST['result'];

    // Update basic training information in the database
    $query = "UPDATE SOLDIERBASICTRAINING SET TRAININGDATE = TO_DATE(:training_date, 'YYYY-MM-DD'), REMARK = :remark 
              WHERE SOLDIERID = :soldier_id AND TRAININGID = :training_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':training_date', $trainingDate);
    oci_bind_by_name($stmt, ':remark', $trainingResult);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
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

    header("Location: manage_training.php?soldier=$soldierID");
    exit();
}

// Process the form submission to add basic training
if (isset($_POST['add_basic_training_submit'])) {
    $soldierID = $_POST['soldier_id'];
    $trainingID = $_POST['basic_training'];

    // Insert basic training information into the database
    $query = "INSERT INTO SOLDIERBASICTRAINING (TRAININGID, TRAININGDATE, SOLDIERID, REMARK) 
              VALUES (:training_id, SYSDATE, :soldier_id, '')";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':training_id', $trainingID);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Basic training added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add basic training: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: manage_training.php?soldier=$soldierID");
    exit();
}

// Process the form submission to add advanced training
if (isset($_POST['add_advanced_training_submit'])) {
    $soldierID = $_POST['soldier_id'];
    $cadreID = $_POST['advanced_training'];

    // Insert advanced training information into the database
    $query = "INSERT INTO SOLDIERADVANCEDTRAINING (CADREID, SOLDIERID, REMARK) 
              VALUES (:cadre_id, :soldier_id, '')";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':cadre_id', $cadreID);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Advanced training added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add advanced training: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: manage_training.php?soldier=$soldierID");
    exit();
}

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


// Fetch basic training history for the soldier
$query = "SELECT T.TRAININGID, T.TRAININGNAME, S.TRAININGDATE, S.REMARK 
          FROM BASICTRAINING T 
          LEFT JOIN SOLDIERBASICTRAINING S ON T.TRAININGID = S.TRAININGID AND S.SOLDIERID = :soldier_id";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierID);
oci_execute($stmt);

$basicTrainingList = array();
while ($row = oci_fetch_assoc($stmt)) {
    $basicTraining = new stdClass();
    $basicTraining->TrainingID = $row['TRAININGID'];
    $basicTraining->TrainingName = $row['TRAININGNAME'];
    $basicTraining->TrainingDate = $row['TRAININGDATE'];
    $basicTraining->Remark = $row['REMARK'];
    $basicTrainingList[] = $basicTraining;
}

oci_free_statement($stmt);


// Fetch advanced training history for the soldier
$query = "SELECT A.CADREID, A.NAME, S.REMARK 
          FROM ADVANCETRAINING A 
          LEFT JOIN SOLDIERADVANCEDTRAINING S ON A.CADREID = S.CADREID AND S.SOLDIERID = :soldier_id";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierID);
oci_execute($stmt);

$advancedTrainingList = array();
while ($row = oci_fetch_assoc($stmt)) {
    $advancedTraining = new stdClass();
    $advancedTraining->CadreID = $row['CADREID'];
    $advancedTraining->Name = $row['NAME'];
    $advancedTraining->Remark = $row['REMARK'];
    $advancedTrainingList[] = $advancedTraining;
}

oci_free_statement($stmt);
oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Manage Training</h3>
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
                                    <td>
                                        <?php echo $soldier['SOLDIERID']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>
                                        <?php echo $soldier['NAME']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Company</th>
                                    <td>
                                        <?php echo $soldier['COMPANYNAME']; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Basic Training History</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Training Name</th>
                                    <th>Date</th>
                                    <th>Result</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($basicTrainingList as $basicTraining): ?>
                                    <tr>
                                        <td>
                                            <?php echo $basicTraining->TrainingName; ?>
                                        </td>
                                        <td>
                                            <?php echo $basicTraining->TrainingDate ? $basicTraining->TrainingDate : 'NA'; ?>
                                        </td>
                                        <td>
                                            <?php echo $basicTraining->Remark ? $basicTraining->Remark : 'NA'; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-target="#editBasicTrainingModal<?php echo $basicTraining->TrainingID; ?>">Edit</button>
                                        </td>
                                    </tr>
                                    <!-- Edit Basic Training Modal -->
                                    <div class="modal fade"
                                        id="editBasicTrainingModal<?php echo $basicTraining->TrainingID; ?>" tabindex="-1"
                                        role="dialog" aria-labelledby="editBasicTrainingModalLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editBasicTrainingModalLabel">Edit Basic
                                                        Training</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post" action="">
                                                        <input type="hidden" name="soldier_id"
                                                            value="<?php echo $soldierID; ?>">
                                                        <input type="hidden" name="training_id"
                                                            value="<?php echo $basicTraining->TrainingID; ?>">
                                                        <div class="form-group">
                                                            <label for="training_date">Date:</label>
                                                            <input type="date" name="training_date" id="training_date"
                                                                class="form-control"
                                                                value="<?php echo $basicTraining->TrainingDate; ?>"
                                                                required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="result">Result:</label>
                                                            <input type="text" name="result" id="result"
                                                                class="form-control"
                                                                value="<?php echo $basicTraining->Remark; ?>" required>
                                                        </div>
                                                        <button type="submit" name="update_basic_training_submit"
                                                            class="btn btn-primary">Save Changes</button>
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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Advanced Training History</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Training Name</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($advancedTrainingList as $advancedTraining): ?>
                                    <tr>
                                        <td>
                                            <?php echo $advancedTraining->Name; ?>
                                        </td>
                                        <td>
                                            <?php echo $advancedTraining->Remark ? $advancedTraining->Remark : 'NA'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Add Basic Training</h5>
                        <form method="post" action="">
                            <input type="hidden" name="soldier_id" value="<?php echo $soldierID; ?>">
                            <div class="form-group">
                                <label for="basic_training">Training:</label>
                                <select name="basic_training" id="basic_training" class="form-control" required>
                                    <?php
                                    $query = "SELECT TRAININGID, TRAININGNAME FROM BASICTRAINING";
                                    $stmt = oci_parse($conn, $query);
                                    oci_execute($stmt);

                                    while ($row = oci_fetch_assoc($stmt)) {
                                        echo '<option value="' . $row['TRAININGID'] . '">' . $row['TRAININGNAME'] . '</option>';
                                    }

                                    oci_free_statement($stmt);
                                    ?>
                                </select>
                            </div>
                            <button type="submit" name="add_basic_training_submit" class="btn btn-primary">Add Basic
                                Training</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Add Advanced Training</h5>
                        <form method="post" action="">
                            <input type="hidden" name="soldier_id" value="<?php echo $soldierID; ?>">
                            <div class="form-group">
                                <label for="advanced_training">Training:</label>
                                <select name="advanced_training" id="advanced_training" class="form-control" required>
                                    <?php
                                    $query = "SELECT CADREID, NAME FROM ADVANCETRAINING";
                                    $stmt = oci_parse($conn, $query);
                                    oci_execute($stmt);

                                    while ($row = oci_fetch_assoc($stmt)) {
                                        echo '<option value="' . $row['CADREID'] . '">' . $row['NAME'] . '</option>';
                                    }

                                    oci_free_statement($stmt);
                                    ?>
                                </select>
                            </div>
                            <button type="submit" name="add_advanced_training_submit"
                                class="btn btn-primary">Add Advanced Training</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
