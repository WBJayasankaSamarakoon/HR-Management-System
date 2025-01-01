<?php
include('security.php');
check_login();

session_start();
include('db.php'); 

// Check if the connection is successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$msg = '';
$error = '';

// Handle deletion of a company
if (isset($_GET['del'])) {
    $id = intval($_GET['del']); // Cast to integer for safety
    $sql = "DELETE FROM company WHERE Id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Company deleted successfully.";
        } else {
            $error = "Error deleting company. Please try again.";
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
    <title>Manage Companies</title>
    
    <!-- Bootstrap CSS -->
    <link href="src/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="src/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>


    <div class="container-fluid mt-4">
        <h2 class="mb-4">Manage Companies</h2>

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

        <!-- Add Company Button -->
        <div class="mb-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCompanyModal">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-building"></i> Company Information
            </div>
            <div class="card-body">
                <table id="companiesTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Fax</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch companies data
                        $sql = "SELECT * FROM company";
                        if ($result = mysqli_query($conn, $sql)) {
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo htmlentities($row['Id']); ?></td>
                                        <td><?php echo htmlentities($row['Name']); ?></td>
                                        <td><?php echo htmlentities($row['Address']); ?></td>
                                        <td><?php echo htmlentities($row['Email']); ?></td>
                                        <td><?php echo htmlentities($row['Telephone']); ?></td>
                                        <td><?php echo htmlentities($row['Fax']); ?></td>
                                        <td>
                                            <button type='button' class='btn-sm btn-warning edit-btn'
                                                data-id='<?php echo htmlentities($row['Id']); ?>'
                                                data-name='<?php echo htmlentities($row['Name']); ?>'
                                                data-address='<?php echo htmlentities($row['Address']); ?>'
                                                data-email='<?php echo htmlentities($row['Email']); ?>'
                                                data-telephone='<?php echo htmlentities($row['Telephone']); ?>'
                                                data-fax='<?php echo htmlentities($row['Fax']); ?>' data-toggle='modal'
                                                data-target='#editCompanyModal'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="view_company.php?del=<?php echo htmlentities($row['Id']); ?>"
                                                class="btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this company?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="7" class="text-center">No companies found</td></tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center">Error fetching data</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Company Modal -->
    <div class="modal fade" id="addCompanyModal" tabindex="-1" role="dialog" aria-labelledby="addCompanyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCompanyModalLabel">Add Company</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="add_company.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="companyName">Name</label>
                            <input type="text" name="name" class="form-control" id="companyName" required>
                        </div>
                        <div class="form-group">
                            <label for="companyAddress">Address</label>
                            <input type="text" name="address" class="form-control" id="companyAddress" required>
                        </div>
                        <div class="form-group">
                            <label for="companyEmail">Email</label>
                            <input type="email" name="email" class="form-control" id="companyEmail" required>
                        </div>
                        <div class="form-group">
                            <label for="companyTelephone">Telephone</label>
                            <input type="text" name="telephone" class="form-control" id="companyTelephone" required>
                        </div>
                        <div class="form-group">
                            <label for="companyFax">Fax</label>
                            <input type="text" name="fax" class="form-control" id="companyFax" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="add_company" class="btn btn-primary">Add Company</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Company Modal -->
    <div class="modal fade" id="editCompanyModal" tabindex="-1" role="dialog" aria-labelledby="editCompanyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCompanyModalLabel">Edit Company</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="edit_company.php" method="POST">
                    <input type="hidden" name="company_id" id="companyId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editCompanyName">Name</label>
                            <input type="text" name="name" class="form-control" id="editCompanyName" required>
                        </div>
                        <div class="form-group">
                            <label for="editCompanyAddress">Address</label>
                            <input type="text" name="address" class="form-control" id="editCompanyAddress" required>
                        </div>
                        <div class="form-group">
                            <label for="editCompanyEmail">Email</label>
                            <input type="email" name="email" class="form-control" id="editCompanyEmail" required>
                        </div>
                        <div class="form-group">
                            <label for="editCompanyTelephone">Telephone</label>
                            <input type="text" name="telephone" class="form-control" id="editCompanyTelephone" required>
                        </div>
                        <div class="form-group">
                            <label for="editCompanyFax">Fax</label>
                            <input type="text" name="fax" class="form-control" id="editCompanyFax" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="edit_company" class="btn btn-primary">Save Changes</button>
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
            $('#companiesTable').DataTable();

            // Populate edit modal with company data
            $('.edit-btn').on('click', function () {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var address = $(this).data('address');
                var email = $(this).data('email');
                var telephone = $(this).data('telephone');
                var fax = $(this).data('fax');

                $('#companyId').val(id);
                $('#editCompanyName').val(name);
                $('#editCompanyAddress').val(address);
                $('#editCompanyEmail').val(email);
                $('#editCompanyTelephone').val(telephone);
                $('#editCompanyFax').val(fax);
            });
        });
    </script>
</body>

</html>