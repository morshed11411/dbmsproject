<?php
include 'views/auth.php';

if (isset($_POST['submit'])) {
    // Get the appointment ID and selected soldier IDs
    $appointment_id = $_POST['appointment_id'];
    $soldier_ids = $_POST['soldier_ids'];

    // Establish a connection to the Oracle database
    $conn = oci_connect('UMS', '12345', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        echo "Failed to connect to Oracle: " . $e['message'];
    } else {
        // Delete existing appointments for the selected soldiers
        $deleteQuery = "DELETE FROM SoldierAppointment WHERE AppointmentID = :appointment_id";
        $deleteStmt = oci_parse($conn, $deleteQuery);
        oci_bind_by_name($deleteStmt, ':appointment_id', $appointment_id);
        oci_execute($deleteStmt);

        // Assign the appointment to the selected soldiers
        $insertQuery = "INSERT INTO SoldierAppointment (SoldierID, AppointmentID) VALUES (:soldier_id, :appointment_id)";
        $insertStmt = oci_parse($conn, $insertQuery);
        oci_bind_by_name($insertStmt, ':appointment_id', $appointment_id);

        // Iterate over the selected soldier IDs and execute the insert statement for each soldier
        foreach ($soldier_ids as $soldier_id) {
            oci_bind_by_name($insertStmt, ':soldier_id', $soldier_id);
            oci_execute($insertStmt);
        }

        oci_commit($conn);

        echo "Appointment assigned successfully.";

        oci_free_statement($deleteStmt);
        oci_free_statement($insertStmt);
        oci_close($conn);
    }
} else {
    echo "No form submission.";
}
?>
