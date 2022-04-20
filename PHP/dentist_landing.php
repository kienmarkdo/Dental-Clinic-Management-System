<?php

ob_start();
session_start();

include 'functions.php';
include_once 'db.php';
error_reporting(0);


//get variable from previous page
$eID = $_SESSION['empID'];
$eUsername = $_SESSION['empUName'];

// Dentist info
$dSin = pg_fetch_row(pg_query($dbconn, "SELECT employee_sin FROM employee WHERE employee_id=$eID;"));
$dName = pg_fetch_row(pg_query($dbconn, "SELECT name FROM employee_info WHERE employee_sin='$dSin[0]';"));
$dWorkLocation = pg_fetch_row(pg_query($dbconn, "SELECT address FROM employee_info WHERE employee_sin='$dSin[0]';"));
$dSalary = pg_fetch_row(pg_query($dbconn, "SELECT annual_salary FROM employee_info WHERE employee_sin='$dSin[0]';"));
$branchId = pg_fetch_row(pg_query($dbconn, "SELECT branch_id FROM employee WHERE employee_sin='$dSin[0]';"));
$branchCity = pg_fetch_row(pg_query($dbconn, "SELECT city FROM branch WHERE branch_id=$branchId[0];"));
$managerID = pg_fetch_row(pg_query($dbconn, "SELECT manager_id FROM branch WHERE branch_id=$branchId[0];"));
$mName= pg_fetch_row(pg_query($dbconn, "SELECT i.name FROM employee e, employee_info i WHERE e.employee_id='$managerID[0]' AND e.employee_sin = i.employee_sin;"));

//get type - dentist/hygienist
$type = pg_fetch_row(pg_query($dbconn, "SELECT employee_type FROM Employee_info WHERE employee_sin = '$dSin[0]';"));
if ($type[0] == 'h'){
	$type = "Hygienist";
} elseif ($type[0] == 'd'){
	$type = "Dentist";
}
//  upcoming appointments for dentist/hygienist
$dAppointments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment WHERE dentist_id=$eID AND appointment_status='Booked' ORDER BY date_of_appointment;"));

// patient records of all the dentist/hygienist's patients
$dPatientRecords = pg_fetch_all(pg_query($dbconn, "SELECT record_id, patient_details, P.patient_id FROM patient_records AS P, appointment AS A WHERE P.patient_id=A.appointment_id AND dentist_id=$eID;"));

// reviews of the dentist/hygienist
$dReviews = pg_fetch_all(pg_query($dbconn, "SELECT * FROM review WHERE dentist_name='$dName[0]' ORDER BY date_of_review DESC;"))
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCMS - <?php echo $type ?> Homepage</title>
    <link rel="icon" type="image/x-icon" href="images/toothmap.png">
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css'>
    <link rel='stylesheet' href='main.css' type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="patient_landing_style.css" />
