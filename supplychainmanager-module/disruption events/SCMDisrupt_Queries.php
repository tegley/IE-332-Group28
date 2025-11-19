<?php
$servername = "mydb.itap.purdue.edu";

$username = "";//yourCAREER/groupusername
$password = "";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

//https://web.ics.purdue.edu/~cox447/SCMDisruptionEventQueries.php?q=2020-01-01,2025-09-30&g=Continent
$tmp = $_GET['q']; //Date range
$quer = $_GET['g']; //User specified filters

// Convert the comma-delimited string into an array of strings.
$tmp = explode(',', $tmp);
// print_r($tmp);
$quer = explode(',', $quer); //["drop down", "user input", "Continent/Tier" WHEN USER SPECIFIES TIER AND REGION ]
// print_r($tmp);


    
    // echo json_encode($companyInfo);

    
        $SelectsQuery = "SELECT COUNT(*) AS NumEvents, ROUND(AVG(d.EventRecoveryDate - d.EventDate), 2) AS ART, SUM(d.EventRecoveryDate - d.EventDate) AS TD, SUM(CASE WHEN i.ImpactLevel = 'High' THEN 1 ELSE 0 END) AS NumHighImpact, SUM(CASE WHEN i.ImpactLevel = 'Medium' THEN 1 ELSE 0 END) AS NumMedImpact, SUM(CASE WHEN i.ImpactLevel = 'Low' THEN 1 ELSE 0 END) AS NumLowImpact
        FROM DisruptionEvent d JOIN DisruptionCategory x ON d.CategoryID = x.CategoryID JOIN ImpactsCompany i ON d.EventID = i.EventID JOIN Company c ON i.AffectedCompanyID = c.CompanyID LEFT JOIN Location l ON l.LocationID = c.LocationID";

        //Where switch statement
        $whereState1 = "";
        $whereState2 = "WHERE d.EventDate BETWEEN '" . $tmp[0] . "' AND '" . $tmp[1] . "'";
        $groupState1 = "";

        //GRoup by statement
        if (!isset($quer[1]) || $quer[1] === "") {
        switch ($quer[0]) {
            case "Company":
                $groupState1 = "GROUP BY c.CompanyName;";
                break;
            case "Tier":
                $groupState1 = "GROUP BY c.TierLevel;";
                break;
            case "Country":
                $groupState1 = "GROUP BY l.CountryName;";
                break;
            case "Continent":
                $groupState1 = "GROUP BY l.ContinentName;";
                break;
            case "Country and Tier":
                $groupState1 = "GROUP BY c.TierLevel, l.CountryName;";
                break;
            case "Continent and Tier":
                $groupState1 = "GROUP BY c.TierLevel, l.ContinentName;";
                break;
            default:
            $groupState1 = ";";
        }
        }
        else {
           switch ($quer[0]) {
            case "Company":
                $whereState1 = "AND c.CompanyName = '" . $quer[1] . "';";
                break;
            case "Tier":
                $whereState1 = "AND c.TierLevel = '" . $quer[1] . "';";
                break;
            case "Country":
                $whereState1 = "AND l.CountryName = '" . $quer[1] . "';";
                break;
            case "Continent":
                $whereState1 = "AND l.ContinentName = '" . $quer[1] . "';";
                break;
            case "Country and Tier":
                $whereState1 = "AND c.TierLevel = '" . $quer[1] . "' AND l.CountryName = '" . $quer[2] . "';";
                break;
            case "Continent and Tier":
                $whereState1 = "AND c.TierLevel = '" . $quer[1] . "' AND l.ContinentName = '" . $quer[2] . "';";
                break;
            default:
                ";";
        } 
        }

        $disQuery = "{$SelectsQuery} {$whereState2} {$whereState1} {$groupState1}";
        echo $disQuery;
        //Execute the SQL query
        $resultDis = mysqli_query($conn, $disQuery);
        // Convert the table into individual rows and reformat.
        $info = []; //Creating shipping Array
        while ($row = mysqli_fetch_array($resultDis, MYSQLI_ASSOC)) {
        $info[] = $row;
        }
        echo json_encode($info);

        //turnary operator

    
    


    //Making JSON Object
    $SCMHomePageCompanyResults = [
        "companyInfo" => $companyInfo,
        "distRoutes" => $distRoutes,
        "productsSupplied" => $productsSupplied,
        "productDiversity" => $productDiversity,
        "dependedOn" => $dependedOn,
        "dependsOn" => $dependsOn,
        "shipping" => $shipping,
        "receivings" => $receivings,
        "adjustments" => $adjustments
    ];

    // echo json_encode($SCMHomePageCompanyResults);

   

$conn->close();
?>
