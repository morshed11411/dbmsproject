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
                    <h1>Soldier Details</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">

                                    <?php
                                    if (isset($_GET['appointment_id'])) {
                                        $appointment_id = $_GET['appointment_id'];

                                        // Fetch the appointment details
                                        $conn = oci_connect('UMS', '12345', 'localhost/XE');
                                        if (!$conn) {
                                            $e = oci_error();
                                            echo "Failed to connect to Oracle: " . $e['message'];
                                        } else {
                                            $query = "SELECT APPOINTMENTNAME FROM APPOINTMENTS WHERE APPOINTMENTID = :appointment_id";
                                            $stmt = oci_parse($conn, $query);
                                            oci_bind_by_name($stmt, ':appointment_id', $appointment_id);
                                            oci_execute($stmt);

                                            $row = oci_fetch_assoc($stmt);
                                            $appointment_name = $row['APPOINTMENTNAME'];

                                            oci_free_statement($stmt);
                                            oci_close($conn);
                                        }
                                    }
                                    ?>

                                    <h2>Appointment:
                                        <?php echo $appointment_name; ?>
                                    </h2>

                                    <!-- Add appointment form -->
                                    <form method="post" action="">
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="appointment_id">Appointment ID:</label>
                                                <input type="text" name="appointment_id" id="appointment_id"
                                                    class="form-control" value="<?php echo $appointment_id; ?>"
                                                    readonly>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="company_id">Company:</label>
                                                <select name="company_id" id="company_id" class="form-control" required
                                                    onchange="fetchSoldiers(this.value)">
                                                    <option value="">Select Company</option>
                                                    <?php
                                                    // Fetch the companies from the database
                                                    include 'conn.php';
                                                    $query = "SELECT * FROM Company";
                                                    $stmt = oci_parse($conn, $query);
                                                    oci_execute($stmt);

                                                    while ($row = oci_fetch_assoc($stmt)) {
                                                        $selected = ($row['COMPANYID'] == $_POST['company_id']) ? 'selected' : '';
                                                        echo "<option value='" . $row['COMPANYID'] . "' $selected>" . $row['COMPANYNAME'] . "</option>";
                                                    }

                                                    oci_free_statement($stmt);
                                                    oci_close($conn);
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="soldier_id">Select Soldier:</label>
                                                <select name="soldier_id" id="soldier_id" class="form-control" required>
                                                    <option value="">Select Soldier</option>
                                                    <?php
                                                    // Fetch the soldiers based on the selected company
                                                    if (isset($_POST['company_id']) && !empty($_POST['company_id'])) {
                                                        include 'conn.php';
                                                        $selected_company_id = $_POST['company_id'];

                                                        $query = "SELECT SOLDIERID, NAME FROM Soldier WHERE COMPANYID = :company_id";
                                                        $stmt = oci_parse($conn, $query);
                                                        oci_bind_by_name($stmt, ':company_id', $selected_company_id);
                                                        oci_execute($stmt);

                                                        while ($row = oci_fetch_assoc($stmt)) {
                                                            $selected = ($_POST['soldier_id'] == $row['SOLDIERID']) ? 'selected' : '';
                                                            echo "<option value='" . $row['SOLDIERID'] . "' $selected>" . $row['NAME'] . "</option>";
                                                        }

                                                        oci_free_statement($stmt);
                                                        oci_close($conn);
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <script>
                                                function fetchSoldiers(companyId) {
                                                    // Clear the soldier dropdown
                                                    document.getElementById("soldier_id").innerHTML = '<option value="">Select Soldier</option>';

                                                    // If a company is selected, fetch soldiers via AJAX
                                                    if (companyId !== "") {
                                                        var xmlhttp = new XMLHttpRequest();
                                                        xmlhttp.onreadystatechange = function () {
                                                            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                                                // Populate the soldier dropdown with the response
                                                                document.getElementById("soldier_id").innerHTML = xmlhttp.responseText;
                                                            }
                                                        };
                                                        xmlhttp.open("GET", "get_soldiers.php?company_id=" + companyId, true);
                                                        xmlhttp.send();
                                                    }
                                                }
                                            </script>


                                        </div>
                                        <input type="submit" name="assign_appointment" value="Assign Appointment"
                                            class="btn btn-primary">
                                    </form>

                                    <?php
                                    if (isset($_POST['assign_appointment'])) {
                                        $appointment_id = $_POST['appointment_id'];
                                        $soldier_id = $_POST['soldier_id'];

                                        // Insert the assignment into the SoldierAppointment table
                                        $conn = oci_connect('UMS', '12345', 'localhost/XE');
                                        if (!$conn) {
                                            $e = oci_error();
                                            echo "Failed to connect to Oracle: " . $e['message'];
                                        } else {
                                            $query = "INSERT INTO SoldierAppointment (SoldierID, AppointmentID) VALUES (:soldier_id, :appointment_id)";
                                            $stmt = oci_parse($conn, $query);
                                            oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
                                            oci_bind_by_name($stmt, ':appointment_id', $appointment_id);
                                            oci_execute($stmt);

                                            echo "Appointment assigned successfully to the soldier.";

                                            oci_free_statement($stmt);
                                            oci_close($conn);
                                        }
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <!-- List of soldiers with the appointment -->
                                    <h3>Soldiers with the Appointment:</h3>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Soldier ID</th>
                                                <th>Name</th>
                                                <th>Company</th>
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
                                                $query = "SELECT Soldier.SOLDIERID, Soldier.NAME, Company.CompanyName 
                                                          FROM Soldier 
                                                          JOIN Company ON Soldier.CompanyID = Company.CompanyID 
                                                          JOIN SoldierAppointment ON Soldier.SoldierID = SoldierAppointment.SoldierID 
                                                          WHERE SoldierAppointment.AppointmentID = :appointment_id";
                                                $stmt = oci_parse($conn, $query);
                                                oci_bind_by_name($stmt, ':appointment_id', $appointment_id);
                                                oci_execute($stmt);

                                                while ($row = oci_fetch_assoc($stmt)) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row['SOLDIERID'] . "</td>";
                                                    echo "<td>" . $row['NAME'] . "</td>";
                                                    echo "<td>" . $row['COMPANYNAME'] . "</td>";
                                                    echo "<td>";
                                                    echo "<a href='remove_appointment.php?appointment_id=" . $appointment_id . "&soldier_id=" . $row['SOLDIERID'] . "'>Remove Appointment</a>";
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