<?php
$servername = "mydb.itap.purdue.edu";

$username = "g1151938";//yourCAREER/groupusername
$password = "Purdue28";//yourgrouppassword
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

$user_input = explode('|', $user_input);
$filter_option = explode('|', $filter_option);

//Common FROM statement
$from = "FROM Company c JOIN ImpactsCompany i ON i.AffectedCompanyID = c.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID JOIN Location l ON l.LocationID = c.LocationID";

//Common WHERE statement
$where = "WHERE ((e.EventDate BETWEEN '{$filter_option[1]}' AND '{$filter_option[2]}') OR (e.EventRecoveryDate BETWEEN '{$filter_option[1]}' AND '{$filter_option[2]}') OR (e.EventDate < '{$filter_option[1]}' AND e.EventRecoveryDate > '{$filter_option[2]}'))";
$where_2 = "WHERE ((e2.EventDate BETWEEN '{$filter_option[1]}' AND '{$filter_option[2]}') OR (e2.EventRecoveryDate BETWEEN '{$filter_option[1]}' AND '{$filter_option[2]}') OR (e2.EventDate < '{$filter_option[1]}' AND e2.EventRecoveryDate > '{$filter_option[2]}'))";

//Chart data - SELECT statements
$DF_chart_query = "SELECT c.CompanyName, (COUNT(*) / TIMESTAMPDIFF(MONTH, '{$filter_option[1]}', '{$filter_option[2]}')) As DF";

$HDR_chart_query = "SELECT c.CompanyName, SUM(CASE WHEN i.ImpactLevel = 'High' THEN 1 ELSE 0 END) AS NumHighImpact";

$DSD_chart_query = "SELECT c.CompanyName, SUM(CASE WHEN i.ImpactLevel = 'High' THEN 1 ELSE 0 END) AS NumHighImpact, 
SUM(CASE WHEN i.ImpactLevel = 'Medium' THEN 1 ELSE 0 END) AS NumMedImpact, 
SUM(CASE WHEN i.ImpactLevel = 'Low' THEN 1 ELSE 0 END) AS NumLowImpact";

$TD_ART_chart_query = "SELECT DATEDIFF(e.EventRecoveryDate, e.EventDate) AS Downtime";

//Overall ART & TD statistics - SELECT statements
$TD_statistic_query = "SELECT SUM(MyTable.Downtime) AS TD FROM (SELECT DISTINCT(e.EventID), DATEDIFF(e.EventRecoveryDate, e.EventDate) AS Downtime"; 
$ART_statistic_query = "SELECT SUM(MyTable.Downtime) * 1.0 / COUNT(*) AS ART FROM (SELECT DISTINCT(e.EventID), DATEDIFF(e.EventRecoveryDate, e.EventDate) AS Downtime"; 

$RRC_setup = "COUNT(DISTINCT(e.EventID)) / (SELECT COUNT(DISTINCT(e2.EventID)) FROM DisruptionEvent e2 {$where_2}) AS RRC
FROM DisruptionEvent e JOIN DisruptionCategory x ON e.CategoryID = x.CategoryID JOIN ImpactsCompany i ON e.EventID = i.EventID JOIN Company c ON i.AffectedCompanyID = c.CompanyID JOIN Location l ON l.LocationID = c.LocationID
{$where}";

//Additional HDR queries
$HDR_overall_query_setup = "(SELECT SUM(CASE WHEN i2.ImpactLevel = 'High' THEN 1 ELSE 0 END) FROM Company c2 JOIN ImpactsCompany i2 ON i2.AffectedCompanyID = c2.CompanyID JOIN DisruptionEvent e2 ON e2.EventID = i2.EventID JOIN Location l2 ON l2.LocationID = c2.LocationID";

$HDR_companies_query = "SELECT c.CompanyName AS HDRCompanyName, (SELECT SUM(CASE WHEN i2.ImpactLevel = 'High' THEN 1 ELSE 0 END) 
                        FROM Company c2 JOIN ImpactsCompany i2 ON i2.AffectedCompanyID = c2.CompanyID JOIN DisruptionEvent e2 ON e2.EventID = i2.EventID JOIN Location l2 ON l2.LocationID = c2.LocationID 
                        {$where_2} AND c2.CompanyName = HDRCompanyName GROUP BY c2.CompanyName) * 100 / COUNT(e.EventID) AS HDR";

