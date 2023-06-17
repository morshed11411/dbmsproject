<!DOCTYPE html>
<?php   include 'views/head.php';
        include 'views/auth.php'; 
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ranks and Soldiers</title>
    <link rel="stylesheet" href="your_stylesheet.css">
</head>
<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Ranks and Soldiers</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <?php
                            // Include the database connection
                            include 'conn.php';

                            // Retrieve the list of ranks and the count of soldiers in each rank
                            $query = "SELECT r.Rank, COUNT(*) AS SoldierCount
                                      FROM Ranks r
                                      JOIN Soldier s ON r.RankID = s.RankID
                                      GROUP BY r.Rank";
                            $stmt = oci_parse($conn, $query);
                            oci_execute($stmt);

                            // Check if any ranks are found
                            if (oci_fetch($stmt)) {
                                // Display the ranks and soldier counts in a table format
                                echo "<table class='table table-bordered'>
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Soldier Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>";

                                // Iterate over the result set and display each rank and soldier count
                                do {
                                    $rank = oci_result($stmt, 'RANK');
                                    $soldierCount = oci_result($stmt, 'SOLDIERCOUNT');

                                    echo "<tr>
                                            <td>$rank</td>
                                            <td>$soldierCount</td>
                                        </tr>";
                                } while (oci_fetch($stmt));

                                echo "</tbody>
                                    </table>";
                            } else {
                                echo "No ranks found.";
                            }

                            oci_free_statement($stmt);
                            oci_close($conn);
                            ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include 'views/footer.php'; ?>
    </div>
</body>
</html>
