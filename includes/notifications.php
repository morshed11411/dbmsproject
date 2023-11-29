<?php
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

$_SESSION['notification']=[];
$_SESSION['notification'] = $notifications;


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
                    <ul class="nav nav-tabs" id="notificationTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="unread-tab" data-toggle="tab" href="#unread" role="tab"
                                aria-controls="unread" aria-selected="true">Unread Notifications</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="read-tab" data-toggle="tab" href="#read" role="tab" aria-controls="read"
                                aria-selected="false">Read Notifications</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-2">
                        <!-- Unread notifications -->
                        <div class="tab-pane fade show active" id="unread" role="tabpanel" aria-labelledby="unread-tab">
                            <ul class="list-group list-group-flush" id="notificationListUnread">

                                <?php $noNotification = true; // Assume no unread notifications initially ?>
                                <?php foreach ($notifications as $notification): ?>
                                    <?php if ($notification['STATUS'] === 'Unread') { ?>
                                        <li class="list-group-item font-weight-bold">
                                            <div class="d-flex justify-content-between">
                                                <span>
                                                    <?php echo $notification['MESSAGE']; ?>
                                                </span>
                                                <form method="post" action="">
                                                    <input type="hidden" name="notificationId"
                                                        value="<?php echo $notification['ID']; ?>">
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
                                        <?php $noNotification = false; // Found an unread notification ?>
                                    <?php } ?>
                                <?php endforeach; ?>

                                <?php if ($noNotification): ?>
                                    <div class="text-center">
                                        <p>No new notifications.</p>
                                    </div>
                                <?php endif; ?>

                            </ul>
                        </div>


                        <!-- Read notifications (collapsed by default) -->
                        <div class="tab-pane fade" id="read" role="tabpanel" aria-labelledby="read-tab">
                            <ul class="list-group list-group-flush" id="notificationListRead">
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
function displayTimeDifference($rawDate)
{
    $date = DateTime::createFromFormat('d-M-y h.i.s.u A', $rawDate, new DateTimeZone('UTC'));
    $date->setTimeZone(new DateTimeZone('Asia/Dhaka'));
    $now = new DateTime();
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