<?php

ob_start();
session_start();

include 'functions.php';
include_once 'db.php';
error_reporting(0);

// get variable from previous page (index.php)
$patientUsername = $_SESSION['pusername'];

// Patient ID and patient info details
$pID = pg_fetch_row(pg_query($dbconn, "SELECT patient_id FROM user_account WHERE username = '$patientUsername';"));

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $patientAmountErr = $insuranceAmountErr = "";

    if (empty($_POST["patientAmount"])) {
        $patientAmountErr = "Required";
    } elseif (!is_numeric($_POST["patientAmount"])) {
        $patientAmountErr = "Enter Numbers only (e.g.: 99.99)";
    }
    if (empty($_POST["insuranceAmount"])) {
        $insuranceAmountErr = "";
    } elseif (!is_numeric($_POST["insuranceAmount"])) {
        $insuranceAmountErr = "Enter Numbers only (e.g.: 99.99)";
    }
}
?>


<!DOCTYPE html>
<!-- https://www.w3schools.com/howto/howto_css_checkout_form.asp -->
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    body {
        font-family: Arial;
        font-size: 17px;
        padding: 8px;
    }

    * {
        box-sizing: border-box;
    }

    .row {
        display: -ms-flexbox; /* IE10 */
        display: flex;
        -ms-flex-wrap: wrap; /* IE10 */
        flex-wrap: wrap;
        margin: 0 -16px;
    }

    .col-25 {
        -ms-flex: 25%; /* IE10 */
        flex: 25%;
    }

    .col-50 {
        -ms-flex: 50%; /* IE10 */
        flex: 50%;
    }

    .col-75 {
        -ms-flex: 75%; /* IE10 */
        flex: 75%;
    }

    .col-25,
    .col-50,
    .col-75 {
        padding: 0 16px;
    }

    .container {
        background-color: #f2f2f2;
        padding: 5px 20px 15px 20px;
        border: 1px solid lightgrey;
        border-radius: 3px;
    }

    input[type="text"] {
        width: 100%;
        margin-bottom: 20px;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    label {
        margin-bottom: 10px;
        display: block;
    }

    .icon-container {
        margin-bottom: 20px;
        padding: 7px 0;
        font-size: 24px;
    }

    .btn {
        background-color: #04aa6d;
        color: white;
        padding: 12px;
        margin: 10px 0;
        border: none;
        width: 100%;
        border-radius: 3px;
        cursor: pointer;
        font-size: 17px;
    }

    .btn:hover {
        background-color: #45a049;
    }

    a {
        color: #2196f3;
    }

    hr {
        border: 1px solid lightgrey;
    }

    span.price {
        float: right;
        color: grey;
    }

    /* Responsive layout - when the screen is less than 800px wide, make the two columns stack on top of each other instead of next to each other (also change the direction - make the "cart" column go on top) */
    @media (max-width: 800px) {
        .row {
            flex-direction: column-reverse;
        }
        .col-25 {
            margin-bottom: 20px;
        }
    }
</style>

</head>
<body>


<h2>Dental Payment Form</h2>
<div class="row">
  <div class="col-75">
    <div class="container">
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      
        <div class="row">

          <div class="col-50">
            <h3>Payment</h3>
            <label for="fname">Accepted Payment Methods</label>
            <div class="icon-container">
				<fieldset>
					<input type="radio" name="payment_method" value="Cash/Debit" checked="true"> Cash/Debit <br>
					<input type="radio" name="payment_method" value="Amex"> Amex<br>
					<input type="radio" name="payment_method" value="Visa"> Visa<br>
					<input type="radio" name="payment_method" value="Mastercard"> Mastercard<br>
				</fieldset>
            </div>
			
            <label for="cname">Patient Amount<span class="error" style="color:red;"> * <?php echo $patientAmountErr; ?></span></label>
            <input type="text" id="patientAmount" name="patientAmount" placeholder="Enter amount (Canadian dollars)">
            <label for="ccnum">Insurance Amount (if applicable) <span class="error" style="color:red;"><?php echo $insuranceAmountErr; ?></span></label>
            <input type="text" id="insuranceAmount" name="insuranceAmount" placeholder="Enter amount (Canadian dollars)">
          </div>
          
        </div>
        <input type="submit" value="Make Payment" class="btn">
      </form>
	  <br>
		<br>
	  <input class="btn" type="button" value="Return to patient landing page" onClick="document.location.href='patient_landing.php'" />
		<br>
		<br>
    </div>
  </div>
  <div class="col-25">
    
  </div>
</div>

</body>
<script>
        // this if statement turns off the "Confirm Form Resubmission" and prevents multiple form submissions
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</html>

<?php if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // insert into database
    echo "Making payment... <br>";

    $pAmount = $_POST["patientAmount"];
    $iAmount = $_POST["insuranceAmount"];
    if (empty($_POST["insuranceAmount"])) {
        $iAmount = 0;
    }
    $totalAmount = $pAmount + $iAmount;
    $payment_method = $_POST["payment_method"];

    $paymentQuery = "
	INSERT INTO Patient_billing(patient_id, patient_amount, insurance_amount, total_amount, payment_type) 
	VALUES ('$pID[0]','$pAmount','$iAmount','$totalAmount','$payment_method');";

    // $dbconn is the db connection in db.php
    $insertPaymentResult = pg_query($dbconn, $paymentQuery); // insert data into database

    if (!$insertPaymentResult) {
        echo pg_last_error($dbconn);
    } else {
        echo "Payment Added Successfully!<br><br><br><br>";
    }
}

?>
