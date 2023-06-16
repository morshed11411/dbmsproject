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
                                                <th>Joining Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT s.SoldierID, s.Name, r.Rank, t.Trade, c.CompanyName, l.LeaveType, l.LeaveStartDate, l.LeaveEndDate, s.DateOfEnroll
                                                      FROM Soldier s
                                                      JOIN Ranks r ON s.RankID = r.RankID
                                                      JOIN Trade t ON s.TradeID = t.TradeID
                                                      JOIN Company c ON s.CompanyID = c.CompanyID
                                                      JOIN LeaveModule l ON s.SoldierID = l.SoldierID
                                                      WHERE TRUNC(l.LeaveStartDate) = TRUNC(SYSDATE)";
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
                                                echo "<td>" . $row['DATEOFENROLL'] . "</td>";
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