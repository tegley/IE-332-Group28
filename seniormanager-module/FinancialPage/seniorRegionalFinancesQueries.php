<?php
$servername = "mydb.itap.purdue.edu";

$username = "cox447";//yourCAREER/groupusername
$password = "LunaZuna704";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}


$quer = $_GET['q']; //User specified filters [Region , Specific Region]

// Convert the comma-delimited string into an array of strings.
$quer = explode('|', $quer); ////["Region Type"]
// print_r($quer);


//We will need to build the queries per user selection, but the added constraints will be the same accross all queries, so we will make additions rn
$orderState = "";
$whereRegionState = "";


    //HAVING statement added per user input
    if (!empty($quer[0])) { //Adding ORDERING SO THAT USER CAN SEE COMPANIES WITHIN A REGION
        switch ($quer[0]) {
            case "Country":
                $orderState = "l.CountryName, ";
                break;
            case "Continent":
                $orderState = "l.ContinentName, ";
                break;
            default:
            $orderState = "";
        }
        if (!empty($quer[1])) { //Adding WHERE SO THAT USER CAN SEE COMPANIES WITHIN A REGION
            switch ($quer[0]) {
                case "Country":
                    $whereRegionState = " WHERE l.CountryName = '" . $quer[1] . "'";
                    break;
                case "Continent":
                    $whereRegionState = " WHERE l.ContinentName = '" . $quer[1] . "'";
                    break;
                default:
                $whereRegionState = "";
            }
    }
    }
    // print_r($whereRegionState);

    //Finanical Health by company
    $companyFinancialsSelect = "SELECT ROUND(AVG(f.HealthScore), 2) AS avgHealth, c.CompanyName, l.CountryName, l.ContinentName FROM Company c JOIN FinancialReport f ON c.CompanyID = f.CompanyID LEFT JOIN Location l ON l.LocationID = c.LocationID";

    //Puting query together and generating result
    $companyFinancialsQuery = "{$companyFinancialsSelect} {$whereRegionState} GROUP BY c.CompanyName ORDER BY {$orderState}avgHealth DESC;";
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
