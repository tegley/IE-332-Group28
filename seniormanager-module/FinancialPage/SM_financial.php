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

                <!-- BOOTSTRAP TABS -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">

                    <!-- TAB 1 -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="typehealth-tab" data-bs-toggle="tab"
                            data-bs-target="#typehealth" type="button" role="tab">
                            Average Financial Health
                        </button>
                    </li>

                    <!-- TAB 2 -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="region-tab" data-bs-toggle="tab"
                            data-bs-target="#region" type="button" role="tab">
                            Financials by Region
                        </button>
                    </li>

                    <!-- TAB 3 -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="addcompany-tab" data-bs-toggle="tab"
                            data-bs-target="#addcompany" type="button" role="tab">
                            Add Company
                        </button>
                    </li>

                </ul>

                <!-- Start TAB CONTENT WRAPPER -->
                <div class="tab-content" id="myTabContent">

                    <!-- TAB 1: AVERAGE FINANCIAL HEALTH BY COMPANY TYPE -->
                    <div class="tab-pane fade show active" id="typehealth" role="tabpanel" aria-labelledby="typehealth-tab">

                        <div class="area-header">Average Financial Health by Company</div>

                        <!-- Search Section INSIDE the tab -->
                        <div class="card mb-3">
                            <div class="card-body row">

                                <div class="col-md-4">
                                    <label>Company Type</label>
                                    <select class="form-control" id="companyType_input">
                                        <option value="">All Types</option>
                                        <option value="Manufacturer">Manufacturer</option>
                                        <option value="Distributor">Distributor</option>
                                        <option value="Retailer">Retailer</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" id="typeStart_input">
                                </div>

                                <div class="col-md-4">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" id="typeEnd_input">
                                </div>

                                <div class="col-12 mt-3 d-flex justify-content-center">
                                    <button type="button" class="btn btn-primary" onclick="if (Validate_Tab1()) LoadTypeHealth(document.getElementById('typeStart_input').value, document.getElementById('typeEnd_input').value, document.getElementById('companyType_input').value);">
                                    Submit</button>
                                </div>

                            </div>
                        </div>

                        <!-- List + Bar Chart -->
                        <div class="row">

                            <div class="col-md-6">
                                <div class="card" style="height: 350px;">
                                    <div class="card-header">Average Financial Health of Companies</div>
                                    <ul class="list-group list-group-flush" id="typeHealthList" style="max-height:700px; overflow-y: auto;">
                                        <p class="text-muted">Submit query to see results...</p>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card" style="height: 350px;">
                                    <div class="card-header">Visualization of Average Financial Health Scores</div>
                                    <div id="typeHealthBarChart" style="height: 300px;">
                                        <p class="text-muted">Submit query to see results...</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>  <!-- END TAB 1 -->

                    <!-- TAB 2: FINANCIALS BY REGION -->
                    <div class="tab-pane fade" id="region" role="tabpanel" aria-labelledby="region-tab">

                        <div class="area-header">Financials by Region</div>

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
                                    <button type="button" class="btn btn-primary"onclick="if (Validate_Tab2()) LoadRegionFinancials(document.getElementById('regionType_input').value,document.getElementById('region_input').value);">
                                        Submit
                                    </button>
                                </div>

                            </div>
                        </div>

                        <!-- List + Bar Chart -->
                        <div class="row">

                            <!-- LIST -->
                            <div class="col-md-6">
                                <div class="card" style="height: 350px;">
                                    <div class="card-header">List of Health Scores</div>
                                    <ul class="list-group list-group-flush" id="regionHealthList" style="max-height:700px; overflow-y: auto;">
                                        <p class="text-muted">Submit query to see results...</p>
                                    </ul>
                                </div>
                            </div>

                            <!-- Company Info -->
                            <div class="col-md-6">
                                <div class="card" style="height: 350px;">
                                    <div class="card-header">Company Information</div>
                                    <div id="companyInfo" style="height: 350px;">
                                        <p class="text-muted">Select a Company</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div> <!-- END TAB 2 -->

                    <!-- TAB 3: ADD COMPANY -->
                    <div class="tab-pane fade" id="addcompany" role="tabpanel" aria-labelledby="addcompany-tab">

                        <div class="area-header">Add New Company</div>

                        <div class="card mb-3">
                            <div class="card-body">

                                <form name="AddCompanyForm" onsubmit="return ValidateAddCompany()" method="post">

                                    <!-- ROW 1 -->
                                    <div class="row mb-4">

                                        <div class="col-md-4">
                                            <label>Company Name</label>
                                            <input type="text" class="form-control" name="CompanyName">
                                        </div>

                                        <div class="col-md-4">
                                            <label>Company Type</label>
                                            <select class="form-control" name="CompanyType">
                                                <option value="">Select Type</option>
                                                <option value="Manufacturer">Manufacturer</option>
                                                <option value="Distributor">Distributor</option>
                                                <option value="Retailer">Retailer</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label>Location</label>
                                            <input type="text" class="form-control" name="Location">
                                        </div>

                                    </div>

                                    <!-- ROW 2 -->
                                    <div class="row mb-4">

                                        <div class="col-md-4">
                                            <label>Shipping</label>
                                            <input type="text" class="form-control" name="Shipping">
                                        </div>

                                        <div class="col-md-4">
                                            <label>Receiving</label>
                                            <input type="text" class="form-control" name="Receiving">
                                        </div>

                                        <div class="col-md-4">
                                            <label>Inventory</label>
                                            <input type="text" class="form-control" name="Inventory">
                                        </div>

                                    </div>

                                    <!-- ADD BUTTON -->
                                    <div class="col-12 mt-3 d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary px-4">
                                            Add
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>

                    </div> <!-- END TAB 3 -->

                </div> <!-- END tab-content -->

            </div> <!-- col-md-9 -->
        </div> <!-- row -->
    </div> <!-- container -->

    <!-- Need refitting for Senior Manager Financial Page-->
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


                } // END onload function
                else{
                    console.log("Failed");
                }
            }

            xhtpp.open("GET", "regionOpts.php?q=" + region, true);
            console.log("regionOpts.php?q=" + region);
            xhtpp.send();
    } //End LoadRegionList
    </script>

    <script>
        function Validate_DateRange(start, end) {
            if (start === "" || end === "") {
                alert("Please provide a start and end date.");
                return false;
            }
            if (start >= end) {
                alert("Start date must be before end date.");
                return false;
            }
            return true;
        }

        // TAB 1 validator
        function Validate_Tab1() {
            const start = document.getElementById("typeStart_input").value;
            const end = document.getElementById("typeEnd_input").value;

            if (start === "" || end === "") {
                alert("Please provide a start and end date.");
                return false;
            }
            if (start >= end) {
                alert("Start date must be before end date.");
                return false;
            }
            return true;
        }

        // TAB 2 validator
        function Validate_Tab2() {
            const regionType = document.getElementById("regionType_input").value;
            const region = document.getElementById("region_input").value;

            if (regionType === "") {
                alert("Please select a Region Type (Country or Continent).");
                return false;
            }

            return true;
        }
    </script>

    <script>
        var my_JSON_object;

        function LoadTypeHealth(start_date, end_date, companyType) {
            //Break start date and end date into their year and quarter for PHP purposes
            const start = new Date(start_date);
            const end = new Date(end_date);
            let startYear = start.getFullYear();
            let startMonth = start.getMonth() + 1; // getMonth() built in JavaScript function considers Jan as 0 so add one
            let startQuarter = Math.ceil(startMonth / 3); //Math.Ciel rounds up, so if the user put something in May, 5/3 = 1.67 will put May as Quarter 2, which we want! In contrast, if it were July 7/3 = 2.33 will get saved as 3, also what we want!

            let endYear = end.getFullYear();
            let endMonth = end.getMonth() + 1;
            let endQuarter = Math.ceil(endMonth / 3);


            input = startYear + "|" + startMonth + "|" + endYear + "|" + endQuarter;

            xhtpp = new XMLHttpRequest();

            xhtpp.onload = function () {
                if (this.readyState == 4 && this.status == 200) {

                    data = JSON.parse(this.responseText);
                    console.log(JSON.stringify(data));

                    // List of health scores
                    const finHealthDiv = document.getElementById("typeHealthList");
                    finHealthDiv.innerHTML = ""; //Clear out placeholder

                    if (data.length > 0) {
                        data.forEach(item => {
                            const li = document.createElement("li");
                            li.className = "list-group-item";
                            li.textContent = `Company Name: ${item.CompanyName} Average Health Score: ${item.avgHealth}`;
                            finHealthDiv.appendChild(li);
                        });
                    } else {
                        finHealthDiv.innerHTML = '<p class="text-muted">No health data found</p>';
                    }
                    //Make a cool, interactive bar chart
            const finHealthBarDiv = document.getElementById("typeHealthBarChart");
            finHealthBarDiv.innerHTML = ""; //Clear out placeholder

            if (data.length > 0) {
                // Create chart container
                finHealthBarDiv.innerHTML = `
                    <div class="chart-container" style="background: linear-gradient(to bottom right, #f8fafc, #e2e8f0); padding: 24px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <h3 style="margin: 0; color: #1f2937; font-size: 24px; font-weight: bold;">Company Health Scores</h3>
                            <button id="sortToggle" style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M7 15l5 5 5-5M7 9l5-5 5 5"/>
                                </svg>
                                <span id="sortLabel">Sort by Name</span>
                            </button>
                        </div>
                        <div style="background: white; border-radius: 8px; padding: 16px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.06); max-height: 600px; overflow-y: auto;">
                            <svg id="healthChart" width="100%" height="${data.length * 35}"></svg>
                        </div>
                        <div style="display: flex; justify-content: center; gap: 24px; margin-top: 16px; font-size: 14px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 16px; height: 16px; background: #10b981; border-radius: 3px;"></div>
                                <span>Excellent (85+)</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 16px; height: 16px; background: #fbbf24; border-radius: 3px;"></div>
                                <span>Good (70-84)</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 16px; height: 16px; background: #fb923c; border-radius: 3px;"></div>
                                <span>Fair (60-69)</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 16px; height: 16px; background: #ef4444; border-radius: 3px;"></div>
                                <span>Poor (&lt;60)</span>
                            </div>
                        </div>
                    </div>
                `;

                // Process and sort data
                let sortBy = 'health';
                const processedData = data.map(item => ({
                    name: item.CompanyName,
                    health: parseFloat(item.avgHealth)
                }));

                function getColor(health) {
                    if (health >= 85) return '#10b981';
                    if (health >= 70) return '#fbbf24';
                    if (health >= 60) return '#fb923c';
                    return '#ef4444';
                }

                function sortData(dataToSort, sortType) {
                    return [...dataToSort].sort((a, b) => {
                        if (sortType === 'health') {
                            return b.health - a.health;
                        } else {
                            return a.name.localeCompare(b.name);
                        }
                    });
                }

                function drawChart(chartData) {
                    const svg = document.getElementById('healthChart');
                    const containerWidth = svg.parentElement.clientWidth - 32;
                    const margin = { top: 5, right: 30, bottom: 5, left: 200 };
                    const width = containerWidth - margin.left - margin.right;
                    const height = chartData.length * 35;
                    const barHeight = 25;

                    svg.setAttribute('height', height);
                    svg.innerHTML = '';

                    // Create scales
                    const maxHealth = 100;
                    const xScale = (value) => (value / maxHealth) * width;

                    // Draw bars
                    chartData.forEach((item, i) => {
                        const y = i * 35 + 5;
                        const barWidth = xScale(item.health);

                        // Bar group
                        const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                        g.setAttribute('class', 'bar-group');
                        g.style.cursor = 'pointer';

                        // Company name
                        const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                        text.setAttribute('x', margin.left - 10);
                        text.setAttribute('y', y + barHeight / 2 + 4);
                        text.setAttribute('text-anchor', 'end');
                        text.setAttribute('fill', '#374151');
                        text.setAttribute('font-size', '12');
                        text.textContent = item.name.length > 25 ? item.name.substring(0, 25) + '...' : item.name;

                        // Bar background (light gray)
                        const bgRect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                        bgRect.setAttribute('x', margin.left);
                        bgRect.setAttribute('y', y);
                        bgRect.setAttribute('width', width);
                        bgRect.setAttribute('height', barHeight);
                        bgRect.setAttribute('fill', '#f3f4f6');
                        bgRect.setAttribute('rx', '4');

                        // Actual bar
                        const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                        rect.setAttribute('x', margin.left);
                        rect.setAttribute('y', y);
                        rect.setAttribute('width', barWidth);
                        rect.setAttribute('height', barHeight);
                        rect.setAttribute('fill', getColor(item.health));
                        rect.setAttribute('rx', '4');

                        // Tooltip on hover
                        g.addEventListener('mouseenter', function(e) {
                            rect.setAttribute('opacity', '0.8');
                            showTooltip(e, item);
                        });
                        g.addEventListener('mouseleave', function() {
                            rect.setAttribute('opacity', '1');
                            hideTooltip();
                        });

                        g.appendChild(text);
                        g.appendChild(bgRect);
                        g.appendChild(rect);
                        svg.appendChild(g);
                    });

                    // X-axis labels
                    const xAxisLabels = [0, 25, 50, 75, 100];
                    xAxisLabels.forEach(value => {
                        const x = margin.left + xScale(value);
                        const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                        line.setAttribute('x1', x);
                        line.setAttribute('y1', 0);
                        line.setAttribute('x2', x);
                        line.setAttribute('y2', height);
                        line.setAttribute('stroke', '#e5e7eb');
                        line.setAttribute('stroke-dasharray', '3,3');
                        svg.appendChild(line);

                        const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                        label.setAttribute('x', x);
                        label.setAttribute('y', height - 2);
                        label.setAttribute('text-anchor', 'middle');
                        label.setAttribute('fill', '#6b7280');
                        label.setAttribute('font-size', '11');
                        label.textContent = value;
                        svg.appendChild(label);
                    });
                }

                // Tooltip functions
                function showTooltip(e, item) {
                    let tooltip = document.getElementById('chartTooltip');
                    if (!tooltip) {
                        tooltip = document.createElement('div');
                        tooltip.id = 'chartTooltip';
                        tooltip.style.position = 'fixed';
                        tooltip.style.background = '#1f2937';
                        tooltip.style.color = 'white';
                        tooltip.style.padding = '8px 12px';
                        tooltip.style.borderRadius = '6px';
                        tooltip.style.fontSize = '14px';
                        tooltip.style.pointerEvents = 'none';
                        tooltip.style.zIndex = '1000';
                        tooltip.style.boxShadow = '0 4px 6px rgba(0,0,0,0.3)';
                        document.body.appendChild(tooltip);
                    }
                    tooltip.innerHTML = `<strong>${item.name}</strong><br>Health Score: <strong>${item.health.toFixed(2)}</strong>`;
                    tooltip.style.display = 'block';
                    tooltip.style.left = (e.pageX + 10) + 'px';
                    tooltip.style.top = (e.pageY + 10) + 'px';
                }

                function hideTooltip() {
                    const tooltip = document.getElementById('chartTooltip');
                    if (tooltip) {
                        tooltip.style.display = 'none';
                    }
                }

                // Initial draw
                let sortedData = sortData(processedData, sortBy);
                drawChart(sortedData);

                // Sort button handler
                document.getElementById('sortToggle').addEventListener('click', function() {
                    sortBy = sortBy === 'health' ? 'name' : 'health';
                    document.getElementById('sortLabel').textContent = sortBy === 'health' ? 'Sort by Name' : 'Sort by Health';
                    sortedData = sortData(processedData, sortBy);
                    drawChart(sortedData);
                });

            } else {
                finHealthBarDiv.innerHTML = '<p class="text-muted">No health data found</p>';
            }
                   

                } // END readyState if
            } // END onload function

            xhtpp.open("GET", "seniorFinancialQueries.php?q=" + input + "&g=" + companyType, true);
            xhtpp.send();
        } // END CompanyInformationAJAX

        function LoadRegionFinancials(regionType, region) {

    const input = regionType + "|" + region;

    xhtpp = new XMLHttpRequest();

    xhtpp.onload = function () {

        if (this.readyState == 4 && this.status == 200) {

            data = JSON.parse(this.responseText);
            console.log(JSON.stringify(data));

            const finHealthDiv = document.getElementById("regionHealthList");
            finHealthDiv.innerHTML = "";

            if (data.length > 0) {
                data.forEach(item => {
                    const div = document.createElement("div");
                    div.className = "list-item";
                    div.innerHTML = `
                        <strong>Company Name:</strong> ${item.CompanyName}<br>
                        <strong>Average Health Score:</strong> ${item.avgHealth}<br>
                        <strong>Country:</strong> ${item.CountryName}<br>
                        <strong>Continent:</strong> ${item.ContinentName}
                    `;

                    div.addEventListener('click', function() {
                        document.querySelectorAll('.list-item').forEach(el => el.style.backgroundColor = '#f8f9fa');
                        this.style.backgroundColor = '#e3f2fd';
                        CompanyInformationAJAX(item.CompanyName);
                    });

                    finHealthDiv.appendChild(div);
                });
            } else {
                finHealthDiv.innerHTML = '<p class="text-muted">No health data found</p>';
            }

        }
    };

    console.log("Sending: seniorRegionalFinancesQueries.php?q=" + input);
    xhtpp.open("GET", "seniorRegionalFinancesQueries.php?q=" + input, true);
    xhtpp.send();
}


