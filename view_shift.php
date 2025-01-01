<?php
include('security.php');
check_login();

session_start();
include('db.php'); 

$msg = '';
$error = '';

// Handle deletion of a shift
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM shift WHERE Id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Shift deleted successfully.";
        } else {
            $error = "Error deleting shift. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Fetch shift data
$sql = "SELECT * FROM shift";
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Shifts</title>
    
    <!-- Bootstrap CSS -->
    <link href="src/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="src/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="container-fluid mt-4">
        <h2 class="mb-4">Manage Shifts</h2>

        <!-- Display error or success messages -->
        <?php if ($error) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>ERROR:</strong> <?php echo htmlentities($error); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <?php if ($msg) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>SUCCESS:</strong> <?php echo htmlentities($msg); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <!-- Add Shift Button -->
        <div class="mb-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addShiftModal">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-clock"></i> Shift Information
            </div>
            <div class="card-body">
                <table id="shiftTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Day</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                    <td><?php echo htmlentities($row['Id']); ?></td>
                                    <td><?php echo htmlentities($row['StartTime']); ?></td>
                                    <td><?php echo htmlentities($row['EndTime']); ?></td>
                                    <td><?php echo htmlentities($row['Week']); ?></td>
                                    <td>
                                        <button type='button' class='btn btn-warning edit-btn'
                                            data-id='<?php echo htmlentities($row['Id']); ?>'
                                            data-starttime='<?php echo htmlentities($row['StartTime']); ?>'
                                            data-endtime='<?php echo htmlentities($row['EndTime']); ?>'
                                            data-week='<?php echo htmlentities($row['Week']); ?>' data-toggle='modal'
                                            data-target='#editShiftModal'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="view_shift.php?del=<?php echo htmlentities($row['Id']); ?>"
                                            class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this shift?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="5" class="text-center">No shifts found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Shift Modal -->
    <div class="modal fade" id="addShiftModal" tabindex="-1" role="dialog" aria-labelledby="addShiftModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addShiftModalLabel">Add Shift</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="add_shift.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="shiftStartTime">Start Time</label>
                            <input type="time" name="start_time" class="form-control" id="shiftStartTime" required>
                        </div>
                        <div class="form-group">
                            <label for="shiftEndTime">End Time</label>
                            <input type="time" name="end_time" class="form-control" id="shiftEndTime" required>
                        </div>
                        <div class="form-group">
                            <label for="shiftWeek">Day</label>
                            <input type="text" name="week" class="form-control" id="shiftWeek" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="add_shift" class="btn btn-primary">Add Shift</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Shift Modal -->
    <div class="modal fade" id="editShiftModal" tabindex="-1" role="dialog" aria-labelledby="editShiftModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editShiftModalLabel">Edit Shift</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="edit_shift.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="shift_id" id="shiftId">
                        <div class="form-group">
                            <label for="editShiftStartTime">Start Time</label>
                            <input type="time" name="start_time" class="form-control" id="editShiftStartTime" required>
                        </div>
                        <div class="form-group">
                            <label for="editShiftEndTime">End Time</label>
                            <input type="time" name="end_time" class="form-control" id="editShiftEndTime" required>
                        </div>
                        <div class="form-group">
                            <label for="editShiftWeek">Day</label>
                            <input type="text" name="week" class="form-control" id="editShiftWeek" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="update_shift" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- Bootstrap JS and dependencies -->
        <script src="src/jquery-3.5.1.slim.min.js"></script>
        <script src="src/popper.min.js"></script>
        <script src="src/bootstrap.min.js"></script>

        <!-- DataTables JS -->
        <script src="src/jquery.dataTables.min.js"></script>
        <script src="src/dataTables.bootstrap4.min.js"></script>
   
    <script>
        // Pass data to Edit Shift Modal
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const startTime = this.getAttribute('data-starttime');
                const endTime = this.getAttribute('data-endtime');
                const week = this.getAttribute('data-week');

                document.getElementById('shiftId').value = id;
                document.getElementById('editShiftStartTime').value = startTime;
                document.getElementById('editShiftEndTime').value = endTime;
                document.getElementById('editShiftWeek').value = week;
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#shiftTable').DataTable();
        });
    </script>
</body>

</html>