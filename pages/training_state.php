<?php
session_start();
include '../includes/connection.php'; // Make sure to include your database connection file

// Fetch overall training state data
$queryOverall = "SELECT 
                    COUNT(EVENTID) AS total_events,
                    SUM(CASE WHEN STATUS = 'Terminated' THEN 1 ELSE 0 END) AS terminated_events,
                    SUM(CASE WHEN STATUS = 'Ongoing' THEN 1 ELSE 0 END) AS ongoing_events,
                    SUM(CASE WHEN STATUS = 'Terminated' AND pass_fail_ratio >= 0.7 THEN 1 ELSE 0 END) AS high_pass_ratio,
                    SUM(CASE WHEN STATUS = 'Terminated' AND pass_fail_ratio < 0.7 THEN 1 ELSE 0 END) AS low_pass_ratio
                FROM TRAININGEVENT";

$stmtOverall = oci_parse($conn, $queryOverall);
oci_execute($stmtOverall);
$overallState = oci_fetch_assoc($stmtOverall);

// Fetch detailed training state data
$queryDetails = "SELECT TRGID, TRGNAME,
                    COUNT(EVENTID) AS total_events,
                    SUM(CASE WHEN STATUS = 'Terminated' THEN 1 ELSE 0 END) AS terminated_events,
                    SUM(CASE WHEN STATUS = 'Ongoing' THEN 1 ELSE 0 END) AS ongoing_events,
                    AVG(pass_fail_ratio) AS avg_pass_ratio
                FROM TRAININGEVENT
                LEFT JOIN (
                    SELECT TRGID, EVENTID,
                        (SUM(CASE WHEN STATUS = 'Pass' THEN 1 ELSE 0 END) / COUNT(SOLDIERID)) AS pass_fail_ratio
                    FROM SOLDIERTRAINING
                    GROUP BY TRGID, EVENTID
                ) AS t ON TRAININGEVENT.EVENTID = t.EVENTID
                GROUP BY TRGID, TRGNAME";

$stmtDetails = oci_parse($conn, $queryDetails);
oci_execute($stmtDetails);
$trainingDetails = [];
while ($row = oci_fetch_assoc($stmtDetails)) {
    $trainingDetails[] = $row;
}

include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Overall Training State Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Overall Training State
                </div>
                <div class="card-body">
                    <p>Total Events: <?= $overallState['total_events'] ?></p>
                    <p>Terminated Events: <?= $overallState['terminated_events'] ?></p>
                    <p>Ongoing Events: <?= $overallState['ongoing_events'] ?></p>
                    <p>High Pass Ratio Events: <?= $overallState['high_pass_ratio'] ?></p>
                    <p>Low Pass Ratio Events: <?= $overallState['low_pass_ratio'] ?></p>
                </div>
            </div>
        </div>

        <!-- Detailed Training State Cards -->
        <div class="col-md-6">
            <?php foreach ($trainingDetails as $training): ?>
                <div class="card">
                    <div class="card-header">
                        Training Type: <?= $training['TRGNAME'] ?>
                    </div>
                    <div class="card-body">
                        <p>Total Events: <?= $training['total_events'] ?></p>
                        <p>Terminated Events: <?= $training['terminated_events'] ?></p>
                        <p>Ongoing Events: <?= $training['ongoing_events'] ?></p>
                        <p>Average Pass Ratio: <?= number_format($training['avg_pass_ratio'], 2) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
