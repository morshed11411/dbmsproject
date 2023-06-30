<?php
session_start();

include '../includes/connection.php';

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

// Process the form submission to give punishment
if (isset($_POST['give_punishment_submit'])) {
    $punishment = $_POST['punishment'];
    $reason = $_POST['reason'];
    $punishmentDate = $_POST['punishment_date'];

    // Insert punishment into the database
    $query = "INSERT INTO PUNISHMENT (SOLDIERID, PUNISHMENT, REASON, PUNISHMENTDATE)
              VALUES (:soldier_id, :punishment, :reason, TO_DATE(:punishment_date, 'YYYY-MM-DD'))";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_bind_by_name($stmt, ':punishment', $punishment);
    oci_bind_by_name($stmt, ':reason', $reason);
    oci_bind_by_name($stmt, ':punishment_date', $punishmentDate);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Punishment given successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to give punishment: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: soldiers.php");
    exit();
}

// Fetch punishment history for the soldier
$query = "SELECT * FROM PUNISHMENT WHERE SOLDIERID = :soldier_id";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierID);
oci_execute($stmt);

$punishmentList = array();
while ($row = oci_fetch_assoc($stmt)) {
    $punishment = new stdClass();
    $punishment->PunishmentID = $row['PUNISHMENTID'];
    $punishment->Punishment = $row['PUNISHMENT'];
    $punishment->Reason = $row['REASON'];
    $punishment->PunishmentDate = $row['PUNISHMENTDATE'];
    $punishmentList[] = $punishment;
}

oci_free_statement($stmt);
oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Give Punishment</h3>
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
                                    <td><?php echo $soldier['SOLDIERID']; ?></td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td><?php echo $soldier['NAME']; ?></td>
                                </tr>
                                <tr>
                                    <th>Company</th>
                                    <td><?php echo $soldier['COMPANYNAME']; ?></td>
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
                        <h5>Give Punishment</h5>
                        <form method="post" action="">
                            <input type="hidden" name="soldier_id" value="<?php echo $soldierID; ?>">
                            <div class="form-group">
                                <label for="punishment">Punishment:</label>
                                <select name="punishment" id="punishment" class="form-control" required>
                                    <option value="28 Days RI">28 Days RI</option>
                                    <option value="14 Days RI">14 Days RI</option>
                                    <option value="Warning">Warning</option>
                                    <option value="CL">CL</option>
                                    <option value="Reprimand">Reprimand</option>
                                    <option value="Severe Reprimand">Severe Reprimand</option>
                                    <option value="Good conduct Badge Pay">Good conduct Badge Pay</option>
                                    <option value="Extra Duty">Extra Duty</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="reason">Reason:</label>
                                <input type="text" name="reason" id="reason" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="punishment_date">Punishment Date:</label>
                                <input type="date" name="punishment_date" id="punishment_date" class="form-control" required>
                            </div>
                            <button type="submit" name="give_punishment_submit" class="btn btn-primary">Give Punishment</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Punishment History</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Punishment ID</th>
                                    <th>Punishment</th>
                                    <th>Reason</th>
                                    <th>Punishment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($punishmentList as $punishment): ?>
                                    <tr>
                                        <td><?php echo $punishment->PunishmentID; ?></td>
                                        <td><?php echo $punishment->Punishment; ?></td>
                                        <td><?php echo $punishment->Reason; ?></td>
                                        <td><?php echo $punishment->PunishmentDate; ?></td>
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
