<?php
include('security.php');
check_login();

include('includes/header.php');
include('includes/navbar.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Total of admin -->
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="register.php" style="text-decoration: none; color: inherit;">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Registered
                                    Admin</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">

                                    <?php
                                    // Database connection
                                    $connection = mysqli_connect("localhost", "ultieeel_ultieeel_ueshrdb_user", "FLnQFozJp,Hj", "ultieeel_ueshrdb");

                                    // Check connection
                                    if (!$connection) {
                                        die("Connection failed: " . mysqli_connect_error());
                                    }

                                    $query = "SELECT COUNT(id) AS total_admins FROM admin";
                                    $query_run = mysqli_query($connection, $query);

                                    if ($query_run) {
                                        $row = mysqli_fetch_assoc($query_run);
                                        echo '<h4>Total Admin: ' . $row['total_admins'] . '</h4>';
                                    } else {
                                        echo '<h1>Error fetching data</h1>';
                                    }

                                    mysqli_close($connection);
                                    ?>

                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-regular fa-address-card fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Count of Total Events -->
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="view_events.php" style="text-decoration: none; color: inherit;">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Events</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">

                                    <?php
                                    // Database connection
                                    $connection = mysqli_connect("localhost", "ultieeel_ultieeel_ueshrdb_user", "FLnQFozJp,Hj", "ultieeel_ueshrdb");

                                    if (!$connection) {
                                        die("Connection failed: " . mysqli_connect_error());
                                    }

                                    $query = "SELECT COUNT(id) AS total_events FROM event";
                                    $query_run = mysqli_query($connection, $query);

                                    if ($query_run) {
                                        $row = mysqli_fetch_assoc($query_run);
                                        echo '<h4>Total Events: ' . $row['total_events'] . '</h4>';
                                    } else {
                                        echo '<h1>Error fetching data</h1>';
                                    }

                                    mysqli_close($connection);
                                    ?>

                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Employees -->
        <div class="col-xl-3 col-md-6 mb-4">
            <a href="manage_employee.php" style="text-decoration: none; color: inherit;">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Employees
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">

                                    <?php
                                    // Database connection
                                    $connection = mysqli_connect("localhost", "ultieeel_ultieeel_ueshrdb_user", "FLnQFozJp,Hj", "ultieeel_ueshrdb");

                                    if (!$connection) {
                                        die("Connection failed: " . mysqli_connect_error());
                                    }

                                    $query = "SELECT COUNT(id) AS total_employees FROM tblemployees";
                                    $query_run = mysqli_query($connection, $query);

                                    if ($query_run) {
                                        $row = mysqli_fetch_assoc($query_run);
                                        echo '<h4>Total Employees: ' . $row['total_employees'] . '</h4>';
                                    } else {
                                        echo '<h1>Error fetching data</h1>';
                                    }

                                    mysqli_close($connection);
                                    ?>

                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>


        <!-- Total Leaves -->
        <div class="col-xl-3 col-md-6 mb-4 total-leaves-card">
            <a href="manage_leaveemp.php" style="text-decoration: none; color: inherit;">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Leaves
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">

                                    <?php
                                    // Database connection
                                    $connection = mysqli_connect("localhost", "ultieeel_ultieeel_ueshrdb_user", "FLnQFozJp,Hj", "ultieeel_ueshrdb");

                                    if (!$connection) {
                                        die("Connection failed: " . mysqli_connect_error());
                                    }

                                    $query = "SELECT COUNT(id) AS total_leaves FROM employee_leaves";
                                    $query_run = mysqli_query($connection, $query);

                                    if ($query_run) {
                                        $row = mysqli_fetch_assoc($query_run);
                                        echo '<h4>Total Leaves: ' . $row['total_leaves'] . '</h4>';
                                    } else {
                                        echo '<h1>Error fetching data</h1>';
                                    }

                                    mysqli_close($connection);
                                    ?>

                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <!-- Include footer -->
    <?php
    include('includes/scripts.php');
    include('includes/footer.php');
    ?>