//Option 1 - Country
if($filter_option[0] == 'country'){
    //Check if the selected country is in the database
    $checksql = "SELECT l.LocationID FROM Location l WHERE l.CountryName = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);
    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error country";
        $conn->close();
        exit();
    }

    //Check if any companies reside in the selected country
    $checksql = "SELECT c.CompanyID FROM Company c JOIN Location l ON c.LocationID = l.LocationID WHERE c.LocationID = l.LocationId AND l.CountryName = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);
    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error company country";
        $conn->close();
        exit();
    }
    
    //Country - GROUP BY and HAVING statements
    $group_by = "GROUP BY c.CompanyName, l.CountryName";
    $having = "HAVING l.CountryName = '{$user_input[0]}'";

    //Country - overall ART & TD statistic statement
    $statistic_statement = " {$where} AND l.CountryName = '{$user_input[0]}'";

    //Country - HDR for companies
    $HDR_companies_where = "AND l.CountryName = '{$user_input[0]}'";

    //Country - HDR overall
    $HDR_overall_select = "SELECT l.CountryName As HDROverall,";
    $HDR_overall_subquery = "GROUP BY l2.CountryName HAVING l2.CountryName = HDROverall) * 100 / COUNT(e.EventID) AS HDRStatistic";
    $HDR_overall_endgroupby = "GROUP BY l.CountryName HAVING l.CountryName = '{$user_input[0]}'";

    //Country - RRC query modifications
    $RRC_select = "SELECT l.CountryName AS Region,";
    $RRC_group_by = "GROUP BY l.CountryName HAVING l.CountryName = '{$user_input[0]}'";
}

//Option 2 - Continent
if($filter_option[0] == 'continent'){
    //Check if the selected continent is in the database
    $checksql = "SELECT l.LocationID FROM Location l WHERE l.ContinentName = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);
    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error continent";
        $conn->close();
        exit();
    }

    //Check if any companies reside in the selected continent
    $checksql = "SELECT c.CompanyID FROM Company c JOIN Location l ON c.LocationID = l.LocationID WHERE c.LocationID = l.LocationId AND l.ContinentName = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);
    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error company continent";
        $conn->close();
        exit();
    }

    //Continent - GROUP BY and HAVING statement
    $group_by = "GROUP BY c.CompanyName, l.ContinentName";
    $having = "HAVING l.ContinentName = '{$user_input[0]}'";

    //Continent - overall ART & TD statistic statement
    $statistic_statement = " {$where} AND l.ContinentName = '{$user_input[0]}'";

    //Contient - HDR for companies
    $HDR_companies_where = "AND l.ContinentName = '{$user_input[0]}'";

    //Continent - HDR overall
    $HDR_overall_select = "SELECT l.ContinentName As HDROverall,";
    $HDR_overall_subquery = "GROUP BY l2.ContinentName HAVING l2.ContinentName = HDROverall) * 100 / COUNT(e.EventID) AS HDRStatistic";
    $HDR_overall_endgroupby = "GROUP BY l.ContinentName HAVING l.ContinentName = '{$user_input[0]}'";

    //Continent - RRC query modifications
    $RRC_select = "SELECT l.ContinentName AS Region,";
    $RRC_group_by = "GROUP BY l.ContinentName HAVING l.ContinentName = '{$user_input[0]}'";
}

//Option 3 - Company
if($filter_option[0] == 'company'){
    //Check if specified company exists in the database
    $checksql = "SELECT c.CompanyID FROM Company c WHERE c.CompanyName = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);
    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error company";
        $conn->close();
        exit();
    }
    
    //Company - GROUP BY and HAVING statements
    $group_by = "GROUP BY c.CompanyName";
    $having = "HAVING c.CompanyName = '{$user_input[0]}'";

    //Company - HDR of the company
    $HDR_companies_where = "AND c.CompanyName = '{$user_input[0]}'";

    //Company - HDR overall
    $HDR_overall_select = "SELECT c.CompanyName As HDROverall,";
    $HDR_overall_subquery = "GROUP BY c2.CompanyName HAVING c2.CompanyName = HDROverall) * 100 / COUNT(e.EventID) AS HDRStatistic";
    $HDR_overall_endgroupby = "GROUP BY c.CompanyName HAVING c.CompanyName = '{$user_input[0]}'";

    //Company - overall ART & TD statistic statement
    $statistic_statement = " {$where} AND c.CompanyName = '{$user_input[0]}'";
}

//Option 4 - Tier
if($filter_option[0] == 'tier'){
    //Check if there are companies in the database at the specified tier
    $checksql = "SELECT DISTINCT(c.TierLevel) FROM Company c WHERE c.TierLevel = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);
    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error tier";
        $conn->close();
        exit();
    }

    //Tier - GROUP BY and HAVING statements
    $group_by = "GROUP BY c.CompanyName, c.TierLevel";
    $having = "HAVING c.TierLevel = '{$user_input[0]}'";

    //Tier - HDR for companies
    $HDR_companies_where = "AND c.TierLevel = '{$user_input[0]}'";

    //Tier - HDR overall
    $HDR_overall_select = "SELECT c.TierLevel As HDROverall,";
    $HDR_overall_subquery = "GROUP BY c2.TierLevel HAVING c2.TierLevel = HDROverall) * 100 / COUNT(e.EventID) AS HDRStatistic";
    $HDR_overall_endgroupby = "GROUP BY c.TierLevel HAVING c.TierLevel = '{$user_input[0]}'";

    //Tier - overall ART & TD statistic statement
    $statistic_statement = " {$where} AND c.TierLevel = '{$user_input[0]}'";
}

