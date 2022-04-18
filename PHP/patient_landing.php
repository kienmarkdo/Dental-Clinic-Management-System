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
$patientRecords = pg_fetch_all(pg_query($dbconn, "SELECT record_id, patient_details FROM Patient_records WHERE patient_id='$pID[0]';"));

// Patient appointment details
// TODO: Not sure if the queries are working or not. Not sure how to print all the fetched apptIDs etc.
$patientAppointments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment WHERE patient_id='$pID[0]';"));



?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>DCMS - Patient Homepage</title>
        <!-- <link rel="stylesheet" href="main.css" /> -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="patient_landing_style.css" />
    </head>
    <body>
        <!-- CSS container START https://www.bootdey.com/snippets/view/user-profile-bio-graph-and-total-sales -->
        <div class="container bootstrap snippets bootdey">
            <div class="row">
                <div class="profile-nav col-md-3">
                    <div class="panel">
                        <div class="user-heading round">
                            <h1>Welcome,</h1>
                            <h2><?php echo $pName[0] ?></h2>
                        </div>

                        <ul class="nav nav-pills nav-stacked">
                            <li class="active">
                                <a href="#patient_info"> <i class="fa fa-user"></i> Patient Information</a>
                            </li>
                            <li>
                                <a href="#patient_records"> <i class="fa fa-heart"></i> Medical Records</a>
                            </li>
                            <li>
                                <a href="#patient_appointments"> <i class="fa fa-calendar"></i> Appointments</a>
                            </li>
                            <li>
                                <a href="#patient_appointment_procedures"> <i class="fa fa-book"></i> Appointment Procedures</a>
                            </li>
                            <li>
                                <a href="#patient_invoices"> <i class="fa fa-credit-card"></i> Invoices</a>
                            </li>
                            <li>
                                <a href="#patient_reviews"> <i class="fa fa-comments-o"></i> My Reviews</a>
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
                                        <?php echo $pName[0] ?>
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
                                        <?php echo $pInsurance[0] ?>
                                    </p>
                                </div>
                                <div class="bio-row">
                                    <p>
                                        <span>Representative </span>
                                        <?php echo $pRepresentative[0] ?>
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
                                    <?php foreach($patientRecords as $patientRecord =>
                                    $patientRecords) :?>
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
                            <!-- List Patient Records here -->
                            <!-- Record ID | Patient details -->
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
                                    <?php foreach($patientAppointments as $patientAppointment =>
                                    $patientAppointments) :?>
                                    <tr>
                                        <td><?php echo $patientAppointments['appointment_id'] ?></td>
                                        <td><?php echo $patientAppointments['employee_id'] ?></td>
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
                    <!-- Patient Appointment Prcocedure START -->

                    <div class="panel" id="patient_appointment_procedures">
                        <div class="bio-graph-heading">
                            <h3>Appointment Procedures</h3>
                        </div>
                        <div class="panel-body bio-graph-info">
                            <!-- List Patient Records here -->
                            <!-- Record ID | Patient details -->
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
                                    <?php foreach($patientAppointments as $patientAppointment =>
                                    $patientAppointments) :?>
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
                    <!-- Patient Appointment Procedures END -->

                    <!-- ==================================================================== -->
                    <!-- Patient Reviews START -->

                    <!-- Review Input Box -->
                    <div class="panel" id="patient_reviews">
                        <form>
                            <textarea placeholder="Whats in your mind today?" rows="2" class="form-control input-lg p-text-area"></textarea>
                        </form>
                        <footer class="panel-footer">
                            <button class="btn btn-warning pull-right">Post</button>
                            <ul class="nav nav-pills">
                                <li>
                                    <a href="#"><i class="fa fa-map-marker"></i></a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa fa-camera"></i></a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa fa-film"></i></a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa fa-microphone"></i></a>
                                </li>
                            </ul>
                        </footer>
                    </div>

                    <!-- Review Cards -->
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

                    <!-- Patient Reviews END -->
                </div>
                <!-- Page Column END -->



            </div>
            <!-- Inner container -->
        </div>
        <!-- CSS container END https://www.bootdey.com/snippets/view/user-profile-bio-graph-and-total-sales -->
        
    </body>
</html>
