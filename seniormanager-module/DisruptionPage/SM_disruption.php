<?php
/*session_start();

//Check if the user is NOT logged in (security measure)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo "<h1>Unauthorized Login</h1>";
    echo "<p>Please visit the <a href='index.php'>login page</a>!</p>";
    exit();
}

$user_FullName = $_SESSION['FullName']; */
?>

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

                 <!-- GLOBAL FILTERS -->
                        <div class="card mb-3">
                            <div class="card-body row">

                                <div class="col-md-3">
                                    <label>Region Type</label>
                                    <select class="form-control" id="regionType_input" onchange='LoadRegionList(document.getElementById("regionType_input").value)'>
                                        <option value="">Select Type</option>
                                        <option value="Country">Country</option>
                                        <option value="Continent">Continent</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Region</label>
                                    <select class="form-control" id="region_input">
                                        <option value="">All Regions</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" id="globalStartDate">
                                </div>

                                <div class="col-md-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" id="globalEndDate">
                                </div>

                                <div class="col-12 mt-3 d-flex justify-content-center">
                                    <button type="button" class="btn btn-primary" onclick="if (CheckUserInput()) updateDropDowns(); LoadRegionDisruptions(document.getElementById('globalStartDate').value, document.getElementById('globalEndDate').value, document.getElementById('regionType_input').value, document.getElementById('region_input').value);">
                                        Submit
                                    </button>
                                </div>

                            </div>
                        </div>

                <!-- BOOTSTRAP TABS -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">

                    <!-- TAB 1 -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="countrycontinent-tab" data-bs-toggle="tab"
                            data-bs-target="#countrycontinent" type="button" role="tab">
                            Region
                        </button>
                    </li>

                    <!-- TAB 2 -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="idname-tab" data-bs-toggle="tab"
                            data-bs-target="#idname" type="button" role="tab">
                            Disruption ID or Company Name
                        </button>
                    </li>

                    <!-- TAB 3 -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="criticality-tab" data-bs-toggle="tab"
                            data-bs-target="#criticality" type="button" role="tab">
                            Criticality
                        </button>
                    </li>

                </ul>

                <!-- Start TAB CONTENT WRAPPER -->
                <div class="tab-content" id="myTabContent">

                    <!-- TAB 1: Regional Disruption info -->
                    <div class="tab-pane fade show active" id="countrycontinent" role="tabpanel" aria-labelledby="countrycontinent-tab">

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

                    </div>  <!-- END TAB 1 -->

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

                    <!-- TAB 3: CRITICALITY -->
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

                    </div> <!-- END TAB 3 -->

                </div> <!-- END tab-content -->

            </div> <!-- col-md-9 -->
        </div> <!-- row -->
    </div> <!-- container -->

    <!-- Need refitting for Senior Manager Disruption Page-->
    <script>
        //Load Company Names when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadCompanies();
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
                    //Populate Company Options
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
            const start_date = document.getElementById("globalStartDate").value;
            const end_date = document.getElementById("globalEndDate").value;
            const regionType = document.getElementById("regionType_input").value;
            const region = document.getElementById("region_input").value;
            const input = regionType + ',' + region + ',' + start_date + ',' + end_date;
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
                xhtpp.open("GET", "regionSelectionOptions.php?q=" + input, true);
                console.log("regionSelectionOptions.php?q=" + input); 
                xhtpp.send();
        }
    </script>

    <script>
        function CheckUserInput() {

            const start_date = document.getElementById("globalStartDate").value;
            const end_date = document.getElementById("globalEndDate").value;

            const regionType = document.getElementById("regionType_input").value;
            console.log("Called!!");
     

            // Validate date range
            if (start_date === "" || end_date === "") {
                alert("Please provide a date range!");
                return false;
            }
            if (start_date >= end_date) {
                alert("Start date must be before end date!");
                return false;
            }

            if (regionType === "") {
                alert("Please select a Region Type (Country or Continent).");
                return false;
            }
            return true;
        }
    </script>

    <script>
        let my_JSON_object = ""; //Making global JSON object so that the user's selection can dynamically update when they select a dropdown filter

        function LoadRegionDisruptions(start_date, end_date, regionType, region) {

           input = start_date + "|" + end_date;
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
            if(!disruptionID || disruptionID.length === 0) { //If selection not selected or value is null, display all data
                data = my_JSON_object.companyAffectedByEvent;
                return false;
            }
            if(my_JSON_object.length === 0){
                alert("Submit Query First!");
                return false;
            }

            const data = my_JSON_object.companyAffectedByEvent.filter(item => item.EventID === disruptionID);
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
        // Filter based on disruption drop down
        function SearchByCompanyName(companyName) {
            if(!companyName || companyName.length === 0) { //If selection not selected or value is null, display all data
                data = my_JSON_object.companyAffectedByEvent;
                return false;
            }
            if(my_JSON_object.length === 0){
                alert("Submit Query First!");
                return false;
            }

            const data = my_JSON_object.companyAffectedByEvent.filter(item => item.CompanyName === companyName);
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

</body>
</html>
