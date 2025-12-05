<?php
$servername = "mydb.itap.purdue.edu";

$username = "cox447";//yourCAREER/groupusername
$password = "LunaZuna704";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

$tmp = $_GET['q'];
$quer = $_GET['g']; //['Region','What drop Down was Selected']

// Convert the comma-delimited string into an array of strings.
$tmp = explode(',', $tmp); //["start year", "initial quarter", "end year", "last quarter"]
// print_r($tmp);
$quer = explode('|', $quer); ////["Region Type"]
// print_r($quer);


//We will need to build the queries per user selection, but the added constraints will be the same accross all queries, so we will make additions rn
$groupByRegion = ""; //Group By
$whereFilterState = "";
$whereStateEvents = "WHERE ((e.EventDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "') OR (e.EventRecoveryDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "') OR (e.EventDate < '" . $tmp[0] . "' AND e.EventRecoveryDate > '" . $tmp[1] . "'))";

    //HAVING statement added per user input
    if (!empty($quer[0])) { //Adding ORDERING SO THAT USER CAN SEE COMPANIES WITHIN A REGION
        switch ($quer[0]) {
            case "country":
                $groupByRegion = "GROUP BY l.CountryName";
                break;
            case "continent":
                $groupByRegion = "GROUP BY l.ContinentName";
                break;
            default:
            $groupByRegion = "";
        }
    }
    // print_r($groupByRegion);

    //Disruption Events Impacting Companies
    $companyAffectedByEventSelect = "SELECT i.AffectedCompanyID,  c.CompanyName, i.ImpactLevel, e.EventID, y.CategoryName FROM ImpactsCompany i JOIN Company c ON i.AffectedCompanyID = c.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID JOIN DisruptionCategory y ON y.CategoryID = e.CategoryID";

    //Puting query together and generating result
    $companyAffectedByEventQuery = "{$companyAffectedByEventSelect} {$whereStateEvents} ORDER BY e.EventID;";
    //  echo $companyAffectedByEventQuery;

    $resultcompanyAffectedByEvent = mysqli_query($conn, $companyAffectedByEventQuery);
    // Convert the table into individual rows and reformat.
    $companyAffectedByEvent = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultcompanyAffectedByEvent, MYSQLI_ASSOC)) {
        $companyAffectedByEvent[] = $row;
    }
      echo json_encode($companyAffectedByEvent);

    // Regional Disruption Overview
    $regionalOverviewSelect = "SELECT COUNT(e.EventID) AS TotalRegionalDisruption, COUNT(CASE WHEN i.ImpactLevel = 'High' THEN 1 ELSE 0 END) AS HighImpactCount FROM Company c JOIN ImpactsCompany i ON i.AffectedCompanyID = c.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID JOIN Location l ON l.LocationID = c.LocationID";

    //Puting query together and generating result
    $regionalOverviewQuery = "{$regionalOverviewSelect} {$whereStateEvents} {$groupByRegion};";
     echo $regionalOverviewQuery;

    $resultregionalOverview = mysqli_query($conn, $regionalOverviewQuery);
    // Convert the table into individual rows and reformat.
    $regionalOverview = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultregionalOverview, MYSQLI_ASSOC)) {
        $regionalOverview[] = $row;
    }
      echo json_encode($regionalOverview);

    // Disruption Frequency Over Time TIMMY HELPPPPPP
    // $regionalOverviewSelect = "SELECT L.ContinentName AS Continent,  Count(I. EventID) AS TotalRegionalDisruption, COUNT(CASE WHEN I.ImpactLevel = ‘High’ THEN 1 END) AS HighImpactCount FROM Company C JOIN ImpactsCompany I ON I.AffectedCompanyID = C.CompanyID JOIN Location L ON L. LocationID = C. LocationID";

    // //Puting query together and generating result
    // $regionalOverviewQuery = "{$regionalOverviewSelect} {$whereStateEvents};";
    //  echo $regionalOverviewQuery;

    // $resultregionalOverview = mysqli_query($conn, $regionalOverviewQuery);
    // // Convert the table into individual rows and reformat.
    // $regionalOverview = []; //Creating shipping Array
    // while ($row = mysqli_fetch_array($resultregionalOverview, MYSQLI_ASSOC)) {
    //     $regionalOverview[] = $row;
    // }
    //   echo json_encode($regionalOverview);



   

$conn->close();
?>
