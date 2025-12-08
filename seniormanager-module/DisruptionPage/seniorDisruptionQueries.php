<?php
$servername = "mydb.itap.purdue.edu";

$username = "g1151938";//yourCAREER/groupusername
$password = "Purdue28";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

$tmp = $_GET['q']; //['Start Date', 'End Date']
$quer = $_GET['g']; //['Region','What drop Down was Selected']
$info = $_GET['a']; //['Disruption or Company','ID or Name']
$num = $_GET['b']; //['Query Needed']

// Convert the comma-delimited string into an array of strings.
$tmp = explode('|', $tmp); //["start date"| "end date"]
// print_r($tmp);
$quer = explode('|', $quer); ////["Region Type", "Region Selected]
// print_r($quer);
$info = explode('|', $info); ////['Disruption or Company','ID or Name']
// print_r($info);
$num = explode('|', $num); ////['Query Needed']
// print_r($info);

//We will need to build the queries per user selection, but the added constraints will be the same accross all queries, so we will make additions rn
$groupByRegion = ""; //Group By
$whereState = "";
$whereStateEvents = "";
$whereStateTab2 = "";

    if (!empty($tmp[0])) { //NOT ALL TABS NEED THIS FILTER WHICH IS WHY IT IS OPTIONAL
        $whereStateEvents = "WHERE ((e.EventDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "') OR (e.EventRecoveryDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "') OR (e.EventDate < '" . $tmp[0] . "' AND e.EventRecoveryDate > '" . $tmp[1] . "'))";
    }
    //Group By statement added per user input USER HAS OPTION TO NOT SELECT THESE FILTERS
    if (!empty($quer[0])) { 
        switch ($quer[0]) {
            case "Country":
                $groupByRegion = "GROUP BY l.CountryName";
                break;
            case "Continent":
                $groupByRegion = "GROUP BY l.ContinentName";
                break;
            default:
            $groupByRegion = "";
        }
    }
    // print_r($groupByRegion);
    //WHERE statement added per user input
        if (!empty($quer[1])) { //Adding appropriate where if user input a specific region.
            switch ($quer[0]) {
                case "Country":
                    $whereState = " WHERE l.CountryName = '" . $quer[1] . "'";
                    break;
                case "Continent":
                    $whereState = " WHERE l.ContinentName = '" . $quer[1] . "'";
                    break;
                default:
                $whereState = "";
            }
        }
        // print_r($whereState);
        if (!empty($info[0])) { 
        switch ($info[0]) {
            case "Disruption":
                $whereStateTab2 = " WHERE e.EventID = '" . $info[1] . "'";
                break;
            case "Company":
                $whereStateTab2 = " WHERE c.CompanyName = '" . $info[1] . "'";
                break;
            default:
            $whereStateTab2 = "";
        }
    }
    // print_r($whereStateTab2);

    //Disruption Events Impacting Companies
    $companyAffectedByEventSelect = "SELECT i.AffectedCompanyID,  c.CompanyName, i.ImpactLevel, e.EventID, e.EventDate, y.CategoryName, l.CountryName, l.ContinentName FROM ImpactsCompany i JOIN Company c ON i.AffectedCompanyID = c.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID JOIN DisruptionCategory y ON y.CategoryID = e.CategoryID JOIN Location l ON l.LocationID = c.LocationID";

    //Puting query together and generating result
    $companyAffectedByEventQuery = "{$companyAffectedByEventSelect}{$whereState}{$whereStateTab2} ORDER BY e.EventID;";
     echo $companyAffectedByEventQuery;

    $resultcompanyAffectedByEvent = mysqli_query($conn, $companyAffectedByEventQuery);
    // Convert the table into individual rows and reformat.
    $companyAffectedByEvent = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultcompanyAffectedByEvent, MYSQLI_ASSOC)) {
        $companyAffectedByEvent[] = $row;
    }
    //   echo json_encode($companyAffectedByEvent);

    // Regional Disruption Overview
    $regionalOverviewSelect = "SELECT l.CountryName, l.ContinentName, COUNT(CASE WHEN i.ImpactLevel = 'High' THEN 0 ELSE 1 END) AS leftOverDisruption, COUNT(CASE WHEN i.ImpactLevel = 'High' THEN 1 ELSE 0 END) AS HighImpactCount FROM Company c JOIN ImpactsCompany i ON i.AffectedCompanyID = c.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID JOIN Location l ON l.LocationID = c.LocationID";

    //Puting query together and generating result
    $regionalOverviewQuery = "{$regionalOverviewSelect} {$whereState} {$groupByRegion};";
    //  echo $regionalOverviewQuery;

    $resultregionalOverview = mysqli_query($conn, $regionalOverviewQuery);
    // Convert the table into individual rows and reformat.
    $regionalOverview = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultregionalOverview, MYSQLI_ASSOC)) {
        $regionalOverview[] = $row;
    }
    //   echo json_encode($regionalOverview);

    // Disruption Frequency Over Time TIMMY HELPPPPPP
    $frequencySelect = "SELECT e.EventDate AS StartDate, COUNT(*) AS EventCount, ROUND(AVG(DATEDIFF(e.EventRecoveryDate, e.EventDate)), 2) AS avgDuration, ROUND(MAX(DATEDIFF(e.EventRecoveryDate, e.EventDate)), 2) AS maxDuration, ROUND(MIN(DATEDIFF(e.EventRecoveryDate, e.EventDate)), 2) AS minDuration  FROM DisruptionEvent e";

    //Puting query together and generating result
    $frequencyQuery = "{$frequencySelect} {$whereStateEvents} GROUP BY e.EventDate;";
    //  echo $frequencyQuery;

    $resultfrequency = mysqli_query($conn, $frequencyQuery);
    // Convert the table into individual rows and reformat.
    $frequency = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultfrequency, MYSQLI_ASSOC)) {
        $frequency[] = $row;
    }
    //   echo json_encode($frequency);

    //Criticality Query
    $companyQuery = "SELECT c.CompanyID, c.CompanyName FROM Company c;";
    //  echo $companyNameQuery;
    //Execute the SQL query
    $resultcompany = mysqli_query($conn, $companyQuery);
    // Convert the table into individual rows and reformat.
    $company = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultcompany, MYSQLI_ASSOC)) {
    $company[] = $row;
    }
    // echo json_encode($company);
    $length = count($company);
    // echo $length;
    $final_results = []; //Array of results
    for ($i = 0; $i< $length; $i++){
        $my_i_result = []; //resetting array
        $companyID = $company[$i]['CompanyID'];
    $query_statement = "SELECT c.CompanyName, COUNT( DISTINCT d.DownstreamCompanyID) * (SELECT COUNT(*) FROM Company x 
        JOIN ImpactsCompany i ON i.AffectedCompanyID = x.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID WHERE x.CompanyID = '{$companyID}' AND i.ImpactLevel = 'High') AS Criticality
        FROM Company c JOIN DependsOn d ON d.UpstreamCompanyID = c.CompanyID WHERE c.CompanyID = '{$companyID}';";
    $result = mysqli_query($conn, $query_statement);
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $my_i_result[] = $row;
        }
    $final_results[$i] = [
        "CompanyID" => $companyID,
        "CompanyName" => $company[$i]['CompanyName'],
        "Criticality" => $my_i_result[0]['Criticality']
    ];
    }
    // echo $my_i_result[0];
    // for ($j=0; $j < count($final_results); $j++){
    //     echo "'{$final_results[$j]}'";
    // }
    // echo json_encode($final_results);


         //Making JSON Object
    $seniorDisruptionResults = [
        "companyAffectedByEvent" => $companyAffectedByEvent,
        "regionalOverview" => $regionalOverview,
        "frequency" => $frequency,
        "criticality" => $final_results
    ];

    echo json_encode($seniorDisruptionResults);
   

$conn->close();
?>
