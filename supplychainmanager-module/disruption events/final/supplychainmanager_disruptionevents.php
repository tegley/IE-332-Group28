<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Manager Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"> <!-- Bootstrap icons library -->
    <script src="https://cdn.plot.ly/plotly-3.3.0.min.js" charset="utf-8"></script> <!-- JavaScript for Plotly -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> <!-- JavaScript for tab navigation -->

    <style>
    @import "standardized_project_formatting.css";
    .filter-group { margin-bottom: 12px; }
    .col-md-9 {
      position: relative; 
    }

    #fixed-container {
        position: absolute;    
        top: 385px;
        left: 0;
        right: 0;
        margin-left: 8px;  /*Adjust this value if needed (e.g., to -15px) */
        margin-right: 8px; /* Adjust this value if needed (e.g., to -15px) */
    }

    #top-position-spacer {
        height: 0; /* Occupies no height */
        margin-top: 325px; /* Pushes the next element 400px down */
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
        <!-- SCM Dashboard Header -->
        <div class="card" id="dashboard-header">
            <?php echo "{$user_FullName}'s SCM Dashboard" ?>
        </div>

        <form name="DisruptionDropDown">
        <label for="DisruptionDropDown">Filter By:</label> <!-- How this is labeled allows it to be then found and stored as an ID later on <body class="p-4">-->
        <select id="DisruptionDropDown" class="form-select mb-3">
          <option value="">Select Filter</option>
          <option value="company">Company Name</option>
          <option value="region">Region</option>
          <option value="tier">Tier</option>
          <option value="regionTier">Region and Tier</option>
        </select>

        <!-- Company filter -->
        <div id="companyNameFilter" class="filter-group" style="display:none;">
          <label for="companyInput">Company Name:</label>
          <input type="text" id="companyInput" class="form-control" placeholder="Enter Company Name">
        </div>

        <!-- Region filter -->
        <div id="regionChooser" class="filter-group" style="display:none;"> 
          <label for="regionSelect">Choose Region Type:</label>
          <select id="regionSelect" class="form-select mb-3"> 
            <option value="" disabled selected>Select Continent or Country</option>
            <option value="continent">Continent</option>
            <option value="country">Country</option>
          </select>
        </div>

        <!-- Continent filter -->
        <div id="continentFilter" class="filter-group" style="display:none;">
          <select id="continentSelect" class="form-select">
            <option value="" disabled selected>Select Continent</option>
            <option>Africa</option>
            <option>Asia</option>
            <option>Oceania</option> 
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
          <label for="tierSelect">Tier:</label>
          <select id="tierSelect" class="form-select">
            <option value="" disabled selected>Select Tier</option>
            <option>Tier 1</option>
            <option>Tier 2</option>
            <option>Tier 3</option>
          </select>
        </div>

        <!-- Region and Tier filter -->
        <div id="regionTierFilter" class="filter-group" style="display:none;">
          <label for="regionTierSelect">Tier:</label>
          <select id="regionTierSelect" class="form-select">
            <option value="" disabled selected>Select Tier</option>
            <option>Tier 1</option>
            <option>Tier 2</option>
            <option>Tier 3</option>
          </select>
        </div>
        </form>
        
        <div id="fixed-container" class="container-fluid">
          <div class="row">
            <div class="col-md-5">
              <label for="startDate">Start Date:</label>
              <input type="date" class="form-control" id="startDate">
            </div>
            <div class="col-md-5">
              <label for="endDate">End Date:</label>
              <input type="date" class="form-control" id="endDate">
            </div>
            <div class="col-md-2 d-flex align-items-end justify-content-center">
              <button type="button" class="btn btn-primary" onclick="CheckUserInput()">Search</button>
            </div>
          </div>
        </div>
        
        <div id="top-position-spacer"></div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button
              class="nav-link active"
              id="df-tab"
              data-bs-toggle="tab"
              data-bs-target="#df"
              type="button"
              role="tab"
              aria-controls="df"
              aria-selected="true"
            >
              DF
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              id="dsd-tab"
              data-bs-toggle="tab"
              data-bs-target="#dsd"
              type="button"
              role="tab"
              aria-controls="dsd"
              aria-selected="false"
            >
              DSD
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              id="hdr-tab"
              data-bs-toggle="tab"
              data-bs-target="#hdr"
              type="button"
              role="tab"
              aria-controls="hdr"
              aria-selected="false"
            >
              HDR
            </button>
          </li>
        </ul>

        <div class="tab-content" id="myTabContent">
          <div
            class="tab-pane fade show active"
            id="df"
            role="tabpanel"
            aria-labelledby="df-tab"
          >
            <h3>DF Bar Chart</h3>
            <div id="df-bar-chart"> </div>
          </div>

          <div
            class="tab-pane fade"
            id="dsd"
            role="tabpanel"
            aria-labelledby="dsd-tab"
          >
            <h3>DSD Stacked Bar Chart</h3>
            <div id="dsd-stackedbar-chart"> </div>

          </div>

          <div
            class="tab-pane fade"
            id="hdr"
            role="tabpanel"
            aria-labelledby="hdr-tab"
          >
            <h3>HDR Pie Chart</h3>
            <div id="hdr-pie-chart"> </div>
          </div>
        </div>
        
        <!-- JavaScript for dropdown filter appearance space minimization -->
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
            //  logging the selected value
            //console.log("DisruptionDropDown selected:", this.value);
            companyNameFilter.style.display = "none"; //display nothing (hiding stuff)
            regionChooser.style.display = "none";
            continentFilter.style.display = "none";
            countryFilter.style.display = "none";
            tierFilter.style.display = "none";
            regionTierFilter.style.display = "none";

            // resets like these are necesary, since if they are not included then swapping between region and region and tier will carry values causing errors and glitches. 
            companyInput.value = "";
            regionSelect.selectedIndex = 0; //setting the Index back to zero automatically selects the first value in the dropdown. Again necessary to avoid issues.
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
            //console.log("RegionSelect selected:", this.value);
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

      </div> <!-- Closes col-md-9 -> add divs above this line!! -->

    </div> <!-- Row -->
  </div> <!-- Container -->
