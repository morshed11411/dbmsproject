<?php
$pageTitle = "Dashboard-UPCS";
define('BASE_URL', 'http://yourdomain.com/upcs/');

require_once($_SERVER['DOCUMENT_ROOT'] . '/upcs/includes/header.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/upcs/includes/parade_controller.php');


foreach ($company as $coy) {
    $solderByCoy = getSoldiers($conn, null, null, null, false, $coy['ID'], null);

    $byCoyCount[$coy['ID']] = count($solderByCoy);
}

foreach ($ranks as $rank) {
    $soldiersByRank = getSoldiers($conn, null, $rank['NAME'], null, false, null, null);

    $byRankCount[$rank['ID']] = count($soldiersByRank);
}




?>
<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Welcome</h3>
        </div>
        <div class="text-right">
            <?php
            $currentDate = date("j F, Y"); // Format the current date as desired
            echo "<h3>Date: " . $currentDate . "</h3>";
            ?>
        </div>
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
                        <?php printSoldierList($postedTotal, 'allSoldier', 'Posted Soldiers') ?>
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
                        <?php printSoldierList($allOfficer, 'allOffr', 'All Officer') ?>
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
                        <?php printSoldierList($allJCO, 'allJCO', 'Posted JCO') ?>
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
                        <?php printSoldierList($allORS, 'allORS', 'All Other Ranks') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->


    <!-- Notice Board and Random Data Table -->
    <div class="row">
        <div class="col-lg-6">
            <!-- Notice Board Card -->
            <div class="card">
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

        <!-- Random Data Table -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title text-white">Random Data Table</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Item 1</td>
                                <td>123</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Item 2</td>
                                <td>456</td>
                            </tr>
                            <!-- Add more rows with random data here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <!-- Charts -->
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