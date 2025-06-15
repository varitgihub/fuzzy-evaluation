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

// Fetch grades
$grades_sql = "SELECT courses.course_name, grades.grade 
               FROM grades 
               JOIN courses ON grades.course_id = courses.id 
               WHERE grades.student_id = ?";
$stmt = $conn->prepare($grades_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$grades_result = $stmt->get_result();
$grades = [];
while ($row = $grades_result->fetch_assoc()) {
    $grades[] = $row;
}

$stmt->close();
$conn->close();

// Fuzzification functions
function fuzzifyGrade($grade) {
    if ($grade <= 50) return 'poor';
    if ($grade <= 70) return 'average';
    if ($grade <= 90) return 'good';
    return 'excellent';
}

// Example fuzzy rules
function evaluatePerformance($fuzzyGrades) {
    $scores = [
        'poor' => 1,
        'average' => 2,
        'good' => 3,
        'excellent' => 4
    ];

    $gradeScores = array_map(function($fg) use ($scores) { return $scores[$fg]; }, $fuzzyGrades);
    $averageGradeScore = array_sum($gradeScores) / count($gradeScores);

    // Convert score to performance category
    if ($averageGradeScore <= 1.5) return 'poor';
    if ($averageGradeScore <= 2.5) return 'average';
    if ($averageGradeScore <= 3.5) return 'good';
    return 'excellent';
}

// Fuzzify the input data
$fuzzyGrades = array_map('fuzzifyGrade', array_column($grades, 'grade'));

// Evaluate the performance based on fuzzy logic
$performance = evaluatePerformance($fuzzyGrades);
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
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #333;
        }
        p {
            font-size: 18px;
            color: #555;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background: #f9f9f9;
            margin: 5px 0;
            padding: 10px;
            border-radius: 4px;
        }
        li:nth-child(odd) {
            background: #efefef;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Performance</h2>
        <p>Student: <strong><?php echo htmlspecialchars($student['name']); ?></strong></p>
        <p>Performance: <strong><?php echo htmlspecialchars($performance); ?></strong></p>
        <h3>Grades:</h3>
        <ul>
            <?php foreach ($grades as $grade): ?>
                <li><?php echo htmlspecialchars($grade['course_name']) . ": " . htmlspecialchars($grade['grade']) . " (" . htmlspecialchars(fuzzifyGrade($grade['grade'])) . ")"; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>