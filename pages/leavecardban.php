<?php
session_start();

include '../includes/connection.php';

// Fetch soldier details from the database
$query = "SELECT s.SOLDIERID, s.NAME, r.RANK, lm.LEAVETYPE, lm.LEAVESTARTDATE, lm.LEAVEENDDATE 
FROM SOLDIER s
JOIN RANKS r ON s.RANKID = r.RANKID
JOIN LEAVEMODULE lm ON s.SOLDIERID = lm.SOLDIERID
WHERE s.SOLDIERID = :soldier_id
ORDER BY lm.LEAVESTARTDATE DESC
FETCH FIRST ROW ONLY
";

$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $_GET['soldier_id']);
oci_execute($stmt);

$soldier = oci_fetch_assoc($stmt);

oci_free_statement($stmt);
oci_close($conn);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Leave Certificate</title>
    <style>
        @import url('https://fonts.maateen.me/adorsho-lipi/font.css');

        body {
            font-family: 'AdorshoLipi', Arial, sans-serif !important;
        }

        h1 {
            text-align: center;
        }

        .leave-info {
            margin-top: 30px;
        }

        table {
            border-collapse: collapse;
            margin: 20px auto;
        }

        table td,
        table th {
            border: 1px solid black;
            padding: 5px;
        }

        .sign-block {
            margin-top: 50px;
            text-align: right;
        }
    </style>
</head>

<body style="font-family: NikoshBAN;">
    <h1 style="text-align: center;">প্রত্যয়ন পত্র</h1>

    <div class="leave-info">
        <p>
            প্রত্যয়ন করা যাচ্ছে যে,
            পদাবী/পেশা:
            <?php echo $soldier['RANKNAME']; ?>
        </p>
        <p>
            নামঃ
            <?php echo $soldier['NAME']; ?>
        </p>
        <p>
            কোম্পানীঃ
            <?php echo $soldier['COMPANYNAME']; ?>
        </p>
        <p>
            ৩ সিগন্যাল ব্যাটালিয়ন, শহীদ সালাহউদ্দিন সেনানিবাস। তাকে তারিখে সময় হইতে ঘটিকা পর্যন্ত বাহিরে যাওয়ার জন্য
            অনুমতি প্রদান করা হলো।
        </p>
        <p>
            স্থানঃ শহীদ সালাহউদ্দিন সেনানিবাস
        </p>
        <p>
            তারিখঃ
            <?php echo date('Y'); ?>
        </p>
    </div>

    <script>
        // Automatically turn on print
        window.onload = function () {
            window.print();
        };
    </script>
</body>


</html>