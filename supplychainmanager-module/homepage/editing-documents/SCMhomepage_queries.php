
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

// Convert the comma-delimited string into an array of strings
$tmp = explode('|', $tmp);
// print_r($tmp);

// function BasicCompanyInfoQueries($tmp, $conn)
// {
    $checksql = "SELECT c.Type, c.CompanyID FROM Company c WHERE c.CompanyName = '" . $tmp[0] . "';";
    // echo $checksql;
    $result = mysqli_query($conn, $checksql);
    $result = $result->fetch_row();
    // echo json_encode($rows);
    // $result = mysqli_query($conn, $query);
    // echo $result;
    // echo count($result);
    //Check company type to form query

    // echo $result[0];
    // echo $result[1];

    $BasicInfoSQL = "SELECT c.CompanyID, c.CompanyName, c.LocationID, c.TierLevel, c.Type, f.HealthScore, l.CountryName, l.City";

    if (count($result) == 0) { //Does company exist?
        echo "Company Not Found";
        $conn->close();
        exit;
    }
    if (strcmp($result[0], 'Manufacturer') == 0) {
        $BasicInfoSQL .= ", m.FactoryCapacity FROM Company c JOIN FinancialReport f ON c.CompanyID = f.CompanyID JOIN Manufacturer m ON c.CompanyID = m.CompanyID JOIN Location l ON l.LocationID = c.LocationID WHERE c.CompanyName = '" . $tmp[0] . "' ORDER BY f.RepYear DESC, f.Quarter DESC LIMIT 1;";
    }
    else{
        $BasicInfoSQL .= " FROM Company c JOIN FinancialReport f ON c.CompanyID = f.CompanyID JOIN Location l ON l.LocationID = c.LocationID WHERE c.CompanyName = '" . $tmp[0] . "' ORDER BY f.RepYear DESC, f.Quarter DESC LIMIT 1;";
    }

    $basicCompanyInfo = mysqli_query($conn, $BasicInfoSQL);
    // echo $BasicInfoSQL;

    // Convert the table into individual rows and reformat.
    $companyInfo = []; //Making Basic Company Info Array
    while ($row = mysqli_fetch_array($basicCompanyInfo, MYSQLI_ASSOC)) {
        $companyInfo[] = $row;
    }
    // echo json_encode($companyInfo);

    $distRoutes = []; //Creating Distributor Routes Array so that it will always be in JSON object
    if (strcmp($result[0], 'Distributor') == 0) {
        $distRoutesQuery = "SELECT o.FromCompanyID, o.ToCompanyID FROM Company c JOIN OperatesLogistics o ON c.CompanyID = o.DistributorID WHERE c.CompanyName = '" . $tmp[0] . "';";
        // echo $distRoutesQuery;
        //Execute the SQL query
        $resultdistRoutes = mysqli_query($conn, $distRoutesQuery);
        // Convert the table into individual rows and reformat.
        while ($row = mysqli_fetch_array($resultdistRoutes, MYSQLI_ASSOC)) {
            $distRoutes[] = $row;
        }
        // echo json_encode($distRoutes);

    }
    

    //Queries that always run
    $productsSuppliedQuery = "SELECT p.ProductID, p.ProductName FROM Product p JOIN SuppliesProduct s ON p.ProductID = s.ProductID JOIN Company c ON s.SupplierID = c.CompanyID  WHERE c.CompanyName = '" . $tmp[0] . "';";
    // echo $productsSuppliedQuery;
        //Execute the SQL query
    $resultproductsSupplied = mysqli_query($conn, $productsSuppliedQuery);
    // Convert the table into individual rows and reformat.
    $productsSupplied = []; //Creating Product Supplied Array
    while ($row = mysqli_fetch_array($resultproductsSupplied, MYSQLI_ASSOC)) {
        $productsSupplied[] = $row;
    }
    // echo json_encode($productsSupplied);


    $productDiversityQuery = "SELECT p.Category, COUNT(*) FROM Product p JOIN SuppliesProduct s ON p.ProductID = s.ProductID JOIN Company c ON s.SupplierID = c.CompanyID WHERE c.CompanyName = '" . $tmp[0] . "'
    GROUP BY p.Category ORDER BY p.Category";
    // echo $productDiversityQuery;
    //Execute the SQL query
    $resultproductDiversity = mysqli_query($conn, $productDiversityQuery);
    // Convert the table into individual rows and reformat.
    $productDiversity = []; //Creating Product Diversity Array
    while ($row = mysqli_fetch_array($resultproductDiversity, MYSQLI_ASSOC)) {
        $productDiversity[] = $row;
    }
    // echo json_encode($productDiversity);
    
    $dependedOnQuery = "SELECT DISTINCT d.DownStreamCompanyID FROM Company c JOIN DependsOn d ON c.companyid = d.UpStreamCompanyID WHERE c.CompanyName = '" . $tmp[0] . "';";
    // echo $dependedOnQuery;
    //Execute the SQL query
    $resultdependedOn = mysqli_query($conn, $dependedOnQuery);
    // Convert the table into individual rows and reformat.
    $dependedOn = []; //Creeating Depended On Array
    while ($row = mysqli_fetch_array($resultdependedOn, MYSQLI_ASSOC)) {
        $dependedOn[] = $row;
    }
    // echo json_encode($dependedOn);

    $dependsOnQuery = "SELECT DISTINCT d.UpStreamCompanyID FROM Company c JOIN DependsOn d ON c.companyid = d.DownStreamCompanyID  WHERE c.CompanyName = '" . $tmp[0] . "';";
    //  echo $dependsOnQuery;
    //Execute the SQL query
    $resultdependsOn = mysqli_query($conn, $dependsOnQuery);
    // Convert the table into individual rows and reformat.
    $dependsOn = []; //Creating Depends on Array
    while ($row = mysqli_fetch_array($resultdependsOn, MYSQLI_ASSOC)) {
        $dependsOn[] = $row;
    }
    //  echo json_encode($dependsOn);

    //Transaction Queries
    $shippingQuery = "SELECT s.ShipmentID, s.ActualDate, s.PromisedDate, p.ProductID, p.ProductName, s.Quantity FROM Shipping s JOIN Product p ON s.ProductID = p.ProductID JOIN Company c ON s.SourceCompanyID = c.CompanyID
            WHERE c.CompanyName = '" . $tmp[0] . "'AND s.ActualDate BETWEEN '" . $tmp[1] . "' AND '" . $tmp[2] . "' GROUP BY s.SourceCompanyID, p.ProductID, p.ProductName, s.DistributorID, s.ShipmentID, s.TransactionID ORDER BY s.ActualDate;";
    //  echo $shippingQuery;
    //Execute the SQL query
    $resultshipping = mysqli_query($conn, $shippingQuery);
    // Convert the table into individual rows and reformat.
    $shipping = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultshipping, MYSQLI_ASSOC)) {
        $shipping[] = $row;
    }
    //   echo json_encode($shipping);

    $receivingsQuery = "SELECT r.ReceivingID, r.ReceivedDate, r.QuantityReceived, p.ProductID, r.ShipmentID, p.ProductName, c.CompanyName, r.TransactionID
        FROM Receiving r JOIN Company c ON r.ReceiverCompanyID = c.CompanyID JOIN Shipping s ON r.ShipmentID = s.ShipmentID JOIN Product p ON s.ProductID = p.ProductID
        WHERE c.CompanyName = '" . $tmp[0] . "'AND r.ReceivedDate BETWEEN '" . $tmp[1] . "' AND '" . $tmp[2] . "' GROUP BY r.ReceivingID, r.ShipmentID, p.ProductID, p.ProductName, c.CompanyName, r.TransactionID
        ORDER BY r.ReceivedDate;";
    //  echo $receivingsQuery;
    //Execute the SQL query
    $resultreceivings = mysqli_query($conn, $receivingsQuery);
    // Convert the table into individual rows and reformat.
    $receivings = []; //Creating Receivings Array
    while ($row = mysqli_fetch_array($resultreceivings, MYSQLI_ASSOC)) {
    $receivings[] = $row;
    }
    // echo json_encode($receivings);

    $adjustmentsQuery = "SELECT a.AdjustmentID, a.AdjustmentDate, p.ProductID, a.QuantityChange, c.CompanyName, a.TransactionID
        FROM InventoryAdjustment a JOIN Company c ON a.CompanyID = c.CompanyID JOIN Product p ON a.ProductID = p.ProductID
        WHERE c.CompanyName = '" . $tmp[0] . "'AND a.AdjustmentDate BETWEEN '" . $tmp[1] . "' AND '" . $tmp[2] . "' GROUP BY a.AdjustmentID, p.ProductID, c.CompanyName, a.TransactionID
        ORDER BY a.AdjustmentDate;";
    //  echo $adjustmentsQuery;
    //Execute the SQL query
    $resultadjustments = mysqli_query($conn, $adjustmentsQuery);
    // Convert the table into individual rows and reformat.
    $adjustments = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultadjustments, MYSQLI_ASSOC)) {
    $adjustments[] = $row;
    }
    // echo json_encode($adjustments);

    //Queries for Key Performance
    //On time Rate Query
    $otrSELECT = "SELECT ROUND(((SUM(CASE WHEN s.ActualDate <= s.PromisedDate THEN 1 ELSE 0 END) / COUNT(DISTINCT s.ShipmentID)) * 100), 2) AS OTR
    FROM (SELECT x.ShipmentID, x.ActualDate, x.PromisedDate FROM Company c JOIN Shipping x ON c.CompanyID = x.SourceCompanyID ";
    $otrQuery = "{$otrSELECT} WHERE c.CompanyName =  '" . $tmp[0] . "' AND x.ActualDate BETWEEN '" . $tmp[1] . "' AND '" . $tmp[2] . "' GROUP BY x.ShipmentID) s;";
    //  echo $otrQuery;
    //Execute the SQL query
    $resultotr = mysqli_query($conn, $otrQuery);
    // Convert the table into individual rows and reformat.
    while ($row = mysqli_fetch_array($resultotr, MYSQLI_ASSOC)) {
    $otr[] = $row;
    }
    //  echo json_encode($otr); 

    $shipmentDetailsQuery = "SELECT ROUND(AVG(s.ActualDate - s.PromisedDate),2) AS avgDelay, ROUND(STDDEV(s.ActualDate - s.PromisedDate),2) AS stdDelay, COUNT(*) FROM Company c JOIN Shipping s ON c.CompanyID = s.SourceCompanyID 
    WHERE CompanyName = '" . $tmp[0] . "' AND s.ActualDate BETWEEN '" . $tmp[1] . "' AND '" . $tmp[2] . "' AND s.PromisedDate <= s.ActualDate;";
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


    $pastHealthScoresQuery = "SELECT f.HealthScore, f.Quarter, f.RepYear FROM Company c JOIN FinancialReport f ON c.CompanyID = f.CompanyID WHERE c.CompanyName = '" . $tmp[0] . "' ORDER BY f.RepYear DESC, f.Quarter DESC LIMIT 5;";
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
    WHERE c.CompanyName = '" . $tmp[0] . "' AND ((d.EventDate BETWEEN '" . $tmp[3] . "' AND '" . $tmp[4] . "') OR (d.EventRecoveryDate BETWEEN '" . $tmp[3] . "' AND '" . $tmp[4] . "') OR (d.EventDate < '" . $tmp[3] . "' AND d.EventRecoveryDate > '" . $tmp[4] . "')) ORDER BY x.CategoryName;";
    //  echo $disruptionEventsQuery;
    //Execute the SQL query
    $resultsdisruptionEvents = mysqli_query($conn, $disruptionEventsQuery);
    // Convert the table into individual rows and reformat.
    $disruptionEvents = []; //Creeating Depended On Array
    while ($row = mysqli_fetch_array($resultsdisruptionEvents, MYSQLI_ASSOC)) {
        $disruptionEvents[] = $row;
    }
    // echo json_encode($disruptionEvents);

    $disruptionEventsDistributionQuery = "SELECT x.CategoryName, COUNT(d.EventID) AS NumEvents
    FROM DisruptionEvent d JOIN DisruptionCategory x ON d.CategoryID = x.CategoryID JOIN ImpactsCompany i ON d.EventID = i.EventID JOIN Company c ON i.AffectedCompanyID = c.CompanyID
    WHERE c.CompanyName = '" . $tmp[0] . "' AND ((d.EventDate BETWEEN '" . $tmp[3] . "' AND '" . $tmp[4] . "') OR (d.EventRecoveryDate BETWEEN '" . $tmp[3] . "' AND '" . $tmp[4] . "') OR (d.EventDate < '" . $tmp[3] . "' AND d.EventRecoveryDate > '" . $tmp[4] . "')) GROUP BY x.CategoryName;";
    //  echo $disruptionEventsDistributionQuery;
    //Execute the SQL query
    $resultsdisruptionEventsDistribution = mysqli_query($conn, $disruptionEventsDistributionQuery);
    // Convert the table into individual rows and reformat.
    $disruptionEventsDistribution = []; //Creeating Depended On Array
    while ($row = mysqli_fetch_array($resultsdisruptionEventsDistribution, MYSQLI_ASSOC)) {
        $disruptionEventsDistribution[] = $row;
    }
    // echo json_encode($disruptionEventsDistribution);

    //Making JSON Object
    $SCMHomePageCompanyResults = [
        "companyInfo" => $companyInfo,
        "distRoutes" => $distRoutes,
        "productsSupplied" => $productsSupplied,
        "productDiversity" => $productDiversity,
        "dependedOn" => $dependedOn,
        "dependsOn" => $dependsOn,
        "shipping" => $shipping,
        "receivings" => $receivings,
        "adjustments" => $adjustments,
        "otr" => $otr,
        "shipmentDetails" => $shipmentDetails,
        "totalShipments" => $totalShipments,
        "pastHealthScores" => $pastHealthScores,
        "disruptionEvents" => $disruptionEvents,
        "disruptionEventsDistribution"=> $disruptionEventsDistribution
    ];

    echo json_encode($SCMHomePageCompanyResults);

    // //Queries for Key Performance
    // $shipmentDetailsQuery = "SELECT AVG(s.ActualDate - s.PromisedDate), STDDEV(s.ActualDate - s.PromisedDate), COUNT(*) FROM Company c JOIN Shipping s ON c.CompanyID = s.SourceCompanyID 
    // WHERE CompanyName = '" . $tmp . "' AND s.ActualDate BETWEEN '2020-01-01' AND '2025-09-30' AND s.ActualDate <= s.PromisedDate;";
    // echo $shipmentDetailsQuery;
    // $totalShipmentsQuery = "SELECT COUNT(*) FROM Company c JOIN Shipping s ON c.CompanyID = s.SourceCompanyID
    // WHERE CompanyName = '" . $tmp . "' AND s.ActualDate BETWEEN '2020-01-01' AND '2025-09-30';";
    // echo $totalShipmentsQuery;
    // $pastHealthScores = "SELECT f.HealthScore FROM Company c JOIN FinancialReport f ON c.CompanyID = f.CompanyID WHERE c.CompanyName = '" . $tmp . "' ORDER BY f.RepYear DESC, f.Quarter DESC LIMIT 5;";
    // echo $pastHealthScores;
    // $disruptionEvents = "SELECT d.EventID, x.CategoryName, d.EventDate, d.EventRecoveryDate, x.Description FROM DisruptionEvent d JOIN DisruptionCategory x ON d.CategoryID = x.CategoryID JOIN ImpactsCompany i ON d.EventID = i.EventID JOIN Company c ON i.AffectedCompanyID = c.CompanyID
    // WHERE c.CompanyName = '" . $tmp . "' GROUP BY x.CategoryName;";
    // echo $disruptionEvents;
// }

$conn->close();
?>
