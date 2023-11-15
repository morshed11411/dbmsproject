<?php
include '../includes/connection.php'; // Include your database connection file

function getParadeState($conn) {
    $queryParadeState = "SELECT * FROM parade_state_view";
    $stmtParadeState = oci_parse($conn, $queryParadeState);
    oci_execute($stmtParadeState);

    $paradeState = [];
    while ($row = oci_fetch_assoc($stmtParadeState)) {
        $paradeState[] = $row;
    }

    oci_free_statement($stmtParadeState);

    return $paradeState;
}

function calculateTotal($paradeState) {
    $total = array(
        'Auth' => 0,
        'Granted' => 0,
        'Leave' => 0,
        'MedicalDisposal' => 0,
        'Absent' => 0,
        'Present' => 0
    );

    foreach ($paradeState as $row) {
        $total['Auth'] += $row['Auth'];
        $total['Granted'] += $row['Granted'];
        $total['Leave'] += $row['Leave'];
        $total['MedicalDisposal'] += $row['MedicalDisposal'];
        $total['Absent'] += $row['Absent'];
        $total['Present'] += $row['Present'];
    }

    return $total;
}

function displayParadeState($paradeState) {
    echo '<table class="table table-bordered" id="paradeState">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Company Name</th>';
    echo '<th>Auth</th>';
    echo '<th>Held</th>';
    echo '<th>Leave</th>';
    echo '<th>Disposal</th>';
    echo '<th>Absent</th>';
    echo '<th>Present</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach ($paradeState as $row) {
        echo '<tr>';
        echo '<td>' . $row['COMPANYNAME'] . '</td>';
        echo '<td>' . $row['Auth'] . '</td>';
        echo '<td>' . $row['Granted'] . '</td>';
        echo '<td>' . $row['Leave'] . '</td>';
        echo '<td>' . $row['MedicalDisposal'] . '</td>';
        echo '<td>' . $row['Absent'] . '</td>';
        echo '<td>' . $row['Present'] . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}

// Fetch parade state data
$paradeStateData = getParadeState($conn);

// Calculate total values
$totalValues = calculateTotal($paradeStateData);

// Include your header file
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div class="text-left">
                    <h3>Parade State Report</h3>
                </div>
                <div class="text-right">
                    <?php
                    date_default_timezone_set("Your/Timezone"); // Replace "Your/Timezone" with your desired timezone
                    $currentDate = date("j F, Y"); // Format the current date as desired
                    echo "<h3>Date: " . $currentDate . "</h3>";
                    ?>
                </div>
            </div>

            <?php
            // Display parade state data
            displayParadeState($paradeStateData);
            ?>

            <!-- Output the total row -->
            <table class="table table-bordered">
                <tbody>
                    <tr class='total-row'>
                        <td>Total</td>
                        <td><?php echo $totalValues['Auth']; ?></td>
                        <td><?php echo $totalValues['Granted']; ?></td>
                        <td><?php echo $totalValues['Leave']; ?></td>
                        <td><?php echo $totalValues['MedicalDisposal']; ?></td>
                        <td><?php echo $totalValues['Absent']; ?></td>
                        <td><?php echo $totalValues['Present']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Include your footer file
include '../includes/footer.php';
?>
