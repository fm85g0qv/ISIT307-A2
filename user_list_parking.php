<?php
include 'db_conn.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not
    header("Location: login.php");
    exit();
}

// Get user's email from session
$userEmail = $_SESSION['email'];

// Query to retrieve parking records with only check-in time (current records)
$sql_current_records = "SELECT pr.RecordID, pr.CheckinTime, pl.Location
                       FROM ParkingRecords pr
                       INNER JOIN ParkingLots pl ON pr.LotID = pl.LotID
                       WHERE pr.UserID = (SELECT UserID FROM Users WHERE Email = '$userEmail') 
                       AND pr.CheckoutTime IS NULL";

$result_current_records = $dbconn->query($sql_current_records);

// Query to retrieve parking records with checkout time (past records)
$sql_past_records = "SELECT pr.RecordID, pr.CheckinTime, pr.CheckoutTime, pr.Cost, pl.Location
                    FROM ParkingRecords pr
                    INNER JOIN ParkingLots pl ON pr.LotID = pl.LotID
                    WHERE pr.UserID = (SELECT UserID FROM Users WHERE Email = '$userEmail') 
                    AND pr.CheckoutTime IS NOT NULL";

$result_past_records = $dbconn->query($sql_past_records);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Parking Records</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>

    <h2>User Parking Records - <?=$_SESSION['email']?></h2>
	<h3><a href="dashboard.php">Back to Dashboard</a></h3>

    <h3>Current Records</h3>
    <table border="1">
        <tr>
            <th>RecordID</th>
            <th>CheckinTime</th>
            <th>Location</th>
        </tr>
        <?php
        // Display current records in a table
        while ($row = $result_current_records->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['RecordID']}</td>";
            echo "<td>{$row['CheckinTime']}</td>";
            echo "<td>{$row['Location']}</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <h3>Past Records</h3>
    <table border="1">
        <tr>
            <th>RecordID</th>
            <th>CheckinTime</th>
            <th>CheckoutTime</th>
            <th>Cost</th>
            <th>Location</th>
        </tr>
        <?php
        // Display past records in a table
        while ($row = $result_past_records->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['RecordID']}</td>";
            echo "<td>{$row['CheckinTime']}</td>";
            echo "<td>{$row['CheckoutTime']}</td>";
            echo "<td>{$row['Cost']}</td>";
            echo "<td>{$row['Location']}</td>";
            echo "</tr>";
        }
        ?>
    </table>

</body>
</html>
