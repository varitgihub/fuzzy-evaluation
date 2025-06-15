<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Performance Evaluation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        .grades, .projects, .attendance {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .grades h3, .projects h3, .attendance h3 {
            margin-bottom: 10px;
            color: #555;
        }
        .grades div, .projects div {
            background: #eaeaea;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 5px;
        }
        .attendance {
            background-color: #f0f0f0;
        }
        .projects {
            background-color: #f5f5f5;
        }
        .performance {
            text-align: center;
            font-size: 1.5em;
            padding: 10px;
            border-radius: 4px;
            margin-top: 20px;
        }
        .poor { background-color: #ffcccc; color: #cc0000; }
        .average { background-color: #fff4cc; color: #e68a00; }
        .good { background-color: #d9ead3; color: #38761d; }
        .excellent { background-color: #cceeff; color: #006699; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var performanceElement = document.querySelector('.performance');
            if (performanceElement) {
                var performance = performanceElement.textContent.trim();
                performanceElement.classList.add(performance.toLowerCase());
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Student Performance Evaluation</h1>
        <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Database connection parameters
        $servername = 'localhost';
        $username = 'root';
        $password = ''; // Change if you have set a MySQL password
        $dbname = 'student_performance';

        // Create a database connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }

        // Assume student_id is received from user input, for example from a form or URL parameter
        if (isset($_GET['student_id'])) {
            $student_id = $_GET['student_id']; // Sanitize and validate as needed
        } else {
            die('Student ID is required.');
        }

        // Fetch student information
        $student_sql = "SELECT * FROM students WHERE id = ?";
        $stmt = $conn->prepare($student_sql);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $student_result = $stmt->get_result();
        $student = $student_result->fetch_assoc();

        if (!$student) {
            die('Student not found.');
        }

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

        // Fetch attendance
        $attendance_sql = "SELECT * FROM attendance WHERE student_id = ?";
        $stmt = $conn->prepare($attendance_sql);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $attendance_result = $stmt->get_result();
        $attendance = $attendance_result->fetch_assoc();

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
        function fuzzifyGrade($grade) {
            if ($grade <= 50) return 'poor';
            if ($grade <= 70) return 'average';
            if ($grade <= 90) return 'good';
            return 'excellent';
        }

        function fuzzifyAttendance($attendance) {
            $percentage = ($attendance['attended_classes'] / $attendance['total_classes']) * 100;
            if ($percentage <= 50) return 'poor';
            if ($percentage <= 70) return 'average';
            if ($percentage <= 90) return 'good';
            return 'excellent';
        }

        function fuzzifyProject($projectGrade) {
            if ($projectGrade <= 50) return 'poor';
            if ($projectGrade <= 70) return 'average';
            if ($projectGrade <= 90) return 'good';
            return 'excellent';
        }

        // Example fuzzy rules
        function evaluatePerformance($fuzzyGrades, $fuzzyAttendance, $fuzzyProjects) {
            $scores = [
                'poor' => 1,
                'average' => 2,
                'good' => 3,
                'excellent' => 4
            ];

            $gradeScores = array_map(function($fg) use ($scores) { return $scores[$fg]; }, $fuzzyGrades);
            $averageGradeScore = count($gradeScores) ? array_sum($gradeScores) / count($gradeScores) : 0;

            $attendanceScore = $scores[$fuzzyAttendance];
            $projectScores = array_map(function($fp) use ($scores) { return $scores[$fp]; }, $fuzzyProjects);
            $averageProjectScore = count($projectScores) ? array_sum($projectScores) / count($projectScores) : 0;

            $overallScore = ($averageGradeScore + $attendanceScore + $averageProjectScore) / 3;

            // Convert score to performance category
            if ($overallScore <= 1.5) return 'poor';
            if ($overallScore <= 2.5) return 'average';
            if ($overallScore <= 3.5) return 'good';
            return 'excellent';
        }

        // Fuzzify the input data
        $fuzzyGrades = array_map('fuzzifyGrade', array_column($grades, 'grade'));
        $fuzzyAttendance = fuzzifyAttendance($attendance);
        $fuzzyProjects = array_map(function($project) { return fuzzifyProject($project['project_grade']); }, $projects);

        // Evaluate the performance based on fuzzy logic
        $performance = evaluatePerformance($fuzzyGrades, $fuzzyAttendance, $fuzzyProjects);

        // Output the result
        echo "<h2>Student: " . htmlspecialchars($student['name']) . "</h2>";
        echo "<div class='grades'><h3>Grades:</h3>";
        foreach ($grades as $grade) {
            echo "<div>" . htmlspecialchars($grade['course_name']) . ": " . htmlspecialchars($grade['grade']) . "</div>";
        }
        echo "</div>";

        $attendancePercentage = round(($attendance['attended_classes'] / $attendance['total_classes']) * 100, 2);
        echo "<div class='attendance'><h3>Attendance: " . $attendancePercentage . "%</h3></div>";

        echo "<div class='projects'><h3>Projects:</h3>";
        foreach ($projects as $project) {
            echo "<div>Project: " . htmlspecialchars($project['project_name']) . " - Grade: " . htmlspecialchars($project['project_grade']) . "</div>";
        }
        echo "</div>";

        echo "<div class='performance " . strtolower($performance) . "'>" . $performance . "</div>";
        ?>
    </div>
</body>
</html>