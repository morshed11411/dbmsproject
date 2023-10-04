<?php
session_start();

include '../includes/connection.php';

// Get the company ID or status ID from the query parameters
$companyID = $_GET['company'] ?? null;
$statusID = $_GET['status'] ?? null;

// Build the SQL query based on the provided parameters
$query = "SELECT S.SOLDIERID, R.RANK, T.TRADE, S.NAME, S.MARITALSTATUS, S.BLOODGROUP, C.COMPANYNAME, S.DISTRICT, ROUND((SYSDATE - S.DATEOFBIRTH)/365) AS AGE, ROUND((SYSDATE - S.DATEOFENROLL)/365) AS SERVICEAGE
FROM SOLDIER S
LEFT JOIN RANKS R ON S.RANKID = R.RANKID
LEFT JOIN TRADE T ON S.TRADEID = T.TRADEID
LEFT JOIN COMPANY C ON S.COMPANYID = C.COMPANYID";

if ($companyID !== null) {
    $query .= " WHERE S.COMPANYID = :company_id";
} elseif ($statusID !== null) {
    $query .= " WHERE S.SERVINGSTATUS = :status_id";
}

$stmt = oci_parse($conn, $query);

if ($companyID !== null) {
    oci_bind_by_name($stmt, ':company_id', $companyID);
} elseif ($statusID !== null) {
    oci_bind_by_name($stmt, ':status_id', $statusID);
}

oci_execute($stmt);

$soldierList = array();
while ($row = oci_fetch_assoc($stmt)) {
    $soldier = new stdClass();
    $soldier->SoldierID = $row['SOLDIERID'];
    $soldier->Rank = $row['RANK'];
    $soldier->Trade = $row['TRADE'];
    $soldier->Name = $row['NAME'];
    $soldier->Company = $row['COMPANYNAME'];
    $soldier->BloodGroup = $row['BLOODGROUP'];
    $soldier->District = $row['DISTRICT'];
    $soldier->Age = $row['AGE'];
    $soldier->ServiceAge = $row['SERVICEAGE'];
    $soldier->MaritalStatus = $row['MARITALSTATUS'];
    $soldierList[] = $soldier;
}

oci_free_statement($stmt);

