<?php

ob_start();
session_start();

include 'functions.php';
include_once 'db.php';
error_reporting(0);

// get variable from previous page (index.php)
$patientUsername = $_SESSION['patientUsername'];

// Patient ID and patient info details
$pID = pg_fetch_row(pg_query($dbconn, "SELECT patient_id FROM user_account WHERE username = '$patientUsername';"));
$pSin = pg_fetch_row(pg_query($dbconn, "SELECT sin_info FROM Patient WHERE patient_id = '$pID[0]';"));
$pName = pg_fetch_row(pg_query($dbconn, "SELECT name FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pGender = pg_fetch_row(pg_query($dbconn, "SELECT gender FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pEmail = pg_fetch_row(pg_query($dbconn, "SELECT email FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pPhone = pg_fetch_row(pg_query($dbconn, "SELECT phone FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pDateOfBirth = pg_fetch_row(pg_query($dbconn, "SELECT date_of_birth FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pAddress = pg_fetch_row(pg_query($dbconn, "SELECT address FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pInsurance = pg_fetch_row(pg_query($dbconn, "SELECT insurance FROM Patient_info WHERE patient_sin='$pSin[0]';"));
$pRepresentative = pg_fetch_row(pg_query($dbconn, "SELECT rep FROM Patient_info WHERE patient_sin='$pSin[0]';"));

// Patient records details
$recordID = pg_fetch_row(pg_query($dbconn, "SELECT record_id FROM Patient_records WHERE patient_id='$pID[0]';"));
$recordDetails = pg_fetch_row(pg_query($dbconn, "SELECT patient_details FROM Patient_records WHERE patient_id='$pID[0]';"));

// Patient appointment details
// TODO: Not sure if the queries are working or not. Not sure how to print all the fetched apptIDs etc.
$apptID = pg_query($dbconn, "SELECT apptID FROM Appointment WHERE patient_id='$pID[0]';");
$apptDentistID = pg_query($dbconn, "SELECT dentist_id FROM Appointment WHERE patient_id='$pID[0]';");
$apptDateOfAppointment = pg_query($dbconn, "SELECT date_of_appointment FROM Appointment WHERE patient_id='$pID[0]';");
$apptStartTime = pg_query($dbconn, "SELECT start_time FROM Appointment WHERE patient_id='$pID[0]';");
$apptEndTime = pg_query($dbconn, "SELECT end_time FROM Appointment WHERE patient_id='$pID[0]';");
$apptType = pg_query($dbconn, "SELECT appointment_type FROM Appointment WHERE patient_id='$pID[0]';");
$apptStatus = pg_query($dbconn, "SELECT appointment_status FROM Appointment WHERE patient_id='$pID[0]';");
$apptRoom = pg_query($dbconn, "SELECT room FROM Appointment WHERE patient_id='$pID[0]';");

// // Patient appointment procedure details
// TODO: Correct the queries.
// $proID = pg_fetch_row(pg_query($dbconn, "SELECT procedure_id FROM Appointment_procedure WHERE patient_id='$pID[0]';"));
// $proInvoiceID = pg_fetch_row(pg_query($dbconn, "SELECT invoice_id FROM Appointment_procedure WHERE patient_id='$pID[0]';"));
// $proDateOfProcedure = pg_fetch_row(pg_query($dbconn, "SELECT date_of_procedure FROM Appointment_procedure WHERE patient_id='$pID[0]';"));
// $proCode = pg_fetch_row(pg_query($dbconn, "SELECT procedure_code FROM Appointment_procedure WHERE patient_id='$pID[0]';"));
// $proDescription = pg_fetch_row(pg_query($dbconn, "SELECT appointment_description FROM Appointment_procedure WHERE patient_id='$pID[0]';"));
// $proTooth = pg_fetch_row(pg_query($dbconn, "SELECT tooth FROM Appointment_procedure WHERE patient_id='$pID[0]';"));
// $proAmount = pg_fetch_row(pg_query($dbconn, "SELECT amount_of_procedure FROM Appointment_procedure WHERE patient_id='$pID[0]';"));
// $proPatientCharge = pg_fetch_row(pg_query($dbconn, "SELECT patient_charge FROM Appointment_procedure WHERE patient_id='$pID[0]';"));
// $progInsuranceCharge = pg_fetch_row(pg_query($dbconn, "SELECT insurance_charge FROM Appointment_procedure WHERE patient_id='$pID[0]';"));
// $proTotalCharge = pg_fetch_row(pg_query($dbconn, "SELECT total_charge FROM Appointment_procedure WHERE patient_id='$pID[0]';"));
// $proInsuranceClaimID = pg_fetch_row(pg_query($dbconn, "SELECT insurance_claim_id FROM Appointment_procedure WHERE patient_id='$pID[0]';"));


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
      <h1>Hello, <?php echo $pName[0] ?>!</h1>
      <br/>
      <!-- Start of patient information -->
      <div class="container">
         <div class="main-body">
            <div class="row gutters-sm">
               <div class="col-md-4 mb-3">
                  <div class="card">
                     <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center">
                           <div class="mt-3">
                              <!-- <h4></h4>
                                 <p class="text-secondary mb-1">Patient Information</p> -->
                              <h2>Patient Information</h2>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-8">
                  <div class="card mb-3">
                     <div class="card-body">
                        <div class="row">
                           <div class="col-sm-3">
                              <h6 class="mb-0">Full Name</h6>
                           </div>
                           <div class="col-sm-9 text-secondary">
                              <?php echo $pName[0] ?>
                           </div>
                        </div>
                        <hr>
                        <div class="row">
                           <div class="col-sm-3">
                              <h6 class="mb-0">SIN</h6>
                           </div>
                           <div class="col-sm-9 text-secondary">
                              <?php echo $pSin[0] ?>
                           </div>
                        </div>
                        <hr>
                        <div class="row">
                           <div class="col-sm-3">
                              <h6 class="mb-0">Address</h6>
                           </div>
                           <div class="col-sm-9 text-secondary">
                              <?php echo $pAddress[0] ?>
                           </div>
                        </div>
                        <hr>
                        <div class="row">
                           <div class="col-sm-3">
                              <h6 class="mb-0">Gender</h6>
                           </div>
                           <div class="col-sm-9 text-secondary">
                              <?php echo $pGender[0] ?>
                           </div>
                        </div>
                        <hr>
                        <div class="row">
                           <div class="col-sm-3">
                              <h6 class="mb-0">Email</h6>
                           </div>
                           <div class="col-sm-9 text-secondary">
                              <?php echo $pEmail[0] ?>
                           </div>
                        </div>
                        <hr>
                        <div class="row">
                           <div class="col-sm-3">
                              <h6 class="mb-0">Phone</h6>
                           </div>
                           <div class="col-sm-9 text-secondary">
                              <?php echo $pPhone[0] ?>
                           </div>
                        </div>
                        <hr>
                        <div class="row">
                           <div class="col-sm-3">
                              <h6 class="mb-0">Date of birth</h6>
                           </div>
                           <div class="col-sm-9 text-secondary">
                              <?php echo $pDateOfBirth[0] ?>
                           </div>
                        </div>
                        <hr>
                        <div class="row">
                           <div class="col-sm-3">
                              <h6 class="mb-0">Insurance</h6>
                           </div>
                           <div class="col-sm-9 text-secondary">
                              <?php echo $pInsurance[0] ?>
                           </div>
                        </div>
                        <hr>
                        <div class="row">
                           <div class="col-sm-3">
                              <h6 class="mb-0">Representative</h6>
                           </div>
                           <div class="col-sm-9 text-secondary">
                              <?php echo $pRepresentative[0] ?>
                           </div>
                        </div>
                        <hr>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- End of patient information -->

   </body>
</html>