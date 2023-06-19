<?php
// edit_soldier.php

// Check if the soldier ID is provided in the URL
if (isset($_GET['soldier_id'])) {
    $soldier_id = $_GET['soldier_id'];

    // Establish a connection to the Oracle database
    $conn = oci_connect('UMS', '12345', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        echo "Failed to connect to Oracle: " . $e['message'];
        exit;
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

    // Fetch soldier data for the given soldier ID
    $query = "SELECT s.SOLDIERID, s.NAME, s.RANKID, s.TRADEID, s.COMPANYID, s.GENDER, s.RELIGION, 
    TO_CHAR(s.DATEOFBIRTH, 'YYYY-MM-DD') AS DATEOFBIRTH, TO_CHAR(s.DATEOFENROLL, 'YYYY-MM-DD') AS DATEOFENROLL, 
    s.BLOODGROUP, s.MARITALSTATUS, s.VILLAGE, s.THANA, s.DISTRICT, s.HEIGHT, s.WEIGHT, s.LIVINGSTATUS,
    r.RANK, t.TRADE, c.COMPANYNAME
    FROM Soldier s
    JOIN Ranks r ON s.RANKID = r.RANKID
    JOIN Trade t ON s.TRADEID = t.TRADEID
    JOIN Company c ON s.COMPANYID = c.COMPANYID
    WHERE s.SOLDIERID = :soldier_id";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
    oci_execute($stmt);

    $soldier = oci_fetch_assoc($stmt);

    oci_free_statement($stmt);
    oci_close($conn);

    // Check if the soldier data is found
    if (!$soldier) {
        echo "Soldier not found.";
        exit;
    }
} else {
    echo "Invalid request. Soldier ID not provided.";
    exit;
}
?>

<?php include 'views/auth.php'; ?>

<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h2>Edit Soldier Data</h2>
                </div>
                <?php
                // Check if the form is submitted
                if (isset($_POST['submit'])) {
                    // Get the form data
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

                    // Establish a connection to the Oracle database
                    $conn = oci_connect('UMS', '12345', 'localhost/XE');
                    if (!$conn) {
                        $e = oci_error();
                        echo "Failed to connect to Oracle: " . $e['message'];
                    } else {
                        // Prepare the UPDATE statement
                        $query = "UPDATE Soldier SET 
                                      NAME = :name, 
                                      RANKID = :rank, 
                                      TRADEID = :trade, 
                                      COMPANYID = :company, 
                                      GENDER = :gender, 
                                      RELIGION = :religion, 
                                      DATEOFBIRTH = TO_DATE(:date_of_birth, 'YYYY-MM-DD'), 
                                      DATEOFENROLL = TO_DATE(:date_of_joining, 'YYYY-MM-DD'), 
                                      BLOODGROUP = :blood_group, 
                                      MARITALSTATUS = :marital_status, 
                                      VILLAGE = :village, 
                                      THANA = :thana, 
                                      DISTRICT = :district, 
                                      HEIGHT = :height, 
                                      WEIGHT = :weight, 
                                      LIVINGSTATUS = :living_status
                                      WHERE SOLDIERID = :soldier_id";
                        $stmt = oci_parse($conn, $query);

                        // Bind the parameters
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
                        oci_bind_by_name($stmt, ':soldier_id', $soldier_id);

                        // Execute the UPDATE statement
                        $result = oci_execute($stmt);
                        if ($result) {
                            echo '<div class="alert alert-success" role="alert">
                                      Soldier data updated successfully.
                                  </div>';
                        } else {
                            $e = oci_error($stmt);
                            echo '<div class="alert alert-danger" role="alert">
                                      Failed to update soldier data: ' . $e['message'] . '
                                  </div>';
                        }

                        oci_free_statement($stmt);
                        oci_close($conn);
                    }
                }
                ?>
            </div>
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
                                                    <input type="text" name="soldier_id" id="soldier_id"
                                                        class="form-control"
                                                        value="<?php echo $soldier['SOLDIERID']; ?>" required readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="name">Name:</label>
                                                    <input type="text" name="name" id="name" class="form-control"
                                                        value="<?php echo $soldier['NAME']; ?>" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="rank_id">Rank:</label>
                                                    <select name="rank_id" id="rank_id"
                                                        class="form-control custom-select" required>
                                                        <option value="">Select Rank</option>
                                                        <?php foreach ($rankList as $rank): ?>
                                                            <option value="<?php echo $rank->RankID ?>" <?php if ($rank->RankID == $soldier['RANKID']) echo 'selected'; ?>><?php echo $rank->Rank ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="trade_id">Trade:</label>
                                                    <select name="trade_id" id="trade_id"
                                                        class="form-control custom-select" required>
                                                        <option value="">Select Trade</option>
                                                        <?php foreach ($tradeList as $trade): ?>
                                                            <option value="<?php echo $trade->TradeID ?>" <?php if ($trade->TradeID == $soldier['TRADEID']) echo 'selected'; ?>><?php echo $trade->Trade ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="company_id">Company:</label>
                                                    <select name="company_id" id="company_id"
                                                        class="form-control custom-select" required>
                                                        <option value="">Select Company</option>
                                                        <?php foreach ($companyList as $company): ?>
                                                            <option value="<?php echo $company->COMPANYID ?>" <?php if ($company->COMPANYID == $soldier['COMPANYID']) echo 'selected'; ?>><?php echo $company->COMPANYNAME ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="gender">Gender:</label>
                                                    <select name="gender" id="gender" class="form-control" required>
                                                        <option value="">Select Gender</option>
                                                        <option value="Male" <?php if ($soldier['GENDER'] == 'Male') echo 'selected'; ?>>Male</option>
                                                        <option value="Female" <?php if ($soldier['GENDER'] == 'Female') echo 'selected'; ?>>Female</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="religion">Religion:</label>
                                                    <select name="religion" id="religion" class="form-control" required>
                                                        <option value="">Select Religion</option>
                                                        <option value="Islam" <?php if ($soldier['RELIGION'] == 'Islam') echo 'selected'; ?>>Islam</option>
                                                        <option value="Hindu" <?php if ($soldier['RELIGION'] == 'Hindu') echo 'selected'; ?>>Hindu</option>
                                                        <option value="Christian" <?php if ($soldier['RELIGION'] == 'Christian') echo 'selected'; ?>>Christian</option>
                                                        <option value="Other" <?php if ($soldier['RELIGION'] == 'Other') echo 'selected'; ?>>Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="date_of_birth">Date of Birth:</label>
                                                    <input type="date" name="date_of_birth" id="date_of_birth"
                                                        class="form-control"
                                                        value="<?php echo $soldier['DATEOFBIRTH']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="date_of_joining">Date of Joining:</label>
                                                    <input type="date" name="date_of_joining" id="date_of_joining"
                                                        class="form-control"
                                                        value="<?php echo $soldier['DATEOFENROLL']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="blood_group">Blood Group:</label>
                                                    <select name="blood_group" id="blood_group" class="form-control"
                                                        required>
                                                        <option value="">Select Blood Group</option>
                                                        <option value="A+" <?php if ($soldier['BLOODGROUP'] == 'A+') echo 'selected'; ?>>A+</option>
                                                        <option value="A-" <?php if ($soldier['BLOODGROUP'] == 'A-') echo 'selected'; ?>>A-</option>
                                                        <option value="B+" <?php if ($soldier['BLOODGROUP'] == 'B+') echo 'selected'; ?>>B+</option>
                                                        <option value="B-" <?php if ($soldier['BLOODGROUP'] == 'B-') echo 'selected'; ?>>B-</option>
                                                        <option value="AB+" <?php if ($soldier['BLOODGROUP'] == 'AB+') echo 'selected'; ?>>AB+</option>
                                                        <option value="AB-" <?php if ($soldier['BLOODGROUP'] == 'AB-') echo 'selected'; ?>>AB-</option>
                                                        <option value="O+" <?php if ($soldier['BLOODGROUP'] == 'O+') echo 'selected'; ?>>O+</option>
                                                        <option value="O-" <?php if ($soldier['BLOODGROUP'] == 'O-') echo 'selected'; ?>>O-</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="marital_status">Marital Status:</label>
                                                    <select name="marital_status" id="marital_status"
                                                        class="form-control" required>
                                                        <option value="">Select Marital Status</option>
                                                        <option value="Married" <?php if ($soldier['MARITALSTATUS'] == 'Married') echo 'selected'; ?>>Married</option>
                                                        <option value="Unmarried" <?php if ($soldier['MARITALSTATUS'] == 'Unmarried') echo 'selected'; ?>>Unmarried</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="village">Village:</label>
                                                    <input type="text" name="village" id="village" class="form-control"
                                                        value="<?php echo $soldier['VILLAGE']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="thana">Thana:</label>
                                                    <input type="text" name="thana" id="thana" class="form-control"
                                                        value="<?php echo $soldier['THANA']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="district">District:</label>
                                                    <input type="text" name="district" id="district"
                                                        class="form-control"
                                                        value="<?php echo $soldier['DISTRICT']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="height">Height (cm):</label>
                                                    <input type="text" name="height" id="height" class="form-control"
                                                        value="<?php echo $soldier['HEIGHT']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="weight">Weight (lbs):</label>
                                                    <input type="text" name="weight" id="weight" class="form-control"
                                                        value="<?php echo $soldier['WEIGHT']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="living_status">Living Status:</label>
                                                    <select name="living_status" id="living_status"
                                                        class="form-control" required>
                                                        <option value="">Select Living Status</option>
                                                        <option value="Inliving" <?php if ($soldier['LIVINGSTATUS'] == 'Inliving') echo 'selected'; ?>>Inliving</option>
                                                        <option value="Outliving" <?php if ($soldier['LIVINGSTATUS'] == 'Outliving') echo 'selected'; ?>>Outliving</option>
                                                        <option value="Barak" <?php if ($soldier['LIVINGSTATUS'] == 'Barak') echo 'selected'; ?>>Barak</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="contact_number1">Personal Number:</label>
                                                    <input type="text" name="contact_number1" id="contact_number1" class="form-control" value="<?php echo isset($existingContactNumbers[0]) ? $existingContactNumbers[0] : ''; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="contact_number2">Wife's Number:</label>
                                                    <input type="text" name="contact_number2" id="contact_number2" class="form-control" value="<?php echo isset($existingContactNumbers[1]) ? $existingContactNumbers[1] : ''; ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <input type="submit" name="submit" value="Update" class="btn btn-primary">
                                    </form>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </section>

        <!-- Page content -->

        <?php include 'views/footer.php'; ?>

    </div>

</body>

</html>
