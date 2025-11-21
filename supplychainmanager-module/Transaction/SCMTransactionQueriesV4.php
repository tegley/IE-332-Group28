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
$quer = $_GET['g']; //User specified filters

// Convert the comma-delimited string into an array of strings.
$tmp = explode(',', $tmp);
// print_r($tmp);
$quer = explode(',', $quer); //["drop down", "user input", "Continent/Tier" WHEN USER SPECIFIES TIER AND REGION ]
// print_r($quer);


//We will need to build the queries per user selection, but the added constraints will be the same accross all queries, so we will make additions rn
$whereState = ""; //If user specifies date range
$groupState = "";

    //Group by statement added per user input
    if (!empty($quer[0])) { //Adding appropriate Having if user input a specific company/location/etc.
        switch ($quer[0]) {
            case "Company":
                $groupState = "GROUP BY c.CompanyName";
                break;
            case "Country":
                $groupState = "GROUP BY l.CountryName";
                break;
            case "Continent":
                $groupState = "GROUP BY l.ContinentName";
                break;
            default:
            $groupState = "";
        }
    }

    //Time range contstraint added per user input
    if (!empty($quer[0])) { //Adding appropriate Having if user inputa specific company/location/etc. Empty built in function check if tmp[o] has no value
        $whereStateShip = "WHERE s.ActualDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "'"; //User specified time range
        $whereStateRec = "WHERE r.ReceivedDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "'"; //User specified time range
        $whereStateAdj = "WHERE a.AdjustmentDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "'"; //User specified time range
         }

    //Transaction Queries
    $shippingSelect = "SELECT s.ShipmentID, s.ActualDate, s.PromisedDate, p.ProductID, p.ProductName, s.SourceCompanyID, s.DestinationCompanyID, s.DistributorID, s.TransactionID FROM Shipping s JOIN Product p ON s.ProductID = p.ProductID
        JOIN Company c ON s.SourceCompanyID = c.CompanyID LEFT JOIN Location l ON l.LocationID = c.LocationID";
    

    $receivingsSelect = "SELECT r.ReceivingID, r.ReceivedDate, r.QuantityReceived, p.ProductID, r.ShipmentID, p.ProductName, c.CompanyName, r.TransactionID
        FROM Receiving r JOIN Company c ON r.ReceiverCompanyID = c.CompanyID JOIN Shipping s ON r.ShipmentID = s.ShipmentID JOIN Product p ON s.ProductID = p.ProductID LEFT JOIN Location l ON l.LocationID = c.LocationID";
    

    $adjustmentsSelect = "SELECT a.AdjustmentID, a.AdjustmentDate, p.ProductID, a.QuantityChange, c.CompanyName, a.TransactionID
        FROM InventoryAdjustment a JOIN Company c ON a.CompanyID = c.CompanyID JOIN Product p ON a.ProductID = p.ProductID LEFT JOIN Location l ON l.LocationID = c.LocationID";
    

    //Puting shipping query together and generating result
    $shippingQuery = "{$shippingSelect} {$whereStateShip} {$groupState};";
    //  echo $shippingQuery;

    $resultshipping = mysqli_query($conn, $shippingQuery);
    // Convert the table into individual rows and reformat.
    $shipping = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultshipping, MYSQLI_ASSOC)) {
        $shipping[] = $row;
    }
    //   echo json_encode($shipping);

    //Putting Receivings query together and generating result
    $receivingsQuery = "{$receivingsSelect} {$whereStateRec} {$groupState};";

    // echo $receivingsQuery;
    //Execute the SQL query
    $resultreceivings = mysqli_query($conn, $receivingsQuery);
    // Convert the table into individual rows and reformat.
    $receivings = []; //Creating Receivings Array
    while ($row = mysqli_fetch_array($resultreceivings, MYSQLI_ASSOC)) {
    $receivings[] = $row;
    }
    // echo json_encode($receivings); //Note that you can only have one uncommented JSON encode at a time


    //Putting Adjustments query together and generating result
    $adjustmentsQuery = "{$adjustmentsSelect} {$whereStateAdj} {$groupState};";

    //  echo $adjustmentsQuery;
    //Execute the SQL query
    $resultadjustments = mysqli_query($conn, $adjustmentsQuery);
    // Convert the table into individual rows and reformat.
    $adjustments = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultadjustments, MYSQLI_ASSOC)) {
    $adjustments[] = $row;
    }
    // echo json_encode($adjustments);

    //Examining Transactions leaving specific company and associated adjusments/receivings NO RESTRITIONS, WILL NEED TO ADD DATE RANGE, LOCATION, OR SPECIFIC COMPANY
    $leavingCompanySelect = "SELECT c.CompanyName, s.ShipmentID, s.ActualDate, s.PromisedDate, p.ProductID, p.ProductName, s.SourceCompanyID, s.DestinationCompanyID, s.DistributorID, s.TransactionID, r.ReceivingID, r.ReceivedDate, r.QuantityReceived, a.AdjustmentID, a.AdjustmentDate, a.QuantityChange
    FROM Shipping s JOIN Product p ON s.ProductID = p.ProductID JOIN Company c ON s.SourceCompanyID = c.CompanyID LEFT JOIN Location l ON l.LocationID = c.LocationID JOIN InventoryTransaction t ON s.TransactionID = t.TransactionID LEFT JOIN Receiving r ON r.ShipmentID = s.ShipmentID LEFT JOIN InventoryAdjustment a ON a.TransactionID = t.TransactionID";
    $orderByState = "ORDER BY c.CompanyName;"; //This will be ordered by company name for neat presentation

    $leavingCompanyQuery = "{$leavingCompanySelect} {$whereStateAdj} {$groupState} {$orderByState}";
    // echo $leavingCompanyQuery;
    //Execute the SQL query
    $resultleavingCompany = mysqli_query($conn, $leavingCompanyQuery);
    // Convert the table into individual rows and reformat.
    $leavingCompany = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultleavingCompany, MYSQLI_ASSOC)) {
    $leavingCompany[] = $row;
    }
    // echo json_encode($leavingCompany); //Concern: There are a lot of nulls because not every transaction involves an adjustment

    //Examining Transactions leaving arriving to a company and associated adjusments/receivings NO RESTRITIONS, WILL NEED TO ADD DATE RANGE, LOCATION, OR SPECIFIC COMPANY
    $arrivingCompanySelect = "SELECT c.CompanyName, r.ReceivingID, r.ReceivedDate, r.QuantityReceived, s.ShipmentID, s.ActualDate, s.PromisedDate, p.ProductID, p.ProductName, s.SourceCompanyID, s.DestinationCompanyID, s.DistributorID, s.TransactionID, a.AdjustmentID, a.AdjustmentDate, a.QuantityChange
    FROM Receiving r JOIN Company c ON r.ReceiverCompanyID = c.CompanyID LEFT JOIN Location l ON l.LocationID = c.LocationID JOIN InventoryTransaction t ON r.TransactionID = t.TransactionID LEFT JOIN Shipping s ON r.ShipmentID = s.ShipmentID JOIN Product p ON s.ProductID = p.ProductID LEFT JOIN InventoryAdjustment a ON a.TransactionID = r.TransactionID
    ";
    $arrivingCompanyQuery = "{$arrivingCompanySelect} {$whereStateAdj} {$groupState} {$orderByState}";
    // echo $arrivingCompanyQuery;
    //Execute the SQL query
    $resultarrivingCompany = mysqli_query($conn, $arrivingCompanyQuery);
    // Convert the table into individual rows and reformat.
    $arrivingCompany = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultarrivingCompany, MYSQLI_ASSOC)) {
    $arrivingCompany[] = $row;
    }
    // echo json_encode($arrivingCompany); //Concern: There are a lot of nulls because not every transaction involves an adjustment

    //Distributor Specific Information Queries. User will see all relevant distributer information and will be able to narrow down with javascript
    $distributorQuery = "SELECT d.CompanyID AS DistributorID, c.CompanyName, COUNT(DISTINCT s.ShipmentID) AS ShipmentVolume, ROUND(((SUM(CASE WHEN s.ActualDate <= s.PromisedDate THEN 1 ELSE 0 END) / COUNT(DISTINCT s.ShipmentID)) * 100), 2) AS OTRate, p.ProductName, p.ProductID
    FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID JOIN Shipping s ON s.DistributorID = d.CompanyID JOIN Product p ON s.ProductID = p.ProductID
    GROUP BY d.CompanyID, c.CompanyName;"; //User might choose to instead group by products on the page. Query as is will allow user to see the various products handled
    //echo $distributorQuery;
    //Execute the SQL query
    $resultdistributor = mysqli_query($conn, $distributorQuery);
    // Convert the table into individual rows and reformat.
    $distributor = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultdistributor, MYSQLI_ASSOC)) {
    $distributor[] = $row;
    }
    // echo json_encode($distributor); 

    //Products Handled Query Does this need extra filters?
    $productsHandledSelect = "SELECT d.CompanyID, c.CompanyName, p.ProductName, p.ProductID, x.ProductCount FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID JOIN Shipping s ON s.DistributorID = d.CompanyID 
    JOIN Product p ON p.ProductID = s.ProductID JOIN (SELECT d.CompanyID, COUNT(DISTINCT p.productID) AS ProductCount FROM Shipping s JOIN Distributor d ON s.DistributorID = d.CompanyID JOIN Product p ON p.ProductID = s.ProductID 
    GROUP BY d.CompanyID) x ON x.CompanyID = d.CompanyID";
    $productsHandledQuery = "{$productsHandledSelect} GROUP BY p.ProductName, p.ProductID ORDER BY d.CompanyID, c.CompanyName;";
    // echo $productsHandledQuery;
    //Execute the SQL query
    $resultproductsHandled = mysqli_query($conn, $productsHandledQuery);
    // Convert the table into individual rows and reformat.
    $productsHandled = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultproductsHandled, MYSQLI_ASSOC)) {
    $productsHandled[] = $row;
    }
    // echo json_encode($productsHandled);

    //What shipments are currently out query
    $shipmentsOutstandingSelect = "SELECT d.CompanyID, c.CompanyName, (CASE WHEN s.ActualDate IS NULL THEN 1 ELSE 0 END) AS CurrentlyOut
    FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID JOIN Shipping s ON s.DistributorID = d.CompanyID";
    $shipmentsOutstandingQuery = "{$shipmentsOutstandingSelect} {$whereStateShip} {$groupState};"; //Will have same filters as user specified
    // echo $shipmentsOutstandingQuery;
    //Execute the SQL query
    $resultshipmentsOutstanding = mysqli_query($conn, $shipmentsOutstandingQuery);
    // Convert the table into individual rows and reformat.
    $shipmentsOutstanding = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultshipmentsOutstanding, MYSQLI_ASSOC)) {
    $shipmentsOutstanding[] = $row; //Note, this is null due to a data base flaw
    }
    // echo json_encode($shipmentsOutstanding);

    //Query that explores disruption event exposure
    $disruptionEventSelect = "SELECT d.CompanyID, c.CompanyName, e.EventID, s.ShipmentID, s.TransactionID, y.CategoryName
    FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID JOIN Shipping s ON s.DistributorID = d.CompanyID  LEFT JOIN Location l ON l.LocationID = c.LocationID
    JOIN ImpactsCompany i ON i.AffectedCompanyID = d.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID JOIN DisruptionCategory y ON y.CategoryID = e.CategoryID";
    $disruptionEventQuery = "{$disruptionEventSelect} {$whereStateShip} AND s.ActualDate BETWEEN e.EventDate AND e.EventRecoveryDate {$groupState} ORDER BY d.CompanyID, c.CompanyName; "; //Will have same filters as user specified
    //echo $disruptionEventQuery;
    //Execute the SQL query
    $resultdisruptionEvent = mysqli_query($conn, $disruptionEventQuery);
    // Convert the table into individual rows and reformat.
    $disruptionEvent = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultdisruptionEvent, MYSQLI_ASSOC)) {
    $disruptionEvent[] = $row; //Note, this is null due to a data base flaw
    }
    // echo json_encode($disruptionEvent);

    //Query that explores disruption event exposure HIGH IMPACT ONLY
    $disruptionHIGHEventSelect = "SELECT d.CompanyID, c.CompanyName, e.EventID, s.ShipmentID, s.TransactionID, y.CategoryName
    FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID JOIN Shipping s ON s.DistributorID = d.CompanyID  JOIN Location l ON l.LocationID = c.LocationID
    JOIN ImpactsCompany i ON i.AffectedCompanyID = d.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID JOIN DisruptionCategory y ON y.CategoryID = e.CategoryID";
    $disruptionHIGHEventQuery = "{$disruptionHIGHEventSelect} {$whereStateShip} AND s.ActualDate BETWEEN e.EventDate AND e.EventRecoveryDate AND i.ImpactLevel = 'High' {$groupState} ORDER BY d.CompanyID, c.CompanyName; "; //Will have same filters as user specified
    //echo $disruptionHIGHEventQuery;
    //Execute the SQL query
    $resultdisruptionHIGHEvent = mysqli_query($conn, $disruptionHIGHEventQuery);
    // Convert the table into individual rows and reformat.
    $disruptionHIGHEvent = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultdisruptionHIGHEvent, MYSQLI_ASSOC)) {
    $disruptionHIGHEvent[] = $row; //Note, this is null due to a data base flaw
    }
    // echo json_encode($disruptionHIGHEvent);
        
    //Are the group bys adding value for the disruption exposure queries?
        
        //Making JSON Object
    $SCMTransactionResults = [
        "shipping" => $shipping,
        "receivings" => $receivings,
        "adjustments" => $adjustments,
        "leavingCompany" => $leavingCompany,
        "arrivingCompany" => $arrivingCompany,
        "distributor"=> $distributor,
        "productsHandled"=> $productsHandled,
        "shipmentsOutstanding"=> $shipmentsOutstanding,
        "disruptionEvent"=> $disruptionEvent,
        "disruptionHIGHEvent"=> $disruptionHIGHEvent
    ];

    echo json_encode($SCMTransactionResults);



   

$conn->close();
?>
