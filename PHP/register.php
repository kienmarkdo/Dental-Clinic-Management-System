<?php
ob_start();
session_start();

include 'functions.php';
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    
    include_once 'db.php';
    
    //check_empty_input returns -1 on empty
    //user account fields
    $username = check_empty_input($_POST["username"]);
    $password = check_empty_input($_POST["password"]);
    $password_verify = check_empty_input($_POST["password_verify"]);
    
    //patient fields
    $patient_fields["patient_sin"] = $_POST["patient_sin"]; //SIN is validated below
    $patient_fields["address"] = check_empty_input($_POST["address"]);
    $patient_fields["fullname"]  = check_empty_input($_POST["fullname"]); 
    $patient_fields["gender"]  = check_empty_input($_POST["gender"]);
    $patient_fields["email"]  = check_empty_input($_POST["email"]);
    $patient_fields["phone"]  = check_empty_input($_POST["phone"]);
    $patient_fields["date_of_birth"]  = check_empty_input($_POST["date_of_birth"]);
    $patient_fields["insurance"]  = sanitize_input($_POST["insurance"]); //insurance not required

    //representative fields
    //these are required if the user is < 15 years old
    $patient_fields["representative_name"]  = check_empty_input($_POST["representative_name"]);
    $patient_fields["representative_phone"]  = check_empty_input($_POST["representative_phone"]); 
    $patient_fields["representative_email"]  = check_empty_input($_POST["representative_email"]); 
    $patient_fields["representative_rel"]  = check_empty_input($_POST["representative_rel"]); 

    //strip sin of spaces : 123 456 789 -> 123456789
    $patient_fields["patient_sin"] = str_replace(' ', '', $patient_fields["patient_sin"]);

    if ( !is_numeric($patient_fields["patient_sin"]) || $patient_fields["patient_sin"] < 100000000 || $patient_fields["patient_sin"] > 999999999) {
        $err = "SIN is invalid";
        $patient_fields["patient_sin"] = "";
    } else if ($password != $password_verify) {
        $err = "Given passwords don't match";
    } else if (!check_alpha_spaces($_POST["fullname"])) {
        $err = "Name contains special characters";
        $patient_fields['fullname'] = "";
    } else if ($username != -1
    && $password != -1
    && $password_verify != -1
    && $patient_fields["patient_sin"] != -1
    && $patient_fields["address"]  != -1
    && $patient_fields["fullname"]  != -1
    && $patient_fields["gender"]  != -1
    && $patient_fields["email"]  != -1
    && $patient_fields["phone"]  != -1
    && $patient_fields["date_of_birth"]  != -1) { //register user in db
 
        echo "Registering user <br>";

        //arguments for patient_info query
        $patient_info_arr = array( $patient_fields["patient_sin"], //1
            $patient_fields["address"], //2
            $patient_fields["fullname"], //3
            $patient_fields["gender"], //4
            $patient_fields["email"], //5
            $patient_fields["phone"], //6
            hyphen_to_space($patient_fields["date_of_birth"]), //7, spaces need to be eliminated to store it
            $patient_fields["insurance"], //8
        );

        //if there's a representative to insert, modify queries and args to accomodate the added rep
        $rep_value = "";
        $rep_args = "";
        if ($patient_fields["representative_name"] != -1
        && $patient_fields["representative_phone"]  != -1
        && $patient_fields["representative_email"]  != -1
        && $patient_fields["representative_rel"]  != -1) {            
            
            $rep_value = ", rep";
            $rep_args = ", ROW($9, $10, $11, $12)";    
            array_push($patient_info_arr, 
                $patient_fields["representative_name"], 
                $patient_fields["representative_phone"],
                $patient_fields["representative_email"],
                $patient_fields["representative_rel"]
            );
        }

        //SQL Queries with params
        $patient_info_query = 
        "INSERT INTO Patient_info (patient_sin, address, name, gender, email, phone, date_of_birth, insurance$rep_value ) 
        VALUES ($1, $2, $3, $4, $5, $6, $7, $8$rep_args);";
        $patient_and_user_account_query = 
        "WITH P AS (
            INSERT INTO Patient (sin_info) 
            VALUES ($1) RETURNING patient_id
        ),
        PR AS (
            INSERT INTO Patient_records (patient_details, patient_id) 
            VALUES ('No information available', (SELECT patient_id FROM P))
        )
        INSERT INTO User_account (username, password, type_id, patient_id) 
        VALUES ($2, $3, 0, (SELECT patient_id FROM P));";

        pg_query($dbconn, 'BEGIN'); // begins transaction
        $patient_info_result = pg_query_params($dbconn, 
            $patient_info_query,
            $patient_info_arr
        );
        $patient_and_user_account_result = pg_query_params($dbconn,
            $patient_and_user_account_query, 
            array( $patient_fields["patient_sin"], //1
                $username, //2
                password_hash($password, PASSWORD_DEFAULT) //3
            )
        );

        if ($patient_info_result && $patient_and_user_account_result) {
            pg_query($dbconn, 'COMMIT');

            //redirect to patient page
            $_SESSION['valid'] = true;
            $_SESSION['timeout'] = time();
            $_SESSION['patientUsername'] = $username; //send username via session
            header('Location:patient_landing.php');
        } else {
            pg_query($dbconn, 'ROLLBACK');
            $err = "Failed to do database transaction. Causes of this include: Duplicate SIN or Username";
        }

    } else {
        $err = "Not all fields were filled in correctly";
    }

    // Shows all form input that was supplied for debugging
    // echo $username . " ". $password . " " . $password_verify; 
    // foreach ($patient_fields as $p) {
    //     echo '<br>';
    //     echo $p;
    // }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCMS - Register</title>
    <link rel="icon" type="image/x-icon" href="images/register.png">
    <!-- <link rel="stylesheet" href="CSS/main.css"> -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/excite-bike/jquery-ui.css" rel="stylesheet" type="text/css">
    <link rel='stylesheet' href='main.css' type="text/css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- Your own script -->
    <script src="scripts/dbms.js"></script>

    <!-- JQuery time picker plugin -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">

    <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>

    <!-- Your own additional style sheet -->
