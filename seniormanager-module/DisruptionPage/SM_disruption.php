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

                <!-- GLOBAL DATE FILTERS -->
                <div class="card mb-3">
                    <div class="card-body row">

                        <div class="col-md-4">
                            <label>Start Date</label>
                            <input type="date" class="form-control" id="globalStartDate">
                        </div>

                        <div class="col-md-4">
                            <label>End Date</label>
                            <input type="date" class="form-control" id="globalEndDate">
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary w-100" onclick="ApplyGlobalDates()">
                                Apply Date Range
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

                    <!-- TAB 1: AVERAGE FINANCIAL HEALTH BY COMPANY TYPE -->
                    <div class="tab-pane fade show active" id="countrycontinent" role="tabpanel" aria-labelledby="countrycontinent-tab">

                        <div class="area-header">Disruption Events by Region and Frequency</div>

                        <!-- Bar Chart + Line Chart -->
                        <div class="row">

                            <div class="col-md-6">
                                <div class="card" style="height: 350px;">
                                    <div class="card-header">Bar Chart</div>
                                    <ul class="list-group list-group-flush" id="typeHealthList">
                                        <p class="text-muted">Submit query to see results...</p>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card" style="height: 350px;">
                                    <div class="card-header">Line Chart</div>
                                    <div id="typeHealthBarChart" style="height: 300px;">
                                        <p class="text-muted">Submit query to see results...</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>  <!-- END TAB 1 -->

                    <!-- TAB 2: SEARCH BY DISRUPTION ID OR COMPANY NAME -->
                    <div class="tab-pane fade" id="idname" role="tabpanel" aria-labelledby="idname-tab">

                        <div class="area-header">Search by Disruption ID or Company Name</div>

                        <div class="row">

                            <!-- LEFT SIDE — SEARCH BY DISRUPTION ID -->
                            <div class="col-md-6">

                                <div class="card mb-3">
                                    <div class="card-header text-center fw-bold">Search by Disruption ID</div>

                                    <div class="card-body">
                                        <label>Disruption ID</label>
                                        <select class="form-control" id="DisruptionID_input">
                                            <option value="">Select ID</option>
                                        </select>

                                        <div class="d-flex justify-content-center mt-3">
                                            <button class="btn btn-primary px-4" onclick="SearchByDisruptionID()">
                                                Submit
                                            </button>
                                        </div>
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
                                            <tbody>
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
                                        <select class="form-control" id="CompanyName_input">
                                            <option value="">Select Company</option>
                                        </select>

                                        <div class="d-flex justify-content-center mt-3">
                                            <button class="btn btn-primary px-4" onclick="SearchByCompanyName()">
                                                Submit
                                            </button>
                                        </div>
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
                                            <tbody>
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

                } // END readyState if
            } // END onload function

            xhtpp.open("GET", "SCMhomepage_queries.php?q=" + input, true);
            xhtpp.send();
        } // END CompanyInformationAJAX
    </script>

</body>
</html>