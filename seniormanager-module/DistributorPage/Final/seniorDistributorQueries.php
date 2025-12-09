<?php

$servername = "mydb.itap.purdue.edu";
$username = "g1151938";//yourCAREER/groupusername
$password = "Purdue28";//yourgrouppassword
$database = $username; // ITaPsetupdatabasename = yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

$tmp = $_GET['q']; //Company Name
//WHERE Statement based off of user input
$whereState = "";
    if (!empty($tmp)) { 
        $whereState = " WHERE c.CompanyName =  '" . $tmp . "'";
    }
    // print_r($whereState);


// Convert the comma-delimited string into an array of strings.
$tmp = explode('|', $tmp);
// print_r($tmp);


$distributor_delay = []; //Creating array for distributor delay
$TD_totalShipments = []; //Creating array for Top Distributor by Total Shipments
$TD_AVGShipment = []; //Creating array for Top Distributor by Average Shipment Quantity


// Build and run distributor delay query
$distributor_delaySelect = "SELECT c.CompanyName, d.CompanyID AS Distributor, ROUND(AVG(DATEDIFF(s.ActualDate, s.PromisedDate)), 2) AS AverageDelay FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID JOIN Shipping s ON d.CompanyID = s.DistributorID ";
$distributor_delayQuery = "{$distributor_delaySelect}{$whereState} GROUP BY d.CompanyID ORDER BY AverageDelay ASC;"; 
$resultdistributor_delay = mysqli_query($conn, $distributor_delayQuery);
while ($row = mysqli_fetch_array($resultdistributor_delay, MYSQLI_ASSOC)) {
    $distributor_delay[] = $row; 
}
// echo $distributor_delayQuery; 

//Build and run Top Distributor by Total Shipments query
$TD_totalShipmentsSelect = "SELECT c.CompanyName, COUNT(s.ShipmentID) AS TotalShipments FROM Shipping s JOIN Company c ON s.DistributorID = c.CompanyID";
$TD_totalShipmentsQuery = "{$TD_totalShipmentsSelect}{$whereState} GROUP BY s.DistributorID ORDER BY TotalShipments DESC;";
$resultTD_totalShipments = mysqli_query($conn, $TD_totalShipmentsQuery);
while ($row = mysqli_fetch_array($resultTD_totalShipments, MYSQLI_ASSOC)) {
   $TD_totalShipments[] = $row; 
}
// echo $TD_totalShipmentsQuery; 

//build and run Top Distributor by Average Shipment Quantity query
$TD_AVGShipmentSelect = "SELECT c.CompanyName, ROUND(AVG(s.Quantity), 2) AS AVGVolume FROM Shipping s JOIN Company c ON s.DistributorID = c.CompanyID";
$TD_AVGShipmentQuery = "{$TD_AVGShipmentSelect}{$whereState} GROUP BY s.DistributorID Order BY AVGVolume DESC;";
$resultTD_AVGShipment = mysqli_query($conn, $TD_AVGShipmentQuery);
while ($row = mysqli_fetch_array($resultTD_AVGShipment, MYSQLI_ASSOC)) {
   $TD_AVGShipment[] = $row; 
}
// echo $TD_AVGShipmentQuery; 

//Products Handled Query Does this need extra filters?
    $productsHandledSelect = "SELECT d.CompanyID, c.CompanyName, p.ProductName, p.ProductID, s.Quantity FROM Distributor d JOIN Company c ON d.CompanyID = c.CompanyID JOIN Shipping s ON s.DistributorID = d.CompanyID 
    JOIN Product p ON p.ProductID = s.ProductID ";
    $productsHandledQuery = "{$productsHandledSelect}{$whereState} GROUP BY p.ProductName, p.ProductID ORDER BY d.CompanyID, c.CompanyName;";
    // echo $productsHandledQuery;
    //Execute the SQL query
    $resultproductsHandled = mysqli_query($conn, $productsHandledQuery);
    // Convert the table into individual rows and reformat.
    while ($row = mysqli_fetch_array($resultproductsHandled, MYSQLI_ASSOC)) {
    $productsHandled[] = $row;
    }
    // echo json_encode($productsHandled);

//Query that gets shipment dates for plot
    $shippingSelect = "SELECT s.PromisedDate, s.Quantity, c.CompanyName FROM Shipping s JOIN Company c ON s.DistributorID = c.CompanyID";
    $shippingQuery = "{$shippingSelect}{$whereState} ORDER BY c.CompanyName;"; 
    //  echo $shippingQuery;
    //Execute the SQL query
    $resultshipping = mysqli_query($conn, $shippingQuery);
    // Convert the table into individual rows and reformat.
    while ($row = mysqli_fetch_array($resultshipping, MYSQLI_ASSOC)) {
    $shipping[] = $row; 
    }
    // echo json_encode($shipping);

//JSON Object for Distributor Results
$DistributorResults = [
   "distributor_delay" => $distributor_delay,
    "TD_totalShipments" => $TD_totalShipments,
    "TD_AVGShipment" => $TD_AVGShipment,
    "productsHandled" => $productsHandled,
    "shipping"=> $shipping
];

echo json_encode($DistributorResults);

$conn->close();
?>  

