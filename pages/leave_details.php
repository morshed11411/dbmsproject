<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php';
include 'views/auth.php';
include 'conn.php';
?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                <button type="button" class="btn btn-warning float-right" onclick="window.print()">
                        <h5>Print</h5>
                    </button>
                    <h1>Leave Details</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">


                                    <!-- Section for Soldiers on Leave Today -->
                                    <h3>Soldiers on Leave Today</h3>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Soldier ID</th>
                                                <th>Name</th>
                                                <th>Rank</th>
                                                <th>Trade</th>
                                                <th>Company Name</th>
                                                <th>Leave Type</th>
                                                <th>Leave Start Date</th>
                                                <th>Leave End Date</th>
                                                <th>Join In</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT SoldierID, Name, Rank, Trade, CompanyName, LeaveType, LeaveStartDate, LeaveEndDate, RemainingLeave
                                            FROM todays_leave_view";
                                            $stmt = oci_parse($conn, $query);
                                            oci_execute($stmt);

                                            while ($row = oci_fetch_assoc($stmt)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['SOLDIERID'] . "</td>";
                                                echo "<td>" . $row['NAME'] . "</td>";
                                                echo "<td>" . $row['RANK'] . "</td>";
                                                echo "<td>" . $row['TRADE'] . "</td>";
                                                echo "<td>" . $row['COMPANYNAME'] . "</td>";
                                                echo "<td>" . $row['LEAVETYPE'] . "</td>";
                                                echo "<td>" . $row['LEAVESTARTDATE'] . "</td>";
                                                echo "<td>" . $row['LEAVEENDDATE'] . "</td>";
                                                echo "<td>" . $row['REMAININGLEAVE'] . " Days </td>";
                                                echo "</tr>";
                                            }
                                            oci_free_statement($stmt);
                                            oci_close($conn);
                                            ?>
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include 'views/footer.php'; ?>

    </div>

</body>

</html>