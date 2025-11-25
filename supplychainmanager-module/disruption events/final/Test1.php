
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Manager Dashboard</title>
    <!--<link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
        rel="stylesheet"> -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
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
    
.disruption-summary {
  text-align: left 
}
.disruption-event-info {
  text-align: left 
}

   </style>
</head>
<body>
    <!-- Company header -->
    <h1>Global Electronics LLC</h1>

    <!-- Align 3 items -->
    <div class="container">
        <div class="row">

<div class="col-md-3">            <!-- Note this is our side bar and it takes up 3/12 of the page -->
<!--Integrate JavaScript to incorporate sidebar module -->
<div id="supplychainmanager_sidebar"></div>

<script>
  fetch('supplychainmanager_sidebar.html')
    .then(response => response.text())
    .then(html => document.getElementById('supplychainmanager_sidebar').innerHTML = html);
</script>
</div>

            <div class="col-md-9">          <!-- As a follow up to the commnet above, this is 9/12 of the page so as long as your html is in this part, then it will fit while also having
                                                 enough space for the sidebar. --> 
                <!-- Dashboard header -->
                <div class="card" id="dashboard-header">
                    <?php echo "{$user_FullName}'s SCM Dashboard" ?>
                </div>           
    <!-- NEW -->            <!--ask about labels -->    <!--submissions all at once/ on the same buttom click must all be withing the same form --> 
                <div class="container mt-4">
  <div class="row">
    <!-- Form Section -->
    <div class="col-md-6">
      <form>
        <div class="card mb-3 disruption-summary">  <!-- this is the card div idk if i love it subject to change-->
        <div class="mb-3">
<label for="DisruptionDropDown">Filter By:</label> <!-- How this is labeled allows it to be then found and stored as an ID later on-->
<select id="DisruptionDropDown" class="form-select mb-3">
  <option value="">Select Filter</option>
  <option value="company">Company Name</option>
  <option value="region">Region</option>
  <option value="regionTier">Region and Tier</option>
  <option value="tier">Tier</option>
</select>

<!-- Company filter -->
<div id="companyNameFilter" class="filter-group" style="display:none;">
  <label for="companyInput">Company Name:</label>
  <input type="text" id="companyInput" class="form-control" placeholder="Enter Company Name">
</div>

<!-- Region filter -->
<div id="regionChooser" class="filter-group" style="display:none;"> 
  <label for="regionSelect">Filter By:</label>
  <select id="regionSelect" class="form-select mb-3"> 
    <option value="">Select Continent or Country</option>
    <option value="continent">Continent</option>
    <option value="country">Country</option>
  </select>
</div>

<!-- Continent filter -->
<div id="continentFilter" class="filter-group" style="display:none;">
  <select id="continentSelect" class="form-select">
    <option value="" disabled selected>Select Continent</option>
    <option>Africa</option>
    <option>Antarctica</option>
    <option>Asia</option>
    <option>Australia/Oceania</option> 
    <option>Europe</option>
    <option>North America</option>
    <option>South America</option>
  </select>
</div>

<!-- Country filter -->
<div id="countryFilter" class="filter-group" style="display:none;"> 
  <label for="countryInput">Country Name:</label>
  <input type="text" id="countryInput" class="form-control mb-3" placeholder="Enter Country Name">
</div>

<!-- Tier filter -->
<div id="tierFilter" class="filter-group" style="display:none;">
  <label>Tier:</label>
  <select id="tierSelect" class="form-select">
    <option value="" disabled selected>Select Tier</option>
    <option>Tier 1</option>
    <option>Tier 2</option>
    <option>Tier 3</option>
  </select>
</div>

<!-- Region and Tier filter -->
<div id="regionTierFilter" class="filter-group" style="display:none;">
  <label>Tier:</label>
  <select id="regionTierSelect" class="form-select">
    <option value="" disabled selected>Select Tier</option>
    <option>Tier 1</option>
    <option>Tier 2</option>
    <option>Tier 3</option>
  </select>
</div>


        </div>

        <div class="mb-3">
       
        </div>

