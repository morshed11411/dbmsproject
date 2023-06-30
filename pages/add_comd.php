<?php
session_start();

include '../includes/connection.php';

// Process the form submission to add temporary command
if (isset($_POST['add_comd_submit'])) {
    $soldierID = $_POST['soldier_id'];
    $comdID = $_POST['comd_id'];
    $startDate = date('Y-m-d'); // Set the start date as the current system date

    // Insert temporary command assignment into the database
    $query = "INSERT INTO SOLDIERTEMPCOMD (SOLDIER_ID, TEMPCOMD_ID, START_DATE) VALUES (:soldier_id, :comd_id, TO_DATE(:start_date, 'YYYY-MM-DD'))";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_bind_by_name($stmt, ':comd_id', $comdID);
    oci_bind_by_name($stmt, ':start_date', $startDate);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Temporary command added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add temporary command: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: add_comd.php?soldier=$soldierID");
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

// Fetch temporary command list from the database
$query = "SELECT COMDID, COMDNAME FROM TEMPORARYCOMMAND";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$comdList = array();
while ($row = oci_fetch_assoc($stmt)) {
    $comd = new stdClass();
    $comd->ComdID = $row['COMDID'];
    $comd->ComdName = $row['COMDNAME'];
    $comdList[] = $comd;
}

oci_free_statement($stmt);

// Process the end temporary command action
if (isset($_POST['end_comd_submit'])) {
    $soldierID = $_POST['soldier_id'];
    $comdID = $_POST['comd_id'];

    // Set the end date as the current system date
    $endDate = date('Y-m-d');

    // Update the end date for the temporary command assignment
    $query = "UPDATE SOLDIERTEMPCOMD SET END_DATE = TO_DATE(:end_date, 'YYYY-MM-DD') WHERE SOLDIER_ID = :soldier_id AND TEMPCOMD_ID = :comd_id AND END_DATE IS NULL";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':end_date', $endDate);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_bind_by_name($stmt, ':comd_id', $comdID);

    $result = oci_execute($stmt);
    $rowCount = oci_num_rows($stmt);

    if ($result && $rowCount > 0) {
        $_SESSION['success'] = "Temporary command ended successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to end temporary command: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: add_comd.php?soldier=$soldierID");
    exit();
}

// Fetch active temporary command assignment for the soldier
$query = "SELECT S.TEMPCOMD_ID, C.COMDNAME, S.START_DATE
          FROM SOLDIERTEMPCOMD S
          JOIN TEMPORARYCOMMAND C ON S.TEMPCOMD_ID = C.COMDID
          WHERE S.SOLDIER_ID = :soldier_id AND S.END_DATE IS NULL";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierID);
oci_execute($stmt);

$activeComd = oci_fetch_assoc($stmt);

// Fetch temporary command history for the soldier
$query = "SELECT C.COMDNAME, S.START_DATE, S.END_DATE 
          FROM SOLDIERTEMPCOMD S 
          JOIN TEMPORARYCOMMAND C ON S.TEMPCOMD_ID = C.COMDID 
          WHERE S.SOLDIER_ID = :soldier_id ORDER BY ID DESC";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierID);
oci_execute($stmt);

$comdHistoryList = array();
while ($row = oci_fetch_assoc($stmt)) {
    $comdHistory = new stdClass();
    $comdHistory->ComdName = $row['COMDNAME'];
    $comdHistory->StartDate = $row['START_DATE'];
    $comdHistory->EndDate = $row['END_DATE'];
    $comdHistoryList[] = $comdHistory;
}

oci_free_statement($stmt);
oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Add/End Temporary Command</h3>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
           <?php include '../includes/alert.php'; ?>
        <div class="row">
            <div class="col-md-6">
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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <?php if ($activeComd): ?>
                            <h5>Active Temporary Command Assignment</h5>
                            <div class="alert alert-info">
                                <p>Soldier is currently assigned to Temporary Command: <strong>
                                        <?php echo $activeComd['COMDNAME']; ?>
                                    </strong> since
                                    <?php echo $activeComd['START_DATE']; ?>
                                </p>
                                <form method="post" action="">
                                    <input type="hidden" name="soldier_id" value="<?php echo $soldierID; ?>">
                                    <input type="hidden" name="comd_id" value="<?php echo $activeComd['TEMPCOMD_ID']; ?>">
                                    <button type="submit" name="end_comd_submit" class="btn btn-danger">End Temporary Command</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <h5>Add Temporary Command</h5>
                            <form method="post" action="">
                                <input type="hidden" name="soldier_id" value="<?php echo $soldierID; ?>">
                                <div class="form-group">
                                    <label for="comd_id">Temporary Command:</label>
                                    <select name="comd_id" id="comd_id" class="form-control" required>
                                        <?php foreach ($comdList as $comd): ?>
                                            <option value="<?php echo $comd->ComdID; ?>"><?php echo $comd->ComdName; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" name="add_comd_submit" class="btn btn-primary">Add Temporary Command</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Temporary Command History</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Command Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comdHistoryList as $comdHistory): ?>
                                    <tr>
                                        <td>
                                            <?php echo $comdHistory->ComdName; ?>
                                        </td>
                                        <td>
                                            <?php echo $comdHistory->StartDate; ?>
                                        </td>
                                        <td>
                                            <?php echo $comdHistory->EndDate ? $comdHistory->EndDate : 'NA'; ?>
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
</section>

<?php include '../includes/footer.php'; ?>
