<section class="content">
    <div class="container-fluid">
           <?php include '../includes/alert.php'; ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Soldier Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="soldierTable" class="table table-bordered table-striped">
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
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include 'conn.php';
                                // Retrieve soldier information
                                $query = "SELECT * FROM SOLDIER_VIEW ORDER BY SOLDIERID";
                                $stmt = oci_parse($conn, $query);
                                oci_execute($stmt);

                                // Fetch and display the soldier information in a table
                                while ($row = oci_fetch_assoc($stmt)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['SOLDIERID'] . "</td>";
                                    echo "<td><a href='profile.php?soldierId=" . $row['SOLDIERID'] . "'>" . $row['NAME'] . "</a></td>";
                                    echo "<td>" . $row['RANK'] . "</td>";
                                    echo "<td>" . $row['TRADE'] . "</td>";
                                    echo "<td>" . $row['COMPANYNAME'] . "</td>";
                                    echo "<td>" . $row['AGE'] . "</td>";
                                    echo "<td>" . $row['GENDER'] . "</td>";
                                    echo "<td>" . $row['MARITALSTATUS'] . "</td>";

                                    // Select Option
                                    echo "<td>";
                                    echo "<a class='btn btn-primary' href='edit_soldier.php?soldier_id=" . $row['SOLDIERID'] . "'>Edit</a> ";
                                    echo "<a class='btn btn-warning' href='soldier_option.php?soldier_id=" . $row['SOLDIERID'] . "'>Options</a> ";
                                    echo "<a class='btn btn-danger' href='delete_soldier.php?soldierId=" . $row['SOLDIERID'] . "'>Delete</a>";
                                    echo "</td>";


                                    echo "</tr>";
                                }

                                oci_free_statement($stmt);
                                oci_close($conn);

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

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<!-- Page specific script -->
<script>
    $(function () {
        $("#soldierTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#soldierTable_wrapper .col-md-6:eq(0)');
    });
</script>