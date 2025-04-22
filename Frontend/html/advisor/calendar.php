<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'advisor') {
    header("Location: /Frontend/html/joint/login.html");
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,user-scalable=yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="stylesheet" href="../../css/joint/header.css">
  <link rel="stylesheet" href="../../css/joint/sidebar.css">
  <link rel="stylesheet" href="../../css/joint/calendar.css">
  <link rel="stylesheet" href="../../css/joint/logout.css">
  <title>University Calendar</title>
</head>

<body>
  <div class="Screen">
    <div class="sidebar">
      <div class="sidebar-logo">
        <img src="../../images/UniversityOfCalgaryLogo.png" alt="University Logo" class="logo">
      </div>
      <a href="advisor-dashboard.php">Dashboard</a>
      <a href="advisee-dashboard.html">My Advisees</a>
      <a href="calendar.php">Academic Calendar</a>
      <a href="program-guide.html">Program Guide</a>
      <a href="advisor-profile.html">My Profile</a>
    </div>

    <div class="header">
      <div class="header-left"></div>

      <!-- Search -->
      <div class="header-right"></div>
      <div class="search-container">
        <input type="text" class="search-bar" placeholder="Search...">
      </div>

      <div>
        <button class="logout-btn" onclick="openLogoutModal()">Logout</button>
      </div>

      <div id="logoutModal" class="modal">
        <div class="modal-content">
          <span class="close" onclick="closeLogoutModal()">&times;</span>
          <h2>Are you sure you want to logout?</h2>
          <div class="logout-actions">
            <button onclick="confirmLogout()" class="confirm-btn">Yes, Logout</button>
            <button onclick="closeLogoutModal()" class="cancel-btn">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Body -->
    <div class="main-body">
      <div class="main-content">
        <h1>Official University Calendar</h1>
        <p><strong>📝 Note: The official university calendar includes important academic dates, Course Name, 
          Course ID, Course Hours, Prerequisite(s), Antirequisite(s), course descriptions, degree requirements, 
          and policies for the academic year.</strong></p>
        <iframe id="calendarFrame"
          src="https://www.ucalgary.ca/pubs/calendar/archives/2023/computer-science.html#gsc.tab=0"
          style="width: 100%; height: 100vh; border: none;">
          <p>Your browser does not support iframes or the file could not be found.</p>
        </iframe>
      </div>
    </div>
  </div>

  <script>
    function openLogoutModal() {
      document.getElementById("logoutModal").style.display = "flex";
    }

    function closeLogoutModal() {
      document.getElementById("logoutModal").style.display = "none";
    }

    function confirmLogout() {
      fetch("/Backend/PHP/logout.php")
        .then(() => {
          window.location.href = "/Frontend/html/joint/login.html";
        })
        .catch(() => {
          alert("Logout failed. Try again.");
        });
    }
  </script>

  <script src="../../javascript/script.js"></script>
  <script src="../../javascript/login.js"></script>
</body>

</html>
