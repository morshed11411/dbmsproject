<?php
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
    $contact_number1 = $_POST['contact_number1'];
    $contact_number2 = $_POST['contact_number2'];

    // Establish a connection to the Oracle database

    // Prepare the INSERT statement for Soldier table
    $querySoldier = "INSERT INTO Soldier (SoldierID, Name, RankID, TradeID, CompanyID, Gender, Religion, DateOfBirth, DateOfEnroll, BloodGroup, MaritalStatus, Village, Thana, District, Height, Weight, LivingStatus) 
                                  VALUES (:soldier_id, :name, :rank, :trade, :company, :gender, :religion, TO_DATE(:date_of_birth, 'YYYY-MM-DD'), TO_DATE(:date_of_joining, 'YYYY-MM-DD'), :blood_group, :marital_status, :village, :thana, :district, :height, :weight, :living_status)";
    $stmtSoldier = oci_parse($conn, $querySoldier);

    // Bind the parameters for Soldier table
    oci_bind_by_name($stmtSoldier, ':soldier_id', $soldier_id);
    oci_bind_by_name($stmtSoldier, ':name', $name);
    oci_bind_by_name($stmtSoldier, ':rank', $rank);
    oci_bind_by_name($stmtSoldier, ':trade', $trade);
    oci_bind_by_name($stmtSoldier, ':company', $company);
    oci_bind_by_name($stmtSoldier, ':gender', $gender);
    oci_bind_by_name($stmtSoldier, ':religion', $religion);
    oci_bind_by_name($stmtSoldier, ':date_of_birth', $date_of_birth);
    oci_bind_by_name($stmtSoldier, ':date_of_joining', $date_of_joining);
    oci_bind_by_name($stmtSoldier, ':blood_group', $blood_group);
    oci_bind_by_name($stmtSoldier, ':marital_status', $marital_status);
    oci_bind_by_name($stmtSoldier, ':village', $village);
    oci_bind_by_name($stmtSoldier, ':thana', $thana);
    oci_bind_by_name($stmtSoldier, ':district', $district);
    oci_bind_by_name($stmtSoldier, ':height', $height);
    oci_bind_by_name($stmtSoldier, ':weight', $weight);
    oci_bind_by_name($stmtSoldier, ':living_status', $living_status);

    // Execute the INSERT statement for Soldier table
    $resultSoldier = oci_execute($stmtSoldier);

    // Prepare the INSERT statement for ContactNumber table
    $queryContactNumber = "INSERT INTO ContactNumber (SoldierID, ContactNumber) VALUES (:soldier_id, :contact_number)";
    $stmtContactNumber = oci_parse($conn, $queryContactNumber);

    // Bind the parameters for ContactNumber table (contact number 1)
    oci_bind_by_name($stmtContactNumber, ':soldier_id', $soldier_id);
    oci_bind_by_name($stmtContactNumber, ':contact_number', $contact_number1);

    // Execute the INSERT statement for ContactNumber table (contact number 1)
    if ($contact_number1 != null) {
        $resultContactNumber1 = oci_execute($stmtContactNumber);
    }

    // Bind the parameters for ContactNumber table (contact number 2)
    oci_bind_by_name($stmtContactNumber, ':contact_number', $contact_number2);

    // Execute the INSERT statement for ContactNumber table (contact number 2)
    if ($contact_number2 != null) {
        $resultContactNumber2 = oci_execute($stmtContactNumber);
    }

    if ($resultSoldier && ($resultContactNumber1 || $resultContactNumber2)) {
        $_SESSION['success'] = "Soldier data inserted successfully. Please upload images. ";
        header("Location: uploadimage.php?soldier=$soldier_id");

    } else {
        $e = oci_error($stmtSoldier);
        $_SESSION['error'] = "Failed to insert soldier data: " . $e['message'];
    }

    oci_free_statement($stmtSoldier);
    oci_free_statement($stmtContactNumber);
    oci_close($conn);
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

oci_close($conn);
include '../includes/header.php';
?>


<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Add Soldier</h3>
        </div>
    </div>
</div>

<?php
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';

}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<section class="content">
    <div class="container-fluid">
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
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Name:</label>
                                        <input type="text" name="name" id="name" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="rank_id">Rank:</label>
                                        <select name="rank_id" id="rank_id" class="form-control custom-select" required>
                                            <option value="">Select Rank</option>
                                            <?php foreach ($rankList as $rank): ?>
                                                <option value="<?php echo $rank->RankID ?>"><?php echo $rank->Rank ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="trade_id">Trade:</label>
                                        <select name="trade_id" id="trade_id" class="form-control custom-select"
                                            required>
                                            <option value="">Select Trade</option>
                                            <?php foreach ($tradeList as $trade): ?>
                                                <option value="<?php echo $trade->TradeID ?>"><?php echo $trade->Trade ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="company_id">Company:</label>
                                        <select name="company_id" id="company_id" class="form-control custom-select"
                                            required>
                                            <option value="">Select Company</option>
                                            <?php foreach ($companyList as $company): ?>
                                                <option value="<?php echo $company->COMPANYID ?>"><?php echo $company->COMPANYNAME ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="date_of_joining">Date of Joining:</label>
                                        <input type="date" name="date_of_joining" id="date_of_joining"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_of_birth">Date of Birth:</label>
                                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">Gender:</label>
                                        <select name="gender" id="gender" class="form-control" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="religion">Religion:</label>
                                        <select name="religion" id="religion" class="form-control" required>
                                            <option value="">Select Religion</option>
                                            <option value="Islam">Islam</option>
                                            <option value="Hindu">Hindu</option>
                                            <option value="Christian">Christian</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="blood_group">Blood Group:</label>
                                        <select name="blood_group" id="blood_group" class="form-control" required>
                                            <option value="">Select Blood Group</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="marital_status">Marital Status:</label>
                                        <select name="marital_status" id="marital_status" class="form-control" required>
                                            <option value="">Select Marital Status</option>
                                            <option value="Married">Married</option>
                                            <option value="Unmarried">Unmarried</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="living_status">Living Status:</label>
                                        <select name="living_status" id="living_status" class="form-control" required>
                                            <option value="">Select Living Status</option>
                                            <option value="Inliving">Inliving</option>
                                            <option value="Outliving">Outliving</option>
                                            <option value="Barak">Offrs Mess</option>
                                            <option value="Barak">Snk Mess</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="height">Height (cm):</label>
                                        <input type="text" name="height" id="height" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="weight">Weight (lbs):</label>
                                        <input type="text" name="weight" id="weight" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="village">Village:</label>
                                        <input type="text" name="village" id="village" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="thana">Thana:</label>
                                        <input type="text" name="thana" id="thana" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="district">District:</label>
                                        <input type="text" name="district" id="district" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="contact_number1">Personal Number:</label>
                                        <input type="text" name="contact_number1" id="contact_number1"
                                            class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact_number2">Emergency Number:</label>
                                        <input type="text" name="contact_number2" id="contact_number2"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                            <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Page content -->
<?php include '../includes/footer.php'; ?>