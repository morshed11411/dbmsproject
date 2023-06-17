<?php
include 'conn.php';
include 'views/auth.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'views/head.php'; ?>
</head>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                <button type="button" class="btn btn-warning float-right" onclick="window.print()">Print Parade State</button>

                    <h1>Parade State</h1>
                </div>

            </div>
            <section class="content">
                
                <div class="container-fluid">
                    <div class="card">

                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="text-left">
                                    <h3>Parade State Report</h3>
                                </div>
                                <div class="text-right">
                                    <?php
                                    date_default_timezone_set("Your/Timezone"); // Replace "Your/Timezone" with your desired timezone
                                    $currentDate = date("j F, Y"); // Format the current date as desired
                                    echo "<h3>Date: " . $currentDate . "</h3>";
                                    ?>
                                </div>
                            </div>

                            <!-- Rest of the code -->
                        </div>


                        
                        <table class="table" id="paradeState">
                            <thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>Auth</th>
                                    <th>Granted</th>
                                    <th>Leave</th>
                                    <th>Disposal</th>
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

                                $totalAuth = 0;
                                $totalGranted = 0;
                                $totalLeave = 0;
                                $totalMedicalDisposal = 0;
                                $totalAbsent = 0;
                                $totalPresent = 0;

                                while ($row = oci_fetch_assoc($stmtParadeState)) {
                                    $companyName = $row['COMPANYNAME'];
                                    $auth = $row['Auth'];
                                    $granted = $row['Granted'];
                                    $leave = $row['Leave'];
                                    $medicalDisposal = $row['MedicalDisposal'];
                                    $absent = $row['Absent'];
                                    $present = $row['Present'];

                                    echo "<tr>";
                                    echo "<td>$companyName</td>";
                                    echo "<td>$auth</td>";
                                    echo "<td>$granted</td>";
                                    echo "<td>$leave</td>";
                                    echo "<td>$medicalDisposal</td>";
                                    echo "<td>$absent</td>";
                                    echo "<td>$present</td>";
                                    echo "</tr>";

                                    // Calculate the total values
                                    $totalAuth += $auth;
                                    $totalGranted += $granted;
                                    $totalLeave += $leave;
                                    $totalMedicalDisposal += $medicalDisposal;
                                    $totalAbsent += $absent;
                                    $totalPresent += $present;
                                }

                                oci_free_statement($stmtParadeState);

                                // Output the total row
                                echo "<tr class='total-row'>";
                                echo "<td>Total</td>";
                                echo "<td>$totalAuth</td>";
                                echo "<td>$totalGranted</td>";
                                echo "<td>$totalLeave</td>";
                                echo "<td>$totalMedicalDisposal</td>";
                                echo "<td>$totalAbsent</td>";
                                echo "<td>$totalPresent</td>";
                                echo "</tr>";
                                ?>
                            </tbody>
                        </table>


                    </div>
                </div>
            </section>
        </div>

        <?php include 'views/footer.php'; ?>
    </div>
</body>


</html>