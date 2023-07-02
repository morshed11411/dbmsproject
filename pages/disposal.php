<?php
session_start();

include '../includes/connection.php';

// Fetch soldier ID and name from the query parameter
if (isset($_GET['soldier'])) {
    $soldierID = $_GET['soldier'];

    // Fetch soldier details from the database
    $query = "SELECT SOLDIERID, NAME, COMPANYNAME FROM SOLDIER JOIN COMPANY USING (COMPANYID) WHERE SOLDIERID = :soldier_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_execute($stmt);

    $soldier = oci_fetch_assoc($stmt);

    // Redirect if soldier not found
    if (!$soldier) {
        header("Location: soldiers.php");
        exit();
    }

    oci_free_statement($stmt);
} else {
    header("Location: disposal.php?soldier=$soldierID");
    exit();
}

// Process the form submission to add disposal information
if (isset($_POST['add_disposal_submit'])) {
    $disposalType = 'R/S';
    $startDate = date('Y-m-d'); // System date
    $endDate = null; // Set End Date as NULL initially
    $reason = $_POST['reason'];

    // Insert disposal information into the database
    $query = "INSERT INTO MEDICALINFO (SOLDIERID, DISPOSALTYPE, STARTDATE, ENDDATE, REASON) 
              VALUES (:soldier_id, :disposal_type, TO_DATE(:start_date, 'YYYY-MM-DD'), 
              :end_date, :reason)";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_bind_by_name($stmt, ':disposal_type', $disposalType);
    oci_bind_by_name($stmt, ':start_date', $startDate);
    oci_bind_by_name($stmt, ':end_date', $endDate);
    oci_bind_by_name($stmt, ':reason', $reason);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Disposal information added successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to add disposal information: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: disposal.php?soldier=$soldierID");
    exit();
}


// Edit Disposal
if (isset($_POST['edit_disposal_submit'])) {
    $editDisposalID = $_POST['edit_disposal_id'];
    $editDisposalType = $_POST['edit_disposal_type'];
    $editEndDate = $_POST['edit_end_date'];
    $editReason = $_POST['edit_reason'];

    $editQuery = "UPDATE MEDICALINFO SET DISPOSALTYPE = :disposal_type, ENDDATE = TO_DATE(:end_date, 'YYYY-MM-DD'), REASON = :reason WHERE MEDICALID = :disposal_id";
    $editStmt = oci_parse($conn, $editQuery);
    oci_bind_by_name($editStmt, ':disposal_type', $editDisposalType);
    oci_bind_by_name($editStmt, ':end_date', $editEndDate);
    oci_bind_by_name($editStmt, ':reason', $editReason);
    oci_bind_by_name($editStmt, ':disposal_id', $editDisposalID);

    $editResult = oci_execute($editStmt);
    if ($editResult) {
        $_SESSION['success'] = "Disposal information updated successfully.";
    } else {
        $editError = oci_error($editStmt);
        $_SESSION['error'] = "Failed to update disposal information: " . $editError['message'];
    }

    oci_free_statement($editStmt);
    oci_close($conn);

    header("Location: disposal.php?soldier=$soldierID");
    exit();
}

// Delete Disposal
if (isset($_POST['delete_disposal_submit'])) {
    $deleteDisposalID = $_POST['delete_disposal_id'];

    $deleteQuery = "DELETE FROM MEDICALINFO WHERE MEDICALID = :disposal_id";
    $deleteStmt = oci_parse($conn, $deleteQuery);
    oci_bind_by_name($deleteStmt, ':disposal_id', $deleteDisposalID);

    $deleteResult = oci_execute($deleteStmt);
    if ($deleteResult) {
        $_SESSION['success'] = "Disposal information deleted successfully.";
    } else {
        $deleteError = oci_error($deleteStmt);
        $_SESSION['error'] = "Failed to delete disposal information: " . $deleteError['message'];
    }

    oci_free_statement($deleteStmt);
    oci_close($conn);

    header("Location: disposal.php?soldier=$soldierID");
    exit();
}

