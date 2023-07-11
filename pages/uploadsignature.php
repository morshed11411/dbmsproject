<?php
session_start();

include '../includes/connection.php';

// Process the form submission to upload the signature
if (isset($_POST['upload_submit'])) {
    $soldierID = $_SESSION['userid'];
    $targetDirectory = "../images/"; // Folder to store the images
    $allowedExtensions = array('jpg', 'jpeg', 'png'); // Allowed file extensions

    // Process the signature upload
    if ($_FILES['signature']['name']) {
        $signatureName = $_FILES['signature']['name'];
        $signatureTmpName = $_FILES['signature']['tmp_name'];
        $signatureExtension = pathinfo($signatureName, PATHINFO_EXTENSION);

        // Check if the file extension is allowed
        if (in_array($signatureExtension, $allowedExtensions)) {
            $signaturePath = $targetDirectory . $soldierID . '_signature.' . $signatureExtension;

            // Move the uploaded file to the target directory
            move_uploaded_file($signatureTmpName, $signaturePath);

            // Check if the signature path already exists in the database
            $query = "SELECT SOLDIER_ID FROM UPLOADED_IMAGES WHERE SOLDIER_ID = :soldier_id";
            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':soldier_id', $soldierID);
            oci_execute($stmt);
            $result = oci_fetch_assoc($stmt);

            if ($result) {
                // Update the signature path in the database
                $query = "UPDATE UPLOADED_IMAGES SET SIGNATURE_PATH = :image_path WHERE SOLDIER_ID = :soldier_id";
                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':image_path', $signaturePath);
                oci_bind_by_name($stmt, ':soldier_id', $soldierID);
                oci_execute($stmt);
            } else {
                // Insert the signature path into the database
                $query = "INSERT INTO UPLOADED_IMAGES (SOLDIER_ID, SIGNATURE_PATH) VALUES (:soldier_id, :image_path)";
                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':soldier_id', $soldierID);
                oci_bind_by_name($stmt, ':image_path', $signaturePath);
                oci_execute($stmt);
            }
        }
    }

    oci_close($conn);

    $_SESSION['success'] = "Signature uploaded successfully.";
    header("Location: uploadsignature.php?soldier=$soldierID");
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
$query = "SELECT SIGNATURE_PATH FROM UPLOADED_IMAGES WHERE SOLDIER_ID = :soldier_id";
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
            <h3>Upload Signature</h3>
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
                        <h5>Upload Signature</h5>
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="signature">Choose Signature Image:</label>
                                <input type="file" name="signature" id="signature" class="form-control-file" required>
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
                        <h5>Uploaded Signature</h5>
                        <?php if ($uploadedImages && $uploadedImages['SIGNATURE_PATH']) : ?>
                            <div>
                                <img src="<?php echo $uploadedImages['SIGNATURE_PATH']; ?>" alt="Uploaded Signature" class="img-thumbnail">
                            </div>
                        <?php else : ?>
                            <p>No signature uploaded</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include '../includes/footer.php'; ?>


