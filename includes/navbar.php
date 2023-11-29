<?php
include '../includes/connection.php';

$currentPage = basename($_SERVER['PHP_SELF']);

// Check if the soldier ID is present in the session or URL parameter
if (isset($_SESSION['userid'])) {
  $soldierId = $_SESSION['userid'];

  // Build the SQL query to retrieve the profile picture
  $query = "SELECT PROFILEPICTURE FROM soldier_view WHERE SOLDIERID = :soldierId";
  $stmt = oci_parse($conn, $query);
  oci_bind_by_name($stmt, ':soldierId', $soldierId);
  oci_execute($stmt);

  // Fetch the result row
  $row = oci_fetch_assoc($stmt);

  // Check if a profile picture is found
  if ($row && !empty($row['PROFILEPICTURE'])) {
    $profilePicture = $row['PROFILEPICTURE'];
  } else {
    $profilePicture = '../images/default_profile_picture.png';
  }
} else {
  // Set a default profile picture path if the soldier ID is not present in the session
  $profilePicture = '../images/default_profile_picture.png';
}
?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top">
  <!-- Navbar content -->

  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" href="javascript:history.go(-1);" role="button"><i class="fas fa-arrow-left"></i></a>
    </li>
  </ul>
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <!-- Display "Dark Mode" on mobile devices -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <div class="custom-control custom-switch  align-items-center"><label
          class="custom-control-label d-none d-md-block" for="darkModeToggle">
          <input type="checkbox" class="custom-control-input" id="darkModeToggle">
          <i class="fas fa-moon"></i>

        </label> <label class="custom-control-label d-md-none md-1" for="darkModeToggle"></label>
      </div>
    </li>
  </ul>

  <!-- Display Date and Time -->
  <a href="calendar.php" class="navbar-text mx-auto text-center text-dark text-bold ">
    <div id="datetime" class=""></div>
  </a>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- Trigger Notification Modal -->
    <button onclick="showNotificationModal()" class="btn btn-light">
      <i class="fas fa-bell"></i>
      <?php if ($unreadCount): ?>
        <span class="badge badge-danger">
          <?php echo $unreadCount; ?>
        </span>
      <?php endif; ?>
    </button>
    <script>
      function updateDateTime() {
        var dateTimeElement = document.getElementById('datetime');
        if (dateTimeElement) {
          var now = new Date();
          var hours = now.getHours();
          var minutes = now.getMinutes();
          var seconds = now.getSeconds();
          var day = now.getDate();
          var month = now.toLocaleString('default', { month: 'short' }); // Get month name
          var dayOfWeek = now.toLocaleString('default', { weekday: 'short' }); // Get day of the week
          var formattedTime = hours + ':' + (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;

          var formattedDate = day + ' ' + month + ', ' + dayOfWeek;

          dateTimeElement.textContent = formattedTime + ', ' + formattedDate;
        }
      }

      // Update date and time every second
      setInterval(updateDateTime, 1000);

      // Initial call to set the initial date and time
      updateDateTime();
    </script>


    <li class="nav-item dropdown">
      <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
        aria-expanded="false">
        <div class="profile-info mr-1 d-flex align-items-center">
          <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-image">
          <span class="d-none d-sm-inline">
            <?php echo $username; ?>
          </span>
          <span class="d-inline d-sm-none"><i class="fas fa-ellipsis-v ml-2"></i></span>
        </div>

      </a>
      <div class="dropdown-menu ml-5" aria-labelledby="navbarDropdown">
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
    <img src="../assets/logo-s.png" alt="logo" class="brand-image-xl logo-xs">
    <img src="../assets/logo-l.png" alt="logo" class="brand-image-xs logo-xl " style="left: 60px">
  </a>

  <!-- Sidebar -->
  <div class="sidebar">



    <!-- Sidebar Menu -->


    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        <?php
        if ($_SESSION['role'] == 'admin') {
          ?>
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
              <i class="fas fa-tachometer-alt nav-icon"></i>
              <p>Dashboard</p>
            </a>
          </li>


          <li class="nav-item">
            <a class="nav-link" href="soldiers.php">
              <i class="fas fa-users nav-icon"></i>
              <p>Soldiers</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="company_view.php" class="nav-link">
              <i class="fas fa-building nav-icon"></i>
              <p>
                Company
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="calendar.php" class="nav-link">
              <i class="fas fa-tachometer-alt nav-icon"></i>
              <p>Calendar</p>
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
                <a href="training_event.php" class="nav-link sublink">
                  <i class="fas fa-chalkboard nav-icon"></i>
                  <p>Training Management</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="training_state.php" class="nav-link sublink">
                  <i class="fas fa-book-reader nav-icon"></i>
                  <p>Training State</p>
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
                <a href="leavejoin.php" class="nav-link sublink">
                  <i class="fas fa-percentage nav-icon"></i>
                  <p>Leave In</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="leaveout.php" class="nav-link sublink">
                  <i class="fas fa-calendar-alt nav-icon"></i>
                  <p>Leave Out</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="add_leave_type.php" class="nav-link sublink">
                  <i class="fas fa-key nav-icon"></i>
                  <p>Manage Leave Type</p>
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
                <a href="parade_state.php" class="nav-link sublink">
                  <i class="fas fa-file-alt nav-icon"></i>
                  <p>Daily Parade State</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="leave_state.php" class="nav-link sublink">
                  <i class="fas fa-archive nav-icon"></i>
                  <p>Leave State</p>
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
                <a href="medical_disposal.php" class="nav-link sublink">
                  <i class="fas fa-notes-medical nav-icon"></i>
                  <p>Medical Disposal</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="add_disposal_type.php" class="nav-link sublink">
                  <i class="fas fa-key nav-icon"></i>
                  <p>Manage Disposal Type</p>
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

              <!-- Company Management Group -->
              <li class="nav-item has-treeview">
                <a href="#" class="nav-link sublink">
                  <i class="fas fa-building nav-icon"></i>
                  <p>
                    Manage Options
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
                  <!-- Add more items related to Company Management here -->


                  <!-- Authorization Management -->
                  <li class="nav-item">
                    <a href="authorization.php" class="nav-link sublink">
                      <i class="fas fa-user-lock nav-icon"></i>
                      <p>Manage Authorization</p>
                    </a>
                  </li>

                  <!-- Trade Management -->
                  <li class="nav-item">
                    <a href="trade.php" class="nav-link sublink">
                      <i class="fas fa-tools nav-icon"></i>
                      <p>Manage Trade</p>
                    </a>
                  </li>

                  <!-- Rank Management -->
                  <li class="nav-item">
                    <a href="ranks.php" class="nav-link sublink">
                      <i class="fas fa-user-tag nav-icon"></i>
                      <p>Manage Rank</p>
                    </a>
                  </li>

                  <!-- ERE Management -->
                  <li class="nav-item">
                    <a href="add_ere_type.php" class="nav-link sublink">
                      <i class="fas fa-user-tag nav-icon"></i>
                      <p>Manage ERE</p>
                    </a>
                  </li>

                  <!-- Attachment Management -->
                  <li class="nav-item">
                    <a href="tempcomd.php" class="nav-link sublink">
                      <i class="fas fa-user-tag nav-icon"></i>
                      <p>Manage Attachment</p>
                    </a>
                  </li>

                  <!-- Serving Status Management -->
                  <li class="nav-item">
                    <a href="servingstatus.php" class="nav-link sublink">
                      <i class="fas fa-user-tag nav-icon"></i>
                      <p>Serving Status</p>
                    </a>
                  </li>

                  <!-- Appointment Management -->
                  <li class="nav-item">
                    <a href="appointment.php" class="nav-link sublink">
                      <i class="fas fa-briefcase nav-icon"></i>
                      <p>Manage Appointment</p>
                    </a>
                  </li>

                  <!-- Training Type Management -->
                  <li class="nav-item">
                    <a href="add_training_type.php" class="nav-link sublink">
                      <i class="fas fa-key nav-icon"></i>
                      <p>Manage Training Type</p>
                    </a>
                  </li>
                </ul>
              </li>
              <!-- Access Management -->
              <li class="nav-item">
                <a href="access.php" class="nav-link sublink">
                  <i class="fas fa-key nav-icon"></i>
                  <p>Manage Access</p>
                </a>
              </li>

              <!-- Password Management -->
              <li class="nav-item">
                <a href="approve_reset.php" class="nav-link sublink">
                  <i class="fas fa-key nav-icon"></i>
                  <p>Manage Password</p>
                </a>
              </li>

            </ul>
          </li>

          <?php
        }

        if ($_SESSION['role'] == 'manager') {
          ?>
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
            <li class="nav-item <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
              <a href="dashboard.php" class="nav-link">
                <i class="fas fa-tachometer-alt nav-icon"></i>
                <p> ড্যাশবোর্ড</p>
              </a>
            </li>

            <li class="nav-item <?php echo strpos($currentPage, 'soldiers.php') !== false ? 'active' : ''; ?>">
              <a class="nav-link" href="soldiers.php">
                <i class="fas fa-users nav-icon"></i>
                <p>জনবল</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="parade_state.php" class="nav-link">
                <i class="fas fa-building nav-icon"></i>
                <p>
                  প্যারেড স্টেট
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="training_event.php" class="nav-link">
                <i class="fas fa-building nav-icon"></i>
                <p>
                  প্রশিক্ষন
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="plan.php" class="nav-link">
                <i class="fas fa-building nav-icon"></i>
                <p>
                  ক্যারিয়ার প্ল্যান
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="profile.php" class="nav-link">
                <i class="fas fa-building nav-icon"></i>
                <p>
                  প্রোফাইল
                </p>
              </a>
            </li>



            <?php
        }
        if ($_SESSION['role'] == 'soldier') { ?>
            <li class="nav-item">
              <a href="profile.php" class="nav-link">
                <i class="fas fa-building nav-icon"></i>
                <p>
                  প্রোফাইল
                </p>
              </a>
            </li>
            <li class="nav-item">
              <a href="change_password.php" class="nav-link">
                <i class="fas fa-building nav-icon"></i>
                <p>
                  পাসওয়ার্ড পরিবর্তন
                </p>
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

<script>
  var currentPageURL = window.location.href;
  var navLinks = document.querySelectorAll('.nav-link');

  navLinks.forEach(function (link) {
    var linkHref = link.getAttribute('href');

    if (currentPageURL.includes(linkHref)) {
      // Add the 'active' class to the link
      link.classList.add('active');

      // If it's a sublink, find its parent list and open it
      if (link.classList.contains('sublink')) {
        var parentList = link.closest('.nav-treeview');
        if (parentList) {
          var parentLink = parentList.previousElementSibling;
          if (parentLink) {
            parentLink.classList.add('active', 'menu-open');
          }
        }
      }

      // If it's an <li> tag with class 'nav-item has-treeview', change its class
      if (link.parentElement.classList.contains('has-treeview')) {
        var parentListItem = link.parentElement;
        if (parentListItem) {
          parentListItem.classList.add('active', 'menu-open');
        }
      }
    }
  });
</script>