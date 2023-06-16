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

                    // Fetch the list of companies
                    $queryCompanies = "SELECT * FROM Company";
                    $stmtCompanies = oci_parse($conn, $queryCompanies);
                    oci_execute($stmtCompanies);

                    // Handle form submission
                    if (isset($_POST['submit'])) {
                        $authorizations = $_POST['authorization'];

                        foreach ($authorizations as $companyID => $companyAuthorizations) {
                            foreach ($companyAuthorizations as $tradeID => $ranks) {
                                foreach ($ranks as $rankID => $manpower) {
                                    $queryUpdate = "MERGE INTO Authorization a
                                                    USING (SELECT :companyID AS companyID, :tradeID AS tradeID, :rankID AS rankID, :manpower AS manpower FROM dual) d
                                                    ON (a.CompanyID = d.companyID AND a.TradeID = d.tradeID AND a.RankID = d.rankID)
                                                    WHEN MATCHED THEN
                                                        UPDATE SET a.Manpower = d.manpower
                                                    WHEN NOT MATCHED THEN
                                                        INSERT (CompanyID, TradeID, RankID, Manpower) VALUES (d.companyID, d.tradeID, d.rankID, d.manpower)";

                                    $stmtUpdate = oci_parse($conn, $queryUpdate);
                                    oci_bind_by_name($stmtUpdate, ':companyID', $companyID);
                                    oci_bind_by_name($stmtUpdate, ':tradeID', $tradeID);
                                    oci_bind_by_name($stmtUpdate, ':rankID', $rankID);
                                    oci_bind_by_name($stmtUpdate, ':manpower', $manpower);
                                    oci_execute($stmtUpdate);
                                }
                            }
                        }

                        echo "Manpower authorizations updated successfully.";
                    }
                    ?>

                    <form method="post" action="">
                        <div class="card">
                            <div class="card-body">
                                <h3>Set Manpower Authorizations</h3>
                                <?php
                                // Display the list of companies
                                while ($company = oci_fetch_assoc($stmtCompanies)) {
                                    $companyID = $company['COMPANYID'];
                                    $companyName = $company['COMPANYNAME'];

                                    echo "<h4>$companyName</h4>";

                                    // Fetch the list of trades and ranks for the current company
                                    $queryTrades = "SELECT * FROM Trade";
                                    $stmtTrades = oci_parse($conn, $queryTrades);
                                    oci_execute($stmtTrades);

                                    $queryRanks = "SELECT * FROM Ranks";
                                    $stmtRanks = oci_parse($conn, $queryRanks);
                                    oci_execute($stmtRanks);

                                    echo "<table class='table table-bordered'>";
                                    echo "<thead>";
                                    echo "<tr>";
                                    echo "<th>Trade</th>";
                                    while ($rank = oci_fetch_assoc($stmtRanks)) {
                                        echo "<th>" . $rank['RANK'] . "</th>";
                                    }
                                    echo "</tr>";
                                    echo "</thead>";
                                    echo "<tbody>";

                                    while ($trade = oci_fetch_assoc($stmtTrades)) {
                                        $tradeID = $trade['TRADEID'];

                                        echo "<tr>";
                                        echo "<td>" . $trade['TRADE'] . "</td>";

                                        // Fetch the authorization for the current trade and company
                                        $queryAuthorization = "SELECT Manpower FROM Authorization WHERE CompanyID = :companyID AND TradeID = :tradeID";
                                        $stmtAuthorization = oci_parse($conn, $queryAuthorization);
                                        oci_bind_by_name($stmtAuthorization, ':companyID', $companyID);
                                        oci_bind_by_name($stmtAuthorization, ':tradeID', $tradeID);
                                        oci_execute($stmtAuthorization);
                                        $authorization = oci_fetch_assoc($stmtAuthorization);
                                        $manpower = $authorization ? $authorization['MANPOWER'] : '';

                                        // Display the input field for the authorization
                                        echo "<td><input type='number' name='authorization[$companyID][$tradeID]' value='$manpower'></td>";

                                        oci_free_statement($stmtAuthorization);
                                    }

                                    echo "</tbody>";
                                    echo "</table>";

                                    oci_free_statement($stmtTrades);
                                    oci_free_statement($stmtRanks);
                                }
                                ?>
                            </div>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </section>
        </div>
        <?php include 'views/footer.php'; ?>
    </div>
</body>

</html>

<?php
oci_free_statement($stmtCompanies);
oci_close($conn);
?>
