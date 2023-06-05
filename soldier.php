<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Soldier Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Soldier ID</th>
                                    <th>Name</th>
                                    <th>Rank</th>
                                    <th>Trade</th>
                                    <th>Company</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Marital Status</th>
                                    <th>District</th>
                                    <th>Select</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $conn = oci_connect('UMS', '12345', 'localhost/XE');
                                if (!$conn) {
                                    $e = oci_error();
                                    echo "Failed to connect to Oracle: " . $e['message'];
                                } else {
                                    // Retrieve soldier information
                                    $query = "SELECT s.SOLDIERID, s.NAME, r.RANK, c.COMPANYNAME, t.TRADE, s.AGE, s.GENDER, s.MARITALSTATUS, s.DISTRICT 
                                              FROM Soldier s 
                                              JOIN Ranks r ON s.RANKID = r.RANKID
                                              JOIN Trade t ON s.TRADEID = t.TRADEID
                                              JOIN Company c ON s.COMPANYID = c.COMPANYID";
                                    $stmt = oci_parse($conn, $query);
                                    oci_execute($stmt);

                                    // Fetch and display the soldier information in a table
                                    while ($row = oci_fetch_assoc($stmt)) {
                                        echo "<tr>";
                                        echo "<td>".$row['SOLDIERID']."</td>";
                                        echo "<td><a href='profile.php?soldierId=".$row['SOLDIERID']."'>".$row['NAME']."</a></td>";
                                        echo "<td>".$row['RANK']."</td>";
                                        echo "<td>".$row['TRADE']."</td>";
                                        echo "<td>".$row['COMPANYNAME']."</td>";
                                        echo "<td>".$row['AGE']."</td>";
                                        echo "<td>".$row['GENDER']."</td>";
                                        echo "<td>".$row['MARITALSTATUS']."</td>";
                                        echo "<td>".$row['DISTRICT']."</td>";
                                        
                                        // Select Option
                                        echo "<td><a href='edit_soldier.php?soldierId=".$row['SOLDIERID']."'>Edit</a></td>";

                                        // Delete Option
                                        echo "<td><a href='delete_soldier.php?soldierId=".$row['SOLDIERID']."'>Delete</a></td>";

                                        echo "</tr>";
                                    }
                                    
                                    oci_free_statement($stmt);
                                    oci_close($conn);
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
