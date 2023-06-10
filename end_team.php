<?php
include 'views/auth.php';
include 'conn.php';

if (isset($_GET['team_id'])) {
    $team_id = $_GET['team_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $query = "UPDATE Team SET EndDate = SYSDATE WHERE TeamID = :team_id";
        $stmt = oci_parse($conn, $query);

        oci_bind_by_name($stmt, ':team_id', $team_id);

        $result = oci_execute($stmt);
        if ($result) {
            echo "Team ended successfully.";
        } else {
            echo "Failed to end team.";
        }

        oci_free_statement($stmt);
    }
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
                                        <input type="submit" name="submit" value="End Team" class="btn btn-primary">
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
