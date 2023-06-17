<?php
$conn = oci_connect('UMS', '12345', 'localhost/XE');
if (!$conn) {
    $e = oci_error();
    echo "Failed to connect to Oracle: " . $e['message'];
} else {

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
        $company->COMPANYID = $row['COMPANYID'];
        $company->COMPANYNAME = $row['COMPANYNAME'];
        $companyList[] = $company;
    }

    oci_free_statement($stmt);


    oci_close($conn);
}
?>


<?php include 'views/auth.php'; ?>

<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h2>Insert Authorization</h2>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">


                                    <form method="post" action="">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="auth_id">Auth ID:</label>
                                                    <input type="text" name="auth_id" id="auth_id"
                                                        class="form-control" required>
                                                </div>
                                               
                                                <div class="form-group">
                                                    <label for="company_id">Company:</label>
                                                    <select name="company_id" id="company_id"
                                                        class="form-control custom-select" required>
                                                        <option value="">Select Company</option>
                                                        <?php foreach ($companyList as $company): ?>
                                                            <option value="<?php echo $company->COMPANYID ?>"><?php echo $company->COMPANYNAME ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="rank_id">Rank:</label>
                                                    <select name="rank_id" id="rank_id"
                                                        class="form-control custom-select" required>
                                                        <option value="">Select Rank</option>
                                                        <?php foreach ($rankList as $rank): ?>
                                                            <option value="<?php echo $rank->RankID ?>"><?php echo $rank->Rank ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="trade_id">Trade:</label>
                                                    <select name="trade_id" id="trade_id"
                                                        class="form-control custom-select" required>
                                                        <option value="">Select Trade</option>
                                                        <?php foreach ($tradeList as $trade): ?>
                                                            <option value="<?php echo $trade->TradeID ?>"><?php echo $trade->Trade ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="Manpower">Manpower:</label>
                                                    <input type="text" name="Manpower" id="Manpower" class="form-control"
                                                        required>
                                                </div>
                                                <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                                               
                                             </div>

                                        
                                    </form>

                                    <?php
                                    // Check if the form is submitted
                                    if (isset($_POST['submit'])) {
                                        // Get the form data
                                        $auth_id = $_POST['auth_id'];
                                        $company = $_POST['company_id'];
                                        $rank = $_POST['rank_id'];
                                        $trade = $_POST['trade_id'];
                                        $Manpower = $_POST['Manpower'];

                                        // Establish a connection to the Oracle database
                                        $conn = oci_connect('UMS', '12345', 'localhost/XE');
                                        if (!$conn) {
                                            $e = oci_error();
                                            echo "Failed to connect to Oracle: " . $e['message'];
                                        } else {
                                            // Prepare the INSERT statement
                                            $query = "INSERT INTO Authorization (authorizationid, CompanyID, A_Rank, A_Trade, Manpower) VALUES (:auth_id, :company, :rank, :trade, :Manpower)";
                                            $stmt = oci_parse($conn, $query);

                                            // Bind the parameters
                                            oci_bind_by_name($stmt, ':auth_id', $auth_id);                                            
                                            oci_bind_by_name($stmt, ':company', $company);
                                            oci_bind_by_name($stmt, ':rank', $rank);
                                            oci_bind_by_name($stmt, ':trade', $trade);
                                            oci_bind_by_name($stmt, ':Manpower', $Manpower);

                                            // Execute the INSERT statement
                                            $result = oci_execute($stmt);
                                            if ($result) {
                                                echo "<h3>Authorization data inserted successfully.</h3>";
                                            } else {
                                                $e = oci_error($stmt);
                                                echo "Failed to insert Authorization data: " . $e['message'];
                                            }

                                            oci_free_statement($stmt);
                                            oci_close($conn);
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </section>

        <!-- Page content -->

        <?php include 'views/footer.php'; ?>



    </div>

</body>

</html>