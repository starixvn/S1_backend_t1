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
// end of general

// ----------------------------------------------------------------------------------------------------------------------------- //

// view_list.php
function checkAll(id) {
    var list = document.getElementsByClassName(id);
    var all = document.getElementById(id);

    if (all.checked) {
        for(i=0; i<list.length; i++) {
            list[i].checked = 1;
        }
    } else {
        for(i=0; i<list.length; i++) {
            list[i].checked = 0;
        }
    }
}

function uncheckAll(id) {
    var list = document.getElementById(id).className;
    var all = document.getElementById(list);

    var item = document.getElementById(id);
    var allitems = document.getElementsByClassName(list);
    var cnt = 0;

    if(item.checked) {
        for(i=0; i<allitems.length; i++) {
            if(allitems[i].checked) { cnt++; }
        }

        if (cnt == allitems.length) { all.checked = 1; }
    } else {
        all.checked = 0;
    }
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

function checkPeriod(e) {
    if (e.value == '') {
        /*alert(moment().format('MMMM D, YYYY'));*/
        $('#dateRangeValue').val(moment().format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
        $('#dateRangeValueStart').val(moment().format('YYYY-MM-DD'));
        $('#dateRangeValueEnd').val(moment().format('YYYY-MM-DD'));

        $("#reportrange").attr("disabled", true);
        $('#reportrange').data('daterangepicker').remove();

        /*$("#start_date").attr("disabled", true);
        $("#end_date").attr("disabled", true);
        $(".type_date").attr("disabled", true);*/

        /*$("#start_date").val('');
        $("#end_date").val('');
        $(".type_date").attr("checked", false);*/
    } else {
        $("#reportrange").attr("disabled", false);
        daterange();

        /*$("#start_date").attr("disabled", false);
        $("#end_date").attr("disabled", false);
        $(".type_date").attr("disabled", false);*/
    }
}

function setDate(e) {
    switch(e.value) {
        case 'week':
            if(Date.today().is().mon()) {
                var start_date = Date.today().toString('yyyy-MM-dd');
                var end_date = Date.today().add(6).days().toString('yyyy-MM-dd');
            } else {
                var start_date = Date.today().last().monday().toString('yyyy-MM-dd');
                var end_date = Date.today().last().monday().add(6).days().toString('yyyy-MM-dd');
            }

            $('#start_date').attr('value', start_date);
            $('#end_date').attr('value', end_date);

            $('#hidden_start_date').attr('value', start_date);
            $('#hidden_end_date').attr('value', end_date);
            break;

        case 'month':
            var start_date = Date.today().moveToFirstDayOfMonth().toString('yyyy-MM-dd');
            var end_date = Date.today().moveToLastDayOfMonth().toString('yyyy-MM-dd');

            $('#start_date').attr('value', start_date);
            $('#end_date').attr('value', end_date);

            $('#hidden_start_date').attr('value', start_date);
            $('#hidden_end_date').attr('value', end_date);
            break;

        default:
            var start_date = Date.today().toString('yyyy-MM-dd');
            var end_date = Date.today().toString('yyyy-MM-dd');

            $('#start_date').attr('value', start_date);
            $('#end_date').attr('value', end_date);

            $('#hidden_start_date').attr('value', start_date);
            $('#hidden_end_date').attr('value', end_date);
            break;
    }
}

$(document).ready(function() { // hide/show for add
    $("#button_list_toggle").click(function(){
        $("#list_panel_body").slideToggle();
        $("#button_span_list_up",this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });
});
// end of view_list.php

// ----------------------------------------------------------------------------------------------------------------------------- //

function get_log_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "report_management/get_log_pages/" + segment;

    var div = document.getElementById("logList");

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

function get_api_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "pt_report_management/get_api_pages/" + segment;

    var div = document.getElementById("logList");

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

function get_payment_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "report_management/get_payment_pages/" + segment;

    var div = document.getElementById("logList");

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

function get_promotion_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "report_management/get_promotion_pages/" + segment;

    var div = document.getElementById("logList");

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

function get_summary_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "report_management/get_summary_pages/" + segment;

    var div = document.getElementById("summaryReportList");

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

function get_income_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "report_management/get_income_pages/" + segment;

    var div = document.getElementById("incomeReportList");

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

function get_games_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "report_management/get_games_pages/" + segment;

    var div = document.getElementById("gamesReportList");

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

function viewNewRegisteredPlayer(date) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "report_management/viewNewRegisteredPlayer/" + date;

    var div = document.getElementById("summaryreport_panel_body");

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

$(document).ready(function(){
    //scroll to top
    var offset = 220;
    var duration = 500;
    jQuery(window).scroll(function() {
        if (jQuery(this).scrollTop() > offset) {
            jQuery('.custom-scroll-top').fadeIn(duration);
        } else {
            jQuery('.custom-scroll-top').fadeOut(duration);
        }
    });

    $('.custom-scroll-top').on('click', function(event) {
        event.preventDefault();
        $('html, body').animate({scrollTop:0}, 'slow');
    });
    //end of scroll to top

    $(".number_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110]) !== -1 ||
             // Allow: Ctrl+A
            (code == 65 && e.ctrlKey === true) ||
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
});

// ----------------------------------------------------------------------------------------------------------------------------- //

$(document).ready(function(){
    $('#periodTypeMonthly').show();
    $('.earnings-table-data-monthly').show();
    $('.earnings-table-data-yearly').hide();
    $('#periodType').val(0);

    $('#periodType').change( function() {
        var val = $(this).val();

        if (val == 0) {
            $('#periodTypeMonthly').removeAttr('disabled');
            //$('#periodTypeMonthly').show();
            $('.earnings-table-data-yearly').hide();
            $('.earnings-table-data-monthly').show();
        } else if (val == 1) {
            $('#periodTypeMonthly').attr('disabled', 'disabled');
            //$('#periodTypeMonthly').hide();
            $('.earnings-table-data-yearly').show();
            $('.earnings-table-data-monthly').hide();
        }
    });
});

// ----------------------------------------------------------------------------------------------------------------------------- //
// PT REPORT
// ----------------------------------------------------------------------------------------------------------------------------- //

$(document).ready(function(){
    // console.log('player report');
    //player transaction in modal content
    //$(".sortby_panel_body").show();
    $(".hide_sortby").click(function() {
        $(".sortby_panel_body").slideToggle();
        $(".hide_sortby_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });
});

// ----------------------------------------------------------------------------------------------------------------------------- //

// sidebar.php
$(document).ready(function() {
    //tooltip
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    var url = document.location.pathname;
    var res = url.split("/");

    for (i = 0; i < res.length; i++) {
        switch(res[i]){
            case 'viewLogs':
                $("a#view_logs").addClass("active");
                break;

            case 'viewPlayerReport':
            case 'searchPlayerReport':
            case 'viewPlayerReportToday':
            case 'viewPlayerReportDaily':
            case 'viewPlayerReportWeekly':
            case 'viewPlayerReportMonthly':
            case 'viewPlayerReportYearly':
            case 'viewRegisteredPlayerToday':
                $("a#player_report").addClass("active");
                break;

            case 'viewPaymentReport':
                $("a#payment_report").addClass("active");
                break;

            case 'viewPlayerReport2':
                $("a#player_report2").addClass("active");

            case 'viewAPIGameReport':
                $("a#api_report").addClass("active");
                break;

            case 'viewPromotionReport':
                $("a#promotion_report").addClass("active");
                break;

            case 'viewPaymentStatusHistoryReport':
                $("a#payment_status_history_report").addClass("active");
                break;

            case 'viewGamesReport':
            case 'searchGamesReport':
            case 'viewGamesReportToday':
            case 'viewGamesReportDaily':
            case 'viewGamesReportWeekly':
            case 'viewGamesReportMonthly':
            case 'viewGamesReportYearly':
                $("a#game_report").addClass("active");
                break;

            case 'viewSummaryReport':
            case 'viewNewRegisteredPlayer':
            case 'viewRegisteredPlayer':
                $("a#summary_report").addClass("active");
                break;

            case 'playerRealtimeBalance':
                $("a#player_balance_report").addClass("active");
                break;

            case 'viewIncomeReport':
            case 'searchIncomeReport':
            case 'viewIncomeReportToday':
            case 'viewIncomeReportDaily':
            case 'viewIncomeReportWeekly':
            case 'viewIncomeReportMonthly':
            case 'viewIncomeReportYearly':
                $("a#income_report").addClass("active");
                break;
            case 'viewTransactionReport':
                $("a#transactions_report").addClass("active");
                break;

            case 'summary_report':
                $("a#summary_report").addClass("active");
                break;

            case 'cashback_report':
                $("a#cashback_report").addClass("active");
                break;

            case 'duplicate_account_report':
                $("a#duplicate_account_report").addClass("active");
                break;

            case 'viewSmsReport':
                $("a#sms_report").addClass("active");
                break;

            case 'dailyPlayerBalanceReport':
                $("a#daily_player_balance_report").addClass("active");
                break;

            case 'responsibleGamingReport':
                $("a#responsible_gaming_report").addClass("active");
                break;

            case 'bonusGamesReport':
                $("a#bonus_games_report").addClass("active");
                break;

            case 'player_analysis_report':
                $("a#player_analysis_report").addClass("active");
                break;

            case 'viewGradeReport':
                $("a#grade_report").addClass("active");
                break;

            case 'viewCommunicationPreferenceReport':
                $("a#communication_preference_report").addClass("active");
                break;

            case 'viewAttachedFileList':
                $("a#attached_file_list").addClass("active");
                break;

            case 'conversion_rate_report':
                $("a#conversion_rate_report").addClass("active");
                break;

            case 'viewOneworksGameReport':
                $("a#oneworks_game_report").addClass("active");

            default:
                break;
        }
    }
});
// end of sidebar.php