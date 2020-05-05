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

function get_depositpromo_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "depositpromo_management/get_depositpromo_pages/" + segment;

    var div = document.getElementById("depositpromo_table");

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

function sortDepositPromo(sort) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "depositpromo_management/sortDepositPromo/" + sort;

    var div = document.getElementById("depositpromo_table");

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

function searchDepositPromo() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var search = document.getElementById('search').value;

    url = base_url + "depositpromo_management/searchDepositPromo/" + search;

    var div = document.getElementById("depositpromo_table");

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
            case 'friend_referral_settings':
                $("a#view_friend_referral").addClass("active");
                break;
            case 'viewDepositPromoManager':
                $("a#view_deposit_promo_list").addClass("active");
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
    DepositPromoManagementProcess.initialize();
});
//player management module
var DepositPromoManagementProcess = {

    initialize : function() {
      // console.log("initialized now!");

        //numeric only
        $("#requiredDepositAmount").numeric();
        $("#bonusAmount").numeric();
        $("#maxDepositAmount").numeric();
        $("#maxBonusAmount").numeric();
        $("#totalBetsAmount").numeric();
        $("#expirationDayCnt").numeric();

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

            $(target).prop('checked', this.checked).prop('selected', this.checked).trigger('chosen:updated');

        });

        $('[data-untoggle="checkbox"]').on('change', function() {

            var element = $(this);
            var target  = element.data('target');

            if ( ! this.checked) {
                $(target).prop('checked', false).prop('selected', false);
            }

        });

        //for add depositpromo panel
        var is_addPanelVisible = false;

        //for depositpromo edit form
        var is_editPanelVisible = false;

        if(!is_addPanelVisible){
            $('.add_depositpromo_sec').hide();
        }else{
            $('.add_depositpromo_sec').show();
        }

        if(!is_editPanelVisible){
            $('.edit_depositpromo_sec').hide();
        }else{
            $('.edit_depositpromo_sec').show();
        }

        //show hide add vip group panel
        $("#add_depositpromo").click(function () {
            if(!is_addPanelVisible){
                is_addPanelVisible = true;
                $('.add_depositpromo_sec').show();
                $('.edit_depositpromo_sec').hide();
                $('#addDepositPromoGlyhicon').removeClass('glyphicon glyphicon-plus-sign');
                $('#addDepositPromoGlyhicon').addClass('glyphicon glyphicon-minus-sign');
            }else{
                is_addPanelVisible = false;
                $('.add_depositpromo_sec').hide();
                $('#addDepositPromoGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
                $('#addDepositPromoGlyhicon').addClass('glyphicon glyphicon-plus-sign');
            }
        });

        //show hide edit vip group panel
        $(".editDepositPromoBtn").click(function () {
                is_editPanelVisible = true;
                $('.add_depositpromo_sec').hide();
                $('.edit_depositpromo_sec').show();
        });

        //cancel add vip group
        $(".add_depositpromo-cancel-btn").click(function () {
                is_addPanelVisible = false;
                $('.add_depositpromo_sec').hide();
                $('#addDepositPromoGlyhicon').removeClass('glyphicon glyphicon-minus-sign');
                $('#addDepositPromoGlyhicon').addClass('glyphicon glyphicon-plus-sign');
        });

        //cancel add vip group
        $(".edit_depositpromo-cancel-btn").click(function () {
                is_editPanelVisible = false;
                $('.edit_depositpromo_sec').hide();
        });
    },

    getDepositPromoDetails : function(depositpromoId) {
        is_editPanelVisible = true;
        $('.add_depositpromo_sec').hide();
        $('.edit_depositpromo_sec').show();
        $.ajax({
            'url' : base_url + 'depositpromo_management/getDepositPromoDetails/' + depositpromoId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                     // console.log(data[0]);
                     $('#editDepositPromoId').val(data[0].depositpromoId);
                     $('#editDepositPromoName').val(data[0].promoName);
                     $('#editDepositPromoPeriodStart').val(data[0].promoPeriodStart);
                     $('#editDepositPromoPeriodEnd').val(data[0].promoPeriodEnd);
                     $('#editRequiredDepositAmount').val(data[0].requiredDepositAmount);
                     $('#editBonusAmount').val(data[0].bonusAmount);
                     $('#editMaxDepositAmount').val(data[0].maxDepositAmount);
                     $('#editMaxBonusAmount').val(data[0].maxBonusAmount);
                     $('#editTotalBetsAmount').val(data[0].totalBetRequirement);
                     $('#editExpirationDayCnt').val(data[0].expirationDayCnt);
                     // for(var i = 0; i < data[0].depositPromoPlayerLevelLimit.length; i++){
                     //    console.log('lvl: '+data[0].depositPromoPlayerLevelLimit[i].groupName);
                     //    $('.chosen-choices').append('<li class="search-choice"><span>'+data[0].depositPromoPlayerLevelLimit[i].groupName+' '+data[0].depositPromoPlayerLevelLimit[i].groupLevel+'</span><a class="search-choice-close" data-option-array-index="'+data[0].depositPromoPlayerLevelLimit[i].groupLevel+'"></a>"');
                     // }
                     $('.currentDepositPromoPlayerLevelLimit').html('');
                     for(var i = 0; i < data[0].depositPromoPlayerLevelLimit.length; i++){
                        // console.log('lvl: '+data[0].depositPromoPlayerLevelLimit[i].groupName);

                        $('.currentDepositPromoPlayerLevelLimit').append(data[0].depositPromoPlayerLevelLimit[i].groupName+' '+data[0].depositPromoPlayerLevelLimit[i].vipLevel+', ');
                     }
                     if (data[0].bonusAmountRuleType == 1) {
                        $('#editBonusAmountRuleType1').prop("checked", true);
                        $('#editBonusAmountRuleType2').prop("checked", false);
                        $('.maxBonusAmount-sec').hide();
                     } else {
                        $('#editBonusAmountRuleType1').prop("checked", false);
                        $('#editBonusAmountRuleType2').prop("checked", true);
                        $('.maxBonusAmount-sec').show();
                     }
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

    //bonus amount rule type
    $(".maxBonusAmount-sec").hide();
    $("#maxBonusAmount").val('');

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


    $("#add_depositpromo").tooltip({
        placement: "left",
        title: "Add new deposit promo",
    });
    //end of tool tip
});


function checkAmountRuleType(type){
    //$("#maxBonusAmount").attr("disabled", true);
    if(type == 'show'){
        $(".maxBonusAmount-sec").show();
    }else{
        $(".maxBonusAmount-sec").hide();
        $("#maxBonusAmount").val('');
    }
}

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