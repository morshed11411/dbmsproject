<?php
// delete_soldier.php

if (isset($_GET['soldierId'])) {
    $soldierId = $_GET['soldierId'];

    // Perform the delete operation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmation']) && $_POST['confirmation'] === 'yes') {
        $conn = oci_connect('UMS', '12345', 'localhost/XE');
        if (!$conn) {
            $e = oci_error();
            echo "Failed to connect to Oracle: " . $e['message'];
            exit;
        }

        $query = "DELETE FROM Soldier WHERE SOLDIERID = :soldierId";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':soldierId', $soldierId);

        $result = oci_execute($stmt);
        if ($result) {
            echo "Soldier deleted successfully.";
            header("Location: dashboard.php"); // Redirect back to the soldiers.php page
        } else {
            $e = oci_error($stmt);
            echo "Failed to delete soldier: " . $e['message'];
        }

        oci_free_statement($stmt);
        oci_close($conn);
    } else {
        // Display the confirmation form
        ?>

        <?php include 'views/auth.php'; ?>

        <!DOCTYPE html>
        <html lang="en">
        <?php include 'views/head.php'; ?>

        <body>
            <div class="wrapper">
                <?php include 'views/navbar.php'; ?>

                <div class="content-wrapper">
                    <div class="content-header">
                        <div class="container-fluid">
                            <h1>Delete Soldier</h1>
                        </div>
                    </div>
                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <form method="post" action="">
                                                <div class="form-group">
                                                    <label for="confirmation">Type 'yes' to confirm deletion:</label>
                                                    <input type="text" name="confirmation" id="confirmation" class="form-control" required>
                                                </div>
                                                <input type="submit" value="Delete" class="btn btn-danger">
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

        <?php
    }
} else {
    echo "Invalid request. Soldier ID not provided.";
    exit;
}
?>
