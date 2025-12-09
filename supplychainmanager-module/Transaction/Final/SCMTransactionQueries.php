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
    $transactionShipSelect = "SELECT t.TransactionID, t.Type, c.CompanyName, l.City, l.CountryName, l.ContinentName FROM InventoryTransaction t JOIN Shipping s ON t.TransactionID = s.TransactionID JOIN Company c ON s.SourceCompanyID = c.CompanyID LEFT JOIN Location l ON l.LocationID = c.LocationID";
    $transactionShipQuery = "{$transactionShipSelect} {$whereStateShip} GROUP BY t.TransactionID, t.Type {$havingState} {$orderByState};";
    // echo $transactionShipQuery;
    //Execute the SQL query
    $resulttransactionShip = mysqli_query($conn, $transactionShipQuery);
    // Convert the table into individual rows and reformat.
    $transactionShip = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resulttransactionShip, MYSQLI_ASSOC)) {
    $transactionShip[] = $row;
    }
    // echo json_encode($transactionShip);

    $transactionRecSelect = "SELECT t.TransactionID, t.Type, c.CompanyName, l.City, l.CountryName, l.ContinentName FROM InventoryTransaction t JOIN Receiving r ON t.TransactionID = r.TransactionID JOIN Company c ON r.ReceiverCompanyID = c.CompanyID LEFT JOIN Location l ON l.LocationID = c.LocationID";
    $transactionRecQuery = "{$transactionRecSelect} {$whereStateRec} GROUP BY t.TransactionID, t.Type, c.CompanyName, l.City, l.CountryName, l.ContinentName {$havingState} {$orderByState};";
    // echo $transactionRecQuery;
    //Execute the SQL query
    $resulttransactionRec = mysqli_query($conn, $transactionRecQuery);
    // Convert the table into individual rows and reformat.
    $transactionRec = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($resulttransactionRec, MYSQLI_ASSOC)) {
    $transactionRec[] = $row;
    }
    // echo json_encode($transactionRec);

    $adjustmentsSelect = "SELECT a.AdjustmentID, a.AdjustmentDate, p.ProductID, a.QuantityChange, a.Reason, c.CompanyName, l.City, l.CountryName, l.ContinentName
        FROM InventoryAdjustment a JOIN Company c ON a.CompanyID = c.CompanyID JOIN Product p ON a.ProductID = p.ProductID LEFT JOIN Location l ON l.LocationID = c.LocationID";


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

    //Examining Transactions leaving specific company and associated adjusments/receivings 
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
    // echo json_encode($leavingCompany);

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
    // echo json_encode($arrivingCompany); 


        
        //Making JSON Object
    $SCMTransactionResults = [
        "transactionShip" => $transactionShip,
        "transactionRec" => $transactionRec,
        "adjustments" => $adjustments,
        "leavingCompany" => $leavingCompany,
        "arrivingCompany" => $arrivingCompany
    ];

    echo json_encode($SCMTransactionResults);



   

$conn->close();
?>
