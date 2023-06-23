<?php include 'views/auth.php'; ?>
<?php include 'conn.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Add Punishment</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">

                                    <!-- Add punishment form -->
                                    <form method="post" action="">
                                        <div class="form-group">
                                            <label for="soldier_id">Soldier:</label>
                                            <select name="soldier_id" id="soldier_id" class="form-control" required>
                                                <option value="">Select Soldier</option>
                                                <?php
                                                include 'conn.php';

                                                $query = "SELECT SoldierID, Name FROM Soldier";
                                                $stmt = oci_parse($conn, $query);
                                                oci_execute($stmt);

                                                while ($row = oci_fetch_assoc($stmt)) {
                                                    echo "<option value='" . $row['SOLDIERID'] . "'>" . $row['NAME'] . "</option>";
                                                }

                                                oci_free_statement($stmt);
                                                oci_close($conn);
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="punishment">Punishment:</label>
                                            <select name="punishment" id="punishment" class="form-control" required>
                                                <option value="28 Days RI">28 Days RI</option>
                                                <option value="14 Days RI">14 Days RI</option>
                                                <option value="Warning">Warning</option>
                                                <option value="CL">CL</option>
                                                <option value="Reprimand">Reprimand</option>
                                                <option value="Severe Reprimand">Severe Reprimand</option>
                                                <option value="Good conduct Badge Pay">Good conduct Badge Pay</option>
                                                <option value="Extra Duty">Extra Duty</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="reason">Reason:</label>
                                            <input type="text" name="reason" id="reason" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="punishment_date">Punishment Date:</label>
                                            <input type="date" name="punishment_date" id="punishment_date"
                                                class="form-control" required>
                                        </div>

                                        <input type="submit" name="add_punishment" value="Add Punishment"
                                            class="btn btn-primary">
                                    </form>

                                    <?php
                                    if (isset($_POST['add_punishment'])) {
                                        $soldier_id = $_POST['soldier_id'];
                                        $punishment = $_POST['punishment'];
                                        $reason = $_POST['reason'];
                                        $punishment_date = $_POST['punishment_date'];

                                        // Insert the punishment into the Punishment table
                                        include 'conn.php';

                                        $query = "INSERT INTO Punishment (PunishmentID, SoldierID, Punishment, Reason, PunishmentDate) 
                                  VALUES (PunishmentSeq.NEXTVAL, :soldier_id, :punishment, :reason, TO_DATE(:punishment_date, 'YYYY-MM-DD'))";
                                        $stmt = oci_parse($conn, $query);
                                        oci_bind_by_name($stmt, ':soldier_id', $soldier_id);
                                        oci_bind_by_name($stmt, ':punishment', $punishment);
                                        oci_bind_by_name($stmt, ':reason', $reason);
                                        oci_bind_by_name($stmt, ':punishment_date', $punishment_date);
                                        $result = oci_execute($stmt);

                                        if ($result) {
                                            echo "Punishment added successfully.";
                                        } else {
                                            $e = oci_error($stmt);
                                            echo "Failed to add punishment: " . $e['message'];
                                        }

                                        oci_free_statement($stmt);
                                        oci_close($conn);
                                    }


                                    ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h3>List of Soldiers with Punishments:</h3>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Soldier ID</th>
                                                <th>Name</th>
                                                <th>Punishment</th>
                                                <th>Reason</th>
                                                <th>Punishment Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            include 'conn.php';

                                            $query = "SELECT Soldier.SoldierID, Soldier.Name, Punishment.Punishment, Punishment.Reason, Punishment.PunishmentDate
                                  FROM Soldier
                                  JOIN Punishment ON Soldier.SoldierID = Punishment.SoldierID";
                                            $stmt = oci_parse($conn, $query);
                                            oci_execute($stmt);

                                            while ($row = oci_fetch_assoc($stmt)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['SOLDIERID'] . "</td>";
                                                echo "<td>" . $row['NAME'] . "</td>";
                                                echo "<td>" . $row['PUNISHMENT'] . "</td>";
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

            </section>
        </div>
        <?php include 'views/footer.php'; ?>
    </div>
</body>

</html>