</head>
<body>
    <div class="container">
        <div class="logout-btn">
            <a href="logout.php" class="logout-btn-text">Return</a>
        </div>

        <h1 style="text-align:center">DCMS - Registration Page</h1>
        <h2>Enter Registration Details</h2>
        <h3 class="error"><?php echo $err ?></h3>
        <span class="error"> * indicates a field is required </span> 
        
        <form class="form-signin" role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <h3>Login Information</h3>
            
            <label for="uname">Username:</label> <!-- the "for" attribute is supposed to math the id of the input type, if there is one -->
            <input type="text" class="form-control" value="<?php echo ($username == -1 ? "" : $username) ?>"
                name="username" placeholder="ex: firstlast123" required> 
                <span class="error"> * <?php echo $username == -1 ? 'Username is required!' : '' ?> </span><br>
            
            <label for="pword1">Password:</label>
            <input type="password" class="form-control"
                name="password" placeholder="ex: 123456" value="" required>
                <span class="error"> * <?php echo $password == -1 ? 'Password is required!' : '' ?> </span><br>
            
            <label for="pword2">Verify Password:</label>
            <input type="password" class="form-control"
                name="password_verify" placeholder="enter the same password" required>
                <span class="error"> * <?php echo $password_verify == -1 ? 'Must enter password twice!' : '' ?> </span><br>
            
            <br><br>
            
            <!-- Patient details here, needs patient_sin, address, name, gender, email, phone, date_of_birth, insurance -->
            <h3>Patient Details</h3>
            <fieldset>
            <legend>What's your gender?</legend>
                <input type="radio" name="gender" value="M">Male<br>
                <input type="radio" name="gender" value="F">Female<br>
                <input type="radio" name="gender" value="X" checked>Other/Prefer not to say<br>
            </fieldset> <br> <br> 
            
            <label for="patient_sin">Social Insurance Number:</label>
            <input type="text" class="form-control" id="sinfield" name="patient_sin" placeholder="ex: 123 456 789" 
            onkeyup="return validateSIN(this.value);" title="16 digits" value="<?php echo $patient_fields["patient_sin"] ?>" required>
            <span class="error"> * <?php echo $patient_fields["patient_sin"] == -1 ? "patient_sin is required!" : '' ?> </span><br>

            <label for="address">Address:</label>
            <input type="text" class="form-control" id="addressfield" name="address" placeholder="ex: 123 Sesame Street" 
            onkeyup="return validateTextField(this);" value="<?php echo $patient_fields["address"] ?>" required>
            <span class="error"> * <?php echo $patient_fields["address"] == -1 ? "patient_sin is required!" : '' ?> </span><br>

            <label for="fullname">Full Name:</label>
            <input type="text" class="form-control" id="namefield" name="fullname" placeholder="ex: John Doe" 
            onkeyup="return validateTextField(this);" value="<?php echo $patient_fields["fullname"] ?>" required>
            <span class="error"> * <?php echo $patient_fields["fullname"] == -1 ? "name is required!" : '' ?> </span><br>

            <label for="email">Email Address:</label>
            <input type="email" class="form-control" id="emailfield" name="email" placeholder="ex: johndoe@gmail.com" 
            onkeyup="return validateEmail(this);" value="<?php echo $patient_fields["email"] ?>" required>
            <span class="error"> * <?php echo $patient_fields["email"] == -1 ? "email is required!" : '' ?> </span><br>

            <label for="phone">Phone Number:</label>
            <input type="tel" class="form-control" id="phonefield" name="phone" placeholder="ex: 6134083244" 
            onkeyup="return validatePhone(this);" value="<?php echo $patient_fields["phone"] ?>" required>
            <span class="error"> * <?php echo $patient_fields["phone"] == -1 ? "phone is required!" : '' ?> </span><br>

            <label for="dateTimeInput">Date of Birth:</label>
            <input type="date" class="form-control" id="dobfield" value="" name="date_of_birth" placeholder="ex: 2002-05-22" 
            onkeyup="return validateDOB(this.value);" onchange="return validateDOB(this.value);" value="<?php echo $patient_fields["date_of_birth"] ?>" required>
            <span class="error"> * <?php echo $patient_fields["date_of_birth"] == -1 ? "date_of_birth is required!" : '' ?> </span><br>

            <label for="insurance">Insurance:</label>
            <input type="text" class="form-control" id="insurancefield" name="insurance" placeholder="ex: Insurance Inc." 
            value="<?php echo $patient_fields["insurance"] ?>">

            <br>


            <h3>Representative</h3>
            <span class="error">Patients aged 14 and lower must have an adult representative, in which case these fields are required!</span>             

            <hr>
            <!-- Need to add JQuery code for Representative -->
            <label for="representative_name">Representative Name:</label>
            <input type="text" class="form-control" id="representative_namefield" name="representative_name" placeholder="ex: Jane Doe" 
            onkeyup="return validateTextField(this);" value="<?php echo $patient_fields["representative_name"] ?>">
            <span class="error"><?php echo $patient_fields["representative_namefield"] == -1 ? "* Representative name is required!" : '' ?> </span><br>

            <!-- Phone field is required if the patients has a representative -->
            <label for="representative_phone">Representative Phone:</label>
            <input type="tel" class="form-control" id="representative_phonefield" name="representative_phone" placeholder="ex: 6134789654" 
            onkeyup="return validatePhone(this);" value="<?php echo $patient_fields["representative_phone"] ?>" >
            <span class="error"><?php echo $patient_fields["representative_phone"] == -1 ? "* Representative phone is required!" : '' ?> </span><br>

            <!-- Email field is required if the patients has a representative -->
            <label for="representative_email">Representative Email:</label>
            <input type="email" class="form-control" id="representative_emailfield" name="representative_email" 
            onkeyup="return validateEmail(this);" placeholder="ex: janedoe@gmail.com" value="<?php echo $patient_fields["representative_email"] ?>" > 
            <span class="error"><?php echo $patient_fields["representative_email"] == -1 ? "* Representative email is required!" : '' ?> </span><br>
            
            <!-- Relationship field is required if the patients has a representative -->
            <label for="representative_rel">Representative Relationship:</label>
            <input type="text" class="form-control" id="representative_relfield" name="representative_rel" placeholder="Mother"
            onkeyup="return validateTextField(this);" value="<?php echo $patient_fields["representative_rel"] ?>" > 
            <span class="error"><?php echo $patient_fields["representative_rel"] == -1 ? "* Representative relationship is required!" : '' ?> </span><br>

            <button class="btn btn-lg btn-primary btn-block" type="submit" 
                name="login">Register</button>
        </form> 
    </div>
    <br> <br>
</body>
<script>
        // this if statement turns off the "Confirm Form Resubmission" and prevents multiple form submissions after a successful form submission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</html>
