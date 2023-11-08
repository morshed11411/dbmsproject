<?php
session_start();

// Include database connection script
include '../includes/connection.php';

// Process the form submission to create a new training event
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_event_submit'])) {
    $trainingType = $_POST['training_type'];
    $eventDate = $_POST['event_date'];
    $boardPresident = $_POST['board_president'];
    $authorityLetterNo = $_POST['authority_letter_no'];

    // Generate a unique board number based on the creation date (you need to implement this logic)
    $boardNo = generateUniqueBoardNumber($eventDate);

    $query = "INSERT INTO TrainingEvent (TrainingType, EventDate, CreationTime, BoardPresident, AuthorityLetterNo, BoardNo)
              VALUES (:training_type, TO_DATE(:event_date, 'yyyy-mm-dd'), CURRENT_TIMESTAMP, :board_president, :authority_letter_no, :board_no)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':training_type', $trainingType);
    oci_bind_by_name($stmt, ':event_date', $eventDate);
    oci_bind_by_name($stmt, ':board_president', $boardPresident);
    oci_bind_by_name($stmt, ':authority_letter_no', $authorityLetterNo);
    oci_bind_by_name($stmt, ':board_no', $boardNo);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Training event added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add training event: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: create_training_event.php");
    exit();
}

// Include your header file
include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Create New Training Event</h3>
        </div>
    </div>
</div>
<?php
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
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
                            <button type="submit" name="create_event_submit" class="btn btn-primary">Create Event</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include your footer file
include '../includes/footer.php';
?>
