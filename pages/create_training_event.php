<?php
session_start();

include '../includes/connection.php';
require_once '../includes/create_notification.php';

// Define an array to store training types
$trainingTypes = array();

// Define an array to store officer names with specified ranks
$officerNames = array();

// Query to fetch training types from the BASICTRAINING table
$query = "SELECT DISTINCT TRGID, TRGNAME FROM BASICTRAINING";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

while ($row = oci_fetch_assoc($stmt)) {
    $trainingType = $row['TRGNAME'];
    $trainingTypeId = $row['TRGID'];
    $trainingTypes[$trainingTypeId] = $trainingType;
}

oci_free_statement($stmt);

// Query to fetch officer names with specified ranks
$query = "SELECT S.SOLDIERID, R.RANK || ' ' || S.NAME AS NAME
          FROM SOLDIER S
          LEFT JOIN RANKS R ON S.RANKID = R.RANKID
          WHERE R.RANK IN ('Lt Col', 'Maj', 'Capt', 'Lt', '2Lt')";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

while ($row = oci_fetch_assoc($stmt)) {
    $soldierID = $row['SOLDIERID'];
    $officerName = $row['NAME'];
    $officerNames[$soldierID] = $officerName;
}

oci_free_statement($stmt);

// Define an array to store the training events
$trainingEvents = array();

// Query to fetch training events
$query = "SELECT TE.EVENTID,TE.EVENTNAME, BT.TRGNAME AS TRAINING_TYPE, TE.EVENTDATE, R.RANK || ' ' || S.NAME AS BOARD_PRESIDENT, TE.AUTHORITYLETTERNO, TE.BOARDNO 
    FROM TRAININGEVENT TE
    LEFT JOIN BASICTRAINING BT ON TE.TRGID = BT.TRGID
    LEFT JOIN SOLDIER S ON TE.BOARDPRESIDENTID = S.SOLDIERID
    LEFT JOIN RANKS R ON S.RANKID = R.RANKID
    WHERE TE.STATUS = 'Ongoing'";

$stmt = oci_parse($conn, $query);
oci_execute($stmt);

// Fetch all the rows into the $trainingEvents array
oci_fetch_all($stmt, $trainingEvents, null, null, OCI_FETCHSTATEMENT_BY_ROW);

oci_free_statement($stmt);


function executeQuery($query, $bindings, $successMessage, $errorMessage)
{
    global $conn;

    $stmt = oci_parse($conn, $query);

    foreach ($bindings as $param => &$value) {
        oci_bind_by_name($stmt, $param, $value);
    }

    $result = oci_execute($stmt);

    if ($result) {
        $_SESSION['success'] = $successMessage;
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = $errorMessage . ': ' . $error['message'];
    }

    oci_free_statement($stmt);
    header("Location: " . $_SERVER['PHP_SELF']);

}

// Process the add training event action
if (isset($_POST['add_event_submit'])) {
    $training_type = $_POST['training_type'];
    $event_date = $_POST['event_date'];
    $event_name = $_POST['event_name'];
    $board_president = $_POST['board_president'];
    $authority_letter_no = $_POST['authority_letter_no'];
    $board_no = $_POST['board_no'];

    $query = "INSERT INTO TRAININGEVENT (TRGID, EVENTDATE, BOARDPRESIDENTID, AUTHORITYLETTERNO, BOARDNO, EVENTNAME, STATUS) VALUES (:training_type, TO_DATE(:event_date, 'YYYY-MM-DD'), :board_president, :authority_letter_no, :board_no, :event_name, 'Ongoing')";
    $bindings = array(
        ':training_type' => $training_type,
        ':event_date' => $event_date,
        ':event_name' => $event_name,
        ':board_president' => $board_president,
        ':authority_letter_no' => $authority_letter_no,
        ':board_no' => $board_no,
    );

    executeQuery($query, $bindings, "Training event added successfully.", "Failed to add training event");
    $notifiedGroup = 'all'; // Assuming 'all' represents all users
    $message = "A new training event ('$event_name') has been added.";
    $notifierSoldierId = $_SESSION['userid'];
    // Call the createNotification function
    $result=createNotification(null, $notifierSoldierId, $notifiedGroup, $message);


}
// Process the edit training event action
if (isset($_POST['edit_event_submit'])) {
    $event_id = $_POST['edit_event_id'];
    $event_name = $_POST['edit_event_name'];
    $training_type = $_POST['edit_training_type'];
    $event_date = $_POST['edit_event_date'];
    $board_president = $_POST['edit_board_president'];
    $authority_letter_no = $_POST['edit_authority_letter_no'];
    $board_no = $_POST['edit_board_no'];

    $query = "UPDATE TRAININGEVENT SET TRGID = :training_type, EVENTNAME= :event_name, EVENTDATE = TO_DATE(:event_date, 'YYYY-MM-DD'), BOARDPRESIDENTID = :board_president, AUTHORITYLETTERNO = :authority_letter_no, BOARDNO = :board_no WHERE EVENTID = :event_id";
    $bindings = array(
        ':training_type' => $training_type,
        ':event_date' => $event_date,
        ':event_name' => $event_name,
        ':board_president' => $board_president,
        ':authority_letter_no' => $authority_letter_no,
        ':board_no' => $board_no,
        ':event_id' => $event_id,
    );

    executeQuery($query, $bindings, "Training event updated successfully.", "Failed to update training event");
}

