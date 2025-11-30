document.addEventListener('DOMContentLoaded', function() {
    fetch('SCM_alerts_query.php')  // This should return your full JSON object
        .then(response => response.json())
        .then(data => {
            
            //Locate Alerts list
            const alerts_list = document.getElementById('alerts-list');
            alerts_list.innerHTML = '';
            
            //List off alerts
            data.Ongoing.forEach(event => {
                const div = document.createElement("div");
                let recovery = "Unknown";
                if(event.EventRecoveryDate != null){
                    recovery = event.EventRecoveryDate;
                }
                div.className = "list-item";
                div.innerHTML = `
                    <strong>Event ID:</strong> ${event.EventID}<br>
                    <strong>Cateogry Name:</strong> ${event.CategoryName}<br>
                    <strong>Event Date:</strong> ${event.EventDate}<br>
                    <strong>Est. Recovery Date:</strong> ${recovery}<br>
                `;
                div.style = "font-size: 14px; border-left: 5px solid #d9534f;"
                alerts_list.appendChild(div);
            });

        });
});