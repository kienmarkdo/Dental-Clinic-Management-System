<?php //This page allows the user to login as an employee or a patient.

ob_start();
session_start();

include_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $username = check_empty_input($_POST["username"]);
    $password = check_empty_input($_POST["password"]);
    $login_type = $_POST['login_type'];
    
    if ($username != -1 && $password != -1) {
        include_once 'db.php';
        $result = pg_query($dbconn, "SELECT password, patient_id, employee_id FROM user_account WHERE username = '$username';");
        $hashedPass = pg_fetch_row($result);
        if (!$dbconn) {
            $err = "Something went wrong in the database. If this error persists, please try again at another time.";
        } else if (!$hashedPass) {
            $err = "The entered username does not match anything existing in our records. Please verify and/or register the entered username.";
        }
        else if (password_verify($password, $hashedPass[0])) {
            
            $_SESSION['valid']=true;
            $_SESSION['timeout']=time();
            if ($hashedPass[1] == null && $login_type == "Patient") {
                $err = "Your login credentials are correct, but there is no Patient info associated with this account. Please register as a patient.";
            } else if ($hashedPass[2] == null && $login_type == "Employee") {
                $err = "Your login credentials are correct, but there is no Employee info associated with this account. Please contact your branch's IT department.";
            } else {
                echo "<h1> $username has logged in as " . $login_type . "! This will redirect you in a future update. </h1>";

                if ($login_type == 'Employee') {
                    //redirect to employee page
                } else {
                    //redirect to patient page
                }
            }
        }
        else {
            //password failed to verify, do something
            $err = "The password you entered for this account is incorrect. Debug: " . $password . " " . $hashedPass[0];
        }
    }
    
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCMS - Login</title>
    <link rel="stylesheet" href="CSS/main.css">
</head>
<body>
    <h1>DCMS - Login page</h1>
    <h2>Enter Login Details</h2>
    <h3 class="error"><?php echo $err ?></h3>
    <span class="error"> * indicates a field is required </span> 
      
    <div class="container">
      
        <form class="form-signin" role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <h4 class="form-signin-heading"><?php echo $msg; ?></h4>
            
            <input type="text" class="form-control" 
                name="username" placeholder="username = firstlast123" required>
                <span class="error"> * <?php echo $username == -1 ? 'Username is required!' : '' ?> </span><br>

            <input type="password" class="form-control"
                name="password" placeholder="password = 1234" required>
                <span class="error"> * <?php echo $password == -1 ? 'Password is required!' : '' ?> </span><br>

            <fieldset>
            <legend>What are you logging in as?</legend>
                <input type="radio" name="login_type" value="Patient">Patient<br>
                <input type="radio" name="login_type" value="Employee">Employee<br>
            </fieldset>

            <button class="btn btn-lg btn-primary btn-block" type="submit" 
                name="login">Login</button>
        </form>

    </div>
        
    <h2> No Account? Register <a href="register.php"> here! </a> </h2>
</body>
</html>