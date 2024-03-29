<?php

include '../includes/connection.php';
include '../includes/leave_controller.php';
include '../includes/disposal_controller.php';

// Check if the soldier ID is present in the session or URL parameter
if (isset($_SESSION['userid'])) {
    if (isset($_GET['soldierid'])) {
        $soldierId = $_GET['soldierid'];
    } else {
        $soldierId = $_SESSION['userid'];
    }
}

// Perform database query to fetch the soldier details
$query = "SELECT * FROM soldier_view WHERE SOLDIERID = :soldierId";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldierId', $soldierId);
oci_execute($stmt);


// Check if the soldier record is found
if ($row = oci_fetch_assoc($stmt)) {
    $soldier = $row;

    // Fetch punishment history for the soldier
    $query = "SELECT * FROM PUNISHMENT WHERE SOLDIERID = :soldierId";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldierId', $soldierId);
    oci_execute($stmt);

    $punishmentList = array();
    while ($row = oci_fetch_assoc($stmt)) {
        $punishment = new stdClass();
        $punishment->PunishmentID = $row['PUNISHMENTID'];
        $punishment->Punishment = $row['PUNISHMENT'];
        $punishment->Reason = $row['REASON'];
        $punishment->PunishmentDate = $row['PUNISHMENTDATE'];
        $punishmentList[] = $punishment;
    }
    oci_free_statement($stmt);


    //fetch medical disposal info
    $query = "SELECT * FROM MEDICALINFO WHERE SOLDIERID = :soldierId";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldierId', $soldierId);
    oci_execute($stmt);

    $disposalList = array();
    while ($row = oci_fetch_assoc($stmt)) {
        $disposal = new stdClass();
        $disposal->DisposalID = $row['MEDICALID'];
        $disposal->DisposalType = $row['DISPOSALID'];
        $disposal->StartDate = $row['STARTDATE'];
        $disposal->EndDate = $row['ENDDATE'];
        $disposal->Reason = $row['REASON'];
        $disposalList[] = $disposal;
    }

    oci_free_statement($stmt);

    // Fetch leave disposal of the soldier
    $dispType = getDisposalTypes($conn);
    $disposalList = medicalDisposal($conn, null, 'all', null, $soldierId);
    $totalDisposal = calculateDisposalCount($conn, $dispType, $soldierId);

    // Fetch leave history of the soldier
    $leaveHistory = getLeaveInfo($conn, null, 'all', null, $soldierId, 'Expired');
    if (isset($leaveHistory[0]))
        $lastLeave = $leaveHistory[0];
    $totalDays = calculateLeaveCount($conn, $leaveTypes, $soldierId);




    $query = "SELECT cp.*
    FROM SOLDIER s
    LEFT JOIN carrierplan cp ON s.SOLDIERID = cp.SOLDIERID
    WHERE s.SOLDIERID = :soldierId";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldierId', $soldierId);
    oci_execute($stmt);

    $careerPlan = array();

    while ($row = oci_fetch_assoc($stmt)) {
        $careerPlan[] = $row;
    }

    oci_free_statement($stmt);

    include '../includes/header.php';

    ?>

    <div class="card-body">
        <div class="d-flex justify-content-between">
            <div class="text-left">
                <h3>Soldier Profile </h3>

            </div>
            <?php if ($_SESSION['role'] == 'admin') { ?>
                <div class="text-right">
                    <a href="edit_soldier.php?soldier=<?php echo $soldier['SOLDIERID']; ?>" class="btn btn-primary">Edit
                        Profile</a>
                <?php } ?>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php include '../includes/alert.php'; ?>

            <div class="row">
                <div class="col-md-3">
                    <div class="card card-primary">
                        <div class="card-body box-profile mt-2">
                            <div class="text-center">

                                <a href="uploadimage.php?soldier=<?php echo $soldier['SOLDIERID']; ?>">
                                    <?php
                                    $profilePicture = $soldier['PROFILEPICTURE'];
                                    if (!empty($profilePicture)) {
                                        ?>
                                        <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="img-thumbnail"
                                            style="max-height: 370px; width: auto;">
                                        <?php
                                    } else {
                                        ?>
                                        <img src="../images/default_profile_picture.png" alt="Profile Picture"
                                            class="img-thumbnail" style="max-height: 370px; width: auto;">
                                        <?php
                                    }
                                    ?>
                                </a>
                            </div>
                            <h3 class="profile-username text-center">
                                <?= $soldier['SOLDIERID'] ?>
                            </h3>
                            <h3 class="profile-username text-center">
                                <?= $soldier['RANK'] . ' ' . $soldier['NAME'] ?>
                            </h3>

                            <p class="text-muted text-center">
                                <?= $soldier['COMPANYNAME'] ?>
                            </p>
                            <p class="text-muted text-center">
                                Joined from leave
                                <?php
                                $lastLeave = $soldier['LASTLEAVE'];
                                if ($lastLeave < 30) {
                                    $lastLeave = $lastLeave . ' days';
                                } else {
                                    $lastLeave = round($lastLeave / 30) . ' month , ' . $lastLeave % 30 . ' days ';
                                }
                                echo $lastLeave;
                                ?> ago.
                            </p>
                            <div class="text-center"> <a href="uploadimage.php?soldier=<?php echo $soldier['SOLDIERID']; ?>"
                                    class="btn btn-success">Image</a>
                                <a href="uploadsignature.php?soldier=<?php echo $soldier['SOLDIERID']; ?>"
                                    class="btn btn-warning">Signature</a>
                            </div>

                        </div>

                    </div>

                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="basicInfoTab" data-toggle="pill" href="#basicInfo"
                                        role="tab" aria-controls="basicInfo" aria-selected="true">Basic Info</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="trainingInfoTab" data-toggle="pill" href="#trainingInfo"
                                        role="tab" aria-controls="trainingInfo" aria-selected="false">Punishment
                                        History
                                    </a>

                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="medicalInfoTab" data-toggle="pill" href="#medicalInfo"
                                        role="tab" aria-controls="medicalInfo" aria-selected="false">Medical Disposal</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="careerPlanInfoTab" data-toggle="pill" href="#careerPlanInfo"
                                        role="tab" aria-controls="careerPlanInfo" aria-selected="false">Career Plan Info</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="leaveHistoryTab" data-toggle="pill" href="#leaveHistory"
                                        role="tab" aria-controls="leaveHistory" aria-selected="false">Leave
                                        History</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="profileTabsContent">
                                <!-- Basic Info Tab -->
                                <div class="tab-pane fade show active" id="basicInfo" role="tabpanel"
                                    aria-labelledby="basicInfoTab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table">
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
                                                    <th>Gender:</th>
                                                    <td>
                                                        <?php echo $soldier['GENDER']; ?>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th>Age:</th>
                                                    <td>
                                                        <?php echo $soldier['AGE']; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Service Age:</th>
                                                    <td>
                                                        <?php echo $soldier['SERVICEAGE']; ?>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th>Religion:</th>
                                                    <td>
                                                        <?php echo $soldier['RELIGION']; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Date of Birth:</th>
                                                    <td>
                                                        <?php echo $soldier['DATEOFBIRTH']; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Date of Enroll:</th>
                                                    <td>
                                                        <?php echo $soldier['DATEOFENROLL']; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Medical Category:</th>
                                                    <td>
                                                        Cat A
                                                        <!-- <?php echo $soldier['MEDCATEGORY']; ?> -->
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
                                        <div class="col-md-6">
                                            <table class="table">
                                                <tr>
                                                    <th>Personal Contact:</th>
                                                    <td>
                                                        <?php echo '+880' . $soldier['PERSONALCONTACT']; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Emergency Contact:</th>
                                                    <td>
                                                        <?php echo '+880' . $soldier['EMERGENCYCONTACT']; ?>
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
                                                    <th>District:</th>
                                                    <td>
                                                        <?php echo $soldier['DISTRICT']; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Marital Status:</th>
                                                    <td>
                                                        <?php echo $soldier['MARITALSTATUS']; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Living Status:</th>
                                                    <td>
                                                        <?php echo $soldier['LIVINGSTATUS']; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Mission Status:</th>
                                                    <td>
                                                        <?php echo $soldier['MISSION']; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Parent Unit:</th>
                                                    <td>
                                                        <?php echo $soldier['PARENTUNIT']; ?>
                                                    </td>
                                                </tr>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- Punishment History Tab -->
                                <div class="tab-pane fade" id="trainingInfo" role="tabpanel"
                                    aria-labelledby="trainingInfoTab">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Ser</th>
                                                <th>Punishment</th>
                                                <th>Reason</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 1;
                                            foreach ($punishmentList as $punishment): ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $i++; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $punishment->Punishment; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $punishment->Reason; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $punishment->PunishmentDate; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Medical Disposal Tab -->
                                <div class="tab-pane fade" id="medicalInfo" role="tabpanel"
                                    aria-labelledby="medicalInfoTab">
                                    <div class='card'>
                                        <div class='card-header'>
                                            <h5 class='card-title'>Medical Disposal Summary</h5>
                                        </div>
                                        <div class='card-body'>
                                            <table class='table table-bordered'>
                                                <thead>
                                                    <tr>
                                                        <?php foreach ($dispType as $disp): ?>
                                                            <th class='text-center'>
                                                                <?php echo $disp; ?>
                                                            </th>
                                                        <?php endforeach; ?>
                                                        <th class='text-center'>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <?php foreach ($dispType as $disp): ?>
                                                            <td class='text-center'>
                                                                <?php echo $totalDisposal[$disp]; ?> Days
                                                            </td>
                                                        <?php endforeach; ?>
                                                        <td class='text-center'>
                                                            <?php echo array_sum($totalDisposal); ?> Days
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class='card'>

                                        <div class='card-header'>
                                            <h5 class='card-title'>Medical Diposal History</h5>
                                        </div>
                                        <div class='card-body'>
                                            <div class="table-responsive">

                                                <table class='table table-bordered' >
                                                    <thead>
                                                        <tr>
                                                            <th>Ser</th>
                                                            <th>Type</th>
                                                            <th>Duration</th>
                                                            <th>Start Date</th>
                                                            <th>End Date</th>
                                                            <th>Reason</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $i = 1;
                                                        foreach ($disposalList as $disposal):
                                                            $startDate = new DateTime($disposal['STARTDATE']);
                                                            $endDate = new DateTime($disposal['ENDDATE']);
                                                            $duration = $startDate->diff($endDate)->format("%a days");

                                                            if ($duration == "0 ") {
                                                                $duration = "1 ";
                                                            } else {
                                                                $duration .= " ";
                                                            }

                                                            echo '<tr>';
                                                            echo '<td>' . $i++ . '</td>';
                                                            echo '<td>' . $disposal['DISPOSALTYPE'] . '</td>';
                                                            echo '<td>' . $duration . '</td>';
                                                            echo '<td>' . $disposal['STARTDATE'] . '</td>';
                                                            echo '<td>' . $disposal['ENDDATE'] . '</td>';
                                                            echo '<td>' . $disposal['REMARKS'] . '</td>';
                                                            echo '</tr>';


                                                            ?>

                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Career Plan Info Tab -->
                                <div class="tab-pane fade" id="careerPlanInfo" role="tabpanel"
                                    aria-labelledby="careerPlanInfoTab">

                                    <!-- Add career plan info content here -->
                                    <?php
                                    if (count($careerPlan) > 0) {
                                        // Career plan information exists
                                        $soldier = $careerPlan[0];
                                        ?>
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="mb-4">Career Plan Info</h5>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <h6>First Cycle</h6>
                                                                    <p>
                                                                    <h4>
                                                                        <?php echo $soldier['FIRSTCYCLE']; ?>
                                                                    </h4>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <h6>Second Cycle</h6>
                                                                    <p>
                                                                    <h4>
                                                                        <?php echo $soldier['SECONDCYCLE']; ?>
                                                                    </h4>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <h6>Third Cycle</h6>
                                                                    <p>
                                                                    <h4>
                                                                        <?php echo $soldier['THIRDCYCLE']; ?>
                                                                    </h4>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <h6>Fourth Cycle</h6>
                                                                    <p>
                                                                    <h4>
                                                                        <?php echo $soldier['FOURTHCYCLE']; ?>
                                                                    </h4>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    } else {
                                        // No career plan found
                                        echo '<div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="alert alert-warning" role="alert">
                                                            No career plan found.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
                                    }
                                    ?>

                                </div>

                                <!-- Leave History Tab -->
                                <div class="tab-pane fade" id="leaveHistory" role="tabpanel"
                                    aria-labelledby="leaveHistoryTab">
                                    <div class='card'>
                                        <div class='card-header'>
                                            <h5 class='card-title'>Leave Summary</h5>
                                        </div>
                                        <div class='card-body'>
                                            <table class='table table-bordered'>
                                                <thead>
                                                    <tr>
                                                        <?php foreach ($leaveTypes as $leaveType): ?>
                                                            <th class='text-center'>
                                                                <?php echo $leaveType; ?>
                                                            </th>
                                                        <?php endforeach; ?>
                                                        <th class='text-center'>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <?php foreach ($leaveTypes as $leaveType): ?>
                                                            <td class='text-center'>
                                                                <?php echo $totalDays[$leaveType]; ?> Days
                                                            </td>
                                                        <?php endforeach; ?>
                                                        <td class='text-center'>
                                                            <?php echo array_sum($totalDays); ?> Days
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class='card'>
                                        <div class='card-header'>
                                            <h5 class='card-title'>Leave History</h5>
                                        </div>
                                        <div class='card-body'>
                                            <div class="table-responsive">

                                                <table class='table table-bordered' >
                                                    <thead>
                                                        <tr>
                                                            <th>Ser</th>
                                                            <th>Type</th>
                                                            <th>Duration</th>
                                                            <th>Start Date</th>
                                                            <th>End Date</th>
                                                            <th>Leave Card</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $i = 1;
                                                        foreach ($leaveHistory as $leave): ?>
                                                            <tr>
                                                                <td>
                                                                    <?php echo $i++; ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $leave['LEAVETYPE']; ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    $startDate = new DateTime($leave['LEAVESTARTDATE']);
                                                                    $endDate = new DateTime($leave['LEAVEENDDATE']);
                                                                    $duration = $startDate->diff($endDate)->format("%a days");

                                                                    if ($duration == "0 days") {
                                                                        $duration = "1 day";
                                                                    } else {
                                                                        $duration .= " days";
                                                                    }

                                                                    echo $duration;

                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $leave['LEAVESTARTDATE']; ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo $leave['LEAVEENDDATE']; ?>
                                                                </td>
                                                                <td>
                                                                    <?php
                                                                    echo '<a href="leavecard.php?leaveid=' . $leave['LEAVEID'] . '" class="btn btn-light" target="_blank">Leave Card</a>';
                                                                    ?>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </div>

    <?php
} else {
    $_SESSION['error'] = 'Please log in first.';
    header('Location: soldiers.php');
    exit;
}

oci_free_statement($stmt);
oci_close($conn);
include '../includes/footer.php';
?>