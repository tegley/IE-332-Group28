<?php
/* ============================================================
   FIX #0 — Ensure JSON output, not HTML warnings
   Her original file allowed PHP warnings to print BEFORE JSON.
   That broke your JavaScript because JSON became invalid.

   Now we FORCE JSON header.
============================================================ */
header('Content-Type: application/json');

/* ============================================================
   DATABASE CONNECTION
============================================================ */
$servername = "mydb.itap.purdue.edu";
$username   = "cox447";
$password   = "LunaZuna704";
$database   = $username;

/*
    FIX #1 — Suppress raw PHP warnings with @

    Her original line:
        $conn = new mysqli(...)
    If connection fails, PHP prints warnings like:
        php_network_getaddresses: getaddrinfo failed

    Those warnings BREAK JSON.

    With @, we catch the error manually and return proper JSON.
*/
$conn = @new mysqli($servername, $username, $password, $database);

/* ============================================================
   FIX #2 — Handle DB failure with CLEAN JSON output
============================================================ */
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

/* ============================================================
   READ USER INPUT
============================================================ */
$tmp  = isset($_GET['q']) ? explode(',', $_GET['q']) : ["", ""];
$quer = isset($_GET['g']) ? explode(',', $_GET['g']) : [""];

/*
    FIX #3 — Always initialize these variables

    Her original code NEVER defined:
      $whereStateShip
      $whereStateRec
      $whereStateAdj

    So when PHP reached:
        "{$shippingSelect} {$whereStateShip}"
    those variables DID NOT EXIST.

    This caused warnings:
        "Undefined variable: whereStateShip"
        "Undefined variable: adjustmentsQuery"

    Now we define them upfront.
*/
$whereStateShip = "";
$whereStateRec  = "";
$whereStateAdj  = "";
$groupState     = "";

/* ============================================================
   GROUP BY LOGIC
============================================================ */
if (!empty($quer[0])) {

    /*
        FIX #4 — Her original condition was wrong:

            if (isset($quer[0]) || $quer[0] != "")

        This condition is ALWAYS TRUE,
        so groupState ALWAYS had a value.

        Now we use !empty() correctly.
    */

    switch ($quer[0]) {
        case "Company":
            $groupState = "GROUP BY c.CompanyName";
            break;

        case "Country":
            $groupState = "GROUP BY l.CountryName";
            break;

        case "Continent":
            $groupState = "GROUP BY l.ContinentName";
            break;

        default:
            $groupState = ""; // Safe fallback
    }
}

/* ============================================================
   DATE RANGE FILTERS
   FIX #5 — Escape user input (SQL safety + reliability)
============================================================ */
if (!empty($tmp[0])) {

    // Prevent SQL injection + invalid SQL
    $start = $conn->real_escape_string($tmp[0]);
    $end   = $conn->real_escape_string($tmp[1]);

    $whereStateShip = "WHERE s.ActualDate BETWEEN '$start' AND '$end'";
    $whereStateRec  = "WHERE r.ReceivedDate BETWEEN '$start' AND '$end'";
    $whereStateAdj  = "WHERE a.AdjustmentDate BETWEEN '$start' AND '$end'";
}

