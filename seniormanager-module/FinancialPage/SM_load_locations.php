<?php
$servername = "mydb.itap.purdue.edu";

$username = "g1151938";//yourCAREER/groupusername
$password = "Purdue28";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

//Always - user_updates[0] = Type, user_updates[1] = CompanyID (initially selected), user_updates[2] = CompanyName, user_updates[3] = TierLevel
//There are also an additional inputs in indecies that vary depend on the selected type

//Temporary variables
$user_updates = $_GET['q'];
$user_updates = explode('|', $user_updates);

$location_query = "SELECT City, CountryName FROM Location";
$result_location = mysqli_query($conn, $location_query);
while ($row = mysqli_fetch_array($result_location, MYSQLI_ASSOC)) {
    $location_array[] = $row;
}

$SeniorLocations = ["Locations" => $location_array ];

echo json_encode($SeniorLocations)

?>
