<?php
$servername = "mydb.itap.purdue.edu";

$username = "g1151938";//yourCAREER/groupusername
$password = "Purdue28";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}
$todays_date = date('Y-m-d');
$alerts_query = "SELECT e.EventID, x.CategoryName, e.EventDate, e.EventRecoveryDate FROM DisruptionEvent e JOIN DisruptionCategory x ON e.CategoryID = x.CategoryID 
                WHERE '{$todays_date}' >= e.EventDate AND (e.EventRecoveryDate > '{$todays_date}' OR e.EventRecoveryDate IS NULL) ORDER BY e.EventDate ASC;";

$result_alerts = mysqli_query($conn, $alerts_query);

while ($row = mysqli_fetch_array($result_alerts, MYSQLI_ASSOC)) {
    $events[] = $row;
}

$SCM_alerts = [
    "Ongoing"=> $events,
];

echo json_encode($SCM_alerts);

?>
