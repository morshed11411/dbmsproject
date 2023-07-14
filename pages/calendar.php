<?php
session_start();

include '../includes/connection.php';


// Add Event form submission
if (isset($_POST['add_event_submit'])) {
    // Retrieve form data
    $eventDate = $_POST['eventDate'];
    $eventName = $_POST['eventName'];
    $eventTimeFrom = $_POST['eventTimeFrom'];
    $eventTimeTo = $_POST['eventTimeTo'];
    $eventTime = $eventTimeFrom . ' - ' . $eventTimeTo;
    $eventLocation = $_POST['eventLocation'];
    $attendeesResponsibility = $_POST['attendeesResponsibility'];
    $remarks = $_POST['remarks'];
    // Format the eventDate variable to match the format expected by the TO_DATE function
    $formattedDate = date('d-M-Y', strtotime($eventDate));

    // Prepare the INSERT statement
    $query = "INSERT INTO events (event_date, event_name, event_time, event_location, attendees_responsibility, remarks)
          VALUES (TO_DATE(:eventDate, 'DD-MON-YYYY'), :eventName, :eventTime, :eventLocation, :attendeesResponsibility, :remarks)";
    $stmt = oci_parse($conn, $query);

    // Bind the parameters
    oci_bind_by_name($stmt, ':eventDate', $formattedDate);
    oci_bind_by_name($stmt, ':eventName', $eventName);
    oci_bind_by_name($stmt, ':eventTime', $eventTime);
    oci_bind_by_name($stmt, ':eventLocation', $eventLocation);
    oci_bind_by_name($stmt, ':attendeesResponsibility', $attendeesResponsibility);
    oci_bind_by_name($stmt, ':remarks', $remarks);


    // Execute the statement
    if (oci_execute($stmt)) {
        // Event added successfully
        $_SESSION['success'] = "Event added successfully.";
        header("Location: calendar.php");
        exit();
    } else {
        // Failed to add event
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add event: " . $error['message'];
        header("Location: calendar.php");
        exit();
    }
}
// Free statement resources
oci_free_statement($stmt);


// Fetch events from the database
$query = 'SELECT * FROM events ORDER BY EVENT_TIME';
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$events = [];
while ($row = oci_fetch_assoc($stmt)) {
    $events[] = $row;
}

// Close the database connection
oci_free_statement($stmt);
oci_close($conn);

// Get the current month and year
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$currentYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Convert the month and year values to integers
$currentMonth = intval($currentMonth);
$currentYear = intval($currentYear);

// Get the total number of days in the current month
$totalDays = date('t', strtotime("$currentYear-$currentMonth-01"));

// Get the month and year to display
$displayMonth = date('F', strtotime("$currentYear-$currentMonth-01"));
$displayYear = $currentYear;

// Calculate the previous and next month
$previousMonth = $currentMonth - 1;
$previousYear = $currentYear;
if ($previousMonth === 0) {
    $previousMonth = 12;
    $previousYear--;
}

$nextMonth = $currentMonth + 1;
$nextYear = $currentYear;
if ($nextMonth === 13) {
    $nextMonth = 1;
    $nextYear++;
}
?>

<?php include '../includes/header.php'; ?>

<div class="card-body">
    <?php include '../includes/alert.php'; ?>

    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Soldier Calendar -
                <?php echo $displayMonth . ' ' . $displayYear; ?>
            </h3>
        </div>
        <div class="text-right">
            <a class="btn btn-primary" href="?month=<?php echo $previousMonth; ?>&year=<?php echo $previousYear; ?>"><i
                    class="fas fa-arrow-left"></i></a>
            <a class="btn btn-primary" href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>"><i
                    class="fas fa-arrow-right"></i></a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <?php
            // Calculate the starting day of the month
            $startDay = date('N', strtotime("$currentYear-$currentMonth-01"));

            // Calculate the number of empty cells before the starting day
            $emptyCells = $startDay - 1;

            // Calculate the total number of rows needed
            $totalRows = ceil(($totalDays + $emptyCells) / 7);

            // Get today's date
            $today = date('Y-m-d');
            $todayYear = date('Y');
            $todayMonth = date('m');
            ?>

            <table class="table table-bordered calendar-table">
                <thead>
                    <tr>
                        <th>Sun</th>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Initialize the day counter
                    $dayCounter = 0;

                    // Loop through each row
                    for ($row = 1; $row <= $totalRows; $row++) {
                        echo '<tr>';

                        // Loop through each day of the week
                        for ($col = 1; $col <= 7; $col++) {
                            if (($row == 1 && $col <= $emptyCells) || $dayCounter > $totalDays) {
                                // Display empty cells before the starting day or after the total days
                                echo '<td></td>';
                            } else {
                                // Get the current date
                                $currentDate = sprintf('%02d', $dayCounter);

                                // Check if there are any events on the current date
                                $eventCount = 0;
                                $currentDateFormatted = sprintf('%02d-%s-%02d', $currentDate, date('M', strtotime("$currentYear-$currentMonth-01")), substr($currentYear, -2));

                                foreach ($events as $event) {
                                    $eventDate = date('d-M-y', strtotime($event['EVENT_DATE']));
                                    if ($eventDate === $currentDateFormatted) {
                                        $eventCount++;
                                    }
                                }

                                // Determine the class for the date cell based on event availability and today's date
                                $cellClass = $eventCount > 0 ? 'bg-success' : '';
                                $cellClass .= ($currentYear == $todayYear && $currentMonth == $todayMonth && $currentDate == date('d')) ? ' today' : '';

                                // Display the date with or without events
                                echo '<td class="' . $cellClass . '" data-toggle="modal" data-target="#eventModal" data-date="' . $currentDate . '">';

                                // Display the event indicator circle within the table cell
                                if ($eventCount > 0) {
                                    echo '<div class="date-container">';
                                    echo '<div class="event-indicator">' . $eventCount . '</div>';
                                    echo '<div class="date">' . $currentDate . '</div>';
                                    echo '</div>';
                                } else {
                                    echo '<div class="date">' . $currentDate . '</div>';
                                }
                                echo '</td>';

                                // Increment the day counter
                                $dayCounter++;
                            }
                        }

                        echo '</tr>';
                    }
                    ?>

                </tbody>
            </table>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>


