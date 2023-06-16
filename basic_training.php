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
                    <h1>Soldier Basic Training</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <h3>Add/Update Soldier Basic Training</h3>
                            <form method="post" action="">
                                <div class="form-group">
                                    <label for="soldier_id">Soldier ID:</label>
                                    <input type="text" name="soldier_id" id="soldier_id" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="training_type">Training Type:</label>
                                    <select name="training_type" id="training_type" class="form-control" required>
                                        <?php
                                        include 'conn.php';

                                        $queryTraining = "SELECT * FROM BasicTraining";
                                        $stmtTraining = oci_parse($conn, $queryTraining);
                                        oci_execute($stmtTraining);

                                        while ($rowTraining = oci_fetch_assoc($stmtTraining)) {
                                            echo "<option value='" . $rowTraining['TRAININGNAME'] . "'>" . $rowTraining['TRAININGNAME'] . "</option>";
                                        }

                                        oci_free_statement($stmtTraining);
                                        oci_close($conn);
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="remark">Remark:</label>
                                    <select name="remark" id="remark" class="form-control" required>
                                        <option value="Pass">Pass</option>
                                        <option value="Fail">Fail</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="training_date">Training Date:</label>
                                    <input type="date" name="training_date" id="training_date" class="form-control" required>
                                </div>

                                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                            </form>
                            <?php
                            include 'conn.php';

                            if (isset($_POST['submit'])) {
                                $soldierID = $_POST['soldier_id'];
                                $trainingType = $_POST['training_type'];
                                $remark = $_POST['remark'];
                                $trainingDate = $_POST['training_date'];

                                // Check if the soldier ID exists
                                $queryCheck = "SELECT COUNT(*) AS COUNT FROM Soldier WHERE SoldierID = :soldierID";
                                $stmtCheck = oci_parse($conn, $queryCheck);
                                oci_bind_by_name($stmtCheck, ':soldierID', $soldierID);
                                oci_execute($stmtCheck);
                                $soldierCount = oci_fetch_assoc($stmtCheck)['COUNT'];

                                if ($soldierCount > 0) {
                                    // Get the training ID based on the training type
                                    $queryTraining = "SELECT TrainingID FROM BasicTraining WHERE TrainingName = :trainingType";
                                    $stmtTraining = oci_parse($conn, $queryTraining);
                                    oci_bind_by_name($stmtTraining, ':trainingType', $trainingType);
                                    oci_execute($stmtTraining);
                                    $trainingID = oci_fetch_assoc($stmtTraining)['TRAININGID'];

                                    if ($trainingID) {
                                        // Check if the soldier basic training record already exists
                                        $queryCheck = "SELECT COUNT(*) AS COUNT FROM SoldierBasicTraining WHERE SoldierID = :soldierID AND TrainingID = :trainingID";
                                        $stmtCheck = oci_parse($conn, $queryCheck);
                                        oci_bind_by_name($stmtCheck, ':soldierID', $soldierID);
                                        oci_bind_by_name($stmtCheck, ':trainingID', $trainingID);
                                        oci_execute($stmtCheck);
                                        $count = oci_fetch_assoc($stmtCheck)['COUNT'];

                                        if ($count > 0) {
                                            // Update the soldier basic training record
                                            $queryUpdate = "UPDATE SoldierBasicTraining SET Remark = :remark, TrainingDate = TO_DATE(:trainingDate, 'YYYY-MM-DD') WHERE SoldierID = :soldierID AND TrainingID = :trainingID";
                                            $stmtUpdate = oci_parse($conn, $queryUpdate);
                                            oci_bind_by_name($stmtUpdate, ':remark', $remark);
                                            oci_bind_by_name($stmtUpdate, ':trainingDate', $trainingDate);
                                            oci_bind_by_name($stmtUpdate, ':soldierID', $soldierID);
                                            oci_bind_by_name($stmtUpdate, ':trainingID', $trainingID);
                                            oci_execute($stmtUpdate);
                                            oci_commit($conn);

                                            echo "Soldier basic training record updated successfully.";
                                        } else {
                                            // Insert a new soldier basic training record
                                            $queryInsert = "INSERT INTO SoldierBasicTraining (TrainingID, SoldierID, Remark, TrainingDate) VALUES (:trainingID, :soldierID, :remark, TO_DATE(:trainingDate, 'YYYY-MM-DD'))";
                                            $stmtInsert = oci_parse($conn, $queryInsert);
                                            oci_bind_by_name($stmtInsert, ':trainingID', $trainingID);
                                            oci_bind_by_name($stmtInsert, ':soldierID', $soldierID);
                                            oci_bind_by_name($stmtInsert, ':remark', $remark);
                                            oci_bind_by_name($stmtInsert, ':trainingDate', $trainingDate);
                                            oci_execute($stmtInsert);
                                            oci_commit($conn);

                                            echo "Soldier basic training record added successfully.";
                                        }
                                    } else {
                                        echo "Invalid training type. Please select a valid training type.";
                                    }
                                } else {
                                    echo "Invalid soldier ID. Please enter a valid soldier ID.";
                                }
                            }

                            oci_close($conn);
                            ?>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h3>Soldier Basic Training Records</h3>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Soldier ID</th>
                                        <th>Soldier Name</th>
                                        <th>Training Type</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include 'conn.php';

                                    $queryRecords = "SELECT s.SoldierID, s.Name, bt.TrainingName, sbt.Remark
                                                     FROM Soldier s
                                                     JOIN SoldierBasicTraining sbt ON s.SoldierID = sbt.SoldierID
                                                     JOIN BasicTraining bt ON sbt.TrainingID = bt.TrainingID";
                                    $stmtRecords = oci_parse($conn, $queryRecords);
                                    oci_execute($stmtRecords);

                                    while ($row = oci_fetch_assoc($stmtRecords)) {
                                        echo "<tr>";
                                        echo "<td>" . $row['SOLDIERID'] . "</td>";
                                        echo "<td>" . $row['NAME'] . "</td>";
                                        echo "<td>" . $row['TRAININGNAME'] . "</td>";
                                        echo "<td>" . $row['REMARK'] . "</td>";
                                        echo "</tr>";
                                    }

                                    oci_free_statement($stmtRecords);
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
