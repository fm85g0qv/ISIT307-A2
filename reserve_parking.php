<?php
include 'db_conn.php';
session_start();

// Function to sanitize user input
function sanitize_input($input) {
    return htmlspecialchars(strip_tags($input));
}

// Check if the LotID is provided in the URL along with other relevant details
if (
    isset($_GET['lotID']) &&
    isset($_GET['Location']) &&
    isset($_GET['CostPerHour'])
) {
    $lotID = sanitize_input($_GET['lotID']);
    $location = sanitize_input($_GET['Location']);
    $costPerHour = sanitize_input($_GET['CostPerHour']);
} else {
    // Redirect to the search page if essential details are not provided
    header("Location: search_parking.php");
    exit();
}

// Check if the reservation form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        //Insert a new parking record into the database
        $checkinTime = sanitize_input($_POST['checkinTime']);
        $intendedDuration = sanitize_input($_POST['intendedDuration']);

        // Calculate cost based on costPerHour and intendedDuration
        $cost = $costPerHour * $intendedDuration;

        // Retrieve UserID based on the user's email stored in the session
        $userEmail = $_SESSION['email'];
        $sql_get_user_id = "SELECT UserID FROM Users WHERE Email = '$userEmail'";
        $result_user_id = $dbconn->query($sql_get_user_id);

        if ($result_user_id && $result_user_id->num_rows > 0) {
            $row_user_id = $result_user_id->fetch_assoc();
            $userID = $row_user_id['UserID'];

            // Insert the parking record with the obtained UserID and calculated cost
            $sql_insert_record = "INSERT INTO ParkingRecords (CheckinTime, IntendedDuration, Cost, UserID, LotID) 
                                VALUES ('$checkinTime', $intendedDuration, $cost, $userID, $lotID)";

            if ($dbconn->query($sql_insert_record) === TRUE) {
                echo '<script>alert("Reservation successful!"); window.location.href="search_parking.php";</script>';
            } else {
                throw new Exception("Error creating parking record: " . $dbconn->error);
            }
        } else {
            throw new Exception("Error: User not found.");
        }
    } catch (Exception $e) {
        echo '<script>alert("Error: ' . $e->getMessage() . '");</script>';
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
    <title>Reserve Parking</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Parking Checkin</h2>
	<h3><a href="dashboard.php">Back to Dashboard</a></h3>
    <p>Check in parking for Parking Location: <?= ($_GET['Location']) ?></p>
    <form action="" method="POST">
        <label for="checkinTime">Check-in Time:</label>
        <input type="datetime-local" id="checkinTime" name="checkinTime" required>

        <label for="intendedDuration">Intended Duration (in hours):</label>
        <input type="number" id="intendedDuration" name="intendedDuration" required>

        <input type="submit" value="Reserve">
    </form>
</body>
</html>
