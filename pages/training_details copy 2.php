<?php
session_start();
include '../includes/connection.php';

// Check if event ID is provided in the URL
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Retrieve event details
    $query = "SELECT TE.EVENTID, TE.EVENTNAME, TE.EVENTDATE, TE.STATUS, BT.TRGNAME
              FROM TRAININGEVENT TE
              JOIN BASICTRAINING BT ON TE.TRGID = BT.TRGID
              WHERE TE.EVENTID = :event_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':event_id', $event_id);
    oci_execute($stmt);

    $event = oci_fetch_assoc($stmt);

    if (!$event) {
        $_SESSION['error'] = "Training event not found.";
        header("Location: training_details.php");
        exit;
    }

    // Retrieve all soldiers
    $query = "SELECT S.SOLDIERID, R.RANK, S.NAME, T.TRADE, C.COMPANYNAME
              FROM SOLDIER S
              JOIN RANKS R ON S.RANKID = R.RANKID
              JOIN TRADE T ON S.TRADEID = T.TRADEID
              JOIN COMPANY C ON S.COMPANYID = C.COMPANYID";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);

    $allSoldiers = [];
    while ($soldier = oci_fetch_assoc($stmt)) {
        $allSoldiers[] = $soldier;
    }

    // Retrieve soldiers assigned to the training event
    $query = "SELECT S.SOLDIERID, R.RANK, S.NAME, T.TRADE, C.COMPANYNAME, ST.STATUS
              FROM SOLDIER S
              JOIN SOLDIERTRAINING ST ON S.SOLDIERID = ST.SOLDIERID
              JOIN RANKS R ON S.RANKID = R.RANKID
              JOIN TRADE T ON S.TRADEID = T.TRADEID
              JOIN COMPANY C ON S.COMPANYID = C.COMPANYID
              WHERE ST.EVENTID = :event_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':event_id', $event_id);
    oci_execute($stmt);

    $assignedSoldiers = [];
    while ($soldier = oci_fetch_assoc($stmt)) {
        $assignedSoldiers[] = $soldier;
    }
}

// Process the form submission
if (isset($_POST['submit'])) {
    $selectedSoldiers = $_POST['soldiers'];

    // Clear existing assigned soldiers for the training event
    $query = "DELETE FROM SOLDIERTRAINING WHERE EVENTID = :event_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':event_id', $event_id);
    oci_execute($stmt);

    // Assign selected soldiers to the training event
    foreach ($selectedSoldiers as $soldierID) {
        $query = "INSERT INTO SOLDIERTRAINING (SOLDIERID, EVENTID, STATUS) VALUES (:soldier_id, :event_id, 'Appeared')";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
        oci_bind_by_name($stmt, ':event_id', $event_id);
        oci_execute($stmt);
    }

    $_SESSION['success'] = "Soldiers assigned to the training event successfully.";

    // Redirect back to the assign_training_event.php page
    header("Location: training_details.php?event_id=$event_id");
    exit();
}



// Process the form submission for updating status
if (isset($_POST['update_status'])) {
    $soldier_id = $_POST['soldier_id'];
    $new_status = $_POST['status'];

    // Update the status in the database
    $query = "UPDATE SOLDIERTRAINING SET STATUS = :new_status WHERE SOLDIERID = :soldier_id AND EVENTID = :event_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':new_status', $new_status);
    oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
    oci_bind_by_name($stmt, ':event_id', $event_id);
    oci_execute($stmt);

    // Redirect back to the page
    header("Location: training_details.php?event_id=$event_id");
    exit();
}

function getStatusClass($status)
{
    switch ($status) {
        case 'Pass':
            return 'text-success';
        case 'Fail':
            return 'text-danger';
        case 'Incomplete':
            return 'text-warning';
        default:
            return 'text-muted'; // Use Bootstrap's muted class for other statuses
    }
}



include '../includes/header.php';
?>












<div class="card-body">

    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>
                <?php echo $event['EVENTNAME']; ?>
            </h3>

        </div>
        <div class="text-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#availableSoldiersModal">
                Assign Soldiers
            </button>
        </div>
    </div>
