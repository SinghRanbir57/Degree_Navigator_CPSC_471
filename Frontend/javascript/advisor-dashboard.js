// advisor dashboard, this will show all funcationalaties,
// such as profile view, schedule meeting, and my advisees.

// get dom elements for fomr and meeting lists
const form     = document.getElementById("meetingForm");
const listMeet = document.getElementById("meetingList");
const listReq  = document.getElementById("requestList");

// 1. Load meetings + advisor info
window.addEventListener("DOMContentLoaded", () => {
  loadEverything();
  loadAdvisorProfile();
  loadAdvisees(); 

});

// fetch and populate the lsit of advisees in the advisor table
function loadAdvisees() {
    const tableBody = document.querySelector(".advisor-student-table tbody");
    tableBody.innerHTML = "<tr><td colspan='5'>Loading…</td></tr>";

    //error handling and checking for the advisees info
    fetch("/Backend/PHP/advisee-info.php" )
      .then(res => res.json())
      .then(data => {

        if (!Array.isArray(data)) {
          tableBody.innerHTML = "<tr><td colspan='5'>Error loading advisees.</td></tr>";
          return;
        }
  
        tableBody.innerHTML = "";
        data.forEach(student => {
          const [major, minor] = (student.MajorMinor || "").split(" / ").map(x => x.trim());
  
          //format major and minor with line breaks
          const formattedMajor = (major || "-").replace(" ", "<br>");
          const formattedMinor = (minor || "-").replace(" ", "<br>");
          
          // split major minor string and format it for display
          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${student.FirstName} ${student.LastName}</td>
            <td>${student.StudentID}</td>
            <td>${formattedMajor}</td>
            <td>${formattedMinor}</td>
            <td class="email-cell">
              <div class="email-wrapper">
                <button class="show-email-btn" onclick="this.nextElementSibling.classList.toggle('visible')">Show Email</button>
                <div class="email-reveal">${student.Email}</div>
              </div>
            </td>
          `;
          tableBody.appendChild(row);
        });
      })
      .catch(err => {
        console.error("Failed to load advisees:", err);
        tableBody.innerHTML = "<tr><td colspan='5'>Failed to fetch data.</td></tr>";
      });
  }
  

//handle Submit meeting form
form.addEventListener("submit", e => {
  e.preventDefault();
  const name = document.getElementById("studentName").value.trim();
  const id   = document.getElementById("studentId").value.trim();
  const date = document.getElementById("date").value;
  const time = document.getElementById("time").value;

  // base input validation
  if (!name || !id || !date || !time) {
    alert("Fill every field");
    return;
  }
//send form data to backend
  fetch("/Backend/PHP/schedule-meeting.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ advisorId, studentName: name, studentId: id, date, time })
  })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        //meeting was a success.
        alert("✅ Meeting successfully scheduled!");
        form.reset();
        loadEverything();
      } else {
        alert(d.error || "Server error");
      }
    });
});

// 3. Load meetings from backend
function loadEverything() {
  listMeet.innerHTML = listReq.innerHTML = "Loading…";
  //now we send to our schedule meeting code this is only to be used by the advisors.
  fetch("/Backend/PHP/schedule-meeting.php")
    .then(r => r.json())
    .then(({ own, requests }) => {
      const upcoming = own.filter(m => m.status === 'accepted');
      render(listMeet, upcoming, false);
      render(listReq, requests, true);
    });
}

// Render meeting list
function render(target, arr, isRequest) {
  target.innerHTML = "";
  arr.forEach(m => {
    const li = document.createElement("li");
    li.textContent = `${m.studentName} – ${m.date} @ ${m.time}`;
    const btnBox = document.createElement("span");

    if (isRequest) {
      //add accept or decline buttons for prompted meeting
      makeBtn(btnBox, "✔ Accept", () => decision(m.id, "accepted"));
      
      makeBtn(btnBox, "✖ Decline", () => decision(m.id, "declined"));
    } 
    else {
      // add edit button to edit a given meeting.
      makeBtn(btnBox, "✎ Edit", () => {
        li.innerHTML = "";
        const dateInput = document.createElement("input");
        dateInput.type = "date";
        dateInput.value = m.date;

        const timeInput = document.createElement("input");
        timeInput.type = "time";
        timeInput.value = m.time;
        // save the meeting that was just created
        const saveBtn = document.createElement("button");
        saveBtn.textContent = "✅ Save";
        saveBtn.onclick = () => {
          fetch("/Backend/PHP/schedule-meeting.php", {
            method: "PATCH",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: m.id, date: dateInput.value, time: timeInput.value })
          })
          //catch any errors.
            .then(r => r.json() )
            .then(res => res.success ? loadEverything() : alert("Update failed."))
            .catch(err => {
              console.error("Update error:", err);
              alert("Network error");
            });
        };
        //cancel a given pre made meeting.
        const cancelBtn = document.createElement("button");
        cancelBtn.textContent = "❌ Cancel";

        cancelBtn.onclick = () => loadEverything();

        li.append(dateInput, timeInput, saveBtn, cancelBtn);
      });
      //del a old meeting.
      makeBtn(btnBox, "🗑 Del", () => decision(m.id, "delete"));
    }

    li.appendChild(btnBox);
    target.appendChild(li);
  });
}

// 5. Create meeting action buttons
function makeBtn(parent, text, cb) 
{
  const b = document.createElement("button");
  b.textContent = text;
  b.onclick = cb;
  b.style.marginLeft = "8px";
  parent.appendChild(b);

}

// 6. handle meeting actions
function decision(meetingId, action) {
  const method = action === "delete" ? "DELETE" : "PATCH";
  const body = { id: meetingId, status: action };

  fetch("/Backend/PHP/schedule-meeting.php", {
    method,
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body)
  }).then(r => r.json()).then(() => loadEverything());
}

// 7. load and inject advisor profile info
function loadAdvisorProfile() {
  const container = document.querySelector(".section-advisor-info");
  if (!container) return;

  fetch("/Backend/PHP/advisor-info.php")
    .then(res => res.json())
    .then(data => {
      if (data.error) return console.error(data.error);
      const p = data.profile;

      container.innerHTML = `
        <h2>My Information</h2>
        <p><strong>Name:</strong> ${p.FirstName} ${p.LastName}</p>
        <p><strong>Advisor ID:</strong> ${p.AdvisorID}</p>
        <p><strong>Department:</strong> ${p.Department}</p>
        <p><strong>Office Hours:</strong> ${p.OfficeHours || "Not set"}</p>
      `;
    })
    //catch any errors.
    .catch(err => console.error("Failed to load advisor info", err));
}
