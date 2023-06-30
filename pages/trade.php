<?php
session_start();

include '../includes/connection.php';

if (isset($_POST['add_trade_submit'])) {
    $trade_name = $_POST['trade_name'];

    $query = "INSERT INTO TRADE (TRADE) VALUES (:trade_name)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':trade_name', $trade_name);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Trade added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add trade: " . $error['message'];
    }

    oci_free_statement($stmt);
}

if (isset($_POST['edit_trade_submit'])) {
    $trade_id = $_POST['edit_trade_id'];
    $trade_name = $_POST['edit_trade_name'];

    $query = "UPDATE TRADE SET TRADE = :trade_name WHERE TRADEID = :trade_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':trade_name', $trade_name);
    oci_bind_by_name($stmt, ':trade_id', $trade_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Trade updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update trade: " . $error['message'];
    }

    oci_free_statement($stmt);
}

if (isset($_POST['delete_trade_submit'])) {
    $trade_id = $_POST['delete_trade_id'];

    $query = "DELETE FROM TRADE WHERE TRADEID = :trade_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':trade_id', $trade_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Trade deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete trade: " . $error['message'];
    }

    oci_free_statement($stmt);
}

oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Trade Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addTradeModal">Add Trade</button>
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
                                $query = "SELECT * FROM TRADE ORDER BY TRADEID";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);

                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['TRADEID'] . "</td>";
                                    echo "<td>" . $row['TRADE'] . "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editTradeModal-' . $row['TRADEID'] . '">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>';
                                    echo '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteTradeModal-' . $row['TRADEID'] . '">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>';
                                    echo "</td>";
                                    echo "</tr>";

                                    // Edit Trade Modal
                                    echo '<div class="modal fade" id="editTradeModal-' . $row['TRADEID'] . '" tabindex="-1" role="dialog" aria-labelledby="editTradeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editTradeModalLabel">Edit Trade</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_trade_id" value="' . $row['TRADEID'] . '">
                                                        <div class="form-group">
                                                            <label for="edit_trade_name">Trade Name:</label>
                                                            <input type="text" name="edit_trade_name" id="edit_trade_name" class="form-control" value="' . $row['TRADE'] . '" required>
                                                        </div>
                                                        <button type="submit" name="edit_trade_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';

                                    // Delete Trade Modal
                                    echo '<div class="modal fade" id="deleteTradeModal-' . $row['TRADEID'] . '" tabindex="-1" role="dialog" aria-labelledby="deleteTradeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteTradeModalLabel">Delete Trade</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this trade?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_trade_id" value="' . $row['TRADEID'] . '">
                                                        <button type="submit" name="delete_trade_submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                }

                                oci_free_statement($stmt);
                                oci_close($conn);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Trade Modal -->
<div class="modal fade" id="addTradeModal" tabindex="-1" role="dialog" aria-labelledby="addTradeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTradeModalLabel">Add Trade</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="trade_name">Trade Name:</label>
                        <input type="text" name="trade_name" id="trade_name" class="form-control" required>
                    </div>
                    <input type="submit" name="add_trade_submit" value="Add Trade" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
