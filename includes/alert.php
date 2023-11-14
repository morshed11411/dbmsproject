<?php
if (isset($_SESSION['success'])) {
    echo '<div id="successAlert" class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['success'] . '
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div id="errorAlert" class="alert alert-danger alert-dismissible fade show" role="alert">' . $_SESSION['error'] . '
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>';
    unset($_SESSION['error']);
}
?>
<script>
    // Auto-dismiss the success and error alerts after 5 seconds
    setTimeout(function () {
        $("#successAlert, #errorAlert").alert('close');
    }, 3000);
</script>
