<?php
include '../includes/connection.php';

// Function to get disposal types
function getDisposalTypes($conn)
{
    $disposalTypes = [];
    $disposalQuery = "SELECT DISPOSALID, DISPOSALTYPE FROM DISPOSALTYPE WHERE SHOW_DISPOSAL = 1";
    $disposalStmt = oci_parse($conn, $disposalQuery);
    oci_execute($disposalStmt);

    while ($disposalRow = oci_fetch_assoc($disposalStmt)) {
        $disposalTypes[$disposalRow['DISPOSALID']] = $disposalRow['DISPOSALTYPE'];
    }

    oci_free_statement($disposalStmt);

    return $disposalTypes;
}

function medicalDisposal($conn, $coyId = null, $currentDate = null, $disposalType = null, $soldierId = null)
{
    $currentDate = $currentDate ?: date('Y-m-d');

    $query = "SELECT DISTINCT S.SOLDIERID, M.MEDICALID, R.RANK, S.NAME, T.TRADE, C.COMPANYNAME,
    D.DISPOSALTYPE, M.STARTDATE, M.ENDDATE,
    M.REASON AS REMARKS
FROM SOLDIER S
JOIN MEDICALINFO M ON S.SOLDIERID = M.SOLDIERID
JOIN DISPOSALTYPE D ON M.DISPOSALID = D.DISPOSALID
JOIN RANKS R ON S.RANKID = R.RANKID
JOIN TRADE T ON S.TRADEID = T.TRADEID
JOIN COMPANY C ON S.COMPANYID = C.COMPANYID
WHERE (D.DISPOSALTYPE IS NOT NULL)";

    if ($coyId !== null) {
        $query .= " AND C.COMPANYID = :coyId";
    }

    if ($disposalType !== null) {
        // Adjust the join condition to use the correct alias
        $query .= " AND D.DISPOSALTYPE = :disposalType";
    }

    if ($currentDate !== null) {
        if ($currentDate !== 'all') {
            $query .= " AND TRUNC(M.STARTDATE) <= TO_DATE(:currentDate, 'YYYY-MM-DD') AND (M.ENDDATE IS NULL OR TRUNC(M.ENDDATE) >= TO_DATE(:currentDate, 'YYYY-MM-DD'))";
        }
    }

    if ($soldierId !== null) {
        $query .= " AND S.SOLDIERID = :soldierId";
    }
    $query .= " ORDER BY M.MEDICALID DESC";


    $stmt = oci_parse($conn, $query);

    if ($coyId !== null) {
        oci_bind_by_name($stmt, ':coyId', $coyId);
    }

    if ($disposalType !== null) {
        oci_bind_by_name($stmt, ':disposalType', $disposalType);
    }

    if ($currentDate !== null) {
        if ($currentDate !== 'all') {
            oci_bind_by_name($stmt, ':currentDate', $currentDate);
        }
    }

    if ($soldierId !== null) {
        oci_bind_by_name($stmt, ':soldierId', $soldierId);
    }

    oci_execute($stmt);

    $allSoldiers = [];
    while ($soldier = oci_fetch_assoc($stmt)) {
        $allSoldiers[] = $soldier;
    }

    oci_free_statement($stmt);

    return $allSoldiers;
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

function printSoldierList($soldiersArray, $id, $name = null)
{
    $count = count($soldiersArray);

    echo '<a href="#" data-toggle="modal" data-target="#soldiersModal' . $id . '">
          ' . $count . '
          </a>';

    if ($count > 0) {
        echo '<div class="modal fade" id="soldiersModal' . $id . '" tabindex="-1" role="dialog" aria-labelledby="soldiersModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                    <div class="modal-header text-center">
                    <h5 class="modal-title" id="soldiersModalLabel">' . $name . ' List</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        <table id="" class="table table-bordered">
                        <thead>
                                    <tr>
                                        <th scope="col">Ser</th>
                                        <th scope="col">Rank</th>
                                        <th scope="col">Trade</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Company</th>
                                        <th scope="col">Start Date</th>
                                        <th scope="col">End Date</th>


                                    </tr>
                                </thead>
                                <tbody>';
        $i = 0;
        foreach ($soldiersArray as $soldier) {
            echo '<tr>';
            echo '<td>' . ++$i . '</td>';
            echo '<td>' . $soldier['RANK'] . '</td>';
            echo '<td>' . $soldier['TRADE'] . '</td>';
            echo '<td>' . $soldier['NAME'] . '</td>';
            echo '<td>' . $soldier['COMPANYNAME'] . '</td>';
            echo '<td>' . $soldier['STARTDATE'] . '</td>';
            echo '<td>' . $soldier['ENDDATE'] . '</td>';


            echo '</tr>';
        }

        echo '</tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>';
    }

    return $count;
}


function addDisposal($soldierID, $disposalType, $startDate, $endDate, $reason)
{
    global $conn;

    // Fetch DISPOSALID based on DISPOSALTYPE
    $query = "SELECT DISPOSALID FROM DISPOSALTYPE WHERE DISPOSALTYPE = :disposal_type";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':disposal_type', $disposalType);
    oci_execute($stmt);

    $disposalIdResult = oci_fetch_assoc($stmt);
    $disposalId = $disposalIdResult['DISPOSALID'];

    // Insert into MEDICALINFO
    $insertQuery = "INSERT INTO MEDICALINFO (SOLDIERID, DISPOSALID, STARTDATE, ENDDATE, REASON) 
              VALUES (:soldier_id, :disposal_id, TO_DATE(:start_date, 'YYYY-MM-DD'), 
              TO_DATE(:end_date, 'YYYY-MM-DD'), :reason)";
    $insertStmt = oci_parse($conn, $insertQuery);
    oci_bind_by_name($insertStmt, ':soldier_id', $soldierID);
    oci_bind_by_name($insertStmt, ':disposal_id', $disposalId); // Use the fetched DISPOSALID
    oci_bind_by_name($insertStmt, ':start_date', $startDate);
    oci_bind_by_name($insertStmt, ':end_date', $endDate);
    oci_bind_by_name($insertStmt, ':reason', $reason);

    $result = oci_execute($insertStmt);
    oci_free_statement($stmt);
    oci_free_statement($insertStmt);

    if ($result) {
        $_SESSION['success'] = "Report sick sent successfully.";
    } else {
        $editError = oci_error($insertStmt);
        $_SESSION['error'] = "Failed to update disposal information: " . $editError['message'];
    }
}
function updateDisposal($disposalID, $disposalType = null, $endDate = null, $reason = null)
{
    global $conn;

    // Fetch DISPOSALID based on DISPOSALTYPE if it's provided
    $disposalId = null;
    if ($disposalType !== null) {
        $query = "SELECT DISPOSALID FROM DISPOSALTYPE WHERE DISPOSALTYPE = :disposal_type";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':disposal_type', $disposalType);
        oci_execute($stmt);

        $disposalIdResult = oci_fetch_assoc($stmt);
        $disposalId = $disposalIdResult['DISPOSALID'];
    }

    $query = "UPDATE MEDICALINFO SET";

    // Conditionally include DISPOSALID in the update if it's not null
    if ($disposalId !== null) {
        $query .= " DISPOSALID = :disposal_id,";
    }

    $query .= " ENDDATE = TO_DATE(:end_date, 'YYYY-MM-DD')";

    // Conditionally include REASON in the update if it's not null
    if ($reason !== null) {
        $query .= ", REASON = :reason";
    }

    $query .= " WHERE MEDICALID = :medical_id";

    $stmt = oci_parse($conn, $query);

    // Bind DISPOSALID parameter only if it's not null
    if ($disposalId !== null) {
        oci_bind_by_name($stmt, ':disposal_id', $disposalId);
    }

    oci_bind_by_name($stmt, ':end_date', $endDate);

    // Bind REASON parameter only if it's not null
    if ($reason !== null) {
        oci_bind_by_name($stmt, ':reason', $reason);
    }

    oci_bind_by_name($stmt, ':medical_id', $disposalID);

    $result = oci_execute($stmt);
    oci_free_statement($stmt);

    if ($result) {
        $_SESSION['success'] = "Disposal information updated successfully.";
    } else {
        $editError = oci_error($stmt);
        $_SESSION['error'] = "Failed to update disposal information: " . $editError['message'];
    }
}


