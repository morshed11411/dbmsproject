<?php
include 'conn.php';

if (isset($_GET['appointment_id']) && isset($_GET['soldier_id'])) {
    $appointment_id = $_GET['appointment_id'];
    $soldier_id = $_GET['soldier_id'];

    // Delete the appointment from the SoldierAppointment table
    $query = "DELETE FROM SoldierAppointment WHERE SoldierID = :soldier_id AND AppointmentID = :appointment_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
    oci_bind_by_name($stmt, ':appointment_id', $appointment_id);
    oci_execute($stmt);

    oci_free_statement($stmt);
    oci_close($conn);

    // Redirect to the page displaying the appointment details
    header("Location: soldier_details.php?appointment_id=" . $appointment_id);
    exit;

} else {
    echo "Invalid appointment or soldier ID.";
}
?>
