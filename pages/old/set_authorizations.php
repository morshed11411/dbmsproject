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

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['company']) && isset($_POST['trades'])) {
                        $companyID = $_POST['company'];
                        $selectedTrades = $_POST['trades'];

                        // Fetch the list of ranks for the selected trades
                        $queryRanks = "SELECT * FROM Ranks";
                        $stmtRanks = oci_parse($conn, $queryRanks);
                        oci_execute($stmtRanks);

                        ?>
                        <form method="post" action="update_authorizations.php">
                            <input type="hidden" name="company" value="<?php echo $companyID; ?>">
                            <input type="hidden" name="trades" value="<?php echo implode(',', $selectedTrades); ?>">
                            <div class="card">
                                <div class="card-body">
                                    <h3>Select Ranks</h3>
                                    <p>Select the ranks for which you want to set authorizations:</p>
                                    <?php
                                    while ($rank = oci_fetch_assoc($stmtRanks)) {
                                        $rankID = $rank['RANKID'];
                                        $rankName = $rank['RANK'];

                                        echo "<div class='form-check'>";
                                        echo "<input class='form-check-input' type='checkbox' name='ranks[]' value='$rankID' id='rank_$rankID'>";
                                        echo "<label class='form-check-label' for='rank_$rankID'>$rankName</label>";
                                        echo "</div>";
                                    }
                                    ?>
                                    <button type="submit" class="btn btn-primary">Save Authorizations</button>
                                </div>
                            </div>
                        </form>
                        <?php
                        oci_free_statement($stmtRanks);
                    } else {
                        echo "No trades selected.";
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
