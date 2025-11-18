<?php
$servername="mydb.itap.purdue.edu";

$username="tegley";//yourCAREER/groupusername
$password="#TurboCoder6412!!";//yourgrouppassword
$database=$username;//ITaPsetupdatabasename=yourcareerlogin

$conn=new mysqli($servername,$username,$password,$database);

if($conn->connect_error){
die("Connection failed:" . $conn->connect_error);
}
//Test array - $tmp = array("Smith INC"); 
//Test single input  - $tmp="Wood Ltd";
$tmp= $_GET['q'];
//$tmp=explode(' ',$tmp);
//$tmp="Wood Ltd";
//$sql = "SELECT * FROM Company WHERE CompanyName='{$tmp[0]}'";
$sql = "SELECT c.CompanyID, c.CompanyName, c.LocationID, c.TierLevel, c.Type, f.HealthScore

FROM Company c JOIN FinancialReport f ON c.CompanyID = f.CompanyID

WHERE c.CompanyName ='{$tmp}'

ORDER BY f.RepYear DESC, f.Quarter DESC LIMIT 1";


$result = mysqli_query($conn, $sql);
$rows = [];
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
{
    $rows[] = $row;
}
echo json_encode($rows);
$conn->close();
?>
