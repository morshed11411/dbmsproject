<?php
session_start();

include '../includes/connection.php';

if (isset($_POST['add_serving_submit'])) {
    $serving_type = $_POST['serving_type'];

    $query = "INSERT INTO SERVINGSTATUS (SERVINGTYPE) VALUES (:serving_type)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':serving_type', $serving_type);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Serving Status added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add Serving Status: " . $error['message'];
    }

    oci_free_statement($stmt);
}

if (isset($_POST['edit_serving_submit'])) {
    $serving_id = $_POST['edit_serving_id'];
    $serving_type = $_POST['edit_serving_type'];

    $query = "UPDATE SERVINGSTATUS SET SERVINGTYPE = :serving_type WHERE STATUSID = :serving_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':serving_type', $serving_type);
    oci_bind_by_name($stmt, ':serving_id', $serving_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Serving Status updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update Serving Status: " . $error['message'];
    }

    oci_free_statement($stmt);
}

if (isset($_POST['delete_serving_submit'])) {
    $serving_id = $_POST['delete_serving_id'];

    $query = "DELETE FROM SERVINGSTATUS WHERE STATUSID = :serving_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':serving_id', $serving_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Serving Status deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete Serving Status: " . $error['message'];
    }

    oci_free_statement($stmt);
}

oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Serving Status Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addServingModal">Add Serving Status</button>
        </div>
    </div>
</div>

<?php
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Serving Status ID</th>
                                    <th>Serving Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM SERVINGSTATUS ORDER BY STATUSID";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);

                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['STATUSID'] . "</td>";
                                    echo "<td>" . $row['SERVINGTYPE'] . "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editServingModal-' . $row['STATUSID'] . '">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>';
                                    echo '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteServingModal-' . $row['STATUSID'] . '">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>';
                                    echo "</td>";
                                    echo "</tr>";

                                    // Edit Serving Status Modal
                                    echo '<div class="modal fade" id="editServingModal-' . $row['STATUSID'] . '" tabindex="-1" role="dialog" aria-labelledby="editServingModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editServingModalLabel">Edit Serving Status</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_serving_id" value="' . $row['STATUSID'] . '">
                                                        <div class="form-group">
                                                            <label for="edit_serving_type">Serving Type:</label>
                                                            <input type="text" name="edit_serving_type" id="edit_serving_type" class="form-control" value="' . $row['SERVINGTYPE'] . '" required>
                                                        </div>
                                                        <button type="submit" name="edit_serving_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';

                                    // Delete Serving Status Modal
                                    echo '<div class="modal fade" id="deleteServingModal-' . $row['STATUSID'] . '" tabindex="-1" role="dialog" aria-labelledby="deleteServingModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteServingModalLabel">Delete Serving Status</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this Serving Status?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_serving_id" value="' . $row['STATUSID'] . '">
                                                        <button type="submit" name="delete_serving_submit" class="btn btn-danger">Delete</button>
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

<!-- Add Serving Status Modal -->
<div class="modal fade" id="addServingModal" tabindex="-1" role="dialog" aria-labelledby="addServingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addServingModalLabel">Add Serving Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="serving_type">Serving Type:</label>
                        <input type="text" name="serving_type" id="serving_type" class="form-control" required>
                    </div>
                    <input type="submit" name="add_serving_submit" value="Add Serving Status" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
