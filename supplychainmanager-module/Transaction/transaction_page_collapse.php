<?php
session_start();

// Security Check
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
    <title>SCM Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import "standardized_project_formatting.css";

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

        /* -------- COLLAPSIBLE -------- */
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

        /* -------- PLOT BOX -------- */
        .plot-box {
            text-align: center;
            margin: 20px;
            width: 100%;
            max-width: 280px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .plot-box img {
            width: 100%;
            height: 180px;
            object-fit: contain;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        body {
            background-color: #f5f6f8;
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

<div class="container-fluid">
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

        <!-- Main Content -->
        <div class="col-md-9">

            <!-- Dashboard Header -->
            <div class="card mt-3 p-3" id="dashboard-header">
                <h3><?php echo "{$user_FullName}'s SCM Dashboard"; ?></h3>
            </div>

            <!-- --------------------- COMPANY TRANSACTIONS --------------------- -->

            <h2 class="section-banner collapsible-header" onclick="toggleCollapse('companySection')">
                Company Transactions
                <span class="collapse-arrow" data-arrow="companySection">▼</span>
            </h2>

            <div id="companySection" class="collapse-content">

                <form class="mt-3">
                    <label>Company Name</label>
                    <input type="text" class="form-control">

                    <label class="mt-2">Location Type</label>
                    <select class="form-control">
                        <option>Country</option>
                        <option>State</option>
                        <option>City</option>
                    </select>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Start Date</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>End Date</label>
                            <input type="date" class="form-control">
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary mt-3">Submit</button>
                </form>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h5>Leaving From</h5>
                        <div class="scroll-box">
                            <p>Location A</p><p>Location B</p><p>Location C</p><p>Location D</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5>Arriving At</h5>
                        <div class="scroll-box">
                            <p>Location X</p><p>Location Y</p><p>Location Z</p><p>Location M</p>
                        </div>
                    </div>
                </div>

            </div> <!-- END companySection -->

            <!-- --------------------- DISTRIBUTOR DETAILS --------------------- -->

            <h2 class="section-banner collapsible-header" onclick="toggleCollapse('distributorSection')">
                Distributor Details
                <span class="collapse-arrow" data-arrow="distributorSection">▼</span>
            </h2>

            <div id="distributorSection" class="collapse-content">

                <form class="mt-3">
                    <label>Distributor Name</label>
                    <input type="text" class="form-control">

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Start Date</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>End Date</label>
                            <input type="date" class="form-control">
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary mt-3">Submit</button>
                </form>

                <div class="row mt-4">

                    <!-- Stats Table -->
                    <div class="col-md-6">
                        <div class="stats-header">Statistics</div>
                        <table class="table stats-table">
                            <tr><td>On Time Delivery Rate</td><td></td></tr>
                            <tr><td>Total Quantity Shipped</td><td></td></tr>
                            <tr><td>Average Shipment Qty</td><td></td></tr>
                            <tr><td>Total Transactions</td><td></td></tr>
                            <tr><td>Disruption Exposure</td><td></td></tr>
                        </table>
                    </div>

                    <!-- Products Handled -->
                    <div class="col-md-6">
                        <div class="stats-header">Products Handled</div>
                        <div class="scroll-box">
                            <p>Product A</p><p>Product B</p><p>Product C</p><p>Product D</p>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <!-- Shipments Out -->
                    <div class="col-md-6">
                        <div class="stats-header">Shipments Out</div>
                        <div class="scroll-box">
                            <p>Shipment 1</p><p>Shipment 2</p><p>Shipment 3</p>
                        </div>
                    </div>

                    <!-- Status Chart -->
                    <div class="col-md-6 text-center">
                        <div class="stats-header">Status Distribution</div>
                        <img src="supply_chain_plot_temp.png" width="250" height="250">
                    </div>
                </div>

            </div> <!-- END distributorSection -->

            <!-- --------------------- USEFUL PLOTS --------------------- -->

            <h2 class="section-banner collapsible-header" onclick="toggleCollapse('plotsSection')">
                Useful Plots
                <span class="collapse-arrow" data-arrow="plotsSection">▼</span>
            </h2>

            <div id="plotsSection" class="collapse-content">

                <div class="container mt-4">
                    <div class="row g-4">

                        <!-- Plot 1 -->
                        <div class="col-md-6 d-flex justify-content-center">
                            <div class="plot-box">
                                <img src="supply_chain_plot_temp.png" alt="Plot 1">
                                <p>Useful Plot #1</p>
                            </div>
                        </div>

                        <!-- Plot 2 -->
                        <div class="col-md-6 d-flex justify-content-center">
                            <div class="plot-box">
                                <img src="supply_chain_plot_temp.png" alt="Plot 2">
                                <p>Useful Plot #2</p>
                            </div>
                        </div>

                        <!-- Plot 3 -->
                        <div class="col-md-6 d-flex justify-content-center">
                            <div class="plot-box">
                                <img src="supply_chain_plot_temp.png" alt="Plot 3">
                                <p>Useful Plot #3</p>
                            </div>
                        </div>

                        <!-- Plot 4 -->
                        <div class="col-md-6 d-flex justify-content-center">
                            <div class="plot-box">
                                <img src="supply_chain_plot_temp.png" alt="Plot 4">
                                <p>Useful Plot #4</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div> <!-- END plotsSection -->

        </div> <!-- END col-md-9 -->
    </div> <!-- END row -->
</div> <!-- END container -->

</body>
</html>
