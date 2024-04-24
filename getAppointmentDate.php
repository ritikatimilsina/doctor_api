<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

include "./database/connection.php";

// SQL statement to select the count of appointments for each date
$sql = "SELECT DATE(date) as appointment_date, COUNT(*) as appointment_count 
        FROM appointments 
        GROUP BY DATE(date) 
        ORDER BY DATE(date) DESC"; // You can use ASC for ascending order

$result = mysqli_query($CON, $sql);

$appointmentsByDate = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointmentsByDate[] = $row;
    }
    echo json_encode([
        "success" => true,
        "appointments_by_date" => $appointmentsByDate
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . mysqli_error($CON)
    ]);
}

mysqli_close($CON);
?>
