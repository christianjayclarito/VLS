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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_title'])) {
    $book_title = $_POST['book_title'];

    // Step 1: Check latest borrow record in borrowed_logs
    $check_sql = "SELECT * FROM borrowed_logs 
                  WHERE student_id = ? AND book_title = ? 
                  ORDER BY id DESC LIMIT 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $student_id, $book_title);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $borrow = $result->fetch_assoc();

        if (strtolower($borrow['status']) === 'returned') {
            echo json_encode(['status' => 'already_returned', 'message' => 'This book has already been returned.']);
        } else {
            // Step 2: Update borrowed_logs
            $update_logs_sql = "UPDATE borrowed_logs 
                                SET return_date = CURRENT_DATE, status = 'returned' 
                                WHERE id = ?";
            $update_logs_stmt = $conn->prepare($update_logs_sql);
            $update_logs_stmt->bind_param("i", $borrow['id']);

            if ($update_logs_stmt->execute()) {
                // Step 3: Update borrowed_books
                $update_bb_sql = "UPDATE borrowed_books 
                                  SET status = 'returned', return_date = CURRENT_DATE 
                                  WHERE student_id = ? AND book_title = ? AND LOWER(status) = 'borrowed'";
                $update_bb_stmt = $conn->prepare($update_bb_sql);
                $update_bb_stmt->bind_param("ss", $student_id, $book_title);
                $update_bb_stmt->execute();

                // Check if borrowed_books row was updated
                if ($update_bb_stmt->affected_rows > 0) {
                    // Step 4: Update books table
                    $book_update_stmt = $conn->prepare("UPDATE books SET number_of_copies = number_of_copies + 1 WHERE title = ?");
                    $book_update_stmt->bind_param("s", $book_title);
                    $book_update_stmt->execute();
                    $book_update_stmt->close();

                    echo json_encode(['status' => 'success', 'message' => 'Book successfully returned.']);
                } else {
                    echo json_encode([
                        'status' => 'warning',
                        'message' => 'Book returned in logs, but not found or not updated in borrowed_books.',
                        'debug' => [
                            'student_id' => $student_id,
                            'book_title' => $book_title
                        ]
                    ]);
                }

                $update_bb_stmt->close();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update return status in borrowed_logs.']);
            }

            $update_logs_stmt->close();
        }
    } else {
        echo json_encode(['status' => 'not_found', 'message' => 'Borrowed record not found.']);
    }

    $check_stmt->close();
} else {
    echo json_encode(['status' => 'invalid', 'message' => 'Invalid request.']);
}

$conn->close();
?>
