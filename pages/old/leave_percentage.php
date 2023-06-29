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
                                <!-- Section for Company-wise Leave Percentage -->
                                <h3>Company-wise Leave Percentage Today - <?php echo date('d-m-y'); ?></h3>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Company Name</th>
                                            <th>Total Manpower</th>
                                            <th>Total On Leave</th>
                                            <th>Leave Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Retrieve all company names
                                        $query = "SELECT CompanyName FROM Company";
                                        $stmt = oci_parse($conn, $query);
                                        oci_execute($stmt);

                                        while ($row = oci_fetch_assoc($stmt)) {
                                            echo "<tr>";
                                            echo "<td>" . $row['COMPANYNAME'] . "</td>";

                                            // Get total manpower for the company
                                            $queryTotalManpower = "SELECT COUNT(*) AS TotalManpower FROM Soldier WHERE CompanyID = (SELECT CompanyID FROM Company WHERE CompanyName = :company_name)";
                                            $stmtTotalManpower = oci_parse($conn, $queryTotalManpower);
                                            oci_bind_by_name($stmtTotalManpower, ':company_name', $row['COMPANYNAME']);
                                            oci_execute($stmtTotalManpower);
                                            $totalManpower = oci_fetch_assoc($stmtTotalManpower);

                                            // Get total on leave count for the company
                                            $queryOnLeave = "SELECT COUNT(*) AS OnLeaveCount
                                            FROM Soldier s
                                            JOIN Company c ON s.CompanyID = c.CompanyID
                                            JOIN LeaveModule l ON s.SoldierID = l.SoldierID
                                            WHERE l.LeaveStartDate <= TRUNC(SYSDATE)
                                            AND l.LeaveEndDate >= TRUNC(SYSDATE)
                                            AND c.CompanyName = :company_name";

                                            $stmtOnLeave = oci_parse($conn, $queryOnLeave);
                                            oci_bind_by_name($stmtOnLeave, ':company_name', $row['COMPANYNAME']);
                                            oci_execute($stmtOnLeave);
                                            $onLeaveCount = oci_fetch_assoc($stmtOnLeave);

                                            $leavePercentage = 0;
                                            if ($totalManpower && $totalManpower['TOTALMANPOWER'] > 0) {
                                                $leavePercentage = round(($onLeaveCount['ONLEAVECOUNT'] / $totalManpower['TOTALMANPOWER']) * 100);
                                            }

                                            echo "<td>" . $totalManpower['TOTALMANPOWER'] . "</td>";
                                            echo "<td>" . $onLeaveCount['ONLEAVECOUNT'] . "</td>";
                                            echo "<td>" . $leavePercentage . "%</td>";

                                            oci_free_statement($stmtTotalManpower);
                                            oci_free_statement($stmtOnLeave);
                                            echo "</tr>";
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
    </div>
    <?php include 'views/footer.php'; ?>

</div>


</body>

</html>