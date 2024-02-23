<?php
// Include your database connection code here if not already included
include 'db_conn.php';


// Fetch existing users for the dropdown
$sql_select_users = "SELECT UserID, CONCAT(Name, ' ', Surname) AS FullName FROM Users";
$result_users = $dbconn->query($sql_select_users);

// Fetch available parking lots for the dropdown
$sql_select_parkinglots = "
    SELECT LotID, Location
    FROM ParkingLots
    WHERE Capacity > (
        SELECT COUNT(RecordID)
        FROM ParkingRecords
        WHERE LotID = ParkingLots.LotID
        AND CheckoutTime IS NULL
    )";

$result_parkinglots = $dbconn->query($sql_select_parkinglots);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Check-in</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Manual Check-in</h2>
<h3><a href="dashboard.php">Back to Dashboard</a></h3>
<form action="process_manual_checkin.php" method="post">
    <label for="user">Select User:</label>
    <select name="user" required>
        <?php while ($row_users = $result_users->fetch_assoc()): ?>
            <option value="<?php echo $row_users['UserID']; ?>"><?php echo $row_users['FullName']; ?></option>
        <?php endwhile; ?>
    </select>
    <br>

    <label for="parkingLot">Select Parking Lot:</label>
    <select name="parkingLot" required>
        <?php while ($row_parkinglots = $result_parkinglots->fetch_assoc()): ?>
            <option value="<?php echo $row_parkinglots['LotID']; ?>"><?php echo $row_parkinglots['Location']; ?></option>
        <?php endwhile; ?>
    </select>
    <br>

    <label for="checkinTime">Check-in Time:</label>
    <input type="datetime-local" name="checkinTime" required>
    <br>

    <label for="intendedDuration">Intended Duration (hours):</label>
    <input type="number" name="intendedDuration" step="0.01" required>
    <br>

    <input type="submit" value="Check-in">
</form>

<script>
    // Display a JavaScript alert on successful check-in
    <?php if (isset($_GET['success']) && $_GET['success'] === 'true'): ?>
        alert("Checked in User!");
    <?php endif; ?>
</script>

</body>
</html>
