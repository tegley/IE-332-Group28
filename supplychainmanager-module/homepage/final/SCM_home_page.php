<?php 
session_start();

//Check if the user is NOT logged in (security measure)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo "<h1>Unauthorized Login</h1>";
    echo "<p>Please visit the <a href='index.php'>login page</a>!</p>";
    exit();
}

$user_FullName = $_SESSION['FullName'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Manager Dashboard</title>

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

        .far-right-update-column{
            width: 250px;
        }

    </style>
</head>

<body>
    <h1>Global Electronics LLC</h1>

    <div class="container">
        <div class="row">

            <!-- Sidebar -->
            <div class="col-md-3">
                <div id="supplychainmanager_sidebar"></div>
                <script>
                    fetch('supplychainmanager_sidebar.html')
                        .then(r => r.text())
                        .then(html => document.getElementById('supplychainmanager_sidebar').innerHTML = html);
                </script>
            </div>

            <div class="col-md-9">

                <!-- Dashboard Header -->
                <div class="card" id="dashboard-header">
                    <?php echo "{$user_FullName}'s SCM Dashboard" ?>
                </div>

                <!-- Search Bar -->
                <div class="row">
                    <form action="#" method="post" name="CompanyInfoForm">

                        <div class="row justify-content-center mb-2">
                            <div class="col-md-6 d-flex"> 
                                <div class="d-flex align-items-center w-100">
                                    <div class="col-4 text-end pe-2"><label for="company_input">Company Name</label></div>
                                    <div class="col-8">
                                        <select class="form-control text-center" name="CompanyName" id="company_input"></select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mb-2">
                            <div class="col-md-6 d-flex"> 
                                
                                <div class="d-flex align-items-center w-50">
                                    <div class="col-4 text-end pe-1"><label for="StartDate">Start Date</label></div>
                                    <div class="col-8">
                                        <input type="date" class="form-control text-center" name="StartDate" id="StartDate">
                                    </div>
                                </div>

                                <div class="d-flex align-items-center w-50">
                                    <div class="col-4 text-end pe-1"><label for="EndDate">End Date</label></div>
                                    <div class="col-8">
                                        <input type="date" class="form-control text-center" name="EndDate" id="EndDate">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row justify-content-center mb-3">
                            <div class="col-auto"> 
                                <button type="button" onclick="CheckUserInput()" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div> <!-- End overarching row -->

                <!-- This section is redo by bootstrap tabs -->

                <!--     BOOTSTRAP TABS     -->

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <!-- Company info tab -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" type="button" role="tab">
                            Company Info
                        </button>
                    </li>

                    <!-- New tab for transactions -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="trans-tab" data-bs-toggle="tab" data-bs-target="#trans" type="button" role="tab">
                            Transactions
                        </button>
                    </li>

                    <!-- New tab for KPIs -->
                    <li class="nav-item" role="presentation">
                        <!-- new tab for KPI -->
                        <button class="nav-link" id="kpi-tab" data-bs-toggle="tab" data-bs-target="#kpi" type="button" role="tab">
                            KPIs
                        </button>
                    </li>

                    <!-- New tab for financial health -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="fin-tab" data-bs-toggle="tab" data-bs-target="#fin" type="button" role="tab">
                            Financials
                        </button>
                    </li>

                    <!-- New tab for disruption distribution -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="disrupt-tab" data-bs-toggle="tab" data-bs-target="#disrupt" type="button" role="tab">
                            Disruptions
                        </button>
                    </li>

                    <!-- Tab for Updating Company Info -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="update-company-tab" data-bs-toggle="tab" data-bs-target="#update-company" type="button" role="tab">
                            Update Company Info
                        </button>
                    </li>

                </ul>

                <!-- START TAB CONTENT WRAPPER -->
                <div class="tab-content" id="myTabContent">

                    <!-- TAB 1: COMPANY INFORMATION -->

                    <div class="tab-pane fade show active" id="company" role="tabpanel" aria-labelledby="company-tab">

                        <!-- Company Information Section -->
                        <div class="area-header">Company Information</div>
                        <div class="card scroll-box-companyinfo">
                        <!-- <div class="card"> -->
                            <div class="card-body row">
                                <!-- Important Info -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Important Info</div>

                                        <div class="card-body" id="important-info-id">
                                        </div>
                                    </div>
                                </div>

                                <!-- Other Info -->
                                <div class="col-md-6">
                                    <div class="card" style="height: 300px;">
                                        <div class="card-header" id="otherInfoHeader">Other Information</div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush" id="otherInfo" style="max-height:400px;">
                                                <p class="text-muted">Submit query to see results...</p>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dependencies -->
                            <div class="card-body row">

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Company Depends On</div>
                                        <ul class="list-group list-group-flush" id="dependsOn">
                                            <p class="text-muted">Submit query to see results...</p>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Company Is Depended On By</div>
                                        <ul class="list-group list-group-flush" id="dependedOn">
                                            <p class="text-muted">Submit query to see results...</p>
                                        </ul>
                                    </div>
                                </div>

                            </div>

                            <!-- Products -->
                            <div class="card-body row">

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Products Supplied</div>
                                        <ul class="list-group list-group-flush" id="productsSupplied">
                                            <p class="text-muted">Submit query to see results...</p>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">Product Diversity</div>
                                        <div id="ProductDiversityPieChart">
                                            <p class="text-muted">Submit query to see results...</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div> <!-- End scroll box behavior -->
                    </div> <!-- END TAB 1 -->

                    <!-- TAB 2: LIST OF TRANSACTIONS -->

                    <div class="tab-pane fade" id="trans" role="tabpanel" aria-labelledby="trans-tab">

                        <div class="area-header">List of Transactions</div>

                        <div class="card">
                            <div class="card-body row">

                                <!-- Shipping -->
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">Shipping</div>
                                        <div class="card-body">
                                            <div class="scroll-box" id="shipmentDetails">
                                                <p class="text-muted">Submit query to see results...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Receiving -->
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">Receiving</div>
                                        <div class="card-body">
                                            <div class="scroll-box" id="receivingDetails">
                                                <p class="text-muted">Submit query to see results...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Adjustments -->
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">Adjustments</div>
                                        <div class="card-body">
                                            <div class="scroll-box" id="adjustmentDetails">
                                                <p class="text-muted">Submit query to see results...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div> <!-- END TAB 2 -->

                    <!-- TAB 3: KEY PERFORMANCE INDICATORS -->

                    <div class="tab-pane fade" id="kpi" role="tabpanel" aria-labelledby="kpi-tab">

                        <div class="area-header">Key Performance Indicators</div>

                        <div class="card">
                            <div class="card-body row">

                                <!-- Stats -->
                                <div class="col-md-6">
                                        <div class="card-header">Statistics</div>
                                        <table class="table stats-table">
                                            <tr><td>On Time Delivery Rate</td><td id="onTimeRate">--</td></tr>
                                            <tr><td>Average Delay</td><td id="avgDelay">--</td></tr>
                                            <tr><td>Standard Deviation of Delay</td><td id="stdDelay">--</td></tr>
                                        </table>
                                </div>

                                <!-- Disruption Events -->
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header">List of Disruption Events Over the Past Year</div>
                                        <div class="card-body scroll-box-disruptionevents">
                                            <ul class="list-group list-group-flush" id="disruptEvents">
                                                <p class="text-muted">Submit query to see results...</p>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div> <!-- END TAB 3 -->

                    <!-- TAB 4: FINANCIAL HEALTH GRAPH                 -->

                    <div class="tab-pane fade" id="fin" role="tabpanel" aria-labelledby="fin-tab">

                        <div class="area-header">Financial Health Status Over the Past Year</div>

                        <div class="card" style="height: 450px;">
                            <div class="card-header">Financial Health Status Over the Past Year</div>

                            <div class="d-flex align-items-center justify-content-center flex-column" id="finHealthPastYear">
                                <p class="text-muted">Submit query to see results...</p>
                            </div>
                        </div>

                    </div> <!-- END TAB 4 -->

                    <!-- TAB 5: DISRUPTION EVENT DISTRIBUTION GRAPH     -->

                    <div class="tab-pane fade" id="disrupt" role="tabpanel" aria-labelledby="disrupt-tab">

                        <div class="area-header">Disruption Event Distribution</div>

                        <div class="card" style="height: 450px;">
                            <div class="card-header">Distribution of Disruption Event Counts Over the Past Year</div>

                            <div id="disruptEventsBarChart" style="width: 100%; height: 400px;">
                                <p class="text-muted">Submit query to see results...</p>
                            </div>
                        </div>

                    </div> <!-- END TAB 5 -->

                    <!-- TAB 6: Update Company Info -->

                    <div class="tab-pane fade" id="update-company" role="tabpanel" aria-labelledby="update-company-tab">

                        <div class="area-header">Update Company Information</div>
                        <div class="card" style="height: 450px;">
                            <div class="card-body"> 
                                <div class="row d-flex justify-content-center">
                                    <div class="col-auto" style="height: 140px;">
                                    <label for="UpdateTier">Update Tier:</label>
                                    <select id="UpdateTier" class="form-select">
                                        <option>Maintain Tier</option>
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                    </select>
                                    </div>

                                    <div class="col-auto" style="height: 140px;">
                                    <label for="CompanyNameUpdateID">Name:</label>
                                    <div id="UpdateCompanyName"> 
                                        <input type="text" class="form-control" name="CompanyNameUpdate" id="CompanyNameUpdateID">
                                    </div>
                                    </div>
                                
                                    <div class="col-auto" style="display:none; height:140px;" id="far-right-option-manufacturer">
                                    <label for="ManufacturerUpdateID">Update Factory Capacity:</label>
                                    <div id="UpdateManufacturer"> 
                                        <input type="number" class="form-control" name="ManufacturerUpdate" id="ManufacturerUpdateID">
                                    </div>
                                    </div>
                                    
                                    <div class="col-auto" style="display:none; height:140px;" id="far-right-option-distributor">
                                    <form id="UpdateDistributor"> 
                                        <label> Update A Distributor Route: </label>
                                        <select class="form-control mb-1" id="Select_FromCompanyID_input">
                                            <option value="">Select From Company</option>
                                        </select>
                                        <select class="form-control mb-1 mt-1" id="Select_ToCompanyID_input">
                                            <option value="">Select Current To Company</option>
                                        </select>
                                        <select class="form-select form-select-sm mt-1" id="Update_ToCompanyID_input">
                                            <option value="">Change To Company</option>
                                        </select>
                                    </form>
                                    </div>

                                    <div class="col-auto" style="display:block; height:140px;" id="far-right-option-retailer">
                                        <i class="fs-1 bi-shop"></i>
                                    </div>    
                                </div>

                                <div class="row d-flex justify-content-center">
                                    <div class="col-auto"> 
                                        <div class="alert alert-success mt-4" id="verification-update-company-info"> Submit query to see results... </div>
                                    </div>
                                </div>

                                <div class="row d-flex justify-content-center">
                                    <div class="col-auto"> 
                                        <button type="button" onclick="UpdateCompanyInfoAJAX()" class="btn btn-primary disabled" id="update-company-info-button">
                                        Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- END TAB 6 -->

                </div> <!-- END overarching Tab Wrapper -->
            </div> <!-- END col-md-9 -->
        </div> <!-- END overarching row -->
    </div> <!-- END overarching container -->

<script> //JavaScript for resizing Plotly graphs

//Ensure graphs are properly sized when other tabs are clicked
//Achieve this by looping through all tabs and triggering the autosize function for all tabs
const tabElms = document.querySelectorAll('button[data-bs-toggle="tab"]');

tabElms.forEach(tabElm => {
    tabElm.addEventListener('shown.bs.tab', event => {
        
        const targetTabId = event.target.getAttribute('data-bs-target');
        
        //If the tab is active, resize the chart to fit to card dimensions
        if (targetTabId === '#fin') {
            const chartContainer = document.getElementById('finHealthPastYear');
            if (chartContainer) {
                Plotly.relayout(chartContainer, { autosize: true });
            }
        } 
        else if (targetTabId === '#disrupt') {
            const chartContainer = document.getElementById('disruptEventsBarChart');
            if (chartContainer) {
                Plotly.relayout(chartContainer, { autosize: true });
            }
        }
        else if (targetTabId == '#company') {
            Plotly.relayout('ProductDiversityPieChart', { autosize: true });
        }
    });
});
</script>

<script>
    //Initially disable update company info button


    //Load Company Names when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadCompanies();
    });

    function loadCompanies() {
        fetch('distributorList.php')
            .then(response => response.json())
            .then(data => {
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
            })
    }
