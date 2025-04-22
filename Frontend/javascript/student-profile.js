// here we have the general logic for the profile
//simple desing to display all the given students information
// name address phone number etc

let addressExists = false;
let phoneNumbers = [];
let personalEmail = "";

//first weetch and display student profile info
window.addEventListener("DOMContentLoaded", () => {
    fetch("/Backend/PHP/student-info.php")
        .then(res => res.json() )
        .then(data => {
            if (data.error) return console.error(data.error);

            const p = data.profile;
            const container = document.querySelector(".profile-info");
            //get from data base

            container.innerHTML = `
                <h3>${p.FirstName} ${p.LastName}</h3>
                <p><strong>Student ID:</strong> ${p.StudentID}</p>
                <p><strong>Birth-date:</strong> ${p.BirthDate}</p>
                <p><strong>SIN:</strong> ***-***-${p.SIN.slice(-3)}</p>
                <p><strong>Nationality:</strong> Canadian</p>
                <p><strong>Permanent Address:</strong> ${p.Address}</p>
                <p><strong>Phone Number:</strong> ${p.PhoneNumber}</p>
            `;

            document.getElementById("uofcEmail").textContent = p.Email;
        })
        .catch(err => console.error("Failed to load profile", err));
});

// same as advisor - Address Logic ---
function addAddress() {
    const addressInput = document.getElementById('addressInput');
    const addressDisplay = document.getElementById('addressDisplay');

    if (addressExists)
     {
        alert("You already have an address. Delete it first to add a new one.");
        return;
    }

    if (addressInput.value.trim() === "") {
        alert("Please enter a valid address.");
        return;
    }

    addressDisplay.innerText = addressInput.value;
    addressExists = true;

    //add the delete button
    addressDisplay.innerHTML += ` <button onclick="deleteAddress()">Delete</button>`;
    addressInput.value = "";
}

function deleteAddress() {
    document.getElementById('addressDisplay').innerText = "-";
    addressExists = false;
}

// same as above but for Phone Logic ---
function addPhone() {
    const phoneInput = document.getElementById('phoneInput');
    const phoneList = document.getElementById('phoneList');
    const phone = phoneInput.value.trim();

    if (!phone) {
        alert("Enter a valid phone number.");
        return;
    }

    if (phoneNumbers.length >= 1) {
        alert("Maximum 1 alternative phone number is allowed.");
        return;
    }

    phoneNumbers.push(phone);

    const li = document.createElement('li');
    
    i.innerHTML = `${phone} <button onclick="removePhone(this, '${phone}')">Delete</button>`;
    phoneList.appendChild(li);

    phoneInput.value = "";
}

function removePhone(btn, phone) {
    phoneNumbers = phoneNumbers.filter(p => p !== phone);
    btn.parentElement.remove();
}

// same as aboce but for Email Logic ---
function updateEmail() {
    const emailInput = document.getElementById('emailInput');
    const email = emailInput.value.trim();
    const personalEmailSpan = document.getElementById('personalEmail');

    if (!email || !email.includes('@') || email.endsWith('@ucalgary.ca')) {
        alert("Enter a valid personal (non-UofC) email.");
        return;
    }

    personalEmail = email;
    personalEmailSpan.innerHTML = `${email} <button onclick="clearPersonalEmail()">Delete</button>`;
    
    emailInput.value = "";
}

//clear 
function clearPersonalEmail() {
    personalEmail = "";
    document.getElementById('personalEmail').innerText = "-";
}
