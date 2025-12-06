<?php
header('Content-Type: application/json; charset=utf-8');

$servername = "mydb.itap.purdue.edu";
$username = "tegley"; // yourCAREER/groupusername
$password = "#TurboCoder6412!!"; // yourgrouppassword
$database = $username; // ITaPsetupdatabasename = yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

$distributor_delay = []; //Creating array for distributor delay
$TD_totalShipments = []; //Creating array for Top Distributor by Total Shipments
$TD_AVGShipment = []; //Creating array for Top Distributor by Average Shipment Quantity


// Build and run distributor delay query
$distributor_delaySelect = "SELECT C.CompanyName, D.CompanyID AS Distributor, AVG(DATEDIFF(S.ActualDate, S.PromisedDate)) AS AverageDelay FROM Distributor D JOIN Company C ON D.CompanyID = C.CompanyID JOIN Shipping S ON D.CompanyID = S.DistributorID ";
$distributor_delayQuery = "{$distributor_delaySelect} GROUP BY D.CompanyID ORDER BY AverageDelay DESC"; 
$resultdistributor_delay = mysqli_query($conn, $distributor_delayQuery);
while ($row = mysqli_fetch_array($resultdistributor_delay, MYSQLI_ASSOC)) {
    $distributor_delay[] = $row; 
}
//echo json_encode($distributor_delayQuery); // Quiry version first than result

//Build and run Top Distributor by Total Shipments query
$TD_totalShipmentsSelect = "SELECT C.CompanyName, COUNT(S.ShipmentID) AS TotalShipments FROM Shipping S JOIN Company C ON S.DistributorID = C.CompanyID";
$TD_totalShipmentsQuery = "{$TD_totalShipmentsSelect} GROUP BY S.DistributorID Order BY TotalShipments DESC";
$resultTD_totalShipments = mysqli_query($conn, $TD_totalShipmentsQuery);
while ($row = mysqli_fetch_array($resultTD_totalShipments, MYSQLI_ASSOC)) {
   $TD_totalShipments[] = $row; 
}
//echo json_encode($TD_totalShipmentsQuery); // Quiry version first than result

//build and run Top Distributor by Average Shipment Quantity query
$TD_AVGShipmentSelect = "SELECT C.CompanyName, AVG(S.Quantity) AS AVGVolume FROM Shipping S JOIN Company C ON S.DistributorID = C.CompanyID";
$TD_AVGShipmentQuery = "{$TD_AVGShipmentSelect} GROUP BY S.DistributorID Order BY AVGVolume DESC";
$resultTD_AVGShipment = mysqli_query($conn, $TD_AVGShipmentQuery);
while ($row = mysqli_fetch_array($resultTD_AVGShipment, MYSQLI_ASSOC)) {
   $TD_AVGShipment[] = $row; 
}
//echo json_encode($TD_AVGShipmentQuery); // Quiry version first than result

//JSON Object for Distributor Results
$DistributorResults = [
   "distributor_delay" => $distributor_delay,
    "TD_totalShipments" => $TD_totalShipments,
    "TD_AVGShipment" => $TD_AVGShipment
];

echo json_encode($DistributorResults);

$conn->close();
?>  