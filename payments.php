<?php
include('security.php');
check_login();

session_start();
include('db.php'); 
include('includes/header.php');
include('includes/navbar.php');

$msg = '';
$error = '';

// Handle deletion of payroll records (if required)
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM payroll WHERE payroll_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Payroll record deleted successfully.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Fetching dynamic salary components from the database
$salaryComponents = [];
$componentQuery = "SELECT COLUMN_NAME FROM information_schema.columns 
                   WHERE TABLE_NAME = 'payroll' 
                   AND COLUMN_NAME NOT IN ('payroll_id', 'employee_id', 'basic_salary', 'deductions', 'net_salary', 'payment_date')";
$componentResult = $conn->query($componentQuery);

if ($componentResult && $componentResult->num_rows > 0) {
    while ($componentRow = $componentResult->fetch_assoc()) {
        $salaryComponents[] = $componentRow['COLUMN_NAME'];
    }
} else {
    $error = "Failed to retrieve salary components: " . mysqli_error($conn);
}

// Constructing the SQL query dynamically to include salary components and employee information
if (!empty($salaryComponents)) {
    $componentColumns = implode(", p.", $salaryComponents);

    $sql = "SELECT p.payroll_id, p.employee_id, e.EmpId, e.NameWithInitials, p.basic_salary, p.deductions, 
            p." . $componentColumns . ", 
            (p.basic_salary + p." . implode(" + p.", $salaryComponents) . " - p.deductions) AS net_salary,
            p.payment_date
            FROM payroll p
            JOIN tblemployees e ON p.employee_id = e.id";
} else {
    $sql = "SELECT p.payroll_id, p.employee_id, e.EmpId, e.NameWithInitials, p.basic_salary, p.deductions, 
            (p.basic_salary - p.deductions) AS net_salary,
            p.payment_date
            FROM payroll p
            JOIN tblemployees e ON p.employee_id = e.id";
}

// Execute the query and check for errors
$result = $conn->query($sql);
if (!$result) {
    $error = "Error executing query: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Payroll Records</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
        <h2 class="mb-4">Payroll Records</h2>

        <?php
        if ($error) {
            echo '<div class="errorWrap"><strong>ERROR</strong>: ' . htmlentities($error) . '</div>';
        }
        if ($msg) {
            echo '<div class="succWrap"><strong>SUCCESS</strong>: ' . htmlentities($msg) . '</div>';
        }
        ?>

        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span><i class="fas fa-dollar-sign"></i> Payroll Information</span>
                <a href="type_payroll.php" class="btn btn-light btn-sm">
                    <i class="fas fa-plus"></i> Add Payroll Record
                </a>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>EmpId & Name</th> <!-- Combining EmpId and Name -->
                            <th>Basic Salary</th>
                            <?php if (!empty($salaryComponents)) { ?>
                                <?php foreach ($salaryComponents as $component) { ?>
                                    <th><?php echo ucwords(str_replace("_", " ", $component)); ?></th>
                                <?php } ?>
                            <?php } ?>
                            <th>Deductions</th>
                            <th>Net Salary</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['payroll_id'] . "</td>";

                                // Display both EmpId and Name in one column
                                echo "<td>" . $row['EmpId'] . " - " . $row['NameWithInitials'] . "</td>";

                                echo "<td>" . number_format((float) $row['basic_salary'], 2) . "</td>";

                                // Display dynamic salary components and handle number formatting
                                foreach ($salaryComponents as $component) {
                                    echo "<td>" . number_format((float) $row[$component], 2) . "</td>";
                                }

                                echo "<td>" . number_format((float) $row['deductions'], 2) . "</td>";
                                echo "<td>" . number_format((float) $row['net_salary'], 2) . "</td>";

                                echo "<td>
                                        <a href='employee_payroll_report.php?id=" . $row['payroll_id'] . "' class='btn btn-info btn-sm' title='View Record'>
                                            <i class='fas fa-eye'></i>
                                        </a>
                                        <a href='edit_payroll.php?id=" . $row['payroll_id'] . "' class='btn btn-warning btn-sm' title='Edit Record'>
                                            <i class='fas fa-edit'></i>
                                        </a>
                                        <a href='payments.php?del=" . $row['payroll_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Do you want to delete this payroll record?\");' title='Delete Record'>
                                            <i class='fas fa-trash-alt'></i>
                                        </a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='12'>No payroll records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS, jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
// Close the database connection
$conn->close();
include('includes/scripts.php');
include('includes/footer.php');
?>