<!DOCTYPE html>
<html>
<head>
    <title>Admin - Student Performance Evaluation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .tab {
            overflow: hidden;
            border-bottom: 1px solid #ccc;
        }
        .tab button {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            transition: 0.3s;
            font-size: 17px;
        }
        .tab button:hover {
            background-color: #ddd;
        }
        .tab button.active {
            background-color: #ccc;
        }
        .tabcontent {
            display: none;
            padding: 20px;
            border: 1px solid #ccc;
            border-top: none;
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
        input[type="text"], input[type="email"], input[type="number"] {
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
        function validateForm(form) {
            let valid = true;
            const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="number"]');
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

        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        window.onload = function() {
            // Open the first tab by default
            document.getElementsByClassName('tablinks')[0].click();
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Student Performance Evaluation Admin</h2>

    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'AddStudent')">Add Student</button>
        <button class="tablinks" onclick="openTab(event, 'UpdateStudent')">Update Student</button>
        <button class="tablinks" onclick="openTab(event, 'DeleteStudent')">Delete Student</button>
    </div>

    <div id="AddStudent" class="tabcontent">
        <h3>Add Student</h3>
        <form action="add_student.php" method="post" onsubmit="return validateForm(this)">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name">
            <span class="error">This field is required</span>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
            <span class="error">This field is required</span>

            <input type="submit" value="Add Student">
        </form>
    </div>

    <div id="UpdateStudent" class="tabcontent">
        <h3>Update Student</h3>
        <form action="update_student.php" method="post" onsubmit="return validateForm(this)">
            <label for="id">ID:</label>
            <input type="number" id="id" name="id" min="1">
            <span class="error">This field is required</span>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name">
            <span class="error">This field is required</span>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
            <span class="error">This field is required</span>

            <input type="submit" value="Update Student">
        </form>
    </div>

    <div id="DeleteStudent" class="tabcontent">
        <h3>Delete Student</h3>
        <form action="delete_student.php" method="post" onsubmit="return validateForm(this)">
            <label for="id">ID:</label>
            <input type="number" id="id" name="id" min="1">
            <span class="error">This field is required</span>

            <input type="submit" value="Delete Student">
        </form>
    </div>
</div>

</body>
</html>