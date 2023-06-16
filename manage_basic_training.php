<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; 
      include 'views/auth.php';  
?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Basic Training</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <h3>Basic Training Details</h3>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Training ID</th>
                                        <th>Training Code</th>
                                        <th>Training Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include 'conn.php';

                                    $query = "SELECT * FROM BasicTraining";
                                    $stmt = oci_parse($conn, $query);
                                    oci_execute($stmt);

                                    while ($row = oci_fetch_assoc($stmt)) {
                                        echo "<tr>";
                                        echo "<td>" . $row['TRAININGID'] . "</td>";
                                        echo "<td>" . $row['TRAININGCODE'] . "</td>";
                                        echo "<td>" . $row['TRAININGNAME'] . "</td>";
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
            </section>
        </div>
        <?php include 'views/footer.php'; ?>
    </div>
</body>

</html>
