<?php
session_start();

include '../includes/disposal_controller.php';

$dispType = getDisposalTypes($conn);

if (isset($_GET['soldier'])) {
    $soldierID = $_GET['soldier'];
    $_SESSION['temp'] = $soldierID;
} else {
    header("Location: {$_SERVER['PHP_SELF']}?soldier={$_SESSION['temp']}");
    exit();
}

// Edit Disposal
if (isset($_POST['edit_disposal_submit'])) {
    $editDisposalID = $_POST['edit_disposal_id'];
    $editDisposalType = $_POST['edit_disposal_type'];
    $editEndDate = $_POST['edit_end_date'];
    $editReason = $_POST['edit_reason'];

    updateDisposal($editDisposalID, $editDisposalType, $editEndDate, $editReason);
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Delete Disposal
if (isset($_POST['delete_disposal_submit'])) {
    $deleteDisposalID = $_POST['delete_disposal_id'];

    deleteDisposal($deleteDisposalID);

    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

global $activeDisposal;
$soldierDisposal = medicalDisposal($conn, null, null, null, $soldierID);
$activeDisposal = !empty($soldierDisposal) ? $soldierDisposal[0] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleFormSubmissions($conn, $dispType, $soldierID);
}
function handleFormSubmissions($conn, $dispType, $soldierID)
{
    if (isset($_POST['add_disposal_submit'])) {
        handleAddDisposal($soldierID);
    } elseif (isset($_POST['return_from_rs_submit'])) {
        handleReturnFromRS($_POST['edit_disposal_type'], $_POST['days']);
    } elseif (isset($_POST['admitted_in_cmh_submit'])) {
        handleAdmittedInCMH($_POST['edit_disposal_type']);
    } elseif (isset($_POST['add_disposal_cmh_submit'])) {
        handleAddDisposalCMH($_POST['edit_disposal_type'], $_POST['no_of_days']);
    } elseif (isset($_POST['no_disposal_submit'])) {
        handleNoDisposalCMH($soldierID);
    }
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

function handleAddDisposal($soldierID)
{
    $disposalType = 'R/S';
    $startDate = date('Y-m-d');
    $endDate = null;
    $reason = $_POST['reason'];
    addDisposal($soldierID, $disposalType, $startDate, $endDate, $reason);

}

function handleReturnFromRS($selectedDisposal, $days)
{
    global $activeDisposal;
    updateDisposal($activeDisposal['MEDICALID'], $selectedDisposal, date('Y-m-d', strtotime("+$days days")), null);
}

function handleAdmittedInCMH($selectedDisposal)
{
    global $activeDisposal;
    updateDisposal($activeDisposal['MEDICALID'], $selectedDisposal, null);
}

function handleAddDisposalCMH($selectedDisposal, $noOfDays)
{
    global $activeDisposal, $soldierID;
    $dischargeReason = 'Disposal given from ' . $activeDisposal['DISPOSALTYPE'] . ' for: ' . $activeDisposal['REMARKS'];
    $disposalReason = 'Discharged from ' . $activeDisposal['DISPOSALTYPE'] . ' for: ' . $activeDisposal['REMARKS'];
    updateDisposal($activeDisposal['MEDICALID'],'Discharged', date('Y-m-d'),$dischargeReason);
    addDisposal($soldierID, $selectedDisposal, date('Y-m-d'), date('Y-m-d', strtotime("+$noOfDays days")), $disposalReason);
}

function handleNoDisposalCMH($soldierID)
{
    global $activeDisposal, $reason;
    updateDisposal($activeDisposal['MEDICALID'], null, date('Y-m-d'));
}

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


        <?php include '../includes/alert.php';
        ?>
        <?php if ($activeDisposal && strpos($activeDisposal['DISPOSALTYPE'], 'CMH') === 0 && $activeDisposal['ENDDATE'] === null): ?>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-hospital text-warning fa-5x"></i>
                            <h5 class="mt-3">Discharged from
                                <?= $activeDisposal['DISPOSALTYPE'] ?>
                            </h5>
                            <button type="button" class="btn btn-warning btn-lg mt-3" data-toggle="modal"
                                data-target="#returnFromCMHModal">
                                Discharge
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($activeDisposal && $activeDisposal['DISPOSALTYPE'] === 'R/S' && $activeDisposal['ENDDATE'] === null): ?>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-hospital-alt text-primary fa-5x"></i>
                            <h5 class="mt-3">Return from R/S or Admitted in CMH</h5>
                            <button type="button" class="btn btn-primary mt-3" data-toggle="modal"
                                data-target="#selectDisposalModal">
                                Return from R/S
                            </button>
                            <button type="button" class="btn btn-warning mt-3" data-toggle="modal"
                                data-target="#updateToCMHModal">
                                Admitted in CMH
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-notes-medical text-primary fa-5x"></i>
                            <h5 class="mt-3">Send Report Sick</h5>
                            <button type="button" class="btn btn-primary mt-3" data-toggle="modal"
                                data-target="#addDisposalModal">
                                Send Report Sick
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

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
                            <button type="submit" name="add_disposal_submit" class="btn btn-primary">Send R/S</button>
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
                                <select name="edit_disposal_type" id="edit_disposal_type" class="form-control" required>
                                    <?php
                                    foreach ($dispType as $disposalId => $disposalType) {
                                        // Skip values starting with "CMH"
                                        if (strpos($disposalType, 'CMH') === 0) {
                                            continue;
                                        }

                                        $selected = ($disposalId == $disposal['DISPOSALID']) ? 'selected' : '';
                                        echo '<option value="' . $disposalType . '" ' . $selected . '>' . $disposalType . '</option>';
                                    }
                                    ?>

                                </select>
                            </div>
                            <div class="form-group">
                                <label for="days">Number of Days:</label>
                                <input type="number" name="days" id="days" class="form-control" >
                            </div>
                            <button type="submit" name="return_from_rs_submit" class="btn btn-primary">Return from
                                R/S</button>
                            <button type="submit" name="no_disposal_submit" class="btn btn-secondary">No
                                Disposal</button>
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
                            <div class="form-group">
                                <label>Select CMH Disposal Type:</label><br>
                                <?php
                                // Assuming $dispType is an array with disposal types
                                foreach ($dispType as $disposalId => $disposalType) {
                                    if (strpos($disposalType, 'CMH') === 0) {
                                        ?>
                                        <input type="radio" name="edit_disposal_type" value="<?php echo $disposalType; ?>"
                                            required>
                                        <?php echo $disposalType; ?><br>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <button type="submit" name="admitted_in_cmh_submit" class="btn btn-primary">Admitted in
                                CMH</button>
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
                                <select name="edit_disposal_type" id="edit_disposal_type" class="form-control" required>
                                    <?php
                                    foreach ($dispType as $disposalId => $disposalType) {
                                        // Skip values starting with "CMH"
                                        if (strpos($disposalType, 'CMH') === 0) {
                                            continue;
                                        }

                                        $selected = ($disposalId == $disposal['DISPOSALID']) ? 'selected' : '';
                                        echo '<option value="' . $disposalType . '" ' . $selected . '>' . $disposalType . '</option>';
                                    }
                                    ?>
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
                            <button type="submit" name="no_disposal_submit" class="btn btn-secondary">No
                                Disposal</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

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

                                    <?php
                                    $disposalList = medicalDisposal($conn, null, 'all', null, $soldierID);

                                    $i = 0;
                                    foreach ($disposalList as $disposal):
                                        echo '<tr>';
                                        echo '<td>' . ++$i . '</td>';
                                        echo '<td>' . $disposal['DISPOSALTYPE'] . '</td>';
                                        echo '<td>' . $disposal['STARTDATE'] . '</td>';
                                        echo '<td>' . $disposal['ENDDATE'] . '</td>';
                                        echo '<td>' . $disposal['REMARKS'] . '</td>';
                                        ?>

                                        <td>
                                            <div class="row">
                                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                                    data-target="#editDisposalModal-<?php echo $disposal['MEDICALID']; ?>">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-danger" data-toggle="modal"
                                                    data-target="#deleteDisposalModal-<?php echo $disposal['MEDICALID']; ?>">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>

                                        <!-- Edit Disposal Modal -->
                                        <div class="modal fade" id="editDisposalModal-<?php echo $disposal['MEDICALID']; ?>"
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
                                                                value="<?php echo $disposal['MEDICALID']; ?>">
                                                            <div class="form-group">
                                                                <label for="edit_disposal_type">Disposal Type:</label>
                                                                <select name="edit_disposal_type" id="edit_disposal_type"
                                                                    class="form-control" required>
                                                                    <?php
                                                                    foreach ($dispType as $disposalId => $disposalType) {
                                                                        // Skip values starting with "CMH"
                                                                        if (strpos($disposalType, 'CMH') === 0) {
                                                                            continue;
                                                                        }

                                                                        $selected = ($disposalId == $disposal['DISPOSALID']) ? 'selected' : '';
                                                                        echo '<option value="' . $disposalType . '" ' . $selected . '>' . $disposalType . '</option>';
                                                                    }
                                                                    ?>

                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="edit_end_date">End Date:</label>
                                                                <input type="date" name="edit_end_date" id="edit_end_date"
                                                                    class="form-control"
                                                                    value="<?php echo $disposal['ENDDATE'] ? date('Y-m-d', strtotime($disposal['ENDDATE'])) : 'null'; ?>">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="edit_reason">Reason:</label>
                                                                <input type="text" name="edit_reason" id="edit_reason"
                                                                    class="form-control"
                                                                    value="<?php echo $disposal['REMARKS']; ?>" required>
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
                                            id="deleteDisposalModal-<?php echo $disposal['MEDICALID']; ?>" tabindex="-1"
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
                                                                value="<?php echo $disposal['MEDICALID']; ?>">
                                                            <button type="submit" name="delete_disposal_submit"
                                                                class="btn btn-danger">Delete</button>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Cancel</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php include '../includes/footer.php'; ?>