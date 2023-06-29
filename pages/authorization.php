<?php
session_start();

include '../includes/connection.php';


// Process the form submission to update the authorization
if (isset($_POST['update_authorization_submit'])) {
    $companyID = $_POST['company_id'];
    $manpower = $_POST['manpower'];

    // Update the authorization in the database
    $updateQuery = "MERGE INTO AUTHORIZATION A USING (SELECT :companyID AS COMPANYID, :manpower AS MANPOWER FROM dual) B 
                    ON (A.COMPANYID = B.COMPANYID)
                    WHEN MATCHED THEN UPDATE SET A.MANPOWER = B.MANPOWER
                    WHEN NOT MATCHED THEN INSERT (A.COMPANYID, A.MANPOWER) VALUES (B.COMPANYID, B.MANPOWER)";
    $stmtUpdate = oci_parse($conn, $updateQuery);
    oci_bind_by_name($stmtUpdate, ':companyID', $companyID);
    oci_bind_by_name($stmtUpdate, ':manpower', $manpower);

    $resultUpdate = oci_execute($stmtUpdate);
    if ($resultUpdate) {
        $_SESSION['success'] = "Authorization updated successfully.";
        header("Location: authorization.php"); // Redirect to prevent form resubmission
        exit();
    } else {
        $error = oci_error($stmtUpdate);
        $_SESSION['error'] = "Failed to update authorization: " . $error['message'];
    }

    oci_free_statement($stmtUpdate);
    oci_close($conn);
}

// Fetch the list of companies
$queryCompanies = "SELECT COMPANYID, COMPANYNAME FROM COMPANY";
$stmtCompanies = oci_parse($conn, $queryCompanies);
oci_execute($stmtCompanies);

$companyList = array();
while ($rowCompany = oci_fetch_assoc($stmtCompanies)) {
    $company = new stdClass();
    $company->COMPANYID = $rowCompany['COMPANYID'];
    $company->COMPANYNAME = $rowCompany['COMPANYNAME'];
    $companyList[] = $company;
}

oci_free_statement($stmtCompanies);

// Fetch the current authorization for each company
$queryAuthorization = "SELECT MANPOWER, COMPANYID FROM AUTHORIZATION";
$stmtAuthorization = oci_parse($conn, $queryAuthorization);
oci_execute($stmtAuthorization);

$authorizationList = array();
while ($rowAuthorization = oci_fetch_assoc($stmtAuthorization)) {
    $authorization = new stdClass();
    $authorization->MANPOWER = $rowAuthorization['MANPOWER'];
    $authorization->COMPANYID = $rowAuthorization['COMPANYID'];
    $authorizationList[] = $authorization;
}

oci_free_statement($stmtAuthorization);
oci_close($conn);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Authorization Management</h3>
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
                                    <th>Company</th>
                                    <th>Authorization</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($companyList as $company): ?>
                                    <?php
                                    $authorization = null;
                                    foreach ($authorizationList as $auth) {
                                        if ($auth->COMPANYID == $company->COMPANYID) {
                                            $authorization = $auth;
                                            break;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $company->COMPANYNAME; ?></td>
                                        <td><?php echo $authorization ? $authorization->MANPOWER : 'Not Set'; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateAuthorizationModal-<?php echo $company->COMPANYID; ?>">
                                                <i class="fas fa-edit"></i> Update
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Update Authorization Modal -->
                                    <div class="modal fade" id="updateAuthorizationModal-<?php echo $company->COMPANYID; ?>" tabindex="-1" role="dialog" aria-labelledby="updateAuthorizationModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="updateAuthorizationModalLabel">Update Authorization for <?php echo $company->COMPANYNAME; ?></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="company_id" value="<?php echo $company->COMPANYID; ?>">
                                                        <div class="form-group">
                                                            <label for="manpower">Authorization:</label>
                                                            <input type="number" name="manpower" id="manpower" class="form-control" value="<?php echo $authorization ? $authorization->MANPOWER : ''; ?>" required>
                                                        </div>
                                                        <button type="submit" name="update_authorization_submit" class="btn btn-primary">Save Changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include '../includes/footer.php';
?>
