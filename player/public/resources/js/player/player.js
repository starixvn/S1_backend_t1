var base_url = "/";
var imgloader = "/resources/images/ajax-loader.gif";

function GetXmlHttpObject() {
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        return new XMLHttpRequest();
    }
    if (window.ActiveXObject) {
        // code for IE6, IE5
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
}

$(document).ready(function() {
    //$("#bank_account_number").numeric();

    //tooltip
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]',
    });

    $("[data-toggle='popover']").popover({
        html: 'true',
        template: '<div class="popover"><div class="arrow"></div><h4 class="popover-title"></h4><div class="popover-content"></div><div class="popover-footer"></div></div>'
    });

    //Use Random Password
    $('#randomPassword').click(function() {
        if ($(this).attr("value") == "randomPassword") {
            $("#passwordField").toggle();
            $("#hiddenField").toggle();
        }
    });
    //End of Use Random Password

    $('span[name=success]').tooltip({ /*or use any other selector, class, ID*/
        placement: "right",
        trigger: "hover"
    });

    $('span[name=fail]').tooltip({ /*or use any other selector, class, ID*/
        placement: "right",
        trigger: "hover"
    });

    // checkReferral();
    // checkAffiliate();
});

function checkReferral() {
    var referral_code = document.getElementById('referral_code');
    var affiliate_code = document.getElementById('affiliate_code');
    if(referral_code){
        if(referral_code.value != "") {
            affiliate_code.value = "";
            $(affiliate_code).prop('disabled',true);
        } else {
            $(affiliate_code).prop('disabled',false);
        }
    }
}

function checkAffiliate() {
    var referral_code = document.getElementById('referral_code');
    var affiliate_code = document.getElementById('affiliate_code');

    if(affiliate_code){
        if(affiliate_code.value != "") {
            referral_code.value = "";
            $(referral_code).prop('disabled',true);
        } else {
            $(referral_code).prop('disabled',false);
        }
    }
}

//Tags
function showDiv(e) {
    if (e.value != '') {
        document.getElementById('hide_im').style.display = "block";
        document.getElementById('account_type').innerHTML = e.value;
    } else {
        document.getElementById('hide_im').style.display = "none";
    }
}

function showDiv2(e) {
    if (e.value != '') {
        document.getElementById('hide_im2').style.display = "block";
        document.getElementById('account_type2').innerHTML = e.value;
    } else {
        document.getElementById('hide_im2').style.display = "none";
    }
}

function showDivRegistration(e) {

    var im_type = $(e).val();
    if ( ! im_type) {
        $('#im_account').val('');
    }

    if (e.value != '') {
        document.getElementById('im_account').disabled = false;
    } else {
        document.getElementById('im_account').disabled = true;
    }
}

function showDivRegistration2(e) {

    var im_type2 = $(e).val();
    if ( ! im_type2) {
        $('#im_account2').val('');
    }

    if (e.value != '') {
        document.getElementById('im_account2').disabled = false;
    } else {
        document.getElementById('im_account2').disabled = true;
    }
}

function getDays(month) {
    if (month == 2) {
        if (!leapYear()) {
            $("#twenty_eight").show();
        } else {
            $("#twenty_nine").show();
        }
    } else if (month == 4 || month == 6 || month == 9 || month == 11) {
        $("#thirty").show();
    } else {
        $("#thirty_one").show();
    }
}

function leapYear() {
    var year = new Date().getFullYear();
    return ((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0);
}

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode

    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

    return true;
}


function paymentMethod(id) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "cashier/paymentMethod/"  + id;

    var div = document.getElementById("payment_method");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }

        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

$(document).ready(function() {
    //game login
    // var username = $('#userName').val();
    // var pt_pw = $('#pt_pw').val();

    // initPTLogin(username,pt_pw);

    $(".amount_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105)) {
            e.preventDefault();
        }
    });

    $(".letters_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 32, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (code == 65 && e.ctrlKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if (e.ctrlKey === true || code < 65 || code > 90) {
            e.preventDefault();
        }
    });

    $(".letters_numbers_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 32, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (code == 65 && e.ctrlKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105) && (e.ctrlKey === true || code < 65 || code > 90)) {
            e.preventDefault();
        }
    });

    $(".usernames_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13]) !== -1 ||
             // Allow: Ctrl+A
            (code == 65 && e.ctrlKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40) ||
             // Allow: underscores
            (e.shiftKey && code == 189)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105) && (e.ctrlKey === true || code < 65 || code > 90)) {
            e.preventDefault();
        }
    });

    $(".emails_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (code == 65 && e.ctrlKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40) ||
             //Allow: Shift+2
            (e.shiftKey && code == 50)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105) && (e.ctrlKey === true || code < 65 || code > 90)) {
            e.preventDefault();
        }
    });
});

function news(ctr) {
    var count = document.getElementById('count').value;

    if(count < 2) {
        return;
    }

    var l = ctr;
    ctr++;

    if(l == 0) {
        l = count;
    }

    $('#news_'+l).fadeOut();
    $('#news_'+ctr).fadeIn(1000);

    if(ctr <= count) {
        setTimeout('news('+ctr+')', 30000);
    } else {
        news(0);
    }
}


// $('input[name="item[shipping]"]').on('click', function() {
//     if ($(this).val() === 'true') {
//         $('#item_shipping_cost').removeProp("disabled");
//     }
//     else {
//         $('#item_shipping_cost').prop("disabled", "disabled");
//     }
// });
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ PT ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//

// function initPTLogin(username,pt_pw) {
//     console.log('test function: '+username,pt_pw);

//     iapiSetCallout('Login', calloutLogin);
//     iapiLogin(username.toUpperCase(), pt_pw, realMode, "en");
// }

function calloutLogin(response) {
     if (response.errorCode) {
            alert("Login failed, " + response.errorText);
        }
        else {
            //window.location = "main.html";
            window.location = base_url+"player_controller/viewGames";
            alert("Game Login Success!");
        }
}

function goBack() {
    window.history.back()
}