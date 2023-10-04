<?php
session_start();


include '../includes/connection.php';



// Process the form submission to add ERE
if (isset($_POST['add_ere_submit'])) {
    $soldierID = $_POST['soldier_id'];
    $ereID = $_POST['ere_id'];
    $startDate = date('Y-m-d'); // Set the start date as the current system date

    // Insert ERE assignment into the database
    $query = "INSERT INTO SOLDIERERE (SOLDIER_ID, ERE_ID, START_DATE) VALUES (:soldier_id, :ere_id, TO_DATE(:start_date, 'YYYY-MM-DD'))";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_bind_by_name($stmt, ':ere_id', $ereID);
    oci_bind_by_name($stmt, ':start_date', $startDate);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "ERE added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add ERE: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: add_ere.php?soldier=$soldierID");
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

// Fetch ERE list from the database
$query = "SELECT EREID, ERENAME FROM ERE";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$ereList = array();
while ($row = oci_fetch_assoc($stmt)) {
    $ere = new stdClass();
    $ere->EreID = $row['EREID'];
    $ere->EreName = $row['ERENAME'];
    $ereList[] = $ere;
}


oci_free_statement($stmt);


// Process the end ERE action
if (isset($_POST['end_ere_submit'])) {
    $soldierID = $_POST['soldier_id'];
    $ereID = $_POST['ere_id'];

    // Set the end date as the current system date
    $endDate = date('Y-m-d');

    // Update the end date for the ERE assignment
    $query = "UPDATE SOLDIERERE SET END_DATE = TO_DATE(:end_date, 'YYYY-MM-DD') WHERE SOLDIER_ID = :soldier_id AND ERE_ID = :ere_id AND END_DATE IS NULL";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':end_date', $endDate);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_bind_by_name($stmt, ':ere_id', $ereID);

    $result = oci_execute($stmt);
    $rowCount = oci_num_rows($stmt);

    if ($result && $rowCount > 0) {
        $_SESSION['success'] = "ERE ended successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to end ERE: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: add_ere.php?soldier=$soldierID");
    exit();
}

// Fetch active ERE assignment for the soldier
$query = "SELECT S.ERE_ID, E.ERENAME, S.START_DATE
          FROM SOLDIERERE S
          JOIN ERE E ON S.ERE_ID = E.EREID
          WHERE S.SOLDIER_ID = :soldier_id AND S.END_DATE IS NULL";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierID);
oci_execute($stmt);

$activeERE = oci_fetch_assoc($stmt);

// Fetch ERE history for the soldier
$query = "SELECT E.ERENAME, S.START_DATE, S.END_DATE 
          FROM SOLDIERERE S 
          JOIN ERE E ON S.ERE_ID = E.EREID 
          WHERE S.SOLDIER_ID = :soldier_id ORDER BY ID DESC";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierID);
oci_execute($stmt);

$ereHistoryList = array();
while ($row = oci_fetch_assoc($stmt)) {
    $ereHistory = new stdClass();
    $ereHistory->EreName = $row['ERENAME'];
    $ereHistory->StartDate = $row['START_DATE'];
    $ereHistory->EndDate = $row['END_DATE'];
    $ereHistoryList[] = $ereHistory;
}

oci_free_statement($stmt);
oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Add/End ERE</h3>
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
                        <?php if ($activeERE): ?>
                            <h5>Active ERE Assignment</h5>
                            <div class="alert alert-info">
                                <p>Soldier is currently assigned to ERE: <strong>
                                        <?php echo $activeERE['ERENAME']; ?>
                                    </strong> since
                                    <?php echo $activeERE['START_DATE']; ?>
                                </p>
                                <form method="post" action="">
                                    <input type="hidden" name="soldier_id" value="<?php echo $soldierID; ?>">
                                    <input type="hidden" name="ere_id" value="<?php echo $activeERE['ERE_ID']; ?>">
                                    <button type="submit" name="end_ere_submit" class="btn btn-danger">End ERE</button>
                                </form>

                            </div>
                        <?php else: ?>
                            <h5>Add ERE</h5>
                            <form method="post" action="">
                                <input type="hidden" name="soldier_id" value="<?php echo $soldierID; ?>">
                                <div class="form-group">
                                    <label for="ere_id">ERE:</label>
                                    <select name="ere_id" id="ere_id" class="form-control" required>
                                        <?php foreach ($ereList as $ere): ?>
                                            <option value="<?php echo $ere->EreID; ?>"><?php echo $ere->EreName; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" name="add_ere_submit" class="btn btn-primary">Add ERE</button>
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
                        <h5>ERE History</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ERE Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ereHistoryList as $ereHistory): ?>
                                    <tr>
                                        <td>
                                            <?php echo $ereHistory->EreName; ?>
                                        </td>
                                        <td>
                                            <?php echo $ereHistory->StartDate; ?>
                                        </td>
                                        <td>
                                            <?php echo $ereHistory->EndDate ? $ereHistory->EndDate : 'NA'; ?>
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