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
    $sql = "UPDATE Department 
            SET Name='$name', Address='$address', Email='$email', Telephone='$telephone', Fax='$fax', Company='$company' 
            WHERE Id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Department updated successfully!'); window.location.href='view_department.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}

// Retrieve the department ID from the query string
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ueshrdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve department details
$sql = "SELECT * FROM Department WHERE Id='$id'";
$result = $conn->query($sql);
$department = $result->fetch_assoc();

if (!$department) {
    die("Department not found");
}

// Retrieve company names for the dropdown
$companies = $conn->query("SELECT Id, Name FROM Company");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .form-container h1 {
            margin-top: 0;
        }
        .form-row {
            margin-bottom: 15px;
        }
        .form-row label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .form-row input, .form-row select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-row button {
            padding: 10px 20px;
            background-color: #007BFF;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-row button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Edit Department</h1>
        <form action="edit_department.php" method="post" id="edit-department-form">
            <div class="form-row">
                <label for="id">ID:</label>
                <input type="text" id="id" name="id" value="<?php echo htmlspecialchars($department['Id']); ?>" required readonly>
            </div>
            <div class="form-row">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($department['Name']); ?>" required>
            </div>
            <div class="form-row">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($department['Address']); ?>" required>
            </div>
            <div class="form-row">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($department['Email']); ?>" required>
            </div>
            <div class="form-row">
                <label for="telephone">Telephone:</label>
                <input type="text" id="telephone" name="telephone" value="<?php echo htmlspecialchars($department['Telephone']); ?>" required>
            </div>
            <div class="form-row">
                <label for="fax">Fax:</label>
                <input type="text" id="fax" name="fax" value="<?php echo htmlspecialchars($department['Fax']); ?>">
            </div>
            <div class="form-row">
                <label for="company">Company:</label>
                <select id="company" name="company" required>
                    <option value="">Select Company</option>
                    <?php while ($row = $companies->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['Id']); ?>" <?php if ($department['Company'] == $row['Id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($row['Name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-row">
                <button type="submit">Update Department</button>
            </div>
        </form>
    </div>
</body>
</html>
