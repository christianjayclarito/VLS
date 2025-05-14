<?php
session_start();
include 'database.php';

$host = "localhost";
$user = "root";
$password = "";
$dbname = "virtual_library_system";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['status' => 'unauthorized', 'message' => 'Unauthorized access.']);
    exit;
}

$student_id = $_SESSION['student_id'];

// Fetch returned books
$sql = "SELECT * FROM borrowed_books WHERE student_id = ? AND status = 'returned'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$returned_books = [];
while ($row = $result->fetch_assoc()) {
    $returned_books[] = $row;
}

if (!empty($returned_books)) {
    echo json_encode(['status' => 'success', 'returned_books' => $returned_books]);
} else {
    echo json_encode(['status' => 'empty', 'message' => 'No returned books found.']);
}

$stmt->close();
$conn->close();
?>
