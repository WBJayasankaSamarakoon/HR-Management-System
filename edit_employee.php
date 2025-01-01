<?php
session_start();
error_reporting(E_ALL); // Enable all error reporting for debugging
include('db.php');

$msg = '';
$error = '';

// Check if empid is set
if (isset($_GET['empid'])) {
    $empid = $_GET['empid'];

    // Retrieve employee details
    $sql = "SELECT * FROM tblemployees WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $empid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Map the database fields to variables
            $nameWithInitials = $row['NameWithInitials'];
            $epfNumber = $row['EPFNumber'];
            $phone = $row['Phone'];
            $currentAddress = $row['CurrentAddress'];
            $permanentAddress = $row['PermanentAddress'];
            $personalEmail = $row['PersonalEmail'];
            $companyEmail = $row['CompanyEmail'];
            $dateOfJoining = $row['DateOfJoining'];
            $status = $row['Status'];
            $salutation = $row['Salutation'];
            $designation = $row['Designation'];
            $branch = $row['Branch'];
            $company = $row['Company'];
            $reportsTo = $row['ReportsTo'];
            $employmentType = $row['EmploymentType'];
            $emergencyContactName = $row['EmergencyContactName'];
            $emergencyPhone = $row['EmergencyPhone'];
            $relation = $row['Relation'];
            $defaultShift = $row['DefaultShift'];
        } else {
            $error = "Employee not found.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
} else {
    $error = "Invalid request.";
}