</div>

<?php include '../includes/alert.php'; ?>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">

                    <!-- Button to trigger modal -->


                    <!-- Modal for Available Soldiers -->
                    <div class="modal fade" id="availableSoldiersModal" tabindex="-1" role="dialog"
                        aria-labelledby="modalTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalTitle">Available Soldiers</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Available soldiers table -->
                                    <div class="card-body table-responsive p-0" style="height: 400px; overflow: auto;">
                                        <!-- Added "overflow: auto;" -->
                                        <form method="POST" action="">

                                            <table id="availableSoldiersTable"
                                                class="table table-bordered table-head-fixed text-nowrap">

                                                <thead>
                                                    <tr>
                                                        <th>Soldier ID</th>
                                                        <th>Rank</th>
                                                        <th>Name</th>
                                                        <th>Select</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($allSoldiers as $soldier): ?>
                                                        <tr>
                                                            <td>
                                                                <?php echo $soldier['SOLDIERID']; ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $soldier['RANK']; ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $soldier['NAME']; ?>
                                                            </td>
                                                            <td>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="soldiers[]"
                                                                        value="<?php echo $soldier['SOLDIERID']; ?>" <?php if (in_array($soldier['SOLDIERID'], array_column($assignedSoldiers, 'SOLDIERID')))
                                                                               echo 'checked'; ?>>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                    </div>
                                    <div class="modal-footer">
                                        <!-- Button to submit the form inside the modal -->
                                        <button type="submit" name="submit" class="btn btn-primary">Assign
                                            Soldiers</button>
                                        </form>
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h5>Assigned Soldiers (
                        <?= count($assignedSoldiers) ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (count($assignedSoldiers) > 0): ?>
                        <div class="card-body table-responsive p-0" style="height: 400px; overflow: auto;">
                            <!-- Added "overflow: auto;" -->
                            <table id="tablex" class="table table-bordered table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Soldier ID</th>
                                        <th>Rank</th>
                                        <th>Name</th>
                                        <th>Trade</th>
                                        <th>Result</th>
                                        <th class="no-export">Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignedSoldiers as $soldier): ?>
                                        <tr>
                                            <td>
                                                <?php echo $soldier['SOLDIERID']; ?>
                                            </td>
                                            <td>
                                                <?php echo $soldier['RANK']; ?>
                                            </td>
                                            <td>
                                                <?php echo $soldier['NAME']; ?>
                                            </td>
                                            <td>
                                                <?php echo $soldier['TRADE']; ?>
                                            </td>


                                            <td class="<?php echo getStatusClass($soldier['STATUS']); ?>">
                                                <?php echo $soldier['STATUS']; ?>
                                            </td>

                                            <td>
                                                <!-- Add a button to trigger the modal for updating status -->
                                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                                    data-target="#updateStatusModal<?php echo $soldier['SOLDIERID']; ?>">
                                                    Update Result
                                                </button>

                                                <!-- Modal for updating status -->
                                                <div class="modal fade"
                                                    id="updateStatusModal<?php echo $soldier['SOLDIERID']; ?>" tabindex="-1"
                                                    role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="modalTitle">Update Result
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- Form to update soldier status -->
                                                                <form method="POST" action="">
                                                                    <input type="hidden" name="soldier_id"
                                                                        value="<?php echo $soldier['SOLDIERID']; ?>">
                                                                    <div class="form-group">
                                                                        <label for="status">Update Result:</label>
                                                                        <select class="form-control" name="status">
                                                                            <option value="Pass">Pass</option>
                                                                            <option value="Fail">Fail</option>
                                                                            <option value="Incomplete">Incomplete</option>
                                                                        </select>
                                                                    </div>
                                                                    <button type="submit" name="update_status"
                                                                        class="btn btn-primary">Update Status</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                    <?php else: ?>
                        <p>No soldiers assigned to this training event.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</section>

<?php include '../includes/footer.php'; ?>