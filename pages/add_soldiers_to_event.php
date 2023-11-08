<?php
session_start();
include '../includes/connection.php';

// Check if event ID is provided in the URL
if (isset($_GET['eventid'])) {
    $event_id = $_GET['eventid'];

    // Retrieve event details
    $query = "SELECT EVENTID, TRAININGTYPE, EVENTDATE, BOARDPRESIDENT, AUTHORITYLETTERNO, BOARDNO
              FROM TRAININGEVENT
              WHERE EVENTID = :event_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':event_id', $event_id);
    oci_execute($stmt);

    $event = oci_fetch_assoc($stmt);

    if (!$event) {
        $_SESSION['error'] = "Training event not found.";
        header("Location: training_events.php");
        exit;
    }

    // Retrieve all soldiers
    $query = "SELECT SOLDIERID, NAME, RANK, TRADE, COMPANY
              FROM SOLDIER";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);

    $allSoldiers = [];
    while ($soldier = oci_fetch_assoc($stmt)) {
        $allSoldiers[] = $soldier;
    }

    // Retrieve soldiers added to the event
    $query = "SELECT S.SOLDIERID, S.NAME, S.RANK, S.TRADE, S.COMPANY
              FROM SOLDIER S
              JOIN SOLDIERTRAINING ST ON S.SOLDIERID = ST.SOLDIERID
              WHERE ST.EVENTID = :event_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':event_id', $event_id);
    oci_execute($stmt);

    $addedSoldiers = [];
    while ($soldier = oci_fetch_assoc($stmt)) {
        $addedSoldiers[] = $soldier;
    }
}

// Process the form submission
if (isset($_POST['submit'])) {
    $selectedSoldiers = $_POST['soldiers'];

    // Clear existing soldiers added to the event
    $query = "DELETE FROM SOLDIERTRAINING WHERE EVENTID = :event_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':event_id', $event_id);
    oci_execute($stmt);

    // Add selected soldiers to the event
    foreach ($selectedSoldiers as $soldierID) {
        $query = "INSERT INTO SOLDIERTRAINING (EVENTID, SOLDIERID, STATUS) VALUES (:event_id, :soldier_id, 'Pending')";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':event_id', $event_id);
        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
        oci_execute($stmt);
    }

    $_SESSION['success'] = "Soldiers added to the training event successfully.";

    // Redirect back to the add_soldiers_to_event.php page
    header("Location: add_soldiers_to_event.php?eventid=$event_id");
    exit();
}

include '../includes/header.php';
?>

<div class="container">
    <h2>Add Soldiers to Training Event</h2>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    ?>
    <?php
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    ?>

    <!-- Event Details -->
    <div class="card">
        <div class="card-body">
            <h5>Event Details</h5>
            <table class="table">
                <tr>
                    <th>Event ID:</th>
                    <td><?php echo $event['EVENTID']; ?></td>
                    <th>Training Type:</th>
                    <td><?php echo $event['TRAININGTYPE']; ?></td>
                </tr>
                <tr>
                    <th>Event Date:</th>
                    <td><?php echo $event['EVENTDATE']; ?></td>
                    <th>Board President:</th>
                    <td><?php echo $event['BOARDPRESIDENT']; ?></td>
                </tr>
                <tr>
                    <th>Authority Letter No:</th>
                    <td><?php echo $event['AUTHORITYLETTERNO']; ?></td>
                    <th>Board No:</th>
                    <td><?php echo $event['BOARDNO']; ?></td>
                </tr>
            </table>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Available Soldiers</h5>

                            <form method="POST" action="">
                                <div class="table-responsive" style="max-height: 450px; overflow: hidden scroll;">
                                    <table id="available-soldiers-table" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Soldier ID</th>
                                                <th>Name</th>
                                                <th>Rank</th>
                                                <th>Trade</th>
                                                <th>Company</th>
                                                <th>Select</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($allSoldiers as $soldier): ?>
                                                <tr>
                                                    <td><?php echo $soldier['SOLDIERID']; ?></td>
                                                    <td><?php echo $soldier['NAME']; ?></td>
                                                    <td><?php echo $soldier['RANK']; ?></td>
                                                    <td><?php echo $soldier['TRADE']; ?></td>
                                                    <td><?php echo $soldier['COMPANY']; ?></td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="soldiers[]"
                                                                value="<?php echo $soldier['SOLDIERID']; ?>" <?php if (in_array($soldier['SOLDIERID'], array_column($addedSoldiers, 'SOLDIERID')))
                                                                       echo 'checked'; ?>>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary">Add Soldiers</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Soldiers Added to Event</h5>
                            <?php if (count($addedSoldiers) > 0): ?>
                                <div class="table-responsive" style="max-height: 450px; overflow: hidden scroll;">
                                    <table id="added-soldiers-table" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Soldier ID</th>
                                                <th>Name</th>
                                                <th>Rank</th>
                                                <th>Trade</th>
                                                <th>Company</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($addedSoldiers as $soldier): ?>
                                                <tr>
                                                    <td><?php echo $soldier['SOLDIERID']; ?></td>
                                                    <td><?php echo $soldier['NAME']; ?></td>
                                                    <td><?php echo $soldier['RANK']; ?></td>
                                                    <td><?php echo $soldier['TRADE']; ?></td>
                                                    <td><?php echo $soldier['COMPANY']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>No soldiers added to this event.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>

<script>
    $(document).ready(function () {
        // Initialize DataTables
        $('#available-soldiers-table').DataTable({
            "responsive": true,
            "paging": false,
            "searching": true,
            "info": false
        });

        $('#added-soldiers-table').DataTable({
            "responsive": true,
            "paging": false,
            "searching": true,
            "info": false
        });
    });
</script>
