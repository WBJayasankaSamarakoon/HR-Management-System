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

    // Retrieve and escape form data
    $id = $conn->real_escape_string($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $address = $conn->real_escape_string($_POST['address']);
    $email = $conn->real_escape_string($_POST['email']);
    $telephone = $conn->real_escape_string($_POST['telephone']);
    $fax = $conn->real_escape_string($_POST['fax']);
    $company = $conn->real_escape_string($_POST['company']);

    // Update data in the database
    $sql = "UPDATE Department SET Name='$name', Address='$address', Email='$email', Telephone='$telephone', Fax='$fax', Company='$company' WHERE Id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Department updated successfully!'); window.location.href='view_department.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Department</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .form-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h1 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
        }
        .form-row {
            margin-bottom: 15px;
        }
        .form-row label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            color: #555;
        }
        .form-row input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-row input:focus {
            border-color: #007bff;
            outline: none;
        }
        .form-row button {
            padding: 10px 20px;
            background-color: #007bff;
            border: none;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        .form-row button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Update Department</h1>
        <form id="update-department-form" action="update_department.php" method="post">
            <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
            <div class="form-row">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo isset($department['Name']) ? htmlspecialchars($department['Name']) : ''; ?>" required>
            </div>
            <div class="form-row">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo isset($department['Address']) ? htmlspecialchars($department['Address']) : ''; ?>" required>
            </div>
            <div class="form-row">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($department['Email']) ? htmlspecialchars($department['Email']) : ''; ?>" required>
            </div>
            <div class="form-row">
                <label for="telephone">Telephone:</label>
                <input type="text" id="telephone" name="telephone" value="<?php echo isset($department['Telephone']) ? htmlspecialchars($department['Telephone']) : ''; ?>" required>
            </div>
            <div class="form-row">
                <label for="fax">Fax:</label>
                <input type="text" id="fax" name="fax" value="<?php echo isset($department['Fax']) ? htmlspecialchars($department['Fax']) : ''; ?>" required>
            </div>
            <div class="form-row">
                <label for="company">Company:</label>
                <input type="text" id="company" name="company" value="<?php echo isset($department['Company']) ? htmlspecialchars($department['Company']) : ''; ?>" required>
            </div>
            <button type="submit">Update Department</button>
        </form>
    </div>
</body>
</html>
