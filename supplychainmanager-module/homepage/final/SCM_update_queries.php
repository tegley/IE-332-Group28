<?php
$servername = "mydb.itap.purdue.edu";

$username = "tegley";//yourCAREER/groupusername
$password = "#TurboCoder6412!!";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

//Always - user_updates[0] = Type, user_updates[1] = CompanyID (initially selected), user_updates[2] = CompanyName, user_updates[3] = TierLevel
//There are also an additional inputs in indecies that vary depend on the selected type

//Temporary variables
$user_updates = $_GET['q'];
$user_updates = explode('|', $user_updates);

if($user_updates[0] == "Manufacturer") { //Case where initially selected company is a manufacuturer - user_updates[4] = FactoryCapacity
    //Update the name and tier
    $update_info = "UPDATE Company SET CompanyName = '{$user_updates[2]}', TierLevel = '{$user_updates[3]}' WHERE CompanyID = {$user_updates[1]}";
    $update_info_result = mysqli_query($conn, $update_info);

    //Update the manufacturing capacity
    $update_capacity = "UPDATE Manufacturer SET FactoryCapacity = '{$user_updates[4]}' WHERE CompanyID = {$user_updates[1]}";
    $update_capacity_result = mysqli_query($conn, $update_capacity);
}

if($user_updates[0] == "DistributorMaintainRoutes" || $user_updates[0] == "Retailer") { //Case where initially selected company is a retailer or distributor iwth no route updates
    //Update the name and tier
    $update_info = "UPDATE Company SET CompanyName = '{$user_updates[2]}', TierLevel = '{$user_updates[3]}' WHERE CompanyID = {$user_updates[1]}";
    $update_info_result = mysqli_query($conn, $update_info);
}

if($user_updates[0] == "DistributorUpdateRoutes") { //Case where initially selected company distributor and the user wants to update a route
    //user_updates[4] = FromCompanyName, user_updates[5] = (prior) ToCompanyName, user_updates[6] = (updated) ToCompanyName 
    //Update route
    $update_route = "UPDATE OperatesLogistics SET ToCompanyID = (SELECT CompanyID FROM Company WHERE CompanyName = '{$user_updates[6]}') 
                    WHERE DistributorID = {$user_updates[1]} 
                    AND FromCompanyID = (SELECT CompanyID FROM Company WHERE CompanyName = '{$user_updates[4]}')
                    AND ToCompanyID = (SELECT CompanyID FROM Company WHERE CompanyName = '{$user_updates[5]}');";
    echo $update_route;
    //$update_route_result = mysqli_query($conn, $update_route);

    //Update the name and tier
    $update_info = "UPDATE Company SET CompanyName = '{$user_updates[2]}', TierLevel = '{$user_updates[3]}' WHERE CompanyID = {$user_updates[1]}";
    $update_info_result = mysqli_query($conn, $update_info);
}

?>
