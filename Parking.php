<?php

include 'db_conn.php';

class Parking
{
    private $dbconn;

    public function __construct($dbconn)
    {
        $this->dbconn = $dbconn;
    }

    public function addParkingLot($location, $description, $costPerHour, $costPerHourLateCheckout, $capacity)
    {
        $stmt = $this->dbconn->prepare("INSERT INTO ParkingLots (Location, Description, CostPerHour, CostPerHourLateCheckout, Capacity) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddi", $location, $description, $costPerHour, $costPerHourLateCheckout, $capacity);

        if ($stmt->execute()) {
            return true; // Insert successful
        } else {
            return false; // Insert failed
        }
    }
}

?>
