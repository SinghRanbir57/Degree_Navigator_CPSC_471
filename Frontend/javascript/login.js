document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();
    const loginMessage = document.getElementById("loginMessage");

    console.log(`Login attempt: ${username} / ${password}`);

    const credentials = {
        student: "student123",
        advisor: "advisorpass123"
    };

    if (credentials[username] && credentials[username] === password) {
        loginMessage.style.color = "green";
        loginMessage.textContent = `✅ Welcome, ${username}! Redirecting...`;

        setTimeout(() => {
            window.location.href = `../../html/student/student-dashboard.html`;
        }, 1000);
    } else {
        loginMessage.style.color = "red";
        loginMessage.textContent = "❌ Invalid username or password.";
    }
});

/********************* Logout Button *********************/
function logout() {
  // Show the popup
  const popup = document.getElementById("logout-popup");
  popup.style.display = "block";

  // Redirect to login page after 1.5 seconds (optional)
  setTimeout(() => {
    window.location.href = "login.html"; // adjust path if needed
  }, 1500);
}

function login() {
  window.location.href = "login.html"; // go back to login when 'Login' button is clicked
}

function logout() {
const popup = document.getElementById("logout-popup");
popup.style.display = "block";
popup.innerHTML = `
    <div class="logout-modal">
    <p>Are you sure you want to logout?</p>
    <button onclick="confirmLogout()">Yes</button>
    <button onclick="cancelLogout()">No</button>
    </div>
`;
}