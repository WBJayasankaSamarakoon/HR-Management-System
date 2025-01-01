<?php
session_start();
include('db.php');

// Check if the necessary POST variables are set
if (isset($_POST['emp_id']) && isset($_POST['year']) && isset($_POST['month'])) {
    $emp_id = $conn->real_escape_string($_POST['emp_id']);
    $year = $conn->real_escape_string($_POST['year']);
    $month = $conn->real_escape_string($_POST['month']);

    // Fetch employee and payroll details
    $query = "
        SELECT 
            e.EmpId as emp_id,
            e.NameWithInitials as name,
            p.basic_salary,
            p.BRA1,
            p.BRA2,
            p.AttendanceIncentive,
            p.SuperAttendance,
            p.PerformanceIncentive,
            p.deductions,
            p.payment_date,
            (p.basic_salary + p.AttendanceIncentive + p.SuperAttendance + p.PerformanceIncentive + p.BRA1 + p.BRA2 - p.deductions) AS net_salary,
            ROUND(SUM(u.Late)/60 + SUM(u.Early)/60, 2) as late_hours,
            COUNT(CASE 
                    WHEN (u.CheckIn IS NULL OR u.CheckIn = '00:00:00' OR u.CheckOut IS NULL OR u.CheckOut = '00:00:00') 
                    THEN 1 
                 END) as no_pay_count
        FROM ueshrattendancedaily u
        JOIN tblemployees e ON u.PersonID = e.EmpId
        LEFT JOIN payroll p ON e.id = p.employee_id
        WHERE e.EmpId = '$emp_id' 
        AND u.Year = '$year' 
        AND u.Month = '$month'
        GROUP BY e.EmpId
    ";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        die('Query Failed: ' . mysqli_error($conn));
    }

    $salarySlip = mysqli_fetch_assoc($result);
} else {
    die('Invalid request.');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Slip for <?php echo htmlentities($salarySlip['name']); ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .salary-slip {
            width: 60%;
            margin: 50px auto;
            background: #fff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            overflow: hidden;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        p {
            text-align: center;
            margin: 0 0 10px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }

        td:first-child {
            text-align: left;
            color: #555;
        }

        .summary-row {
            background-color: #e9f6ff;
            font-weight: bold;
        }

        .no-border {
            border: none !important;
        }

        .center {
            text-align: center !important;
            font-weight: bold;
        }

        .footer-table td {
            border: none;
        }

        .highlight {
            color: #ff6600;
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="salary-slip">
        <h2>ULTIMATE ENGINEERING SOLUTIONS (PVT) LTD</h2>

        <table class="no-border">
            <tr>
                <td class="no-border"><strong>Name:</strong> <?php echo htmlentities($salarySlip['name']); ?></td>
                <td class="no-border"><strong>Employee No:</strong> <?php echo htmlentities($salarySlip['emp_id']); ?>
                </td>
                <td class="no-border center"><strong>Month: <?php echo htmlentities($month . ' / ' . $year); ?></strong>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td>Basic Salary</td>
                <td><?php echo number_format($salarySlip['basic_salary'], 2); ?></td>
            </tr>
            <tr>
                <td>B.R.A. 1</td>
                <td><?php echo number_format($salarySlip['BRA1'], 2); ?></td>
            </tr>
            <tr>
                <td>B.R.A. 2</td>
                <td><?php echo number_format($salarySlip['BRA2'], 2); ?></td>
            </tr>
            <tr class="summary-row">
                <td><strong>Gross Salary</strong></td>
                <td><strong><?php echo number_format($salarySlip['basic_salary'] + $salarySlip['BRA1'] + $salarySlip['BRA2'], 2); ?></strong>
                </td>
            </tr>
            <tr>
                <td>Nopay - <?php echo $salarySlip['no_pay_count']; ?> Days</td>
                <td><?php echo number_format(0, 2); ?></td>
            </tr>
            <tr>
                <td>Late - <?php echo $salarySlip['late_hours']; ?> Hours</td>
                <td><?php echo number_format(0, 2); ?></td>
            </tr>
            <tr class="summary-row">
                <td><strong>Salary for EPF</strong></td>
                <td><strong><?php echo number_format($salarySlip['basic_salary'], 2); ?></strong></td>
            </tr>

            <tr class="summary-row">
                <td colspan="2" class="center"><strong>Additions</strong></td>
            </tr>
            <tr>
                <td>Attendance Incentive</td>
                <td><?php echo number_format($salarySlip['AttendanceIncentive'], 2); ?></td>
            </tr>
            <tr>
                <td>Supper Attendance</td>
                <td><?php echo number_format($salarySlip['SuperAttendance'], 2); ?></td>
            </tr>
            <tr>
                <td>Performance Incentive</td>
                <td><?php echo number_format($salarySlip['PerformanceIncentive'], 2); ?></td>
            </tr>
            <tr>
                <td>Sales Incentive</td>
                <td><?php echo number_format(0, 2); ?></td>
            </tr>
            <tr class="summary-row">
                <td><strong>Total Additions</strong></td>
                <td><strong><?php echo number_format($salarySlip['AttendanceIncentive'] + $salarySlip['SuperAttendance'] + $salarySlip['PerformanceIncentive'], 2); ?></strong>
                </td>
            </tr>

            <tr class="summary-row">
                <td colspan="2" class="center"><strong>Deductions</strong></td>
            </tr>
            <tr>
                <td>Deductions</td>
                <td><?php echo number_format($salarySlip['deductions'], 2); ?></td>
            </tr>
            <tr class="summary-row">
                <td><strong>Total Deductions</strong></td>
                <td><strong><?php echo number_format($salarySlip['deductions'], 2); ?></strong></td>
            </tr>

            <tr class="summary-row">
                <td><strong>Net Salary</strong></td>
                <td><strong class="highlight"><?php echo number_format($salarySlip['net_salary'], 2); ?></strong></td>
            </tr>
        </table>

        <table class="footer-table">
            <tr>
                <td>Payment Date: <?php echo date('Y-m-d', strtotime($salarySlip['payment_date'])); ?></td>
            </tr>
        </table>

        <!-- Action Buttons -->
        <div class="text-center mt-4">
            <a href="employee_payroll_report.php?id=4" class="btn btn-secondary">Back</a>
            <button class="btn btn-primary" onclick="generatePayslipPDF()">Generate Payslip PDF</button>
            <button class="btn btn-success" onclick="sendPayslips()">Send Payslips</button>
        </div>
    </div>

    <!-- Include Bootstrap JS, jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function generatePayslipPDF() {
            // Your logic to generate PDF goes here
            alert('PDF generation functionality to be implemented.');
        }

        function sendPayslips() {
            // Your logic to send payslips goes here
            alert('Email sending functionality to be implemented.');
        }
    </script>
</body>

</html>