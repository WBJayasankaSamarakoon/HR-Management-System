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

// Fetch all companies from the database
$sql = "SELECT * FROM Company";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Companies</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .action-buttons {
            margin-bottom: 20px;
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
        <h1 class="mb-4 text-center">View Companies</h1>

        <div class="action-buttons text-right">
            <a href="add_company.php" class="btn btn-primary">Add New Company</a>
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['Id']); ?></td>
                            <td><?php echo htmlspecialchars($row['Name']); ?></td>
                            <td><?php echo htmlspecialchars($row['Address']); ?></td>
                            <td><?php echo htmlspecialchars($row['Email']); ?></td>
                            <td><?php echo htmlspecialchars($row['Telephone']); ?></td>
                            <td><?php echo htmlspecialchars($row['Fax']); ?></td>
                            <td>
                                <a href="edit_company.php?id=<?php echo htmlspecialchars($row['Id']); ?>"
                                    class="btn edit-btn">Edit</a>
                                <a href="delete_company.php?id=<?php echo htmlspecialchars($row['Id']); ?>"
                                    class="btn delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this company?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No companies found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


</body>

</html>

<?php
$conn->close();
?>