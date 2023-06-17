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
                    <h2>Insert Authorization</h2>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">


                                    <form method="post" action="">
                                        <div class="row">
                                            <div class="col-md-3">

                                                <div class="form-group">
                                                    <label for="dt">Date:</label>
                                                    <input type="date" name="dt" id="dt"
                                                        class="form-control" required>
                                                </div>
                                                                                                                                       
                                                <div class="form-group">
                                                    <label for="Duty Officer">Duty Officer:</label>
                                                    <input type="text" name="doffr" id="doofr" class="form-control"
                                                        required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="Duty JCO">Duty JCO:</label>
                                                    <input type="text" name="djco" id="djco" class="form-control"
                                                        required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="Duty NCO">Duty NCO:</label>
                                                    <input type="text" name="dnco" id="dnco" class="form-control"
                                                        required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="Duty Clerkr">Duty CLerk</label>
                                                    <input type="text" name="dclk" id="dclk" class="form-control"
                                                        required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="Duty Runner">Duty Runner</label>
                                                    <input type="text" name="drnr" id="drnr" class="form-control"
                                                        required>
                                                </div>


                                    
                                                <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                                               
                                             </div>

                                        
                                    </form>

                                    <?php
                                    // Check if the form is submitted
                                    if (isset($_POST['submit'])) {
                                        // Get the form data
                                        $dt = $_POST['dt'];
                                        $doffr = $_POST['doffr'];
                                        $djco = $_POST['djco'];
                                        $dnco = $_POST['dnco'];
                                        $dclk = $_POST['dclk'];
                                        $drnr = $_POST['drnr'];

                                        // Establish a connection to the Oracle database
                                        $conn = oci_connect('UMS', '12345', 'localhost/XE');
                                        if (!$conn) {
                                            $e = oci_error();
                                            echo "Failed to connect to Oracle: " . $e['message'];
                                        } else {
                                            // Prepare the INSERT statement
                                            $query = "INSERT into duty (dt, doffr, djco, dnco, dclk, drnr) values (TO_DATE(:dt, 'YYYY-MM-DD'), :doffr, :djco, :dnco, :dclk, :drnr)";
                                            $stmt = oci_parse($conn, $query);

                                            // Bind the parameters
                                            oci_bind_by_name($stmt, ':dt', $dt); 
                                            oci_bind_by_name($stmt, ':doffr', $doffr);                                            
                                            oci_bind_by_name($stmt, ':djco', $djco);
                                            oci_bind_by_name($stmt, ':dnco', $dnco);
                                            oci_bind_by_name($stmt, ':dclk', $dclk);
                                            oci_bind_by_name($stmt, ':drnr', $drnr);

                                            // Execute the INSERT statement
                                            $result = oci_execute($stmt);
                                            if ($result) {
                                                echo "<h3>Data inserted successfully.</h3>";
                                            } else {
                                                $e = oci_error($stmt);
                                                echo "Failed to insert Data: " . $e['message'];
                                            }

                                            oci_free_statement($stmt);
                                            oci_close($conn);
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </section>

        <!-- Page content -->

        <?php include 'views/footer.php'; ?>



    </div>

</body>

</html>