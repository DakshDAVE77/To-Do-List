<?php
// Include the connection file
include "connect.php";

// Initialize variables
$message = "";

// Validation function
function validateForm($task, $start_time, $end_time, $conn)
{
    $errorMessages = [];

    if (empty($task)) {
        $errorMessages[] = "Please fill in your task.";
    }
    if (empty($start_time)) {
        $errorMessages[] = "Please fill in your Start time.";
    }
    if (empty($end_time)) {
        $errorMessages[] = "Please fill in your End time.";
    }

    // Check if the task already exists
    $sql_check = "SELECT * FROM work WHERE task = '$task'";
    $result_check = mysqli_query($conn, $sql_check);
    if (mysqli_num_rows($result_check) > 0) {
        $errorMessages[] = "Task already exists.";
    }

    return $errorMessages;
}

// Function to delete all records from work table
function deleteAllTasks($conn)
{
    $sql = "DELETE FROM work";
    if (mysqli_query($conn, $sql)) {
        return true;
    } else {
        return false;
    }
}

// Function to delete a single task
function deleteTask($conn, $task_no)
{
    $sql = "DELETE FROM work WHERE task_no = '$task_no'";
    if (mysqli_query($conn, $sql)) {
        return true;
    } else {
        return false;
    }
}

// Function to fetch a single task for editing
function fetchTask($conn, $task_no)
{
    $sql = "SELECT * FROM work WHERE task_no = '$task_no'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

// Function to update start and end time of a task
function updateTask($conn, $task_no, $start_time, $end_time)
{
    $sql = "UPDATE work SET start_time = '$start_time', end_time = '$end_time' WHERE task_no = '$task_no'";
    if (mysqli_query($conn, $sql)) {
        return true;
    } else {
        return false;
    }
}

// Function to get the last cleanup timestamp from a file or database
function getLastCleanupTime()
{
    // Example: Read from a file or database where you store the last cleanup timestamp
    // For simplicity, using a file here (timestamp.txt) in the same directory
    $filename = 'timestamp.txt';
    if (file_exists($filename)) {
        $last_time = trim(file_get_contents($filename));
        return strtotime($last_time);
    } else {
        return 0;
    }
}

// Insertion of new task
if (isset($_POST["create"])) {
    $task = $_POST["task"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];

    // Perform validation
    $validationErrors = validateForm($task, $start_time, $end_time, $conn);

    if (empty($validationErrors)) {
        // Insert data into database
        $sql = "INSERT INTO work (task, start_time, end_time) VALUES ('$task', '$start_time', '$end_time')";
        if (mysqli_query($conn, $sql)) {
            $message = "Task inserted successfully";
        } else {
            $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    } else {
        // Handle validation errors
        $message = implode("<br>", $validationErrors);
    }
}

// Deletion of tasks if confirmed
if (isset($_POST["confirm_delete"])) {
    if ($_POST["confirm_delete"] === "yes") {
        if (deleteAllTasks($conn)) {
            $message = "All tasks deleted successfully.";
            // Update the last cleanup timestamp after successful deletion
            $timestamp = date('Y-m-d H:i:s');
            $filename = 'timestamp.txt';
            file_put_contents($filename, $timestamp);
        } else {
            $message = "Error occurred during deletion.";
        }
    } else {
        $message = "Deletion canceled.";
    }
}

// Delete individual task
if (isset($_POST["delete_single"])) {
    $task_no = $_POST["delete_single"];
    if (deleteTask($conn, $task_no)) {
        $message = "Task deleted successfully.";
    } else {
        $message = "Error deleting task.";
    }
}

// Edit task
if (isset($_POST["edit_single"])) {
    $task_no = $_POST["edit_single"];
    $start_time = $_POST["start_time_edit"];
    $end_time = $_POST["end_time_edit"];

    // Validate start and end times
    $validationErrors = [];
    if (empty($start_time)) {
        $validationErrors[] = "Please fill in Start time.";
    }
    if (empty($end_time)) {
        $validationErrors[] = "Please fill in End time.";
    }

    if (empty($validationErrors)) {
        // Update task in the database
        if (updateTask($conn, $task_no, $start_time, $end_time)) {
            $message = "Task updated successfully.";
            // Refresh the page after update to reflect changes
            echo '<script>window.location.href = window.location.href;</script>';
            exit;
        } else {
            $message = "Error updating task.";
        }
    } else {
        $message = implode("<br>", $validationErrors);
    }
}



// Check if current time is past midnight (00:00) and prompt for deletion
$current_time = date("H:i");
if ($current_time >= "00:00") {
    // Check if cleanup has already been performed today
    $last_cleanup_time = getLastCleanupTime();
    $today_midnight = strtotime(date("Y-m-d 00:00:00"));
    if ($last_cleanup_time < $today_midnight) {
        // Prompt user for confirmation before deleting
        $message .= " Are you sure you want to delete all tasks from the previous day? This action cannot be undone.";
        $message .= '<form method="post"><input type="hidden" name="confirm_delete" value="yes">';
        $message .= '<button type="submit" class="btn btn-danger">Yes, delete all</button>';
        $message .= '<button onclick="window.location.reload();" class="btn btn-secondary">Cancel</button></form>';
    }
}

// Query to retrieve data
$sql = "SELECT * FROM work";
// Perform the query
$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Zen+Dots&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- Reference to the external CSS file -->
    <style>
        body {
            background-color: #ffffff; /* Set background color to white */
        }
        .form-bg {
            max-width:980px;
            width:100%;
        }
        h1, h2 {
            font-family: 'Zen Dots';
            color: black;
            text-align: center;
        }
        .form-container {
            background-color: #ffffff; /* Set background color to white */
            padding:20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="form-bg">
        <div class="container mt-5">
            <div class="form-container">
                <h1>To-Do List</h1>
                <form method="post">
                    <div class="mb-3">
                        <label for="task" class="form-label">Task</label>
                        <select id="task" name="task" class="form-select">
                            <option value="Wake-Up">Wake-Up</option>
                            <option value="Gym">Gym</option>
                            <option value="Breakfast">Breakfast</option>
                            <option value="Heading Office">Heading Office</option>
                            <option value="Internship(Office)">Internship(Office)</option>
                            <option value="Lunch">Lunch</option>
                            <option value="High Tea">High Tea</option>
                            <option value="Heading Home">Heading Home</option>
                            <option value="Dinner">Dinner</option>
                            <option value="Evening Walk">Evening Walk</option>
                            <option value="Sleep">Sleep</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" name="start_time" id="start_time" class="form-control" color="black">
                    </div>

                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" name="end_time" id="end_time" class="form-control">
                    </div>

                    <button type="submit" name="create" class="btn btn-primary">Add Task</button>
                    <button onclick="window.location.reload();" class="btn btn-secondary mt-3">Refresh</button>
                    <div id="error-container" class="mt-3">
                        <?php if (!empty($message)): ?>
                            <p style="color: green;"><?php echo $message; ?></p>
                        <?php endif; ?>
                    </div>
                </form>

                <h2 class="mt-5">Task List</h2>
                <form method="post" id="deleteForm">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Task</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $row["task"]; ?></td>
                                    <td><?php echo $row["start_time"]; ?></td>
                                    <td><?php echo $row["end_time"]; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row["task_no"]; ?>">Edit</button>
                                        <button type="submit" name="delete_single" value="<?php echo $row["task_no"]; ?>" class="btn btn-danger btn-sm">Delete</button>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal<?php echo $row["task_no"]; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $row["task_no"]; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel<?php echo $row["task_no"]; ?>">Edit Task</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <input type="hidden" name="edit_single" value="<?php echo $row["task_no"]; ?>">
                                                    <div class="mb-3">
                                                        <label for="start_time_edit" class="form-label">Start Time</label>
                                                        <input type="time" name="start_time_edit" id="start_time_edit" class="form-control" value="<?php echo $row["start_time"]; ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="end_time_edit" class="form-label">End Time</label>
                                                        <input type="time" name="end_time_edit" id="end_time_edit" class="form-control" value="<?php echo $row["end_time"]; ?>">
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php if (!empty(mysqli_num_rows($result))): ?>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
