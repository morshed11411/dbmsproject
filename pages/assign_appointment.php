<?php
session_start();

include '../includes/connection.php';

// Process the form submission to add an appointment
if (isset($_POST['assign_appointment_submit'])) {
    $soldierID = $_POST['soldier_id'];
    $appointmentID = $_POST['appointment_id'];
    $startDate = date('Y-m-d'); // Set the start date as the current system date

    // Check if the appointment is already active for the soldier
    $query = "SELECT * FROM SOLDIERAPPOINTMENT WHERE SOLDIER_ID = :soldier_id AND APPOINTMENT_ID = :appointment_id AND END_DATE IS NULL";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_bind_by_name($stmt, ':appointment_id', $appointmentID);
    oci_execute($stmt);

    $existingAppointment = oci_fetch_assoc($stmt);

    if ($existingAppointment) {
        $_SESSION['error'] = "Appointment already assigned to the soldier.";
        oci_free_statement($stmt);
        oci_close($conn);
        header("Location: assign_appointment.php?soldier=$soldierID");
        exit();
    }

    oci_free_statement($stmt);

    // Insert appointment assignment into the database
    $query = "INSERT INTO SOLDIERAPPOINTMENT (SOLDIER_ID, APPOINTMENT_ID, START_DATE) VALUES (:soldier_id, :appointment_id, TO_DATE(:start_date, 'YYYY-MM-DD'))";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_bind_by_name($stmt, ':appointment_id', $appointmentID);
    oci_bind_by_name($stmt, ':start_date', $startDate);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Appointment added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add appointment: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: assign_appointment.php?soldier=$soldierID");
    exit();
}

// Fetch soldier ID and name from the query parameter
if (isset($_GET['soldier'])) {
    $soldierID = $_GET['soldier'];

    // Fetch soldier details from the database
    $query = "SELECT SOLDIERID, NAME, COMPANYNAME FROM SOLDIER JOIN COMPANY USING (COMPANYID) WHERE SOLDIERID = :soldier_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_execute($stmt);

    $soldier = oci_fetch_assoc($stmt);

    // Redirect if soldier not found
    if (!$soldier) {
        header("Location: soldiers.php");
        exit();
    }

    oci_free_statement($stmt);
} else {
    header("Location: soldiers.php");
    exit();
}

// Fetch appointment list from the database
$query = "SELECT APPOINTMENTID, APPOINTMENTNAME FROM APPOINTMENTS";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$appointmentList = array();
while ($row = oci_fetch_assoc($stmt)) {
    $appointment = new stdClass();
    $appointment->AppointmentID = $row['APPOINTMENTID'];
    $appointment->AppointmentName = $row['APPOINTMENTNAME'];
    $appointmentList[] = $appointment;
}

oci_free_statement($stmt);

// Process the end appointment action
if (isset($_POST['end_appointment_submit'])) {
    $soldierID = $_POST['soldier_id'];
    $appointmentID = $_POST['appointment_id'];

    // Set the end date as the current system date
    $endDate = date('Y-m-d');

    // Update the end date for the appointment assignment
    $query = "UPDATE SOLDIERAPPOINTMENT SET END_DATE = TO_DATE(:end_date, 'YYYY-MM-DD') WHERE SOLDIER_ID = :soldier_id AND APPOINTMENT_ID = :appointment_id AND END_DATE IS NULL";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':end_date', $endDate);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_bind_by_name($stmt, ':appointment_id', $appointmentID);

    $result = oci_execute($stmt);
    $rowCount = oci_num_rows($stmt);

    if ($result && $rowCount > 0) {
        $_SESSION['success'] = "Appointment ended successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to end appointment: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: assign_appointment.php?soldier=$soldierID");
    exit();
}

// Fetch active appointment assignments for the soldier
$query = "SELECT SA.APPOINTMENT_ID, A.APPOINTMENTNAME, SA.START_DATE
          FROM SOLDIERAPPOINTMENT SA
          JOIN APPOINTMENTS A ON SA.APPOINTMENT_ID = A.APPOINTMENTID
          WHERE SA.SOLDIER_ID = :soldier_id AND SA.END_DATE IS NULL";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierID);
oci_execute($stmt);

$activeAppointments = oci_fetch_all($stmt, $activeAppointmentArray, null, null, OCI_FETCHSTATEMENT_BY_ROW);

oci_free_statement($stmt);

// Fetch appointment history for the soldier
$query = "SELECT A.APPOINTMENTNAME, SA.START_DATE, SA.END_DATE 
          FROM SOLDIERAPPOINTMENT SA 
          JOIN APPOINTMENTS A ON SA.APPOINTMENT_ID = A.APPOINTMENTID 
          WHERE SA.SOLDIER_ID = :soldier_id ORDER BY SA.ID DESC";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierID);
oci_execute($stmt);

$appointmentHistoryList = array();
while ($row = oci_fetch_assoc($stmt)) {
    $appointmentHistory = new stdClass();
    $appointmentHistory->AppointmentName = $row['APPOINTMENTNAME'];
    $appointmentHistory->StartDate = $row['START_DATE'];
    $appointmentHistory->EndDate = $row['END_DATE'];
    $appointmentHistoryList[] = $appointmentHistory;
}

oci_free_statement($stmt);
oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Add/End Appointment</h3>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
           <?php include '../includes/alert.php'; ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Soldier Information</h5>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Soldier ID</th>
                                    <td>
                                        <?php echo $soldier['SOLDIERID']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>
                                        <?php echo $soldier['NAME']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Company</th>
                                    <td>
                                        <?php echo $soldier['COMPANYNAME']; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <?php if ($activeAppointments > 0): ?>
                            <h5>Active Appointments</h5>
                            <?php foreach ($activeAppointmentArray as $activeAppointment): ?>
                                <div class="alert alert-info">
                                    <p>Soldier is currently assigned to Appointment: <strong>
                                            <?php echo $activeAppointment['APPOINTMENTNAME']; ?>
                                        </strong> since
                                        <?php echo $activeAppointment['START_DATE']; ?>
                                    </p>
                                    <form method="post" action="">
                                        <input type="hidden" name="soldier_id" value="<?php echo $soldierID; ?>">
                                        <input type="hidden" name="appointment_id" value="<?php echo $activeAppointment['APPOINTMENT_ID']; ?>">
                                        <button type="submit" name="end_appointment_submit" class="btn btn-danger">End Appointment</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <h5>Add Appointment</h5>
                        <form method="post" action="">
                            <input type="hidden" name="soldier_id" value="<?php echo $soldierID; ?>">
                            <div class="form-group">
                                <label for="appointment_id">Appointment:</label>
                                <select name="appointment_id" id="appointment_id" class="form-control" required>
                                    <?php foreach ($appointmentList as $appointment): ?>
                                        <option value="<?php echo $appointment->AppointmentID; ?>"><?php echo $appointment->AppointmentName; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="assign_appointment_submit" class="btn btn-primary">Add Appointment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Appointment History</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Appointment Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointmentHistoryList as $appointmentHistory): ?>
                                    <tr>
                                        <td>
                                            <?php echo $appointmentHistory->AppointmentName; ?>
                                        </td>
                                        <td>
                                            <?php echo $appointmentHistory->StartDate; ?>
                                        </td>
                                        <td>
                                            <?php echo $appointmentHistory->EndDate ? $appointmentHistory->EndDate : 'NA'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
