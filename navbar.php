<?php

// Check if the user_type is set in the session
if (isset($_SESSION['user_type'])) {
    $userType = $_SESSION['user_type'];
} else {
    // Default to regular user if user_type is not set
    $userType = 'User';
}

?>

<nav>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="search_parking.php">List and Search Parking Spaces</a></li>

        <?php if ($userType === 'Administrator'): ?>
            <li><a href="admin_create_parking.php">Create Parking Locations</a></li>
            <li><a href="list_checked_in_users_admin.php">List Checked In Users</a></li>
            <li><a href="list_parking_admin.php">View Parking Spaces</a></li>
            <li><a href="manual_checkin.php">Check in a User</a></li>
			<li><a href="search_users_admin.php">Search a User</a></li>
        <?php else: ?>
            <li><a href="reserve_parking.php">Check into Parking</a></li>
            <li><a href="user_list_parking.php">List Parking Records</a></li>
            <li><a href="user_checkout.php">Check Out from Parking</a></li>
        <?php endif; ?>
    </ul>
</nav>
