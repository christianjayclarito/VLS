<?php
include 'database.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND role='$role'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        if ($password == $row['password']) {
            $_SESSION['student_id'] = $row['student_id']; // store the user id in session
            $_SESSION['role'] = $role;

            if ($role == "student") {
                header("Location: stud.php");
                exit();
            } else if ($role == "librarian") {
                header("Location: librarian.php");
                exit();
            }
        } else {
            echo "<script>alert('Invalid password!'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('Invalid login credentials!'); window.location.href='login.html';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login & Register</title>
  <link rel="stylesheet" href="login.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

</head>
<body>
  <div class="left-panel"></div>

  <div class="right-panel">
    <div class="login-container">
      <img src="logo2.png" alt="Logo" class="logo" />
      <h2>Virtual Library System</h2>

      <!-- Login Form -->
      <form action="login.php" method="POST">
        <h3>Login</h3>
        <div class="form-group">
          <label for="role">Select Role</label>
          <select name="role" id="role" required>
            <option value="">-- Choose Role --</option>
            <option value="student">Student</option>
            <option value="librarian">Librarian</option>
          </select>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" placeholder="Use USM Email only!" required />
        </div>

        <div class="form-group" style="position: relative;">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" placeholder="Enter your password" required />
          <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 10px; top: 38px; cursor: pointer;"></i>
        </div>
        
        <script>
          const togglePassword = document.getElementById("togglePassword");
          const passwordField = document.getElementById("password");
        
          togglePassword.addEventListener("click", function () {
            const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
            passwordField.setAttribute("type", type);
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
          });
        </script>
        
        <button type="submit" class="login-btn">Login</button>
      </form>

      <hr style="margin: 30px 0;">

      <!-- Register Form -->
      <!-- After your login form -->
<p style="margin-top: 20px; text-align: center;">
  Don't have an account? 
  <a href="register.html" style="color: #4CAF50; text-decoration: none;">Create one here</a>
</p>


    </div>
  </div>
</body>
</html>

