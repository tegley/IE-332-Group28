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

    /* Top Dasboard Header */
    #dashboard-header{
        background-color: #ececec;  /* CHANGED: from #c0bebe → softer gray */
        width: auto;
        height: auto;
        font-family: Cambria, serif; /* sets pretty font */
        font-size: 30px;
        color: #222;                /* NEW: darker text */
        padding: 15px;              /* NEW: adds balance to main panel */
        border-radius: 6px;         /* NEW: smoother shape */
        text-align: center;         /* NEW: explicitly centers dashboard text */
        margin-bottom: 10px;
    }

    /* Bubble Header */
    .bubble-header{
        display: inline-block;
        padding: 12px 50px;
        border-radius: 20px;
        color: white;
        font-weight: bold;
        margin: 15px;
        text-align: center;
        background-color: #0f6fab;  /* CHANGED: from teal (#0fa3b1) → ERP-style blue */
        color: white;               /* NEW: added contrast */
        font-size: 20px;            /* CHANGED: slightly smaller for proportion */
    }

    /* Area Header */
    .area-header{
        background-color: #cbcbcb;
        width: auto;
        height: auto;
        font-family: Cambria, serif; /* sets pretty font */
        font-size: 20px;
        color: #222;                /* NEW: darker text */
        border-radius: 8px;         /* NEW: smoother shape */
        text-align: center;         /* NEW: explicitly centers dashboard text */
        margin-top: 15px;
        margin-bottom: 10px;
        padding: 15px;
    }

    /* Create a scroll bar */
    #scroll-format {
  		/*max-height: 250px;          set desired height */
  		overflow-x: hidden;        /* hide horizontal scrollbar */
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
                            <form action = "#" method = "post" name="CompanyInfoForm">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="w-50 me-2 text-end">
                                        <label>Company Name</label>
                                    </div>
                                    <input type="text" class="form-control me-2 w-50" name="CompanyName">
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
                                    <button type="button" onclick="CheckUserInput()" class="btn btn-primary">Submit</button>
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
                            <div class="card"> <div class="card-header">Important Info</div>
                                <div class="card-body row">
                                    <div class="col-4"> <div class="card">
                                            <div class="card-body">Address</div>
                                        </div>
                                    </div>
                                    <div class="col-8"> <div class="card">
                                            <div class="card-body" id="address"></div>
                                        </div>
                                    </div>
                                    <div class="col-6"> <div class="card"> 
                                            <div class="card-body">Company Type</div>
                                        </div>
                                    </div>
                                    <div class="col-6"> <div class="card">
                                            <div class="card-body" id="company-type"></div>
                                        </div>
                                    </div>
                                    <div class="col-6"> <div class="card">
                                            <div class="card-body">Tier Level</div>
                                        </div>
                                    </div>
                                    <div class="col-6"> <div class="card">
                                            <div class="card-body" id="tier-level"></div>
                                        </div>
                                    </div>
                                    <div class="col-6"> <div class="card">
                                            <div class="card-body">Financial Health Score</div>
                                        </div>
                                    </div>
                                    <div class="col-6"> <div class="card">
                                            <div class="card-body" id="financial-health-score"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-3" style="height: 500px;">
                                <div class="card-header">Other Information</div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush" id="scroll-format" style="max-height:400px;">
                                            <li class="list-group-item">Item 1</li>
                                            <li class="list-group-item">Item 2</li>
                                            <li class="list-group-item">Item 3</li>
                                            <li class="list-group-item">Item 4</li>
                                            <li class="list-group-item">Item 5</li>
                                            <li class="list-group-item">Item 6</li>
                                            <li class="list-group-item">Item 7</li>
                                            <li class="list-group-item">Item 8</li>
                                            <li class="list-group-item">Item 9</li>
                                            <li class="list-group-item">Item 10</li>
                                            <li class="list-group-item">Item 11</li>
                                            <li class="list-group-item">Item 12</li>
                                            <li class="list-group-item">Item 13</li>
                                            <li class="list-group-item">Item 14</li>
                                            <li class="list-group-item">Item 15</li>
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
                                    <ul class="list-group list-group-flush" id="scroll-format" style="max-height:400px;">
                                        <li class="list-group-item">Supplier 1</li>
                                        <li class="list-group-item">Supplier 2</li>
                                        <li class="list-group-item">Supplier 3</li>
                                    </ul>
                            </div>
                        </div>
                        <div class="col-md-6"> <!-- Right Side - Depended On By-->
                            <div class="card">
                                <div class="card-header">Company Is Depended On By</div>
                                    <ul class="list-group list-group-flush" id="scroll-format" style="max-height:400px;">
                                        <li class="list-group-item">Client 1</li>
                                        <li class="list-group-item">Client 2</li>
                                        <li class="list-group-item">Client 3</li>
                                        <li class="list-group-item">Client 1</li>
                                        <li class="list-group-item">Client 2</li>
                                        <li class="list-group-item">Client 3</li>
                                        <li class="list-group-item">Client 1</li>
                                        <li class="list-group-item">Client 2</li>
                                        <li class="list-group-item">Client 3</li>
                                        <li class="list-group-item">Client 1</li>
                                        <li class="list-group-item">Client 2</li>
                                        <li class="list-group-item">Client 3</li>
                                        <li class="list-group-item">Client 1</li>
                                        <li class="list-group-item">Client 2</li>
                                        <li class="list-group-item">Client 3</li>
                                    </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3" style="height: 450px;"> <!-- Larger Card 3-->
                    <div class="card-header">Product Diversity</div>
                        <div class="d-flex align-items-center justify-content-center flex-column">
                            <img src = "supply_chain_plot_temp.png" width=300 height=350>
                            <p>Pie Chart</p>
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
                                <ul class="list-group list-group-flush" id="scroll-format" style="max-height:400px;">
                                    <li class="list-group-item">Product X</li>
                                    <li class="list-group-item">Product Y</li>
                                    <li class="list-group-item">Product Z</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4"> <!-- Middle - Receiving -->
                            <div class="card">
                                <div class="card-header">Receiving</div>
                                <ul class="list-group list-group-flush" id="scroll-format" style="max-height:400px;">
                                    <li class="list-group-item">Product X</li>
                                    <li class="list-group-item">Product Y</li>
                                    <li class="list-group-item">Product Z</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4"> <!-- Right - Adjustments -->
                            <div class="card">
                                <div class="card-header">Adjustments</div>
                                <ul class="list-group list-group-flush" id="scroll-format" style="max-height:400px;">
                                    <li class="list-group-item">Product X</li>
                                    <li class="list-group-item">Product Y</li>
                                    <li class="list-group-item">Product Z</li>
                                </ul>
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
                            <div class="card mb-3"> <div class="card-header">Statistics</div>
                                <div class="card-body row">
                                    <div class="col-6"> <div class="card">
                                            <div class="card-body">On Time Delivery Rate</div>
                                        </div>
                                    </div>
                                    <div class="col-6"> <div class="card">
                                            <div class="card-body">50%</div>
                                        </div>
                                    </div>
                                    <div class="col-6"> <div class="card">
                                            <div class="card-body">Average Delay</div>
                                        </div>
                                    </div>
                                    <div class="col-6"> <div class="card">
                                            <div class="card-body">15</div>
                                        </div>
                                    </div>
                                    <div class="col-6"> <div class="card">
                                            <div class="card-body">Standard Deviation <br> of Delay</div>
                                        </div>
                                    </div>
                                    <div class="col-6"> <div class="card">
                                            <div class="card-body">9</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6"> <!-- Right Side - List of Disruption Events -->
                            <div class="card mb-3"> <div class="card-header">List of Disruption Events Over the Past Year</div>
                                <div class="card-body row">
                                    <ul class="list-group list-group-flush" id="scroll-format" style="max-height:300px;">
                                        <li class="list-group-item">Event 1</li>
                                        <li class="list-group-item">Event 2</li>
                                        <li class="list-group-item">Event 3</li>
                                        <li class="list-group-item">Event 4</li>
                                        <li class="list-group-item">Client 2</li>
                                        <li class="list-group-item">Client 3</li>
                                        <li class="list-group-item">Client 1</li>
                                        <li class="list-group-item">Client 2</li>
                                        <li class="list-group-item">Client 3</li>
                                        <li class="list-group-item">Client 1</li>
                                        <li class="list-group-item">Client 2</li>
                                        <li class="list-group-item">Client 3</li>
                                        <li class="list-group-item">Client 1</li>
                                        <li class="list-group-item">Client 2</li>
                                        <li class="list-group-item">Client 3</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-2 mb-2" style="height: 450px;"> <!-- Larger Card 2 - Financial Health Status Over the Past Year-->
                    <div class="card-header">Financial Health Status Over the Past Year</div>
                        <div class="d-flex align-items-center justify-content-center flex-column">
                            <img src = "supply_chain_plot_temp.png" width=300 height=350>
                            <p>Line Chart</p>
                        </div>
                </div>
                
                <div class="card mt-2 mb-3" style="height: 450px;"> <!-- Larger Card 3 - Financial Health Status Over the Past Year-->
                    <div class="card-header">Distribution of Disruption Event Counts Over the Past Year</div>
                        <div class="d-flex align-items-center justify-content-center flex-column">
                            <img src = "supply_chain_plot_temp.png" width=300 height=350>
                            <p>Histogram</p>
                        </div>
                </div>            
            
            </div> <!-- Closes col-md-9 -> add divs above this line!! -->

        </div> <!-- Row -->
    </div> <!-- Container -->
</body>

<script>
function CheckUserInput() {
    //User inputs
    const company_name = document.CompanyInfoForm.CompanyName.value;
    const start_date = document.CompanyInfoForm.StartDate.value;
    const end_date = document.CompanyInfoForm.EndDate.value;
    
    //Check for company input
    if (company_name == "") {
        alert ("Please provide a company!");
        document.CompanyInfoForm.CompanyName.focus();
        return false;
    }

    //Check for date input
    if (start_date =="" || end_date=="") {
        alert ("Please provide date range!");
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

function CompanyInformationAJAX(company_name, start_date, end_date) {    
    input = company_name + "," + start_date + "," + end_date;
    xhtpp = new XMLHttpRequest();
    xhtpp.onload = function(){
      if (this.readyState == 4 && this.status == 200) {
        const my_JSON_object = JSON.parse(this.responseText);

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
      }
  };
    xhtpp.open("GET", "supplychainmanager_homepage_queries.php?q=" + input, true);
    xhtpp.send();
}

</script>

</html>




<!-- This is a single-line comment -->
</body>
</html>
