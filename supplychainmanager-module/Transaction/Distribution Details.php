<?php
session_start();

// Security check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$user_FullName = $_SESSION['FullName'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distributor Details</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import "standardized_project_formatting.css";

        .scroll-box {
            height: 200px;
            overflow-y: auto;
            border: 1px solid #aaa;
            padding: 10px;
            border-radius: 6px;
            background-color: white;
        }

        .blue-banner {
            background-color: #0f6fab;
            color: white;
            padding: 20px;
            font-size: 40px;
            margin-top: 20px;
            border-radius: 6px;
            font-family: Cambria, serif;
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
    </style>
</head>

<body>

<!-- Banner -->
<h1 class="blue-banner">Distributor Details</h1>

<div class="container">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3">
            <div id="supplychainmanager_sidebar"></div>
            <script>
                fetch('supplychainmanager_sidebar.html')
                    .then(response => response.text())
                    .then(html => document.getElementById('supplychainmanager_sidebar').innerHTML = html);
            </script>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">

            <!-- Dashboard header -->
            <div class="card" id="dashboard-header">
                <?php echo "{$user_FullName}'s SCM Dashboard" ?>
            </div>

            <!-- Distributor Name + Date Inputs -->
            <form action="#" method="post" name="DistributorForm" class="mt-3">

                <label>Distributor Name</label>
                <input type="text" class="form-control" name="DistributorName">

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Start Date</label>
                        <input type="date" class="form-control" name="StartDate">
                    </div>

                    <div class="col-md-6">
                        <label>End Date</label>
                        <input type="date" class="form-control" name="EndDate">
                    </div>
                </div>

                <button type="button" onclick="PullDistributor()" class="btn btn-primary mt-3">Submit</button>
            </form>

            <!-- Statistics + Products Handled -->
            <div class="row mt-4">

                <!-- LEFT: Statistics Table -->
                <div class="col-md-6">

                    <div class="stats-header">Statistics</div>

                    <table class="table stats-table">
                        <tr><td>On Time Delivery Rate</td></tr>
                        <tr><td>Total Quantity Shipped</td></tr>
                        <tr><td>Average Shipment Quantity Per Transaction</td></tr>
                        <tr><td>Total Number of Transactions</td></tr>
                        <tr><td>Disruption Exposure</td></tr>
                    </table>

                </div>

                <!-- RIGHT: Products Handled scroll-box -->
                <div class="col-md-6">
                    <div class="stats-header">Products Handled</div>
                    <div class="scroll-box">
                        <p>Product A</p>
                        <p>Product B</p>
                        <p>Product C</p>
                        <p>Product D</p>
                        <p>Product E</p>
                    </div>
                </div>

            </div>

            <!-- Bottom row: Shipments Out + Status Pie Chart -->
            <div class="row mt-4">

                <!-- LEFT -->
                <div class="col-md-6">
                    <div class="stats-header">Shipments Out for Distribution</div>
                    <div class="scroll-box">
                        <p>Shipment 1</p>
                        <p>Shipment 2</p>
                        <p>Shipment 3</p>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="col-md-6 text-center">
                <div class="stats-header">Status Distribution</div>
                <img src="supply_chain_plot_temp.png" width="250" height="250">
                <p>Chart</p>
                </div>
            </div>

        </div> <!-- end col-md-9 -->

    </div> <!-- row -->
</div> <!-- container -->


<script>
function PullDistributor() {
    const input = document.DistributorForm.DistributorName.value;

    xhtpp = new XMLHttpRequest();
    xhtpp.onload = function(){
        if (this.readyState == 4 && this.status == 200) {
            const object = JSON.parse(this.responseText);
            index_zero = object[0];

            // add these later
        }
    };
    xhtpp.open("GET", "distributor_details_query.php?q=" + input, true);
    xhtpp.send();
}
</script>

</body>
</html>