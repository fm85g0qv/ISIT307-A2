<?php 
class User {
    private $id;
    private $name;
    private $surname;
    private $phone;
    private $email;
    private $type;

    // Constructor to initialize user properties
    public function __construct($name, $surname, $phone, $email, $type) {
        $this->name = $name;
        $this->surname = $surname;
        $this->phone = $phone;
        $this->email = $email;
        $this->type = $type;
    }

    // Getter for user ID
    public function getId() {
        return $this->id;
    }

    // Function to add a user to the database
    public function addUserToDatabase($dbconn) {
        $insertQuery = "INSERT INTO Users (Name, Surname, Phone, Email, UserType) VALUES (?, ?, ?, ?, ?)";
        $stmt = $dbconn->prepare($insertQuery);

        // Bind parameters
        $stmt->bind_param("sssss", $this->name, $this->surname, $this->phone, $this->email, $this->type);

        // Execute the statement
        $stmt->execute();

        // Get the inserted ID
        $this->id = $stmt->insert_id;

        // Close the statement
        $stmt->close();

        return $this->id;
    }
	
	public function getUserType($email, $dbconn) {
		$query = "SELECT UserType FROM Users WHERE Email = ?";
		
		$stmt = $dbconn->prepare($query);
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->bind_result($userType);

		if ($stmt->fetch()) {
			$stmt->close();
			return $userType;
		} else {
			$stmt->close();
			return null; // User not found with the given ID
		}
	}
}
?>