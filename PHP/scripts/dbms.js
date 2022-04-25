
function validateSIN(patient_sin) {
    function format(patient_sin) {
        patient_sin = patient_sin.replace(/[^0-9]/g,'');
        return patient_sin.toString().replace(/\d{3}(?=.)/g, '$& ');
    }

    patient_sin = patient_sin.replace(/[^0-9]/g,'');
    $("#sinfield").val(format(patient_sin)); // 1234567890123456
    if(patient_sin == ' ' || !patient_sin.match(/^[0-9]{9}$/))
    {
        $("#sinfield").css({'background' : '#FFEDEF', 'border' : 'solid 1px red'});
        return false;
    }
    else
    {
        $("#sinfield").css({'background' : '#99FF99', 'border' : 'solid 1px #99FF99'});
        return true
    }
}

function validateTextField(input) {
    if (input.value == ''){
        input.className = "form-control badinput"
        return false;
    }
    else {
        input.className = "form-control goodinput"
        return true
    }
}

function validateEmail(input) {
    let re = /\S+@\S+\.\S+/; //regular expression to verify an @ in the string
    if (input.value == '' || !re.test(input.value)){
        input.className = "form-control badinput"
        return false;
    }
    else {
        input.className = "form-control goodinput"
        return true
    }
}

// https://www.youtube.com/watch?app=desktop&v=r5IbUHETupk
function validatePhone(input) {
    let phone = input.value;
    phone = phone.replace(/[^0-9]/g,'');
    input.value = phone;
    if(phone.length < 10) {
        input.className = "form-control badinput";
        return false;
    }
    else {
        input.className = "form-control goodinput"
        return true
    }
}

function validateDOB(DOB) {
    //verify entered DOB is of correct length and that the date entered isn't past today
    if (DOB.length != 10 || Date.parse(DOB) > new Date()){
        $("#dobfield").css({'background' : '#FFEDEF', 'border' : 'solid 1px red'});
        return false;
    }
    else {
        $("#dobfield").css({'background' : '#99FF99', 'border' : 'solid 1px #99FF99'});
        disableRepFields();

        return true
    }

    
}

function disableRepFields() {
    let needsRepInput = $("#needsrep")[0];
        
    let disabled = false;
    needsRepInput.value = true;     
    //if the user is > 15 years old then disable the representative field
    //15 years * 365 days in a year * 86400 seconds in a day * 1000 milliseconds in a second
    if (new Date() - Date.parse(DOB) > 15 * 365 * 86400 * 1000) {
        disabled = true;
        needsRepInput.value = false;   
    }
        
    //select all the representative inputs
    let repInputs = $("input[id^='representative']");
    for (let i = 0; i < repInputs.length; i++) {
        //if user < 15 years, then the rep inputs are required
        //i.e. if not disabled, then it is also required
        repInputs[i].disabled = disabled;
        repInputs[i].required = !disabled;
    }
}
