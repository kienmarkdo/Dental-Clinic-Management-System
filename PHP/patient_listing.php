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

// TODO: All of this code below is me trying to print $apptDentistNames, one name at a time...
// print_r($apptDentistNames);

// for ($x = 0; $x <= sizeof($apptDentistNames); $x++) {
//     echo json_encode($apptDentistNames[$x]);
// }

// foreach($apptDentistNames as $dentistName)
// {
//     echo $dentistName['name'] . "<br/>";
// }

// echo '<table>
//         <tr>
//          <td>Dentist Name</td>
//         </tr>';

// foreach($apptDentistNames as $dentistName)
// {
//     echo '<tr>
//             <td>'. $dentistName.'</td>
//           </tr>';
// }
// echo '</table>';

// Treatment query
$patientTreatments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Treatment WHERE patient_id='$pID[0]' ORDER BY appointment_id DESC;"));

// Appointment_Procedure query
$apptProcedures = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment_procedure WHERE patient_id='$pID[0]' ORDER BY date_of_procedure DESC;"));

// Invoice query - all of patient's invoice
$patientInvoice = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Invoice WHERE patient_id='$pID[0]' ORDER BY date_of_issue DESC;"));

// Review query (all reviews on the website)
$reviews = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Review ORDER BY date_of_review DESC;"));

?>

