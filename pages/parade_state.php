<?php
include '../includes/disposal_controller.php';

// Get date to filter
$currentDate = null;
if (isset($_POST['filterBtn'])) {
    $currentDate = $_POST['currentDate'] ?? null;
}
// Function to get all company data
$company = getAllCompanyData($conn);

// Array of disposal types
$disposalTypes = getDisposalTypes($conn);


// Associative arrays to store disposal counts
$disposalHolderList = [];


// Loop through disposal types and companies to get counts
foreach ($disposalTypes as $disposal) {
    $total[$disposal] = 0;
    foreach ($company as $coy) {
        $disposalHolder = medicalDisposal($conn, $coy['ID'], $currentDate, $disposal, null);
        $disposalHolderList[$disposal][$coy['ID']] = $disposalHolder;
    }
}

include '../includes/header.php';
?>
<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Parade State</h3>
        </div>
        <div class="text-right">
            <form method="post" action="">
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <label class="sr-only" for="currentDate">Current Date</label>
                        <input type="date" class="form-control" id="currentDate" name="currentDate"
                            value="<?= date('Y-m-d'); ?>">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary" name="filterBtn">Filter</button>
                    </div>
                </div>
            </form>

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
        <table id="" class="table  table-bordered">
            <tr>
                <th style=" width: 150px;">
                    DETAILS</th>
                <?php foreach ($company as $coy): ?>
                    <th style=" width: 100px; text-align: center;">
                        <?= $coy['NAME'] ?>
                    </th>
                <?php endforeach; ?>
                <th style=" width: 100px; text-align: center;">
                    TOTAL</th>
            </tr>

            <?php
            foreach ($disposalTypes as $disposal): ?>
                <tr>
                    <td>
                        <?= $disposal ?>
                    </td>
                    <?php
                    $rowTotal = [];
                    foreach ($company as $coy):
                        echo '<td style="text-align: center;">';
                        $dispList = $disposalHolderList[$disposal][$coy['ID']];
                        $modalId = $coy['ID'] . '-' . $disposal;
                        $modalName = $coy['NAME'] . ' ' . $disposal . ' ';
                        printSoldierList($dispList, $modalId, $modalName);
                        $rowTotal = array_merge($rowTotal, $dispList); // Use array_merge to combine arrays
                        echo '</td>';
                    endforeach;
                    ?>

                    <td style="text-align: center;">
                        <?php
                        printSoldierList($rowTotal, $disposal, 'Total ' . $disposal);
                        ?>
                    </td>

                </tr>
            <?php endforeach; ?>

            <tr>
                <td>
                    Grand Total
                </td>

                <?php
                $grandTotal = [];
                foreach ($company as $coy):
                    $coyTotal = [];
                    echo '<td style="text-align: center;">';
                    foreach ($disposalTypes as $disposal):
                        $dispList = $disposalHolderList[$disposal][$coy['ID']];
                        $coyTotal = array_merge($coyTotal, $dispList); // Assuming you want to count the total soldiers for each disposal type
                    endforeach;
                    printSoldierList($coyTotal, $coy['ID'], $coy['NAME'] . ' Company Disposal ');
                    $grandTotal = array_merge($grandTotal, $coyTotal);
                    echo '</td>';
                endforeach;
                ?>

                <td style="text-align: center;">
                    <?php
                    printSoldierList($grandTotal, 'Grand', 'Grand Total');
                    ?>
                </td>
            </tr>

        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>