/****************** Show/hide GPA ************************/
function showGPA() {
  document.getElementById("gpa-popup").style.display = "flex";
}

// hide the gpa popup
function hideGPA() {
  document.getElementById("gpa-popup").style.display = "none";
}

/****************** Feedback on Save Advisor Note Button ************************/
document.addEventListener("DOMContentLoaded", () => {

  const saveNoteBtn = document.querySelector(".btn");
  if (saveNoteBtn ) {
    // add click event
    saveNoteBtn.addEventListener("click", () => {
      alert( "Note saved!");
    });
  }
});

/****************** Function to toggle dropdowns ************************/
function toggleDropdown(id) {
  const dropdown = document.getElementById(id);
  if (dropdown.style.display === "block") {
    dropdown.style.display = "none";
  } else {
    dropdown.style.display = "block";
  }
}

// toggle sidebar visibility and shifts main content, when sidebar toggle
document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.querySelector('.sidebar-toggle');
  if (toggleBtn ) {
    //toggle it
    toggleBtn.addEventListener('click', function() {
      document.querySelector('.sidebar').classList.toggle('open');
      document.querySelector('.main-content').classList.toggle('shift');
    });
  }
});

/****************** Collapse all sections on page load ************************/
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".requirements-section").forEach((section) => {
    const content = section.querySelector(".section-content");
    const arrow = section.querySelector(".arrow");

    content.style.display = "none"; // Collapse all sections
    section.classList.remove("expanded");
    arrow.innerHTML = "▶"; // Reset arrows
  });
});

/****************** Javacsript for Requirements.html Page ************************/
function toggleSection(header) {
  const section = header.parentElement;
  const content = section.querySelector(".section-content");
  const arrow = section.querySelector(".arrow");

  if (content.style.display === "block") {
    content.style.display = "none";
    // colapse
    section.classList.remove("expanded" );
    arrow.innerHTML = "▶";
  } else {
    //expand the section
    content.style.display = "block";
    section.classList.add("expanded");
    arrow.innerHTML = "▼";
  }
}

//here we are calculating the sets for the progress abrs for completed, in progress and incomplete
document.addEventListener("DOMContentLoaded", function () {
  const totalUnits = 48  + 9 + 63;

  const completedUnits = 48;

  const inProgressUnits = 9;
  const incompleteUnits =  63;

  const completedWidth = (completedUnits / totalUnits) * 100;
  const inProgressWidth = (inProgressUnits / totalUnits) * 100;
  const incompleteWidth = (incompleteUnits / totalUnits) * 100;

  const completedBar = document.querySelector(".progress-completed");
  
  const inProgressBar = document.querySelector(".progress-in-progress");
  
  const incompleteBar = document.querySelector(".progress-incomplete");

  //update the bar
  if (completedBar) completedBar.style.width = completedWidth + "%";
  if (inProgressBar) {
    inProgressBar.style.width = inProgressWidth + "%";
    inProgressBar.style.left = completedWidth + "%";
  }

  if (incompleteBar) {
    incompleteBar.style.width = incompleteWidth + "%";
    incompleteBar.style.left = (completedWidth + inProgressWidth) + "%";
  }

});

/****************** header ************************/
function loadHeader() {
  fetch('header.html')
    .then(response => response.text())
    .then(data => document.getElementById('header-container').innerHTML = data);
}

document.addEventListener("DOMContentLoaded", function () {
  loadHeader();
});

function toggleSection(header) {
  const section = header.parentElement;
  const content = section.querySelector(".section-content");
  const descriptions = section.querySelectorAll(".course-description");
  const arrow = header.querySelector(".arrow");

  if (content.style.display === "block") {
    content.style.display = "none";
    arrow.innerHTML = "▶";
  } else {
    content.style.display = "block";
    arrow.innerHTML = "▼";
  }

  descriptions.forEach(description => {
    description.style.display = (description.style.display === "block") ? "none" : "block";
  });
}

