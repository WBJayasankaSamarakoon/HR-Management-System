<?php
// Start output buffering
ob_start();
include('security.php');
check_login();
include('db.php');
include('includes/header.php');
include('includes/navbar.php');

// Check if year and month are selected and query the files
$selectedYear = isset($_POST['year']) ? $conn->real_escape_string($_POST['year']) : null;
$selectedMonth = isset($_POST['month']) ? $conn->real_escape_string($_POST['month']) : null;

// Fetch uploaded files based on selected year and month
$files = [];
$filesQuery = "SELECT filename, year, month FROM uploaded_files";

if ($selectedYear && $selectedMonth) {
    $filesQuery .= " WHERE year = ? AND month = ?";
}

// Prepare the statement
$stmt = $conn->prepare($filesQuery);

// Bind parameters if necessary
if ($selectedYear && $selectedMonth) {
    $stmt->bind_param("is", $selectedYear, $selectedMonth); // Use 'is' for int and string
}

// Execute the query
$stmt->execute();
$filesResult = $stmt->get_result();

// Check for errors in the files query
if (!$filesResult) {
    die("Error fetching files: " . $conn->error);
}

while ($row = $filesResult->fetch_assoc()) {
    $files[] = $row;
}

// Handle file deletion
if (isset($_POST['delete_file'])) {
    $fileToDelete = $conn->real_escape_string($_POST['filename']);
    $yearToDelete = $conn->real_escape_string($_POST['year']);
    $monthToDelete = $conn->real_escape_string($_POST['month']);

    // Begin transaction
    $conn->begin_transaction();
    try {
        // Delete file record from database
        $deleteFilesQuery = "DELETE FROM uploaded_files WHERE filename = '$fileToDelete' AND year = '$yearToDelete' AND month = '$monthToDelete'";
        if ($conn->query($deleteFilesQuery) !== TRUE) {
            throw new Exception("Error deleting file record: " . $conn->error);
        }

        // Delete related records from ueshrattendancedaily table
        $deleteAttendanceQuery = "DELETE FROM ueshrattendancedaily WHERE Year = '$yearToDelete' AND Month = '$monthToDelete'";
        if ($conn->query($deleteAttendanceQuery) !== TRUE) {
            throw new Exception("Error deleting attendance records: " . $conn->error);
        }

        // Delete file from filesystem
        $filePath = 'uploads/' . $fileToDelete;
        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                throw new Exception("Error deleting file from filesystem");
            }
        }

        // Commit transaction
        $conn->commit();

        // Set success message in session and redirect
        $_SESSION['status'] = 'File and records deleted successfully!';
        $_SESSION['status_type'] = 'success';
        header('Location: view_uploads.php');
        exit();
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();

        // Set error message in session and redirect
        $_SESSION['status'] = $e->getMessage();
        $_SESSION['status_type'] = 'danger';
        header('Location: view_uploads.php');
        exit();
    }
}

// Include header and navbar

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Uploaded Files</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 1200px;
        }

        .button-group {
            display: inline-flex;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Display Success/Error Message -->
        <?php if (isset($_SESSION['status'])): ?>
            <div class="alert alert-<?php echo $_SESSION['status_type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['status']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php
            // Clear the status after displaying
            unset($_SESSION['status']);
            unset($_SESSION['status_type']);
            ?>
        <?php endif; ?>

        <!-- Filter Form -->
        <h1 class="text-center mb-4">View Uploaded Files</h1>
        <form action="view_uploads.php" method="post" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="year">Select Year</label>
                    <select name="year" id="year" class="form-control">
                        <option value="">Choose Year</option>
                        <?php
                        $yearsResult = $conn->query("SELECT DISTINCT year FROM uploaded_files ORDER BY year DESC");
                        while ($row = $yearsResult->fetch_assoc()) {
                            $selected = ($row['year'] == $selectedYear) ? 'selected' : '';
                            echo "<option value='" . $row['year'] . "' $selected>" . $row['year'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="month">Select Month</label>
                    <select name="month" id="month" class="form-control">
                        <option value="">Choose Month</option>
                        <?php
                        $months = [
                            '01' => 'January',
                            '02' => 'February',
                            '03' => 'March',
                            '04' => 'April',
                            '05' => 'May',
                            '06' => 'June',
                            '07' => 'July',
                            '08' => 'August',
                            '09' => 'September',
                            '10' => 'October',
                            '11' => 'November',
                            '12' => 'December'
                        ];

                        foreach ($months as $num => $name) {
                            $selected = ($num == $selectedMonth) ? 'selected' : '';
                            echo "<option value='$num' $selected>$name</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Filter Files</button>
        </form>

        <h2 class="mb-4 d-flex justify-content-between align-items-center">
            Uploaded Files
            <!-- New Back Button -->
            <a href="up_index.php" class="btn btn-secondary">Upload New File</a>
        </h2>

        <?php if (count($files) > 0): ?>
            <?php foreach ($files as $file): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <?php echo htmlspecialchars($file['filename']); ?>
                        <span class="badge badge-primary badge-pill">
                            <?php echo htmlspecialchars($file['year']); ?>-<?php echo htmlspecialchars($file['month']); ?>
                        </span>
                    </div>
                    <div class="button-group">
                        <!-- New 'View' button linking to up_viewbtn.php -->
                        <a href="up_viewbtn.php?filename=<?php echo urlencode($file['filename']); ?>&year=<?php echo urlencode($file['year']); ?>&month=<?php echo urlencode($file['month']); ?>"
                            class="btn btn-info btn-sm ml-2" target="_blank">View</a>

                        <!-- Existing delete form -->
                        <form action="view_uploads.php" method="post" class="ml-2"
                            onsubmit="return confirm('Are you sure you want to delete this file?');">
                            <input type="hidden" name="filename" value="<?php echo htmlspecialchars($file['filename']); ?>">
                            <input type="hidden" name="year" value="<?php echo htmlspecialchars($file['year']); ?>">
                            <input type="hidden" name="month" value="<?php echo htmlspecialchars($file['month']); ?>">
                            <button type="submit" name="delete_file" class="btn btn-danger btn-sm">Delete</button>
                        </form>

                        <!-- New process form -->
                        <form action="process_file.php" method="post" class="ml-2">
                            <input type="hidden" name="filename" value="<?php echo htmlspecialchars($file['filename']); ?>">
                            <input type="hidden" name="year" value="<?php echo htmlspecialchars($file['year']); ?>">
                            <input type="hidden" name="month" value="<?php echo htmlspecialchars($file['month']); ?>">
                            <button type="submit" name="process_file" class="btn btn-warning btn-sm">Process</button>
                        </form>

                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning">No files found for the selected year and month.</div>
        <?php endif; ?>
        </ul>

    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>