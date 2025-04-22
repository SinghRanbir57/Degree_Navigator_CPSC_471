document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("plan-form");

  if (!form) {
    console.warn("No form with id='plan-form' found.");
    return; // stop here inorder to prevent null error
  }

  // handle form submission
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    
    // get the vals from the semster and year dropdowns
    const semester = document.getElementById("semester").value;
    const year = document.getElementById("year" ).value;
  
    //get all selected courses
    const selectedCourses = Array.from(
      form.querySelectorAll("input[name='courses[]']:checked")
    ).map((checkbox) => checkbox.value);
  
    // Send to backend via fetch
    fetch("/Backend/PHP/save-plan.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        semester,
        year,
        courses: selectedCourses
      })
    })
      .then(async response => {
        const data = await response.json().catch(() => ({ error: "Invalid JSON response" }));
        if (!response.ok) {
          throw new Error(data.error || "Unknown server error");
        }
        if (data.success) {
          alert("Semester plan saved successfully!");
        } else {
          alert("Failed to save plan: " + (data.error || "Unknown error"));
        }
      })
      .catch(error => {
      console.error("Fetch Error:", error);
      alert("An error occurred: " + error.message);
    });
  });
});  

//logout and login handling moved from inline
function logout() {
  document.getElementById("logout-popup").style.display = "block";
}

//confirm logout, make sure sending to correct file path,
function confirmLogout() {
  fetch("/Backend/PHP/logout.php")
    .then(() => {
      //redirect to l ogin page after layout
      window.location.href = "/Frontend/html/joint/login.html";
    })
    .catch(() => {
      alert("Logout failed. Try again.");
    });
}


//redirect user to the login page.
function login() {
  window.location.href = "login.html";
}

const courses = [
  // 🧠 Junior Computer Science Courses
  { code: "CPSC 203", name: "Introduction to Problem Solving using Application Software", credits: 3, prerequisite: null, antirequisite: "CPSC majors" },
  { code: "CPSC 217", name: "Intro to CS for Multidisciplinary Studies I", credits: 3, prerequisite: null, antirequisite: "CPSC 215, CPSC 231" },
  { code: "CPSC 219", name: "Intro to CS for Multidisciplinary Studies II", credits: 3, prerequisite: "CPSC 217 or Data Science 211", antirequisite: "CPSC 233" },
  { code: "CPSC 231", name: "Intro to CS for CS Majors I", credits: 3, prerequisite: "Admission to CS/Bioinformatics", antirequisite: "CPSC 215, CPSC 217" },
  { code: "CPSC 233", name: "Intro to CS for CS Majors II", credits: 3, prerequisite: "CPSC 231 + Admission to CS", antirequisite: "CPSC 219" },
  { code: "CPSC 235", name: "Advanced Intro to CS", credits: 3, prerequisite: "Department Consent", antirequisite: "All other CS intro courses" },
  { code: "CPSC 251", name: "Theoretical Foundations of CS I", credits: 3, prerequisite: "CPSC 219 or CPSC 231", antirequisite: "MATH 271, MATH 273" },

  // 🚀 Senior Computer Science Courses
  { code: "CPSC 304", name: "Survey of CS for Non-Majors", credits: 3, prerequisite: null, antirequisite: "CS majors" },
  { code: "CPSC 313", name: "Intro to Computability", credits: 3, prerequisite: "MATH 271 or 273, PHIL 279 or 377, and CPSC 219 or 233 or 235", antirequisite: "CPSC 351" },
  { code: "CPSC 319", name: "Data Structures, Algorithms & Apps", credits: 3, prerequisite: "CPSC 219 or 233 or 235", antirequisite: "CS majors" },
  { code: "CPSC 329", name: "Info Security & Privacy", credits: 3, prerequisite: "MATH 30-1, 30-2 or 31", antirequisite: null },
  { code: "CPSC 331", name: "Data Structures, Algorithms & Analysis", credits: 3, prerequisite: "CPSC 251 and CPSC 219 or 233 or 235", antirequisite: "CPSC 319" },
  { code: "CPSC 335", name: "Intermediate Info Structures", credits: 3, prerequisite: "CPSC 319 or CPSC 331", antirequisite: null },
  { code: "CPSC 351", name: "Theoretical Foundations of CS II", credits: 3, prerequisite: "CPSC 219 or 233 or 235, CPSC 251, MATH 249 or 265 or 275, PHIL 279 or 377", antirequisite: "CPSC 313" },
  { code: "CPSC 355", name: "Computing Machinery I", credits: 3, prerequisite: "CPSC 219 or 233 or 235", antirequisite: "CPSC 265, ENGG 369" },
  { code: "CPSC 359", name: "Computing Machinery II", credits: 3, prerequisite: "CPSC 355 and PHIL 279 or 377", antirequisite: null },
  { code: "CPSC 393", name: "Metacognition in CS", credits: 3, prerequisite: "CPSC 219 or 233 or Data Science 311 and CS Major", antirequisite: null },
  { code: "CPSC 399", name: "Special Topics in CS", credits: 3, prerequisite: "Department Consent", antirequisite: null },
  { code: "CPSC 405", name: "Software Entrepreneurship", credits: 3, prerequisite: "Software Engineering 300 or 301", antirequisite: null },
  { code: "CPSC 409", name: "History of Computation", credits: 3, prerequisite: "CPSC 355", antirequisite: null },
  { code: "CPSC 411", name: "Compiler Construction", credits: 3, prerequisite: "CPSC 319 or 331 and CPSC 355", antirequisite: null },
  { code: "CPSC 413", name: "Design & Analysis of Algorithms I", credits: 3, prerequisite: "CPSC 331, CPSC 313 or 351, MATH 211 or 213, and MATH 249 or 265 or 275", antirequisite: null },
  { code: "CPSC 418", name: "Intro to Cryptography", credits: 3, prerequisite: "CPSC 331 and CPSC 351 or MATH 271 or 273 or 315", antirequisite: "CPSC 429, CPSC 557" },
  { code: "CPSC 433", name: "Artificial Intelligence", credits: 3, prerequisite: "CPSC 313 or 351 and PHIL 279 or 377", antirequisite: null },
  { code: "CPSC 441", name: "Computer Networks", credits: 3, prerequisite: null, antirequisite: null }
];

