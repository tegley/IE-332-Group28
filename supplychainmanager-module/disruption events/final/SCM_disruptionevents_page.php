<?php
session_start();

//Check if the user is NOT logged in (security measure)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo "<h1>Unauthorized Login</h1>";
    echo "<p>Please visit the <a href='index.php'>login page</a>!</p>";
    exit();
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"> <!-- Bootstrap icons library -->
    <script src="https://cdn.plot.ly/plotly-3.3.0.min.js" charset="utf-8"></script> <!-- JavaScript for Plotly -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> <!-- JavaScript for tab navigation -->
    <script src="SCM_display_alerts.js"></script>

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
        margin-left: 5px;
        margin-right: 5px;
        margin-top: 2px;
    }

    #myTab {
        margin-top: 15px;
    }

    .my-table td:first-child {
      border-right: 1px solid #000;
    }

    .my-table{
      text-align: center;
      padding: 0.5rem 1rem;
      margin: 5px;
    }

    .list-item {
      padding: 8px;
      margin: 4px 0;
      background: #f8f9fa;
      border-radius: 4px;
    }

    .scroll-box {
        height: 120px;
        overflow-y: auto;
        border: 1px solid #aaa;
        padding: 10px;
        border-radius: 6px;
        background-color: white;
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
            <select id="companyInput" class="form-select mb-2">
              <option value="">Loading companies...</option>
            </select>
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
            <select id="countryInput" class="form-select mb-3">
              <option value="">Loading countries...</option>
            </select>
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

        <!-- Region and Tier filter  <div id="top-position-spacer"></div> -->
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
        
          <div id="StatusMessage" class="mt-3">
          </div>
          
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
            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                id="art-td-tab"
                data-bs-toggle="tab"
                data-bs-target="#art-td"
                type="button"
                role="tab"
                aria-controls="art-td"
                aria-selected="false"
              >
                ART & TD
              </button>
            </li>

            <li class="nav-item" role="presentation">
              <button
                class="nav-link"
                id="rrc-tab"
                data-bs-toggle="tab"
                data-bs-target="#rrc"
                type="button"
                role="tab"
                aria-controls="rrc"
                aria-selected="false"
              >
                RRC
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
              <div class="card mb-2 w-100" id="df-bar-chart" style="height: 525px"> </div>
            </div>

            <div
              class="tab-pane fade"
              id="dsd"
              role="tabpanel"
              aria-labelledby="dsd-tab"
            >
              <div class="card mb-2 w-100" id="dsd-stackedbar-chart" style="height: 525px"> </div>
            </div>

            <div
              class="tab-pane fade"
              id="hdr"
              role="tabpanel"
              aria-labelledby="hdr-tab"
            >
              <div class="card">
                <div class="card-body row">

                  <div class="col-md-6">
                    <div class="card">
                      <div class="card-header">HDR Values by Company</div>
                        <div class="card-body">
                          <div class="scroll-box" id="hdr-company-values">
                            <p class="text-muted">Submit query to see results...</p>
                          </div>
                        </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="card">
                      <div class="card-header">Overall HDR Statistic</div>
                        <ul class="list-group list-group-flush" id="overall-hdr-statistic">
                          <p class="text-muted">Submit query to see results...</p>
                        </ul>
                    </div>
                  </div>

                </div>
              </div>
              <div id="hdr-chart-card"> 
                <div class="card mb-2 w-100" id="hdr-pie-chart" style="height: 525px"> </div>
              </div>
            </div>

            <div
              class="tab-pane fade"
              id="art-td"
              role="tabpanel"
              aria-labelledby="art-td-tab"
            >
              <div class="row g-0">
                <div class="col-6 p-0">
                    <table class="table my-table" style="border-right: 1px solid #666;">
                        <tr><td>Total Downtime</td><td id="OverallTD">--</td></tr>
                    </table>
                </div>
                
                <div class="col-6 p-0">
                    <table class="table my-table" style="border-left: 1px solid #666;">
                        <tr><td>Average Recovery Time</td><td id="OverallART">--</td></tr>
                    </table>
                </div>
              </div>
              <div class="row g-0" id="art-td-card">
                <div class="card mb-2 w-100" id="art-td-histogram-chart" style="height: 525px"> </div>
              </div>
            </div>

            <div
              class="tab-pane fade"
              id="rrc"
              role="tabpanel"
              aria-labelledby="rrc-tab"
            >
              <div class="card mb-2 w-100" id="rrc-heatmap-chart" style="height: 525px"> </div>
            </div>

          </div> <!-- End tab content -->
        </div>
      </div> <!-- Closes col-md-9 -> add divs above this line!! -->

    </div> <!-- Row -->
  </div> <!-- Container -->

<script> //Populate dropdown menus
document.addEventListener('DOMContentLoaded', function() {
    LoadDropdowns();
});
</script>

<script> //JavaScript for dropdown filter appearance & space minimization
  //Allows values on the page to be stored as IDs so they can be found and easily kept track of
  //Note these lines will then point to which corresponding drop down the ID corresponds to. 
  const DisruptionDropDown = document.getElementById("DisruptionDropDown"); 
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
    //Hide all filters
    companyNameFilter.style.display = "none";
    regionChooser.style.display = "none";
    continentFilter.style.display = "none";
    countryFilter.style.display = "none";
    tierFilter.style.display = "none";
    regionTierFilter.style.display = "none";

    //Reset dropdown values to prevent errors and glitches.
    companyInput.selectedIndex = 0;
    regionSelect.selectedIndex = 0;
    continentSelect.selectedIndex = 0;
    countryInput.selectedIndex = 0;
    tierSelect.selectedIndex = 0;
    regionTierSelect.selectedIndex = 0;

//If statements to control which dropdown is selected
    if (this.value === "company") { 
      companyNameFilter.style.display = "block";
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

  //Region function, this is necessary because it helps eliminate errors when carrying over data about regions.
  //Since we have both select by region and also select by region and tier -> we need to avoid errors
  regionSelect.addEventListener("change", function () {
    continentFilter.style.display = "none";
    countryFilter.style.display = "none";

    continentSelect.selectedIndex = 0;
    countryInput.selectedIndex = 0;

    if (this.value === "continent") {
      continentFilter.style.display = "block";
    }
    if (this.value === "country") {
      countryFilter.style.display = "block";
    }
  });
</script>

<script> //JavaScript for resizing Plotly graphs
//Ensure graphs are properly sized when other tabs are clicked
//Achieve this by looping through all tabs and triggering the autosize function for all tabs
const tabElms = document.querySelectorAll('button[data-bs-toggle="tab"]');

// Loop through each tab element
tabElms.forEach(tabElm => {
    // Add an event listener for when the tab is fully shown
    // 'shown.bs.tab' is the Bootstrap event that fires *after* the content is visible
    tabElm.addEventListener('shown.bs.tab', event => {
        
        const targetTabId = event.target.getAttribute('data-bs-target');
        
        //If the tab is active, resize the chart to fit to card dimensions
        if (targetTabId === '#dsd') {
            const chartContainer = document.getElementById('dsd-stackedbar-chart');
            if (chartContainer) {
                Plotly.relayout(chartContainer, { autosize: true });
            }
        } 
        else if (targetTabId === '#df') {
            const chartContainer = document.getElementById('df-bar-chart');
            if (chartContainer) {
                Plotly.relayout(chartContainer, { autosize: true });
            }
        }
        else if (targetTabId === '#hdr') {
            const chartContainer = document.getElementById('hdr-pie-chart');
            if (chartContainer) {
                Plotly.relayout(chartContainer, { autosize: true });
            }
        }
        else if (targetTabId === '#art-td') {
            const chartContainer = document.getElementById('art-td-histogram-chart');
            if (chartContainer) {
                Plotly.relayout(chartContainer, { autosize: true });
            }
          }
        else if (targetTabId === '#rrc') {
            const chartContainer = document.getElementById('rrc-heatmap-chart');
            if (chartContainer) {
                Plotly.relayout(chartContainer, { autosize: true });
            }
          }
    });
});

//Prevent height compression on first active tab
const activePane = document.querySelector('.tab-pane.show.active');
if (activePane) {
  //Find the chart container inside that active pane
  const ChartContainer = activePane.querySelector('[id$="-chart"]');

  if (ChartContainer) {
    //Pass the DOM element to Plotly
    Plotly.relayout(ChartContainer, { autosize: true });
  }
}
</script>

</body> <!-- End Document -->
<script> //JavaScript functions
function LoadDropdowns() {
    //Fetch data from your PHP file that returns all the data
    fetch('distributorList.php')
        .then(response => response.json())
        .then(data => {
            //Populate companies dropdown
            const companyDropdown = document.getElementById('companyInput');
            companyDropdown.innerHTML = '';
            
            const defaultCompanyOption = document.createElement('option');
            defaultCompanyOption.value = '';
            defaultCompanyOption.textContent = 'Select a company';
            companyDropdown.appendChild(defaultCompanyOption);
            
            data.company.forEach(company => {
                const option = document.createElement('option');
                option.value = company.CompanyName;
                option.textContent = company.CompanyName;
                companyDropdown.appendChild(option);
            });
            
            //Populate the countries dropdowns
            const countryDropdown = document.getElementById('countryInput');
            countryDropdown.innerHTML = '';
            
            const defaultCountryOption = document.createElement('option');
            defaultCountryOption.value = '';
            defaultCountryOption.textContent = 'Select a country';
            countryDropdown.appendChild(defaultCountryOption);

            data.country.forEach(country => {
                const option = document.createElement('option');
                option.value = country.CountryName;
                option.textContent = country.CountryName;
                countryDropdown.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading data:', error);
        });
}

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
      user_input = country_input + "|" + "";
    }
    if(region_selection == "continent") { //Option 2 - Continent only
      const continent_input = document.getElementById("continentSelect").value;
      if (continent_input == "") {
        alert("Please select a continent!");
        document.getElementById("continentSelect").focus();
        return false;
      }
      DisruptionDropDown = "continent";
      user_input = continent_input + "|" + "";
    }
  }
  if(DisruptionDropDown == "company") { //Option 3 - Company name
    const company_input = document.getElementById("companyInput").value;
    if (company_input == "") { 
      alert("Please provide a company!");
      document.getElementById("companyInput").focus();
      return false;
    }
    user_input = company_input + "|" + "";
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
    user_input = tier_input + "|" + "";
  }
  if(DisruptionDropDown == "regionTier") {
    const region_selection = document.getElementById("regionSelect").value;
    let tier_input = document.getElementById("regionTierSelect").value;
    switch (tier_input) {
      case "Tier 1": tier_input = "1"; break;
      case "Tier 2": tier_input = "2"; break;
      case "Tier 3": tier_input = "3"; break;
      default: tier_input = ""; 
    }
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
      user_input = country_input + "|" + tier_input;
    }
    else if(region_selection =="continent"){ //Option 6 - Continent & tier
      const continent_input = document.getElementById("continentSelect").value;
      if (continent_input == "") {
        alert("Please enter a continent!");
        document.getElementById("continentSelect").focus();
        return false;
      }
      DisruptionDropDown = "continent-tier";
      user_input = continent_input + "|" + tier_input;
    }
  }
  //If user input is valid
  const date_result = CheckDates()
  if (date_result) {
    const [start_date, end_date] = date_result;    
    DisruptionEventsAJAX(DisruptionDropDown, user_input, start_date, end_date);

  } else {
    console.log("Date check failed.");
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
    g_input = DisruptionDropDown + "|" + start_date + "|" + end_date;
    
    xhtpp = new XMLHttpRequest();
    xhtpp.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
          const error_check = this.responseText; //Temporarily store query results
          console.log(error_check);
          
          let status = true; //Variable to determine if charts should be executed
          try {
            my_JSON_object = JSON.parse(this.responseText); //Attempt to parse JSON object
          } catch (error) { //If there is an error, it means that a component of the user input was not found in the database
            console.error("An error occurred:", error.message);
            status = false; //In the case of an error, the charts should not be executed and there is no need to check for null data
            if(error_check=="Error country") {
              document.getElementById("StatusMessage").innerHTML = '<div class="alert alert-warning">Selected country is not in the database</div>';
            }
            if(error_check=="Error continent") {
              document.getElementById("StatusMessage").innerHTML = '<div class="alert alert-warning">Selected continent is not in the database</div>';
            }
            if(error_check=="Error company") {
              document.getElementById("StatusMessage").innerHTML = '<div class="alert alert-warning">Selected company is not in the database</div>';
            }
            if(error_check=="Error tier") {
              document.getElementById("StatusMessage").innerHTML = '<div class="alert alert-warning">There are no companies at the specified tier</div>';
            }
            if(error_check=="Error company country") {
              document.getElementById("StatusMessage").innerHTML = '<div class="alert alert-warning">There are no companies residing in the selected country</div>';
            }
            if(error_check=="Error company continent") {
              document.getElementById("StatusMessage").innerHTML = '<div class="alert alert-warning">There are no companies residing in the selected continent</div>';
            }
            if(error_check=="Error country tier") {
              document.getElementById("StatusMessage").innerHTML = '<div class="alert alert-warning">There are no companies at the specified tier residing in the selected country</div>';
            } 
            if(error_check=="Error continent tier") {
              document.getElementById("StatusMessage").innerHTML = '<div class="alert alert-warning">There are no companies at the specified tier residing in the selected continent</div>';
            }
          } finally {
            if(status==true) { //In case that data output is returned, perform initial check on data
              const null_check = my_JSON_object.TD_overall[0]["TD"]; //Account for case where there are no disruption events within time range
              if(null_check == null){ //If there are no disruption events within the time range, don't plot any charts (everything would break)
                document.getElementById("StatusMessage").innerHTML = '<div class="alert alert-warning">There are no disruption events occuring within the specified time period</div>';
                status = false; //This also means that graphs shouldn't generate
              }
            }

            if(status==false) { //If status is false, meaning there is an error in the query output or there is no data, reset all graphs
              document.getElementById('df').innerHTML = '<div class="card mb-2 w-100" style="height: 525px"> </div>';
              document.getElementById('dsd').innerHTML = '<div class="card mb-2 w-100" style="height: 525px"> </div>';
              document.getElementById('hdr-chart-card').innerHTML = '<div class="card mb-2 w-100" style="height: 525px"> </div>';
              document.getElementById('OverallART').innerHTML = "--";
              document.getElementById('OverallTD').innerHTML = "--";
              document.getElementById('art-td-card').innerHTML = '<div class="card mb-2 w-100" style="height: 525px"> </div>';
              document.getElementById('rrc').innerHTML = '<div class="card mb-2 w-100" style="height: 525px"> </div>';
              document.getElementById('hdr-company-values').innerHTML = '<p class="text-muted">Submit query to see results...</p>'
              document.getElementById('overall-hdr-statistic').innerHTML = '<p class="text-muted">Submit query to see results...</p>'
            }

            if(status==true) { //If status is true, meaning no errors were identified in the query output and the data valid, plot the charts
              document.getElementById('df').innerHTML = '<div class="card mb-2 w-100" id="df-bar-chart" style="height: 525px"> </div>'
              document.getElementById('dsd').innerHTML = '<div class="card mb-2 w-100" id="dsd-stackedbar-chart" style="height: 525px"> </div>'
              document.getElementById('hdr-chart-card').innerHTML = '<div class="card mb-2 w-100" id="hdr-pie-chart" style="height: 525px"> </div>'
              document.getElementById('art-td-card').innerHTML = '<div class="card mb-2 w-100" id="art-td-histogram-chart" style="height: 525px"> </div>'
              document.getElementById('rrc').innerHTML = '<div class="card mb-2 w-100" id="rrc-heatmap-chart" style="height: 525px"> </div>';
              document.getElementById("StatusMessage").innerHTML = ""; //Clear status message
              
              //To log the raw-data
              console.log(JSON.stringify(my_JSON_object));
              
              //DF - bar chart
              const df_companies = my_JSON_object.DF_chart.map((item) => { return String(item.CompanyName) });
              const df_values = my_JSON_object.DF_chart.map((item) => { return item.DF });
              CreateDFBarChart(df_companies, df_values);

              //DSD - stacked bar chart
              const dsd_companies = my_JSON_object.DSD_chart.map((item) => { return String(item.CompanyName) });
              const dsd_low_values = my_JSON_object.DSD_chart.map((item) => { return item.NumLowImpact });
              const dsd_medium_values = my_JSON_object.DSD_chart.map((item) => { return item.NumMedImpact });
              const dsd_high_values = my_JSON_object.DSD_chart.map((item) => { return item.NumHighImpact });
              CreateDSDStackedBarChart(dsd_companies, dsd_low_values, dsd_medium_values, dsd_high_values);

              //HDR - by company
              const HDR_by_company_Div = document.getElementById('hdr-company-values');
              HDR_by_company_Div.innerHTML = '';
              my_JSON_object.HDR_companies.forEach(item => {
                const div = document.createElement("div");
                div.className = "list-item";
                div.innerHTML = `<strong>Company Name:</strong> ${item.HDRCompanyName} <br>
                                 <strong>HDR:</strong> ${item.HDR}`;
                HDR_by_company_Div.appendChild(div);
              });

              //HDR - overall statistic
              const HDR_overall_statistic_Div = document.getElementById('overall-hdr-statistic');
              HDR_overall_statistic_Div.innerHTML = '';
              if (my_JSON_object.HDR_overall[0].HDRStatistic !== null) {
                    const li = document.createElement("li");
                    li.className = "list-group-item";
                    li.innerHTML = `Overall Statistic: ${my_JSON_object.HDR_overall[0].HDRStatistic}`;
                    HDR_overall_statistic_Div.appendChild(li);
              } else {
                  HDR_overall_statistic_Div.innerHTML = '<p class="text-muted">Invalid input</p>';
              }

              //HDR - pie chart
              const hdr_companies = my_JSON_object.HDR_chart.map((item) => { return String(item.CompanyName) });
              const hdr_values = my_JSON_object.HDR_chart.map((item) => { return item.NumHighImpact });
              const hdr_length = hdr_values.length;
              //Omit 0 values from the pie chart
              let position = 0;
              let filtered_hdr_companies = [];
              let filtered_hdr_values = [];
              for (let i = 0; i < hdr_length; i++) {
                if(hdr_values[i]!="0"){
                  filtered_hdr_companies[position] = hdr_companies[i];
                  filtered_hdr_values[position] = hdr_values[i];
                  position += 1;
                }
              }
              //If there are more than 8 companies, don't label the slices
              let text_display = "label+percent";
              if(hdr_length > 8){
                text_display = "none";
              }
              CreateHDRPieChart(filtered_hdr_companies, filtered_hdr_values, text_display);
              
              //ART & TD - histogram
              const downtime_values = my_JSON_object.TD_ART_chart.map((item) => { return item.Downtime });
              CreateART_TDHistogram(downtime_values);

              //ART & TD - overall statistics
              const ART_overall = String(Number(my_JSON_object.ART_overall[0]["ART"]).toFixed(2));
              const TD_overall = my_JSON_object.TD_overall[0]["TD"];
              document.getElementById('OverallART').innerHTML = `${ART_overall} days`;
              document.getElementById('OverallTD').innerHTML = `${TD_overall} days`;

              //RRC - heatmap
              const RRC_values_object = my_JSON_object.RRC_chart;
              if (RRC_values_object == null){
                document.getElementById("rrc-heatmap-chart").innerHTML = '<p class="text-muted" style="margin-top: 125px; font-size: 18px;">Please filter by a region to see the RRC heatmap!</p>'
              }
              else{
                CreateRRCHeatmap(RRC_values_object, DisruptionDropDown);
              }              
            }
          }
        };
    }
    const url = "supplychainmanager_disruptionevents_queries.php?q=" + encodeURIComponent(q_input) + "&g=" + encodeURIComponent(g_input);
    xhtpp.open("GET", url, true);
    xhtpp.send();
}

