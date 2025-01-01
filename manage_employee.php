<?php
include('security.php');
check_login();

session_start();
error_reporting(E_ALL);
include('db.php');
$msg = '';
$error = '';

// Handle deletion of an employee
if (isset($_GET['delid'])) {
    $id = $_GET['delid'];
    $sql = "DELETE FROM tblemployees WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Employee record deleted successfully";
        } else {
            $error = "Error executing query: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle form submission for adding a new employee
if (isset($_POST['add_employee'])) {
    $empId = $_POST['EmpId'];
    $nameWithInitials = $_POST['NameWithInitials'];
    $epfNumber = $_POST['EPFNumber'];
    $phone = $_POST['Phone'];
    $currentAddress = $_POST['CurrentAddress'];
    $permanentAddress = $_POST['PermanentAddress'];
    $personalEmail = $_POST['PersonalEmail'];
    $companyEmail = $_POST['CompanyEmail'];
    $dateOfJoining = $_POST['DateOfJoining'];
    $status = $_POST['Status'];
    $salutation = $_POST['Salutation'];
    $designation = $_POST['Designation'];
    $branch = $_POST['Branch'];
    $company = $_POST['Company'];
    $reportsTo = $_POST['ReportsTo'];
    $employmentType = $_POST['EmploymentType'];
    $emergencyContactName = $_POST['EmergencyContactName'];
    $emergencyPhone = $_POST['EmergencyPhone'];
    $relation = $_POST['Relation'];
    $defaultShift = $_POST['DefaultShift'];

    // Insert query to add an employee
    $sql = "INSERT INTO tblemployees (EmpId, NameWithInitials, EPFNumber, Phone, CurrentAddress, PermanentAddress, 
            PersonalEmail, CompanyEmail, DateOfJoining, Status, Salutation, Designation, Branch, Company, ReportsTo, 
            EmploymentType, EmergencyContactName, EmergencyPhone, Relation, DefaultShift) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssssssssssssss",
            $empId,
            $nameWithInitials,
            $epfNumber,
            $phone,
            $currentAddress,
            $permanentAddress,
            $personalEmail,
            $companyEmail,
            $dateOfJoining,
            $status,
            $salutation,
            $designation,
            $branch,
            $company,
            $reportsTo,
            $employmentType,
            $emergencyContactName,
            $emergencyPhone,
            $relation,
            $defaultShift
        );
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Employee added successfully";
        } else {
            $error = "Error executing query: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle edit employee form submission
if (isset($_POST['empid'])) {
    $id = $_POST['empid'];
    $empId = $_POST['EmpId'];
    $nameWithInitials = $_POST['NameWithInitials'];
    $epfNumber = $_POST['EPFNumber'];
    $phone = $_POST['Phone'];
    $currentAddress = $_POST['CurrentAddress'];
    $permanentAddress = $_POST['PermanentAddress'];
    $personalEmail = $_POST['PersonalEmail'];
    $companyEmail = $_POST['CompanyEmail'];
    $dateOfJoining = $_POST['DateOfJoining'];
    $status = $_POST['Status'];
    $salutation = $_POST['Salutation'];
    $designation = $_POST['Designation'];
    $branch = $_POST['Branch'];
    $company = $_POST['Company'];
    $reportsTo = $_POST['ReportsTo'];
    $employmentType = $_POST['EmploymentType'];
    $emergencyContactName = $_POST['EmergencyContactName'];
    $emergencyPhone = $_POST['EmergencyPhone'];
    $relation = $_POST['Relation'];
    $defaultShift = $_POST['DefaultShift'];

    $sql = "UPDATE tblemployees SET EmpId = ?, NameWithInitials = ?, EPFNumber = ?, Phone = ?, CurrentAddress = ?, PermanentAddress = ?, 
    PersonalEmail = ?, CompanyEmail = ?, DateOfJoining = ?, Status = ?, Salutation = ?, Designation = ?, Branch = ?, Company = ?, 
    ReportsTo = ?, EmploymentType = ?, EmergencyContactName = ?, EmergencyPhone = ?, Relation = ?, DefaultShift = ? WHERE id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssssssssssssssi",
            $empId,
            $nameWithInitials,
            $epfNumber,
            $phone,
            $currentAddress,
            $permanentAddress,
            $personalEmail,
            $companyEmail,
            $dateOfJoining,
            $status,
            $salutation,
            $designation,
            $branch,
            $company,
            $reportsTo,
            $employmentType,
            $emergencyContactName,
            $emergencyPhone,
            $relation,
            $defaultShift,
            $id
        );
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Employee updated successfully";
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
    <title>Manage Employees</title>
    
    <!-- Bootstrap CSS -->
    <link href="src/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="src/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>
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

    .employee-icon {
        font-size: 1.5rem;
        margin-right: 10px;
    }

    .btn-custom-margin {
        margin-bottom: 5px;
    }


    .btn-icons {
        font-size: 1.2rem;
    }

    .table th,
    .table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="container-fluid mt-4">
        <h3 class="mb-4">Manage Employees</h3>

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

        <!-- Add Employee Button with Icon -->
        <div class="add-employee-btn mb-3">
            <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addEmployeeModal">
                <i class="fas fa-user-plus"></i></button>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-users"></i> Employees Information
            </div>
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table id="employeesTable" class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Emp Id</th>
                                <th>Name</th>
                                <th>EPF</th>
                                <th>Phone</th>
                                <th>Current Address</th>
                                <th>Permanent Address</th>
                                <th>Personal Email</th>
                                <th>Company Email</th>
                                <th>Date of Joining</th>
                                <th>Status</th>
                                <th>Salutation</th>
                                <th>Designation</th>
                                <th>Branch</th>
                                <th>Company</th>
                                <th>Reports To</th>
                                <th>Employment Type</th>
                                <th>Emergency Con. Name</th>
                                <th>Emergency Phone</th>
                                <th>Relation</th>
                                <th>Shift</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM tblemployees";
                            if ($result = mysqli_query($conn, $sql)) {
                                if (mysqli_num_rows($result) > 0) {
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                        <tr>
                                            <td><?php echo htmlentities($cnt); ?></td>
                                            <td><?php echo htmlentities($row['EmpId']); ?></td>
                                            <td><?php echo htmlentities($row['NameWithInitials']); ?></td>
                                            <td><?php echo htmlentities($row['EPFNumber']); ?></td>
                                            <td><?php echo htmlentities($row['Phone']); ?></td>
                                            <td><?php echo htmlentities($row['CurrentAddress']); ?></td>
                                            <td><?php echo htmlentities($row['PermanentAddress']); ?></td>
                                            <td><?php echo htmlentities($row['PersonalEmail']); ?></td>
                                            <td><?php echo htmlentities($row['CompanyEmail']); ?></td>
                                            <td><?php echo isset($row['DateOfJoining']) ? htmlentities($row['DateOfJoining']) : 'N/A'; ?>
                                            </td>
                                            <td><?php echo isset($row['Status']) ? htmlentities($row['Status']) : 'N/A'; ?></td>
                                            <td><?php echo isset($row['Salutation']) ? htmlentities($row['Salutation']) : 'N/A'; ?>
                                            </td>
                                            <td><?php echo isset($row['Designation']) ? htmlentities($row['Designation']) : 'N/A'; ?>
                                            </td>
                                            <td><?php echo isset($row['Branch']) ? htmlentities($row['Branch']) : 'N/A'; ?></td>
                                            <td><?php echo isset($row['Company']) ? htmlentities($row['Company']) : 'N/A'; ?></td>
                                            <td><?php echo isset($row['ReportsTo']) ? htmlentities($row['ReportsTo']) : 'N/A'; ?>
                                            </td>
                                            <td><?php echo isset($row['EmploymentType']) ? htmlentities($row['EmploymentType']) : 'N/A'; ?>
                                            </td>
                                            <td><?php echo isset($row['EmergencyContactName']) ? htmlentities($row['EmergencyContactName']) : 'N/A'; ?>
                                            </td>
                                            <td><?php echo isset($row['EmergencyPhone']) ? htmlentities($row['EmergencyPhone']) : 'N/A'; ?>
                                            </td>
                                            <td><?php echo isset($row['Relation']) ? htmlentities($row['Relation']) : 'N/A'; ?></td>
                                            <td><?php echo isset($row['DefaultShift']) ? htmlentities($row['DefaultShift']) : 'N/A'; ?>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-warning btn-icons" title="Edit"
                                                    data-toggle="modal" data-target="#editEmployeeModal" onclick='editEmployee(
                                                            <?php echo json_encode($row['id']); ?>, 
                                                            <?php echo json_encode($row['EmpId']); ?>, 
                                                            <?php echo json_encode($row['NameWithInitials']); ?>, 
                                                            <?php echo json_encode($row['EPFNumber']); ?>, 
                                                            <?php echo json_encode($row['Phone']); ?>, 
                                                            <?php echo json_encode($row['CurrentAddress']); ?>, 
                                                            <?php echo json_encode($row['PersonalEmail']); ?>, 
                                                            <?php echo json_encode($row['CompanyEmail']); ?>, 
                                                            <?php echo json_encode($row['DateOfJoining']); ?>, 
                                                            <?php echo json_encode($row['Status']); ?>, 
                                                            <?php echo json_encode($row['Salutation']); ?>, 
                                                            <?php echo json_encode($row['Designation']); ?>, 
                                                            <?php echo json_encode($row['Branch']); ?>, 
                                                            <?php echo json_encode($row['Company']); ?>, 
                                                            <?php echo json_encode($row['ReportsTo']); ?>, 
                                                            <?php echo json_encode($row['EmploymentType']); ?>, 
                                                            <?php echo json_encode($row['EmergencyContactName']); ?>, 
                                                            <?php echo json_encode($row['EmergencyPhone']); ?>, 
                                                            <?php echo json_encode($row['Relation']); ?>, 
                                                            <?php echo json_encode($row['DefaultShift']); ?>);'>
                                                    <i class="fas fa-edit"></i>
                                                </a>


                                                <a href="manage_employee.php?delid=<?php echo $row['id']; ?>"
                                                    onclick="return confirm('Are you sure you want to delete this employee?');"
                                                    class="btn btn-danger btn-icons">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                        $cnt++;
                                    }
                                    mysqli_free_result($result);
                                } else {
                                    echo "<tr><td colspan='22'>No records found.</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='22'>Error fetching records: " . mysqli_error($conn) . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Employee Modal -->
        <div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog"
            aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEmployeeModalLabel">Add New Employee</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="manage_employee.php">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Column 1 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="EmpId">Employee ID</label>
                                        <input type="text" class="form-control" id="EmpId" name="EmpId" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="NameWithInitials">Name with Initials</label>
                                        <input type="text" class="form-control" id="NameWithInitials"
                                            name="NameWithInitials" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="EPFNumber">EPF Number</label>
                                        <input type="text" class="form-control" id="EPFNumber" name="EPFNumber">
                                    </div>
                                    <div class="form-group">
                                        <label for="Phone">Phone Number</label>
                                        <input type="text" class="form-control" id="Phone" name="Phone">
                                    </div>
                                </div>
                                <!-- Column 2 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="CurrentAddress">Current Address</label>
                                        <textarea class="form-control" id="CurrentAddress"
                                            name="CurrentAddress"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="PersonalEmail">Personal Email</label>
                                        <input type="email" class="form-control" id="PersonalEmail"
                                            name="PersonalEmail">
                                    </div>
                                    <div class="form-group">
                                        <label for="DateOfJoining">Date of Joining</label>
                                        <input type="date" class="form-control" id="DateOfJoining" name="DateOfJoining">
                                    </div>
                                    <div class="form-group">
                                        <label for="Salutation">Salutation</label>
                                        <select class="form-control" id="Salutation" name="Salutation">
                                            <option value="Mr">Mr</option>
                                            <option value="Ms">Ms</option>
                                            <option value="Mrs">Mrs</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Column 3 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="PermanentAddress">Permanent Address</label>
                                        <textarea class="form-control" id="PermanentAddress"
                                            name="PermanentAddress"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="CompanyEmail">Company Email</label>
                                        <input type="email" class="form-control" id="CompanyEmail" name="CompanyEmail">
                                    </div>
                                    <div class="form-group">
                                        <label for="Status">Status</label>
                                        <select class="form-control" id="Status" name="Status">
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                            <option value="Suspended">Suspended</option>
                                            <option value="Left">Left</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="Designation">Designation</label>
                                        <select class="form-control" id="Designation" name="Designation">
                                            <option value="Director">Director</option>
                                            <option value="Project Manager">Project Manager</option>
                                            <option value="General Manager">General Manager</option>
                                            <option value="HR Manager">HR Manager</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Column 4 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Branch">Branch</label>
                                        <select class="form-control" id="Branch" name="Branch">
                                            <option value="Branch 01">Branch 01</option>
                                            <option value="Branch 02">Branch 02</option>
                                            <option value="Branch 03">Branch 03</option>
                                            <option value="Branch 04">Branch 04</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="Company">Company</label>
                                        <select class="form-control" id="Company" name="Company">
                                            <option value="Company 01">Company 01</option>
                                            <option value="Company 02">Company 02</option>
                                            <option value="Company 03">Company 03</option>
                                            <option value="Company 04">Company 04</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="ReportsTo">Reports To</label>
                                        <input type="text" class="form-control" id="ReportsTo" name="ReportsTo">
                                    </div>
                                    <div class="form-group">
                                        <label for="EmploymentType">Employment Type</label>
                                        <select class="form-control" id="EmploymentType" name="EmploymentType">
                                            <option value="Intern">Intern</option>
                                            <option value="Full-time">Full-time</option>
                                            <option value="Part-time">Part-time</option>
                                            <option value="Contract">Contract</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Column 5 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="DefaultShift">Default Shift</label>
                                        <select class="form-control" id="DefaultShift" name="DefaultShift">
                                            <option value="WeekDay">WeekDay</option>
                                            <option value="WeekEnd">WeekEnd</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="EmergencyContactName">Emergency Contact Name</label>
                                        <input type="text" class="form-control" id="EmergencyContactName"
                                            name="EmergencyContactName">
                                    </div>
                                    <div class="form-group">
                                        <label for="EmergencyPhone">Emergency Phone</label>
                                        <input type="text" class="form-control" id="EmergencyPhone"
                                            name="EmergencyPhone">
                                    </div>
                                    <div class="form-group">
                                        <label for="Relation">Relation</label>
                                        <select class="form-control" id="Relation" name="Relation">
                                            <option value="Married">Married</option>
                                            <option value="Unmarried">Unmarried</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="add_employee" class="btn btn-primary">Add Employee</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Edit Employee Modal -->
        <div class="modal fade" id="editEmployeeModal" tabindex="-1" role="dialog"
            aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEmployeeModalLabel">Edit Employee</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="manage_employee.php">
                        <div class="modal-body">
                            <!-- Hidden field to store employee id -->
                            <input type="hidden" name="empid" id="editEmpId">

                            <div class="row">
                                <!-- Column 1 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="editEmpIdField">Employee ID</label>
                                        <input type="text" class="form-control" id="editEmpIdField" name="EmpId"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpNameField">Name with Initials</label>
                                        <input type="text" class="form-control" id="editEmpNameField"
                                            name="NameWithInitials" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpEPF">EPF Number</label>
                                        <input type="text" class="form-control" id="editEmpEPF" name="EPFNumber"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpPhone">Phone</label>
                                        <input type="text" class="form-control" id="editEmpPhone" name="Phone" required>
                                    </div>
                                </div>

                                <!-- Column 2 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="editEmpCurrentAddress">Current Address</label>
                                        <textarea class="form-control" id="editEmpCurrentAddress"
                                            name="CurrentAddress"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpPersonalEmail">Personal Email</label>
                                        <input type="email" class="form-control" id="editEmpPersonalEmail"
                                            name="PersonalEmail" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpDateOfJoining">Date of Joining</label>
                                        <input type="date" class="form-control" id="editEmpDateOfJoining"
                                            name="DateOfJoining" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpSalutation">Salutation</label>
                                        <select class="form-control" id="editEmpSalutation" name="Salutation">
                                            <option value="Mr">Mr</option>
                                            <option value="Ms">Ms</option>
                                            <option value="Mrs">Mrs</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Column 3 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="editEmpPermanentAddress">Permanent Address</label>
                                        <textarea class="form-control" id="editEmpPermanentAddress"
                                            name="PermanentAddress"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpCompanyEmail">Company Email</label>
                                        <input type="email" class="form-control" id="editEmpCompanyEmail"
                                            name="CompanyEmail" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpStatus">Status</label>
                                        <select class="form-control" id="editEmpStatus" name="Status">
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                            <option value="Suspended">Suspended</option>
                                            <option value="Left">Left</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="editEmpDesignation">Designation</label>
                                        <select class="form-control" id="editEmpDesignation" name="Designation">
                                            <option value="Director">Director</option>
                                            <option value="Project Manager">Project Manager</option>
                                            <option value="General Manager">General Manager</option>
                                            <option value="HR Manager">HR Manager</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Column 4 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="editEmpBranch">Branch</label>
                                        <input type="text" class="form-control" id="editEmpBranch" name="Branch">
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpCompany">Company</label>
                                        <input type="text" class="form-control" id="editEmpCompany" name="Company">
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpReportsTo">Reports To</label>
                                        <input type="text" class="form-control" id="editEmpReportsTo" name="ReportsTo">
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpEmploymentType">Employment Type</label>
                                        <select class="form-control" id="editEmpEmploymentType" name="EmploymentType">
                                            <option value="Intern">Intern</option>
                                            <option value="Full-time">Full-time</option>
                                            <option value="Part-time">Part-time</option>
                                            <option value="Contract">Contract</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Column 5 -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="editEmpDefaultShift">Default Shift</label>
                                        <input type="text" class="form-control" id="editEmpDefaultShift"
                                            name="DefaultShift">
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpEmergencyContactName">Emergency Contact Name</label>
                                        <input type="text" class="form-control" id="editEmpEmergencyContactName"
                                            name="EmergencyContactName">
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpEmergencyPhone">Emergency Phone</label>
                                        <input type="text" class="form-control" id="editEmpEmergencyPhone"
                                            name="EmergencyPhone">
                                    </div>
                                    <div class="form-group">
                                        <label for="editEmpRelation">Relation</label>
                                        <input type="text" class="form-control" id="editEmpRelation" name="Relation">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
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

                $('#employeesTable').DataTable();
            });
            function editEmployee(id, empId, name, epf, phone, currentAddress, personalEmail, companyEmail, dateOfJoining, status, salutation, designation, branch, company, reportsTo, employmentType, emergencyContactName, emergencyPhone, relation, shift) {
                console.log(id, empId, name, epf, phone);  // Debugging log

                document.getElementById('editEmpId').value = id;
                document.getElementById('editEmpIdField').value = empId;
                document.getElementById('editEmpNameField').value = name;
                document.getElementById('editEmpEPF').value = epf;
                document.getElementById('editEmpPhone').value = phone;
                document.getElementById('editEmpCurrentAddress').value = currentAddress;
                document.getElementById('editEmpPersonalEmail').value = personalEmail;
                document.getElementById('editEmpCompanyEmail').value = companyEmail;
                document.getElementById('editEmpDateOfJoining').value = dateOfJoining;
                document.getElementById('editEmpStatus').value = status;
                document.getElementById('editEmpSalutation').value = salutation;
                document.getElementById('editEmpDesignation').value = designation;
                document.getElementById('editEmpBranch').value = branch;
                document.getElementById('editEmpCompany').value = company;
                document.getElementById('editEmpReportsTo').value = reportsTo;
                document.getElementById('editEmpEmploymentType').value = employmentType;
                document.getElementById('editEmpEmergencyContactName').value = emergencyContactName;
                document.getElementById('editEmpEmergencyPhone').value = emergencyPhone;
                document.getElementById('editEmpRelation').value = relation;
                document.getElementById('editEmpDefaultShift').value = shift;

                $('#editEmployeeModal').modal('show');
            }


        </script>

</body>

</html>