<!DOCTYPE html>
<html lang="en">

<?php
include 'views/head.php';
include 'views/auth.php';
include 'conn.php'; // Include the database connection file

// Fetch the company information for each cycle
$queryCompanies = "SELECT c.COMPANYID, c.COMPANYNAME,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') THEN s.SOLDIERID END) AS TOTAL_SOLDIERS,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.FIRSTCYCLE = 'Admin' THEN s.SOLDIERID END) AS FIRST_CYCLE_ADMIN,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.FIRSTCYCLE = 'Leave' THEN s.SOLDIERID END) AS FIRST_CYCLE_PLEAVE,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.FIRSTCYCLE = 'Training' THEN s.SOLDIERID END) AS FIRST_CYCLE_TRAINING,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.SECONDCYCLE = 'Admin' THEN s.SOLDIERID END) AS SECOND_CYCLE_ADMIN,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.SECONDCYCLE = 'Leave' THEN s.SOLDIERID END) AS SECOND_CYCLE_PLEAVE,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.SECONDCYCLE = 'Training' THEN s.SOLDIERID END) AS SECOND_CYCLE_TRAINING,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.THIRDCYCLE = 'Admin' THEN s.SOLDIERID END) AS THIRD_CYCLE_ADMIN,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.THIRDCYCLE = 'Leave' THEN s.SOLDIERID END) AS THIRD_CYCLE_PLEAVE,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.THIRDCYCLE = 'Training' THEN s.SOLDIERID END) AS THIRD_CYCLE_TRAINING,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.FOURTHCYCLE = 'Admin' THEN s.SOLDIERID END) AS FOURTH_CYCLE_ADMIN,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.FOURTHCYCLE = 'Leave' THEN s.SOLDIERID END) AS FOURTH_CYCLE_PLEAVE,
    COUNT(CASE WHEN r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO') AND cp.FOURTHCYCLE = 'Training' THEN s.SOLDIERID END) AS FOURTH_CYCLE_TRAINING
FROM Company c
LEFT JOIN Soldier s ON c.COMPANYID = s.COMPANYID
LEFT JOIN Ranks r ON s.RANKID = r.RANKID
LEFT JOIN CarrierPlan cp ON s.SOLDIERID = cp.PLANID
WHERE r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO')
GROUP BY c.COMPANYID, c.COMPANYNAME";

$currentMonth = date('m'); // Get the current month (e.g., 01 for January, 02 for February, etc.)

// Determine the current cycle based on the month
if ($currentMonth >= 1 && $currentMonth <= 3) {
    $currentCycle = 'First Cycle';
} elseif ($currentMonth >= 4 && $currentMonth <= 6) {
    $currentCycle = 'Second Cycle';
} elseif ($currentMonth >= 7 && $currentMonth <= 9) {
    $currentCycle = 'Third Cycle';
} else {
    $currentCycle = 'Fourth Cycle';
}


$stmtCompanies = oci_parse($conn, $queryCompanies);
oci_execute($stmtCompanies);
?>

<head>
    <meta charset="UTF-8">
    <title>Career Plan</title>
</head>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
            <div class="container-fluid">
    <div class="alert alert-info alert-dismissible text-center">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><p><b> <?php echo $currentCycle; ?> </b> is running</p> </h4>
    </div>
</div>


                <div class="container-fluid">
                    <h1>Career Plan</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3>First Cycle</h3>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Company</th>
                                                <th>Total Soldiers</th>
                                                <th>Admin</th>
                                                <th>Leave</th>
                                                <th>Training</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($company = oci_fetch_assoc($stmtCompanies)) {
                                                $companyID = $company['COMPANYID'];
                                                $companyName = $company['COMPANYNAME'];
                                                $totalSoldiers = $company['TOTAL_SOLDIERS'];
                                                $firstCycleAdmin = $company['FIRST_CYCLE_ADMIN'];
                                                $firstCyclePLeave = $company['FIRST_CYCLE_PLEAVE'];
                                                $firstCycleTraining = $company['FIRST_CYCLE_TRAINING'];

                                                echo "<tr>
                                                        <td><a href='manage_career_plan.php?company=$companyID'>$companyName</a></td>
                                                        <td>$totalSoldiers</td>
                                                        <td>$firstCycleAdmin</td>
                                                        <td>$firstCyclePLeave</td>
                                                        <td>$firstCycleTraining</td>
                                                    </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3>Second Cycle</h3>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Company</th>
                                                <th>Total Soldiers</th>
                                                <th>Admin</th>
                                                <th>Leave</th>
                                                <th>Training</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            oci_execute($stmtCompanies); // Re-execute the query to start from the beginning
                                            while ($company = oci_fetch_assoc($stmtCompanies)) {
                                                $companyID = $company['COMPANYID'];
                                                $companyName = $company['COMPANYNAME'];
                                                $totalSoldiers = $company['TOTAL_SOLDIERS'];
                                                $secondCycleAdmin = $company['SECOND_CYCLE_ADMIN'];
                                                $secondCyclePLeave = $company['SECOND_CYCLE_PLEAVE'];
                                                $secondCycleTraining = $company['SECOND_CYCLE_TRAINING'];

                                                echo "<tr>
                                                        <td><a href='manage_career_plan.php?company=$companyID'>$companyName</a></td>
                                                        <td>$totalSoldiers</td>
                                                        <td>$secondCycleAdmin</td>
                                                        <td>$secondCyclePLeave</td>
                                                        <td>$secondCycleTraining</td>
                                                    </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3>Third Cycle</h3>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Company</th>
                                                <th>Total Soldiers</th>
                                                <th>Admin</th>
                                                <th>Leave</th>
                                                <th>Training</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            oci_execute($stmtCompanies); // Re-execute the query to start from the beginning
                                            while ($company = oci_fetch_assoc($stmtCompanies)) {
                                                $companyID = $company['COMPANYID'];
                                                $companyName = $company['COMPANYNAME'];
                                                $totalSoldiers = $company['TOTAL_SOLDIERS'];
                                                $thirdCycleAdmin = $company['THIRD_CYCLE_ADMIN'];
                                                $thirdCyclePLeave = $company['THIRD_CYCLE_PLEAVE'];
                                                $thirdCycleTraining = $company['THIRD_CYCLE_TRAINING'];

                                                echo "<tr>
                                                        <td><a href='manage_career_plan.php?company=$companyID'>$companyName</a></td>
                                                        <td>$totalSoldiers</td>
                                                        <td>$thirdCycleAdmin</td>
                                                        <td>$thirdCyclePLeave</td>
                                                        <td>$thirdCycleTraining</td>
                                                    </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3>Fourth Cycle</h3>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Company</th>
                                                <th>Total Soldiers</th>
                                                <th>Admin</th>
                                                <th>Leave</th>
                                                <th>Training</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            oci_execute($stmtCompanies); // Re-execute the query to start from the beginning
                                            while ($company = oci_fetch_assoc($stmtCompanies)) {
                                                $companyID = $company['COMPANYID'];
                                                $companyName = $company['COMPANYNAME'];
                                                $totalSoldiers = $company['TOTAL_SOLDIERS'];
                                                $fourthCycleAdmin = $company['FOURTH_CYCLE_ADMIN'];
                                                $fourthCyclePLeave = $company['FOURTH_CYCLE_PLEAVE'];
                                                $fourthCycleTraining = $company['FOURTH_CYCLE_TRAINING'];

                                                echo "<tr>
                                                        <td><a href='manage_career_plan.php?company=$companyID'>$companyName</a></td>
                                                        <td>$totalSoldiers</td>
                                                        <td>$fourthCycleAdmin</td>
                                                        <td>$fourthCyclePLeave</td>
                                                        <td>$fourthCycleTraining</td>
                                                    </tr>";
                                            }
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

<?php
oci_free_statement($stmtCompanies);
oci_close($conn);
?>