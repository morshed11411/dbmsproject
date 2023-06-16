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
                    <h1>Insert Trade Data</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <form method="post" action="">
                                        <div class="form-group">
                                            <label for="trade_id">Trade ID:</label>
                                            <input type="text" name="trade_id" id="trade_id" class="form-control"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label for="trade_name">Trade Name:</label>
                                            <input type="text" name="trade_name" id="trade_name" class="form-control"
                                                required>
                                        </div>


                                   <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                                    </form>
                                    <?php
                                    if (isset($_POST['submit'])) {
                                        $trade_id = $_POST['trade_id'];
                                        $trade_name = $_POST['trade_name'];

                                        $conn = oci_connect('UMS', '12345', 'localhost/XE');
                                        if (!$conn) {
                                            $e = oci_error();
                                            echo "Failed to connect to Oracle: " . $e['message'];
                                        } else {
                                            $query = "INSERT INTO TRADE (TRADEID, TRADE) VALUES (:trade_id, :trade_name)";
                                            $stmt = oci_parse($conn, $query);

                                            oci_bind_by_name($stmt, ':trade_id', $trade_id);
                                            oci_bind_by_name($stmt, ':trade_name', $trade_name);

                                            $result = oci_execute($stmt);
                                            if ($result) {
                                                echo "Trade data inserted successfully.";
                                            } else {
                                                $e = oci_error($stmt);
                                                if ($e['code'] == 1 && strpos($e['message'], 'SYS_C007204') !== false) {
                                                    echo "Failed to insert trade data: The Trade ID already exists. Please enter a unique Trade ID.";
                                                } else {
                                                    echo "Failed to insert trade data: Please enter valid data.";
                                                }
                                            }

                                            oci_free_statement($stmt);
                                            oci_close($conn);
                                        }
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Trade ID</th>
                                                <th>Trade Name</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $conn = oci_connect('UMS', '12345', 'localhost/XE');
                                            if (!$conn) {
                                                $e = oci_error();
                                                echo "Failed to connect to Oracle: " . $e['message'];
                                            } else {
                                                $query = "SELECT * FROM TRADE ORDER BY TRADEID";
                                                $stmt = oci_parse($conn, $query);
                                                oci_execute($stmt);

                                                while ($row = oci_fetch_assoc($stmt)) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row['TRADEID'] . "</td>";
                                                    echo "<td>" . $row['TRADE'] . "</td>";
                                                    echo "<td>";
                                                    echo "<a href='edit_trade.php?trade_id=" . $row['TRADEID'] . "'>Edit</a> | ";
                                                    echo "<a href='delete_trade.php?trade_id=" . $row['TRADEID'] . "'>Delete</a>";
                                                    echo "</td>";
                                                    echo "</tr>";
                                                }

                                                oci_free_statement($stmt);
                                                oci_close($conn);
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include 'views/footer.php';?>

    </div>

</body>

</html>