function sortTableDropdown() {
  const sortBy = document.getElementById("sort-options").value;
  const table = document.getElementById("course-table-body");
  const rows = Array.from(table.getElementsByTagName("tr"));

  rows.sort((rowA, rowB) => {
    let valueA, valueB;

    switch (sortBy) {
      case "course":
        valueA = rowA.cells[0].textContent.trim();
        valueB = rowB.cells[0].textContent.trim();
        break;
      case "term":
        valueA = convertTerm(rowA.cells[2].textContent.trim());
        valueB = convertTerm(rowB.cells[2].textContent.trim());
        break;
      case "year":
        valueA = parseInt(rowA.cells[3].textContent.trim()) || 0;
        valueB = parseInt(rowB.cells[3].textContent.trim()) || 0;
        return valueB - valueA;
      case "grade":
        valueA = convertGrade(rowA.cells[4].textContent.trim());
        valueB = convertGrade(rowB.cells[4].textContent.trim());
        break;
      case "status":
        valueA = convertStatus(rowA.cells[5].textContent.trim());
        valueB = convertStatus(rowB.cells[5].textContent.trim());
        break;
      default:
        return 0;
    }

    return valueA > valueB ? 1 : -1;
  });

  table.innerHTML = "";
  rows.forEach(row => table.appendChild(row));
}

function convertTerm(term) {
  const termOrder = { "Winter": 1, "Spring": 2, "Summer": 3, "Fall": 4, "-": 5 };
  return termOrder[term] || 0;
}

// give equivalent for converting to pie
function convertGrade(grade) {
  const gradeScale = {
    "A+": 1, "A": 2, "A-": 3,
    "B+": 4, "B": 5, "B-": 6,
    "C+": 7, "C": 8, "C-": 9,
    "D+": 10, "D": 11, "F": 12,
    "-": 13
  };
  return gradeScale[grade] || -1;
}

function convertStatus(status) {
  const statusOrder = { "Complete": 1, "In-progress": 2, "Incomplete": 3, "-": 4 };
  return statusOrder[status] || 5;
}

// fetch data from table
function getTableData() {
  let totalCredits = 0;
  // set vars to 0
  let completedCredits = 0;

  document.querySelectorAll(".course-table tbody tr").forEach(row => {
    let credits = parseInt(row.cells[1].innerText);
    let status = row.cells[2].innerText.trim();

    totalCredits += credits;
    if (status === "Completed") {
      completedCredits += credits;
    }
  });

  return { totalCredits, completedCredits };
}

//update the progress bar when change has been made
function updateProgressBar() {
  let { totalCredits, completedCredits } = getTableData();
  let percentage = ( completedCredits / totalCredits) * 100;

  let progressBar = document.getElementById("progress-bar");
  progressBar.style.width = percentage + "%";
  progressBar.innerText = Math.round(percentage) + "% Completed";

}

let chartInstance = null;

function updatePieChart() {
  let {  totalCredits, completedCredits } = getTableData();
  let remainingCredits = totalCredits - completedCredits;

  let ctx = document.getElementById("myPieChart").getContext("2d");

  if (chartInstance ) {

    chartInstance.destroy();
  }

  //create a new pie chart instance
  chartInstance = new Chart(ctx, {
    type: "pie",
    data: {
      // add lables
      labels: ["Completed", "Remaining" ],
      datasets: [{
        data: [completedCredits, remainingCredits],
        backgroundColor: ["#4CAF50", "#FFC107"],
      }]
    },
    // make the chart resize automatically
    options: {
      responsive: true,
      plugins: {
        legend: { position: "bottom" }
      }
    }
  });
}

document.addEventListener("DOMContentLoaded", function () {
  updateProgressBar();
  updatePieChart();

  document.querySelectorAll(".course-status").forEach(select => {
    select.addEventListener("change", function () {
      updateProgressBar();
      updatePieChart();
    });
  });
});
