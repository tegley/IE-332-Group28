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
                        <button class="nav-link active" id="count-tab" data-bs-toggle="tab" data-bs-target="#count" type="button" role="tab">
                            Transaction Count
                        </button>
                    </li>

                    <!-- TAB 2 -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="volume-tab" data-bs-toggle="tab" data-bs-target="#volume" type="button" role="tab">
                            Shipment Volume
                        </button>
                    </li>

                    <!-- TAB 3 -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="average_delay-tab" data-bs-toggle="tab" data-bs-target="#average_delay" type="button" role="tab">
                            Average Delay
                        </button>
                    </li>

                    <!-- TAB 4 -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="product-tab" data-bs-toggle="tab" data-bs-target="#product" type="button" role="tab">
                            Product
                        </button>
                    </li>

                </ul>

                <!-- Start TAB CONTENT WRAPPER -->
                <div class="tab-content" id="myTabContent">

                    <!-- TAB 1: TOP DISTRIBUTORS BY TOTAL TRANSACTION COUNT -->
                    <div class="tab-pane fade show active" id="count" role="tabpanel" aria-labelledby="count-tab">

                        <div class="area-header">Distributor Shipment Frequency</div>

                        <!-- Search Bar for Tab 1 -->
                        <div class="card mb-3">
                            <div class="card-body row">

                                <div class="col-md-12">
                                    <label>Select Distributor</label>
                                    <select class="form-control" id="CountDistributor_input" onchange="loadResultsTransactions(document.getElementById('CountDistributor_input').value)">
                                        <option value="">All Distributors</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <!-- TABLE CARD -->
                        <div class="col-md-12">
                            <div class="card d-flex flex-column mb-3" style="height: 550px; overflow: hidden;">
                                <div class="card-header fw-bold text-center">Shipment Quantity Over Time </div>
                                <div id="DistributorTransactionVolumeChart" style="height: 550px;">
                                    <p class="text-muted">Submit query to see results...</p>
                                </div>
                            </div>
                        </div>

                    </div> <!-- END TAB 1 -->

                    <!-- TAB 2: TOP DISTRIBUTORS BY SHIPMENT VOLUME -->
                    <div class="tab-pane fade" id="volume" role="tabpanel" aria-labelledby="volume-tab">

                        <div class="area-header">Top Distributors by Shipment Volume</div>

                        <!-- Search Bar for Tab 2 -->
                        <div class="card mb-3">
                            <div class="card-body row">

                                <div class="col-md-12">
                                    <label>Select Distributor</label>
                                    <select class="form-control" id="VolumeDistributor_input" onchange="loadResultsVolume(document.getElementById('VolumeDistributor_input').value)">
                                        <option value="">All Distributors</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="row">

                            <!-- LEFT TABLE -->
                            <div class="col-md-6">
                                <div class="card" style="height: 350px;">
                                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Distributor</th>
                                                    <th>Total Volume</th>
                                                </tr>
                                            </thead>
                                            <tbody id="TopDistributorVolumeTable">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT CHART -->
                            <div class="col-md-6">
                                <div class="card d-flex flex-column" style="height: 350px; overflow: hidden">
                                    <div class="card-header fw-bold text-center">Top 10 Distributors by Average Shipment Volume</div>
                                    <div id="DistributorVolumeChart" class="flex-grow-1">
                                        <p class="text-muted">Submit query to see results...</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div> <!-- END TAB 2 -->

                    <!-- TAB 3: DISTRIBUTORS BY AVERAGE DELAY -->
                    <div class="tab-pane fade" id="average_delay" role="tabpanel" aria-labelledby="average_delay-tab">

                        <div class="area-header">Distributors by Average Delay</div>

                        <!-- Search Bar for Tab 3 -->
                        <div class="card mb-3">
                            <div class="card-body row">

                                <div class="col-md-12">
                                    <label>Select Distributor</label>
                                    <select class="form-control" id="AvgDelayDistributor_input" onchange="loadResultsAVGDelay(document.getElementById('AvgDelayDistributor_input').value)">
                                        <option value="">All Distributors</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="row">

                            <!-- LEFT TABLE -->
                            <div class="col-md-6">
                                <div class="card" style="height: 350px;">
                                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Distributor</th>
                                                    <th>Avg Delay</th>
                                                </tr>
                                            </thead>
                                            <tbody id="AvgDelayTable">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT CHART -->
                            <div class="col-md-6">
                                <div class="card d-flex flex-column" style="height: 350px; overflow: hidden">
                                    <div class="card-header fw-bold text-center">Top 10 Distributors with Least Delay</div>
                                    <div id="AvgDelayChart" class="flex-grow-1">
                                        <p class="text-muted">Submit query to see results...</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div> <!-- END TAB 3 -->

                    <!-- TAB 4: PRODUCT DISTRIBUTION -->
                    <div class="tab-pane fade" id="product" role="tabpanel" aria-labelledby="product-tab">

                        <div class="area-header">Product Distribution</div>

                        <!-- Search Bar for Tab 4 -->
                        <div class="card mb-3">
                            <div class="card-body row">

                                <div class="col-md-12">
                                    <label>Select Distributor</label>
                                    <select class="form-control" id="ProductDistributor_input" onchange="loadResultsProduct(document.getElementById('ProductDistributor_input').value)">
                                        <option value="">All Distributors</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="row">

                            <!-- LEFT TABLE -->
                            <div class="col-md-6">
                                <div class="card" style="height: 350px;">
                                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Total Volume</th>
                                                </tr>
                                            </thead>
                                            <tbody id="ProductDistTable">
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted">
                                                        Select Distributor of Interest
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT PIE CHART -->
                            <div class="col-md-6">
                                <div class="card d-flex flex-column" style="height: 350px; overflow: hidden">
                                    <div class="card-header fw-bold text-center">Product Delivered Distribution Chart</div>
                                    <div id="ProductDistPie" class="flex-grow-1">
                                        <p class="text-muted">Submit query to see results...</p>
                                    </div>
                                </div>
                            </div>


                        </div>

                    </div> <!-- END TAB 4 -->
                </div> <!-- END tab-content -->

            </div> <!-- col-md-9 -->
        </div> <!-- row -->
    </div> <!-- container -->

