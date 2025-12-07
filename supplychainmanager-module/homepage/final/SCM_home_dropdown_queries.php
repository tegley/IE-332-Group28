<?php
$servername = "mydb.itap.purdue.edu";

$username = "tegley";//yourCAREER/groupusername
$password = "#TurboCoder6412!!";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

//Always - $tmp[0] = ID, $tmp[1] = Distributors or Transactions
$tmp = $_GET['q'];
$tmp = explode('|', $tmp);


if($tmp[1]=='Distributor') {
    $from_company_name = "SELECT DISTINCT(c.CompanyName) FROM Company c JOIN OperatesLogistics o ON c.CompanyID = o.FromCompanyID WHERE o.DistributorID = {$tmp[0]}";
    $from_company_name_result = mysqli_query($conn, $from_company_name);

    $to_company_name = "SELECT DISTINCT(c.CompanyName) FROM Company c JOIN OperatesLogistics o ON c.CompanyID = o.ToCompanyID WHERE o.DistributorID = {$tmp[0]}";
    $to_company_name_result = mysqli_query($conn, $to_company_name);

    $update_to_company_name = "SELECT CompanyName FROM Company WHERE Type != 'Distributor';";
    $update_to_company_name_result = mysqli_query($conn, $update_to_company_name);

    while ($row = mysqli_fetch_array($from_company_name_result, MYSQLI_ASSOC)) {
        $from_name_array[] = $row;
    }

    while ($row = mysqli_fetch_array($to_company_name_result, MYSQLI_ASSOC)) {
        $to_name_array[] = $row;
    }

    
    while ($row = mysqli_fetch_array($update_to_company_name_result, MYSQL_ASSOC)) {
        $update_to_name_array[] = $row;
    }

    $DistributorDropdownResults = [
        "FromCompany"=> $from_name_array,
        "ToCompany" => $to_name_array,
        "UpdateToCompany" => $update_to_name_array
        ];

    echo json_encode($DistributorDropdownResults);
}

$conn->close();
?>

    
