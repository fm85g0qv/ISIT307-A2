<?php
include 'database_credentials.php';

$dbconn = new mysqli(
    $databaseConfig['servername'],
    $databaseConfig['username'],
    $databaseConfig['password'],
    $databaseConfig['database']
);

//give an error if connection failed
if ($dbconn->connect_error) {
    die("Connection failed: " . $dbconn->connect_error);
}
?>
