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

// Fetch department data
$sql = "SELECT * FROM Department";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Departments</title>
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
            margin-bottom: 10px;
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
        <h1 class="mb-4 text-center">View Departments</h1>

        <div class="action-buttons text-right">
            <a href="add_department.php" class="btn btn-primary">Add New Department</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Telephone</th>
                    <th>Fax</th>
                    <th>Company</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['Id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Address']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Telephone']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Fax']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Company']) . "</td>";
                        echo "<td>";
                        echo "<a href='edit_department.php?id=" . htmlspecialchars($row['Id']) . "' class='btn edit-btn'>Edit</a>";
                        echo "<a href='delete_department.php?id=" . htmlspecialchars($row['Id']) . "' class='btn delete-btn' onclick=\"return confirm('Are you sure you want to delete this department?');\">Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>No departments found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>