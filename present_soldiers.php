<!DOCTYPE html>
<?php
include 'conn.php';
include 'views/auth.php';
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Present Soldiers</title>
    <link rel="stylesheet" href="style.css"> <!-- Replace "style.css" with your actual CSS file name and path -->
    <?php include 'views/head.php'; ?>
</head>
<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Present Soldiers</h1>
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
                                                <th>Rank</th>
                                                <th>Company</th>
                                                <th>Name</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include 'conn.php'; // Include the conn.php file for database connection
                                            
                                            $query = "SELECT * FROM SOLDIER_VIEW WHERE ISPRESENT=1 ORDER BY SOLDIERID";

                                            $stmt = oci_parse($conn, $query);
                                            oci_execute($stmt);

                                            while ($row = oci_fetch_assoc($stmt)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['SOLDIERID'] . "</td>";
                                                echo "<td>" . $row['RANK'] . "</td>";
                                                echo "<td>" . $row['COMPANYNAME'] . "</td>";
                                                echo "<td>" . $row['NAME'] . "</td>";
                                                
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
