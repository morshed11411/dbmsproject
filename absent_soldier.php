<!DOCTYPE html>
<html lang="en">
<?php
include 'views/head.php';
include 'views/auth.php';
?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Absent Soldiers</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Rank</th>
                                                <th>Company</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include 'conn.php'; // Include the conn.php file for database connection
                                            
                                            $query = "SELECT sp.SoldierID, sp.Name, sp.Rank, sp.CompanyName, 'ERE' AS Reason 
          FROM SoldierProfile sp
          WHERE sp.ERE = 'Yes'
          UNION
          SELECT sp.SoldierID, sp.Name, sp.Rank, sp.CompanyName, 'Temporary Command' AS Reason 
          FROM SoldierProfile sp
          WHERE sp.TemporaryCommand = 'Yes'
          UNION
          SELECT sp.SoldierID, sp.Name, sp.Rank, sp.CompanyName, 'AWOL' AS Reason 
          FROM SoldierProfile sp
          WHERE sp.ServingStatus = 'AWOL'
          UNION
          SELECT tl.SoldierID, sp.Name, sp.Rank, sp.CompanyName, 'Leave' AS Reason 
          FROM TodaysLeaveView tl
          INNER JOIN SoldierProfile sp ON tl.SoldierID = sp.SoldierID
          UNION
          SELECT md.SoldierID, sp.Name, sp.Rank, sp.CompanyName, 'Medical Disposal' AS Reason 
          FROM MedicalDisposal md
          INNER JOIN SoldierProfile sp ON md.SoldierID = sp.SoldierID";

                                            $stmt = oci_parse($conn, $query);
                                            oci_execute($stmt);

                                            while ($row = oci_fetch_assoc($stmt)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['SOLDIERID'] . "</td>";
                                                echo "<td>" . $row['NAME'] . "</td>";
                                                echo "<td>" . $row['RANK'] . "</td>";
                                                echo "<td>" . $row['COMPANYNAME'] . "</td>";
                                                echo "<td>" . $row['REASON'] . "</td>";
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