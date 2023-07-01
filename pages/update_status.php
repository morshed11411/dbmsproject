<?php
session_start();

include '../includes/connection.php';

// Process the form submission to set soldier status
if (isset($_POST['set_status_submit'])) {
    $soldierID = $_POST['soldier_id'];
    $statusID = $_POST['status_id'];

    // Check if the soldier status already exists
    $query = "SELECT SOLDIER_ID FROM SOLDIERSTATUS WHERE SOLDIER_ID = :soldier_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_execute($stmt);
    $existingStatus = oci_fetch_assoc($stmt);

    if ($existingStatus) {
        // Update soldier status
        $query = "UPDATE SOLDIERSTATUS SET STATUSID = :status_id WHERE SOLDIER_ID = :soldier_id";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':status_id', $statusID);
        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
        $result = oci_execute($stmt);

        if ($result) {
            $_SESSION['success'] = "Soldier status updated successfully.";
        } else {
            $error = oci_error($stmt);
            $_SESSION['error'] = "Failed to update soldier status: " . $error['message'];
        }
    } else {
        // Insert new soldier status
        $query = "INSERT INTO SOLDIERSTATUS (SOLDIER_ID, STATUSID) VALUES (:soldier_id, :status_id)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
        oci_bind_by_name($stmt, ':status_id', $statusID);
        $result = oci_execute($stmt);

        if ($result) {
            $_SESSION['success'] = "Soldier status set successfully.";
        } else {
            $error = oci_error($stmt);
            $_SESSION['error'] = "Failed to set soldier status: " . $error['message'];
        }
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: update_status.php?soldier=$soldierID");
    exit();
}

// Fetch soldier ID and name from the query parameter
if (isset($_GET['soldier'])) {
    $soldierID = $_GET['soldier'];

    // Fetch soldier details from the database
    $query = "SELECT SOLDIERID, NAME FROM SOLDIER WHERE SOLDIERID = :soldier_id";
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

// Fetch serving status list from the database
$query = "SELECT STATUSID, SERVINGTYPE FROM SERVINGSTATUS";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$servingStatusList = array();
while ($row = oci_fetch_assoc($stmt)) {
    $servingStatus = new stdClass();
    $servingStatus->StatusID = $row['STATUSID'];
    $servingStatus->ServingType = $row['SERVINGTYPE'];
    $servingStatusList[] = $servingStatus;
}

oci_free_statement($stmt);

$query = "SELECT S.SOLDIER_ID, S.STATUSID, SV.SERVINGTYPE
          FROM SOLDIERSTATUS S
          JOIN SERVINGSTATUS SV ON S.STATUSID = SV.STATUSID
          WHERE S.SOLDIER_ID = :soldier_id";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierID);
oci_execute($stmt);

$currentStatus = oci_fetch_assoc($stmt);

oci_free_statement($stmt);


include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Set Soldier Status</h3>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php include '../includes/alert.php'; ?>
        <?php if ($currentStatus): ?>
                <div class="alert alert-warning">
                    <h3>Current Status:
                        <b><?php echo $currentStatus['SERVINGTYPE']; ?></b>
                    </h3>
                </div>
            <?php endif; ?>
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Set Soldier Status</h5>
                        <form method="post" action="">
                            <input type="hidden" name="soldier_id" value="<?php echo $soldierID; ?>">
                            <div class="form-group">
                                <label for="status_id">Status:</label>
                                <select name="status_id" id="status_id" class="form-control" required>
                                    <?php foreach ($servingStatusList as $status): ?>
                                        <option value="<?php echo $status->StatusID; ?>"><?php echo $status->ServingType; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="set_status_submit" class="btn btn-primary">Set Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>