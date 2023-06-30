<?php
session_start();
include '../includes/connection.php';
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

    $querySoldiers = "SELECT s.SOLDIERID AS ID, s.NAME, r.RANK, cp.*
    FROM SOLDIER s
    JOIN RANKS r ON s.RANKID = r.RANKID
    LEFT JOIN CarrierPlan cp ON s.SOLDIERID = cp.SOLDIERID
    WHERE s.COMPANYID = :companyID
    AND r.RANK IN ('Snk', 'Lcpl', 'Cpl', 'Sgt', 'WO', 'SWO')";

    $stmtSoldiers = oci_parse($conn, $querySoldiers);
    oci_bind_by_name($stmtSoldiers, ':companyID', $companyID);
    oci_execute($stmtSoldiers);
}

$queryCompanies = "SELECT * FROM COMPANY";
$stmtCompanies = oci_parse($conn, $queryCompanies);
oci_execute($stmtCompanies);

if (isset($_POST['submit'])) {

    foreach ($_POST['firstCycle'] as $soldierID => $firstCycle) {
        $secondCycle = $_POST['secondCycle'][$soldierID];
        $thirdCycle = $_POST['thirdCycle'][$soldierID];
        $fourthCycle = $_POST['fourthCycle'][$soldierID];

        // Check if career plan already exists for the soldier
        $queryCheckPlan = "SELECT COUNT(*) FROM CarrierPlan WHERE SOLDIERID = :soldierID";
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
                                WHERE SOLDIERID = :soldierID";

            $stmtUpdatePlan = oci_parse($conn, $queryUpdatePlan);
            oci_bind_by_name($stmtUpdatePlan, ':firstCycle', $firstCycle);
            oci_bind_by_name($stmtUpdatePlan, ':secondCycle', $secondCycle);
            oci_bind_by_name($stmtUpdatePlan, ':thirdCycle', $thirdCycle);
            oci_bind_by_name($stmtUpdatePlan, ':fourthCycle', $fourthCycle);
            oci_bind_by_name($stmtUpdatePlan, ':soldierID', $soldierID);
            oci_execute($stmtUpdatePlan);
        } else {
            // Perform insert operation
            $queryInsertPlan = "INSERT INTO CarrierPlan (SOLDIERID, FIRSTCYCLE, SECONDCYCLE, THIRDCYCLE, FOURTHCYCLE)
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

    $_SESSION['success'] = "Career Plan updated successfully";

    // Redirect back to the assign_team.php page
    header("Location: update_plan.php?company=$companyID");
    exit();
}

include '../includes/header.php';
?>


<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Manage Career Plan -
                <?php echo $selectedCompanyName; ?>
            </h3>
        </div>
    </div>
</div>



<section class="content">
    <div class="container-fluid">
           <?php include '../includes/alert.php'; ?>
    <?php
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        <form action="" method="POST">

                            <table class="table table-bordered">
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
                                        $soldierID = $soldier['ID'];
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
<script>
  // Add event listener to each select element
  var selectElements = document.querySelectorAll('select[name^="firstCycle"], select[name^="secondCycle"], select[name^="thirdCycle"], select[name^="fourthCycle"]');
  selectElements.forEach(function(selectElement) {
    selectElement.addEventListener('change', function() {
      applyColor(this);
    });
    // Apply initial color on page load
    applyColor(selectElement);
  });
  
  // Apply color based on the selected option
  function applyColor(selectElement) {
    var selectedOption = selectElement.options[selectElement.selectedIndex].value;
    var colorClass = getColorClass(selectedOption);
    selectElement.classList = '';
    selectElement.classList.add('form-control', colorClass);
  }
  
  // Get the color class based on the selected option
  function getColorClass(option) {
    switch (option) {
      case 'Admin':
        return 'admin-color';
      case 'Training':
        return 'training-color';
      case 'Leave':
        return 'leave-color';
      default:
        return '';
    }
  }
</script>

<?php
oci_free_statement($stmtSoldiers);
oci_free_statement($stmtCompanies);
include '../includes/footer.php'; ?>
