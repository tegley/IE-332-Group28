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
        margin-left: 5px;
        margin-right: 5px;
        margin-top: 2px;
    }

    #myTab {
        margin-top: 15px;
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

          </ul>

          <div class="tab-content" id="myTabContent">
            <div
              class="tab-pane fade show active"
              id="df"
              role="tabpanel"
              aria-labelledby="df-tab"
            >
              <div class="card mb-2 w-100" id="df-bar-chart" style="height: 500px"> </div>
            </div>

            <div
              class="tab-pane fade"
              id="dsd"
              role="tabpanel"
              aria-labelledby="dsd-tab"
            >
              <div class="card mb-2 w-100" id="dsd-stackedbar-chart" style="height: 500px"> </div>

            </div>

            <div
              class="tab-pane fade"
              id="hdr"
              role="tabpanel"
              aria-labelledby="hdr-tab"
            >
              <div class="card mb-2 w-100" id="hdr-pie-chart" style="height: 500px"> </div>
            </div>

            <div
              class="tab-pane fade"
              id="art-td"
              role="tabpanel"
              aria-labelledby="art-td-tab"
            >
              <div class="card mb-2 w-100" id="art-td-histogram-chart" style="height: 500px"> </div>
            </div>
          </div>
        </div>
      </div> <!-- Closes col-md-9 -> add divs above this line!! -->

    </div> <!-- Row -->
  </div> <!-- Container -->

<script> //JavaScript for dropdown filter appearance space minimization
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
        
        // This condition checks which tab was just activated
        if (targetTabId === '#dsd') {
            // Get the container element for the DSD chart
            const chartContainer = document.getElementById('dsd-stackedbar-chart');
            // Tell Plotly to resize the chart to its new, visible container size
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
    });
});

//Prevent height compression of first active tab
const activePane = document.querySelector('.tab-pane.show.active');
if (activePane) {
  //Find the chart container inside that active pane
  const ChartContainer = activePane.querySelector('[id$="-chart"]');

  if (ChartContainer) {
    //Pass the DOM element to Plotly (FIXED: using dfChartContainer, not its ID string)
    Plotly.relayout(ChartContainer, { autosize: true });
  }
}
</script>

</body> <!-- End Document -->
<script> //JavaScript functions
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

          //DF - bar chart
          const df_companies = my_JSON_object.DF.map((item) => { return String(item.CompanyName) });
          const df_values = my_JSON_object.DF.map((item) => { return item.Total });
          CreateDFBarChart(df_companies, df_values);

          //DSD - stacked bar chart
          const dsd_companies = my_JSON_object.DSD.map((item) => { return String(item.CompanyName) });
          const dsd_low_values = my_JSON_object.DSD.map((item) => { return item.NumLowImpact });
          const dsd_medium_values = my_JSON_object.DSD.map((item) => { return item.NumMedImpact });
          const dsd_high_values = my_JSON_object.DSD.map((item) => { return item.NumHighImpact });
          CreateDSDStackedBarChart(dsd_companies, dsd_low_values, dsd_medium_values, dsd_high_values);

          //HDR - pie chart
          const hdr_companies = my_JSON_object.HDR.map((item) => { return String(item.CompanyName) });
          const hdr_values = my_JSON_object.HDR.map((item) => { return item.NumHighImpact });
          CreateHDRPieChart(hdr_companies, hdr_values);

          //ART & TD - histogram
          const downtime_values = my_JSON_object.TD_ART.map((item) => { return item.Downtime })
          console.log(downtime_values);
          CreateART_TDHistogram(downtime_values);  
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
    type: 'bar'
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
    name: 'Low Impact'
  };

  var medium = {
    x: dsd_companies,
    y: dsd_medium_values,
    type: 'bar',
    name: 'Medium Imapct'
  };
  
  var high = {
    x: dsd_companies,
    y: dsd_high_values,
    type: 'bar',
    name: 'High Impact'
  };

  data=[low, medium, high];
  //Execute Plotly
  Plotly.newPlot(StackedBarChart, data, layout);
}

function CreateHDRPieChart(hdr_companies, hdr_values) {
  //Placement
  const PieChart = document.getElementById('hdr-pie-chart');
  //Data
  var data = [{
    type: "pie",
    values: hdr_values,
    labels: hdr_companies,
    textinfo: "label+percent",
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
  //Placement
  const Histogram = document.getElementById('art-td-histogram-chart');
  //Data
  var trace = {
    x: downtime_values,
    type: 'histogram',
  };
  var data = [trace];
  //Layout
  var layout = {
    title: {
        text: 'Disruption Event Downtime'
    },
    yaxis: {
        title: {
            text: 'Downtime (days)'
        }
    },
    yaxis: {
        title: {
            text: 'Frequency of Downtime'
        }
    }
  };
  //Execute Plotly
  Plotly.newPlot(Histogram, data, layout);
}
</script>
</html>
