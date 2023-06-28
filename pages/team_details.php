<?php
include 'views/auth.php';
include 'conn.php';

// Get Team ID from the URL
$team_id = $_GET['team_id'];

// Get Team Details
$query = "SELECT * FROM Team WHERE TeamID = :team_id";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':team_id', $team_id);
oci_execute($stmt);

$team = oci_fetch_assoc($stmt);
if (!$team) {
    echo "Team not found.";
    exit;
}

// Get All Soldiers
$query = "SELECT s.SOLDIERID, RANKS.RANK||' '||s.NAME AS NAME, c.CompanyName
FROM Soldier s NATURAL JOIN RANKS
JOIN Company c ON s.CompanyID = c.CompanyID";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$allSoldiers = [];
while ($soldier = oci_fetch_assoc($stmt)) {
    $allSoldiers[] = $soldier;
}

// Get Soldiers Assigned to the Team
$query = "SELECT s.SOLDIERID, RANKS.RANK||' '||s.NAME AS NAME, c.CompanyName, t.TeamName
FROM Soldier s NATURAL JOIN RANKS
JOIN Company c ON s.CompanyID = c.CompanyID
LEFT JOIN SoldierTeam st ON s.SoldierID = st.SoldierID
LEFT JOIN Team t ON st.TeamID = t.TeamID
WHERE st.TeamID = :team_id";

$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':team_id', $team_id);
oci_execute($stmt);

$assignedSoldiers = [];
while ($soldier = oci_fetch_assoc($stmt)) {
    $assignedSoldiers[] = $soldier;
}

// Update Team Soldiers
if (isset($_POST['submit'])) {
    $selectedSoldiers = $_POST['soldiers'];

    // Clear existing assigned soldiers for the team
    $query = "DELETE FROM SoldierTeam WHERE TeamID = :team_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':team_id', $team_id);
    oci_execute($stmt);

    // Assign selected soldiers to the team
    foreach ($selectedSoldiers as $soldierID) {
        $query = "INSERT INTO SoldierTeam (SoldierID, TeamID) VALUES (:soldier_id, :team_id)";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
        oci_bind_by_name($stmt, ':team_id', $team_id);
        oci_execute($stmt);
    }

    // Redirect to the updated team details page
    header("Location: team_details.php?team_id=$team_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Team Details</h1>
                    <p><strong>Team ID:</strong>
                        <?php echo $team['TEAMID']; ?>
                    </p>
                    <p><strong>Team Name:</strong>
                        <?php echo $team['TEAMNAME']; ?>
                    </p>
                    <p><strong>Start Date:</strong>
                        <?php echo $team['STARTDATE']; ?>
                    </p>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Available Soldiers</h5>
                                    <form method="post" action="">
                                    <div class="card-body table-responsive p-0" style="height: 600px;">
                                    <table class="table table-head-fixed text-nowrap">
                                                <thead class="thead-light sticky-top">
                                                    <tr>
                                                        <th>Soldier ID</th>
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
                                                                <?php echo $soldier['NAME']; ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $soldier['COMPANYNAME']; ?>
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


                                        <br>
                                        <input type="submit" name="submit" value="Update Team" class="btn btn-primary">
                                    </form>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Assigned Soldiers</h5>
                                    <?php if (count($assignedSoldiers) > 0): ?>
                                        <div class="table-responsive">
                                            <table id="soldierTable" class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Soldier ID</th>
                                                        <th>Name</th>
                                                        <th>Team Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($assignedSoldiers as $soldier): ?>
                                                        <tr>
                                                            <td>
                                                                <?php echo $soldier['SOLDIERID']; ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $soldier['NAME']; ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $soldier['TEAMNAME']; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <br>
                                        <p>No soldiers assigned to this team.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include 'views/footer.php'; ?>
    </div>
</body>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        $('#soldierTable').DataTable();
    });
</script>


</html>