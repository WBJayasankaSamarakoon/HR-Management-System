<?php
session_start();
error_reporting(0);
include('db.php');

$msg = '';

// Handle delete action
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM tblleavetype WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $msg = "Leave type record deleted";
        mysqli_stmt_close($stmt);
    } else {
        $msg = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle edit form submission
if (isset($_POST['edit_leave_type'])) {
    $id = $_POST['leave_id'];
    $name = $_POST['leave_type'];
    $description = $_POST['description'];

    $sql = "UPDATE tblleavetype SET LeaveType = ?, Description = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssi", $name, $description, $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Leave type updated successfully";
        } else {
            $msg = "Error updating record: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Manage Leave Type</title>
    
    <!-- Bootstrap CSS -->
    <link href="src/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="src/dataTables.bootstrap4.min.css" rel="stylesheet">

    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="container-fluid">
        <h3 class="mt-4">Manage Leave Type</h3>

        <!-- Success or Error Message -->
        <?php if ($msg) { ?>
            <div class="alert alert-success succWrap">
                <strong>SUCCESS</strong> : <?php echo htmlentities($msg); ?>
            </div>
        <?php } ?>

        <div class="card">
            <div class="card-header">
                <h5>Leave Type Info</h5>
            </div>
            <div class="card-body">
                <table id="example" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Sr no</th>
                            <th>Leave Type</th>
                            <th>Description</th>
                            <th>Creation Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM tblleavetype";
                        if ($result = mysqli_query($conn, $sql)) {
                            $cnt = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                    <td><?php echo htmlentities($cnt); ?></td>
                                    <td><?php echo htmlentities($row['LeaveType']); ?></td>
                                    <td><?php echo htmlentities($row['Description']); ?></td>
                                    <td><?php echo htmlentities($row['CreationDate']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary"
                                            onclick="editLeaveType(<?php echo $row['id']; ?>, '<?php echo $row['LeaveType']; ?>', '<?php echo $row['Description']; ?>')">
                                            Edit
                                        </button>
                                        <a href="manage_leave.php?del=<?php echo htmlentities($row['id']); ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Do you want to delete');">Delete</a>
                                    </td>
                                </tr>
                                <?php $cnt++;
                            }
                            mysqli_free_result($result);
                        } else {
                            echo "<tr><td colspan='5'>Error fetching records: " . mysqli_error($conn) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Editing Leave Type -->
    <div class="modal fade" id="editLeaveTypeModal" tabindex="-1" role="dialog" aria-labelledby="editLeaveTypeLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLeaveTypeLabel">Edit Leave Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="leave_id" id="editLeaveId">
                        <div class="form-group">
                            <label for="leave_type">Leave Type</label>
                            <input type="text" class="form-control" id="editLeaveType" name="leave_type" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3"
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="edit_leave_type" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
     <!-- Bootstrap JS and dependencies -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
       

    <!-- Bootstrap and other JS -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        function editLeaveType(id, leaveType, description) {
            $('#editLeaveId').val(id);
            $('#editLeaveType').val(leaveType);
            $('#editDescription').val(description);
            $('#editLeaveTypeModal').modal('show');
        }
    </script>
</body>

</html>

<?php
include('includes/scripts.php');
include('includes/footer.php');
?>