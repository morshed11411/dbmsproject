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
                    <h1>Insert Company Data</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <form method="post" action="">
                                        <div class="form-group">
                                            <label for="company_id">Company ID:</label>
                                            <input type="text" name="company_id" id="company_id" class="form-control"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label for="company_name">Company Name:</label>
                                            <input type="text" name="company_name" id="company_name"
                                                class="form-control" required>
                                        </div>


                                        <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                                    </form>
                                    <?php
                                    include 'conn.php'; // Include the conn.php file for database connection

                                    if (isset($_POST['submit'])) {
                                        $company_id = $_POST['company_id'];
                                        $company_name = $_POST['company_name'];

                                        $query = "INSERT INTO Company (CompanyID, CompanyName) VALUES (:company_id, :company_name)";
                                        $stmt = oci_parse($conn, $query);

                                        oci_bind_by_name($stmt, ':company_id', $company_id);
                                        oci_bind_by_name($stmt, ':company_name', $company_name);

                                        $result = oci_execute($stmt);
                                        if ($result) {
                                            echo "Company data inserted successfully.";
                                        } else {
                                            $e = oci_error($stmt);
                                            if ($e['code'] == 1 && strpos($e['message'], 'SYS_C007204') !== false) {
                                                echo "Failed to insert company data: The Company ID already exists. Please enter a unique Company ID.";
                                            } else {
                                                echo "Failed to insert company data: Please enter valid data.";
                                            }
                                        }

                                        oci_free_statement($stmt);
                                    }

                                    $query = "SELECT * FROM Company";
                                    $stmt = oci_parse($conn, $query);
                                    oci_execute($stmt);
                                    ?>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Company ID</th>
                                                <th>Company Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = oci_fetch_assoc($stmt)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['COMPANYID'] . "</td>";
                                                echo "<td>" . $row['COMPANYNAME'] . "</td>";
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
