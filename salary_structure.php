<?php
include('security.php');
check_login();

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db.php');
include('includes/header.php');
include('includes/navbar.php');

$msg = '';
$error = '';

// Handle deletion of a salary component
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM salarystructure WHERE ID = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Salary component deleted successfully";
        } else {
            $error = "Something went wrong. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle adding a new salary component
if (isset($_POST['add_component'])) {
    $name = $_POST['Name'];
    $value = $_POST['Value'];

    // Insert new component into SalaryStructure table
    $sql = "INSERT INTO salarystructure (Name, Value) VALUES (?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $name, $value);
        if (mysqli_stmt_execute($stmt)) {
            // Successfully added, now update the payroll table
            $alter_sql = "ALTER TABLE payroll ADD COLUMN " . mysqli_real_escape_string($conn, strtolower($name)) . " DECIMAL(10,2)";
            if (mysqli_query($conn, $alter_sql)) {
                $msg = "Salary component added and payroll table updated successfully.";
            } else {
                $error = "Component added but failed to update payroll table: " . mysqli_error($conn);
            }
        } else {
            $error = "Something went wrong. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle editing an existing salary component
if (isset($_POST['componentid'])) {
    $id = $_POST['componentid'];
    $name = $_POST['Name'];
    $value = $_POST['Value'];

    $sql = "UPDATE salarystructure SET Name = ?, Value = ? WHERE ID = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssi", $name, $value, $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Salary component updated successfully";
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
    <title>Admin | Manage Salary Structure</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">
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
    <div class="container-fluid mt-4">
        <h2 class="mb-4">Manage Salary Structure</h2>

        <?php
        if ($error) {
            echo '<div class="errorWrap"><strong>ERROR</strong>: ' . htmlentities($error) . '</div>';
        }
        if ($msg) {
            echo '<div class="succWrap"><strong>SUCCESS</strong>: ' . htmlentities($msg) . '</div>';
        }
        ?>

        <div class="mb-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addComponentModal">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-dollar-sign"></i> Salary Information
            </div>
            <div class="card-body">
                <table id="componentsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Value</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM salarystructure";
                        if ($result = mysqli_query($conn, $sql)) {
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo htmlentities($row['ID']); ?></td>
                                        <td><?php echo htmlentities($row['Name']); ?></td>
                                        <td><?php echo htmlentities($row['Value']); ?></td>
                                        <td>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-warning" title="Edit"
                                                data-toggle="modal" data-target="#editComponentModal"
                                                onclick="editComponent(<?php echo $row['ID']; ?>, '<?php echo $row['Name']; ?>', '<?php echo $row['Value']; ?>');">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="salary_structure.php?del=<?php echo htmlentities($row['ID']); ?>"
                                                class="btn btn-sm btn-danger" title="Delete"
                                                onclick="return confirm('Do you want to delete this component?');">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                mysqli_free_result($result);
                            } else {
                                echo "<tr><td colspan='4'>No records found.</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Error fetching records: " . mysqli_error($conn) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Salary Component Modal -->
    <div class="modal fade" id="addComponentModal" tabindex="-1" role="dialog" aria-labelledby="addComponentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addComponentModalLabel">Add Salary Component</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addComponentForm" method="POST">
                        <div class="form-group">
                            <label for="Name">Name</label>
                            <input type="text" class="form-control" id="Name" name="Name" required>
                        </div>
                        <div class="form-group">
                            <label for="Value">Value</label>
                            <input type="text" class="form-control" id="Value" name="Value" required>
                        </div>
                        <button type="submit" name="add_component" class="btn btn-primary">Add Component</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Salary Component Modal -->
    <div class="modal fade" id="editComponentModal" tabindex="-1" role="dialog"
        aria-labelledby="editComponentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editComponentModalLabel">Edit Salary Component</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editComponentForm" method="POST">
                        <input type="hidden" id="componentid" name="componentid">
                        <div class="form-group">
                            <label for="editName">Name</label>
                            <input type="text" class="form-control" id="editName" name="Name" required>
                        </div>
                        <div class="form-group">
                            <label for="editValue">Value</label>
                            <input type="text" class="form-control" id="editValue" name="Value" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Component</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editComponent(id, name, value) {
            document.getElementById('componentid').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editValue').value = value;
        }

        $(document).ready(function () {
            $('#componentsTable').DataTable();
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>