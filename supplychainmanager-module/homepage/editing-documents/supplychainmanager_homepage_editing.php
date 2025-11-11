<?php
session_start();

//**Check if the user is NOT logged in** (Security Check)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    //If not logged in, redirect to the login page
    header("Location: login.php");
    exit;
}

//If the code reaches here, the user has been authenticated.
$user_FullName = $_SESSION['FullName'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Manager Dashboard</title>
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
        rel="stylesheet">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">
    <style>
    @import "standardized_project_formatting.css";

    .bubble{
        display: inline-block;
        padding: 15px 25px;
        border-radius: 20px;
        color: white;
        font-weight: bold;
        margin: 25px;
    }
    .red{background-color: red;}
    .blue{background-color: blue;}
    
    .button{
        display: inline-block;
        background-color: #26b0b9;
        color: white;
        padding: 8px 14px;
        border-radius: 6px;
        margin-top: 15px;
        margin-left: -475px;
        text-decoration: none;
    }

    .plots{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 30px;
    }

    .plot{
        margin: 15px;
        text-align: center;
    }

    .plot img{
        width: 150px;
        height: 200px;
        background-color: white; /* Placeholder */
    }


    
    /* Column 2 - Dasboard Header */
    #dashboard-header{
        background-color: #ececec;  /* CHANGED: from #c0bebe → softer gray */
        width: auto;
        height: auto;
        font-family: Cambria, serif; /* sets pretty font */
        font-size: 30px;
        color: #222;                /* NEW: darker text */
        padding: 15px;              /* NEW: adds balance to main panel */
        border-radius: 6px;         /* NEW: smoother shape */
        text-align: center;         /* NEW: explicitly centers dashboard text */
    }


   </style>
</head>
<body>
    <!-- Company header -->
    <h1>Global Electronics LLC</h1>

    <!-- Align 3 items -->
    <div class="container">
        <div class="row">

<div class="col-md-3">            
<!--Integrate JavaScript to incorporate sidebar module -->
<div id="supplychainmanager_sidebar"></div>
<script>
  fetch('supplychainmanager_sidebar.html')
    .then(response => response.text())
    .then(html => document.getElementById('supplychainmanager_sidebar').innerHTML = html);
</script>
</div>

            <div class="col-md-9">           
                <!-- Dashboard header -->
                <div class="card" id="dashboard-header">
                    <?php echo "{$user_FullName}'s SCM Dashboard" ?>
                </div>
                <div> <!-- Insert cards here -->
                    <form action = "#" method = "post" name="CompanyForm">
                        <label>Type Company Name</label>
                        <input type="text" class="form-control" name="CompanyName">
                        <button type="button" onclick="PullFromPHP()" class="btn btn-primary">Submit</button>
                	</form>
                <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Basic Company Info</h5>
                    <h6 class="card-body" id="type">Type: </h6>
                    <h6 class="card-body" id="tier">Tier: </h6>
                    <h6 class="card-body" id="score">Financial Health Score: </h6>
                </div>
                </div>

                <div class="plots">
                    <div class="plot">
                        <img src="supply_chain_plot_temp.png" width=250 height=250 alt="Supply Chain Plot 1">
                        <p>Plot #1</p>
                    </div>
                    <div class="plot">
                        <img src="supply_chain_plot_temp.png" width=250 height=250 alt="Supply Chain Plot 2">
                        <p>Plot #2</p>
                    </div>
                    <div class="plot">
                        <img src="supply_chain_plot_temp.png" width=250 height=250 alt="Supply Chain Plot 3">
                        <p>Plot #3</p>
                    </div>
                    <div class="plot">
                        <img src="supply_chain_plot_temp.png" width=250 height=250 alt="Supply Chain Plot 4">
                        <p>Plot #4</p>
                    </div>
                </div></div></div>
            </div>  
            <!-- End of page divs -->
        </div>
    </div>
</body>

<script>
function PullFromPHP() {
    const input = document.CompanyForm.CompanyName.value;
    xhtpp = new XMLHttpRequest();
    xhtpp.onload = function(){
      if (this.readyState == 4 && this.status == 200) {
        //document.getElementById("type").innerHTML = this.responseText;
        const object = JSON.parse(this.responseText);
        index_zero = object[0];
        document.getElementById("type").innerHTML = "Type:" + " " + index_zero.Type;
        document.getElementById("tier").innerHTML = "Tier:" + " " + index_zero.TierLevel;
        document.getElementById("score").innerHTML = "Financial Health Score:" + " " + index_zero.HealthScore;
      }
  };
    xhtpp.open("GET", "supplychainmanager_homepage_queries.php?q=" + input, true);
    xhtpp.send();
}
</script>
</html>




<!-- This is a single-line comment -->
</body>

</html>
