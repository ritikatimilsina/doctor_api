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

if (!isAdmin($token)) {
    echo json_encode([
        "success" => false,
        "message" => "You are not authorized!"
    ]);
    die();
}

// ...[previous code]...

// Check if both title and the file are set
if (isset($_POST['title']) && isset($_FILES['image_file'])) {
    $title = $_POST['title'];

    // Process the uploaded file
    $image_file = $_FILES['image_file'];
    $upload_directory = './images/'; // Make sure this directory exists and is writable
    $upload_file = $upload_directory . basename($image_file['name']);

    // Validate the image file and move it to the upload directory
    if (move_uploaded_file($image_file['tmp_name'], $upload_file)) {
        $image_url = $upload_file; // The URL or path to access the file

        // ...[previous SQL code]...

        // Include the image_url in the INSERT statement
        $sql = "INSERT INTO specialization (title, image_url) VALUES ('$title', '$image_url')";
        $result = mysqli_query($CON, $sql);

        if (!$result) {
            echo json_encode([
                "success" => false,
                "message" => "Specialization not added!"
            ]);
            die();
        } else {
            echo json_encode([
                "success" => true,
                "message" => "Specialization added successfully!"
            ]);
            die();
        } 
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to upload image!"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Title and image file are required!"
    ]);
}





   
