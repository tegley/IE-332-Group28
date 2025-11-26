<?php
$servername = "mydb.itap.purdue.edu";

$username = "tegley";//yourCAREER/groupusername
$password = "#TurboCoder6412!!";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

//Always - filter_option[1] = start_date, filter_option[2] = end_date 
//Option 1: filter_option[0] = country; user_input[0] = country_input; 
//Option 2: filter_option[0] = continent; user_input[0] = continent_input; 
//Option 3: filter_option[0] = company; user_input[0] = company_input; 
//Option 4: filter_option[0] = tier; user_input[0] = tier_input
//Option 5: filter_option[0] = country-tier; user_input[0] = country_input, user_input[1] = tier_input
//Option 6: filter_option[0] = continent-tier; user_input[0] = continent_input, user_input[1] = tier_input

//Temporary variables
$user_input = $_GET['q']; //Input from options 1-6
$filter_option = $_GET['g']; //Selected filter option, start date, end date

$user_input = explode(',', $user_input);
$filter_option = explode(',', $filter_option);
//echo "{$filter_option[0]}";
//echo "{$user_input[0]}";

//Select statements
$DF_charts_query = "SELECT c.CompanyName, COUNT(*) As Total";

$HDR_charts_query = "SELECT c.CompanyName, ";

$DSD_charts_query = "SELECT c.CompanyName, SUM(CASE WHEN i.ImpactLevel = 'High' THEN 1 ELSE 0 END) AS NumHighImpact, 
SUM(CASE WHEN i.ImpactLevel = 'Medium' THEN 1 ELSE 0 END) AS NumMedImpact, 
SUM(CASE WHEN i.ImpactLevel = 'Low' THEN 1 ELSE 0 END) AS NumLowImpact";

//Common FROM statement
$from = "FROM Company c JOIN ImpactsCompany i ON i.AffectedCompanyID = c.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID JOIN Location l ON l.LocationID = c.LocationID";

//Common WHERE statement 
$where = "WHERE ((e.EventDate BETWEEN '{$filter_option[1]}' AND '{$filter_option[2]}') OR (e.EventRecoveryDate BETWEEN '{$filter_option[1]}' AND '{$filter_option[2]}') OR (e.EventDate < '{$filter_option[1]}' AND e.EventRecoveryDate > '{$filter_option[2]}'))";

//Option 1 - Country
if($filter_option[0] == 'country'){
    //Check if country exists
    $checksql = "SELECT DISTINCT(l.LocationID) FROM Company c JOIN Location l WHERE c.LocationID = l.LocationId AND l.CountryName = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);

    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Country Not Found";
        $conn->close();
    }
    //Country GROUP BY and HAVING statements
    $group_by = "GROUP BY c.CompanyName, l.CountryName";
    $having = "HAVING l.CountryName = '{$user_input[0]}'";
    
    //Add HDR subquery
    $HDR_charts_query .= "( SUM(CASE WHEN i.ImpactLevel = 'High' THEN 1 ELSE 0 END) * 1.0 / (SELECT SUM(CASE WHEN i2.ImpactLevel = 'High' THEN 1 ELSE 0 END) 
    FROM Company c2 JOIN ImpactsCompany i2 ON i2.AffectedCompanyID = c2.CompanyID JOIN DisruptionEvent e2 ON e2.EventID = i2.EventID JOIN Location l2 ON l2.LocationID = c2.LocationID 
    WHERE ((e2.EventDate BETWEEN '{$filter_option[1]}' AND '{$filter_option[2]}') OR (e2.EventRecoveryDate BETWEEN '{$filter_option[1]}' AND '{$filter_option[2]}') 
    OR (e2.EventDate < '{$filter_option[1]}' AND e2.EventRecoveryDate > '{$filter_option[2]}')) AND l2.CountryName = '{$user_input[0]}' ) ) AS HDR";}

