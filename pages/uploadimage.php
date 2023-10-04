<?php
session_start();

include '../includes/connection.php';

// Process the form submission to upload images
if (isset($_POST['upload_submit'])) {
    $soldierID = $_SESSION['soldier_id'];
    $targetDirectory = "../images/"; // Folder to store the images
    $allowedExtensions = array('jpg', 'jpeg', 'png'); // Allowed file extensions

    // Process passport size picture upload
    if ($_FILES['passport_picture']['name']) {
        $passportPictureName = $_FILES['passport_picture']['name'];
        $passportPictureTmpName = $_FILES['passport_picture']['tmp_name'];
        $passportPictureExtension = pathinfo($passportPictureName, PATHINFO_EXTENSION);

        // Check if the file extension is allowed
        if (in_array($passportPictureExtension, $allowedExtensions)) {
            $passportPicturePath = $targetDirectory . $soldierID . '_passport.' . $passportPictureExtension;

            // Move the uploaded file to the target directory
            move_uploaded_file($passportPictureTmpName, $passportPicturePath);

            // Check if the image path already exists in the database
            $query = "SELECT SOLDIER_ID FROM UPLOADED_IMAGES WHERE SOLDIER_ID = :soldier_id";
            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':soldier_id', $soldierID);
            oci_execute($stmt);
            $result = oci_fetch_assoc($stmt);

            if ($result) {
                // Update the image path in the database
                $query = "UPDATE UPLOADED_IMAGES SET PASSPORT_PICTURE_PATH = :image_path WHERE SOLDIER_ID = :soldier_id";
                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':image_path', $passportPicturePath);
                oci_bind_by_name($stmt, ':soldier_id', $soldierID);
                oci_execute($stmt);
            } else {
                // Insert the image path into the database
                $query = "INSERT INTO UPLOADED_IMAGES (SOLDIER_ID, PASSPORT_PICTURE_PATH) VALUES (:soldier_id, :image_path)";
                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':soldier_id', $soldierID);
                oci_bind_by_name($stmt, ':image_path', $passportPicturePath);
                oci_execute($stmt);
            }
        }
    }

    // Process NID picture upload
    if ($_FILES['nid_picture']['name']) {
        $nidPictureName = $_FILES['nid_picture']['name'];
        $nidPictureTmpName = $_FILES['nid_picture']['tmp_name'];
        $nidPictureExtension = pathinfo($nidPictureName, PATHINFO_EXTENSION);

        // Check if the file extension is allowed
        if (in_array($nidPictureExtension, $allowedExtensions)) {
            $nidPicturePath = $targetDirectory . $soldierID . '_nid.' . $nidPictureExtension;

            // Move the uploaded file to the target directory
            move_uploaded_file($nidPictureTmpName, $nidPicturePath);

            // Check if the image path already exists in the database
            $query = "SELECT SOLDIER_ID FROM UPLOADED_IMAGES WHERE SOLDIER_ID = :soldier_id";
            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':soldier_id', $soldierID);
            oci_execute($stmt);
            $result = oci_fetch_assoc($stmt);

            if ($result) {
                // Update the image path in the database
                $query = "UPDATE UPLOADED_IMAGES SET NID_PICTURE_PATH = :image_path WHERE SOLDIER_ID = :soldier_id";
                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':image_path', $nidPicturePath);
                oci_bind_by_name($stmt, ':soldier_id', $soldierID);
                oci_execute($stmt);
            } else {
                // Insert the image path into the database
                $query = "INSERT INTO UPLOADED_IMAGES (SOLDIER_ID, NID_PICTURE_PATH) VALUES (:soldier_id, :image_path)";
                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':soldier_id', $soldierID);
                oci_bind_by_name($stmt, ':image_path', $nidPicturePath);
                oci_execute($stmt);
            }
        }
    }

    // Process combo ID card picture upload
    if ($_FILES['combo_id_picture']['name']) {
        $comboIdPictureName = $_FILES['combo_id_picture']['name'];
        $comboIdPictureTmpName = $_FILES['combo_id_picture']['tmp_name'];
        $comboIdPictureExtension = pathinfo($comboIdPictureName, PATHINFO_EXTENSION);

        // Check if the file extension is allowed
        if (in_array($comboIdPictureExtension, $allowedExtensions)) {
            $comboIdPicturePath = $targetDirectory . $soldierID . '_combo.' . $comboIdPictureExtension;

            // Move the uploaded file to the target directory
            move_uploaded_file($comboIdPictureTmpName, $comboIdPicturePath);

            // Check if the image path already exists in the database
            $query = "SELECT SOLDIER_ID FROM UPLOADED_IMAGES WHERE SOLDIER_ID = :soldier_id";
            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':soldier_id', $soldierID);
            oci_execute($stmt);
            $result = oci_fetch_assoc($stmt);

            if ($result) {
                // Update the image path in the database
                $query = "UPDATE UPLOADED_IMAGES SET COMBO_ID_PICTURE_PATH = :image_path WHERE SOLDIER_ID = :soldier_id";
                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':image_path', $comboIdPicturePath);
                oci_bind_by_name($stmt, ':soldier_id', $soldierID);
                oci_execute($stmt);
            } else {
                // Insert the image path into the database
                $query = "INSERT INTO UPLOADED_IMAGES (SOLDIER_ID, COMBO_ID_PICTURE_PATH) VALUES (:soldier_id, :image_path)";
                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':soldier_id', $soldierID);
                oci_bind_by_name($stmt, ':image_path', $comboIdPicturePath);
                oci_execute($stmt);
            }
        }
    }

    oci_close($conn);

    $_SESSION['success'] = "Images uploaded successfully.";
    header("Location: uploadimage.php?soldier=$soldierID");
    exit();
}

