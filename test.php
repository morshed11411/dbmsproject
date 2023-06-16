<?php
include 'conn.php'; // Include the database connection file

if (isset($_POST['submit'])) {
    $soldierID = $_POST['soldier_id'];

    // Write the SQL query to fetch the leave history for the specified soldier
    $query = "SELECT lm.LeaveStartDate, lm.LeaveType, (lm.LeaveEndDate - lm.LeaveStartDate + 1) AS LeaveDuration
              FROM LeaveModule lm
              WHERE lm.SoldierID = :soldierID";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldierID', $soldierID);
    oci_execute($stmt);

    // Check if any leave history records are found
    if (oci_fetch($stmt)) {
        // Display the leave history in a table format
        echo "<table>
                <thead>
                    <tr>
                        <th>Leave Date</th>
                        <th>Leave Type</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>";

        // Iterate over the result set and display each leave history record
        do {
            $leaveDate = oci_result($stmt, 'LEAVESTARTDATE');
            $leaveType = oci_result($stmt, 'LEAVETYPE');
            $duration = oci_result($stmt, 'LEAVEDURATION');

            echo "<tr>
                    <td>$leaveDate</td>
                    <td>$leaveType</td>
                    <td>$duration</td>
                </tr>";
        } while (oci_fetch($stmt));

        echo "</tbody>
            </table>";
    } else {
        echo "No leave history found for the specified soldier.";
    }

    oci_free_statement($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave History</title>
</head>
<body>
    <h1>Leave History</h1>
    <form method="post" action="">
        <label for="soldier_id">Soldier ID:</label>
        <input type="text" name="soldier_id" id="soldier_id" required>
        <input type="submit" name="submit" value="Get Leave History">
    </form>
</body>
</html>
