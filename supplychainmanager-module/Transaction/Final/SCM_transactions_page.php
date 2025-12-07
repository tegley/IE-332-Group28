<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCM Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.plot.ly/plotly-3.3.0.min.js" charset="utf-8"></script> <!-- JavaScript for Plotly -->
    
    <style>
        @import "standardized_project_formatting.css";
        #form-box {
            height: 250px;
            box-sizing: border-box;
        }

        .section-banner {
            background-color: #0f6fab;
            color: white;
            padding: 18px;
            font-size: 32px;
            margin-top: 25px;
            border-radius: 6px;
            font-family: Cambria, serif;
        }

        .scroll-box {
            height: 180px;
            overflow-y: auto;
            border: 1px solid #aaa;
            padding: 10px;
            border-radius: 6px;
            background-color: white;
        }

        .scroll-box-MainContent {
            height: 300px;
            overflow-y: auto;
            border: 1px solid #aaa;
            padding: 10px;
            border-radius: 6px;
            background-color: white;
        }

        .stats-table td {
            border: 1px solid #666;
            padding: 10px;
            font-size: 15px;
        }

        .stats-header {
            background-color: #cfcfcf;
            font-weight: bold;
            text-align: center;
            padding: 8px;
        }

        .collapsible-header {
            cursor: pointer;
            user-select: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .collapse-arrow {
            transition: transform 0.3s ease;
        }
        .collapse-arrow.open {
            transform: rotate(180deg);
        }

        .collapse-content {
            overflow: hidden;
            transition: max-height 0.35s ease;
            max-height: 0;
        }
        .collapse-content.open {
            max-height: 2000px;
        }


        body {
            background-color: #f5f6f8;
        }

        .list-item {
            padding: 8px;
            margin: 4px 0;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #0f6fab;
        }

        .col-md-4-custom {
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }

        /* Chart container sizing for responsive Plotly charts */
        #lineChartAdjustments, 
        #pieChartOut, 
        #barChartDisrupt, 
        #lineChartShipments {
        width: 100%;
        min-height: 300px;
        }
    
        /* Larger height for the full-width delivery performance chart */
        #lineChartShipments {
        min-height: 400px;
        }
</style>

<script>
    function toggleCollapse(sectionId) {
        const content = document.getElementById(sectionId);
        const arrow = document.querySelector(`[data-arrow='${sectionId}']`);
        content.classList.toggle("open");
        arrow.classList.toggle("open");
    }
</script>

</head>
<body>
<!-- Company header -->
<h1>Global Electronics LLC</h1>

