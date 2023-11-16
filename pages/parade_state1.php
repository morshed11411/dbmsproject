<?php
include '../includes/parade_controller.php';
include '../includes/disposal_controller.php';
include '../includes/header.php';

// Function to get soldiers by company and status
$allSoldiers = getSoldiers($conn, null, null, null, false, null, 'Posted');

// Function to get all company data
$company = getAllCompanyData($conn);

// Array of disposal types
$disposalTypes = ['PPG', 'PPGF', 'ATNC', 'SIQ', 'CMH'];

// Associative arrays to store disposal counts
$disposalCount = [];
$total = [];

// Loop through disposal types and companies to get counts
foreach ($disposalTypes as $disposal) {
    $total[$disposal] = 0;

    foreach ($company as $coy) {
        $disposalHolder = medicalDisposal($conn, $coy['ID'], null, $disposal, null);

        $disposalCount[$disposal][$coy['ID']] = $disposalHolder['total'];
        $total[$disposal] += $disposalHolder['total'];
    }
}

// Output the HTML
?>
<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Parade State</h3>
        </div>
        <div class="text-right">
        </div>
    </div>
</div>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Parade State</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="maximize">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered">
            <tr>
                <td>DETAILS</td>
                <?php foreach ($company as $coy): ?>
                    <td><?= $coy['NAME'] ?></td>
                <?php endforeach; ?>
                <td>TOTAL</td>
            </tr>

            <?php foreach ($disposalTypes as $disposal): ?>
                <tr>
                    <td><?= $disposal ?></td>
                    <?php
                    $total = 0;
                    foreach ($company as $coy):
                        $count = $disposalCount[$disposal][$coy['ID']];
                        $total += $count;
                        ?>
                        <td><?= $count ?></td>
                    <?php endforeach; ?>
                    <td><?= $total ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
