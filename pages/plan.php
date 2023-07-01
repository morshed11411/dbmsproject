<?php
session_start();
include '../includes/connection.php';

// Check if the form is submitted
if (isset($_POST['submit'])) {
    $companyID = $_POST['company'];
    $cycle = $_POST['cycle'];

    // Retrieve the selected company name
    $queryCompany = "SELECT COMPANYNAME FROM COMPANY WHERE COMPANYID = :companyID";
    $stmtCompany = oci_parse($conn, $queryCompany);
    oci_bind_by_name($stmtCompany, ':companyID', $companyID);
    oci_execute($stmtCompany);
    $row = oci_fetch_assoc($stmtCompany);
    $selectedCompanyName = $row['COMPANYNAME'];
    oci_free_statement($stmtCompany);

    // Retrieve the career plan for the selected company and cycle
    $queryPlan = "SELECT s.SOLDIERID, s.NAME, r.RANK, t.TRADE, cp.FIRSTCYCLE, cp.SECONDCYCLE, cp.THIRDCYCLE, cp.FOURTHCYCLE
                  FROM SOLDIER s
                  JOIN RANKS r ON s.RANKID = r.RANKID
                  JOIN TRADE t ON s.TRADEID = t.TRADEID
                  LEFT JOIN CarrierPlan cp ON s.SOLDIERID = cp.SOLDIERID
                  WHERE s.COMPANYID = :companyID
                  AND r.RANK IN ('Snk', 'Lcpl', 'Cpl', 'Sgt', 'WO', 'SWO')";

    $stmtPlan = oci_parse($conn, $queryPlan);
    oci_bind_by_name($stmtPlan, ':companyID', $companyID);
    oci_bind_by_name($stmtPlan, ':cycle', $cycle);
    oci_execute($stmtPlan);
}

$queryCompanies = "SELECT * FROM COMPANY";
$stmtCompanies = oci_parse($conn, $queryCompanies);
oci_execute($stmtCompanies);

oci_close($conn);

include '../includes/header.php';
?>

<!-- Select Company and Cycle Modal -->
<div class="modal fade" id="selectModal" tabindex="-1" role="dialog" aria-labelledby="selectModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="selectModalLabel">Select Company</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="company">Company:</label>
                        <select class="form-control" id="company" name="company" required>
                            <?php while ($company = oci_fetch_assoc($stmtCompanies)): ?>
                                <option value="<?php echo $company['COMPANYID']; ?>"><?php echo $company['COMPANYNAME']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit">View Career Plan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>View Career Plan -
                <?php echo $selectedCompanyName; ?>
            </h3>
        </div>
        <div class="text-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#selectModal"> <h5>Select Company</h5>
            </button>
        </div>
    </div>
</div>

<?php if (isset($selectedCompanyName)): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="tablex" class="table table-bordered" >
                        <thead>
                            <tr>
                                <th>Soldier ID</th>
                                <th>Rank</th>
                                <th>Trade</th>
                                <th>Name</th>
                                <th>First Cycle</th>
                                <th>Second Cycle</th>
                                <th>Third Cycle</th>
                                <th>Fourth Cycle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($plan = oci_fetch_assoc($stmtPlan)): ?>
                                <tr>
                                    <td>
                                        <?php echo $plan['SOLDIERID']; ?>
                                    </td>
                                    <td>
                                        <?php echo $plan['RANK']; ?>
                                    </td>
                                    <td>
                                        <?php echo $plan['TRADE']; ?>
                                    </td>
                                    <td>
                                        <?php echo $plan['NAME']; ?>
                                    </td>
                                    <td>
                                        <?php echo $plan['FIRSTCYCLE']; ?>
                                    </td>
                                    <td>
                                        <?php echo $plan['SECONDCYCLE']; ?>
                                    </td>
                                    <td>
                                        <?php echo $plan['THIRDCYCLE']; ?>
                                    </td>
                                    <td>
                                        <?php echo $plan['FOURTHCYCLE']; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php
oci_free_statement($stmtCompanies);
include '../includes/footer.php';
?>