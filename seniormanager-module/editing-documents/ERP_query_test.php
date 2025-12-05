<?php
$connection = mysqli_connect("localhost", "root", "password", "myDB");
$id = array(1,2,3);
$new_email = array(1,2,3);
$query = "UPDATE users SET email = '{$new_email[0]}' WHERE id = {$id[0]}";
echo $query;

$servername = "mydb.itap.purdue.edu";

$username = "tegley";//yourCAREER/groupusername
$password = "#TurboCoder6412!!";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

$company = [3, 4];
$length = count($company);
$final_results = []; //Array of results
for ($i = 0; $i< $length; $i++){
$query_statement = "SELECT COUNT( DISTINCT d.DownstreamCompanyID) * (SELECT SUM(CASE WHEN i.ImpactLevel = 'High' THEN 1 ELSE 0 END) FROM Company c 
JOIN ImpactsCompany i ON i.AffectedCompanyID = c.CompanyID JOIN DisruptionEvent e ON e.EventID = i.EventID WHERE c.CompanyID = '{$company[$i]}') 
FROM Company c JOIN DependsOn d ON d.UpstreamCompanyID = c.CompanyID WHERE c.CompanyID = '{$company[$i]}';";
$result = mysqli_query($conn, $query_statement);
echo $query_statement;
while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    $my_i_result[] = $row;
    }
$final_results[i] = $my_i_result;
}
echo $my_i_result[0];
for ($j=0; $j < count($final_results); $j++){
    echo "'{$final_results[$j]}'";
}
echo json_encode($final_results);
?>