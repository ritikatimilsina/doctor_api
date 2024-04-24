<?php
include "./database/connection.php";

function sendNotification($title, $description, $user_id)
{

    global $CON;

    $sql = "INSERT INTO notifications (title,description,user_id) VALUES ('$title','$description','$user_id')";
    $result = mysqli_query($CON, $sql);

    if ($result) {
        return true;
    } else {
        return false;
    }
}
