<?php include 'views/auth.php'; ?>

<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>
<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>
        <div class="content-wrapper">
            <?php include 'views/header.php'; ?>
            <section class="content">
                <div class="container-fluid">
  <div class="row">

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Soldiers</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
                <?php
                  require 'conn.php';
                  
                  $query = oci_parse($conn,"Select soldierid from soldier order by soldierid");
                  $query_run = oci_execute($query);
                  $numrows=oci_fetch_all($query,$res);
                  echo '<h4>'.$numrows.'</h4>';                
                ?>
                
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-user fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">On Leave</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
              <?php
                  require 'conn.php';
                  
                  $query = oci_parse($connection,"Select medical_officer_id from medical_officer order by medical_officer_id");
                  $query_run = oci_execute($query);
                  $numrows=oci_fetch_all($query,$res);
                  echo '<h4>'.$numrows.'</h4>';
                  
                ?>
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-briefcase fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">On Course Cadre</div>
              <div class="row no-gutters align-items-center">
                <div class="col-auto">
                  <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                  <?php
                  require 'conn.php';
                  
                  $query = oci_parse($connection,"Select person_id from person order by person_id");
                  $query_run = oci_execute($query);
                  $numrows=oci_fetch_all($query,$res);
                  echo '<h4>'.$numrows.'</h4>';
                  
                ?>
                  </div>
                </div>
                
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pending Requests Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ongoing Events:</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
              <?php
                  require 'conn.php';
                  
                  $query = oci_parse($connection,"Select * from event where sysdate<event_enddate");
                  $query_run = oci_execute($query);
                  $numrows=oci_fetch_all($query,$res);
                  echo '<h4> '.$numrows.'</h4>';
                  
                ?>
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-calendar fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="row">

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-6 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Duty Roaster</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
                            
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Officer</th>
                                                <th>JCO</th>
                                                <th>NCO</th>
                                                <th>Clerk</th>
                                                <th>Runner</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT doffr, djco, dnco, dclk, drnr FROM duty";
                                            $stmt = oci_parse($conn, $query);
                                            oci_execute($stmt);
                                            while ($row = oci_fetch_assoc($stmt)) {
                                                echo "<tr>";
                                                echo "<td>" . $row['doffr'] . "</td>";
                                                echo "<td>" . $row['djco'] . "</td>";
                                                echo "<td>" . $row['dnco'] . "</td>";
                                                echo "<td>" . $row['dclk'] . "</td>";
                                                echo "<td>" . $row['drnr'] . "</td>";
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

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-6 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">On Leave</div>
              <div class="h5 mb-0 font-weight-bold text-gray-800">
              <?php
                  require 'conn.php';
                  
                  $query = oci_parse($connection,"Select medical_officer_id from medical_officer order by medical_officer_id");
                  $query_run = oci_execute($query);
                  $numrows=oci_fetch_all($query,$res);
                  echo '<h4>'.$numrows.'</h4>';
                  
                ?>
              </div>
            </div>
            <div class="col-auto">
              <i class="fas fa-briefcase fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    

    <!-- Pending Requests Card Example -->
   

  </div>
                    <!-- Page content -->
                </div>
            </section>

        </div>

        
        <?php include 'views/footer.php'; ?>
    </div>
</body>
</html>