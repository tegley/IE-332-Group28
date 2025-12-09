<?php
$servername = "mydb.itap.purdue.edu";

$username = "g1151938";//yourCAREER/groupusername
$password = "Purdue28";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}


$distributorNameQuery = "SELECT CompanyName FROM Company WHERE Type = 'Distributor';";

//   echo $distributorNameQuery;
//Execute the SQL query
$resultdistributorName = mysqli_query($conn, $distributorNameQuery);
// Convert the table into individual rows and reformat.
$distributorName = []; //Creating shipping Array
while ($row = mysqli_fetch_array($resultdistributorName, MYSQLI_ASSOC)) {
$distributorName[] = $row;
}
// echo json_encode($distributorName);

$companyNameQuery = "SELECT CompanyName FROM Company;";

//  echo $companyNameQuery;
//Execute the SQL query
$resultcompanyName = mysqli_query($conn, $companyNameQuery);
// Convert the table into individual rows and reformat.
$companyName = []; //Creating shipping Array
while ($row = mysqli_fetch_array($resultcompanyName, MYSQLI_ASSOC)) {
$companyName[] = $row;
}
// echo json_encode($companyName);

$countryQuery = "SELECT DISTINCT CountryName FROM Location;";
$resultcountry = mysqli_query($conn, $countryQuery);
$country = [];
while ($row = mysqli_fetch_array($resultcountry, MYSQLI_ASSOC)) {
    $country[] = $row;
}
// echo json_encode($country);

$cityQuery = "SELECT DISTINCT City FROM Location WHERE City IS NOT NULL";
$resultcity = mysqli_query($conn, $cityQuery);
$city = [];
while ($row = mysqli_fetch_array($resultcity, MYSQLI_ASSOC)) {
    $city[] = $row;
}

$disruptionIDsQuery = "SELECT DISTINCT EventID FROM DisruptionEvent";
$result_disruption = mysqli_query($conn, $disruptionIDsQuery);
while ($row = mysqli_fetch_array($result_disruption, MYSQLI_ASSOC)) {
    $disruptionIDs[] = $row;
}

$productIDsQuery = "SELECT DISTINCT ProductID FROM Product";
$result_products = mysqli_query($conn, $productIDsQuery);
while ($row = mysqli_fetch_array($result_products, MYSQLI_ASSOC)) {
    $productIDs[] = $row;
}

$SCMDistributorResults = [
        "distributors"=> $distributorName,
        "company"=> $companyName,
        "country" => $country,
        "city"=> $city,
        "disruptionID" => $disruptionIDs,
        "productID" => $productIDs
    ];

echo json_encode($SCMDistributorResults);

$conn->close();
?>
