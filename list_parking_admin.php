<?php
include 'db_conn.php';

// Function to sanitize user input
function sanitize_input($input) {
    return htmlspecialchars(strip_tags($input));
}

// Initialize list option
$listOption = isset($_GET['listOption']) ? sanitize_input($_GET['listOption']) : 'all';

// what to query based on the selected option
switch ($listOption) {
    case 'available':
        $sql = "SELECT 
                    pl.LotID,
                    pl.Location,
                    pl.Description,
                    pl.Capacity,
                    COUNT(pr.RecordID) AS CheckedInCount
                FROM ParkingLots pl
                LEFT JOIN ParkingRecords pr ON pl.LotID = pr.LotID
                WHERE (pr.CheckoutTime IS NULL OR (pr.CheckoutTime IS NOT NULL AND NOW() < DATE_ADD(pr.CheckinTime, INTERVAL pr.IntendedDuration MINUTE)))
                GROUP BY pl.LotID
                HAVING (pl.Capacity - CheckedInCount) > 0";
        break;
    case 'full':
        $sql = "SELECT 
                    pl.LotID,
                    pl.Location,
                    pl.Description,
                    pl.Capacity,
                    COUNT(pr.RecordID) AS CheckedInCount
                FROM ParkingLots pl
                LEFT JOIN ParkingRecords pr ON pl.LotID = pr.LotID
                WHERE (pr.CheckoutTime IS NULL OR (pr.CheckoutTime IS NOT NULL AND NOW() < DATE_ADD(pr.CheckinTime, INTERVAL pr.IntendedDuration MINUTE)))
                GROUP BY pl.LotID
                HAVING (pl.Capacity - CheckedInCount) = 0";
        break;
    default:
        $sql = "SELECT 
                    pl.LotID,
                    pl.Location,
                    pl.Description,
                    pl.Capacity,
                    COUNT(pr.RecordID) AS CheckedInCount
                FROM ParkingLots pl
                LEFT JOIN ParkingRecords pr ON pl.LotID = pr.LotID
                GROUP BY pl.LotID";
        break;
}

$result = $dbconn->query($sql);

if (!$result) {
    die("Error executing query: " . $dbconn->error);
}

// Close the database connection
$dbconn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking List - Admin</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Parking List - Admin</h2>
	<h3><a href="dashboard.php">Back to Dashboard</a></h3>
    <form action="" method="GET">
        <label for="listOption">Select List Option:</label>
        <select name="listOption" id="listOption">
            <option value="all" <?php echo ($listOption === 'all') ? 'selected' : ''; ?>>All Parking</option>
            <option value="available" <?php echo ($listOption === 'available') ? 'selected' : ''; ?>>Available Parking</option>
            <option value="full" <?php echo ($listOption === 'full') ? 'selected' : ''; ?>>Full Parking (0 Available Spaces)</option>
        </select>
        <input type="submit" value="List">
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>ParkingID</th>
                <th>Location</th>
                <th>Description</th>
                <th>Capacity</th>
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
                    <td><?php echo $row['Capacity'] - $row['CheckedInCount']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
