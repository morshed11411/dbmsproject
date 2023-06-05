<?php include 'views/auth.php'; ?>

<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>

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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Page content -->
        </div>



        <?php
        include 'views/footer.php';
        if (isset($_POST['submit'])) {
            $company_id = $_POST['company_id'];
            $company_name = $_POST['company_name'];

            $conn = oci_connect('UMS', '12345', 'localhost/XE');
            if (!$conn) {
                $e = oci_error();
                echo "Failed to connect to Oracle: " . $e['message'];
            } else {
                $query = "INSERT INTO Company (CompanyID, CompanyName) VALUES (:company_id, :company_name)";
                $stmt = oci_parse($conn, $query);

                oci_bind_by_name($stmt, ':company_id', $company_id);
                oci_bind_by_name($stmt, ':company_name', $company_name);

                $result = oci_execute($stmt);
                if ($result) {
                    echo "Company data inserted successfully.";
                } else {
                    $e = oci_error($stmt);
                    echo "Failed to insert company data: " . $e['message'];
                }

                oci_free_statement($stmt);
                oci_close($conn);
            }
        }
        ?>

    </div>
</body>

</html>