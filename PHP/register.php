<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCMS - Register</title>
</head>
<body>
    <h1>DCMS - Registration page</h1>
    <h2>Enter Registration Details</h2>
      
    <div class="container">
      
        <form class="form-signin" role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <h4 class="form-signin-heading"><?php echo $msg; ?></h4>

            <h3>Login Information</h3>
            <input type="text" class="form-control" 
                name="username" placeholder="username = firstlast123" required>
            <input type="password" class="form-control"
                name="password" placeholder="password = 1234" required>
            <input type="password" class="form-control"
                name="password_verify" placeholder="password = 1234" required><br><br>

            
            <!-- Patient details here, needs patient_sin, address, name, gender, email, phone, date_of_birth, insurance -->
            <h3>Patient Details</h3>
            <?php
                $fields = array();
                //format: type, name, placeholder, required
                array_push($fields, array("text", "patient_sin", "patient_sin = 123456789", true) );
                array_push($fields, array("text", "address", "address = 123 Sesame Street", true) );
                array_push($fields, array("text", "name", "name = First Last", true) );
                array_push($fields, array("text", "gender", "gender = X", true) ); //might want a radio button instead?
                array_push($fields, array("email", "email", "email = flast@gmail.com", true) );
                array_push($fields, array("text", "phone", "phone = 123-456-7890", true) );
                array_push($fields, array("text", "date_of_birth", "date_of_birth = 2000 01 01", true) );
                array_push($fields, array("text", "insurance", "insurance = Insurance Inc.", true) );
                foreach ($fields as $field) { ?>
                    <input type="<?php echo $field[0]; ?>" class="form-control"
                    name="<?php echo $field[1]; ?>" placeholder="<?php echo $field[2]; ?>" <?php echo ($field[3] ? 'required' : ''); ?> >
                <?php }

            ?>
            <br> 
            <button class="btn btn-lg btn-primary btn-block" type="submit" 
                name="login">Register</button>
        </form>

    </div>
</body>
</html>