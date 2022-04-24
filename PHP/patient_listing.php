<?php

ob_start();
session_start();

include 'functions.php';
include_once 'db.php';
error_reporting(0);

// get variable from submit button
$pID =  $_SESSION['patientID'];
$pSin = pg_fetch_row(pg_query($dbconn, "SELECT sin_info FROM Patient WHERE patient_id = '$pID[0]';"));
$pNameFetch = pg_fetch_row(pg_query($dbconn, "SELECT name FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pName = $pNameFetch[0];

// Patient ID and patient info details
//$pID = pg_fetch_row(pg_query($dbconn, "SELECT patient_id FROM Patient WHERE sin_info = '$pSin[0]';"));
$pGender = pg_fetch_row(pg_query($dbconn, "SELECT gender FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pEmail = pg_fetch_row(pg_query($dbconn, "SELECT email FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pPhone = pg_fetch_row(pg_query($dbconn, "SELECT phone FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pDateOfBirth = pg_fetch_row(pg_query($dbconn, "SELECT date_of_birth FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pAddress = pg_fetch_row(pg_query($dbconn, "SELECT address FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pInsurance = pg_fetch_row(pg_query($dbconn, "SELECT insurance FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pRepresentative = pg_fetch_row(pg_query($dbconn, "SELECT rep FROM Patient_info WHERE patient_sin='$pSin[0]';"));

// Patient records details
$patientRecords = pg_fetch_all(pg_query($dbconn, "SELECT record_id, patient_details FROM Patient_records WHERE patient_id='$pID[0]';"));

// Patient appointment details
$patientAppointments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment WHERE patient_id='$pID[0]' ORDER BY date_of_appointment DESC;"));
// TODO: apptDentistNames is stores the names of all the dentists who served this patient
$apptDentistNames = pg_fetch_all(pg_query($dbconn, "
-- query to display all names of DENTISTS WHO SERVE PATIENT WITH ID $pID[0] in Appointments
SELECT dinfo.name FROM Employee_info AS dinfo WHERE 
dinfo.employee_sin IN (
	SELECT d.employee_sin FROM Employee AS d WHERE d.employee_id IN (
		SELECT dentist_id FROM Appointment WHERE patient_id='$pID[0]'
	)
);
"));

// Treatment query
$patientTreatments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Treatment WHERE patient_id='$pID[0]' ORDER BY appointment_id DESC;"));

// Appointment_Procedure query
$apptProcedures = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment_procedure WHERE patient_id='$pID[0]' ORDER BY date_of_procedure DESC;"));

// Invoice query - all of patient's invoice
$patientInvoice = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Invoice WHERE patient_id='$pID[0]' ORDER BY date_of_issue DESC;"));

// Review query (all reviews on the website)
$reviews = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Review ORDER BY date_of_review DESC;"));

// Get all the types of procedure
$procedureCodes = pg_fetch_all(pg_query($dbconn, "SELECT * FROM procedure_codes ORDER BY procedure_code;"));

// Get all the dentists
$doctors = pg_fetch_all(pg_query($dbconn, "SELECT E.employee_id, I.name
                                            FROM employee AS E, employee_info AS I 
                                            WHERE E.employee_sin = I.employee_sin AND (I.employee_type='d' OR I.employee_type='h');")); 

// Get the procedure IDs
$procedureIDs = pg_fetch_all(pg_query($dbconn, "SELECT * FROM appointment_procedure WHERE patient_id = '$pID[0]' AND invoice_id IS NULL;"));

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>DCMS - Patient Information</title>
        <link rel="icon" type="image/x-icon" href="images/information.png">
        <link rel="stylesheet" href="main.css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="patient_landing_style.css" />
    </head>
    <body>
        <!-- Logout Button START -->
        <div class="container">
            <div class="logout-btn">
                <a href="logout.php" class="logout-btn-text">Logout</a>
            </div>
        </div>
        <!-- Logout Button END -->

        <!-- CSS container START https://www.bootdey.com/snippets/view/user-profile-bio-graph-and-total-sales -->
        <div class="container bootstrap snippets bootdey">
            <div class="row">
                <div class="profile-nav col-md-3" style="position: sticky; top: 0px;">
                    <div class="panel">
                        <div class="user-heading round">
                            <h1>Profile of</h1>
                            <h2><?php echo $pName?></h2>
                        </div>

                        <ul class="nav nav-pills nav-stacked">
                            <!-- Font awesome fonts version 4: https://fontawesome.com/v4/icons/ -->
                            <li>
                                <a href="#patient_info"> <i class="fa fa-user"></i> Patient Information</a>
                            </li>
                            <li>
                                <a href="#patient_records"> <i class="fa fa-heart"></i> Medical Records</a>
                            </li>
                            <li>
                                <a href="#patient_appointments"> <i class="fa fa-calendar"></i> Appointments</a>
                            </li>
                            <li>
                                <a href="#patient_treatments"> <i class="fa fa-flag"></i> Treatments</a>
                            </li>
                            <li>
                                <a href="#patient_appointment_procedures"> <i class="fa fa-book"></i> Appointment Procedures</a>
                            </li>
                            <li>
                                <a href="#patient_invoices"> <i class="fa fa-credit-card"></i> Invoices</a>
                            </li>
                            
                        </ul>
                    </div>
                </div>
                <!-- Page Column START -->
                <div class="profile-info col-md-9">
                    <!-- Patient Information START -->

                    <?php
                    // This PHP block error traps the user inputs
                    // The PHP block that inserts the data into the Postgres database is below the form
                    if($_POST['edit']){
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {

                            $pnameErr = $pdobErr = $pnumErr = $pemailErr = $paddrErr = $pgenderErr = "";

                            // checks whether all mandatory fields are filled out or not

                            if (empty($_POST["fullname"])) {
                                $pnameErr = "Required";
                            }
                            if (empty($_POST["pdob"])) {
                                $pdobErr = "Required";
                            }
                            if (empty($_POST["pnum"])) {
                                $pnumErr = "Required";
                            }
                            if (empty($_POST["pemail"])) {
                                $pemailErr = "Required";
                            }
                            if (empty($_POST["paddr"])) {
                                $paddr = "Required";
                            }
                            if (empty($_POST["pgender"])) {
                                $paddr = "Required";
                            }
                        }
                    } elseif($_POST['add']) {
                        $dateError = $doctorError = $procedureError = $startTimeError = $endTimeError = $roomError = "";
                            
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {

                            // checks if all the required fiels are empty or not

                            if (empty($_POST["date_of_appointment"])) {
                                $dateError = "Required";
                            }
                            if (empty($_POST["start_time"])) {
                                $startTimeError = "Required";
                            }
                            if (empty($_POST["end_time"])) {
                                $endTimeError = "Required";
                            }
                            if ($_POST["doctorName"] == "-") {
                                $doctorError = "Required";
                            }
                            if ($_POST["procedure"] == "-") {
                                $procedureError = "Required";
                            }
                            if (empty($_POST["room"])) {
                                $roomError = "Required";
                            }
                        }
                    }elseif($_POST['addInvoice']) {
                        $invoiceDateError = $contactError = $chargeError = $insuranceError = $discountError = $penaltyError = $appProcError ="";
                            
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {

                            // checks if all the required fields are empty or not

                            if (empty($_POST["date_of_issue"])) {
                                 $invoiceDateError = "Required";
                            }
                            if (empty($_POST["contact_info"])) {
                                $contactError = "Required";
                            }
                             if (empty($_POST["patient_charge"])) {
                                $chargeError = "Required";
                            }
                             if (empty($_POST["insurance_charge"])) {
                                $insuranceError = "Required";
                            }
                             if (empty($_POST["patient_discount"])) {
                                $discountError = "Required";
                            }
                             if (empty($_POST["patient_penalty"])) {
                                $penaltyError = "Required";
                            }
                             if (empty($_POST["appProcID"])) {
                                $appProcError = "Required";
                            }
                        }
                    } 
                    
                    
                    ?>

                    <form  action = "" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

                        <div class="panel" id="patient_info">
                            <div class="bio-graph-heading">
                                <h3><i class="fa fa-user"></i> Patient Information</h3>
                            </div>
                            <div class="panel-body bio-graph-info">
                                <h1>
                                    Patient ID -
                                    <?php echo $pID[0] ?>
                                </h1>
                                <div class="row">
                                    <div class="bio-row">
                                        <p>
                                            <span>Full Name </span>
                                            <input type="text" id="fullname" name="fullname" placeholder="<?php echo $pName?>" value="<?php echo $pName?>" maxlength="255">
                                            <span class="error">* <?php echo $pnameErr;?></span>
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Gender </span>
                                            <input type="text" id="pgender" name="pgender" placeholder="<?php echo $pGender[0]?>" value="<?php echo $pGender[0]?>" maxlength="1">
                                            <span class="error">* <?php echo $pgenderErr;?></span>
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                    <p>
                                            <span>SIN </span>
                                            <?php echo $pSin[0] ?>
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Date Of Birth</span>
                                            <input type="text" id="pdob" name="pdob" placeholder="<?php echo $pDateOfBirth[0]?>" value="<?php echo $pDateOfBirth[0]?>" maxlength = "10">
                                            <span class="error">* <?php echo $pdobErr;?></span>
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Phone Number </span>
                                            <input type="text" id="pnum" name="pnum" placeholder="<?php echo $pPhone[0]?>" value="<?php echo $pPhone[0]?>" maxlength="20">
                                            <span class="error">* <?php echo $pnumErr;?></span>
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Email </span>
                                            <input type="text" id="pemail" name="pemail" placeholder="<?php echo $pEmail[0]?>" value="<?php echo $pEmail[0]?>" maxlength="255">
                                            <span class="error">* <?php echo $pemailErr;?></span>
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Address </span>
                                            <input type="text" id="paddr" name="paddr" placeholder="<?php echo $pAddress[0];?>" value="<?php echo $pAddress[0]?>" maxlength="255">
                                            <span class="error">* <?php echo $paddrErr;?></span>
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Insurance </span>
                                            <?php
                                            if ($pInsurance[0] == null) {
                                                $insVal = "";
                                            } else {
                                                $insVal = $pInsurance[0];
                                            }
                                            ?>
                                            <input type="text" id="pins" name="pins" placeholder="<?php echo $insVal?>" value="<?php echo $insVal?>" maxlength="255">

                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Representative </span>
                                            
                                            <?php
                                            if ($pRepresentative[0] == null) {
                                            $repVal = "None";


                                        } else {
                                            // pRepresentative[0] itself is a horrible string. These 4 lines of code below cleans the string and splits the parameters (name, phone, email, relationship) into an array
                                            $pRepresentative[0] = str_replace("(","",$pRepresentative[0]);
                                            $pRepresentative[0] = str_replace(")","",$pRepresentative[0]);
                                            $pRepresentative[0] = str_replace('"',"",$pRepresentative[0]);
                                            $pRepresentativeArr = preg_split ("/\,/", $pRepresentative[0]);
                                            
                                            $repName = $pRepresentativeArr[0];
                                            //echo $repName;
                                            $repPhone = $pRepresentativeArr[1];
                                            $repEmail = $pRepresentativeArr[2]; 
                                            $repRelation = $pRepresentativeArr[3];
                                           
                                        }
                                            ?>

                                            <p>
                                            <span>Name  </span>
                                            <input type="text" id="rname" name="rname" placeholder="<?php echo $repName?>" value="<?php echo $repName?>" maxlength="255">
                                            </p>

                                            <p>
                                            <span>Phone  </span>
                                            <input type="text" id="rphone" name="rphone" placeholder="<?php echo $repPhone?>" value="<?php echo $repPhone?>" maxlength="255">
                                            </p>

                                            <p>
                                            <span>Email  </span>
                                            <input type="text" id="remail" name="remail" placeholder="<?php echo $repEmail?>" value="<?php echo $repEmail?>" maxlength="255">
                                            </p>

                                            <p>
                                            <span>Relationship  </span>
                                            <input type="text" id="relo" name="relo" placeholder="<?php echo $repRelation?>" value="<?php echo $repRelation?>" maxlength="255">
                                            </p>

                                        </p>
                                    </div>
                                </div><input type="submit" class= "btn btn-primary" name="edit" value="Edit information"> 
                            </div>
                        </div>

                    </form>

                    <?php
                    
                    // this PHP block processes the patient info input after the response was submitted successfully

                    // if response was submitted successfully
                    if (!(empty($_POST["fullname"]) || 
                        empty($_POST["pdob"]) || 
                        empty($_POST["pnum"]) ||
                        empty($_POST["pemail"]) ||
                        empty($_POST["pgender"]) ||
                        empty($_POST["paddr"]))) {

                        echo "<h4>Submitting New Patient Information</h4>";

                        // update data in Patient_info table in Postgres
                        if ($_SERVER['REQUEST_METHOD'] === "POST") {
                            
                            // move the $_POST inputs into PHP variables for cleaner code
                            $pNameInput = str_replace("'", "''", $_POST['fullname']);
                            $pGenderInput = str_replace("'", "''", $_POST['pgender']);
                            $pDobInput = str_replace("'", "''", $_POST['pdob']);
                            $pNumInput = str_replace("'", "''", $_POST['pnum']);
                            $pEmailInput = str_replace("'", "''", $_POST['pemail']);
                            $pAddressInput = str_replace("'", "''", $_POST['paddr']);
                            $pInsInput = str_replace("'", "''", $_POST['pins']);
                            $pRepNameInput = str_replace("'", "''", $_POST['rname']);
                            $pRepPhoneInput = str_replace("'", "''", $_POST['rphone']);
                            $pRepEmailInput = str_replace("'", "''", $_POST['remail']);
                            $pRepRelaInput = str_replace("'", "''", $_POST['relo']);

                            echo "Editing... <br>";

                            $updatePatientInfoQuery = "
                                UPDATE Patient_info
                                SET address = '$pAddressInput',
                                    name = '$pNameInput',
                                    gender = '$pGenderInput',
                                    email = '$pEmailInput',
                                    phone = '$pNumInput',
                                    date_of_birth = '$pDobInput',
                                    insurance = '$pInsInput',
                                    rep = ROW('$pRepNameInput', '$pRepPhoneInput', '$pRepEmailInput', '$pRepRelaInput')
                                WHERE patient_sin='$pSin[0]';
                            ";

                            // $dbconn is the db connection in db.php
                            $updatePatientInfoResult = pg_query($dbconn, $updatePatientInfoQuery); // insert data into database

                            if (!$updatePatientInfoResult) {
                                echo pg_last_error($dbconn);
                            } else {
                                echo "<h5>You've sucessfully updated the information of $pName!<h5>";
                                echo "<h5><strong style=\"color:red\">Please refresh the page to view the changes</strong></h5>". "<br><br>";
                            }


                        } // end if ($_SERVER['REQUEST_METHOD'] === "POST")

                    } // end if (if response was submitted successfully)

                    elseif($_POST['edit_status']){
                        $updateStatus = "
                        UPDATE appointment
                        SET appointment_status = $1 
                        WHERE appointment_id= $2; 
                        ";
                        $statusInput = $_POST['cars'];
                        $aptId = $_POST['apt_id'];
                        
                        $updatePatientInfoResult = pg_query_params($dbconn, $updateStatus, array($statusInput,$aptId)); // insert data into database

                        if(!$updatePatientInfoResult) {
                            echo preg_last_error($dbconn);
                            echo "<h5>There was an error updating the status</h5>";
                        } else {
                            echo "<h5>You've sucessfully updated the status of appointment with ID $aptId!<h5>";
                            echo "<h5><strong style=\"color:red\">Please refresh the page to view the changes</strong></h5>". "<br><br>";
                        }
                    }
                    
                        
                    ?>
                    <!-- ==================================================================== -->

        

                    <!-- Patient Information END -->
                    <!-- ==================================================================== -->
                    <!-- Patient Records START -->

                    <div class="panel" id="patient_records">
                        <div class="bio-graph-heading">
                            <h3><i class="fa fa-heart"></i> Patient Medical Records</h3>
                        </div>
                        <div class="panel-body bio-graph-info">
                            <!-- List Patient Records here -->
                            <!-- Record ID | Patient details -->
                            <table id="records_grid" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Record ID</th>
                                        <th>Medical Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($patientRecords as $patientRecord => $patientRecords) :?>
                                    <tr>
                                        <td><?php echo $patientRecords['record_id'] ?></td>
                                        <td><?php echo $patientRecords['patient_details'] ?></td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                   <!-- Patient Records END -->
                    <!-- ==================================================================== -->
                    <!-- Patient Appointments START -->
                    
                    <div class="panel" id="patient_appointments">
                        <div class="bio-graph-heading">
                            <h3><i class="fa fa-calendar"></i> Patient Appointments</h3>
                        </div>
                        <div class="panel-body bio-graph-info">
                            <table id="appointments_grid" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Appointment ID</th>
                                        <th>Doctor Name</th>
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Type</th>
                                        <th>Room</th>
                                        <th>Status</th>
                                        <th>Editable Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($patientAppointments as $patientAppointment => $patientAppointments) :?>
                                    <form action="<?php echo "#patient_appointments" ?>" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                        <input name="apt_id" type="text" hidden value="<?php echo $patientAppointments['appointment_id'] ?>">
                                        <tr>
                                            <td><?php echo $patientAppointments['appointment_id'] ?></td>
                                            <td><?php 
                                                $doctorId = $patientAppointments['dentist_id'];
                                                $doctorSin = pg_fetch_row(pg_query($dbconn,"SELECT employee_sin FROM employee WHERE employee_id=$doctorId;"));
                                                $doctorName = pg_fetch_row(pg_query($dbconn, "SELECT name FROM employee_info WHERE employee_sin='$doctorSin[0]';"));
                                                echo $doctorName[0] ?></td>
                                            <!-- <td><?php //echo pg_fetch_result(pg_query($dbconn, "SELECT e_info.name FROM Employee_info AS e_info WHERE e_info.employee_sin in (SELECT e.employee_sin FROM Employee AS e WHERE e.employee_id='$patientAppointments['employee_id']');")); ?></td> -->
                                            <td><?php echo $patientAppointments['date_of_appointment'] ?></td>
                                            <td><?php echo $patientAppointments['start_time'] ?></td>
                                            <td><?php echo $patientAppointments['end_time'] ?></td>
                                            <td><?php echo $patientAppointments['appointment_type'] ?></td>
                                            <td><?php echo $patientAppointments['room'] ?></td>
                                            <td><?php echo $patientAppointments['appointment_status'] ?></td>
                                            <td>
                                            
                                                <?php 
                                                $status = $patientAppointments['appointment_status'];
                                                if ($status == 'Booked'){
                                                    echo "<select id='cars' name='cars'>
                                                        <option value='Booked' selected>Booked</option>
                                                        <option value='Completed'>Completed</option>
                                                        <option value='Cancelled'>Cancelled</option>
                                                        <option value='No Show'>No Show</option>
                                                    </select>";
                                                }

                                                elseif ($status == 'Completed'){
                                                    echo "<select id='cars' name='cars'>
                                                        <option value='Booked'>Booked</option>
                                                        <option value='Completed' selected>Completed</option>
                                                        <option value='Cancelled'>Cancelled</option>
                                                        <option value='No Show'>No Show</option>
                                                    </select>";
                                                }

                                                elseif ($status == 'Cancelled'){
                                                    echo "<select id='cars' name='cars'>
                                                        <option value='Booked'>Booked</option>
                                                        <option value='Completed'>Completed</option>
                                                        <option value='Cancelled' selected>Cancelled</option>
                                                        <option value='No Show'>No Show</option>
                                                    </select>";
                                                }
                                                elseif ($status == 'No Show'){
                                                    echo "<select id='cars' name='cars'>
                                                        <option value='Booked'>Booked</option>
                                                        <option value='Completed'>Completed</option>
                                                        <option value='Cancelled'>Cancelled</option>
                                                        <option value='No Show' selected>No Show</option>
                                                    </select>";
                                                } ?>

                                                <input type="submit" class= "btn btn-primary" style="padding:1px" name="edit_status" value="Save"> 
                                                

                                            </td>
                                        </tr>
                                    </form>

                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                        <hr>
            
                        <form action="<?php echo "#set_appointment"; ?>" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ;?>">
                        <div class="panel" id="set_appointment">
                            <div class="panel-body bio-graph-info">
                                <h1>Set an appointment for <?php echo $pName ?></h1>
                                <h5><span class="error">*</span> indicates required fields </h5>
                                <div class="row">
                                    <div class="bio-row">
                                        <p>
                                            <span>Date</span>
                                            <input type="date" id="date_of_appointment" name="date_of_appointment">
                                            <span class="error">* <?php echo $dateError?></span>
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Start time </span>
                                            <input type="time" id="start_time" name="start_time">
                                            <span class="error">* <?php echo $startTimeError?></span>  
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Dentist/Hygienist </span>
                                            <select name="doctorName" id="doctorName">
                                                <option>-</option>
                                                <?php 
                                                    foreach($doctors as $doctor => $doctors) :?>
                                                    <option value="<?php echo $doctors['name']?>">
                                                        <?php 

                                                        $doctorName = $doctors['name'];

                                                        $eLetterType = pg_fetch_row(pg_query($dbconn, "SELECT employee_type FROM employee_info WHERE name='$doctorName';"));

                                                        if($eLetterType[0] == 'd'){
                                                            $eType = "Dentist";
                                                        } 
                                                        elseif($eLetterType[0] == 'h'){
                                                            $eType = "Hygienist";
                                                        }
                                                        echo $doctors['name']. " - " . $eType ?>
                                                    </option>
                                                <?php endforeach?>
                                            </select>
                                            <span class="error">* <?php echo $doctorError?></span>
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>End time</span>
                                            <input type="time" id="end_time" name="end_time">
                                            <span class="error">* <?php echo $endTimeError?></span>
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Type of procedure </span>
                                            <select name="procedure" id="procedure">
                                                <option>-</option>
                                                <?php 
                                                    foreach($procedureCodes as $procedureCode => $procedureCodes) :?>
                                                    <option value="<?php echo $procedureCodes['procedure_code']?>">
                                                        <?php echo $procedureCodes['procedure_code'] . " - " . $procedureCodes['procedure_name']?>
                                                    </option>
                                                <?php endforeach?>
                                            </select>
                                            <span class="error">* <?php echo $procedureError ?></span>                                           
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                            <span>Room </span>
                                            <input type="number" id="room" name="room" placeholder="Enter room number">
                                            <span class="error">* <?php echo $roomError ?></span>
                                        </p>
                                    </div>                                   
                                </div>
                                <input class="btn btn-primary"type="submit" name="add" value="Set appointment"> 
                            </div>
                        </div> 
                        </form>
                    </div>
                    <?php 
                        if (!(empty($_POST["date_of_appointment"]) && empty($_POST["start_time"]) &&
                            empty($_POST["end_time"]) && empty($_POST["room"])) && $_POST["doctorName"] != "-" &&
                            $_POST["procedure"] != "-") {
        
                                if ($_SERVER['REQUEST_METHOD'] === "POST") {
                                    $doctorNameInput = $_POST['doctorName'];
                                    $dId = pg_fetch_row(pg_query($dbconn, "SELECT E.employee_id FROM employee AS E, employee_info AS I WHERE I.employee_sin=E.employee_sin AND name='$doctorNameInput';"));
                                    $dateInput = $_POST['date_of_appointment'];
                                    $startTimeInput = $_POST['start_time'] . ":00";
                                    $endTimeInput = $_POST['end_time'] . ":00";
                                    $appointmentInput = $_POST['procedure'];
                                    $roomInput = $_POST['room'];

                                    $query = "INSERT INTO appointment (patient_id,dentist_id,date_of_appointment,start_time,end_time,appointment_type,appointment_status,room) VALUES ('$pID[0]','$dId[0]','$dateInput','$startTimeInput','$endTimeInput','$appointmentInput','Booked','$roomInput')";
                                    $addAppointment = pg_query($dbconn, $query);
                                   
                                    if (!$addAppointment) {
                                        echo pg_last_error($dbconn);
                                        echo "<h5>There was an error when setting the appointment</h5>";
                                    } else {
                                        $time = $_POST['start_time'];
                                        echo "<h5>You've succesfully set an appointment for $pName on $dateInput at $time. </h5>";
                                        echo "<h5><strong style=\"color:red\">Please refresh to see the changes</strong>.<br></h5>";
                                    }
                                }
                        }
                    ?>
                    <!-- Patient Appointments END -->
                    <!-- ==================================================================== -->
                    <!-- Patient Treatments START -->

                    <div class="panel" id="patient_treatments">
                        <div class="bio-graph-heading">
                            <h3><i class="fa fa-flag"></i> Patient Treatments</h3>
                        </div>
                        <div class="panel-body bio-graph-info">
                            <table id="appointments_grid" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Treatment ID</th>
                                        <th>Treatment Type</th>
                                        <th>Medication</th>
                                        <th>Symptoms</th>
                                        <th>Tooth</th>
                                        <th>Comments</th>
                                        <th>Appointment ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($patientTreatments as $patientTreatment => $patientTreatments) :?>
                                    <tr>
                                        <td><?php echo $patientTreatments['treatment_id'] ?></td>
                                        <td><?php echo $patientTreatments['treatment_type'] ?></td>
                                        <td><?php echo $patientTreatments['medication'] ?></td>
                                        <td><?php echo $patientTreatments['symptoms'] ?></td>
                                        <td><?php echo $patientTreatments['tooth'] ?></td>
                                        <td><?php echo $patientTreatments['comments'] ?></td>
                                        <td><?php echo $patientTreatments['appointment_id'] ?></td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Patient Treatments END -->
                    <!-- ==================================================================== -->
                    <!-- Patient Appointment Procedures START -->

                    <div class="panel" id="patient_appointment_procedures">
                        <div class="bio-graph-heading">
                            <h3><i class="fa fa-book"></i> Appointment Procedures</h3>
                        </div>
                        <div class="panel-body bio-graph-info">
                            <table id="appointments_grid" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Procedure ID</th>
                                        <th>Appointment ID</th>
                                        <th>Date</th>
                                        <th>Invoice ID</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Tooth</th>
                                        <th><abbr title="Refers to the amount of procedure to perform">Amount</abbr></th>
                                        <th>Total Charge</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($apptProcedures as $apptProcedure => $apptProcedures) :?>
                                    <tr>
                                        <td><?php echo $apptProcedures['procedure_id'] ?></td>
                                        <td><?php echo $apptProcedures['appointment_id'] ?></td>
                                        <td><?php echo $apptProcedures['date_of_procedure'] ?></td>
                                        <td><?php echo $apptProcedures['invoice_id'] ?></td>
                                        <td><?php echo $apptProcedures['procedure_code'] ?></td>
                                        <td><?php echo $apptProcedures['appointment_description'] ?></td>
                                        <td><?php echo $apptProcedures['tooth'] ?></td>
                                        <td><?php echo $apptProcedures['amount_of_procedure'] ?></abbr></td>
                                        <td><?php echo $apptProcedures['total_charge'] ?></td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Patient Appointment Procedures END -->
                    <!-- ==================================================================== -->
                    <!-- Patient Invoice START -->

                    <div class="panel" id="patient_invoices">
                        <div class="bio-graph-heading">
                            <h3><i class="fa fa-credit-card"></i> Invoices</h3>
                        </div>
                        <div class="panel-body bio-graph-info">
                            <table id="appointments_grid" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Invoice ID</th>
                                        <th>Date Of Issue</th>
                                        <th>Contact Info</th>
                                        <th>Patient Charge</th>
                                        <th>Insurance Charge</th>
                                        <th>Discount</th>
                                        <th>Penalty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($patientInvoice as $invoice => $patientInvoice) :?>
                                    <tr>
                                        <td><?php echo $patientInvoice['invoice_id'] ?></td>
                                        <td><?php echo $patientInvoice['date_of_issue'] ?></td>
                                        <td><?php echo $patientInvoice['contact_info'] ?></td>
                                        <td><?php echo $patientInvoice['patient_charge'] ?></td>
                                        <td><?php echo $patientInvoice['insurance_charge'] ?></td>
                                        <td><?php echo $patientInvoice['discount'] ?></td>
                                        <td><?php echo $patientInvoice['penalty'] ?></td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Patient Invoice END -->

                    <!-- Edit patient invoice START -->
                    <form action="<?php echo "#set_invoice"; ?>" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ;?>">
                        <div class="panel" id="set_invoice">
                            <div class="panel-body bio-graph-info">
                                <h1>Set an invoice for <?php echo $pName ?></h1>
                                <p><i class="fa fa-question-circle"></i> Note that invoices can only be added for Appointment Procedures that do not currently have one.</p>
                                    <br>
                                <h5><span class="error">*</span> indicates required fields </h5>
                                <div class="row">
                                    <div class="bio-row">
                                        <p>
                                            <span>Date of Issue</span>
                                            <input type="date" id="date_of_issue" name="date_of_issue">
                                            <span class="error">* <?php echo $invoiceDateError?></span>
                                        </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                        <span>Appointment Procedure ID</span>
                                        <select name="appProcID" id="appProcID">
                                            <option>-</option>
                                            <?php 
                                                foreach($procedureIDs as $oneID => $procedureIDs) :?>
                                                <option value="<?php echo $procedureIDs['procedure_id']?>">
                                                    <?php echo $procedureIDs['procedure_id']?>
                                                </option>
                                            <?php endforeach?>
                                        </select>
                                        <span class="error">* <?php echo $appProcError ?></span>                                           
                                    </p>
                                    </div>
                                    <div class="bio-row">
                                        <p>
                                           <span>Contact Info</span>
                                            <input type="text" id="contact_info" name="contact_info" maxlength="255">
                                            <span class="error">* <?php echo $contactError ?></span>      
                                        </p>
                                    </div>

                                    <div class="bio-row">
                                        <p>
                                           <span>Patient Charge</span>
                                            <input type="number" min="0.00"step="0.01" id="patient_charge" name="patient_charge">
                                            <span class="error">* <?php echo $chargeError?></span>
                                        </p>
                                    </div>

                                    <div class="bio-row">
                                        <p>
                                           <span>Insurance</span>
                                            <input type="number" min="0.00"step="0.01" id="insurance_charge" name="insurance_charge">
                                            <span class="error">* <?php echo $insuranceError?></span>
                                        </p>
                                    </div>

                                    <div class="bio-row">
                                        <p>
                                           <span>Discount</span>
                                            <input type="number" min="0.00"step="0.01" id="patient_discount" name="patient_discount">
                                            <span class="error">* <?php echo $discountError?></span>
                                        </p>
                                    </div>

                                     <div class="bio-row">
                                        <p>
                                           <span>Penalty Fee</span>
                                            <input type="number" min="0.00"step="0.01" id="patient_penalty" name="patient_penalty">
                                            <span class="error">* <?php echo $penaltyError?></span>
                                        </p>
                                    </div>
                                                                       
                                </div>
                                <input class="btn btn-primary"type="submit" name="addInvoice" value="Add Invoice"> 
                            </div>
                        </div> 
                        </form>
                        <?php 
                        if (!(empty($_POST["date_of_issue"]) && empty($_POST["contact_info"]) &&
                            empty($_POST["patient_charge"]) && empty($_POST["patient_penalty"]) 
                            && empty($_POST["insurance_charge"]) && empty($_POST["discount"])) && $_POST["appProcID"] != "-") {
                                
                                if ($_SERVER['REQUEST_METHOD'] === "POST") {
                                    $contactInput = $_POST['contact_info'];                                  
                                    $dateIssueInput = $_POST['date_of_issue'];
                                    $chargeInput = $_POST['patient_charge'];
                                    $insuranceInput = $_POST['insurance_charge'];
                                    $discountInput = $_POST['patient_discount'];
                                    $penaltyInput = $_POST['patient_penalty'];
                                    $procedureIDInput = $_POST['appProcID'];

                                    $iquery = "INSERT INTO invoice (patient_id, date_of_issue, contact_info, patient_charge, insurance_charge, discount, penalty) VALUES ('$pID[0]', '$dateIssueInput', '$contactInput', '$chargeInput', '$insuranceInput', '$discountInput', '$penaltyInput')";
                                    $addInvoice = pg_query($dbconn, $iquery);

                                    // Get new invoice ID
                                    $invoiceIDQuery = pg_fetch_row(pg_query($dbconn, "SELECT invoice_id FROM invoice WHERE patient_id = '$pID[0]' ORDER BY invoice_id DESC;"));

                                    // update Appointment Procedure
                                    // TODO (?) : We can't set insurance_claim_id because we did not populate that table
                                    echo "AAA";
                                    echo $procedureIDInput; echo "BBB";

                                    $updateApptProcedure =  "UPDATE appointment_procedure SET invoice_id = '$invoiceIDQuery[0]', insurance_charge = '$insuranceInput', patient_charge = '$chargeInput' WHERE (procedure_id = '$procedureIDInput');";

                                    pg_query($dbconn, $updateApptProcedure);

                                    if (!$addInvoice) {
                                        echo "<h4>There was an error adding the invoice. Please fill in all the required fields</h4>";
                                    } else {
                                        echo "<h5>You've succesfully set invoice for $pName.</h5>";
                                        echo "<h5><strong style=\"color:red\">Please refresh to see the changes</strong>.<br></h5>";
                                    }
                                }
                        }
                    ?>
                    </div>
                    

                    <!-- edit patient Invoice END-->

                    
                </div>
                <!-- Page Column END -->



            </div>
            <!-- Inner container -->
        </div>
        <!-- CSS container END https://www.bootdey.com/snippets/view/user-profile-bio-graph-and-total-sales -->
        <br>
        <br>
        <br>
        <br>             
    </body>
    <script>
        // this if statement turns off the "Confirm Form Resubmission" and prevents multiple form submissions after a successful form submission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</html>