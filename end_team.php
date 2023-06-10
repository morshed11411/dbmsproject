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
                    <h1>End Team</h1>
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
                                            <label for="end_date">End Date:</label>
                                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                                        </div>

                                        <input type="submit" name="submit" value="Update" class="btn btn-primary">
                                        <?php 
                                        if (isset($_GET['team_id'])) {
                                            $team_id = $_GET['team_id'];
                                        
                                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                                $end_date = $_POST['end_date'];
                                        
                                                $query = "UPDATE Team SET EndDate = TO_DATE(:end_date, 'YYYY-MM-DD') WHERE TeamID = :team_id";
                                                $stmt = oci_parse($conn, $query);
                                        
                                                oci_bind_by_name($stmt, ':end_date', $end_date);
                                                oci_bind_by_name($stmt, ':team_id', $team_id);
                                        
                                                $result = oci_execute($stmt);
                                                if ($result) {
                                                    echo "Team end date updated successfully.";
                                                } else {
                                                    echo "Failed to update team end date.";
                                                }
                                        
                                                oci_free_statement($stmt);
                                            }
                                        }
                                        ?>
                                        
                                    </form>
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
