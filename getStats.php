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

$token = $_POST['token'];

include "./database/connection.php";
include "./helpers/auth.php";

$is_hospital = isHospital($token);

if (!$is_hospital) {
    echo json_encode([
        "success" => false,
        "message" => "You are not authorized"
    ]);
    die();
}

$hospitalId = getUserId($token);

$isAdmin = isAdmin($token);

$no_of_doctors;

$sql = '';

if ($isAdmin) {
    $sql = "select count(*) as totalDoctors from doctors";
} else {
    $sql = "select count(*) as totalDoctors from doctors where hospital_id='$hospitalId'";
}

global $CON;

$result = mysqli_query($CON, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $no_of_doctors = $row['totalDoctors'];
} else {
    echo json_encode([
        "success" => false,
        "message" => "Something went wrong!"
    ]);
    die();
}

$totalMonthlyIncome = 0;

$sql = '';

if ($isAdmin) {
    $sql = "select sum(amount) as totalIncome from payments where MONTH(payment_at) = MONTH(CURRENT_DATE()) AND YEAR(payment_at) = YEAR(CURRENT_DATE())";
} else {
    $sql = "select sum(amount) as totalIncome from payments join appointments on payments.appointment_id=appointments.appointment_id join doctors on appointments.doctor_id=doctors.id where doctors.hospital_id='$hospitalId' AND MONTH(payment_at) = MONTH(CURRENT_DATE()) AND YEAR(payment_at) = YEAR(CURRENT_DATE())";
}

$result = mysqli_query($CON, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalMonthlyIncome = $row['totalIncome'];
} else {
    echo json_encode([
        "success" => false,
        "message" => "Something went wrong!"
    ]);
    die();
}

$totalIncome = 0;

$sql = '';

if ($isAdmin) {
    $sql = "select sum(amount) as totalIncome from payments";
} else {
    $sql = "select sum(amount) as totalIncome from payments join appointments on payments.appointment_id=appointments.appointment_id join doctors on appointments.doctor_id=doctors.id where doctors.hospital_id='$hospitalId'";
}

$result = mysqli_query($CON, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalIncome = $row['totalIncome'];
} else {
    echo json_encode([
        "success" => false,
        "message" => "Something went wrong!"
    ]);
    die();
}

$totalAppointments = 0;

$sql = '';

if ($isAdmin) {
    $sql = "select count(*) as totalAppointments from appointments where status='paid'";
} else {
    $sql = "select count(*) as totalAppointments from appointments join doctors on appointments.doctor_id=doctors.id where doctors.hospital_id='$hospitalId' AND status='paid'";
}

$result = mysqli_query($CON, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalAppointments = $row['totalAppointments'];
} else {
    echo json_encode([
        "success" => false,
        "message" => "Something went wrong!"
    ]);
    die();
}

if ($isAdmin) {
    $totalUsers = 0;

    $sql = '';

    if ($isAdmin) {
        $sql = "select count(*) as totalUsers from users where role='user'";
    }

    $result = mysqli_query($CON, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $totalUsers = $row['totalUsers'];
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Something went wrong!"
        ]);
        die();
    }

    echo json_encode([
        "success" => true,
        "message" => "Stats fetched successfully!",
        "stats" => [
            "no_of_doctors" => $no_of_doctors,
            "totalIncome" => $totalIncome,
            "totalMonthlyIncome" => $totalMonthlyIncome,
            "totalAppointments" => $totalAppointments,
            "totalUsers" => $totalUsers
        ]
    ]);
} else {
    echo json_encode([
        "success" => true,
        "message" => "Stats fetched successfully!",
        "stats" => [
            "no_of_doctors" => $no_of_doctors,
            "totalIncome" => $totalIncome,
            "totalMonthlyIncome" => $totalMonthlyIncome,
            "totalAppointments" => $totalAppointments,
        ]
    ]);
}
