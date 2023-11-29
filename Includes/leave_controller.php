<?php
include '../includes/connection.php';


$startDate = date('Y-m-d', strtotime('-7 days'));
$endDate = date('Y-m-t');  // the end date will be the end date of the month 
// Handle filter button 
if (isset($_POST['filterBtn'])) {
    $startDate = $_POST['startDate'] ?? date('Y-m-d', strtotime('-7 days'));
    $endDate = $_POST['endDate'] ?? date('Y-m-t');


    $_SESSION['success'] = 'Showing leave state from: ' . $startDate. ' to '.$endDate;
}
function getLeaveTypes($conn)
{
    $leaveTypes = [];

    $leaveQuery = "SELECT LEAVETYPEID, LEAVETYPE FROM LEAVETYPE WHERE SHOW_LEAVE = 1";
    $leaveStmt = oci_parse($conn, $leaveQuery);
    oci_execute($leaveStmt);

    while ($leaveRow = oci_fetch_assoc($leaveStmt)) {
        $leaveTypes[$leaveRow['LEAVETYPEID']] = $leaveRow['LEAVETYPE'];
    }

    oci_free_statement($leaveStmt);

    return $leaveTypes;
}



function getLeaveInfo($conn, $coyId = null, $currentDate = null, $leaveType = null, $soldierId = null)
{
    $currentDate = $currentDate ?: date('Y-m-d');

    $query = "SELECT S.SOLDIERID, R.RANK, S.NAME, T.TRADE, C.COMPANYNAME,
    LT.LEAVETYPE, LM.LEAVEID, LM.LEAVESTARTDATE, LM.LEAVEENDDATE,
    LM.STATUS AS REMARKS
FROM SOLDIER S
JOIN LEAVEMODULE LM ON S.SOLDIERID = LM.SOLDIERID
JOIN LEAVETYPE LT ON LM.LEAVETYPEID = LT.LEAVETYPEID
JOIN RANKS R ON S.RANKID = R.RANKID
JOIN TRADE T ON S.TRADEID = T.TRADEID
JOIN COMPANY C ON S.COMPANYID = C.COMPANYID
WHERE (LT.LEAVETYPE IS NOT NULL) AND LM.STATUS = 'On Leave' AND LM.ONLEAVE = 1";

    if ($coyId !== null) {
        $query .= " AND C.COMPANYID = :coyId";
    }

    if ($leaveType !== null) {
        // Adjust the join condition to use the correct alias
        $query .= " AND LT.LEAVETYPE = :leaveType";
    }

    if ($currentDate !== null) {
        if ($currentDate !== 'all') {
            $query .= " AND TRUNC(LM.LEAVESTARTDATE) <= TO_DATE(:currentDate, 'YYYY-MM-DD') AND (LM.LEAVEENDDATE IS NULL OR TRUNC(LM.LEAVEENDDATE) >= TO_DATE(:currentDate, 'YYYY-MM-DD'))";
        }
    }

    if ($soldierId !== null) {
        $query .= " AND S.SOLDIERID = :soldierId";
    }
    $query .= " ORDER BY LM.LEAVEID DESC";

    $stmt = oci_parse($conn, $query);

    if ($coyId !== null) {
        oci_bind_by_name($stmt, ':coyId', $coyId);
    }

    if ($leaveType !== null) {
        oci_bind_by_name($stmt, ':leaveType', $leaveType);
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



function printLeaveSoldierList($soldiersArray, $id, $name = null)
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

//print_r(leaveDisposal($conn,1,null,null,null));

function getLeaveCountsByDateRange($conn, $companies, $startDate, $endDate)
{
    $leaveCountsByDate = array();

    // Loop through each date in the date range
    $currentDate = new DateTime($startDate);
    $endDateObj = new DateTime($endDate);

    while ($currentDate <= $endDateObj) {
        $date = $currentDate->format('Y-m-d');

        // Loop through each company and get leave count for the current date
        foreach ($companies as $company) {
            $leaveCount = getLeaveInfo($conn, $company['ID'], $date, null, null);

            // Store leave count in the 2D array
            $leaveCountsByDate[$date][$company['ID']] = count($leaveCount);
        }

        // Move to the next date
        $currentDate->modify('+1 day');
    }

    return $leaveCountsByDate;
}

?>