<?php
include('security.php');
check_login();

session_start();
include('db.php');
$msg = '';
$error = '';

// Handle deletion of a position
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM position WHERE Id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Position deleted successfully.";
        } else {
            $error = "Error deleting position. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Display success or error messages
if (isset($_SESSION['success'])) {
    $msg = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['status'])) {
    $error = $_SESSION['status'];
    unset($_SESSION['status']);
}

// Fetch all positions from the database
$sql = "SELECT * FROM position";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Positions</title>
    
    <!-- Bootstrap CSS -->
    <link href="src/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="src/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>


    <div class="container-fluid mt-4">
        <h2 class="mb-4">Manage Positions</h2>

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

        <!-- Add Position Button -->
        <div class="mb-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPositionModal">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-briefcase"></i> Position Information
            </div>
            <div class="card-body">
                <table id="positionsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                    <td><?php echo htmlentities($row['Id']); ?></td>
                                    <td><?php echo htmlentities($row['Name']); ?></td>
                                    <td>
                                        <button type='button' class='btn btn-warning edit-btn'
                                            data-id='<?php echo htmlentities($row['Id']); ?>'
                                            data-name='<?php echo htmlentities($row['Name']); ?>' data-toggle='modal'
                                            data-target='#editPositionModal'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="view_position.php?del=<?php echo htmlentities($row['Id']); ?>"
                                            class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this position?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="3" class="text-center">No positions found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Position Modal -->
    <div class="modal fade" id="addPositionModal" tabindex="-1" role="dialog" aria-labelledby="addPositionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPositionModalLabel">Add Position</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="add_position.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="positionName">Name</label>
                            <input type="text" name="name" class="form-control" id="positionName" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="add_position" class="btn btn-primary">Add Position</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Position Modal -->
    <div class="modal fade" id="editPositionModal" tabindex="-1" role="dialog" aria-labelledby="editPositionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPositionModalLabel">Edit Position</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="edit_position.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="position_id" id="positionId">
                        <div class="form-group">
                            <label for="editPositionName">Name</label>
                            <input type="text" name="name" class="form-control" id="editPositionName" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="update_position" class="btn btn-primary">Save Changes</button>
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
        $(document).ready(function () {
            $('#positionsTable').DataTable();
        });

        // Pass data to Edit Position Modal
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                document.getElementById('positionId').value = id;
                document.getElementById('editPositionName').value = name;
            });
        });
    </script>
</body>

<?php
include('includes/scripts.php');
include('includes/footer.php');
?>