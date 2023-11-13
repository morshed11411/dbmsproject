<?php
session_start();

include '../includes/connection.php';

// Process the add team action
if (isset($_POST['add_team_submit'])) {
    $team_name = $_POST['team_name'];
    $oic_id = $_POST['team_oic'];
    $start_date = date('Y-m-d'); // Set the start date as the current system date

    $query = "INSERT INTO TEAM (TEAMNAME, STARTDATE, TEAMOIC) VALUES (:team_name, TO_DATE(:start_date, 'YYYY-MM-DD'), :oic_id)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':team_name', $team_name);
    oci_bind_by_name($stmt, ':start_date', $start_date);
    oci_bind_by_name($stmt, ':oic_id', $oic_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Team added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add Team: " . $error['message'];
    }

    oci_free_statement($stmt);
}

// Process the edit team action
if (isset($_POST['edit_team_submit'])) {
    $team_id = $_POST['edit_team_id'];
    $team_name = $_POST['edit_team_name'];
    $oic_id = $_POST['edit_team_oic'];

    $query = "UPDATE TEAM SET TEAMNAME = :team_name, TEAMOIC = :oic_id WHERE TEAMID = :team_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':team_name', $team_name);
    oci_bind_by_name($stmt, ':oic_id', $oic_id);
    oci_bind_by_name($stmt, ':team_id', $team_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Team updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update Team: " . $error['message'];
    }

    oci_free_statement($stmt);
}

// Process the end team action
if (isset($_POST['end_team_submit'])) {
    $team_id = $_POST['team_id'];
    $end_date = date('Y-m-d'); // Set the end date as the current system date

    $query = "UPDATE TEAM SET ENDDATE = TO_DATE(:end_date, 'YYYY-MM-DD') WHERE TEAMID = :team_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':end_date', $end_date);
    oci_bind_by_name($stmt, ':team_id', $team_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Team ended successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to end Team: " . $error['message'];
    }

    oci_free_statement($stmt);
    header("Location: team.php");
    exit();
}

