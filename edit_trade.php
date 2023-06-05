<?php
// edit_trade.php

if (isset($_GET['trade_id'])) {
    $trade_id = $_GET['trade_id'];

    // Fetch trade details from the database based on the trade_id
    $conn = oci_connect('UMS', '12345', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        echo "Failed to connect to Oracle: " . $e['message'];
        exit;
    }

    $query = "SELECT * FROM TRADE WHERE TRADEID = :trade_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':trade_id', $trade_id);
    oci_execute($stmt);

    $trade = oci_fetch_assoc($stmt);
    if (!$trade) {
        echo "Trade not found.";
        exit;
    }

    oci_free_statement($stmt);
    oci_close($conn);

    // Render the form for editing trade details
    include 'views/auth.php';
    include 'views/head.php';

    ?>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Edit Trade</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <form method="post" action="update_trade.php">
                                        <input type="hidden" name="trade_id" value="<?php echo $trade['TRADEID']; ?>">
                                        <div class="form-group">
                                            <label for="trade_name">Trade Name:</label>
                                            <input type="text" name="trade_name" id="trade_name" class="form-control"
                                                required value="<?php echo $trade['TRADE']; ?>">
                                        </div>

                                        <input type="submit" name="submit" value="Update" class="btn btn-primary">
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
    
    <?php
} else {
    echo "Invalid request. Trade ID not provided.";
    exit;
}


?>