const meetingForm = document.getElementById("meetingForm");
const meetingList = document.getElementById("meetingList");

let editingIndex = null; // for tracking if editing an entry

window.addEventListener("DOMContentLoaded", () => {
    loadMeetings();
});

meetingForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const name = document.getElementById("studentName").value.trim();
    const id = document.getElementById("studentId").value.trim();
    const date = document.getElementById("date").value;
    const time = document.getElementById("time").value;

    if (!name || !id || !date || !time) {
        alert("Please fill out all fields.");
        return;
    }

    const meeting = { name, id, date, time };
    const storedMeetings = JSON.parse(localStorage.getItem("meetings")) || [];

    if (editingIndex !== null) {
        storedMeetings[editingIndex] = meeting;
        editingIndex = null;
    } else {
        storedMeetings.push(meeting);
    }

    localStorage.setItem("meetings", JSON.stringify(storedMeetings));
    meetingForm.reset();
    loadMeetings();
});

function loadMeetings() {
    meetingList.innerHTML = "";
    const meetings = JSON.parse(localStorage.getItem("meetings")) || [];

    meetings.forEach((meeting, index) => {
        const li = document.createElement("li");

        // Create main meeting text
        const meetingText = document.createElement("p");
        meetingText.textContent = `${meeting.name} (ID: ${meeting.id}) - ${meeting.date} at ${meeting.time}`;
        li.appendChild(meetingText);

        // Create a div for buttons (to appear below)
        const buttonGroup = document.createElement("div");
        buttonGroup.style.marginTop = "5px";

        const editBtn = document.createElement("button");
        editBtn.textContent = "Edit";
        editBtn.onclick = () => editMeeting(index);

        const deleteBtn = document.createElement("button");
        deleteBtn.textContent = "Delete";
        deleteBtn.style.marginLeft = "10px";
        deleteBtn.onclick = () => deleteMeeting(index);

        buttonGroup.appendChild(editBtn);
        buttonGroup.appendChild(deleteBtn);
        li.appendChild(buttonGroup);

        meetingList.appendChild(li);
    });
}

function deleteMeeting(index) {
    const meetings = JSON.parse(localStorage.getItem("meetings")) || [];
    meetings.splice(index, 1);
    localStorage.setItem("meetings", JSON.stringify(meetings));
    loadMeetings();
}

function editMeeting(index) {
    const meetings = JSON.parse(localStorage.getItem("meetings")) || [];
    const meeting = meetings[index];

    document.getElementById("studentName").value = meeting.name;
    document.getElementById("studentId").value = meeting.id;
    document.getElementById("date").value = meeting.date;
    document.getElementById("time").value = meeting.time;

    editingIndex = index;
}