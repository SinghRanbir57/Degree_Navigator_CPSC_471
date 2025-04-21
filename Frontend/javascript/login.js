
// go back to login when 'Login' button is clicked
function login() {
  window.location.href = "login.html"; 
}
// give a confirmation message to user, then confirm.
function logout() {
const popup = document.getElementById("logout-popup");
popup.style.display = "block";
popup.innerHTML = 
`
    <div class="logout-modal">
    <p>Are you sure you want to logout?</p>
    <button onclick="confirmLogout()">Yes</button>
    <button onclick="cancelLogout()">No</button>
    </div>
`;
}