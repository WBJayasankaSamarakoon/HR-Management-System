<?php
include('security.php');
check_login();

session_start();
include('db.php'); 
$msg = '';
$error = '';

// Handle deletion of a gender
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM gender WHERE Id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Gender deleted successfully.";
        } else {
            $error = "Error deleting gender. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Fetch all genders from the database
$sql = "SELECT * FROM gender";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Genders</title>
    
    <!-- Bootstrap CSS -->
    <link href="src/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="src/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="container-fluid mt-4">
        <h2 class="mb-4">Manage Genders</h2>

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

        <!-- Add Gender Button -->
        <div class="mb-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addGenderModal">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-venus-mars"></i> Gender Information
            </div>
            <div class="card-body">
                <table id="gendersTable" class="table table-bordered table-striped">
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
                                            data-target='#editGenderModal'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="view_gender.php?del=<?php echo htmlentities($row['Id']); ?>"
                                            class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this gender?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="3" class="text-center">No genders found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Gender Modal -->
    <div class="modal fade" id="addGenderModal" tabindex="-1" role="dialog" aria-labelledby="addGenderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGenderModalLabel">Add Gender</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="add_gender.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="genderName">Name</label>
                            <input type="text" name="name" class="form-control" id="genderName" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="add_gender" class="btn btn-primary">Add Gender</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Gender Modal -->
    <div class="modal fade" id="editGenderModal" tabindex="-1" role="dialog" aria-labelledby="editGenderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGenderModalLabel">Edit Gender</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="edit_gender.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="gender_id" id="genderId">
                        <div class="form-group">
                            <label for="editGenderName">Name</label>
                            <input type="text" name="name" class="form-control" id="editGenderName" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="update_gender" class="btn btn-primary">Save Changes</button>
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
            $('#gendersTable').DataTable(); // Initialize DataTable
        });

        // Pass data to Edit Gender Modal
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                document.getElementById('genderId').value = id;
                document.getElementById('editGenderName').value = name;
            });
        });
    </script>
</body>
<?php
include('includes/scripts.php');
include('includes/footer.php');
?>