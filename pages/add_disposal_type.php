<?php
session_start();

include '../includes/connection.php';

// Add Disposal Type
if (isset($_POST['add_disposal_submit'])) {
    $disposal_type = $_POST['disposal_type'];
    $show_disposal = isset($_POST['show_disposal']) ? 1 : 0;

    $query = "INSERT INTO DISPOSALTYPE (DISPOSALTYPE, SHOW_DISPOSAL) VALUES (:disposal_type, :show_disposal)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':disposal_type', $disposal_type);
    oci_bind_by_name($stmt, ':show_disposal', $show_disposal);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Disposal Type added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add Disposal Type: " . $error['message'];
    }

    oci_free_statement($stmt);
}

// Edit Disposal Type
if (isset($_POST['edit_disposal_submit'])) {
    $disposal_id = $_POST['edit_disposal_id'];
    $disposal_type = $_POST['edit_disposal_type'];
    $show_disposal = isset($_POST['edit_show_disposal']) ? 1 : 0;

    $query = "UPDATE DISPOSALTYPE SET DISPOSALTYPE = :disposal_type, SHOW_DISPOSAL = :show_disposal WHERE DISPOSALID = :disposal_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':disposal_type', $disposal_type);
    oci_bind_by_name($stmt, ':show_disposal', $show_disposal);
    oci_bind_by_name($stmt, ':disposal_id', $disposal_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Disposal Type updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update Disposal Type: " . $error['message'];
    }

    oci_free_statement($stmt);
}

// Delete Disposal Type
if (isset($_POST['delete_disposal_submit'])) {
    $disposal_id = $_POST['delete_disposal_id'];

    $query = "DELETE FROM DISPOSALTYPE WHERE DISPOSALID = :disposal_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':disposal_id', $disposal_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Disposal Type deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete Disposal Type: " . $error['message'];
    }

    oci_free_statement($stmt);
}

// Function to get visible disposal types
function getVisibleDisposalTypes($connection)
{
    $query = "SELECT DISPOSALID, DISPOSALTYPE FROM DISPOSALTYPE WHERE SHOW_DISPOSAL = 1";
    $stmt = oci_parse($connection, $query);
    oci_execute($stmt);

    $result = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $result[] = ['disposal_id' => $row['DISPOSALID'], 'disposal_type' => $row['DISPOSALTYPE']];
    }

    oci_free_statement($stmt);

    return $result;
}

// Example usage:
$visibleDisposalTypes = getVisibleDisposalTypes($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Disposal Type Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addDisposalModal">Add Disposal Type</button>
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
                                    <th>Disposal ID</th>
                                    <th>Disposal Type</th>
                                    <th>Show Disposal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM DISPOSALTYPE ORDER BY DISPOSALID";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);

                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['DISPOSALID'] . "</td>";
                                    echo "<td>" . $row['DISPOSALTYPE'] . "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-success toggle-show-hide" data-id="' . $row['DISPOSALID'] . '" data-status="' . $row['SHOW_DISPOSAL'] . '">' . ($row['SHOW_DISPOSAL'] ? 'Hide' : 'Show') . '</button>';
                                    echo "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editDisposalModal-' . $row['DISPOSALID'] . '">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>';
                                    echo '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteDisposalModal-' . $row['DISPOSALID'] . '">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>';
                                    echo "</td>";
                                    echo "</tr>";

                                    // Edit Disposal Type Modal
                                    echo '<div class="modal fade" id="editDisposalModal-' . $row['DISPOSALID'] . '" tabindex="-1" role="dialog" aria-labelledby="editDisposalModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editDisposalModalLabel">Edit Disposal Type</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_disposal_id" value="' . $row['DISPOSALID'] . '">
                                                        <div class="form-group">
                                                            <label for="edit_disposal_type">Disposal Type:</label>
                                                            <input type="text" name="edit_disposal_type" id="edit_disposal_type" class="form-control" value="' . $row['DISPOSALTYPE'] . '" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="edit_show_disposal">Show Disposal:</label>
                                                            <input type="checkbox" name="edit_show_disposal" id="edit_show_disposal" value="1" ' . ($row['SHOW_DISPOSAL'] ? 'checked' : '') . '>
                                                        </div>
                                                        <button type="submit" name="edit_disposal_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';

                                    // Delete Disposal Type Modal
                                    echo '<div class="modal fade" id="deleteDisposalModal-' . $row['DISPOSALID'] . '" tabindex="-1" role="dialog" aria-labelledby="deleteDisposalModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteDisposalModalLabel">Delete Disposal Type</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this Disposal Type?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_disposal_id" value="' . $row['DISPOSALID'] . '">
                                                        <button type="submit" name="delete_disposal_submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                }

                                oci_free_statement($stmt);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Disposal Type Modal -->
<div class="modal fade" id="addDisposalModal" tabindex="-1" role="dialog" aria-labelledby="addDisposalModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDisposalModalLabel">Add Disposal Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="disposal_type">Disposal Type:</label>
                        <input type="text" name="disposal_type" id="disposal_type" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="show_disposal">Show Disposal:</label>
                        <input type="checkbox" name="show_disposal" id="show_disposal" value="1" checked>
                    </div>
                    <input type="submit" name="add_disposal_submit" value="Add Disposal Type" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

