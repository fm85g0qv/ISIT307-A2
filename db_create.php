<?php
// Include the database connection file
include 'database_credentials.php';

$dbconn = new mysqli(
    $databaseConfig['servername'],
    $databaseConfig['username'],
    $databaseConfig['password']
);

if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}
// Create the database
$sql_create_db = "CREATE DATABASE IF NOT EXISTS php_db";
if ($dbconn->query($sql_create_db) === TRUE) {
    echo "Database created successfully.<br>";
} else {
    echo "Error creating database: " . $dbconn->error . "<br>";
}

// Close the connection to the MySQL server
$dbconn->close();

// Create a new connection to the created database
include 'db_conn.php';

// Create the Users table
$sql_create_users_table = "CREATE TABLE IF NOT EXISTS Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Surname VARCHAR(255) NOT NULL,
    Phone VARCHAR(20),
    Email VARCHAR(255) UNIQUE NOT NULL, -- Make Email column unique
    UserType ENUM('Administrator', 'User') NOT NULL
)";
if ($dbconn->query($sql_create_users_table) === TRUE) {
    echo "Users table created successfully.<br>";
} else {
    echo "Error creating Users table: " . $dbconn->error . "<br>";
}

// Create the ParkingLots table
$sql_create_parkinglots_table = "CREATE TABLE IF NOT EXISTS ParkingLots (
    LotID INT AUTO_INCREMENT PRIMARY KEY,
    Location VARCHAR(255) UNIQUE NOT NULL,
    Description VARCHAR(255),
    CostPerHour DECIMAL(10,2) NOT NULL,
    CostPerHourLateCheckout DECIMAL(10,2) NOT NULL,
    Capacity INT CHECK (Capacity >= 0) NOT NULL
)";
if ($dbconn->query($sql_create_parkinglots_table) === TRUE) {
    echo "ParkingLots table created successfully.<br>";
} else {
    echo "Error creating ParkingLots table: " . $dbconn->error . "<br>";
}

// Create the ParkingRecords table with IntendedDuration
$sql_create_parkingrecords_table = "CREATE TABLE IF NOT EXISTS ParkingRecords (
    RecordID INT AUTO_INCREMENT PRIMARY KEY,
    CheckinTime DATETIME NOT NULL,
    CheckoutTime DATETIME,
    Cost DECIMAL(10,2),
    LateCheckoutCost DECIMAL(10,2),
    IntendedDuration DECIMAL(4,2), -- Supports up to 99.99 hours.
    UserID INT,
    LotID INT,
    FOREIGN KEY (UserID) REFERENCES Users(UserID),
    FOREIGN KEY (LotID) REFERENCES ParkingLots(LotID)
)";
if ($dbconn->query($sql_create_parkingrecords_table) === TRUE) {
    echo "ParkingRecords table created successfully.<br>";
	echo "<a href=index.php>go to index</a>";
} else {
    echo "Error creating ParkingRecords table: " . $dbconn->error . "<br>";
}

// Close the connection to the database
$dbconn->close();
?>
