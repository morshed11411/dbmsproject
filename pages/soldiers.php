<?php
session_start();

include '../includes/connection.php';
include '../includes/parade_controller.php';



$userCoy = isset($_GET['company']) ? $_GET['company'] : $_SESSION['usercoy'];

$role = $_SESSION['role'];
$soldierList = [];
$byRankCount = [];
$soldiersByRank = [];


if ($role === 'admin') {
    if (isset($_GET['company'])) {
        $soldierList = getSoldiers($conn, null, null, null, false, $userCoy, null);

    } else {
        $soldierList = getSoldiers($conn, null, null, null, false, null, null);

    }
} elseif ($role === 'manager') {
    $soldierList = getSoldiers($conn, null, null, null, false, $userCoy, null);
}


if ($role === 'admin') {

    foreach ($ranks as $rank) {
        if (isset($_GET['company'])) {
            $soldiersByRank = getSoldiers($conn, null, $rank['NAME'], null, false, $userCoy, null);

        } else {
            $soldiersByRank = getSoldiers($conn, null, $rank['NAME'], null, false, null, null);

        }

        $byRankCount[$rank['ID']] = count($soldiersByRank);
    }
}


if ($role === 'manager') {
    foreach ($ranks as $rank) {
        $soldiersByRank = getSoldiers($conn, null, $rank['NAME'], null, false, $userCoy, null);

        $byRankCount[$rank['ID']] = count($soldiersByRank);
    }
}

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
            <?php if ($_SESSION['role'] === 'admin') { ?>
                <a href="add_soldier.php" class="btn btn-primary">Add New Soldier</a>
                <a href="import.php" class="btn btn-secondary">CSV Import</a>

            <?php } ?>

        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php include '../includes/alert.php'; ?>

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Soldier By Rank</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <?php foreach ($ranks as $rank): ?>
                                <?php if ($byRankCount[$rank['ID']] > 0): ?>
                                    <th class="text-center">
                                        <?php echo $rank['NAME']; ?>
                                    </th>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($ranks as $rank): ?>
                                <?php if ($byRankCount[$rank['ID']] > 0): ?>
                                    <td class="text-center">
                                        <?php echo $byRankCount[$rank['ID']] ? $byRankCount[$rank['ID']] : 0; ?>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>


        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">All Posted Soldiers</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
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
                                    <?php echo $soldier['SOLDIERID']; ?>
                                </td>
                                <td>
                                    <?php echo $soldier['RANK']; ?>
                                </td>
                                <td>
                                    <?php echo $soldier['TRADE']; ?>
                                </td>
                                <td>
                                    <a href="profile.php?soldierid=<?php echo $soldier['SOLDIERID']; ?>">
                                        <?php echo $soldier['NAME']; ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo $soldier['COMPANYNAME']; ?>
                                </td>
                                <td>
                                    <?php echo $soldier['BLOODGROUP']; ?>
                                </td>
                                <td>
                                    <?php echo $soldier['DISTRICT']; ?>
                                </td>
                                <td>
                                    <?php echo calculateAge($soldier['DATEOFBIRTH']); ?>
                                </td>
                                <td class="d-none">
                                    <?php echo $soldier['MARITALSTATUS']; ?>
                                </td>

                                <?php if ($_SESSION['role'] == 'manager') { ?>

                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                Options
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item"
                                                    href="send_leave.php?soldier=<?php echo $soldier['SOLDIERID']; ?>">Send
                                                    Leave Request</a>
                                                <a class="dropdown-item"
                                                    href="report_sick.php?soldier=<?php echo $soldier['SOLDIERID']; ?>">Send
                                                    Report Sick
                                                </a>
                                                <a class="dropdown-item"
                                                    href="uploadimage.php?soldier=<?php echo $soldier['SOLDIERID']; ?>">Upload
                                                    Image</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="no-print d-none">
                                        <!-- Remarks -->
                                    </td>
                                    <?php
                                }
                                if ($_SESSION['role'] == 'admin') { ?>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                Options
                                            </button>
                                            <div class="dropdown-menu">

                                                <a class="dropdown-item"
                                                    href="send_leave.php?soldier=<?php echo $soldier['SOLDIERID']; ?>">Send
                                                    Leave</a>
                                                <a class="dropdown-item"
                                                    href="report_sick.php?soldier=<?php echo $soldier['SOLDIERID']; ?>">Send
                                                    Report Sick
                                                </a>
                                                
                                                <a class="dropdown-item"
                                                    href="give_punishment.php?soldier=<?php echo $soldier['SOLDIERID']; ?>">Manage
                                                    Punishment</a>
                                                <a class="dropdown-item"
                                                    href="assign_appointment.php?soldier=<?php echo $soldier['SOLDIERID']; ?>">Assign
                                                    Appointment</a>
                                                
                                                <a class="dropdown-item"
                                                    href="add_ere.php?soldier=<?php echo $soldier['SOLDIERID']; ?>">Manage
                                                    ERE</a>
                                                <a class="dropdown-item"
                                                    href="add_comd.php?soldier=<?php echo $soldier['SOLDIERID']; ?>">Manage
                                                    Attachment</a>
                                                <a class="dropdown-item"
                                                    href="update_status.php?soldier=<?php echo $soldier['SOLDIERID']; ?>">Serving
                                                    Status</a>
                                                <a class="dropdown-item"
                                                    href="uploadimage.php?soldier=<?php echo $soldier['SOLDIERID']; ?>">Upload
                                                    Image</a>

                                            </div>
                                        </div>

                                        <div class="btn-group">
                                            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                Select
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="edit_soldier.php?soldier=<?php echo $soldier['SOLDIERID']; ?>"
                                                    class="dropdown-item text-warning"><i
                                                        class="fas fa-pen"></i>&nbsp;&nbsp;&nbsp;&nbsp;
                                                    Edit</a>
                                                <div class="dropdown-divider"></div>

                                                <button type="button" class="dropdown-item text-danger" data-toggle="modal"
                                                    data-target="#deleteSoldierModal-<?php echo $soldier['SOLDIERID']; ?>">
                                                    <i class="fas fa-trash"></i> &nbsp;&nbsp;&nbsp;&nbsp; Delete
                                                </button>
                                            </div>

                                        </div>

                                        <!-- Delete Button -->

                                        <!-- Delete Soldier Modal -->
                                        <div class="modal fade" id="deleteSoldierModal-<?php echo $soldier['SOLDIERID']; ?>"
                                            tabindex="-1" role="dialog"
                                            aria-labelledby="deleteSoldierModalLabel-<?php echo $soldier['SOLDIERID']; ?>"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="deleteSoldierModalLabel-<?php echo $soldier['SOLDIERID']; ?>">
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
                                                                value="<?php echo $soldier['SOLDIERID']; ?>">
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
                                <?php } ?>
                            </tr>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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