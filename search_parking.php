<?php
include 'db_conn.php';
session_start();

// Function to sanitize user input
function sanitize_input($input) {
    return htmlspecialchars(strip_tags($input));
}

// Initialize search parameters
$parkingID = isset($_GET['parkingID']) ? sanitize_input($_GET['parkingID']) : '';
$location = isset($_GET['location']) ? sanitize_input($_GET['location']) : '';
$description = isset($_GET['description']) ? sanitize_input($_GET['description']) : '';

// Check if the Reset button is clicked
if (isset($_GET['reset'])) {
    // Reset the search parameters
    $parkingID = '';
    $location = '';
    $description = '';
}

// Construct the SQL query
$sql = "SELECT 
            pl.LotID,
            pl.Location,
            pl.Description,
            pl.Capacity,
            pl.CostPerHour,
            pl.CostPerHourLateCheckout,
            (pl.Capacity - COUNT(pr.RecordID)) AS AvailableSpaces
        FROM ParkingLots pl
        LEFT JOIN ParkingRecords pr ON pl.LotID = pr.LotID AND pr.CheckoutTime IS NULL
        WHERE pl.LotID LIKE '%$parkingID%'
            AND pl.Location LIKE '%$location%'
            AND pl.Description LIKE '%$description%'
            AND (pr.CheckoutTime IS NULL OR (pr.CheckoutTime IS NOT NULL AND NOW() < DATE_ADD(pr.CheckinTime, INTERVAL pr.IntendedDuration MINUTE)))
        GROUP BY pl.LotID";

$result = $dbconn->query($sql);

if (!$result) {
    die("Error executing query: " . $dbconn->error);
}

// Display search results in a table
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Search</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Parking Search</h2>
	<h3><a href="dashboard.php">Back to Dashboard</a></h3>
    <form action="" method="GET">
        ParkingID: <input type="text" name="parkingID" value="<?php echo $parkingID; ?>">
        Location: <input type="text" name="location" value="<?php echo $location; ?>">
        Description: <input type="text" name="description" value="<?php echo $description; ?>">
        <input type="submit" value="Search">
        <button type="submit" name="reset">Reset</button>
    </form>
    <table border="1">
        <thead>
            <tr>
                <th>ParkingID</th>
                <th>Location</th>
                <th>Description</th>
                <th>Capacity</th>
                <th>Cost Per Hour</th>
                <th>Cost Per Hour Late Checkout</th>
                <th>Available Spaces</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['LotID']; ?></td>
        <td><?php echo $row['Location']; ?></td>
        <td><?php echo $row['Description']; ?></td>
        <td><?php echo $row['Capacity']; ?></td>
        <td><?php echo $row['CostPerHour']; ?></td>
        <td><?php echo $row['CostPerHourLateCheckout']; ?></td>
        <td><?php echo $row['AvailableSpaces']; ?></td>
		<td>
			<?php
			// Display the reservation button if available spaces are greater than 0
			if ($row['AvailableSpaces'] > 0) {
				echo '<a href="reserve_parking.php?lotID=' . $row['LotID'] . '&Location=' . $row['Location'] . '&CostPerHour=' . $row['CostPerHour'] . '">Reserve</a>';
			} else {
				echo 'No Available Spaces';
			}
			?>
		</td>
    </tr>
<?php } ?>
        </tbody>
    </table>
</body>
</html>
<?php
// Close the database connection
$dbconn->close();
?>