// Handle form submission
if (isset($_POST['update'])) {
    // Sanitize input values
    $nameWithInitials = trim($_POST['nameWithInitials']);
    $epfNumber = trim($_POST['epfNumber']);
    $phone = trim($_POST['phone']);
    $currentAddress = trim($_POST['currentAddress']);
    $permanentAddress = trim($_POST['permanentAddress']);
    $personalEmail = trim($_POST['personalEmail']);
    $companyEmail = trim($_POST['companyEmail']);
    $dateOfJoining = trim($_POST['dateOfJoining']);
    $status = $_POST['status'];
    $salutation = $_POST['salutation'];
    $designation = trim($_POST['designation']);
    $branch = trim($_POST['branch']);
    $company = trim($_POST['company']);
    $reportsTo = trim($_POST['reportsTo']);
    $employmentType = $_POST['employmentType'];
    $emergencyContactName = trim($_POST['emergencyContactName']);
    $emergencyPhone = trim($_POST['emergencyPhone']);
    $relation = trim($_POST['relation']);
    $defaultShift = trim($_POST['defaultShift']);

    // Update employee data
    $sql = "UPDATE tblemployees SET 
        NameWithInitials = ?, EPFNumber = ?, Phone = ?, CurrentAddress = ?, PermanentAddress = ?, 
        PersonalEmail = ?, CompanyEmail = ?, DateOfJoining = ?, Status = ?, Salutation = ?, 
        Designation = ?, Branch = ?, Company = ?, ReportsTo = ?, EmploymentType = ?, 
        EmergencyContactName = ?, EmergencyPhone = ?, Relation = ?, DefaultShift = ? 
        WHERE id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssisssssssssssi",
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
            $empid
        );

        if (mysqli_stmt_execute($stmt)) {
            $msg = "Employee record updated successfully";
        } else {
            $error = "Error executing query: " . mysqli_error($conn);
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
    <title>Admin | Edit Employee</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
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

    <div class="container mt-4">
        <h2>Edit Employee</h2>

        <?php if ($error) { ?>
            <div class="errorWrap">
                <strong>ERROR</strong>: <?php echo htmlentities($error); ?>
            </div>
        <?php } else if ($msg) { ?>
                <div class="succWrap">
                    <strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?>
                </div>
        <?php } ?>

        <form method="post">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nameWithInitials">Name with Initials</label>
                    <input type="text" class="form-control" id="nameWithInitials" name="nameWithInitials"
                        value="<?php echo htmlentities($nameWithInitials); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="epfNumber">EPF Number</label>
                    <input type="text" class="form-control" id="epfNumber" name="epfNumber"
                        value="<?php echo htmlentities($epfNumber); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                        value="<?php echo htmlentities($phone); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="currentAddress">Current Address</label>
                    <input type="text" class="form-control" id="currentAddress" name="currentAddress"
                        value="<?php echo htmlentities($currentAddress); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="permanentAddress">Permanent Address</label>
                    <input type="text" class="form-control" id="permanentAddress" name="permanentAddress"
                        value="<?php echo htmlentities($permanentAddress); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="personalEmail">Personal Email</label>
                    <input type="email" class="form-control" id="personalEmail" name="personalEmail"
                        value="<?php echo htmlentities($personalEmail); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="companyEmail">Company Email</label>
                    <input type="email" class="form-control" id="companyEmail" name="companyEmail"
                        value="<?php echo htmlentities($companyEmail); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="dateOfJoining">Date of Joining</label>
                    <input type="date" class="form-control" id="dateOfJoining" name="dateOfJoining"
                        value="<?php echo htmlentities($dateOfJoining); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="Active" <?php echo ($status == 'Active') ? 'selected' : ''; ?>>Active</option>
                        <option value="Inactive" <?php echo ($status == 'Inactive') ? 'selected' : ''; ?>>Inactive
                        </option>
                        <option value="Suspended" <?php echo ($status == 'Suspended') ? 'selected' : ''; ?>>Suspended
                        </option>
                        <option value="Left" <?php echo ($status == 'Left') ? 'selected' : ''; ?>>Left</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="salutation">Salutation</label>
                    <select id="salutation" name="salutation" class="form-control" required>
                        <option value="Mr" <?php echo ($salutation == 'Mr') ? 'selected' : ''; ?>>Mr</option>
                        <option value="Ms" <?php echo ($salutation == 'Ms') ? 'selected' : ''; ?>>Ms</option>
                        <option value="Mrs" <?php echo ($salutation == 'Mrs') ? 'selected' : ''; ?>>Mrs</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="designation">Designation</label>
                    <input type="text" class="form-control" id="designation" name="designation"
                        value="<?php echo htmlentities($designation); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="branch">Branch</label>
                    <input type="text" class="form-control" id="branch" name="branch"
                        value="<?php echo htmlentities($branch); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="company">Company</label>
                    <input type="text" class="form-control" id="company" name="company"
                        value="<?php echo htmlentities($company); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="reportsTo">Reports To</label>
                    <input type="text" class="form-control" id="reportsTo" name="reportsTo"
                        value="<?php echo htmlentities($reportsTo); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="employmentType">Employment Type</label>
                    <select id="employmentType" name="employmentType" class="form-control" required>
                        <option value="Full-time" <?php echo ($employmentType == 'Full-time') ? 'selected' : ''; ?>>
                            Full-time</option>
                        <option value="Part-time" <?php echo ($employmentType == 'Part-time') ? 'selected' : ''; ?>>
                            Part-time</option>
                        <option value="Intern" <?php echo ($employmentType == 'Intern') ? 'selected' : ''; ?>>Intern
                        </option>
                        <option value="Contract" <?php echo ($employmentType == 'Contract') ? 'selected' : ''; ?>>
                            Contract</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label for="emergencyContactName">Emergency Contact Name</label>
                    <input type="text" class="form-control" id="emergencyContactName" name="emergencyContactName"
                        value="<?php echo htmlentities($emergencyContactName); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="emergencyPhone">Emergency Phone</label>
                    <input type="text" class="form-control" id="emergencyPhone" name="emergencyPhone"
                        value="<?php echo htmlentities($emergencyPhone); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="relation">Relation</label>
                    <input type="text" class="form-control" id="relation" name="relation"
                        value="<?php echo htmlentities($relation); ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="defaultShift">Default Shift</label>
                    <input type="text" class="form-control" id="defaultShift" name="defaultShift"
                        value="<?php echo htmlentities($defaultShift); ?>" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" name="update">Update</button>
        </form>
    </div>

    <?php include('includes/footer.php'); ?>
</body>

</html>