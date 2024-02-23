<?php
session_start();

// Include the database connection file
include 'db_conn.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page or handle unauthorized access
    header('Location: login.php');
    exit();
}

// Get the user's email from the session
$userEmail = $_SESSION['email'];

// Query to retrieve user records without a checkout time
$sql = "SELECT pr.RecordID, pr.CheckinTime, pr.Cost, pl.CostPerHourLateCheckout, pl.Location
        FROM ParkingRecords pr
        INNER JOIN ParkingLots pl ON pr.LotID = pl.LotID
        INNER JOIN Users u ON pr.UserID = u.UserID
        WHERE u.Email = '$userEmail' AND pr.CheckoutTime IS NULL";

// Execute the query
$result = $dbconn->query($sql);

// Display the records in a table
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Records</title>
	<link rel="stylesheet" href="style.css">
</head>

<body>
    <h2>User Records</h2>
	<h3><a href="dashboard.php">Back to Dashboard</a></h3>
    <table border="1">
        <tr>
            <th>RecordID</th>
            <th>Checkin Time</th>
            <th>Cost per Hour</th>
            <th>Late Checkout Cost Per Hour</th>
            <th>Location</th>
            <th>Action</th>
        </tr>

        <?php
        // Display user records in the table
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['RecordID']}</td>";
            echo "<td>{$row['CheckinTime']}</td>";
            echo "<td>{$row['Cost']}</td>";
            echo "<td>{$row['CostPerHourLateCheckout']}</td>";
            echo "<td>{$row['Location']}</td>";
            echo "<td><form method='POST' action='checkout_process.php'>
                      <input type='hidden' name='recordID' value='{$row['RecordID']}'>
                      <button type='submit'>Checkout</button>
                  </form></td>";
            echo "</tr>";
        }
        ?>

    </table>
</body>

</html>
