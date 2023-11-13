<?php
session_start();

require_once('../includes/connection.php');

// Check if the form is submitted for uploading
if (isset($_POST['upload'])) {
    // Check if a CSV file is uploaded
    if (!empty($_FILES['csv_file']['tmp_name'])) {
        // Process the uploaded CSV file
        $csvFile = $_FILES['csv_file']['tmp_name'];

        // Check if the headers are valid
        $validHeaders = validateCsvHeaders($csvFile);

        if ($validHeaders) {
            // Process the uploaded CSV file
            $importedData = processCsvFile($csvFile);
        } else {
            $_SESSION['error'] = 'Invalid CSV file headers. Please check the file format.';
        }
    } else {
        $_SESSION['error'] = 'No CSV file uploaded. Please choose a file.';
    }
}

function validateCsvHeaders($csvFile)
{
    $handle = fopen($csvFile, 'r');
    $expectedHeaders = array(
        'SOLDIERID', 'NAME', 'MARITALSTATUS', 'BLOODGROUP', 'WEIGHT', 'HEIGHT',
        'RELIGION', 'DATEOFBIRTH', 'GENDER', 'LIVINGSTATUS', 'VILLAGE', 'THANA',
        'DISTRICT', 'DATEOFENROLL', 'TRADEID', 'RANKID', 'COMPANYID', 'AGE',
        'PARENTUNIT', 'MISSION', 'MEDCATEGORY', 'NOOFCHILDREN', 'DATERETIREMENT',
        'PERSONALCONTACT', 'EMERGENCYCONTACT'
    );

    $actualHeaders =  fgetcsv($handle);


    fclose($handle);

    // Compare the expected and actual headers
    return ($actualHeaders !== false) && ($expectedHeaders === $actualHeaders);
}


if (isset($_POST['import']) && isset($_POST['importedData'])) {
    $finalImportedData = json_decode($_POST['importedData'], true);

    // Check if decoding was successful
    if ($finalImportedData !== null) {

        foreach ($finalImportedData as $rowData) {
            if (!empty($rowData['soldierid'])) {
                $result = insertSoldierData($rowData);
        
                if ($result === true) {
                    $_SESSION['success'][] = "Soldier '{$rowData['name']}' (ID: {$rowData['soldierid']}) imported successfully.";
                } else {
                    $_SESSION['error'][] = "Failed to import soldier '{$rowData['name']}' (ID: {$rowData['soldierid']}): " . $result;
                }
            } else {
                $_SESSION['error'][] = "Invalid data for soldier '{$rowData['name']}': Soldier ID is empty.";
            }
        }
        
    } else {
        echo "Error decoding JSON data.";
    }
}



// Function to process the uploaded CSV file and return imported data
function processCsvFile($csvFile)
{
    $importedData = [];

    $handle = fopen($csvFile, 'r');
    global $headers;
    // Assuming the first row in CSV contains headers
    $headers = fgetcsv($handle);
    while (($row = fgetcsv($handle)) !== false) {
        $rowData = [];
        foreach ($headers as $index => $header) {
            $paramName = strtolower(str_replace(' ', '_', $header));
            $paramValue = $row[$index];
            $rowData[$paramName] = $paramValue;
        }

        $importedData[] = $rowData;
    }

    fclose($handle);

    return $importedData;
}

// Function to insert data into the Soldier table
// Function to insert data into the Soldier table
function insertSoldierData($data)
{
    global $conn;

    // Prepare the INSERT statement for Soldier table
    $querySoldier = "INSERT INTO SOLDIER (SOLDIERID, NAME, RANKID, TRADEID, COMPANYID, GENDER, RELIGION, DATEOFBIRTH, DATEOFENROLL, BLOODGROUP, MARITALSTATUS, VILLAGE, THANA, DISTRICT, HEIGHT, WEIGHT, LIVINGSTATUS, PARENTUNIT, MISSION, MEDCATEGORY, NOOFCHILDREN, DATERETIREMENT, PERSONALCONTACT, EMERGENCYCONTACT) 
                 VALUES (:soldier_id, :name, :rank, :trade, :company, :gender, :religion, TO_DATE(:date_of_birth, 'DD-Mon-YY'), TO_DATE(:date_of_enroll, 'DD-Mon-YY'), :blood_group, :marital_status, :village, :thana, :district, :height, :weight, :living_status, :parent_unit, :mission, :med_category, :no_of_children, TO_DATE(:date_retirement, 'DD-Mon-YY'), :personal_contact, :emergency_contact)";
    $stmt = oci_parse($conn, $querySoldier);

    oci_bind_by_name($stmt, ':soldier_id', $data['soldierid']);
    oci_bind_by_name($stmt, ':name', $data['name']);
    oci_bind_by_name($stmt, ':rank', $data['rankid']);
    oci_bind_by_name($stmt, ':trade', $data['tradeid']);
    oci_bind_by_name($stmt, ':company', $data['companyid']);
    oci_bind_by_name($stmt, ':gender', $data['gender']);
    oci_bind_by_name($stmt, ':religion', $data['religion']);
    oci_bind_by_name($stmt, ':date_of_birth', $data['dateofbirth']);
    oci_bind_by_name($stmt, ':date_of_enroll', $data['dateofenroll']);
    oci_bind_by_name($stmt, ':blood_group', $data['bloodgroup']);
    oci_bind_by_name($stmt, ':marital_status', $data['maritalstatus']);
    oci_bind_by_name($stmt, ':village', $data['village']);
    oci_bind_by_name($stmt, ':thana', $data['thana']);
    oci_bind_by_name($stmt, ':district', $data['district']);
    oci_bind_by_name($stmt, ':height', $data['height']);
    oci_bind_by_name($stmt, ':weight', $data['weight']);
    oci_bind_by_name($stmt, ':living_status', $data['livingstatus']);
    oci_bind_by_name($stmt, ':parent_unit', $data['parentunit']);
    oci_bind_by_name($stmt, ':mission', $data['mission']);
    oci_bind_by_name($stmt, ':med_category', $data['medcategory']);
    oci_bind_by_name($stmt, ':no_of_children', $data['noofchildren']);
    oci_bind_by_name($stmt, ':date_retirement', $data['dateretirement']);
    oci_bind_by_name($stmt, ':personal_contact', $data['personalcontact']);
    oci_bind_by_name($stmt, ':emergency_contact', $data['emergencycontact']);
    // Execute the INSERT statement for Soldier table
    $result = oci_execute($stmt);

    if (!$result) {
        $e = oci_error($stmt);
        return $e['message']; // Return the Oracle error message
    }

    oci_free_statement($stmt);

    return true; // Return true for success
}


