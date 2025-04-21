const meetingForm = document.getElementById("meetingForm");
const meetingList = document.getElementById("meetingList");

let editingIndex = null;

window.addEventListener("DOMContentLoaded", () => {
    loadMeetings(); // 🔄 pulls from backend instead of localStorage
});

meetingForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const name = document.getElementById("advisorName").value.trim();
    const date = document.getElementById("date").value;
    const time = document.getElementById("time").value;

    if (!name || !date || !time) {
        alert("Please fill out all fields.");
        return;
    }

    fetch("/Backend/PHP/schedule-meeting.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            name,
            id: "placeholder-student-id", // ideally get real ID from session
            date,
            time
        })
    })
    .then((res) => res.json())
    .then((data) => {
        if (data.success) {
            alert("Meeting scheduled!");
            meetingForm.reset();
            loadMeetings();
        } else {
            alert("Error: " + (data.error || "Unknown error"));
        }
    })
    .catch((err) => {
        console.error("Fetch error:", err);
        alert("Network error while saving meeting.");
    });
});

function loadMeetings() {
    meetingList.innerHTML = "";

    fetch("/Backend/PHP/schedule-meeting.php") // ✅ updated path
        .then((res) => res.json())
        .then((meetings) => {
            if (!Array.isArray(meetings)) {
                throw new Error("Invalid data");
            }

            meetings.forEach((meeting, index) => {
                const li = document.createElement("li");
                const meetingText = document.createElement("p");
                meetingText.textContent = `${meeting.name} | ${meeting.date} | ${meeting.time}`;
                li.appendChild(meetingText);

                const buttonGroup = document.createElement("div");
                buttonGroup.style.marginTop = "5px";

                const editBtn = document.createElement("button");
                editBtn.textContent = "Edit";
                editBtn.onclick = () => editMeeting(meeting);

                const deleteBtn = document.createElement("button");
                deleteBtn.textContent = "Delete";
                deleteBtn.style.marginLeft = "10px";
                deleteBtn.onclick = () => {
                    fetch("/Backend/PHP/schedule-meeting.php", {
                        method: "DELETE",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ id: meeting.id, date: meeting.date })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert("Meeting deleted!");
                            loadMeetings(); // refresh list
                        } else {
                            alert("Delete failed: " + (data.error || "Unknown error"));
                        }
                    })
                    .catch(err => {
                        console.error("Delete error:", err);
                        alert("Network error while deleting meeting.");
                    });
                };
                
                buttonGroup.appendChild(editBtn);
                buttonGroup.appendChild(deleteBtn);
                li.appendChild(buttonGroup);

                meetingList.appendChild(li);
            });
        })
        .catch((err) => {
            console.error("Failed to load meetings:", err);
        });
}

function editMeeting(meeting) {
    document.getElementById("advisorName").value = meeting.name;
    document.getElementById("date").value = meeting.date;
    document.getElementById("time").value = meeting.time;
}
