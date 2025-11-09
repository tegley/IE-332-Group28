<?php
$servername="mydb.itap.purdue.edu";

$username="tegley";//yourCAREER/groupusername
$password="#Turbo6412!!";//yourgrouppassword
$database=$username;//ITaPsetupdatabasename=yourcareerlogin

$conn=new mysqli($servername,$username,$password,$database);
if($conn->connect_error){
die("Connection failed:" . $conn->connect_error);
}
$tmp= $_GET['q']; //[0]=Username,[1]=Password

//Check if username is in the database
$check_username="SELECT Username FROM User WHERE Username=".$tmp[0];
$result_check_username = mysqli_query($conn, $check_username);

if ($result_check_username == 0) { //Check is username is valid
    echo json_encode("Invalid username");
    $conn->close();
    exit;
}
else {
    $check_password_correct="SELECT Password FROM User WHERE Username=" .$tmp[0]. "AND Password=" .$tmp[1]; //Check if password is correct
    $result_check_password_correct=mysqli_query($conn,$result_check_password_correct);
    if($result_check_password_correct==0) {
        echo json_encode("Invalid password");
        $conn->close();
        exit;
        }
    else { //Find role of the user
        $sql="SELECT Role FROM User WHERE Username=" .$tmp[0]. "AND Password=" .$tmp[1];
        $result=mysqli_query($conn,$sql);
        echo json_encode("$result");
        $conn->close();

    }
}
?>
