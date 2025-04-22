<?php

// advisor-dashboard.php
// this is for advisor dashboard backed looping, will send all the required knowledge to the correct place,
// filters to header, sidebar, footer, logout
session_start();

//block access if not logged in or not an advisor
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
    <title>Advisor Dashboard</title>
    <link rel="stylesheet" href="../../css/advisor/advisor-dashboard.css">
    <link rel="stylesheet" href="../../css/joint/header.css">
    <link rel="stylesheet" href="../../css/joint/sidebar.css">
    <link rel="stylesheet" href="../../css/joint/footer.css">
    <link rel="stylesheet" href="../../css/joint/logout.css">

    <style>
        .email-reveal {
            display: none;
            margin-top: 6px;
            background-color: #f9f9f9;
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-family: monospace;
            font-size: 14px;
            width: fit-content;
            white-space: nowrap;
            cursor: text;
            user-select: all;
        }

        .email-reveal.visible {
            display: inline-block;
        }
    </style>
</head>
<!--add main images and content for advisor dashboard -->

<body>
    <div class="screen">
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

            <script>
                function openLogoutModal() {
                    // check logout
                    document.getElementById("logoutModal").style.display = "flex";
                }

                function closeLogoutModal() {
                    document.getElementById("logoutModal").style.display = "none";
                }

                function confirmLogout() {
                    // confirm logout, and logout accross all tabs., filter to the backend login.php
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

        <footer class="footer">
            <a href="https://ucalgary.service-now.com/it?id=kb_article&amp;sys_id=e86c4d3913b93ec06f3afbb2e144b03d" target="_blank">Account FAQs</a>
            <a href="https://ucalgary.service-now.com/it?id=contact_and_help" target="_blank">Contact Support</a>
            <div class="copyright">
                © <script>document.write(new Date().getFullYear());</script> University of Calgary
            </div>
        </footer>

        <!-- Main Content -->
        <div class="main-body">
            <div class="section-advisor-info">
                <h2>My Information</h2>
                <p><strong>Name:</strong> Alex Johnson</p>
                <p><strong>Department:</strong> Computer Science</p>
                <p><strong>Office Hours:</strong> Tues & Thurs, 1 - 4 PM</p>
            </div>

            <div class="section">
                <h2>My Advisees</h2>
                <div class="advisee-table-wrapper">
                    <table class="advisor-student-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Student ID</th>
                                <th>Major</th>
                                <th>Minor</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody id="adviseeTableBody">
                            <tr><td colspan="5">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section">
                <h2>Schedule a Meeting</h2>
                <form id="meetingForm">
                    <label for="studentName">Student Name:</label>
                    <input type="text" id="studentName" name="studentName" required>

                    <label for="studentId">Student ID:</label>
                    <input type="text" id="studentId" name="studentId" required>

                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>

                    <label for="time">Time:</label>
                    <input type="time" id="time" name="time" required>

                    <button type="submit">Schedule</button>
                </form>

                <h2 id="Upcoming-meetings">Upcoming Meetings</h2>
                <ul id="meetingList" style="margin-bottom: 30px;"></ul>

                <h2>Meeting Requests</h2>
                <ul id="requestList" style="margin-bottom: 30px;"></ul>
            </div>
        </div>
    </div>

    <!-- Inject advisorId before loading external JS -->
    <script>
        const advisorId = <?php echo (int)$_SESSION['user_id']; ?>;
    </script>

    <script src="../../javascript/advisor-dashboard.js"></script>
</body>
</html>
