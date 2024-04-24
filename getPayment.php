<?php
header("Access-Control-Allow-Origin: *");

// Allow the following methods from any origin
header("Access-Control-Allow-Methods: GET");

// Allow the following headers from any origin
header("Access-Control-Allow-Headers: Content-Type");

include "./database/connection.php"; // Make sure this path is correct

// SQL to get all payments with user full name
$sql = "SELECT p.payment_id, p.appointment_id, p.amount, p.details, p.payment_at, u.full_name 
        FROM payments AS p 
        JOIN users AS u ON p.user_id = u.user_id";

$result = $CON->query($sql);

$payments = [];

// Check if the query was successful
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    echo json_encode(['success' => true, 'payments' => $payments]);
} else {
    // If the query failed, send a JSON encoded error message
    echo json_encode(['success' => false, 'message' => "Error fetching payments: " . $CON->error]);
}

// Close the database connection
$CON->close();
?>