</div> <!-- this is the card /div idk if i love it subject to change-->
</div>
    <!-- This is on the outside of of the row specification, so that it can be to the right side -->
    <div class="col-md-6">
    <div class="card mb-3 disruption-summary">
      <div class="card-body">
        <div class="row mb-3">
          <div class="col">
            <label class="form-label">Start Date</label>
            <input type="date" class="form-control">
          </div>
          <div class="col">
            <label class="form-label">End Date</label>
            <input type="date" class="form-control">
          </div>
        </div>
      </div>
  </div>
    </div>
 <button type="submit" class="btn btn-primary">Submit</button>
      </form>
      
      



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
                    <div class="row">  <!-- bootstrap already defines row, so thats why we dont need it in our css-->
  <!-- Card 1  first 2 plots-->
  <div class="col-md-4">
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Disruption Frequency & Recovery</h5>
        <div class="plot">Plot #1: Disruption Frequency</div>
        <div class="plot">Plot #2: Average Recovery Time</div>
      </div>
    </div>
  </div>

  <!-- Card 2 second 2 plots-->
  <div class="col-md-4">
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Impact & Downtime</h5>
        <div class="plot">Plot #3: High Impact Disruption Rate</div>
        <div class="plot">Plot #4: Total Downtime</div>
      </div>
    </div>
  </div>
 
  <!-- Card 3 third 2 plots-->
  <div class="col-md-4">
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Regional & Severity</h5>
        <div class="plot">Plot #5: Regional Risk Concentration</div>
        <div class="plot">Plot #6: Severity Distribution</div>
      </div>
    </div>
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

<script>
    //allows values on the page to be stored as IDs so they can be found and easily kept track of
    //Note these lines will then point to which corresponding drop down the ID corresponds to. 
  const DisruptionDropDown = document.getElementById("DisruptionDropDown");  //const refers to a constant variable in javascript
  const companyNameFilter = document.getElementById("companyNameFilter");
  const regionChooser     = document.getElementById("regionChooser");
  const regionSelect      = document.getElementById("regionSelect");
  const continentFilter   = document.getElementById("continentFilter");
  const countryFilter     = document.getElementById("countryFilter");
  const tierFilter        = document.getElementById("tierFilter");
  const regionTierFilter  = document.getElementById("regionTierFilter");

  const companyInput      = document.getElementById("companyInput");
  const countryInput      = document.getElementById("countryInput");
  const continentSelect   = document.getElementById("continentSelect");
  const tierSelect        = document.getElementById("tierSelect");
  const regionTierSelect  = document.getElementById("regionTierSelect");

//addEventListener 
  DisruptionDropDown.addEventListener("change", function () {  // grouping drop downs into a function
    companyNameFilter.style.display = "none"; //display nothing (hiding stuff)
    regionChooser.style.display = "none";
    continentFilter.style.display = "none";
    countryFilter.style.display = "none";
    tierFilter.style.display = "none";
    regionTierFilter.style.display = "none";

    // resets like these are necesary, since if they are not included then swapping between region and region and tier will carry values causing errors and glitches. 
    companyInput.value = "";
    regionSelect.selectedIndex = 0; //setting the Index back to zero automatically selects the first value in the dropdown. Again necessary to avoid issues. Also looks nicer.
    continentSelect.selectedIndex = 0;
    countryInput.value = "";
    tierSelect.selectedIndex = 0;
    regionTierSelect.selectedIndex = 0;

//if statements
    if (this.value === "company") { //for example, if company is selected. (the ID) 
      companyNameFilter.style.display = "block";  //then that corresponding dropdown will be displayed
    }
    if (this.value === "region") {
      regionChooser.style.display = "block";
    }
    if (this.value === "tier"){
      tierFilter.style.display ="block";
    }
    if (this.value === "regionTier"){
      regionChooser.style.display ="block";
      regionTierFilter.style.display ="block";
    }
  });

  // region function, this is necessary because it helps eliminate errors when carrying over data about regions.
  //since we have both select by region and also select by region and tier. It can cause errors.
  regionSelect.addEventListener("change", function () {
    continentFilter.style.display = "none";
    countryFilter.style.display = "none";

    // reset choices
    continentSelect.selectedIndex = 0;
    countryInput.value = "";

    if (this.value === "continent") {
      continentFilter.style.display = "block";
    }
    if (this.value === "country") {
      countryFilter.style.display = "block";
    }
  });
</script>

<!-- Bootstrap JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- This is a single-line comment -->
</body>
</html>