//Option 5 - Country & tier
if($filter_option[0] == 'country-tier'){
    //Check if the selected country is in the database
    $checksql = "SELECT l.LocationID FROM Location l WHERE l.CountryName = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);
    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error country";
        $conn->close();
        exit();
    }

    //Check if there are companies in the database at the specified tier
    $checksql = "SELECT DISTINCT(c.TierLevel) FROM Company c WHERE c.TierLevel = '{$user_input[1]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);
    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error tier";
        $conn->close();
        exit();
    }

    //Check if there are any companies in the database at the specified tier that reside in the specified country
    $checksql = "SELECT DISTINCT(c.TierLevel) FROM Company c JOIN Location l ON c.LocationID = l.LocationID WHERE c.TierLevel = '{$user_input[1]}' AND l.CountryName = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);
    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error country tier";
        $conn->close();
        exit();
    }

    //Country & tier - GROUP BY and HAVING statements
    $group_by = "GROUP BY c.CompanyName, c.TierLevel, l.CountryName";
    $having = "HAVING c.TierLevel = '{$user_input[1]}' AND l.CountryName = '{$user_input[0]}'";

    //Country & tier - HDR for companies
    $HDR_companies_where = "AND c.TierLevel = '{$user_input[1]}' AND l.CountryName = '{$user_input[0]}'";

    //Country & tier - HDR overall
    $HDR_overall_select = "SELECT c.TierLevel As HDROverall,";
    $HDR_overall_subquery = "GROUP BY c2.TierLevel, l2.CountryName HAVING c2.TierLevel = '{$user_input[1]}' AND l2.CountryName = '{$user_input[0]}') * 100 / COUNT(e.EventID) AS HDRStatistic";
    $HDR_overall_endgroupby = "GROUP BY c.TierLevel, l.CountryName HAVING c.TierLevel = '{$user_input[1]}' AND l.CountryName = '{$user_input[0]}'";

    //Country & tier - overall ART & TD statistic statement
    $statistic_statement = " {$where} AND c.TierLevel = '{$user_input[1]}' AND l.CountryName = '{$user_input[0]}'";
}

//Option 6 - Continent & tier
if($filter_option[0] == 'continent-tier'){
    //Check if the selected continent is in the database
    $checksql = "SELECT l.LocationID FROM Location l WHERE l.ContinentName = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);
    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error continent";
        $conn->close();
        exit();
    }

    //Check if there are companies in the database at the specified tier
    $checksql = "SELECT DISTINCT(c.TierLevel) FROM Company c WHERE c.TierLevel = '{$user_input[1]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);
    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error tier";
        $conn->close();
        exit();
    }

    //Check if there are any companies in the database at the specified tier that reside in the specified continent
    $checksql = "SELECT DISTINCT(c.TierLevel) FROM Company c JOIN Location l ON c.LocationID = l.LocationID WHERE c.TierLevel = '{$user_input[1]}' AND l.ContinentName = '{$user_input[0]}';";
    $checksql_result = mysqli_query($conn, $checksql);
    $checksql_result = mysqli_fetch_row($checksql_result);
    if (isset($checksql_result[0]) == false || $checksql_result[0] == "") {
        echo "Error continent tier";
        $conn->close();
        exit();
    }

    //Continent & tier - GROUP BY and HAVING statements
    $group_by = "GROUP BY c.CompanyName, c.TierLevel, l.ContinentName";
    $having = "HAVING c.TierLevel = '{$user_input[1]}' AND l.ContinentName = '{$user_input[0]}'";

    //Country & tier - HDR overall
    $HDR_overall_select = "SELECT c.TierLevel As HDROverall,";
    $HDR_overall_subquery = "GROUP BY c2.TierLevel, l2.ContinentName HAVING c2.TierLevel = '{$user_input[1]}' AND l2.ContinentName = '{$user_input[0]}') * 100 / COUNT(e.EventID) AS HDRStatistic";
    $HDR_overall_endgroupby = "GROUP BY c.TierLevel, l.ContinentName HAVING c.TierLevel = '{$user_input[1]}' AND l.ContinentName = '{$user_input[0]}'";

    //Contient & tier - HDR for companies
    $HDR_companies_where = "AND c.TierLevel = '{$user_input[1]}' AND l.ContinentName = '{$user_input[0]}'";

    //Continent & tier - overall ART & TD statistic statement
    $statistic_statement = " {$where} AND c.TierLevel = '{$user_input[1]}' AND l.ContinentName = '{$user_input[0]}'";
}


