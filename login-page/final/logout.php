<?php
session_start(); //Start the session

//Unset session variables
unset($_SESSION['username']);
unset($_SESSION['loggedin']);
unset($_SESSION['FullName']);

session_destroy(); //Destroy the session

//Redirect to the index page
header("Location: index.php");
exit;
?>
