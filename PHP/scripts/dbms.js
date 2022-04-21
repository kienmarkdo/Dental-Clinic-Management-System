
function validateSIN(patient_sin)
{
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

function validateAddress(Address) {
    if (Address == ''){
        $("#addressfield").css({'background' : '#FFEDEF', 'border' : 'solid 1px red'});
        return false;
        
    }
    else {
        $("#addressfield").css({'background' : '#99FF99', 'border' : 'solid 1px #99FF99'});
        return true
    }
}

function validateName(Name) {
    if (Name == ''){
        $("#namefield").css({'background' : '#FFEDEF', 'border' : 'solid 1px red'});
        return false;
        
    }
    else {
        $("#namefield").css({'background' : '#99FF99', 'border' : 'solid 1px #99FF99'});
        return true
    }
}

function validateEmail(Email) {
    if (Email == ''){
        $("#emailfield").css({'background' : '#FFEDEF', 'border' : 'solid 1px red'});
        return false;
        
    }
    else {
        $("#emailfield").css({'background' : '#99FF99', 'border' : 'solid 1px #99FF99'});
        return true
    }
}

// https://www.youtube.com/watch?app=desktop&v=r5IbUHETupk
function validatePhone(phone)
    {
        phone = phone.replace(/[^0-9]/g,'');
        $("#phonefield").val(phone);
        if(phone == ' ' || !phone.match(/^[0-9]{10}$/))
        {
            $("#phonefield").css({'background' : '#FFEDEF', 'border' : 'solid 1px red'});
            return false;
        }
        else
        {
            $("#phonefield").css({'background' : '#99FF99', 'border' : 'solid 1px #99FF99'});
            return true
        }
}

function validateDOB(DOB) {
    if (DOB.length != 10){
        $("#dobfield").css({'background' : '#FFEDEF', 'border' : 'solid 1px red'});
        return false;
        
    }
    else {
        $("#dobfield").css({'background' : '#99FF99', 'border' : 'solid 1px #99FF99'});
        return true
    }
}

function validateInsurance(insurance) {
    if (insurance == ''){
        $("#insurancefield").css({'background' : '#ffff99', 'border' : 'solid 1px red'});
        return false;
        
    }
    else {
        $("#insurancefield").css({'background' : '#99FF99', 'border' : 'solid 1px #99FF99'});
        return true
    }
}

