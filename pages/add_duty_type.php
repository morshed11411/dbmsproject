<?php
session_start();

include '../includes/connection.php';

// Add Duty Type
if (isset($_POST['add_duty_type_submit'])) {
    $duty_type = $_POST['duty_type'];
    $show_duty = isset($_POST['show_duty']) ? 1 : 0;

    $query = "INSERT INTO DUTYTYPES (TYPENAME, SHOW_DUTY) VALUES (:duty_type, :show_duty)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':duty_type', $duty_type);
    oci_bind_by_name($stmt, ':show_duty', $show_duty);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Duty Type added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add Duty Type: " . $error['message'];
    }

    oci_free_statement($stmt);
}

// Edit Duty Type
if (isset($_POST['edit_duty_type_submit'])) {
    $duty_type_id = $_POST['edit_duty_type_id'];
    $duty_type = $_POST['edit_duty_type'];
    $show_duty = isset($_POST['edit_show_duty']) ? 1 : 0;

    $query = "UPDATE DUTYTYPES SET TYPENAME = :duty_type, SHOW_DUTY = :show_duty WHERE DUTYTYPEID = :duty_type_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':duty_type', $duty_type);
    oci_bind_by_name($stmt, ':show_duty', $show_duty);
    oci_bind_by_name($stmt, ':duty_type_id', $duty_type_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Duty Type updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update Duty Type: " . $error['message'];
    }

    oci_free_statement($stmt);
}

// Delete Duty Type
if (isset($_POST['delete_duty_type_submit'])) {
    $duty_type_id = $_POST['delete_duty_type_id'];

    $query = "DELETE FROM DUTYTYPES WHERE DUTYTYPEID = :duty_type_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':duty_type_id', $duty_type_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Duty Type deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete Duty Type: " . $error['message'];
    }

    oci_free_statement($stmt);
}

// Function to get visible duty types
function getVisibleDutyTypes($connection)
{
    $query = "SELECT DUTYTYPEID, TYPENAME FROM DUTYTYPES WHERE SHOW_DUTY = 1";
    $stmt = oci_parse($connection, $query);
    oci_execute($stmt);

    $result = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $result[] = ['duty_type_id' => $row['DUTYTYPEID'], 'duty_type' => $row['TYPENAME']];
    }

    oci_free_statement($stmt);

    return $result;
}

// Example usage:
$visibleDutyTypes = getVisibleDutyTypes($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Duty Type Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addDutyTypeModal">Add Duty Type</button>
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
                                    <th>Duty Type ID</th>
                                    <th>Duty Type</th>
                                    <th>Show Duty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM DUTYTYPES ORDER BY DUTYTYPEID";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);

                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['DUTYTYPEID'] . "</td>";
                                    echo "<td><a href='manage_duties.php?dutytypeid=" . $row['DUTYTYPEID'] . "'>" . $row['TYPENAME'] . "</a></td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-success toggle-show-hide" data-id="' . $row['DUTYTYPEID'] . '" data-status="' . $row['SHOW_DUTY'] . '">' . ($row['SHOW_DUTY'] ? 'Hide' : 'Show') . '</button>';
                                    echo "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editDutyTypeModal-' . $row['DUTYTYPEID'] . '">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>';
                                    echo '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteDutyTypeModal-' . $row['DUTYTYPEID'] . '">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>';
                                    echo "</td>";
                                    echo "</tr>";

                                    // Edit Duty Type Modal
                                    echo '<div class="modal fade" id="editDutyTypeModal-' . $row['DUTYTYPEID'] . '" tabindex="-1" role="dialog" aria-labelledby="editDutyTypeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editDutyTypeModalLabel">Edit Duty Type</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_duty_type_id" value="' . $row['DUTYTYPEID'] . '">
                                                        <div class="form-group">
                                                            <label for="edit_duty_type">Duty Type:</label>
                                                            <input type="text" name="edit_duty_type" id="edit_duty_type" class="form-control" value="' . $row['TYPENAME'] . '" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="edit_show_duty">Show Duty:</label>
                                                            <input type="checkbox" name="edit_show_duty" id="edit_show_duty" value="1" ' . ($row['SHOW_DUTY'] ? 'checked' : '') . '>
                                                        </div>
                                                        <button type="submit" name="edit_duty_type_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';

                                    // Delete Duty Type Modal
                                    echo '<div class="modal fade" id="deleteDutyTypeModal-' . $row['DUTYTYPEID'] . '" tabindex="-1" role="dialog" aria-labelledby="deleteDutyTypeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteDutyTypeModalLabel">Delete Duty Type</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this Duty Type?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_duty_type_id" value="' . $row['DUTYTYPEID'] . '">
                                                        <button type="submit" name="delete_duty_type_submit" class="btn btn-danger">Delete</button>
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

<!-- Add Duty Type Modal -->
<div class="modal fade" id="addDutyTypeModal" tabindex="-1" role="dialog" aria-labelledby="addDutyTypeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDutyTypeModalLabel">Add Duty Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="duty_type">Duty Type:</label>
                        <input type="text" name="duty_type" id="duty_type" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="show_duty">Show Duty:</label>
                        <input type="checkbox" name="show_duty" id="show_duty" value="1" checked>
                    </div>
                    <input type="submit" name="add_duty_type_submit" value="Add Duty Type" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
