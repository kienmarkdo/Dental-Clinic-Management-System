<?php

ob_start();
session_start();

include 'functions.php';
include_once 'db.php';
error_reporting(0);

// get variable from submit button
$pName =  $_GET['viewPatient']; 

// Patient ID and patient info details
$pSin = pg_fetch_row(pg_query($dbconn, "SELECT patient_sin FROM Patient_info WHERE name = '$pName';"));
$pID = pg_fetch_row(pg_query($dbconn, "SELECT patient_id FROM Patient WHERE sin_info = '$pSin[0]';"));
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
$dentists = pg_fetch_all(pg_query($dbconn, "SELECT E.employee_id, I.name
                                            FROM employee AS E, employee_info AS I 
                                            WHERE E.employee_sin = I.employee_sin AND I.employee_type='d'; ")) 

?>

<!DOCTYPE html>
<html lang="en" style="scroll-behavior: smooth;">
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
                <div class="profile-nav col-md-3">
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
                        $dateError = $dentistError = $procedureError = $startTimeError = $endTimeError = $roomError = "";
                            
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
                            if ($_POST["dentistName"] == "-") {
                                $dentistError = "Required";
                            }
                            if ($_POST["procedure"] == "-") {
                                $procedureError = "Required";
                            }
                            if (empty($_POST["room"])) {
                                $roomError = "Required";
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
                                </div><input type="submit" name="edit"> 
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

                        echo "<h2>Submitting New Patient Information</h2>";
                        echo "<br>";

                        
                        if(isset($_POST['fullname'])) {
                            echo "Full Name: ". htmlspecialchars($_POST['fullname'])."<br>";
                        }
                        if(isset($_POST['paddr'])) {
                            echo "Gender: ".htmlspecialchars($_POST['pgender'])."<br>";
                        }
                        if(isset($_POST['pdob'])) {
                            echo "Date of birth: ".htmlspecialchars($_POST['pdob'])."<br>";
                        }
                        if(isset($_POST['pnum'])) {
                            echo "Phone Number: ".htmlspecialchars($_POST['pnum'])."<br>";
                        }
                        if(isset($_POST['pemail'])) {
                            echo "Email: ".htmlspecialchars($_POST['pemail'])."<br>";
                        }
                        if(isset($_POST['paddr'])) {
                            echo "Address: ".htmlspecialchars($_POST['paddr'])."<br>";
                        }
                        if(isset($_POST['pins'])) {
                            echo "Insurance: ".htmlspecialchars($_POST['pins'])."<br>";
                        }
                        if(isset($_POST['rname'])) {
                            echo "Representative name:  ".htmlspecialchars($_POST['rname'])."<br>";
                        }
                        if(isset($_POST['rphone'])) {
                            echo "Representative phone:  ".htmlspecialchars($_POST['rphone'])."<br>";
                        }
                        if(isset($_POST['remail'])) {
                            echo "Representative email:  ".htmlspecialchars($_POST['remail'])."<br>";
                        }
                         if(isset($_POST['relo'])) {
                            echo "Representative relationship to patient:  ".htmlspecialchars($_POST['relo'])."<br>";
                        }


                        echo "<br>";

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

                            echo "Adding... <br>";

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
                                echo "Updated patient information successfully!<br>";
                                echo "<strong style=\"color:red\">Please refresh the page to view the changes</strong>" . "<br><br>";
                            }


                        } // end if ($_SERVER['REQUEST_METHOD'] === "POST")

                    } // end if (if response was submitted successfully)
                    
                        
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
                                        <th>Dentist ID</th>
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($patientAppointments as $patientAppointment => $patientAppointments) :?>
                                    <tr>
                                        <td><?php echo $patientAppointments['appointment_id'] ?></td>
                                        <td><?php echo $patientAppointments['dentist_id'] ?></td>
                                        <!-- <td><?php //echo pg_fetch_result(pg_query($dbconn, "SELECT e_info.name FROM Employee_info AS e_info WHERE e_info.employee_sin in (SELECT e.employee_sin FROM Employee AS e WHERE e.employee_id='$patientAppointments['employee_id']');")); ?></td> -->
                                        <td><?php echo $patientAppointments['date_of_appointment'] ?></td>
                                        <td><?php echo $patientAppointments['start_time'] ?></td>
                                        <td><?php echo $patientAppointments['end_time'] ?></td>
                                        <td><?php echo $patientAppointments['appointment_type'] ?></td>
                                        <td><?php echo $patientAppointments['appointment_status'] ?></td>
                                        <td><?php echo $patientAppointments['room'] ?></td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                        <hr>
            
                        <form action="<?php echo "#patient_appointments"; ?>" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ;?>">
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
                                            <span>Dentist </span>
                                            <select name="dentistName" id="dentistName">
                                                <option>-</option>
                                                <?php 
                                                    foreach($dentists as $dentist => $dentists) :?>
                                                    <option value="<?php echo $dentists['name']?>">
                                                        <?php echo $dentists['name'] ?>
                                                    </option>
                                                <?php endforeach?>
                                            </select>
                                            <span class="error">* <?php echo $dentistError?></span>
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
                                <input type="submit" name="add"> 
                            </div>
                        </div> 
                        </form>
                    </div>
                    <?php 
                        if (!(empty($_POST["date_of_appointment"]) && empty($_POST["start_time"]) &&
                            empty($_POST["end_time"]) && empty($_POST["room"])) && $_POST["dentistName"] != "-" &&
                            $_POST["procedure"] != "-") {
        
                                if ($_SERVER['REQUEST_METHOD'] === "POST") {
                                    $dentistNameInput = $_POST['dentistName'];
                                    $dId = pg_fetch_row(pg_query($dbconn, "SELECT E.employee_id FROM employee AS E, employee_info AS I WHERE I.employee_sin=E.employee_sin AND name='$dentistNameInput';"));
                                    $dateInput = $_POST['date_of_appointment'];
                                    $startTimeInput = $_POST['start_time'] . ":00";
                                    $endTimeInput = $_POST['end_time'] . ":00";
                                    $appointmentInput = $_POST['procedure'];
                                    $roomInput = $_POST['room'];

                                    $query = "INSERT INTO appointment (patient_id,dentist_id,date_of_appointment,start_time,end_time,appointment_type,appointment_status,room) VALUES ('$pID[0]','$dId[0]','$dateInput','$startTimeInput','$endTimeInput','$appointmentInput','Booked','$roomInput')";
                                    $addAppointment = pg_query($dbconn, $query);
                                   
                                    if (!$addAppointment) {
                                        echo pg_last_error($dbconn);
                                        echo "<h1>ERROR</h1>";
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
                                        <th><abbr title="This is actually the Procedure Code (1: Teeth Cleanings, 2: Teeth Whitening, 3: Extractions, 4: Veneers, 5: Fillings, 6: Crowns, 7: Root Canal, 8: Braces/Invisalign, 9: Bonding, 10: Dentures)">Amount</abbr></th>
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

                    
                </div>
                <!-- Page Column END -->



            </div>
            <!-- Inner container -->
        </div>
        <!-- CSS container END https://www.bootdey.com/snippets/view/user-profile-bio-graph-and-total-sales -->
        
    </body>
    <script>
        // this if statement turns off the "Confirm Form Resubmission" and prevents multiple form submissions after a successful form submission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</html>
