<?php include '../includes/header.php'; ?>
<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>This is a blank page</h3>
        </div>
        <div class="text-right">
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <a href="<?php echo $_SERVER["REQUEST_URI"]; ?>"><?php echo $title; ?></a>
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body collapse show" id="collapseExample">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>First Name</label>
                                <?php
                                $name = "First Name";
                                echo "<input type='text' name='" . $name . "' class='form-control' value='" . $first_name . "'>";
                                echo "<br>";
                                if (isset($_POST[$name])) {
                                    echo "<input type='text' name='" . $name . "' class='form-control' value='" . $first_name . "'>";
                                } else {
                                    echo "<input type='text' name='" . $name . "' class='form-control' value=''>";
                                }
                                echo "<br>";
                                if (isset($_POST[$name])) {
                                    echo "<input type='text' name='" . $name . "' class='form-control' value='" . $first_name . "'>";
                                } else {
                                    echo "<input type='text' name='" . $name . "' class='form-control' value=''>";
                                }
                                echo "<br>";
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>