// Process the form submission to return from R/S
if (isset($_POST['return_from_rs_submit'])) {
    $selectedDisposal = $_POST['selected_disposal'];
    $days = $_POST['days'] - 1;

    // Update the disposal information for return from R/S
    $query = "UPDATE MEDICALINFO SET DISPOSALTYPE = :disposal_type, ENDDATE = TRUNC(SYSDATE) + :days WHERE SOLDIERID = :soldier_id AND ENDDATE IS NULL";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':disposal_type', $selectedDisposal);
    oci_bind_by_name($stmt, ':days', $days);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Returned from R/S successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update disposal information: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: disposal.php?soldier=$soldierID");
    exit();
}

// Process the form submission to admit in CMH
if (isset($_POST['admitted_in_cmh_submit'])) {
    // Update the disposal information for admitted in CMH
    $query = "UPDATE MEDICALINFO SET DISPOSALTYPE = 'CMH' WHERE SOLDIERID = :soldier_id AND ENDDATE IS NULL";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);

    $result = oci_execute($stmt);
    if ($result) {
        $_SESSION['success'] = "Admitted in CMH successfully.";
    } else {
        $error = oci_error($stmt);
        $_SESSION['error'] = "Failed to update disposal information: " . $error['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: disposal.php?soldier=$soldierID");
    exit();
}

// Handle Return from CMH form submissions
if (isset($_POST['add_disposal_cmh_submit'])) {
    $disposalType = $_POST['disposal_type'];
    $startDate = date('Y-m-d'); // System date
    $noOfDays = $_POST['no_of_days'];
    $endDate = date('Y-m-d', strtotime("+$noOfDays days", strtotime($startDate . ' +1 day')));
    $reason = $_POST['reason'];

    $updateQuery = "UPDATE MEDICALINFO SET ENDDATE = SYSDATE WHERE SOLDIERID = :soldier_id AND DISPOSALTYPE = 'CMH' AND ENDDATE IS NULL";
    $updateStmt = oci_parse($conn, $updateQuery);
    oci_bind_by_name($updateStmt, ':soldier_id', $soldierID);
    oci_execute($updateStmt);

    oci_free_statement($updateStmt);

    // Insert new disposal information into the database
    $insertQuery = "INSERT INTO MEDICALINFO (SOLDIERID, DISPOSALTYPE, STARTDATE, ENDDATE, REASON) 
                    VALUES (:soldier_id, :disposal_type, TO_DATE(:start_date, 'YYYY-MM-DD'), TO_DATE(:end_date, 'YYYY-MM-DD'), :reason)";
    $insertStmt = oci_parse($conn, $insertQuery);
    oci_bind_by_name($insertStmt, ':soldier_id', $soldierID);
    oci_bind_by_name($insertStmt, ':disposal_type', $disposalType);
    oci_bind_by_name($insertStmt, ':start_date', $startDate);
    oci_bind_by_name($insertStmt, ':end_date', $endDate);
    oci_bind_by_name($insertStmt, ':reason', $reason);

    $result = oci_execute($insertStmt);
    if ($result) {
        $_SESSION['success'] = "Disposal information added successfully.";
    } else {
        $error = oci_error($insertStmt);
        $_SESSION['error'] = "Failed to add disposal information: " . $error['message'];
    }

    oci_free_statement($insertStmt);
    oci_close($conn);

    header("Location: disposal.php?soldier=$soldierID");
    exit();
} elseif (isset($_POST['no_disposal_cmh_submit'])) {
    // Update the CMH disposal end date as sysdate
    $updateQuery = "UPDATE MEDICALINFO SET ENDDATE = SYSDATE WHERE SOLDIERID = :soldier_id AND DISPOSALTYPE = 'CMH' AND ENDDATE IS NULL";
    $updateStmt = oci_parse($conn, $updateQuery);
    oci_bind_by_name($updateStmt, ':soldier_id', $soldierID);
    oci_execute($updateStmt);

    oci_free_statement($updateStmt);
    oci_close($conn);

    header("Location: disposal.php?soldier=$soldierID");
    exit();
}


// Fetch disposal information for the soldier
$query = "SELECT * FROM (SELECT * FROM MEDICALINFO WHERE SOLDIERID = :soldier_id ORDER BY MEDICALID DESC) WHERE ROWNUM = 1";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierID);
oci_execute($stmt);

