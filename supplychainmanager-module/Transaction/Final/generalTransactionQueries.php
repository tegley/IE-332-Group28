<?php
$servername = "mydb.itap.purdue.edu";

$username = "g1151938";//yourCAREER/groupusername
$password = "Purdue28";//yourgrouppassword
$database = $username;//ITaPsetupdatabasename=yourcareerlogin

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}

$tmp = $_GET['q'];

// Convert the comma-delimited string into an array of strings.
$tmp = explode(',', $tmp);
// print_r($tmp);



//We will need to build the queries per user selection, but the added constraints will be the same accross all queries, so we will make additions rn
$whereState = ""; //If user specifies date range



    //Time range contstraint added per user input
    if (!empty($tmp[0])) { //Adding appropriate Having if user inputa specific company/location/etc. Empty built in function check if tmp[o] has no value
        $whereState = "WHERE t.TransactionID = '" . $tmp[0] . "'"; //User specified transaction of Interest
         }

    //What type of transaction is it?
    if (!empty($tmp[1])) { //Make sure not empty
        if (($tmp[1]) == 'Shipping') { //Determine Query based on type of Transaction
            $select = "SELECT c.CompanyName, s.ShipmentID, s.Quantity, s.ActualDate, l.City, l.CountryName, l.ContinentName, p.ProductID FROM Shipping s JOIN Product p ON s.ProductID = p.ProductID JOIN Company c ON s.SourceCompanyID = c.CompanyID LEFT JOIN Location l ON l.LocationID = c.LocationID JOIN InventoryTransaction t ON s.TransactionID = t.TransactionID"; //User specified transaction of Interest
         }
        if (($tmp[1]) == 'Receiving') { //Determine Query based on type of Transaction
            $select = "SELECT c.CompanyName, r.ReceivingID, r.ReceivedDate, r.QuantityReceived, l.City, l.CountryName, l.ContinentName, p.ProductID
            FROM Receiving r JOIN Company c ON r.ReceiverCompanyID = c.CompanyID JOIN Location l ON l.LocationID = c.LocationID JOIN InventoryTransaction t ON r.TransactionID = t.TransactionID JOIN Shipping s ON s.ShipmentID = r.ShipmentID
            JOIN Product p ON s.ProductID = p.ProductID"; //User specified transaction of Interest
        }
         }

    //Put together and return Query Result
    $query = "{$select} {$whereState};";
    // echo $query;
    //Execute the SQL query
    $result = mysqli_query($conn, $query);
    // Convert the table into individual rows and reformat.
    $answer = []; //Creating shipping Array
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $answer[] = $row;
    }
    echo json_encode($answer);

   



   

$conn->close();
?>
