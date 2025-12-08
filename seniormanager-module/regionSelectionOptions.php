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
// Convert the comma-delimited string into an array of strings.
$tmp = explode(',', $tmp);
// print_r($tmp);
$whereState = "";

//WHERE statement added per user input
    if (!empty($tmp[1])) { //Adding appropriate where if user input a specific region.
        switch ($tmp[0]) {
            case "Country":
                $whereState = "WHERE l.CountryName = '" . $tmp[1] . "'";
                break;
            case "Continent":
                $whereState = "WHERE l.ContinentName = '" . $tmp[1] . "'";
                break;
            default:
            $whereState = "";
        }
    }
    // print_r($whereState);

$companyNameQuery = "SELECT c.CompanyName FROM Company c JOIN Location l ON l.LocationID = c.LocationID {$whereState};";

//  echo $companyNameQuery;
//Execute the SQL query
$resultcompanyName = mysqli_query($conn, $companyNameQuery);
// Convert the table into individual rows and reformat.
$companyName = []; //Creating shipping Array
while ($row = mysqli_fetch_array($resultcompanyName, MYSQLI_ASSOC)) {
$companyName[] = $row;
}
// echo json_encode($companyName);

$disruptionIDQuery = "SELECT DISTINCT e.EventID FROM DisruptionEvent e JOIN ImpactsCompany i ON i.EventID = e.EventID JOIN Company c ON c.CompanyID = i.AffectedCompanyID JOIN Location l ON l.LocationID = c.LocationID {$whereState};";
$resultdisruptionID = mysqli_query($conn, $disruptionIDQuery);
$disruptionID = [];
while ($row = mysqli_fetch_array($resultdisruptionID, MYSQLI_ASSOC)) {
    $disruptionID[] = $row;
}
// echo json_encode($disruptionID);

$totalResults = [
        "company"=> $companyName,
        "disruptionID" => $disruptionID
    ];

    echo json_encode($totalResults);

$conn->close();
?>

    
