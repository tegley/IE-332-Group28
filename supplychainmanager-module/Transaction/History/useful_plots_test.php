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
    <title>Useful Plots</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @import "standardized_project_formatting.css";

        .blue-banner {
            background-color: #0f6fab;
            color: white;
            padding: 20px;
            font-size: 40px;
            margin-top: 20px;
            border-radius: 6px;
            font-family: Cambria, serif;
        }

        .plot-box {
            text-align: center;
            margin: 20px;
        }

        .plot-box img {
            width: 180px;
            height: 130px;
            background-color: white;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
    </style>
</head>

<body>

<h1 class="blue-banner">Useful Plots</h1>

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

            <div class="card" id="dashboard-header">
                <?php echo "{$user_FullName}'s SCM Dashboard" ?>
            </div>

            <!-- Plot Grid -->
            <div class="row mt-4">

                <!-- Plot 1 -->
                <div class="col-md-6 plot-box">
                    <img src="supply_chain_plot_temp.png" alt="Plot 1">
                    <p>Useful Plot #1</p>
                </div>

                <!-- Plot 2 -->
                <div class="col-md-6 plot-box">
                    <img src="supply_chain_plot_temp.png" alt="Plot 2">
                    <p>Useful Plot #2</p>
                </div>

                <!-- Plot 3 -->
                <div class="col-md-6 plot-box">
                    <img src="supply_chain_plot_temp.png" alt="Plot 3">
                    <p>Useful Plot #3</p>
                </div>

                <!-- Plot 4 -->
                <div class="col-md-6 plot-box">
                    <img src="supply_chain_plot_temp.png" alt="Plot 4">
                    <p>Useful Plot #4</p>
                </div>

            </div>

        </div> <!-- col-md-9 -->

    </div> <!-- row -->
</div> <!-- container -->

</body>
</html>
