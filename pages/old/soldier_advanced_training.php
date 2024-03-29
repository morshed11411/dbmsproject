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
                    <h1>Soldier Advanced Training</h1>
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
                                            <label for="cadreid">Cadre ID:</label>
                                            <select name="cadreid" id="cadreid" class="form-control" required>
                                                <?php
                                                $conn = oci_connect('UMS', '12345', 'localhost/XE');
                                                if (!$conn) {
                                                    $e = oci_error();
                                                    echo "Failed to connect to Oracle: " . $e['message'];
                                                } else {
                                                    $query = "SELECT CADREID, NAME FROM ADVANCETRAINING";
                                                    $stmt = oci_parse($conn, $query);
                                                    oci_execute($stmt);

                                                    while ($row = oci_fetch_assoc($stmt)) {
                                                        echo "<option value='" . $row['CADREID'] . "'>" . $row['CADREID'] . " - " . $row['NAME'] . "</option>";
                                                    }

                                                    oci_free_statement($stmt);
                                                    oci_close($conn);
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="soldierid">Soldier ID:</label>
                                            <input type="text" name="soldierid" id="soldierid" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="remark">Remark:</label>
                                            <select name="remark" id="remark" class="form-control" required>
                                                <option value="Pass">Pass</option>
                                                <option value="Fail">Fail</option>
                                            </select>
                                        </div>

                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                    </form>

                                    <?php
                                    if (isset($_POST['submit'])) {
                                        $cadreID = $_POST['cadreid'];
                                        $soldierID = $_POST['soldierid'];
                                        $remark = $_POST['remark'];

                                        $conn = oci_connect('UMS', '12345', 'localhost/XE');
                                        if (!$conn) {
                                            $e = oci_error();
                                            echo "Failed to connect to Oracle: " . $e['message'];
                                        } else {
                                            $query = "INSERT INTO SOLDIERADVANCEDTRAINING (CADREID, SOLDIERID, REMARK) VALUES (:cadreID, :soldierID, :remark)";
                                            $stmt = oci_parse($conn, $query);

                                            oci_bind_by_name($stmt, ':cadreID', $cadreID);
                                            oci_bind_by_name($stmt, ':soldierID', $soldierID);
                                            oci_bind_by_name($stmt, ':remark', $remark);

                                            $result = oci_execute($stmt);
                                            if ($result) {
                                                echo "Advanced training data inserted successfully.";
                                            } else {
                                                $e = oci_error($stmt);
                                                if ($e['code'] == 1 && strpos($e['message'], 'SYS_C007204') !== false) {
                                                    echo "Failed to insert advanced training data: The Cadre ID and Soldier ID combination already exists. Please enter a unique combination.";
                                                } else {
                                                    echo "Failed to insert advanced training data: Please enter valid data.";
                                                }
                                            }

                                            oci_free_statement($stmt);
                                            oci_close($conn);
                                        }
                                    }
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
                                                <th>Cadre ID</th>
                                                <th>Cadre Name</th>
                                                <th>Soldier ID</th>
                                                <th>Remark</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $conn = oci_connect('UMS', '12345', 'localhost/XE');
                                            if (!$conn) {
                                                $e = oci_error();
                                                echo "Failed to connect to Oracle: " . $e['message'];
                                            } else {
                                                $query = "SELECT s.CADREID, a.NAME AS CADRENAME, s.SOLDIERID, s.REMARK 
                                                          FROM SOLDIERADVANCEDTRAINING s
                                                          INNER JOIN ADVANCETRAINING a ON s.CADREID = a.CADREID
                                                          ORDER BY s.CADREID, s.SOLDIERID";
                                                $stmt = oci_parse($conn, $query);
                                                oci_execute($stmt);

                                                while ($row = oci_fetch_assoc($stmt)) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row['CADREID'] . "</td>";
                                                    echo "<td>" . $row['CADRENAME'] . "</td>";
                                                    echo "<td>" . $row['SOLDIERID'] . "</td>";
                                                    echo "<td>" . $row['REMARK'] . "</td>";
                                                    echo "<td>";
                                                    echo "<a href='edit_advanced_training.php?cadreid=" . $row['CADREID'] . "&soldierid=" . $row['SOLDIERID'] . "'>Edit</a> | ";
                                                    echo "<a href='delete_advanced_training.php?cadreid=" . $row['CADREID'] . "&soldierid=" . $row['SOLDIERID'] . "'>Delete</a>";
                                                    echo "</td>";
                                                    echo "</tr>";
                                                }

                                                oci_free_statement($stmt);
                                                oci_close($conn);
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
