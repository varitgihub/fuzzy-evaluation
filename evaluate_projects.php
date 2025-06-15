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

// Fetch projects
$projects_sql = "SELECT * FROM projects WHERE student_id = ?";
$stmt = $conn->prepare($projects_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$projects_result = $stmt->get_result();
$projects = [];
while ($row = $projects_result->fetch_assoc()) {
    $projects[] = $row;
}

$stmt->close();
$conn->close();

// Fuzzification functions
function fuzzifyProjectScore($score) {
    if ($score <= 50) return 'poor';
    if ($score <= 70) return 'average';
    if ($score <= 90) return 'good';
    return 'excellent';
}

// Evaluate the performance based on fuzzy logic
function evaluatePerformance($fuzzyScores) {
    $scores = [
        'poor' => 1,
        'average' => 2,
        'good' => 3,
        'excellent' => 4
    ];

    if (empty($fuzzyScores)) {
        return 'No projects';
    }

    $scoreValues = array_map(function($fs) use ($scores) { return $scores[$fs]; }, $fuzzyScores);
    $averageScore = array_sum($scoreValues) / count($scoreValues);

    // Convert score to performance category
    if ($averageScore <= 1.5) return 'poor';
    if ($averageScore <= 2.5) return 'average';
    if ($averageScore <= 3.5) return 'good';
    return 'excellent';
}
// Fuzzify the input data
$fuzzyScores = array_map('fuzzifyProjectScore', array_column($projects, 'project_grade'));

// Evaluate the performance based on fuzzy logic
$performance = evaluatePerformance($fuzzyScores);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Projects</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .student-info {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }
        .performance {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #007bff;
        }
        .projects {
            margin-top: 20px;
        }
        .project-item {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .project-name {
            font-weight: bold;
            color: #333;
        }
        .project-score {
            margin-top: 5px;
            color: #6c757d;
        }
        .project-performance {
            font-style: italic;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Projects</h2>
        
        <div class="student-info">
            <p><strong>Student:</strong> <?php echo $student['name']; ?></p>
        </div>
        
        <div class="performance">
            <p>Performance: <?php echo $performance; ?></p>
        </div>
        
        <div class="projects">
            <h3>Projects:</h3>
            <?php foreach ($projects as $project): ?>
                <div class="project-item">
                    <p class="project-name"><?php echo $project['project_name']; ?></p>
                    <p class="project-score"><?php echo $project['project_grade']; ?></p>
                    <p class="project-performance">(<?php echo fuzzifyProjectScore($project['project_grade']); ?>)</p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>