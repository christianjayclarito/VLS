<?php include 'database.php';  // Your connection file

$book = $_POST['book_title'];
$student_id = $_POST['student_id'];

$sql = "INSERT INTO borrowed_logs (student_id, book_title) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $student_id, $book);
if ($stmt->execute()) {
  echo "Borrow request sent!";
} else {
  echo "Error: " . $stmt->error;
}
?>
