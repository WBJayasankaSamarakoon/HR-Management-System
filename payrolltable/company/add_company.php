<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ueshrdb";

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve form data
    $name = $conn->real_escape_string($_POST['name']);
    $address = $conn->real_escape_string($_POST['address']);
    $email = $conn->real_escape_string($_POST['email']);
    $telephone = $conn->real_escape_string($_POST['telephone']);
    $fax = $conn->real_escape_string($_POST['fax']);

    // Check if email already exists
    $emailCheck = $conn->query("SELECT * FROM Company WHERE Email = '$email'");
    if ($emailCheck->num_rows > 0) {
        echo "<script>alert('Error: The email address already exists.'); window.location.href='add_company.php';</script>";
    } else {
        // Retrieve the next company ID
        $result = $conn->query("SELECT MAX(Id) AS maxId FROM Company");
        if (!$result) {
            die("Query failed: " . $conn->error);
        }
        $row = $result->fetch_assoc();
        $nextId = ($row['maxId'] !== NULL) ? $row['maxId'] + 1 : 1; // Handle empty table case

        // Insert data into the database
        $sql = "INSERT INTO Company (Id, Name, Address, Email, Telephone, Fax) VALUES ('$nextId', '$name', '$address', '$email', '$telephone', '$fax')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Company added successfully!'); window.location.href='view_company.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Company</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.form-container {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 400px;
    max-width: 90%;
    box-sizing: border-box; /* Ensure padding is included in width */
}

.form-container h2 {
    margin-top: 0;
    font-size: 24px;
    color: #333;
}

.form-row {
    margin-bottom: 15px;
    box-sizing: border-box; /* Ensure padding is included in width */
}

.form-row label {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
    color: #555;
}

.form-row input[type="text"], .form-row input[type="email"] {
    width: calc(100% - 22px); /* Adjust width to include padding and borders */
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box; /* Ensure padding and border are included in width */
}

.form-row input[type="text"]:focus, .form-row input[type="email"]:focus {
    border-color: #007bff;
    outline: none;
}

.form-row button {
    padding: 10px 15px;
    background-color: #007bff;
    border: none;
    color: #fff;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
    transition: background-color 0.3s ease;
    box-sizing: border-box; /* Ensure padding is included in width */
}

.form-row button:hover {
    background-color: #0056b3;
}

    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New Company</h2>
        <form action="add_company.php" method="post">
            <div class="form-row">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-row">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-row">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-row">
                <label for="telephone">Telephone:</label>
                <input type="text" id="telephone" name="telephone" required>
            </div>
            <div class="form-row">
                <label for="fax">Fax:</label>
                <input type="text" id="fax" name="fax">
            </div>
            <div class="form-row">
                <button type="submit">Add Company</button>
            </div>
        </form>
    </div>
</body>
</html>

