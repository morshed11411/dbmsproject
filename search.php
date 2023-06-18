<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get selected filter values from the form
    $trade = $_POST['trade'];
    $rank = $_POST['rank'];
    $company = $_POST['company'];
    $overweight = $_POST['overweight'];
    $trainingStatus = $_POST['training_status'];

    // Construct the SQL query
    $query = "SELECT * FROM soldier_view WHERE 1=1"; // Start with a basic query

    // Add filters to the query based on the selected values
    if (!empty($trade)) {
        $query .= " AND trade = '$trade'";
    }
    if (!empty($rank)) {
        $query .= " AND rank = '$rank'";
    }
    if (!empty($company)) {
        $query .= " AND company = '$company'";
    }
    if (!empty($overweight)) {
        $query .= " AND overweight = '$overweight'";
    }
    if (!empty($trainingStatus)) {
        $query .= " AND training_status = '$trainingStatus'";
    }

    // Execute the query and fetch soldier information
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);

    // Display the retrieved soldier information in a table or any desired format
    echo "<table>";
    echo "<tr><th>Soldier ID</th><th>Name</th><th>Rank</th><th>Company</th></tr>";
    while ($row = oci_fetch_assoc($stmt)) {
        echo "<tr>";
        echo "<td>" . $row['SOLDIERID'] . "</td>";
        echo "<td>" . $row['NAME'] . "</td>";
        echo "<td>" . $row['RANK'] . "</td>";
        echo "<td>" . $row['COMPANY'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    oci_free_statement($stmt);
    oci_close($conn);
}
?>

<!-- HTML code for the search form -->
<form method="POST" action="search.php">
    <!-- Add filter options as desired -->
    <label for="trade">Trade:</label>
    <input type="text" name="trade">

    <label for="rank">Rank:</label>
    <input type="text" name="rank">

    <label for="company">Company:</label>
    <input type="text" name="company">

    <label for="overweight">Overweight:</label>
    <input type="checkbox" name="overweight" value="1">

    <label for="training_status">Training Status:</label>
    <select name="training_status">
        <option value="">-- Select Status --</option>
        <option value="passed">Passed</option>
        <option value="failed">Failed</option>
    </select>

    <button type="submit">Search</button>
</form>
