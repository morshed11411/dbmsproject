<!DOCTYPE html>
<html lang="en">

<?php
include 'views/head.php';
include 'views/auth.php';
include 'conn.php';

$selectedCompanyName = "";

if (isset($_GET['company'])) {
    $companyID = $_GET['company'];

    // Retrieve the selected company name
    $queryCompany = "SELECT COMPANYNAME FROM COMPANY WHERE COMPANYID = :companyID";
    $stmtCompany = oci_parse($conn, $queryCompany);
    oci_bind_by_name($stmtCompany, ':companyID', $companyID);
    oci_execute($stmtCompany);
    $row = oci_fetch_assoc($stmtCompany);
    $selectedCompanyName = $row['COMPANYNAME'];
    oci_free_statement($stmtCompany);

    $querySoldiers = "SELECT s.SOLDIERID, s.NAME, r.RANK, cp.*
    FROM SOLDIER s
    JOIN RANKS r ON s.RANKID = r.RANKID
    LEFT JOIN CarrierPlan cp ON s.SOLDIERID = cp.PLANID
    WHERE s.COMPANYID = :companyID
    AND r.RANK IN ('SNK', 'LCPL', 'CPL', 'SGT', 'WO', 'SWO')";

    $stmtSoldiers = oci_parse($conn, $querySoldiers);
    oci_bind_by_name($stmtSoldiers, ':companyID', $companyID);
    oci_execute($stmtSoldiers);
}

$queryCompanies = "SELECT * FROM COMPANY";
$stmtCompanies = oci_parse($conn, $queryCompanies);
oci_execute($stmtCompanies);
?>

<head>
    <meta charset="UTF-8">
    <title>Manage Career Plan - <?php echo $selectedCompanyName; ?></title>
