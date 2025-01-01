<?php
session_start();
include('db.php');


if (isset($_POST['add'])) {
    $leavetype = $_POST['leavetype'];
    $description = $_POST['description'];

    // Use prepared statements for security
    $stmt = $conn->prepare("INSERT INTO tblleavetype (LeaveType, Description) VALUES (?, ?)");
    $stmt->bind_param("ss", $leavetype, $description);

    if ($stmt->execute()) {
        $msg = "Leave type added successfully";
    } else {
        $error = "Something went wrong. Please try again";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin | Add Leave Type</title>
    <!-- Bootstrap CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="container-fluid">
        <h3 class="mt-4">Add Leave Type</h3>

        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo htmlentities($error); ?></div>
        <?php } elseif (isset($msg)) { ?>
            <div class="alert alert-success"><?php echo htmlentities($msg); ?></div>
        <?php } ?>

        <form method="POST" name="addleavetype" class="mt-3">
            <div class="form-group">
                <label for="leavetype">Leave Type</label>
                <input type="text" class="form-control" id="leavetype" name="leavetype" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" name="add" class="btn btn-primary">Add</button>
        </form>
    </div>
    
     <!-- Bootstrap JS and dependencies -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
       

    <!-- Include Bootstrap JS -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>