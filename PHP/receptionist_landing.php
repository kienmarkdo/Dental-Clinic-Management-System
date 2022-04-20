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
$allPatients = pg_query($dbconn, "SELECT name FROM patient_info;");

//input patient names to array
$arr = pg_fetch_all_columns($allPatients, 0);

?>


<!DOCTYPE html>


<html lang="en" style="scroll-behavior: smooth;">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>DCMS - Patient Homepage</title>
        <link rel="stylesheet" href="main.css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="receptionist_landing_style.css" />
    </head>
    <body>
        <!-- Logout Button START -->
        <div class="container" style="position: sticky; top: 0px; z-index:1">
            <div class="logout-btn">
                <a href="logout.php" class="logout-btn-text">Logout</a>
            </div>
        </div>
        <!-- Logout Button END -->

        <!-- CSS container START https://www.bootdey.com/snippets/view/user-profile-bio-graph-and-total-sales -->
        <div class="container bootstrap snippets bootdey">
            <div class="row">
                <div class="profile-nav col-md-3" style="position: sticky; top: 0px;">
                    <div class="panel">
                        <div class="user-heading round">
                            <h1>Welcome,</h1>
                            <h2><?php echo $rName[0] ?></h2>
                        </div>

                        <ul class="nav nav-pills nav-stacked">
                            <!-- Font awesome fonts version 4: https://fontawesome.com/v4/icons/ -->
                            <li>
                                <a href="#myInfo"> <i class="fa fa-user"></i> My Information</a>
                            </li>
            
                            <li>
                                <a href="#managePatients"> <i class="fa fa-book"></i> Manage Patients</a>
                            </li>
                            
                           
                        </ul>
                    </div>
                </div>
                <!-- Page Column START -->
                <div class="profile-info col-md-9">
                    <!-- Patient Information START -->
                    <div class="panel" id="receptionist_info">
                        <div class="bio-graph-heading">
                            <h3><i class="fa fa-user"></i> My Information</h3>
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

                     <!-- view patients START -->

                    <div class="panel" id="managePatients">
                        <div class="bio-graph-heading">
                            <h3><i class="fa fa-book"></i> Manage Patients</h3>
                        </div>

                    <!-- to autocomplete search bar : code from https://www.w3schools.com/howto/howto_js_autocomplete.asp -->

                    <style>
                    .autocomplete {
                      position: relative;
                      display: inline-block;
                    }

                    input {
                      border: 1px solid transparent;
                      background-color: #f1f1f1;
                      padding: 15px;
                      font-size: 16px;
                      margin : 15px;
                    }

                    input[type=text] {
                      background-color: #f1f1f1;
                      width: 100%;
                    }

                    input[type=submit] {
                      background-color: DodgerBlue;
                      color: #fff;
                      cursor: pointer;
                    }

                    .autocomplete-items {
                      position: absolute;
                      border: 1px solid #d4d4d4;
                      border-bottom: none;
                      border-top: none;
                      z-index: 99;
                      /*position the autocomplete items to be the same width as the container:*/
                      top: 100%;
                      left: 0;
                      right: 0;
                    }

                    .autocomplete-items div {
                      padding: 10px;
                      cursor: pointer;
                      background-color: #fff; 
                      border-bottom: 1px solid #d4d4d4; 
                    }

                    /*when hovering an item:*/
                    .autocomplete-items div:hover {
                      background-color: #e9e9e9; 
                    }

                    /*when navigating through the items using the arrow keys:*/
                    .autocomplete-active {
                      background-color: DodgerBlue !important; 
                      color: #ffffff; 
                    }
                    </style>
                        
                    <body>

                    <!--Make sure the form has the autocomplete function switched off:
                        move to patient_listing page when submitted -->
                    <form autocomplete="off" action="patient_listing.php", method = "GET"> 
                      <div class="autocomplete" style="width:300px;">
                        <input id="myInput" type="text" name="viewPatient" placeholder="Enter a patient's name">
                      </div>
                      <input type="submit">
                    </form>

                    <br><br>

                    <script>
                    function autocomplete(inp, arr) {
                      /*the autocomplete function takes two arguments,
                      the text field element and an array of possible autocompleted values:*/
                      var currentFocus;
                      /*execute a function when someone writes in the text field:*/
                      inp.addEventListener("input", function(e) {
                          var a, b, i, val = this.value;
                          /*close any already open lists of autocompleted values*/
                          closeAllLists();
                          if (!val) { return false;}
                          currentFocus = -1;
                          /*create a DIV element that will contain the items (values):*/
                          a = document.createElement("DIV");
                          a.setAttribute("id", this.id + "autocomplete-list");
                          a.setAttribute("class", "autocomplete-items");
                          /*append the DIV element as a child of the autocomplete container:*/
                          this.parentNode.appendChild(a);
                          /*for each item in the array...*/
                          for (i = 0; i < arr.length; i++) {
                            /*check if the item starts with the same letters as the text field value:*/
                            if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                              /*create a DIV element for each matching element:*/
                              b = document.createElement("DIV");
                              /*make the matching letters bold:*/
                              b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                              b.innerHTML += arr[i].substr(val.length);
                              /*insert a input field that will hold the current array item's value:*/
                              b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                              /*execute a function when someone clicks on the item value (DIV element):*/
                              b.addEventListener("click", function(e) {
                                  /*insert the value for the autocomplete text field:*/
                                  inp.value = this.getElementsByTagName("input")[0].value;
                                  /*close the list of autocompleted values,
                                  (or any other open lists of autocompleted values:*/
                                  closeAllLists();
                              });
                              a.appendChild(b);
                            }
                          }
                      });
                      /*execute a function presses a key on the keyboard:*/
                      inp.addEventListener("keydown", function(e) {
                          var x = document.getElementById(this.id + "autocomplete-list");
                          if (x) x = x.getElementsByTagName("div");
                          if (e.keyCode == 40) {
                            /*If the arrow DOWN key is pressed,
                            increase the currentFocus variable:*/
                            currentFocus++;
                            /*and and make the current item more visible:*/
                            addActive(x);
                          } else if (e.keyCode == 38) { //up
                            /*If the arrow UP key is pressed,
                            decrease the currentFocus variable:*/
                            currentFocus--;
                            /*and and make the current item more visible:*/
                            addActive(x);
                          } else if (e.keyCode == 13) {
                            /*If the ENTER key is pressed, prevent the form from being submitted,*/
                            e.preventDefault();
                            if (currentFocus > -1) {
                              /*and simulate a click on the "active" item:*/
                              if (x) x[currentFocus].click();
                            }
                          }
                      });
                      function addActive(x) {
                        /*a function to classify an item as "active":*/
                        if (!x) return false;
                        /*start by removing the "active" class on all items:*/
                        removeActive(x);
                        if (currentFocus >= x.length) currentFocus = 0;
                        if (currentFocus < 0) currentFocus = (x.length - 1);
                        /*add class "autocomplete-active":*/
                        x[currentFocus].classList.add("autocomplete-active");
                      }
                      function removeActive(x) {
                        /*a function to remove the "active" class from all autocomplete items:*/
                        for (var i = 0; i < x.length; i++) {
                          x[i].classList.remove("autocomplete-active");
                        }
                      }
                      function closeAllLists(elmnt) {
                        /*close all autocomplete lists in the document,
                        except the one passed as an argument:*/
                        var x = document.getElementsByClassName("autocomplete-items");
                        for (var i = 0; i < x.length; i++) {
                          if (elmnt != x[i] && elmnt != inp) {
                            x[i].parentNode.removeChild(x[i]);
                          }
                        }
                      }
                      /*execute a function when someone clicks in the document:*/
                      document.addEventListener("click", function (e) {
                          closeAllLists(e.target);
                      });
                    }

                    //convert patient names array from php to js
                    var patientNames = <?php echo json_encode($arr); ?>

                    /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
                    autocomplete(document.getElementById("myInput"), patientNames);
                    </script>

                    <!-- view patients, search bar END -->


                </div>
                <!-- Page Column END -->



            </div>
            <!-- Inner container -->
        </div>
        <!-- CSS container END https://www.bootdey.com/snippets/view/user-profile-bio-graph-and-total-sales -->
        
    </body>
</html>