include '../includes/header.php';

// Fetch soldier ID and name from the query parameter
if (isset($_GET['soldier'])) {
    $soldierID = $_GET['soldier'];
    $_SESSION['soldier_id'] = $soldierID;

    // Fetch soldier details from the database
    $query = "SELECT SOLDIERID, NAME, COMPANYNAME FROM SOLDIER JOIN COMPANY USING (COMPANYID) WHERE SOLDIERID = :soldier_id";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldier_id', $soldierID);
    oci_execute($stmt);

    $soldier = oci_fetch_assoc($stmt);

    // Redirect if soldier not found
    if (!$soldier) {
        header("Location: soldiers.php");
        exit();
    }

    oci_free_statement($stmt);
} else {
    header("Location: soldiers.php");
    exit();
}

// Fetch uploaded image paths for the soldier
$query = "SELECT PASSPORT_PICTURE_PATH, NID_PICTURE_PATH, COMBO_ID_PICTURE_PATH FROM UPLOADED_IMAGES WHERE SOLDIER_ID = :soldier_id";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':soldier_id', $soldierID);
oci_execute($stmt);

$uploadedImages = oci_fetch_assoc($stmt);

oci_free_statement($stmt);
oci_close($conn);
?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Upload Images</h3>
        </div>
    </div>
</div>
<?php 
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>
<section class="content">
    <div class="container-fluid">
           <?php include '../includes/alert.php'; ?>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Upload Passport Size Picture</h5>
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="passport_picture">Choose Picture:</label>
                                <input type="file" name="passport_picture" id="passport_picture" class="form-control-file" required>
                            </div>
                            <button type="submit" name="upload_submit" class="btn btn-primary">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Upload NID Picture</h5>
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="nid_picture">Choose Picture:</label>
                                <input type="file" name="nid_picture" id="nid_picture" class="form-control-file" required>
                            </div>
                            <button type="submit" name="upload_submit" class="btn btn-primary">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Upload Combo ID Card Picture</h5>
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="combo_id_picture">Choose Picture:</label>
                                <input type="file" name="combo_id_picture" id="combo_id_picture" class="form-control-file" required>
                            </div>
                            <button type="submit" name="upload_submit" class="btn btn-primary">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Uploaded Images</h5>
                        <?php if ($uploadedImages && ($uploadedImages['PASSPORT_PICTURE_PATH'] || $uploadedImages['NID_PICTURE_PATH'] || $uploadedImages['COMBO_ID_PICTURE_PATH'])) : ?>
                            <div class="row">
                                <?php if ($uploadedImages['PASSPORT_PICTURE_PATH']) : ?>
                                    <div class="col-md-4">
                                        <h6>Passport Size Picture:</h6>
                                        <img src="<?php echo $uploadedImages['PASSPORT_PICTURE_PATH']; ?>" class="img-thumbnail" alt="Passport Size Picture">
                                    </div>
                                <?php endif; ?>
                                <?php if ($uploadedImages['NID_PICTURE_PATH']) : ?>
                                    <div class="col-md-4">
                                        <h6>NID Picture:</h6>
                                        <img src="<?php echo $uploadedImages['NID_PICTURE_PATH']; ?>" class="img-thumbnail" alt="NID Picture">
                                    </div>
                                <?php endif; ?>
                                <?php if ($uploadedImages['COMBO_ID_PICTURE_PATH']) : ?>
                                    <div class="col-md-4">
                                        <h6>Combo ID Card Picture:</h6>
                                        <img src="<?php echo $uploadedImages['COMBO_ID_PICTURE_PATH']; ?>" class="img-thumbnail" alt="Combo ID Card Picture">
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else : ?>
                            <p>No images uploaded</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