</body>

<script>
function CheckUserInput() {
  let DisruptionDropDown = document.getElementById("DisruptionDropDown").value;
  if(DisruptionDropDown == "") {
    alert("Please select a filter!")
  }
  if(DisruptionDropDown == "region") {
    const region_selection = document.getElementById("regionSelect").value;
    if(region_selection == "") {
      alert("Please select a region type!");
      document.getElementById("regionSelect").focus();
      return false;
    }
    if(region_selection == "country") { //Option 1 - Country only
      const country_input = document.getElementById("countryInput").value;
      if (country_input == "") {
        alert("Please enter a country!");
        document.getElementById("countryInput").focus();
        return false;
      }
      DisruptionDropDown = "country";
      user_input = country_input + "," + "";
    }
    if(region_selection == "continent") { //Option 2 - Continent only
      const continent_input = document.getElementById("continentSelect").value;
      if (continent_input == "") {
        alert("Please select a continent!");
        document.getElementById("continentSelect").focus();
        return false;
      }
      DisruptionDropDown = "continent";
      user_input = continent_input + "," + "";
    }
  }
  if(DisruptionDropDown == "company") { //Option 3 - Company name
    const company_input = document.getElementById("companyInput").value;
    if (company_input == "") { 
      alert("Please provide a company!");
      document.getElementById("companyInput").focus();
      return false;
    }
    user_input = company_input + "," + "";
  }
  if(DisruptionDropDown == "tier") { //Option 4 - Tier level
    let tier_input = document.getElementById("tierSelect").value;
    if (tier_input == "") { 
      alert("Please select a tier!");
      document.getElementById("tierSelect").focus();
      return false;
    }
    switch (tier_input) {
      case "Tier 1": tier_input = "1"; break;
      case "Tier 2": tier_input = "2"; break;
      case "Tier 3": tier_input = "3"; break;
    }
    user_input = tier_input + "," + "";
  }
  if(DisruptionDropDown == "regionTier") {
    const region_selection = document.getElementById("regionSelect").value;
    const tier_input = document.getElementById("regionTierSelect").value;
    if(region_selection == "" && tier_input =="") {
      alert("Please select a region and tier!");
      document.getElementById("regionSelect").focus();
      return false;
    }
    else if(region_selection == "") {
      alert("Please select a region type!");
      document.getElementById("regionSelect").focus();
      return false;
    }
    else if(tier_input == "") {
      alert("Please select a tier!");
      document.getElementById("regionTierSelect").focus();
      return false;
    }
    else if(region_selection =="country"){ //Option 5 - Country & tier
      const country_input = document.getElementById("countryInput").value;
      if (country_input == "") {
        alert("Please enter a country!");
        document.getElementById("countryInput").focus();
        return false;
      }
      DisruptionDropDown = "country-tier";
      user_input = country_input + "," + tier_input;
    }
    else if(region_selection =="continent"){ //Option 6 - Continent & tier
      const continent_input = document.getElementById("continentSelect").value;
      if (continent_input == "") {
        alert("Please enter a continent!");
        document.getElementById("continentSelect").focus();
        return false;
      }
      DisruptionDropDown = "continent-tier";
      user_input = continent_input + "," + tier_input;
    }
  }
  //If user input is valid
  const date_result = CheckDates()
  if (date_result) {
    // If result is not false, use destructuring assignment to get the dates
    const [start_date, end_date] = date_result;    
    //console.log("Valid Dates:", start_date, end_date);
    DisruptionEventsAJAX(DisruptionDropDown, user_input, start_date, end_date);

  } else {
    //console.log("Date check failed.");
  }
}