function CreateDFBarChart(df_companies, df_values){
  //Placement
  const BarChart = document.getElementById('df-bar-chart');
  //Layout
  var layout = {
      title: {
          text: 'Disruption Frequency'
      },
      xaxis: {
        tickfont: {
          size: 8,
        }
      },
      yaxis: {
          title: {
              text: 'DF'
          }
      }
  };
  //Data
  var data = [
  {
    x: df_companies,
    y: df_values,
    type: 'bar',
    marker: {
      color: '#0f6fab'
    }
  }
  ];
  //Execute Plotly
  Plotly.newPlot(BarChart, data, layout);
}

function CreateDSDStackedBarChart(dsd_companies, dsd_low_values, dsd_medium_values, dsd_high_values){
  //Placement  
  const StackedBarChart = document.getElementById('dsd-stackedbar-chart');
  //Layout
  var layout = {
      title: {
          text: 'Disruption Severity Distribution'
      },
      xaxis: {
        tickfont: {
          size: 8, // Adjust this value to your desired font size
        }
      },
      yaxis: {
          title: {
              text: 'DSD'
          }
      },
      barmode: 'stack'
  };
  //Data
  var low = {
    x: dsd_companies,
    y: dsd_low_values,
    type: 'bar',
    name: 'Low Impact',
    marker: {
      color: '#FFDD00'
    }
  };

  var medium = {
    x: dsd_companies,
    y: dsd_medium_values,
    type: 'bar',
    name: 'Medium Imapct',
    marker: {
      color: 'orange'
    }
  };  
  
  var high = {
    x: dsd_companies,
    y: dsd_high_values,
    type: 'bar',
    name: 'High Impact',
    marker: {
      color: 'red'
    }
  };
  data=[low, medium, high];
  //Execute Plotly
  Plotly.newPlot(StackedBarChart, data, layout);
}

