const form        = document.getElementById("meetingForm");
const listMeet    = document.getElementById("meetingList");
const listReq     = document.getElementById("requestList");

window.addEventListener("DOMContentLoaded", loadEverything);

form.addEventListener("submit", e => {
  e.preventDefault();
  const name = document.getElementById("studentName").value.trim();
  const id   = document.getElementById("studentId").value.trim();
  const date = document.getElementById("date").value;
  const time = document.getElementById("time").value;
  if (!name || !id || !date || !time) return alert("Fill every field");

  fetch("/Backend/PHP/schedule-meeting.php", {
    method : "POST",
    headers: {"Content-Type":"application/json"},
    body   : JSON.stringify({
        advisorId,             // <─ this now exists because of PHP injection
        studentName: name,
        studentId  : id,
        date, time
      })
      
  })
  .then(r => r.json()).then(d => {
    if (d.success) {
      alert("✅ Meeting successfully scheduled!");
      form.reset();
      loadEverything();
    } else {
      alert(d.error || "Server error");
    }
  });  
});

function loadEverything(){
  listMeet.innerHTML = listReq.innerHTML = "Loading…";

  fetch("/Backend/PHP/schedule-meeting.php")          // GET = everything relevant
     .then(r=>r.json())
     .then(({own,requests})=>{
        render(listMeet,  own,       false);   // editable/delete
        render(listReq,   requests,  true );   // accept/decline
     })
     .catch(err=>console.error(err));
}

function render(target, arr, isRequest){
  target.innerHTML = "";
  arr.forEach(m=>{
      const li   = document.createElement("li");
      li.textContent = `${m.studentName} – ${m.date} @ ${m.time}`;
      // buttons
      const btnBox = document.createElement("span");
      if(isRequest){
         makeBtn(btnBox,"✔ Accept", ()=>decision(m.id,"accepted"));
         makeBtn(btnBox,"✖ Decline",()=>decision(m.id,"declined"));
      }else{
         makeBtn(btnBox,"✎ Edit",   ()=>alert("Implement edit UI"));
         makeBtn(btnBox,"🗑 Del",   ()=>decision(m.id,"delete"));
      }
      li.appendChild(btnBox);
      target.appendChild(li);
  });
}

function makeBtn(parent,text,cb){
  const b = document.createElement("button"); b.textContent=text; b.onclick=cb;
  b.style.marginLeft="8px"; parent.appendChild(b);
}

function decision(meetingId,action){
   const method = action==="delete" ? "DELETE" : "PUT";
   const body   = { id:meetingId, status:action };
   fetch("/Backend/PHP/schedule-meeting.php",{ method,
           headers:{"Content-Type":"application/json"},
           body:JSON.stringify(body)})
     .then(r=>r.json()).then(()=>loadEverything());
}