<div class="container">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3">
            <div id="supplychainmanager_sidebar"></div>
            <script>
                fetch('supplychainmanager_sidebar.html')
                    .then(r => r.text())
                    .then(html => document.getElementById('supplychainmanager_sidebar').innerHTML = html)
                    .catch(err => console.log('Sidebar not loaded'));
            </script>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">

            <!-- Dashboard Header -->
            <div class="card" id="dashboard-header">
                SCM Dashboard
            </div>

            <!-- --------------------- COMPANY TRANSACTIONS --------------------- -->
            <h2 class="section-banner collapsible-header" onclick="toggleCollapse('companySection')">
                Company Transactions
                <span class="collapse-arrow" data-arrow="companySection">▼</span>
            </h2>

            <div id="companySection" class="collapse-content">

                <form name="myForm" id="form-box">
                    <!-- Overall Filter -->
                    <div id="FilterChooser"> 
                    <label for="myForm">Filter By:</label>
                    <select id="filterType" class="form-select mb-3">
                        <option value="" disabled selected>Select Filter</option>
                        <option value="company">Company</option>
                        <option value="city">City</option>
                        <option value="country">Country</option>
                        <option value="continent">Continent</option>
                    </select>
                    </div>
       
                    <!-- Company sub-filter -->
                    <div id="companyFilter" style="display:none;"> 
                        <select id="company_input" class="form-select mb-2">
                            <option value="">Loading companies...</option>
                        </select>
                    </div>

                    <!-- City sub-filter -->
                    <div id="cityFilter" style="display:none;"> 
                        <select id="city_input" class="form-select mb-2">
                            <option value="">Loading cities...</option>
                        </select>
                    </div>

                    <!-- Country sub-filter -->
                    <div id="countryFilter" style="display:none;"> 
                        <select id="country_input" class="form-select mb-2">
                            <option value="">Loading countries...</option>
                        </select>
                    </div>

                    <!-- Continent filter -->
                    <div id="continentFilter" class="filter-group" style="display:none;">
                    <select id="continent_input" class="form-select">
                        <option value="" disabled selected>Select Continent</option>
                        <option>Africa</option>
                        <option>Asia</option>
                        <option>Oceania</option> 
                        <option>Europe</option>
                        <option>North America</option>
                        <option>South America</option>
                    </select>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Start Date e.g. (XXXX-XX-XX)</label>
                            <input type="date" class="form-control"  value="2020-09-09" id="startDate">
                        </div>
                        <div class="col-md-6">
                            <label>End Date e.g. (XXXX-XX-XX)</label>
                            <input type="date" class="form-control"  value="2025-09-09" id="endDate">
                        </div>
                    </div>

                    <button type="button" onclick="CheckUserInput()" class="btn btn-primary">Submit</button> 

                </form>

                <!-- Status message for user validation -->
                <div id="statusMessage" class="mt-3"></div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5>Leaving From</h5>
                        <div class="scroll-box" id="leavingBox">
                            <p class="text-muted">Submit query to see results...</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5>Arriving At</h5>
                        <div class="scroll-box" id="arrivingBox">
                            <p class="text-muted">Submit query to see results...</p>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">

                    <div class="col-md-6">
                        <div class="stats-header">Adjustments Overview</div>
                        <div class="scroll-box" id="adjustmentDetails">
                            <p class="text-muted">Submit query to see results...</p>
                        </div>
                    </div>

                    <div class="col-md-6 text-center">
                        <div class="stats-header">Top Companies by Shipment Volume</div>
                           <div class = "col-md-6 text-center" id = "lineChartAdjustments"></div>
                    </div>

                </div>

            </div> <!-- End Company transactions -->

            <!-- --------------------- DISTRIBUTOR DETAILS --------------------- -->
            <h2 class="section-banner collapsible-header" onclick="toggleCollapse('distributorSection')">
                Distributor Details
                <span class="collapse-arrow" data-arrow="distributorSection">▼</span>
            </h2>

            <div id="distributorSection" class="collapse-content">

                 <form class="mt-3" id="myFormDist">
                    <label>Distributor Name</label>
                    <select class="form-control" id="Distributor_Name"></select>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Start Date e.g. (XXXX-XX-XX)</label>
                            <input type="date" class="form-control" id = "StartDist">
                        </div>
                        <div class="col-md-6">
                            <label>End Date e.g. (XXXX-XX-XX)</label>
                            <input type="date" class="form-control" id = "EndDist">
                        </div>
                    </div>

                    <button type="button" onclick="CheckUserInputDist()" class="btn btn-primary">Submit</button>
                </form>

                <!-- Status message for user validation -->
                <div id="statusMessageDist" class="mt-3"></div>

                <div class = "scroll-box-MainContent" id = disruptDets>
                    <div class="row mt-4">

                        <div class="col-md-4">
                            <div class="stats-header">Statistics</div>

                            <table class="table stats-table">
                                <tr><td>On Time Delivery Rate</td><td id="onTimeRate">--</td></tr>
                                <tr><td>Total Quantity Shipped</td><td id="totalQty">--</td></tr>
                                <tr><td>Average Shipment Qty</td><td id="avgQty">--</td></tr>
                                <tr><td>Total Shippings</td><td id="totalShippingsCell">--</td></tr>
                                <tr><td>Disruption Exposure</td><td id="disruption">--</td></tr>
                            </table>
                        </div>

                        <div class="col-md-4">
                            <div class="stats-header">Products Handled</div>
                            <div class="scroll-box" id="productsHandled">
                                <p class="text-muted">Submit query to see results...</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="stats-header">Shipments Out</div>
                            <div class="scroll-box" id="shipmentsOut">
                                <p class="text-muted">Submit query to see results...</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">

                        <div class="col-md-4 text-center">
                            <div class="stats-header">Status Distribution</div>
                            <div class = "col-md-6 text-center" id = "pieChartOut"></div>
                        </div>

                        <div class="col-md-4">
                            <div class="stats-header">Disruption Event Breakdown</div>
                            <div class="scroll-box" id="disruptDetails">
                                <p class="text-muted">Submit query to see results...</p>
                            </div>
                        </div>

                        <div class="col-md-4 text-center">
                            <div class="stats-header">Disruption Event Distribution</div>
                            <div class = "col-md-6 text-center" id = "barChartDisrupt"></div>
                        </div>

                    </div>
                </div> 

                <div class="row mt-4">
                        <div class="stats-header">Actual Date Recieved versus Promised</div>
                        <div class = "col-md-6 text-center" id = "lineChartShipments"></div>
                </div>

            </div> <!-- End Distributor Details -->


           

        </div> <!-- End col-md-9 -->

    </div> <!-- End row -->
