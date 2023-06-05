<?php
$conn = oci_connect('UMS', '12345', 'localhost/XE');
if (!$conn) {
    $e = oci_error();
    echo "Failed to connect to Oracle: " . $e['message'];
} else {

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
}
?>


<html>
<head>
    <title>Insert Soldier Data</title>
</head>
<body>
    <h2>Insert Soldier Data</h2>
    <form method="post" action="">
        <label for="soldier_id">Soldier ID:</label>
        <input type="text" name="soldier_id" id="soldier_id" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>

        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required><br>

        <label for="marital_status">Marital Status:</label>
        <input type="text" name="marital_status" id="marital_status" required><br>

        <label for="blood_group">Blood Group:</label>
        <input type="text" name="blood_group" id="blood_group" required><br>

        <label for="weight">Weight:</label>
        <input type="text" name="weight" id="weight" required><br>

        <label for="height">Height:</label>
        <input type="text" name="height" id="height" required><br>

        <label for="religion">Religion:</label>
        <input type="text" name="religion" id="religion" required><br>

        <label for="age">Age:</label>
        <input type="text" name="age" id="age" required><br>

        <label for="date_of_birth">Date of Birth:</label>
        <input type="date" name="date_of_birth" id="date_of_birth" required><br>

        <label for="gender">Gender:</label>
        <input type="text" name="gender" id="gender" required><br>

        <label for="living_status">Living Status:</label>
        <input type="text" name="living_status" id="living_status" required><br>

        <label for="village">Village:</label>
        <input type="text" name="village" id="village" required><br>

        <label for="thana">Thana:</label>
        <input type="text" name="thana" id="thana" required><br>

        <label for="district">District:</label>
        <input type="text" name="district" id="district" required><br>

        <label for="date_of_enroll">Date of Enrollment:</label>
        <input type="date" name="date_of_enroll" id="date_of_enroll" required><br>

        <label for="temporary_command">Temporary Command:</label>
        <input type="text" name="temporary_command" id="temporary_command" ><br>

        <label for="ere">ERE:</label>
        <input type="text" name="ere" id="ere" ><br>

        <label for="serving_status">Serving Status:</label>
        <input type="text" name="serving_status" id="serving_status" ><br>

        <label for="trade_id">Trade:</label>
        <select name="trade_id" id="trade_id" class="form-control custom-select" required>
            <option value="">Select Trade</option>
            <?php foreach ($tradeList as $trade): ?>
                <option value="<?php echo $trade->TradeID ?>"><?php echo $trade->Trade ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="rank_id">Rank:</label>
        <select name="rank_id" id="rank_id" class="form-control custom-select" required>
            <option value="">Select Rank</option>
            <?php foreach ($rankList as $rank): ?>
                <option value="<?php echo $rank->RankID ?>"><?php echo $rank->Rank ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="company_id">Company:</label>
        <select name="company_id" id="company_id" class="form-control custom-select" required>
            <option value="">Select Company</option>
            <?php foreach ($companyList as $company): ?>
                <option value="<?php echo $company->COMPANYID ?>"><?php echo $company->COMPANYNAME ?></option>
            <?php endforeach; ?>
        </select><br>


        <label for="carrier_plan_id">Carrier Plan ID:</label>
        <input type="text" name="carrier_plan_id" id="carrier_plan_id" ><br>

        <input type="submit" name="submit" value="Submit">
    </form>

    <?php
    // Check if the form is submitted
    if (isset($_POST['submit'])) {
        // Get the form data
        $soldier_id = $_POST['soldier_id'];
        $password = $_POST['password'];
        $name = $_POST['name'];
        $marital_status = $_POST['marital_status'];
        $blood_group = $_POST['blood_group'];
        $weight = $_POST['weight'];
        $height = $_POST['height'];
        $religion = $_POST['religion'];
        $age = $_POST['age'];
        $date_of_birth = $_POST['date_of_birth'];
        $gender = $_POST['gender'];
        $living_status = $_POST['living_status'];
        $village = $_POST['village'];
        $thana = $_POST['thana'];
        $district = $_POST['district'];
        $date_of_enroll = $_POST['date_of_enroll'];
        $temporary_command = $_POST['temporary_command'];
        $ere = $_POST['ere'];
        $serving_status = $_POST['serving_status'];
        $trade_id = $_POST['trade_id'];
        $rank_id = $_POST['rank_id'];
        $company_id = $_POST['company_id'];
        $carrier_plan_id = $_POST['carrier_plan_id'];

        // Establish a connection to the Oracle database
        $conn = oci_connect('UMS', '12345', 'localhost/XE');
        if (!$conn) {
            $e = oci_error();
            echo "Failed to connect to Oracle: " . $e['message'];
        } else {
            // Prepare the INSERT statement
            $query = "INSERT INTO Soldier (SoldierID, Password, Name, MaritalStatus, BloodGroup, Weight, Height, Religion, Age, DateOfBirth, Gender, LivingStatus, Village, Thana, District, DateOfEnroll, TemporaryCommand, ERE, ServingStatus, TradeID, RankID, CompanyID, CarrierPlanID) 
                      VALUES (:soldier_id, :password, :name, :marital_status, :blood_group, :weight, :height, :religion, :age, TO_DATE(:date_of_birth, 'YYYY-MM-DD'), :gender, :living_status, :village, :thana, :district, TO_DATE(:date_of_enroll, 'YYYY-MM-DD'), :temporary_command, :ere, :serving_status, :trade_id, :rank_id, :company_id, :carrier_plan_id)";
            $stmt = oci_parse($conn, $query);

            // Bind the parameters
            oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
            oci_bind_by_name($stmt, ':password', $password);
            oci_bind_by_name($stmt, ':name', $name);
            oci_bind_by_name($stmt, ':marital_status', $marital_status);
            oci_bind_by_name($stmt, ':blood_group', $blood_group);
            oci_bind_by_name($stmt, ':weight', $weight);
            oci_bind_by_name($stmt, ':height', $height);
            oci_bind_by_name($stmt, ':religion', $religion);
            oci_bind_by_name($stmt, ':age', $age);
            oci_bind_by_name($stmt, ':date_of_birth', $date_of_birth);
            oci_bind_by_name($stmt, ':gender', $gender);
            oci_bind_by_name($stmt, ':living_status', $living_status);
            oci_bind_by_name($stmt, ':village', $village);
            oci_bind_by_name($stmt, ':thana', $thana);
            oci_bind_by_name($stmt, ':district', $district);
            oci_bind_by_name($stmt, ':date_of_enroll', $date_of_enroll);
            oci_bind_by_name($stmt, ':temporary_command', $temporary_command);
            oci_bind_by_name($stmt, ':ere', $ere);
            oci_bind_by_name($stmt, ':serving_status', $serving_status);
            oci_bind_by_name($stmt, ':trade_id', $trade_id);
            oci_bind_by_name($stmt, ':rank_id', $rank_id);
            oci_bind_by_name($stmt, ':company_id', $company_id);
            oci_bind_by_name($stmt, ':carrier_plan_id', $carrier_plan_id);

            // Execute the INSERT statement
            $result = oci_execute($stmt);
            if ($result) {
                echo "Soldier data inserted successfully.";
            } else {
                $e = oci_error($stmt);
                echo "Failed to insert soldier data: " . $e['message'];
            }

            oci_free_statement($stmt);
            oci_close($conn);
        }
    }
    ?>
</body>
</html>
