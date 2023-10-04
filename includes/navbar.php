<?php
// Get the current page name from the URL
$currentUrl = $_SERVER['REQUEST_URI'];
$currentPage = substr($currentUrl, strrpos($currentUrl, '/') + 1);
?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top">
  <!-- Navbar content -->

  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>

  </ul>
  <ul class="navbar-nav ml-1">
    <li class="nav-item">
      <div class="custom-control custom-switch d-flex align-items-center">
        <input type="checkbox" class="custom-control-input" id="darkModeToggle">
        <label class="custom-control-label" for="darkModeToggle">Dark Mode</label>
      </div>


    </li>


  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <a href="profile.php"><img src="../images/default_profile_picture.png" alt="Logo" class="brand-image mr-1"></a>
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <?php echo $username; ?>
      </a>
      <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="profile.php">View Profile</a>
        <a class="dropdown-item" href="change_password.php">Change Password</a>
        <div class="dropdown-divider"></div>
        <form method="post">
          <button type="submit" name="logout" class="dropdown-item"
            onclick="return confirm('Are you sure you want to logout?')">Logout</button>
        </form>
      </div>
    </li>
  </ul>

</nav>
<!-- /.navbar -->



<aside class="main-sidebar sidebar-dark-primary elevation-4 position-fixed">

  <a href="dashboard.php" class="brand-link logo-switch d-flex align-items-center">
    <img src="../assets/logo-s.png" alt="AdminLTE Docs Logo Small" class="brand-image-xl logo-xs">

    <img src="../assets/logo-l.png" alt="AdminLTE Docs Logo Large" class="brand-image-xs logo-xl " style="left: 60px">
  </a>

  <!-- 
  <a href="dashboard.php" class="brand-link d-flex align-items-center">
    <img src="../assets/favicon1.png" alt="Logo" class="brand-image" style="opacity: .8; width: 160px; height: 160px;">
  </a>
    <a href="dashboard.php" class="brand-link d-flex align-items-center">
        <img src="../assets/logo.png" alt="Profile Picture" class="brand-image"
            style="opacity: .8; width: 160px; height: 160px; margin: 0; padding: 0;">
    </a>

-->



  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    <?php
    if ($_SESSION['role'] == 'admin') {
      ?>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
            <a href="dashboard.php" class="nav-link">
              <i class="fas fa-tachometer-alt nav-icon"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item <?php echo strpos($currentPage, 'soldiers.php') !== false ? 'active' : ''; ?>">
            <a class="nav-link" href="soldiers.php">
              <i class="fas fa-users nav-icon"></i>
              <p>Soldiers</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="company.php" class="nav-link">
              <i class="fas fa-building nav-icon"></i>
              <p>
                Company
              </p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="fas fa-chalkboard-teacher nav-icon"></i>
              <p>
                Training
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="basic_training.php" class="nav-link sublink">
                  <i class="fas fa-chalkboard nav-icon"></i>
                  <p>Basic Training</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="advanced_training.php" class="nav-link sublink">
                  <i class="fas fa-book-reader nav-icon"></i>
                  <p>Manage Training</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="soldier_advanced_training.php" class="nav-link sublink">
                  <i class="fas fa-book-reader nav-icon"></i>
                  <p>Advanced Training</p>
                </a>
              </li>
            </ul>

          </li>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="fas fa-suitcase nav-icon"></i>
              <p>
                Career Plan
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="plan_view.php" class="nav-link sublink">
                  <i class="fas fa-edit nav-icon"></i>
                  <p>Update Plan</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="plan.php" class="nav-link sublink">
                  <i class="fas fa-edit nav-icon"></i>
                  <p>View Plan</p>
                </a>
              </li>
            </ul>
          </li>


          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="fas fa-user-clock nav-icon"> </i>
              <p>
                Leave Management
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="approve_leave.php" class="nav-link sublink">
                  <i class="fas fa-check-circle nav-icon"></i>
                  <p>Approve Leave</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="leave_percentage.php" class="nav-link sublink">
                  <i class="fas fa-percentage nav-icon"></i>
                  <p>Leave Percentage</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="leave_details.php" class="nav-link sublink">
                  <i class="fas fa-calendar-alt nav-icon"></i>
                  <p>Todays Leave</p>
                </a>
              </li>

            </ul>
          </li>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="fas fa-clipboard-check nav-icon"></i>
              <p>
                Parade State
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="generate_parade_state.php" class="nav-link sublink">
                  <i class="fas fa-file-alt nav-icon"></i>
                  <p>Create Parade State</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="absent_soldier.php" class="nav-link sublink">
                  <i class="fas fa-archive nav-icon"></i>
                  <p>Archive</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="fas fa-medkit nav-icon"></i>
              <p>
                Medical Disposal
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="medical_info.php" class="nav-link sublink">
                  <i class="fas fa-notes-medical nav-icon"></i>
                  <p>Add Medical Info</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="archive_medical_info.php" class="nav-link sublink">
                  <i class="fas fa-archive nav-icon"></i>
                  <p>Todays Disposal</p>
                </a>
              </li>

            </ul>

          <li class="nav-item has-treeview">
            <a href="team.php" class="nav-link">
              <i class="fas fa-users nav-icon"></i>
              <p>Manage Team</p>
            </a>
          </li>

          </li>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="fas fa-cogs nav-icon"></i>
              <p>
                Settings
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="company.php" class="nav-link sublink">
                  <i class="fas fa-tools nav-icon"></i>
                  <p>Manage Company</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="trade.php" class="nav-link sublink">
                  <i class="fas fa-tools nav-icon"></i>
                  <p>Manage Trade</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="ranks.php" class="nav-link sublink">
                  <i class="fas fa-user-tag nav-icon"></i>
                  <p>Manage Rank</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="ere.php" class="nav-link sublink">
                  <i class="fas fa-user-tag nav-icon"></i>
                  <p>Manage ERE</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="tempcomd.php" class="nav-link sublink">
                  <i class="fas fa-user-tag nav-icon"></i>
                  <p>Temporary Comd</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="servingstatus.php" class="nav-link sublink">
                  <i class="fas fa-user-tag nav-icon"></i>
                  <p>Serving Status</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="appointment.php" class="nav-link sublink">
                  <i class="fas fa-briefcase nav-icon"></i>
                  <p>Manage Appointment</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="authorization.php" class="nav-link sublink">
                  <i class="fas fa-user-lock nav-icon"></i>
                  <p>Manage Authorization</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="access.php" class="nav-link sublink">
                  <i class="fas fa-key nav-icon"></i>
                  <p>Manage Access</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="access.php" class="nav-link sublink">
                  <i class="fas fa-cog nav-icon"></i>
                  <p>Application Settings</p>
                </a>
              </li>
            </ul>
          </li>

          <?php
    }

    if (($_SESSION['role'] == 'admin') || ($_SESSION['role'] == 'Soldier')) {
      ?>
          <?php
    }
    ?>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>