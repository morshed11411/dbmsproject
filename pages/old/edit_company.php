<?php
// edit_company.php

if (isset($_GET['company_id'])) {
    $company_id = $_GET['company_id'];

    // Fetch company details from the database based on the company_id
    include 'conn.php'; // Include the conn.php file for database connection

    $query = "SELECT * FROM Company WHERE CompanyID = :company_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':company_id', $company_id);
    oci_execute($stmt);

    $company = oci_fetch_assoc($stmt);
    if (!$company) {
        echo "Company not found.";
        exit;
    }

    oci_free_statement($stmt);
    oci_close($conn);

    // Render the form for editing company details
    include 'views/auth.php';
    include 'views/head.php';

    ?>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Edit Company</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <form method="post" action="update_company.php">
                                        <input type="hidden" name="company_id" value="<?php echo $company['COMPANYID']; ?>">
                                        <div class="form-group">
                                            <label for="company_name">Company Name:</label>
                                            <input type="text" name="company_name" id="company_name" class="form-control"
                                                required value="<?php echo $company['COMPANYNAME']; ?>">
                                        </div>

                                        <input type="submit" name="submit" value="Update" class="btn btn-primary">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
        <?php include 'views/footer.php'; ?>
    </div>
    
    <?php
} else {
    echo "Invalid request. Company ID not provided.";
    exit;
}
?>
