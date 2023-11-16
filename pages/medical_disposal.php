<?php
include '../includes/connection.php';

// Function to print disposal details
function printDisposalDetails($disposalDetails)
{
    if ($disposalDetails['total'] > 0) {
        echo "Disposal holder(s):\n";
        foreach ($disposalDetails['details'] as $detail) {
            echo "SOLDIERID: {$detail['SOLDIERID']}, NAME: {$detail['NAME']}, TRADE: {$detail['TRADE']}, COMPANY: {$detail['COMPANYNAME']}, ";
            echo "DISPOSAL TYPE: {$detail['DISPOSALTYPE']}, REMARKS: {$detail['REMARKS']}, START DATE: {$detail['STARTDATE']}, END DATE: {$detail['ENDDATE']}\n";
        }
    } else {
        echo "No disposal holders.\n";
    }

    echo "\n";
}

function findDisposalHolders($conn, $coyId = null, $currentDate = null, $disposalType = null, $soldierId = null)
{
    // If $currentDate is not provided, use the current date
    $currentDate = $currentDate ? $currentDate : date('Y-m-d');

    // Query to find disposal holders
    $query = "SELECT DISTINCT S.SOLDIERID, S.NAME, T.TRADE, C.COMPANYNAME, 
              M.DISPOSALTYPE, M.STARTDATE, M.ENDDATE,
              REASON AS REMARKS
              FROM SOLDIER S
              JOIN MEDICALINFO M ON S.SOLDIERID = M.SOLDIERID
              JOIN TRADE T ON S.TRADEID = T.TRADEID
              JOIN COMPANY C ON S.COMPANYID = C.COMPANYID
              WHERE (M.DISPOSALTYPE IS NOT NULL)";

    // Apply filters based on parameters
    if ($coyId !== null) {
        $query .= " AND C.COMPANYID = :coyId";
    }

    if ($disposalType !== null) {
        $query .= " AND M.DISPOSALTYPE = :disposalType";
    }

    if ($currentDate !== null) {
        $query .= " AND TRUNC(M.STARTDATE) <= TO_DATE(:currentDate, 'YYYY-MM-DD') AND (M.ENDDATE IS NULL OR TRUNC(M.ENDDATE) >= TO_DATE(:currentDate, 'YYYY-MM-DD'))";
    }

    if ($soldierId !== null) {
        $query .= " AND S.SOLDIERID = :soldierId";
    }

    $stmt = oci_parse($conn, $query);

    // Bind parameters
    if ($coyId !== null) {
        oci_bind_by_name($stmt, ':coyId', $coyId);
    }

    if ($disposalType !== null) {
        oci_bind_by_name($stmt, ':disposalType', $disposalType);
    }

    if ($currentDate !== null) {
        oci_bind_by_name($stmt, ':currentDate', $currentDate);
    }

    if ($soldierId !== null) {
        oci_bind_by_name($stmt, ':soldierId', $soldierId);
    }

    oci_execute($stmt);

    $disposalDetails = [
        'total' => 0,
        'details' => [],
    ];

    while ($disposalDetail = oci_fetch_assoc($stmt)) {
        // Check if the soldier has more than one disposal, add a star (*) if true
        $remarks = $disposalDetail['REMARKS'];
        $soldierId = $disposalDetail['SOLDIERID'];

        if (isset($disposalDetails['details'][$soldierId])) {
            $disposalDetails['details'][$soldierId]['REMARKS'] .= ' *';
        } else {
            $disposalDetails['details'][$soldierId] = $disposalDetail;
            $disposalDetails['total']++;
        }
    }

    oci_free_statement($stmt);

    return $disposalDetails;
}

function getAllCompanyData($conn)
{
    $query = "SELECT COMPANYID, COMPANYNAME FROM COMPANY";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);

    $companyData = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $companyData[] = [
            'ID' => $row['COMPANYID'],
            'NAME' => $row['COMPANYNAME'],
        ];
    }

    return $companyData;
}

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    <?= htmlspecialchars("Disposal List") ?>
                </h1>
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
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Company:</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="coyId">
                                <option value="">All Companies</option>
                                <?php
                                $companyData = getAllCompanyData($conn); // Assuming getAllCompanyData returns an array with both ID and Name
                                foreach ($companyData as $company) {
                                    $companyId = $company['ID'];
                                    $companyName = $company['NAME'];
                                    echo "<option value=\"" . $companyId . "\">" . htmlspecialchars($companyName) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <?php
                // Check if the form is submitted
                if (isset($_POST['filterBtn'])) {
                    // Get form values
                    $coyId = $_POST['coyId'] ?? null;
                    $disposalType = $_POST['disposalType'] ?? null;
                    $currentDate = $_POST['currentDate'] ?? null;

                    // Call the findDisposalHolders function
                    $disposalDetails = findDisposalHolders($conn, $coyId, $currentDate, $disposalType);

                    // Print the disposal details
                    printDisposalDetails($disposalDetails);
                }
                ?>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Disposal Type:</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="disposalType"
                                placeholder="Enter disposal type">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Date:</label>
                        <div class="col-sm-4">
                            <input type="date" class="form-control" name="currentDate">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary" name="filterBtn">Filter</button>
                        </div>
                    </div>
                </form>
            </div>



        </div>
</section>

<?php include '../includes/footer.php'; ?>