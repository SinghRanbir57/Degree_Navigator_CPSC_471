const form     = document.getElementById("meetingForm");
const listMeet = document.getElementById("meetingList");
const listReq  = document.getElementById("requestList");

// 1. Load meetings + advisor info
window.addEventListener("DOMContentLoaded", () => {
  loadEverything();
  loadAdvisorProfile();
});

// 2. Submit meeting form
form.addEventListener("submit", e => {
  e.preventDefault();
  const name = document.getElementById("studentName").value.trim();
  const id   = document.getElementById("studentId").value.trim();
  const date = document.getElementById("date").value;
  const time = document.getElementById("time").value;

  if (!name || !id || !date || !time) {
    alert("Fill every field");
    return;
  }

  fetch("/Backend/PHP/schedule-meeting.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      advisorId, // injected by PHP
      studentName: name,
      studentId: id,
      date,
      time
    })
  })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
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

  fetch("/Backend/PHP/schedule-meeting.php")
    .then(r => r.json())
    .then(({ own, requests }) => {
      render(listMeet, own, false);     // editable/delete
      render(listReq, requests, true);  // accept/decline
    })
    .catch(err => console.error(err));
}

// 4. Render meeting list
function render(target, arr, isRequest) {
  target.innerHTML = "";
  arr.forEach(m => {
    const li = document.createElement("li");
    li.textContent = `${m.studentName} – ${m.date} @ ${m.time}`;
    const btnBox = document.createElement("span");

    if (isRequest) {
      makeBtn(btnBox, "✔ Accept", () => decision(m.id, "accepted"));
      makeBtn(btnBox, "✖ Decline", () => decision(m.id, "declined"));
    } else {
      makeBtn(btnBox, "✎ Edit", () => alert("Implement edit UI"));
      makeBtn(btnBox, "🗑 Del", () => decision(m.id, "delete"));
    }

    li.appendChild(btnBox);
    target.appendChild(li);
  });
}

// 5. Create meeting action buttons
function makeBtn(parent, text, cb) {
  const b = document.createElement("button");
  b.textContent = text;
  b.onclick = cb;
  b.style.marginLeft = "8px";
  parent.appendChild(b);
}

// 6. Handle meeting actions
function decision(meetingId, action) {
  const method = action === "delete" ? "DELETE" : "PUT";
  const body = { id: meetingId, status: action };

  fetch("/Backend/PHP/schedule-meeting.php", {
    method,
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body)
  })
    .then(r => r.json())
    .then(() => loadEverything());
}

// 7. Load and inject advisor profile info
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
    .catch(err => console.error("Failed to load advisor info", err));
}
