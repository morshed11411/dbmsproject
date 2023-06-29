<?php
include 'views/auth.php';
include 'conn.php';

if (isset($_GET['company_id'])) {
    $company_id = $_GET['company_id'];

    $query = "SELECT Soldier.SOLDIERID, Ranks.RANK, Trade.TRADE, Soldier.NAME,
              LISTAGG(Appointments.APPOINTMENTNAME, ', ') WITHIN GROUP (ORDER BY Appointments.APPOINTMENTNAME) AS APPOINTMENTS
              FROM Soldier
              LEFT JOIN Ranks ON Soldier.RANKID = Ranks.RANKID
              LEFT JOIN Trade ON Soldier.TRADEID = Trade.TRADEID
              LEFT JOIN SoldierAppointment ON Soldier.SOLDIERID = SoldierAppointment.SOLDIERID
              LEFT JOIN Appointments ON SoldierAppointment.APPOINTMENTID = Appointments.APPOINTMENTID
              WHERE Soldier.COMPANYID = :company_id
              GROUP BY Soldier.SOLDIERID, Ranks.RANK, Trade.TRADE, Soldier.NAME ORDER BY Soldier.SOLDIERID";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':company_id', $company_id);
    oci_execute($stmt);
} else {
    header("Location: index.php"); // Redirect to the main page if company_id is not provided
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Company Details</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Soldier ID</th>
                                        <th>Rank</th>
                                        <th>Trade</th>
                                        <th>Soldier Name</th>
                                        <th>Appointment(s)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = oci_fetch_assoc($stmt)) {
                                        echo "<tr>";
                                        echo "<td>" . $row['SOLDIERID'] . "</td>";
                                        echo "<td>" . $row['RANK'] . "</td>";
                                        echo "<td>" . $row['TRADE'] . "</td>";
                                        echo "<td>" . $row['NAME'] . "</td>";
                                        echo "<td>" . $row['APPOINTMENTS'] . "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include 'views/footer.php'; ?>
    </div>
</body>

</html>

<?php
oci_free_statement($stmt);
oci_close($conn);
?>