// Process the end training event action
if (isset($_POST['end_event_submit'])) {
    $event_id = $_POST['event_id'];
    $end_date = date('Y-m-d'); // Set the end date as the current system date

    $query = "UPDATE TRAININGEVENT SET STATUS = 'Terminated' WHERE EVENTID = :event_id";
    $bindings = array(
        ':event_id' => $event_id,
    );

    executeQuery($query, $bindings, "Training event ended successfully.", "Failed to end training event");

}

// Process the delete training event action
if (isset($_POST['delete_event_submit'])) {
    $event_id = $_POST['delete_event_id'];

    $query = "DELETE FROM TRAININGEVENT WHERE EVENTID = :event_id";
    $bindings = array(
        ':event_id' => $event_id,
    );

    executeQuery($query, $bindings, "Training event deleted successfully.", "Failed to delete training event");
}


// Function to display the modal for terminating an event
function displayTerminateEventModal($eventId)
{
    echo '<div class="modal fade" id="terminateEventModal-' . $eventId . '" tabindex="-1" role="dialog" aria-labelledby="terminateEventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="terminateEventModalLabel">Terminate Training Event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to terminate this Training Event?</p>
                    <form method="POST" action="">
                        <input type="hidden" name="terminate_event_id" value="' . $eventId . '">
                        <button type="submit" name="terminate_event_submit" class="btn btn-danger">Terminate Training Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>';
}


function generateEditEventModal($row, $trainingTypes, $officerNames)
{
    echo '<div class="modal fade" id="editEventModal-' . $row['EVENTID'] . '" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEventModalLabel">Edit Training Event: ' . $row['EVENTNAME'] . '</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="edit_event_id" value="' . $row['EVENTID'] . '">

                        <div class="form-group">
                            <label for="edit_training_type">Training Type:</label>
                            <select name="edit_training_type" id="edit_training_type" class="form-control" required>';
                            foreach ($trainingTypes as $trainingTypeId => $trainingType) {
                                $selected = ($row['TRGID'] == $trainingTypeId) ? 'selected' : '';
                                echo "<option value=\"$trainingTypeId\" $selected>$trainingType</option>";
                            }

                            echo '</select>
                        </div>

                        <div class="form-group">
                            <label for="edit_event_name">Event Name:</label>
                            <input type="text" name="edit_event_name" id="edit_event_name" class="form-control" value="' . $row['EVENTNAME'] . '" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_event_date">Event Date:</label>
                            <input type="date" name="edit_event_date" id="edit_event_date" class="form-control" value="' . date('Y-m-d', strtotime($row['EVENTDATE'])) . '" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_board_president">Board President:</label>
                            <select name="edit_board_president" id="edit_board_president" class="form-control" required>';
                            foreach ($officerNames as $soldierID => $officerName) {
                                $selected = ($row['BOARDPRESIDENTID'] == $soldierID) ? 'selected' : '';
                                echo "<option value=\"$soldierID\" $selected>$officerName</option>";
                            }

                            echo '</select>
                        </div>

                        <div class="form-group">
                            <label for="edit_authority_letter_no">Authority Letter No:</label>
                            <input type="text" name="edit_authority_letter_no" id="edit_authority_letter_no" class="form-control" value="' . $row['AUTHORITYLETTERNO'] . '" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_board_no">Board No:</label>
                            <input type="number" name="edit_board_no" id="edit_board_no" class="form-control" value="' . $row['BOARDNO'] . '" required>
                        </div>

                        <button type="submit" name="edit_event_submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>';
}



function generateDeleteEventModal($row)
{
    echo '<div class="modal fade" id="deleteEventModal-' . $row['EVENTID'] . '" tabindex="-1" role="dialog" aria-labelledby="deleteEventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteEventModalLabel">Delete Training Event: ' . $row['EVENTNAME'] . '</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this Training Event?</p>
                    <form method="POST" action="">
                        <input type="hidden" name="delete_event_id" value="' . $row['EVENTID'] . '">
                        <button type="submit" name="delete_event_submit" class="btn btn-danger">Delete Training Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>';
}


// Process the terminate training event action
if (isset($_POST['terminate_event_submit'])) {
    $event_id = $_POST['terminate_event_id'];
    $end_date = date('Y-m-d'); // Set the end date as the current system date

    $query = "UPDATE TRAININGEVENT SET STATUS = 'Terminated' WHERE EVENTID = :event_id";
    $bindings = array(
        ':event_id' => $event_id,
    );

    executeQuery($query, $bindings, "Training event terminated successfully.", "Failed to terminate training event");

    exit();
}

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Training Event Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addEventModal">Add Training Event</button>
        </div>
    </div>
