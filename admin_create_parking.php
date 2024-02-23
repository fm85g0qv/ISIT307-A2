<?php
// Include the database connection file
include 'db_conn.php';

// Initialize variables for pop-up messages
$successMessage = $errorMessage = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $location = $_POST['location'];
    $description = $_POST['description'];
    $costPerHour = $_POST['costPerHour'];
    $costPerHourLateCheckout = $_POST['costPerHourLateCheckout'];
    $capacity = $_POST['capacity'];

    try {
        // Insert data into ParkingLots table using prepared statement
        $sql_insert_parkinglot = "INSERT INTO ParkingLots (Location, Description, CostPerHour, CostPerHourLateCheckout, Capacity)
                                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $dbconn->prepare($sql_insert_parkinglot);
        $stmt->bind_param("ssddd", $location, $description, $costPerHour, $costPerHourLateCheckout, $capacity);
        $stmt->execute();

        $successMessage = "Parking lot created successfully.";
        // JavaScript for pop-up message on success
        echo '<script>alert("' . $successMessage . '"); window.location.href = "admin_create_parking.php";</script>';
    } catch (Exception $e) {
        $errorMessage = "Error creating parking lot: " . $e->getMessage();
        // JavaScript for pop-up message on failure
        echo '<script>alert("' . $errorMessage . '"); window.location.href = "admin_create_parking.php";</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Parking Lot</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Create Parking Lot</h2>
<h3><a href="dashboard.php">Back to Dashboard</a></h3>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    Location: <input type="text" name="location" required><br>
    Description: <input type="text" name="description"><br>
    Cost Per Hour: <input type="text" name="costPerHour" required><br>
    Cost Per Hour (Late Checkout): <input type="text" name="costPerHourLateCheckout" required><br>
    Capacity: <input type="number" name="capacity" required><br>
    <input type="submit" value="Create Parking Lot">
</form>

</body>
</html>
