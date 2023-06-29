<?php
session_start();

include '../includes/connection.php';

if (isset($_POST['add_tempcomd_submit'])) {
    $tempcomd_name = $_POST['tempcomd_name'];

    $query = "INSERT INTO TEMPORARYCOMMAND (COMDNAME) VALUES (:tempcomd_name)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':tempcomd_name', $tempcomd_name);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Temporary Command added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add Temporary Command: " . $error['message'];
    }

    oci_free_statement($stmt);
}

if (isset($_POST['edit_tempcomd_submit'])) {
    $tempcomd_id = $_POST['edit_tempcomd_id'];
    $tempcomd_name = $_POST['edit_tempcomd_name'];

    $query = "UPDATE TEMPORARYCOMMAND SET COMDNAME = :tempcomd_name WHERE COMDID = :tempcomd_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':tempcomd_name', $tempcomd_name);
    oci_bind_by_name($stmt, ':tempcomd_id', $tempcomd_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Temporary Command updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update Temporary Command: " . $error['message'];
    }

    oci_free_statement($stmt);
}

if (isset($_POST['delete_tempcomd_submit'])) {
    $tempcomd_id = $_POST['delete_tempcomd_id'];

    $query = "DELETE FROM TEMPORARYCOMMAND WHERE COMDID = :tempcomd_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':tempcomd_id', $tempcomd_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Temporary Command deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete Temporary Command: " . $error['message'];
    }

    oci_free_statement($stmt);
}

oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Temporary Command Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addTempcomdModal">Add Temporary Command</button>
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
                                    <th>Temporary Command ID</th>
                                    <th>Temporary Command Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM TEMPORARYCOMMAND ORDER BY COMDID";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);

                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['COMDID'] . "</td>";
                                    echo "<td>" . $row['COMDNAME'] . "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editTempcomdModal-' . $row['COMDID'] . '">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>';
                                    echo '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteTempcomdModal-' . $row['COMDID'] . '">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>';
                                    echo "</td>";
                                    echo "</tr>";

                                    // Edit Temporary Command Modal
                                    echo '<div class="modal fade" id="editTempcomdModal-' . $row['COMDID'] . '" tabindex="-1" role="dialog" aria-labelledby="editTempcomdModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editTempcomdModalLabel">Edit Temporary Command</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_tempcomd_id" value="' . $row['COMDID'] . '">
                                                        <div class="form-group">
                                                            <label for="edit_tempcomd_name">Temporary Command Name:</label>
                                                            <input type="text" name="edit_tempcomd_name" id="edit_tempcomd_name" class="form-control" value="' . $row['COMDNAME'] . '" required>
                                                        </div>
                                                        <button type="submit" name="edit_tempcomd_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';

                                    // Delete Temporary Command Modal
                                    echo '<div class="modal fade" id="deleteTempcomdModal-' . $row['COMDID'] . '" tabindex="-1" role="dialog" aria-labelledby="deleteTempcomdModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteTempcomdModalLabel">Delete Temporary Command</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this Temporary Command?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_tempcomd_id" value="' . $row['COMDID'] . '">
                                                        <button type="submit" name="delete_tempcomd_submit" class="btn btn-danger">Delete</button>
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

<!-- Add Temporary Command Modal -->
<div class="modal fade" id="addTempcomdModal" tabindex="-1" role="dialog" aria-labelledby="addTempcomdModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTempcomdModalLabel">Add Temporary Command</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="tempcomd_name">Temporary Command Name:</label>
                        <input type="text" name="tempcomd_name" id="tempcomd_name" class="form-control" required>
                    </div>
                    <input type="submit" name="add_tempcomd_submit" value="Add Temporary Command" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
