<?php include '../includes/header.php'; ?>
<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Unit Dashboard</h3>
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
        <div class="col-md-3">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Soldiers</span>
                    <span class="info-box-number">100</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Soldiers Present</span>
                    <span class="info-box-number">75</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Teams</span>
                    <span class="info-box-number">10</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-user-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Soldiers on Leave</span>
                    <span class="info-box-number">25</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <!-- Notice Board -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Notice Board</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-bullhorn"></i> Important Announcement: ...</li>
                        <li><i class="fas fa-bullhorn"></i> Reminder: ...</li>
                        <li><i class="fas fa-bullhorn"></i> New Policy Update: ...</li>
                        <li><i class="fas fa-bullhorn"></i> Event Tomorrow: ...</li>
                        <!-- Add more notices here -->
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Random Data Table -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Random Data Table</h3>
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

    <div class="row">
        <div class="col-md-6">
            <!-- Bar Chart -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Bar Chart</h3>
                </div>
                <div class="card-body">
                    <canvas id="barChart" style="height: 200px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Pie Chart -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Pie Chart</h3>
                </div>
                <div class="card-body">
                    <canvas id="pieChart" style="height: 200px;"></canvas>
                </div>
            </div>
        </div>


    </div>
</section>

<!-- Include necessary scripts for charts (Chart.js) here -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>

<!-- Initialize and configure your charts (pieChart and barChart) here -->
<script>
    // Pie Chart
    var pieData = {
        datasets: [{
            data: [30, 70], // Replace with your data
            backgroundColor: ['green', 'blue'], // Replace with your colors
        }],
        labels: ['Label 1', 'Label 2'], // Replace with your labels
    };

    var pieOptions = {
        responsive: true,
        legend: {
            display: true,
        },
    };

    var pieChartCanvas = document.getElementById('pieChart').getContext('2d');
    new Chart(pieChartCanvas, {
        type: 'pie',
        data: pieData,
        options: pieOptions,
    });

    // Bar Chart
    var barData = {
        labels: ['Label 1', 'Label 2', 'Label 3'], // Replace with your labels
        datasets: [{
            label: 'Dataset 1',
            backgroundColor: 'blue', // Replace with your color
            data: [50, 30, 70], // Replace with your data
        }],
    };

    var barOptions = {
        responsive: true,
        scales: {
            xAxes: [{
                barPercentage: 0.5,
                barThickness: 20,
                maxBarThickness: 30,
                minBarLength: 2,
                gridLines: {
                    offsetGridLines: true,
                },
            }],
        },
    };

    var barChartCanvas = document.getElementById('barChart').getContext('2d');
    new Chart(barChartCanvas, {
        type: 'bar',
        data: barData,
        options: barOptions,
    });
</script>


<?php include '../includes/footer.php'; ?>