</div> <!-- End container -->

<script> //Event listener functions
//Run this when the page loads to populate drop downs and prefill date with date range examples
document.addEventListener('DOMContentLoaded', function() {
    loadDistributors();
});

</script>

<script> //JavaScript for dropdown filter appearance space minimization
//Filter IDs 
const myForm = document.getElementById("filterType");  //Overarching filter
const companyFilter = document.getElementById("companyFilter");
const cityFilter = document.getElementById("cityFilter");
const countryFilter = document.getElementById("countryFilter");
const continentFilter = document.getElementById("continentFilter");

//Input IDs
const company_input = document.getElementById("company_input");
const city_input = document.getElementById("city_input");
const country_input = document.getElementById("country_input");
const continent_input = document.getElementById("continent_input");

//Add EventListener 
myForm.addEventListener("change", function () {  // grouping drop downs into a function
    console.log(myForm.value);
    //Reset filters display
    companyFilter.style.display = "none";
    cityFilter.style.display = "none";
    countryFilter.style.display = "none";
    continentFilter.style.display = "none";

    //Resets inputs 
    company_input.value = "";
    city_input.value = "";
    country_input.value = "";
    continent_input.selectedIndex = 0;

    //If statements - display functionality
    if (this.value === "company") { 
      companyFilter.style.display = "block";
    }
    if (this.value === "city") {
      cityFilter.style.display = "block";
    }
    if (this.value === "country"){
      countryFilter.style.display ="block";
    }
    if (this.value === "continent"){
      continentFilter.style.display ="block";
    }
});
</script>