function CompanyInformationAJAX(company_name) {
    input = company_name;
    xhtpp = new XMLHttpRequest();
    xhtpp.onload = function () {
    if (this.readyState == 4 && this.status == 200) {
        my_JSON_object = JSON.parse(this.responseText);
        console.log(JSON.stringify(my_JSON_object));
    
        //Company Information - Important Info
        const companyInfoDiv = document.getElementById("companyInfo");
        companyInfoDiv.innerHTML = ""; //Clear out placeholder
        address = String(my_JSON_object.companyInfo[0].City) + ", " + String(my_JSON_object.companyInfo[0].CountryName);
    
        var div1 = document.createElement("div");
        div1.className = "list-item";
        div1.innerHTML = `<strong>Company Name:</strong> ${my_JSON_object.companyInfo[0].CompanyName}`;
        companyInfoDiv.appendChild(div1);
        var li5 = document.createElement("div");
        li5.className = "list-item";
        li5.innerHTML = `<strong>CompanyID:</strong> ${my_JSON_object.companyInfo[0].CompanyID}`;
        companyInfoDiv.appendChild(li5);
        var li2 = document.createElement("div");
        li2.className = "list-item";
        li2.innerHTML = `<strong>Company Address:</strong> ${address}`;
        companyInfoDiv.appendChild(li2);
        var li3 = document.createElement("div");
        li3.className = "list-item";
        li3.innerHTML = `<strong>Company Type:</strong> ${my_JSON_object.companyInfo[0].Type}`;
        companyInfoDiv.appendChild(li3);
        var li4 = document.createElement("div");
        li4.className = "list-item";
        li4.innerHTML = `<strong>Company Tier:</strong> ${my_JSON_object.companyInfo[0].TierLevel}`;
        companyInfoDiv.appendChild(li3);    
    }
    };
    xhtpp.open("GET", "SCMhomepage_queries.php?q=" + input, true);
    console.log("Sending request with q=" + input);
    xhtpp.send();
}
    </script>

</body>
</html>
