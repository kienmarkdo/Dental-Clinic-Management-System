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
$patientAppointments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment WHERE patient_id='$pID[0]' ORDER BY date_of_appointment DESC;"));
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

// Treatment query
$patientTreatments = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Treatment WHERE patient_id='$pID[0]' ORDER BY appointment_id DESC;"));

// Appointment_Procedure query
$apptProcedures = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment_procedure WHERE patient_id='$pID[0]' ORDER BY date_of_procedure DESC;"));

// Invoice query - all of patient's invoice
$patientInvoice = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Invoice WHERE patient_id='$pID[0]' ORDER BY date_of_issue DESC;"));

// Review query (all reviews on the website)
$reviews = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Review ORDER BY date_of_review DESC;"));

// insert patient review into Postgres
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    
    //check_empty_input returns -1 on empty
    $dentistNameInput = check_empty_input($_POST["dentistName"]);
    $professionalismInput = check_empty_input($_POST["professionalism"]);
    $communicationInput = check_empty_input($_POST["communication"]);
    $cleanlinessInput = check_empty_input($_POST["cleanliness"]);
    date_default_timezone_set("America/New_York");
    $reviewDate = date("Y-m-d"); // YYYY-MM-DD format
    $procedureIDInput = check_empty_input($_POST["procedure_id"]);

    echo "Adding Review <br/>";

    pg_query($dbconn, 'BEGIN'); // begins transaction
    $reviewResult = pg_query_params($dbconn, 
        "INSERT INTO Review (dentist_name, professionalism, communication, cleanliness, date_of_review, procedure_id) 
        VALUES ($1, $2, $3, $4, $5, $6);",
        array(
            $dentistNameInput, //1
            $professionalismInput, //2
            $communicationInput, //3
            $cleanlinessInput, //4
            $reviewDate, //5
            $procedureIDInput, //6
        )
    );

}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>DCMS - Patient Homepage</title>
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

                    <!-- ==================================================================== -->
                    <!-- Patient Reviews START -->
                    
                    <!-- Patient Review VIEW ALL EXISTING REVIEWS START -->

                    <div class="panel" id="patient_reviews">
                        <div class="bio-graph-heading">
                            <h3>Reviews</h3>
                        </div>
                        <div class="panel-body bio-graph-info">
                            <table id="appointments_grid" class="table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Review ID</th>
                                        <th>Dentist Name</th>
                                        <!-- <th>Description</th> -->
                                        <th>Professionalism</th>
                                        <th>Communication</th>
                                        <th>Cleanliness</th>
                                        <th>Date Of Review</th>
                                        <th>Procedure ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($reviews as $review => $reviews) :?>
                                    <tr>
                                        <td><?php echo $reviews['review_id'] ?></td>
                                        <td><?php echo $reviews['dentist_name'] ?></td>
                                        <!-- <td><?php echo $reviews['review_description'] ?></td> -->
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
                    // define variables and set to empty values
                    /*$procedureIDErr = */
                    $professionalismErr = $communicationErr = $cleanlinessErr = $dentistNameErr = "";
                    $comment = "";

                    if ($_SERVER["REQUEST_METHOD"] == "POST") {

                        if (empty($_POST["dentistName"])) {
                            $dentistNameErr = "";
                        }
                        if (empty($_POST["professionalism"])) {
                            $professionalismErr = "";
                        }
                        if (empty($_POST["communication"])) {
                            $communicationErr = "";
                        }
                        if (empty($_POST["cleanliness"])) {
                            $cleanlinessErr = "";
                        }
                        if (empty($_POST["procedure_id"])) {
                            $procedureIDErr = "";
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
                        $data = htmlspecialchars($data);
                        return $data;
                    }
                    ?>

                    <h2>Tell Us About Your Experience</h2>
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <div class="panel" id="patient_reviews">
                            <!-- Comment -->
                            <textarea name="comment" placeholder="Leave us an anonymous review! (Max. 255 characters)" rows="4" class="form-control input-lg p-text-area" maxlength="255"><?php echo $comment;?></textarea>
                            
                            <footer class="panel-footer">
                                <!-- Submit button -->
                                <input class="btn btn-warning pull-right" type="submit" value="Submit"></input>

                                <ul> <!-- This makes it look nicer, but it covers a bit of the Submit button... style="position:relative; right:40px; top:8px; z-index: 1" -->
                                    <!-- <li> -->
                                        <!-- Dentist Name Working -->
                                        <label for="dentistName">Dentist:<span class="error">* <?php echo $dentistNameErr;?></span></label>
                                        <select name="dentistName" id="dentistName">
                                            <option value="">-</option>
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
                                        <!-- <label for="procedure_id">Procedure:<span class="error">* <?php //echo $procedureIDErr;?></span></label>
                                        <select name="procedure_id" id="procedure_id">
                                            <option value="">-</option>
                                            <?php 
                                            // Appointment_Procedure query
                                            //$apptProcedures = pg_fetch_all(pg_query($dbconn, "SELECT * FROM Appointment_procedure WHERE patient_id='$pID[0]' ORDER BY date_of_procedure DESC;"));
                                            
                                            // populate dropdown menu with the patient's past procedure IDs
                                            //foreach($apptProcedures as $apptProcedure => $apptProcedures) :?>
                                            <option value="<?php //echo $apptProcedures['procedure_id'];?>">
                                            <?php //echo $apptProcedures['procedure_id'];?></option>
                                            <?php //endforeach; ?>
                                        </select> -->
                                    <!-- </li> -->
                                </ul>
                            </footer>
                        </div>
                    </form>
                    
                    <?php
                    
                    /*&& isset($_POST['procedure_id'])*/ 
                    if (!(empty($_POST["dentistName"]) || 
                        empty($_POST["professionalism"]) || 
                        empty($_POST["communication"]) || 
                        empty($_POST["cleanliness"]))) {

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
                        // if(isset($_POST['procedure_id'])) {
                        //     echo "Procedure ID: ".htmlspecialchars($_POST['procedure_id'])."<br>";
                        // }
                    }
                        
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
    </body>
</html>
