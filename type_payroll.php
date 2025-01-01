<?php
ob_start(); // Start output buffering
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db.php');
include('includes/header.php');
include('includes/navbar.php');

// Function to get employees from tblemployees
function getEmployees($conn)
{
    $sql = "SELECT id, EmpId, NameWithInitials, EPFNumber FROM tblemployees WHERE Status = 'Active'";
    $result = $conn->query($sql);
    $employees = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
    }
    return $employees;
}

// Fetch employees once
$employees = getEmployees($conn);

// Fetch salary structure components from the database
$salary_components = [];
$sql = "SELECT * FROM salarystructure";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $salary_components[] = $row;
    }
}

// Initialize message variables
$msg = '';
$error = '';

// Function to check if a column exists in the payroll table
function columnExists($conn, $column)
{
    $result = $conn->query("SHOW COLUMNS FROM payroll LIKE '$column'");
    return $result->num_rows > 0;
}

// Function to add a new column to the payroll table
function addPayrollColumn($conn, $column)
{
    $sql = "ALTER TABLE payroll ADD $column DECIMAL(10,2) DEFAULT 0";
    return $conn->query($sql);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = $_POST['employee_id'] ?? null;
    $basic_salary = $_POST['basic_salary'] ?? 0;
    $payment_date = $_POST['payment_date'] ?? null;
    $deductions = $_POST['deductions'] ?? 0;

    // Validate payment date
    if (!$payment_date || $payment_date === '0000-00-00') {
        $error = "Invalid payment date.";
    } else {
        // Dynamically collect components from the form
        $component_values = [];
        foreach ($salary_components as $component) {
            $component_name = strtolower($component['Name']);
            $component_values[$component_name] = isset($_POST[$component_name]) ? (float) $_POST[$component_name] : 0.0;
        }

        // Validate basic salary and deductions
        $basic_salary = (float) $basic_salary;
        $deductions = (float) $deductions;

        // Calculate total allowances including new components
        $total_allowances = $basic_salary; // Start with basic salary

        foreach ($component_values as $component_value) {
            $total_allowances += $component_value;
        }

        // Calculate net salary
        $net_salary = $total_allowances - $deductions;

        // Insert query for payroll table
        $sql = "INSERT INTO payroll (employee_id, basic_salary, deductions, payment_date, net_salary, ";

        // Add dynamic component columns to query
        foreach ($component_values as $component_name => $value) {
            $sql .= "$component_name, ";
        }
        $sql = rtrim($sql, ', ') . ") VALUES (?, ?, ?, ?, ?, ";

        // Add dynamic placeholders for component values
        foreach ($component_values as $component_name => $value) {
            $sql .= "?, ";
        }
        $sql = rtrim($sql, ', ') . ")";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            $params = [$employee_id, $basic_salary, $deductions, $payment_date, $net_salary];

            // Bind dynamic component values
            foreach ($component_values as $value) {
                $params[] = $value;
            }

            mysqli_stmt_bind_param($stmt, str_repeat('d', count($params)), ...$params);
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to payments.php after successful insertion
                header("Location: payments.php");
                exit(); // Ensure script stops executing after redirect
            } else {
                $error = "Error adding record: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Error preparing statement: " . mysqli_error($conn);
        }
    }
}
$conn->close();
ob_end_flush(); // End output buffering and flush output
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Payroll</title>

    <script>
        // Function to calculate and display the net salary dynamically
        function calculateNetSalary() {
            var basicSalary = parseFloat(document.getElementById('basic_salary').value) || 0;
            var deductions = parseFloat(document.getElementById('deductions').value) || 0;
            var totalSalary = basicSalary;

            // Add all component values
            document.querySelectorAll('.salary-component').forEach(function (input) {
                var componentValue = parseFloat(input.value) || 0;
                totalSalary += componentValue;
            });

            // Calculate net salary
            var netSalary = totalSalary - deductions;
            document.getElementById('net_salary').value = netSalary.toFixed(2);
        }

        function fillSalary() {
            var employees = <?php echo json_encode($employees); ?>;
            var employeeSelect = document.getElementById('employee_id');
            var salaryInput = document.getElementById('basic_salary');
            var selectedEmployeeId = employeeSelect.value;
            var selectedEmployee = employees.find(emp => emp.id == selectedEmployeeId);
            if (selectedEmployee) {
                salaryInput.value = selectedEmployee.EPFNumber;
            } else {
                salaryInput.value = '';
            }
            calculateNetSalary();
        }
    </script>
</head>

<body>
    <div class="container">
        <h2>Employee Payroll Form</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlentities($error); ?></div>
        <?php endif; ?>

        <!-- Payroll Form -->
        <form action="" method="POST">
            <!-- Employee Selection -->
            <div class="form-group">
                <label for="employee_id">Employee:</label>
                <select name="employee_id" id="employee_id" class="form-control" onchange="fillSalary()" required>
                    <option value="">Select Employee</option>
                    <?php
                    foreach ($employees as $employee) {
                        echo "<option value='" . $employee['id'] . "'>" . $employee['EmpId'] . " - " . $employee['NameWithInitials'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Basic Salary Input -->
            <div class="form-group">
                <label for="basic_salary">Basic Salary:</label>
                <input type="number" step="0.01" name="basic_salary" id="basic_salary" class="form-control" required
                    onchange="calculateNetSalary()">
            </div>

            <!-- Dynamically Generated Salary Components -->
            <?php
            if (!empty($salary_components)) {
                foreach ($salary_components as $component) {
                    $component_name = strtolower($component['Name']);
                    echo "<div class='form-group'>";
                    echo "<label for='$component_name'>" . htmlspecialchars($component['Name']) . ":</label>";
                    echo "<input type='number' step='0.01' name='$component_name' class='form-control salary-component' value='0.00' onchange='calculateNetSalary()'>";
                    echo "</div>";
                }
            } else {
                echo "<p>No salary components available.</p>";
            }
            ?>

            <!-- Deductions Input -->
            <div class="form-group">
                <label for="deductions">Deductions:</label>
                <input type="number" step="0.01" name="deductions" id="deductions" class="form-control"
                    onchange="calculateNetSalary()">
            </div>

            <!-- Net Salary Display -->
            <div class="form-group">
                <label for="net_salary">Net Salary:</label>
                <input type="number" step="0.01" name="net_salary" id="net_salary" class="form-control" readonly>
            </div>

            <!-- Payment Date Input -->
            <div class="form-group">
                <label for="payment_date">Payment Date:</label>
                <input type="date" name="payment_date" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <br></br>
    </div>
</body>

 <!-- jQuery, Bootstrap JS, and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>


</html>