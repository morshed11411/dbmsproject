<?php
session_start();
require_once '../includes/access.php';

$pageTitle = "Dashboard-UPCS";
define('BASE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/upcs/');


require_once(BASE_DIR . 'includes/header.php');
require_once(BASE_DIR . 'includes/parade_controller.php');
require_once(BASE_DIR . 'includes/leave_controller.php');
require_once(BASE_DIR . 'includes/disposal_controller.php');


$company = getAllCompanyData($conn);



foreach ($company as $coy) {
    $solderByCoy = getSoldiers($conn, null, null, null, false, $coy['ID'], null);

    $byCoyCount[$coy['ID']] = count($solderByCoy);
}



foreach ($ranks as $rank) {
    $soldiersByRank = getSoldiers($conn, null, $rank['NAME'], null, false, null, null);

    $byRankCount[$rank['ID']] = count($soldiersByRank);
}


// Example usage
$companies = $company;
$startDate = '2023-11-20'; // Replace with your start date
$endDate = '2023-11-31';   // Replace with your end date

$result = getLeaveCountsByDateRange($conn, $companies, $startDate, $endDate);





?>


<div class="card-body">
    <div class="d-flex justify-content-between">

    </div>
</div>

<?php include '../includes/alert.php'; ?>
<section class="content">
    <div class="row">
        <div class="col-lg-3">
            <!-- Total Soldiers Card -->
            <div class="card">
                <div class="card-header bg-info">
                    <h5 class="card-title text-white"><i class="fas fa-users"></i> Total Soldiers</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <?php printAllSoldierList($postedTotal, 'allSoldier', 'Posted Soldiers') ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <!-- Soldiers Present Card -->
            <div class="card">
                <div class="card-header bg-info">
                    <h5 class="card-title text-white"><i class="fas fa-user-tie"></i> Total Officer</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <?php printAllSoldierList($allOfficer, 'allOffr', 'All Officer') ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <!-- Total Teams Card -->
            <div class="card">
                <div class="card-header bg-info">
                    <h5 class="card-title text-white"><i class="fas fa-user-secret"></i> Total JCO</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <?php printAllSoldierList($allJCO, 'allJCO', 'Posted JCO') ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <!-- Soldiers on Leave Card -->
            <div class="card">
                <div class="card-header bg-info">
                    <h5 class="card-title text-white"><i class="fas fa-user"></i> Other Ranks</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <?php printAllSoldierList($allORS, 'allORS', 'All Other Ranks') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->


    <!-- Notice Board and Random Data Table -->
    <div class="row d-flex">

        <div class="col-lg-6 flex-fill">
            <!-- Notice Board Card -->
            <div class="card h-100"> <!-- Added 'h-100' to make the card fill the column height -->
                <div class="card-header bg-info">
                    <h3 class="card-title text-white">Notice Board</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <?php if (empty($notifications)): ?>
                            <p>No notifications available.</p>
                        <?php else: ?>
                            <?php $noNotification = true; // Assume no unread notifications initially ?>
                            <?php foreach ($notifications as $notification): ?>
                                <?php if ($notification['STATUS']) { ?>
                                    <li>
                                        <i class="fas fa-bullhorn"></i> <span>
                                            <?php echo $notification['MESSAGE']; ?>
                                        </span>
                                        <?php
                                        // Display the time difference
                                        displayTimeDifference($notification['CREATED_AT']);
                                        ?>
                                    </li>
                                    <?php $noNotification = false; // Found an unread notification ?>
                                <?php } ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-6 flex-fill">
            <div class="card h-100"> <!-- Added 'h-100' to make the card fill the column height -->
                <div class="card-header bg-info">
                    <h3 class="card-title text-white">Solder Leave State</h3>
                </div>
                <div class="card-body">
                    <canvas id="leave-counts-chart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
