<?php
session_start();

include '../includes/connection.php';
include '../includes/parade_controller.php';


$role = $_SESSION['role'];
$userCoy = $_SESSION['usercoy'];
$soldierList = [];
$byRankCount = [];


if ($role === 'admin') {
    $soldierList = getSoldiers($conn, null, null, null, false, null, null);
} elseif ($role === 'manager') {
    $soldierList = getSoldiers($conn, null, null, null, false, $userCoy, null);
}


if ($role === 'admin') {

    foreach ($ranks as $rank) {
        $soldiersByRank = getSoldiers($conn, null, $rank['NAME'], null, false, null, null);

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


<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>আমার কোম্পানি</h3>
        </div>

    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php include '../includes/alert.php'; ?>
        <?php include 'soldiers_card.php'; ?>

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">পদবী অনুসারে জনবল</h3>
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