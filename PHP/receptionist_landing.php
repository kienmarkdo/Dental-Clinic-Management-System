<?php

ob_start();
session_start();

include 'functions.php';
include_once 'db.php';
error_reporting(0);

//get variable from previous page
$eID = $_SESSION['empID'];
$eUsername = $_SESSION['empUName'];

// Receptionist info
$rSin = pg_fetch_row(pg_query($dbconn, "SELECT employee_sin FROM employee WHERE employee_id=$eID;"));
$rName = pg_fetch_row(pg_query($dbconn, "SELECT name FROM employee_info WHERE employee_sin='$rSin[0]';"));
$rWorkLocation = pg_fetch_row(pg_query($dbconn, "SELECT address FROM employee_info WHERE employee_sin='$rSin[0]';"));
$rSalary = pg_fetch_row(pg_query($dbconn, "SELECT annual_salary FROM employee_info WHERE employee_sin='$rSin[0]';"));
$branch = pg_fetch_row(pg_query($dbconn, "SELECT branch_id FROM employee WHERE employee_sin='$rSin[0]';"));
$bcity =  pg_fetch_row(pg_query($dbconn, "SELECT city FROM branch WHERE branch_id='$branch[0]';"));
$managerID = pg_fetch_row(pg_query($dbconn, "SELECT manager_id FROM branch WHERE branch_id='$branch[0]';"));
$managerName= pg_fetch_row(pg_query($dbconn, "SELECT i.name FROM employee e, employee_info i WHERE e.employee_id='$managerID[0]' AND e.employee_sin = i.employee_sin;"));
$rSalary = pg_fetch_row(pg_query($dbconn, "SELECT annual_salary FROM employee_info WHERE employee_sin='$rSin[0]';"));


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
                            <h2><?php echo $rName[0] ?></h2>
                        </div>

                        <ul class="nav nav-pills nav-stacked">
                            <!-- Font awesome fonts version 4: https://fontawesome.com/v4/icons/ -->
                            <li class="active">
                                <a href="#myInfo"> <i class="fa fa-user"></i> My Information</a>
                            </li>
            
                            <li>
                                <a href="#viewPatients"> <i class="fa fa-book"></i> View Patients</a>
                            </li>

                            <li>
                                <a href="add_patient.php"> <i class="fa fa-plus-square"></i> Add new Patient</a>
                            </li>

                            
                           
                        </ul>
                    </div>
                </div>
                <!-- Page Column START -->
                <div class="profile-info col-md-9">
                    <!-- Patient Information START -->
                    <div class="panel" id="receptionist_info">
                        <div class="bio-graph-heading">
                            <h3>My Information</h3>
                        </div>
                        <div class="panel-body bio-graph-info">
                            <h1>
                                Employee ID -
                                <?php echo $eID[0] ?>
                            </h1>
                            <div class="row">
                                <!-- <div class="bio-row">
                                    <p><span>Patient ID </span><?php echo $pName[0] ?></p>
                                </div> -->
                                <div class="bio-row">
                                    <p>
                                        <span>Full Name </span>
                                        <?php echo $rName[0] ?>
                                    </p>
                                </div>
                                <div class="bio-row">
                                    <p>
                                        <span>SIN </span>
                                        <?php echo $rSin[0] ?>
                                    </p>
                                </div>
                                <div class="bio-row">
                                    <p>
                                        <span>Work Location</span>
                                        <?php echo $rWorkLocation[0] ?>
                                    </p>
                                </div>
                                <div class="bio-row">
                                    <p>
                                        <span>Annual Salary </span>
                                        <?php echo $rSalary[0] ?>
                                    </p>
                                </div>
                                <div class="bio-row">
                                    <p>
                                        <span>Branch City </span>
                                        <?php echo $bcity[0] ?>
                                    </p>
                                </div>
                                <div class="bio-row">
                                    <p>
                                        <span>Manager </span>
                                        <?php echo $managerName[0] ?>
                                    </p>
                                </div> 
                            
                                <div class="bio-row">
                                    <p>
                                        <span>Branch ID </span>
                                        <?php echo $branch[0] ?>
                                    </p>
                                </div>

                                 <div class="bio-row">
                                    <p>
                                        <span>Role </span>
                                        Receptionist
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

                     <!-- view patients START -->

                    <div class="panel" id="viewPatients">
                        <div class="bio-graph-heading">
                            <h3>View Patients</h3>
                        </div>

                        <div class="panel" id="searchBar">

                        <form action="patient_listing.php">
                        
                            <input type="text" placeholder="Please search for a patient.." name="search">

                            <footer class="panel-footer">
                                <button class="btn btn-warning pull-right">Submit</button>
                                <ul class="nav nav-pills">
                                </ul>
                            </footer>

                        </form>

                    </div>
                    <!-- view patients END -->


                </div>
                <!-- Page Column END -->



            </div>
            <!-- Inner container -->
        </div>
        <!-- CSS container END https://www.bootdey.com/snippets/view/user-profile-bio-graph-and-total-sales -->
        
    </body>
</html>
