<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = 'localhost';
$username = 'root';
$password = ''; // Change if you have set a MySQL password
$dbname = 'student_performance';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$student_id = 1; // Example student ID

// Fetch student information
$student_sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($student_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student_result = $stmt->get_result();
$student = $student_result->fetch_assoc();

// Fetch attendance
$attendance_sql = "SELECT * FROM attendance WHERE student_id = ?";
$stmt = $conn->prepare($attendance_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$attendance_result = $stmt->get_result();
$attendance = $attendance_result->fetch_assoc();

$stmt->close();
$conn->close();

// Fuzzification functions
function fuzzifyAttendance($attendance) {
    $percentage = ($attendance['attended_classes'] / $attendance['total_classes']) * 100;
    if ($percentage <= 50) return 'poor';
    if ($percentage <= 70) return 'average';
    if ($percentage <= 90) return 'good';
    return 'excellent';
}

// Example fuzzy rules
function evaluatePerformance($fuzzyAttendance) {
    $scores = [
        'poor' => 1,
        'average' => 2,
        'good' => 3,
        'excellent' => 4
    ];

    $attendanceScore = $scores[$fuzzyAttendance];

    // Convert score to performance category
    if ($attendanceScore <= 1.5) return 'poor';
    if ($attendanceScore <= 2.5) return 'average';
    if ($attendanceScore <= 3.5) return 'good';
    return 'excellent';
}

// Fuzzify the input data
$fuzzyAttendance = fuzzifyAttendance($attendance);

// Evaluate the performance based on fuzzy logic
$performance = evaluatePerformance($fuzzyAttendance);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Performance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
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
        .result {
            margin-top: 20px;
            padding: 10px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .performance {
            font-weight: bold;
            color: #007bff;
        }
        .attendance-info {
            margin-top: 10px;
        }
        .attendance-info table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .attendance-info th, .attendance-info td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .attendance-info th {
            background-color: #f2f2f2;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Performance</h2>
        <div class="result">
            <p>Student: <?php echo $student['name']; ?></p>
            <p>Performance: <span class="performance"><?php echo $performance; ?></span></p>
        </div>
        <div class="attendance-info">
            <h3>Attendance Information</h3>
            <table>
                <tr>
                    <th>Total Classes</th>
                    <td><?php echo $attendance['total_classes']; ?></td>
                </tr>
                <tr>
                    <th>Attended Classes</th>
                    <td><?php echo $attendance['attended_classes']; ?></td>
                </tr>
                <tr>
                    <th>Attendance Percentage</th>
                    <td><?php echo ($attendance['attended_classes'] / $attendance['total_classes']) * 100 . "% (" . fuzzifyAttendance($attendance) . ")"; ?></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>