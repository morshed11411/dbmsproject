<?php


session_start();

include '../includes/connection.php';

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get the form data
    $soldier_id = $_POST['soldier_id'];
    $name = $_POST['name'];
    $rank = $_POST['rank_id'];
    $trade = $_POST['trade_id'];
    $company = $_POST['company_id'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $date_of_birth = $_POST['date_of_birth'];
    $date_of_joining = $_POST['date_of_joining'];
    $blood_group = $_POST['blood_group'];
    $marital_status = $_POST['marital_status'];
    $village = $_POST['village'];
    $thana = $_POST['thana'];
    $district = $_POST['district'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $living_status = $_POST['living_status'];
    $parent_unit = $_POST['parent_unit'];
    $mission = $_POST['mission'];
    $med_category = $_POST['med_category'];
    $no_of_children = $_POST['no_of_children'];
    $date_retirement = $_POST['date_retirement'];
    $personal_contact = $_POST['personal_contact'];
    $emergency_contact = $_POST['emergency_contact'];

    // Prepare the UPDATE statement for Soldier table
    $querySoldier = "UPDATE Soldier SET Name = :name, RankID = :rank, TradeID = :trade, CompanyID = :company, Gender = :gender, Religion = :religion, DateOfBirth = TO_DATE(:date_of_birth, 'YYYY-MM-DD'), DateOfEnroll = TO_DATE(:date_of_joining, 'YYYY-MM-DD'), BloodGroup = :blood_group, MaritalStatus = :marital_status, Village = :village, Thana = :thana, District = :district, Height = :height, Weight = :weight, LivingStatus = :living_status, ParentUnit = :parent_unit, Mission = :mission, MedCategory = :med_category, NoOfChildren = :no_of_children, DateRetirement = TO_DATE(:date_retirement, 'YYYY-MM-DD'), PersonalContact = :personal_contact, EmergencyContact = :emergency_contact WHERE SoldierID = :soldier_id";
    $stmt = oci_parse($conn, $querySoldier);

    // Bind the parameters for Soldier table
    oci_bind_by_name($stmt, ':name', $name);
    oci_bind_by_name($stmt, ':rank', $rank);
    oci_bind_by_name($stmt, ':trade', $trade);
    oci_bind_by_name($stmt, ':company', $company);
    oci_bind_by_name($stmt, ':gender', $gender);
    oci_bind_by_name($stmt, ':religion', $religion);
    oci_bind_by_name($stmt, ':date_of_birth', $date_of_birth);
    oci_bind_by_name($stmt, ':date_of_joining', $date_of_joining);
    oci_bind_by_name($stmt, ':blood_group', $blood_group);
    oci_bind_by_name($stmt, ':marital_status', $marital_status);
    oci_bind_by_name($stmt, ':village', $village);
    oci_bind_by_name($stmt, ':thana', $thana);
    oci_bind_by_name($stmt, ':district', $district);
    oci_bind_by_name($stmt, ':height', $height);
    oci_bind_by_name($stmt, ':weight', $weight);
    oci_bind_by_name($stmt, ':living_status', $living_status);
    oci_bind_by_name($stmt, ':parent_unit', $parent_unit);
    oci_bind_by_name($stmt, ':mission', $mission);
    oci_bind_by_name($stmt, ':med_category', $med_category);
    oci_bind_by_name($stmt, ':no_of_children', $no_of_children);
    oci_bind_by_name($stmt, ':date_retirement', $date_retirement);
    oci_bind_by_name($stmt, ':personal_contact', $personal_contact);
    oci_bind_by_name($stmt, ':emergency_contact', $emergency_contact);
    oci_bind_by_name($stmt, ':soldier_id', $soldier_id);

    // Execute the UPDATE statement for Soldier table
    $result = oci_execute($stmt);

    if ($result !== false) {
        $_SESSION['success'] = "Soldier updated successfully.";
        header("Location: profile.php?soldierid=$soldier_id");
        exit();
    } else {
        $e = oci_error($stmt);
        $_SESSION['error'] = "Failed to update soldier: " . $e['message'];
        header("Location: edit_soldier.php?soldier=$soldier_id");
        exit();
    }
    oci_free_statement($stmt);
}

// Get the soldier ID from the URL
if (isset($_GET['soldier'])) {
    $soldier_id = $_GET['soldier'];

    // Fetch soldier data from the database
    $querySoldier = "SELECT S.SOLDIERID, S.NAME, S.GENDER, S.RELIGION, S.DATEOFBIRTH, S.DATEOFENROLL, S.BLOODGROUP,
    S.MARITALSTATUS, S.VILLAGE, S.THANA, S.DISTRICT, S.HEIGHT, S.WEIGHT, S.LIVINGSTATUS, S.PARENTUNIT,
    S.MISSION, S.MEDCATEGORY, S.NOOFCHILDREN, S.DATERETIREMENT, S.PERSONALCONTACT, S.EMERGENCYCONTACT,
    T.TRADEID, T.TRADE, R.RANKID, R.RANK, C.COMPANYID, C.COMPANYNAME
FROM SOLDIER S
JOIN TRADE T ON S.TRADEID = T.TRADEID
JOIN RANKS R ON S.RANKID = R.RANKID
JOIN COMPANY C ON S.COMPANYID = C.COMPANYID
WHERE S.SOLDIERID = :soldier_id";

    $stmt = oci_parse($conn, $querySoldier);
    oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
    oci_execute($stmt);
    if ($rowSoldier = oci_fetch_assoc($stmt)) {
        $soldier_id = $rowSoldier['SOLDIERID'];
        $name = $rowSoldier['NAME'];
        $rank_id = $rowSoldier['RANKID'];
        $trade_id = $rowSoldier['TRADEID'];
        $company_id = $rowSoldier['COMPANYID'];
        $gender = $rowSoldier['GENDER'];
        $religion = $rowSoldier['RELIGION'];
        $date_of_birth = $rowSoldier['DATEOFBIRTH'];
        $date_of_joining = $rowSoldier['DATEOFENROLL'];
        $blood_group = $rowSoldier['BLOODGROUP'];
        $marital_status = $rowSoldier['MARITALSTATUS'];
        $village = $rowSoldier['VILLAGE'];
        $thana = $rowSoldier['THANA'];
        $district = $rowSoldier['DISTRICT'];
        $height = $rowSoldier['HEIGHT'];
        $weight = $rowSoldier['WEIGHT'];
        $living_status = $rowSoldier['LIVINGSTATUS'];
        $parent_unit = $rowSoldier['PARENTUNIT'];
        $mission = $rowSoldier['MISSION'];
        $med_category = $rowSoldier['MEDCATEGORY'];
        $no_of_children = $rowSoldier['NOOFCHILDREN'];
        $date_retirement = $rowSoldier['DATERETIREMENT'];
        $personal_contact = $rowSoldier['PERSONALCONTACT'];
        $emergency_contact = $rowSoldier['EMERGENCYCONTACT'];
    } else {
        $_SESSION['error'] = "Soldier ID not found.";
        header("Location: soldiers.php");
    }

    oci_free_statement($stmt);
} else {
    $_SESSION['error'] = "Invalid URL.";
    header("Location: add_soldier.php");
}

// Fetch data for the trade table
$queryTrade = "SELECT TRADEID, TRADE FROM TRADE";
$stmtTrade = oci_parse($conn, $queryTrade);
oci_execute($stmtTrade);

$tradeList = array();
while ($rowTrade = oci_fetch_assoc($stmtTrade)) {
    $trade = new stdClass();
    $trade->TradeID = $rowTrade['TRADEID'];
    $trade->Trade = $rowTrade['TRADE'];
    $tradeList[] = $trade;
}

oci_free_statement($stmtTrade);

// Fetch data for the rank table
$queryRank = "SELECT RANKID, RANK FROM Ranks";
$stmtRank = oci_parse($conn, $queryRank);
oci_execute($stmtRank);

$rankList = array();
while ($rowRank = oci_fetch_assoc($stmtRank)) {
    $rank = new stdClass();
    $rank->RankID = $rowRank['RANKID'];
    $rank->Rank = $rowRank['RANK'];
    $rankList[] = $rank;
}

oci_free_statement($stmtRank);

$query = "SELECT COMPANYID, COMPANYNAME FROM Company";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$companyList = array();

while ($row = oci_fetch_assoc($stmt)) {
    $company = new stdClass();
    $company->COMPANYID = $row['COMPANYID'];
    $company->COMPANYNAME = $row['COMPANYNAME'];
    $companyList[] = $company;
}

oci_free_statement($stmt);

include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Edit Soldier</h3>
            <?php echo $result; ?>
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
                        <form method="post" action="">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="soldier_id">Soldier ID:</label>
                                        <input type="text" name="soldier_id" id="soldier_id" class="form-control"
                                            value="<?php echo $soldier_id; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Name:</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            value="<?php echo $name; ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="rank_id">Rank:</label>
                                        <select name="rank_id" id="rank_id" class="form-control custom-select" required>
                                            <option value="">Select Rank</option>
                                            <?php foreach ($rankList as $rank): ?>
                                                <option value="<?php echo $rank->RankID ?>" <?php if (isset($rank_id) && $rank_id == $rank->RankID): ?>selected<?php endif; ?>><?php echo $rank->Rank ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="trade_id">Trade:</label>
                                        <select name="trade_id" id="trade_id" class="form-control custom-select"
                                            required>
                                            <option value="">Select Trade</option>
                                            <?php foreach ($tradeList as $trade): ?>
                                                <option value="<?php echo $trade->TradeID ?>" <?php if (isset($trade_id) && $trade_id == $trade->TradeID): ?>selected<?php endif; ?>><?php echo $trade->Trade ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="company_id">Company:</label>
                                        <select name="company_id" id="company_id" class="form-control custom-select"
                                            required>
                                            <option value="">Select Company</option>
                                            <?php foreach ($companyList as $company): ?>
                                                <option value="<?php echo $company->COMPANYID ?>" <?php if (isset($company_id) && $company_id == $company->COMPANYID): ?>selected<?php endif; ?>><?php echo $company->COMPANYNAME ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="date_of_joining">Date of Joining:</label>
                                        <input type="date" name="date_of_joining" id="date_of_joining"
                                            class="form-control"
                                            value="<?php echo date('Y-m-d', strtotime($date_of_joining)); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_of_birth">Date of Birth:</label>
                                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control"
                                            value="<?php echo date('Y-m-d', strtotime($date_of_birth)); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">Gender:</label>
                                        <select name="gender" id="gender" class="form-control" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male" <?php if ($gender == 'Male') {
                                                echo 'selected';
                                            } ?>>Male
                                            </option>
                                            <option value="Female" <?php if ($gender == 'Female') {
                                                echo 'selected';
                                            } ?>>
                                                Female
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="religion">Religion:</label>
                                        <select name="religion" id="religion" class="form-control" required>
                                            <option value="">Select Religion</option>
                                            <option value="Islam" <?php if ($religion == 'Islam') {
                                                echo 'selected';
                                            } ?>>
                                                Islam
                                            </option>
                                            <option value="Hindu" <?php if ($religion == 'Hindu') {
                                                echo 'selected';
                                            } ?>>
                                                Hindu
                                            </option>
                                            <option value="Christian" <?php if ($religion == 'Christian') {
                                                echo 'selected';
                                            } ?>>Christian
                                            </option>
                                            <option value="Other" <?php if ($religion == 'Other') {
                                                echo 'selected';
                                            } ?>>
                                                Other
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="blood_group">Blood Group:</label>
                                        <select name="blood_group" id="blood_group" class="form-control" required>
                                            <option value="">Select Blood Group</option>
                                            <option value="A+" <?php if ($blood_group == 'A+') {
                                                echo 'selected';
                                            } ?>>A+
                                            </option>
                                            <option value="A-" <?php if ($blood_group == 'A-') {
                                                echo 'selected';
                                            } ?>>A-
                                            </option>
                                            <option value="B+" <?php if ($blood_group == 'B+') {
                                                echo 'selected';
                                            } ?>>B+
                                            </option>
                                            <option value="B-" <?php if ($blood_group == 'B-') {
                                                echo 'selected';
                                            } ?>>B-
                                            </option>
                                            <option value="AB+" <?php if ($blood_group == 'AB+') {
                                                echo 'selected';
                                            } ?>>AB+
                                            </option>
                                            <option value="AB-" <?php if ($blood_group == 'AB-') {
                                                echo 'selected';
                                            } ?>>AB-
                                            </option>
                                            <option value="O+" <?php if ($blood_group == 'O+') {
                                                echo 'selected';
                                            } ?>>O+
                                            </option>
                                            <option value="O-" <?php if ($blood_group == 'O-') {
                                                echo 'selected';
                                            } ?>>O-
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="marital_status">Marital Status:</label>
                                        <select name="marital_status" id="marital_status" class="form-control" required>
                                            <option value="">Select Marital Status</option>
                                            <option value="Married" <?php if ($marital_status == 'Married') {
                                                echo 'selected';
                                            } ?>>Married
                                            </option>
                                            <option value="Unmarried" <?php if ($marital_status == 'Unmarried') {
                                                echo 'selected';
                                            } ?>>Unmarried
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="living_status">Living Status:</label>
                                        <select name="living_status" id="living_status" class="form-control" required>
                                            <option value="">Select Living Status</option>
                                            <option value="Inliving" <?php if ($living_status == 'Inliving') {
                                                echo 'selected';
                                            } ?>>Inliving
                                            </option>
                                            <option value="Outliving" <?php if ($living_status == 'Outliving') {
                                                echo 'selected';
                                            } ?>>Outliving
                                            </option>
                                            <option value="Barak" <?php if ($living_status == 'Barak') {
                                                echo 'selected';
                                            } ?>>Offrs Mess
                                            </option>
                                            <option value="Barak" <?php if ($living_status == 'Barak') {
                                                echo 'selected';
                                            } ?>>Snk Mess
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="height">Height (cm):</label>
                                        <input type="text" name="height" id="height" class="form-control"
                                            value="<?php echo $height; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="weight">Weight (lbs):</label>
                                        <input type="text" name="weight" id="weight" class="form-control"
                                            value="<?php echo $weight; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="village">Village:</label>
                                        <input type="text" name="village" id="village" class="form-control"
                                            value="<?php echo $village; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="thana">Thana:</label>
                                        <input type="text" name="thana" id="thana" class="form-control"
                                            value="<?php echo $thana; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="district">District:</label>
                                        <input type="text" name="district" id="district" class="form-control"
                                            value="<?php echo $district; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="date_retirement">Date of Retirement:</label>
                                        <input type="date" name="date_retirement" id="date_retirement"
                                            class="form-control"
                                            value="<?php echo date('Y-m-d', strtotime($date_retirement)); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="personal_contact">Personal Contact:</label>
                                        <input type="text" name="personal_contact" id="personal_contact"
                                            class="form-control" value="<?php echo $personal_contact; ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="emergency_contact">Emergency Contact:</label>
                                        <input type="text" name="emergency_contact" id="emergency_contact"
                                            class="form-control" value="<?php echo $emergency_contact; ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="parent_unit">Parent Unit:</label>
                                        <input type="text" name="parent_unit" id="parent_unit" class="form-control"
                                            value="<?php echo $parent_unit; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="mission">Mission:</label>
                                        <select name="mission" id="mission" class="form-control" required>
                                            <option value="">Select Mission</option>
                                            <option value="Completed" <?php if ($mission == 'Completed') {
                                                echo 'selected';
                                            } ?>>Completed
                                            </option>
                                            <option value="Not Completed" <?php if ($mission == 'Not Completed') {
                                                echo 'selected';
                                            } ?>>Not Completed
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="med_category">Medical Category:</label>
                                        <select name="med_category" id="med_category" class="form-control" required>
                                            <option value="">Select Medical Category</option>
                                            <option value="A" <?php if ($med_category == 'A') {
                                                echo 'selected';
                                            } ?>>Cat A
                                            </option>
                                            <option value="B" <?php if ($med_category == 'B') {
                                                echo 'selected';
                                            } ?>>Cat   B
                                            </option>
                                            <option value="C" <?php if ($med_category == 'C') {
                                                echo 'selected';
                                            } ?>>Cat   C
                                            </option>
                                            <option value="D" <?php if ($med_category == 'D') {
                                                echo 'selected';
                                            } ?>>Cat   D
                                            </option>
                                            <option value="E" <?php if ($med_category == 'E') {
                                                echo 'selected';
                                            } ?>>Cat E
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_of_children">Number of Children:</label>
                                        <select name="no_of_children" id="no_of_children" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="">N/A</option>
                                            <option value="1" <?php if ($no_of_children == '1') {
                                                echo 'selected';
                                            } ?>>1
                                            </option>
                                            <option value="2" <?php if ($no_of_children == '2') {
                                                echo 'selected';
                                            } ?>>2
                                            </option>
                                            <option value="3" <?php if ($no_of_children == '3') {
                                                echo 'selected';
                                            } ?>>3
                                            </option>
                                            <option value="4" <?php if ($no_of_children == '4') {
                                                echo 'selected';
                                            } ?>>4
                                            </option>
                                            <option value="5" <?php if ($no_of_children == '5') {
                                                echo 'selected';
                                            } ?>>5
                                            </option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-md-3">

                                </div>
                            </div>
                            <input type="submit" name="submit" value="Update" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Page content -->
<?php include '../includes/footer.php'; ?>