<!-- Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Ser</th>
                            <th>Name</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Attendees/Responsibility</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="eventModalBody">
                    </tbody>
                </table>
                <button class="btn btn-primary" data-toggle="modal" data-target="#addEventModal" data-dismiss="modal">Add Event</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Event Modal -->
<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEventModalLabel">Add New Event - <span id="selectedDate">
                        <?php echo isset($_POST['eventDate']) ? $_POST['eventDate'] : ''; ?>
                    </span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <input type="hidden" id="eventDate" name="eventDate" value="">
                    </div>
                    <div class="form-group">
                        <label for="eventName">Event Name</label>
                        <input type="text" class="form-control" id="eventName" name="eventName" required>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="eventTimeFrom">Time From</label>
                            <input type="time" class="form-control" id="eventTimeFrom" name="eventTimeFrom" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="eventTimeTo">Time To</label>
                            <input type="time" class="form-control" id="eventTimeTo" name="eventTimeTo" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="eventLocation">Event Location</label>
                        <input type="text" class="form-control" id="eventLocation" name="eventLocation" required>
                    </div>
                    <div class="form-group">
                        <label for="attendeesResponsibility">Attendees/Responsibility</label>
                        <input type="text" class="form-control" id="attendeesResponsibility"
                            name="attendeesResponsibility" required>
                    </div>
                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" name="add_event_submit">Add Event</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
  // Event modal show event
  $('#eventModal').on('show.bs.modal', function(event) {
    var triggerElement = $(event.relatedTarget);
    var date = triggerElement.data('date');
    var selectedMonth = <?php echo $currentMonth; ?>;
    var selectedYear = <?php echo $currentYear; ?>;

    // Modify the modal title with the selected date
    $(this).find('.modal-title').text('Events - ' + date);

    // Get the events for the selected date
    var events = <?php echo json_encode($events); ?>;
    var eventModalBody = $('#eventModalBody');
    eventModalBody.empty();

    var serialNumber = 1; // Counter variable for serial number

    for (var i = 0; i < events.length; i++) {
      var eventDate = new Date(events[i].EVENT_DATE);
      var eventMonth = eventDate.getMonth() + 1;
      var eventYear = eventDate.getFullYear();

      if (eventMonth === selectedMonth && eventYear === selectedYear && eventDate.getDate() == date) {
        var eventRow = '<tr>' +
          '<td>' + serialNumber + '</td>' +
          '<td>' + events[i].EVENT_NAME + '</td>' +
          '<td>' + events[i].EVENT_TIME + '</td>' +
          '<td>' + events[i].EVENT_LOCATION + '</td>' +
          '<td>' + events[i].ATTENDEES_RESPONSIBILITY + '</td>' +
          '<td>' + events[i].REMARKS + '</td>' +
          '</tr>';

        eventModalBody.append(eventRow);

        serialNumber++; // Increment the serial number counter
      }
    }

    // Set the selected date in the "Add Event" form
    $('#eventDate').val(selectedYear + '-' + selectedMonth.toString().padStart(2, '0') + '-' + date);
    $('#selectedDate').text(selectedYear + '-' + selectedMonth.toString().padStart(2, '0') + '-' + date);
  });

});

</script>

<style>
    .calendar-table td.today {
        background-color: #f0ad4e;
        border: 2px dashed red;
    }

    .calendar-table td:hover {
        background-color: grey;
        cursor: pointer;
    }

    .calendar-table td {
        height: 70px;
        vertical-align: middle;
        cursor: pointer;
        position: relative;
    }

    .event-indicator {
        position: absolute;
        top: 50%;
        right: 5px;
        transform: translate(-50%, -50%);
        background-color: red;
        color: white;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        text-align: center;
        line-height: 18px;
        font-size: 12px;
    }
</style>

