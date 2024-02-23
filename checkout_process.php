<?php
// Function to update the checkout time of a parking record
include 'db_conn.php';
function updateCheckoutTime($recordID, $dbconn) {
    // Set the default timezone to UTC
    date_default_timezone_set('Asia/Singapore');
	$currentTime = date("Y-m-d H:i:s");

    $currentTime = $dbconn->real_escape_string($currentTime); // Escape the string for safety

    $sql_update_checkout = "UPDATE ParkingRecords SET CheckoutTime = '$currentTime' WHERE RecordID = $recordID";
    if ($dbconn->query($sql_update_checkout) === TRUE) {
        return $currentTime;
    } else {
        return "Error updating checkout time: " . $dbconn->error;
    }
}

function calculateLateCheckoutCost($dbconn, $recordID) {
    // Step 1: Query current time
	date_default_timezone_set('Asia/Singapore');
    $currentTime = date("Y-m-d H:i:s");

    // Step 2: Add CheckinTime and IntendedDuration (hours)
    $sql = "SELECT CheckinTime, IntendedDuration, LotID FROM ParkingRecords WHERE RecordID = $recordID";
    $result = $dbconn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $checkinTime = $row['CheckinTime'];
        $intendedDuration = $row['IntendedDuration'];
        $lotID = $row['LotID'];

        $checkoutDateTime = new DateTime($currentTime);
		$checkoutDateTime->modify("+ $intendedDuration hours");


        // Step 3: Calculate the difference in hours
        $hoursDifference = $checkoutDateTime->diff($checkinDateTime)->h;

        // Step 4: Fetch CostPerHourLateCheckout from ParkingLots
        $sqlCost = "SELECT CostPerHourLateCheckout FROM ParkingLots WHERE LotID = $lotID";
        $resultCost = $dbconn->query($sqlCost);

        if ($resultCost->num_rows > 0) {
            $rowCost = $resultCost->fetch_assoc();
            $costPerHourLateCheckout = $rowCost['CostPerHourLateCheckout'];

            // Step 5: Calculate LateCheckoutCost
            $lateCheckoutCost = $hoursDifference * $costPerHourLateCheckout;

            // Step 6: Update LateCheckoutCost in ParkingRecords
            $sqlUpdate = "UPDATE ParkingRecords SET LateCheckoutCost = $lateCheckoutCost WHERE RecordID = $recordID";
            if ($dbconn->query($sqlUpdate) === TRUE) {
                return "Late checkout cost calculated and updated successfully.";
            } else {
                return "Error updating LateCheckoutCost: " . $dbconn->error;
            }
        } else {
            return "Error fetching CostPerHourLateCheckout: " . $dbconn->error;
        }
    } else {
        return "Record not found.";
    }
}


// Function to calculate and update LateCheckoutCost
/* function updateLateCheckoutCost($recordID, $dbconn) {
    $sql_select_record = "SELECT CheckinTime, IntendedDuration, LotID FROM ParkingRecords WHERE RecordID = $recordID";
    $result = $dbconn->query($sql_select_record);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $checkinTime = strtotime($row['CheckinTime']);
        $intendedDuration = $row['IntendedDuration'];
        $lotID = $row['LotID'];

        $currentTimestamp = time();
        $elapsedTime = ($currentTimestamp - $checkinTime) / 3600; // Convert seconds to hours

        $lateCheckoutCost = max(0, ($elapsedTime - $intendedDuration) * getLateCheckoutCostPerHour($lotID, $dbconn));

        $sql_update_late_cost = "UPDATE ParkingRecords SET LateCheckoutCost = $lateCheckoutCost WHERE RecordID = $recordID";
        
        if ($dbconn->query($sql_update_late_cost) === TRUE) {
            return "Late checkout cost updated: $" . number_format($lateCheckoutCost, 2);
        } else {
            return "Error updating late checkout cost: " . $dbconn->error;
        }
    } else {
        return "Record not found";
    }
} */

// Function to query and return the cost of a parking record
function getParkingRecordCost($recordID, $dbconn) {
    $sql_select_cost = "SELECT Cost FROM ParkingRecords WHERE RecordID = $recordID";
    $result = $dbconn->query($sql_select_cost);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['Cost'];
    } else {
        return "Record not found";
    }
}

// Helper function to get the CostPerHourLateCheckout from ParkingLots table
/* function getLateCheckoutCostPerHour($lotID, $dbconn) {
    $sql_select_cost_per_hour = "SELECT CostPerHourLateCheckout FROM ParkingLots WHERE LotID = $lotID";
    $result = $dbconn->query($sql_select_cost_per_hour);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['CostPerHourLateCheckout'];
    } else {
        return 0; // Default to 0 if LotID is not found
    }
} */

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["recordID"])) {
    $recordID = $_POST["recordID"];
    // Step 1: Update Checkout Time
    $currentTimeUpdateResult = updateCheckoutTime($recordID, $dbconn);
	//$costTotal = getParkingRecordCost($recordID, $dbconn);
	//$lateCheckoutCostResult = calculateLateCheckoutCost($dbconn, $recordID);
	echo '<script>alert("Checkout successful!"); window.location.href="user_checkout.php";</script>';

    // Check if updateCheckoutTime was successful
    if ($currentTimeUpdateResult === TRUE) {
        // Step 2: Get Total Cost
        $costTotal = getParkingRecordCost($recordID, $dbconn);

        // Step 3: Calculate Late Checkout Cost
        $lateCheckoutCostResult = calculateLateCheckoutCost($dbconn, $recordID);

        // Check if calculateLateCheckoutCost was successful
        if (is_string($lateCheckoutCostResult)) {
            echo "Checkout successful. Checkout time: $currentTime. Total Cost is: $ $costTotal. Late Checkout Cost: $ $lateCheckoutCostResult";
        } else {
            // Echo raw error message for calculateLateCheckoutCost
            echo "Error calculating Late Checkout Cost: " . $lateCheckoutCostResult;
        }
    } else {
        // Echo raw error message for updateCheckoutTime
        echo "Error updating Checkout Time: " . $currentTimeUpdateResult;
    }
}




?>
