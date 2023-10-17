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
    $query = "SELECT s.SOLDIERID, c.COMPANYNAME, s.PERSONALCONTACT, s.EMERGENCYCONTACT, s.NAME, r.RANK, lm.LEAVEID, lm.LEAVETYPE, lm.LEAVESTARTDATE, lm.LEAVEENDDATE, lm.AUTHBY
    FROM SOLDIER s
    JOIN COMPANY c ON c.COMPANYID = s.COMPANYID
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


$authid = $soldier['AUTHBY'];

// Prepare and execute the SQL query
$query = "SELECT S.NAME AS AUTHNAME, R.RANK
          FROM SOLDIER S
          JOIN RANKS R ON S.RANKID = R.RANKID
          WHERE S.SOLDIERID = :authid";

$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':authid', $authid);
oci_execute($stmt);

// Fetch the result
if ($row = oci_fetch_assoc($stmt)) {
    $authname = $row['AUTHNAME'];
    $rank = $row['RANK'];

}

// Close the database connection
oci_free_statement($stmt);


// Fetch uploaded image paths for the officer
$query = "SELECT SIGNATURE_PATH FROM UPLOADED_IMAGES WHERE SOLDIER_ID = :soldier_id";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldier['AUTHBY']);
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
<!DOCTYPE html>
<html>

<head>
    <title>Leave Certificate</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin-top: auto;
            padding-top: 30px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
        }

        h3 {
            text-align: center;
            text-decoration: underline;
        }

        .leave-info {
            margin-top: 30px;
            font-size: 14pt;
        }

        .contact-info {
            margin-top: 10px;
        }

        .contact-info ul {
            list-style-type: none;
            padding: 0;
        }

        .contact-info ul li {
            margin-bottom: 5px;
        }

        .sign-block {
            margin-top: 30px;
            margin-right: -120px;
            text-align: right;
        }

        .sign-block img {
            max-width: 100px;
        }

        .signature-line {
            margin-top: -10px;
            margin-bottom: 0;
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
        <table id="table" style="border-collapse: collapse;">
            <tr>
                <td colspan="2">
                    <h6 style="text-align: center;">RESTRICTED</h6>
                    <h3>E-Leave Certificate</h3>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="leave-info">
                        <p style="text-align: justify;">
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
                            Company:
                            <strong>
                                <?php echo $soldier['COMPANYNAME']; ?>
                            </strong>,
                            has been granted
                            <strong>
                                <?php echo $soldier['LEAVETYPE']; ?>
                            </strong>
                            for
                            <strong>
                                <?php echo date_diff(date_create($soldier['LEAVESTARTDATE']), date_create($soldier['LEAVEENDDATE']))->format('%a')+1; ?>
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
                        <div class="contact-info">
                            <p>Contact:</p>
                            <ul>
                                <li>
                                    <?php echo '0' . $soldier['PERSONALCONTACT'] . ' (Personal)'; ?>
                                </li>
                                <li>
                                    <?php echo '0' . $soldier['EMERGENCYCONTACT'] . ' (Emergency)'; ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: middle; text-align: center;">
                    <img src="data:image/png;base64,<?php echo $base64Image; ?>" alt="Barcode">
                </td>
                <td style="vertical-align: middle; text-align: right;">
                    <div class="sign-block">
                        <p class="signature-line" style="text-align: center;">
                            <?php if ($uploadedImages && $uploadedImages['SIGNATURE_PATH']): ?>
                                <img src="<?php echo $uploadedImages['SIGNATURE_PATH']; ?>" alt="Signature">
                            <?php endif; ?>

                        </p>


                        <p class="signature-line" style="text-align: center;">_____________________________</p>
                        <p style="text-align: center;">
                            <?php echo $rank . ' ' . $authname; ?>
                        </p>
                        <p style="text-align: center;">Date:
                            <?php echo date('d M Y'); ?>
                        </p>
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <h6 style="text-align: left;">
                        <ul>
                            <li>This leave certificate is auto-generated.</li>
                            <li>This signature is a digital signature and verified by the company commander.</li>
                            <li>Printed at: <?php echo date('Y-m-d H:i:s'); ?></li>
                        </ul>


                    </h6>
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