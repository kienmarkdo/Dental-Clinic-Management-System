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
$patientAppointments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment WHERE patient_id='$pID[0]' ORDER BY appointment_id DESC;")); // assume that latest appointment_id means latest date_of_appointment
// apptDentistNames stores the names of all the dentists who served this patient
$apptDentistNames = pg_fetch_all(pg_query($dbconn, "
-- query to display all names of DENTISTS WHO SERVE PATIENT WITH ID $pID[0] in Appointments
SELECT dinfo.name FROM Employee_info AS dinfo WHERE 
dinfo.employee_sin IN (
    SELECT d.employee_sin FROM Employee AS d WHERE d.employee_id IN (
        SELECT dentist_id FROM Appointment WHERE patient_id='$pID[0]'
    )
);
"));

//Get all dentist names associated with dentist ids
$dentist_names_results = pg_fetch_all(pg_query($dbconn, "SELECT DISTINCT a.dentist_id, e_info.name AS dentist_name FROM appointment a JOIN Employee e ON a.dentist_id = e.employee_id JOIN Employee_info e_info ON e.employee_sin = e_info.employee_sin;"));

$d_id_to_name = array(); //array that associates dentist IDs to dentist names
foreach ($dentist_names_results as $dentist_names_result) {
    $d_id_to_name[$dentist_names_result["dentist_id"]] = $dentist_names_result["dentist_name"];
}

// Treatment query
$patientTreatments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Treatment WHERE patient_id='$pID[0]' ORDER BY appointment_id DESC;"));

// Appointment_Procedure query
$apptProcedures = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment_procedure WHERE patient_id='$pID[0]' ORDER BY procedure_id DESC;")); // assume that latest procedure_id means latest date_of_procedure

// Invoice query - all of patient's invoice
$patientInvoice = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Invoice WHERE patient_id='$pID[0]' ORDER BY invoice_id DESC;")); // assume that latest review ID means latest date_of_issue

// Review query (all reviews on the website)
$reviews = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Review ORDER BY review_id DESC;"));
$sorting = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sorting =  $_POST["sorting"];
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>DCMS - Patient Homepage</title>
        <link rel="icon" type="image/x-icon" href="images/toothmap.png">
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
                            <h1>Welcome,</h1>
                            <h2><?php echo $pName[0] ?></h2>
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
                            <li>
                                <a href="#patient_reviews"> <i class="fa fa-comments-o"></i> Reviews</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Page Column START -->
                <div class="profile-info col-md-9">
                    <!-- Patient Information START -->
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
                                        <?php echo $pName[0] ?>
                                    </p>
                                </div>
                                <div class="bio-row">
                                    <p>
                                        <span>Gender </span>
                                        <?php echo $pGender[0] ?>
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
                                        <br>
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
                                    <!-- Populate table with patient records data from Postgres -->
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
                        <form action = "patient_landing.php#patient_appointments" method = "post" style="margin: auto; width: 40%; padding: 10px;" >
                            <label for="sorting">Sort by: </label>
                            <select name="sorting" id="sorting">
                                <?php 
                                // This $apptOptionArr and the foreach loop below dynamic populates the Sort By dropdown menu. The purpose is to retain the sort option selected by the user
                                $apptOptionArr = array("Appointment ID (High to Low)" => "apptSort1", "Appointment ID (Low to High)" => "apptSort2", "Dentist Name" => "apptSort3", "Date (Latest)" => "apptSort4", "Date (Oldest)" => "apptSort5", "Type" => "apptSort6", "Status" => "apptSort7");
                                foreach($apptOptionArr as $key => $value){
                                    $isSelected = "";
                                    if($_POST["sorting"] == $value){
                                        $isSelected = "selected";
                                    }
                                    echo '<option value="'.$value.'"'.$isSelected.'>'.$key.'</option>';
                                }
                                ?>
                                <!-- This is not preferred because the dropdown menu will be static and does not retain the user selected sorting option
                                <option value="apptSort1">Appointment ID (High to Low)</option>
                                <option value="apptSort2">Appointment ID (Low to High)</option>
                                <option value="apptSort3">Dentist Name</option>
                                <option value="apptSort4">Date (Latest)</option>
                                <option value="apptSort5">Date (Oldest)</option>
                                <option value="apptSort6">Type</option>
                                <option value="apptSort7">Status</option> -->
                            </select>
                            <input class="" type="submit" value="Submit"/><br>
                        </form>
                        <div class="panel-body bio-graph-info">
                            <table id="appointments_grid" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Appointment ID</th>
                                        <th>Dentist Name</th>
                                        <th>Date</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Populate table with patient appointments data from Postgres -->
                                    <?php 
                                    switch ($sorting) {
                                        case "apptSort1":
                                            $patientAppointments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment WHERE patient_id='$pID[0]' ORDER BY appointment_id DESC;"));
                                            break;
                                        case "apptSort2":
                                            $patientAppointments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment WHERE patient_id='$pID[0]' ORDER BY appointment_id;"));
                                            break;
                                        case "apptSort3":
                                            $patientAppointments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment WHERE patient_id='$pID[0]' ORDER BY dentist_id"));
                                            break;
                                        case "apptSort4":
                                            $patientAppointments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment WHERE patient_id='$pID[0]' ORDER BY date_of_appointment DESC;"));
                                            break;
                                        case "apptSort5":
                                            $patientAppointments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment WHERE patient_id='$pID[0]' ORDER BY date_of_appointment;"));
                                            break;
                                        case "apptSort6":
                                            $patientAppointments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment WHERE patient_id='$pID[0]' ORDER BY appointment_type;"));
                                            break;
                                        case "apptSort7":
                                            $patientAppointments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment WHERE patient_id='$pID[0]' ORDER BY appointment_status;"));
                                            break;
                                        default:
                                            // same as case 1
                                            $patientAppointments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment WHERE patient_id='$pID[0]' ORDER BY appointment_id DESC;"));
                                      }
                                    foreach($patientAppointments as $patientAppointment => $patientAppointments) :?>
                                    <tr>
                                        <td><?php echo $patientAppointments['appointment_id'] ?></td>
                                        <!-- Change this line below to dentist name instead of dentist_id if possible -->
                                        <td><?php echo $d_id_to_name[$patientAppointments['dentist_id']] ?></td>
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

                    <!-- ==================================================================== -->
                    <!-- Patient Reviews START -->
                    
                    <!-- Patient Review VIEW ALL EXISTING REVIEWS START -->

                    <div class="panel" id="patient_reviews">
                        <div class="bio-graph-heading">
                            <h3><i class="fa fa-comments-o"></i> Reviews</h3>
                        </div>
                        <div class="panel-body bio-graph-info">
                            <table id="appointments_grid" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Review ID</th>
                                        <th>Dentist Name</th>
                                        <th>Description</th>
                                        <th>Professionalism</th>
                                        <th>Communication</th>
                                        <th>Cleanliness</th>
                                        <th>Date Of Review</th>
                                        <th>Procedure ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Populate table with patient reviews data from Postgres -->
                                    <?php foreach($reviews as $review => $reviews) :?>
                                    <tr>
                                        <td><?php echo $reviews['review_id'] ?></td>
                                        <td><?php echo $reviews['dentist_name'] ?></td>
                                        <td><?php echo $reviews['review_description'] ?></td>
                                        <td><?php echo $reviews['professionalism'] ?></td>
                                        <td><?php echo $reviews['communication'] ?></td>
                                        <td><?php echo $reviews['cleanliness'] ?></td>
                                        <td><?php echo $reviews['date_of_review'] ?></td>
                                        <td><?php echo $reviews['procedure_id'] ?></td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Patient Review VIEW ALL EXISTING REVIEWS END -->

                    <!-- Review Input Box -->
                    <?php
                    // This PHP block error checks the user inputs

                    // define variables and set to empty values
                    
                        
                        $professionalismErr = $communicationErr = $cleanlinessErr = $dentistNameErr = $procedureIDErr = "";
                        $comment = "";

                        if ($_SERVER["REQUEST_METHOD"] == "POST") {

                            // checks whether all mandatory fields are filled out or not

                            if (empty($_POST["dentistName"])) {
                                $dentistNameErr = "Required";
                            }
                            if (empty($_POST["professionalism"])) {
                                $professionalismErr = "Required";
                            }
                            if (empty($_POST["communication"])) {
                                $communicationErr = "Required";
                            }
                            if (empty($_POST["cleanliness"])) {
                                $cleanlinessErr = "Required";
                            }
                            if (empty($_POST["procedure_id"])) {
                                $procedureIDErr = "Required";
                            }

                            if (empty($_POST["comment"])) {
                                $comment = "";
                            } else {
                                $comment = test_input($_POST["comment"]);
                            }
                        }

                        function test_input($data)
                        {
                            $data = trim($data);
                            $data = stripslashes($data);
                            $data = str_replace("\\", "", $data);
                            $data = htmlspecialchars($data);
                            return $data;
                        }
                    
                    ?>

                    <h2>Tell Us About Your Experience</h2>
                    
                    <form action = "patient_landing.php#patient_reviews_submit" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <div class="panel" id="patient_reviews_submit">
                            <!-- Comment box -->
                            <textarea name="comment" placeholder="Leave us an anonymous review! (Max. 255 characters)" rows="4" class="form-control input-lg p-text-area" maxlength="255" style="color:#6C6C6C"><?php echo $comment;?></textarea>
                            
                            <footer class="panel-footer">
                                <!-- Submit button -->
                                <input class="btn btn-warning pull-right" type="submit" value="Submit"></input>

                                <ul> <!-- This makes it look nicer, but it covers a bit of the Submit button... style="position:relative; right:40px; top:8px; z-index: 1" -->
                                    <!-- <li> -->
                                        <!-- Dentist Name Working -->
                                        <label for="dentistName">Dentist:<span class="error">* <?php echo $dentistNameErr;?></span></label>
                                        <select name="dentistName" id="dentistName">
                                            <option value="">-</option>
                                            <!-- Populate dropdown menu with DENTIST NAMES WHO ARE IN THE PATIENT'S APPOINTMENT TABLE from Postgres -->
                                            <?php foreach($apptDentistNames as $dentistName => $apptDentistNames) :?>
                                            <option value="<?php echo $apptDentistNames['name'];?>"><?php echo $apptDentistNames['name'];?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <!-- </li> -->
                                    <!-- <br> -->
                                    <!-- Professionalism Working -->
                                    <!-- <li> -->
                                        <label for="professionalism">Professionalism:<span class="error">* <?php echo $professionalismErr;?></span></label>
                                        <select name="professionalism" id="professionalism">
                                            <option value="">-</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    <!-- </li> -->
                                    <!-- <br> -->
                                    <!-- Communication Working -->
                                    <!-- <li> -->
                                        <label for="communication">Communication:<span class="error">* <?php echo $communicationErr;?></span></label>
                                        <select name="communication" id="communication">
                                            <option value="">-</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    <!-- </li> -->
                                    <!-- <br> -->
                                    <!-- Cleanliness Working -->
                                    <!-- <li> -->
                                        <label for="cleanliness">Cleanliness:<span class="error">* <?php echo $cleanlinessErr;?></span></label>
                                        <select name="cleanliness" id="cleanliness">
                                            <option value="">-</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    <!-- </li>
                                    <li> -->
                                    <!-- <br> -->
                                        <label for="procedure_id">Procedure:<span class="error">* <?php echo $procedureIDErr;?></span></label>
                                        <select name="procedure_id" id="procedure_id">
                                            <option value="">-</option>
                                            <?php 
                                            // Appointment_Procedure query
                                            $apptProcedures = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment_procedure WHERE patient_id='$pID[0]' ORDER BY date_of_procedure DESC;"));
                                            
                                            // populate dropdown menu with the patient's past procedure IDs
                                            foreach($apptProcedures as $apptProcedure => $apptProcedures) :?>
                                            <option value="<?php echo $apptProcedures['procedure_id'];?>">
                                            <?php echo $apptProcedures['procedure_id'];?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <!-- </li> -->
                                </ul>
                            </footer>
                        </div>
                    </form>
                    
                    <?php
                    // this PHP block processes the Review input after the response was submitted successfully

                    // if response was submitted successfully
                    if (!(empty($_POST["dentistName"]) || 
                        empty($_POST["professionalism"]) || 
                        empty($_POST["communication"]) || 
                        empty($_POST["cleanliness"]) ||
                        empty($_POST["procedure_id"]))) {
                           
                        echo "<h2>Thank you for submitting your input!</h2>";
                        echo "Your comment: " . $comment;
                        echo "<br>";
                        if(isset($_POST['dentistName'])) {
                            echo "Selected Dentist: ". htmlspecialchars($_POST['dentistName'])."<br>";
                        }
                        if(isset($_POST['professionalism'])) {
                            echo "Professionalism: ".htmlspecialchars($_POST['professionalism'])."<br>";
                        }
                        if(isset($_POST['communication'])) {
                            echo "Communication: ".htmlspecialchars($_POST['communication'])."<br>";
                        }
                        if(isset($_POST['cleanliness'])) {
                            echo "Cleanliness: ".htmlspecialchars($_POST['cleanliness'])."<br>";
                        }
                        if(isset($_POST['procedure_id'])) {
                            echo "Procedure ID: ".htmlspecialchars($_POST['procedure_id'])."<br>";
                        }

                        echo "<br>";

                        // insert data into Review table in Postgres
                        // insert patient review into Postgres
                        if ($_SERVER['REQUEST_METHOD'] === "POST") {
                            
                            // move the $_POST userinputs into PHP variables for cleaner code
                            $dentistNameInput = $_POST["dentistName"];
                            $professionalismInput = $_POST["professionalism"];
                            $comment = str_replace("'", "''", $comment); // replace ' with '' for Postgres insertion
                            $communicationInput = $_POST["communication"];
                            $cleanlinessInput = $_POST["cleanliness"];
                            $reviewDate = date("Y-m-d",time()); // YYYY-MM-DD format
                            $procedureIDInput = $_POST["procedure_id"];

                            echo "Adding Review... <br>";

                            $reviewQuery = "INSERT INTO Review (dentist_name, review_description, professionalism, communication, cleanliness, date_of_review, procedure_id) VALUES ('$dentistNameInput','$comment','$professionalismInput','$communicationInput','$cleanlinessInput','$reviewDate','$procedureIDInput');";

                            // $dbconn is the db connection in db.php
                            $insertReviewResult = pg_query($dbconn, $reviewQuery); // insert data into database

                            if (!$insertReviewResult) {
                                echo pg_last_error($dbconn);
                            } else {
                                echo "Review Added Successfully!<br><br>";
                                echo "Your response was submitted on " . date("Y-m-d",time()) . "<br><br><br><br>";
                            }


                        } // end if ($_SERVER['REQUEST_METHOD'] === "POST")

                    } // end if (if response was submitted successfully)
                    
                        
                    ?>

                    

                    <!-- Patient Reviews END -->
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
        // this if statement turns off the "Confirm Form Resubmission" and prevents multiple Review submissions
        //  after a successful Review submission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</html>
