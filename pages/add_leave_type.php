<?php
session_start();

include '../includes/connection.php';

// Add Leave Type
if (isset($_POST['add_leave_submit'])) {
    $leave_type = $_POST['leave_type'];
    $show_leave = isset($_POST['show_leave']) ? 1 : 0;

    $query = "INSERT INTO LEAVETYPE (LEAVETYPE, SHOW_LEAVE) VALUES (:leave_type, :show_leave)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':leave_type', $leave_type);
    oci_bind_by_name($stmt, ':show_leave', $show_leave);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Leave Type added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add Leave Type: " . $error['message'];
    }

    oci_free_statement($stmt);
}

// Edit Leave Type
if (isset($_POST['edit_leave_submit'])) {
    $leave_id = $_POST['edit_leave_id'];
    $leave_type = $_POST['edit_leave_type'];
    $show_leave = isset($_POST['edit_show_leave']) ? 1 : 0;

    $query = "UPDATE LEAVETYPE SET LEAVETYPE = :leave_type, SHOW_LEAVE = :show_leave WHERE LEAVETYPEID = :leave_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':leave_type', $leave_type);
    oci_bind_by_name($stmt, ':show_leave', $show_leave);
    oci_bind_by_name($stmt, ':leave_id', $leave_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Leave Type updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update Leave Type: " . $error['message'];
    }

    oci_free_statement($stmt);
}

// Delete Leave Type
if (isset($_POST['delete_leave_submit'])) {
    $leave_id = $_POST['delete_leave_id'];

    $query = "DELETE FROM LEAVETYPE WHERE LEAVETYPEID = :leave_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':leave_id', $leave_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Leave Type deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete Leave Type: " . $error['message'];
    }

    oci_free_statement($stmt);
}


// Handle Show/Hide Leave Type
if (isset($_POST['toggle_show_hide'])) {
    $leave_id = $_POST['leave_id'];
    $show_leave = $_POST['show_leave'];

    $result = toggleShowHideLeaveType($conn, $leave_id, $show_leave);

    if ($result['success']) {
        echo json_encode(['success' => true, 'message' => 'Leave Type updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update Leave Type: ' . $result['message']]);
    }

    oci_close($conn);
    exit; // Terminate script after handling AJAX request
}

function toggleShowHideLeaveType($connection, $LEAVETYPEID, $showStatus)
{
    $query = "UPDATE LEAVETYPE SET SHOW_LEAVE = :show_leave WHERE LEAVETYPEID = :leave_id";
    $stmt = oci_parse($connection, $query);
    oci_bind_by_name($stmt, ':show_leave', $showStatus);
    oci_bind_by_name($stmt, ':leave_id', $LEAVETYPEID);

    $result = oci_execute($stmt);
    if ($result) {
        return ['success' => true];
    } else {
        $error = oci_error($stmt);
        return ['success' => false, 'message' => $error['message']];
    }

    oci_free_statement($stmt);
}

oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Leave Type Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addLeaveModal">Add Leave Type</button>
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
                                    <th>Leave ID</th>
                                    <th>Leave Type</th>
                                    <th>Show Leave</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM LEAVETYPE ORDER BY LEAVETYPEID";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);

                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['LEAVETYPEID'] . "</td>";
                                    echo "<td>" . $row['LEAVETYPE'] . "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-success toggle-show-hide" data-id="' . $row['LEAVETYPEID'] . '" data-status="' . $row['SHOW_LEAVE'] . '">' . ($row['SHOW_LEAVE'] ? 'Hide' : 'Show') . '</button>';
                                    echo "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editLeaveModal-' . $row['LEAVETYPEID'] . '">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>';
                                    echo '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteLeaveModal-' . $row['LEAVETYPEID'] . '">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>';
                                    echo "</td>";
                                    echo "</tr>";

                                    // Edit Leave Type Modal
                                    echo '<div class="modal fade" id="editLeaveModal-' . $row['LEAVETYPEID'] . '" tabindex="-1" role="dialog" aria-labelledby="editLeaveModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editLeaveModalLabel">Edit Leave Type</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_leave_id" value="' . $row['LEAVETYPEID'] . '">
                                                        <div class="form-group">
                                                            <label for="edit_leave_type">Leave Type:</label>
                                                            <input type="text" name="edit_leave_type" id="edit_leave_type" class="form-control" value="' . $row['LEAVETYPE'] . '" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="edit_show_leave">Show Leave:</label>
                                                            <input type="checkbox" name="edit_show_leave" id="edit_show_leave" value="1" ' . ($row['SHOW_LEAVE'] ? 'checked' : '') . '>
                                                        </div>
                                                        <button type="submit" name="edit_leave_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';

                                    // Delete Leave Type Modal
                                    echo '<div class="modal fade" id="deleteLeaveModal-' . $row['LEAVETYPEID'] . '" tabindex="-1" role="dialog" aria-labelledby="deleteLeaveModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteLeaveModalLabel">Delete Leave Type</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this Leave Type?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_leave_id" value="' . $row['LEAVETYPEID'] . '">
                                                        <button type="submit" name="delete_leave_submit" class="btn btn-danger">Delete</button>
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

<!-- Add Leave Type Modal -->
<div class="modal fade" id="addLeaveModal" tabindex="-1" role="dialog" aria-labelledby="addLeaveModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLeaveModalLabel">Add Leave Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="leave_type">Leave Type:</label>
                        <input type="text" name="leave_type" id="leave_type" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="show_leave">Show Leave:</label>
                        <input type="checkbox" name="show_leave" id="show_leave" value="1" checked>
                    </div>
                    <input type="submit" name="add_leave_submit" value="Add Leave Type" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
