<?php
session_start();

include '../includes/connection.php';
include '../includes/parade_controller.php';

// Assuming $postedTotal is an array of soldiers obtained from the getSoldiers function
$postedTotal = getSoldiers($conn, null, null, null, false, null, null);

// ... (existing code)

// Handle form submission for updating access
if (isset($_POST['edit_access_submit'])) {
    $edit_access_soldier_id = $_POST['edit_access_soldier_id'];
    $edit_access_level = $_POST['edit_access_level'];

    // Perform validation if needed

    // Update user's access level
    $updateResult = updateUserAccess($conn, $edit_access_soldier_id, $edit_access_level);

    if ($updateResult) {
        $_SESSION['success'] = "User access level updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update user access level.";
    }
}

// ... (rest of your code)

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Soldier Access Control</h3>
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
                        <table id="tablex" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Soldier ID</th>
                                    <th>Rank</th>
                                    <th>Trade</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Access</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($postedTotal as $soldier) {
                                    echo "<tr>";
                                    echo "<td>{$soldier['SOLDIERID']}</td>";
                                    echo "<td>{$soldier['RANK']}</td>";
                                    echo "<td>{$soldier['TRADE']}</td>";
                                    echo "<td>{$soldier['NAME']}</td>";
                                    echo "<td>{$soldier['COMPANYNAME']}</td>";
                               
                                    // Display Action buttons based on access level
                                    echo "<td>";
                                    $accessLevel = getUserAccess($conn, $soldier['SOLDIERID']);
                                    if ($accessLevel == 'admin') {
                                        echo '<button type="button" class="btn btn-success">Admin</button>';
                                    } elseif ($accessLevel == 'manager') {
                                        echo '<button type="button" class="btn btn-warning">Manager</button>';
                                    } elseif ($accessLevel == 'soldier') {
                                        echo '<button type="button" class="btn btn-info">Soldier</button>';
                                    }
                                    echo "</td>";
                                    echo "<td>";

                                    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editAccessModal-' . $soldier['SOLDIERID'] . '">
                                            <i class="fas fa-edit"></i> Edit Access
                                          </button>';
                                    echo "</td>";
                                    echo "</tr>";


                                    // Edit Access Modal
                                    echo '<div class="modal fade" id="editAccessModal-' . $soldier['SOLDIERID'] . '" tabindex="-1" role="dialog" aria-labelledby="editAccessModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editAccessModalLabel">Edit Access</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="">
                                                    <input type="hidden" name="edit_access_soldier_id" value="' . $soldier['SOLDIERID'] . '">
                                                    <div class="form-group">
                                                        <label for="edit_access_level">Select Access Level:</label>
                                                        <select name="edit_access_level" id="edit_access_level" class="form-control" required>
                                                            <option value="admin" ' . (getUserAccess($conn, $soldier['SOLDIERID']) == 'Admin' ? 'selected' : '') . '>Admin</option>
                                                            <option value="manager" ' . (getUserAccess($conn, $soldier['SOLDIERID']) == 'Manager' ? 'selected' : '') . '>Manager</option>
                                                            <option value="soldier" ' . (getUserAccess($conn, $soldier['SOLDIERID']) == 'Soldier' ? 'selected' : '') . '>Soldier</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" name="edit_access_submit" class="btn btn-primary">Update Access</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    </div>';
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

<?php include '../includes/footer.php'; ?>