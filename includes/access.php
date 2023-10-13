<?php
session_start(); // Start the session

// Define user roles
$userRoles = [
    'CO', '2IC', 'Adjt', 'QM', 'BSM', 'RP NCO', 'Trg NCO',
    'Company Commander', 'CSM', 'Coy Clerk', 'Others'
];

// Function to check if a user has access to a page/action
function hasAccess($requiredRole, $companyID = null) {
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
function canAccessCompanyData($userRole, $companyID) {
    // Implement logic to check if $userRole is allowed to access data for $companyID
    // Return true if allowed, false otherwise
}


?>