<?php
session_start();
include 'database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(["error" => "Student not logged in"]);
    exit;
}

$student_id = $_SESSION['student_id'];

$sql = "SELECT book_title, borrow_date, return_date FROM borrowed_books WHERE student_id = ? ORDER BY borrow_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$borrowedBooks = [];
$returnedBooks = [];

while ($row = $result->fetch_assoc()) {
    if ($row['return_date'] === null) {
        // Currently borrowed
        $borrowedBooks[] = [
            "book_title" => $row['book_title'],
            "borrow_date" => $row['borrow_date']
        ];
    } else {
        // Returned
        $returnedBooks[] = [
            "book_title" => $row['book_title'],
            "borrow_date" => $row['borrow_date'],
            "return_date" => $row['return_date']
        ];
    }
}

echo json_encode([
    "borrowedBooks" => $borrowedBooks,
    "returnedBooks" => $returnedBooks
]);
?>
