<?php

ob_start();
session_start();

include 'functions.php';
include_once 'db.php';
error_reporting(0);

//get variable from previous page
$eID = $_SESSION['empID'];
$eUsername = $_SESSION['empUName'];

// Receptionist info
$dSin = pg_fetch_row(pg_query($dbconn, "SELECT employee_sin FROM employee WHERE employee_id=$eID;"));
$dName = pg_fetch_row(pg_query($dbconn, "SELECT name FROM employee_info WHERE employee_sin='$dSin[0]';"));
$dWorkLocation = pg_fetch_row(pg_query($dbconn, "SELECT address FROM employee_info WHERE employee_sin='$dSin[0]';"));
$dSalary = pg_fetch_row(pg_query($dbconn, "SELECT annual_salary FROM employee_info WHERE employee_sin='$dSin[0]';"));
$branch = pg_fetch_row(pg_query($dbconn, "SELECT branch_id FROM employee WHERE employee_sin='$dSin[0]';"));



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCMS - Receptionist Homepage</title>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css'>
    <link rel='stylesheet' href='main.css' type="text/css">
</head>
<body>
    <div class="container">
        <div class="logout-btn">
            <a href="logout.php" class="logout-btn-text">Logout</a>
        </div>
        <h1>Hello <?php echo $dName[0] ?></h1>
        <hr>
        <!-- Displays some information concerning the dentist -->
        <h3>My information</h3>
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="h-100">
                    <div class="boxInfo">
                        <div class="text-muted">Employee position</div>
                        <div class="h3"> Receptionist </div>
                    </div> 
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="h-100">
                    <div class="boxInfo">
                        <div class="text-muted">Current Work Location</div>
                        <div class="h3"> Branch <?php echo $branch[0] ?> - <?php echo $dWorkLocation[0] ?> </div>
                    </div> 
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="boxInfo">
                        <div class="text-muted">Current Salary</div>
                        <div class="h3"><?php echo $dSalary[0] ?> </div>
                </div>
            </div>
        </div>
        <hr>
        <!-- Shows patients, possibly a button that redirects to another page
        similar to Mark's to display patient info. Also must be able to edit & set appointments.  -->
        <!--<div class="card card-header-actions mb-4 mt-4">
            <div class="card-header"><h3>Upcoming appointment</h3></div>
                <div class="card-body px-0">
                    <div class="appointment-info">
                    <?php 
                        /*if ($appointID != null) {
                            echo "
                            <h5>Patient's Name: $pName[0]</h5>
                            <h5>Date: $upcomingInfo[0]</h5>
                            <h5>Start Time: $upcomingInfo[1]</h5>
                            <h5>End Time: $upcomingInfo[2]</h5>
                            <h5>Procedure to do: $procedName[0]</h5>
                            <h5>Procedure description: $appointDesc[0]</h5>";
                        } else {
                            echo "<h5>You do not have any upcoming appointments...</h5>";*/ //uncomment heer
                        //}?>
                    </div>
                </div>
            </div>
        </div>--> <!--uncomment here-->
    </div>
</body>
</html>