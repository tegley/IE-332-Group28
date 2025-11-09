<?php
session_start();

//**Check if the user is NOT logged in** (Security Check)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    //If not logged in, redirect to the login page
    header("Location: login.php");
    exit;
}

//If the code reaches here, the user has been authenticated.
$user_FullName = $_SESSION['FullName'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Manager Dashboard</title>
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
        rel="stylesheet">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">
    <style>
    @import "standardized_project_formatting.css";
    .custom-sidebar {
        background-color: #e4e1d6; /* Light beige */
        color: black;
        position: fixed;           /* Keep it at the top-left of the viewport */
        top: 150px;
        left: 0;
        float: left;
        z-index: 1000;
        width: 275px;            /* Make sure it stays above main content */
    }

    .bubble{
        display: inline-block;
        padding: 15px 25px;
        border-radius: 20px;
        color: white;
        font-weight: bold;
        margin: 25px;
    }
    .red{background-color: red;}
    .blue{background-color: blue;}
    
    .button{
        display: inline-block;
        background-color: #26b0b9;
        color: white;
        padding: 8px 14px;
        border-radius: 6px;
        margin-top: 15px;
        margin-left: -475px;
        text-decoration: none;
    }

    .plots{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 30px;
    }

    .plot{
        margin: 15px;
        text-align: center;
    }

    .plot img{
        width: 150px;
        height: 200px;
        background-color: white; /* Placeholder */
    }

   </style>
</head>

<!--Integrate JavaScript to incorporate sidebar module -->
<div id="sidebar_supplychainmanager"></div>
<script>
  fetch('sidebar_supplychainmanager.html')
    .then(response => response.text())
    .then(html => document.getElementById('sidebar_supplychainmanager').innerHTML = html);
</script>
<body>     
    <div>
        <span class="bubble red">Alerts</span>
        <span class="bubble blue">Messages</span>
    </div>

    <div>
        <a href="#" class="button"> Welcome <?php echo htmlspecialchars($user_FullName);?>   </a>
    </div>

    <div class="plots">
        <div class="plot">
            <img src="supply_chain_plot_temp.png" width=250 height=250 alt="Supply Chain Plot 1">
            <p>Useful plot #1</p>
        </div>
        <div class="plot">
            <img src="supply_chain_plot_temp.png" width=250 height=250 alt="Supply Chain Plot 2">
            <p>Useful plot #2</p>
        </div>
        <div class="plot">
            <img src="supply_chain_plot_temp.png" width=250 height=250 alt="Supply Chain Plot 3">
            <p>Useful plot #3</p>
        </div>
        <div class="plot">
            <img src="supply_chain_plot_temp.png" width=250 height=250 alt="Supply Chain Plot 4">
            <p>Useful plot #4</p>
        </div>
    </div>
    
</body>
</html>