$disposal = oci_fetch_assoc($stmt);
$lastReason = $disposal['REASON'];

oci_free_statement($stmt);
oci_close($conn);

include '../includes/header.php';
?>


<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Disposal Information</h3>
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
                        <h5>Soldier Information</h5>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Soldier ID</th>
                                    <td>
                                        <?php echo $soldier['SOLDIERID']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>
                                        <?php echo $soldier['NAME']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Company</th>
                                    <td>
                                        <?php echo $soldier['COMPANYNAME']; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



        <?php if ($disposal && $disposal['DISPOSALTYPE'] === 'CMH' && $disposal['ENDDATE'] === null): ?>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Return from CMH</h5>
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#returnFromCMHModal">Return from CMH</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($disposal && $disposal['DISPOSALTYPE'] === 'R/S' && $disposal['ENDDATE'] === null): ?>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Return from R/S or Admitted in CMH</h5>
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#selectDisposalModal">Return from R/S</button>
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#updateToCMHModal">Admitted in CMH</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Send Report Sick</h5>
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#addDisposalModal">Send Report Sick</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Add Disposal Modal -->
        <div class="modal fade" id="addDisposalModal" tabindex="-1" role="dialog"
            aria-labelledby="addDisposalModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDisposalModalLabel">Send Report Sick</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="reason">Reason:</label>
                                <input type="text" name="reason" id="reason" class="form-control" required>
                            </div>
                            <button type="submit" name="add_disposal_submit" class="btn btn-primary">Send
                                R/S</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Select Disposal Modal -->
        <div class="modal fade" id="selectDisposalModal" tabindex="-1" role="dialog"
            aria-labelledby="selectDisposalModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="selectDisposalModalLabel">Return from R/S</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="selected_disposal">Select Disposal:</label>
                                <select name="selected_disposal" id="selected_disposal" class="form-control" required>
                                    <option value="PPG">PPG</option>
                                    <option value="PPGF">PPGF</option>
                                    <option value="SIQ">SIQ</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="days">Number of Days:</label>
                                <input type="number" name="days" id="days" class="form-control" required>
                            </div>
                            <button type="submit" name="return_from_rs_submit" class="btn btn-primary">Return
                                from R/S</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update to CMH Modal -->
        <div class="modal fade" id="updateToCMHModal" tabindex="-1" role="dialog"
            aria-labelledby="updateToCMHModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateToCMHModalLabel">Admitted in CMH</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <button type="submit" name="admitted_in_cmh_submit" class="btn btn-primary">Admitted
                                in CMH</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Return from CMH Modal -->
        <div class="modal fade" id="returnFromCMHModal" tabindex="-1" role="dialog"
            aria-labelledby="returnFromCMHModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="returnFromCMHModalLabel">Return from CMH</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="disposal_type">Disposal Type:</label>
                                <select name="disposal_type" id="disposal_type" class="form-control" required>
                                    <option value="PPG">PPG</option>
                                    <option value="PPGF">PPGF</option>
                                    <option value="SIQ">SIQ</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="no_of_days">Number of Days:</label>
                                <input type="number" name="no_of_days" id="no_of_days" class="form-control">
                            </div>
                            <input type="hidden" name="reason"
                                value="Discharged from CMH with disposal for: <?php echo $lastReason; ?>">
                            <button type="submit" name="add_disposal_cmh_submit" class="btn btn-primary">Add
                                Disposal</button>
                            <button type="submit" name="no_disposal_cmh_submit" class="btn btn-secondary">No
                                Disposal</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <?php
        // Fetch disposal information for the soldier
        $query = "SELECT * FROM MEDICALINFO WHERE SOLDIERID = :soldier_id";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':soldier_id', $soldierID);
        oci_execute($stmt);

        $disposalList = array();
        while ($row = oci_fetch_assoc($stmt)) {
            $disposal = new stdClass();
            $disposal->DisposalID = $row['MEDICALID'];
            $disposal->DisposalType = $row['DISPOSALTYPE'];
            $disposal->StartDate = $row['STARTDATE'];
            $disposal->EndDate = $row['ENDDATE'];
            $disposal->Reason = $row['REASON'];
            $disposalList[] = $disposal;
        }

        oci_free_statement($stmt);
        oci_close($conn);
        ?>

        <!-- ... previous code ... -->

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Disposal History</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Disposal ID</th>
                                        <th>Disposal Type</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Reason</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($disposalList as $disposal): ?>
                                        <tr>
                                            <td>
                                                <?php echo $disposal->DisposalID; ?>
                                            </td>
                                            <td>
                                                <?php echo $disposal->DisposalType; ?>
                                            </td>
                                            <td>
                                                <?php echo $disposal->StartDate; ?>
                                            </td>
                                            <td>
                                                <?php echo $disposal->EndDate; ?>
                                            </td>
                                            <td>
                                                <?php echo $disposal->Reason; ?>
                                            </td>
                                            <td>
                                                <div class="row">

                                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#editDisposalModal-<?php echo $disposal->DisposalID; ?>">
                                                        Edit
                                                    </button>
                                                    <button type="button" class="btn btn-danger" data-toggle="modal"
                                                        data-target="#deleteDisposalModal-<?php echo $disposal->DisposalID; ?>">
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Edit Disposal Modal -->
                                        <div class="modal fade" id="editDisposalModal-<?php echo $disposal->DisposalID; ?>"
                                            tabindex="-1" role="dialog" aria-labelledby="editDisposalModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editDisposalModalLabel">Edit Disposal
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="POST" action="">
                                                            <input type="hidden" name="edit_disposal_id"
                                                                value="<?php echo $disposal->DisposalID; ?>">
                                                            <div class="form-group">
                                                                <label for="edit_disposal_type">Disposal Type:</label>
                                                                <select name="edit_disposal_type" id="edit_disposal_type"
                                                                    class="form-control" required>
                                                                    <option value="PPG" <?php if ($disposal->DisposalType === 'PPG')
                                                                        echo 'selected'; ?>>
                                                                        PPG</option>
                                                                    <option value="PPGF" <?php if ($disposal->DisposalType === 'PPGF')
                                                                        echo 'selected'; ?>>
                                                                        PPGF</option>
                                                                    <option value="SIQ" <?php if ($disposal->DisposalType === 'SIQ')
                                                                        echo 'selected'; ?>>
                                                                        SIQ</option>
                                                                    <option value="CMH" <?php if ($disposal->DisposalType === 'CMH')
                                                                        echo 'selected'; ?>>
                                                                        CMH</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="edit_end_date">End Date:</label>
                                                                <input type="date" name="edit_end_date" id="edit_end_date"
                                                                    class="form-control"
                                                                    value="<?php echo $disposal->EndDate; ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="edit_reason">Reason:</label>
                                                                <input type="text" name="edit_reason" id="edit_reason"
                                                                    class="form-control"
                                                                    value="<?php echo $disposal->Reason; ?>" required>
                                                            </div>
                                                            <button type="submit" name="edit_disposal_submit"
                                                                class="btn btn-primary">Save Changes</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Delete Disposal Modal -->
                                        <div class="modal fade"
                                            id="deleteDisposalModal-<?php echo $disposal->DisposalID; ?>" tabindex="-1"
                                            role="dialog" aria-labelledby="deleteDisposalModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteDisposalModalLabel">Delete
                                                            Disposal
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete this disposal?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <form method="POST" action="">
                                                            <input type="hidden" name="delete_disposal_id"
                                                                value="<?php echo $disposal->DisposalID; ?>">
                                                            <button type="submit" name="delete_disposal_submit"
                                                                class="btn btn-danger">Delete</button>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Cancel</button>
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

        <!-- ... remaining code ... -->

    </div>
</section>

<?php include '../includes/footer.php'; ?>