include '../includes/header.php';
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Import Soldiers</h3>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
    <?php
// Display import log
if (isset($_SESSION['success']) || isset($_SESSION['error'])) {
    echo '<div class="card card-warning">    ';
    echo '<div class="card-header">';
    echo '<b>Import Log</b>';
    echo '</div>';
    echo '<div class="card-body">';
    
    // Display success messages in green text
    if (isset($_SESSION['success'])) {
        foreach ($_SESSION['success'] as $successMessage) {
            echo '<div style="color: green;">' . $successMessage . '</div>';
        }
        unset($_SESSION['success']); // Clear the success messages
    }

    // Display error messages
    if (isset($_SESSION['error'])) {
        if (is_array($_SESSION['error'])) {
            // Display multiple error messages in red text
            foreach ($_SESSION['error'] as $errorMessage) {
                echo '<div style="color: red;">' . $errorMessage . '</div>';
            }
        } else {
            // Display a single error message in red text
            echo '<div style="color: red;">' . $_SESSION['error'] . '</div>';
        }
        unset($_SESSION['error']); // Clear the error messages
    }

    echo '</div>';
    echo '</div>';
}
?>


<?php
        if (empty($importedData)) {?>


        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="csv_file">Upload CSV File:</label>
                                <input type="file" name="csv_file" id="formFileLg" class="form-control form-control-lg" accept=".csv"
                                    required>
                            </div>

                            <input type="submit" name="upload" value="Upload" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
        }
        if (isset($importedData) && !empty($importedData)) {
            // Display the preview table
            echo '<div class="row">';
            echo '<div class="col-md-12">';
            echo '<div class="card card-primary">';
            echo '<div class="card-header">';
            echo '<h3 class="card-title">Data Preview</h3>';
            echo '<div class="card-tools">';
            echo '<div class="input-group input-group-sm" style="width: 150px;">';
            echo '<input type="text" name="table_search" class="form-control float-right" placeholder="Search">';
            echo '<div class="input-group-append">';
            echo '<button type="submit" class="btn btn-default">';
            echo '<i class="fas fa-search"></i>';
            echo '</button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '<div class="card-body table-responsive p-0" style="height: 400px; overflow: auto;">'; // Added "overflow: auto;"
            echo '<table class="table table-bordered table-head-fixed text-nowrap">';
            echo '<thead>';
            echo '<tr>';
            foreach ($headers as $header) {
                echo '<th>' . $header . '</th>';
            }
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($importedData as $rowData) {
                echo '<tr>';
                foreach ($rowData as $value) {
                    echo '<td>' . $value . '</td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';


            // Import confirmation button
            echo '<div class="row mt-3">';
            echo '<div class="col-md-12">';
            echo '<button type="button" class="btn btn-success" data-toggle="modal" data-target="#importModal">Import Data</button>';
            echo '<a href="import.php" class="btn btn-secondary ml-2">Cancel</a>';

            echo '</div>';
            echo '</div>';
            // Import confirmation modal
            echo '<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">';
            echo '<div class="modal-dialog" role="document">';
            echo '<div class="modal-content">';
            echo '<div class="modal-header">';
            echo '<h5 class="modal-title" id="importModalLabel">Import Confirmation</h5>';
            echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
            echo '<span aria-hidden="true">&times;</span>';
            echo '</button>';
            echo '</div>';
            echo '<div class="modal-body">';
            echo '<form method="post" action="">';
            echo '<input type="hidden" name="importedData" value="' . htmlspecialchars(json_encode($importedData)) . '">';
            echo '<p>Total rows to be imported: ' . count($importedData) . '</p>';
            echo '<p>Are you sure you want to proceed?</p>';
            echo '</div>';
            echo '<div class="modal-footer">';
            echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>';
            echo '<button type="submit" name="import" class="btn btn-primary">Import</button>';
            echo '</form>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>