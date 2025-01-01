<?php
// Include security checks
include_once('security.php'); // Changed to include_once
check_login();

include('includes/header.php');
include('includes/navbar.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FullCalendar with Year View</title>
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <style>
        /* Custom CSS for Calendar */
        #calendar {
            border: 1px solid black;
            border-radius: 5px;
            overflow: hidden;
            width: 60%;
            margin: 20px auto;
            padding: 10px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div id="calendar"></div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <!-- Custom JS -->
    <script src="calendar_init.js"></script>
</body>

</html>