</script>

<script>
function CheckUserInput() {
    const company_name = document.CompanyInfoForm.CompanyName.value;
    const start_date = document.CompanyInfoForm.StartDate.value;
    const end_date = document.CompanyInfoForm.EndDate.value;

    if (company_name == "") { alert("Please provide a company!"); return false; }
    if (start_date == "" || end_date == "") { alert("Please provide date range!"); return false; }
    if (start_date >= end_date) { alert("Start date must be before end date!"); return false; }

    CompanyInformationAJAX(company_name, start_date, end_date);
    return true;
}
</script>

<script>
//JSON objects
var my_JSON_object;
var update_companyinfo_object;

//Initiallly selected values
var initally_selected_company_type;
var initally_selected_company_name;
var initally_selected_company_id;
var initally_selected_company_tier;
var initally_selected_factory_capacity;

//AJAX to call company information queries
function CompanyInformationAJAX(company_name, start_date, end_date) {

    let todays_date = new Date().toJSON().slice(0, 10);
    one_year_ago = String(todays_date.slice(0, 4) - 1);
    month = todays_date.slice(5, 7);
    day = todays_date.slice(8, 10);
    one_year_ago_from_today_date = `${one_year_ago}-${month}-${day}`;

    input = company_name + "|" + start_date + "|" + end_date + "|" + todays_date + "|" + one_year_ago_from_today_date;

    xhtpp = new XMLHttpRequest();

    xhtpp.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
            //console.log(this.responseText);

            //Reset all Update Company Info dropdown forms
            document.getElementById('UpdateDistributor').querySelector('select[id="Select_FromCompanyID_input"]').innerHTML = '';
            document.getElementById('UpdateDistributor').querySelector('select[id="Select_ToCompanyID_input"]').innerHTML = '';
            document.getElementById('UpdateDistributor').querySelector('select[id="Update_ToCompanyID_input"]').innerHTML = '';

            document.getElementById('UpdateDistributor').querySelector('select[id="Select_FromCompanyID_input"]').selectedIndex = 0;
            document.getElementById('UpdateDistributor').querySelector('select[id="Select_ToCompanyID_input"]').selectedIndex = 0;
            document.getElementById('UpdateDistributor').querySelector('select[id="Update_ToCompanyID_input"]').selectedIndex = 0;
            document.getElementById('UpdateCompanyName').querySelector('input[name="CompanyNameUpdate"]').value ='';
            document.getElementById('UpdateTier').selectedIndex = 0;
            document.getElementById('UpdateManufacturer').querySelector('input[name="ManufacturerUpdate"]').value = '';

            my_JSON_object = JSON.parse(this.responseText);
            console.log(JSON.stringify(my_JSON_object));

            //Enable user to update company info & transaction data
            document.getElementById('update-company-info-button').className = "btn btn-primary";

            //Save information of initially selected company
            initally_selected_company_type = my_JSON_object.companyInfo[0]["Type"];
            initally_selected_company_name = my_JSON_object.companyInfo[0]["CompanyName"];
            initally_selected_company_id = my_JSON_object.companyInfo[0]["CompanyID"];
            initally_selected_company_tier = my_JSON_object.companyInfo[0]["TierLevel"];
            address_country = my_JSON_object.companyInfo[0]["CountryName"];
            address_city = my_JSON_object.companyInfo[0]["City"];
            financial_score = my_JSON_object.companyInfo[0]["HealthScore"];

            const infoDiv = document.getElementById('important-info-id');
            infoDiv.innerHTML = "";
            const div = document.createElement("div");
                div.className = "list-item";
                div.innerHTML = `<strong>Address:</strong> ${address_city}, ${address_country}<br>
                                    <strong>Company Type:</strong> ${initally_selected_company_type}<br>
                                    <strong>Tier Level:</strong> ${initally_selected_company_tier}<br>
                                    <strong>Most Recent Financial Health Score:</strong> ${financial_score}`;
                infoDiv.appendChild(div);

            //Depending on type, enable user to update the factory capacity, update the distributor routes, or see a retailer picture
            if (initally_selected_company_type == "Manufacturer") {
                initally_selected_factory_capacity = my_JSON_object.companyInfo[0]["FactoryCapacity"];
                document.getElementById('far-right-option-manufacturer').style.display = "block";
                document.getElementById('far-right-option-distributor').style.display = "none";
                document.getElementById('far-right-option-retailer').style.display = "none";
            }

            if (initally_selected_company_type == "Distributor") {
                document.getElementById('far-right-option-manufacturer').style.display = "none";
                document.getElementById('far-right-option-distributor').style.display = "block";
                document.getElementById('far-right-option-retailer').style.display = "none";
                LoadDistributorDropDownAJAX();
            }

            if (initally_selected_company_type == "Retailer") {
                document.getElementById('far-right-option-manufacturer').style.display = "none";
                document.getElementById('far-right-option-distributor').style.display = "none";
                document.getElementById('far-right-option-retailer').style.display = "block";
            }

            //Financial Health Line chart
            const x_vals = my_JSON_object.pastHealthScores.map((item) => { return String(item.Quarter + " " + item.RepYear) }).map(String).reverse()
            const y_vals = my_JSON_object.pastHealthScores.map((item) => { return item.HealthScore }).map(Number).reverse();

            var layout = {
                title: { text: 'Financial Health Status Over Past Year from Today' },
                xaxis: { title: { text: 'Quarter & Year' } },
                yaxis: { range: [25, 100], title: { text: 'Financial Health Score' } }
            };

            const TESTER = document.getElementById('finHealthPastYear');
            TESTER.innerHTML = "";
            Plotly.newPlot(TESTER, [{ x: x_vals, y: y_vals }], layout);

            // Other Info
            const otherInfoLabel = document.getElementById("otherInfoHeader");
            const otherInfoDiv = document.getElementById("otherInfo");
            otherInfoDiv.innerHTML = "";

            if (my_JSON_object.companyInfo[0].Type === "Distributor") {
                otherInfoLabel.innerHTML = "Unique Routes Operated";
                my_JSON_object.distRoutes.forEach(item => {
                    const li = document.createElement("li");
                    li.className = "list-group-item";
                    li.innerHTML = `From Company ID: ${item.FromCompanyID} | To Company ID: ${item.ToCompanyID} <br>
                                    From ${item.FromCompanyName} To ${item.ToCompanyName}`;
                    otherInfoDiv.appendChild(li);
                });
            }
            if (my_JSON_object.companyInfo[0].Type === "Manufacturer") {
                otherInfoLabel.innerHTML = "Manufacturer Capacity";
                const li = document.createElement("li");
                li.className = "list-group-item";
                li.textContent = `Factory Capacity: ${my_JSON_object.companyInfo[0].FactoryCapacity}`;
                otherInfoDiv.appendChild(li);
            }

            if (my_JSON_object.companyInfo[0].Type === "Retailer") {
                otherInfoLabel.innerHTML = "No Additional Information for This Retailer";
            }

            // Dependencies
            const dependsOnDiv = document.getElementById("dependsOn");
            const dependedOnDiv = document.getElementById("dependedOn");
            dependsOnDiv.innerHTML = "";
            dependedOnDiv.innerHTML = "";

            if (my_JSON_object.dependsOn.length > 0) {
                my_JSON_object.dependsOn.forEach(item => {
                    const li = document.createElement("li");
                    li.className = "list-group-item";
                    li.innerHTML = `Upstream Company ID: ${item.UpStreamCompanyID}<br>
                                    Upstream Company Name: ${item.CompanyName}`;
                    dependsOnDiv.appendChild(li);
                });
            } else {
                dependsOnDiv.innerHTML = '<p class="text-muted">No dependencies found</p>';
            }

            if (my_JSON_object.dependedOn.length > 0) {
                my_JSON_object.dependedOn.forEach(item => {
                    const li = document.createElement("li");
                    li.className = "list-group-item";
                    li.innerHTML = `Downstream Company ID: ${item.DownStreamCompanyID}<br>
                                    Downstream Company Name: ${item.CompanyName}`;
                    dependedOnDiv.appendChild(li);
                });
            } else {
                dependedOnDiv.innerHTML = '<p class="text-muted">No dependencies found</p>';
            }

            // Products
            const productsDiv = document.getElementById("productsSupplied");
            productsDiv.innerHTML = "";
            my_JSON_object.productsSupplied.forEach(item => {
                const li = document.createElement("li");
                li.className = "list-group-item";
                li.textContent = `Product Name: ${item.ProductName} Product ID: ${item.ProductID}`;
                productsDiv.appendChild(li);
            });

            // Product Diversity Pie
            const pieDiv = document.getElementById("ProductDiversityPieChart");
            pieDiv.innerHTML = "";
            if (my_JSON_object.productDiversity.length > 0) {
                const categories = my_JSON_object.productDiversity.map(item => item.Category);
                const counts = my_JSON_object.productDiversity.map(item => parseInt(item["COUNT(*)"]));
                Plotly.newPlot('ProductDiversityPieChart', [{
                    values: counts,
                    labels: categories,
                    type: 'pie'
                }]);
            }

            // Shipping
            const shippingDiv = document.getElementById("shipmentDetails");
            shippingDiv.innerHTML = "";
            my_JSON_object.shipping.forEach(item => {
                const div = document.createElement("div");
                div.className = "list-item";
                div.innerHTML = `<strong>Shipment ID:</strong> ${item.ShipmentID}<br>
                                    <strong>Date Delivered:</strong> ${item.ActualDate}<br>
                                    <strong>Product & Quantity:</strong> ${item.ProductID}, ${item.Quantity}`;
                shippingDiv.appendChild(div);
            });

            // Receiving
            const receivingDiv = document.getElementById("receivingDetails");
            receivingDiv.innerHTML = "";
            my_JSON_object.receivings.forEach(item => {
                const div = document.createElement("div");
                div.className = "list-item";
                div.innerHTML = `<strong>Receiving ID:</strong> ${item.ReceivingID}<br>
                                    <strong>Date Received:</strong> ${item.ReceivedDate}<br>
                                    <strong>Product & Quantity:</strong> ${item.ProductID}, ${item.QuantityReceived}`;
                receivingDiv.appendChild(div);
            });

            // Adjustments
            const adjustmentsDiv = document.getElementById("adjustmentDetails");
            adjustmentsDiv.innerHTML = "";
            my_JSON_object.adjustments.forEach(item => {
                const div = document.createElement("div");
                div.className = "list-item";
                div.innerHTML = `<strong>Adjustment ID:</strong> ${item.AdjustmentID}<br>
                                    <strong>Date:</strong> ${item.AdjustmentDate}<br>
                                    <strong>Product & Quantity:</strong> ${item.ProductID}, ${item.QuantityChange}<br>
                                    <strong>Reason:</strong> ${item.Reason}`;
                adjustmentsDiv.appendChild(div);
            });

            // KPI
            document.getElementById("onTimeRate").innerHTML =
                (my_JSON_object.otr[0].OTR || "N/A") + "%";

            document.getElementById("avgDelay").innerHTML =
                (my_JSON_object.shipmentDetails[0].avgDelay || "N/A") + " days";

            document.getElementById("stdDelay").innerHTML =
                (my_JSON_object.shipmentDetails[0].stdDelay || "N/A") + " days";

            // Disruption Events
            const disruptionDiv = document.getElementById("disruptEvents");
            disruptionDiv.innerHTML = "";
            my_JSON_object.disruptionEvents.forEach(item => {
                const li = document.createElement("li");
                li.className = "list-group-item";
                li.textContent =
                    `${item.CategoryName} | ID: ${item.EventID} | Date: ${item.EventDate} → Recovery: ${item.EventRecoveryDate}`;
                disruptionDiv.appendChild(li);
            });

            // Disruption Distribution Bar Chart
            const distDiv = document.getElementById("disruptEventsBarChart");
            distDiv.innerHTML = "";
            if (my_JSON_object.disruptionEventsDistribution.length > 0) {
                const categories = my_JSON_object.disruptionEventsDistribution.map(item => item.CategoryName);
                const counts = my_JSON_object.disruptionEventsDistribution.map(item => parseInt(item.NumEvents));
                Plotly.newPlot('disruptEventsBarChart', [{
                    x: categories,
                    y: counts,
                    type: 'bar',
                    marker: { color: '#0f6fab' }
                }]);
            }

        } // END readyState if
    } // END onload function

    xhtpp.open("GET", "SCMhomepage_queries.php?q=" + input, true);
    xhtpp.send();
} // END CompanyInformationAJAX

