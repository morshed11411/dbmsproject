<?php
include 'conn.php';
include 'views/auth.php';
// Retrieve soldier information
$soldierId = $_GET['soldierId'];
$query = "SELECT s.SoldierID, s.Name, s.MaritalStatus, s.BloodGroup, s.Weight, s.Height, s.Religion,
              s.Age, s.DateOfBirth, s.Gender, s.LivingStatus, s.Village, s.Thana, s.District,
              s.DateOfEnroll, s.TemporaryCommand, s.ERE, s.ServingStatus, t.Trade, r.Rank, c.CompanyName
              FROM Soldier s
              INNER JOIN Trade t ON s.TradeID = t.TradeID
              INNER JOIN Ranks r ON s.RankID = r.RankID
              INNER JOIN Company c ON s.CompanyID = c.CompanyID
              WHERE s.SoldierID = :soldier_id";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierId);
oci_execute($stmt);
$soldier = oci_fetch_assoc($stmt);
oci_free_statement($stmt);

oci_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h2>Soldier Profile</h2>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <?php if ($soldier): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="basicInfoTab" data-toggle="pill"
                                                    href="#basicInfo" role="tab" aria-controls="basicInfo"
                                                    aria-selected="true">Basic Info</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="trainingInfoTab" data-toggle="pill"
                                                    href="#trainingInfo" role="tab" aria-controls="trainingInfo"
                                                    aria-selected="false">Training Info</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="medicalInfoTab" data-toggle="pill"
                                                    href="#medicalInfo" role="tab" aria-controls="medicalInfo"
                                                    aria-selected="false">Medical Info</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="careerPlanInfoTab" data-toggle="pill"
                                                    href="#careerPlanInfo" role="tab" aria-controls="careerPlanInfo"
                                                    aria-selected="false">Career Plan Info</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="leaveHistoryTab" data-toggle="pill"
                                                    href="#leaveHistory" role="tab" aria-controls="leaveHistory"
                                                    aria-selected="false">Leave History</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="profileTabsContent">
                                            <!-- Basic Info Tab -->
                                            <div class="tab-pane fade show active" id="basicInfo" role="tabpanel"
                                                aria-labelledby="basicInfoTab">
                                                <h5 class="mb-4">Basic Info</h5> <!-- Updated code -->
                                                <table class="table">
                                                    <tr>
                                                        <th>Soldier ID:</th>
                                                        <td>
                                                            <?php echo $soldier['SOLDIERID']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Name:</th>
                                                        <td>
                                                            <?php echo $soldier['NAME']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Marital Status:</th>
                                                        <td>
                                                            <?php echo $soldier['MARITALSTATUS']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Blood Group:</th>
                                                        <td>
                                                            <?php echo $soldier['BLOODGROUP']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Weight:</th>
                                                        <td>
                                                            <?php echo $soldier['WEIGHT']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Height:</th>
                                                        <td>
                                                            <?php echo $soldier['HEIGHT']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Religion:</th>
                                                        <td>
                                                            <?php echo $soldier['RELIGION']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Age:</th>
                                                        <td>
                                                            <?php echo $soldier['AGE']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Date of Birth:</th>
                                                        <td>
                                                            <?php echo $soldier['DATEOFBIRTH']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Gender:</th>
                                                        <td>
                                                            <?php echo $soldier['GENDER']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Living Status:</th>
                                                        <td>
                                                            <?php echo $soldier['LIVINGSTATUS']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Village:</th>
                                                        <td>
                                                            <?php echo $soldier['VILLAGE']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Thana:</th>
                                                        <td>
                                                            <?php echo $soldier['THANA']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>District:</th>
                                                        <td>
                                                            <?php echo $soldier['DISTRICT']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Date of Enroll:</th>
                                                        <td>
                                                            <?php echo $soldier['DATEOFENROLL']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Temporary Command:</th>
                                                        <td>
                                                            <?php echo $soldier['TEMPORARYCOMMAND']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>ERE:</th>
                                                        <td>
                                                            <?php echo $soldier['ERE']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Serving Status:</th>
                                                        <td>
                                                            <?php echo $soldier['SERVINGSTATUS']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Trade:</th>
                                                        <td>
                                                            <?php echo $soldier['TRADE']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Rank:</th>
                                                        <td>
                                                            <?php echo $soldier['RANK']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Company:</th>
                                                        <td>
                                                            <?php echo $soldier['COMPANYNAME']; ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <!-- Training Info Tab -->
                                            <div class="tab-pane fade" id="trainingInfo" role="tabpanel"
                                                aria-labelledby="trainingInfoTab">
                                                <h5>Training Info</h5>
                                                <!-- Add training info content here -->
                                            </div>

                                            <!-- Medical Info Tab -->
                                            <div class="tab-pane fade" id="medicalInfo" role="tabpanel"
                                                aria-labelledby="medicalInfoTab">
                                                <h5>Medical Info</h5>
                                                <!-- Add medical info content here -->
                                            </div>

                                            <!-- Career Plan Info Tab -->
                                            <div class="tab-pane fade" id="careerPlanInfo" role="tabpanel"
                                                aria-labelledby="careerPlanInfoTab">
                                                <h5>Career Plan Info</h5>
                                                <!-- Add career plan info content here -->
                                            </div>

                                            <!-- Leave History Tab -->
                                            <div class="tab-pane fade" id="leaveHistory" role="tabpanel"
                                                aria-labelledby="leaveHistoryTab">
                                                <h5>Leave History</h5>
                                                <!-- Add leave history content here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">Soldier not found.</div>
                    <?php endif; ?>
                </div>
            </section>


        </div>
        <?php include 'views/footer.php'; ?>
    </div>

    <script>
        $(document).ready(function () {
            $('#profileTabs a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
</body>

</html>