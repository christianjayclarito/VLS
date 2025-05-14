<?php 

// Start session and connect to the database
session_start();

include 'database.php';

$host = "localhost";
$user = "root";
$password = "";
$dbname = "virtual_library_system";

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get student name from session or set default
$librarianName = isset($_SESSION['librarian_name']) ? $_SESSION['librarian_name'] : "Librarian";

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Librarian Dashboard</title>
  <link rel="stylesheet" href="librarian.css" />
</head>
<body>
<header>
  <div class="logo">
    <img src="usm seal.png" alt="USM Logo">
    <h1>Virtual Library System</h1>
  </div>
  <nav>
    <a href="javascript:void(0)" onclick="showSection('dashboard')">Dashboard</a>
    <a href="javascript:void(0)" onclick="showSection('manage-books')">Manage Books</a>
    <a href="javascript:void(0)" onclick="showSection('borrowed-logs')">Borrowed Logs</a>
     
    <a href="javascript:void(0)" onclick="logout()" class="logout-btn">Logout</a>
  </nav>
</header>

  <main>
    <section id="dashboard" class="section active">
      <h2>📊 Dashboard Overview</h2>
      <div class="dashboard-cards">
        <div class="card"><h3>Total Books</h3><p></p></div>
        <div class="card"><h3>Borrowed Today</h3><p></p></div>
        <div class="card"><h3>Active Users</h3><p></p></div>
      </div>
    </section>

    <section id="manage-books" class="section">
      <h2>📚 Manage Books</h2>
      <input type="text" id="searchInput" placeholder="Search by title..." onkeyup="filterBooks()" style="margin-bottom: 10px; padding: 8px; width: 300px; font-size:18px; border-radius: 6px; ">

      <table>
        
       <thead>
  <tr>
    <th>Book Title</th>
    <th>No. of Copies</th>
    <th>Status</th>
  </tr>
</thead>

     <tbody>
  <?php
  $sql = "SELECT title, number_of_copies FROM books";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          $status = ($row['number_of_copies'] > 0) ? "Available" : "Not Available";
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row['title']) . "</td>";
          echo "<td>" . htmlspecialchars($row['number_of_copies']) . "</td>";
          echo "<td>" . $status . "</td>";
          echo "</tr>";
      }
  } else {
      echo "<tr><td colspan='3'>No books found.</td></tr>";
  }
  ?>
</tbody>



      </table>
    </section>

    <section id="borrowed-logs" class="section">
      <h2>📄 Borrowed Logs</h2>
       
      <div id="borrowedLogs"></div>
<input type="text" id="searchStudentId" placeholder="Search by Student ID..." onkeyup="filterBorrowLogs()" style="margin-bottom: 10px; padding: 8px; width: 300px; font-size:18px; border-radius: 6px;">

      <table id="log-table">
       <thead>
  <tr>
    <th>Borrower (Student ID)</th>
    <th>Book Title</th>
    <th>Date</th>
    <th>Status</th>
  </tr>
</thead>

        <tbody></tbody>
      </table>
      
    </section>
    

  </main>

  <script>

    function filterBooks() {
  const input = document.getElementById("searchInput");
  const filter = input.value.toLowerCase();
  const table = document.querySelector("#manage-books table");
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    const titleCell = rows[i].getElementsByTagName("td")[0];
    if (titleCell) {
      const title = titleCell.textContent || titleCell.innerText;
      rows[i].style.display = title.toLowerCase().includes(filter) ? "" : "none";
    }
  }
}

function filterBorrowLogs() {
  const input = document.getElementById("searchStudentId");
  const filter = input.value.toLowerCase();
  const table = document.querySelector("#borrowed-logs table");
  const rows = table.getElementsByTagName("tr");

  for (let i = 1; i < rows.length; i++) {
    const studentCell = rows[i].getElementsByTagName("td")[0];
    if (studentCell) {
      const studentId = studentCell.textContent || studentCell.innerText;
      rows[i].style.display = studentId.toLowerCase().includes(filter) ? "" : "none";
    }
  }
}


    function showSection(sectionId) {
      document.querySelectorAll('.section').forEach(section =>
        section.classList.remove('active')
      );
      document.getElementById(sectionId).classList.add('active');
    
      if (sectionId === 'borrowed-logs') {
        fetchBorrowLogs();
      }
    }
    
    function logout() {
      if (confirm("Are you sure you want to logout?")) {
        window.location.href = "login.html";
      }
    }
    
    function editBook() {
      alert("Edit functionality coming soon!");
    }
    
    function deleteBook(button) {
      const row = button.closest("tr");
      row.remove();
      alert("Book deleted.");
    }
    
    // --- Real-time borrowed logs ---
  function fetchBorrowLogs() {
  fetch('getBorrowLogs.php')
    .then(res => res.json())
    .then(data => {
      const tbody = document.querySelector("#log-table tbody");
      tbody.innerHTML = "";

      data.forEach(row => {
        const status = row.status === 'returned' 
  ? `<span style="color:green;">✅ Returned</span>` 
  : `<span style="color:orange;">📕 Borrowed</span>`;


        tbody.innerHTML += `<tr>
          <td>${row.student_id}</td>
          <td>${row.book_title}</td>
          <td>${row.borrow_date}</td>
          <td>${status}</td>
        </tr>`;
      });
    });
}


    
    // --- Real-time dashboard stats ---
    function fetchStats() {
      fetch('getStats.php')
        .then(res => res.json())
        .then(stats => {
          document.querySelector(".card:nth-child(1) p").innerText = stats.total_books;
          document.querySelector(".card:nth-child(2) p").innerText = stats.borrowed_today;
          document.querySelector(".card:nth-child(3) p").innerText = stats.overdue_books;
          document.querySelector(".card:nth-child(4) p").innerText = stats.active_users;
        });
    }
    
    // Poll every 5 seconds
    setInterval(() => {
      fetchStats();
      fetchBorrowLogs();
    }, 5000);
    
    // Initial load
    window.onload = () => {
      fetchStats();
      fetchBorrowLogs();
    };
    </script>
    
</body>
</html>
