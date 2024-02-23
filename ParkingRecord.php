<?php
// Include db_conn.php
include 'db_conn.php';

// Create a ParkingRecord class
class ParkingRecord {
    public $checkinTime;
    public $checkoutTime;
    public $cost;
    public $lateCheckoutCost;
    public $intendedDuration;
    public $userID;
    public $lotID;

    // Constructor
    public function __construct($checkinTime, $intendedDuration, $userID, $lotID) {
        $this->checkinTime = $checkinTime;
        $this->intendedDuration = $intendedDuration;
        $this->userID = $userID;
        $this->lotID = $lotID;
    }
}

// Function to add ParkingRecord to the database
function addParkingRecord($parkingRecord, $dbconn) {
    // Escape values to prevent SQL injection
    $checkinTime = mysqli_real_escape_string($dbconn, $parkingRecord->checkinTime);
    $intendedDuration = mysqli_real_escape_string($dbconn, $parkingRecord->intendedDuration);
    $userID = mysqli_real_escape_string($dbconn, $parkingRecord->userID);
    $lotID = mysqli_real_escape_string($dbconn, $parkingRecord->lotID);

    // Insert the record into the ParkingRecords table
    $sql = "INSERT INTO ParkingRecords (CheckinTime, IntendedDuration, UserID, LotID) VALUES ('$checkinTime', $intendedDuration, $userID, $lotID)";

    if ($dbconn->query($sql) === TRUE) {
        echo "ParkingRecord added successfully.<br>";
    } else {
        echo "Error adding ParkingRecord: " . $dbconn->error . "<br>";
    }
}

// Close the database connection
$dbconn->close();
?>
