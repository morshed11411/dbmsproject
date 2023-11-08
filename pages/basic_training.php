<?php
session_start();

include '../includes/connection.php';

// Function to execute SQL queries
function executeQuery($query, $params = []) {
    global $conn;
   // $stmt = $conn->prepare($query);
    if ($params) {
        foreach ($params as $paramName => $paramValue) {
     //       $stmt->bindValue($paramName, $paramValue);
        }
    }
    //$result = $stmt->execute();
    //return $result ? $stmt : false;
}

// Function to handle success messages
function setSuccessMessage($message) {
    $_SESSION['success'] = $message;
}

// Function to handle error messages
function setErrorMessage($message) {
    $_SESSION['error'] = $message;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_submit'])) {
        // Handle add operation
        $query = "INSERT INTO BASICTRAINING (TRAININGCODE, TRAININGNAME) VALUES (:trainingcode, :trainingname)";
        $params = [
            ':trainingcode' => $_POST['trainingcode'],
            ':trainingname' => $_POST['trainingname'],
        ];
        
        $result = executeQuery($query, $params);
        
        if ($result) {
            setSuccessMessage("Record added successfully.");
        } else {
            setErrorMessage("Failed to add record.");
        }
    } elseif (isset($_POST['edit_submit'])) {
        // Handle edit operation
        $query = "UPDATE BASICTRAINING SET TRAININGCODE = :trainingcode, TRAININGNAME = :trainingname WHERE TRAININGID = :record_id";
        $params = [
            ':trainingcode' => $_POST['trainingcode'],
            ':trainingname' => $_POST['trainingname'],
            ':record_id' => $_POST['record_id'],
        ];
        
        $result = executeQuery($query, $params);
        
        if ($result) {
            setSuccessMessage("Record updated successfully.");
        } else {
            setErrorMessage("Failed to update record.");
        }
    } elseif (isset($_POST['delete_submit'])) {
        // Handle delete operation
        $query = "DELETE FROM BASICTRAINING WHERE TRAININGID = :record_id";
        $params = [':record_id' => $_POST['record_id']];
        
        $result = executeQuery($query, $params);
        
        if ($result) {
            setSuccessMessage("Record deleted successfully.");
        } else {
            setErrorMessage("Failed to delete record.");
        }
    }
}

// Fetch records
$query = "SELECT * FROM BASICTRAINING";
$stmt = executeQuery($query);

include '../includes/header.php';
?>

<!-- Your HTML and form code for displaying records and CRUD operations here -->
<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Record Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addModal">Add Record</button>
        </div>
    </div>
</div>

<!-- Add Record Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Record</h5>
                </div>
                <div class="modal-body">
                    <!-- Add record form here -->
                    <div class="form-group">
                        <label for="trainingcode">Training Code</label>
                        <input type="text" class="form-control" id="trainingcode" name="trainingcode" required>
                    </div>
                    <div class="form-group">
                        <label for="trainingname">Training Name</label>
                        <input type="text" class="form-control" id="trainingname" name="trainingname" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="add_submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Record Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Record</h5>
                </div>
                <div class="modal-body">
                    <!-- Edit record form here -->
                    <input type="hidden" name="record_id" id="edit_record_id">
                    <div class="form-group">
                        <label for="edit_trainingcode">Training Code</label>
                        <input type="text" class="form-control" id="edit_trainingcode" name="trainingcode" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_trainingname">Training Name</label>
                        <input type="text" class="form-control" id="edit_trainingname" name="trainingname" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="edit_submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Record Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Record</h5>
                </div>
                <div class="modal-body">
                    <!-- Delete record confirmation here -->
                    <p>Are you sure you want to delete this record?</p>
                    <input type="hidden" name="record_id" id="delete_record_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" name="delete_submit">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Display records in a table -->
<section class="content">
    <div class="container-fluid">
        <?php include '../includes/alert.php'; ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Records</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Training Code</th>
                                    <th>Training Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch and display records here
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<tr>';
                                    echo '<td>' . $row['TRAININGID'] . '</td>';
                                    echo '<td>' . $row['TRAININGCODE'] . '</td>';
                                    echo '<td>' . $row['TRAININGNAME'] . '</td>';
                                    echo '<td>';
                                    echo '<button class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editModal" data-id="' . $row['TRAININGID'] . '">Edit</button>';
                                    echo '<button class="btn btn-sm btn-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-id="' . $row['TRAININGID'] . '">Delete</button>';
                                    echo '</td>';
                                    echo '</tr>';
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

<!-- JavaScript code to handle edit and delete buttons -->
<script>
    $(document).ready(function () {
        $('.edit-btn').click(function () {
            var id = $(this).data('id');
            var trainingcode = $(this).closest('tr').find('td:eq(1)').text();
            var trainingname = $(this).closest('tr').find('td:eq(2)').text();
            
            $('#edit_record_id').val(id);
            $('#edit_trainingcode').val(trainingcode);
            $('#edit_trainingname').val(trainingname);
        });

        $('.delete-btn').click(function () {
            var id = $(this).data('id');
            $('#delete_record_id').val(id);
        });
    });
</script>

<?php include '../includes/footer.php'; ?>
