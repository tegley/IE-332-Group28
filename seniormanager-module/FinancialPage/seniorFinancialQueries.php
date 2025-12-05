<?php
$servername = "mydb.itap.purdue.edu";

$username = "tegley";//yourCAREER/groupusername
$password = "#TurboCoder6412!!";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

$tmp = $_GET['q'];
$quer = $_GET['g']; //User specified filters

// Convert the comma-delimited string into an array of strings.
$tmp = explode(',', $tmp); //["start year", "initial quarter", "end year", "last quarter"]
// print_r($tmp);
$quer = explode('|', $quer); //["company type"]
// print_r($quer);


//We will need to build the queries per user selection, but the added constraints will be the same across all queries, so we will make additions rn
$whereDateState = ""; 
$havingTypeState = "";


//Time range contstraint added per user input
if (!empty($tmp[0])) { //Adding appropriate where based on user prpovided date range so long as sate range was properly recieved WHERE f.Quarter = ‘Input1’ AND f.Year BETWEEN “InputStartYear”
    $whereDateState = "WHERE (f.RepYear * 10 + (CASE f.Quarter WHEN 'Q1' THEN 1 WHEN 'Q2' THEN 2 WHEN 'Q3' THEN 3 WHEN 'Q4' THEN 4 END)) BETWEEN ({$tmp[0]} * 10 + {$tmp[1]}) AND ({$tmp[2]} * 10 + {$tmp[3]})"; //Gets financial health scores from start date to end date
        }

//HAVING statement added per user input
if (!empty($quer[0])) { //Adding appropriate company type WHERE ending if user input a specific company type. OPTIONAL TO USER
    switch ($quer[0]) {
        case "company":
            $havingTypeState = "AND c.Type = '" . $quer[1] . "'";
            break;
        default:
        $havingTypeState = "";
    }
}
// print_r($whereTypeState);

    //Finanical Health by company
    $companyFinancialsSelect = "SELECT ROUND(AVG(f.HealthScore), 2) AS avgHealth, c.CompanyName FROM Company c JOIN FinancialReport f ON c.CompanyID = f.CompanyID";

    //Puting query together and generating result
    $companyFinancialsQuery = "{$companyFinancialsSelect} {$whereDateState} GROUP BY c.CompanyName ORDER BY avgHealth DESC;";
    //  echo $companyFinancialsQuery;

    $resultcompanyFinancials = mysqli_query($conn, $companyFinancialsQuery);
    // Convert the table into individual rows and reformat.
    $companyFinancials = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultcompanyFinancials, MYSQLI_ASSOC)) {
        $companyFinancials[] = $row;
    }
      echo json_encode($companyFinancials);


    

   

$conn->close();
?>
