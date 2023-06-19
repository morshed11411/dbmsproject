<?php
include 'views/auth.php';
include 'conn.php';

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
                    <h1>Insert Team Data</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <form method="post" action="">

                                        <div class="form-group">
                                            <label for="team_name">Team Name:</label>
                                            <input type="text" name="team_name" id="team_name" class="form-control"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label for="start_date">Start Date:</label>
                                            <input type="date" name="start_date" id="start_date" class="form-control"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label for="team_oic">Team OIC:</label>
                                            <input type="text" name="team_oic" id="team_oic" class="form-control"
                                                required>
                                        </div>

                                        <input type="submit" name="submit" value="Submit" class="btn btn-primary">


                                    </form>

                                    <?php


                                    // Insert Team
                                    if (isset($_POST['submit'])) {
                                        $team_id = $_POST['team_id'];
                                        $team_name = $_POST['team_name'];
                                        $start_date = $_POST['start_date'];
                                        $team_oic = $_POST['team_oic'];

                                        $query = "INSERT INTO Team (TeamID, TeamName, StartDate, TeamOIC) VALUES (TEAMIDSEQ.NEXTVAL, :team_name, TO_DATE(:start_date, 'YYYY-MM-DD'), :team_oic)";
                                        $stmt = oci_parse($conn, $query);

                                        oci_bind_by_name($stmt, ':team_id', $team_id);
                                        oci_bind_by_name($stmt, ':team_name', $team_name);
                                        oci_bind_by_name($stmt, ':start_date', $start_date);
                                        oci_bind_by_name($stmt, ':team_oic', $team_oic);

                                        $result = oci_execute($stmt);
                                        if ($result) {
                                            echo "Team data inserted successfully.";
                                        } else {
                                            $e = oci_error($stmt);
                                            if ($e['code'] == 1 && strpos($e['message'], 'SYS_C007204') !== false) {
                                                echo "Failed to insert Team data: The Team ID already exists. Please enter a unique Team ID.";
                                            } else {
                                                echo "Failed to insert Team data: Please enter valid data.";
                                            }
                                        }

                                        oci_free_statement($stmt);
                                    }

                                    // End Team
                                    if (isset($_POST['end_team'])) {
                                        $team_id = $_POST['team_id'];
                                        $end_date = date('Y-m-d');

                                        $query = "UPDATE Team SET EndDate = TO_DATE(:end_date, 'YYYY-MM-DD') WHERE TeamID = :team_id";
                                        $stmt = oci_parse($conn, $query);

                                        oci_bind_by_name($stmt, ':end_date', $end_date);
                                        oci_bind_by_name($stmt, ':team_id', $team_id);

                                        $result = oci_execute($stmt);
                                        if ($result) {
                                            echo "Team ended successfully.";
                                        } else {
                                            echo "Failed to end the team.";
                                        }

                                        oci_free_statement($stmt);
                                    }

                                    // Delete Team
                                    if (isset($_POST['delete_team'])) {
                                        $team_id = $_POST['team_id'];

                                        $query = "DELETE FROM Team WHERE TeamID = :team_id";
                                        $stmt = oci_parse($conn, $query);

                                        oci_bind_by_name($stmt, ':team_id', $team_id);

                                        $result = oci_execute($stmt);
                                        if ($result) {
                                            echo "Team deleted successfully.";
                                        } else {
                                            echo "Failed to delete the team.";
                                        }

                                        oci_free_statement($stmt);
                                    }

                                    $query = "SELECT * FROM Team";
                                    $stmt = oci_parse($conn, $query);
                                    oci_execute($stmt);

                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
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
                                            while ($row = oci_fetch_assoc($stmt)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['TEAMID'] . "</td>";
                                                echo "<td><a href='team_details.php?team_id=" . $row['TEAMID'] . "'>" . $row['TEAMNAME'] . "</a></td>";
                                                echo "<td>" . $row['STARTDATE'] . "</td>";
                                                if ($row['ENDDATE'] === null) {
                                                    echo "<td>Running</td>";
                                                } else {
                                                    echo "<td>" . $row['ENDDATE'] . "</td>";
                                                }
                                                echo "<td>" . $row['TEAMOIC'] . "</td>";
                                                echo "<td>";
                                                echo "<form method='post' action='manage_team.php'>";
                                                echo "<input type='hidden' name='team_id' value='" . $row['TEAMID'] . "'>";
                                                if ($row['ENDDATE'] === null) {
                                                    echo "<button type='submit' name='end_team' class='btn btn-danger'>End Team</button> ";
                                                } else {
                                                    echo "Team Ended ";
                                                }
                                                echo "<button type='submit' name='delete_team' class='btn btn-danger'>Delete</button>";
                                                echo "</form>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
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

</html>