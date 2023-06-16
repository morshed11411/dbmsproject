<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Manage Authorizations</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <?php
                    include 'conn.php';

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['company']) && isset($_POST['trades']) && isset($_POST['ranks'])) {
                        $companyID = $_POST['company'];
                        $selectedTrades = explode(',', $_POST['trades']);
                        $selectedRanks = $_POST['ranks'];

                        // Perform the necessary update or insert operations based on the selected trades and ranks
                        foreach ($selectedTrades as $tradeID) {
                            foreach ($selectedRanks as $rankID) {
                                // Check if an authorization record already exists for the company, trade, and rank
                                $queryCheck = "SELECT COUNT(*) AS count FROM Authorization WHERE CompanyID = :companyID AND TradeID = :tradeID AND RankID = :rankID";
                                $stmtCheck = oci_parse($conn, $queryCheck);
                                oci_bind_by_name($stmtCheck, ':companyID', $companyID);
                                oci_bind_by_name($stmtCheck, ':tradeID', $tradeID);
                                oci_bind_by_name($stmtCheck, ':rankID', $rankID);
                                oci_execute($stmtCheck);
                                $count = oci_fetch_assoc($stmtCheck)['COUNT'];

                                if ($count > 0) {
                                    // Update the existing authorization record
                                    $queryUpdate = "UPDATE Authorization SET Manpower = :manpower WHERE CompanyID = :companyID AND TradeID = :tradeID AND RankID = :rankID";
                                    $stmtUpdate = oci_parse($conn, $queryUpdate);
                                    oci_bind_by_name($stmtUpdate, ':manpower', $_POST["manpower_$tradeID_$rankID"]);
                                    oci_bind_by_name($stmtUpdate, ':companyID', $companyID);
                                    oci_bind_by_name($stmtUpdate, ':tradeID', $tradeID);
                                    oci_bind_by_name($stmtUpdate, ':rankID', $rankID);
                                    oci_execute($stmtUpdate);
                                } else {
                                    // Insert a new authorization record
                                    $queryInsert = "INSERT INTO Authorization (CompanyID, TradeID, RankID, Manpower) VALUES (:companyID, :tradeID, :rankID, :manpower)";
                                    $stmtInsert = oci_parse($conn, $queryInsert);
                                    oci_bind_by_name($stmtInsert, ':companyID', $companyID);
                                    oci_bind_by_name($stmtInsert, ':tradeID', $tradeID);
                                    oci_bind_by_name($stmtInsert, ':rankID', $rankID);
                                    oci_bind_by_name($stmtInsert, ':manpower', $_POST["manpower_$tradeID_$rankID"]);
                                    oci_execute($stmtInsert);
                                }
                            }
                        }

                        echo "Authorizations updated successfully.";
                    } else {
                        echo "Invalid request.";
                    }

                    oci_close($conn);
                    ?>
                </div>
            </section>
        </div>
        <?php include 'views/footer.php'; ?>
    </div>
</body>

</html>
