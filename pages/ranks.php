<?php
session_start();

include '../includes/connection.php';

if (isset($_POST['add_rank_submit'])) {
    $rank_name = $_POST['rank_name'];

    $query = "INSERT INTO RANKS (RANK) VALUES (:rank_name)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':rank_name', $rank_name);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Rank added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add rank: " . $error['message'];
    }

    oci_free_statement($stmt);
}

if (isset($_POST['edit_rank_submit'])) {
    $rank_id = $_POST['edit_rank_id'];
    $rank_name = $_POST['edit_rank_name'];

    $query = "UPDATE RANKS SET RANK = :rank_name WHERE RANKID = :rank_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':rank_name', $rank_name);
    oci_bind_by_name($stmt, ':rank_id', $rank_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Rank updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update rank: " . $error['message'];
    }

    oci_free_statement($stmt);
}

if (isset($_POST['delete_rank_submit'])) {
    $rank_id = $_POST['delete_rank_id'];

    $query = "DELETE FROM RANKS WHERE RANKID = :rank_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':rank_id', $rank_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Rank deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete rank: " . $error['message'];
    }

    oci_free_statement($stmt);
}

oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Rank Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addRankModal">Add Rank</button>
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
                                    <th>Rank ID</th>
                                    <th>Rank Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM RANKS ORDER BY RANKID";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);

                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['RANKID'] . "</td>";
                                    echo "<td>" . $row['RANK'] . "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editRankModal-' . $row['RANKID'] . '">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>';
                                    echo '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteRankModal-' . $row['RANKID'] . '">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>';
                                    echo "</td>";
                                    echo "</tr>";

                                    // Edit Rank Modal
                                    echo '<div class="modal fade" id="editRankModal-' . $row['RANKID'] . '" tabindex="-1" role="dialog" aria-labelledby="editRankModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editRankModalLabel">Edit Rank</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_rank_id" value="' . $row['RANKID'] . '">
                                                        <div class="form-group">
                                                            <label for="edit_rank_name">Rank Name:</label>
                                                            <input type="text" name="edit_rank_name" id="edit_rank_name" class="form-control" value="' . $row['RANK'] . '" required>
                                                        </div>
                                                        <button type="submit" name="edit_rank_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';

                                    // Delete Rank Modal
                                    echo '<div class="modal fade" id="deleteRankModal-' . $row['RANKID'] . '" tabindex="-1" role="dialog" aria-labelledby="deleteRankModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteRankModalLabel">Delete Rank</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this rank?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_rank_id" value="' . $row['RANKID'] . '">
                                                        <button type="submit" name="delete_rank_submit" class="btn btn-danger">Delete</button>
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

<!-- Add Rank Modal -->
<div class="modal fade" id="addRankModal" tabindex="-1" role="dialog" aria-labelledby="addRankModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRankModalLabel">Add Rank</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="rank_name">Rank Name:</label>
                        <input type="text" name="rank_name" id="rank_name" class="form-control" required>
                    </div>
                    <input type="submit" name="add_rank_submit" value="Add Rank" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
