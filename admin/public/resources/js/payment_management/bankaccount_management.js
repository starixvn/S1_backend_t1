//general
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

function get_bankaccount_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "bankaccount_management/get_bankaccount_pages/" + segment;

    var div = document.getElementById("bankaccount_table");

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

function sortBankAccount(sort) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "bankaccount_management/sortBankAccount/" + sort;

    var div = document.getElementById("bankaccount_table");

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

function searchBankAccount() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var search = document.getElementById('search').value;

    url = base_url + "bankaccount_management/searchBankAccount/" + search;

    var div = document.getElementById("bankaccount_table");

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
// ----------------------------------------------------------------------------------------------------------------------------- //

// sidebar.php
$(document).ready(function() {
    var url = document.location.pathname;
    var res = url.split("/");
    for (i = 0; i < res.length; i++) {
        switch (res[i]) {
            case 'vipGroupSettingList':
            case 'editVipGroupLevel':
            case 'viewVipGroupRules':
                $("a#view_vipsetting_list").addClass("active");
                break;
            case 'viewBankAccountManager':
                $("a#view_payment_settings").addClass("active");
                break;
            default:
                break;
        }
    }
});
// end of sidebar.php

// ----------------------------------------------------------------------------------------------------------------------------- //

//--------DOCUMENT READY---------
//---------------
$(document).ready(function() {
    BankAccountManagementProcess.initialize();
});
//player management module
var BankAccountManagementProcess = {

    initialize : function() {
      // console.log("initialized now!");

      //validation
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

        //numeric only
        $("#accountNumber").numeric();
        $("#dailyMaxDepositAmount").numeric();

        //tooltip
        $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
        });

        //jquery choosen
        $(".chosen-select").chosen({
            disable_search: true,
        });

        $('input[data-toggle="checkbox"]').click(function() {

            var element = $(this);
            var target  = element.data('target');

            $(target).prop('checked', this.checked).prop('selected', this.checked);
            $(target).parent().trigger('chosen:updated');
            $(target).parent().trigger('change');

        });

        $('[data-untoggle="checkbox"]').on('change', function() {

            var element = $(this);
            var target  = element.data('target');
            if (element.is('select')) {
                $(target).prop('checked', element.children('option').length == element.children('option:selected').length);
            } else {
                $(target).prop('checked', this.checked);
            }

        });

        //for add bank account panel
        var is_addPanelVisible = false;

        //for bank account  edit form
        var is_editPanelVisible = false;

        if(!is_addPanelVisible){
            $('.add_bankaccount_sec').hide();
        }else{
            $('.add_bankaccount_sec').show();
        }

        if(!is_editPanelVisible){
            $('.edit_bankaccount_sec').hide();
        }else{
            $('.edit_bankaccount_sec').show();
        }

        //show hide add vip group panel
        $("#add_bankaccount").click(function () {
            if(!is_addPanelVisible){
                is_addPanelVisible = true;
                $('.add_bankaccount_sec').show();
                $('.edit_bankaccount_sec').hide();
                $('#addBankAccountGlyhicon').removeClass('glyphicon glyphicon-plus-sign');
                $('#addBankAccountGlyhicon').addClass('glyphicon glyphicon-minus-sign');
            }else{
                is_addPanelVisible = false;
                $('.add_bankaccount_sec').hide();
                $('#addBankAccountGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
                $('#addBankAccountGlyhicon').addClass('glyphicon glyphicon-plus-sign');
            }
        });

        //show hide edit vip group panel
        $(".editBankAccountBtn").click(function () {
                is_editPanelVisible = true;
                $('.add_bankaccount_sec').hide();
                $('.edit_bankaccount_sec').show();
        });

        //cancel add vip group
        $(".addbankaccount-cancel-btn").click(function () {
                is_addPanelVisible = false;
                $('.add_bankaccount_sec').hide();
                $('#addBankAccountGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
                $('#addBankAccountGlyhicon').addClass('glyphicon glyphicon-plus-sign');
        });

        //cancel add vip group
        $(".editbankaccount-cancel-btn").click(function () {
                is_editPanelVisible = false;
                $('.edit_bankaccount_sec').hide();
        });
    },

    getBankAccountDetails : function(bankAccountId) {
        is_editPanelVisible = true;
        $('.add_bankaccount_sec').hide();
        $('.edit_bankaccount_sec').show();
        $.ajax({
            'url' : base_url + 'bankaccount_management/getBankAccountDetails/' + bankAccountId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                     // console.log(data[0]);
                     $('#editBankAccountId').val(data[0].otcPaymentMethodId);
                     $('#editBankName').val(data[0].bankName);
                     $('#editBankAccountName').val(data[0].accountName);
                     $('#editBankAccountNumber').val(data[0].accountNumber);
                     $('#editBankAccountBranchName').val(data[0].branchName);
                     $('#editTransactionFee').val(data[0].transactionFee);
                     $('#editDailyMaxDepositAmount').val(data[0].dailyMaxDepositAmount);
                     $('#editBankAccountDescription').val(data[0].description);
                     $('.currentBankAccountPlayerLevelLimit').html('');
                     $('#form_edit #playerLevels option').prop('selected', false);
                     for(var i = 0; i < data[0].bankAccountPlayerLevelLimit.length; i++){
                         var id = data[0].bankAccountPlayerLevelLimit[i].vipsettingcashbackruleId;
                         $('#form_edit #playerLevels option[value="'+id+'"]').prop('selected', true);
                     }
                     $('#form_edit #playerLevels').trigger('chosen:updated');
                    }
        },'json');
        return false;
    },
};

$(document).ready(function() {
    var offset = 200;
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

    //modal Tags
    $("#tags").change(function() {
        $("#tags option:selected").each(function() {
            if ($(this).attr("value") == "Others") {
                $("#specify").show();
            } else {
                $("#specify").hide();
            }
        });
    }).change();
    //end modal Tags

    $("#edit_column").tooltip({
        placement: "left",
        title: "Edit columns",
    });

    $("#show_advance_search").tooltip({
        placement: "left",
        title: "Advance search",
    });


    $("#add_bankaccount").tooltip({
        placement: "left",
        title: "Add new bank account",
    });
    //end of tool tip
});

function checkAll(id) {
    var list = document.getElementsByClassName(id);
    var all = document.getElementById(id);

    if (all.checked) {
        for (i = 0; i < list.length; i++) {
            list[i].checked = 1;
        }
    } else {
        all.checked;

        for (i = 0; i < list.length; i++) {
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

    if (item.checked) {
        for (i = 0; i < allitems.length; i++) {
            if (allitems[i].checked) {
                cnt++;
            }
        }

        if (cnt == allitems.length) {
            all.checked = 1;
        }
    } else {
        all.checked = 0;
    }
}