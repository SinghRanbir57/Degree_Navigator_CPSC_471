<?php
session_start();

if (!isset($_SESSION['username'] ) || $_SESSION['role'] !== 'student') {
    header('Location: /Frontend/html/joint/login.html');
    exit;
}
?>
<!-- created into php folder. -->


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,user-scalable=yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="stylesheet" href="../../css/student/student-dashboard.css">
  <link rel="stylesheet" href="../../css/joint/header.css">
  <link rel="stylesheet" href="../../css/joint/sidebar.css">
  <link rel="stylesheet" href="../../css/joint/logout.css">
  <title>Student Dashboard</title>
</head>

<body>
  <div class="Screen">
    <div class="sidebar">
      <div class="sidebar-logo">
        <img src="../../images/UniversityOfCalgaryLogo.png" alt="University Logo" class="logo">
      </div>
      <a href="../student/student-dashboard.php">Home</a>
      <a href="../student/semester-builder.html">Semester Builder</a>
      <a href="../student/requirements.html">Academic Report</a>
      <a href="../student/calendar.html">View Calendar</a>
      <a href="../student/program-guide.html">Program Guide</a>
      <a href="../student/student-profile.html">Profile</a>
    </div>

    <div class="header">
      <div class="header-left"></div>
    
      <!-- Search -->
      <div class="header-right"></div>
      
      <div class="search-container">
        <input type="text" class="search-bar" placeholder="Search for a course...">
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

    </div>
    
    <!-- Main Body -->
    <div class="main-body">
      <!-- Combined Section: Profile and Degree Progress -->
      <div class="profile-degree-container">
        <!-- User Profile -->
        <div class="profile-section">
          <img src="../../images/user image.png" alt="Profile Photo" class="profile-photo">
          <div class="profile-info">
            <h3>James Gosling</h3>
            <p>B.Sc. in Computer Science</p>
            <button class="gpa-btn" onclick="showGPA()">GPA</button>
          </div>
        </div>
        <!-- GPA Popup -->
        <div id="gpa-popup" class="gpa-popup">
          <div class="gpa-popup-content">
            <button class="gpa-close-btn" onclick="hideGPA()">✖</button>
            <h2>Your GPA</h2>
            <p><strong>3.75</strong></p>
          </div>
        </div>
    
        <!-- Degree Progress -->
        <div class="degree-progress-section">
          <h4>Degree Progress</h4>
          <div class="progress-container">
            <div class="pie-chart"></div>
            <div class="progress-key">
              <p><span class="color-box completed"></span>Completed: 48</p>
              <p><span class="color-box in-progress"></span> In Progress: 9</p>
              <p><span class="color-box incomplete"></span> Incomplete: 63</p>
            </div>
          </div>
        </div>
      </div>

      <div class="academic-content">
        <!-- Academic Standing Summary -->
        <div class="section-4">
          <h4>Academic Standing Summary</h4>
          <ul>
            <li><strong>Total Credits Earned:</strong> 60.00</li>
            <li><strong>Current GPA:</strong> 3.75</li>
            <li><strong>Status:</strong> Good Standing</li>
            <li><strong>Expected Graduation:</strong> Spring 2026</li>
          </ul>
        </div>
        <!-- Academic Advisor -->
        <div class="section-5">
          <h4>Course History Timeline</h4>
          <div class="timeline">
            <div class="timeline-item">
              <span class="term">Fall 2021</span>
              <p>CPSC 101 - A</p>
              <p>MATH 101 - A-</p>
            </div>
            <div class="timeline-item">
              <span class="term">Fall 2022</span>
              <p>CPSC 201 - A</p>
            </div>
            <!-- Add more items -->
          </div>
        </div>
      </div>

      <!-- Section 2: Completed Courses -->
      <div class="section-2">
        <h4>Completed Courses</h4>
        <!-- Year 1 Dropdown -->
        <div class="requirements-section">
          <h2 onclick="toggleDropdown('year1')">
            <span class="arrow">▶</span> Year 1
          </h2>
          <div id="year1" class="section-content">
            <div class="sorting-container">
              <div class="sort-box">
                <select id="sort-options" onchange="sortTableDropdown()">
                  <option value="" disabled selected>Sort by:</option>
                  <option value="course">Course (A - Z)</option>
                  <option value="term">Term</option>
                  <option value="year">Year</option>
                  <option value="grade">Grade</option>
                  <option value="status">Status</option>
                </select>
              </div>
            </div>
            <table class="requirements-table">
              <tr>
                <th>Course</th>
                <th>Units</th>
                <th>Term</th>
                <th>Year</th>
                <th>Grade</th>
                <th>Status</th>
              </tr>
              <tr>
                <td>CPSC 101</td>
                <td>3.00</td>
                <td>Fall</td>
                <td>2021</td>
                <td>A</td>
                <td><span class="completed">Complete</span></td>
              </tr>
              <tr>
                <td>MATH 101</td>
                <td>3.00</td>
                <td>Fall</td>
                <td>2021</td>
                <td>A-</td>
                <td><span class="completed">Complete</span></td>
              </tr>
              <!-- Add more courses here -->
            </table>
          </div>
        </div>
        <!-- Year 2 Dropdown -->
        <div class="requirements-section">
          <h2 onclick="toggleDropdown('year2')">
            <span class="arrow">▶</span> Year 2
          </h2>
          <div id="year2" class="section-content">
            <div class="sorting-container">
              <div class="sort-box">
                <select id="sort-options" onchange="sortTableDropdown()">
                  <option value="" disabled selected>Sort by:</option>
                  <option value="course">Course (A - Z)</option>
                  <option value="term">Term</option>
                  <option value="year">Year</option>
                  <option value="grade">Grade</option>
                  <option value="status">Status</option>
                </select>
              </div>
            </div>
            <table class="requirements-table">
              <tr>
                <th>Course</th>
                <th>Units</th>
                <th>Term</th>
                <th>Year</th>
                <th>Grade</th>
                <th>Status</th>
              </tr>
              <tr>
                <td>CPSC 201</td>
                <td>3.00</td>
                <td>Fall</td>
                <td>2022</td>
                <td>A</td>
                <td><span class="completed">Complete</span></td>
              </tr>
              <tr>
                <td>MATH 211</td>
                <td>3.00</td>
                <td>Winter</td>
                <td>2023</td>
                <td>B-</td>
                <td><span class="completed">Complete</span></td>
              </tr>
              <!-- Add more courses here -->
            </table>
          </div>
        </div>
      </div>

      <div class="section-3">
        <h4>Upcoming Courses</h4>
        <!-- Year 1 Dropdown -->
        <div class="requirements-section">
          <h2 onclick="toggleDropdown('Upcoming Courses')">
            <span class="arrow">▶</span> Year 1
          </h2>
          <div id="Upcoming Courses" class="section-content">
            <div class="sorting-container">
              <div class="sort-box">
                <select id="sort-options" onchange="sortTableDropdown()">
                  <option value="" disabled selected>Sort by:</option>
                  <option value="course">Course (A - Z)</option>
                  <option value="term">Term</option>
                  <option value="year">Year</option>
                  <option value="grade">Grade</option>
                  <option value="status">Status</option>
                </select>
              </div>
            </div>
            <table class="requirements-table">
              <tr>
                <th>Course</th>
                <th>Units</th>
                <th>Term</th>
                <th>Year</th>
                <th>Status</th>
              </tr>
              <tr>
                <td>CPSC 331</td>
                <td>3.00</td>
                <td>Fall</td>
                <td>2025</td>
                <td><span class="in-progress">Planned</span></td>
              </tr>
              <tr>
                <td>PHIL 279</td>
                <td>3.00</td>
                <td>Fall</td>
                <td>2025</td>
                <td><span class="in-progress">Planned</span></td>
              </tr>
            </table>
          </div>
        </div>  
      </div>
    </div>
  </div>
<div id="logout-popup" style="display: none;"></div>
<script src="../../javascript/script.js"></script>
<script src="../../javascript/login.js"></script>
</body>
</html>