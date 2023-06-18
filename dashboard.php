<?php
include 'conn.php';
include 'views/auth.php';

// Count total number of soldiers
$query = "SELECT COUNT(*) AS total_soldiers FROM Soldier";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);
$totalSoldiers = oci_fetch_assoc($stmt)['TOTAL_SOLDIERS'];

// Count number of soldiers in each company
$query = "SELECT c.CompanyName, COUNT(s.SoldierID) AS soldiers_count
          FROM Company c
          LEFT JOIN Soldier s ON c.CompanyID = s.CompanyID
          GROUP BY c.CompanyName";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);
$companySoldiers = [];
while ($row = oci_fetch_assoc($stmt)) {
    $companySoldiers[$row['COMPANYNAME']] = $row['SOLDIERS_COUNT'];
}

// Count total number of teams
$query = "SELECT COUNT(*) AS total_teams FROM Team";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);
$totalTeams = oci_fetch_assoc($stmt)['TOTAL_TEAMS'];

// Fetch the leave count from the TODAYS_LEAVE_VIEW
$queryLeaveCount = "SELECT COUNT(*) AS LeaveCount FROM TODAYS_LEAVE_VIEW";
$stmtLeaveCount = oci_parse($conn, $queryLeaveCount);
oci_execute($stmtLeaveCount);
$leaveCount = oci_fetch_assoc($stmtLeaveCount)['LEAVECOUNT'];

oci_free_statement($stmt);
oci_close($conn);
?>
<?php
// Query to count the number of present soldiers
$query = "SELECT COUNT(*) AS numPresentSoldiers FROM Soldier WHERE ISPRESENT = 1";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);
$row = oci_fetch_assoc($stmt);
$numPresentSoldiers = $row['NUMPRESENTSOLDIERS'];

