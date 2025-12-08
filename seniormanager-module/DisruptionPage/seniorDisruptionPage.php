<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senior Manager Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.plot.ly/plotly-2.35.2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="SCM_display_alerts.js"></script>

    <style>
        @import "standardized_project_formatting.css";

        .bubble-header {
            display: inline-block;
            padding: 12px 50px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            margin: 15px;
            text-align: center;
            background-color: #0f6fab;
            font-size: 20px;
        }

        .area-header {
            background-color: #cbcbcb;
            font-family: Cambria, serif;
            font-size: 20px;
            color: #222;
            border-radius: 8px;
            text-align: center;
            margin-top: 15px;
            margin-bottom: 10px;
            padding: 15px;
        }

        .scroll-box {
            height: 180px;
            overflow-y: auto;
            border: 1px solid #aaa;
            padding: 10px;
            border-radius: 6px;
            background-color: white;
        }

        .list-item {
            padding: 8px;
            margin: 4px 0;
            background: #f8f9fa;
            border-left: 3px solid #0f6fab;
            border-radius: 4px;
        }

        .stats-table td {
            border: 1px solid #666;
            padding: 10px;
            font-size: 15px;
        }

        .scroll-box-companyinfo {
            max-height: 350px;
            overflow-y: auto;
            border: 1px solid #aaa;
            padding: 10px;
            border-radius: 6px;
            background-color: white;
        }

        .scroll-box-disruptionevents {
            height: 115px;
            max-height: 115px;
            overflow-y: auto;
            border: 1px solid #aaa;
            padding: 10px;
            border-radius: 6px;
            background-color: white;
        }
    </style>
</head>