function UpdateCompanyInfoAJAX() {
    let update_tier = document.getElementById('UpdateTier').value;
    let update_name = document.getElementById('UpdateCompanyName').querySelector('input[name="CompanyNameUpdate"]').value;

    //Maintain the tier & name if they aren't selected
    if (update_tier == "Maintain Tier"){
        update_tier = initally_selected_company_tier;
    }

    if (update_name == ""){
        update_name = initally_selected_company_name;
    }

    if (initally_selected_company_type == "Manufacturer") {
        update_factory_capacity = document.getElementById('UpdateManufacturer').querySelector('input[name="ManufacturerUpdate"]').value;
        if (update_factory_capacity == ""){
            update_factory_capacity = initally_selected_factory_capacity;
        }

        q_input = initally_selected_company_type + "|" + initally_selected_company_id + "|" + update_name + "|" + update_tier + "|" + update_factory_capacity;
    }

    else if (initally_selected_company_type == "Distributor") {
        same_from_company = document.getElementById('UpdateDistributor').querySelector('select[id="Select_FromCompanyID_input"]').value;
        prior_to_company = document.getElementById('UpdateDistributor').querySelector('select[id="Select_ToCompanyID_input"]').value;
        update_to_company = document.getElementById('UpdateDistributor').querySelector('select[id="Update_ToCompanyID_input"]').value;

        if (same_from_company == "Select From Company" || prior_to_company == "Select Current To Company" || update_to_company == "Change To Company") {
            q_input = "DistributorMaintainRoutes" + "|" + initally_selected_company_id + "|" + update_name + "|" + update_tier;
        }
        else{
            q_input = "DistributorUpdateRoutes" + "|" + initally_selected_company_id + "|" + update_name + "|" + update_tier + "|" + same_from_company + "|" + prior_to_company + "|" + update_to_company;
        }
    }

    else if (initally_selected_company_type == "Retailer") {
        q_input = initally_selected_company_type + "|" + initally_selected_company_id + "|" + update_name + "|" + update_tier;
    }

    console.log(q_input);

    xhtpp = new XMLHttpRequest();
    xhtpp.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            document.getElementById('verification-update-company-info').innerText = 'Company Information Updated Successfully!'
        };
    }
    const url = "SCM_update_queries.php?q=" + encodeURIComponent(q_input);
    xhtpp.open("GET", url, true);
    xhtpp.send(); 
}