function CreateHDRPieChart(hdr_companies, hdr_values, text_display) {
  //Placement
  const PieChart = document.getElementById('hdr-pie-chart');
  //Data
  var data = [{
    type: "pie",
    values: hdr_values,
    labels: hdr_companies,
    textinfo: `${text_display}`,
  }];
  //Layout
  var layout = {
    title: {
        text: 'High-Impact Disruption Rate'
    },
    showlegend: true
    };
  //Execute Plotly
  Plotly.newPlot(PieChart, data, layout);
}

function CreateART_TDHistogram(downtime_values) {
  const Histogram = document.getElementById('art-td-histogram-chart');

  //Convert query output to numeric
  const values = downtime_values
    .map(v => {
      //Handle string numbers with commas or whitespace
      if (typeof v === 'string') v = v.replace(/,/g, '').trim();
      return Number(v);
    })
    .filter(v => Number.isFinite(v));

  if (!values.length) {
    console.warn('CreateART_TDHistogram: no numeric values to plot.');
    Plotly.purge(Histogram);
    Histogram.innerHTML = '<div style="padding:12px">No numeric downtime values found.</div>';
    return;
  }

  // --- Helper: compute quantile (linear interpolation) ---
  function histCalculations(arr, q) {
    const sorted = [...arr].sort((a, b) => a - b);
    const n = sorted.length;
    const pos = (n - 1) * q;
    const base = Math.floor(pos);
    const rest = pos - base;
    if (base + 1 < n) {
      return sorted[base] + rest * (sorted[base + 1] - sorted[base]);
    }
    return sorted[base];
  }

  const n = values.length;
  const minimumValue = Math.min(...values);
  const maximumValue = Math.max(...values);
  const q1 = histCalculations(values, 0.25);
  const q3 = histCalculations(values, 0.75);
  const iqr = q3 - q1;

  // Freedman–Diaconis bin width
  let binWidth = (iqr > 0) ? (2 * iqr / Math.cbrt(n)) : 0;

  // Fallback if IQR = 0 or binWidth is 0 / extremely small:
  // use Sturges' rule to pick a sensible number of bins
  let numBins;
  if (!binWidth || !isFinite(binWidth) || binWidth <= 0) {
    numBins = Math.ceil(Math.log2(n) + 1); // Sturges
    binWidth = (maximumValue - minimumValue) / numBins || 1; // if range is 0, default width 1
  } else {
    numBins = Math.ceil((maximumValue - minimumValue) / binWidth) || 1;
    // In some edge cases (tiny range or rounding) ensure at least 1 bin
    if (numBins < 1) numBins = 1;
  }

  // If computed binWidth ends up >= range (-> single bin), reduce size by factor to get some bins
  const range = maximumValue - minimumValue;
  if (range > 0 && binWidth >= range) {
    // choose at least min(10, n) bins so user sees distribution
    const fallbackBins = Math.min(10, Math.max(1, Math.floor(Math.sqrt(n))));
    binWidth = range / fallbackBins;
    numBins = fallbackBins;
  }

  // Align start to a nice multiple of binWidth so bins look clean
  const start = Math.floor(minimumValue / binWidth) * binWidth;
  const end = start + numBins * binWidth;

  //Debug information - print to console if desired
  //console.log('n =', n);
  //console.log('min =', minimumValue);
  //console.log('max =', maximumValue);
  //console.log('q1 =', q1, 'q3 =', q3, 'IQR =', iqr);
  //console.log('binWidth =', binWidth, 'numBins =', numBins);
  //console.log('start =', start, 'end =', end);
  //console.groupEnd();

  //Plotly histogram trace with explicit xbins
  const trace = {
    x: values,
    type: 'histogram',
    xbins: {
      start: start,
      end: end,
      size: binWidth
    },
    marker: {
      color: '#0f6fab',
      line: {
        width: 1
      }
    }
  };

  const layout = {
    title: { text: 'Disruption Event Downtime' },
    xaxis: {
      title: { text: `Downtime (days)` }
    },
    yaxis: {
      title: { text: 'Frequency of Downtime' }
    },
    bargap: 0.05
  };

  Plotly.newPlot(Histogram, [trace], layout, {responsive: true});
}

