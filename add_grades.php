<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = 'localhost';
$username = 'root';
$password = ''; // Change if you have set a MySQL password
$dbname = 'student_performance';

$conn = new mysqli($servername, $username, $password, $dbname);

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $grade = $_POST['grade'];

    $sql = "INSERT INTO grades (student_id, course_id, grade) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iid", $student_id, $course_id, $grade);

    if ($stmt->execute()) {
        $message = "New grade added successfully";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Grade</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="number"], input[type="submit"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            border-radius: 4px;
        }
        .success {
            background-color: #28a745;
            color: #fff;
        }
        .error {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var message = "<?php echo $message; ?>";
            if (message) {
                var messageClass = "<?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>";
                var messageElement = '<div class="message ' + messageClass + '">' + message + '</div>';
                document.body.insertAdjacentHTML('beforeend', messageElement);
                setTimeout(function() {
                    document.querySelector('.message').style.display = 'none';
                }, 3000);
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Add Grade</h2>
        <form action="add_grades.php" method="post">
            Student ID: <input type="number" name="student_id" required><br>
            Course ID: <input type="number" name="course_id" required><br>
            Grade: <input type="number" step="0.01" name="grade" required><br>
            <input type="submit" value="Add Grade">
        </form>
    </div>
</body>
</html>