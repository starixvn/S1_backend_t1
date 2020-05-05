// general
var base_url = "/";
var imgloader = "/resources/images/ajax-loader.gif";

function GetXmlHttpObject()
{
    if (window.XMLHttpRequest)
    {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        return new XMLHttpRequest();
    }
    if (window.ActiveXObject)
    {
        // code for IE6, IE5
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
}

// function setDateTime(period) {
//     switch(period) {
//         case "Today":
//             var result = getDateToSet('Today');
//             $('#start_date').val(result[0]);
//             $('#end_date').val(result[1]);
//             break;

//         case "Weekly":
//             var result = getDateToSet('Weekly');
//             $('#start_date').val(result[0]);
//             $('#end_date').val(result[1]);
//             break;

//         case "Monthly":
//             var result = getDateToSet('Monthly');
//             $('#start_date').val(result[0]);
//             $('#end_date').val(result[1]);
//             break;

//         case "Yearly":
//             var result = getDateToSet('Yearly');
//             $('#start_date').val(result[0]);
//             $('#end_date').val(result[1]);
//             break;

//         default:
//             $('#start_date').val('yyyy-mm-dd');
//             $('#end_date').val('yyyy-mm-dd');
//             break;
//     }
// }

// function getDateToSet(period) {
//     var now = new Date();
//     var month = (now.getMonth() + 1);
//     var day = now.getDate();
//     if(month < 10)
//         month = "0" + month;
//     if(day < 10)
//         day = "0" + day;

//     var start_date = null;
//     var end_date = null;

//     switch(period) {
//         case "Today":
//             start_date = now.getFullYear() + '-' + month + '-' + day;
//             end_date = now.getFullYear() + '-' + month + '-' + day;
//             break;

//         case "Weekly":
//             var first = now.getDate() - now.getDay() + (now.getDay() == 0 ? -6:1); // First day is the day of the month - the day of the week
//             var last = first + 6;

//             if(first < 10) {
//                 first = '0' + first;
//             }

//             if(last < 10) {
//                 last = '0' + last;
//             }

//             start_date = now.getFullYear() + '-' + month + '-' + first;
//             end_date = now.getFullYear() + '-' + month + '-' + last;
//             break;

//         case "Monthly":
//             var last = new Date(now.getFullYear(), month, 0);
//             start_date = last.getFullYear() + '-' + month + '-01';
//             end_date = last.getFullYear() + '-' + month + '-' + last.getDate();
//             break;

//         case "Yearly":
//             start_date = now.getFullYear() + '-01-01';
//             end_date = now.getFullYear() + '-12-31';
//             break;

//         default:
//             break;
//     }

//     return [start_date, end_date]
// }

$(document).ready(function() {
    sidebar();

    // key down checking of text accept only number
    $(".number_only").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $('.close').click(function(){
        $('.modalbg').hide();
        $('.alert').hide();
    });

    //tooltip
    /*$('input[type=text][name=username]').tooltip({
        placement: "top",
        trigger: "focus"
    });

    $('input[type=password][name=password]').tooltip({
        placement: "top",
        trigger: "focus"
    });

    $('input[type=password][name=confirm_password]').tooltip({
        placement: "top",
        trigger: "focus"
    });

    $('input[type=text][name=email]').tooltip({
        placement: "top",
        trigger: "focus"
    });

    $('input[type=text][name=imtype1]').tooltip({
        placement: "top",
        trigger: "focus"
    });

    $('input[type=text][name=imtype2]').tooltip({
        placement: "top",
        trigger: "focus"
    });

    $('select[name=mode_of_contact]').tooltip({
        placement: "top",
        trigger: "focus"
    });

    $('input[type=text][name=website]').tooltip({
        placement: "top",
        trigger: "focus"
    });

    //show hide div
    $("#change_password_toggle").click(function() {
        $("#change_password_panel_body").slideToggle();
        $("#button_span_password_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    $("#change_email_toggle").click(function() {
        $("#change_email_panel_body").slideToggle();
        $("#button_span_email_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    $("#change_contact_toggle").click(function() {
        $("#change_contact_panel_body").slideToggle();
        $("#button_span_contact_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    $("#change_im_toggle").click(function() {
        $("#change_im_panel_body").slideToggle();
        $("#button_span_im_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    $("#info_toggle").click(function() {
        $("#info_panel_body").slideToggle();
        $("#button_span_info_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });*/
});
// end of general

// ----------------------------------------------------------------------------------------------------------------------------- //

// sidebar.php
function sidebar() {
    var url = document.location.pathname;
    var res = url.split("/");

    for (i = 0; i < res.length; i++) {
        switch(res[i]){
            case 'viewTrafficStatistics':
            case 'viewTrafficStatisticsToday':
            case 'viewTrafficStatisticsDaily':
            case 'viewTrafficStatisticsWeekly':
            case 'viewTrafficStatisticsMonthly':
            case 'viewTrafficStatisticsYearly':
            case 'showStats':
                $("li#trafficStats").addClass("active");
                break;

            case 'monthlyEarnings':
            case 'viewEarningsToday':
            case 'viewEarningsDaily':
            case 'viewEarningsWeekly':
            case 'viewEarningsMonthly':
            case 'viewEarningsYearly':
            case 'showEarnings':
                $("li#monthlyEarnings").addClass("active");
                break;

            case 'modifyAccount':
            case 'modifyPassword':
            case 'editInfo':
            case 'addNewAccount':
            case 'editPayment':
                $("li#modifyAccount").addClass("active");
                break;

            case 'paymentHistory':
                $("li#paymentHistory").addClass("active");
                break;

            case 'bannerLists':
            case 'searchBannerLists':
                $("li#bannerLists").addClass("active");
                break;

            case 'cashier':
                $("li#cashier").addClass("active");
                break;

            case 'subaffiliates':
                $("li#subaffiliates").addClass("active");
                break;

            case 'playersList':
                $("li#playersList").addClass("active");
                break;

            case 'affiliate_player_report':
                $("li#affiliate_player_report").addClass("active");
                break;

            case 'affiliate_game_history':
                $("li#affiliate_game_history").addClass("active");
                break;

            case 'affiliate_credit_transactions':
                $("li#affiliate_credit_transactions").addClass("active");
                break;

            default:
                break;
        }
    }
}
// end of sidebar.php

// ----------------------------------------------------------------------------------------------------------------------------- //

// banner
function displayBanner(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var time_period = document.getElementById('time_period').value;
    var start_date = document.getElementById('start_date').value;
    var end_date = document.getElementById('end_date').value;

    url = base_url + "affiliate/displayBanner/" + segment;

    var div = document.getElementById("view_banner");
    $('#view_banner').show();

    var poststr =
        "&time_period=" + encodeURI(time_period) +
        "&start_date=" + encodeURI(start_date) +
        "&end_date=" + encodeURI(end_date);

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            if(xmlhttp.responseText == '') {
                window.location = base_url + 'affiliate/bannerLists';
            } else {
                div.innerHTML = xmlhttp.responseText;
            }
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(poststr);
}

function specify(e) {
    if (e.value == 'specify') {
        $("#start_date").attr("disabled", false);
        $("#end_date").attr("disabled", false);
    } else {
        $("#start_date").attr("disabled", true);
        $("#end_date").attr("disabled", true);
    }
}

// function checkPeriod(e) {
//     if (e.value == '') {

//         // expand search select
//         //hideAffSearchOptions();

//         /*alert(moment().format('MMMM D, YYYY'));*/
//         $('#dateRangeValue').val(moment().format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
//         $('#dateRangeValueStart').val(moment().format('YYYY-MM-DD'));
//         $('#dateRangeValueEnd').val(moment().format('YYYY-MM-DD'));

//         $("#reportrange").attr("disabled", true);
//         $('#reportrange').data('daterangepicker').remove();
//         $(".type_date").attr("disabled", true);
//         $(".type_date").attr("checked", false);

//         /*$("#start_date").attr("disabled", true);
//         $("#end_date").attr("disabled", true);
//         $(".type_date").attr("disabled", true);*/

//         /*$("#start_date").val('');
//         $("#end_date").val('');
//         $(".type_date").attr("checked", false);*/
//     } else {

//         // show search select
//         // showAffSearchOptions();

//         var startDate;
//         var endDate;

//         switch(e.value) {
//             case 'daily':
//                 startDate = moment().format('YYYY-MM-DD');
//                 endDate = moment().format('YYYY-MM-DD');
//             break;
//             case 'weekly':
//                 startDate = moment().subtract(6, 'days').format('YYYY-MM-DD');
//                 endDate = moment().format('YYYY-MM-DD');
//             break;
//             case 'monthly':
//                 startDate = moment().startOf('month').format('YYYY-MM-DD');
//                 endDate = moment().endOf('month').format('YYYY-MM-DD');
//             break;
//             case 'yearly':
//                 startDate = moment().startOf('year').format('YYYY-MM-DD');
//                 endDate = moment().endOf('year').format('YYYY-MM-DD');
//             break;
//         }

//         //alert(e.value + ' : ' + startDate + '-' + endDate);
//         $('#dateRangeValue').val(startDate + ' - ' + endDate);
//         $('#dateRangeValueStart').val(startDate);
//         $('#dateRangeValueEnd').val(endDate);

//         $("#reportrange").attr("disabled", false);
//         //daterange();
//         $(".type_date").attr("disabled", false);

//         if($('.type_date').checked == null) {
//             $('#report_date').attr('checked', true);
//         }

//         /*$("#start_date").attr("disabled", false);
//         $("#end_date").attr("disabled", false);
//         $(".type_date").attr("disabled", false);*/
//     }
// }

// hide affiliate search options
function hideAffSearchOptions() {
    // get parent div
    var searchSelect = $('.searchSelect');
    var searchOptions = $('.searchOptions');

    searchSelect.removeClass('col-md-4');
    searchSelect.addClass('col-md-12');
    // hide search options
    searchOptions.css('display', 'none');
}

// show affiliate search options
function showAffSearchOptions() {
    // get parent div
    var searchSelect = $('.searchSelect');
    var searchOptions = $('.searchOptions');

    searchSelect.removeClass('col-md-12');
    searchSelect.addClass('col-md-4');
    // hide search options
    searchOptions.css('display', 'block');
}


// end of banner

// ----------------------------------------------------------------------------------------------------------------------------- //

// traffic stats
// end of traffic stats

// ----------------------------------------------------------------------------------------------------------------------------- //

// earnings
function displayEarnings(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var time_period = document.getElementById('time_period').value;
    var start_date = document.getElementById('start_date').value;
    var end_date = document.getElementById('end_date').value;
    var sort = document.getElementById('sort').value;
    var desc = document.getElementById('desc').value;

    url = base_url + "affiliate/displayEarnings/" + segment;

    var div = document.getElementById("view_earnings");
    $('#view_earnings').show();

    var poststr =
        "&time_period=" + encodeURI(time_period) +
        "&start_date=" + encodeURI(start_date) +
        "&end_date=" + encodeURI(end_date) +
        "&sort=" + encodeURI(sort) +
        "&desc=" + encodeURI(desc);

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            if(xmlhttp.responseText == '') {
                window.location = base_url + 'affiliate/monthlyEarnings';
            } else {
                div.innerHTML = xmlhttp.responseText;
            }
        }

        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(poststr);
}
// end of earnings

