<?php
//session_start();

include '../includes/connection.php';
include '../includes/head.php';
require 'vendor/autoload.php'; // Include the TCPDF library

use TCPDF as TCPDF;

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

// Create a new TCPDF instance
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Your Creator');
$pdf->SetAuthor('Your Author');
$pdf->SetTitle('Leave Certificate');
$pdf->SetSubject('Leave Certificate');
$pdf->SetKeywords('Leave Certificate, Barcode');

// Add a page
$pdf->AddPage();

// Generate barcode
$barcode = $pdf->getBarcodeHTML($soldier['LEAVEID'], 'C128');

// Output barcode HTML
$pdf->writeHTML('<div>' . $barcode . '</div>', true, 0, true, 0);

// ... (Continue with the rest of your content)

// Output PDF content
$pdfContent = $pdf->Output('', 'S');

// Output PDF content for download
echo '<button onclick="downloadPDF()">Download PDF</button>';
echo '<script>
        function downloadPDF() {
            var blob = new Blob([\'', $pdfContent, '\'], { type: "application/pdf" });
            var link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "leave_certificate.pdf";
            link.click();
        }
      </script>';
?>
