<?php
$servername = "mydb.itap.purdue.edu";

$username = "";//yourCAREER/groupusername
$password = "";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

$tmp = $_GET['q'];
// $tmp=explode(' ',$tmp);

function BasicCompanyInfoQueries($tmp, $conn)
{
    $checksql = "SELECT c.Type, c.CompanyID FROM Company c WHERE c.CompanyName = '" . $tmp . "';";
    // echo $checksql;
    $result = mysqli_query($conn, $checksql);
    $result = $result->fetch_row();
    // echo json_encode($rows);
    // $result = mysqli_query($conn, $query);
    // echo $result;
    // echo count($result);
    //Check company type to form query

    // echo $result[0];
    // echo $result[1];

    $BasicInfoSQL = "SELECT c.CompanyID, c.CompanyName, c.LocationID, c.TierLevel, c.Type, f.HealthScore";

    if (count($result) == 0) { //Does company exist?
        echo "Company Not Found";
        $conn->close();
        exit;
    }
    if (strcmp($result[0], 'Manufacturer') == 0) {
        $BasicInfoSQL .= ", m.FactoryCapacity";
    }
    $BasicInfoSQL .= " FROM Company c JOIN FinancialReport f ON c.CompanyID = f.CompanyID JOIN Manufacturer m ON c.CompanyID = m.CompanyID WHERE c.CompanyName = '" . $tmp . "' ORDER BY f.RepYear DESC, f.Quarter DESC LIMIT 1;";

    // $basicCompanyInfo = mysqli_query($conn, $BasicInfoSQL);
    echo $BasicInfoSQL;
    // return "";
    if (strcmp($result[0], 'Distributor') == 0) {
        $distRoutesQuery = "SELECT o.FromCompanyID, o.ToCompanyID FROM Company c JOIN OperatesLogistics o ON c.CompanyID = o.DistributorID WHERE c.CompanyName = '" . $tmp . "';";
    }
    echo $distRoutesQuery;

    //Queries that always run
    $productsSuppliedQuery = "SELECT p.ProductID, p.ProductName FROM Product p JOIN SuppliesProduct s ON p.ProductID = s.ProductID JOIN Company c ON s.SupplierID = c.CompanyID  WHERE c.CompanyName = '" . $tmp . "';";
    echo $productsSuppliedQuery;
    $productDiversityQuery = "SELECT p.Category FROM Product p JOIN SuppliesProduct s ON p.ProductID = s.ProductID JOIN Company c ON s.SupplierID = c.CompanyID   WHERE c.CompanyName = '" . $tmp . "';";
    echo $productDiversityQuery;
    $dependedOnQuery = "SELECT DISTINCT d.DownStreamCompanyID FROM Company c JOIN DependsOn d ON c.companyid = d.UpStreamCompanyID WHERE c.CompanyName = '" . $tmp . "';";
    echo $dependedOnQuery;
    $dependsOnQuery = "SELECT DISTINCT d.UpStreamCompanyID FROM Company c JOIN DependsOn d ON c.companyid = d.DownStreamCompanyID  WHERE c.CompanyName = '" . $tmp . "';";
    echo $dependsOnQuery;

    //Queries for Key Performance

}

$query = BasicCompanyInfoQueries($tmp, $conn);


$result = mysqli_query($conn, $query);
$rows = [];
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $rows[] = $row;
}
echo json_encode($rows);
$conn->close();
?>
