<?php

ob_start();

include 'functions.php';

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
    $patient_fields["name"]  = check_empty_input($_POST["name"]); 
    $patient_fields["gender"]  = check_empty_input($_POST["gender"]);
    $patient_fields["email"]  = check_empty_input($_POST["email"]);
    $patient_fields["phone"]  = check_empty_input($_POST["phone"]);
    $patient_fields["date_of_birth"]  = check_empty_input($_POST["date_of_birth"]);
    $patient_fields["insurance"]  = sanitize_input($_POST["insurance"]); //insurance not required

    if ( !is_numeric($patient_fields["patient_sin"]) || $patient_fields["patient_sin"] < 100000000 || $patient_fields["patient_sin"] > 999999999) {
        $err = "SIN is invalid";
    } else if ($password != $password_verify) {
        $err = "Given passwords don't match";
    } else if (!check_alpha_spaces($_POST['name'])) {
        $err = "Name contains special characters";
    } else if ($username != -1
        && $password != -1
        && $password_verify != -1
        && $patient_fields["patient_sin"] != -1
        && $patient_fields["address"]  != -1
        && $patient_fields["name"]  != -1
        && $patient_fields["gender"]  != -1
        && $patient_fields["email"]  != -1
        && $patient_fields["phone"]  != -1
        && $patient_fields["date_of_birth"]  != -1) { //register user in db
 
        echo "Registering user <br>";

        pg_query($dbconn, 'BEGIN'); // begins transaction
        $patient_info_result = pg_query_params($dbconn, 
            "INSERT INTO Patient_info (patient_sin, address, name, gender, email, phone, date_of_birth, insurance) 
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8);",
            array( $patient_fields["patient_sin"], //1
                $patient_fields["address"], //2
                $patient_fields["name"], //3
                $patient_fields["gender"], //4
                $patient_fields["email"], //5
                $patient_fields["phone"], //6
                hyphen_to_space($patient_fields["date_of_birth"]), //7, spaces need to be eliminated to store it
                $patient_fields["insurance"], //8
            )
        );
        $patient_and_user_account_result = pg_query_params($dbconn,
            "WITH P AS (
                INSERT INTO Patient (sin_info) 
                VALUES ($1) RETURNING patient_id
            ),
            PR AS (
                INSERT INTO Patient_records (patient_details, patient_id) 
                VALUES ('No information available', (SELECT patient_id FROM P))
            )
            INSERT INTO User_account (username, password, type_id, patient_id) 
            VALUES ($2, $3, 0, (SELECT patient_id FROM P));", 
            array( $patient_fields["patient_sin"], //1
                $username, //2
                password_hash($password, PASSWORD_DEFAULT) //3
            )
        );

        if ($patient_info_result && $patient_and_user_account_result) {
            pg_query($dbconn, 'COMMIT');
        } else {
            pg_query($dbconn, 'ROLLBACK');
            $err = "Failed to do database transaction";
        }

    } else {
        $err = "Not all fields were filled in correctly";
    }
    
    echo $username . " ". $password . " " . $password_verify; 
    foreach ($patient_fields as $p) {
        echo '<br>';
        echo $p;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCMS - Register</title>
    <link rel="stylesheet" href="CSS/main.css">
</head>
<body>
    <h1>DCMS - Registration page</h1>
    <h2>Enter Registration Details</h2>
    <h3 class="error"><?php echo $err ?></h3>
    <span class="error"> * indicates a field is required </span> 

    <div class="container">
        
        <form class="form-signin" role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <h3>Login Information</h3>
            <input type="text" class="form-control" value="<?php echo ($username == -1 ? "" : $username) ?>"
                name="username" placeholder="username = firstlast123" required> 
                <span class="error"> * <?php echo $username == -1 ? 'Username is required!' : '' ?> </span><br>
            <input type="password" class="form-control"
                name="password" placeholder="password = 1234" required>
                <span class="error"> * <?php echo $password == -1 ? 'Password is required!' : '' ?> </span><br>
            <input type="password" class="form-control"
                name="password_verify" placeholder="password = 1234" required>
                <span class="error"> * <?php echo $password_verify == -1 ? 'Must enter password twice!' : '' ?> </span><br>
            
            <br><br>
            
            <!-- Patient details here, needs patient_sin, address, name, gender, email, phone, date_of_birth, insurance -->
            <h3>Patient Details</h3>
            <fieldset>
            <legend>What's your gender?</legend>
                <input type="radio" name="gender" value="X" checked>Other/Prefer not to say<br>
                <input type="radio" name="gender" value="M">Male<br>
                <input type="radio" name="gender" value="F">Female<br>
            </fieldset> <br>
            <?php
                $fields = array();
                //format: type, name, placeholder, required
                array_push($fields, array("number", "patient_sin", "patient_sin = 123456789", true) );
                array_push($fields, array("text", "address", "address = 123 Sesame Street", true) );
                array_push($fields, array("text", "name", "name = First Last", true) );
                array_push($fields, array("email", "email", "email = flast@gmail.com", true) );
                array_push($fields, array("tel", "phone", "phone = 123-456-7890", true) );
                array_push($fields, array("date", "date_of_birth", "date_of_birth = 2000 01 01", true) );
                array_push($fields, array("text", "insurance", "insurance = Insurance Inc.", false) );
                foreach ($fields as $field) { ?>
                    <input type="<?php echo $field[0]; ?>" class="form-control" value="<?php echo ($patient_fields[$field[1]] == -1 ? "" : $patient_fields[$field[1]]); ?>"
                    name="<?php echo $field[1]; ?>" placeholder="<?php echo $field[2]; ?>" <?php echo ($field[3] ? 'required' : ''); ?> >
                    <?php if ($field[3]) { ?> 
                    <span class="error"> * <?php echo $patient_fields[$field[1]] == -1 ? "$field[1] is required!" : '' ?> </span><br>
                <?php } }

            ?>
            <br> 
            <button class="btn btn-lg btn-primary btn-block" type="submit" 
                name="login">Register</button>
        </form>

    </div>
</body>
</html>