//Option 2 - Continent
if($filter_option[0] == 'continent'){
    $checksql = "SELECT DISTINCT(l.LocationID) FROM Company c JOIN Location l WHERE c.LocationID = l.LocationId AND l.ContinentName = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);

    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error in continent selection";
        $conn->close();
    }

    //Continent HAVING statement
    $group_by = "GROUP BY c.CompanyName, l.ContinentName";
    $having = "HAVING l.ContinentName = '{$user_input[0]}'";
    
    //Add HDR subquery
    $HDR_charts_query .= "( SUM(CASE WHEN i.ImpactLevel = 'High' THEN 1 ELSE 0 END) * 1.0 / (SELECT SUM(CASE WHEN i2.ImpactLevel = 'High' THEN 1 ELSE 0 END) 
    FROM Company c2 JOIN ImpactsCompany i2 ON i2.AffectedCompanyID = c2.CompanyID JOIN DisruptionEvent e2 ON e2.EventID = i2.EventID JOIN Location l2 ON l2.LocationID = c2.LocationID 
    WHERE ((e2.EventDate BETWEEN '{$filter_option[1]}' AND '{$filter_option[2]}') OR (e2.EventRecoveryDate BETWEEN '{$filter_option[1]}' AND '{$filter_option[2]}') 
    OR (e2.EventDate < '{$filter_option[1]}' AND e2.EventRecoveryDate > '{$filter_option[2]}')) AND l2.ContinentName = '{$user_input[0]}' ) ) AS HDR";
}

//Option 3 - Company
if($filter_option[0] == 'company'){
    $checksql = "SELECT c.CompanyID FROM Company c WHERE c.CompanyName = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);

    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Company Not Found";
        $conn->close();
    }

    //Company HAVING statement
    $group_by = "GROUP BY c.CompanyName";
    $having = "HAVING c.CompanyName = '{$user_input[0]}'";
    
    //Add HDR value calculation
    $HDR_charts_query .= "SUM(CASE WHEN i.ImpactLevel = 'High' THEN 1 ELSE 0 END) AS HDR";
}

//Option 4 - Tier
if($filter_option[0] == 'tier'){
    $checksql = "SELECT c.TierLevel FROM Company c WHERE c.TierLevel = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);

    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error in tier selection";
        $conn->close();
    }

    //Tier HAVING statement
    $group_by = "GROUP BY c.CompanyName, c.TierLevel";
    $having = "HAVING c.TierLevel = '{$user_input[0]}'";
    
    //Add HDR subquery
    $HDR_charts_query .= "( SUM(CASE WHEN i.ImpactLevel = 'High' THEN 1 ELSE 0 END) * 1.0 / (SELECT SUM(CASE WHEN i2.ImpactLevel = 'High' THEN 1 ELSE 0 END) 
    FROM Company c2 JOIN ImpactsCompany i2 ON i2.AffectedCompanyID = c2.CompanyID JOIN DisruptionEvent e2 ON e2.EventID = i2.EventID JOIN Location l2 ON l2.LocationID = c2.LocationID 
    WHERE ((e2.EventDate BETWEEN '{$filter_option[1]}' AND '{$filter_option[2]}') OR (e2.EventRecoveryDate BETWEEN '{$filter_option[1]}' AND '{$filter_option[2]}') 
    OR (e2.EventDate < '{$filter_option[1]}' AND e2.EventRecoveryDate > '{$filter_option[2]}')) AND c2.TierLevel = '{$user_input[0]}' ) ) AS HDR";
}

//Build queries
$DF_charts_query .= " {$from} {$where} {$group_by} {$having}";
$DSD_charts_query .= " {$from} {$where} {$group_by} {$having}";
$HDR_charts_query .= " {$from} {$where} {$group_by} {$having}";
//echo "\n {$HDR_charts_query} \n";

//Run all queries
$DF_charts_query_result = mysqli_query($conn, $DF_charts_query);
while ($row = mysqli_fetch_array($DF_charts_query_result, MYSQLI_ASSOC)) {
    $DF_charts[] = $row;
}

$DSD_charts_query_result = mysqli_query($conn, $DSD_charts_query);
while ($row = mysqli_fetch_array($DSD_charts_query_result, MYSQLI_ASSOC)) {
    $DSD_charts[] = $row;
}

$HDR_charts_query_result = mysqli_query($conn, $HDR_charts_query);
while ($row = mysqli_fetch_array($HDR_charts_query_result, MYSQLI_ASSOC)) {
    $HDR_charts[] = $row;
}

//Create and encode JSON object
$SCMDisruptionEventResults = [
    "DF" => $DF_charts,
    "DSD" => $DSD_charts,
    "HDR" => $HDR_charts
];

echo json_encode($SCMDisruptionEventResults);
?>