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

// sidebar.php
$(document).ready(function() {
    var url = document.location.pathname;
    var res = url.split("/");
    // console.log('test:'+res);
    for (i = 0; i < res.length; i++) {
        console.log('res-i', res[i]);
        switch (res[i]) {
            // OGP-15145 - view_marketing_setting does not exist.  Discarding all related code.
            // case 'marketingSettings':
            // case 'friend_referral_settings':
            // case 'promoTypeManager':
            // case 'promoCancellationSetup':
            // case 'duplicatePlayerAccountCheckerSetting':
            // case 'viewRegistrationSettings':
            //     $("a#view_marketing_settings").addClass("active");
            //     break;
            case 'viewPromoDepositManager':
                $("a#view_deposit_promo_list").addClass("active");
                break;

            case 'promoApplicationList':
                $("a#view_promoapp_list").addClass("active");
                break;

            case 'promoCancellationList':
                $("a#view_promocancel_list").addClass("active");
                break;

            case 'promoPlayerList':
                $("a#view_promoplayer_list").addClass("active");
                break;
            case 'addNewPromo':
            case 'promoRuleManager':
                $("a#view_promorules_settings").addClass("active");
                break;

            case 'manage_cashback_request' :
                $("a#view_cashback_request_list").addClass("active");
                break;

            case 'viewGameLogs':
                $("a#view_game_logs").addClass("active");
                break;

            case 'shoppingCenterItemList':
                $("a#view_shopping_center_list").addClass("active");
                break;

            case 'shoppingClaimRequestList':
                $("a#view_shopper_request_list").addClass("active");
                break;

            case 'bonusGameSettings':
                $("a#bonus_game_settings").addClass("active");
                break;

            case 'ole777_wager_sync':
                $("a#ole777_wager_sync").addClass("active");
                break;

            case 'cashbackPayoutSetting' :
                console.log('cashbackPayoutSetting hit');
                $('a#cashbackSettings').addClass("active");
                break;

            default:
                break;
        }
    }
});


//cashback
$(document).ready(function() {
    // $('#periodTypeMonthly').show();
    // $('.earnings-table-data-monthly').show();

    // $('#periodType').val(0);

    // $('#setTimeHolder').hide();
    // //$('#saveSetting').attr('disabled', true);
    // $('#periodType').change( function() {
    //     var val = $(this).val();
    //     // console.log("val: "+val);

    //    if(val == 0){
    //         $('#setTimeHolder').hide();
    //         $('#saveSetting').attr('disabled', true);
    //     }else{
    //         $('#setTimeHolder').show();
    //         $('#saveSetting').attr('disabled', false);
    //     }

    // });

    // $('#gameType').attr('disabled', false);
    // $('#progressiveType').attr('disabled', false);
    // $('#brandedGame').attr('disabled', false);
    // $('#activeGame').attr('disabled', false);

    // $('#gameProvider').change( function() {
    //     var val = $(this).val();
    //     if (val == 1) {
    //         $('#gameType').attr('disabled', false);
    //         $('#progressiveType').attr('disabled', false);
    //         $('#brandedGame').attr('disabled', false);
    //         $('#activeGame').attr('disabled', false);
    //     } else if (val == 2) {

    //         $('#gameType').attr('disabled', true);
    //         $('#progressiveType').attr('disabled', true);
    //         $('#brandedGame').attr('disabled', true);
    //         $('#activeGame').attr('disabled', true);
    //         $('#gameType').val("");
    //         $('#progressiveType').val("");
    //         $('#brandedGame').val("");
    //         $('#activeGame').val("");

    //     }
    // });
});

// general
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
// end of general

function get_user_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "user_management/get_user_pages/" + segment;

    var div = document.getElementById("user-container");

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

function get_skip_pages(totalpage) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var page = document.getElementById('page_num').value;
    var segment = 5;

    if (page == 1 || page > totalpage) {
        segment = '';
    } else if (page > 2) {
        segment = segment * (page - 1);
    }

    if (page == null) {
        return;
    }

    url = base_url + "user_management/get_user_pages/" + segment;

    var div = document.getElementById("user-container");

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

$(".alert-message").alert();
window.setTimeout(function() {
    $(".alert-message").alert('close');
}, 2000);

$(function() {
    $('span#description').popover({
        html: true
    });
});

