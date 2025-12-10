<?php
$servername = "mydb.itap.purdue.edu";

$username = "g1151938";//yourCAREER/groupusername
$password = "Purdue28";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

//If $case = 'Update Company Info'
//user_updates[0] = Type, user_updates[1] = CompanyID (initially selected), user_updates[2] = CompanyName, user_updates[3] = TierLevel

//If $case = 'Update Transactions'; 
//user_updates[0] = Transaction Type/Table to use -> build query based off of the type and insert data where the TransactionID = user_updates[1]

//Temporary variables
$user_updates = $_GET['q'];
$case = $_GET['g'];

$user_updates = explode('|', $user_updates);
$case = explode('|', $case);
if($case[0] == 'Update Company Info'){
    if($user_updates[0] == "Manufacturer") { //Case where initially selected company is a manufacuturer - user_updates[4] = FactoryCapacity
        //Update the name and tier
        $update_info = "UPDATE Company SET CompanyName = '{$user_updates[2]}', TierLevel = '{$user_updates[3]}' WHERE CompanyID = {$user_updates[1]}";
        $update_info_result = mysqli_query($conn, $update_info);

        //Update the manufacturing capacity
        $update_capacity = "UPDATE Manufacturer SET FactoryCapacity = '{$user_updates[4]}' WHERE CompanyID = {$user_updates[1]}";
        $update_capacity_result = mysqli_query($conn, $update_capacity);
    }

    if($user_updates[0] == "DistributorMaintainRoutes" || $user_updates[0] == "Retailer") { //Case where initially selected company is a retailer or distributor with no route updates
        //Update the name and tier
        $update_info = "UPDATE Company SET CompanyName = '{$user_updates[2]}', TierLevel = '{$user_updates[3]}' WHERE CompanyID = {$user_updates[1]}";
        $update_info_result = mysqli_query($conn, $update_info);
    }

    if($user_updates[0] == "DistributorUpdateRoutes") { //Case where initially selected company distributor and the user wants to update a route
        //Update route
        $update_route = "UPDATE OperatesLogistics SET ToCompanyID = (SELECT CompanyID FROM Company WHERE CompanyName = '{$user_updates[6]}') 
                        WHERE DistributorID = {$user_updates[1]} 
                        AND FromCompanyID = (SELECT CompanyID FROM Company WHERE CompanyName = '{$user_updates[4]}')
                        AND ToCompanyID = (SELECT CompanyID FROM Company WHERE CompanyName = '{$user_updates[5]}');";
        $update_route_result = mysqli_query($conn, $update_route);

        //Update the name and tier
        $update_info = "UPDATE Company SET CompanyName = '{$user_updates[2]}', TierLevel = '{$user_updates[3]}' WHERE CompanyID = {$user_updates[1]}";
        $update_info_result = mysqli_query($conn, $update_info);
    }
    echo "Successful company update!";
    $conn->close();
    exit();
}

if($case[0] == 'Update Transactions'){
    if($user_updates[0] == "Shipping") { //Case where the user wants to update a shipping transaction
        $update_transactions = "UPDATE Shipping SET ProductID = {$user_updates[2]}, Quantity = {$user_updates[3]}, PromisedDate = '{$user_updates[4]}', ActualDate = '{$user_updates[5]}' WHERE TransactionID = {$user_updates[1]}";
        $update_transactions_result = mysqli_query($conn, $update_transactions);
    }

    if($user_updates[0] == "Receiving") { //Case where the user wants to update a receiving transaction
        $update_transactions = "UPDATE Receiving SET QuantityReceived = {$user_updates[2]}, ReceivedDate = '{$user_updates[3]}' WHERE TransactionID = {$user_updates[1]}";
        $update_transactions_result = mysqli_query($conn, $update_transactions);
    }

    if($user_updates[0] == "Adjustment") { //Case where the user wants to update an adjustment
        $update_transactions = "UPDATE InventoryAdjustment SET ProductID = {$user_updates[2]}, QuantityChange = {$user_updates[3]}, AdjustmentDate = '{$user_updates[4]}', Reason = '{$user_updates[5]}' WHERE TransactionID = {$user_updates[1]}";
        $update_transactions_result = mysqli_query($conn, $update_transactions);
    }
    echo "Successful transactions update!";
    $conn->close();
    exit();
}
?>
