<?php
// Assuming $conn is your Oracle database connection
include '../includes/connection.php';


function getAllRank($conn)
{
    $query = "SELECT RANKID, RANK FROM RANKS";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);

    $rankData = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $rankData[] = [
            'ID' => $row['RANKID'],
            'NAME' => $row['RANK'],
        ];
    }

    return $rankData;
}


// Example usage:
$ranks = getAllRank($conn);

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
        $conditions[] = "C.COMPANYID = :company";
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


function printAllSoldierList($soldiersArray, $id, $name = null)
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


$postedTotal=getSoldiers($conn, null, null,  null, false, null, null) ;
$allOfficer=getSoldiers($conn, null, null,  'Officer', false, null, null) ;
$allJCO=getSoldiers($conn, null, null,  'JCO', false, null, null) ;
$allORS=getSoldiers($conn, null, null,  'ORS', false, null, null) ;



function getManpowerByCompany($conn) {
    $manpowerByCompany = array();

    // Query to retrieve manpower and company information
    $queryAuthorization = "SELECT MANPOWER, COMPANYID FROM AUTHORIZATION";
    $stmtAuthorization = oci_parse($conn, $queryAuthorization);
    oci_execute($stmtAuthorization);

    // Fetch the results
    while ($row = oci_fetch_assoc($stmtAuthorization)) {
        $companyId = $row['COMPANYID'];
        $manpower = $row['MANPOWER'];

        // Store the manpower information in the array
        $manpowerByCompany[$companyId] = $manpower;
    }

    // Close the statement
    oci_free_statement($stmtAuthorization);

    return $manpowerByCompany;
}

// Example usage
$manpowerData = getManpowerByCompany($conn);



?>
