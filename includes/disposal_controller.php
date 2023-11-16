<?php
// Assuming $conn is your Oracle database connection
include '../includes/connection.php';


function medicalDisposal($conn, $coyId = null, $currentDate = null, $disposalType = null, $soldierId = null)
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


?>
