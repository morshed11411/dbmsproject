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
                    <h1>Advanced Training</h1>
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
                                            <label for="cadre_id">Cadre ID:</label>
                                            <input type="number" name="cadre_id" id="cadre_id" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="name">Name:</label>
                                            <input type="text" name="name" id="name" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="training_startdate">Training Start Date:</label>
                                            <input type="date" name="training_startdate" id="training_startdate" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="training_enddate">Training End Date:</label>
                                            <input type="date" name="training_enddate" id="training_enddate" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="training_oic">Training OIC:</label>
                                            <input type="text" name="training_oic" id="training_oic" class="form-control">
                                        </div>

                                        <div class="form-group">
                                            <label for="instructor">Instructor:</label>
                                            <input type="text" name="instructor" id="instructor" class="form-control">
                                        </div>

                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                    </form>

                                    <?php
                                    if (isset($_POST['submit'])) {
                                        $cadreid = $_POST['cadre_id'];
                                        $name = $_POST['name'];
                                        $trainingstartdate = $_POST['training_startdate'];
                                        $trainingenddate = $_POST['training_enddate'];
                                        $trainingoic = $_POST['training_oic'];
                                        $instructor = $_POST['instructor'];

                                        $conn = oci_connect('UMS', '12345', 'localhost/XE');
                                        if (!$conn) {
                                            $e = oci_error();
                                            echo "Failed to connect to Oracle: " . $e['message'];
                                        } else {
                                            $query = "INSERT INTO ADVANCETRAINING (CADREID, NAME, TRAININGSTARTDATE, TRAININGENDDATE, TRAININGOIC, INSTRUCTOR) VALUES (:cadreid, :name, TO_DATE(:trainingstartdate, 'YYYY-MM-DD'), TO_DATE(:trainingenddate, 'YYYY-MM-DD'), :trainingoic, :instructor)";
                                            $stmt = oci_parse($conn, $query);

                                            oci_bind_by_name($stmt, ':cadreid', $cadreid);
                                            oci_bind_by_name($stmt, ':name', $name);
                                            oci_bind_by_name($stmt, ':trainingstartdate', $trainingstartdate);
                                            oci_bind_by_name($stmt, ':trainingenddate', $trainingenddate);
                                            oci_bind_by_name($stmt, ':trainingoic', $trainingoic);
                                            oci_bind_by_name($stmt, ':instructor', $instructor);

                                            $result = oci_execute($stmt);
                                            if ($result) {
                                                echo "Soldier's advanced training data inserted successfully.";
                                            } else {
                                                $e = oci_error($stmt);
                                                echo "Failed to insert soldier's advanced training data: " . $e['message'];
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
                                    <h3>View All</h3>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Cadre ID</th>
                                                <th>Name</th>
                                                <th>Training Start Date</th>
                                                <th>Training End Date</th>
                                                <th>Training OIC</th>
                                                <th>Instructor</th>
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
                                                $query = "SELECT * FROM ADVANCETRAINING";
                                                $stmt = oci_parse($conn, $query);
                                                oci_execute($stmt);

                                                while ($row = oci_fetch_assoc($stmt)) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row['CADREID'] . "</td>";
                                                    echo "<td>" . $row['NAME'] . "</td>";
                                                    echo "<td>" . $row['TRAININGSTARTDATE'] . "</td>";
                                                    echo "<td>" . $row['TRAININGENDDATE'] . "</td>";
                                                    echo "<td>" . $row['TRAININGOIC'] . "</td>";
                                                    echo "<td>" . $row['INSTRUCTOR'] . "</td>";
                                                    echo "<td>";
                                                    echo "<a href='edit_cadre.php?cadreid=" . $row['CADREID'] . "' class='btn btn-primary btn-sm'>Edit</a>";
                                                    echo "<a href='delete_cadre.php?cadreid=" . $row['CADREID'] . "' class='btn btn-danger btn-sm'>Delete</a>";
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

    <!-- Bootstrap JS and additional scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
