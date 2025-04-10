document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("plan-form");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const semester = document.getElementById("semester").value;
    const year = document.getElementById("year").value;

    const selectedCourses = Array.from(
      form.querySelectorAll("input[name='courses[]']:checked")
    ).map((checkbox) => checkbox.value);

    // Mock saving to backend (future: send via fetch to PHP)
    console.log("Semester:", semester);
    console.log("Year:", year);
    console.log("Selected Courses:", selectedCourses);

    alert("Semester plan saved!");
  });
});

// Logout and login handling (moved from inline)
function logout() {
  document.getElementById("logout-popup").style.display = "block";
}

function login() {
  window.location.href = "login.html";
}