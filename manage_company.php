<!DOCTYPE html>
<html lang="en">
<?php   include 'views/head.php';
        include 'views/auth.php'; 
?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Manage Company View</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <h3>Company Details</h3>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Company Name</th>
                                        <th>Coy Comd</th>
                                        <th>Total Manpower</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include 'conn.php';

                                    $query = "SELECT * FROM manage_company_view";
                                    $stmt = oci_parse($conn, $query);
                                    oci_execute($stmt);

                                    while ($row = oci_fetch_assoc($stmt)) {
                                        echo "<tr>";
                                        echo "<td><a href='company_details.php?company_id=" . $row['COMPANYID'] . "'>" . $row['COMPANYNAME'] . "</a></td>";
                                        echo "<td>" . $row['Coy Comd'] . "</td>";
                                        echo "<td>" . $row['TOTALMANPOWER'] . "</td>";
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
