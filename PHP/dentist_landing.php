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
$mName = pg_fetch_row(pg_query($dbconn, "SELECT i.name FROM employee e, employee_info i WHERE e.employee_id='$managerID[0]' AND e.employee_sin = i.employee_sin;"));

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
$dPatientRecords = pg_fetch_all(pg_query($dbconn, "SELECT DISTINCT record_id, patient_details, P.patient_id FROM patient_records AS P, appointment AS A WHERE P.patient_id=A.patient_id AND A.dentist_id=$eID;"));

// gets all the appointment ids related to the doctor
$dAllAppointmentIds = pg_fetch_all(pg_query($dbconn, "SELECT appointment_id FROM Appointment WHERE dentist_id=$eID ORDER BY appointment_id;"));

// reviews of the dentist/hygienist
$dReviews = pg_fetch_all(pg_query($dbconn, "SELECT * FROM review WHERE dentist_name='$dName[0]' ORDER BY date_of_review DESC;"));

// gets all the patient of the doctor
$dAllPatients = pg_fetch_all(pg_query($dbconn, "SELECT DISTINCT I.name, P.patient_id FROM patient_info AS I, patient as P WHERE I.patient_sin=P.sin_info AND P.patient_id IN (SELECT DISTINCT patient_id FROM appointment WHERE dentist_id=$eID);"));

// gets all the treatments of the patients
$allTreatments = pg_fetch_all(pg_query($dbconn,"SELECT * FROM treatment as T WHERE T.patient_id IN (SELECT DISTINCT P.patient_id FROM patient_info AS I, patient as P WHERE I.patient_sin=P.sin_info AND P.patient_id IN (SELECT DISTINCT patient_id FROM appointment WHERE dentist_id=$eID));"));

// gets the different types of procedure available
$treatmentTypes = pg_fetch_all(pg_query($dbconn, "SELECT * FROM procedure_codes;"));

