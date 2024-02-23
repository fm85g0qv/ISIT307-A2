<?php
// Include your database connection code here
include 'db_conn.php';

// Retrieve Lot Locations for the drop-down menu
$sql_get_lot_locations = "SELECT LotID, Location FROM ParkingLots";
$result_lot_locations = $dbconn->query($sql_get_lot_locations);

if (!$result_lot_locations) {
    echo "Error retrieving lot locations: " . $dbconn->error;
}

// HTML Header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checked-In Users in a Parking Location</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Checked-In Users in a Parking Location</h1>
<h3><a href="dashboard.php">Back to Dashboard</a></h3>
<!-- Drop-down menu for Lot Locations -->
<form action="" method="post">
    <label for="lotSelect">Select Parking Location:</label>
    <select name="lotSelect" id="lotSelect">
        <?php
        while ($row = $result_lot_locations->fetch_assoc()) {
            echo "<option value='{$row['LotID']}'>{$row['Location']}</option>";
        }
        ?>
    </select>
    <input type="submit" value="Show Checked-In Users">
</form>

<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get selected LotID from the form
    $selectedLotID = $_POST['lotSelect'];

    // Retrieve checked-in users for the selected lot
    $sql_checked_in_users = "SELECT Users.UserID, Users.Name, Users.Email, ParkingRecords.CheckinTime
                             FROM Users
                             INNER JOIN ParkingRecords ON Users.UserID = ParkingRecords.UserID
                             WHERE ParkingRecords.LotID = $selectedLotID
                               AND ParkingRecords.CheckoutTime IS NULL";

    $result_checked_in_users = $dbconn->query($sql_checked_in_users);

    if (!$result_checked_in_users) {
        echo "Error retrieving checked-in users: " . $dbconn->error;
    } else {
        // Display the table of checked-in users
        echo "<h2>Checked-In Users at Selected Lot</h2>";
        echo "<table border='1'>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Check-In Time</th>
                </tr>";

        while ($userRow = $result_checked_in_users->fetch_assoc()) {
            echo "<tr>
                    <td>{$userRow['UserID']}</td>
                    <td>{$userRow['Name']}</td>
                    <td>{$userRow['Email']}</td>
                    <td>{$userRow['CheckinTime']}</td>
                  </tr>";
        }

        echo "</table>";
    }
}

// HTML Footer
?>
</body>
</html>
