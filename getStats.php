<?php
include 'database.php';

// Total books
$totalBooksQuery = $conn->query("SELECT COUNT(*) AS total FROM books");
$totalBooks = $totalBooksQuery->fetch_assoc()['total'];

// Borrowed today
$today = date("Y-m-d");
$borrowedTodayQuery = $conn->query("SELECT COUNT(*) AS total FROM borrowed_logs WHERE borrow_date = '$today'");
$borrowedToday = $borrowedTodayQuery->fetch_assoc()['total'];

// Overdue books
$overdueQuery = $conn->query("SELECT COUNT(*) AS total FROM borrowed_logs WHERE return_date < CURDATE()");
$overdue = $overdueQuery->fetch_assoc()['total'];

// Active users
$activeUsersQuery = $conn->query("SELECT COUNT(DISTINCT student_id) AS total FROM borrowed_logs WHERE borrow_date = '$today'");
$activeUsers = $activeUsersQuery->fetch_assoc()['total'];

echo json_encode([
    "total_books" => $totalBooks,
    "borrowed_today" => $borrowedToday,
    "overdue_books" => $overdue,
    "active_users" => $activeUsers
]);

$conn->close();
?>
