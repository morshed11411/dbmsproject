<?php


// Check if already on the target page to avoid redirection loop
$current_page = basename($_SERVER['PHP_SELF']);
$target_pages = ['dashboard.php', 'coy-dashboard.php', 'profile.php'];

if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            if ($current_page != 'dashboard.php') {
                header('Location: dashboard.php');
            }
            break;
        case 'manager':
            if ($current_page != 'coy-dashboard.php') {
                header('Location: coy-dashboard.php');
            }
            break;
        case 'soldier':
            if ($current_page != 'profile.php') {
                header('Location: profile.php');
            }
            break;
        default:
            // Handle other roles or unexpected cases
            break;
    }
} else {
    // Handle the case when the role is not set in the session
    // Redirect to a default location or show an error message
    if (!in_array($current_page, $target_pages)) {
        header('Location: login.php'); // Redirect to the login page, for example
    }
}
?>

<!-- <?php
session_start(); // Start the session

// Define user roles
$appts = [
    'CO',
    '2IC',
    'Adjt',
    'QM',
    'BSM',
    'RP NCO',
    'Trg NCO',
    'Company Commander',
    'CSM',
    'Coy Clerk',
    'Others'
];

// Function to check if a user has access to a page/action
function hasAccess($requiredRole, $companyID = null)
{
    global $userRoles;

    // Check if the user is logged in and their role is allowed
    if (isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], $userRoles)) {
        $userRole = $_SESSION['user_role'];

        // Check specific role-based permissions
        if ($userRole === 'CO' || $userRole === '2IC' || $userRole === 'Adjt' || $userRole === 'QM') {
            return true; // Admins have access to everything
        } elseif ($userRole === 'BSM' && $requiredRole === 'BSM') {
            return true; // BSM can access specific functions
        } elseif ($userRole === 'RP NCO' && $requiredRole === 'RP NCO') {
            return true; // RP NCO can access RP Gate
        } elseif ($userRole === 'Trg NCO' && $requiredRole === 'Trg NCO') {
            return true; // Trg NCO can access training page
        } elseif ($companyID && ($userRole === 'Company Commander' || $userRole === 'CSM' || $userRole === 'Coy Clerk')) {
            // Check if the user is allowed to access data for their company
            // You can implement this check based on the user's company ID
            return canAccessCompanyData($userRole, $companyID);
        } elseif ($userRole === 'Others') {
            return true; // Others can only see their own profile
        }
    }

    return false; // User does not have access
}

// Function to check if a user can access data for their company
function canAccessCompanyData($userRole, $companyID)
{
    // Implement logic to check if $userRole is allowed to access data for $companyID
    // Return true if allowed, false otherwise
}


?> -->