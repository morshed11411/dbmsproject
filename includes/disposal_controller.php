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



?>