function LoadDistributorDropDownAJAX() {
    const from_id_dropdown = document.getElementById('UpdateDistributor').querySelector('select[id="Select_FromCompanyID_input"]');
    const to_id_dropdown = document.getElementById('UpdateDistributor').querySelector('select[id="Select_ToCompanyID_input"]');
    const update_to_id_dropdown = document.getElementById('UpdateDistributor').querySelector('select[id="Update_ToCompanyID_input"]');
    
    const distributor_id = initally_selected_company_id; //Pass DistributorID into the query
    q_input = distributor_id + '|' + "Distributor";

    xhtpp = new XMLHttpRequest();
    xhtpp.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            const data = JSON.parse(this.responseText);
            
            //From Company
            const initial_from = document.createElement('option');
            initial_from.textContent="Select From Company";
            from_id_dropdown.appendChild(initial_from);
            if(data.FromCompany.length > 0){ //If the distributor has routes, list their partners
                data.FromCompany.forEach(FromID => {
                    const from_id_option = document.createElement('option');
                    from_id_option.value = FromID.CompanyName;
                    from_id_option.textContent = FromID.CompanyName;
                    from_id_dropdown.appendChild(from_id_option);
                });
            } else{ //If the distributor has no routes, state such
            const from_id_option = document.createElement('option');
            from_id_option.value = '';
            from_id_option.textContent = 'There are no routes to update';
            from_id_dropdown.appendChild(from_id_option);
            }

            //To Company
            const initial_to = document.createElement('option');
            initial_to.textContent="Select Current To Company";
            to_id_dropdown.appendChild(initial_to);
            if(data.ToCompany.length > 0){ //If the distributor has routes, list the current destinations
                data.ToCompany.forEach(ToID => {
                    const to_id_option = document.createElement('option');
                    to_id_option.value = ToID.CompanyName;
                    to_id_option.textContent = ToID.CompanyName;
                    to_id_dropdown.appendChild(to_id_option);
                });
            } else{ //If the distributor has no routes, state such
            const to_id_option = document.createElement('option');
            to_id_option.value = '';
            to_id_option.textContent = 'There are no routes to update';
            to_id_dropdown.appendChild(from_id_option);
            }

            //Update To Company
            const update_initial_from = document.createElement('option');
            update_initial_from.textContent="Change To Company";
            update_to_id_dropdown.appendChild(update_initial_from);
            if(data.UpdateToCompany.length > 0){ //If the distributor has routes, list all non-distributor companies as potential destinations
                data.UpdateToCompany.forEach(UpdateID => {
                    const update_id_option = document.createElement('option');
                    update_id_option.value = UpdateID.CompanyName;
                    update_id_option.textContent = UpdateID.CompanyName;
                    update_to_id_dropdown.appendChild(update_id_option);
                });
            } else{ //If the distributor has no routes, state such
            const update_id_option = document.createElement('option');
            update_id_option.value = '';
            update_id_option.textContent = 'There are no routes to update';
            update_to_id_dropdown.appendChild(update_id_option);
            }
        };
    }
    const url = "SCM_home_dropdown_queries.php?q=" + encodeURIComponent(q_input);
    xhtpp.open("GET", url, true);
    xhtpp.send();
}
</script>

</body>
</html>
