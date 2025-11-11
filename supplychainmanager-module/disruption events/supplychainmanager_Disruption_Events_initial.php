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

    #scroll-format {
  		max-height: 250px;         /* set desired height */
  		overflow-x: hidden;        /* hide horizontal scrollbar */
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
                <form action = "#" method = "post" name="DRForm">
                    <label>Type Company, Region, Tier, Time Period</label>
                    <input type="text" class="form-control" name="DRName"> <!-- search by company, region, tier, over period of time -->
                    <button type="button" onclick="PullFromPHP()" class="btn btn-primary">Submit</button>
                </form>
                <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Disruption Event Info</h5>
                    <h6 class="card-body" id="disruption frequency">Disruption Frequency: </h6>
                    <h6 class="card-body" id="total downtime">Total Downtime: </h6>
                </div>
                </div>

                <div class="plots">
                    <div class="plot">
                        <img src="disruption_frequency_plot_temp.png" width=250 height=250 alt="Disruption Frequency Plot (DF)">
                        <p>Plot #1</p>
                    </div>
                    <div class="plot">
                        <img src="average_recovery_time_plot_temp.png" width=250 height=250 alt="Average Recovery Time Plot (ART)">
                        <p>Plot #2</p>
                    </div>
                    <div class="plot">
                        <img src="high_impact_disruption_rate_plot_temp.png" width=250 height=250 alt="High Impact Disruption Rate Plot (HDR)">
                        <p>Plot #3</p>
                    </div>
                    <div class="plot">
                        <img src="total_downtime_plot_temp.png" width=250 height=250 alt="Total Downtime Plot (TD)">
                        <p>Plot #4</p>
                    </div>
                    <div class="plot">
                        <img src="regional_risk_concentration_plot_temp.png" width=250 height=250 alt="Regional Risk Concentration Plot (RRC)">
                        <p>Plot #5</p>
                    </div>
                    <div class="plot">
                        <img src="disruption_severity_distribution_plot_temp.png" width=250 height=250 alt="Disruption Severity Distribution Plot (DSD)">
                        <p>Plot #6</p>
                    </div>

                </div>
                <body data-bs-spy="scroll" data-bs-target="#navbar-example3" data-bs-offset="0" tabindex="0">

                <div class="container-fluid mt-4">
                <div class="row">

                    <!-- 🧭 Sidebar Navigation -->
                    <div class="col-3 border-end">
                    <nav id="navbar-example3" class="h-100 flex-column align-items-stretch pe-3">
                        <nav class="nav nav-pills flex-column">
                        <a class="nav-link" href="#basic-info">Basic Info</a>
                        <a class="nav-link" href="#dependencies">Dependencies</a>
                        <a class="nav-link" href="#products">Products</a>
                        <a class="nav-link" href="#transactions">Transactions</a>
                        </nav>
                    </nav>
                    </div>

                    <!-- Main Content Area -->
                    <div class="col-9 scrollspy-example">

                    <!-- 🔹 Section 1: Basic Info -->
                    <div id="basic-info" class="mb-5">
                        <h1 class="display-6">Basic Disruption Event Info</h1>

                        <div class="form-group mb-3">
                        <label for="companyName">Disruption Name</label>
                        <input type="text" class="form-control" id="companyName" placeholder="Enter Name">
                        </div>

                        <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Disruption Event Name</h5>
                            <h6 class="card-subtitle mb-2 text-body-secondary">User input name</h6>
                            <h6 class="card-body">Company Affected:</h6>
                            <h6 class="card-body">Region:</h6>
                            <h6 class="card-body">Tier:</h6>
                            <h6 class="card-body">Time Period:</h6>
                        </div>
                        </div>
                    </div>

                    <!-- 🔹 Section 2: new/ongoing alerts -->
<!-- This part would be based on real-time data and should disappear when no alerts are present (not yet implemented) -->
                    <div id="transactions" class="mb-5">
                        <h1 class="display-6">Alerts !!!</h1>

                        <div class="row">
                        <div class="col-md-6">
                            <h6 class="card-body">On-going Alerts</h6>
                            <div class="card">
                            <ul class="list-group list-group-flush" id="scroll-format">
                                <li class="list-group-item">Disruption Event:</li>
                                <li class="list-group-item">Company Affected:</li>
                                <li class="list-group-item">Region:</li>
                                <li class="list-group-item">Tier:</li>
                            </ul>
                            </div>
                        </div>
                    </div>

                    <!-- 🔹 Section 3: Transactions -->
                    <div id="transactions" class="mb-5">
                        <h1 class="display-6">Transactions</h1>
                        <h5>Shipment Volume</h5>
                        <div class="card">
                        <div class="card-body">
                        <ul class="list-group list-group-flush" id="scroll-format">
                        <p> Company's Shipments within Date Range </p>
                            <li class="list-group-item">Shipment X</li>
                            <li class="list-group-item">Shipment Y</li>
                            <li class="list-group-item">Shipment Z</li>
                        </ul>
                        </div>
                    </div>
                    <h5>On-time delivery rate</h5>
                        <div class="card">
                        <div class="card-body">
                        <ul class="list-group list-group-flush" id="scroll-format">
                        <p> Company's Receivings within Date Range </p>
                            <li class="list-group-item">rate X</li>
                            <li class="list-group-item">rate Y</li>
                            <li class="list-group-item">rate Z</li>
                        </ul>
                        </div>
                    </div>
                    <h5>Shipments currently out</h5>
                        <div class="card">
                        <div class="card-body">
                        <ul class="list-group list-group-flush" id="scroll-format">
                        <p> Company's Adjustments within Date Range </p>
                            <li class="list-group-item">Ship X</li>
                            <li class="list-group-item">Ship Y</li>
                            <li class="list-group-item">Ship Z</li>
                        </ul>
                        </div>
                    </div>
                    <h5>Products handled</h5>
                        <div class="card">
                        <div class="card-body">
                        <ul class="list-group list-group-flush" id="scroll-format">
                        <p> Company's Adjustments within Date Range </p>
                            <li class="list-group-item">Product X</li>
                            <li class="list-group-item">Product Y</li>
                            <li class="list-group-item">Product Z</li>
                        </ul>
                        </div>
                    </div>
                    <h5>Disruption exposures</h5>
                        <div class="card">
                        <div class="card-body">
                        <ul class="list-group list-group-flush" id="scroll-format">
                        <p> Company's Adjustments within Date Range </p>
                            <li class="list-group-item">exposures X</li>
                            <li class="list-group-item">exposures Y</li>
                            <li class="list-group-item">exposures Z</li>
                        </ul>
                        </div>
                    </div>
                    </div>

                    </div>


                    <!-- 🔹 Section 4: Useful Plots -->
                    <div class="plots">
                        <div class="plot">
                        <img src="on-time_delivery_rate_plot_temp.png" alt="Plot 1">
                        <p>Useful plot #1</p>
                        </div>
                        <div class="plot">
                        <img src="products_handled_plot_temp.png" alt="Plot 2">
                        <p>Useful plot #2</p>
                        </div>
                        <div class="plot">
                        <img src="disruption_exposure_plot_temp.png" alt="Plot 3">
                        <p>Useful plot #3</p>
                        </div>
                        <div class="plot">
                        <img src="shipment_volume_plot_temp.png" alt="Plot 4">
                        <p>Useful plot #4</p>
                        </div>
                    </div>

                    </div>
                </div>
                </div>
            
            </div> <!-- Closes col-md-9 -> add divs above this line!! -->

        </div> <!-- Row -->
    </div> <!-- Container -->
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
