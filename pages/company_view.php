<?php
session_start();

include '../includes/connection.php';

if (isset($_POST['add_company_submit'])) {
    $company_name = $_POST['company_name'];

    $query = "INSERT INTO COMPANY (COMPANYNAME) VALUES (:company_name)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':company_name', $company_name);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Company added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add company: " . $error['message'];
    }

    oci_free_statement($stmt);
}

if (isset($_POST['edit_company_submit'])) {
    $company_id = $_POST['edit_company_id'];
    $company_name = $_POST['edit_company_name'];

    $query = "UPDATE COMPANY SET COMPANYNAME = :company_name WHERE COMPANYID = :company_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':company_name', $company_name);
    oci_bind_by_name($stmt, ':company_id', $company_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Company updated successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update company: " . $error['message'];
    }

    oci_free_statement($stmt);
}

if (isset($_POST['delete_company_submit'])) {
    $company_id = $_POST['delete_company_id'];

    $query = "DELETE FROM COMPANY WHERE COMPANYID = :company_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':company_id', $company_id);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Company deleted successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete company: " . $error['message'];
    }

    oci_free_statement($stmt);
}

oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Company Management</h3>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addCompanyModal">Add Company</button>
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
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Company ID</th>
                                    <th>Company Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM COMPANY ORDER BY COMPANYID";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);
                                $i=1;
                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo '<td>' . $i . '</td>';
                                    echo '<td><a href="soldiers.php?company=' . $row['COMPANYID'] . '">';
                                    echo '<div style="height:100%;width:100%">';
                                    echo $row['COMPANYNAME'];
                                    echo '</div>';
                                    echo '</a></td>';
                                    echo "<td>";
                                    echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editCompanyModal-' . $row['COMPANYID'] . '">
                                            <i class="fas fa-edit"></i> Edit
                                          </button>';
                                    echo '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteCompanyModal-' . $row['COMPANYID'] . '">
                                            <i class="fas fa-trash"></i> Delete
                                          </button>';
                                    echo "</td>";
                                    echo "</tr>";
                                    

                                    // Edit Company Modal
                                    echo '<div class="modal fade" id="editCompanyModal-' . $row['COMPANYID'] . '" tabindex="-1" role="dialog" aria-labelledby="editCompanyModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editCompanyModalLabel">Edit Company</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="edit_company_id" value="' . $row['COMPANYID'] . '">
                                                        <div class="form-group">
                                                            <label for="edit_company_name">Company Name:</label>
                                                            <input type="text" name="edit_company_name" id="edit_company_name" class="form-control" value="' . $row['COMPANYNAME'] . '" required>
                                                        </div>
                                                        <button type="submit" name="edit_company_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';

                                    // Delete Company Modal
                                    echo '<div class="modal fade" id="deleteCompanyModal-' . $row['COMPANYID'] . '" tabindex="-1" role="dialog" aria-labelledby="deleteCompanyModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteCompanyModalLabel">Delete Company</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this company?</p>
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="delete_company_id" value="' . $row['COMPANYID'] . '">
                                                        <button type="submit" name="delete_company_submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                    $i++;
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

<!-- Add Company Modal -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" role="dialog" aria-labelledby="addCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCompanyModalLabel">Add Company</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="company_name">Company Name:</label>
                        <input type="text" name="company_name" id="company_name" class="form-control" required>
                    </div>
                    <input type="submit" name="add_company_submit" value="Add Company" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