// ----------------------------------------------------------------------------------------------------------------------------- //

// payment history
function displayPaymentHistory(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var time_period = document.getElementById('time_period').value;
    var start_date = document.getElementById('start_date').value;
    var end_date = document.getElementById('end_date').value;
    var status = document.getElementById('status').value;
    var desc = document.getElementById('desc').value;
    var sort = document.getElementById('sort').value;

    url = base_url + "affiliate/displayPaymentHistory/" + segment;

    var div = document.getElementById("view_payments");
    $('#view_payments').show();

    var poststr =
        "&time_period=" + encodeURI(time_period) +
        "&start_date=" + encodeURI(start_date) +
        "&end_date=" + encodeURI(end_date) +
        "&status=" + encodeURI(status) +
        "&desc=" + encodeURI(desc) +
        "&sort=" + encodeURI(sort);

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            if(xmlhttp.responseText == '') {
                window.location = base_url + 'affiliate/paymentHistory';
            } else {
                div.innerHTML = xmlhttp.responseText;
            }
        }

        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(poststr);
}
// end of payment history

// ----------------------------------------------------------------------------------------------------------------------------- //

// modifyAccount
function editInfo(affiliate_id) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "affiliate/editInfo/" + affiliate_id;

    var div = document.getElementById("info_panel_body");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function deactivatePayment(payment_id, bank_name) {
    if (confirm('Are you sure you want to Deactivate this payment method: ' + bank_name + '?')) {
        window.location = base_url + "affiliate/deactivatePayment/" + payment_id + "/" + bank_name;
    }
}

