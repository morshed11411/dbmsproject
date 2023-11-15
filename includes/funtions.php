<?php
// Assuming $conn is your Oracle database connection
include '../includes/connection.php';

function getSoldiers($conn, $soldierId = null, $rank = null, $category = null, $onLeave = false, $company = null, $status = null) {
    $query = "SELECT S.SOLDIERID, R.RANK, S.NAME, T.TRADE, C.COMPANYNAME
              FROM SOLDIER S
              JOIN RANKS R ON S.RANKID = R.RANKID
              JOIN TRADE T ON S.TRADEID = T.TRADEID
              JOIN COMPANY C ON S.COMPANYID = C.COMPANYID";

    // Add WHERE clause based on parameters
    $conditions = [];

    if ($soldierId !== null) {
        $conditions[] = "S.SOLDIERID = :soldierId";
    } elseif ($rank !== null) {
        $conditions[] = "R.RANK = :rank";
    } elseif ($category !== null) {
        if ($category === 'Officer') {
            $conditions[] = "R.RANK IN ('Lt Col', 'Maj', 'Capt', 'Lt', '2Lt')";
        } elseif ($category === 'JCO') {
            $conditions[] = "R.RANK IN ('H Capt', 'H Lt', 'MWO', 'SWO', 'WO')";
        } elseif ($category === 'ORS') {
            $conditions[] = "R.RANK NOT IN ('Lt Col', 'Maj', 'Capt', 'Lt', '2Lt', 'H Capt', 'H Lt', 'MWO', 'SWO', 'WO')";
        }
    }

    if ($onLeave) {
        $conditions[] = "S.SOLDIERID IN (SELECT SOLDIER.SOLDIERID FROM LEAVEMODULE 
                                        JOIN SOLDIER ON LEAVEMODULE.SOLDIERID = SOLDIER.SOLDIERID 
                                        WHERE ONLEAVE = 1)";
    }

    if ($company !== null) {
        $conditions[] = "C.COMPANYNAME = :company";
    }

    if ($status !== null) {
            $conditions[] = "S.SOLDIERID IN (SELECT SOLDIER_ID FROM SOLDIERSTATUS WHERE STATUSID IN (SELECT STATUSID FROM SERVINGSTATUS WHERE SERVINGTYPE = :status))";
        
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = oci_parse($conn, $query);

    // Bind parameters
    if ($soldierId !== null) {
        oci_bind_by_name($stmt, ":soldierId", $soldierId);
    } elseif ($rank !== null) {
        oci_bind_by_name($stmt, ":rank", $rank);
    } elseif ($company !== null) {
        oci_bind_by_name($stmt, ":company", $company);
    } elseif ($status !== null) {
        oci_bind_by_name($stmt, ":status", $status);
    }

    oci_execute($stmt);

    $allSoldiers = [];
    while ($soldier = oci_fetch_assoc($stmt)) {
        $allSoldiers[] = $soldier;
    }

    return $allSoldiers;
}


// Assuming $conn is your Oracle database connection

