<?php
// Start the session
session_start();

// Check if the login form is submitted
if (isset($_POST['login'])) {
    // Get the form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Establish a connection to the Oracle database
    $conn = oci_connect('UMS', '12345', 'localhost/XE');
    if (!$conn) {
        $e = oci_error();
        echo "Failed to connect to Oracle: " . $e['message'];
    } else {
        // Prepare the SQL statement
        $query = "SELECT * FROM soldier WHERE soldierid = :username AND password = :password";
        $stmt = oci_parse($conn, $query);

        // Bind the parameters
        oci_bind_by_name($stmt, ':username', $username);
        oci_bind_by_name($stmt, ':password', $password);

        // Execute the statement
        oci_execute($stmt);

        // Check if a matching user is found
        if ($row = oci_fetch_assoc($stmt)) {
            // User is authenticated
            $_SESSION['username'] = $row['SOLDIERID']; // Ensure the column name is correct
            // You can store other relevant data as needed

            // Redirect to the dashboard or desired page
            header("Location: dashboard.php");
            exit();
        } else {
            // Invalid credentials
            echo "Invalid username or password.";
        }

        oci_free_statement($stmt);
        oci_close($conn);
    }
}
?>