</head>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">

            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h3>Manage Career Plan - <?php echo $selectedCompanyName; ?></h3>
                                    <form action="" method="POST">

                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Soldier ID</th>
                                                    <th>Name</th>
                                                    <th>Rank</th>
                                                    <th>First Cycle</th>
                                                    <th>Second Cycle</th>
                                                    <th>Third Cycle</th>
                                                    <th>Fourth Cycle</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                while ($soldier = oci_fetch_assoc($stmtSoldiers)) {
                                                    $soldierID = $soldier['SOLDIERID'];
                                                    $name = $soldier['NAME'];
                                                    $rank = $soldier['RANK'];
                                                    $firstCycle = $soldier['FIRSTCYCLE'];
                                                    $secondCycle = $soldier['SECONDCYCLE'];
                                                    $thirdCycle = $soldier['THIRDCYCLE'];
                                                    $fourthCycle = $soldier['FOURTHCYCLE'];

                                                    echo "<tr>
                                                            <td>$soldierID</td>
                                                            <td>$name</td>
                                                            <td>$rank</td>
                                                            <td>
                                                                <select class='form-control' name='firstCycle[$soldierID]' required>
                                                                    <option value='Admin' " . ($firstCycle === 'Admin' ? 'selected' : '') . ">Admin</option>
                                                                    <option value='Leave' " . ($firstCycle === 'Leave' ? 'selected' : '') . ">Leave</option>
                                                                    <option value='Training' " . ($firstCycle === 'Training' ? 'selected' : '') . ">Training</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class='form-control' name='secondCycle[$soldierID]' required>
                                                                    <option value='Admin' " . ($secondCycle === 'Admin' ? 'selected' : '') . ">Admin</option>
                                                                    <option value='Leave' " . ($secondCycle === 'Leave' ? 'selected' : '') . ">Leave</option>
                                                                    <option value='Training' " . ($secondCycle === 'Training' ? 'selected' : '') . ">Training</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class='form-control' name='thirdCycle[$soldierID]' required>
                                                                    <option value='Admin' " . ($thirdCycle === 'Admin' ? 'selected' : '') . ">Admin</option>
                                                                    <option value='Leave' " . ($thirdCycle === 'Leave' ? 'selected' : '') . ">Leave</option>
                                                                    <option value='Training' " . ($thirdCycle === 'Training' ? 'selected' : '') . ">Training</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class='form-control' name='fourthCycle[$soldierID]' required>
                                                                    <option value='Admin' " . ($fourthCycle === 'Admin' ? 'selected' : '') . ">Admin</option>
                                                                    <option value='Leave' " . ($fourthCycle === 'Leave' ? 'selected' : '') . ">Leave</option>
                                                                    <option value='Training' " . ($fourthCycle === 'Training' ? 'selected' : '') . ">Training</option>
                                                                </select>
                                                            </td>
                                                        </tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <button type="submit" name="submit" class="btn btn-primary">Update Career Plan</button>
                                    </form>
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
if (isset($_POST['submit'])) {
    include 'conn.php'; // Include the database connection file

    foreach ($_POST['firstCycle'] as $soldierID => $firstCycle) {
        $secondCycle = $_POST['secondCycle'][$soldierID];
        $thirdCycle = $_POST['thirdCycle'][$soldierID];
        $fourthCycle = $_POST['fourthCycle'][$soldierID];

        // Check if career plan already exists for the soldier
        $queryCheckPlan = "SELECT COUNT(*) FROM CarrierPlan WHERE PLANID = :soldierID";
        $stmtCheckPlan = oci_parse($conn, $queryCheckPlan);
        oci_bind_by_name($stmtCheckPlan, ':soldierID', $soldierID);
        oci_execute($stmtCheckPlan);
        $planExists = oci_fetch_row($stmtCheckPlan)[0] > 0;
        oci_free_statement($stmtCheckPlan);

        if ($planExists) {
            // Perform update operation
            $queryUpdatePlan = "UPDATE CarrierPlan
                                SET FIRSTCYCLE = :firstCycle,
                                    SECONDCYCLE = :secondCycle,
                                    THIRDCYCLE = :thirdCycle,
                                    FOURTHCYCLE = :fourthCycle
                                WHERE PLANID = :soldierID";

            $stmtUpdatePlan = oci_parse($conn, $queryUpdatePlan);
            oci_bind_by_name($stmtUpdatePlan, ':firstCycle', $firstCycle);
            oci_bind_by_name($stmtUpdatePlan, ':secondCycle', $secondCycle);
            oci_bind_by_name($stmtUpdatePlan, ':thirdCycle', $thirdCycle);
            oci_bind_by_name($stmtUpdatePlan, ':fourthCycle', $fourthCycle);
            oci_bind_by_name($stmtUpdatePlan, ':soldierID', $soldierID);
            oci_execute($stmtUpdatePlan);
        } else {
            // Perform insert operation
            $queryInsertPlan = "INSERT INTO CarrierPlan (PLANID, FIRSTCYCLE, SECONDCYCLE, THIRDCYCLE, FOURTHCYCLE)
                                VALUES (:soldierID, :firstCycle, :secondCycle, :thirdCycle, :fourthCycle)";

            $stmtInsertPlan = oci_parse($conn, $queryInsertPlan);
            oci_bind_by_name($stmtInsertPlan, ':soldierID', $soldierID);
            oci_bind_by_name($stmtInsertPlan, ':firstCycle', $firstCycle);
            oci_bind_by_name($stmtInsertPlan, ':secondCycle', $secondCycle);
            oci_bind_by_name($stmtInsertPlan, ':thirdCycle', $thirdCycle);
            oci_bind_by_name($stmtInsertPlan, ':fourthCycle', $fourthCycle);
            oci_execute($stmtInsertPlan);
        }
    }

    // Close the database connection
    oci_close($conn);

    // Redirect or display a success message after updating the career plans
    echo "<script>alert('Career plans have been updated successfully'); window.location.href = 'manage_plan.php';</script>";
    exit();
}

oci_free_statement($stmtSoldiers);
oci_free_statement($stmtCompanies);
oci_close($conn);
?>
