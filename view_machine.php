<?php
include('security.php');
check_login();

session_start();
include('db.php');

$msg = '';
$error = '';

// Handle deletion of a machine
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM machine WHERE Id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Machine deleted successfully.";
        } else {
            $error = "Error deleting machine. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle add machine form submission
if (isset($_POST['add_machine'])) {
    $name = $_POST['name'];
    $model = $_POST['model'];
    $brand = $_POST['brand'];

    $sql = "INSERT INTO machine (Name, Model, Brand) VALUES (?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $name, $model, $brand);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Machine added successfully.";
        } else {
            $error = "Error adding machine. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle edit machine form submission
if (isset($_POST['machine_id'])) {
    $id = $_POST['machine_id'];
    $name = $_POST['name'];
    $model = $_POST['model'];
    $brand = $_POST['brand'];

    $sql = "UPDATE machine SET Name = ?, Model = ?, Brand = ? WHERE Id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssi", $name, $model, $brand, $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Machine updated successfully.";
        } else {
            $error = "Error updating machine. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Machines</title>
    
    <!-- Bootstrap CSS -->
    <link href="src/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="src/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="container-fluid mt-4">
        <h2 class="mb-4">Manage Machines</h2>

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

        <!-- Add Machine Button -->
        <div class="mb-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMachineModal">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-tools"></i> Machine Information
            </div>
            <div class="card-body">
                <table id="machinesTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Model</th>
                            <th>Brand</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch machine data
                        $sql = "SELECT * FROM machine";
                        if ($result = mysqli_query($conn, $sql)) {
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo htmlentities($row['Id']); ?></td>
                                        <td><?php echo htmlentities($row['Name']); ?></td>
                                        <td><?php echo htmlentities($row['Model']); ?></td>
                                        <td><?php echo htmlentities($row['Brand']); ?></td>
                                        <td>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-warning" title="Edit"
                                                data-toggle="modal" data-target="#editMachineModal"
                                                onclick="editMachine(<?php echo $row['Id']; ?>, '<?php echo $row['Name']; ?>', '<?php echo $row['Model']; ?>', '<?php echo $row['Brand']; ?>');">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="view_machine.php?del=<?php echo htmlentities($row['Id']); ?> "
                                                class="btn btn-sm btn-danger" title="Delete"
                                                onclick="return confirm('Do you want to delete this machine?');">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='5'>No machines found.</td></tr>";
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

    <!-- Add Machine Modal -->
    <div class="modal fade" id="addMachineModal" tabindex="-1" role="dialog" aria-labelledby="addMachineModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMachineModalLabel">Add Machine</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="view_machine.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="addMachineName">Name</label>
                            <input type="text" name="name" class="form-control" id="addMachineName" required>
                        </div>
                        <div class="form-group">
                            <label for="addMachineModel">Model</label>
                            <input type="text" name="model" class="form-control" id="addMachineModel" required>
                        </div>
                        <div class="form-group">
                            <label for="addMachineBrand">Brand</label>
                            <input type="text" name="brand" class="form-control" id="addMachineBrand" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="add_machine" class="btn btn-primary">Add Machine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Machine Modal -->
    <div class="modal fade" id="editMachineModal" tabindex="-1" role="dialog" aria-labelledby="editMachineModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMachineModalLabel">Edit Machine</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="view_machine.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="machine_id" id="editMachineId">
                        <div class="form-group">
                            <label for="editMachineName">Name</label>
                            <input type="text" name="name" class="form-control" id="editMachineName" required>
                        </div>
                        <div class="form-group">
                            <label for="editMachineModel">Model</label>
                            <input type="text" name="model" class="form-control" id="editMachineModel" required>
                        </div>
                        <div class="form-group">
                            <label for="editMachineBrand">Brand</label>
                            <input type="text" name="brand" class="form-control" id="editMachineBrand" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
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
            $('#machinesTable').DataTable();
        });

        function editMachine(id, name, model, brand) {
            $('#editMachineId').val(id);
            $('#editMachineName').val(name);
            $('#editMachineModel').val(model);
            $('#editMachineBrand').val(brand);
        }
    </script>
</body>

</html>