<?php
$conn = oci_connect('UMS', '12345', 'localhost/XE');
if (!$conn) {
    $e = oci_error();
    echo "Failed to connect to Oracle: " . $e['message'];
} else {

    // Fetch data for the Soldier table
    $querySoldier = "SELECT SOLDIERID FROM SOLDIER";
    $stmtSoldier = oci_parse($conn, $querySoldier);
    oci_execute($stmtSoldier);

    $SoldierList = array();
    while ($rowSoldier = oci_fetch_assoc($stmtSoldier)) {
        $Soldier = new stdClass();
        $Soldier->SoldierID = $rowSoldier['SOLDIERID'];
        $SoldierList[] = $Soldier;
    }

    oci_free_statement($stmtSoldier);




    oci_close($conn);
}
?>


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
                    <h2>Insert Punishment</h2>
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
                                                    <label for="punishmnent_id">Punishment ID:</label>
                                                    <input type="text" name="punishmnent_id" id="punishmnent_id"
                                                        class="form-control" required>
                                                </div>
                                               
                                                <div class="form-group">
                                                    <label for="Soldier_id">Soldier:</label>
                                                    <select name="Soldier_id" id="Soldier_id"
                                                        class="form-control custom-select" required>
                                                        <option value="">Select Soldier</option>
                                                        <?php foreach ($SoldierList as $Soldier): ?>
                                                            <option value="<?php echo $Soldier->SoldierID ?>"><?php echo $Soldier->SoldierID ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="p_type">Punishment Type:</label>
                                                    
                                                        <select name="p_type" id="p_type" class="form-control"
                                                        required>
                                                        <option value="">Select Punishment</option>
                                                        <option value="Rigorous Imprisonment">Rigorous Imprisonment</option>
                                                        <option value="Extra Duty">Extra Duty</option>
                                                        <option value="Reprimand">Reprimand</option>
                                                        <option value="Severe Reprimand">Severe Reprimand</option>
                                                        <option value="Hold Clean Service">Hold Clean Service</option>                                                        
                                                        <option value="Warning">Warning</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="reason">Reason:</label>
                                                    <input type="text" name="reason" id="reason" class="form-control"
                                                        required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="dt">Date:</label>
                                                    <input type="date" name="dt" id="dt"
                                                        class="form-control" required>
                                                </div>

                                                <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                                               
                                            

                                        
                                    </form>

                                    <?php

                                    include 'conn.php';
                                    // Check if the form is submitted
                                    if (isset($_POST['submit'])) {
                                        // Get the form data
                                        $punishmnent_id = $_POST['punishmnent_id'];
                                        $Soldier = $_POST['Soldier_id'];
                                        $p_type = $_POST['p_type'];
                                        $reason = $_POST['reason'];
                                        $dt = $_POST['dt'];
                                        

                                        
                                            // Prepare the INSERT statement
                                            $query = "INSERT INTO Punishment (Punishmentid, soldierid, punishment, reason, punishmentdate) VALUES (:punishmnent_id, :soldier, :punishment, :reason, TO_DATE(:dt, 'YYYY-MM-DD'))";
                                            $stmt = oci_parse($conn, $query);
                                            
                                            // Bind the parameters
                                            oci_bind_by_name($stmt, ':punishmnent_id', $punishmnent_id);                                            
                                            oci_bind_by_name($stmt, ':soldier', $Soldier);
                                            oci_bind_by_name($stmt, ':punishment', $p_type);
                                            oci_bind_by_name($stmt, ':reason', $reason);
                                            oci_bind_by_name($stmt, ':dt', $dt);

                                            // Execute the INSERT statement
                                            $result = oci_execute($stmt);
                                            if ($result) {
                                                echo "<h3>Punishment data inserted successfully.</h3>";
                                            } else {
                                                $e = oci_error($stmt);
                                                echo "Failed to insert Punishment data: " . $e['message'];
                                            }

                                            oci_free_statement($stmt);
                                           
                                        }

                                    $query = "SELECT * FROM PUNISHMENT";
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
                                                <th>Punishmnet ID</th>
                                                <th>Soldier ID</th>
                                                <th>Punishmnet</th>
                                                <th>Reason</th>
                                                <th>Date</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = oci_fetch_assoc($stmt)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['PUNISHMENTID'] . "</td>";
                                                echo "<td>" . $row['SOLDIERID'] . "</td>";
                                                echo "<td>" . $row['PUNISHEMENT'] . "</td>";
                                                echo "<td>" . $row['REASON'] . "</td>";
                                                echo "<td>" . $row['PUNISHMENTDATE'] . "</td>";
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
        </div>
        </section>

        <!-- Page content -->

        <?php include 'views/footer.php'; ?>



    </div>

</body>

</html>