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

include '../includes/header.php';

?>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <div class="text-left">
            <h3>Upload Images</h3>
        </div>
        <div class="text-right">
            <!-- Button to Trigger Upload Modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadModal">
                Upload Image
            </button>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <?php include '../includes/alert.php'; ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Uploaded Images</h5>
                        <?php if ($uploadedImages && ($uploadedImages['PASSPORT_PICTURE_PATH'] || $uploadedImages['NID_PICTURE_PATH'] || $uploadedImages['COMBO_ID_PICTURE_PATH'])): ?>
                            <div class="row">
                                <?php if ($uploadedImages['PASSPORT_PICTURE_PATH']): ?>
                                    <div class="col-md-4">
                                        <h6>Passport Size Picture:</h6>
                                        <img src="<?php echo $uploadedImages['PASSPORT_PICTURE_PATH']; ?>" class="img-thumbnail"
                                            alt="Passport Size Picture">
                                    </div>
                                <?php endif; ?>
                                <?php if ($uploadedImages['NID_PICTURE_PATH']): ?>
                                    <div class="col-md-4">
                                        <h6>NID Picture:</h6>
                                        <img src="<?php echo $uploadedImages['NID_PICTURE_PATH']; ?>" class="img-thumbnail"
                                            alt="NID Picture">
                                    </div>
                                <?php endif; ?>
                                <?php if ($uploadedImages['COMBO_ID_PICTURE_PATH']): ?>
                                    <div class="col-md-4">
                                        <h6>Combo ID Card Picture:</h6>
                                        <img src="<?php echo $uploadedImages['COMBO_ID_PICTURE_PATH']; ?>" class="img-thumbnail"
                                            alt="Combo ID Card Picture">
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <p>No images uploaded</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Upload Image Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="image_type">Select Image Type:</label>
                        <select class="form-control" id="image_type" onchange="toggleUploadField()">
                            <option value="passport_picture">Passport Size Picture</option>
                            <option value="nid_picture">NID Picture</option>
                            <option value="combo_id_picture">Combo ID Card Picture</option>
                        </select>
                    </div>
                    <div id="upload_field">
                        <div class="form-group">
                            <label for="selected_picture">Choose Picture:</label>
                            <input type="file" name="selected_picture" id="selected_picture" class="form-control-file"
                                required>
                        </div>
                        <button type="submit" name="upload_submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>