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

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    // Redirect to login page if no session exists
    header("Location: login.html");
    exit();
}

$student_id = $_SESSION['student_id'];

// Get student name from the database
$sql = "SELECT * FROM users WHERE student_id = '$student_id'";
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);
$studentName = $student ? $student['name'] : "Student";

// Fetch borrowed books by this student
$sql_books = "SELECT * FROM borrowed_books WHERE student_id = '$student_id' ORDER BY borrow_date DESC";

$result_books = mysqli_query($conn, $sql_books);
$borrowed_books = mysqli_fetch_all($result_books, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Virtual Library - USM Theme</title>
  <link rel="stylesheet" href="stud.css">

  <style>
    /* (your modal CSS) */
    .modal-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.4);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      font-family: Arial, sans-serif;
    }

    .modal {
      background: #fff;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
      text-align: center;
      max-width: 400px;
      width: 90%;
    }

    .modal-message {
      font-size: 1rem;
      margin-bottom: 20px;
      color: #333;
    }

    .modal button {
      padding: 10px 20px;
      margin: 0 10px;
      border-radius: 6px;
      font-size: 0.95rem;
      font-weight: bold;
      border: none;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .modal .confirm { background-color: #00a600; color: #fff; }
    .modal .confirm:hover { background-color: #0056b3; }
    .modal .cancel { background-color: #6c757d; color: #fff; }
    .modal .cancel:hover { background-color: #5a6268; }
  </style>

  <script>
    function showSection(id) {
      document.querySelectorAll("main section").forEach(sec => sec.style.display = 'none');
      document.getElementById(id).style.display = 'block';
    }

    function confirmBorrow(bookTitle) {
      const modal = document.getElementById("confirmationModal");
      modal.querySelector(".modal-message").textContent = `Do you want to borrow "${bookTitle}"?`;
      modal.style.display = "flex";

      const confirmBtn = modal.querySelector(".confirm");
      const cancelBtn = modal.querySelector(".cancel");

      confirmBtn.onclick = function () {
        modal.style.display = "none";
        borrowBook(bookTitle);
      };

      cancelBtn.onclick = function () {
        modal.style.display = "none";
      };
    }

    function borrowBook(bookTitle) {
      fetch("borrowBook.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `book_title=${encodeURIComponent(bookTitle)}`
      })
      .then(response => response.text())
      .then(data => {
        alert(data);

        if (data.toLowerCase().includes("success")) {
          addBookToMyBooks(bookTitle);
        }
      })
      .catch(error => {
        console.error('Error borrowing book:', error);
      });
    }
    
    function addBookToMyBooks(bookTitle) {
        const myBooksSection = document.querySelector("#my-books .book-list");

        const imageMap = {
            "Introduction to Information Systems": "book1.jpg",
            "Database Management Systems": "book2.jpg",
            "Business Information Systems": "book3.jpg",
            "Data Communications and Networking": "book4.jpg",
            "Systems Analysis and Design": "book5.jpg",
            "Enterprise Resource Planning": "book6.jpg",
            "E-Commerce: Business, Technology, Society": "book7.jpg",
            "Information Technology for Management": "book8.jpg",
            "Digital Transformation: Survive and Thrive in an Era of Mass Extinction": "book9.jpg",
            "Cloud Computing: Concepts, Technology & Architecture": "book10.jpg",
            "Information Systems: A Manager’s Guide to Harnessing Technology": "book11.jpg",
            "Management Information Systems": "book12.jpg",
            "IT Project Management: On Track from Start to Finish": "book13.jpg",
            "Data Science for Business": "book14.jpg",
            "Business Intelligence: A Managerial Perspective on Analytics": "book15.jpg",
            "Cybersecurity and Cyberwar: What Everyone Needs to Know": "book16.jpg",
            "Introduction to the Theory of Computation": "book17.jpg",
            "Computer Networks: A Systems Approach": "book18.jpg",
            "Information Systems Development: Methods, Techniques, and Tools": "book19.jpg",
            "The Big Data-Driven Business": "book20.jpg",
            
            
            
            
            
        };

        const imageFilename = imageMap[bookTitle] || "placeholder.jpg";

        const newBookDiv = document.createElement("div");
        newBookDiv.classList.add("book");

        newBookDiv.innerHTML = `
            <img src="${imageFilename}" alt="Book Cover" style="width: 100px; height: 150px;">
            <div class="book-title">${bookTitle}</div>
            <button onclick="confirmReturn('${bookTitle}', this)">Return</button>
        `;

        myBooksSection.appendChild(newBookDiv);
    }



    function searchBooks() {
      const query = document.getElementById("search-input").value.toLowerCase();
      const books = document.querySelectorAll(".book");
      books.forEach(book => {
        const title = book.querySelector(".book-title").textContent.toLowerCase();
        book.style.display = title.includes(query) ? "block" : "none";
      });
    }

    function logout() {
      if (confirm("Are you sure you want to logout?")) {
        window.location.href = "login.html";
      }
    }

    window.onload = function () {
  showSection('featured');

  fetch("getBorrowedBook.php")
  .then(res => res.json())
  .then(data => {
    if (Array.isArray(data)) {
      data.forEach(entry => {
        addBookToMyBooks(entry.book_title);
      });
    }
  });

};


    function confirmReturn(bookTitle, buttonElement) {
      const modal = document.getElementById("confirmationModal");
      modal.querySelector(".modal-message").textContent = `Do you want to return "${bookTitle}"?`;
      modal.style.display = "flex";

      const confirmBtn = modal.querySelector(".confirm");
      const cancelBtn = modal.querySelector(".cancel");

      confirmBtn.onclick = function () {
        modal.style.display = "none";
        returnBook(bookTitle, buttonElement);
      };

      cancelBtn.onclick = function () {
        modal.style.display = "none";
      };
    }

    function returnBook(bookTitle, buttonElement) {
      fetch("returnBook.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `book_title=${encodeURIComponent(bookTitle)}`
      })
      .then(response => response.text())
      .then(data => {
        alert(data);
        if (buttonElement) {
          const bookDiv = buttonElement.closest(".book");
          if (bookDiv) {
  const button = bookDiv.querySelector("button");
  button.textContent = "Returned";
  button.disabled = true;
}

        }
      })
      .catch(error => {
        console.error('Error returning book:', error);
      });
    }
  </script>

</head>
<body>

<header>
  <div class="logo">
    <img src="usm seal.png" alt="USM Logo">
    <h1>Virtual Library System</h1>
  </div>
  <nav>
    <a href="javascript:void(0)" onclick="showSection('featured')">Home</a>
    <a href="javascript:void(0)" onclick="showSection('categories')">Browse</a>
    <a href="javascript:void(0)" onclick="showSection('my-books')">My Books</a>
    <div class="search-container">
      <input type="text" id="search-input" placeholder="Search books...">
      <button class="search-btn" onclick="searchBooks()">Search</button>
    </div>
    <span class="profile">Welcome, <?php echo htmlspecialchars($studentName); ?></span>
    <a href="javascript:void(0)" onclick="logout()" class="logout-btn">Logout</a>
  </nav>
</header>

<main>
  <section id="featured" style= "display:flex;" >
    <h2>Featured E-Books</h2>
    <div class="book-list">
      
        <div class="book book11"><img src="book11.jpg"><div class="book-title">Information Systems: A Manager’s Guide to Harnessing Technology</div><button onclick="confirmBorrow('Information Systems: A Manager’s Guide to Harnessing Technology')">Borrow</button></div>
        <div class="book book12"><img src="book12.jpg"><div class="book-title">Management Information Systems</div><button onclick="confirmBorrow('Management Information Systems')">Borrow</button></div>
        <div class="book book13"><img src="book13.jpg"><div class="book-title">IT Project Management: On Track from Start to Finish</div><button onclick="confirmBorrow('IT Project Management: On Track from Start to Finish')">Borrow</button></div>
        <div class="book book14"><img src="book14.jpg"><div class="book-title">Data Science for Business</div><button onclick="confirmBorrow('Data Science for Business')">Borrow</button></div>
        <div class="book book15"><img src="book15.jpg"><div class="book-title">Business Intelligence: A Managerial Perspective on Analytics</div><button onclick="confirmBorrow('Business Intelligence: A Managerial Perspective on Analytics')">Borrow</button></div>
        <div class="book book16"><img src="book16.jpg"><div class="book-title">Cybersecurity and Cyberwar: What Everyone Needs to Know</div><button onclick="confirmBorrow('Cybersecurity and Cyberwar: What Everyone Needs to Know')">Borrow</button></div>
        <div class="book book17"><img src="book17.jpg"><div class="book-title">Introduction to the Theory of Computation</div><button onclick="confirmBorrow('Introduction to the Theory of Computation')">Borrow</button></div>
        <div class="book book18"><img src="book18.jpg"><div class="book-title">Computer Networks: A Systems Approach</div><button onclick="confirmBorrow('Computer Networks: A Systems Approach')">Borrow</button></div>
        
    </div>
  </section>

  <section id="my-books">
  <h2>My Books</h2>
  <div class="book-list">
    <?php
      foreach ($borrowed_books as $book):
        $title = htmlspecialchars($book['book_title']);
        $imageMap = [
          "Introduction to Information Systems" => "book1.jpg",
          "Database Management Systems" => "book2.jpg",
          "Business Information Systems" => "book3.jpg",
          "Data Communications and Networking" => "book4.jpg",
          "Systems Analysis and Design" => "book5.jpg",
          "Enterprise Resource Planning" => "book6.jpg",
          "E-Commerce: Business, Technology, Society" => "book7.jpg",
          "Information Technology for Management" => "book8.jpg",
          "Digital Transformation: Survive and Thrive in an Era of Mass Extinction" => "book9.jpg",
          "Cloud Computing: Concepts, Technology & Architecture" => "book10.jpg",
          "Information Systems: A Manager’s Guide to Harnessing Technology" => "book11.jpg",
          "Management Information Systems" => "book12.jpg",
          "IT Project Management: On Track from Start to Finish" => "book13.jpg",
          "Data Science for Business" => "book14.jpg",
          "Business Intelligence: A Managerial Perspective on Analytics" => "book15.jpg",
          "Cybersecurity and Cyberwar: What Everyone Needs to Know" => "book16.jpg",
          "Introduction to the Theory of Computation" => "book17.jpg",
          "Computer Networks: A Systems Approach" => "book18.jpg",
          "Information Systems Development: Methods, Techniques, and Tools" => "book19.jpg",
          "The Big Data-Driven Business" => "book20.jpg",
          "The Art of Computer Programming" => "book21.jpg",
          "Computer Organization and Design" => "book22.jpg",
          "Modern Operating Systems" => "book23.jpg",
          "Artificial Intelligence: A Modern Approach" => "book24.jpg",
          "Computer Security: Principles and Practice" => "book25.jpg",
          "Programming Languages: Principles and Practice" => "book26.jpg",
          "Introduction to Algorithms" => "book27.jpg",
          "Discrete Mathematics and Its Applications" => "book28.jpg",
          "Design Patterns: Elements of Reusable Object-Oriented Software" => "book29.jpg",
          "Operating System Concepts" => "book30.jpg",
          "Ethical Hacking" => "book31.jpg",
          "Network Security Essentials" => "book32.jpg",
          "Cryptography and Network Security" => "book33.jpg",
          "Principles of Compiler Design" => "book34.jpg",
          "Cloud Native Applications" => "book35.jpg",
          "Full Stack Web Development" => "book36.jpg",
          "Linux for Beginners" => "book37.jpg",
          "Fundamentals of Networking" => "book38.jpg",
          "Game Development with Unity" => "book39.jpg",
          "Virtual Reality Development" => "book40.jpg",
          "Introduction to Information Systems" => "book41.jpg",
          "Programming in C" => "book42.jpg",
          "Programming in C++" => "book43.jpg",
          "Programming in JavaScript" => "book44.jpg",
          "React.js Essentials" => "book45.jpg",
          "Node.js in Action" => "book46.jpg",
          "PHP and MySQL Web Development" => "book47.jpg",
          "Laravel: Up & Running" => "book48.jpg",
          "Python Crash Course" => "book49.jpg",
          "Artificial Intelligence with Python" => "book50.jpg",
          "Android Programming for Beginners" => "book51.jpg",
          "Swift for Absolute Beginners" => "book52.jpg",
          "iOS App Development with Swift" => "book53.jpg",
          "Digital Image Processing" => "book54.jpg",
          "Computer Vision" => "book55.jpg",
          "Natural Language Processing" => "book56.jpg",
          "Data Analytics with R" => "book57.jpg",
          "Statistics for Data Science" => "book58.jpg",
          "Principles of Management" => "book59.jpg",
          "ITIL Foundation" => "book60.jpg",
          "Computer Ethics" => "book61.jpg",
          "Human-Computer Interaction" => "book62.jpg",
          "Information Systems" => "book63.jpg",
          "E-Commerce Technologies" => "book64.jpg",
          "Blockchain Basics" => "book65.jpg",
          "DevOps Handbook" => "book66.jpg",
          "Machine Learning for Hackers" => "book67.jpg",
          "AI Superpowers" => "book68.jpg",
          "Data Science from Scratch" => "book69.jpg",
          "Learning SQL" => "book70.jpg",
          "Designing Data-Intensive Applications" => "book71.jpg",
          "Programming Rust" => "book72.jpg",
          "Grokking Algorithms" => "book73.jpg",
          "System Design Interview" => "book74.jpg",
          "Building Microservices" => "book75.jpg",
          "You Don’t Know JS" => "book76.jpg",
          "Clean Code" => "book77.jpg",
          "Refactoring" => "book78.jpg",
          "Domain-Driven Design" => "book79.jpg",
          "Continuous Delivery" => "book80.jpg",
          "Test-Driven Development" => "book81.jpg",
          "The Mythical Man-Month" => "book82.jpg",
          "Structure and Interpretation of Computer Programs" => "book83.jpg",
          "Design Patterns" => "book84.jpg",
          "The Pragmatic Programmer" => "book85.jpg",
          "Cracking the Coding Interview" => "book86.jpg",
          "Head First Design Patterns" => "book87.jpg",
          "Algorithms Unlocked" => "book88.jpg"
        ];

        $image = isset($imageMap[$title]) ? $imageMap[$title] : "placeholder.jpg";
    ?>
      <div class="book">
        <img src="<?php echo $image; ?>" alt="Book Cover" style="width: 100px; height: 150px;">
        <div class="book-title"><?php echo $title; ?></div>
        <?php if ($book['return_date'] === null): ?>
  <button onclick="confirmReturn('<?php echo $title; ?>', this)">Return</button>
<?php else: ?>
  <button disabled>Returned</button>
<?php endif; ?>

      </div>
    <?php endforeach; ?>
  </div>
</section>


  <section id="categories" >
    <h2>Browse Books</h2>
    <div class="book-list">
         <div class="book book1"><img src="book1.jpg"><div class="book-title">Introduction to Information Systems</div><button onclick="confirmBorrow('Introduction to Information Systems')">Borrow</button></div>
        <div class="book book2"><img src="book2.jpg"><div class="book-title">Database Management Systems</div><button onclick="confirmBorrow('Database Management Systems')">Borrow</button></div>
        <div class="book book3"><img src="book3.jpg"><div class="book-title">Business Information Systems</div><button onclick="confirmBorrow('Business Information Systems')">Borrow</button></div>
        <div class="book book4"><img src="book4.jpg"><div class="book-title">Data Communications and Networking</div><button onclick="confirmBorrow('Data Communications and Networking')">Borrow</button></div>
        <div class="book book5"><img src="book5.jpg"><div class="book-title">Systems Analysis and Design</div><button onclick="confirmBorrow('Systems Analysis and Design')">Borrow</button></div>
        <div class="book book6"><img src="book6.jpg"><div class="book-title">Enterprise Resource Planning</div><button onclick="confirmBorrow('Enterprise Resource Planning')">Borrow</button></div>
        <div class="book book7"><img src="book7.jpg"><div class="book-title">E-Commerce: Business, Technology, Society</div><button onclick="confirmBorrow('E-Commerce: Business, Technology, Society')">Borrow</button></div>
        <div class="book book8"><img src="book8.jpg"><div class="book-title">Information Technology for Management</div><button onclick="confirmBorrow('Information Technology for Management')">Borrow</button></div>
        <div class="book book9"><img src="book9.jpg"><div class="book-title">Digital Transformation: Survive and Thrive in an Era of Mass Extinction</div><button onclick="confirmBorrow('Digital Transformation: Survive and Thrive in an Era of Mass Extinction')">Borrow</button></div>
        <div class="book book10"><img src="book10.jpg"><div class="book-title">Cloud Computing: Concepts, Technology & Architecture</div><button onclick="confirmBorrow('Cloud Computing: Concepts, Technology & Architecture')">Borrow</button></div>
        <div class="book book11"><img src="book11.jpg"><div class="book-title">Information Systems: A Manager’s Guide to Harnessing Technology</div><button onclick="confirmBorrow('Information Systems: A Manager’s Guide to Harnessing Technology')">Borrow</button></div>
        <div class="book book12"><img src="book12.jpg"><div class="book-title">Management Information Systems</div><button onclick="confirmBorrow('Management Information Systems')">Borrow</button></div>
        <div class="book book13"><img src="book13.jpg"><div class="book-title">IT Project Management: On Track from Start to Finish</div><button onclick="confirmBorrow('IT Project Management: On Track from Start to Finish')">Borrow</button></div>
        <div class="book book14"><img src="book14.jpg"><div class="book-title">Data Science for Business</div><button onclick="confirmBorrow('Data Science for Business')">Borrow</button></div>
        <div class="book book15"><img src="book15.jpg"><div class="book-title">Business Intelligence: A Managerial Perspective on Analytics</div><button onclick="confirmBorrow('Business Intelligence: A Managerial Perspective on Analytics')">Borrow</button></div>
        <div class="book book16"><img src="book16.jpg"><div class="book-title">Cybersecurity and Cyberwar: What Everyone Needs to Know</div><button onclick="confirmBorrow('Cybersecurity and Cyberwar: What Everyone Needs to Know')">Borrow</button></div>
        <div class="book book17"><img src="book17.jpg"><div class="book-title">Introduction to the Theory of Computation</div><button onclick="confirmBorrow('Introduction to the Theory of Computation')">Borrow</button></div>
        <div class="book book18"><img src="book18.jpg"><div class="book-title">Computer Networks: A Systems Approach</div><button onclick="confirmBorrow('Computer Networks: A Systems Approach')">Borrow</button></div>
        <div class="book book19"><img src="book19.jpg"><div class="book-title">Information Systems Development: Methods, Techniques, and Tools</div><button onclick="confirmBorrow('Information Systems Development: Methods, Techniques, and Tools')">Borrow</button></div>
        <div class="book book20"><img src="book20.jpg"><div class="book-title">The Big Data-Driven Business</div><button onclick="confirmBorrow('The Big Data-Driven Business')">Borrow</button></div>
        <div class="book book21"><img src="book21.jpg"><div class="book-title">The Art of Computer Programming</div><button onclick="confirmBorrow('The Art of Computer Programming')">Borrow</button></div>
<div class="book book22"><img src="book22.jpg"><div class="book-title">Computer Organization and Design</div><button onclick="confirmBorrow('Computer Organization and Design')">Borrow</button></div>
<div class="book book23"><img src="book23.jpg"><div class="book-title">Modern Operating Systems</div><button onclick="confirmBorrow('Modern Operating Systems')">Borrow</button></div>
<div class="book book24"><img src="book24.jpg"><div class="book-title">Artificial Intelligence: A Modern Approach</div><button onclick="confirmBorrow('Artificial Intelligence: A Modern Approach')">Borrow</button></div>
<div class="book book25"><img src="book25.jpg"><div class="book-title">Computer Security: Principles and Practice</div><button onclick="confirmBorrow('Computer Security: Principles and Practice')">Borrow</button></div>
<div class="book book26"><img src="book26.jpg"><div class="book-title">Programming Languages: Principles and Practice</div><button onclick="confirmBorrow('Programming Languages: Principles and Practice')">Borrow</button></div>
<div class="book book27"><img src="book27.jpg"><div class="book-title">Introduction to Algorithms</div><button onclick="confirmBorrow('Introduction to Algorithms')">Borrow</button></div>
<div class="book book28"><img src="book28.jpg"><div class="book-title">Discrete Mathematics and Its Applications</div><button onclick="confirmBorrow('Discrete Mathematics and Its Applications')">Borrow</button></div>
<div class="book book29"><img src="book29.jpg"><div class="book-title">Design Patterns: Elements of Reusable Object-Oriented Software</div><button onclick="confirmBorrow('Design Patterns: Elements of Reusable Object-Oriented Software')">Borrow</button></div>
<div class="book book30"><img src="book30.jpg"><div class="book-title">Operating System Concepts</div><button onclick="confirmBorrow('Operating System Concepts')">Borrow</button></div>
<div class="book book31"><img src="book31.jpg"><div class="book-title">Ethical Hacking</div><button onclick="confirmBorrow('Ethical Hacking')">Borrow</button></div>
<div class="book book32"><img src="book32.jpg"><div class="book-title">Network Security Essentials</div><button onclick="confirmBorrow('Network Security Essentials')">Borrow</button></div>
<div class="book book33"><img src="book33.jpg"><div class="book-title">Cryptography and Network Security</div><button onclick="confirmBorrow('Cryptography and Network Security')">Borrow</button></div>
<div class="book book34"><img src="book34.jpg"><div class="book-title">Principles of Compiler Design</div><button onclick="confirmBorrow('Principles of Compiler Design')">Borrow</button></div>
<div class="book book35"><img src="book35.jpg"><div class="book-title">Cloud Native Applications</div><button onclick="confirmBorrow('Cloud Native Applications')">Borrow</button></div>
<div class="book book36"><img src="book36.jpg"><div class="book-title">Full Stack Web Development</div><button onclick="confirmBorrow('Full Stack Web Development')">Borrow</button></div>
<div class="book book37"><img src="book37.jpg"><div class="book-title">Linux for Beginners</div><button onclick="confirmBorrow('Linux for Beginners')">Borrow</button></div>
<div class="book book38"><img src="book38.jpg"><div class="book-title">Fundamentals of Networking</div><button onclick="confirmBorrow('Fundamentals of Networking')">Borrow</button></div>
<div class="book book39"><img src="book39.jpg"><div class="book-title">Game Development with Unity</div><button onclick="confirmBorrow('Game Development with Unity')">Borrow</button></div>
<div class="book book40"><img src="book40.jpg"><div class="book-title">Virtual Reality Development</div><button onclick="confirmBorrow('Virtual Reality Development')">Borrow</button></div>
<div class="book book41"><img src="book41.jpg"><div class="book-title">Augmented Reality Design</div><button onclick="confirmBorrow('Augmented Reality Design')">Borrow</button></div>
<div class="book book42"><img src="book42.jpg"><div class="book-title">Programming in C</div><button onclick="confirmBorrow('Programming in C')">Borrow</button></div>
<div class="book book43"><img src="book43.jpg"><div class="book-title">Programming in C++</div><button onclick="confirmBorrow('Programming in C++')">Borrow</button></div>
<div class="book book44"><img src="book44.jpg"><div class="book-title">Programming in JavaScript</div><button onclick="confirmBorrow('Programming in JavaScript')">Borrow</button></div>
<div class="book book45"><img src="book45.jpg"><div class="book-title">React.js Essentials</div><button onclick="confirmBorrow('React.js Essentials')">Borrow</button></div>
<div class="book book46"><img src="book46.jpg"><div class="book-title">Node.js in Action</div><button onclick="confirmBorrow('Node.js in Action')">Borrow</button></div>
<div class="book book47"><img src="book47.jpg"><div class="book-title">PHP and MySQL Web Development</div><button onclick="confirmBorrow('PHP and MySQL Web Development')">Borrow</button></div>
<div class="book book48"><img src="book48.jpg"><div class="book-title">Laravel: Up & Running</div><button onclick="confirmBorrow('Laravel: Up & Running')">Borrow</button></div>
<div class="book book49"><img src="book49.jpg"><div class="book-title">Python Crash Course</div><button onclick="confirmBorrow('Python Crash Course')">Borrow</button></div>
<div class="book book50"><img src="book50.jpg"><div class="book-title">Artificial Intelligence with Python</div><button onclick="confirmBorrow('Artificial Intelligence with Python')">Borrow</button></div>
<div class="book book51"><img src="book51.jpg"><div class="book-title">Android Programming for Beginners</div><button onclick="confirmBorrow('Android Programming for Beginners')">Borrow</button></div>
<div class="book book52"><img src="book52.jpg"><div class="book-title">Swift for Absolute Beginners</div><button onclick="confirmBorrow('Swift for Absolute Beginners')">Borrow</button></div>
<div class="book book53"><img src="book53.jpg"><div class="book-title">iOS App Development with Swift</div><button onclick="confirmBorrow('iOS App Development with Swift')">Borrow</button></div>
<div class="book book54"><img src="book54.jpg"><div class="book-title">Digital Image Processing</div><button onclick="confirmBorrow('Digital Image Processing')">Borrow</button></div>
<div class="book book55"><img src="book55.jpg"><div class="book-title">Computer Vision</div><button onclick="confirmBorrow('Computer Vision')">Borrow</button></div>
<div class="book book56"><img src="book56.jpg"><div class="book-title">Natural Language Processing</div><button onclick="confirmBorrow('Natural Language Processing')">Borrow</button></div>
<div class="book book57"><img src="book57.jpg"><div class="book-title">Data Analytics with R</div><button onclick="confirmBorrow('Data Analytics with R')">Borrow</button></div>
<div class="book book58"><img src="book58.jpg"><div class="book-title">Statistics for Data Science</div><button onclick="confirmBorrow('Statistics for Data Science')">Borrow</button></div>
<div class="book book59"><img src="book59.jpg"><div class="book-title">Principles of Management</div><button onclick="confirmBorrow('Principles of Management')">Borrow</button></div>
<div class="book book60"><img src="book60.jpg"><div class="book-title">ITIL Foundation</div><button onclick="confirmBorrow('ITIL Foundation')">Borrow</button></div>
<div class="book book61"><img src="book61.jpg"><div class="book-title">Computer Ethics</div><button onclick="confirmBorrow('Computer Ethics')">Borrow</button></div>
<div class="book book62"><img src="book62.jpg"><div class="book-title">Human-Computer Interaction</div><button onclick="confirmBorrow('Human-Computer Interaction')">Borrow</button></div>
<div class="book book63"><img src="book63.jpg"><div class="book-title">Information Systems</div><button onclick="confirmBorrow('Information Systems')">Borrow</button></div>
<div class="book book64"><img src="book64.jpg"><div class="book-title">E-Commerce Technologies</div><button onclick="confirmBorrow('E-Commerce Technologies')">Borrow</button></div>
<div class="book book65"><img src="book65.jpg"><div class="book-title">Blockchain Basics</div><button onclick="confirmBorrow('Blockchain Basics')">Borrow</button></div>
<div class="book book66"><img src="book66.jpg"><div class="book-title">DevOps Handbook</div><button onclick="confirmBorrow('DevOps Handbook')">Borrow</button></div>
<div class="book book67"><img src="book67.jpg"><div class="book-title">Machine Learning for Hackers</div><button onclick="confirmBorrow('Machine Learning for Hackers')">Borrow</button></div>
<div class="book book68"><img src="book68.jpg"><div class="book-title">AI Superpowers</div><button onclick="confirmBorrow('AI Superpowers')">Borrow</button></div>
<div class="book book69"><img src="book69.jpg"><div class="book-title">Data Science from Scratch</div><button onclick="confirmBorrow('Data Science from Scratch')">Borrow</button></div>
<div class="book book70"><img src="book70.jpg"><div class="book-title">Learning SQL</div><button onclick="confirmBorrow('Learning SQL')">Borrow</button></div>
<div class="book book71"><img src="book71.jpg"><div class="book-title">Designing Data-Intensive Applications</div><button onclick="confirmBorrow('Designing Data-Intensive Applications')">Borrow</button></div>
<div class="book book72"><img src="book72.jpg"><div class="book-title">Programming Rust</div><button onclick="confirmBorrow('Programming Rust')">Borrow</button></div>
<div class="book book73"><img src="book73.jpg"><div class="book-title">Grokking Algorithms</div><button onclick="confirmBorrow('Grokking Algorithms')">Borrow</button></div>
<div class="book book74"><img src="book74.jpg"><div class="book-title">System Design Interview</div><button onclick="confirmBorrow('System Design Interview')">Borrow</button></div>
<div class="book book75"><img src="book75.jpg"><div class="book-title">Building Microservices</div><button onclick="confirmBorrow('Building Microservices')">Borrow</button></div>
<div class="book book76"><img src="book76.jpg"><div class="book-title">You Don’t Know JS</div><button onclick="confirmBorrow('You Don’t Know JS')">Borrow</button></div>
<div class="book book77"><img src="book77.jpg"><div class="book-title">Clean Code</div><button onclick="confirmBorrow('Clean Code')">Borrow</button></div>
<div class="book book78"><img src="book78.jpg"><div class="book-title">Refactoring</div><button onclick="confirmBorrow('Refactoring')">Borrow</button></div>
<div class="book book79"><img src="book79.jpg"><div class="book-title">Domain-Driven Design</div><button onclick="confirmBorrow('Domain-Driven Design')">Borrow</button></div>
<div class="book book80"><img src="book80.jpg"><div class="book-title">Continuous Delivery</div><button onclick="confirmBorrow('Continuous Delivery')">Borrow</button></div>
<div class="book book81"><img src="book81.jpg"><div class="book-title">Test-Driven Development</div><button onclick="confirmBorrow('Test-Driven Development')">Borrow</button></div>
<div class="book book82"><img src="book82.jpg"><div class="book-title">The Mythical Man-Month</div><button onclick="confirmBorrow('The Mythical Man-Month')">Borrow</button></div>
<div class="book book83"><img src="book83.jpg"><div class="book-title">Structure and Interpretation of Computer Programs</div><button onclick="confirmBorrow('Structure and Interpretation of Computer Programs')">Borrow</button></div>
<div class="book book84"><img src="book84.jpg"><div class="book-title">Design Patterns</div><button onclick="confirmBorrow('Design Patterns')">Borrow</button></div>
<div class="book book85"><img src="book85.jpg"><div class="book-title">The Pragmatic Programmer</div><button onclick="confirmBorrow('The Pragmatic Programmer')">Borrow</button></div>
<div class="book book86"><img src="book86.jpg"><div class="book-title">Cracking the Coding Interview</div><button onclick="confirmBorrow('Cracking the Coding Interview')">Borrow</button></div>
<div class="book book87"><img src="book87.jpg"><div class="book-title">Head First Design Patterns</div><button onclick="confirmBorrow('Head First Design Patterns')">Borrow</button></div>
<div class="book book88"><img src="book88.jpg"><div class="book-title">Algorithms Unlocked</div><button onclick="confirmBorrow('Algorithms Unlocked')">Borrow</button></div>


      </div>
  </section>
</main>

<!-- Modal -->
<div class="modal-overlay" id="confirmationModal">
  <div class="modal">
    <p class="modal-message"></p>
    <button class="confirm">Yes</button>
    <button class="cancel">No</button>
  </div>
</div>

</body>
</html>

