<?php
session_start();
error_reporting(0);
include('db.php');

$msg = '';
$error = '';

if (isset($_POST['add_leave'])) {
    $employee_id = $_POST['employee_id'];
    $leave_type_id = $_POST['leave_type_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];
    $status = 'Pending';

    // Check if the end date is after the start date
    if ($start_date > $end_date) {
        $error = "End date should be after start date.";
    } else {
        $sql = "INSERT INTO employee_leaves (employee_id, leave_type_id, start_date, end_date, reason, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $query = $conn->prepare($sql);
        $query->bind_param('iissss', $employee_id, $leave_type_id, $start_date, $end_date, $reason, $status);
        $query->execute();

        if ($query->affected_rows > 0) {
            $msg = "Leave added successfully";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee Leave</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="container mt-4">
        <h2>Add Employee Leave</h2>

        <?php if ($error) { ?>
            <div class="alert alert-danger">
                <strong>Error</strong>: <?php echo htmlentities($error); ?>
            </div>
        <?php } elseif ($msg) { ?>
            <div class="alert alert-success">
                <strong>Success</strong>: <?php echo htmlentities($msg); ?>
            </div>
        <?php } ?>

        <form method="post">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="employee_id">Employee</label>
                    <select id="employee_id" name="employee_id" class="form-control" required>
                        <option value="">Select Employee...</option>
                        <?php
                        $sql = "SELECT id, NameWithInitials AS name FROM tblemployees";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($employee = $result->fetch_assoc()) {
                                echo '<option value="' . $employee['id'] . '">' . $employee['name'] . '</option>';
                            }
                        } else {
                            echo '<option value="">No employees found</option>';
                        }
                        ?>
                    </select>

                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="leave_type_id">Leave Type</label>
                    <select id="leave_type_id" name="leave_type_id" class="form-control" required>
                        <option value="">Select Leave Type...</option>
                        <?php
                        $sql = "SELECT id, LeaveType FROM tblleavetype";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($type = $result->fetch_assoc()) {
                                echo '<option value="' . $type['id'] . '">'
                                    . $type['LeaveType'] . '</option>';
                            }
                        } else {
                            echo '<option value="">No leave types found</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" required>
            </div>

            <div class="form-group">
                <label for="reason">Reason</label>
                <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
            </div>

            <button type="submit" name="add_leave" class="btn btn-primary">Submit Leave</button>
        </form>
    </div>

    <!-- Include Bootstrap JS, jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
// Close the database connection
$conn->close();
include('includes/scripts.php');
include('includes/footer.php');
?>