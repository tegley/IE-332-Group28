<?php
$servername = "mydb.itap.purdue.edu";

$username = "g1151938";//yourCAREER/groupusername
$password = "Purdue28";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

//Always - $tmp[0] = ID, $tmp[1] = Distributors or Transactions, $tmp[2] = Type (Transactions type), $tmp[3] = CompanyType
$tmp = $_GET['q'];
$tmp = explode('|', $tmp);


if($tmp[1]=='Transactions') {
    $transaction_ids_array = [];
    if($tmp[2] == 'Adjustment'){ //Adjustment
        $transaction_id_list_query = "SELECT DISTINCT(TransactionID) FROM InventoryAdjustment WHERE CompanyID = {$tmp[0]}";
    }

    else if($tmp[2] == 'Shipping'){
        switch($tmp[3]){
            case "Manufacturer": 
                $transaction_id_list_query = "SELECT DISTINCT(TransactionID) FROM Shipping WHERE SourceCompanyID = {$tmp[0]}";
                break;
            case "Distributor": 
                $transaction_id_list_query = "SELECT DISTINCT(TransactionID) FROM Shipping WHERE DistributorID = {$tmp[0]}";
                break;
            case "Retailer": 
                echo "Error - retailers don't have shipping transactions";
                break;
        }
    }
    else if($tmp[2] == 'Receiving'){
        $transaction_id_list_query = "SELECT DISTINCT(TransactionID) FROM Receiving WHERE ReceiverCompanyID = {$tmp[0]}";
    }
    
    //Run the query
    $transaction_id_list_result = mysqli_query($conn, $transaction_id_list_query);
    while ($row = mysqli_fetch_array($transaction_id_list_result, MYSQLI_ASSOC)) {
        $transaction_ids_array[] = $row;
    }

    //JSON object conversion
    $TransactionIDListResults = [
        "TransactionIDs"=> $transaction_ids_array
        ];

    echo json_encode($TransactionIDListResults);
}


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

    
