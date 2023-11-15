<?php
session_start();

include '../includes/connection.php';
require_once '../includes/create_notification.php';


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
include '../includes/header.php'; // Include header after setting session variables

?>

<!-- Add Bootstrap CSS and JS links here -->

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Create Custom Notification</h3>
        </div>
        <div class="text-right">
        </div>
    </div>
    <?php include '../includes/alert.php'; ?>
    <!-- Button to trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#sendNotificationModal">
        Create Custom Notification
    </button>

    <!-- Modal -->
    <div class="modal fade" id="sendNotificationModal" tabindex="-1" role="dialog" aria-labelledby="sendNotificationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendNotificationModalLabel">Create Custom Notification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="">
                        <!-- Your form fields go here -->
                        <div class="form-group">
                            <label for="notified_soldier_id">Notified Soldier ID:</label>
                            <input type="text" class="form-control" name="notified_soldier_id">
                        </div>

                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea class="form-control" name="message" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="notified_group">Notified Group:</label>
                            <select class="form-control" name="notified_group">
                                <option value="all">All</option>
                                <option value="appt_holder">Appointment Holder</option>
                                <option value="">None</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Send Notification</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
