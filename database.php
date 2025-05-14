<?php
$host = "localhost";
$user = "root"; // default username
$pass = "";     // default has no password
$dbname = "virtual_library_system"; // name of your database

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>