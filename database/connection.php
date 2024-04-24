<?php

$HOST = 'localhost';
$USER = 'root';
$PASS = '';
$DB = 'eye_care_appointment_system';

$CON = mysqli_connect($HOST, $USER, $PASS, $DB);

if (!$CON) {

    echo 'Connection failed';
}
