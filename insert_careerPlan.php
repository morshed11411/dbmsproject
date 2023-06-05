<html>
<head>
    <title>Insert CareerPlan Data</title>
</head>
<body>
    <h2>Insert CareerPlan Data</h2>
    <form method="post" action="">
        <label for="plan_id">Plan ID:</label>
        <input type="text" name="plan_id" id="plan_id" required><br>

        <label for="first_cycle">First Cycle:</label>
        <input type="text" name="first_cycle" id="first_cycle" required><br>

        <label for="second_cycle">Second Cycle:</label>
        <input type="text" name="second_cycle" id="second_cycle" required><br>

        <label for="third_cycle">Third Cycle:</label>
        <input type="text" name="third_cycle" id="third_cycle" required><br>

        <label for="fourth_cycle">Fourth Cycle:</label>
        <input type="text" name="fourth_cycle" id="fourth_cycle" required><br>

        <input type="submit" name="submit" value="Submit">
    </form>

<?php
if (isset($_POST['submit'])) {
    $plan_id = $_POST['plan_id'];
    $first_cycle = $_POST['first_cycle'];
    $second_cycle = $_POST['second_cycle'];
    $third_cycle = $_POST['third_cycle'];
    $fourth_cycle = $_POST['fourth_cycle'];

    $conn = oci_connect('UMS', '12345', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        echo "Failed to connect to Oracle: " . $e['message'];
    } else {
        $query = "INSERT INTO CarrierPlan (PlanID, FirstCycle, SecondCycle, ThirdCycle, FourthCycle) VALUES (:plan_id, :first_cycle, :second_cycle, :third_cycle, :fourth_cycle)";
        $stmt = oci_parse($conn, $query);

        oci_bind_by_name($stmt, ':plan_id', $plan_id);
        oci_bind_by_name($stmt, ':first_cycle', $first_cycle);
        oci_bind_by_name($stmt, ':second_cycle', $second_cycle);
        oci_bind_by_name($stmt, ':third_cycle', $third_cycle);
        oci_bind_by_name($stmt, ':fourth_cycle', $fourth_cycle);

        $result = oci_execute($stmt);
        if ($result) {
            echo "CareerPlan data inserted successfully.";
        } else {
            $e = oci_error($stmt);
            echo "Failed to insert CareerPlan data: " . $e['message'];
        }

        oci_free_statement($stmt);
        oci_close($conn);
    }
}
?>
</body>
</html>