<!DOCTYPE html>
<html lang="en" style="scroll-behavior: smooth;">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>DCMS - Patient Homepage</title>
        <link rel="icon" type="image/x-icon" href="images/information.png">
        <link rel="stylesheet" href="main.css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="patient_landing_style.css" />
    </head>
    <body>
        <!-- Logout Button START -->
        <div class="container" style="position: sticky; top: 0px; z-index:1">
            <div class="logout-btn bg-primary">
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
                    <div class="panel" id="patient_info">
                        <div class="bio-graph-heading">
                            <h3>Patient Information</h3>
                        </div>
                        <div class="panel-body bio-graph-info">
                            <h1>
                                Patient ID -
                                <?php echo $pID[0] ?>
                            </h1>
                            <div class="row">
                                <!-- <div class="bio-row">
                                    <p><span>Patient ID </span><?php echo $pName[0] ?></p>
                                </div> -->
                                <div class="bio-row">
                                    <p>
                                        <span>Full Name </span>
                                        <?php echo $pName?>
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
                                        <?php echo $pDateOfBirth[0] ?>
                                    </p>
                                </div>
                                <div class="bio-row">
                                    <p>
                                        <span>Phone Number </span>
                                        <?php echo $pPhone[0] ?>
                                    </p>
                                </div>
                                <div class="bio-row">
                                    <p>
                                        <span>Email </span>
                                        <?php echo $pEmail[0] ?>
                                    </p>
                                </div>
                                <div class="bio-row">
                                    <p>
                                        <span>Address </span>
                                        <?php echo $pAddress[0] ?>
                                    </p>
                                </div>
                                <div class="bio-row">
                                    <p>
                                        <span>Insurance </span>
                                        <?php
                                        if ($pInsurance[0] == null) {
                                        echo "None";
                                        } else {
                                        echo $pInsurance[0];
                                        }
                                        ?>

                                    </p>
                                </div>
                                <div class="bio-row">
                                    <p>
                                        <span>Representative </span>
                                        
                                        <?php
                                        if ($pRepresentative[0] == null) {
                                        echo "None";
                                    } else {
                                        // pRepresentative[0] itself is a horrible string. These 4 lines of code below cleans the string and splits the parameters (name, phone, email, relationship) into an array
                                        $pRepresentative[0] = str_replace("(","",$pRepresentative[0]);
                                        $pRepresentative[0] = str_replace(")","",$pRepresentative[0]);
                                        $pRepresentative[0] = str_replace('"',"",$pRepresentative[0]);
                                        $pRepresentativeArr = preg_split ("/\,/", $pRepresentative[0]);
                                        echo "<ul>";
                                        echo "<li>Name: " . $pRepresentativeArr[0] . "</li>";
                                        echo "<li>Phone: " . $pRepresentativeArr[1] . "</li>";
                                        echo "<li>Email: " . $pRepresentativeArr[2] . "</li>";
                                        echo "<li>Relationship: " . $pRepresentativeArr[3] . "</li>";
                                        echo "</ul>";
                                    }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ==================================================================== -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="bio-chart">
                                        <div style="display: inline; width: 100px; height: 100px;">
                                            <canvas width="100" height="100px"></canvas>
                                            <input
                                                class="knob"
                                                data-width="100"
                                                data-height="100"
                                                data-displayprevious="true"
                                                data-thickness=".2"
                                                value="35"
                                                data-fgcolor="#e06b7d"
                                                data-bgcolor="#e8e8e8"
                                                style="
                                                    width: 54px;
                                                    height: 33px;
                                                    position: absolute;
                                                    vertical-align: middle;
                                                    margin-top: 33px;
                                                    margin-left: -77px;
                                                    border: 0px;
                                                    font-weight: bold;
                                                    font-style: normal;
                                                    font-variant: normal;
                                                    font-stretch: normal;
                                                    font-size: 20px;
                                                    line-height: normal;
                                                    font-family: Arial;
                                                    text-align: center;
                                                    color: rgb(224, 107, 125);
                                                    padding: 0px;
                                                    -webkit-appearance: none;
                                                    background: none;
                                                "
                                            />
                                        </div>
                                    </div>
                                    <div class="bio-desk">
                                        <h4 class="red">Envato Website</h4>
                                        <p>Started : 15 July</p>
                                        <p>Deadline : 15 August</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="bio-chart">
                                        <div style="display: inline; width: 100px; height: 100px;">
                                            <canvas width="100" height="100px"></canvas>
                                            <input
                                                class="knob"
                                                data-width="100"
                                                data-height="100"
                                                data-displayprevious="true"
                                                data-thickness=".2"
                                                value="63"
                                                data-fgcolor="#4CC5CD"
                                                data-bgcolor="#e8e8e8"
                                                style="
                                                    width: 54px;
                                                    height: 33px;
                                                    position: absolute;
                                                    vertical-align: middle;
                                                    margin-top: 33px;
                                                    margin-left: -77px;
                                                    border: 0px;
                                                    font-weight: bold;
                                                    font-style: normal;
                                                    font-variant: normal;
                                                    font-stretch: normal;
                                                    font-size: 20px;
                                                    line-height: normal;
                                                    font-family: Arial;
                                                    text-align: center;
                                                    color: rgb(76, 197, 205);
                                                    padding: 0px;
                                                    -webkit-appearance: none;
                                                    background: none;
                                                "
                                            />
                                        </div>
                                    </div>
                                    <div class="bio-desk">
                                        <h4 class="terques">ThemeForest CMS</h4>
                                        <p>Started : 15 July</p>
                                        <p>Deadline : 15 August</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="bio-chart">
                                        <div style="display: inline; width: 100px; height: 100px;">
                                            <canvas width="100" height="100px"></canvas>
                                            <input
                                                class="knob"
                                                data-width="100"
                                                data-height="100"
                                                data-displayprevious="true"
                                                data-thickness=".2"
                                                value="75"
                                                data-fgcolor="#96be4b"
                                                data-bgcolor="#e8e8e8"
                                                style="
                                                    width: 54px;
                                                    height: 33px;
                                                    position: absolute;
                                                    vertical-align: middle;
                                                    margin-top: 33px;
                                                    margin-left: -77px;
                                                    border: 0px;
                                                    font-weight: bold;
                                                    font-style: normal;
                                                    font-variant: normal;
                                                    font-stretch: normal;
                                                    font-size: 20px;
                                                    line-height: normal;
                                                    font-family: Arial;
                                                    text-align: center;
                                                    color: rgb(150, 190, 75);
                                                    padding: 0px;
                                                    -webkit-appearance: none;
                                                    background: none;
                                                "
                                            />
                                        </div>
                                    </div>
                                    <div class="bio-desk">
                                        <h4 class="green">VectorLab Portfolio</h4>
                                        <p>Started : 15 July</p>
                                        <p>Deadline : 15 August</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-body">
                                    <div class="bio-chart">
                                        <div style="display: inline; width: 100px; height: 100px;">
                                            <canvas width="100" height="100px"></canvas>
                                            <input
                                                class="knob"
                                                data-width="100"
                                                data-height="100"
                                                data-displayprevious="true"
                                                data-thickness=".2"
                                                value="50"
                                                data-fgcolor="#cba4db"
                                                data-bgcolor="#e8e8e8"
                                                style="
                                                    width: 54px;
                                                    height: 33px;
                                                    position: absolute;
                                                    vertical-align: middle;
                                                    margin-top: 33px;
                                                    margin-left: -77px;
                                                    border: 0px;
                                                    font-weight: bold;
                                                    font-style: normal;
                                                    font-variant: normal;
                                                    font-stretch: normal;
                                                    font-size: 20px;
                                                    line-height: normal;
                                                    font-family: Arial;
                                                    text-align: center;
                                                    color: rgb(203, 164, 219);
                                                    padding: 0px;
                                                    -webkit-appearance: none;
                                                    background: none;
                                                "
                                            />
                                        </div>
                                    </div>
                                    <div class="bio-desk">
                                        <h4 class="purple">Adobe Muse Template</h4>
                                        <p>Started : 15 July</p>
                                        <p>Deadline : 15 August</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Patient Information END -->
                    <!-- ==================================================================== -->
                    <!-- Patient Records START -->

                    <div class="panel" id="patient_records">
                        <div class="bio-graph-heading">
                            <h3>Patient Medical Records</h3>
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
                            <h3>Patient Appointments</h3>
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
                    </div>
                    <!-- Patient Appointments END -->
                    <!-- ==================================================================== -->
                    <!-- Patient Treatments START -->

                    <div class="panel" id="patient_treatments">
                        <div class="bio-graph-heading">
                            <h3>Patient Treatments</h3>
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
                            <h3>Appointment Procedures</h3>
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
                            <h3>Invoices</h3>
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
</html>
