<!DOCTYPE html>
<html>
<head>
    <title>Update Student Name and Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0 10px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            margin: 10px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 0.9em;
            display: none;
        }
    </style>
    <script>
        function validateForm() {
            let valid = true;
            const inputs = document.querySelectorAll('input[type="text"], input[type="email"]');
            inputs.forEach(input => {
                if (input.value === '') {
                    input.nextElementSibling.style.display = 'block';
                    valid = false;
                } else {
                    input.nextElementSibling.style.display = 'none';
                }
            });
            return valid;
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Update Student Name and Email</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateForm()">
        <label for="id">Student ID:</label>
        <input type="number" id="id" name="id" required>
        
        <label for="name">New Name:</label>
        <input type="text" id="name" name="name" required>
        <span class="error">This field is required</span>
        
        <label for="email">New Email:</label>
        <input type="email" id="email" name="email" required>
        <span class="error">This field is required</span>
        
        <input type="submit" value="Update Name and Email">
    </form>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "student_performance";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $email = $_POST['email'];

    if (!empty($name) && !empty($email)) {
        $stmt = $conn->prepare("UPDATE students SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $id);

        if ($stmt->execute()) {
            echo '<p style="text-align: center; margin-top: 20px;">Student name and email updated successfully.</p>';
        } else {
            echo '<p style="text-align: center; color: red; margin-top: 20px;">Error updating student: ' . $stmt->error . '</p>';
        }

        $stmt->close();
    } else {
        echo '<p style="text-align: center; color: red; margin-top: 20px;">Name and email are required fields.</p>';
    }

    $conn->close();
}
?>

</body>
</html>