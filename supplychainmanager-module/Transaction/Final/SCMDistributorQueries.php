<?php
$servername = "mydb.itap.purdue.edu";

$username = "g1151938";//yourCAREER/groupusername
$password = "Purdue28";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

$tmp = $_GET['q'];
$quer = $_GET['g']; //User specified filters

// Convert the comma-delimited string into an array of strings.
$tmp = explode(',', $tmp);
// print_r($tmp);
$quer = explode('|', $quer); //[Company Name]
// print_r($quer);


//We will need to build the queries per user selection, but the added constraints will be the same accross all queries, so we will make additions rn
$whereStateShip = "WHERE s.ActualDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "'"; //User specified time range //If user specifies date range
$whereStateOut = "WHERE s.PromisedDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "'";
$whereStateEvents = "WHERE ((e.EventDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "') OR (e.EventRecoveryDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "') OR (e.EventDate < '" . $tmp[0] . "' AND e.EventRecoveryDate > '" . $tmp[1] . "'))";
 

    //Distributor Specific Information Queries. User will see all relevant distributer information and will be able to narrow down with javascript
    //Distributor Specific Where statement based on which distributor user selects
    //Making distributor specfic arrays so that they are always in the JSON object
    $distributor = []; //Creating array for shipment volume and on time rate
    $productsHandled = []; //Creating products handled array
    $shipmentsOutstanding = []; //Creating shipments outstanding array
    $disruptionEvent = []; //Creating disruption event array
    $disruptionEXPOSUREEvent = []; //Disruption event exposure
    //Start Queries
    $whereDis = " WHERE c.CompanyName =  '" . $quer[0] . "'";

    $distributorSELECT = "SELECT d.CompanyID, c.CompanyName, COUNT(DISTINCT s.ShipmentID) AS ShipmentVolume, SUM(s.Quantity) AS TotQuantityShipped, ROUND(AVG(s.Quantity), 2) AS AVGShipQuantity
    FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID JOIN Shipping s ON s.DistributorID = d.CompanyID JOIN Product p ON s.ProductID = p.ProductID
    "; //User might choose to instead group by products on the page. Query as is will allow user to see the various products handled
    $distributorQuery = "{$distributorSELECT} {$whereDis} AND s.ActualDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "';";
    // echo $distributorQuery;
    //Execute the SQL query
    $resultdistributor = mysqli_query($conn, $distributorQuery);
    // Convert the table into individual rows and reformat.
    while ($row = mysqli_fetch_array($resultdistributor, MYSQLI_ASSOC)) {
    $distributor[] = $row;
    }
    // echo json_encode($distributor); 

    //On time Rate Query
    $otrSELECT = "SELECT ROUND(((SUM(CASE WHEN s.ActualDate <= s.PromisedDate THEN 1 ELSE 0 END) / COUNT(DISTINCT s.ShipmentID)) * 100), 2) AS OTR
    FROM (SELECT x.ShipmentID, x.ActualDate, x.PromisedDate FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID JOIN Shipping x ON x.DistributorID = d.CompanyID ";
    $otrQuery = "{$otrSELECT} {$whereDis} AND x.ActualDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "' GROUP BY x.ShipmentID) s;";
    // echo $otrQuery;
    //Execute the SQL query
    $resultotr = mysqli_query($conn, $otrQuery);
    // Convert the table into individual rows and reformat.
    while ($row = mysqli_fetch_array($resultotr, MYSQLI_ASSOC)) {
    $otr[] = $row;
    }
    // echo json_encode($otr); 

    //Products Handled Query
    $productsHandledSelect = "SELECT d.CompanyID, c.CompanyName, p.ProductName, p.ProductID, x.ProductCount FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID JOIN Shipping s ON s.DistributorID = d.CompanyID 
    JOIN Product p ON p.ProductID = s.ProductID JOIN (SELECT d.CompanyID, COUNT(DISTINCT p.productID) AS ProductCount FROM Shipping s JOIN Distributor d ON s.DistributorID = d.CompanyID JOIN Product p ON p.ProductID = s.ProductID 
    GROUP BY d.CompanyID) x ON x.CompanyID = d.CompanyID";
    $productsHandledQuery = "{$productsHandledSelect} {$whereDis} AND s.ActualDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "' GROUP BY p.ProductName, p.ProductID ORDER BY d.CompanyID, c.CompanyName;";
    // echo $productsHandledQuery;
    //Execute the SQL query
    $resultproductsHandled = mysqli_query($conn, $productsHandledQuery);
    // Convert the table into individual rows and reformat.
    while ($row = mysqli_fetch_array($resultproductsHandled, MYSQLI_ASSOC)) {
    $productsHandled[] = $row;
    }
    // echo json_encode($productsHandled);

    //What shipments are currently out query I don't think this is working
    $shipmentsOutstandingSelect = "SELECT s.ShipmentID, d.CompanyID, c.CompanyName, (SELECT COUNT(*) FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID JOIN Shipping s ON s.DistributorID = d.CompanyID {$whereStateOut} AND c.CompanyName =  '" . $quer[0] . "' AND s.ActualDate IS NULL {$groupState}) AS CountOutstanding
    FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID JOIN Shipping s ON s.DistributorID = d.CompanyID";
    $shipmentsOutstandingQuery = "{$shipmentsOutstandingSelect} {$whereStateOut} AND c.CompanyName =  '" . $quer[0] . "' AND s.ActualDate IS NULL {$groupState};"; //Will have same filters as user specified
    //  echo $shipmentsOutstandingQuery;
    //Execute the SQL query
    $resultshipmentsOutstanding = mysqli_query($conn, $shipmentsOutstandingQuery);
    // Convert the table into individual rows and reformat.
    while ($row = mysqli_fetch_array($resultshipmentsOutstanding, MYSQLI_ASSOC)) {
    $shipmentsOutstanding[] = $row; 
    }
    // echo json_encode($shipmentsOutstanding);

    //Query that gets shipment dates for plot
    $shippingSelect = "SELECT s.ShipmentID, s.ActualDate, s.PromisedDate FROM Shipping s JOIN Company c ON s.DistributorID = c.CompanyID";
    $shippingQuery = "{$shippingSelect} {$whereStateShip} AND c.CompanyName =  '" . $quer[0] . "';"; 
    //  echo $shippingQuery;
    //Execute the SQL query
    $resultshipping = mysqli_query($conn, $shippingQuery);
    // Convert the table into individual rows and reformat.
    while ($row = mysqli_fetch_array($resultshipping, MYSQLI_ASSOC)) {
    $shipping[] = $row; 
    }
    // echo json_encode($shipping);

    //Query that explores disruption event exposure
    $disruptionEventSelect = "SELECT e.EventID, y.CategoryName, i.ImpactLevel
    FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID JOIN Shipping s ON s.DistributorID = d.CompanyID  JOIN Location l ON l.LocationID = c.LocationID
    JOIN ImpactsCompany i ON i.AffectedCompanyID = d.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID JOIN DisruptionCategory y ON y.CategoryID = e.CategoryID";
    $disruptionEventQuery = "{$disruptionEventSelect} {$whereStateEvents} AND c.CompanyName =  '" . $quer[0] . "' GROUP BY e.EventID, y.CategoryName
; "; //Will have same time range filters as user specified 
    //Fix to where the disruption date is inbetween
    // echo $disruptionEventQuery;
    //Execute the SQL query
    $resultdisruptionEvent = mysqli_query($conn, $disruptionEventQuery);
    // Convert the table into individual rows and reformat.
    while ($row = mysqli_fetch_array($resultdisruptionEvent, MYSQLI_ASSOC)) {
    $disruptionEvent[] = $row; 
    }
    // echo json_encode($disruptionEvent);

    //Query that explores disruption event exposure 
    $disruptionEXPOSUREEventSelect = "SELECT COUNT(e.EventID) AS disruptionExposure, SUM(CASE WHEN i.ImpactLevel = 'High' THEN 1 ELSE 0 END) AS NumHighImpact, SUM(CASE WHEN i.ImpactLevel = 'Medium' THEN 1 ELSE 0 END) AS NumMedImpact, SUM(CASE WHEN i.ImpactLevel = 'Low' THEN 1 ELSE 0 END) AS NumLowImpact
    FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID
    JOIN ImpactsCompany i ON i.AffectedCompanyID = d.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID";
    $disruptionEXPOSUREEventQuery = "{$disruptionEXPOSUREEventSelect} {$whereStateEvents} AND c.CompanyName =  '" . $quer[0] . "'; "; //Will have same time range filters as user specified
    // echo $disruptionEXPOSUREEventQuery;
    //Execute the SQL query
    $resultdisruptionEXPOSUREEvent = mysqli_query($conn, $disruptionEXPOSUREEventQuery);
    // Convert the table into individual rows and reformat.
    while ($row = mysqli_fetch_array($resultdisruptionEXPOSUREEvent, MYSQLI_ASSOC)) {
    $disruptionEXPOSUREEvent[] = $row; 
    }
    // echo json_encode($disruptionEXPOSUREEvent);
        
        //Making JSON Object
    $SCMDistributorResults = [
        "distributor"=> $distributor,
        "otr" => $otr,
        "productsHandled"=> $productsHandled,
        "shipmentsOutstanding"=> $shipmentsOutstanding,
        "shipping" => $shipping,
        "disruptionEvent"=> $disruptionEvent,
        "disruptionEXPOSUREEvent"=> $disruptionEXPOSUREEvent
    ];

    echo json_encode($SCMDistributorResults);



   

$conn->close();
?>
