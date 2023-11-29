<?php
// Check if the soldier ID is present in the session or URL parameter
if (isset($soldierID)) {

    // Build the SQL query to retrieve the profile picture
    $query = "SELECT * FROM soldier_view WHERE SOLDIERID = :soldierId";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':soldierId', $soldierID);
    oci_execute($stmt);

    // Fetch the result row
    $soldierProfileInfo = oci_fetch_assoc($stmt);

    // Check if a profile picture is found
    if ($row && !empty($soldierProfileInfo['PROFILEPICTURE'])) {
        $profilePicture = $soldierProfileInfo['PROFILEPICTURE'];
    } else {
        $profilePicture = '../images/default_profile_picture.png';
    }
} else {
    // Set a default profile picture path if the soldier ID is not present in the session
    $profilePicture = '../images/default_profile_picture.png';
}
?>



<!-- Profile Image -->

<div class="card card-primary">

    <div class="card-body box-profile mt-2">
        <div class="text-center">
            
        <img class="profile-user-img img-fluid img-circle rounded-circle" src="<?php echo $profilePicture; ?>" alt="Profile Picture">
        </div>

        <h3 class="profile-username text-center">
            <?= $soldierProfileInfo['RANK'] . ' ' . $soldierProfileInfo['NAME'] ?>
        </h3>

        <p class="text-muted text-center">
            <?= $soldierProfileInfo['COMPANYNAME'] ?>
        </p>
    </div>
</div>
