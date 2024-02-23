<?php
include 'db_conn.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $userID = $_POST["user"];
    $lotID = $_POST["parkingLot"];
    $checkinTime = $_POST["checkinTime"];
    $intendedDuration = $_POST["intendedDuration"];

    // Insert the manual check-in record into ParkingRecords table
    $sql_insert_record = "INSERT INTO ParkingRecords (CheckinTime, IntendedDuration, UserID, LotID) 
                         VALUES ('$checkinTime', $intendedDuration, $userID, $lotID)";

    if ($dbconn->query($sql_insert_record) === TRUE) {
        // Update the Capacity in ParkingLots table
        $sql_update_capacity = "UPDATE ParkingLots SET Capacity = Capacity - 1 WHERE LotID = $lotID";
        $dbconn->query($sql_update_capacity);

        // Redirect back to manual_checkin.php with a success parameter
        header("Location: manual_checkin.php?success=true");
        exit();
    } else {
        // Redirect back to manual_checkin.php with an error parameter
        header("Location: manual_checkin.php?error=" . urlencode($dbconn->error));
        exit();
    }
} else {
    // Redirect back to manual_checkin.php if the form is not submitted
    header("Location: manual_checkin.php");
    exit();
}
?>