function sortBy(sortName) {
    var xmlhttp = GetXmlHttpObject();
    var order = {
        username: ""
    };

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    order = $('span#sort_' + sortName).text();

    if (order == 'ASC') {
        $('span#sort_' + sortName).innerHTML = 'DESC';
    } else {
        $('span#sort_' + sortName).innerHTML = 'ASC';
    }

    url = base_url + "user_management/sortUsersBy/" + sortName + "/" + order;

    var div = document.getElementById("list_panel_body");

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

// -----------------------------------------------------------------------------------------------------------------------------
// KAISER DAPAR
// -----------------------------------------------------------------------------------------------------------------------------

// GLOBAL VARIABLES
//var base_url    = 'http://' + window.location.hostname + '/';
//var imgloader   = 'http://' + window.location.hostname + '/resources/images/ajax-loader.gif';

$(document).ready(function() {

    // variables
    var url = document.location.pathname;
    var urlSegment = url.split('/');

    $.each(urlSegment, function(index, value) {
        if (value) {
            $('a#' + value).addClass('active');
        }
    });

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    // $(".chosen-select").chosen({
    //     disable_search: true,
    // });

    // $("#summernote").summernote();

    $('input[data-toggle="checkbox"]').click(function() {

        var element = $(this);
        var target = element.data('target');

        $(target).prop('checked', this.checked).prop('selected', this.checked).trigger('chosen:updated');

    });

    $('[data-untoggle="checkbox"]').on('change', function() {

        var element = $(this);
        var target = element.data('target');

        if (!this.checked) {
            $(target).prop('checked', false).prop('selected', false);
        }

    });

    $('#form-promoHtmlDescription').submit(function(e) {
        var code = $('#summernote').code();
        $('#promoHtmlDescription').val(code);
        return true;
    });

    // $('#promoStartTimestamp').daterangepicker({
    //     startDate: moment(),
    //     endDate: moment(),
    //     format: 'MMMM DD, YYYY',
    //     showWeekNumbers: true,
    //     singleDatePicker: true,
    //     showDropdowns: true,
    // }, setEndDate);

    // $('#promoPeriodTimestamp').daterangepicker({
    //     format: 'MMMM DD, YYYY',
    //     showWeekNumbers: true,
    //     showDropdowns: true,
    // });

    // $('#promoPeriodTimestamp').on('apply.daterangepicker', function (event, picker) {
    //     $('#promoStartTimestamp').val(picker.startDate.format('YYYY-MM-DD'));
    //     $('#promoEndTimestamp').val(picker.endDate.format('YYYY-MM-DD'));
    // });

    // $('#promoConditionPeriodTimestamp').daterangepicker({
    //     format: 'MMMM DD, YYYY',
    //     showWeekNumbers: true,
    //     showDropdowns: true,
    // });

    // $('#promoConditionPeriodTimestamp').on('apply.daterangepicker', function (event, picker) {
    //     $('#promoConditionStartTimestamp').val(picker.startDate.format('YYYY-MM-DD'));
    //     $('#promoConditionEndTimestamp').val(picker.endDate.format('YYYY-MM-DD'));
    // });

    // $('.daterange').daterangepicker({
    //     format: 'MMMM DD, YYYY',
    //     showWeekNumbers: true,
    //     showDropdowns: true,
    // });

    // $('#promoConditionType').change( function() {
    //     var val = $(this).val();
    //     $('#promoNth').val(null);
    //     if (val == 0) {
    //         $('#promoNth').removeAttr('disabled');
    //         $('#promoNth').show();
    //     } else if (val == 1) {
    //         $('#promoNth').attr('disabled', 'disabled');
    //         $('#promoNth').hide();
    //     }
    // });

    // $('#promoRequiredType').change( function() {
    //     var val = $(this).val();
    //     switch (val) {
    //         case '0':
    //             $('.promoRequiredType').text('Deposit');
    //             break;
    //         case '1':
    //             $('.promoRequiredType').text('Bet');
    //             break;
    //     }
    // });

    // $('#promoPeriod').change( function() {
    //     var val = $(this).val();
    //     switch (val) {
    //         case '0':
    //             $('.promoPeriod').text('Daily');
    //             break;
    //         case '1':
    //             $('.promoPeriod').text('Weekly');
    //             break;
    //         case '2':
    //             $('.promoPeriod').text('Monthly');
    //             break;
    //         case '3':
    //             $('.promoPeriod').text('Yearly');
    //             break;
    //         case '4':
    //             $('.promoPeriod').text('');
    //             break;
    //     }
    // });



    // $('#promoPeriod,#promoLength,#promoStartTimestamp').change(setEndDate);


    $(".number_only").keydown(function(e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
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

});

function setEndDate(start, end) {
    var date = $('#promoStartTimestamp').val();
    var start = moment(date, 'MMMM DD, YYYY');

    var promoPeriod = $('#promoPeriod option:selected').text();
    var promoLength = $('#promoLength').val();

    if (date.trim().length == 0) {
        $('#promoEndTimestamp').val('Please select a Start Date');
    } else if (!start.isValid()) {
        $('#promoEndTimestamp').val('Invalid Date');
    } else if (promoLength == 0) {
        $('#promoEndTimestamp').val('Never');
    } else {
        $('#promoEndTimestamp').val(start.add(promoLength, promoPeriod).format('MMMM DD, YYYY'));
    }
}

function checkSearchGameLogs(value) {
    var checkbox = document.getElementById(value);

    if (checkbox.checked) {
        $('#' + value + "_search").show();
    } else {
        $('#' + value + "_search").hide();
    }
}

function initializedCheckAll(id) {
    var cnt = 0;
    var list = document.getElementsByClassName(id);
    var check = document.getElementById(id);

    for (i = 0; i < list.length; i++) {
        if (list[i].checked)
            cnt++;
    }

    if (id == 'visible' && cnt == 0) {
        // document.getElementById('required').disabled = 1;
    }

    if (cnt == list.length) { check.checked = 1; }
}

function checkAll(type) {
    var list = document.getElementsByClassName(type);
    var list1 = document.getElementsByClassName('required');
    var check = document.getElementById(type);

    if (check.checked) {
        for (i = 0; i < list.length; i++) {
            if (type == 'visible') {
                list[i].disabled = 0;
            }

            list[i].checked = 1;
        }

        if (type == 'visible') {
            document.getElementById('required').disabled = 0;

            for (i = 0; i < list1.length; i++) {
                list1[i].disabled = 0;
            }
        }
    } else {
        check.checked;

        for (i = 0; i < list.length; i++) {
            list[i].checked = 0;
        }

        if (type == 'visible') {
            var get = document.getElementById('31_visible');
            if (get != null)
                get.disabled = 1;

            document.getElementById('required').disabled = 1;

            for (i = 0; i < list1.length; i++) {
                list1[i].disabled = 1;
            }
        }
    }
}

function uncheckAllPlayer(id) {
    var list = document.getElementById(id).className;
    // var all = $(list); // document.getElementById(list);

    var item = document.getElementById(id);
    var allitems = document.getElementsByClassName(list);
    var cnt = 0;

    if (item.checked) {
        var res = id.split("_");

        // if(res[0] == '3' && res[1] == 'visible') {
        //     var get1 = document.getElementById('31_visible');
        //     var get2 = document.getElementById('31_required');

        //     get1.disabled = 0;
        //     get2.disabled = 0;
        // }

        var get = document.getElementById(res[0] + '_required');
        if (get != null) {
            get.disabled = 0;
        }

        for (i = 0; i < allitems.length; i++) {
            if (allitems[i].checked) {
                cnt++;
            }
        }

        if (cnt == allitems.length) {
            $("#visible").prop('checked', true);
            $("#required").prop('disabled', false);
            //           document.getElementById('required').disabled = 0;
        }
    } else {
        $("#visible").prop('checked', false);
        // all.checked = 0;

        var res = id.split("_");

        // if(res[0] == '3' && res[1] == 'visible') {
        //     var get1 = document.getElementById('31_visible');
        //     var get2 = document.getElementById('31_required');

        //     get1.checked = 0;
        //     get1.disabled = 1;

        //     get2.checked = 0;
        //     get2.disabled = 1;
        // }

        if (res[1] == 'visible') {
            document.getElementById('required').disabled = 1;
            var get = document.getElementById(res[0] + '_required');
            if (get != null) {
                get.checked = 0;
                get.disabled = 1;
            }
        }
    }
}

function uncheckAll(id) {
    var list = document.getElementById(id).className;
    // var all = document.getElementById(list);

    var item = document.getElementById(id);
    var allitems = document.getElementsByClassName(list);
    var cnt = 0;

    if (item.checked) {
        var res = id.split("_");

        var get = document.getElementById(res[0] + '_required');
        if (get != null) {
            get.disabled = 0;
        }

        for (i = 0; i < allitems.length; i++) {
            if (allitems[i].checked) {
                cnt++;
            }
        }

        if (cnt == allitems.length) {
            $("#visible").prop('checked', true);
            // all.checked = 1;
            document.getElementById('required').disabled = 0;
        }
    } else {
        // all.checked = 0;
        $("#visible").prop('checked', false);

        var res = id.split("_");

        if (res[1] == 'visible') {
            document.getElementById('required').disabled = 1;
            var get = document.getElementById(res[0] + '_required');
            if (get != null) {
                get.checked = 0;
                get.disabled = 1;
            }
        }
    }
}

function toggleFieldVisibility(field) {

    var isVisible = $('#' + field + '_visible:checked').val();

    if (field == 8 && isVisible) {
        // TODO: If Password show, "Find password by security question" will active automatically
        // TODO: If Email show, "Find password by email" will active automatically
        // If Mobile show, "Find password by SMS" will active automatically
        $('#password_recovery_option_2').prop('checked', true);
    }

    if ( ! isVisible) {
        $('#' + field + '_edit').prop('checked', false);
        $('#' + field + '_required').prop('checked', false);
    }
    $('#' + field + '_edit').prop('disabled', ! isVisible);
    $('#' + field + '_required').prop('disabled', ! isVisible);
}