<?php
$servername = "mydb.itap.purdue.edu";
$username   = "cox447";
$password   = "LunaZuna704";
$database   = $username;

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) { die("Connection failed:" . $conn->connect_error); }

if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit();
}

$tmp = explode('|', $_GET['q']);    // ["Country", "Australia"]

$regionType  = $tmp[0] ?? "";
$regionName  = $tmp[1] ?? "";

// Build WHERE filters
$whereRegion = "";
$orderRegion = "";

if ($regionType === "Country") {
    $orderRegion = "l.CountryName, ";
    if ($regionName !== "") {
        $whereRegion = "WHERE l.CountryName = '" . $conn->real_escape_string($regionName) . "'";
    }
}
elseif ($regionType === "Continent") {
    $orderRegion = "l.ContinentName, ";
    if ($regionName !== "") {
        $whereRegion = "WHERE l.ContinentName = '" . $conn->real_escape_string($regionName) . "'";
    }
}

$select = "
    SELECT 
        ROUND(AVG(f.HealthScore), 2) AS avgHealth,
        c.CompanyName,
        l.CountryName,
        l.ContinentName
    FROM Company c
    JOIN FinancialReport f ON c.CompanyID = f.CompanyID
    LEFT JOIN Location l ON l.LocationID = c.LocationID
";

$query = "$select $whereRegion GROUP BY c.CompanyName ORDER BY {$orderRegion}avgHealth DESC;";

$result = mysqli_query($conn, $query);
$output = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $output[] = $row;
    }
}

echo json_encode($output);

$conn->close();
?>