function CreateRRCHeatmap(RRC_values_object, DisruptionDropDown) {
  const continentCountries = {
    "Africa": [
      "DZA", "AGO", "BEN", "BWA", "IOT", "BFA", "BDI", "CPV", "CMR", "CAF",
      "TCD", "COM", "COG", "COD", "CIV", "DJI", "EGY", "GNQ", "ERI", "SWZ",
      "ETH", "ATF", "GAB", "GMB", "GHA", "GIN", "GNB", "KEN", "LSO", "LBR",
      "LBY", "MDG", "MWI", "MLI", "MRT", "MUS", "MYT", "MAR", "MOZ", "NAM",
      "NER", "NGA", "REU", "RWA", "SHN", "STP", "SEN", "SYC", "SLE", "SOM",
      "ZAF", "SSD", "SDN", "TZA", "TGO", "TUN", "UGA", "ESH", "ZMB", "ZWE"],

    "Europe": [
      "ALA", "ALB", "AND", "AUT", "BLR", "BEL", "BIH", "BGR", "HRV", "CZE",
      "DNK", "EST", "FRO", "FIN", "FRA", "DEU", "GIB", "GRC", "GGY", "VAT",
      "HUN", "ISL", "IRL", "IMN", "ITA", "JEY", "LVA", "LIE", "LTU", "LUX",
      "MLT", "MDA", "MCO", "MNE", "NLD", "MKD", "NOR", "POL", "PRT", "ROU",
      "RUS", "SMR", "SRB", "SVK", "SVN", "ESP", "SJM", "SWE", "CHE", "UKR",
      "GBR"],

    "Asia": [
      "AFG", "ARM", "AZE", "BHR", "BGD", "BTN", "BRN", "KHM", "CHN", "CYP",
      "GEO", "HKG", "IND", "IDN", "IRN", "IRQ", "ISR", "JPN", "JOR", "KAZ",
      "PRK", "KOR", "KWT", "KGZ", "LAO", "LBN", "MAC", "MYS", "MDV", "MNG",
      "MMR", "NPL", "OMN", "PAK", "PSE", "PHL", "QAT", "SAU", "SGP", "LKA",
      "SYR", "TJK", "THA", "TLS", "TUR", "TKM", "ARE", "UZB", "VNM", "YEM"],
      
    "North America": [
      "AIA", "ATG", "ABW", "BHS", "BRB", "BLZ", "BMU", "BES", "CAN", "CYM",
      "CRI", "CUB", "CUW", "DMA", "DOM", "SLV", "GRL", "GRD", "GLP", "GTM",
      "HTI", "HND", "JAM", "MTQ", "MEX", "MSR", "NIC", "PAN", "PRI", "BLM",
      "KNA", "LCA", "MAF", "SPM", "VCT", "SXM", "TTO", "TCA", "USA", "VGB",
      "VIR"],
      
    "South America": [
      "ARG", "BOL", "BVT", "BRA", "CHL", "COL", "ECU", "FLK", "GUF", "GUY",
      "PRY", "PER", "SGS", "SUR", "URY", "VEN"],
        
    "Oceania": [
      "ASM", "AUS", "CXR", "CCK", "COK", "FJI", "PYF", "GUM", "HMD", "KIR",
      "MHL", "FSM", "NRU", "NCL", "NZL", "NIU", "NFK", "MNP", "PLW", "PNG",
      "PCN", "WSM", "SLB", "TKL", "TON", "TUV", "UMI", "VUT", "WLF"]
  };


  const countryToISO3 = {
    "Algeria":"DZA","Angola":"AGO","Benin":"BEN","Botswana":"BWA","Burkina Faso":"BFA",
    "Burundi":"BDI","Cabo Verde":"CPV","Cameroon":"CMR","Central African Republic":"CAF","Chad":"TCD",
    "Comoros":"COM","Congo":"COG","Democratic Republic of the Congo":"COD","Côte d'Ivoire":"CIV","Djibouti":"DJI",
    "Egypt":"EGY","Equatorial Guinea":"GNQ","Eritrea":"ERI","Eswatini":"SWZ","Ethiopia":"ETH",
    "Gabon":"GAB","Gambia":"GMB","Ghana":"GHA","Guinea":"GIN","Guinea-Bissau":"GNB",
    "Kenya":"KEN","Lesotho":"LSO","Liberia":"LBR","Libya":"LBY","Madagascar":"MDG",
    "Malawi":"MWI","Mali":"MLI","Mauritania":"MRT","Mauritius":"MUS","Mayotte":"MYT",
    "Morocco":"MAR","Mozambique":"MOZ","Namibia":"NAM","Niger":"NER","Nigeria":"NGA",
    "Réunion":"REU","Rwanda":"RWA","Saint Helena":"SHN","Sao Tome and Principe":"STP","Senegal":"SEN",
    "Seychelles":"SYC","Sierra Leone":"SLE","Somalia":"SOM","South Africa":"ZAF","South Sudan":"SSD",
    "Sudan":"SDN","Tanzania":"TZA","Togo":"TGO","Tunisia":"TUN","Uganda":"UGA",
    "Western Sahara":"ESH","Zambia":"ZMB","Zimbabwe":"ZWE",
    "Åland Islands":"ALA","Albania":"ALB","Andorra":"AND","Austria":"AUT","Belarus":"BLR",
    "Belgium":"BEL","Bosnia and Herzegovina":"BIH","Bulgaria":"BGR","Croatia":"HRV","Czechia":"CZE",
    "Denmark":"DNK","Estonia":"EST","Faroe Islands":"FRO","Finland":"FIN","France":"FRA",
    "Germany":"DEU","Gibraltar":"GIB","Greece":"GRC","Guernsey":"GGY","Holy See":"VAT",
    "Hungary":"HUN","Iceland":"ISL","Ireland":"IRL","Isle of Man":"IMN","Italy":"ITA",
    "Jersey":"JEY","Latvia":"LVA","Liechtenstein":"LIE","Lithuania":"LTU","Luxembourg":"LUX",
    "Malta":"MLT","Moldova":"MDA","Monaco":"MCO","Montenegro":"MNE","Netherlands":"NLD",
    "North Macedonia":"MKD","Norway":"NOR","Poland":"POL","Portugal":"PRT","Romania":"ROU",
    "Russia":"RUS","San Marino":"SMR","Serbia":"SRB","Slovakia":"SVK","Slovenia":"SVN",
    "Spain":"ESP","Svalbard and Jan Mayen":"SJM","Sweden":"SWE","Switzerland":"CHE","Ukraine":"UKR",
    "United Kingdom":"GBR",
    "Afghanistan":"AFG","Armenia":"ARM","Azerbaijan":"AZE","Bahrain":"BHR","Bangladesh":"BGD",
    "Bhutan":"BTN","Brunei":"BRN","Cambodia":"KHM","China":"CHN","Cyprus":"CYP",
    "Georgia":"GEO","Hong Kong":"HKG","India":"IND","Indonesia":"IDN","Iran":"IRN",
    "Iraq":"IRQ","Israel":"ISR","Japan":"JPN","Jordan":"JOR","Kazakhstan":"KAZ",
    "North Korea":"PRK","South Korea":"KOR","Kuwait":"KWT","Kyrgyzstan":"KGZ","Laos":"LAO",
    "Lebanon":"LBN","Macau":"MAC","Malaysia":"MYS","Maldives":"MDV","Mongolia":"MNG",
    "Myanmar":"MMR","Nepal":"NPL","Oman":"OMN","Pakistan":"PAK","Palestine":"PSE",
    "Philippines":"PHL","Qatar":"QAT","Saudi Arabia":"SAU","Singapore":"SGP","Sri Lanka":"LKA",
    "Syria":"SYR","Tajikistan":"TJK","Thailand":"THA","Timor-Leste":"TLS","Turkey":"TUR",
    "Turkmenistan":"TKM","United Arab Emirates":"ARE","Uzbekistan":"UZB","Vietnam":"VNM","Yemen":"YEM",
    "Anguilla":"AIA","Antigua and Barbuda":"ATG","Aruba":"ABW","Bahamas":"BHS","Barbados":"BRB",
    "Belize":"BLZ","Bermuda":"BMU","Bonaire, Sint Eustatius and Saba":"BES","Canada":"CAN","Cayman Islands":"CYM",
    "Costa Rica":"CRI","Cuba":"CUB","Curaçao":"CUW","Dominica":"DMA","Dominican Republic":"DOM",
    "El Salvador":"SLV","Greenland":"GRL","Grenada":"GRD","Guadeloupe":"GLP","Guatemala":"GTM",
    "Haiti":"HTI","Honduras":"HND","Jamaica":"JAM","Martinique":"MTQ","Mexico":"MEX",
    "Montserrat":"MSR","Nicaragua":"NIC","Panama":"PAN","Puerto Rico":"PRI","Saint Barthélemy":"BLM",
    "Saint Kitts and Nevis":"KNA","Saint Lucia":"LCA","Saint Martin":"MAF","Saint Pierre and Miquelon":"SPM","Saint Vincent and the Grenadines":"VCT",
    "Sint Maarten":"SXM","Trinidad and Tobago":"TTO","Turks and Caicos Islands":"TCA","United States":"USA","British Virgin Islands":"VGB","U.S. Virgin Islands":"VIR",
    "Argentina":"ARG","Bolivia":"BOL","Bouvet Island":"BVT","Brazil":"BRA","Chile":"CHL",
    "Colombia":"COL","Ecuador":"ECU","Falkland Islands":"FLK","French Guiana":"GUF","Guyana":"GUY",
    "Paraguay":"PRY","Peru":"PER","South Georgia and the South Sandwich Islands":"SGS","Suriname":"SUR","Uruguay":"URY","Venezuela":"VEN",
    "American Samoa":"ASM","Australia":"AUS","Christmas Island":"CXR","Cocos (Keeling) Islands":"CCK","Cook Islands":"COK",
    "Fiji":"FJI","French Polynesia":"PYF","Guam":"GUM","Heard Island and McDonald Islands":"HMD","Kiribati":"KIR",
    "Marshall Islands":"MHL","Micronesia":"FSM","Nauru":"NRU","New Caledonia":"NCL","New Zealand":"NZL",
    "Niue":"NIU","Norfolk Island":"NFK","Northern Mariana Islands":"MNP","Palau":"PLW","Papua New Guinea":"PNG",
    "Pitcairn Islands":"PCN","Samoa":"WSM","Solomon Islands":"SLB","Tokelau":"TKL","Tonga":"TON",
    "Tuvalu":"TUV","U.S. Minor Outlying Islands":"UMI","Vanuatu":"VUT","Wallis and Futuna":"WLF"
  };

  let locations = [];
  let values = [];
  let hoverTexts = [];

  RRC_values_object.forEach(region => {
    const rrc = parseFloat(region['RRC']);
    if(DisruptionDropDown == "continent") {
      const countries = continentCountries[region['Region']] || [];
      countries.forEach(c => {
        locations.push(c);
        values.push(rrc);
        hoverTexts.push(`${region['Region']}: ${rrc}%`);
      });
    }
    else if(DisruptionDropDown == "country") {
      const iso = countryToISO3[region['Region']];
      locations.push(iso);
      values.push(rrc);
      hoverTexts.push(`${region['Region']}: ${rrc}`);
    }
  });

  const data = [{
    type: 'choropleth',
    locations: locations,
    z: values,
    locationmode: 'ISO-3',
    colorscale: 'Reds',
    autocolorscale: false,
    zmin: 0,
    zmax: 1, 
    colorbar: { 
      title: 'RRC'
    },
    text: hoverTexts,
    hoverinfo: 'text'
  }];

  const layout = {
    title: { text: 'Regional Risk Concentration' },
    geo: {
      showframe: false,
      showcoastlines: true,
      projection: { type: 'natural earth' }
    },
    margin: { t: 50 }
  };

  Plotly.newPlot('rrc-heatmap-chart', data, layout);
}

</script>
</html>
