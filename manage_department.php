<?php
include('security.php');
check_login();

session_start();
error_reporting(0);
include('db.php');

$msg = '';
$error = '';

// Handle deletion of a department
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM tbldepartments WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Department record deleted successfully";
        } else {
            $error = "Something went wrong. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle add department form submission
if (isset($_POST['add_department'])) {
    $name = $_POST['DepartmentName'];
    $shortName = $_POST['DepartmentShortName'];
    $code = $_POST['DepartmentCode'];

    $sql = "INSERT INTO tbldepartments (DepartmentName, DepartmentShortName, DepartmentCode) VALUES (?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sss", $name, $shortName, $code);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Department added successfully";
        } else {
            $error = "Something went wrong. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle edit department form submission
if (isset($_POST['deptid'])) {
    $id = $_POST['deptid'];
    $name = $_POST['DepartmentName'];
    $shortName = $_POST['DepartmentShortName'];
    $code = $_POST['DepartmentCode'];

    $sql = "UPDATE tbldepartments SET DepartmentName = ?, DepartmentShortName = ?, DepartmentCode = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssi", $name, $shortName, $code, $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Department updated successfully";
        } else {
            $error = "Something went wrong. Please try again.";
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
    <title>Admin | Manage Departments</title>
    <!-- Bootstrap CSS -->
    
    <!-- Bootstrap CSS -->
    <link href="src/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="src/dataTables.bootstrap4.min.css" rel="stylesheet">


    <!-- Custom Styles -->
    <style>
        .errorWrap {
            padding: 10px;
            margin-bottom: 20px;
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
            border-radius: 5px;
        }

        .succWrap {
            padding: 10px;
            margin-bottom: 20px;
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="container-fluid mt-4">
        <h2 class="mb-4">Manage Departments</h2>

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

        <!-- Add Department Button with Icon -->
        <div class="mb-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addDepartmentModal">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <!-- Add Department Modal -->
        <div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog"
            aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addDepartmentModalLabel">Add Department</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addDepartmentForm" method="POST">
                            <div class="form-group">
                                <label for="DepartmentName">Department Name</label>
                                <input type="text" class="form-control" id="DepartmentName" name="DepartmentName"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="DepartmentShortName">Department Short Name</label>
                                <input type="text" class="form-control" id="DepartmentShortName"
                                    name="DepartmentShortName" required>
                            </div>
                            <div class="form-group">
                                <label for="DepartmentCode">Department Code</label>
                                <input type="text" class="form-control" id="DepartmentCode" name="DepartmentCode"
                                    required>
                            </div>
                            <button type="submit" name="add_department" class="btn btn-primary">Add Department</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-building"></i> Departments Information
            </div>
            <div class="card-body">
                <table id="departmentsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Dept Name</th>
                            <th>Dept Short Name</th>
                            <th>Dept Code</th>
                            <th>Creation Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM tbldepartments";
                        if ($result = mysqli_query($conn, $sql)) {
                            if (mysqli_num_rows($result) > 0) {
                                $cnt = 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo htmlentities($cnt); ?></td>
                                        <td><?php echo htmlentities($row['DepartmentName']); ?></td>
                                        <td><?php echo htmlentities($row['DepartmentShortName']); ?></td>
                                        <td><?php echo htmlentities($row['DepartmentCode']); ?></td>
                                        <td><?php echo htmlentities($row['CreationDate']); ?></td>
                                        <td>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-warning" title="Edit"
                                                data-toggle="modal" data-target="#editDepartmentModal"
                                                onclick="editDepartment(<?php echo $row['id']; ?>, '<?php echo $row['DepartmentName']; ?>', '<?php echo $row['DepartmentShortName']; ?>', '<?php echo $row['DepartmentCode']; ?>');">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="manage_department.php?del=<?php echo htmlentities($row['id']); ?>"
                                                class="btn btn-sm btn-danger" title="Delete"
                                                onclick="return confirm('Do you want to delete this department?');">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                    $cnt++;
                                }
                                mysqli_free_result($result);
                            } else {
                                echo "<tr><td colspan='6'>No records found.</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Error fetching records: " . mysqli_error($conn) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Department Modal -->
    <div class="modal fade" id="editDepartmentModal" tabindex="-1" role="dialog"
        aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editDepartmentForm" method="POST">
                        <input type="hidden" id="deptid" name="deptid">
                        <div class="form-group">
                            <label for="editDepartmentName">Department Name</label>
                            <input type="text" class="form-control" id="editDepartmentName" name="DepartmentName"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="editDepartmentShortName">Department Short Name</label>
                            <input type="text" class="form-control" id="editDepartmentShortName"
                                name="DepartmentShortName" required>
                        </div>
                        <div class="form-group">
                            <label for="editDepartmentCode">Department Code</label>
                            <input type="text" class="form-control" id="editDepartmentCode" name="DepartmentCode"
                                required>
                        </div>
                        <button type="submit" class="btn btn-warning">Update Department</button>
                    </form>
                </div>
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
            $('#departmentsTable').DataTable();
        });

        function editDepartment(id, name, shortName, code) {
            $('#deptid').val(id);
            $('#editDepartmentName').val(name);
            $('#editDepartmentShortName').val(shortName);
            $('#editDepartmentCode').val(code);
        }
    </script>
</body>

</html>