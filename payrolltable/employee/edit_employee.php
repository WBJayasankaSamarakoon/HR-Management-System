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
    $firstName = $conn->real_escape_string($_POST['first_name']);
    $middleName = $conn->real_escape_string($_POST['middle_name']);
    $lastName = $conn->real_escape_string($_POST['last_name']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $gender = $conn->real_escape_string($_POST['gender']);

    // Update data in the database
    $sql = "UPDATE Employee 
            SET FirstName='$firstName', MiddleName='$middleName', LastName='$lastName', DOB='$dob', Gender='$gender' 
            WHERE Id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Employee updated successfully!'); window.location.href='view_employee.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}

// Retrieve the employee ID from the query string
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

// Retrieve employee details
$sql = "SELECT * FROM Employee WHERE Id='$id'";
$result = $conn->query($sql);
$employee = $result->fetch_assoc();

if (!$employee) {
    die("Employee not found");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
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
        <h1>Edit Employee</h1>
        <form action="edit_employee.php" method="post" id="edit-employee-form">
            <div class="form-row">
                <label for="id">ID:</label>
                <input type="text" id="id" name="id" value="<?php echo htmlspecialchars($employee['Id']); ?>" required readonly>
            </div>
            <div class="form-row">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($employee['FirstName']); ?>" required>
            </div>
            <div class="form-row">
                <label for="middle_name">Middle Name:</label>
                <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($employee['MiddleName']); ?>">
            </div>
            <div class="form-row">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($employee['LastName']); ?>" required>
            </div>
            <div class="form-row">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($employee['DOB']); ?>" required>
            </div>
            <div class="form-row">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?php if ($employee['Gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($employee['Gender'] == 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($employee['Gender'] == 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
            <div class="form-row">
                <button type="submit">Update Employee</button>
            </div>
        </form>
    </div>
</body>
</html>
