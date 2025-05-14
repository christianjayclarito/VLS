<?php
include 'database.php'; // database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $student_id = isset($_POST['student_id']) ? $_POST['student_id'] : NULL;

    // Check USM email
    if (!str_ends_with($email, '@usm.edu.ph')) {
        echo "<script>alert('Registration failed: Please use a USM Email (@usm.edu.ph) only.'); window.location.href='register.html';</script>";
        exit();
    }

    // Build correct SQL
    if ($role == 'student') {
        $sql = "INSERT INTO users (role, name, email, password, student_id) 
                VALUES ('$role', '$name', '$email', '$password', '$student_id')";
    } else { // librarian no student_id
        $sql = "INSERT INTO users (role, name, email, password) 
                VALUES ('$role', '$name', '$email', '$password')";
    }

    // Execute SQL
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Account created successfully! You can now log in.'); window.location.href='login.html';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Account</title>
  <link rel="stylesheet" href="login.css" />
</head>
<body>
  <div class="left-panel"></div>

  <div class="right-panel">
    <div class="login-container">
      <img src="logo2.png" alt="Logo" class="logo" />
      <h2>Create Account</h2>

      <form action="register.php" method="POST" onsubmit="return validateEmail()">
        <div class="form-group">
          <label for="role">Role</label>
          <select name="role" id="role" required onchange="toggleStudentIdField()">
            <option value="">-- Choose Role --</option>
            <option value="student">Student</option>
            <option value="librarian">Librarian</option>
          </select>
        </div>
      
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" name="name" id="name" placeholder="Enter full name" required />
        </div>
      
        <div class="form-group" id="studentIdField">
          <label for="student_id">Student ID</label>
          <input type="text" name="student_id" id="student_id" placeholder="Enter student ID" />
        </div>
      
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" placeholder="Use USM Email only!" required />
        </div>
      
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" placeholder="Create a password" required />
        </div>
      
        <button type="submit" class="login-btn" style="background-color: #4CAF50;">Register</button>
      </form>
      
      <script>
      function validateEmail() {
        var email = document.getElementById('email').value;
        if (!email.endsWith("@usm.edu.ph")) {
          alert("Please use a valid USM Email address (@usm.edu.ph only).");
          return false;
        }
        return true;
      }
      
      function toggleStudentIdField() {
        var role = document.getElementById('role').value;
        var studentIdField = document.getElementById('studentIdField');
        if (role === 'student') {
          studentIdField.style.display = 'block';
          document.getElementById('student_id').required = true;
        } else {
          studentIdField.style.display = 'none';
          document.getElementById('student_id').required = false;
        }
      }
      </script>
      

      <p style="margin-top: 20px; text-align: center;">
        Already have an account? 
        <a href="login.html" style="color: #4CAF50; text-decoration: none;">Login here</a>
      </p>
    </div>
  </div>

  <script>
    function validateEmail() {
      var email = document.getElementById('email').value;
      if (!email.endsWith("@usm.edu.ph")) {
        alert("Please use a valid USM Email address (@usm.edu.ph only).");
        return false; // Stop the form from submitting
      }
      return true;
    }
  </script>
</body>
</html>
