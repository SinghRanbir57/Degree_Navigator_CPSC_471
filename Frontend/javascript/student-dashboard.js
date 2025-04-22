document.addEventListener("DOMContentLoaded", () => {
    const meetingForm = document.getElementById("meetingForm");
    const meetingList = document.getElementById("meetingList");
  
    let studentId = null;
    let studentName = "";
  
    // Utility for ordinal formatting
    function ordinal(n) {
      const suffixes = ["th", "st", "nd", "rd"];
      const v = n % 100;
      return n + (suffixes[(v - 20) % 10] || suffixes[v] || suffixes[0]);
    }
  
    function showAlert(message, type = "success") {
      const alertBox = document.getElementById("custom-alert");
      if (!alertBox) return alert(message); // fallback to native alert
  
      alertBox.textContent = message;
      alertBox.className = `custom-alert ${type} show`;
  
      setTimeout(() => {
        alertBox.classList.remove("show");
      }, 3000);
    }
  
    // Load student profile
    fetch("/Backend/PHP/student-info.php")
      .then(res => res.json())
      .then(data => {
        if (data.error) return console.error(data.error);
  
        const p = data.profile;
        studentId = p.StudentID;
        studentName = `${p.FirstName} ${p.LastName}`;
  
        const target = document.getElementById("studentProfile");
        target.innerHTML = `
          <h2>Hello ${studentName}!</h2>
          <p><strong>Student ID:</strong> ${p.StudentID}</p>
          <p><strong>Program:</strong> B.Sc. in ${p.MajorMinor.split(" ")[0]}</p>
          <p><strong>Major:</strong> ${p.MajorMinor}</p>
          <p><strong>Minor:</strong> None</p>
          <p><strong>Year:</strong> ${ordinal(p.Course_year)} Year</p>
          <p><strong>Semester:</strong> Winter 2025</p>
        `;
  
        if (p.GPA) {
          const gpaBox = document.querySelector("#gpa-popup p strong");
          if (gpaBox) gpaBox.textContent = p.GPA;
        }
      });
  
    // Submit meeting request
    meetingForm.addEventListener("submit", function (event) {
      event.preventDefault();
  
      const advisorName = document.getElementById("advisorName").value.trim();
      const date = document.getElementById("date").value;
      const time = document.getElementById("time").value;
  
      if (!advisorName || !date || !time) {
        showAlert("Please fill out all fields.", "error");
        return;
      }
  
      // Fetch advisor ID from backend
      fetch("/Backend/PHP/request-meeting.php?advisorName=" + encodeURIComponent(advisorName))
        .then(res => res.json())
        .then(result => {
          if (result.error || !result.advisorId) {
            showAlert("❌ Invalid advisor name or not your assigned advisor.", "error");
            return;
          }
  
          const advisorId = result.advisorId;
  
          fetch("/Backend/PHP/request-meeting.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              advisorId,
              studentId,
              studentName,
              date,
              time
            })
          })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                showAlert("✅ Meeting request sent!");
                meetingForm.reset();
                loadMeetings();
              } else {
                console.error(data);
                showAlert("❌ " + (data.error || "Unknown error"), "error");
              }
            })
            .catch(err => {
              console.error("Request error:", err);
              showAlert("⚠️ Network error occurred", "error");
            });
        })
        .catch(err => {
          console.error("Advisor lookup failed:", err);
          showAlert("⚠️ Advisor validation failed", "error");
        });
    });
  
    // Load meetings
    function loadMeetings() {
      meetingList.innerHTML = "";
  
      fetch("/Backend/PHP/schedule-meeting.php")
        .then(res => res.json())
        .then((meetingsData) => {
          const meetings = meetingsData.own || [];
          if (meetings.length === 0) {
            meetingList.innerHTML = "<li>No upcoming meetings yet.</li>";
            return;
          }
  
          meetings.forEach((meeting) => {
            const li = document.createElement("li");
            li.textContent = `${meeting.date} ${meeting.time} with Advisor ID ${meeting.advisorId} [${meeting.status}]`;
            meetingList.appendChild(li);
          });
        })
        .catch(err => {
          console.error("Failed to load meetings:", err);
        });
    }
  
    loadMeetings(); // ✅ Run initially
  });
  