<?php
// Include essential files early
include('security.php');
check_login();

include('db.php');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Check for POST request and file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['excelFile']) && $_FILES['excelFile']['error'] == 0 && isset($_POST['year']) && isset($_POST['month'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $uploadFile = $uploadDir . basename($_FILES['excelFile']['name']);

        // Validate file type (only allow .xlsx, .xls)
        $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        if ($fileType != "xlsx" && $fileType != "xls") {
            echo "<div class='alert alert-danger' role='alert'>Invalid file format! Only .xlsx and .xls files are allowed.</div>";
        } else {
            if (move_uploaded_file($_FILES['excelFile']['tmp_name'], $uploadFile)) {
                // Load the spreadsheet
                $spreadsheet = IOFactory::load($uploadFile);
                if (!$spreadsheet) {
                    die("Failed to load the spreadsheet.");
                }

                // Get the active sheet
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray();

                $importSuccess = true;
                $year = $conn->real_escape_string($_POST['year']);
                $month = $conn->real_escape_string($_POST['month']);

                // Iterate over the data and insert into the database
                foreach ($data as $index => $row) {
                    if ($index === 0) {
                        continue;  // Skip header row
                    }

                    // Adjust column indices as needed
                    $index = $conn->real_escape_string($row[0]);
                    $personID = $conn->real_escape_string($row[1]);
                    $name = $conn->real_escape_string($row[2]);
                    $department = $conn->real_escape_string($row[3]);
                    $position = $conn->real_escape_string($row[4]);
                    $gender = $conn->real_escape_string($row[5]);
                    $date = $conn->real_escape_string($row[6]);
                    $week = $conn->real_escape_string($row[7]);
                    $timetable = $conn->real_escape_string($row[8]);
                    $checkin = $conn->real_escape_string($row[9]);
                    $checkout = $conn->real_escape_string($row[10]);
                    $work = $conn->real_escape_string($row[11]);
                    $ot = $conn->real_escape_string($row[12]);
                    $attended = $conn->real_escape_string($row[13]);
                    $late = $conn->real_escape_string($row[14]);
                    $early = $conn->real_escape_string($row[15]);
                    $absent = $conn->real_escape_string($row[16]);
                    $leave = $conn->real_escape_string($row[17]);
                    $status = $conn->real_escape_string($row[18]);
                    $records = $conn->real_escape_string($row[19]);

                    // Insert into the database, including year and month
                    $sql = "INSERT INTO ueshrattendancedaily (`Index`, PersonID, Name, Department, Position, Gender, Date, Week, Timetable, CheckIn, CheckOut, Work, OT, Attended, Late, Early, Absent, `Leave`, `Status`, Records, Year, Month)
                            VALUES ('$index', '$personID', '$name', '$department', '$position', '$gender', '$date', '$week', '$timetable', '$checkin', '$checkout', '$work', '$ot', '$attended', '$late', '$early', '$absent', '$leave', '$status', '$records', '$year', '$month')";

                    if ($conn->query($sql) === FALSE) {
                        $importSuccess = false;
                        echo "<div class='alert alert-danger' role='alert'>Error importing row: " . $conn->error . "</div>";
                        break;
                    }
                }

                // Insert file information into `uploaded_files` table
                if ($importSuccess) {
                    $filename = $conn->real_escape_string(basename($_FILES['excelFile']['name']));
                    $sqlFile = "INSERT INTO uploaded_files (filename, year, month) VALUES ('$filename', '$year', '$month')";
                    if ($conn->query($sqlFile) === FALSE) {
                        echo "<div class='alert alert-danger' role='alert'>Error recording file: " . $conn->error . "</div>";
                    } else {
                        // Redirect to view_uploads.php after successful upload, including year and month
                        header("Location: view_uploads.php?year=$year&month=$month");
                        exit(); // Make sure to exit after header to stop script
                    }
                } else {
                    echo "<div class='alert alert-danger' role='alert'>Data import failed!</div>";
                }
            } else {
                echo "<div class='alert alert-danger' role='alert'>Possible file upload attack!</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger' role='alert'>No file uploaded or upload error. Error Code: " . $_FILES['excelFile']['error'] . "</div>";
    }
}
?>

<?php
// Move HTML rendering after all PHP logic
include('includes/header.php');
include('includes/navbar.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Attendance Report</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 600px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center mb-4">Upload Attendance Report</h1>
        <form action="up_index.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="year">Year</label>
                <select name="year" id="year" class="form-control">
                    <?php
                    $currentYear = date("Y");
                    for ($year = $currentYear; $year >= 2000; $year--) {
                        echo "<option value=\"$year\">$year</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="month">Month</label>
                <select name="month" id="month" class="form-control">
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
                    foreach ($months as $monthNumber => $month) {
                        echo "<option value=\"$monthNumber\">$month</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="excelFile">Excel File</label>
                <input type="file" name="excelFile" id="excelFile" class="form-control-file" accept=".xlsx, .xls">
            </div>
            <!-- Display buttons in a single line -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Upload</button>
                <a href="view_uploads.php" class="btn btn-secondary">View Uploaded Files</a>
            </div>
        </form>
    </div>
    
    <!-- jQuery, Popper.js, Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Include footer -->
    <?php
    include('includes/scripts.php');
    ?>
</body>

</html>