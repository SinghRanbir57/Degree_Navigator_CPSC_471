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
            window.location.href = `../html/student-dashboard.html`;
        }, 1000);
    } else {
        loginMessage.style.color = "red";
        loginMessage.textContent = "❌ Invalid username or password.";
    }
});