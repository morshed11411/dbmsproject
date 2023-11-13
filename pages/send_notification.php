<?php
include '../includes/connection.php';
require_once '../includes/create_notification.php';

include '../includes/header.php'; // Include header after setting session variables

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $notifiedSoldierId = $_POST['notified_soldier_id'];
    $message = $_POST['message'];
    $notifiedGroup = $_POST['notified_group'];

    // Assuming the notifierSoldierId comes from the session userid
    $notifierSoldierId = $_SESSION['userid'];

    // Call the createNotification function
    $success = createNotification($notifiedSoldierId, $notifierSoldierId, $notifiedGroup, $message);

    if ($success) {
        $_SESSION['success'] = "Notification sent successfully.";
    } else {
        $_SESSION['error'] = "Failed to send notification.";
    }

    // Redirect back to the same page
    header("Location: " . $_SERVER['PHP_SELF']);    
}


?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Create Custom Notification</h3>
        </div>
        <div class="text-right">
        </div>
    </div>
    <?php include '../includes/alert.php'; ?>
    <form method="post" action="">
        <div class="form-group">
            <label for="notified_soldier_id">Notified Soldier ID:</label>
            <input type="text" class="form-control" name="notified_soldier_id" >
        </div>

        <div class="form-group">
            <label for="message">Message:</label>
            <textarea class="form-control" name="message" required></textarea>
        </div>

        <div class="form-group">
            <label for="notified_group">Notified Group:</label>
            <select class="form-control" name="notified_group">
                <option value="all">All</option>
                <option value="appt_holder">Apointment Holder</option>
                <option value="">None</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Send Notification</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
