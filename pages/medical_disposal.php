<?php
// Include necessary files
include '../includes/connection.php';
include '../includes/disposal_controller.php';
include '../includes/header.php';

// Get disposal types
$dispType = getDisposalTypes($conn);
$disposalDetails=[];
// Process form submission
if (isset($_POST['filterBtn'])) {
    // Get form values
    $coyId = $_POST['coyId'] ?? null;
    $disposalType = $_POST['edit_disposal_type'] ?? null;
    $currentDate = $_POST['currentDate'] ?? null;

    // Call the medicalDisposal function
    $disposalDetails = medicalDisposal($conn, $coyId, $currentDate, $disposalType);
}

?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?= htmlspecialchars("Disposal List") ?></h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Filter Disposal List</h3>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <!-- Company Filter -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Company:</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="coyId">
                                <option value="">All Companies</option>
                                <?php foreach (getAllCompanyData($conn) as $company): ?>
                                    <option value="<?= $company['ID'] ?>"><?= htmlspecialchars($company['NAME']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Disposal Type Filter -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Disposal Type:</label>
                        <div class="col-sm-4">
                            <select name="edit_disposal_type" id="edit_disposal_type" class="form-control" required>
                                <?php foreach ($dispType as $disposalId => $disposalType): ?>
                                    <?php $selected = ($disposalId == $disposal['DISPOSALID']) ? 'selected' : ''; ?>
                                    <option value="<?= $disposalType ?>" <?= $selected ?>><?= $disposalType ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Date Filter -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Date:</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="currentDate">
                        </div>
                    </div>

                    <!-- Filter Button -->
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary" name="filterBtn">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Display Disposal Holders -->
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        Disposal Holders
                    </div>
                    <div class="card-body">
                        <?php if (count($disposalDetails) > 0): ?>
                            <form method="POST" action="">
                                <div class="card-body table-responsive p-0" style="height: 400px;">
                                    <table id="tablem" class="table table-bordered table-head-fixed text-nowrap">
                                        <thead>
                                            <tr>
                                                <th style="width: 80px;">Soldier ID</th>
                                                <th style="width: 80px;">Rank</th>
                                                <th style="width: 120px;">Name</th>
                                                <th style="width: 120px;">Trade</th>
                                                <th style="width: 120px;">Disposal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($disposalDetails as $soldier): ?>
                                                <tr>
                                                    <td><?= $soldier['SOLDIERID']; ?></td>
                                                    <td><?= $soldier['RANK']; ?></td>
                                                    <td><?= $soldier['NAME']; ?></td>
                                                    <td><?= $soldier['TRADE']; ?></td>
                                                    <td><?= $soldier['REMARKS']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        <?php else: ?>
                            <p class="text-center">No soldiers found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
