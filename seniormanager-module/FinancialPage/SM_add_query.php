<?php
$servername = "mydb.itap.purdue.edu";

$username = "g1151938";//yourCAREER/groupusername
$password = "Purdue28";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

//Always - user_insert[0] = CompanyName, user_insert[1] = Type, user_insert[2] = TierLevel, user_insert[3] = City, user_insert[4] = CountryName, 

//Temporary variables
$user_insert = $_GET['q'];
$user_insert = explode('|', $user_insert);

//String handling on company name
$CompanyName = "{$user_insert[0]}";

//Check if a company with the name already exists in the database
$add_company_check_query = "SELECT EXISTS (SELECT 1 FROM Company WHERE CompanyName = '$CompanyName') AS CompanyCheck";
$add_company_check_query_result = mysqli_query($conn, $add_company_check_query);
$add_company_array = mysqli_fetch_array($add_company_check_query_result, MYSQLI_NUM);
if ($add_company_array[0] == 1){
    echo "Company already exists";
    $conn->close();
    exit();
}

else{
    //Get LocationID from city & country input
    $location_id_query = "SELECT LocationID FROM Location WHERE City = '{$user_insert[3]}' AND CountryName = '{$user_insert[4]}'";
    $location_id_result = mysqli_query($conn, $location_id_query);
    $location_id_result_array = mysqli_fetch_array($location_id_result, MYSQLI_NUM);
    $location_id = (int) $location_id_result_array[0];

    //Run add company insert statement
    $add_company_query = "INSERT INTO Company (CompanyName, Type, TierLevel, LocationID) VALUES ('$CompanyName' , '{$user_insert[1]}' , '{$user_insert[2]}', $location_id)";
    $add_company = mysqli_query($conn, $add_company_query);
    echo "Company was successfully inserted into the database";
    $conn->close();
}
?>
