<?php
session_start();

include '../includes/connection.php';

// Process the form submission to add an appointment
if (isset($_POST['add_appointment_submit'])) {
    $appointmentName = $_POST['appointment_name'];

    $query = "INSERT INTO appointments (APPOINTMENTNAME) VALUES (:appointment_name)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':appointment_name', $appointmentName);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Appointment added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add appointment: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: appointment.php");
    exit();
}

// Process the form submission to edit an appointment
if (isset($_POST['edit_appointment_submit'])) {
    $appointmentID = $_POST['edit_appointment_id'];
    $appointmentName = $_POST['edit_appointment_name'];

    $query = "UPDATE appointments SET APPOINTMENTNAME = :appointment_name WHERE APPOINTMENTID = :appointment_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':appointment_name', $appointmentName);
    oci_bind_by_name($stmt, ':appointment_id', $appointmentID);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Appointment updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update appointment: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: appointment.php");
    exit();
}

// Process the form submission to delete an appointment
if (isset($_POST['delete_appointment_submit'])) {
    $appointmentID = $_POST['delete_appointment_id'];

    $query = "DELETE FROM appointments WHERE APPOINTMENTID = :appointment_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':appointment_id', $appointmentID);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Appointment deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete appointment: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: appointment.php");
    exit();
}

// Fetch data from the appointments table
$query = "SELECT * FROM appointments";
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
oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Appointment Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addAppointmentModal">Add Appointment</button>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
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
                                <?php foreach ($appointmentList as $appointment): ?>
                                    <tr>
                                        <td><?php echo $appointment->AppointmentID; ?></td>
                                        <td><?php echo $appointment->AppointmentName; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editAppointmentModal-<?php echo $appointment->AppointmentID; ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAppointmentModal-<?php echo $appointment->AppointmentID; ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Edit Appointment Modal -->
                                    <div class="modal fade" id="editAppointmentModal-<?php echo $appointment->AppointmentID; ?>" tabindex="-1" role="dialog" aria-labelledby="editAppointmentModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editAppointmentModalLabel">Edit Appointment</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_appointment_id" value="<?php echo $appointment->AppointmentID; ?>">
                                                        <div class="form-group">
                                                            <label for="edit_appointment_name">Appointment Name:</label>
                                                            <input type="text" name="edit_appointment_name" id="edit_appointment_name" class="form-control" value="<?php echo $appointment->AppointmentName; ?>" required>
                                                        </div>
                                                        <button type="submit" name="edit_appointment_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Delete Appointment Modal -->
                                    <div class="modal fade" id="deleteAppointmentModal-<?php echo $appointment->AppointmentID; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteAppointmentModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteAppointmentModalLabel">Delete Appointment</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this appointment?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_appointment_id" value="<?php echo $appointment->AppointmentID; ?>">
                                                        <button type="submit" name="delete_appointment_submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Appointment Modal -->
<div class="modal fade" id="addAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAppointmentModalLabel">Add Appointment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="appointment_name">Appointment Name:</label>
                        <input type="text" name="appointment_name" id="appointment_name" class="form-control" required>
                    </div>
                    <input type="submit" name="add_appointment_submit" value="Add Appointment" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
