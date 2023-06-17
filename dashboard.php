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

// Count number of soldiers on leave
$query = "SELECT COUNT(*) AS soldiers_on_leave FROM Soldier WHERE STATUS = 'Leave'";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);
$soldiersOnLeave = oci_fetch_assoc($stmt)['SOLDIERS_ON_LEAVE'];

// Count number of soldiers present
$query = "SELECT COUNT(*) AS soldiers_present FROM Soldier WHERE STATUS = 'Present'";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);
$soldiersPresent = oci_fetch_assoc($stmt)['SOLDIERS_PRESENT'];

oci_free_statement($stmt);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Dashboard</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Soldiers</span>
                                    <span class="info-box-number">
                                        <?php echo $totalSoldiers; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-user-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Soldiers Present</span>
                                    <span class="info-box-number">
                                        <?php echo $soldiersPresent; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Teams</span>
                                    <span class="info-box-number">
                                        <?php echo $totalTeams; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-user-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Soldiers on Leave</span>
                                    <?php
                                    // Write the SQL query to count the number of soldiers on leave today
                                    $query = "SELECT COUNT(*) AS SoldiersOnLeave
                                            FROM Soldier s
                                            JOIN LeaveModule l ON s.SoldierID = l.SoldierID
                                            WHERE TRUNC(l.LeaveStartDate) = TRUNC(SYSDATE)";
                                    $stmt = oci_parse($conn, $query);
                                    oci_execute($stmt);

                                    $row = oci_fetch_assoc($stmt);
                                    $soldiersOnLeave = $row['SOLDIERSONLEAVE'];

                                    echo "<span class='info-box-number'>$soldiersOnLeave</span>";

                                    oci_free_statement($stmt);
                                    ?>
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