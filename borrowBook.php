<?php
session_start();
include 'database.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['student_id'])) {
    die("User not logged in.");
}

$student_id = $_SESSION['student_id'];

if (!isset($_POST['book_title']) || empty($_POST['book_title'])) {
    die("Missing required data.");
}

$book_title = $_POST['book_title'];
$borrow_date = date("Y-m-d");
$return_date = null;

// Check if copies are available
$check_stmt = $conn->prepare("SELECT number_of_copies FROM books WHERE title = ?");
$check_stmt->bind_param("s", $book_title);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    die("Book not found.");
}

$row = $result->fetch_assoc();
if ($row['number_of_copies'] < 1) {
    die("No copies available.");
}

// Insert into borrowed_logs
$stmt_logs = $conn->prepare("INSERT INTO borrowed_logs (book_title, student_id, borrow_date, return_date) VALUES (?, ?, ?, ?)");
$stmt_logs->bind_param("ssss", $book_title, $student_id, $borrow_date, $return_date);
$stmt_logs->execute();
$stmt_logs->close();

// Insert into borrowed_books
$stmt_books = $conn->prepare("INSERT INTO borrowed_books (book_title, student_id, borrow_date, return_date) VALUES (?, ?, ?, ?)");
$stmt_books->bind_param("ssss", $book_title, $student_id, $borrow_date, $return_date);
$stmt_books->execute();
$stmt_books->close();

// Decrease number of copies
$update_stmt = $conn->prepare("UPDATE books SET number_of_copies = number_of_copies - 1 WHERE title = ?");
$update_stmt->bind_param("s", $book_title);
$update_stmt->execute();
$update_stmt->close();

echo "Book borrowed successfully!";
$conn->close();

?>

