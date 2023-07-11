<?php
session_start();

include '../includes/connection.php';

include '../includes/head.php';
require_once('../assets/phpqrcode/qrlib.php'); // Include the QR Code library

// Check if the soldier_id or leaveid parameter exists in the URL
if (isset($_GET['soldier_id'])) {
    $soldierId = $_GET['soldier_id'];
    // Fetch soldier details by soldier_id from the database
    $query = "SELECT s.SOLDIERID, s.NAME, r.RANK, lm.LEAVEID, lm.LEAVETYPE, lm.LEAVESTARTDATE, lm.LEAVEENDDATE 
    FROM SOLDIER s
    JOIN RANKS r ON s.RANKID = r.RANKID
    JOIN LEAVEMODULE lm ON s.SOLDIERID = lm.SOLDIERID
    WHERE s.SOLDIERID = :soldier_id
    ORDER BY lm.LEAVEID DESC
    FETCH FIRST ROW ONLY";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierId);
    oci_execute($stmt);

    $soldier = oci_fetch_assoc($stmt);

    oci_free_statement($stmt);
} elseif (isset($_GET['leaveid'])) {
    $leaveId = $_GET['leaveid'];
    // Fetch soldier details by leaveid from the database
    $query = "SELECT s.SOLDIERID, s.NAME, r.RANK, lm.LEAVEID, lm.LEAVETYPE, lm.LEAVESTARTDATE, lm.LEAVEENDDATE 
    FROM SOLDIER s
    JOIN RANKS r ON s.RANKID = r.RANKID
    JOIN LEAVEMODULE lm ON s.SOLDIERID = lm.SOLDIERID
    WHERE lm.LEAVEID = :leave_id";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':leave_id', $leaveId);
    oci_execute($stmt);

    $soldier = oci_fetch_assoc($stmt);

    oci_free_statement($stmt);
} else {
    // If neither soldier_id nor leaveid parameter is provided in the URL, redirect or display an error message
    // Redirect example:
    // header("Location: error.php");
    // exit;
    echo "Soldier ID or Leave ID not specified.";
    exit;
}

// Fetch uploaded image paths for the officer
$query = "SELECT SIGNATURE_PATH FROM UPLOADED_IMAGES WHERE SOLDIER_ID = :soldier_id";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $_SESSION['userid']);
oci_execute($stmt);

$uploadedImages = oci_fetch_assoc($stmt);

oci_free_statement($stmt);
oci_close($conn);

ob_start();
QRcode::png($soldier['LEAVEID'], null, QR_ECLEVEL_H, 3);
$imageData = ob_get_contents();
ob_end_clean();
$base64Image = base64_encode($imageData);

?>
<html>

<head>
    <title>Leave Certificate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin-top: auto;

            padding-top: 30px;
        }

        h1 {
            text-align: center;
        }

        .leave-info {
            margin-top: 30px;
            font-size: 14pt;
        }

        .sign-block {
            margin-top: 30px;
            text-align: right;
        }

        /* Remove table border */
        #table {
            border-collapse: collapse;
            border: none;
        }

        /* Remove table cell borders */
        #table td,
        #table th {
            border: none;
        }

        @media print {

            /* Hide print button on print view */
            .print-button {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <table id="table" class="table">
            <tr>
                <td colspan="2">
                    <h6 style="text-align: center;">RESTRICTED</h6>
                    <br>
                    <u>

                        <h3 style="text-align: center;">E-Leave Certificate</h3>
                    </u>
                </td>
            </tr>
            <tr>

                <td colspan="2">
                    <div class="leave-info">
                        <p>
                            This is to certify that No:
                            <strong>
                                <?php echo $soldier['SOLDIERID']; ?>
                            </strong>,
                            Rank:
                            <strong>
                                <?php echo $soldier['RANK']; ?>
                            </strong>,
                            Name:
                            <strong>
                                <?php echo $soldier['NAME']; ?>
                            </strong>,
                            has been granted
                            <strong>
                                <?php echo $soldier['LEAVETYPE']; ?>
                            </strong>
                            for
                            <strong>
                                <?php echo date_diff(date_create($soldier['LEAVESTARTDATE']), date_create($soldier['LEAVEENDDATE']))->format('%a'); ?>
                            </strong>
                            days,
                            starting from
                            <strong>
                                <?php echo $soldier['LEAVESTARTDATE']; ?>
                            </strong> and ending on
                            <strong>
                                <?php echo $soldier['LEAVEENDDATE']; ?>
                            </strong>.
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <img src="data:image/png;base64,<?php echo $base64Image; ?>" alt="Barcode"
                        style="max-width: 140px;">
                </td>
                <td style="text-align: right;">
                    <div class="sign-block" style="text-align: right;">
                        <div style="display: inline-block; text-align: center;">
                            <?php if ($uploadedImages && $uploadedImages['SIGNATURE_PATH']): ?>
                                <img src="<?php echo $uploadedImages['SIGNATURE_PATH']; ?>" alt="Signature"
                                    style="max-width: 100px;">
                            <?php endif; ?>
                            <p style="margin-top: 0px; margin-bottom: 0px;">_____________________________</p>

                            <p>
                                <?php echo $_SESSION['username']; ?> <br> 
                                Date: <?php echo date('d M Y'); ?>
                            </p>
                        </div>
                    </div>

                </td>

            </tr>
            <tr>
                <td colspan="2">
                    <h6 style="text-align: center;">RESTRICTED</h6>
                </td>
            </tr>
        </table>
    </div>


    <script>
        // Automatically turn on print
        window.onload = function () {
            window.print();
        };
    </script>
</body>

</html>