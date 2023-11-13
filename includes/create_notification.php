<?php
function createNotification($notifiedSoldierId, $notifierSoldierId, $notifiedGroup, $message)
{
    global $conn;

    $query = "INSERT INTO NOTIFICATIONS (NOTIFIED_SOLDIERID, NOTIFIER_SOLDIERID, NOTIFIED_GROUP, MESSAGE, STATUS) 
              VALUES (:notified_soldier_id, :notifier_soldier_id, :notified_group, :message, 'Unread')";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':notified_soldier_id', $notifiedSoldierId);
    oci_bind_by_name($stmt, ':notifier_soldier_id', $notifierSoldierId);
    oci_bind_by_name($stmt, ':notified_group', $notifiedGroup);
    oci_bind_by_name($stmt, ':message', $message);
    $success = oci_execute($stmt);

    return $success;
}



?>