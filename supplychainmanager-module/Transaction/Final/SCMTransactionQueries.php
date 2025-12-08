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
$quer = explode('|', $quer); //["filter type, "user input"]
// print_r($quer);


//We will need to build the queries per user selection, but the added constraints will be the same accross all queries, so we will make additions rn
$whereState = ""; //If user specifies date range
$havingState = "";

    //HAVING statement added per user input
    if (!empty($quer[0])) { //Adding appropriate Having if user input a specific company/location/etc.
        switch ($quer[0]) {
            case "company":
                $havingState = "HAVING c.CompanyName = '" . $quer[1] . "'";
                break;
            case "city":
                $havingState = "HAVING l.City = '" . $quer[1] . "'";
                break;
            case "country":
                $havingState = "HAVING l.CountryName = '" . $quer[1] . "'";
                break;
            case "continent":
                $havingState = "HAVING l.ContinentName = '" . $quer[1] . "'";
                break;
            default:
            $havingState = "";
        }
    }
    // print_r($havingState);


    //Time range contstraint added per user input
    if (!empty($tmp[0])) { //Adding appropriate Having if user inputa specific company/location/etc. Empty built in function check if tmp[o] has no value
        $whereStateShip = "WHERE s.ActualDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "'"; //User specified time range
        $whereStateRec = "WHERE r.ReceivedDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "'"; //User specified time range
        $whereStateAdj = "WHERE a.AdjustmentDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "'"; //User specified time range
         }

    //Transaction Queries
    // $shippingSelect = "SELECT s.ShipmentID, s.ActualDate, s.PromisedDate, p.ProductID, p.ProductName, s.SourceCompanyID, s.DestinationCompanyID, s.DistributorID, s.TransactionID FROM Shipping s JOIN Product p ON s.ProductID = p.ProductID
    //     JOIN Company c ON s.SourceCompanyID = c.CompanyID LEFT JOIN Location l ON l.LocationID = c.LocationID";
    $shippingSelect = "SELECT s.ShipmentID, s.ActualDate, s.PromisedDate, s.TransactionID, r.ReceivingID, r.QuantityReceived, c.CompanyName, l.City, l.CountryName, l.ContinentName FROM Shipping s JOIN Product p ON s.ProductID = p.ProductID
        JOIN Company c ON s.SourceCompanyID = c.CompanyID LEFT JOIN Receiving r ON r.TransactionID = s.TransactionID LEFT JOIN Location l ON l.LocationID = c.LocationID";

    $receivingsSelect = "SELECT r.ReceivingID, r.ReceivedDate, r.QuantityReceived, r.TransactionID, c.CompanyName, l.City, l.CountryName, l.ContinentName
        FROM Receiving r JOIN Company c ON r.ReceiverCompanyID = c.CompanyID JOIN Shipping s ON r.ShipmentID = s.ShipmentID JOIN Product p ON s.ProductID = p.ProductID LEFT JOIN Location l ON l.LocationID = c.LocationID";
    

    $adjustmentsSelect = "SELECT a.AdjustmentID, a.AdjustmentDate, p.ProductID, a.QuantityChange, a.Reason, c.CompanyName, l.City, l.CountryName, l.ContinentName
        FROM InventoryAdjustment a JOIN Company c ON a.CompanyID = c.CompanyID JOIN Product p ON a.ProductID = p.ProductID LEFT JOIN Location l ON l.LocationID = c.LocationID";
    

    //Puting shipping query together and generating result
    $shippingQuery = "{$shippingSelect} {$whereStateShip} GROUP BY s.ShipmentID, s.ActualDate, s.PromisedDate, s.TransactionID, r.ReceivingID, r.QuantityReceived {$havingState};";
    //  echo $shippingQuery;

    $resultshipping = mysqli_query($conn, $shippingQuery);
    // Convert the table into individual rows and reformat.
    $shipping = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultshipping, MYSQLI_ASSOC)) {
        $shipping[] = $row;
    }
    //   echo json_encode($shipping);

    //Putting Receivings query together and generating result
    $receivingsQuery = "{$receivingsSelect} {$whereStateRec} GROUP BY r.ReceivingID, r.ReceivedDate, r.QuantityReceived, r.TransactionID {$havingState};";

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
    $adjustmentsQuery = "{$adjustmentsSelect} {$whereStateAdj} GROUP BY a.AdjustmentID, a.AdjustmentDate, p.ProductID, a.QuantityChange, a.Reason {$havingState};";

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
    $leavingCompanySelect = "SELECT c.CompanyName, s.ShipmentID, s.Quantity, s.ActualDate, l.City, l.CountryName, l.ContinentName, p.ProductID
    FROM Shipping s JOIN Product p ON s.ProductID = p.ProductID JOIN Company c ON s.SourceCompanyID = c.CompanyID LEFT JOIN Location l ON l.LocationID = c.LocationID JOIN InventoryTransaction t ON s.TransactionID = t.TransactionID LEFT JOIN Receiving r ON r.ShipmentID = s.ShipmentID LEFT JOIN InventoryAdjustment a ON a.TransactionID = t.TransactionID";
    $orderByState = "ORDER BY c.CompanyName;"; //This will be ordered by company name for neat presentation

    $leavingCompanyQuery = "{$leavingCompanySelect} {$whereStateShip} GROUP BY s.ShipmentID, s.Quantity, s.ActualDate {$havingState} {$orderByState}";
    // echo $leavingCompanyQuery;
    //Execute the SQL query
    $resultleavingCompany = mysqli_query($conn, $leavingCompanyQuery);
    // Convert the table into individual rows and reformat.
    $leavingCompany = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultleavingCompany, MYSQLI_ASSOC)) {
    $leavingCompany[] = $row;
    }
    // echo json_encode($leavingCompany); //Concern: There are a lot of nulls because not every transaction involves an adjustment

    //Examining Transactions leaving arriving to a company and associated adjusments/receivings 
    $arrivingCompanySelect = "SELECT c.CompanyName, r.ReceivingID, r.ReceivedDate, r.QuantityReceived, l.City, l.CountryName, l.ContinentName, p.ProductID
    FROM Receiving r JOIN Company c ON r.ReceiverCompanyID = c.CompanyID LEFT JOIN Location l ON l.LocationID = c.LocationID JOIN InventoryTransaction t ON r.TransactionID = t.TransactionID LEFT JOIN Shipping s ON r.ShipmentID = s.ShipmentID JOIN Product p ON s.ProductID = p.ProductID
    ";
    $arrivingCompanyQuery = "{$arrivingCompanySelect} {$whereStateRec} GROUP BY r.ReceivingID, r.ReceivedDate, r.QuantityReceived {$havingState} {$orderByState}";
    // echo $arrivingCompanyQuery;
    //Execute the SQL query
    $resultarrivingCompany = mysqli_query($conn, $arrivingCompanyQuery);
    // Convert the table into individual rows and reformat.
    $arrivingCompany = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resultarrivingCompany, MYSQLI_ASSOC)) {
    $arrivingCompany[] = $row;
    }
    // echo json_encode($arrivingCompany); //Concern: There are a lot of nulls because not every transaction involves an adjustment

    
        
    //Are the group bys adding value for the disruption exposure queries?
        
        //Making JSON Object
    $SCMTransactionResults = [
        "shipping" => $shipping,
        "receivings" => $receivings,
        "adjustments" => $adjustments,
        "leavingCompany" => $leavingCompany,
        "arrivingCompany" => $arrivingCompany
    ];

    echo json_encode($SCMTransactionResults);



   

$conn->close();
?>
