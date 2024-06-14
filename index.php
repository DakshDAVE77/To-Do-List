<?php
// Include the connection file
include "connect.php";

// Initialize variables
$message = "";

// Validation function
function validateForm($task, $start_time, $end_time)
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

    return $errorMessages;
}

// Insertion of new task
if (isset($_POST["create"])) {
    $task = $_POST["task"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];

    // Perform validation
    $validationErrors = validateForm($task, $start_time, $end_time);

    if (empty($validationErrors)) {
        // Check if the task already exists
        $sql_check = "SELECT * FROM work WHERE task = '$task'";
        $result_check = mysqli_query($conn, $sql_check);
        if (mysqli_num_rows($result_check) > 0) {
            $message = "Task already exists.";
        } else {
            // Insert data into database
            $sql = "INSERT INTO work (task, start_time, end_time) VALUES ('$task', '$start_time', '$end_time')";
            if (mysqli_query($conn, $sql)) {
                $message = "Task inserted successfully";
            } else {
                $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    } else {
        // Handle validation errors
        $message = implode("<br>", $validationErrors);
    }
}

// Deletion of task
if (isset($_POST["delete"])) {
    $deleteIds = $_POST["deleteIds"];
    if (!empty($deleteIds)) {
        $uniqueIds = array_unique($deleteIds); // Remove duplicates 
        if (count($uniqueIds) !== count($deleteIds)) {
            $message = "Duplicate tasks selected for deletion.";
        } else {
            foreach ($deleteIds as $id) {
                // Delete task from database
                $sql = "DELETE FROM work WHERE task_no = '$id'";
                mysqli_query($conn, $sql);
            }
            $message = "Selected tasks deleted successfully";
        }
    } else {
        // If no checkboxes are checked, refresh the page
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
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

                    <button type="submit" name="create" class="btn btn-primary">Add Task</button><button onclick="window.location.reload();" class="btn btn-secondary mt-3">Refresh</button>
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
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $row["task"]; ?></td>
                                    <td><?php echo $row["start_time"]; ?></td>
                                    <td><?php echo $row["end_time"]; ?></td>
                                    <td><input type="checkbox" name="deleteIds[]" value="<?php echo $row["task_no"]; ?>"></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <button type="submit" name="delete" class="btn btn-danger">Delete Selected</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
