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
                    <h1>Insert Appointment Data</h1>
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
                                            <label for="appointment_id">Appointment ID:</label>
                                            <input type="text" name="appointment_id" id="appointment_id" class="form-control"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label for="appointment_name">Appointment Name:</label>
                                            <input type="text" name="appointment_name" id="appointment_name" class="form-control"
                                                required>
                                        </div>


                                   <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                                    </form>
                                    <?php
                                    if (isset($_POST['submit'])) {
                                        $appointment_id = $_POST['appointment_id'];
                                        $appointment_name = $_POST['appointment_name'];

                                        $conn = oci_connect('UMS', '12345', 'localhost/XE');
                                        if (!$conn) {
                                            $e = oci_error();
                                            echo "Failed to connect to Oracle: " . $e['message'];
                                        } else {
                                            $query = "INSERT INTO APPOINTMENTS (APPOINTMENTID, APPOINTMENTNAME) VALUES (:appointment_id, :appointment_name)";
                                            $stmt = oci_parse($conn, $query);

                                            oci_bind_by_name($stmt, ':appointment_id', $appointment_id);
                                            oci_bind_by_name($stmt, ':appointment_name', $appointment_name);

                                            $result = oci_execute($stmt);
                                            if ($result) {
                                                echo "Appointment data inserted successfully.";
                                            } else {
                                                $e = oci_error($stmt);
                                                if ($e['code'] == 1 && strpos($e['message'], 'SYS_C007204') !== false) {
                                                    echo "Failed to insert Appointment data: The Appointment ID already exists. Please enter a unique Appointment ID.";
                                                } else {
                                                    echo "Failed to insert Appointment data: Please enter valid data.";
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
                                                <th>Appointment ID</th>
                                                <th>Appointment Name</th>
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
                                                $query = "SELECT * FROM APPOINTMENTS";
                                                $stmt = oci_parse($conn, $query);
                                                oci_execute($stmt);

                                                while ($row = oci_fetch_assoc($stmt)) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row['APPOINTMENTID'] . "</td>";
                                                    echo "<td>" . $row['APPOINTMENTNAME'] . "</td>";
                                                    echo "<td>";
                                                    echo "<a href='edit_appointment.php?appointment_id=" . $row['APPOINTMENTID'] . "'>Edit</a> | ";
                                                    echo "<a href='delete_appointment.php?appointment_id=" . $row['APPOINTMENTID'] . "'>Delete</a>";
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
        <?php include 'views/footer.php';?>

    </div>

</body>

</html>