<!-- Need rewrite to fit Senior Manager distributor Page-->
<script> //JavaScript for resizing Plotly graphs
//Ensure graphs are properly sized when other tabs are clicked
//Achieve this by looping through all tabs and triggering the autosize function for all tabs
const tabElms = document.querySelectorAll('button[data-bs-toggle="tab"]');

// Loop through each tab element
tabElms.forEach(tabElm => {
    tabElm.addEventListener('shown.bs.tab', event => {
        
        const targetTabId = event.target.getAttribute('data-bs-target');
        
        //If the tab is active, resize the chart to fit to card dimensions
        if (targetTabId === '#volume') {
            const chartContainer = document.getElementById('DistributorVolumeChart');
            if (chartContainer) {
                Plotly.relayout(chartContainer, { autosize: true });
            }
        } 
        else if (targetTabId === '#average_delay') {
            const chartContainer = document.getElementById('AvgDelayChart');
            if (chartContainer) {
                Plotly.relayout(chartContainer, { autosize: true });
            }
        }
    });
});

//Load Company Names when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadCompanies();
    //loadResultsTransactions("");
    //loadResultsVolume("");
    //loadResultsAVGDelay("");
});

function loadCompanies() {
    fetch('distributorList.php')
        .then(response => response.json())
        .then(data => {

            // Populate Distributor Count dropdown
            const distCountDropdown = document.getElementById('CountDistributor_input');
            distCountDropdown.innerHTML = '';
            distCountDropdown.appendChild(createDisabledOption());
            distCountDropdown.appendChild(createDefaultOption());
            
            data.distributors.forEach(distributor => {
                const option = document.createElement('option');
                option.value = distributor.CompanyName;
                option.textContent = distributor.CompanyName;
                distCountDropdown.appendChild(option);
            });

            //Popluate Distributor Volume Drop Down
            const distVolDropdown = document.getElementById('VolumeDistributor_input');
            distVolDropdown.innerHTML = '';
            distVolDropdown.appendChild(createDisabledOption());
            distVolDropdown.appendChild(createDefaultOption());
            
            data.distributors.forEach(distributor => {
                const option = document.createElement('option');
                option.value = distributor.CompanyName;
                option.textContent = distributor.CompanyName;
                distVolDropdown.appendChild(option);
            });

            //Populate Average Delay Drop Down
            const distDelayDropdown = document.getElementById('AvgDelayDistributor_input');
            distDelayDropdown.innerHTML = '';
            distDelayDropdown.appendChild(createDisabledOption());
            distDelayDropdown.appendChild(createDefaultOption());
            
            data.distributors.forEach(distributor => {
                const option = document.createElement('option');
                option.value = distributor.CompanyName;
                option.textContent = distributor.CompanyName;
                distDelayDropdown.appendChild(option);
            });

            //Populate Product Drop Down
            const distProductDropdown = document.getElementById('ProductDistributor_input');
            distProductDropdown.innerHTML = '';
            distProductDropdown.appendChild(createDisabledOption());

            data.distributors.forEach(distributor => {
                const option = document.createElement('option');
                option.value = distributor.CompanyName;
                option.textContent = distributor.CompanyName;
                distProductDropdown.appendChild(option);
            });
        })
}