// Process the delete team action
if (isset($_POST['delete_team_submit'])) {
    $team_id = $_POST['delete_team_id'];

    $query = "DELETE FROM TEAM WHERE TEAMID = :team_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':team_id', $team_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Team deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete Team: " . $error['message'];
    }

    oci_free_statement($stmt);
}

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Team List Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addTeamModal">Add Team</button>
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
                        <h5>Running Teams</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Team ID</th>
                                    <th>Team Name</th>
                                    <th>Start Date</th>
                                    <th>Team OIC</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT T.TEAMID, T.TEAMNAME, T.STARTDATE, R.RANK || ' ' || S.NAME AS OIC
                                FROM TEAM T
                                JOIN SOLDIER S ON T.TEAMOIC = S.SOLDIERID
                                JOIN RANKS R ON S.RANKID = R.RANKID
                                WHERE T.ENDDATE IS NULL
                                ORDER BY T.TEAMID";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);

                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['TEAMID'] . "</td>";
                                    echo '<td><a href="assign_team.php?teamid=' . $row['TEAMID'] . '">' . $row['TEAMNAME'] . '</a></td>';
                                    echo "<td>" . $row['STARTDATE'] . "</td>";
                                    echo "<td>" . $row['OIC'] . "</td>";
                                    echo "<td>";
                                    echo "<button type='button' class='btn btn-danger' data-toggle='modal' data-target='#endTeamModal-" . $row['TEAMID'] . "'>End Team</button>";
                                    echo "</td>";
                                    echo "</tr>";

                                    // End Team Modal
                                    echo '<div class="modal fade" id="endTeamModal-' . $row['TEAMID'] . '" tabindex="-1" role="dialog" aria-labelledby="endTeamModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="endTeamModalLabel">End Team</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to end this Team?</p>
                                                <form method="POST" action="">
                                                    <input type="hidden" name="team_id" value="' . $row['TEAMID'] . '">
                                                    <button type="submit" name="end_team_submit" class="btn btn-danger">End Team</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>';

                                }

                                oci_free_statement($stmt);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Previous Teams</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Team ID</th>
                                    <th>Team Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Team OIC</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT T.TEAMID, T.TEAMNAME, T.STARTDATE, T.ENDDATE, R.RANK || ' ' || S.NAME AS OIC
                                FROM TEAM T
                                JOIN SOLDIER S ON T.TEAMOIC = S.SOLDIERID
                                JOIN RANKS R ON S.RANKID = R.RANKID
                                WHERE T.ENDDATE IS NOT NULL
                                ORDER BY T.TEAMID";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);

                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['TEAMID'] . "</td>";
                                    echo '<td><a href="assign_team.php?teamid=' . $row['TEAMID'] . '">' . $row['TEAMNAME'] . '</a></td>';
                                    echo "<td>" . $row['STARTDATE'] . "</td>";
                                    echo "<td>" . ($row['ENDDATE'] ? $row['ENDDATE'] : 'Running') . "</td>";
                                    echo "<td>" . $row['OIC'] . "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editTeamModal-' . $row['TEAMID'] . '">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>';
                                    echo '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteTeamModal-' . $row['TEAMID'] . '">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>';
                                    echo "</td>";
                                    echo "</tr>";

                                    // Edit Team Modal
                                    echo '<div class="modal fade" id="editTeamModal-' . $row['TEAMID'] . '" tabindex="-1" role="dialog" aria-labelledby="editTeamModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editTeamModalLabel">Edit Team</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_team_id" value="' . $row['TEAMID'] . '">
                                                        <div class="form-group">
                                                            <label for="edit_team_name">Team Name:</label>
                                                            <input type="text" name="edit_team_name" id="edit_team_name" class="form-control" value="' . $row['TEAMNAME'] . '" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="edit_team_oic">Team OIC:</label>
                                                            <select name="edit_team_oic" id="edit_team_oic" class="form-control" required>';
                                    $oic_query = "SELECT R.RANK || ' ' || S.NAME AS OIC, S.SOLDIERID
                                            FROM SOLDIER S
                                            JOIN RANKS R ON S.RANKID = R.RANKID";
                                    $oic_stmt = oci_parse($conn, $oic_query);
                                    oci_execute($oic_stmt);

                                    while ($oic_row = oci_fetch_assoc($oic_stmt)) {
                                        echo '<option value="' . $oic_row['SOLDIERID'] . '"';
                                        if ($oic_row['SOLDIERID'] == $row['TEAMOIC']) {
                                            echo ' selected';
                                        }
                                        echo '>' . $oic_row['OIC'] . '</option>';
                                    }

                                    oci_free_statement($oic_stmt);

                                    echo '</select>
                                                        </div>
                                                        <button type="submit" name="edit_team_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';

                                    // Delete Team Modal
                                    echo '<div class="modal fade" id="deleteTeamModal-' . $row['TEAMID'] . '" tabindex="-1" role="dialog" aria-labelledby="deleteTeamModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteTeamModalLabel">Delete Team</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this Team?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_team_id" value="' . $row['TEAMID'] . '">
                                                        <button type="submit" name="delete_team_submit" class="btn btn-danger">Delete Team</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                }

                                oci_free_statement($stmt);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End Team Modal -->
<div class="modal fade" id="endTeamModal-<?php echo $row['TEAMID']; ?>" tabindex="-1" role="dialog"
    aria-labelledby="endTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="endTeamModalLabel">End Team</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to end this Team?</p>
                <form method="POST" action="">
                    <input type="hidden" name="team_id" value="<?php echo $row['TEAMID']; ?>">
                    <button type="submit" name="end_team_submit" class="btn btn-danger">End Team</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Team Modal -->
<div class="modal fade" id="addTeamModal" tabindex="-1" role="dialog" aria-labelledby="addTeamModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTeamModalLabel">Add Team</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="team_name">Team Name:</label>
                        <input type="text" name="team_name" id="team_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="team_oic">Team OIC:</label>
                        <select name="team_oic" id="team_oic" class="form-control" required>
                            <?php
                            $oic_query = "SELECT R.RANK || ' ' || S.NAME AS OIC, S.SOLDIERID
                                FROM SOLDIER S
                                JOIN RANKS R ON S.RANKID = R.RANKID";
                            $oic_stmt = oci_parse($conn, $oic_query);
                            oci_execute($oic_stmt);

                            while ($oic_row = oci_fetch_assoc($oic_stmt)) {
                                echo '<option value="' . $oic_row['SOLDIERID'] . '">' . $oic_row['OIC'] . '</option>';
                            }

                            oci_free_statement($oic_stmt);
                            oci_close($conn);

                            ?>
                        </select>
                    </div>
                    <input type="submit" name="add_team_submit" value="Add Team" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>