<script> //JavaScript functions
function loadDistributors() {
    // Fetch data from your PHP file that returns all the data
    fetch('distributorList.php')  // This should return your full JSON object
        .then(response => response.json())
        .then(data => {
            // Populate Distributors dropdown
            const distDropdown = document.getElementById('Distributor_Name');
            distDropdown.innerHTML = '';
            
            const defaultDistOption = document.createElement('option');
            defaultDistOption.value = '';
            defaultDistOption.textContent = 'Select a distributor';
            distDropdown.appendChild(defaultDistOption);
            
            data.distributors.forEach(distributor => {
                const option = document.createElement('option');
                option.value = distributor.CompanyName;
                option.textContent = distributor.CompanyName;
                distDropdown.appendChild(option);
            });

            // Populate Companies dropdown
            const companyDropdown = document.getElementById('company_input');
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

            // Populate Countries dropdown
            const countryDropdown = document.getElementById('country_input');
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

            // Populate Cities dropdown (remove nulls)
            const cityDropdown = document.getElementById('city_input');
            cityDropdown.innerHTML = '';
            
            const defaultCityOption = document.createElement('option');
            defaultCityOption.value = '';
            defaultCityOption.textContent = 'Select a city';
            cityDropdown.appendChild(defaultCityOption);
            
            // Filter out null values caused by the Python code
            const nonnullCities = data.city.map(c => c.City).filter(city => city !== null).sort();
            nonnullCities.forEach(cityName => {
                const option = document.createElement('option');
                option.value = cityName;
                option.textContent = cityName;
                cityDropdown.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading data:', error);
        });
}

function CheckUserInput() {
    // Extract User input from web page
    const filterType = document.getElementById("filterType").value; //Overarching filter
    const companyInput = document.getElementById("company_input").value; //Subfilter
    const cityInput = document.getElementById("city_input").value; //SUbfilter
    const countryInput = document.getElementById("country_input").value; //Subfilter
    const continentInput = document.getElementById("continent_input").value; //Subfilter
    const startDate = document.getElementById("startDate").value;
    const endDate = document.getElementById("endDate").value;

    console.log("Filter Type:", filterType);
    console.log("Start Date:", startDate);
    console.log("End Date:", endDate);

    // Validate user input dates
    if (!startDate || !endDate) {
        document.getElementById("statusMessage").innerHTML = 
            '<div class="alert alert-warning">Please select both start and end dates</div>';
        return;
    }
    //Variables needed specifically for comparing start and end dates
    const start = new Date(startDate);
    const end = new Date(endDate);
    // console.log("Start:", start);
    // console.log("End:", end);
    if (start >= end) {
        document.getElementById("statusMessage").innerHTML = 
            '<div class="alert alert-warning">Start date must be before end date!</div>';
        return;
    }
    // Validate user input filter
    if(filterType == "") {
        document.getElementById("statusMessage").innerHTML = 
            '<div class="alert alert-warning">Please select a filter!</div>';
            return;
    }
    //Define what user input as their choice of filter
    let userInput = ""; //Placeholder for user's choice of filter
    if(filterType === "company") {
        if(companyInput === ""){
            document.getElementById("statusMessage").innerHTML = 
            '<div class="alert alert-warning">Please select a company!</div>';
            return;
        }
        else{
            userInput = companyInput;
        }
    }
    if(filterType === "city") {
        if(cityInput === ""){
            document.getElementById("statusMessage").innerHTML = 
            '<div class="alert alert-warning">Please select a city!</div>';
            return;
        }
        else{
            userInput = cityInput;
        }
    }
    if(filterType === "country") {
        if(countryInput === ""){
            document.getElementById("statusMessage").innerHTML = 
            '<div class="alert alert-warning">Please select a country!</div>';
            return;
        }
        else{
            userInput = countryInput;
        }
    }
    if(filterType === "continent") {
        if(continentInput === ""){
            document.getElementById("statusMessage").innerHTML = 
            '<div class="alert alert-warning">Please select a continent!</div>';
            return;
        }
        else{
            userInput = continentInput;
        }
    }
    console.log("User Input:", userInput);
    showCustomer(startDate, endDate, userInput, filterType)
}

function showCustomer(startDate, endDate, userInput, filterType) {
    console.log("showCustomer() called");
    // Package parameters
    const q = `${startDate},${endDate}`;
    const g = `${filterType}|${userInput}`;

    console.log("Sending request with q=" + q + " and g=" + g);

    // Show loading message
    document.getElementById("statusMessage").innerHTML = 
        '<div class="alert alert-info">Loading data...</div>';
    document.getElementById("leavingBox").innerHTML = '<p class="text-muted">Loading...</p>';
    document.getElementById("arrivingBox").innerHTML = '<p class="text-muted">Loading...</p>';
    document.getElementById("adjustmentDetails").innerHTML = '<p class="text-muted">Loading...</p>';
    document.getElementById("lineChartAdjustments").innerHTML = '<p class="text-muted">Loading chart...</p>';
    

    // Create XMLHttpRequest
    const xhttp = new XMLHttpRequest();

    xhttp.onload = function() {
        console.log("Response received. Status:", this.status);
        console.log("Ready state:", this.readyState);
        
        if (this.readyState == 4 && this.status == 200) {
            console.log("Response text:", this.responseText.substring(0, 200));

            try {
                // Parse the JSON returned from PHP
                const data = JSON.parse(this.responseText);
                console.log("Parsed data:", data);

                // Clear boxes
                const leavingDiv = document.getElementById("leavingBox");
                const arrivingDiv = document.getElementById("arrivingBox");
                const adjusmentsDiv = document.getElementById("adjustmentDetails");
                leavingDiv.innerHTML = "";
                arrivingDiv.innerHTML = "";
                adjusmentsDiv.innerHTML = "";
                document.getElementById("lineChartAdjustments").innerHTML = "";

                // Display leaving shipments
                if (data.leavingCompany && data.leavingCompany.length > 0) {
                    data.leavingCompany.forEach(item => {
                        const div = document.createElement("div");
                        div.className = "list-item";
                        div.innerHTML = `
                            <strong>Shipment ID:</strong> ${item.ShipmentID}<br>
                            <strong>Company:</strong> ${item.CompanyName}<br>
                            <strong>Quantity:</strong> ${item.Quantity}
                        `;
                        leavingDiv.appendChild(div);
                    });
                } else {
                    leavingDiv.innerHTML = '<p class="text-muted">No shipments found</p>';
                }

                // Display arriving shipments
                if (data.arrivingCompany && data.arrivingCompany.length > 0) {
                    data.arrivingCompany.forEach(item => {
                        const div = document.createElement("div");
                        div.className = "list-item";
                        div.innerHTML = `
                            <strong>Receiving ID:</strong> ${item.ReceivingID}<br>
                            <strong>Company:</strong> ${item.CompanyName}<br>
                            <strong>Quantity:</strong> ${item.QuantityReceived}
                        `;
                        arrivingDiv.appendChild(div);
                    });
                } else {
                    arrivingDiv.innerHTML = '<p class="text-muted">No shipments found</p>';
                }

                //Display Adjustment Information
                if (data.adjustments && data.adjustments.length > 0) {
                    data.adjustments.forEach(item => {
                        const div = document.createElement("div");
                        div.className = "list-item";
                        div.innerHTML = `
                            <strong>Adujustment ID:</strong> ${item.AdjustmentID}, <strong>Date:</strong> ${item.AdjustmentDate} <br>
                            <strong>Company:</strong> ${item.CompanyName}<br>
                            <strong>Product & Quanitiy Involved:</strong> ${item.ProductID} , ${item.QuantityChange} <br>
                            <strong>Reason for Adjustment:</strong> ${item.Reason}
                        `;
                        adjusmentsDiv.appendChild(div);
                    });
                } else {
                    adjusmentsDiv.innerHTML = '<p class="text-muted">No adjustments found</p>';
                }
                if (data.leavingCompany && data.leavingCompany.length > 0) {
            
            // Aggregate quantities by company
            const companyCounts = {};
            data.leavingCompany.forEach(item => {
                const company = item.CompanyName;
                const quantity = parseInt(item.Quantity) || 0;
                companyCounts[company] = (companyCounts[company] || 0) + quantity;
            });

            // Filter by date range
        const filteredData = data.leavingCompany.filter(item => {
            const itemDate = new Date(item.ActualDate); // Your date field
            return itemDate >= new Date(startDate) && itemDate <= new Date(endDate);
        });

        const sortedCompanies = Object.entries(companyCounts)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 10);

        var trace = {
            y: sortedCompanies.map(item => item[0]),  // Horizontal bars
            x: sortedCompanies.map(item => item[1]),
            type: 'bar',
            orientation: 'h',
            marker: { 
                color: sortedCompanies.length === 1 ? '#d95f02' : sortedCompanies.map((item, i) => i === 0 ? '#d95f02' : '#0f6fab'),
                line: { color: '#fff', width: 1 }
            },
            hovertemplate: '<b>%{y}</b><br>Quantity: %{x:,}<extra></extra>'
        };

        var layout = {
            title: {
                text: `Top Companies by Shipment Volume<br><sub>${startDate} to ${endDate}</sub>`,
            },
            xaxis: { title: 'Total Quantity Shipped', tickformat: ',' },
            yaxis: { title: '', automargin: true },
            autosize: true,
            margin: { l: 180, r: 50, t: 80, b: 50 }
        };

            Plotly.newPlot('lineChartAdjustments', [trace], layout, {responsive: true});
        } else {
            document.getElementById("lineChartAdjustments").innerHTML = 
                '<p class="text-muted">No shipment data available</p>';
        }

                // Update status message
                document.getElementById("statusMessage").innerHTML = 
                    `<div class="alert alert-success">Data loaded successfully for date range ${startDate} to ${endDate}: ${data.leavingCompany ? data.leavingCompany.length : 0} leaving, ${data.arrivingAt ? data.arrivingAt.length : 0} arriving</div>`;
                
            } catch (error) {
                console.error("Error parsing response:", error);
                document.getElementById("statusMessage").innerHTML = 
                    `<div class="alert alert-danger">Error parsing data: ${error.message}</div>`;
                document.getElementById("leavingBox").innerHTML = '<p class="text-danger">Error loading data</p>';
                document.getElementById("arrivingBox").innerHTML = '<p class="text-danger">Error loading data</p>';
            }

        } else if (this.readyState == 4) {
            console.error("Error loading data. Status:", this.status);
            document.getElementById("statusMessage").innerHTML = 
                `<div class="alert alert-danger">Error loading data. Status: ${this.status}</div>`;
        }
    };

    xhttp.onerror = function() {
        console.error("Network error occurred");
        document.getElementById("statusMessage").innerHTML = 
            '<div class="alert alert-danger">Network error occurred. Check console for details.</div>';
    };

    // Send the request
    const url = "SCMTransactionQueries.php?q=" + encodeURIComponent(q) + "&g=" + encodeURIComponent(g);
    console.log("Opening request to:", url);
    
    xhttp.open("GET", url, true);
    xhttp.send();
}

function CheckUserInputDist() {
    // Extract User input from web page
    const CompanyName = document.getElementById("Distributor_Name").value;
    const startDate = document.getElementById("StartDist").value;
    const endDate = document.getElementById("EndDist").value;

    console.log("Distributor Name:", CompanyName);
    console.log("Start Date:", startDate);
    console.log("End Date:", endDate);

    // Validate user properly input dates and distributor
    if (CompanyName == "") {
        document.getElementById("statusMessageDist").innerHTML = 
            '<div class="alert alert-warning">Please select a distributor!</div>';
        return;
    }
    if (!startDate || !endDate) {
        document.getElementById("statusMessageDist").innerHTML = 
            '<div class="alert alert-warning">Please select both start and end dates</div>';
        return;
    }
    //Variables needed specifically for comparing start and end dates
    const start = new Date(startDate);
    const end = new Date(endDate);
    // console.log("Start:", start);
    // console.log("End:", end);
    if (start >= end) {
        document.getElementById("statusMessageDist").innerHTML = 
            '<div class="alert alert-warning">Start date must be before end date!</div>';
        return; 
    }
    showCustomerDistributors(startDate, endDate, CompanyName)
}

function showCustomerDistributors(startDate, endDate, CompanyName) {
    console.log("showCustomerDistributors() called");

    // Package parameters
    const q = `${startDate},${endDate}`;
    const g = CompanyName;

    console.log("Sending request with q=" + q + " and g=" + g);
    // Show loading message
    document.getElementById("statusMessageDist").innerHTML = 
        '<div class="alert alert-info">Loading data...</div>';
    document.getElementById("productsHandled").innerHTML = '<p class="text-muted">Loading...</p>';
    document.getElementById("shipmentsOut").innerHTML = '<p class="text-muted">Loading...</p>';

    // Create XMLHttpRequest
    const xhttp = new XMLHttpRequest();

    xhttp.onload = function() {
        console.log("Response received. Status:", this.status);
        console.log("Ready state:", this.readyState);
        
        if (this.readyState == 4 && this.status == 200) {
            console.log("Response text:", this.responseText.substring(0, 200));

            try {
                // Parse the JSON returned from PHP
                const data = JSON.parse(this.responseText);
                console.log("Parsed data:", data);

                //Fill in Distributor Info
                const OTRateDiv = document.getElementById("onTimeRate");
                const totQuantityShippedDiv = document.getElementById("totalQty");
                const averageShipmentDiv = document.getElementById("avgQty");
                const totShipsDiv = document.getElementById("totalShippingsCell");
                const disruptExposureDiv = document.getElementById("disruption");
                OTRateDiv.innerHTML = data.otr[0].OTR;
                totQuantityShippedDiv.innerHTML = data.distributor[0].TotQuantityShipped;
                averageShipmentDiv.innerHTML = data.distributor[0].AVGShipQuantity;
                totShipsDiv.innerHTML = data.distributor[0].ShipmentVolume;
                //What if the distributor hasn't experienced a disruption event?
                if (data.disruptionEXPOSUREEvent[0].disruptionExposure && data.disruptionEXPOSUREEvent[0].disruptionExposure.length > 0) {
                    disruptExposureDiv.innerHTML = data.disruptionEXPOSUREEvent[0].disruptionExposure;
                }
                else{
                    disruptExposureDiv.innerHTML = "No Disruption Events Found";
                }

                // Clear boxes
                const productsDiv = document.getElementById("productsHandled");
                const outDiv = document.getElementById("shipmentsOut");
                const disruptDiv = document.getElementById("disruptDetails");
                productsDiv.innerHTML = "";
                outDiv.innerHTML = "";
                disruptDiv.innerHTML = "";

                // Display Product Details
                if (data.productsHandled && data.productsHandled.length > 0) {
                    data.productsHandled.forEach(item => {
                        const div = document.createElement("div");
                        div.className = "list-item";
                        div.innerHTML = `
                            <strong>Product Name:</strong> ${item.ProductName}<br>
                            <strong>Product ID:</strong> ${item.ProductID}<br>
                        `;
                        productsDiv.appendChild(div);
                    });
                } else {
                    productsDiv.innerHTML = '<p class="text-muted">No products found</p>';
                }

                // Display outstanding shipments
                if (data.shipmentsOutstanding && data.shipmentsOutstanding.length > 0) {
                    data.shipmentsOutstanding.forEach(item => {
                        const div = document.createElement("div");
                        div.className = "list-item";
                        div.innerHTML = `
                            <strong>Shipment ID:</strong> ${item.ShipmentID}<br>
                            <strong>Company:</strong> ${item.CompanyName}<br>
                        `;
                        outDiv.appendChild(div);
                    });
                } else {
                    outDiv.innerHTML = '<p class="text-muted">No Currently Out Shipments</p>';
                }

                //Pie chart of status distribution
                    var labels = ['Delivered', 'Outstanding'];
                // Calculate outstanding count safely
                    var outstandingCount = data.shipmentsOutstanding && data.shipmentsOutstanding.length > 0 ? data.shipmentsOutstanding.length : 0;

                // Calculate delivered shipments (within user specified itme range)
                var totalShipments = parseInt(data.distributor[0].ShipmentVolume) || 0;
                var deliveredCount = totalShipments - outstandingCount;
                //Plot Details
                var pieData = [{
                    values: [deliveredCount, outstandingCount],
                    labels: labels,
                    type: 'pie'
                    }];
                var layout = {
                    title: 'Status Distribution',
                    autosize: true,
                    margin: { l: 20, r: 20, t: 40, b: 20 }
                    };

                Plotly.newPlot('pieChartOut', pieData, layout, {responsive: true});

                //Show what disruption events are impacting distributor
                // Display disruption events and bar chart (Have to go in same if statement incase the company was unaffected by disruption events)
                if (data.disruptionEvent && data.disruptionEvent.length > 0) {
                    // Display individual disruption events
                    data.disruptionEvent.forEach(item => {
                        const div = document.createElement("div");
                        div.className = "list-item";
                        div.innerHTML = `
                            <strong>Event ID:</strong> ${item.EventID}<br>
                            <strong>Event Category:</strong> ${item.CategoryName}<br>
                            <strong>Impact Level:</strong> ${item.ImpactLevel}<br>
                        `;
                        disruptDiv.appendChild(div);
                    });

                    // Bar Chart of disruption event impact breakdown - only if there is data
                    if (data.disruptionEXPOSUREEvent[0].NumHighImpact !== undefined) {
                        var labelsBar = ['High', 'Medium', 'Low'];
                        var countHigh = parseInt(data.disruptionEXPOSUREEvent[0].NumHighImpact) || 0;
                        var countMed = parseInt(data.disruptionEXPOSUREEvent[0].NumMedImpact) || 0;
                        var countLow = parseInt(data.disruptionEXPOSUREEvent[0].NumLowImpact) || 0;
                        
                        var barData = [{
                            x: labelsBar,
                            y: [countHigh, countMed, countLow],
                            type: 'bar'
                        }];
                        var barLayout = {
                            title: 'Disruption Event Breakdown',
                            autosize: true,
                            margin: { l: 50, r: 50, t: 50, b: 50 }
                        };
                        Plotly.newPlot('barChartDisrupt', barData, barLayout, {responsive: true});
                    } else {
                        document.getElementById('barChartDisrupt').innerHTML = '<p class="text-muted">No summary data available</p>';
                    }
                } else {
                    // If no disruption events found
                    disruptDiv.innerHTML = '<p class="text-muted">No Disruption Events in Time Period!</p>';
                    document.getElementById('barChartDisrupt').innerHTML = '<p class="text-muted">No data to display</p>';
                }

                // Calculate delivery delay (ActualDate - PromisedDate in days)
                if (!data.shipping || data.shipping.length === 0) { //Make sure there is data in the array
                    document.getElementById('lineChartShipments').innerHTML = 
                        '<p class="text-muted">No shipping data available for chart</p>';
                } else {
                    // Calculate delay for each shipment
                    const shipmentDelays = data.shipping.map(item => { //Need to make new variables since Javascript can't take dates directly from array
                        const actualDate = new Date(item.ActualDate); 
                        const promisedDate = new Date(item.PromisedDate);
                        
                        // Calculate difference in days
                        const diffTime = actualDate - promisedDate; //JavaScript will rerutn the number of MILLISECONDS between the dates...we don't want that
                        const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)); //Convert from milliseconds to days in between
                        
                        return {
                            shipmentId: item.ShipmentID,
                            actualDate: item.ActualDate,
                            delayDays: diffDays
                        };
                    });

                    // Sort by actual date for better visualization
                    shipmentDelays.sort((a, b) => new Date(a.actualDate) - new Date(b.actualDate));

                    // Create the chart
                    var trace = {
                        x: shipmentDelays.map(item => item.actualDate),
                        y: shipmentDelays.map(item => item.delayDays),
                        type: 'bar',
                        name: 'Delivery Delay (days)',
                        text: shipmentDelays.map(item => {
                            if (item.delayDays === 0) return `Shipment ${item.shipmentId}: On Time ✓`;
                            return `Shipment ${item.shipmentId}: ${item.delayDays} days ${item.delayDays > 0 ? 'late' : 'early'}`;
                        }),
                        marker: {
                            color: shipmentDelays.map(item => {
                                if (item.delayDays > 0) return '#dc3545';  // Red for late
                                if (item.delayDays === 0) return '#007bff'; // Blue for on-time
                                return '#28a745'; // Green for early
                            })
                        }
                    };

                    var plotData = [trace];

                    var layout = {
                        title: 'Shipment Delivery Performance (Days Late/Early)',
                        xaxis: { title: 'Actual Delivery Date' },
                        yaxis: { 
                            title: 'Days from Promised Date',
                            zeroline: true,
                            zerolinewidth: 2,
                            zerolinecolor: '#666'
                        },
                        autosize: true,
                        margin: { l: 60, r: 50, t: 50, b: 50 }
                    };

                    Plotly.newPlot('lineChartShipments', plotData, layout, {responsive: true});
                }


                // Update status message
                document.getElementById("statusMessageDist").innerHTML = 
                    `<div class="alert alert-success">Data loaded successfully for date range ${startDate} to ${endDate} and ${data.distributor[0].CompanyName}! CompanyID: ${data.distributor[0].CompanyID}</div>`;
                
            } catch (error) {
                console.error("Error parsing response:", error);
                document.getElementById("statusMessageDist").innerHTML = 
                    `<div class="alert alert-danger">Error parsing data: ${error.message}</div>`;
                document.getElementById("productsHandled").innerHTML = '<p class="text-danger">Error loading data</p>';
                document.getElementById("shipmentsOut").innerHTML = '<p class="text-danger">Error loading data</p>';
            }

        } else if (this.readyState == 4) {
            console.error("Error loading data. Status:", this.status);
            document.getElementById("statusMessageDist").innerHTML = 
                `<div class="alert alert-danger">Error loading data. Status: ${this.status}</div>`;
        }
    };

    xhttp.onerror = function() {
        console.error("Network error occurred");
        document.getElementById("statusMessageDist").innerHTML = 
            '<div class="alert alert-danger">Network error occurred. Check console for details.</div>';
    };

    // Send the request
    const url = "SCMDistributorQueries.php?q=" + encodeURIComponent(q) + "&g=" + encodeURIComponent(g);
    console.log("Opening request to:", url);
    
    xhttp.open("GET", url, true);
    xhttp.send();
}
</script>

</body>
</html>