function CheckDates(){
  const start_date = document.getElementById("startDate").value; 
  const end_date = document.getElementById("endDate").value;

  //Check for date input
  if (start_date == "" || end_date == "") {
      alert("Please provide date range!");
      return false;
  }

  //Verify start date is before end date
  if (start_date >= end_date) {
      alert("Start date must be before end date!");
      return false;
  }
  return [start_date, end_date];
}

var my_JSON_object;
function DisruptionEventsAJAX(DisruptionDropDown, user_input, start_date, end_date) {
    q_input = user_input;
    g_input = DisruptionDropDown + "," + start_date + "," + end_date;
    console.log(q_input);
    console.log(g_input);
    
    xhtpp = new XMLHttpRequest();
    xhtpp.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
          //console.log(this.responseText);
          my_JSON_object = JSON.parse(this.responseText);
          console.log(JSON.stringify(my_JSON_object));

          //DF Bar chart
          const df_companies = my_JSON_object.DF.map((item) => { return String(item.CompanyName) })
          const df_values = my_JSON_object.DF.map((item) => { return item.Total });
          CreateDSDBarChart(df_companies, df_values);

          //Financial Health line chart example
          //const x_vals = my_JSON_object.pastHealthScores.map((item) => { return String(item.Quarter + " " + item.RepYear) }).map(String).reverse()
          //const y_vals = my_JSON_object.pastHealthScores.map((item) => { return item.HealthScore }).map(Number).reverse();
          };
        }
    const url = "supplychainmanager_disruptionevents_queries.php?q=" + encodeURIComponent(q_input) + "&g=" + encodeURIComponent(g_input);
    xhtpp.open("GET", url, true);
    xhtpp.send();
}

function CreateDSDBarChart(df_companies, df_values){
  
  var layout = {
      title: {
          text: 'Disruption Frequency Bar Chart'
      },
      xaxis: {
          title: {
              text: 'Companies'
          }
      },
      yaxis: {
          title: {
              text: 'DF'
          }
      }
  };

  const BarChart = document.getElementById('df-bar-chart');
  
  var data = [
  {
    x: df_companies,
    y: df_values,
    type: 'bar'
  }
  ];

  Plotly.newPlot(BarChart, data, layout);
}

</script>
</html>
