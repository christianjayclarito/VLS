<?php
include 'database.php';

$sql = "SELECT book_title, student_id, borrow_date, return_date FROM borrowed_books ORDER BY borrow_date DESC";
$result = $conn->query($sql);

$logs = [];
while ($row = $result->fetch_assoc()) {
    // If return_date is NULL, it's still borrowed, so set a placeholder value
    if ($row['return_date'] === NULL) {
        $row['return_date'] = "Not returned yet";
    }
    $logs[] = $row;
}


echo json_encode($logs);
?>