oci_free_statement($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="text-left">
                        <h3>Unit Dashboard</h3>
                    </div>
                    <div class="text-right">
                        <?php
                        date_default_timezone_set("Your/Timezone"); // Replace "Your/Timezone" with your desired timezone
                        $currentDate = date("j F, Y"); // Format the current date as desired
                        echo "<h3>Date: " . $currentDate . "</h3>";
                        ?>
                    </div>
                </div>
                <!-- Rest of the code -->
            </div>

            <section class="content">
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-md-3">
                            <a href="manage_soldier.php">
                                <div class="info-box bg-primary">
                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Soldiers</span>
                                        <span class="info-box-number">
                                            <?php echo $totalSoldiers; ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="present_soldiers.php">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Soldiers Present</span>
                                        <span class="info-box-number">
                                            <?php echo $numPresentSoldiers; ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="manage_team.php">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Teams</span>
                                        <span class="info-box-number">
                                            <?php echo $totalTeams; ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="leave_details.php">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-user-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Soldiers on Leave</span>
                                        <span class="info-box-number">
                                            <?php echo $leaveCount; ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="card-group">

                        <div class="col-md-6">

                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <h3>Soldiers on Duty</h3>
                                    </h5>
                                    <div class="card-text">
                                        <?php
                                        // Fetch soldiers with the specified appointments
                                        $query = "SELECT r.RANK, s.NAME, a.APPOINTMENTID, a.APPOINTMENTNAME 
                                    FROM Soldier s
                                    JOIN SoldierAppointment sa ON s.SOLDIERID = sa.SOLDIERID
                                    JOIN Appointments a ON sa.APPOINTMENTID = a.APPOINTMENTID
                                    JOIN Ranks r ON s.RANKID = r.RANKID
                                    WHERE a.APPOINTMENTNAME IN ('Duty Officer', 'Duty JCO', 'Duty NCO')
                                    ";
                                        $stmt = oci_parse($conn, $query);
                                        oci_execute($stmt);

                                        // Display the soldier information
                                        
                                        echo "<table class='table table-bordered'>
                                            <thead>
                                                <tr>
                                                    <th>Rank</th>
                                                    <th>Name</th>
                                                    <th>Duty</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>";

                                        while ($row = oci_fetch_assoc($stmt)) {
                                            echo "<tr>";
                                            echo "<td>" . $row['RANK'] . "</td>";
                                            echo "<td>" . $row['NAME'] . "</td>";

                                            // Get the soldier's duty based on the appointment
                                            $duty = "";
                                            if ($row['APPOINTMENTNAME'] == 'Duty Officer') {
                                                $duty = "Duty Officer";
                                            } elseif ($row['APPOINTMENTNAME'] == 'Duty JCO') {
                                                $duty = "Duty JCO";
                                            } elseif ($row['APPOINTMENTNAME'] == 'Duty NCO') {
                                                $duty = "Duty NCO";
                                            }

                                            echo "<td>" . $duty . "</td>";

                                            // Add the "Change" button with the appointment ID as a parameter
                                            echo "<td>
                                                <a href='soldier_details.php?appointment_id=" . $row['APPOINTMENTID'] . "' class='btn btn-primary'>Change</a>
                                            </td>";

                                            echo "</tr>";
                                        }

                                        echo "</tbody>
                                        </table>";


                                        oci_free_statement($stmt);
                                        oci_close($conn);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    Overweight Soldiers
                                </div>
                                <div class="card-body table-responsive p-0" style="height: 300px;">
                                    <table class="table table-head-fixed text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Soldier ID</th>
                                                <th>Rank</th>
                                                <th>Name</th>
                                                <th>Company</th>
                                                <th>Pounds Overweight</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Fetch the overweight soldier information from the view
                                            $query = "SELECT SOLDIERID, RANK, NAME, COMPANYNAME, POUNDSOVERWEIGHT
                              FROM OVERWEIGHTSOLDIERSVIEW";
                                            $stmt = oci_parse($conn, $query);
                                            oci_execute($stmt);

                                            $soldierIDs = [];
                                            $poundsOverweight = [];

                                            while ($row = oci_fetch_assoc($stmt)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['SOLDIERID'] . "</td>";
                                                echo "<td>" . $row['RANK'] . "</td>";
                                                echo "<td>" . $row['NAME'] . "</td>";
                                                echo "<td>" . $row['COMPANYNAME'] . "</td>";
                                                echo "<td>" . $row['POUNDSOVERWEIGHT'] . "</td>";
                                                echo "</tr>";

                                                // Store the data for the chart
                                                $soldierIDs[] = $row['SOLDIERID'];
                                                $poundsOverweight[] = $row['POUNDSOVERWEIGHT'];
                                            }

                                            oci_free_statement($stmt);
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-group">


                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3>Leave Percentage by Company</h3>
                                    <div class="chart-container" style="position: relative; height: 300px;">
                                        <canvas id="leaveChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3>Medical Disposal Holders Today</h3>
                                    <div class="chart-container" style="position: relative; height: 300px;">
                                        <canvas id="disposalChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    

                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        // Chart.js configuration
                        var poundsOverweightData = {
                            labels: <?php echo json_encode($soldierIDs); ?>,
                            datasets: [{
                                label: 'Pounds Overweight',
                                data: <?php echo json_encode($poundsOverweight); ?>,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        };

                        var poundsOverweightOptions = {
                            indexAxis: 'x',
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        };

                        // Create the pounds overweight bar chart
                        var poundsOverweightChart = new Chart(document.getElementById('poundsOverweightChart'), {
                            type: 'bar',
                            data: poundsOverweightData,
                            options: poundsOverweightOptions
                        });
                    </script>


                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        // ... Chart.js configuration for Leave Percentage by Company
                        <?php
                        // Fetch the company names and medical disposal counts from the "TODAYS_DISPOSAL_HOLDER" view
                        $queryDisposalToday = "SELECT COMPANYNAME, COUNT(*) AS DISPOSAL_COUNT FROM TODAYS_DISPOSAL_HOLDER GROUP BY COMPANYNAME";
                        $stmtDisposalToday = oci_parse($conn, $queryDisposalToday);
                        oci_execute($stmtDisposalToday);

                        $companyNames = [];
                        $disposalCounts = [];

                        while ($row = oci_fetch_assoc($stmtDisposalToday)) {
                            $companyNames[] = $row['COMPANYNAME'];
                            $disposalCounts[] = $row['DISPOSAL_COUNT'];
                        }

                        oci_free_statement($stmtDisposalToday);
                        ?>

                        // Chart.js configuration
                        var disposalData = {
                            labels: <?php echo json_encode($companyNames); ?>,
                            datasets: [{
                                label: 'Medical Disposal Count',
                                data: <?php echo json_encode($disposalCounts); ?>,
                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        };

                        var disposalOptions = {
                            indexAxis: 'x', // Set to 'x' for vertical bars
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        };

                        // Create the medical disposal count bar chart
                        var disposalChart = new Chart(document.getElementById('disposalChart'), {
                            type: 'bar',
                            data: disposalData,
                            options: disposalOptions
                        });

                        // ... Chart.js configuration for Medical Disposal Holders Today
                        <?php
                        // Fetch the company names and leave percentages from the "TODAYS_LEAVE_VIEW" view
                        $queryLeaveToday = "SELECT COMPANYNAME, COUNT(*) AS LEAVE_COUNT FROM TODAYS_LEAVE_VIEW GROUP BY COMPANYNAME";
                        $stmtLeaveToday = oci_parse($conn, $queryLeaveToday);
                        oci_execute($stmtLeaveToday);

                        $companyNames = [];
                        $leaveCounts = [];

                        while ($row = oci_fetch_assoc($stmtLeaveToday)) {
                            $companyNames[] = $row['COMPANYNAME'];
                            $leaveCounts[] = $row['LEAVE_COUNT'];
                        }

                        oci_free_statement($stmtLeaveToday);
                        ?>

                        // Chart.js configuration
                        var leaveData = {
                            labels: <?php echo json_encode($companyNames); ?>,
                            datasets: [{
                                label: 'Leave Count',
                                data: <?php echo json_encode($leaveCounts); ?>,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        };

                        var leaveOptions = {
                            indexAxis: 'x', // Set to 'x' for vertical bars
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        };

                        // Create the leave count bar chart
                        var leaveChart = new Chart(document.getElementById('leaveChart'), {
                            type: 'bar',
                            data: leaveData,
                            options: leaveOptions
                        });
                    </script>

            </section>



        </div>
        <?php include 'views/footer.php'; ?>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</html>