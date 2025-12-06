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
$tmp = explode('|', $tmp); //["start date"| "end date"]
// print_r($tmp);
$quer = explode('|', $quer); ////["Region Type", "Region Selected]
// print_r($quer);


//We will need to build the queries per user selection, but the added constraints will be the same accross all queries, so we will make additions rn
$groupByRegion = ""; //Group By
$whereState = "";
$whereStateEvents = "WHERE ((e.EventDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "') OR (e.EventRecoveryDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "') OR (e.EventDate < '" . $tmp[0] . "' AND e.EventRecoveryDate > '" . $tmp[1] . "'))";

    //Group By statement added per user input
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
                    $whereState = " AND l.CountryName = '" . $quer[1] . "'";
                    break;
                case "Continent":
                    $whereState = " AND l.ContinentName = '" . $quer[1] . "'";
                    break;
                default:
                $whereState = "";
            }
        }
        // print_r($whereState);

    //Disruption Events Impacting Companies
    $companyAffectedByEventSelect = "SELECT i.AffectedCompanyID,  c.CompanyName, i.ImpactLevel, e.EventID, e.EventDate, y.CategoryName, l.CountryName, l.ContinentName FROM ImpactsCompany i JOIN Company c ON i.AffectedCompanyID = c.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID JOIN DisruptionCategory y ON y.CategoryID = e.CategoryID JOIN Location l ON l.LocationID = c.LocationID";

    //Puting query together and generating result
    $companyAffectedByEventQuery = "{$companyAffectedByEventSelect} {$whereStateEvents}{$whereState} ORDER BY e.EventID;";
    //  echo $companyAffectedByEventQuery;

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
    $regionalOverviewQuery = "{$regionalOverviewSelect} {$whereStateEvents}{$whereState} {$groupByRegion};";
    //  echo $regionalOverviewQuery;

    $resultregionalOverview = mysqli_query($conn, $regionalOverviewQuery);
    // Convert the table into individual rows and reformat.
    $regionalOverview = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultregionalOverview, MYSQLI_ASSOC)) {
        $regionalOverview[] = $row;
    }
    //   echo json_encode($regionalOverview);

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


         //Making JSON Object
    $seniorDisruptionResults = [
        "companyAffectedByEvent" => $companyAffectedByEvent,
        "regionalOverview" => $regionalOverview
    ];

    echo json_encode($seniorDisruptionResults);
   

$conn->close();
?>