// gets all the appointment procedure related to the concerned doctor
$allAppointmentProceds = pg_fetch_all(pg_query($dbconn,"SELECT * FROM appointment_procedure WHERE appointment_id IN (SELECT appointment_id FROM Appointment WHERE dentist_id=$eID ORDER BY appointment_id) ORDER BY date_of_procedure DESC;"))
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
    
        <div class="container">
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
                            <a href="#viewPatientRecords"><i class="fa fa-heart"></i> View Patient Medical Records</a>
                        </li>
                        <li>
                            <a href="#patient_treatments"> <i class="fa fa-flag"></i> Patient Treatments</a>
                        </li>
                        <li>
                            <a href="#patient_appointment_procedures"> <i class="fa fa-book"></i> Appointment Procedures</a>
                        </li>
                        <li>
                            <a href="#viewReviews"><i class="fa fa-comments-o"></i> View Reviews</a>
                        </li>                         
                    </ul>
                </div>
            </div>
            <?php 

            if ($_POST['addTreatment']) {
                
                $patientNameErr = $treatmentTypeErr = $medicationErr = $symptomsErr = $toothErr = $commentsErr = $idErr = "";

                if ($_SERVER["REQUEST_METHOD"] == "POST") {

                    // checks if all required fields are filled

                    if ($_POST["patient"] == "-") {
                        $patientNameErr = "Required";
                    }
                    if ($_POST["treatment"] == "-") {
                        $treatmentTypeErr = "Required";
                    }
                    if (empty($_POST["medication"])) {
                        $medicationErr = "Required";
                    }
                    if (empty($_POST["symptoms"])) {
                        $symptomsErr = "Required";
                    }
                    if (empty($_POST["tooth"])) {
                        $toothErr = "Required";
                    }
                    if (empty($_POST["comments"])) {
                        $commentsErr = "Required";
                    }
                    if ($_POST["concernedAppointment"] == "-") {
                        $idErr = "Required";
                    }

                }
            } elseif ($_POST['addAppointmentProcedure']) {

                $apptPatientNameErr = $apptToothErr = $apptProcedureErr = $amountErr = $apptIdErr = $totalChargeErr = $apptDescErr = "";

                 // checks if all required fields are filled

                if ($_POST["apptPatient"] == "-") {
                    $apptPatientNameErr = "Required";
                }
                if (empty($_POST["apptTooth"])) {
                    $apptToothErr = "Required";
                }
                if ($_POST["apptProcedure"] == "-") {
                    $apptProcedureErr = "Required";
                }
                if (empty($_POST["amount"])) {
                    $amountErr = "Required";
                }
                if ($_POST["apptId"] == "-") {
                    $apptIdErr = "Required";
                }
                if (empty($_POST["totalCharge"])) {
                    $totalChargeErr = "Required";
                }
                if (empty($_POST["apptDesc"])) {
                    $apptDescErr = "Required";
                }
                
            }

            ?>
        
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
                            <p><i class="fa fa-question-circle"></i> Please view each patient's Medical Records before administering an Appointment Procedure.</p>
                            <table id="appointments_grid" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Patient Name</th>
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th><abbr title="End Time is provided for informational purposes only - « à titre indicatif » en français">End Time</abbr></th>
                                        <th>Procedure To Do</th>
                                        <th>Room</th>
                                        <th>Appointment ID</th>
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
                                            $procedureName = pg_fetch_row(pg_query($dbconn, "SELECT procedure_name FROM procedure_codes WHERE procedure_code=CAST((SELECT appointment_type FROM appointment WHERE dentist_id=$eID AND appointment_status='Booked' AND appointment_id=$aId) AS INT);"));
                                            echo $procedureName[0] ?>
                                        <td><?php echo $dAppointments['room'] ?></td>
                                        <td><?php echo $dAppointments['appointment_id'] ?></td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                </div>
           
            <div class="panel" id="viewPatientRecords">
                    <div class="bio-graph-heading">
                        <h3><i class="fa fa-heart"></i> View Patient Medical Records</h3>
                    </div>
                    <div class="panel-body bio-graph-info">
                        <p><i class="fa fa-question-circle"></i> Notes on patient medical history and other relevant health information</p>
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

            <div class="panel" id="patient_treatments">
                    <div class="bio-graph-heading">
                        <h3><i class="fa fa-flag"></i> Patient Treatments</h3>
                    </div>
                    <div class="panel-body bio-graph-info">
                        <p><i class="fa fa-question-circle"></i> Treatments are diagnosed after an Appointment with a patient and are required before creating a patient's Appointment Procedure</p>
                        <table id="appointments_grid" class="table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Treatment Type</th>
                                    <th>Medication</th>
                                    <th>Symptoms</th>
                                    <th>Tooth</th>
                                    <th>Comments</th>
                                    <th>Appointment ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($allTreatments as $allTreatment => $allTreatments) :?>
                                <tr>
                                    <td><?php 
                                    $pId = $allTreatments['patient_id'];
                                    $pName = pg_fetch_row(pg_query($dbconn,"SELECT I.name FROM patient_info AS I, patient AS P WHERE I.patient_sin=P.sin_info AND P.patient_id=$pId;"));
                                    echo $pName[0] ?></td>
                                    <td><?php echo $allTreatments['treatment_type'] ?></td>
                                    <td><?php echo $allTreatments['medication'] ?></td>
                                    <td><?php echo $allTreatments['symptoms'] ?></td>
                                    <td><?php echo $allTreatments['tooth'] ?></td>
                                    <td><?php echo $allTreatments['comments'] ?></td>
                                    <td><?php echo $allTreatments['appointment_id'] ?></td>
                                </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                    <hr>
            
                    <form action="<?php echo "#add_treatment"; ?>" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ;?>">
                        <div class="panel" id="add_treatment">
                            <div class="panel-body bio-graph-info">
                                <h1>Add a treatment for a patient</h1>
                                <p><i class="fa fa-question-circle"></i> Note that you can only add a treatment for <abbr title="Patients with whom you have had an appointment">your patients and your appointments</abbr></p>
                                <br>
                                <p><span class="error">* indicates required fields </span></p>
                                <div class="row">
                                    <div class="bio-row">
                                        <p>
                                        <span>Patient Name </span>
                                        <select name="patient" id="patient">
                                            <option>-</option>
                                            <?php
                                                foreach($dAllPatients as $dAllPatient => $dAllPatients) :?>
                                                <option value="<?php echo $dAllPatients['name']?>">
                                                    <?php echo $dAllPatients['name'] ?>
                                                </option>
                                            <?php endforeach?>
                                        </select>
                                        <span class="error">* <?php echo $patientNameErr ?></span>
                                        <p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Treatment type </span>
                                            <select name="treatment" id="treatment">
                                                <option>-</option>
                                                <?php 
                                                    foreach($treatmentTypes as $treatmentType => $treatmentTypes) :?>
                                                    <option value="<?php echo $treatmentTypes['procedure_name']?>">
                                                        <?php echo $treatmentTypes['procedure_name']?>
                                                    </option>
                                                <?php endforeach?>
                                            </select>
                                            <span class="error">* <?php echo $treatmentTypeErr ?></span>
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Medication </span>
                                            <input type="text" id="medication" name="medication" placeholder="Enter medication" maxlength="255">
                                            <span class="error">* <?php echo $medicationErr ?></span>  
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Symptoms </span>
                                            <input type="text" id="symptoms" name="symptoms" placeholder="Enter symptoms" maxlength="255">
                                            <span class="error">* <?php echo $symptomsErr ?></span>  
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Tooth number </span>
                                            <input type="number" id="tooth" name="tooth" placeholder="Enter tooth number">
                                            <span class="error">* <?php echo $toothErr?></span>  
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Comments </span>
                                            <input type="text" id="comments" name="comments" placeholder="Enter any comments" maxlength="255">
                                            <span class="error">* <?php echo $commentsErr ?></span>  
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>ID of Concerned Appointement  </span>
                                            <select name="concernedAppointment" id="concernedAppointment">
                                                <option>-</option>
                                                <?php 
                                                    foreach($dAllAppointmentIds as $dAllAppointmentId => $dAllAppointmentIds) :?>
                                                    <option value="<?php echo $dAllAppointmentIds['appointment_id']?>">
                                                        <?php echo $dAllAppointmentIds['appointment_id'] ?>
                                                    </option>
                                                <?php endforeach?>
                                            </select>
                                            <span class="error">* <?php echo $idErr ?></span>  
                                        </p>
                                    </div>
                                                                   
                                </div>
                                <input class="btn btn-primary"type="submit" name="addTreatment" value="Add Treatment"> 
                            </div>
                        </div> 
                    </form>
            </div>
            <?php
            if ($_POST["patient"] != "-" && $_POST["treatment"] != "-" && $_POST["concernedAppointment"] != "-" &&
                (!(empty($_POST["medication"]) && empty($_POST["symptoms"]) && empty($_POST["tooth"]) 
                    && empty($_POST["comments"])))) {

                    if ($_SERVER['REQUEST_METHOD'] === "POST") {

                        $treatmentTypeInput = $_POST['treatment'];
                        $medicationInput = $_POST['medication'];
                        $symptomsInput = $_POST['symptoms'];
                        $toothInput = $_POST['tooth'];
                        $commentsInput = $_POST['comments'];
                        $patientNameInput = $_POST['patient'];
                        $patientIdFecth = pg_fetch_row(pg_query($dbconn,"SELECT P.patient_id FROM patient AS P, patient_info AS I WHERE I.patient_sin=P.sin_info AND I.name='$patientNameInput';"));
                        $patientId = $patientIdFecth[0];
                        $appointmentIdInput = $_POST['concernedAppointment'];

                        $query = "INSERT INTO treatment (treatment_type,medication,symptoms,tooth,comments,patient_id,appointment_id) VALUES('$treatmentTypeInput','$medicationInput','$symptomsInput','$toothInput','$commentsInput','$patientId','$appointmentIdInput')";
                        
                        $addTreatment = pg_query($dbconn,$query);

                        if (!$addTreatment) {
                            echo preg_last_error($dbconn);
                            echo "<h5>There was an error adding the treatment</h5>";
                        } else {
                            echo "<h5>You've succesfully added a treatment for $patientNameInput.</h5>";
                            echo "<h5><strong style=\"color:red\">Please refresh to see the changes</strong>.<br></h5>";
                        }
                    }
                }
                          
            ?>
            
            <div class="panel" id="patient_appointment_procedures">
                    <div class="bio-graph-heading">
                        <h3><i class="fa fa-book"></i> Appointment Procedures</h3>
                    </div>
                    <div class="panel-body bio-graph-info">
                        <p><i class="fa fa-question-circle"></i> Note that the date of the Appointment Procedures are the same as the date of their respective Appointment IDs </abbr></p>
                        <table id="appointments_grid" class="table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Appointment ID</th>
                                    <th>Date</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Tooth</th>
                                    <th><abbr title="Refers to the amount of procedure to perform">Amount</th>
                                    <th>Total Charge</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($allAppointmentProceds as $allAppointmentProced => $allAppointmentProceds) :?>
                                <tr>
                                    <td><?php 
                                    $pIdProced = $allAppointmentProceds['patient_id'];
                                    $pNameProced = pg_fetch_row(pg_query($dbconn,"SELECT I.name FROM patient_info AS I, patient AS P WHERE I.patient_sin=P.sin_info AND P.patient_id=$pIdProced;"));
                                    echo $pNameProced[0] ?></td>
                                    <td><?php echo $allAppointmentProceds['appointment_id'] ?></td>
                                    <td><?php echo $allAppointmentProceds['date_of_procedure'] ?></td>
                                    <td><?php echo $allAppointmentProceds['procedure_code'] ?></td>
                                    <td><?php echo $allAppointmentProceds['appointment_description'] ?></td>
                                    <td><?php echo $allAppointmentProceds['tooth'] ?></td>
                                    <td><?php echo $allAppointmentProceds['amount_of_procedure'] ?></td>
                                    <td><?php echo $allAppointmentProceds['total_charge'] ?></td>
                                </tr>
                                <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        
                        <form action="<?php echo "#add_appt_procedure"; ?>" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ;?>">
                            <div class="panel" id="add_appt_procedure">
                                <div class="panel-body bio-graph-info">
                                    <h1>Add an appointment procedure for a patient</h1>
                                    <p><i class="fa fa-question-circle"></i> Note that the Invoice will be taken care of by the Receptionist after the procedure is completed</p>
                                    <br>
                                    <p><span class="error">* indicates required fields </span></p>
                                    <div class="row">
                                        <div class="bio-row">
                                            <p>
                                            <span>Patient Name </span>
                                            <select name="apptPatient" id="apptPatient">
                                                <option>-</option>
                                                <?php
                                                    $dAllPatients = pg_fetch_all(pg_query($dbconn, "SELECT DISTINCT I.name, P.patient_id FROM patient_info AS I, patient as P WHERE I.patient_sin=P.sin_info AND P.patient_id IN (SELECT DISTINCT patient_id FROM appointment WHERE dentist_id=$eID);"));
                                                    foreach($dAllPatients as $dAllPatient => $dAllPatients) :?>
                                                    <option value="<?php echo $dAllPatients['name']?>">
                                                        <?php echo $dAllPatients['name'] ?>
                                                    </option>
                                                <?php endforeach?>
                                            </select>
                                            <span class="error">* <?php echo $apptPatientNameErr ?></span>
                                            <p>
                                        </div>
                                        <div class="bio-row">
                                            <p>
                                            <span>Tooth Number </span>
                                            <input type="number" id="apptTooth" name="apptTooth" placeholder="Enter tooth number">
                                            <span class="error">* <?php echo $apptToothErr ?></span>  
                                            </p>
                                        </div>
                                        <div class="bio-row">
                                            <p>
                                            <span>Procedure Code </span>
                                            <select name="apptProcedure" id="apptProcedure">
                                                <option>-</option>
                                                <?php 
                                                    $procedureCodes = pg_fetch_all(pg_query($dbconn, "SELECT * FROM procedure_codes;"));
                                                    foreach($procedureCodes as $procedureCode => $procedureCodes) :?>
                                                    <option value="<?php echo $procedureCodes['procedure_code']?>">
                                                        <?php echo $procedureCodes['procedure_code'] . " - " . $procedureCodes['procedure_name']?>
                                                    </option>
                                                <?php endforeach?>
                                            </select>
                                            <span class="error">* <?php echo $apptProcedureErr ?></span>
                                            </p>
                                        </div>
                                        <div class="bio-row">
                                            <p>
                                            <span>Procedure Description </span>
                                            <input type="text" id="apptDesc" name="apptDesc" placeholder="Enter description" maxlength="255">
                                            <span class="error">* <?php echo $apptDescErr?></span>  
                                            </p>
                                        </div>
                                        <div class="bio-row">
                                            <p>
                                            <span>Total Charge </span>
                                            <input type="number" id="totalCharge" name="totalCharge" placeholder="Enter total charge"> 
                                            <span class="error">* <?php echo $totalChargeErr?></span> 
                                        </p>
                                        </div> 
                                        <div class="bio-row">
                                            <p>
                                            <span><abbr title="The number of times this procedure needs to be performed">Procedure Amount</abbr> </span>
                                            <input type="number" id="amount" name="amount" placeholder="Enter amount">
                                            <span class="error">* <?php echo $amountErr?></span>  
                                            </p>
                                        </div>                                       
                                        <div class="bio-row">
                                            <p>
                                            <span><abbr title="ID of a previous Appointment you've had with a patient">Appointment ID</abbr> </span>
                                            <select name="apptId" id="apptId">
                                                <option>-</option>
                                                <?php 
                                                    $dAllAppointmentIds = pg_fetch_all(pg_query($dbconn, "SELECT appointment_id FROM Appointment WHERE dentist_id=$eID ORDER BY appointment_id;"));
                                                    foreach($dAllAppointmentIds as $dAllAppointmentId => $dAllAppointmentIds) :?>
                                                    <option value="<?php echo $dAllAppointmentIds['appointment_id']?>">
                                                        <?php echo $dAllAppointmentIds['appointment_id'] ?>
                                                    </option>
                                                <?php endforeach?>
                                            </select>
                                            <span class="error">* <?php echo $apptIdErr ?></span>  
                                            </p>
                                        </div>                                                                   
                                    </div>

                                    <input class="btn btn-primary"type="submit" name="addAppointmentProcedure" value="Add Appointment Procedure"> 
                                </div>
                            </div> 
                        </form>

            </div>
            <?php
            if ($_POST["apptPatient"] != "-" && $_POST["apptProcedure"] != "-" && $_POST["apptId"] != "-" &&
                (!(empty($_POST["apptTooth"]) && empty($_POST["apptDesc"]) && empty($_POST["totalCharge"]) 
                    && empty($_POST["amount"])))) {

                    if ($_SERVER['REQUEST_METHOD'] === "POST") {
                        
                        $apptIdInput = $_POST['apptId'];

                        $apptNameInput = $_POST['apptPatient'];
                        $apptPIDFetch = pg_fetch_row(pg_query($dbconn,"SELECT P.patient_id FROM patient AS P, patient_info AS I WHERE I.patient_sin=P.sin_info AND I.name='$apptNameInput';"));
                        $apptPIDInput = $apptPIDFetch[0];

                        $apptDateFecth = pg_fetch_row(pg_query($dbconn, "SELECT date_of_appointment FROM appointment WHERE appointment_id=$apptIdInput;"));
                        $apptDateInput = $apptDateFecth[0];

                        $apptProcedCodeInput = $_POST['apptProcedure'];
                        $apptDescInput = $_POST['apptDesc'];
                        $apptToothInput = $_POST['apptTooth'];
                        $amountInput = $_POST['amount'];
                        $totalChargeInput = $_POST['totalCharge'];
                        
                        $query = "INSERT INTO appointment_procedure (appointment_id,patient_id,date_of_procedure,invoice_id,procedure_code,appointment_description,tooth,amount_of_procedure,patient_charge,insurance_charge,total_charge,insurance_claim_id) 
                                VALUES('$apptIdInput','$apptPIDInput','$apptDateInput',NULL,'$apptProcedCodeInput','$apptDescInput','$apptToothInput','$amountInput',NULL,NULL,'$totalChargeInput',NULL)";
                        
                        $addApptProced = pg_query($dbconn,$query);

                        if (!$addApptProced) {
                            echo preg_last_error($dbconn);
                            echo "<h5>There was an error adding the appointment procedure</h5>";
                        } else {
                            echo "<h5>You've succesfully added an appointment procedure for $apptNameInput.</h5>";
                            echo "<h5><strong style=\"color:red\">Please refresh to see the changes</strong>.<br></h5>";
                        }
                    }
                }
                          
            ?>

            <div class="panel" id="viewReviews">
                    <div class="bio-graph-heading">
                        <h3><i class="fa fa-comments-o"></i> View Reviews</h3>
                    </div>
                    <div class="panel-body bio-graph-info">
                        <p><i class="fa fa-question-circle"></i> Note that patient reviews are anonymous. Ratings are on a scale from 1 to 5.</p>
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
        <br>
        <br>
        <br>
        <br>
</body>
<script>
        // this if statement turns off the "Confirm Form Resubmission" and prevents multiple Review submissions
        //  after a successful Review submission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</html>