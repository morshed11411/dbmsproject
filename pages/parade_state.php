<?php
include '../includes/disposal_controller.php';
include '../includes/leave_controller.php';
include '../includes/parade_controller.php';


// Get date to filter
$currentDate = null;
if (isset($_POST['filterBtn'])) {
    $currentDate = $_POST['currentDate'] ?? null;
    $formattedDate = date('j F, Y', strtotime($currentDate));

    $_SESSION['success'] = 'Showing parade state for: ' . $formattedDate;

}


// Function to get all company data
$company = getAllCompanyData($conn);

// Array of disposal types
$disposalTypes = getDisposalTypes($conn);

$leaveTypes = getLeaveTypes($conn);


// Associative arrays to store disposal counts
$disposalHolderList = [];
$onLeaveSolderList = [];


// Loop through disposal types and companies to get counts
foreach ($disposalTypes as $disposal) {
    $total[$disposal] = 0;
    foreach ($company as $coy) {
        $disposalHolder = medicalDisposal($conn, $coy['ID'], $currentDate, $disposal, null);
        $disposalHolderList[$disposal][$coy['ID']] = $disposalHolder;
    }
}
// Loop through leavetypes and companies to get counts
foreach ($leaveTypes as $leaveType) {
    $total[$leaveType] = 0;
    foreach ($company as $coy) {
        // Use getLeaveInfo instead of medicalDisposal for onLeaveSolderList
        $soldierOnLeave = getLeaveInfo($conn, $coy['ID'], $currentDate, $leaveType, null);
        $onLeaveSolderList[$leaveType][$coy['ID']] = $soldierOnLeave;
    }
}

foreach ($company as $coy) {
    $solderByCoy = getSoldiers($conn, null, null, null, false, $coy['ID'], null);

    $byCoySoldiderList[$coy['ID']] = $solderByCoy;
}


include '../includes/header.php';
?>
<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Parade State</h3>
            <?php

            ?>
        </div>
        <div class="text-right">
            <form method="post" action="">
                <div class="form-row align-items-center">
                    <div class="col-auto">
                        <label class="sr-only" for="currentDate">Current Date</label>
                        <input type="date" class="form-control" id="currentDate" name="currentDate"
                            value="<?= $currentDate = $_POST['currentDate'] ?? date('Y-m-d'); ?>">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary" name="filterBtn">Filter</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">


        <?php include '../includes/alert.php';
        ?>

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
                <table id="" class="table  table-bordered" style="border: 1px solid black;">
                    <tr>
                        <th style=" width: 150px;">
                            DETAILS
                        </th>
                        <?php foreach ($company as $coy): ?>
                            <th style=" width: 100px; text-align: center;">
                                <?= $coy['NAME'] ?>
                            </th>
                        <?php endforeach; ?>
                        <th style=" width: 100px; text-align: center;">
                            TOTAL
                        </th>
                    </tr>
                    <!-- Authorization Section -->

                    <tr>
                        <td>
                            Authorized
                        </td>
                        <?php
                        $totalAuth = 0;
                        foreach ($company as $coy): ?>
                            <td style="text-align: center;">
                                <?php
                                $totalAuth += $manpowerData[$coy['ID']];
                                echo $manpowerData[$coy['ID']];
                                ?>

                            </td>
                        <?php endforeach; ?>
                        <td style="text-align: center;">
                            <?= $totalAuth ?>
                        </td>
                    </tr>
                    <!-- Held Section -->
                    <tr>
                        <td>
                            Held
                        </td>
                        <?php
                        $heldTotal = [];
                        foreach ($company as $coy): ?>
                            <td style="text-align: center;">
                                <?php
                                $byCoyHeldlist = $byCoySoldiderList[$coy['ID']];
                                $modalId = $coy['ID'] . '-' . 'held';
                                $modalName = $coy['NAME'] . '-' . 'Total Held' . ' ';
                                echo count($byCoyHeldlist);
                                //printAllSoldierList($byCoyHeldlist, $modalId, $modalName);
                                $heldTotal = array_merge($heldTotal, $byCoyHeldlist);

                                ?>

                            </td>
                        <?php endforeach; ?>
                        <td style="text-align: center;">
                            <?php
                            echo count($byCoyHeldlist);

                            // printAllSoldierList($heldTotal, 'held', 'Total Held');
                            ?>
                        </td>
                    </tr>
                    <!-- Disposal Section -->

                    <?php foreach ($disposalTypes as $disposal): ?>
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
                                $rowTotal = array_merge($rowTotal, $dispList);
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

                    <?php foreach ($leaveTypes as $leaveType): ?>
                        <tr>
                            <td>
                                <?= $leaveType ?>
                            </td>
                            <?php
                            $rowTotalLeave = [];
                            foreach ($company as $coy):
                                echo '<td style="text-align: center;">';
                                $leaveList = $onLeaveSolderList[$leaveType][$coy['ID']];
                                $modalId = $coy['ID'] . '-' . $leaveType;
                                $modalName = $coy['NAME'] . ' ' . $leaveType . ' ';
                                printLeaveSoldierList($leaveList, $modalId, $modalName);
                                $rowTotalLeave = array_merge($rowTotalLeave, $leaveList);
                                echo '</td>';
                            endforeach;
                            ?>

                            <td style="text-align: center;">
                                <?php
                                printLeaveSoldierList($rowTotalLeave, $leaveType, 'Total ' . $leaveType);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Grand Total Section -->
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
                                $coyTotal = array_merge($coyTotal, $dispList);
                            endforeach;

                            foreach ($leaveTypes as $leaveType):
                                $leaveList = $onLeaveSolderList[$leaveType][$coy['ID']];
                                $coyTotal = array_merge($coyTotal, $leaveList);
                            endforeach;

                            printAllSoldierList($coyTotal, $coy['ID'], $coy['NAME'] . ' Company Total ');
                            $grandTotal = array_merge($grandTotal, $coyTotal);
                            echo '</td>';
                        endforeach;
                        ?>

                        <td style="text-align: center;">
                            <?php
                            printAllSoldierList($grandTotal, 'Grand', 'Grand Total');
                            ?>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>