<nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top">
  <!-- Navbar content -->

  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <form method="post">
        <button type="submit" name="logout" class="btn btn-danger small"><i class="fas fa-sign-out-alt"></i> Logout</button>
      </form>
    </li>
  </ul>
</nav>
<!-- /.navbar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4 position-fixed">

  <!--   <a href="#" class="navbar-brand">
    <img src="../assets/favicon1.png" alt="Logo" class="brand-image">
  </a> -->

  <a href="dashboard.php" class="brand-link">
    <span class="brand-text font-weight-light">Unit Management System</span>
  </a>
  <a href="#" class="brand-link" style="padding-left: 30px;">
    <span class="brand-text font-weight-light">Welcome,
      <?php echo $username; ?>
    </span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    <?php
    if ($_SESSION['role'] == 'admin') {
      ?>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link active">
              <i class="fas fa-tachometer-alt nav-icon"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="fas fa-users nav-icon"></i>
              <p>
                Soldier
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="soldiers.php" class="nav-link sublink">
                  <i class="fas fa-users nav-icon"></i>
                  <p>Manage Soldier</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="fas fa-building nav-icon"></i>
              <p>
                Company
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="company.php" class="nav-link sublink">
                  <i class="fas fa-plus-circle nav-icon"></i>
                  <p>View Company</p>
                </a>
              </li>
            </ul>
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
            </ul>
          </li>


          <li class="nav-header">User Options</li>
          <?php
    }

    if (($_SESSION['role'] == 'admin') || ($_SESSION['role'] == 'Soldier')) {
      ?>
          <li class="nav-item">
            <a href="profile.php?soldierId=<?php echo $userid; ?>" class="nav-link">
              <i class="fas fa-user-circle nav-icon"></i>
              <p class="text">Profile</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="edit_password.php" class="nav-link">
              <i class="fas fa-key nav-icon"></i>
              <p class="text">Change Password</p>
            </a>
          </li>
          <?php
    }
    ?>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>