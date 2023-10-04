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
    $height_ft = $_POST['height_ft'];
    $height_in = $_POST['height_in'];
    $height = ($height_ft * 12) + $height_in;
    $weight = $_POST['weight'];
    $living_status = $_POST['living_status'];
    $parent_unit = $_POST['parent_unit'];
    $mission = $_POST['mission'];
    $med_category = $_POST['med_category'];
    $no_of_children = $_POST['no_of_children'];
    $date_retirement = $_POST['date_retirement'];
    $personal_contact = $_POST['personal_contact'];
    $emergency_contact = $_POST['emergency_contact'];
    // Establish a connection to the Oracle database

    // Prepare the INSERT statement for Soldier table
    $querySoldier = "INSERT INTO Soldier (SoldierID, Name, RankID, TradeID, CompanyID, Gender, Religion, DateOfBirth, DateOfEnroll, BloodGroup, MaritalStatus, Village, Thana, District, Height, Weight, LivingStatus, ParentUnit, Mission, MedCategory, NoOfChildren, DateRetirement, PersonalContact, EmergencyContact) 
                 VALUES (:soldier_id, :name, :rank, :trade, :company, :gender, :religion, TO_DATE(:date_of_birth, 'YYYY-MM-DD'), TO_DATE(:date_of_joining, 'YYYY-MM-DD'), :blood_group, :marital_status, :village, :thana, :district, :height, :weight, :living_status, :parent_unit, :mission, :med_category, :no_of_children, TO_DATE(:date_retirement, 'YYYY-MM-DD'), :personal_contact, :emergency_contact)";
    $stmt = oci_parse($conn, $querySoldier);

    // Bind the parameters for Soldier table
    oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
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

    // Execute the INSERT statement for Soldier table
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
            <h3>Add Soldier</h3>
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
                                        <input type="number" name="soldier_id" id="soldier_id" class="form-control"
                                            minlength="4" maxlength="7" required>
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
                                        <label for="height">Height (ft/in):</label>

                                        <div class="row">

                                            <div class="col">
                                                <select name="height_ft" id="height_ft" class="form-control">
                                                    <option value="5">5 ft</option>
                                                    <option value="6">6 ft</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <select name="height_in" id="height_in" class="form-control">
                                                    <?php
                                                    for ($i = 0; $i <= 11; $i++) {
                                                        echo "<option value=\"$i\">$i in</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
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
                                        <div class="row">
                                            <div class="col">
                                                <label for="divisionSelect">Division</label>
                                                <select id="divisionSelect" class="form-control">
                                                    <option value="">Select</option>
                                                    <option value="Barisal">Barisal</option>
                                                    <option value="Chittagong">Chittagong</option>
                                                    <option value="Dhaka">Dhaka</option>
                                                    <option value="Khulna">Khulna</option>
                                                    <option value="Mymensingh">Mymensingh</option>
                                                    <option value="Rajshahi">Rajshahi</option>
                                                    <option value="Rangpur">Rangpur</option>
                                                    <option value="Sylhet">Sylhet</option>
                                                </select>
                                            </div>
                                            <div class="col">
                                                <label for="district">District</label>
                                                <select id="district" class="form-control" disabled>
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="date_retirement">Date of Retirement:</label>
                                        <input type="date" name="date_retirement" id="date_retirement"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="personal_contact">Personal Contact:</label>
                                        <input type="text" name="personal_contact" id="personal_contact"
                                            class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="emergency_contact">Emergency Contact:</label>
                                        <input type="text" name="emergency_contact" id="emergency_contact"
                                            class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="parent_unit">Parent Unit:</label>
                                        <input type="text" name="parent_unit" id="parent_unit" class="form-control"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="mission">Mission:</label>
                                        <select name="mission" id="mission" class="form-control" required>
                                            <option value="">Select Mission</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Not Completed">Not Completed</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="med_category">Medical Category:</label>
                                        <select name="med_category" id="med_category" class="form-control" required>
                                            <option value="">Select Medical Category</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                            <option value="E">E</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_of_children">Number of Children:</label>
                                        <select name="no_of_children" id="no_of_children" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="">N/A</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-md-3">

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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function () {
        var districtsByDivision = {
            "Barisal": [
                "Barguna", "Barisal", "Bhola", "Jhalokati",
                "Patuakhali", "Pirojpur"
            ],
            "Chittagong": [
                "Bandarban", "Brahmanbaria", "Chandpur", "Chittagong",
                "Comilla", "Cox's Bazar", "Feni", "Khagrachhari",
                "Lakshmipur", "Noakhali", "Rangamati"
            ],
            "Dhaka": [
                "Dhaka", "Faridpur", "Gazipur", "Gopalganj",
                "Kishoreganj", "Madaripur", "Manikganj", "Munshiganj",
                "Narayanganj", "Narsingdi", "Rajbari", "Shariatpur",
                "Tangail"
            ],
            "Khulna": [
                "Bagerhat", "Chuadanga", "Jessore", "Jhenaidah",
                "Khulna", "Kushtia", "Magura", "Meherpur",
                "Narail", "Satkhira"
            ],
            "Mymensingh": [
                "Jamalpur", "Mymensingh", "Netrokona", "Sherpur"
            ],
            "Rajshahi": [
                "Bogra", "Joypurhat", "Naogaon", "Natore",
                "Nawabganj", "Pabna", "Rajshahi", "Sirajganj"
            ],
            "Rangpur": [
                "Dinajpur", "Gaibandha", "Kurigram", "Lalmonirhat",
                "Nilphamari", "Panchagarh", "Rangpur", "Thakurgaon"
            ],
            "Sylhet": [
                "Habiganj", "Moulvibazar", "Sunamganj", "Sylhet"
            ]
        };

        $("#divisionSelect").change(function () {
            var divisionId = $(this).val();
            var districts = districtsByDivision[divisionId];
            var districtSelect = $("#district");

            districtSelect.empty();

            if (districts) {
                districtSelect.prop("disabled", false);
                $.each(districts, function (index, district) {
                    districtSelect.append($('<option></option>').attr('value', district).text(district));
                });
            } else {
                districtSelect.prop("disabled", true);
                districtSelect.append($('<option></option>').attr('value', '').text('Select District'));
            }
        });
    });
</script>
<!-- Page content -->
<?php include '../includes/footer.php'; ?>