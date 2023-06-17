<?php
include 'conn.php';
include 'views/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parade State</title>
    <link rel="stylesheet" href="style.css"> <!-- Replace "style.css" with your actual CSS file name and path -->
    <?php include 'views/head.php'; ?>
</head>
<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Parade State</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <h3>Parade State Report</h3>
                            <p>Tabular format datasheet for parade state:</p>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Company Name</th>
                                        <th>Auth</th>
                                        <th>Granted</th>
                                        <th>Leave</th>
                                        <th>Absent</th>
                                        <th>Present</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch the parade state data from the view
                                    $queryParadeState = "SELECT * FROM parade_state_view";
                                    $stmtParadeState = oci_parse($conn, $queryParadeState);
                                    oci_execute($stmtParadeState);

                                    while ($row = oci_fetch_assoc($stmtParadeState)) {
                                        $companyName = $row['COMPANYNAME'];
                                        $auth = $row['Auth'];
                                        $granted = $row['Granted'];
                                        $leave = $row['Leave'];
                                        $absent = $row['Absent'];
                                        $present = $row['Present'];

                                        echo "<tr>";
                                        echo "<td>$companyName</td>";
                                        echo "<td>$auth</td>";
                                        echo "<td>$granted</td>";
                                        echo "<td>$leave</td>";
                                        echo "<td>$absent</td>";
                                        echo "<td>$present</td>";
                                        echo "</tr>";
                                    }

                                    oci_free_statement($stmtParadeState);
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include 'views/footer.php'; ?>
    </div>
</body>
</html>
