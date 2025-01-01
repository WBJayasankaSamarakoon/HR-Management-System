<?php
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

// Fetch employee data
$sql = "SELECT * FROM Employee";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employees</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .action-buttons {
            margin-bottom: 20px;
        }

        .action-buttons .btn {
            margin-right: 10px;
        }

        .edit-btn {
            background-color: #28a745;
            border: none;
            color: white;
            margin: 5px;
        }

        .delete-btn {
            background-color: #dc3545;
            border: none;
            color: white;

        }

        .edit-btn:hover {
            background-color: #218838;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">View Employees</h1>

        <div class="action-buttons text-right">
            <a href="add_employee.php" class="btn btn-primary">Add New Employee</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['Id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['FirstName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['MiddleName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['LastName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['DOB']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Gender']) . "</td>";
                        echo "<td>";
                        echo "<a href='edit_employee.php?id=" . htmlspecialchars($row['Id']) . "' class='btn edit-btn'>Edit</a>";
                        echo "<a href='delete_employee.php?id=" . htmlspecialchars($row['Id']) . "' class='btn delete-btn' onclick=\"return confirm('Are you sure you want to delete this employee?');\">Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No employees found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>


</body>

</html>