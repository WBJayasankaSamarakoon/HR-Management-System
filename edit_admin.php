<?php
include('security.php');
include('db.php');
include('includes/header.php');
include('includes/navbar.php');

// Check if the form is submitted
if (isset($_POST['edit_btn'])) {
    $id = $_POST['edit_id'];

    // Fetch the current data of the selected admin
    $query = "SELECT * FROM admin WHERE id='$id'";
    $query_run = mysqli_query($conn, $query);

    if (mysqli_num_rows($query_run) > 0) {
        $row = mysqli_fetch_assoc($query_run);
        ?>
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Admin Profile</h6>
                </div>
                <div class="card-body">
                    <form action="update_admin.php" method="POST">
                        <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">

                        <div class="form-group">
                            <label> Username </label>
                            <input type="text" name="username" value="<?php echo $row['username']; ?>" class="form-control"
                                placeholder="Enter Username" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo $row['email']; ?>" class="form-control"
                                placeholder="Enter Email" required>
                        </div>
                        <div class="form-group">
                            <label>Password (Leave blank if you don't want to change)</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter New Password">
                        </div>

                        <a href="register.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" name="update_btn" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo "<h4>No Record Found</h4>";
    }
}

include('includes/scripts.php');
include('includes/footer.php');
?>