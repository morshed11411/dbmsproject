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

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['company'])) {
                        $companyID = $_POST['company'];

                        // Fetch the list of trades for the selected company
                        $queryTrades = "SELECT * FROM Trade";
                        $stmtTrades = oci_parse($conn, $queryTrades);
                        oci_execute($stmtTrades);

                        ?>
                        <form method="post" action="set_authorizations.php">
                            <input type="hidden" name="company" value="<?php echo $companyID; ?>">
                            <div class="card">
                                <div class="card-body">
                                    <h3>Select Trades</h3>
                                    <p>Select the trades for which you want to set authorizations:</p>
                                    <?php
                                    while ($trade = oci_fetch_assoc($stmtTrades)) {
                                        $tradeID = $trade['TRADEID'];
                                        $tradeName = $trade['TRADE'];

                                        echo "<div class='form-check'>";
                                        echo "<input class='form-check-input' type='checkbox' name='trades[]' value='$tradeID' id='trade_$tradeID'>";
                                        echo "<label class='form-check-label' for='trade_$tradeID'>$tradeName</label>";
                                        echo "</div>";
                                    }
                                    ?>
                                    <button type="submit" class="btn btn-primary">Next</button>
                                </div>
                            </div>
                        </form>
                        <?php
                        oci_free_statement($stmtTrades);
                    } else {
                        echo "<div class='card'>";
                        echo "<div class='card-body'>";
                        echo "<h3>Select Company</h3>";
                        echo "<p>Select the company for which you want to manage authorizations:</p>";
                        echo "<form method='post' action=''>";
                        echo "<div class='form-group'>";
                        echo "<label for='company'>Company:</label>";
                        echo "<select name='company' id='company' class='form-control'>";
                        while ($company = oci_fetch_assoc($stmtCompanies)) {
                            $companyID = $company['COMPANYID'];
                            $companyName = $company['COMPANYNAME'];

                            echo "<option value='$companyID'>$companyName</option>";
                        }
                        echo "</select>";
                        echo "</div>";
                        echo "<button type='submit' class='btn btn-primary'>Next</button>";
                        echo "</form>";
                        echo "</div>";
                        echo "</div>";
                    }

                    oci_free_statement($stmtCompanies);
                    oci_close($conn);
                    ?>
                </div>
            </section>
        </div>
        <?php include 'views/footer.php'; ?>
    </div>
</body>

</html>
