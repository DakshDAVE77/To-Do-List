<?php
// Include the connection file
include('connect.php');

// Initialize variables
$message = '';

// Insertion of new task
if(isset($_POST['create'])) {
    $task = $_POST['task'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    
    // Insert data into database
    $sql = "INSERT INTO work (task, start_time, end_time) VALUES ('$task', '$start_time', '$end_time')";
    
    if(mysqli_query($conn, $sql)) {
        $message = "Task inserted successfully";
    } else {
        $message = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Query to retrieve data
if($message == "Task inserted successfully"){// Replace 'your_table_name' with the actual name of your table


    $sql = "SELECT * FROM work";
// Perform the query
    $result = mysqli_query($conn, $sql);

     // Check if there are any results
     if(mysqli_num_rows($result) > 0) {
        echo "<div id='myModal' class='modal' style='display: block'>";
        echo "<div class='modal-dialog'>";
        echo "<div class='modal-content'>";
        echo "<div class='modal-header'>";
        echo "<h4 class='modal-title'>Task List</h4>";
        echo "<button type='button' class='close' data-dismiss='modal'>&times;</button>";
        echo "</div>";
        echo "<div class='modal-body'>";
        echo "<table class='table'>";
        echo "<thead><tr><th>ID</th><th>Task</th><th>Start Time</th><th>End Time</th></tr></thead>";
        echo "<tbody>";
        // Output data of each row
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>" . $row["task_no"] . "</td><td>" . $row["task"] . "</td><td>" . $row["start_time"] . "</td><td>" . $row["end_time"] . "</td></tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "<div class='modal-footer'>";
        
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "0 results";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgb(0,0,0);
            background: radial-gradient(circle, rgba(0,0,0,0.028124999999999956) 0%, rgba(253,187,45,1) 100%);
            font-family: Arial, sans-serif;
            text-align: center;
        }

        form {
            width: 300px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: radial-gradient(circle, rgba(0,0,0,0.028124999999999956) 0%, rgba(253,187,45,1) 100%);
        }

        input[type="text"], button {
            width: calc(100% - 20px); /* Subtracting padding from the width */
            margin-bottom: 10px;
            padding: 8px;
            box-sizing: border-box; /* Ensure padding is included in the width */
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <form method="post">
        <h1>To-Do List</h1>
        <input type="text" name="task" id="task" placeholder="Enter task">
        <input type="text" name="start_time" id="start_time" placeholder="Enter start time">
        <input type="text" name="end_time" id="end_time" placeholder="Enter end time">
        <button type="submit" name="create" class="btn btn-primary">Add Task</button>
        <?php if (!empty($message)): ?>
            <p style="color: green;"><?php echo $message; ?></p>
        <?php endif; ?>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Get the close button element
        var closeButton = document.querySelector('.close');

        // Add click event listener to close the modal
        closeButton.addEventListener('click', function() {
            var modal = document.querySelector('.modal');
            modal.style.display = 'none';
        });
    </script>
</body>
</html>
