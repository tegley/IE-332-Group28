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

                    <!-- Tab for Updating Transactions -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="update-transactions-tab" data-bs-toggle="tab" data-bs-target="#update-transactions" type="button" role="tab">
                            Update Transactions
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

                                        <div class="card-body row">
                                            <div class="col-4">
                                                <div class="card"><div class="card-body">Address</div></div>
                                            </div>
                                            <div class="col-8">
                                                <div class="card"><div class="card-body" id="address"></div></div>
                                            </div>

                                            <div class="col-6">
                                                <div class="card"><div class="card-body">Company Type</div></div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card"><div class="card-body" id="company-type"></div></div>
                                            </div>

                                            <div class="col-6">
                                                <div class="card"><div class="card-body">Tier Level</div></div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card"><div class="card-body" id="tier-level"></div></div>
                                            </div>

                                            <div class="col-6">
                                                <div class="card"><div class="card-body">Financial Health Score</div></div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card"><div class="card-body" id="financial-health-score"></div></div>
                                            </div>
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

                    <!-- TAB 3: KEY PERFORMANCE INDICATORS              -->

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
                            <div class="card-body row d-flex justify-content-center">
                                <div class="col-auto">
                                <label for="UpdateTier">Update Tier:</label>
                                <select id="UpdateTier" class="form-select">
                                    <option value="" disabled selected>New Tier</option>
                                    <option>Tier 1</option>
                                    <option>Tier 2</option>
                                    <option>Tier 3</option>
                                </select>
                                </div>

                                <div class="col-auto">
                                <label for="UpdateAddress">Update Address:</label>
                                <select id="UpdateAddress" class="form-select">
                                    <option value="" disabled selected>Update Address</option>
                                    <option value="city">City</option>
                                    <option value="country">Country</option>
                                    <option value="continent">Continent</option>
                                </select>
                                </div>

                                <div class="col-auto">
                                <label for="UpdateCompanyName">Name:</label>
                                <div id="UpdateCompanyName"> 
                                    <input type="text" class="form-control" name="CompanyNameUpdate" id="CompanyNameUpdateID">
                                </div>
                                </div>
                               
                                
                                <div class="col-auto far-right-update-column" style="display:none">
                                <label for="UpdateManufacturer">Update Factory Capacity:</label>
                                <div id="UpdateManufacturer"> 
                                    <input type="text" class="form-control" name="ManufacturerUpdate" id="ManufacturerUpdateID">
                                </div>
                                </div>
                                
                                <div class="col-auto far-right-update-column" style="display:none">
                                <label for="UpdateDistributor">Update A Distributor Route:</label>
                                <div id="UpdateDistributor"> 
                                    <input type="text" class="form-control text-center" name="DistributorUpdat" id="DistributorUpdate">
                                    <input type="text" class="form-control text-center" name="DistributorUpdate" id="DistributorUpdateID">
                                </div>
                                </div>

                                <div class="col-auto far-right-update-column" style="display:none">
                                    <img src="TimmysHeadshot.JPG" height=200></p>
                                </div>    

                            </div>
                        </div>
                    </div> <!-- END TAB 6 -->

                    <div class="tab-pane fade" id="update-transactions" role="tabpanel" aria-labelledby="update-transactions-tab">
                        <div class="area-header">Disruption Event Distribution</div>

                        <div class="card" style="height: 450px;">
                            <div class="card-header">Distribution of Disruption Event Counts Over the Past Year</div>

                            <div id="disruptEventsBarChart" style="width: 100%; height: 400px;">
                                <p class="text-muted">Submit query to see results...</p>
                            </div>
                        </div>
                    </div> <!-- END TAB 7 -->

                </div> <!-- END overarching Tab Wrapper -->
            </div> <!-- END col-md-9 -->
        </div> <!-- END overarching row -->
    </div> <!-- END overarching container -->

    <script>
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
        var my_JSON_object;

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
                    console.log(this.responseText);

                    /*
                    my_JSON_object = JSON.parse(this.responseText);
                    console.log(JSON.stringify(my_JSON_object));

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

                    // Company Information
                    document.getElementById("address").innerHTML =
                        my_JSON_object.companyInfo[0].City + ", " + my_JSON_object.companyInfo[0].CountryName;

                    document.getElementById("company-type").innerHTML =
                        my_JSON_object.companyInfo[0].Type;

                    document.getElementById("tier-level").innerHTML =
                        my_JSON_object.companyInfo[0].TierLevel;

                    document.getElementById("financial-health-score").innerHTML =
                        my_JSON_object.companyInfo[0].HealthScore;

                    // Other Info
                    const otherInfoLabel = document.getElementById("otherInfoHeader");
                    const otherInfoDiv = document.getElementById("otherInfo");
                    otherInfoDiv.innerHTML = "";

                    if (my_JSON_object.companyInfo[0].Type === "Distributor") {
                        otherInfoLabel.innerHTML = "Unique Routes Operated";
                        my_JSON_object.distRoutes.forEach(item => {
                            const li = document.createElement("li");
                            li.className = "list-group-item";
                            li.textContent = `From Company ID: ${item.FromCompanyID} To Company ID: ${item.ToCompanyID}`;
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

                    // Dependencies
                    const dependsOnDiv = document.getElementById("dependsOn");
                    const dependedOnDiv = document.getElementById("dependedOn");
                    dependsOnDiv.innerHTML = "";
                    dependedOnDiv.innerHTML = "";

                    if (my_JSON_object.dependsOn.length > 0) {
                        my_JSON_object.dependsOn.forEach(item => {
                            const li = document.createElement("li");
                            li.className = "list-group-item";
                            li.textContent = `UpStream Company ID: ${item.UpStreamCompanyID}`;
                            dependsOnDiv.appendChild(li);
                        });
                    } else {
                        dependsOnDiv.innerHTML = '<p class="text-muted">No dependencies found</p>';
                    }

                    if (my_JSON_object.dependedOn.length > 0) {
                        my_JSON_object.dependedOn.forEach(item => {
                            const li = document.createElement("li");
                            li.className = "list-group-item";
                            li.textContent = `DownStream Company ID: ${item.DownStreamCompanyID}`;
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
*/
                } // END readyState if
            } // END onload function

            xhtpp.open("GET", "SCMhomepage_queries.php?q=" + input, true);
            xhtpp.send();
        } // END CompanyInformationAJAX
    </script>

</body>
</html>
