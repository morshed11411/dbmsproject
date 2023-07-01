<?php
session_start();

include '../includes/connection.php';

// Get the company ID or status ID from the query parameters
$companyID = $_GET['company'] ?? null;
$statusID = $_GET['status'] ?? null;

// Build the SQL query based on the provided parameters
$query = "SELECT S.SOLDIERID, R.RANK, T.TRADE, S.NAME, C.COMPANYNAME, S.DISTRICT, ROUND((SYSDATE - S.DATEOFBIRTH)/365) AS AGE, ROUND((SYSDATE - S.DATEOFENROLL)/365) AS SERVICEAGE
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
    $soldier->District = $row['DISTRICT'];
    $soldier->Age = $row['AGE'];
    $soldier->ServiceAge = $row['SERVICEAGE'];
    $soldierList[] = $soldier;
}

oci_free_statement($stmt);
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        <table id="soldierTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Soldier ID</th>
                                    <th>Rank</th>
                                    <th>Trade</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>District</th>
                                    <th class="no-export">Age</th>
                                    <th class="no-export">Service Age</th>
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
                                            <?php echo $soldier->District; ?>
                                        </td>
                                        <td>
                                            <?php echo $soldier->Age; ?>
                                        </td>
                                        <td>
                                            <?php echo $soldier->ServiceAge; ?>
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
                                                        href="add_disposal.php?soldier=<?php echo $soldier->SoldierID; ?>">Add
                                                        Disposal</a>
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
                                            <a href="edit_soldier.php?soldier=<?php echo $soldier->SoldierID; ?>"
                                                class="btn btn-warning">Edit</a>
                                            <a href="delete_soldier.php?soldier=<?php echo $soldier->SoldierID; ?>"
                                                class="btn btn-danger">Delete</a>
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