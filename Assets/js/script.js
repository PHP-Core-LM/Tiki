$(document).ready(function() {
    $('input[type="submit"]').on("click", function() {
        let data = getData(); //Data send to server
        // Check data is missing
        if (data == false) {
            Console.log("Missing Data");
            return;
        }
        else {
            processData(data);
        }
    });
});

var processData = function(data) {
    let url = "index.php?filter";
    changeStatus('process');

    // Sending data to server
    $.ajax({
        url: url,
        type: "POST",
        data: data,
        success: function(data, status, request) {
            let type = request.getResponseHeader("Content-Type");
            if (type === "application/json"){
                savingData(JSON.parse(JSON.stringify(data)));
            }
            else {
                notifyResult(false);
            }
        }
    });
};

var savingData = function(data) {
    let url = "index.php?saving";
    changeStatus('saving');
    if (data[0] == false) return;

    // Sending data to server
    $.ajax({
        url: url,
        type: "POST",
        data: {
            data: JSON.stringify(data)
        },
        success: function(data, status, request) {
            let type = request.getResponseHeader("Content-Type");
            if (type === "application/json") {
                notifyResult(JSON.parse(JSON.stringify(data)));
            }
            else {
                notifyResult(false);
            }
        }
    });
};

var notifyResult = function(result) {
    if (typeof(result) === "boolean" && !result){
        changeStatus("error");
        return;
    }
    else if (typeof(result) === "object" && result.status) {
        changeStatus("success");
        return;
    }

    changeStatus(false);
};

var getData = function() {
    let inputs = $('input[type="number"]');
    let lang = $('select').val();
    let data = "lang=" + lang + "&";
    if (inputs.length > 0) {
        for (let i = 0; i<inputs.length; i++){
            let element = inputs[i];
            let name = $(element).attr('id');
            let value = $(element).val();
            if (value.length == 0 && !isNumber(value)) return false;
            data += (name + "=" + value + "&");
        }
    }
    return data.substring(0, data.length - 1);
};

var isNumber = function(value){
    let regex = /[a-zA-Z_-]+/;
    if (value.match(regex)) return false;
    return true;
};

var changeStatus = function(status) {
    let element = $('.status');
    $(element).removeClass();
    $(element).addClass("status");
    if (status == false){
        $(element).addClass("error");
    }
    else {
        $(element).addClass(status);
    }
};