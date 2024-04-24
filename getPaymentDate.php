<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

include "./database/connection.php";

// SQL statement to select the count of payments for each date
$sql = "SELECT DATE(payment_at) as payment_date, COUNT(*) as payment_count 
        FROM payments 
        GROUP BY DATE(payment_at) 
        ORDER BY DATE(payment_at) DESC"; // You can use ASC for ascending order

$result = mysqli_query($CON, $sql);

$paymentsByDate = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $paymentsByDate[] = $row;
    }
    echo json_encode([
        "success" => true,
        "payments_by_date" => $paymentsByDate
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . mysqli_error($CON)
    ]);
}

mysqli_close($CON);
?>