//Build chart queries
$DF_chart_query .= " {$from} {$where} {$group_by} {$having}";
$DSD_chart_query .= " {$from} {$where} {$group_by} {$having}";
$HDR_chart_query .= " {$from} {$where} {$group_by} {$having}";

//Modify GROUP BY statement for TD/ART chart query
switch($filter_option[0]){
    case "country": 
        $group_by = "GROUP BY l.CountryName, e.EventID";
        break;
    case "continent": 
        $group_by = "GROUP BY l.ContinentName, e.EventID";
        break;
    case "company": 
        $group_by = "GROUP BY c.CompanyName, e.EventID";
        break;
    case "tier": 
        $group_by = "GROUP BY c.TierLevel, e.EventID";
        break;
    case "country-tier":
        $group_by = "GROUP BY c.TierLevel, l.CountryName, e.EventID";
        break;
    case "continent-tier":
        $group_by = "GROUP BY c.TierLevel, l.ContinentName, e.EventID";
        break;
}

$TD_ART_chart_query .= " {$from} {$where} {$group_by} {$having}";

//Build overall statistic queries
$TD_statistic_query .= " {$from} {$statistic_statement}) As MyTable";
$ART_statistic_query .= " {$from} {$statistic_statement}) As MyTable";

//Build RRC query
$RRC_query = "{$RRC_select} {$RRC_setup} {$RRC_group_by}";

//Build HDR by companies query
$HDR_companies_query .= " {$from} {$where} {$HDR_companies_where} GROUP BY c.CompanyName";

//Build HDR overall query
$HDR_overall_query = "{$HDR_overall_select} {$HDR_overall_query_setup} {$where_2} {$HDR_overall_subquery} {$from} {$where} {$HDR_overall_endgroupby}";

//Run all queries
$DF_chart_query_result = mysqli_query($conn, $DF_chart_query);
while ($row = mysqli_fetch_array($DF_chart_query_result, MYSQLI_ASSOC)) {
    $DF_chart[] = $row;
}

$DSD_chart_query_result = mysqli_query($conn, $DSD_chart_query);
while ($row = mysqli_fetch_array($DSD_chart_query_result, MYSQLI_ASSOC)) {
    $DSD_chart[] = $row;
}

$HDR_chart_query_result = mysqli_query($conn, $HDR_chart_query);
while ($row = mysqli_fetch_array($HDR_chart_query_result, MYSQLI_ASSOC)) {
    $HDR_chart[] = $row;
}

$HDR_by_companies_result = mysqli_query($conn, $HDR_companies_query);
while ($row = mysqli_fetch_array($HDR_by_companies_result, MYSQLI_ASSOC)) {
    $HDR_companies[] = $row;
}

$HDR_overall_result = mysqli_query($conn, $HDR_overall_query);
while ($row = mysqli_fetch_array($HDR_overall_result, MYSQLI_ASSOC)) {
    $HDR_overall[] = $row;
}

$TD_ART_chart_query_result = mysqli_query($conn, $TD_ART_chart_query);
while ($row = mysqli_fetch_array($TD_ART_chart_query_result, MYSQLI_ASSOC)) {
    $TD_ART_chart[] = $row;
}

$TD_statistic_query_result = mysqli_query($conn, $TD_statistic_query);
while ($row = mysqli_fetch_array($TD_statistic_query_result, MYSQLI_ASSOC)) {
    $TD_statistic[] = $row;
}

$ART_statistic_query_result = mysqli_query($conn, $ART_statistic_query);
while ($row = mysqli_fetch_array($ART_statistic_query_result, MYSQLI_ASSOC)) {
    $ART_statistic[] = $row;
}

$RRC_query_result = mysqli_query($conn, $RRC_query);
while ($row = mysqli_fetch_array($RRC_query_result, MYSQLI_ASSOC)) {
    $RRC_chart[] = $row;
}

//Create and encode JSON object
$SCMDisruptionEventResults = [
    "DF_chart" => $DF_chart,
    "DSD_chart" => $DSD_chart,
    "HDR_chart" => $HDR_chart,
    "HDR_companies" => $HDR_companies,
    "HDR_overall" => $HDR_overall,
    "TD_ART_chart" => $TD_ART_chart,
    "TD_overall" => $TD_statistic,
    "ART_overall" => $ART_statistic,
    "RRC_chart" => $RRC_chart
];

echo json_encode($SCMDisruptionEventResults);
?>
