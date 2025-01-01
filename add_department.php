<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('db.php');

$msg = '';
$error = '';

// Check if the form has been submitted
if (isset($_POST['add'])) {

    $deptname = htmlspecialchars(trim($_POST['departmentname']));
    $deptshortname = htmlspecialchars(trim($_POST['departmentshortname']));
    $deptcode = htmlspecialchars(trim($_POST['deptcode']));

    // Ensure none of the fields are empty
    if (!empty($deptname) && !empty($deptshortname) && !empty($deptcode)) {

        if ($conn instanceof mysqli) {
            $sql = "INSERT INTO tbldepartments (DepartmentName, DepartmentShortName, DepartmentCode) VALUES (?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {

                mysqli_stmt_bind_param($stmt, "sss", $deptname, $deptshortname, $deptcode);
                mysqli_stmt_execute($stmt);


                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $msg = "Department Created Successfully";
                } else {
                    $error = "Something went wrong. Please try again.";
                }

                mysqli_stmt_close($stmt);
            } else {
                $error = 'Prepare failed: ' . htmlspecialchars(mysqli_error($conn));
            }
        } else {
            $error = 'Database connection not established.';
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Add Department</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
        }

        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h2 class="page-title">Add Department</h2>
                <!-- Display error or success messages -->
                <?php if ($error) { ?>
                    <div class="errorWrap"><strong>ERROR</strong>: <?php echo htmlentities($error); ?> </div>
                <?php } else if ($msg) { ?>
                        <div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?> </div>
                <?php } ?>

                <!-- Form to add a new department -->
                <form method="post">
                    <div class="form-group">
                        <label for="departmentname">Department Name</label>
                        <input type="text" class="form-control" id="departmentname" name="departmentname" required>
                    </div>
                    <div class="form-group">
                        <label for="departmentshortname">Department Short Name</label>
                        <input type="text" class="form-control" id="departmentshortname" name="departmentshortname"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="deptcode">Department Code</label>
                        <input type="text" class="form-control" id="deptcode" name="deptcode" required>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary">Add Department</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>