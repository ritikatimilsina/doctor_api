<?php
header("Access-Control-Allow-Origin: *");

// Allow the following methods from any origin
header("Access-Control-Allow-Methods: POST");

// Allow the following headers from any origin
header("Access-Control-Allow-Headers: Content-Type");
// Ensure connection.php and auth.php are correctly included
include "./database/connection.php";
include "./helpers/auth.php";

// Check if the token is posted
if (!isset($_POST['token'])) {
    echo json_encode([
        "success" => false,
        "message" => "Token not found!"
    ]);
    die();
}

$token = $_POST['token'];

// Assuming the token still needs to be validated for other reasons,
// even if we're not using it to filter appointments
$is_hospital = isHospital($token);
$user_Id = getUserId($token);

// Updated SQL query to fetch all appointments without user or hospital specific filtering
$sql = "SELECT appointments.*, doctors.*, payments.user_id, payments.amount, payments.details
        FROM appointments 
        LEFT JOIN payments ON appointments.appointment_id = payments.appointment_id 
        JOIN doctors ON appointments.doctor_id = doctors.id";

global $CON;

$result = mysqli_query($CON, $sql);

if ($result) {
    $appointments = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }

    echo json_encode([
        "success" => true,
        "message" => "Appointments fetched successfully!",
        "appointments" => $appointments
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Something went wrong!"
    ]);
}
?>
