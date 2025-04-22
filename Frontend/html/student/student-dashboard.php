<?php
session_start();

//make sure valid login from student.
if (!isset($_SESSION['username'] ) || $_SESSION['role'] !== 'student') {
    header('Location: /Frontend/html/joint/login.html');
    exit;
}
?>
<!-- created into php folder.. -->


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
        const studentId = <?php echo (int)$_SESSION['user_id']; ?>;
      </script>

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
        <div class="profile-info" id="studentProfile">
          <h2>Loading your profile…</h2>
        </div>
        <div class="academic-standing">
          <h2>Academic Standing Summary</h2>
          <ul>
            <p><strong>- Total Credits Earned:</strong> 60.00</p>
            <p><strong>- Status:</strong> Good Standing</p>
            <p><strong>- Expected Graduation:</strong> Fall 2026</p>
          </ul>
          <button class="gpa-btn" onclick="showGPA()">View GPA</button>
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
          <h2>Degree Progress</h2>
          <div class="progress-container">
            <div class="pie-chart"></div>
            <div class="progress-key">
              <p><span class="color-box completed"></span><strong>Completed:</strong>&nbsp;48</p>
              <p><span class="color-box in-progress"></span><strong>In-Progress:</strong>&nbsp;9</p>
              <p><span class="color-box incomplete"></span><strong>Incomplete:</strong>&nbsp;63</p>
            </div>
          </div>
        </div>
      </div>

      <div class="academic-content">
        <!-- Academic Standing Summary -->
        <div class="book-meeting">
          <h2>Request a Meeting with Advisor</h2>
          <form id="meetingForm" onsubmit="return false;">
          
            <label for="advisorName"><strong>Advisor Name:</strong></label>
            <input type="text" id="advisorName" name="advisorName" required>
          
            <label for="date"><strong>Date:</strong></label>
            <input type="date" id="date" name="date" required>
          
            <label for="time"><strong>Time:</strong></label>
            <input type="time" id="time" name="time" required>
          
            <button type="submit">Request</button>
          </form>
          <h2 id="Upcoming-meetings">Upcoming Meetings</h2>
          <ul id="meetingList"></ul>
        </div>
        <!-- Academic Advisor -->
        <div class="section-5">
          <h2>Course History Timeline</h2>
          <div style="display: flex; flex-wrap: wrap; gap: 40px;">
            <!-- Year 1 Timeline -->
            <div class="timeline" style="flex: 1;">
              <h3>Year 1</h3>
              <div class="timeline-item">
                <span class="term">Fall 2021</span>
                <p>CPSC 101 - A</p>
                <p>MATH 101 - A-</p>
              </div>
              <div class="timeline-item">
                <span class="term">Winter 2022</span>
                <p>CPSC 219 - A</p>
                <p>CPSC 233 - A</p>
              </div>
            </div>
        
            <!-- Year 2 Timeline -->
            <div class="timeline" style="flex: 1;">
              <h3>Year 2</h3>
              <div class="timeline-item">
                <span class="term">Fall 2022</span>
                <p>CPSC 331 - A-</p>
                <p>CPSC 359 - A</p>
              </div>
              <div class="timeline-item">
                <span class="term">Winter 2023</span>
                <p>CPSC 319 - B+</p>
                <p>CPSC 457 - A-</p>
              </div>
              <div class="timeline-item">
                <span class="term">Fall 2023</span>
                <p>CPSC 449 - A-</p>
                <p>CPSC 441 - B+</p>
                <p>CPSC 351 - B+</p>
                <p>CPSC 457 - A-</p>
                <p>ANTH 203 - A</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Section 2: Completed Courses -->
      <div class="section-2">
        <h2>Completed Courses</h2>
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
                <tr>
                  <td>CPSC 219</td>
                  <td>3.00</td>
                  <td>Winter</td>
                  <td>2022</td>
                  <td>A</td>
                  <td><span class="completed">Complete</span></td>
                </tr>
                <tr>
                  <td>CPSC 233</td>
                  <td>3.00</td>
                  <td>Winter</td>
                  <td>2022</td>
                  <td>A</td>
                  <td><span class="completed">Complete</span></td>
                </tr>
                <tr>
                  <td>ANTH 203</td>
                  <td>3.00</td>
                  <td>Fall</td>
                  <td>2023</td>
                  <td>A</td>
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
                <td>CPSC 331</td>
                <td>3.00</td>
                <td>Fall</td>
                <td>2022</td>
                <td>A-</td>
                <td><span class="completed">Complete</span></td>
              </tr>
              <tr>
                <td>CPSC 359</td>
                <td>3.00</td>
                <td>Fall</td>
                <td>2022</td>
                <td>A</td>
                <td><span class="completed">Complete</span></td>
              </tr>
              <tr>
                <td>CPSC 319</td>
                <td>3.00</td>
                <td>Winter</td>
                <td>2023</td>
                <td>B+</td>
                <td><span class="completed">Complete</span></td>
              </tr>
              <tr>
                <td>CPSC 457</td>
                <td>3.00</td>
                <td>Winter</td>
                <td>2023</td>
                <td>A-</td>
                <td><span class="completed">Complete</span></td>
              </tr>
              <tr>
                <td>CPSC 449</td>
                <td>3.00</td>
                <td>Fall</td>
                <td>2023</td>
                <td>A-</td>
                <td><span class="completed">Complete</span></td>
              </tr>
              <tr>
                <td>CPSC 441</td>
                <td>3.00</td>
                <td>Fall</td>
                <td>2023</td>
                <td>B+</td>
                <td><span class="completed">Complete</span></td>
              </tr>
              <tr>
                <td>CPSC 351</td>
                <td>3.00</td>
                <td>Fall</td>
                <td>2023</td>
                <td>B+</td>
                <td><span class="completed">Complete</span></td>
              </tr>
              <tr>
                <td>CPSC 457</td>
                <td>3.00</td>
                <td>Fall</td>
                <td>2023</td>
                <td>A-</td>
                <td><span class="completed">Complete</span></td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <div class="section-3">
        <h2>In-progress Courses</h2>
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
                <th>Grade</th>
                <th>Status</th>
              </tr>
              <tr>
                <td>CPSC 471</td>
                <td>3.00</td>
                <td>Winter</td>
                <td>2025</td>
                <td>—</td>
                <td><span class="in-progress">In Progress</span></td>
              </tr>
              <tr>
                <td>CPSC 481</td>
                <td>3.00</td>
                <td>Winter</td>
                <td>2025</td>
                <td>—</td>
                <td><span class="in-progress">In Progress</span></td>
              </tr>
              <tr>
                <td>CPSC 411</td>
                <td>3.00</td>
                <td>Winter</td>
                <td>2025</td>
                <td>—</td>
                <td><span class="in-progress">In Progress</span></td>
              </tr>
            </table>
          </div>
        </div>  
      </div>
    </div>
  </div>
<div id="logout-popup" style="display: none;"></div>

<script>
  const studentId = <?php echo (int)$_SESSION['user_id']; ?>;
  const studentName = <?php echo json_encode($_SESSION['username']); ?>;
</script>

<script src="../../javascript/script.js"></script>
<script src="../../javascript/login.js"></script>
<script>
  const studentId = <?php echo (int)$_SESSION['user_id']; ?>;
  const studentName = <?php echo json_encode($_SESSION['username']); ?>;
</script>

<script>
  const studentId = <?php echo (int)$_SESSION['user_id']; ?>;
</script>

<script src="../../javascript/student-dashboard.js" defer></script>
<div id="custom-alert" class="custom-alert"></div>
</body>
</html>