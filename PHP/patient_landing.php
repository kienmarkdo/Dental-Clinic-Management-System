<?php

/*
TODO / DISCLAIMER:

THE CODE IN THIS PAGE DOESN'T WORK.

Goal: Take $username from index.php, use that to get the patient's info.
Currently trying to just display something like "Welcome Bob!" where Bob is the name
that is associated with the username/account that was entered in index.php's login form.

*/

ob_start();
session_start();

include 'functions.php';
include_once 'index.php';
error_reporting(0);

echo "aasadsoifahsof";

include_once 'db.php';
    
// fetch patient data into variables
$username = $_POST['username'];
$patient_id = pg_query($dbconn, "SELECT patient_id FROM User_account WHERE username = '$username';");
$patient_sin = pg_query($dbconn, "SELECT sin_info FROM Patient WHERE patient_id = '$patient_id';");
$patient_name = pg_query($dbconn, "SELECT name FROM Patient_info WHERE patient_sin='$patient_sin';");

echo "aasadsoifahsof";
echo $username;
echo $patient_id;
echo $patient_sin;
echo $patient_name;

// search for patient_id's


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
    <h1>DCMS - Patient's <?php $patient_name ?> homepage</h1>

    </div>
</body>
</html>