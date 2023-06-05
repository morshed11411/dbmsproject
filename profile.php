<?php
    $conn = oci_connect('UMS', '12345', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        echo "Failed to connect to Oracle: " . $e['message'];
    } else {
        // Retrieve soldier information
        $soldierId = $_GET['soldierId']; // Assuming soldierId is passed as a query parameter
        $query = "SELECT * FROM Soldier WHERE SoldierID = :soldierId";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':soldierId', $soldierId);
        oci_execute($stmt);
        $row = oci_fetch_assoc($stmt);

        // Check if the soldier exists
        if (!$row) {
            echo "Soldier not found.";
        } else {
            // Display soldier profile
?>
<!DOCTYPE html>
<html>
<head>
    <title>Soldier Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Soldier Profile</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Soldier ID:</label>
                            <p><?php echo $row['SOLDIERID']; ?></p>
                        </div>
                        <div class="form-group">
                            <label>Name:</label>
                            <p><?php echo $row['NAME']; ?></p>
                        </div>
                        <div class="form-group">
                            <label>Rank:</label>
                            <p><?php echo $row['RANKID']; ?></p>
                        </div>
                        <!-- Add more profile fields here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
        }
        oci_free_statement($stmt);
        oci_close($conn);
    }
?>
