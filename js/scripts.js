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


    var table = $('#tabley').DataTable({
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

    table.buttons().container().appendTo('#tabley_wrapper .col-md-6:eq(0)');
});


$(document).ready(function () {
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
    $('#darkModeToggle').on('change', function () {
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

//login page password toggole 

document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.getElementById("password");
    const togglePasswordIcon = document.getElementById("togglePassword");

    togglePasswordIcon.addEventListener("click", function () {
       if (passwordInput.type === "password") {
          passwordInput.type = "text";
          togglePasswordIcon.classList.remove("fa-eye-slash");
          togglePasswordIcon.classList.add("fa-eye");
       } else {
          passwordInput.type = "password";
          togglePasswordIcon.classList.remove("fa-eye");
          togglePasswordIcon.classList.add("fa-eye-slash");
       }
    });
 });


 