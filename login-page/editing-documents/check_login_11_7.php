<?php
$servername="mydb.itap.purdue.edu";

$username="tegley";//yourCAREER/groupusername
$password="#TurboCoder6412!!";//yourgrouppassword
$database=$username;//ITaPsetupdatabasename=yourcareerlogin

$conn=new mysqli($servername,$username,$password,$database);
if($conn->connect_error){
die("Connection failed:" . $conn->connect_error);
}

//Temporary arrays
//$tmp= array("admin1","password123"); //[0]=Username,[1]=Password
$tmp= $_GET['q'];
$tmp=explode(' ',$tmp);
$array_result = array(); //Username is 0; Password is 1

//Query - check if username is in the database
$check_username = "SELECT Username, Password FROM User WHERE Username='" .$tmp[0]. "'";
$result_check_username = mysqli_query($conn, $check_username);
$first_result = mysqli_fetch_array($result_check_username, MYSQLI_NUM); //Capture query result
if ($first_result[0] == NULL) { //Check is username is valid
    $array_result[0] = "0"; //Empty result
    $array_result[1] = "Username is incorrect";
    echo json_encode($array_result);
    $conn->close();
    exit;
}
else {
    //Query - check if password is correct
    $check_password_correct = "SELECT Username, Password FROM User WHERE Username='" .$tmp[0]. "' AND Password='" .$tmp[1]. "'";
    $result_check_password_correct = mysqli_query($conn, $check_password_correct);
    $second_result = mysqli_fetch_array($result_check_password_correct,MYSQLI_NUM); //Capture query result
    if($second_result[0]==NULL) { //Check if password is correct
        $array_result[0] = $tmp[0]; //Username
        $array_result[1] = "Password is incorrect";
        echo json_encode($array_result);
        $conn->close();
        exit;
        }
    else {
        //Query - return role from database
        $sql = "SELECT Role, FullName FROM User WHERE Username='" .$tmp[0]. "'AND Password='" .$tmp[1]. "'";
        $final_result=mysqli_query($conn, $sql);
        $parsed_result = mysqli_fetch_array($final_result, MYSQLI_NUM);
        $array_result[0] = $parsed_result[0]; //Role
        $array_result[1] = $parsed_result[1]; //Full Name
        echo json_encode($array_result);
        $conn->close();
    }
}
?>
