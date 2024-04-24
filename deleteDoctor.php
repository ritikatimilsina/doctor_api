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


$token = $_POST['token'];

$is_hospital = isHospital($token);

if (!$is_hospital) {
    echo json_encode([
        "success" => false,
        "message" => "You are not authorized"
    ]);
    die();
}





$hospitalId = getUserId($token);


if (!isset($_POST['doctor_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Doctor id is required"
    ]);
    die();
}

$doctor_id = $_POST['doctor_id'];

global $CON;

$sql = "select * from doctors where id='$doctor_id'";

$result = mysqli_query($CON, $sql);

$doctor = mysqli_fetch_assoc($result);

if (!$doctor) {
    echo json_encode([
        "success" => false,
        "message" => "doctor not found"
    ]);
    die();
}

if ($hospitalId != $doctor['hospital_id']) {
    echo json_encode([
        "success" => false,
        "message" => "Your hospital is not authorized"
    ]);
    die();
}

$isDeleted = $doctor["isDeleted"] == 0 ? false : true;

$sql = '';

if ($isDeleted) {
    $sql = "update doctors set isDeleted=0 where id='$doctor_id'";
} else {
    $sql = "update doctors set isDeleted=1 where id='$doctor_id'";
}

$result = mysqli_query($CON, $sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to update"
    ]);
    die();
}

if ($isDeleted) {
    echo json_encode([
        "success" => true,
        "message" => "Doctor restored successfully!"
    ]);
    die();
} else {
    echo json_encode([
        "success" => true,
        "message" => "Doctor deleted successfully!"
    ]);
    die();
}
