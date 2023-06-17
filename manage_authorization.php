<?php
include 'conn.php';
include 'views/auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Authorizations</title>
    <link rel="stylesheet" href="style.css"> <!-- Replace "style.css" with your actual CSS file name and path -->
    <?php include 'views/head.php'; ?>
</head>
<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Manage Authorizations</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <h3>Add Authorization</h3>
                            <p>Add a new authorization for a company:</p>
                            <form method="post" action="">
                                <div class="form-group">
                                    <label for="company">Select Company:</label>
                                    <select name="company" id="company" class="form-control">
                                        <?php
                                        // Fetch the list of companies
                                        $queryCompanies = "SELECT * FROM COMPANY";
                                        $stmtCompanies = oci_parse($conn, $queryCompanies);
                                        oci_execute($stmtCompanies);

                                        while ($company = oci_fetch_assoc($stmtCompanies)) {
                                            $companyID = $company['COMPANYID'];
                                            $companyName = $company['COMPANYNAME'];

                                            echo "<option value='$companyID'>$companyName</option>";
                                        }

                                        oci_free_statement($stmtCompanies);
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="authorization_id">Authorization ID:</label>
                                    <input type="text" name="authorization_id" id="authorization_id" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="manpower">Manpower:</label>
                                    <input type="number" name="manpower" id="manpower" class="form-control" required>
                                </div>
                                <button type="submit" name="add_authorization" class="btn btn-primary">Add Authorization</button>
                            </form>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-body">
                            <h3>Authorized Manpower</h3>
                            <p>Authorized manpower for each company:</p>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Company Name</th>
                                        <th>Authorized Manpower</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include 'conn.php';
                                    // Fetch the list of companies and their authorized manpower
                                    $queryAuth = "SELECT c.COMPANYNAME, a.MANPOWER FROM COMPANY c LEFT JOIN AUTHORIZATION a ON c.COMPANYID = a.COMPANYID";
                                    $stmtAuth = oci_parse($conn, $queryAuth);
                                    oci_execute($stmtAuth);

                                    while ($rowAuth = oci_fetch_assoc($stmtAuth)) {
                                        $companyName = $rowAuth['COMPANYNAME'];
                                        $manpower = $rowAuth['MANPOWER'];

                                        echo "<tr>";
                                        echo "<td>$companyName</td>";
                                        echo "<td>$manpower</td>";
                                        echo "</tr>";
                                    }

                                    oci_free_statement($stmtAuth);
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_authorization'])) {
                        $companyID = $_POST['company'];
                        $authorizationID = $_POST['authorization_id'];
                        $manpower = $_POST['manpower'];

                        // Check if the authorization already exists for the company
                        $queryCheck = "SELECT * FROM AUTHORIZATION WHERE COMPANYID = :COMPANYID";
                        $stmtCheck = oci_parse($conn, $queryCheck);
                        oci_bind_by_name($stmtCheck, ':COMPANYID', $companyID);
                        oci_execute($stmtCheck);
                        $rowCheck = oci_fetch_assoc($stmtCheck);

                        if ($rowCheck) {
                            // Authorization already exists, update the existing record
                            $queryUpdate = "UPDATE AUTHORIZATION SET AUTHORIZATIONID = :AUTHORIZATIONID, MANPOWER = :MANPOWER WHERE COMPANYID = :COMPANYID";
                            $stmtUpdate = oci_parse($conn, $queryUpdate);
                            oci_bind_by_name($stmtUpdate, ':AUTHORIZATIONID', $authorizationID);
                            oci_bind_by_name($stmtUpdate, ':MANPOWER', $manpower);
                            oci_bind_by_name($stmtUpdate, ':COMPANYID', $companyID);
                            $resultUpdate = oci_execute($stmtUpdate);

                            if ($resultUpdate) {
                                echo "<div class='alert alert-success'>Authorization Updated Successfully</div>";
                            } else {
                                echo "<div class='alert alert-warning'>Failed to Update Authorization</div>";
                            }

                            oci_free_statement($stmtUpdate);
                        } else {
                            // Authorization doesn't exist, insert a new record
                            $queryInsert = "INSERT INTO AUTHORIZATION (AUTHORIZATIONID, MANPOWER, COMPANYID) VALUES (:AUTHORIZATIONID, :MANPOWER, :COMPANYID)";
                            $stmtInsert = oci_parse($conn, $queryInsert);
                            oci_bind_by_name($stmtInsert, ':AUTHORIZATIONID', $authorizationID);
                            oci_bind_by_name($stmtInsert, ':MANPOWER', $manpower);
                            oci_bind_by_name($stmtInsert, ':COMPANYID', $companyID);
                            $resultInsert = oci_execute($stmtInsert);

                            if ($resultInsert) {
                                echo "<div class='alert alert-success'>Authorization Added Successfully</div>";
                            } else {
                                echo "<div class='alert alert-warning'>Failed to Add Authorization</div>";
                            }

                            oci_free_statement($stmtInsert);
                        }

                        oci_free_statement($stmtCheck);
                    }

                    oci_close($conn);
                    ?>
                </div>
            </section>
        </div>

        <?php include 'views/footer.php'; ?>
    </div>
</body>
</html>