<body>
    <h1>Global Electronics LLC</h1>

    <div class="container">
        <div class="row">

            <!-- Sidebar -->
            <div class="col-md-3">
                <div id="seniormanager_sidebar"></div>
                <script>
                    fetch('seniormanager_sidebar.html')
                        .then(r => r.text())
                        .then(html => document.getElementById('seniormanager_sidebar').innerHTML = html);
                </script>
            </div>

            <div class="col-md-9">

                <!-- Dashboard Header -->
                <div class="card" id="dashboard-header">
                    <?php echo "{$user_FullName}'s SM Dashboard" ?>
                </div>

                <!-- BOOTSTRAP TABS -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">

                    <!-- TAB 1 -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="countrycontinent-tab" data-bs-toggle="tab" data-bs-target="#countrycontinent" type="button" role="tab">
                            Region
                        </button>
                    </li>

                    <!-- TAB 2 -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="idname-tab" data-bs-toggle="tab" data-bs-target="#idname" type="button" role="tab">
                            Disruption ID or Company Name
                        </button>
                    </li>

                    <!-- TAB 3 -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="frequency-tab" data-bs-toggle="tab" data-bs-target="#frequency" type="button" role="tab">
                            Disruption Frequency
                        </button>
                    </li>

                    <!-- TAB 4 -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="criticality-tab" data-bs-toggle="tab" data-bs-target="#criticality" type="button" role="tab">
                            Criticality
                        </button>
                    </li>

                </ul>

                <!-- Start TAB CONTENT WRAPPER -->
                <div class="tab-content" id="myTabContent">

                    <!-- TAB 1: AVERAGE FINANCIAL HEALTH BY COMPANY TYPE -->
                    <div class="tab-pane fade show active" id="countrycontinent" role="tabpanel" aria-labelledby="countrycontinent-tab">

                        <div class="area-header">Disruption Events by Region</div>

                        <!-- Input Section -->
                        <div class="card mb-3">
                            <div class="card-body row">

                                <div class="col-md-6">
                                    <label>Region Type</label>
                                    <select class="form-control" id="regionType_input" onchange='LoadRegionList(document.getElementById("regionType_input").value)'>
                                        <option value="">Select Type</option>
                                        <option value="Country">Country</option>
                                        <option value="Continent">Continent</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Region</label>
                                    <select class="form-control" id="region_input">
                                        <option value="">All Regions</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-3 d-flex justify-content-center">
                                    <button type="button" class="btn btn-primary" onclick="if (CheckUserInput()) LoadRegionDisruptions(document.getElementById('regionType_input').value, document.getElementById('region_input').value);">
                                        Submit
                                    </button>
                                </div>

                            </div>
                        </div>

                        <!-- TAB 1: Regional Disruption info -->
                        <!-- The line below that is commented out is a duplicated line which breaks everything -->
                    <!-- <div class="tab-pane fade show active" id="countrycontinent" role="tabpanel" aria-labelledby="countrycontinent-tab"> -->

                        <div class="area-header">Disruption Events by Region and Frequency</div>

                        <!-- Table + Bar Chart -->
                        <div class="row">

                            <div class="col-md-6">
                                <div class="card" style="height: 350px;">
                                    <div class="card-header"><h4>Regional Disruption Overview</h4></div>
                                    <ul class="list-group list-group-flush" id="regionalOverviewList" style="max-height:700px; overflow-y: auto;">
                                        <p class="text-muted">Submit query to see results...</p>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card" style="height: 350px;">
                                    <div class="card-header"><h4>Regional Disruption Bar Chart</h4></div>
                                    <div id="disruptionLevelChart" style="height: 300px;">
                                        <p class="text-muted">Submit query to see results...</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div> <!-- END TAB 1 -->

                     <!-- TAB 2: SEARCH BY DISRUPTION ID OR COMPANY NAME -->
                    <div class="tab-pane fade" id="idname" role="tabpanel" aria-labelledby="idname-tab">

                        <div class="area-header">Search by Disruption ID or Company Name within Region</div>

                        <div class="row">

                            <!-- LEFT SIDE — SEARCH BY DISRUPTION ID -->
                            <div class="col-md-6">

                                <div class="card mb-3">
                                    <div class="card-header text-center fw-bold">Search by Disruption ID</div>

                                    <div class="card-body">
                                        <label>Disruption ID</label>
                                        <select class="form-control" id="DisruptionID_input" onchange="SearchByDisruptionID(document.getElementById('DisruptionID_input').value)">
                                            <option value="">Select ID</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Results (LEFT TABLE) -->
                                <div class="card">
                                    <div class="card-header">Company Affected and Impact Level</div>

                                    <div class="card-body" style="max-height: 320px; overflow-y: auto;">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Company Affected</th>
                                                    <th>Impact Level</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyDisruptID">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div> <!-- END LEFT COL -->

                            <!-- RIGHT SIDE — SEARCH BY COMPANY NAME -->
                            <div class="col-md-6">

                                <div class="card mb-3">
                                    <div class="card-header text-center fw-bold">Search by Company Name</div>

                                    <div class="card-body">
                                        <label>Company Name</label>
                                        <select class="form-control" id="CompanyName_input" onchange="SearchByCompanyName(document.getElementById('CompanyName_input').value)">
                                            <option value="">Select Company</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Results (RIGHT TABLE) -->
                                <div class="card">
                                    <div class="card-header">Disruption Event, Date and Impact Level</div>

                                    <div class="card-body" style="max-height: 320px; overflow-y: auto;">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Disruption Event</th>
                                                    <th>Date</th>
                                                    <th>Impact Level</th>
                                                </tr>
                                            </thead>
                                            <tbody id = "tbodyCompany">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div> <!-- END RIGHT COL -->

                        </div> <!-- END ROW -->

                    </div> <!-- END TAB 2 -->

                    <!-- TAB 3: DISRUPTION FREQUENCY -->
                    <div class="tab-pane fade" id="frequency" role="tabpanel" aria-labelledby="frequency-tab">

                        <div class="area-header">Disruption Frequency</div>

                        <!-- Search Card -->
                        <div class="card mb-3">
                            <div class="card-body row">

                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" id="freqStartDate" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="date" id="freqEndDate" class="form-control">
                                </div>

                                <div class="col-12 mt-3 d-flex justify-content-center">
                                    <button class="btn btn-primary px-4" onclick="if (ValidateFrequencyTab()) LoadDisruptionFrequency(document.getElementById('freqStartDate').value,document.getElementById('freqEndDate').value);">
                                        Submit
                                    </button>
                                </div>

                            </div>
                        </div>

                        <!-- Frequency Line Chart -->
                        <div class="card" style="height: 600px;">
                            <div class="card-header fw-bold text-center">
                                Disruption Frequency Line Chart
                            </div>
                            <div class="card-body">
                                <div id="DisruptionFreqChart" style="height: 600px;">
                                    <p class="text-muted">Submit query to see results...</p>
                                </div>
                            </div>
                        </div>

                    </div> <!-- END TAB 3 -->

                    <!-- TAB 4: CRITICALITY -->
                    <div class="tab-pane fade" id="criticality" role="tabpanel" aria-labelledby="criticality-tab">

                        <div class="area-header">Criticality</div>

                        <!-- Card Container -->
                        <div class="card">
                            <div class="card-header fw-bold">
                                Company's Score
                            </div>
                            <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Company</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                                    <tbody id="CriticalityTable">
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">
                                                Submit query to see results...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div> <!-- END TAB 4 -->

                </div> <!-- END tab-content -->

            </div> <!-- col-md-9 -->
        </div> <!-- row -->
    </div> <!-- container -->

    <!-- Need refitting for Senior Manager Disruption Page-->
    <script>
        //Load Company Names when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadCompanies();
            updateDropDowns();
            LoadDisruptionFrequency('2020-09-09', '2025-09-09');
        });

        function LoadRegionList(region) {
            xhtpp = new XMLHttpRequest();
            const input = region;
            console.log("LoadRegionListCalled!");
            

            xhtpp.onload = function () {
                if (this.readyState == 4 && this.status == 200) {

                    const my_JSON_object = JSON.parse(this.responseText);
                    console.log(JSON.stringify(my_JSON_object));

                    const regionDropdown = document.getElementById('region_input');
                    regionDropdown.innerHTML = '';
                    
                    const defaultRegionOption = document.createElement('option');
                    defaultRegionOption.value = '';
                    defaultRegionOption.textContent = 'Select a specific region';
                    regionDropdown.appendChild(defaultRegionOption);
                    
                    //Since the key will change based on user input, we must find out what the key is
                    const $key = Object.keys(my_JSON_object[0])[0];

                    my_JSON_object.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item[$key];
                        option.textContent = item[$key];
                        regionDropdown.appendChild(option);
                        });

                    //Add All option
                    const allOption = document.createElement('option');
                        allOption.value = "";
                        allOption.textContent = "All Regions";
                        regionDropdown.appendChild(allOption);


                } // END onload function
                else{
                    console.log("Failed");
                }
            }

            xhtpp.open("GET", "regionOpts.php?q=" + region, true);
            console.log("regionOpts.php?q=" + region); 
            xhtpp.send();
    } //End LoadRegionList

        function loadCompanies() {
            fetch('distributorList.php')
                .then(response => response.json())
                .then(data => {
                    const companyDropdown = document.getElementById('CompanyName_input');
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
                    //Population Disruption Event IDs
                    const disruptionDropdown = document.getElementById('DisruptionID_input');
                    disruptionDropdown.innerHTML = '';
                    
                    const defaultdisruptionOption = document.createElement('option');
                    defaultdisruptionOption.value = '';
                    defaultdisruptionOption.textContent = 'Select a disruptionID';
                    disruptionDropdown.appendChild(defaultdisruptionOption);
                    
                    data.disruptionID.forEach(disruptionID => {
                        const option = document.createElement('option');
                        option.value = disruptionID.EventID;
                        option.textContent = disruptionID.EventID;
                        disruptionDropdown.appendChild(option);
                    });
                })
        }
         // Update Dropdown List
        function updateDropDowns(){
            xhtpp = new XMLHttpRequest();
            // const regionType = document.getElementById("regionType_input").value;
            // const region = document.getElementById("region_input").value;
            // const input = regionType + ',' + region;
            console.log("updateRegionsCalled!");
            

            xhtpp.onload = function () {
                if (this.readyState == 4 && this.status == 200) {

                    const data = JSON.parse(this.responseText);
                    console.log(JSON.stringify(data));
                     const companyDropdown = document.getElementById('CompanyName_input');
                    companyDropdown.innerHTML = '';
                    const disruptionDropdown = document.getElementById('DisruptionID_input');
                    disruptionDropdown.innerHTML = '';

                    if(data.company.length > 0){
                    //Populate Company Options
                    const defaultCompanyOption = document.createElement('option');
                    defaultCompanyOption.value = '';
                    defaultCompanyOption.textContent = 'Select a company';
                    companyDropdown.appendChild(defaultCompanyOption);
                    
                    data.company.forEach(company => {
                        const option = document.createElement('option');
                        option.value = company.CompanyName;
                        option.textContent = company.CompanyName;
                        companyDropdown.appendChild(option);
                    });}else{
                        const defaultCompanyOption = document.createElement('option');
                        defaultCompanyOption.value = '';
                        defaultCompanyOption.textContent = 'No Companies in Filter';
                        companyDropdown.appendChild(defaultCompanyOption);
                        const disruptionDropdown = document.getElementById('DisruptionID_input');
                        disruptionDropdown.innerHTML = '';
                    }
                    if(data.disruptionID.length > 0){
                    //Population Disruption Event IDs
                    const defaultdisruptionOption = document.createElement('option');
                    defaultdisruptionOption.value = '';
                    defaultdisruptionOption.textContent = 'Select a disruptionID';
                    disruptionDropdown.appendChild(defaultdisruptionOption);
                    data.disruptionID.forEach(disruptionID => {
                        const option = document.createElement('option');
                        option.value = disruptionID.EventID;
                        option.textContent = disruptionID.EventID;
                        disruptionDropdown.appendChild(option);
                    });
                    } else{
                    const defaultdisruptionOption = document.createElement('option');
                    defaultdisruptionOption.value = '';
                    defaultdisruptionOption.textContent = 'No Disruptions within Filter';
                    disruptionDropdown.appendChild(defaultdisruptionOption);
                    }


                } // END onload function
                else{
                    console.log("Failed");
                }
                }
                xhtpp.open("GET", "regionSelectionOptions.php", true);
                console.log("regionSelectionOptions.php"); 
                xhtpp.send();
        }
    </script>

    <script>
        function CheckUserInput() {

            const regionType = document.getElementById("regionType_input").value;
            console.log("Called!!");
             if (regionType === "") {
                alert("Please select a Region Type (Country or Continent).");
                return false;
            }
            return true;


            return true;
        }
    </script>

    <script>
        function ValidateFrequencyTab() {

            const start = document.getElementById("freqStartDate").value;
            const end = document.getElementById("freqEndDate").value;

            if (start === "" || end === "") {
                alert("Please select both a start and end month.");
                return false;
            }

            if (start >= end) {
                alert("Start date must be before end date.");
                return false;
            }

            return true;
        }
    </script>
     <script>
        let my_JSON_object = ""; //Making global JSON object so that the user's selection can dynamically update when they select a dropdown filter

        function LoadRegionDisruptions(regionType, region) {
            input = "";
            g = regionType + "|" + region;

            xhtpp = new XMLHttpRequest();

            xhtpp.onload = function () {
                if (this.readyState == 4 && this.status == 200) {

                    data = JSON.parse(this.responseText);
                    my_JSON_object = data; //Update global object
                    console.log("Katya is rad");
                    console.log(JSON.stringify(data));

                    // Table of Events
                    const companyImpactedDiv = document.getElementById("regionalOverviewList");
                    companyImpactedDiv.innerHTML = ""; //Clear out placeholder

                    if (data.companyAffectedByEvent && data.companyAffectedByEvent.length > 0) {
                        data.companyAffectedByEvent.forEach(item => {
                            const div = document.createElement("div");
                            div.className = "list-item";
                            div.innerHTML = `
                                <strong>Company Name:</strong> ${item.CompanyName} <strong>ID:</strong> ${item.CompanyID}<br>
                                <strong>Event ID:</strong> ${item.EventID} <strong>Impact Level:</strong> ${item.ImpactLevel}<br>
                                <strong>Country:</strong> ${item.CategoryName}<br>
                                <strong>Country:</strong> ${item.CountryName} <strong>Continent:</strong> ${item.ContinentName}
                            `;
                            companyImpactedDiv.appendChild(div);
                        });
                    } else {
                        companyImpactedDiv.innerHTML = '<p class="text-muted">No disruptions found</p>';
                    }
                    let regionDesired = "";
                    switch (regionType){
                        case "Country":
                            regionDesired = "CountryName";
                            break;
                        case "Continent":
                            regionDesired = "ContinentName";
                            break;
                    }
                    console.log(regionDesired);

                    //Plot
                    const dsd_regions = data.regionalOverview.map((item) => { return item[regionDesired] });
                    console.log(dsd_regions);
                    const dsd_other_values = data.regionalOverview.map((item) => { return item.leftOverDisruption });
                    console.log(dsd_other_values);
                    const dsd_high_values = data.regionalOverview.map((item) => { return item.HighImpactCount });
                    console.log(dsd_high_values);
                    CreateDSDStackedBarChart(dsd_regions, dsd_other_values, dsd_high_values);
                   

                } // END readyState if
                else{
                    console.log("Bad bad bad")
                }
            } // END onload function
            console.log("Sending: seniorDisruptionQueries.php?q=" + input + "&g=" + g)
            xhtpp.open("GET", "seniorDisruptionQueries.php?q=" + input + "&g=" + g, true);
            xhtpp.send();
        } // END AJAX
        // Filter based on disruption drop down
        function SearchByDisruptionID(disruptionID) {
            xhtpp = new XMLHttpRequest();

            xhtpp.onload = function () {
                if (this.readyState == 4 && this.status == 200) {

                    my_JSON_object = JSON.parse(this.responseText); //This JSON object is ALL of the data from the queries, not limited by region
                    console.log("Katya is rad");
                    console.log(JSON.stringify(my_JSON_object));
                    const data = my_JSON_object.companyAffectedByEvent.filter(item => item.EventID === disruptionID); //Companies affected by disruption ID
                    const disruptionIDtBody = document.getElementById("tbodyDisruptID");
                    disruptionIDtBody.innerHTML = ""; //Clear out placeholder

                    if (data && data.length > 0) {
                        for (let i = 0; i < data.length; i++){
                            const row = disruptionIDtBody.insertRow();
                            row.innerHTML = `
                                <td>${data[i].CompanyName}</td>
                                <td>${data[i].ImpactLevel}</td>
                                `;
                        }
                    } else {
                        const row = disruptionIDtBody.insertRow();
                            row.innerHTML = `
                                <td>No company affected</td>
                                <td>N/A</td>
                                `;
                    }
                }
            }
            console.log("Sending: seniorDisruptionQueries.php")
            xhtpp.open("GET", "seniorDisruptionQueries.php", true);
            xhtpp.send();

        }
        // Filter based on disruption drop down
        function SearchByCompanyName(companyName) {

            xhtpp.onload = function () {
                if (this.readyState == 4 && this.status == 200) {
                    my_JSON_object = JSON.parse(this.responseText); //This JSON object is ALL of the data from the queries, not limited by region
                    console.log("Katya is rad");
                    console.log(JSON.stringify(my_JSON_object));
                    const data = my_JSON_object.companyAffectedByEvent.filter(item => item.CompanyName === companyName); //Getting data ONLY regarding user's choice of company
                    const disruptionIDtBody = document.getElementById("tbodyCompany");
                            disruptionIDtBody.innerHTML = ""; //Clear out placeholder

                    if (data && data.length > 0) {
                        for (let i = 0; i < data.length; i++){
                            const row = disruptionIDtBody.insertRow();
                            row.innerHTML = `
                                <td>${data[i].EventID}</td>
                                <td>${data[i].EventDate}</td>
                                <td>${data[i].ImpactLevel}</td>
                                `;
                        }
                    } else {
                        const row = disruptionIDtBody.insertRow();
                            row.innerHTML = `
                                <td>No company affected</td>
                                <td>N/A</td>
                                `;
                    }
                }
            }
            console.log("Sending: seniorDisruptionQueries.php")
            xhtpp.open("GET", "seniorDisruptionQueries.php", true);
            xhtpp.send();
        }
         // LineChart
        function LoadDisruptionFrequency(start_date, end_date) {
            const input = start_date + "|" + end_date;
            //Get DIV for display
            const freqChartDiv = document.getElementById("DisruptionFreqChart");
            freqChartDiv.innerHTML = ""; //Clear out placeholder

            xhtpp.onload = function () {
                if (this.readyState == 4 && this.status == 200) {
                    my_JSON_object = JSON.parse(this.responseText);
                    console.log("Katya is rad");
                    console.log(JSON.stringify(my_JSON_object));
                    
                    // Extract the frequency data array
                    const frequencyData = my_JSON_object.frequency;
                    
                    // Prepare data arrays for plotting
                    var dates = [];
                    var eventCounts = [];
                    var avgDurations = [];
                    var maxDurations = [];
                    
                    frequencyData.forEach(function(datum) {
                        dates.push(new Date(datum.StartDate));
                        eventCounts.push(parseInt(datum.EventCount));
                        avgDurations.push(parseFloat(datum.avgDuration));
                        maxDurations.push(parseInt(datum.maxDuration));
                    });
                    
                    // Define traces for the plot
                    var traces = [
                        {
                            name: 'Event Count',
                            mode: 'lines+markers',
                            x: dates,
                            y: eventCounts,
                            yaxis: 'y1',
                            line: { color: '#1f77b4' },
                            marker: { size: 6 }
                        },
                        {
                            name: 'Avg Duration (days)',
                            mode: 'lines+markers',
                            x: dates,
                            y: avgDurations,
                            yaxis: 'y2',
                            line: { color: '#ff7f0e' },
                            marker: { size: 6 }
                        },
                        {
                            name: 'Max Duration (days)',
                    mode: 'lines',
                    x: dates,
                    y: maxDurations,
                    yaxis: 'y2',
                    line: { color: '#2ca02c', dash: 'dash' },
                    opacity: 0.6
                        }
                    ];
                    
                    // Define range selector options
                    var selectorOptions = {
                        buttons: [
                            {
                                step: 'month',
                                stepmode: 'backward',
                                count: 1,
                                label: '1m'
                            },
                            {
                                step: 'month',
                                stepmode: 'backward',
                                count: 6,
                                label: '6m'
                            },
                            {
                                step: 'year',
                                stepmode: 'todate',
                                count: 1,
                                label: 'YTD'
                            },
                            {
                                step: 'year',
                                stepmode: 'backward',
                                count: 1,
                                label: '1y'
                            },
                            {
                                step: 'all',
                                label: 'All'
                            }
                        ]
                    };
                    
                    // Define layout
                    var layout = {
                        title: {
                            text: 'Disruption Event Frequency and Duration'
                        },
                        xaxis: {
                            title: 'Adjust Slide Bar to Explore Date Ranges',
                            rangeselector: selectorOptions,
                            rangeslider: {}
                        },
                        yaxis: {
                            title: 'Number of Events',
                            fixedrange: true,
                            side: 'left'
                        },
                        yaxis2: {
                            title: 'Average Duration (days)',
                            overlaying: 'y',
                            side: 'right',
                            fixedrange: true
                        },
                        hovermode: 'x unified',
                        height: 500,
                        width: 950,
                        showlegend: true,
                        legend: {
                            x: 0.01,
                            y: 0.99,
                            bgcolor: 'rgba(255, 255, 255, 0.8)'
                        }
                    };
                    
                    // Create the plot
                    Plotly.newPlot('DisruptionFreqChart', traces, layout);
                }
                else {
                    freqChartDiv.innerHTML = "No data in provided time range!";
                }
            }
    
    console.log("Sending: seniorDisruptionQueries.php?q=" + input);
    xhtpp.open("GET", "seniorDisruptionQueries.php?q=" + input, true);
    xhtpp.send();
}
        function CreateDSDStackedBarChart(dsd_regions, dsd_other_values, dsd_high_values){
            //Placement  
            const StackedBarChart = document.getElementById('disruptionLevelChart');
            StackedBarChart.innerHTML = "";
            //Layout
            var layout = {
                title: {
                    text: 'Portion of High Impact Disruption Events'
                },
                xaxis: {
                    tickfont: {
                    size: 8, // Adjust this value to your desired font size
                    }
                },
                yaxis: {
                    title: {
                        text: 'Count of Events'
                    }
                },
                barmode: 'stack'
            };
            //Data
            var other = {
                x: dsd_regions,
                y: dsd_other_values,
                type: 'bar',
                name: 'Disruption Events',
                marker: {
                color: '#FFDD00'
                }
            };
            
            var high = {
                x: dsd_regions,
                y: dsd_high_values,
                type: 'bar',
                name: 'High Impact',
                marker: {
                color: 'red'
                }
            };
            data=[other, high];
            //Execute Plotly
            Plotly.newPlot(StackedBarChart, data, layout);
            }
    </script>
