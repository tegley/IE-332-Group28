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

if ($tmp == 'Country') {
   $query = "SELECT DISTINCT CountryName FROM Location;";
}
if($tmp == 'Continent'){
    $query = "SELECT DISTINCT ContinentName FROM Location;";
}
// echo $query;
 $resultregion = mysqli_query($conn, $query);
    $region = [];
    while ($row = mysqli_fetch_array($resultregion, MYSQLI_ASSOC)) {
        $region[] = $row;
    }
echo json_encode($region);

$conn->close();
?>
