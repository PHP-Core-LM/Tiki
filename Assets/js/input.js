var inputs = $('input[type="number"]');
var selects = $('select');

$(document).ready(function() {
    $(inputs).focusin(function(e){
        focusInput(e.target);
    });

    $(inputs).focusout(function(){
        focusInput(false);
    });

    $(selects).focusin(function(e){
        focusSelectInput(e.target);
    });

    $(selects).focusout(function(){
        focusSelectInput(false);
    });

    $(inputs).on("change paste keyup", function(e){
        let value = $(e.target).val();
        if (!isNumber(value)){
            errorInput(e.target); //Add class error to input
        }
        else {
            if (value.length > 0){
                let id = $(e.target).attr("id");
                if (id === "soLuong"){
                    let value_from = $('input#from').val();
                    if (value_from.length === 0) return;
                    let value_to = parseInt(value) + parseInt(value_from);
                    // Set new value to input#to
                    $('input#to').val(value_to);
                }
                else if (id == "from"){
                    let value_soLuong = $('input#soLuong').val();
                    if (value_soLuong.length === 0) return;
                    let value_to = parseInt(value_soLuong) + parseInt(value);
                    // Set new value to input#to
                    $('input#to').val(value_to);
                }
                else if (id === "to"){
                    errorInput(false);
                    //Validate input#soLuong
                    let value_soLuong = $('input#soLuong').val();
                    if (value_soLuong.length === 0) return;
                    //Validate input#from
                    let value_from = $('input#from').val();
                    if (value_from.length === 0) return;
                    //Set new value for input#soLuong or input#from
                    let denta = parseInt(value_from) - (parseInt(value) - parseInt(value_soLuong));
                    if (denta < 0){
                        //Set new value for input#soLuong
                        value_soLuong = parseInt(value_soLuong) + Math.abs(denta);
                        $('input#soLuong').val(value_soLuong);
                    }
                    else if (denta > 0){
                        if (parseInt(value_from) >= parseInt(value)) errorInput(e.target);
                        else {
                            value_soLuong = parseInt(value_soLuong) - Math.abs(denta);
                            $('input#soLuong').val(value_soLuong);
                        }
                    }
                }
            }
        }
    });
});


var focusInput = function(input){
    $('input[type="number"]').removeClass("focus");
    if (input !== false)
        $(input).addClass("focus");
};

var focusSelectInput = function(select){
    $('select').removeClass("focus");
    if (select !== false)
        $(select).addClass("focus");
};

var errorInput = function(input){
    $('input[type="number"]').removeClass("error");
    if (input !== false)
        $(input).addClass("error");
};

var isNumber = function(value){
    let regex = /[a-zA-Z_-]+/;
    if (value.match(regex)) return false;
    return true;
};