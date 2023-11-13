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




// Process the form submission for updating status in bulk
if (isset($_POST['update_status_bulk'])) {
    $updateResults = $_POST['result'];

    // Loop through each soldier's result and update the status
    foreach ($updateResults as $soldierID => $newStatus) {
        // Update the status in the database
        $query = "UPDATE SOLDIERTRAINING SET STATUS = :new_status WHERE SOLDIERID = :soldier_id AND EVENTID = :event_id";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':new_status', $newStatus);
        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
        oci_bind_by_name($stmt, ':event_id', $event_id);

        if (oci_execute($stmt) === false) {
            // Handle the error (you can customize this based on your needs)
            $_SESSION['error'] = "Error updating status for soldier with ID $soldierID";
            header("Location: training_details.php?event_id=$event_id");
            exit();
        }
    }

    // Redirect back to the page
    $_SESSION['success'] = "Status updated for selected soldiers.";
    header("Location: training_details.php?event_id=$event_id");
    exit();
}

// ... (Rest of your existing code)


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

<!-- Button to trigger modal -->


<!-- Modal for Available Soldiers -->
<div class="modal fade" id="availableSoldiersModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"
    aria-hidden="true">
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

                        <table id="availableSoldiersTable" class="table table-bordered table-head-fixed text-nowrap">

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
                                                <input class="form-check-input" type="checkbox" name="soldiers[]"
                                                    value="<?php echo $soldier['SOLDIERID']; ?>" <?php if (in_array($soldier['SOLDIERID'], array_column($assignedSoldiers, 'SOLDIERID')))
                                                           echo 'checked'; ?>>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Button to submit the form inside the modal -->
                <button type="submit" name="submit" class="btn btn-primary">Assign
                    Soldiers</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php include '../includes/alert.php'; ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        Assigned Soldiers (
                        <?= count($assignedSoldiers) ?>)

                    </div>

                    <div class="card-body">
                        <?php if (count($assignedSoldiers) > 0): ?>
                            <form method="POST" action="">
                                <div class="card-body  table-responsive p-0" style="height: 400px;">
                                    <table id="tablem" class="table table-bordered table-head-fixed text-nowrap">
                                        <thead>
                                            <tr>
                                                <th style="width: 80px;">Soldier ID</th>
                                                <th style="width: 80px;">Rank</th>
                                                <th style="width: 120px;">Name</th>
                                                <th style="width: 120px;">Trade</th>
                                                <th style="width: 120px;">Result</th>
                                                <th class="no-export" style="width:60px;">Pass</th>
                                                <th class="no-export" style="width: 60px;">Fail</th>
                                                <th class="no-export" style="width: 60px;">Incomplete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($assignedSoldiers as $soldier): ?>
                                                <tr>
                                                    <td>
                                                        <?= $soldier['SOLDIERID']; ?>
                                                    </td>
                                                    <td>
                                                        <?= $soldier['RANK']; ?>
                                                    </td>
                                                    <td>
                                                        <?= $soldier['NAME']; ?>
                                                    </td>
                                                    <td>
                                                        <?= $soldier['TRADE']; ?>
                                                    </td>
                                                    <td class="<?= getStatusClass($soldier['STATUS']); ?>">
                                                        <?= $soldier['STATUS']; ?>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input type="radio"
                                                                name="result[<?php echo $soldier['SOLDIERID']; ?>]" value="Pass"
                                                                <?php echo ($soldier['STATUS'] === 'Pass') ? 'checked' : ''; ?>
                                                                required>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input type="radio"
                                                                name="result[<?php echo $soldier['SOLDIERID']; ?>]" value="Fail"
                                                                <?php echo ($soldier['STATUS'] === 'Fail') ? 'checked' : ''; ?>
                                                                required>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input type="radio"
                                                                name="result[<?php echo $soldier['SOLDIERID']; ?>]"
                                                                value="Incomplete" <?php echo ($soldier['STATUS'] === 'Incomplete') ? 'checked' : ''; ?>
                                                                required>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="submit" name="update_status_bulk" class="btn btn-primary">Update
                                    Result</button>
                            </form>
                        <?php else: ?>
                            <p>No soldiers assigned to this training event.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>


<?php include '../includes/footer.php'; ?>

<script>
    $(document).ready(function () {
        var table = $('#tablem').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "paging": false,  // Add this line to disable paging
            "buttons": [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    }
                },
                'colvis'
            ]
        });

        table.buttons().container().appendTo('#tablem_wrapper .col-md-6:eq(0)');
    });
</script>