if (isset($_POST['delete_soldier_submit'])) {
    // Get the soldier ID from the form
    $soldierId = $_POST['delete_soldier_id'];

    // Perform the delete operation
    $query = "DELETE FROM SOLDIER WHERE SOLDIERID = :soldierId";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldierId', $soldierId);

    if (oci_execute($stmt)) {
        $_SESSION['success'] = 'Soldier deleted successfully.';
    } else {
        $e = oci_error($stmt);
        $_SESSION['error'] = "Failed to delete soldier: " . $e['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    // Redirect to the appropriate page after deletion
    header('Location: soldiers.php');
    exit;
}
oci_close($conn);

include '../includes/header.php';
?>

<style>
    @media print {
        .no-print {
            display: table-cell !important;
        }
    }
</style>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Soldier Management</h3>
        </div>
        <div class="text-right">
            <a href="add_soldier.php" class="btn btn-primary">Add New Soldier</a>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php include '../includes/alert.php'; ?>
        <?php
        // ... (Your existing code above) ...
        
        // Add a function to get the rank-wise soldier count
        function getRankWiseSoldierCount($conn)
        {
            $rankCounts = array();
            $query = "SELECT R.RANK, COUNT(S.SOLDIERID) AS COUNT
              FROM RANKS R
              LEFT JOIN SOLDIER S ON S.RANKID = R.RANKID
              GROUP BY R.RANK";
            $stmt = oci_parse($conn, $query);
            oci_execute($stmt);

            while ($row = oci_fetch_assoc($stmt)) {
                $rankCounts[$row['RANK']] = $row['COUNT'];
            }

            oci_free_statement($stmt);

            return $rankCounts;
        }

        // Get rank-wise soldier count
        $rankCounts = getRankWiseSoldierCount($conn);
        ?>

        <!-- Add a new table to display rank-wise soldier count -->


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <?php foreach ($rankCounts as $rank => $count): ?>
                                        <th>
                                            <a href="soldiers.php?rank=<?php echo urlencode($rank); ?>">
                                                <?php echo $rank; ?>
                                            </a>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php foreach ($rankCounts as $count): ?>
                                        <td>
                                            <?php echo $count; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

                        <table id="soldierTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Pers No</th>
                                    <th>Rank</th>
                                    <th>Trade</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th class="no-export">Blood Gp</th>
                                    <th class="no-export">District</th>
                                    <th class="no-export ">Age</th>
                                    <th class="no-export d-none">Marital Status</th>
                                    <th class="no-export">Action</th>
                                    <th class="no-print d-none">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($soldierList as $soldier): ?>
                                    <tr>
                                        <td>
                                            <?php echo $soldier->SoldierID; ?>
                                        </td>
                                        <td>
                                            <?php echo $soldier->Rank; ?>
                                        </td>
                                        <td>
                                            <?php echo $soldier->Trade; ?>
                                        </td>
                                        <td>
                                            <a href="profile.php?soldierid=<?php echo $soldier->SoldierID; ?>">
                                                <?php echo $soldier->Name; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php echo $soldier->Company; ?>
                                        </td>
                                        <td>
                                            <?php echo $soldier->BloodGroup; ?>
                                        </td>
                                        <td>
                                            <?php echo $soldier->District; ?>
                                        </td>
                                        <td>
                                            <?php echo $soldier->Age; ?>
                                        </td>
                                        <td class="d-none">
                                            <?php echo $soldier->MaritalStatus; ?>
                                        </td>
                                        <td>

                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Options
                                                </button>
                                                <div class="dropdown-menu">

                                                    <a class="dropdown-item"
                                                        href="send_leave.php?soldier=<?php echo $soldier->SoldierID; ?>">Send
                                                        Leave</a>
                                                    <a class="dropdown-item"
                                                        href="disposal.php?soldier=<?php echo $soldier->SoldierID; ?>">Send
                                                        Report Sick
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="manage_training.php?soldier=<?php echo $soldier->SoldierID; ?>">Manage
                                                        Training</a>
                                                    <a class="dropdown-item"
                                                        href="give_punishment.php?soldier=<?php echo $soldier->SoldierID; ?>">Give
                                                        Punishment</a>
                                                    <a class="dropdown-item"
                                                        href="assign_appointment.php?soldier=<?php echo $soldier->SoldierID; ?>">Assign
                                                        Appointment</a>
                                                    <a class="dropdown-item"
                                                        href="assign_team.php?soldier=<?php echo $soldier->SoldierID; ?>">Assign
                                                        Team</a>
                                                    <a class="dropdown-item"
                                                        href="add_ere.php?soldier=<?php echo $soldier->SoldierID; ?>">Manage
                                                        ERE</a>
                                                    <a class="dropdown-item"
                                                        href="add_comd.php?soldier=<?php echo $soldier->SoldierID; ?>">Manage
                                                        Comd</a>
                                                    <a class="dropdown-item"
                                                        href="update_status.php?soldier=<?php echo $soldier->SoldierID; ?>">Update
                                                        State</a>
                                                    <a class="dropdown-item"
                                                        href="uploadimage.php?soldier=<?php echo $soldier->SoldierID; ?>">Upload
                                                        Image</a>

                                                </div>
                                            </div>

                                            <div class="btn-group">
                                                <button type="button" class="btn btn-warning dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Select
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a href="edit_soldier.php?soldier=<?php echo $soldier->SoldierID; ?>"
                                                        class="dropdown-item text-warning"><i
                                                            class="fas fa-pen"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                                                        Edit</a>
                                                    <div class="dropdown-divider"></div>

                                                    <button type="button" class="dropdown-item text-danger"
                                                        data-toggle="modal"
                                                        data-target="#deleteSoldierModal-<?php echo $soldier->SoldierID; ?>">
                                                        <i class="fas fa-trash"></i> &nbsp;&nbsp;&nbsp;&nbsp; Delete
                                                    </button>
                                                </div>

                                            </div>

                                            <!-- Delete Button -->

                                            <!-- Delete Soldier Modal -->
                                            <div class="modal fade"
                                                id="deleteSoldierModal-<?php echo $soldier->SoldierID; ?>" tabindex="-1"
                                                role="dialog"
                                                aria-labelledby="deleteSoldierModalLabel-<?php echo $soldier->SoldierID; ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="deleteSoldierModalLabel-<?php echo $soldier->SoldierID; ?>">
                                                                Delete Soldier
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to delete this soldier?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <form method="POST" action="soldiers.php">
                                                                <input type="hidden" name="delete_soldier_id"
                                                                    value="<?php echo $soldier->SoldierID; ?>">
                                                                <button type="submit" name="delete_soldier_submit"
                                                                    class="btn btn-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </td>
                                        <td class="no-print d-none">
                                            <!-- Remarks -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<?php include '../includes/footer.php'; ?>

<script>

    $(document).ready(function () {
        var table = $('#soldierTable').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    }
                },
                'colvis'
            ]
        });

        table.buttons().container().appendTo('#soldierTable_wrapper .col-md-6:eq(0)');
    });

</script>