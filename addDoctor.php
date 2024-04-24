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

if (!isHospital($token)) {
    echo json_encode([
        "success" => false,
        "message" => "You are not authorized!"
    ]);
    die();
}

global $CON;

if (isset($_POST['name'], $_POST['consultation_charge'], $_POST['specialization_id'], $_FILES['avatar'], $_POST['experience'], $_POST['doctor_description'])) {

    $name = $_POST['name'];
    $consultation_charge = $_POST['consultation_charge'];
    $specialization_id = $_POST['specialization_id'];
    $avatar = $_FILES['avatar'];
    $experience = $_POST['experience'];
    $doctor_description = $_POST['doctor_description']; 
    $avatar_name = $avatar['name'];
    $avatar_tmp_name = $avatar['tmp_name'];
    $avatar_size = $avatar['size'];

    $hospital_Id = getUserId($token);

    $ext = pathinfo($avatar_name, PATHINFO_EXTENSION);

    if ($ext != "jpg" && $ext != "jpeg" && $ext != "png" && $ext != "webp") {
        echo json_encode([
            "success" => false,
            "message" => "Only image files are allowed!"
        ]);
        die();
    }

    if ($avatar_size > 1000000) {
        echo json_encode([
            "success" => false,
            "message" => "Image size should be less than 1MB!"
        ]);
        die();
    }

    $avatar_name = uniqid() . "." . $ext;

    if (!move_uploaded_file($avatar_tmp_name, "./images/" . $avatar_name)) {
        echo json_encode([
            "success" => false,
            "message" => "Image upload failed!"
        ]);
        die();
    }

    // Prepared statement to prevent SQL injection
    $stmt = $CON->prepare("INSERT INTO doctors (name, consultation_charge, hospital_id, specialization_id, avatar, experience, doctor_description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiisis", $name, $consultation_charge, $hospital_Id, $specialization_id, $avatar_name_db, $experience, $doctor_description);
    
    $avatar_name_db = "images/$avatar_name"; // Safe to include in the database
    $result = $stmt->execute();

    if (!$result) {
        echo json_encode([
            "success" => false,
            "message" => "Doctor not added!"
        ]);
        die();
    } else {
        echo json_encode([
            "success" => true,
            "message" => "Doctor added successfully!"
        ]);
    }
    $stmt->close();
} else {
    echo json_encode([
        "success" => false,
        "message" => "All fields are required!"
    ]);
    die();
}
?>
