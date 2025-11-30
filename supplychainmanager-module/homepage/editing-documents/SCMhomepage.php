<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Manager Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.plot.ly/plotly-2.35.2.min.js" charset="utf-8"></script>
    <style>
        @import "standardized_project_formatting.css";
        /* Bubble Header */
        .bubble-header {
            display: inline-block;
            padding: 12px 50px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            margin: 15px;
            text-align: center;
            background-color: #0f6fab;
            /* CHANGED: from teal (#0fa3b1) → ERP-style blue */
            color: white;
            /* NEW: added contrast */
            font-size: 20px;
            /* CHANGED: slightly smaller for proportion */
        }

        /* Area Header */
        .area-header {
            background-color: #cbcbcb;
            width: auto;
            height: auto;
            font-family: Cambria, serif;
            /* sets pretty font */
            font-size: 20px;
            color: #222;
            /* NEW: darker text */
            border-radius: 8px;
            /* NEW: smoother shape */
            text-align: center;
            /* NEW: explicitly centers dashboard text */
            margin-top: 15px;
            margin-bottom: 10px;
            padding: 15px;
        }

        /* Create a scroll bar */
        #scroll-format {
            /*max-height: 250px;          set desired height */
            overflow-x: hidden;
            /* hide horizontal scrollbar */
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
            border-radius: 4px;
            border-left: 3px solid #0f6fab;
        }

        .stats-table td {
            border: 1px solid #666;
            padding: 10px;
            font-size: 15px;
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

                <!-- Search Bar & Page Navigation -->
                <!-- Search Bar -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="container mx-auto" style="max-width: 400px; padding: 0;">
                            <div class="bubble-header">Search Bar</div>
                            <form action="#" method="post" name="CompanyInfoForm">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="w-50 me-2 text-end">
                                        <label>Company Name</label>
                                    </div>
                                    <select class="form-control me-2 w-50" name="CompanyName" id="company_input"></select>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="w-50 me-2 text-end">
                                        <label>Start Date</label>
                                    </div>
                                    <input type="date" class="form-control me-2 w-50" name="StartDate">
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="w-50 me-2 text-end">
                                        <label>End Date</label>
                                    </div>
                                    <input type="date" class="form-control me-2 w-50" name="EndDate">
                                </div>
                                <div class="d-flex justify-content-center mb-3">
                                    <button type="button" onclick="CheckUserInput()"
                                        class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Nav Bar -->
                    <div class="col-md-6">
                        <div class="bubble-header">Page Navigation</div>
                        <nav class="nav nav-pills flex-column">
                            <a class="nav-link" href="#company-info">Company Information</a>
                            <a class="nav-link" href="#list-of-transactions">List of Transactions</a>
                            <a class="nav-link" href="#key-performance-indicators">Key Performance Indicators</a>
                        </nav>
                    </div>
                </div>
                <!-- Company Info -->
                <!-- Header -->
                <div class="area-header" id="company-info">Company Information</div>
                <div class="card"> <!-- Larger Card 1 - Basic Information-->
                    <div class="card-body row">
                        <div class="col-md-6"> <!-- Left Side - Important Info -->
                            <div class="card">
                                <div class="card-header">Important Info</div>
                                <div class="card-body row">
                                    <div class="col-4">
                                        <div class="card">
                                            <div class="card-body">Address</div>
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <div class="card">
                                            <div class="card-body" id="address"></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card">
                                            <div class="card-body">Company Type</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card">
                                            <div class="card-body" id="company-type"></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card">
                                            <div class="card-body">Tier Level</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card">
                                            <div class="card-body" id="tier-level"></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card">
                                            <div class="card-body">Financial Health Score</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card">
                                            <div class="card-body" id="financial-health-score"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-3" style="height: 300px;">
                                <div class="card-header" id="otherInfoHeader">Other Information</div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush" id="otherInfo"
                                        style="max-height:400px;">
                                        <p class="text-muted">Submit query to see results...</p>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3"> <!-- Larger Card 2 - Dependencies-->
                    <div class="card-body row">
                        <div class="col-md-6"> <!-- Left Side - Depends On -->
                            <div class="card">
                                <div class="card-header">Company Depends On</div>
                                <ul class="list-group list-group-flush" id="dependsOn" style="max-height:400px;">
                                    <p class="text-muted">Submit query to see results...</p>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6"> <!-- Right Side - Depended On By-->
                            <div class="card">
                                <div class="card-header">Company Is Depended On By</div>
                                <ul class="list-group list-group-flush" id="dependedOn" style="max-height:400px;">
                                    <p class="text-muted">Submit query to see results...</p>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3"> <!-- Larger Card 3 - Products-->
                    <div class="card-body row">
                        <div class="col-md-6"> <!-- Left Side - Products Supplied -->
                            <div class="card">
                                <div class="card-header">Products Supplied</div>
                                <ul class="list-group list-group-flush" id="productsSupplied" style="height:300px;">
                                    <p class="text-muted">Submit query to see results...</p>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6"> <!-- Right Side - Diversity Pie Chart-->
                            <div class="card">
                                <div class="card-header">Product Diversity</div>
                                <ul class="card-body text-center" id="ProductDiversityPieChart" style="height:300px;">
                                    <p class="text-muted">Submit query to see results...</p>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- List of Transactions -->
                <!-- Header -->
                <div class="area-header" id="list-of-transactions">List of Transactions</div>
                <div class="card"> <!-- Larger Card - Transactions-->
                    <div class="card-body row">
                        <div class="col-md-4"> <!-- Left - Shippments -->
                            <div class="card">
                                <div class="card-header">Shipping</div>
                                <div class="card-body">
                                    <div class="scroll-box" id="shipmentDetails">
                                        <p class="text-muted">Submit query to see results...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4"> <!-- Middle - Receiving -->
                            <div class="card">
                                <div class="card-header">Receiving</div>
                                <div class="card-body">
                                <div class="scroll-box" id="receivingDetails">
                                    <p class="text-muted">Submit query to see results...</p>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-4"> <!-- Right - Adjustments -->
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

                <!-- Key Performance Indicators -->
                <!-- Header -->
                <div class="area-header" id="key-performance-indicators">Key Performance Indicators</div>
                <div class="card"> <!-- Larger Card 1 - Statistics & List of Disruption Events -->
                    <div class="card-body row">
                        <div class="col-md-6"> <!-- Left Side - Statistics -->
                            <div class="card mb-3">
                                <div class="card-header">Statistics</div>
                                    <table class="table stats-table">
                                        <tr><td>On Time Delivery Rate</td><td id="onTimeRate">--</td></tr>
                                        <tr><td>Average Delay</td><td id="avgDelay">--</td></tr>
                                        <tr><td>Standard Deviation of Delay</td><td id="stdDelay">--</td></tr>
                                    </table>
                            </div> 
                        </div>
                            <div class="col-md-6"> <!-- Right Side - List of Disruption Events -->
                                <div class="card mb-3">
                                    <div class="card-header">List of Disruption Events Over the Past Year</div>
                                    <div class="card-body row">
                                        <ul class="list-group list-group-flush" id="disruptEvents"
                                            style="max-height:500px;">
                                            <p class="text-muted">Submit query to see results...</p>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="card mt-2 mb-2" style="height: 450px;">
                    <!-- Larger Card 2 - Financial Health Status Over the Past Year-->
                    <div class="card-header">Financial Health Status Over the Past Year</div>
                    <div class="d-flex align-items-center justify-content-center flex-column" id ="finHealthPastYear">
                        <p class="text-muted">Submit query to see results...</p>
                    </div>
                </div>

                <div class="card mt-2 mb-3" style="height: 450px;">
                    <!-- Larger Card 3 - Disruption Event Distribution-->
                    <div class="card-header">Distribution of Disruption Event Counts Over the Past Year</div>
                    <div id="disruptEventsBarChart" style="width: 100%; height: 400px;">
                        <p class="text-muted">Submit query to see results...</p>
                    </div>
                </div>

            </div> <!-- Closes col-md-9 -> add divs above this line!! -->

        </div> <!-- Row -->
    </div> <!-- Container -->
</body>

<script>
//Load Company Names when page loads
    document.addEventListener('DOMContentLoaded', function() {
    loadCompanies();
    });
function loadCompanies() {
    // Fetch data from your PHP file that returns all the data
    fetch('distributorList.php')  // This should return your full JSON object
        .then(response => response.json())
        .then(data => {
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
        })

}
function CheckUserInput() {
    //User inputs
    const company_name = document.CompanyInfoForm.CompanyName.value;
    const start_date = document.CompanyInfoForm.StartDate.value;
    const end_date = document.CompanyInfoForm.EndDate.value;

    //Check for company input
    if (company_name == "") {
        alert("Please provide a company!");
        document.CompanyInfoForm.CompanyName.focus();
        return false;
    }

    //Check for date input
    if (start_date == "" || end_date == "") {
        alert("Please provide date range!");
        document.CompanyInfoForm.CompanyName.focus();
        return false;
    }

    //Verify start date is before end date
    if (start_date >= end_date) {
        alert("Start date must be before end date!");
        document.CompanyInfoForm.CompanyName.focus();
        return false;
    }

    //Execute AJAX functions
    CompanyInformationAJAX(company_name, start_date, end_date);
    return true;
}
var my_JSON_object;
function CompanyInformationAJAX(company_name, start_date, end_date) {
    //Get todays date and one year ago from today date
            let todays_date = new Date().toJSON().slice(0, 10);
            one_year_ago = String(todays_date.slice(0, 4) - 1);
            month = todays_date.slice(5, 7);
            day = todays_date.slice(8, 10);
            one_year_ago_from_today_date = `${one_year_ago}-${month}-${day}`;

            console.log(todays_date);
            console.log(one_year_ago_from_today_date);
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
                title: {
                    text: 'Financial Health Status Over Past Year from Today'
                },
                xaxis: {
                    title: {
                        text: 'Quarter & Year'
                    }
                },
                yaxis: {
                    range: [25, 100],
                    title: {
                        text: 'Financial Health Score'
                    }
                }
            };


            const TESTER = document.getElementById('finHealthPastYear');
            TESTER.innerHTML = "";
            Plotly.newPlot(TESTER, [{
                x: x_vals,
                y: y_vals
            }], layout); //, { margin: { t: 0 } }

            //Company Information - Important Info
            address = String(my_JSON_object.companyInfo[0].City) + ", " + String(my_JSON_object.companyInfo[0].CountryName);
            document.getElementById("address").innerHTML = address;
            document.getElementById("company-type").innerHTML = my_JSON_object.companyInfo[0].Type;
            document.getElementById("tier-level").innerHTML = my_JSON_object.companyInfo[0].TierLevel;
            document.getElementById("financial-health-score").innerHTML = my_JSON_object.companyInfo[0].HealthScore;
            //index_zero = object[0];
            //document.getElementById("type").innerHTML = "Type:" + " " + index_zero.Type;
            //document.getElementById("tier").innerHTML = "Tier:" + " " + index_zero.TierLevel;
            //document.getElementById("score").innerHTML = "Financial Health Score:" + " " + index_zero.HealthScore;

            //Other Info Box based on company type
            const otherInfoLabel = document.getElementById("otherInfoHeader");
            const otherInfoDiv = document.getElementById("otherInfo");
            otherInfoLabel.innerHTML = "";
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
            if (my_JSON_object.companyInfo[0].Type === "Retailer") {
                otherInfoLabel.innerHTML = "Retailor Products";
                my_JSON_object.receivings.forEach(item => {
                    const li = document.createElement("li");
                    li.className = "list-group-item";
                    li.textContent = `Product Name: ${item.ProductName} Product ID: ${item.ProductID}`;
                    otherInfoDiv.appendChild(li);
                });
            }

            //Company Dependencies
            // Clear box
            const dependsOnDiv = document.getElementById("dependsOn");
            const dependedOnDiv = document.getElementById("dependedOn");
            dependsOnDiv.innerHTML = "";
            dependedOnDiv.innerHTML = "";

            // Display depends on companies
            if (my_JSON_object.dependsOn && my_JSON_object.dependsOn.length > 0) {
                my_JSON_object.dependsOn.forEach(item => {
                    const li = document.createElement("li");
                    li.className = "list-group-item";
                    li.textContent = `UpStream Company ID: ${item.UpStreamCompanyID}`;
                    dependsOnDiv.appendChild(li);
                });
            } else {
                dependsOnDiv.innerHTML = '<p class="text-muted">No dependencies found</p>';
            }
            // Display depended on companies
            if (my_JSON_object.dependedOn && my_JSON_object.dependedOn.length > 0) {
                my_JSON_object.dependedOn.forEach(item => {
                    const li = document.createElement("li");
                    li.className = "list-group-item";
                    li.textContent = `DownStream ID: ${item.DownStreamCompanyID}`;
                    dependedOnDiv.appendChild(li);
                });
            } else {
                dependedOnDiv.innerHTML = '<p class="text-muted">No dependencies found</p>';
            }
            //Products Supplied List
            const productsSuppliedDiv = document.getElementById("productsSupplied");
            productsSuppliedDiv.innerHTML = "";

            // Display products supplied list
            if (my_JSON_object.productsSupplied && my_JSON_object.productsSupplied.length > 0) {
                my_JSON_object.productsSupplied.forEach(item => {
                    const li = document.createElement("li");
                    li.className = "list-group-item";
                    li.textContent = `Product Name: ${item.ProductName} Product ID: ${item.ProductID}`;
                    productsSuppliedDiv.appendChild(li);
                });
            } else {
                productsSuppliedDiv.innerHTML = '<p class="text-muted">No products supplied!</p>';
            }
            //Pie chart of product diveristy
            const pieDiv = document.getElementById("ProductDiversityPieChart");
            pieDiv.innerHTML = "";

            if (my_JSON_object.productDiversity && my_JSON_object.productDiversity.length > 0) {
            const categories = my_JSON_object.productDiversity.map(item => item.Category);
            const counts = my_JSON_object.productDiversity.map(item => parseInt(item["COUNT(*)"]));
            //Plot Details
            var pieData = [{
                values: counts,
                labels: categories,
                type: 'pie'
                }];
            var layout = {
                title: 'Product Diversity',
                autosize: true,
                margin: { l: 20, r: 20, t: 40, b: 20 }
                };

            Plotly.newPlot('ProductDiversityPieChart', pieData, layout, {responsive: true});
            } else{
                pieDiv.innerHTML = "Company does not supply products!";
            }

            //Clear Transaction Place Holders
            const shippingDiv = document.getElementById("shipmentDetails");
            const receivingDiv = document.getElementById("receivingDetails");
            const adjustmentsDiv = document.getElementById("adjustmentDetails");
            shippingDiv.innerHTML = "";
            receivingDiv.innerHTML = "";
            adjustmentsDiv.innerHTML = "";
            //Display Shipment Information
                if (my_JSON_object.shipping && my_JSON_object.shipping.length > 0) {
                    my_JSON_object.shipping.forEach(item => {
                        const div = document.createElement("div");
                        div.className = "list-item";
                        div.innerHTML = `
                            <strong>Shipment ID:</strong> ${item.ShipmentID}<br>
                            <strong>Date Delivered:</strong> ${item.ActualDate}<br>
                            <strong>Product & Quanitiy Shipped:</strong> ${item.ProductID} , ${item.Quantity} <br>
                        `;
                        shippingDiv.appendChild(div);
                    });
                } else {
                    shippingDiv.innerHTML = "No shipments found";
                }
            //Display Recieving Information
                if (my_JSON_object.receivings && my_JSON_object.receivings.length > 0) {
                    my_JSON_object.receivings.forEach(item => {
                        const div = document.createElement("div");
                        div.className = "list-item";
                        div.innerHTML = `
                            <strong>Recieving ID:</strong> ${item.ReceivingID}<br>
                            <strong>Date Recieved:</strong> ${item.ReceivedDate}<br>
                            <strong>Product & Quanitiy Recieved:</strong> ${item.ProductID} , ${item.QuantityReceived} <br>
                        `;
                        receivingDiv.appendChild(div);
                    });
                } else {
                    receivingDiv.innerHTML = "No recievings found";
                }
            //Display Adjustment Information
                if (my_JSON_object.adjustments && my_JSON_object.adjustments.length > 0) {
                    my_JSON_object.adjustments.forEach(item => {
                        const div = document.createElement("div");
                        div.className = "list-item";
                        div.innerHTML = `
                            <strong>Adujustment ID:</strong> ${item.AdjustmentID}<br>
                            <strong>Date:</strong> ${item.AdjustmentDate} <br>
                            <strong>Product & Quanitiy Involved:</strong> ${item.ProductID} , ${item.QuantityChange} <br>
                            <strong>Reason for Adjustment:</strong> ${item.Reason}
                        `;
                        adjustmentsDiv.appendChild(div);
                    });
                } else {
                    adjustmentsDiv.innerHTML = '<p class="text-muted">No adjustments found</p>';
                }

                //Fill in Key Performance Delivery Info
                const OTRateDiv = document.getElementById("onTimeRate");
                const avgDelayDiv = document.getElementById("avgDelay");
                const stdDelayDiv = document.getElementById("stdDelay");

                // Fix: Use || instead of | for OR operator, and add % sign
                OTRateDiv.innerHTML = (my_JSON_object.otr[0].OTR || "N/A") + "%";

                if (my_JSON_object.shipmentDetails && my_JSON_object.shipmentDetails.length > 0) {
                    avgDelayDiv.innerHTML = (my_JSON_object.shipmentDetails[0].avgDelay || "N/A") + " days";
                    stdDelayDiv.innerHTML = (my_JSON_object.shipmentDetails[0].stdDelay || "N/A") + " days";
                } else {
                    avgDelayDiv.innerHTML = "N/A";
                    stdDelayDiv.innerHTML = "N/A";
                }

                //Disruption Event List
                const disruptionDiv = document.getElementById("disruptEvents");
                 disruptionDiv.innerHTML = "";
                if (my_JSON_object.disruptionEvents && my_JSON_object.disruptionEvents.length > 0) {
                my_JSON_object.disruptionEvents.forEach(item => {
                    const li = document.createElement("li");
                    li.className = "list-group-item";
                    li.textContent = `${item.CategoryName} ID: ${item.EventID} Date of Event: ${item.EventDate} Recovered: ${item.EventRecoveryDate} `;
                    disruptionDiv.appendChild(li);
                });
                } else {
                    disruptionDiv.innerHTML = '<p class="text-muted">No disruption events found</p>';
                }

                //Disruption Event Category BarChart
                // Bar Chart of disruption event category distribution
                const disruptionBarDiv = document.getElementById("disruptEventsBarChart");
                disruptionBarDiv.innerHTML = "";
                if (my_JSON_object.disruptionEventsDistribution && my_JSON_object.disruptionEventsDistribution.length > 0) {
                    const categories = my_JSON_object.disruptionEventsDistribution.map(item => item.CategoryName);
                    const counts = my_JSON_object.disruptionEventsDistribution.map(item => parseInt(item.NumEvents));
                    
                    var barData = [{
                        x: categories,
                        y: counts,
                        type: 'bar',
                        marker: {
                            color: '#0f6fab'
                        }
                    }];
                    
                    var barLayout = {
                        title: 'Disruption Event Category Distribution',
                        autosize: true,
                        margin: { l: 50, r: 50, t: 50, b: 120 },
                        xaxis: {
                            // tickangle: -45,
                            title: 'Event Category'
                        },
                        yaxis: {
                            title: 'Number of Events'
                        }
                    };
                    
                    Plotly.newPlot('disruptEventsBarChart', barData, barLayout, {responsive: true});
                } else {
                    disruptionBarDiv.innerHTML = '<p class="text-muted">No disruption events data available</p>';
                }
                                    
        }
    };
    xhtpp.open("GET", "supplychainmanager_homepage_queries.php?q=" + input, true);
    console.log("Sending request with q=" + input);
    xhtpp.send();
}

</script>
</html>
