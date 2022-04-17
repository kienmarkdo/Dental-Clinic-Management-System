<?php

ob_start();
session_start();

include 'functions.php';
include_once 'db.php';
error_reporting(0);

//get variable from previous page
$patientUsername = $_SESSION['patientUsername'];

//get the patient ID, SIN and Name
$pID = pg_fetch_row(pg_query($dbconn, "SELECT patient_id FROM user_account WHERE username = '$patientUsername';"));
$pSin = pg_fetch_row(pg_query($dbconn, "SELECT sin_info FROM Patient WHERE patient_id = '$pID[0]';"));
$pName = pg_fetch_row(pg_query($dbconn, "SELECT name FROM Patient_info WHERE patient_sin='$pSin[0]';"));

echo nl2br("Patient ID : $pID[0]\n");
echo nl2br("Patient SIN : $pSin[0]\n");
echo nl2br("Patient Name : $pName[0]\n");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCMS - Patient Homepage</title>
    <link rel="stylesheet" href="CSS/main.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body>
    <h1>DCMS - Patient <?php echo $patientUsername ?> homepage</h1>

    </div>
</body>
</html>