</head>
<body>
    
        <div class="container" style="position: sticky; top: 0px; z-index:1">
            <div class="logout-btn">
                <a href="logout.php" class="logout-btn-text">Logout</a>
            </div>
        </div>
        <!-- Logout Button END -->

    <!-- LEFT NAVIGATION BAR -->
    <div class="container bootstrap snippets bootdey">
        <div class="row">
            <div class="profile-nav col-md-3" style="position: sticky; top: 0px;">
                <div class="panel">
                    <div class="user-heading round">
                        <h1>Welcome,</h1>
                        <h2>Dr. <?php echo $dName[0] ?></h2>
                    </div>
                    <ul class="nav nav-pills nav-stacked">
                        <!-- Font awesome fonts version 4: https://fontawesome.com/v4/icons/ -->
                        <li class="active">
                            <a href="#myInfo"> <i class="fa fa-user"></i> My Information</a>
                        </li>
                        <li>
                            <a href="#viewAppointments"><i class="fa fa-calendar"></i> View upcoming appointment</a>
                        </li>
                        <li>
                            <a href="#viewPatientRecords"><i class="fa fa-heart"></i> View Patient Records</a>
                        </li>
                        <li>
                            <a href="#viewReviews"><i class="fa fa-comments-o"></i> View reviews</a>
                        </li>                         
                    </ul>
                </div>
            </div>
        
            <!-- Pages section -->
            <div class="profile-info col-md-9">
                <!-- Dentist information -->
                <div class="panel" id="dentist_info">
                    <div class="bio-graph-heading">
                        <h3> <i class="fa fa-user"></i> My Information</h3>
                    </div>
                    <div class="panel-body bio-graph-info">
                        <h1>Employee ID - <?php echo $eID ?></h1>
                        <div class="row">
                            <div class="bio-row">
                                <p>
                                    <span>Full Name </span>
                                    <?php echo $dName[0] ?>
                                </p>  
                            </div>
                            <div class="bio-row">
                                <p>
                                    <span>SIN </span>
                                    <?php echo $dSin[0] ?>
                                </p>  
                            </div>
                            <div class="bio-row">
                                <p>
                                    <span>Work Location </span>
                                    <?php echo $dWorkLocation[0] ?>
                                </p>  
                            </div>
                            <div class="bio-row">
                                <p>
                                    <span>Annual Salary </span>
                                    <?php echo $dSalary[0] ?>
                                </p>  
                            </div>
                            <div class="bio-row">
                                <p>
                                    <span>Branch City </span>
                                    <?php echo $branchCity[0] ?>
                                </p>  
                            </div>
                            <div class="bio-row">
                                <p>
                                    <span>Manager </span>
                                    <?php echo $mName[0] ?>
                                </p>  
                            </div>
                            <div class="bio-row">
                                <p>
                                    <span>Branch ID </span>
                                    <?php echo $branchId[0] ?>
                                </p>  
                            </div>
                            <div class="bio-row">
                                <p>
                                    <span>Role </span>
                                    <?php echo $type ?>
                                </p> 
                            </div>
                            
                        </div>   
                    </div>
                </div>
                <div class="panel" id="viewAppointments">
                        <div class="bio-graph-heading">
                            <h3><i class="fa fa-calendar"></i> Upcoming Appointments</h3>
                        </div>
                        <div class="panel-body bio-graph-info">
                            <h5>Please view each patient's medical records before administering the procedure. Please note that the end time is only here for information ("à titre indicatif" in french)</h5>
                            <table id="appointments_grid" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Patient Name</th>
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Procedure Description</th>
                                        <th>Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($dAppointments as $dAppointment => $dAppointments) :?>
                                    <tr>
                                        <td><?php 
                                            $pID = $dAppointments['patient_id'];
                                            $pSin = pg_fetch_row(pg_query($dbconn, "SELECT sin_info FROM patient WHERE patient_id = $pID;"));
                                            $pName = pg_fetch_row(pg_query($dbconn, "SELECT name FROM patient_info WHERE patient_sin='$pSin[0]';")); 
                                            echo $pName[0] ?></td>
                                        <td><?php echo $dAppointments['date_of_appointment'] ?></td>
                                        <td><?php echo $dAppointments['start_time'] ?></td>
                                        <td><?php echo $dAppointments['end_time'] ?></td>
                                        <td>
                                        <?php 
                                            $aId = $dAppointments['appointment_id'];
                                            $procedureDesc = pg_fetch_row(pg_query($dbconn, "SELECT appointment_description FROM appointment_procedure WHERE appointment_id=$aId;"));
                                            echo $procedureDesc[0] ?>
                                        <td><?php echo $dAppointments['room'] ?></td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                </div>
           
            <div class="panel" id="viewPatientRecords">
                    <div class="bio-graph-heading">
                        <h3><i class="fa fa-heart"></i> View Patient Records</h3>
                    </div>
                    <div class="panel-body bio-graph-info">
                        <table id="appointments_grid" class="table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Details</th>
                                    <th>Record ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($dPatientRecords as $dPatientRecord => $dPatientRecords) :?>
                                <tr>
                                    <td><?php 
                                        $pID = $dPatientRecords['patient_id'];
                                        $pSin = pg_fetch_row(pg_query($dbconn, "SELECT sin_info FROM patient WHERE patient_id = $pID;"));
                                        $pName = pg_fetch_row(pg_query($dbconn, "SELECT name FROM patient_info WHERE patient_sin='$pSin[0]';")); 
                                        echo $pName[0] ?></td>
                                    <td><?php echo $dPatientRecords['patient_details'] ?></td>
                                    <td><?php echo $dPatientRecords['record_id'] ?></td> 
                                </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>   
            </div>
            <div class="panel" id="viewReviews">
                    <div class="bio-graph-heading">
                        <h3><i class="fa fa-comments-o"></i> View Reviews</h3>
                    </div>
                    <div class="panel-body bio-graph-info">
                        <h5>Please note that the reviews are anonymous. Professionalism, Communication and Cleanliness are rated out of 5.</h5>
                            <table id="appointments_grid" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Date of review</th>
                                        <th>Comments</th>
                                        <th>Professionalism</th>
                                        <th>Communication</th>
                                        <th>Cleanliness</th>
                                        <th>Procedure</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($dReviews as $dReview => $dReviews) :?>
                                    <tr>
                                        <td><?php echo $dReviews['date_of_review'] ?></td>
                                        <td><?php echo $dReviews['review_description'] ?></td>
                                        <td><?php echo $dReviews['professionalism'] ?></td> 
                                        <td><?php echo $dReviews['communication'] ?></td> 
                                        <td><?php echo $dReviews['cleanliness'] ?></td> 
                                        <td><?php 
                                            $reviewId = $dReviews['review_id'];
                                            $procedName = pg_fetch_row(pg_query($dbconn, "SELECT procedure_name FROM procedure_codes WHERE procedure_code=CAST((SELECT procedure_id FROM review WHERE dentist_name='$dName[0]' AND review_id=$reviewId[0]) AS INT);"));
                                            echo $procedName[0] ?></td> 
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                
            </div>
        </div>
</body>
</html>