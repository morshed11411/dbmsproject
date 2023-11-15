<?php
// Assuming $conn is your Oracle database connection
include '../includes/connection.php';

include '../includes/header.php';
function getSoldiers($conn, $soldierId = null, $rank = null, $category = null, $onLeave = false, $company = null, $status = null)
{
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

// Example usage:

function displaySoldiersModal($soldiersArray,$name)
{
    // Modal
    echo '        <a href="#" data-toggle="modal" data-target="#soldiersModal'.$name.'">
    ' . count($soldiersArray) . '
    </a>';
    if (count($soldiersArray) > 0) {
        echo '
        <!-- Button trigger modal -->


        <!-- Soldiers Modal -->
        <div class="modal fade" id="soldiersModal'.$name.'" tabindex="-1" role="dialog" aria-labelledby="soldiersModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="soldiersModalLabel">Soldiers List</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
    <!-- Display soldiers list here -->
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Rank</th>
                <th scope="col">Name</th>
                <th scope="col">Trade</th>
                <th scope="col">Company</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($soldiersArray as $soldier) {
            echo '<tr>';
            echo '<td>' . $soldier['RANK'] . '</td>';
            echo '<td>' . $soldier['NAME'] . '</td>';
            echo '<td>' . $soldier['TRADE'] . '</td>';
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
}


//getSoldiers($conn, $soldierId = null, $rank = null, $category = null, $onLeave = false, $company = null, $status = null)
// Example usage:
$soldiersByStatus = getSoldiers($conn, null, null, null, false, null, 'Posted');
$allSoldier = getSoldiers($conn, null, null, null, false, null, null);
$onLeave = getSoldiers($conn, null, 'Lt Col', null, false, null, null);

displaySoldiersModal($allSoldier,'all');
displaySoldiersModal($soldiersByStatus,'posted');
displaySoldiersModal($onLeave,'leave');


include '../includes/footer.php';
?>