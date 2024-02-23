<?php
// dashboard.php

// Start the session
session_start();

// Check if the user is logged in, redirect to login page if not
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['user_type'] === 'Administrator') {
	
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<h2>Welcome to the Dashboard, <?php echo $_SESSION['email'] . " | " . $_SESSION['user_type']; ?>!</h2>
	<?=include 'navbar.php'?>

</body>
</html>
