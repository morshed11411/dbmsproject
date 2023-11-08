<?php
session_start();

include '../includes/connection.php';

// Process the add training event action
if (isset($_POST['add_event_submit'])) {
    $training_type = $_POST['training_type'];
    $event_date = $_POST['event_date'];
    $board_president = $_POST['board_president'];
    $authority_letter_no = $_POST['authority_letter_no'];
    $board_no = $_POST['board_no'];

    $query = "INSERT INTO TrainingEvent (TrainingType, EventDate, BoardPresident, AuthorityLetterNo, BoardNo) VALUES (:training_type, TO_DATE(:event_date, 'YYYY-MM-DD'), :board_president, :authority_letter_no, :board_no)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':training_type', $training_type);
    oci_bind_by_name($stmt, ':event_date', $event_date);
    oci_bind_by_name($stmt, ':board_president', $board_president);
    oci_bind_by_name($stmt, ':authority_letter_no', $authority_letter_no);
    oci_bind_by_name($stmt, ':board_no', $board_no);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Training event added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add training event: " . $error['message'];
    }

    oci_free_statement($stmt);
}

// Process the edit training event action
if (isset($_POST['edit_event_submit'])) {
    $event_id = $_POST['edit_event_id'];
    $training_type = $_POST['edit_training_type'];
    $event_date = $_POST['edit_event_date'];
    $board_president = $_POST['edit_board_president'];
    $authority_letter_no = $_POST['edit_authority_letter_no'];
    $board_no = $_POST['edit_board_no'];

    $query = "UPDATE TRAININGEVENT SET TRAININGTYPE = :TRAINING_TYPE, EVENTDATE = TO_DATE(:EVENT_DATE, 'YYYY-MM-DD'), BOARDPRESIDENT = :BOARD_PRESIDENT, AUTHORITYLETTERNO = :AUTHORITY_LETTER_NO, BOARDNO = :BOARD_NO WHERE EVENTID = :EVENT_ID";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':training_type', $training_type);
    oci_bind_by_name($stmt, ':event_date', $event_date);
    oci_bind_by_name($stmt, ':board_president', $board_president);
    oci_bind_by_name($stmt, ':authority_letter_no', $authority_letter_no);
    oci_bind_by_name($stmt, ':board_no', $board_no);
    oci_bind_by_name($stmt, ':event_id', $event_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Training event updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update training event: " . $error['message'];
    }

    oci_free_statement($stmt);
}

// Process the end training event action
if (isset($_POST['end_event_submit'])) {
    $event_id = $_POST['event_id'];
    $end_date = date('Y-m-d'); // Set the end date as the current system date

    $query = "UPDATE TRAININGEVENT SET ENDDATE = TO_DATE(:END_DATE, 'YYYY-MM-DD') WHERE EVENTID = :EVENT_ID";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':end_date', $end_date);
    oci_bind_by_name($stmt, ':event_id', $event_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Training event ended successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to end training event: " . $error['message'];
    }

    oci_free_statement($stmt);
    header("Location: create_event.php");
    exit();
}

// Process the delete training event action
if (isset($_POST['delete_event_submit'])) {
    $event_id = $_POST['delete_event_id'];

    $query = "DELETE FROM TrainingEvent WHERE EventID = :event_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':event_id', $event_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Training event deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete training event: " . $error['message'];
    }

    oci_free_statement($stmt);
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
                        <input type="text" name="training_type" id="training_type" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="event_date">Event Date:</label>
                        <input type="date" name="event_date" id="event_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="board_president">Board President:</label>
                        <input type="text" name="board_president" id="board_president" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="authority_letter_no">Authority Letter No:</label>
                        <input type="text" name="authority_letter_no" id="authority_letter_no" class="form-control" required>
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
                <div class="card">
                    <div class="card-body">
                        <h5>Training Events</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Event ID</th>
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
                                $query = "SELECT EVENTID, TRAININGTYPE, EVENTDATE, BOARDPRESIDENT, AUTHORITYLETTERNO, BOARDNO FROM TRAININGEVENT";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);

                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['EVENTID'] .  "</td>";
                                    echo "<td>" . $row['TRAININGTYPE'] . "</td>";
                                    echo "<td>" . $row['EVENTDATE'] . "</td>";
                                    echo "<td>" . $row['BOARDPRESIDENT'] . "</td>";
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

                                    // Edit Event Modal
                                    echo '<div class="modal fade" id="editEventModal-' . $row['EVENTID'] . '" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editEventModalLabel">Edit Training Event</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_event_id" value="' . $row['EVENTID'] . '">
                                                        <div class="form-group">
                                                            <label for="edit_training_type">Training Type:</label>
                                                            <input type="text" name="edit_training_type" id="edit_training_type" class="form-control" value="' . $row['TRAININGTYPE'] . '" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="edit_event_date">Event Date:</label>
                                                            <input type="date" name="edit_event_date" id="edit_event_date" class="form-control" value="' . date('Y-m-d', strtotime($row['EVENTDATE']))  . '" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="edit_board_president">Board President:</label>
                                                            <input type="text" name="edit_board_president" id="edit_board_president" class="form-control" value="' . $row['BOARDPRESIDENT'] . '" required>
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

                                    // Delete Event Modal
                                    echo '<div class="modal fade" id="deleteEventModal-' . $row['EVENTID'] . '" tabindex="-1" role="dialog" aria-labelledby="deleteEventModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteEventModalLabel">Delete Training Event</h5>
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

                                oci_free_statement($stmt);
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
