<?php
// Include necessary files and start the session
session_start();
require_once('../includes/connection.php');
include '../includes/parade_controller.php';
include '../includes/header.php';

// Fetch data for the trade table
$queryTrade = "SELECT TRADEID, TRADE FROM TRADE";
$stmtTrade = oci_parse($conn, $queryTrade);
oci_execute($stmtTrade);

$tradeList = array();

while ($rowTrade = oci_fetch_assoc($stmtTrade)) {
    $trade = new stdClass();
    $trade->TradeID = $rowTrade['TRADEID'];
    $trade->Trade = $rowTrade['TRADE'];
    $tradeList[] = $trade;
}

oci_free_statement($stmtTrade);

// Fetch data for the rank table
$queryRank = "SELECT RANKID, RANK FROM Ranks";
$stmtRank = oci_parse($conn, $queryRank);
oci_execute($stmtRank);

$rankList = array();
while ($rowRank = oci_fetch_assoc($stmtRank)) {
    $rank = new stdClass();
    $rank->RankID = $rowRank['RANKID'];
    $rank->Rank = $rowRank['RANK'];
    $rankList[] = $rank;
}

oci_free_statement($stmtRank);

$query = "SELECT COMPANYID, COMPANYNAME FROM Company";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$companyList = array();

while ($row = oci_fetch_assoc($stmt)) {
    $company = new stdClass();
    $company->CompanyID = $row['COMPANYID'];
    $company->Company = $row['COMPANYNAME'];
    $companyList[] = $company;
}

oci_free_statement($stmt);
?>
<script src="../js/select2.full.min.js"></script>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Advanced Search</h3>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php include '../includes/alert.php'; ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-body">
                            <?php
                            $unitList = array("1 Sig Bn", "2 Sig Bn", "3 Sig Bn", "4 Sig Bn", "5 Sig Bn", "6 Sig Bn", "7 Sig Bn", "8 Sig Bn", "9 Sig Bn", "10 Sig Bn", "11 Sig Bn", "12 Sig Bn");
                            $missionList = array("Completed", "Not Completed");
                            $medCategoryList = array("A", "B", "C", "D", "E");
                            ?>

                            <form method="post" action="">



                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="rank_id">Rank:</label>
                                            <?php
                                            $rankValues = array_column($rankList, 'Rank');
                                            $rankIDs = array_column($rankList, 'RankID');
                                            echo createMultiSelect("rank_id[]", $rankValues, $rankIDs);
                                            ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="blood_group">Blood Group:</label>
                                            <?php echo createSelectElement("blood_group", array("A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-")); ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="med_category">Medical Category:</label>
                                            <?php echo createSelectElement("med_category", $medCategoryList); ?>
                                        </div>

                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="trade_id">Trade:</label>
                                            <?php
                                            $tradeValues = array_column($tradeList, 'Trade');
                                            $tradeIDs = array_column($tradeList, 'TradeID');
                                            echo createMultiSelect("trade_id[]", $tradeValues, $tradeIDs);
                                            ?>
                                        </div>


                                        <div class="form-group">
                                            <label for="religion">Religion:</label>
                                            <?php echo createSelectElement("religion", array("Islam", "Hindu", "Christian", "Other")); ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="parent_unit">Parent Unit:</label>
                                            <?php echo createSelectElement("parent_unit", $unitList); ?>
                                        </div>

                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="company_id">Company:</label>
                                            <?php
                                            $companyValues = array_column($companyList, 'Company');
                                            $companyIDs = array_column($companyList, 'CompanyID');
                                            echo createMultiSelect("company_id[]", $companyValues, $companyIDs);
                                            ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="marital_status">Marital Status:</label>
                                            <?php echo createSelectElement("marital_status", array("Married", "Unmarried")); ?>
                                        </div>
                                        <div class="form-group">
                                            <label for="mission">Mission:</label>
                                            <?php echo createSelectElement("mission", $missionList); ?>
                                        </div>


                                    </div>


                                </div>

                                <!-- Add other filters as needed -->

                                <input type="submit" name="submit" value="Search" class="btn btn-primary">
                            </form>

                            <?php
                            if (isset($_POST['submit'])) {
                                echo '<div class="result">';
                                echo '<h3>Selected Values:</h3>';

                                if (isset($_POST['trade_id'])) {
                                    echo '<p>Trade: ' . implode(', ', $_POST['trade_id']) . '</p>';
                                }

                                if (isset($_POST['rank_id'])) {
                                    echo '<p>Rank: ' . implode(', ', $_POST['rank_id']) . '</p>';
                                }

                                if (isset($_POST['company_id'])) {
                                    echo '<p>Company: ' . implode(', ', $_POST['company_id']) . '</p>';
                                }

                                // Display selected values for additional parameters
                                echo '<p>Blood Group: ' . $_POST['blood_group'] . '</p>';
                                // Display selected values for additional parameters
                                echo '<p>Blood Group: ' . $_POST['blood_group'] . '</p>';
                                echo '<p>Religion: ' . $_POST['religion'] . '</p>';
                                echo '<p>Parent Unit: ' . $_POST['parent_unit'] . '</p>';
                                echo '<p>Mission: ' . $_POST['mission'] . '</p>';
                                echo '<p>Medical Category: ' . $_POST['med_category'] . '</p>';

                                echo '</div>';
                            }
                            ?>
                        </div>


                    </div>
                </div>
            </div>
        </div>
</section>

<?php include '../includes/footer.php'; ?>
<script>
    $(document).ready(function () {
        // Initialize Select2 Elements
        $('.select2').select2();
    });
</script>