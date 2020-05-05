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


function viewBankingFunctions(playerId, path) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "cashier/" + path + "/" + playerId;

    var div = document.getElementById("banking_functions");

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
    //$("#bank_account_number").numeric();

    //tooltip
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]',
        placement: "right"
    });

    $("[data-toggle='popover']").popover({
        html: 'true',
        template: '<div class="popover"><div class="arrow"></div><h4 class="popover-title"></h4><div class="popover-content"></div></div>'
    });

    //password press
    $("#cpassword").keyup(checkPass);
    $("#password").keyup(checkPass);
    //end password press

    //Use Random Password
    $('#randomPassword').click(function() {
        if ($(this).attr("value") == "randomPassword") {
            $("#passwordField").toggle();
            $("#hiddenField").toggle();
        }
    });
    //End of Use Random Password

    //password press
    $("#email").keyup(checkEmail);
    $("#retyped_email").keyup(checkEmail);
    //end password press

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

    if(referral_code.value != "") {
        affiliate_code.value = "";
        affiliate_code.disabled = true;
    } else {
        affiliate_code.disabled = false;
    }
}

function checkAffiliate() {
    var referral_code = document.getElementById('referral_code');
    var affiliate_code = document.getElementById('affiliate_code');

    if(affiliate_code.value != "") {
        referral_code.value = "";
        referral_code.disabled = true;
    } else {
        referral_code.disabled = false;
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
    if (e.value != '') {
        document.getElementById('im_account').disabled = false;
    } else {
        document.getElementById('im_account').disabled = true;
    }
}

function showDivRegistration2(e) {
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

//check if passwords match

function checkPass() {
    //Store the password field objects into variables ...
    var pass1 = document.getElementById('password');
    var pass2 = document.getElementById('cpassword');
    //Store the Confimation Message Object ...
    var message = document.getElementById('lcpassword');
    $('#lcpassword').show();
    //Set the colors we will be using ...
    var goodColor = "#66cc66";
    var badColor = "#ff6666";
    //Compare the values in the password field
    //and the confirmation field
    if (pass1.value == "" || pass2.value == "") {
        pass2.style.backgroundColor = "";
        message.innerHTML = "";
    } else if (pass1.value == pass2.value) {
        //The passwords match.
        //Set the color to the good color and inform
        //the user that they have entered the correct password
        //pass2.style.backgroundColor = goodColor;
        message.style.color = goodColor;
        message.innerHTML = "Passwords match!"
    } else {
        //The passwords do not match.
        //Set the color to the bad color and
        //notify the user.
        //pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
        message.innerHTML = "Passwords do not match!"
    }
}
//end of check if passwords match

function checkEmail() {
    //Store the password field objects into variables ...
    var email = document.getElementById('email');
    var retyped_email = document.getElementById('retyped_email');
    //Store the Confimation Message Object ...
    var message = document.getElementById('lretype_email');

    $('#lretype_email').show();
    //Set the colors we will be using ...
    var goodColor = "#66cc66";
    var badColor = "#ff6666";
    //Compare the values in the password field
    //and the confirmation field
    if (email.value == "" || retyped_email.value == "") {
        retyped_email.style.backgroundColor = "";
        message.innerHTML = "";
    } else if (email.value == retyped_email.value) {
        //The passwords match.
        //Set the color to the good color and inform
        //the user that they have entered the correct password
        //pass2.style.backgroundColor = goodColor;
        message.style.color = goodColor;
        message.innerHTML = "Email Match!"
    } else {
        //The passwords do not match.
        //Set the color to the bad color and
        //notify the user.
        //pass2.style.backgroundColor = badColor;
        message.style.color = badColor;
        message.innerHTML = "Email Do Not Match!"
    }
}

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode

    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

function verifyPaymentMethod(player_id) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var method = document.getElementById('payment_method').value;

    var error = true;
    $('#error').html('');
    $('#error').hide();

    if (!method) {
        $('#error').html('Payment method is required.');
        $('#error').show();
        error = false;
    }

    if (error == true) {
        url = base_url + "cashier/postMakeDeposit/"  + player_id;

        var poststr =
            "&method=" + encodeURI(method) +
            "&player_id=" + encodeURI(player_id);

        var div = document.getElementById("banking_functions");

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                //div.innerHTML = xmlhttp.responseText;
                viewBankingFunctions(player_id + '/' + method, 'deposit');
            }
            if (xmlhttp.readyState != 4) {
                div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
            }
        }

        xmlhttp.open("POST", url, true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(poststr);
    }
}

function verifyDeposit(player_id) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var deposit_from = document.getElementById('deposit_from').value;
    var deposit_to = document.getElementById('deposit_to').value;
    var deposit_amount = document.getElementById('deposit_amount').value;
    var password = document.getElementById('password').value;

    var error = true;
    $('#error').html('');
    $('#error').hide();

    if (!deposit_from || !deposit_to || !deposit_amount || !password) {
        $('#error').html('All fields are required.');
        $('#error').show();
        error = false;
    }

    if (error == true) {
        url = base_url + "cashier/postDeposit";

        var poststr =
            "&deposit_from=" + encodeURI(deposit_from) +
            "&deposit_to=" + encodeURI(deposit_to) +
            "&deposit_amount=" + encodeURI(deposit_amount) +
            "&password=" + encodeURI(password) +
            "&player_id=" + encodeURI(player_id);

        var div = document.getElementById("banking_functions");

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                //div.innerHTML = xmlhttp.responseText;
                viewBankingFunctions(player_id + '/' + deposit_from, 'deposit');
            }
            if (xmlhttp.readyState != 4) {
                div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
            }
        }

        xmlhttp.open("POST", url, true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(poststr);
    }
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

    $(".number_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110]) !== -1 ||
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
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
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
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
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
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
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
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
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

function checkAccept(val) {
    var sbmt = document.getElementById("accept");

    if (val.checked == true)
    {
        sbmt.disabled = false;
    }
    else
    {
        sbmt.disabled = true;
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

function safelog(msg){
    if(variables.debugLog && typeof(console)!='undefined' && !_.isUndefined(console) && _.isFunction(console.log)){
        console.log(msg);
    }
}
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ PT ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~//

// $(document).ready(function() {
//     iapiSetCallout('Login', calloutLogin);
//     iapiSetCallout('Logout', calloutLogout);


//     console.log('username: '+username+' pw:'+pt_pw);
//     login(1);

//     function login(realMode) {
//       iapiLogin(document.getElementById("loginform").username.value.toUpperCase(), document.getElementById("loginform").password.value, realMode, "en");
//     }

//     function logout(allSessions, realMode) {
//       iapiLogout(allSessions, realMode);
//     }

//     function calloutLogin(response) {
//      if (response.errorCode) {
//             alert("Login failed, " + response.errorText);
//         }
//         else {
//             window.location = "main.html";
//             alert("Login Success!");
//         }
//     }

//     function calloutLogout(response) {
//      if (response.errorCode) {
//      alert("Logout failed, " + response.errorCode);
//      }
//      else {
//      alert("Logout OK");
//      }
//     }
// });

/*function fillTransferTo(value, game) {
    $('.transfer_to').hide();
    $('.transfer_to_label').show();

    if(value == '0') {
        $('#transfer_to_game').show();
    } else {
        $('#transfer_to_main').show();
    }
}*/
