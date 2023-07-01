</div>
</div>
</div>
<footer class="main-footer">
  <strong>Unit Management System v2.0 &copy; 2023</strong>
</footer>
</div>
<?php     oci_close($conn); ?>
<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/jquery.dataTables.min.js"></script>
<script src="../js/dataTables.bootstrap4.min.js"></script>
<script src="../js/dataTables.responsive.min.js"></script>
<script src="../js/responsive.bootstrap4.min.js"></script>
<script src="../js/dataTables.buttons.min.js"></script>
<script src="../js/buttons.bootstrap4.min.js"></script>
<script src="../js/jszip.min.js"></script>
<script src="../js/pdfmake.min.js"></script>
<script src="../js/vfs_fonts.js"></script>
<script src="../js/buttons.html5.min.js"></script>
<script src="../js/buttons.print.min.js"></script>
<script src="../js/buttons.colVis.min.js"></script>
<script src="../js/adminlte.min.js"></script>
<script src="../js/chart.js"></script>
<script>
  /*
  document.addEventListener("keydown", function (event){
    if (event.ctrlKey){
       event.preventDefault();
    }
    if(event.keyCode == 123){
       event.preventDefault();
    }
});
document.addEventListener('contextmenu', 
     event => event.preventDefault()
);*/
</script>



<script>

    
    $(document).ready(function () {
        var table = $('#tablex').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": [
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':not(.no-export)'
                    }
                },
                'colvis'
            ]
        });

        table.buttons().container().appendTo('#tablex_wrapper .col-md-6:eq(0)');
    });

$(document).ready(function() {
  // Check if dark mode is enabled
  var darkModeEnabled = localStorage.getItem('darkModeEnabled');

  // Set the initial dark mode state
  if (darkModeEnabled === 'true') {
    enableDarkMode();
    $('#darkModeToggle').prop('checked', true); // Set the switch as checked
  } else {
    disableDarkMode();
  }

  // Listen for dark mode toggle button change event
  $('#darkModeToggle').on('change', function() {
    if (this.checked) {
      enableDarkMode();
    } else {
      disableDarkMode();
    }
  });

  // Function to enable dark mode
  function enableDarkMode() {
    $('body').addClass('dark-mode');
    $('.main-header').addClass('navbar-dark bg-dark');
    $('.main-header').removeClass('navbar-light');
    localStorage.setItem('darkModeEnabled', 'true');
  }

  // Function to disable dark mode
  function disableDarkMode() {
    $('body').removeClass('dark-mode');
    $('.main-header').removeClass('navbar-dark bg-dark');
    $('.main-header').addClass('navbar-light');
    localStorage.setItem('darkModeEnabled', 'false');
  }
});

</script>

</body>

</html>