<?php
include('security.php');
check_login();

session_start();
include('db.php');
$msg = '';
$error = '';

// Handle deletion of an event
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM event WHERE Id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Event deleted successfully.";
        } else {
            $error = "Error deleting event. Please try again.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Fetch all events from the database
$sql = "SELECT * FROM event";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    
    <!-- Bootstrap CSS -->
    <link href="src/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="src/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="container-fluid mt-4">
        <h2 class="mb-4">Manage Events</h2>

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

        <!-- Add Event Button -->
        <div class="mb-3">
            <a href="index_calendar.php" class="btn btn-primary">
                <i class="fas fa-plus"></i>
            </a>
        </div>


        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-calendar-alt"></i> Events List
            </div>
            <div class="card-body">
                <table id="eventsTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                    <td><?php echo htmlentities($row['Id']); ?></td>
                                    <td><?php echo htmlentities($row['Title']); ?></td>
                                    <td><?php echo htmlentities($row['Date']); ?></td>
                                    <td>
                                        <a href="index_calendar.php" class="btn btn-warning edit-btn">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <a href="view_events.php?del=<?php echo htmlentities($row['Id']); ?>"
                                            class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this event?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="4" class="text-center">No events found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEventModalLabel">Add Event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="add_event.php" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="eventTitle">Title</label>
                            <input type="text" name="title" class="form-control" id="eventTitle" required>
                        </div>
                        <div class="form-group">
                            <label for="eventDate">Date</label>
                            <input type="date" name="date" class="form-control" id="eventDate" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="add_event" class="btn btn-primary">Add Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="edit_event.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="event_id" id="eventId">
                        <div class="form-group">
                            <label for="editEventTitle">Title</label>
                            <input type="text" name="title" class="form-control" id="editEventTitle" required>
                        </div>
                        <div class="form-group">
                            <label for="editEventDate">Date</label>
                            <input type="date" name="date" class="form-control" id="editEventDate" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="update_event" class="btn btn-primary">Save Changes</button>
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
            $('#eventsTable').DataTable(); // Initialize DataTable
        });

        // Pass data to Edit Event Modal
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const date = this.getAttribute('data-date');

                document.getElementById('eventId').value = id;
                document.getElementById('editEventTitle').value = title;
                document.getElementById('editEventDate').value = date;
            });
        });
    </script>
</body>

<?php
include('includes/scripts.php');
include('includes/footer.php');
?>