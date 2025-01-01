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

    // Update data in the database
    $sql = "UPDATE Company SET Name='$name', Address='$address', Email='$email', Telephone='$telephone', Fax='$fax' WHERE Id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Company updated successfully!'); window.location.href='view_company.php';</script>";
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
    <title>Update Company</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .popup {
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            visibility: hidden;
        }
        .popup.active {
            visibility: visible;
        }
        .popup-content {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            width: 500px;
            max-width: 90%;
        }
        .popup-content h2 {
            margin-top: 0;
        }
        .popup-content .form-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .popup-content label {
            display: block;
            font-size: 12px;
        }
        .popup-content input[type="text"], 
        .popup-content input[type="email"], 
        .popup-content input[type="tel"] {
            width: 100%;
            padding: 5px;
            margin-top: 3px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 12px;
        }
        .popup-content button {
            padding: 10px 20px;
            background-color: #007BFF;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
            font-size: 14px;
        }
        .popup-content button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <button onclick="showPopup()">Update Company</button>

    <div class="popup" id="popup">
        <div class="popup-content">
            <h2>Update Company</h2>
            <?php
            if (isset($_GET['id'])) {
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

                // Retrieve the company details
                $id = $conn->real_escape_string($_GET['id']);
                $sql = "SELECT * FROM Company WHERE Id='$id'";
                $result = $conn->query($sql);
                $company = $result->fetch_assoc();

                $conn->close();
            }
            ?>

            <form action="update_company.php" method="post">
                <input type="hidden" id="id" name="id" value="<?php echo isset($company['Id']) ? $company['Id'] : ''; ?>">
                <div class="form-row">
                    <div>
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo isset($company['Name']) ? $company['Name'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" value="<?php echo isset($company['Address']) ? $company['Address'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo isset($company['Email']) ? $company['Email'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="telephone">Telephone:</label>
                        <input type="tel" id="telephone" name="telephone" value="<?php echo isset($company['Telephone']) ? $company['Telephone'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label for="fax">Fax:</label>
                        <input type="tel" id="fax" name="fax" value="<?php echo isset($company['Fax']) ? $company['Fax'] : ''; ?>">
                    </div>
                </div>
                <button type="submit">Update Company</button>
            </form>
        </div>
    </div>

    <script>
        function showPopup() {
            document.getElementById('popup').classList.add('active');
        }
    </script>
</body>
</html>
