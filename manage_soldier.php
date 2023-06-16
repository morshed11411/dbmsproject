<?php
include 'conn.php';
include 'views/auth.php';

?>

<!DOCTYPE html>
<html lang="en">
<?php include 'views/head.php'; ?>

<body>
    <div class="wrapper">
        <?php include 'views/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Manage Soldier</h1>
                </div>
            </div>
            <?php include 'soldier.php'; ?>
        </div>
        <?php include 'views/footer.php'; ?>
    </div>
</body>

</html>
