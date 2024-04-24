<?php
header("Access-Control-Allow-Origin: *");

// Allow the following methods from any origin
header("Access-Control-Allow-Methods: POST");

// Allow the following headers from any origin
header("Access-Control-Allow-Headers: Content-Type");
if (!isset($_POST['token'])) {
    echo json_encode([
        "success" => false,
        "message" => "Token not found!"
    ]);
    die();
}

include "./database/connection.php";
include "./helpers/auth.php";
include "./helpers/notify.php";

$token = $_POST['token'];
$user_id = getUserId($token);
$problems = '';

if (
    isset(
        $_POST['doctor_id'],
        $_POST['date'],
    )
) {
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date'];
    if (isset($_POST['problems'])) {
        $problems = $_POST['problems'];
    }
    $sql = "insert into appointments (user_id,doctor_id,date,problems) values ('$user_id','$doctor_id','$date','$problems')";
    global $CON;
    $result = mysqli_query($CON, $sql);
    $appointment_id = mysqli_insert_id($CON);

    $sql = "select * from doctors where id='$doctor_id'";

    $result = mysqli_query($CON, $sql);

    $doctor = mysqli_fetch_assoc($result);

    $doctor_name = $doctor['name'];
    $hospital_id = $doctor['hospital_id'];

    $title = "Appointment made with $doctor_name";
    $description = "Your appointment with $doctor_name has been made successfully!";
    $user_id = getUserId($token);

    sendNotification($title, $description, $user_id);
    sendNotification($title, $description, $hospital_id);

    if ($result) {

        echo json_encode([
            "success" => true,
            "message" => "Appointment made successfully!",
            "appointment_id" => $appointment_id
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Something went wrong!"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "doctor_id and date are required!"
    ]);
}