//Need to create the default option seperately cuz a DOM object can only exist in one place at a time
function createDefaultOption() {
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'All Distributors';
    return defaultOption;
}

function createDisabledOption() {
    const disabled_option = document.createElement('option');
    disabled_option.value = "";
    disabled_option.textContent = "Select Distributor:";
    disabled_option.disabled = true;
    disabled_option.selected = true;
    return disabled_option;
}

</script>


    <script>

        function loadResultsTransactions(company_name){
            console.log(company_name);
            xhtpp = new XMLHttpRequest();

            xhtpp.onload = function () {
                if (this.readyState == 4 && this.status == 200) {

                    my_JSON_object = JSON.parse(this.responseText);
                    console.log("Data loaded successfully");
                    console.log(JSON.stringify(my_JSON_object));

                    const chart = document.getElementById("DistributorTransactionVolumeChart");
                    chart.innerHTML = ""; //Clear out placeholder

                    // Filter data if user specified
                    let data = "";
                    if(!company_name || company_name.length === 0) {
                        data = my_JSON_object.shipping;
                    } else {
                        data = my_JSON_object.shipping.filter(item => item.CompanyName === company_name);
                    }

                    if (!data || data.length === 0) {
                        chart.innerHTML = '<p class="text-muted text-center">No data available for selected distributor</p>';
                        return;
                    }

                    // Aggregate quantity by date
                    const dateQuantities = {};
                    
                    data.forEach(item => {
                        const date = item.PromisedDate;
                        const quantity = parseInt(item.Quantity) || 0;
                        
                        if (dateQuantities[date]) {
                            dateQuantities[date] += quantity;
                        } else {
                            dateQuantities[date] = quantity;
                        }
                    });
                    
                    // Convert to arrays and sort by date
                    const dates = Object.keys(dateQuantities).sort();
                    const quantities = dates.map(date => dateQuantities[date]);
                    
                    // Convert dates to Date objects for Plotly
                    const dateObjects = dates.map(d => new Date(d));

                    // Create the plot
                    var trace = {
                        mode: 'lines+markers',
                        x: dateObjects,
                        y: quantities,
                        type: 'scatter',
                        line: {
                            color: '#0f6fab',
                            width: 2
                        },
                        marker: {
                            size: 6,
                            color: '#0f6fab'
                        },
                        hovertemplate: '<b>%{x|%Y-%m-%d}</b><br>Quantity: %{y:,}<extra></extra>'
                    };
                    
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
                    
                    var layout = {
                        title: {
                            text: company_name && company_name.length > 0 ? 
                                `Shipment Quantity Over Time - ${company_name}` : 
                                'Shipment Quantity Over Time (All Distributors)'
                        },
                        xaxis: {
                            rangeselector: selectorOptions,
                            rangeslider: {
                                visible: true,
                                thickness: 0.1,
                                bgcolor: '#f8f9fa',
                                bordercolor: '#dee2e6',
                                borderwidth: 1
                            },
                            title: 'Shipment Date: Use Slider to Adjust Date Range',
                            type: 'date'
                        },
                        yaxis: {
                            fixedrange: false,
                            title: 'Total Quantity Shipped',
                            tickformat: ','
                        },
                        hovermode: 'closest',
                        autosize: true,
                        margin: { l: 75, r: 50, t: 75, b: 80 }
                    };
                    
                    Plotly.newPlot('DistributorTransactionVolumeChart', [trace], layout, {responsive: true});

                } // END readyState if
                else{
                    console.log("Error loading data")
                }
            } // END onload function
            
            console.log("Sending: seniorDistributorQueries.php" + (company_name ? "?q=" + company_name : ""));
            xhtpp.open("GET", "seniorDistributorQueries.php" + (company_name ? "?q=" + company_name : ""), true);
            xhtpp.send();

        }

        function loadResultsVolume(company_name){

            xhtpp = new XMLHttpRequest();

            xhtpp.onload = function () {
                if (this.readyState == 4 && this.status == 200) {

                    my_JSON_object = JSON.parse(this.responseText);
                    console.log("Katya is rad");
                    let data = "";
                    console.log(JSON.stringify(my_JSON_object));

                    const TopDistributorVolumeTable = document.getElementById("TopDistributorVolumeTable");
                    TopDistributorVolumeTable.innerHTML = ""; //Clear out placeholder

                    //Filter data if user specified
                    if(!company_name || company_name.length === 0) { //If selection not selected or value is null, display all data
                        data = my_JSON_object.TD_AVGShipment;
                        console.log("Why aren't you working");
                    } else{
                        data = my_JSON_object.TD_AVGShipment.filter(item => item.CompanyName === company_name);
                        console.log(data);
                    }

                    if (data && data.length > 0) {
                        for (let i = 0; i < data.length; i++){
                                const row = TopDistributorVolumeTable.insertRow();
                                row.innerHTML = `
                                    <td>${data[i].CompanyName}</td>
                                    <td>${data[i].AVGVolume}</td>
                                    `;
                            }
                        } else {
                            const row = TopDistributorVolumeTable.insertRow();
                                row.innerHTML = `
                                    <td>No company data</td>
                                    <td>N/A</td>
                                    `;
                        }
                    //Plot top Distributors by Shipment Volume
                    const chart = document.getElementById("DistributorVolumeChart");
                    chart.innerHTML = ""; //Clear out placeholder
                    const avgVol = my_JSON_object.TD_AVGShipment.slice(0, 10).map((item) => { return item.AVGVolume }); //Limit to top 10
                    console.log(avgVol);
                    const topCompanies = my_JSON_object.TD_AVGShipment.slice(0, 10).map((item) => { return item.CompanyName}); //Limit to top 10
                    console.log(topCompanies);
                    var trace = {
                    y: topCompanies,  // Horizontal bars
                    x: avgVol,
                    type: 'bar',
                    orientation: 'h',
                    marker: { 
                        color: topCompanies.map((item, i) => i === 0 ? '#d95f02' : '#0f6fab'),
                        line: { color: '#fff', width: 1 }
                    },
                    hovertemplate: '<b>%{y}</b><br>Quantity: %{x:,}<extra></extra>'
                    };

                    var layout = {
                        xaxis: { title: 'Average Quantity Shipped', tickformat: ',' },
                        yaxis: { title: '', automargin: true },
                        autosize: true,
                        margin: { l: 20, r: 20, t: 20, b: 60 }
                    };

                    Plotly.newPlot('DistributorVolumeChart', [trace], layout, {responsive: true});

                } // END readyState if
                else{
                    console.log("Bad bad bad")
                }
            } // END onload function
            console.log("Sending: seniorDistributorQueries.php")
            xhtpp.open("GET", "seniorDistributorQueries.php", true);
            xhtpp.send();

        }

        function loadResultsAVGDelay(company_name){

            xhtpp = new XMLHttpRequest();

            xhtpp.onload = function () {
                if (this.readyState == 4 && this.status == 200) {

                    my_JSON_object = JSON.parse(this.responseText);
                    console.log("Katya is rad");
                    let data = "";
                    console.log(JSON.stringify(my_JSON_object));

                    const AvgDelayTable = document.getElementById("AvgDelayTable");
                    AvgDelayTable.innerHTML = ""; //Clear out placeholder

                    //Filter data if user specified
                    if(!company_name || company_name.length === 0) { //If selection not selected or value is null, display all data
                        data = my_JSON_object.distributor_delay;
                        console.log("Why aren't you working");
                    } else{
                        data = my_JSON_object.distributor_delay.filter(item => item.CompanyName === company_name);
                        console.log(data);
                    }

                    if (data && data.length > 0) {
                        for (let i = 0; i < data.length; i++){
                                const row = AvgDelayTable.insertRow();
                                row.innerHTML = `
                                    <td>${data[i].CompanyName} </td>
                                    <td>${data[i].AverageDelay}</td>
                                    `;
                            }
                        } else {
                            const row = AvgDelayTable.insertRow();
                                row.innerHTML = `
                                    <td>No company data</td>
                                    <td>N/A</td>
                                    `;
                        }
                    //Plot top Distributors by Shipment Volume
                    const chart = document.getElementById("AvgDelayChart");
                    chart.innerHTML = ""; //Clear out placeholder
                    const avgDelay = my_JSON_object.distributor_delay.slice(0, 10).map((item) => { return item.AverageDelay }); //Limit to 10 companies with the least delay
                    console.log(avgDelay);
                    const topCompanies = my_JSON_object.distributor_delay.slice(0, 10).map((item) => { return item.CompanyName}); //Limit to 10 companies with the least delay
                    console.log(topCompanies);
                    var trace = {
                    y: topCompanies,  // Horizontal bars
                    x: avgDelay,
                    type: 'bar',
                    orientation: 'h',
                    marker: { 
                        color: topCompanies.map((item, i) => i === 0 ? '#355E3B' : '#0f6fab'),
                        line: { color: '#fff', width: 1 }
                    },
                    hovertemplate: '<b>%{y}</b><br>Delay: %{x:,}<extra></extra>'
                    };

                    var layout = {
                        xaxis: { title: 'Average Delay in Days', tickformat: ',' },
                        yaxis: { title: '', automargin: true },
                        autosize: true,
                        margin: { l: 20, r: 20, t: 20, b: 60 }
                    };

                    Plotly.newPlot('AvgDelayChart', [trace], layout, {responsive: true});

                } // END readyState if
                else{
                    console.log("Bad bad bad")
                }
            } // END onload function
            console.log("Sending: seniorDistributorQueries.php")
            xhtpp.open("GET", "seniorDistributorQueries.php", true);
            xhtpp.send();

        }

        function loadResultsProduct(company_name){

            xhtpp = new XMLHttpRequest();

            xhtpp.onload = function () {
                if (this.readyState == 4 && this.status == 200) {

                    data = JSON.parse(this.responseText);
                    console.log("Katya is rad");
                    console.log(JSON.stringify(data));

                    const ProductDistTable = document.getElementById("ProductDistTable");
                    ProductDistTable.innerHTML = ""; //Clear out placeholder


                    if (data.productsHandled && data.productsHandled.length > 0) {
                        data.productsHandled.forEach(item => {
                                const row = ProductDistTable.insertRow();
                                row.innerHTML = `
                                    <td>${item.ProductName} </td>
                                    <td>${item.Quantity}</td>
                                    `;
                            }
                        )} else {
                            const row = ProductDistTable.insertRow();
                                row.innerHTML = `
                                    <td>No company data</td>
                                    <td>N/A</td>
                                    `;
                        }
                    //Pie chart of Product Shipped Distribution
                    const ProductDistPie = document.getElementById("ProductDistPie");
                    ProductDistPie.innerHTML = ""; //Clear out placeholder
                        var products = data.productsHandled
                        var labels = products.map(item => item.ProductName);
                        console.log(labels);

                    // Calculate delivered shipments (within user specified itme range)
                    var productQuantity = products.map(item => item.Quantity);
                    console.log(productQuantity);

                    //Plot Details
                    var pieData = [{
                        values: productQuantity,
                        labels: labels,
                        type: 'pie',
                        textinfo: 'none'
                        }];
                    var layout = {
                        autosize: true,
                        margin: { l: 20, r: 20, t: 20, b: 20 },
                        hovertemplate: '<b>%{labels}</b><br>Quantity: %{values:,}<extra></extra>'
                        };

                    Plotly.newPlot('ProductDistPie', pieData, layout, {responsive: true});

                } // END readyState if
                else{
                    console.log("Bad bad bad")
                }
            } // END onload function
            console.log("Sending: seniorDistributorQueries.php?q=" + company_name)
            xhtpp.open("GET", "seniorDistributorQueries.php?q=" + company_name, true);
            xhtpp.send();

        }

    </script>

</body>
</html>
</html>
