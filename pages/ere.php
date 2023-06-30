<?php
session_start();

include '../includes/connection.php';

if (isset($_POST['add_ere_submit'])) {
    $ere_name = $_POST['ere_name'];

    $query = "INSERT INTO ERE (ERENAME) VALUES (:ere_name)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':ere_name', $ere_name);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "ERE added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add ERE: " . $error['message'];
    }

    oci_free_statement($stmt);
}

if (isset($_POST['edit_ere_submit'])) {
    $ere_id = $_POST['edit_ere_id'];
    $ere_name = $_POST['edit_ere_name'];

    $query = "UPDATE ERE SET ERENAME = :ere_name WHERE EREID = :ere_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':ere_name', $ere_name);
    oci_bind_by_name($stmt, ':ere_id', $ere_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "ERE updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update ERE: " . $error['message'];
    }

    oci_free_statement($stmt);
}

if (isset($_POST['delete_ere_submit'])) {
    $ere_id = $_POST['delete_ere_id'];

    $query = "DELETE FROM ERE WHERE EREID = :ere_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':ere_id', $ere_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "ERE deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete ERE: " . $error['message'];
    }

    oci_free_statement($stmt);
}

oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>ERE List Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addEREModal">Add ERE</button>
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
                                    <th>ERE ID</th>
                                    <th>ERE Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM ERE ORDER BY EREID";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);

                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['EREID'] . "</td>";
                                    echo "<td>" . $row['ERENAME'] . "</td>";
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editEREModal-' . $row['EREID'] . '">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>';
                                    echo '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteEREModal-' . $row['EREID'] . '">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>';
                                    echo "</td>";
                                    echo "</tr>";

                                    // Edit ERE Modal
                                    echo '<div class="modal fade" id="editEREModal-' . $row['EREID'] . '" tabindex="-1" role="dialog" aria-labelledby="editEREModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editEREModalLabel">Edit ERE</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_ere_id" value="' . $row['EREID'] . '">
                                                        <div class="form-group">
                                                            <label for="edit_ere_name">ERE Name:</label>
                                                            <input type="text" name="edit_ere_name" id="edit_ere_name" class="form-control" value="' . $row['ERENAME'] . '" required>
                                                        </div>
                                                        <button type="submit" name="edit_ere_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';

                                    // Delete ERE Modal
                                    echo '<div class="modal fade" id="deleteEREModal-' . $row['EREID'] . '" tabindex="-1" role="dialog" aria-labelledby="deleteEREModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteEREModalLabel">Delete ERE</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this ERE?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_ere_id" value="' . $row['EREID'] . '">
                                                        <button type="submit" name="delete_ere_submit" class="btn btn-danger">Delete</button>
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

<!-- Add ERE Modal -->
<div class="modal fade" id="addEREModal" tabindex="-1" role="dialog" aria-labelledby="addEREModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEREModalLabel">Add ERE</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="ere_name">ERE Name:</label>
                        <input type="text" name="ere_name" id="ere_name" class="form-control" required>
                    </div>
                    <input type="submit" name="add_ere_submit" value="Add ERE" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
