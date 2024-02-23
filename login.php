<?php

session_start();
require_once('db_conn.php');
require('User.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Prepare and execute the query
    $stmt = $dbconn->prepare("SELECT Email FROM Users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($resultEmail);
    $stmt->fetch();
    $stmt->close();

    // Check if the email exists in the database
    if ($resultEmail === $email) {
        $_SESSION['email'] = $email;
		
		//get the logged in user's type and assign to session variable
		$user = new User("", "", "", $email, "");
		$userType = $user->getUserType($email, $dbconn);
		$_SESSION['user_type'] = $userType;
		
		//go to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Login failed. Email not found.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Login</h2>
    <form action="login.php" method="post">
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <button type="submit">Login</button>
    </form>
	
	<p>Don't have an account? <a href="register.php">Register</a></p>
</body>
</html>