function findDisposalHolders($conn, $currentDate = null, $disposalType = null) {
    // If $currentDate is not provided, use the current date
    $currentDate = $currentDate ? $currentDate : date('Y-m-d');

    // Query to find disposal holders
    $query = "SELECT S.SOLDIERID, S.NAME, T.TRADE, C.COMPANYNAME, 
              M.DISPOSALTYPE, M.STARTDATE, M.ENDDATE,
              CASE 
                  WHEN M.DISPOSALTYPE = 'R/S' THEN 'Rest/Sick Leave'
                  WHEN M.DISPOSALTYPE = 'CMH' THEN 'Admitted in CMH'
                  ELSE 'Unknown Disposal'
              END AS REMARKS
              FROM SOLDIER S
              JOIN MEDICALINFO M ON S.SOLDIERID = M.SOLDIERID
              JOIN TRADE T ON S.TRADEID = T.TRADEID
              JOIN COMPANY C ON S.COMPANYID = C.COMPANYID
              WHERE M.DISPOSALTYPE IS NOT NULL
              AND TRUNC(M.STARTDATE) <= TO_DATE(:current_date, 'YYYY-MM-DD')
              AND (M.ENDDATE IS NULL OR TRUNC(M.ENDDATE) >= TO_DATE(:current_date, 'YYYY-MM-DD'))";

    // Apply filters if provided
    if ($disposalType !== null) {
        $query .= " AND M.DISPOSALTYPE = :disposal_type";
    }

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':current_date', $currentDate);
    
    if ($disposalType !== null) {
        oci_bind_by_name($stmt, ':disposal_type', $disposalType);
    }

    oci_execute($stmt);

    $disposalDetails = [
        'total' => 0,
        'details' => [],
    ];

    while ($disposalDetail = oci_fetch_assoc($stmt)) {
        // Calculate duration
        $startDate = new DateTime($disposalDetail['STARTDATE']);
        $endDate = $disposalDetail['ENDDATE'] ? new DateTime($disposalDetail['ENDDATE']) : new DateTime($currentDate);

        $duration = $endDate->diff($startDate)->format('%a days');

        // Add details to the result
        $disposalDetails['details'][] = [
            'SOLDIERID' => $disposalDetail['SOLDIERID'],
            'NAME' => $disposalDetail['NAME'],
            'TRADE' => $disposalDetail['TRADE'],
            'COMPANYNAME' => $disposalDetail['COMPANYNAME'],
            'DISPOSALTYPE' => $disposalDetail['DISPOSALTYPE'],
            'REMARKS' => $disposalDetail['REMARKS'],
            'DURATION' => $duration,
        ];

        // Count each disposal type
        $disposalTypeCount = $disposalDetail['DISPOSALTYPE'];
        $disposalDetails['total']++;
        if (!isset($disposalDetails[$disposalTypeCount])) {
            $disposalDetails[$disposalTypeCount] = 1;
        } else {
            $disposalDetails[$disposalTypeCount]++;
        }
    }

    oci_free_statement($stmt);

    return $disposalDetails;
}

// Example usage:

// Call the function to find all disposal holders for today
$allDisposalHoldersToday = findDisposalHolders($conn);

// Output the result
if ($allDisposalHoldersToday['total'] > 0) {
    echo "All Disposal holder(s) for today:\n";
    foreach ($allDisposalHoldersToday['details'] as $detail) {
        echo "SOLDIERID: {$detail['SOLDIERID']}, NAME: {$detail['NAME']}, TRADE: {$detail['TRADE']}, COMPANY: {$detail['COMPANYNAME']}, ";
        echo "DISPOSAL TYPE: {$detail['DISPOSALTYPE']}, REMARKS: {$detail['REMARKS']}, DURATION: {$detail['DURATION']}\n";
    }

    echo "\nDisposal Type Counts:\n";
    foreach ($allDisposalHoldersToday as $type => $count) {
        if ($type !== 'total' && $count > 0) {
            echo "{$type}: {$count}\n";
        }
    }
} else {
    echo "No disposal holder for today.\n";
}

// Call the function to find disposal holders of a specific type for today
$specificDisposalTypeHoldersToday = findDisposalHolders($conn, null, 'R/S');

// Output the result
if ($specificDisposalTypeHoldersToday['total'] > 0) {
    echo "\nR/S Disposal holder(s) for today:\n";
    foreach ($specificDisposalTypeHoldersToday['details'] as $detail) {
        echo "SOLDIERID: {$detail['SOLDIERID']}, NAME: {$detail['NAME']}, TRADE: {$detail['TRADE']}, COMPANY: {$detail['COMPANYNAME']}, ";
        echo "DISPOSAL TYPE: {$detail['DISPOSALTYPE']}, REMARKS: {$detail['REMARKS']}, DURATION: {$detail['DURATION']}\n";
    }

    echo "\nR/S Disposal Type Counts:\n";
    foreach ($specificDisposalTypeHoldersToday as $type => $count) {
        if ($type !== 'total' && $count > 0) {
            echo "{$type}: {$count}\n";
        }
    }
} else {
    echo "No R/S disposal holder for today.\n";
}
// Example usage:


// 3. Get soldiers by serving status
$soldiersByStatus = getSoldiers($conn, null, null, null, false, null, 'Posted');
print_r($soldiersByStatus);

?>
