<?php
include_once 'User.php';
include 'db_conn.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $type = $_POST['type'];

    // Create a new User object
    $user = new User($name, $surname, $phone, $email, $type);

    // Check if the email already exists in the database
    $existingUserType = $user->getUserType($email, $dbconn);

    if ($existingUserType !== null) {
        // Email already exists
        echo "<script>alert('Email already exists. Please use a different email address.');</script>";
    } else {
        // Add user to the database
        $userId = $user->addUserToDatabase($dbconn);

        // Check if the user was successfully added
        if ($userId) {
            // Registration successful
            echo "<script>alert('Registration successful! Please login at the next page'); window.location.href='login.php';</script>";
        } else {
            // Registration failed
            echo "<script>alert('Registration failed. Please try again.');</script>";
        }
    }
}

// Close the database connection
$dbconn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>

<form method="post" action="">
    <label for="name">Name:</label>
    <input type="text" name="name" required><br>

    <label for="surname">Surname:</label>
    <input type="text" name="surname" required><br>

    <label for="phone">Phone:</label>
    <input type="text" name="phone" pattern="[0-9]+" title="Enter a valid phone number" required><br>

    <label for="email">Email:</label>
    <input type="email" name="email" required><br>

    <label for="type">Type:</label>
	<select name="type" required>
		<option value="ADMINISTRATOR">ADMINISTRATOR</option>
		<option value="USER">USER</option>
	</select><br>


    <input type="submit" value="Register">
</form>

</body>
</html>