/* ============================================================
   FIX #6 — Helper function to avoid repeated code

   Her original code repeated:
       $result = mysqli_query()
       while loop fetch

   AND she attempted to use $adjustmentsQuery BEFORE defining it.

   This helper avoids that mistake entirely.
============================================================ */
function fetchAll($conn, $query) {
    $result = mysqli_query($conn, $query);
    if (!$result) return []; // Do not break JSON
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/* ============================================================
   ORIGINAL QUERIES — UNMODIFIED
   (only fetchAll() and input validation changed)
============================================================ */

/* ---------------- SHIPPING ---------------- */
$shippingSelect = "SELECT s.ShipmentID, s.ActualDate, s.PromisedDate, p.ProductID, p.ProductName,
s.SourceCompanyID, s.DestinationCompanyID, s.DistributorID, s.TransactionID
FROM Shipping s JOIN Product p ON s.ProductID = p.ProductID
JOIN Company c ON s.SourceCompanyID = c.CompanyID
LEFT JOIN Location l ON l.LocationID = c.LocationID";

$shippingQuery = "$shippingSelect $whereStateShip $groupState;";
$shipping = fetchAll($conn, $shippingQuery);

/* ---------------- RECEIVINGS ---------------- */
$receivingsSelect = "SELECT r.ReceivingID, r.ReceivedDate, r.QuantityReceived, p.ProductID,
r.ShipmentID, p.ProductName, c.CompanyName, r.TransactionID
FROM Receiving r
JOIN Company c ON r.ReceiverCompanyID = c.CompanyID
JOIN Shipping s ON r.ShipmentID = s.ShipmentID
JOIN Product p ON s.ProductID = p.ProductID
LEFT JOIN Location l ON l.LocationID = c.LocationID";

$receivingsQuery = "$receivingsSelect $whereStateRec $groupState;";
$receivings = fetchAll($conn, $receivingsQuery);

/* ---------------- ADJUSTMENTS ---------------- */
/*
    FIX #7 — Her original file COMMENTED OUT $adjustmentsQuery:
        //$adjustmentsQuery = "..."

    But then she STILL used $adjustmentsQuery below.

    That caused:
        mysqli_query(): Empty query
        mysqli_fetch_array(): bool given

    Now we always define it.
*/
$adjustmentsSelect = "SELECT a.AdjustmentID, a.AdjustmentDate, p.ProductID, a.QuantityChange,
c.CompanyName, a.TransactionID
FROM InventoryAdjustment a
JOIN Company c ON a.CompanyID = c.CompanyID
JOIN Product p ON a.ProductID = p.ProductID
LEFT JOIN Location l ON l.LocationID = c.LocationID";

$adjustmentsQuery = "$adjustmentsSelect $whereStateAdj $groupState;";
$adjustments = fetchAll($conn, $adjustmentsQuery);

/* ---------------- LEAVING COMPANY ---------------- */
$leavingCompanySelect = "SELECT c.CompanyName, s.ShipmentID, s.ActualDate, s.PromisedDate, p.ProductID,
p.ProductName, s.SourceCompanyID, s.DestinationCompanyID, s.DistributorID, s.TransactionID,
r.ReceivingID, r.ReceivedDate, r.QuantityReceived, a.AdjustmentID, a.AdjustmentDate, a.QuantityChange
FROM Shipping s
JOIN Product p ON s.ProductID = p.ProductID
JOIN Company c ON s.SourceCompanyID = c.CompanyID
LEFT JOIN Location l ON l.LocationID = c.LocationID
JOIN InventoryTransaction t ON s.TransactionID = t.TransactionID
LEFT JOIN Receiving r ON r.ShipmentID = s.ShipmentID
LEFT JOIN InventoryAdjustment a ON a.TransactionID = t.TransactionID";

$orderByState = "ORDER BY c.CompanyName;";
$leavingCompanyQuery = "$leavingCompanySelect $whereStateAdj $groupState $orderByState";
$leavingCompany = fetchAll($conn, $leavingCompanyQuery);

/* ---------------- ARRIVING COMPANY ---------------- */
$arrivingCompanySelect = "SELECT c.CompanyName, r.ReceivingID, r.ReceivedDate, r.QuantityReceived,
s.ShipmentID, s.ActualDate, s.PromisedDate, p.ProductID, p.ProductName,
s.SourceCompanyID, s.DestinationCompanyID, s.DistributorID, s.TransactionID,
a.AdjustmentID, a.AdjustmentDate, a.QuantityChange
FROM Receiving r
JOIN Company c ON r.ReceiverCompanyID = c.CompanyID
LEFT JOIN Location l ON l.LocationID = c.LocationID
JOIN InventoryTransaction t ON r.TransactionID = t.TransactionID
LEFT JOIN Shipping s ON r.ShipmentID = s.ShipmentID
JOIN Product p ON s.ProductID = p.ProductID
LEFT JOIN InventoryAdjustment a ON a.TransactionID = r.TransactionID";

$arrivingCompanyQuery = "$arrivingCompanySelect $whereStateAdj $groupState $orderByState";
$arrivingCompany = fetchAll($conn, $arrivingCompanyQuery);

/* ---------------- DISTRIBUTOR ---------------- */
$distributorQuery = "SELECT d.CompanyID AS DistributorID, c.CompanyName,
COUNT(DISTINCT s.ShipmentID) AS ShipmentVolume,
ROUND(((SUM(CASE WHEN s.ActualDate <= s.PromisedDate THEN 1 ELSE 0 END)
       / COUNT(DISTINCT s.ShipmentID)) * 100), 2) AS OTRate,
p.ProductName, p.ProductID
FROM Distributor d
JOIN Company c ON d.CompanyID = c.CompanyID
JOIN Shipping s ON s.DistributorID = d.CompanyID
JOIN Product p ON s.ProductID = p.ProductID
GROUP BY d.CompanyID, c.CompanyName;";

$distributor = fetchAll($conn, $distributorQuery);

/* ---------------- PRODUCTS HANDLED ---------------- */
$productsHandledSelect = "SELECT d.CompanyID, c.CompanyName, p.ProductName, p.ProductID, x.ProductCount
FROM Distributor d
JOIN Company c ON d.CompanyID = c.CompanyID
JOIN Shipping s ON s.DistributorID = d.CompanyID
JOIN Product p ON p.ProductID = s.ProductID
JOIN (
    SELECT d.CompanyID, COUNT(DISTINCT p.productID) AS ProductCount
    FROM Shipping s
    JOIN Distributor d ON s.DistributorID = d.CompanyID
    JOIN Product p ON p.ProductID = s.ProductID
    GROUP BY d.CompanyID
) x ON x.CompanyID = d.CompanyID";

$productsHandledQuery = "$productsHandledSelect GROUP BY p.ProductName, p.ProductID ORDER BY d.CompanyID, c.CompanyName;";
$productsHandled = fetchAll($conn, $productsHandledQuery);

/* ---------------- SHIPMENTS OUTSTANDING ---------------- */
$shipmentsOutstandingSelect = "SELECT d.CompanyID, c.CompanyName,
(CASE WHEN s.ActualDate IS NULL THEN 1 ELSE 0 END) AS CurrentlyOut
FROM Distributor d
JOIN Company c ON d.CompanyID = c.CompanyID
JOIN Shipping s ON s.DistributorID = d.CompanyID";

$shipmentsOutstandingQuery = "$shipmentsOutstandingSelect $whereStateShip $groupState;";
$shipmentsOutstanding = fetchAll($conn, $shipmentsOutstandingQuery);

/* ---------------- DISRUPTION EVENT ---------------- */
$disruptionEventSelect = "SELECT d.CompanyID, c.CompanyName, e.EventID, s.ShipmentID, s.TransactionID, y.CategoryName
FROM Distributor d
JOIN Company c ON d.CompanyID = c.CompanyID
JOIN Shipping s ON s.DistributorID = d.CompanyID
LEFT JOIN Location l ON l.LocationID = c.LocationID
JOIN ImpactsCompany i ON i.AffectedCompanyID = d.CompanyID
JOIN DisruptionEvent e ON e.EventID = i.EventID
JOIN DisruptionCategory y ON y.CategoryID = e.CategoryID";

$disruptionEventQuery = "$disruptionEventSelect $whereStateShip AND s.ActualDate BETWEEN e.EventDate AND e.EventRecoveryDate $groupState ORDER BY d.CompanyID, c.CompanyName;";
$disruptionEvent = fetchAll($conn, $disruptionEventQuery);

/* ---------------- HIGH IMPACT EVENT ---------------- */
$disruptionHIGHEventSelect = "SELECT d.CompanyID, c.CompanyName, e.EventID, s.ShipmentID, s.TransactionID, y.CategoryName
FROM Distributor d
JOIN Company c ON d.CompanyID = c.CompanyID
JOIN Shipping s ON s.DistributorID = d.CompanyID
JOIN Location l ON l.LocationID = c.LocationID
JOIN ImpactsCompany i ON i.AffectedCompanyID = d.CompanyID
JOIN DisruptionEvent e ON e.EventID = i.EventID
JOIN DisruptionCategory y ON y.CategoryID = e.CategoryID";

$disruptionHIGHEventQuery = "$disruptionHIGHEventSelect $whereStateShip AND s.ActualDate BETWEEN e.EventDate AND e.EventRecoveryDate AND i.ImpactLevel = 'High' $groupState ORDER BY d.CompanyID, c.CompanyName;";
$disruptionHIGHEvent = fetchAll($conn, $disruptionHIGHEventQuery);

/* ============================================================
   FIX #8 — Output clean JSON at END with NO echo before it
============================================================ */
echo json_encode([
    "shipping"             => $shipping,
    "receivings"           => $receivings,
    "adjustments"          => $adjustments,
    "leavingCompany"       => $leavingCompany,
    "arrivingCompany"      => $arrivingCompany,
    "distributor"          => $distributor,
    "productsHandled"      => $productsHandled,
    "shipmentsOutstanding" => $shipmentsOutstanding,
    "disruptionEvent"      => $disruptionEvent,
    "disruptionHIGHEvent"  => $disruptionHIGHEvent
]);

$conn->close();
?>