function deleteDisposal($deleteDisposalID)
{
    global $conn;

    $deleteQuery = "DELETE FROM MEDICALINFO WHERE MEDICALID = :disposal_id";
    $deleteStmt = oci_parse($conn, $deleteQuery);

    oci_bind_by_name($deleteStmt, ':disposal_id', $deleteDisposalID);

    $deleteResult = oci_execute($deleteStmt);

    if ($deleteResult) {
        $_SESSION['success'] = "Disposal information deleted successfully.";
    } else {
        $deleteError = oci_error($deleteStmt);
        $_SESSION['error'] = "Failed to delete disposal information: " . $deleteError['message'];
    }

    oci_free_statement($deleteStmt);
}


function calculateDisposalCount($conn, $disposalTypes, $soldierId)
{
    $totalDays = [];
    foreach ($disposalTypes as $disposalType) {
        $total[$disposalType] = 0;
        $disposalCountList = medicalDisposal($conn, null, 'all', $disposalType, $soldierId);
        $totalDays[$disposalType] = 0;
        foreach ($disposalCountList as $disposal) {
            $startDate = new DateTime($disposal['STARTDATE']);
            $endDate = new DateTime($disposal['ENDDATE']);
            if ($startDate && $endDate) {
                $duration = $startDate->diff($endDate)->format("%a");
                if ($duration == 0) {
                    $duration = 1; // Set the duration to 1 day
                }

                $totalDays[$disposalType] += $duration;
            }
        }
    }
    return $totalDays;
}


?>