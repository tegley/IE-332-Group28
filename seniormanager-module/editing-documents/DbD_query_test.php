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

$tmp = $_GET['q'];
$quer = $_GET['g']; //User specified filters

// Convert the comma-delimited string into an array of strings.
$tmp = explode(',', $tmp);
// print_r($tmp);
$quer = explode('|', $quer); //[Company Name]
// print_r($quer);

// Build and run distributor delay query
$distributor_delaySelect = "SELECT C.CompanyName, D.CompanyID AS Distributor, AVG(DATEDIFF(S.ActualDate, S.PromisedDate)) AS AverageDelay FROM Distributor D JOIN Company C ON D.CompanyID = C.CompanyID JOIN Shipping S ON D.CompanyID = S.DistributorID ";
$wherearival = "WHERE S.PromisedDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "'";
$distributor_delayQuery = "{$distributor_delaySelect} {$wherearival} AND S.ActualDate >= S.PromisedDate GROUP BY D.CompanyID;";

$resultdistributor_delay = mysqli_query($conn, $distributor_delayQuery);
while ($row = mysqli_fetch_array($resultdistributor_delay, MYSQLI_ASSOC)) {
    $distributor_delay[] = $row; 
}
//echo json_encode($distributor_delay); // Quiry version first than result

//JSON Object for Distributor Delay
$DistributorResults = [
   "distributor_delay" => $distributor_delay,
];

echo json_encode($DistributorResults);

$conn->close();
?>  