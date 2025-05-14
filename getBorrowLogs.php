<?php
include 'database.php';
header('Content-Type: application/json');

$sql = "SELECT book_title, student_id, borrow_date, return_date, status FROM borrowed_logs ORDER BY borrow_date DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(["error" => mysqli_error($conn)]);
    exit;
}

$logs = [];

while ($row = mysqli_fetch_assoc($result)) {
    $logs[] = [
        "book_title" => $row['book_title'],
        "student_id" => $row['student_id'],
        "borrow_date" => $row['borrow_date'],
        "return_date" => $row['return_date'],
        "status" => $row['status']
    ];
}

echo json_encode($logs);
?>
