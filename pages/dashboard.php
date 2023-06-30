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
    
</section>

<?php include '../includes/footer.php'; ?>