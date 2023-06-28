<?php
// update_appointment.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['appointment_id']) && isset($_POST['appointment_name'])) {
        $appointment_id = $_POST['appointment_id'];
        $appointment_name = $_POST['appointment_name'];

        // Update the appointment details in the database
        $conn = oci_connect('UMS', '12345', 'localhost/XE');
        if (!$conn) {
            $e = oci_error();
            echo "Failed to connect to Oracle: " . $e['message'];
            exit;
        }

        $query = "UPDATE APPOINTMENTS SET APPOINTMENTNAME = :appointment_name WHERE APPOINTMENTID = :appointment_id";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':appointment_name', $appointment_name);
        oci_bind_by_name($stmt, ':appointment_id', $appointment_id);

        if (oci_execute($stmt)) {
            echo "Appointment updated successfully.";
            header("Location: manage_appointment.php"); // Redirect back to company.php
        } else {
            $e = oci_error($stmt);
            echo "Failed to update appointment: " . $e['message'];
        }

        oci_free_statement($stmt);
        oci_close($conn);
    } else {
        echo "Invalid request. Appointment ID or name not provided.";
    }
} else {
    echo "Invalid request method. POST method expected.";
}