document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("searchInput");

  if (!searchInput) {
    console.warn("Search input not found.");
    return;
  }

  searchInput.addEventListener("input", function () {
    const query = this.value.toLowerCase();
    const results = courses.filter(course =>
      course.code.toLowerCase().includes(query) ||
      course.name.toLowerCase().includes(query)
    );

    displaySearchResults(results);
  });
});

function displaySearchResults(results) {
  const container = document.getElementById("searchResults");
  container.innerHTML = "";

  if (results.length === 0) {
    container.innerHTML = "<p>No courses found.</p>";
    return;
  }

  results.forEach(course => {
    const div = document.createElement("div");
    div.className = "course-result";
    div.innerHTML = `
      <strong>${course.code}</strong>: ${course.name} (${course.credits} credits)
      <br>
      <em>Prerequisite:</em> ${course.prerequisite || "None"}<br>
      <em>Antirequisite:</em> ${course.antirequisite || "None"}<br>
      <button onclick="addCourseToPlan('${course.code}', '${course.name}', ${course.credits})">Add to Plan</button>
    `;
    container.appendChild(div);
  });
}

function addCourseToPlan(code, name, credits) {
  const course = courses.find(c => c.code === code);
  const tableBody = document.querySelector(".course-table tbody");

  // Prevent duplicates
  if ([...tableBody.querySelectorAll("td:nth-child(2)")].some(td => td.textContent === code)) {
    alert(`${code} is already in the plan.`);
    return;
  }

  // Prerequisite check
  const prereq = course?.prerequisite;
  if (prereq && ![...tableBody.querySelectorAll("td:nth-child(2)")].some(td => td.textContent === prereq)) {
    const proceed = confirm(`${code} has a prerequisite: ${prereq}. Add it first?\nPress OK to continue anyway.`);
    if (!proceed) return;
  }

  const row = document.createElement("tr");
  row.innerHTML = `
    <td><input type="checkbox" name="courses[]" value="${code}"></td>
    <td>${code}</td>
    <td>${name}</td>
    <td>${credits}</td>
    <td>${prereq || "None"}</td>
    <td style="color:${course?.antirequisite ? 'red' : 'inherit'}">
      ${course?.antirequisite || "None"}
    </td>
    <td><button type="button" onclick="removeCourseRow(this)">Remove</button></td>
  `;
  tableBody.appendChild(row);

  // Save to localStorage
  savePlanToLocalStorage();
}

function savePlanToLocalStorage() {
  const tableBody = document.querySelector(".course-table tbody");
  const courses = [...tableBody.querySelectorAll("tr")].map(row => {
    const tds = row.querySelectorAll("td");
    return {
      code: tds[1].textContent,
      name: tds[2].textContent,
      credits: parseInt(tds[3].textContent),
      prerequisite: tds[4].textContent,
      antirequisite: tds[5].textContent
    };
  });
  localStorage.setItem("plannedCourses", JSON.stringify(courses));
}

function loadPlanFromLocalStorage() {
  const saved = localStorage.getItem("plannedCourses");
  if (!saved) return;

  const courseList = JSON.parse(saved);
  courseList.forEach(course => {
    addCourseToPlan(course.code, course.name, course.credits);
  });
}

document.addEventListener("DOMContentLoaded", () => {
  loadPlanFromLocalStorage();
});

function removeCourseRow(button) {
  const row = button.closest('tr');
  row.remove();
  savePlanToLocalStorage(); // optional: keep localStorage updated
}