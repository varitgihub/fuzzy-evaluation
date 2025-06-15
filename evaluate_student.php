<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Evaluation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 600px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        p {
            font-size: 18px;
            color: #555;
        }
        .result {
            margin-top: 20px;
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50; /* Green color for good and excellent */
        }
        .error {
            color: #FF5733; /* Red color for error messages */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Evaluation</h2>
        <?php
        $conn = new mysqli('localhost', 'root', '', 'student_performance');

        if ($conn->connect_error) {
            echo '<p class="error">Connection failed: ' . $conn->connect_error . '</p>';
        } else {
            $student_id = $_GET['student_id'];

            $sql = "SELECT grade FROM grades WHERE student_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $grades = [];
            while ($row = $result->fetch_assoc()) {
                $grades[] = $row['grade'];
            }

            $stmt->close();
            $conn->close();

            // Function to fuzzify the grade
            function fuzzifyGrade($grade) {
                if ($grade <= 50) return 'poor';
                if ($grade <= 70) return 'average';
                if ($grade <= 90) return 'good';
                return 'excellent';
            }

            // Function to evaluate performance based on fuzzy logic
            function evaluatePerformance($grades) {
                global $student_id;

                if (empty($grades)) {
                    return "<span class='error'>No grades found for student ID $student_id</span>";
                }

                $fuzzyScores = array_map('fuzzifyGrade', $grades);
                $averageGrade = array_sum($grades) / count($grades);

                $scores = [
                    'poor' => 1,
                    'average' => 2,
                    'good' => 3,
                    'excellent' => 4
                ];

                $gradeValues = array_map(function($fs) use ($scores) {
                    return $scores[$fs];
                }, $fuzzyScores);

                $averageScore = array_sum($gradeValues) / count($gradeValues);

                if ($averageScore <= 1.5) return 'poor';
                if ($averageScore <= 2.5) return 'average';
                if ($averageScore <= 3.5) return 'good';
                return 'excellent';
            }

            // Evaluate performance
            $performance = evaluatePerformance($grades);

            echo "<p>Evaluation for student ID <strong>$student_id</strong>:</p>";
            echo "<p class='result'>$performance</p>";
        }
        ?>
    </div>
</body>
</html>