</div>
<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEventModalLabel">Add Training Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="training_type">Training Type:</label>
                        <select name="training_type" id="training_type" class="form-control" required>
                            <option value="">Select Training Type</option>
                            <?php
                            foreach ($trainingTypes as $trainingTypeId => $trainingType) {
                                echo "<option value=\"$trainingTypeId\">$trainingType</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="event_name">Event Name:</label>
                        <input type="text" name="event_name" id="event_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="event_date">Event Date:</label>
                        <input type="date" name="event_date" id="event_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="board_president">Board President:</label>
                        <select name="board_president" id="board_president" class="form-control" required>
                            <option value="">Select Board President</option>
                            <?php
                            foreach ($officerNames as $soldierID => $officerName) {
                                echo "<option value=\"$soldierID\">$officerName</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="authority_letter_no">Authority Letter No:</label>
                        <input type="text" name="authority_letter_no" id="authority_letter_no" class="form-control"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="board_no">Board No:</label>
                        <input type="number" name="board_no" id="board_no" class="form-control" required>
                    </div>
                    <input type="submit" name="add_event_submit" value="Add Event" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>



<section class="content">
    <div class="container-fluid">
        <?php include '../includes/alert.php'; ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary expanded-card">
                    <div class="card-header">
                        <h3 class="card-title">Ongoing Training Events (
                            <?php echo count($trainingEvents); ?>)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Event Name</th>
                                    <th>Training Type</th>
                                    <th>Event Date</th>
                                    <th>Board President</th>
                                    <th>Authority Letter No</th>
                                    <th>Board No</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($trainingEvents as $row) {
                                    echo "<tr>";
                                    echo "<td>" . $row['EVENTID'] . "</td>";
                                    echo "<td><a href='training_details.php?event_id=" . $row['EVENTID'] . "'>" . $row['EVENTNAME'] . "</a></td>";
                                    echo "<td>" . $row['TRAINING_TYPE'] . "</td>";
                                    echo "<td>" . $row['EVENTDATE'] . "</td>";
                                    echo "<td>" . $row['BOARD_PRESIDENT'] . "</td>";
                                    echo "<td>" . $row['AUTHORITYLETTERNO'] . "</td>";
                                    echo "<td>" . $row['BOARDNO'] . "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#terminateEventModal-' . $row['EVENTID'] . '">
                                                    <i class="fas fa-ban"></i> Terminate
                                                </button>';
                                    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editEventModal-' . $row['EVENTID'] . '">
                                                <i class="fas fa-edit"></i> Edit
                                                </button>';
                                    echo "</td>";
                                    echo "</tr>";
                                    displayTerminateEventModal($row['EVENTID']);
                                    generateEditEventModal($row, $trainingTypes, $officerNames);
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php
                // Query to fetch terminated training events
                $query = "SELECT TE.EVENTID, TE.EVENTNAME, BT.TRGNAME AS TRAINING_TYPE, TE.EVENTDATE, R.RANK || ' ' || S.NAME AS BOARD_PRESIDENT, TE.AUTHORITYLETTERNO, TE.BOARDNO 
                    FROM TRAININGEVENT TE
                    LEFT JOIN BASICTRAINING BT ON TE.TRGID = BT.TRGID
                    LEFT JOIN SOLDIER S ON TE.BOARDPRESIDENTID = S.SOLDIERID
                    LEFT JOIN RANKS R ON S.RANKID = R.RANKID
                    WHERE TE.STATUS = 'Terminated'";

                $stmt = oci_parse($conn, $query);
                oci_execute($stmt);

                // Fetch all the rows into the $terminatedTrainingEvents array
                oci_fetch_all($stmt, $terminatedTrainingEvents, null, null, OCI_FETCHSTATEMENT_BY_ROW);

                oci_free_statement($stmt);
                ?>

                <div class="card card-secondary collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Terminated Training Events (
                            <?php echo count($terminatedTrainingEvents); ?>)
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Event Name</th>
                                    <th>Training Type</th>
                                    <th>Event Date</th>
                                    <th>Board President</th>
                                    <th>Authority Letter No</th>
                                    <th>Board No</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($terminatedTrainingEvents as $row) {
                                    echo "<tr>";
                                    echo "<tr>";
                                    echo "<td>" . $row['EVENTID'] . "</td>";
                                    echo "<td><a href='training_details.php?event_id=" . $row['EVENTID'] . "'>" . $row['EVENTNAME'] . "</a></td>";
                                    echo "<td>" . $row['TRAINING_TYPE'] . "</td>";
                                    echo "<td>" . $row['EVENTDATE'] . "</td>";
                                    echo "<td>" . $row['BOARD_PRESIDENT'] . "</td>";
                                    echo "<td>" . $row['AUTHORITYLETTERNO'] . "</td>";
                                    echo "<td>" . $row['BOARDNO'] . "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editEventModal-' . $row['EVENTID'] . '">
                                    <i class="fas fa-edit"></i> Edit
                                    </button>';
                                    echo '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteEventModal-' . $row['EVENTID'] . '">
                                    <i class="fas fa-trash"></i> Delete
                                    </button>';

                                    echo "</td>";
                                    echo "</tr>";
                                    generateEditEventModal($row, $trainingTypes, $officerNames);
                                    generateDeleteEventModal($row);
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>