<?php
session_start();
$soldierId = $_SESSION['userid'];


// Handle Mark as Read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_as_read'])) {
    $notificationId = $_POST['notificationId'];
    markNotificationAsRead($notificationId);
}

// Function to mark a notification as read

// Retrieve notifications for the current soldier from the database
$query = "SELECT ID, MESSAGE, STATUS, CREATED_AT FROM NOTIFICATIONS WHERE NOTIFIED_SOLDIERID = :soldier_id OR NOTIFIED_GROUP = 'all' ORDER BY CREATED_AT DESC";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierId);
oci_execute($stmt);

$notifications = [];
// After executing the query, print the results
while ($notification = oci_fetch_assoc($stmt)) {
    $notifications[] = $notification;
}


// Function to count unread notifications
function countUnreadNotifications($soldierId)
{
    global $conn;

    $query = "SELECT COUNT(*) AS unread_count FROM NOTIFICATIONS WHERE (NOTIFIED_SOLDIERID = :soldier_id OR NOTIFIED_GROUP = 'all') AND STATUS = 'Unread'";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierId);
    oci_execute($stmt);

    $result = oci_fetch_assoc($stmt);
    return $result['UNREAD_COUNT'];
}

$unreadCount = countUnreadNotifications($soldierId);
// Function to create a custom notification

function markNotificationAsRead($notificationId)
{
    global $conn;

    $query = "UPDATE NOTIFICATIONS SET STATUS = 'Read' WHERE ID = :notification_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':notification_id', $notificationId);

    if (oci_execute($stmt)) {
        // Successfully marked as read
    } else {
        // Failed to mark as read
        echo 'Error marking notification as read.';
    }
}

?>
<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Check if there are no notifications -->
                <?php if (empty($notifications)): ?>
                    <p>No notifications available.</p>
                <?php else: ?>
                    <!-- Unread notifications -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h6 class="mb-0">Unread Notifications</h6>
                        </div>
                        <ul class="list-group list-group-flush" id="notificationListUnread">
                            <?php foreach ($notifications as $notification): ?>
                                <?php if ($notification['STATUS'] === 'Unread'): ?>
                                    <li class="list-group-item font-weight-bold">
                                        <div class="d-flex justify-content-between">
                                            <span>
                                                <?php echo $notification['MESSAGE']; ?>
                                            </span>
                                            <form method="post" action="">
                                                <input type="hidden" name="notificationId" value="<?php echo $notification['ID']; ?>">
                                                <button type="submit" name="mark_as_read" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Mark as Read
                                                </button>
                                            </form>
                                        </div>
                                        <?php 
                                        // Display the time difference
                                        displayTimeDifference($notification['CREATED_AT']);
                                        ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Read notifications (collapsed by default) -->
                    <div class="card card-secondary mt-3">
                        <div class="card-header" data-toggle="collapse" data-target="#notificationListRead">
                            <h6 class="mb-0">Old Notifications</h6>
                        </div>
                        <ul class="list-group list-group-flush collapse" id="notificationListRead">
                            <?php foreach ($notifications as $notification): ?>
                                <?php if ($notification['STATUS'] === 'Read'): ?>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <span>
                                                <?php echo $notification['MESSAGE']; ?>
                                            </span>
                                        </div>
                                        <?php 
                                        // Display the time difference
                                        displayTimeDifference($notification['CREATED_AT']);
                                        ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function showNotificationModal() {
        $('#notificationModal').modal('show');
    }
</script>

<?php
// Function to display the time difference
function displayTimeDifference($rawDate) {
    // Convert the raw date to a DateTime object
    $date = DateTime::createFromFormat('d-M-y h.i.s.u A', $rawDate, new DateTimeZone('UTC'));

    // Convert to the user's time zone
    $date->setTimeZone(new DateTimeZone('Asia/Dhaka'));

    // Get the current date and time
    $now = new DateTime();

    // Calculate the difference between the two dates
    $interval = $now->diff($date);

    // Format the output based on the difference
    if ($interval->y > 0) {
        echo '<small>' . $interval->format('%y years ago') . '</small>';
    } elseif ($interval->m > 0) {
        echo '<small>' . $interval->format('%m months ago') . '</small>';
    } elseif ($interval->d > 0) {
        echo '<small>' . $interval->format('%d days ago') . '</small>';
    } elseif ($interval->h > 0) {
        echo '<small>' . $interval->format('%h hours ago') . '</small>';
    } elseif ($interval->i > 0) {
        echo '<small>' . $interval->format('%i minutes ago') . '</small>';
    } else {
        echo '<small>Just now</small>';
    }
}
?>
