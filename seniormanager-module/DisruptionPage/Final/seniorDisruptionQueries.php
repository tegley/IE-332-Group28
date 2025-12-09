<?php
$servername = "mydb.itap.purdue.edu";

$username = "g1151938";//yourCAREER/groupusername
$password = "Purdue28";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

$date = $_GET['q'];
$quer = $_GET['g']; //['Region','What drop Down was Selected']

// Convert the comma-delimited string into an array of strings.
$date = explode('|', $date); //["start date"| "end date"]
$quer = explode('|', $quer); ////["Region Type", "Region Selected]

//Disruption frequency chart
if($date[0] !== 'Search'){
    //Date handling - convert month inputs to yyyy-mm-dd format
    $tmp = ['Date 1', 'Date 2'];
    //Starting month -> yyyy-mm-01
    $start_date_object = DateTime::createFromFormat('Y-m', $date[0]);
    $start_date = $start_date_object->format('Y-m-d');

    //Ending month -> yyyy-mm-last day of corresponding month
    $end_date_object = DateTime::createFromFormat('Y-m', $date[1]);
    $end_date = $end_date_object->format('Y-m-t');

    //Assigning values to tmp
    $tmp[0] = $start_date;
    $tmp[1] = $end_date;
    $whereStateEvents = "WHERE ((e.EventDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "') OR (e.EventRecoveryDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "') OR (e.EventDate < '" . $tmp[0] . "' AND e.EventRecoveryDate > '" . $tmp[1] . "'))";

    // Disruption Frequency Over Time - Calculations for outlining time frequency chart 
    $frequencySelect_average_max = "SELECT ROUND(AVG(DATEDIFF(e.EventRecoveryDate, e.EventDate)), 2) AS avgDuration, ROUND(MAX(DATEDIFF(e.EventRecoveryDate, e.EventDate)), 2) AS maxDuration, ROUND(MIN(DATEDIFF(e.EventRecoveryDate, e.EventDate)), 2) AS minDuration  FROM DisruptionEvent e";

    //Puting query together and generating result
    $frequencyQuery_1 = "{$frequencySelect_average_max} {$whereStateEvents} GROUP BY DATE_FORMAT(e.EventDate, '%Y-%m');";
    //  echo $frequencyQuery;

    $resultfrequency_1 = mysqli_query($conn, $frequencyQuery_1);
    // Convert the table into individual rows and reformat.
    while ($row = mysqli_fetch_array($resultfrequency_1, MYSQLI_ASSOC)) {
        $frequency_1[] = $row;
    }

    // Disruption Frequency Over Time - Counts by month
    $frequencySelect_counts_by_month = "SELECT DATE_FORMAT(e.EventDate, '%Y-%m') AS YearMonth, COUNT(e.EventID) AS DisruptionFrequency";
    $frequency_from = "FROM ImpactsCompany i JOIN DisruptionEvent e ON e.EventID = i.EventID";
    $frequency_groupby = "GROUP BY DATE_FORMAT(e.EventDate, '%Y-%m')";

    //Puting query together and generating result
    $frequencyQuery_2 = "{$frequencySelect_counts_by_month} {$frequency_from} {$whereStateEvents} {$frequency_groupby};";

    $resultfrequency_2 = mysqli_query($conn, $frequencyQuery_2);
    // Convert the table into individual rows and reformat.
    while ($row = mysqli_fetch_array($resultfrequency_2, MYSQLI_ASSOC)) {
        $frequency_2[] = $row;
    }

    //Making JSON Object
    $seniorDisruptionResults = [
        "frequency" => $frequency_1,
        "frequency_counts" => $frequency_2
    ];
    
    echo json_encode($seniorDisruptionResults);
    $conn->close();
    exit();
}

//Other charts
else {

$groupByRegion = ""; //Group By
$whereState = "";
$whereStateEvents = "";

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
$companyAffectedByEventSelect = "SELECT i.AffectedCompanyID,  c.CompanyName, i.ImpactLevel, e.EventID, e.EventDate, e.EventRecoveryDate, y.CategoryName, l.CountryName, l.ContinentName FROM ImpactsCompany i JOIN Company c ON i.AffectedCompanyID = c.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID JOIN DisruptionCategory y ON y.CategoryID = e.CategoryID JOIN Location l ON l.LocationID = c.LocationID";

//Puting query together and generating result
$companyAffectedByEventQuery = "{$companyAffectedByEventSelect} {$whereStateEvents}{$whereState} ORDER BY e.EventID;";
//  echo $companyAffectedByEventQuery;

$resultcompanyAffectedByEvent = mysqli_query($conn, $companyAffectedByEventQuery);
// Convert the table into individual rows and reformat.
$companyAffectedByEvent = []; //Creating shipping Array
while ($row = mysqli_fetch_array($resultcompanyAffectedByEvent, MYSQLI_ASSOC)) {
    $companyAffectedByEvent[] = $row;
}

// Regional Disruption Overview
$regionalOverviewSelect = "SELECT l.CountryName, l.ContinentName, COUNT(i.EventID) AS leftOverDisruption, SUM(CASE WHEN i.ImpactLevel = 'High' THEN 1 ELSE 0 END) AS HighImpactCount FROM Company c JOIN ImpactsCompany i ON i.AffectedCompanyID = c.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID JOIN Location l ON l.LocationID = c.LocationID";

//Puting query together and generating result
$regionalOverviewQuery = "{$regionalOverviewSelect} {$whereStateEvents}{$whereState} {$groupByRegion};";

$resultregionalOverview = mysqli_query($conn, $regionalOverviewQuery);
// Convert the table into individual rows and reformat.
$regionalOverview = []; //Creating shipping Array
while ($row = mysqli_fetch_array($resultregionalOverview, MYSQLI_ASSOC)) {
    $regionalOverview[] = $row;
}

//Making JSON Object
$seniorDisruptionResults = [
    "companyAffectedByEvent" => $companyAffectedByEvent,
    "regionalOverview" => $regionalOverview,
];

echo json_encode($seniorDisruptionResults);

$conn->close();
}
?>
