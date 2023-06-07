<?php
// edit_appointment.php

if (isset($_GET['appointment_id'])) {
    $appointment_id = $_GET['appointment_id'];

    // Fetch appointment details from the database based on the appointment_id
    $conn = oci_connect('UMS', '12345', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        echo "Failed to connect to Oracle: " . $e['message'];
        exit;
    }

    $query = "SELECT * FROM APPOINTMENTS WHERE APPOINTMENTID = :appointment_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':appointment_id', $appointment_id);
    oci_execute($stmt);

    $appointment = oci_fetch_assoc($stmt);
    if (!$appointment) {
        echo "Appointment not found.";
        exit;
    }

    oci_free_statement($stmt);
    oci_close($conn);

    // Render the form for editing appointment details
    include 'views/auth.php';
    include 'views/head.php';
    ?>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Edit Appointment</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <form method="post" action="update_appointment.php">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['APPOINTMENTID']; ?>">
                                        <div class="form-group">
                                            <label for="appointment_name">Appointment Name:</label>
                                            <input type="text" name="appointment_name" id="appointment_name" class="form-control"
                                                required value="<?php echo $appointment['APPOINTMENTNAME']; ?>">
                                        </div>

                                        <input type="submit" name="submit" value="Update" class="btn btn-primary">
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
    
    <?php
} else {
    echo "Invalid request. Appointment ID not provided.";
    exit;
}
?>
