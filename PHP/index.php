<!DOCTYPE html>
<?php

ob_start();
session_start();
$dbconn=pg_connect("host=localhost port=5432 dbname=dcms user=dcms password=password");

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username']; 
    $password = $_POST['password'];

    $result = pg_query($dbconn, "SELECT password, patient_id, employee_id FROM user_account WHERE username = '$username';");
    $hashedPass = pg_fetch_row($result);
    if (!$hashedPass) {
        //query failed to return something, do error handling here
    }
    else if (password_verify($password, $hashedPass)) {

        $_SESSION['valid']=true;
        $_SESSION['timeout']=time();
    }
    else {
        //password failed to verify, do something
    }
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCMS - Login</title>
</head>
<body>
    <h1>DCMS - Login page</h1>
    <h2>Enter Login Details</h2>
      
    <div class="container">
      
        <form class="form-signin" role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <h4 class="form-signin-heading"><?php echo $msg; ?></h4>
            
            <input type="text" class="form-control" 
                name="username" placeholder="username=tutorialspoint" 
                required autofocus></br>

            <input type="password" class="form-control"
                name="password" placeholder="password=1234" required>

            <fieldset>
            <legend>What are you logging in as?</legend>
                <input type="radio" name="login_type" value="Patient">Patient<br>
                <input type="radio" name="login_type" value="Employee">Employee<br>
            </fieldset>

            <button class="btn btn-lg btn-primary btn-block" type="submit" 
                name="login">Login</button>
        </form>

        Click here to clean <a href="logout.php" tite="Logout">Session</a>.

    </div>
        
    <h2> No Account? Register <a href="register.php"> here! </a> </h2>
</body>
</html>