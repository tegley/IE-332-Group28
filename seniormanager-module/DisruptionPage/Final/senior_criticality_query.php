<?php
$servername = "mydb.itap.purdue.edu";

$username = "g1151938";//yourCAREER/groupusername
$password = "Purdue28";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

//Criticality Query

//Beginn by finding all CompanyID's in the database
$companyQuery = "SELECT c.CompanyID, c.CompanyName FROM Company c;";
$resultcompany = mysqli_query($conn, $companyQuery);
while ($row = mysqli_fetch_array($resultcompany, MYSQLI_ASSOC)) {
    $company[] = $row;
}

//For every CompanyID in the database, compute the criticality for that company
$length = count($company);
$final_results = []; //Overarching of results
for ($i = 0; $i< $length; $i++){ //Loop through all IDs
    $my_i_result = []; //Resetting array
    $companyID = $company[$i]['CompanyID'];
$query_statement = "SELECT c.CompanyName, COUNT( DISTINCT d.DownstreamCompanyID) * (SELECT COUNT(*) FROM Company x 
    JOIN ImpactsCompany i ON i.AffectedCompanyID = x.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID WHERE x.CompanyID = '{$companyID}' AND i.ImpactLevel = 'High') AS Criticality
    FROM Company c JOIN DependsOn d ON d.UpstreamCompanyID = c.CompanyID WHERE c.CompanyID = '{$companyID}';";

//Computing the result
$result = mysqli_query($conn, $query_statement);
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $my_i_result[] = $row;
    }

    //Store results in a unique JSON object
$final_results[$i] = [
    "CompanyID" => $companyID,
    "CompanyName" => $company[$i]['CompanyName'],
    "Criticality" => $my_i_result[0]['Criticality']
    ];
}

//Making JSON Object
$senior_criticality = [
    "criticality" => $final_results
];

echo json_encode($senior_criticality);
   

$conn->close();
?>