function activatePayment(payment_id, bank_name) {
    if (confirm('Are you sure you want to Activate this payment method: ' + bank_name + '?')) {
        window.location = base_url + "affiliate/activatePayment/" + payment_id + "/" + bank_name;
    }
}

function deletePayment(payment_id, bank_name) {
    if (confirm('Are you sure you want to Delete this payment method: ' + bank_name + '?')) {
        window.location = base_url + "affiliate/deletePayment/" + payment_id + "/" + bank_name;
    }
}

// end of modifyAccount

// ----------------------------------------------------------------------------------------------------------------------------- //

// register
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

function imCheck(value, im) {
    if (value == "") {
        $('#im'+im).attr('readonly', true);
    } else {
        $('#im'+im).attr('readonly', false);
    }
}
// end of register

// ----------------------------------------------------------------------------------------------------------------------------- //

//cashier
function setModify(id) {
    $('#modify').attr('href', base_url + 'affiliate/modifyPayment/' + id);
}
//end of cashier

// ----------------------------------------------------------------------------------------------------------------------------- //

//change language
function changeLanguage() {
    var lang = $('#language').val();
    $.ajax({
        'url' : base_url +'affiliate/changeLanguage/'+lang,
        'type' : 'GET',
        'dataType' : "json",
        'success' : function(data){
            // console.log(data);


            location.reload();
        }
    });
}
//end of changeLanguage