<br>
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-6">
            <!-- Online Store Visitors Chart -->
            <div class="card">
                <div class="card-header bg-info">
                    <h5 class="card-title text-white"><i class="fas fa-chart-bar"></i> Soldiers by Company</h5>
                </div>
                <div class="card-body">
                    <canvas id="soldiersByCompanyChart" height="200"></canvas>
                </div>
            </div>
            <!-- /.card -->
        </div>

        <!-- Right Column -->
        <div class="col-lg-6">
            <!-- Online Store Visitors Chart -->
            <div class="card">
                <div class="card-header bg-info">
                    <h5 class="card-title text-white">Soldiers by Rank</h5>
                </div>
                <div class="card-body">
                    <!-- Create a canvas element for the bar chart -->
                    <canvas id="soldiers-by-rank-chart" height="200"></canvas>
                </div>
            </div>

            <!-- /.card -->
        </div>
    </div>
    <!-- /.row -->
    <!-- /.container-fluid -->
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script>
    // Replace the PHP code with the actual data from your PHP script
    var leaveCountsByDate = <?php echo json_encode($result); ?>;
    var companies = <?php echo json_encode($companies); ?>;

    // Extract data for the chart
    var dates = Object.keys(leaveCountsByDate);

    // Create arrays to store data for the chart
    var labels = dates;
    var datasets = [];

    // Define colors for each company
    var colors = ['rgba(255, 99, 132, 0.5)', 'rgba(255, 205, 86, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(75, 192, 192, 0.5)'];

    // Loop through companies to create datasets
    for (var i = 0; i < companies.length; i++) {
        var companyId = companies[i]['ID'];
        var companyName = companies[i]['NAME'];
        var data = [];

        // Extract leave counts for the current company
        for (var j = 0; j < dates.length; j++) {
            data.push(leaveCountsByDate[dates[j]][companyId] || 0);
        }

        // Add dataset for the current company
        datasets.push({
            label: companyName,
            data: data,
            backgroundColor: colors[i % colors.length], // Use modulus to loop through colors if more companies than colors
            borderColor: colors[i % colors.length],
            borderWidth: 1
        });
    }

    // Create the leave counts by date and company chart
    var leaveCountsChartCanvas = document.getElementById('leave-counts-chart').getContext('2d');
    new Chart(leaveCountsChartCanvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    min: 5, // Set the minimum value to 5

                }

            }
        }
    });
</script>
<!-- Initialize and configure your charts (visitors-chart, sales-chart) here -->
<script>
    // Data for Soldiers by Company
    var companies = <?php echo json_encode(array_column($company, 'NAME')); ?>;
    var soldiersCount = <?php echo json_encode(array_values($byCoyCount)); ?>;

    // Bar Chart
    var soldiersByCompanyChartCanvas = document.getElementById('soldiersByCompanyChart').getContext('2d');
    new Chart(soldiersByCompanyChartCanvas, {
        type: 'line',
        data: {
            labels: companies,
            datasets: [{
                label: 'Total',
                data: soldiersCount,
                backgroundColor: 'rgba(0, 123, 255, 0.2)', // Bootstrap primary color class
                borderColor: 'rgba(0, 123, 255, 1)', // Bootstrap primary color class
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>



<script>
    // Get the data for soldiers by rank
    var ranks = <?php echo json_encode($ranks); ?>;
    var byRankCount = <?php echo json_encode($byRankCount); ?>;

    // Create arrays to store data for the chart
    var rankLabels = [];
    var rankData = [];

    // Extract data from the PHP arrays
    for (var i = 0; i < ranks.length; i++) {
        rankLabels.push(ranks[i]['NAME']);
        rankData.push(byRankCount[ranks[i]['ID']]);
    }

    // Create the soldiers by rank chart
    var soldiersByRankChartCanvas = document.getElementById('soldiers-by-rank-chart').getContext('2d');
    new Chart(soldiersByRankChartCanvas, {
        type: 'bar',
        data: {
            labels: rankLabels,
            datasets: [{
                label: 'Soldiers by Rank',
                data: rankData,
                backgroundColor: 'rgba(0, 123, 255, 0.5)', // Bootstrap primary color with 50% opacity
                borderColor: 'rgba(0, 123, 255, 1)', // Bootstrap primary color
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<?php include '../includes/footer.php'; ?>