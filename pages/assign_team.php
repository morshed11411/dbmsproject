<?php
session_start();
include '../includes/connection.php';

// Check if team ID is provided in the URL
if (isset($_GET['teamid'])) {
    $team_id = $_GET['teamid'];

    // Retrieve team details
    $query = "SELECT T.TEAMID, T.TEAMNAME, T.TEAMOIC, COUNT(ST.SOLDIERID) AS TOTAL_SOLDIERS
    FROM TEAM T
    LEFT JOIN SOLDIERTEAM ST ON T.TEAMID = ST.TEAMID
    WHERE T.TEAMID = :team_id
    GROUP BY T.TEAMID, T.TEAMNAME, T.TEAMOIC";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':team_id', $team_id);
    oci_execute($stmt);

    $team = oci_fetch_assoc($stmt);

    if (!$team) {
        echo "Team not found.";
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

    // Retrieve assigned soldiers for the team
    $query = "SELECT S.SOLDIERID, R.RANK, S.NAME, T.TRADE, C.COMPANYNAME
              FROM SOLDIER S
              JOIN SOLDIERTEAM ST ON S.SOLDIERID = ST.SOLDIERID
              JOIN RANKS R ON S.RANKID = R.RANKID
              JOIN TRADE T ON S.TRADEID = T.TRADEID
              JOIN COMPANY C ON S.COMPANYID = C.COMPANYID
              WHERE ST.TEAMID = :team_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':team_id', $team_id);
    oci_execute($stmt);

    $assignedSoldiers = [];
    while ($soldier = oci_fetch_assoc($stmt)) {
        $assignedSoldiers[] = $soldier;
    }
}

// Process the form submission
if (isset($_POST['submit'])) {
    $selectedSoldiers = $_POST['soldiers'];

    // Clear existing assigned soldiers for the team
    $query = "DELETE FROM SOLDIERTEAM WHERE TEAMID = :team_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':team_id', $team_id);
    oci_execute($stmt);

    // Assign selected soldiers to the team
    foreach ($selectedSoldiers as $soldierID) {
        $query = "INSERT INTO SOLDIERTEAM (SOLDIERID, TEAMID) VALUES (:soldier_id, :team_id)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
        oci_bind_by_name($stmt, ':team_id', $team_id);
        oci_execute($stmt);
    }

    $_SESSION['success'] = "Team soldiers updated successfully.";

    // Redirect back to the assign_team.php page
    header("Location: assign_team.php?teamid=$team_id");
    exit();
}

include '../includes/header.php';
?>

<div class="card-body">
<h3>Assign Team</h3>

    <div class="d-flex justify-content-between">
    <div class="text-left">

            <p><strong>Team ID:</strong>
                <?php echo $team['TEAMID']; ?>
            </p>
            <p><strong>Team Name:</strong>
                <?php echo $team['TEAMNAME']; ?>
            </p>
            </div>
            <div class="text-right">

            <p><strong>OIC Name:</strong>
                <?php echo $team['TEAMOIC']; ?>
            </p>
            <p><strong>Total Assigned Soldiers:</strong>
                <?php echo $team['TOTAL_SOLDIERS']; ?>
            </p>
            </div>
    </div>
</div>

<?php
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
?>

<section class="content">
    <div class="container-fluid">
           <?php include '../includes/alert.php'; ?>
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
                                        <th>Rank</th>
                                        <th>Trade</th>
                                        <th>Name</th>
                                        <th>Company</th>
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
                                                <?php echo $soldier['TRADE']; ?>
                                            </td>
                                            <td>
                                                <?php echo $soldier['NAME']; ?>
                                            </td>

                                            <td>
                                                <?php echo $soldier['COMPANYNAME']; ?>
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
                            <button type="submit" name="submit" class="btn btn-primary">Update Team</button>
                        </form>

                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Assigned Soldiers</h5>
                        <?php if (count($assignedSoldiers) > 0): ?>
                            <div class="table-responsive" style="max-height: 450px; overflow: hidden scroll;">
                                <table id="assigned-soldiers-table" class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th>Soldier ID</th>
                                            <th>Rank</th>
                                            <th>Name</th>
                                            <th>Trade</th>
                                            <th>Company</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($assignedSoldiers as $soldier): ?>
                                            <tr>
                                                <td><?php echo $soldier['SOLDIERID']; ?></td>
                                                <td><?php echo $soldier['RANK']; ?></td>
                                                <td><?php echo $soldier['NAME']; ?></td>
                                                <td><?php echo $soldier['TRADE']; ?></td>
                                                <td><?php echo $soldier['COMPANYNAME']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>No soldiers assigned to this team.</p>
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
        // Initialize DataTables
        $('#available-soldiers-table').DataTable({
            "responsive": true,

            "paging": false,
            "searching": true,
            "info": false
        });

        $('#assigned-soldiers-table').DataTable({
            "responsive": true,

            "paging": false,
            "searching": true,
            "info": false
        });
    });
</script>