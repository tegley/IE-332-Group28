<?php
$servername = "mydb.itap.purdue.edu";

$username = "";//yourCAREER/groupusername
$password = "";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

// Get the value from the q variable within the get request sent by AJAX
// Try this URL ending for testing: .php?q=Wood%20Ltd,2020-01-01,2025-09-30
$tmp = $_GET['q'];
// print_r($tmp);


// Convert the comma-delimited string into an array of strings.
$tmp = explode(',', $tmp);
// print_r($tmp);



    //Queries for Key Performance
    $shipmentDetailsQuery = "SELECT AVG(s.ActualDate - s.PromisedDate), STDDEV(s.ActualDate - s.PromisedDate), COUNT(*) FROM Company c JOIN Shipping s ON c.CompanyID = s.SourceCompanyID 
    WHERE CompanyName = '" . $tmp[0] . "' AND s.ActualDate BETWEEN '" . $tmp[1] . "' AND '" . $tmp[2] . "' AND s.ActualDate <= s.PromisedDate;";
    // echo $shipmentDetailsQuery;
     //Execute the SQL query
    $resultshipmentDetails = mysqli_query($conn, $shipmentDetailsQuery);
    // Convert the table into individual rows and reformat.
    $shipmentDetails = []; //Creeating Depended On Array
    while ($row = mysqli_fetch_array($resultshipmentDetails, MYSQLI_ASSOC)) {
        $shipmentDetails[] = $row;
    }
    // echo json_encode($shipmentDetails);

    $totalShipmentsQuery = "SELECT COUNT(*) FROM Company c JOIN Shipping s ON c.CompanyID = s.SourceCompanyID
    WHERE CompanyName = '" . $tmp[0] . "' AND s.ActualDate BETWEEN '" . $tmp[1] . "' AND '" . $tmp[2] . "';";
    // echo $totalShipmentsQuery;
    //Execute the SQL query
    $resultstotalShipments = mysqli_query($conn, $totalShipmentsQuery);
    // Convert the table into individual rows and reformat.
    $totalShipments = []; //Creeating Depended On Array
    while ($row = mysqli_fetch_array($resultstotalShipments, MYSQLI_ASSOC)) {
        $totalShipments[] = $row;
    }
    // echo json_encode($totalShipments);


    $pastHealthScoresQuery = "SELECT f.HealthScore FROM Company c JOIN FinancialReport f ON c.CompanyID = f.CompanyID WHERE c.CompanyName = '" . $tmp[0] . "' ORDER BY f.RepYear DESC, f.Quarter DESC LIMIT 5;";
    // echo $pastHealthScoresQuery;
    //Execute the SQL query
    $resultspastHealthScores = mysqli_query($conn, $pastHealthScoresQuery);
    // Convert the table into individual rows and reformat.
    $pastHealthScores = []; //Creeating Depended On Array
    while ($row = mysqli_fetch_array($resultspastHealthScores, MYSQLI_ASSOC)) {
        $pastHealthScores[] = $row;
    }
    // echo json_encode($pastHealthScores);

    $disruptionEventsQuery = "SELECT d.EventID, x.CategoryName, d.EventDate, d.EventRecoveryDate, x.Description FROM DisruptionEvent d JOIN DisruptionCategory x ON d.CategoryID = x.CategoryID JOIN ImpactsCompany i ON d.EventID = i.EventID JOIN Company c ON i.AffectedCompanyID = c.CompanyID
    WHERE c.CompanyName = '" . $tmp[0] . "' GROUP BY x.CategoryName;";
    // echo $disruptionEvents;
    //Execute the SQL query
    $resultsdisruptionEvents = mysqli_query($conn, $disruptionEventsQuery);
    // Convert the table into individual rows and reformat.
    $disruptionEvents = []; //Creeating Depended On Array
    while ($row = mysqli_fetch_array($resultsdisruptionEvents, MYSQLI_ASSOC)) {
        $disruptionEvents[] = $row;
    }
    // echo json_encode($disruptionEvents);

    $SCMHomePageKeyPerformanceResults = [
        "shipmentDetails" => $shipmentDetails,
        "totalShipments" => $totalShipments,
        "pastHealthScores" => $pastHealthScores,
        "disruptionEvents" => $disruptionEvents,
    ];

    echo json_encode($SCMHomePageKeyPerformanceResults);



$conn->close();
?>
