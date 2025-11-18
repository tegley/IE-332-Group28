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

// function BasicCompanyInfoQueries($tmp, $conn)
// {
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
        $BasicInfoSQL .= ", m.FactoryCapacity FROM Company c JOIN FinancialReport f ON c.CompanyID = f.CompanyID JOIN Manufacturer m ON c.CompanyID = m.CompanyID WHERE c.CompanyName = '" . $tmp . "' ORDER BY f.RepYear DESC, f.Quarter DESC LIMIT 1;";
    }
    else{
        $BasicInfoSQL .= " FROM Company c JOIN FinancialReport f ON c.CompanyID = f.CompanyID WHERE c.CompanyName = '" . $tmp . "' ORDER BY f.RepYear DESC, f.Quarter DESC LIMIT 1;";
    }

    $basicCompanyInfo = mysqli_query($conn, $BasicInfoSQL);
    // echo $BasicInfoSQL;

    // Convert the table into individual rows and reformat.
    $companyInfo = []; //Making Basic COmpany Info Array
    while ($row = mysqli_fetch_array($basicCompanyInfo, MYSQLI_ASSOC)) {
        $companyInfo[] = $row;
    }
    // echo json_encode($companyInfo);

    $distRoutes = []; //Creating Distributor Routes Array so that it will always be in JSON object
    if (strcmp($result[0], 'Distributor') == 0) {
        $distRoutesQuery = "SELECT o.FromCompanyID, o.ToCompanyID FROM Company c JOIN OperatesLogistics o ON c.CompanyID = o.DistributorID WHERE c.CompanyName = '" . $tmp . "';";
        // echo $distRoutesQuery;
        //Execute the SQL query
        $resultdistRoutes = mysqli_query($conn, $distRoutesQuery);
        // Convert the table into individual rows and reformat.
        while ($row = mysqli_fetch_array($resultdistRoutes, MYSQLI_ASSOC)) {
            $distRoutes[] = $row;
        }
        // echo json_encode($distRoutes);

    }
    

    //Queries that always run
    $productsSuppliedQuery = "SELECT p.ProductID, p.ProductName FROM Product p JOIN SuppliesProduct s ON p.ProductID = s.ProductID JOIN Company c ON s.SupplierID = c.CompanyID  WHERE c.CompanyName = '" . $tmp . "';";
    // echo $productsSuppliedQuery;
        //Execute the SQL query
    $resultproductsSupplied = mysqli_query($conn, $productsSuppliedQuery);
    // Convert the table into individual rows and reformat.
    $productsSupplied = []; //Creating Product Supplied Array
    while ($row = mysqli_fetch_array($resultproductsSupplied, MYSQLI_ASSOC)) {
        $productsSupplied[] = $row;
    }
    // echo json_encode($productsSupplied);


    $productDiversityQuery = "SELECT p.Category, COUNT(*) FROM Product p JOIN SuppliesProduct s ON p.ProductID = s.ProductID JOIN Company c ON s.SupplierID = c.CompanyID WHERE c.CompanyName = '" . $tmp . "'
    GROUP BY p.Category ORDER BY p.Category";
    // echo $productDiversityQuery;
    //Execute the SQL query
    $resultproductDiversity = mysqli_query($conn, $productDiversityQuery);
    // Convert the table into individual rows and reformat.
    $productDiversity = []; //Creating Product Diversity Array
    while ($row = mysqli_fetch_array($resultproductDiversity, MYSQLI_ASSOC)) {
        $productDiversity[] = $row;
    }
    // echo json_encode($productDiversity);
    
    $dependedOnQuery = "SELECT DISTINCT d.DownStreamCompanyID FROM Company c JOIN DependsOn d ON c.companyid = d.UpStreamCompanyID WHERE c.CompanyName = '" . $tmp . "';";
    // echo $dependedOnQuery;
    //Execute the SQL query
    $resultdependedOn = mysqli_query($conn, $dependedOnQuery);
    // Convert the table into individual rows and reformat.
    $dependedOn = []; //Creeating Depended On Array
    while ($row = mysqli_fetch_array($resultdependedOn, MYSQLI_ASSOC)) {
        $dependedOn[] = $row;
    }
    // echo json_encode($dependedOn);

    $dependsOnQuery = "SELECT DISTINCT d.UpStreamCompanyID FROM Company c JOIN DependsOn d ON c.companyid = d.DownStreamCompanyID  WHERE c.CompanyName = '" . $tmp . "';";
    // echo $dependsOnQuery;
    //Execute the SQL query
    $resultdependsOn = mysqli_query($conn, $dependsOnQuery);
    // Convert the table into individual rows and reformat.
    $dependsOn = []; //Creating Depends on Array
    while ($row = mysqli_fetch_array($resultdependsOn, MYSQLI_ASSOC)) {
        $dependsOn[] = $row;
    }
    // echo json_encode($dependsOn);

    $SCMHomePageCompanyResults = [
        "companyInfo" => $companyInfo,
        "distRoutes" => $distRoutes,
        "productsSupplied" => $productsSupplied,
        "productDiversity" => $productDiversity,
        "dependedOn" => $dependedOn,
        "dependsOn" => $dependsOn
    ];

    echo json_encode($SCMHomePageCompanyResults);

    // //Queries for Key Performance
    // $shipmentDetailsQuery = "SELECT AVG(s.ActualDate - s.PromisedDate), STDDEV(s.ActualDate - s.PromisedDate), COUNT(*) FROM Company c JOIN Shipping s ON c.CompanyID = s.SourceCompanyID 
    // WHERE CompanyName = '" . $tmp . "' AND s.ActualDate BETWEEN '2020-01-01' AND '2025-09-30' AND s.ActualDate <= s.PromisedDate;";
    // echo $shipmentDetailsQuery;
    // $totalShipmentsQuery = "SELECT COUNT(*) FROM Company c JOIN Shipping s ON c.CompanyID = s.SourceCompanyID
    // WHERE CompanyName = '" . $tmp . "' AND s.ActualDate BETWEEN '2020-01-01' AND '2025-09-30';";
    // echo $totalShipmentsQuery;
    // $pastHealthScores = "SELECT f.HealthScore FROM Company c JOIN FinancialReport f ON c.CompanyID = f.CompanyID WHERE c.CompanyName = '" . $tmp . "' ORDER BY f.RepYear DESC, f.Quarter DESC LIMIT 5;";
    // echo $pastHealthScores;
    // $disruptionEvents = "SELECT d.EventID, x.CategoryName, d.EventDate, d.EventRecoveryDate, x.Description FROM DisruptionEvent d JOIN DisruptionCategory x ON d.CategoryID = x.CategoryID JOIN ImpactsCompany i ON d.EventID = i.EventID JOIN Company c ON i.AffectedCompanyID = c.CompanyID
    // WHERE c.CompanyName = '" . $tmp . "' GROUP BY x.CategoryName;";
    // echo $disruptionEvents;
// }

$conn->close();
?>
