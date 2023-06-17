<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>
<?php include 'views/auth.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Medical Disposal Today</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h3>Medical Disposal Holders Today</h3>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Soldier ID</th>
                                                <th>Name</th>
                                                <th>Rank</th>
                                                <th>Trade</th>
                                                <th>Company Name</th>
                                                <th>Disposal Type</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include 'conn.php';

                                            $query = "SELECT SOLDIERID, NAME, RANK, TRADE, COMPANYNAME, DISPOSALTYPE, STARTDATE, ENDDATE
                                                      FROM todays_disposal_holder";
                                            $stmt = oci_parse($conn, $query);
                                            oci_execute($stmt);

                                            while ($row = oci_fetch_assoc($stmt)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['SOLDIERID'] . "</td>";
                                                echo "<td>" . $row['NAME'] . "</td>";
                                                echo "<td>" . $row['RANK'] . "</td>";
                                                echo "<td>" . $row['TRADE'] . "</td>";
                                                echo "<td>" . $row['COMPANYNAME'] . "</td>";
                                                echo "<td>" . $row['DISPOSALTYPE'] . "</td>";
                                                echo "<td>" . $row['STARTDATE'] . "</td>";
                                                echo "<td>" . $row['ENDDATE'] . "</td>";
                                                echo "</tr>";
                                            }
                                            oci_free_statement($stmt);
                                            oci_close($conn);
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include 'views/footer.php'; ?>

    </div>

</body>

</html>