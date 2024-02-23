<?php
include 'db_conn.php';

// Function to sanitize user input
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get search values from the form
    $name = sanitize_input($_POST["name"]);
    $surname = sanitize_input($_POST["surname"]);
    $email = sanitize_input($_POST["email"]);
    $userType = sanitize_input($_POST["userType"]);

    // Build the query for searching users
    $sql_search_users = "SELECT * FROM Users WHERE 1";

    if (!empty($name)) {
        $sql_search_users .= " AND Name LIKE '%$name%'";
    }

    if (!empty($surname)) {
        $sql_search_users .= " AND Surname LIKE '%$surname%'";
    }

    if (!empty($email)) {
        $sql_search_users .= " AND Email LIKE '%$email%'";
    }

    if (!empty($userType)) {
        $sql_search_users .= " AND UserType = '$userType'";
    }

    // Execute the query
    $result_users = $dbconn->query($sql_search_users);

    if ($result_users) {
        // Display the search form
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Search Users (Admin)</title>
			<link rel="stylesheet" href="style.css">
        </head>
        <body>
            <h1>Search Users (Admin)</h1>
            <form method="POST" action="">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name">

                <label for="surname">Surname:</label>
                <input type="text" id="surname" name="surname">

                <label for="email">Email:</label>
                <input type="text" id="email" name="email">

                <label for="userType">User Type:</label>
                <select id="userType" name="userType">
                    <option value="">All</option>
                    <option value="Administrator">Administrator</option>
                    <option value="User">User</option>
                </select>

                <input type="submit" value="Search">
            </form>

            <!-- Display search results -->
            <h2>Search Results</h2>
            <table border='1'>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Email</th>
                    <th>User Type</th>
                </tr>
                <?php
                while ($row = $result_users->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['UserID']}</td>
                            <td>{$row['Name']}</td>
                            <td>{$row['Surname']}</td>
                            <td>{$row['Email']}</td>
                            <td>{$row['UserType']}</td>
                        </tr>";
                }
                ?>
            </table>
        </body>
        </html>
        <?php
    } else {
        echo "Error: " . $dbconn->error;
    }
} else {
    // Display the search form if the form is not submitted
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Search Users (Admin)</title>
		<link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Search Users (Admin)</h1>
		<h3><a href="dashboard.php">Back to Dashboard</a></h3>
        <form method="POST" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name">

            <label for="surname">Surname:</label>
            <input type="text" id="surname" name="surname">

            <label for="email">Email:</label>
            <input type="text" id="email" name="email">

            <label for="userType">User Type:</label>
            <select id="userType" name="userType">
                <option value="">All</option>
                <option value="Administrator">Administrator</option>
                <option value="User">User</option>
            </select>

            <input type="submit" value="Search">
        </form>
    </body>
    </html>
    <?php
}
?>
