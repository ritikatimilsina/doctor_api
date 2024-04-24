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

// Validate the token and get the user ID associated with it
$user_Id = getUserId($token);

// Check if a valid user ID was returned
if (!$user_Id) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid token or user ID not found."
    ]);
    die();
}

// Updated SQL query to fetch all appointments for a specific user
$sql = "SELECT appointments.*, doctors.*, payments.user_id, payments.amount, payments.details
        FROM appointments 
        LEFT JOIN payments ON appointments.appointment_id = payments.appointment_id 
        JOIN doctors ON appointments.doctor_id = doctors.id
        WHERE appointments.user_id = ?"; // Using a placeholder for prepared statement

global $CON;

// Prepare statement
$stmt = mysqli_prepare($CON, $sql);

// Bind parameters
mysqli_stmt_bind_param($stmt, "i", $user_Id); // "i" denotes the type of the parameter is integer

// Execute the prepared statement
mysqli_stmt_execute($stmt);

// Bind the result variables
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $history = [];

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
