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
    //parse the json response from the server
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          //notify user success
          alert("Semester plan saved successfully!");
        } else {
          //notify user failure
          alert("Failed to save plan: " + (data.error || "Unknown error"));
        }
      })

      // just logging any errors to console
      .catch(error => {
        console.error("Error:", error);
        alert("An error occurred.");
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