<?php
//generic utility functions

//sanitize input
function sanitize_input($input) {
    $data = trim($input); //removes leading and trailing spaces
    $data = stripslashes($input);
    $data = htmlspecialchars($input); //html-encodes any special characters
    return $input;
}

//check if an input is empty, return -1 if it is
function check_empty_input($input) {
    return (!empty($input) ? $input : -1); 
}

//check if a string is alphabetical, with spaces in betweens
function check_alpha_spaces($input) {
    $words = explode(" ", $input);
    foreach ($words as $word) {
        if (!ctype_alpha($word)) {
            return false;
        }
    } 
    return true;
}

function hyphen_to_space($input) {
    return str_replace('-', ' ', $input);
}


?>