// ----------------------------------------------------------------------------------------------------------------------------- //

// ----------------------------------------------------------------------------------------------------------------------------- //
// daterange picker
// ----------------------------------------------------------------------------------------------------------------------------- //
// $(document).ready(function(){
//    daterange();
// });

// function daterange() {
//      var cb = function(start, end, label) {
//         // console.log(start.toISOString(), end.toISOString(), label);
//         $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
//         $('#dateRangeValue').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
//         $('#dateRangeValueStart').val(start.format('YYYY-MM-DD'));
//         $('#dateRangeValueEnd').val(end.format('YYYY-MM-DD'));
//         //console.log(start.format('MMMM D, YYYY'));
//         //alert("Callback has fired: [" + start.format('MMMM D, YYYY') + " to " + end.format('MMMM D, YYYY') + ", label = " + label + "]");
//     }

//     var optionSet1 = {
//         startDate: moment().subtract(29, 'days'),
//         endDate: moment(),
//         minDate: '01/01/2012',
//         maxDate: '12/31/2015',
//         showDropdowns: true,
//         showWeekNumbers: true,
//         timePicker: false,
//         timePickerIncrement: 1,
//         timePicker12Hour: true,
//         ranges: {
//             'Today': [moment(), moment()],
//             'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
//             'Last 7 Days': [moment().subtract(6, 'days'), moment()],
//             // 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
//             'This Month': [moment().startOf('month'), moment().endOf('month')],
//             'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
//         },
//         opens: 'left',
//         buttonClasses: ['btn btn-default'],
//         applyClass: 'btn-small btn-primary',
//         cancelClass: 'btn-small',
//         format: 'MM/DD/YYYY',
//         separator: ' to ',
//         locale: {
//             applyLabel: 'Submit',
//             cancelLabel: 'Clear',
//             fromLabel: 'From',
//             toLabel: 'To',
//             customRangeLabel: 'Custom',
//             daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
//             monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
//             firstDay: 1
//         }
//     };

//     // $('#reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
//     //$('#reportrange span').html(moment().format('MMMM D, YYYY')+ ' - ' +moment().format('MMMM D, YYYY'));
//     if($('#dateRangeValue').val() == ''){ $('#dateRangeValue').val(moment().subtract(6, 'days').format('MMMM D, YYYY')+ ' - ' +moment().format('MMMM D, YYYY')); }
//     if($('#dateRangeValueStart').val() == ''){ $('#dateRangeValueStart').val(moment().subtract(6, 'days').format('YYYY-MM-DD')); }
//     if($('#dateRangeValueEnd').val() == ''){ $('#dateRangeValueEnd').val(moment().format('YYYY-MM-DD')); }
//     // console.log(moment().format('MMMM D, YYYY'));
//     $('#reportrange').daterangepicker(optionSet1, cb);

//     $('#reportrange').on('show.daterangepicker', function() { //console.log("show event fired");
//     });
//     $('#reportrange').on('hide.daterangepicker', function() { //console.log("hide event fired");
//     });
//     $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
//         // console.log("apply event fired, start/end dates are "
//         // + picker.startDate.format('MMMM D, YYYY')
//         // + " to "
//         // + picker.endDate.format('MMMM D, YYYY')
//         // );
//     });
//     $('#reportrange').on('cancel.daterangepicker', function(ev, picker) { //console.log("cancel event fired");
//     });

//     $('#options1').click(function() {
//         $('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
//     });

//     $('#destroy').click(function() {
//         $('#reportrange').data('daterangepicker').remove();
//     });


// }

