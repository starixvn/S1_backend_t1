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

function getRadioCheckedValue(radio_name)
{
   var oRadio = document.forms[0].elements[radio_name];

   for(var i = 0; i < oRadio.length; i++)
   {
      if(oRadio[i].checked)
      {
         return oRadio[i].value;
      }
   }

   return '';
}

$(document).ready(function() {
    /*$(".edit").tooltip({
        placement: "right",
        title: "Edit Details",
    });

    $(".delete").tooltip({
        placement: "right",
        title: "Delete this Item",
    });

    $(".freeze").tooltip({
        placement: "right",
        title: "Freeze this affiliate account",
    });

    $(".unfreeze").tooltip({
        placement: "right",
        title: "Unfreeze this affiliate account",
    });

    $(".traffic").tooltip({
        placement: "right",
        title: "Display Traffic Statistics of Players under this affiliate account",
    });

    $(".active_acc").tooltip({
        placement: "right",
        title: "Activate this affiliate account",
    });

    $(".tags").tooltip({
        placement: "right",
        title: "Add tags to this affiliate account",
    });

    //tooltip
    $('input[type=text][name=username]').tooltip({
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

    $('input[type=email][name=email]').tooltip({
        placement: "top",
        trigger: "focus"
    });

    $('input[type=text][name=imtype]').tooltip({
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
    });*/

    //scroll to top
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
    //end of scroll to top

    //modal
    $('#save_changes').on("click", function(e) {
        e.preventDefault();
        $("form#modal_column_form").submit();
    });
    //end of modal

    // view_affiliate.php MAIN
    $("#hide_main").click(function() {
        $("#main_panel_body").slideToggle();
        $("#hide_main_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });
    // view_information.php PERSONAL INFO
    $("#hide_affpersonal_info").click(function() {
        $(".affpersonal_panel_body").slideToggle();
        $("#hide_affpi_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // view_information.php CONTACT INFO
    $("#hide_affcontact_info").click(function() {
        $(".aff_contactinfo_panel_body").slideToggle();
        $("#hide_affci_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // view_information.php BANK INFO
    $("#hide_affbank_info").click(function() {
        $(".affbank_panel_body").slideToggle();
        $("#hide_affbi_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // view_information.php AFF TERM
    $("#hide_share_settings").click(function() {
        $(".share_settings_body").slideToggle();
        $("#hide_share_settings_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    $("#hide_commission_settings").click(function() {
        $(".commission_settings_body").slideToggle();
        $("#hide_commission_settings_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    $("#hide_sub_aff_settings").click(function() {
        $(".sub_aff_settings_body").slideToggle();
        $("#hide_sub_aff_settings_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });

    // view_information.php AFF TRACK CODE
    $("#hide_afftrack_info").click(function() {
        $(".afftrack_panel_body").slideToggle();
        $("#hide_affatk_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // view_information.php AFF TRACK CODE
    $("#hide_affearn_info").click(function() {
        $(".affae_panel_body").slideToggle();
        $("#hide_affae_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });
    // view_information.php AFF TRACK CODE
    $("#hide_affpay_info").click(function() {
        $(".affap_panel_body").slideToggle();
        $("#hide_affap_up", this).toggleClass("glyphicon glyphicon-chevron-right glyphicon glyphicon-chevron-down");
    });

    //payment
    $('#myModal').on('shown.bs.modal', function () {
        $('#myInput').focus()
    });
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
// end of general

// ----------------------------------------------------------------------------------------------------------------------------- //

// sidebar.php
$(document).ready(function() {
    var url = document.location.pathname;
    var res = url.split("/");

    for (i = 0; i < res.length; i++) {
        switch(res[i]){
            case 'viewAffiliates':
            case 'userInformation':
            case 'postSearchPage':
            /*case 'addAffiliate':
            case 'stepTwo':
            case 'stepThree':
            case 'stepFour':
            case 'stepFive':
            case 'stepSix':
            case 'stepSeven':
            case 'stepEight':*/
            case 'editAffiliate':
            case 'selectedAffiliates':
            case 'actionType':
            case 'trafficStats':
                $("a#view_affiliates_list").addClass("active");
                break;

            case 'viewAffiliatePayment':
            case 'paymentSearchPage':
                $("a#affiliate_payments").addClass("active");
                break;

            case 'viewAffiliateBanner':
            case 'bannerSearchPage':
            case 'actionBanner':
                $("a#banner_settings").addClass("active");
                break;

            case 'viewAffiliateTag':
                $("a#affiliate_tag").addClass("active");
                break;

            case 'affiliate_statistics':
                 $("#affiliate_statistics").addClass("active");
                break;
            case 'viewAffiliateStatisticsToday':
            case 'viewAffiliateStatisticsDaily':
            case 'viewAffiliateStatisticsWeekly':
            case 'viewAffiliateStatisticsMonthly':
            case 'viewAffiliateStatisticsYearly':
            case 'viewAffiliatePlayers':
            case 'searchStatistics':
                $("a#affiliate_statistics").addClass("active");
                break;

            case 'viewTermsSetup':
            case 'setAsDefault':
                $("a#affiliate_terms").addClass("active");
                break;

            case 'viewAffiliateMonthlyEarnings':
            case 'editAffiliateMonthlyEarnings':
            case 'viewAffiliateEarnings':
                $("a#affiliate_earnings").addClass("active");
                break;

            case 'affiliate_withdraw':
                  $("a#affiliate_withdraw").addClass("active");
                  break;

            case 'affiliate_deposit':
                  $("a#affiliate_deposit").addClass("active");
                  break;

            case 'aff_list':
                  $("a#view_affiliates_list").addClass("active");
                  break;

            case 'viewDomain':
            case 'addDomain':
            case 'editDomain':
                  $("a#viewDomain").addClass("active");
                  break;

            default:
                break;
        }
    }
});
// end of sidebar.php

// ----------------------------------------------------------------------------------------------------------------------------- //

// affiliate.php
function displayAffiliates(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "affiliate_management/viewAffiliatesList/" + segment;

    var div = document.getElementById("affiliateList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }

        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send();
}

// function deleteAffiliate(affiliate_id, username) {
//     if (confirm('Are you sure you want to delete this affiliate: ' + username + '?')) {
//         window.location = base_url + "affiliate_management/deleteAffiliate/" + affiliate_id + "/" + username;
//     }
// }

function freezeAffiliate(affiliate_id, username) {
    if (confirm('Are you sure you want to freeze this affiliate: ' + username + '?')) {
        window.location = base_url + "affiliate_management/freezeAffiliate/" + affiliate_id + "/" + username;
    }
}

function unfreezeAffiliate(affiliate_id, username) {
    if (confirm('Are you sure you want to unfreeze this affiliate: ' + username + '?')) {
        window.location = base_url + "affiliate_management/unfreezeAffiliate/" + affiliate_id + "/" + username;
    }
}

function activateAffiliate(affiliate_id, username) {
    if (confirm('Are you sure you want to activate this affiliate: ' + username + '?')) {
        window.location = base_url + "affiliate_management/activateAffiliate/" + affiliate_id + "/" + username;
    }
}

function randomCode(len)
{
    var text = '';

    var charset = "abcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < len; i++ ) {
        text += charset.charAt(Math.floor(Math.random() * charset.length));
    }

    $('#tracking_code').val(text);
}


function randomNumber(len)
{
    var text = '';

    var charset = "0123456789";

    for( var i=0; i < len; i++ ) {
        text += charset.charAt(Math.floor(Math.random() * charset.length));
    }

    $('#tracking_code').val(text);
}

function viewAffiliateWithCurrentPage(affiliateId, path) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "affiliate_management/" + path + "/" + affiliateId;

    $('#toggleView').removeClass('col-md-12');
    $('#toggleView').addClass('col-md-7');

    document.getElementById('affiliate_details').style.display = "block";

    if ($('#toggleView').hasClass('col-md-7')) {
        $('table#myTable td#visible').hide();
        $('table#myTable th#visible').hide();
    } else {
        $('table#myTable td#visible').show();
        $('table#myTable th#visible').show();
    }

    var div = document.getElementById("affiliate_details");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            //$('html, body').animate({scrollTop: $("#toggleView").offset().top}, 1500);
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function closeDetails() {
    $('#toggleView').removeClass('col-md-7');
    $('#toggleView').addClass('col-md-12');

    if ($('#toggleView').hasClass('col-md-7')) {
        $('table#myTable td#visible').hide();
        $('table#myTable th#visible').hide();
    } else {
        $('table#myTable td#visible').show();
        $('table#myTable th#visible').show();
    }
    document.getElementById('affiliate_details').style.display = "none";
}

function specify(e) {
    if (e.value == 'specify') {
        $('#reportrange').show();
        $("#start_date").attr("disabled", false);
        $("#end_date").attr("disabled", false);
    } else {
        $('#reportrange').hide();
        $("#start_date").attr("disabled", true);
        $("#end_date").attr("disabled", true);
    }
}

function checkPeriod(e) {
    if (e.value == '') {
        $("#reportrange").hide();
        $(".type_date").attr("disabled", true);
    } else {
        $("#reportrange").show();
        $(".type_date").attr("disabled", false);
    }
}

function showDivs(e) {
    if(e.value == 'tag') {
        document.getElementById('tag').style.display = "block";
        document.getElementById('block_lock').style.display = "none";
        document.getElementById('game').style.display = "none";
    } else {
        document.getElementById('block_lock').style.display = "block";
        document.getElementById('tag').style.display = "none";
        document.getElementById('game').style.display = "none";
    }
}

function displayTrafficStats(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "affiliate_management/displayTrafficStats/" + segment;

    var div = document.getElementById("trafficList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }

        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send();
}

function players(trafficId) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "affiliate_management/players/" + trafficId;

    var div = document.getElementById("trafficList");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }

        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send();
}

function getMonthlyEarnings(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var affiliate_id = document.getElementById('affiliate_id').value;

    url = base_url + "affiliate_management/getMonthlyEarnings/" + segment + "/" + affiliate_id;

    var div = document.getElementById("monthlyEarnings");

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

function getPayments(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var affiliate_id = document.getElementById('affiliate_id').value;

    url = base_url + "affiliate_management/getPayments/" + segment + "/" + affiliate_id;

    var div = document.getElementById("paymentHistory");

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

function editAffiliateInfo(affiliate_id) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    /*var firstname = document.getElementById('aff_firstname').value;
    var lastname = document.getElementById('aff_lastname').value;
    var birthday = document.getElementById('aff_birthday').value;
    var gender = document.getElementById('aff_gender').value;
    var company = document.getElementById('aff_company').value;
    var occupation = document.getElementById('aff_occupation').value;
    var email = document.getElementById('aff_email').value;
    var city = document.getElementById('aff_city').value;
    var address = document.getElementById('aff_address').value;
    var zip = document.getElementById('aff_zip.value');
    var state = document.getElementById('aff_state').value;
    var country = document.getElementById('aff_country').value;
    var mobile = document.getElementById('aff_mobile').value;
    var phone = document.getElementById('aff_phone').value;
    var imType1 = document.getElementById('aff_imType1').value;
    var im1 = document.getElementById('aff_im1').value;
    var imType2 = document.getElementById('aff_imType2').value;
    var im2 = document.getElementById('aff_im2').value;
    var modeOfContact = document.getElementById('aff_modeOfContact').value;
    var website = document.getElementById('aff_website').value;*/

    url = base_url + "affiliate_management/editAffiliateInfo/" + affiliate_id;

    var div = document.getElementById("affiliate_info");

    var poststr =
        /*"&firstname=" + encodeURI(firstname) +
        "&lastname=" + encodeURI(lastname) +
        "&birthday=" + encodeURI(birthday) +
        "&gender=" + encodeURI(gender) +
        "&company=" + encodeURI(company) +
        "&occupation=" + encodeURI(occupation) +
        "&email=" + encodeURI(email) +
        "&city=" + encodeURI(city) +
        "&address=" + encodeURI(address) +
        "&zip=" + encodeURI(zip) +
        "&state=" + encodeURI(state) +
        "&country=" + encodeURI(country) +
        "&mobile=" + encodeURI(mobile) +
        "&phone=" + encodeURI(phone) +
        "&imType1=" + encodeURI(imType1) +
        "&im1=" + encodeURI(im1) +
        "&imType2=" + encodeURI(imType2) +
        "&im2=" + encodeURI(im2) +
        "&modeOfContact=" + encodeURI(modeOfContact) +
        "&website=" + encodeURI(website);*/

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }

        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    //xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(null);
}

function imCheck(value, im) {
    if (value == "") {
        $('#im'+im).attr('readonly', true);
        $('#im'+im).val('');
    } else {
        $('#im'+im).attr('readonly', false);
        if(value == "QQ"){
            $('#im'+im).val('');
            $('#im'+im).addClass("number_only");
            $('#im'+im).attr("type", "number");
            $('#im'+im).attr("min", "1");
        }else{
            $('#im'+im).removeClass("number_only");
            $('#im'+im).attr("type", "text");
        }
    }
}
// end of affiliate.php

// ----------------------------------------------------------------------------------------------------------------------------- //

// banner_settings.php
function setURL(value) {
    var val = value;
    var res = val.split("\\");

    document.getElementById('banner_url').value = base_url + 'resources/images/banner/' + res[2];
}

function get_banner_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "affiliate_management/getBannerPages/" + segment;

    var div = document.getElementById("bannerList");

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

function viewBanner(value) {
    document.getElementById('banner_img').src = value;
}

function checkCategory(value) {
    if(value == 'Others') {
        $("#width").val('');
        $("#width").attr("readonly", false);

        $("#height").val('');
        $("#height").attr("readonly", false);
    } else {
        var category = value.split('(');
        var size = category[1].split('x');

        $("#width").val(size[0]);
        $("#width").attr("readonly", true);

        $("#height").val(size[1].substring(0, size[1].length - 1));
        $("#height").attr("readonly", true);
    }
}

function activateBanner(banner_id, banner_name) {
    if (confirm('Are you sure you want to Activate this banner: ' + banner_name + '?')) {
        window.location = base_url + "affiliate_management/activateBanner/" + banner_id + "/" + banner_name;
    }
}

function deactivateBanner(banner_id, banner_name) {
    if (confirm('Are you sure you want to Deactivate this banner: ' + banner_name + '?')) {
        window.location = base_url + "affiliate_management/deactivateBanner/" + banner_id + "/" + banner_name;
    }
}
// end of banner_settings.php

// ----------------------------------------------------------------------------------------------------------------------------- //

// payment_history.php
function displayPaymentHistory(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var username = document.getElementById('username').value;
    var start_date = document.getElementById('start_date').value;
    var end_date = document.getElementById('end_date').value;
    var desc = document.getElementById('desc').checked;
    var sort = document.getElementById('sort').value;

    url = base_url + "affiliate_management/displayPaymentHistory/" + segment;

    var div = document.getElementById("view_payments");
    $('#view_payments').show();

    var poststr =
        "&username=" + encodeURI(username) +
        "&start_date=" + encodeURI(start_date) +
        "&end_date=" + encodeURI(end_date) +
        "&desc=" + encodeURI(desc) +
        "&sort=" + encodeURI(sort);

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }

        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(poststr);
}

function deactivatePayment(payment_id, bank_name, affiliate_id) {
    if (confirm('Are you sure you want to Deactivate this payment method: ' + bank_name + '?')) {
        window.location = base_url + "affiliate_management/deactivatePayment/" + payment_id + "/" + affiliate_id;
    }
}

function activatePayment(payment_id, bank_name, affiliate_id) {
    if (confirm('Are you sure you want to Activate this payment method: ' + bank_name + '?')) {
        window.location = base_url + "affiliate_management/activatePayment/" + payment_id + "/" + affiliate_id;
    }
}

function deletePayment(payment_id, bank_name, affiliate_id) {
    if (confirm('Are you sure you want to Delete this payment method: ' + bank_name + '?')) {
        window.location = base_url + "affiliate_management/deletePayment/" + payment_id + "/" + affiliate_id;
    }
}
// end of payment_history.php

// ----------------------------------------------------------------------------------------------------------------------------- //

// payments.php
function displayPayments(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "affiliate_management/displayPayments/" + segment;

    var div = document.getElementById("paymentList");

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

function processPayment(request_id, username) {
    if (confirm('Are you sure you want to mark the payment of ' + username + ' as processing?')) {
        window.location = base_url + "affiliate_management/processPayment/" + request_id + "/" + username;
    }
}

function approvePayment(request_id, username) {
    if (confirm('Are you sure you want to mark the payment of ' + username + ' as processed?')) {
        window.location = base_url + "affiliate_management/approvePayment/" + request_id + "/" + username;
    }
}

function denyPayment(request_id, username) {
    if (confirm('Are you sure you want to mark the payment of ' + username + ' as denied?')) {
        window.location = base_url + "affiliate_management/denyPayment/" + request_id + "/" + username;
    }
}
// end of payment_history.php

// ----------------------------------------------------------------------------------------------------------------------------- //

// date function
function setDateTime(period) {
    switch(period) {
        case "Today":
            var result = getDateToSet('Today');
            $('#start_date').val(result[0]);
            $('#end_date').val(result[1]);
            break;

        case "Weekly":
            var result = getDateToSet('Weekly');
            $('#start_date').val(result[0]);
            $('#end_date').val(result[1]);
            break;

        case "Monthly":
            var result = getDateToSet('Monthly');
            $('#start_date').val(result[0]);
            $('#end_date').val(result[1]);
            break;

        case "Yearly":
            var result = getDateToSet('Yearly');
            $('#start_date').val(result[0]);
            $('#end_date').val(result[1]);
            break;

        default:
            $('#start_date').val('yyyy-mm-dd');
            $('#end_date').val('yyyy-mm-dd');
            break;
    }
}

function getDateToSet(period) {
    var now = new Date();
    var month = (now.getMonth() + 1);
    var day = now.getDate();
    if(month < 10)
        month = "0" + month;
    if(day < 10)
        day = "0" + day;

    var start_date = null;
    var end_date = null;

    switch(period) {
        case "Today":
            start_date = now.getFullYear() + '-' + month + '-' + day;
            end_date = now.getFullYear() + '-' + month + '-' + day;
            break;

        case "Weekly":
            var first = now.getDate() - now.getDay() + (now.getDay() == 0 ? -6:1); // First day is the day of the month - the day of the week
            var last = first + 6;

            if(first < 10) {
                first = '0' + first;
            }

            if(last < 10) {
                last = '0' + last;
            }

            start_date = now.getFullYear() + '-' + month + '-' + first;
            end_date = now.getFullYear() + '-' + month + '-' + last;
            break;

        case "Monthly":
            var last = new Date(now.getFullYear(), month, 0);
            start_date = last.getFullYear() + '-' + month + '-01';
            end_date = last.getFullYear() + '-' + month + '-' + last.getDate();
            break;

        case "Yearly":
            start_date = now.getFullYear() + '-01-01';
            end_date = now.getFullYear() + '-12-31';
            break;

        default:
            break;

    }

    return [start_date, end_date]
}
// end of date function

// ----------------------------------------------------------------------------------------------------------------------------- //

// add affiliates
function gamePercentage(id) {
    var percentage_check = document.getElementById('check_percentage');

    if(id != null) {
        var game_check = document.getElementById('check_game_' + id);

        if(game_check.checked && percentage_check.checked) {
            $('#percentage_' + id).attr("readonly", false);
        } else {
            $('#percentage_' + id).attr("readonly", true);
            $('#percentage_' + id).val('0');
        }
    } else {
        if(percentage_check.checked) {
            $('.percentage').attr("readonly", false);
        } else {
            $('.percentage').attr("readonly", true);
            $(".percentage").val('0');
        }
    }
}

function percentage() {
    var percentage_check = document.getElementById('check_percentage');

    if(percentage_check.checked) {
        $('.percentage').attr("readonly", false);
    } else {
        $('.percentage').attr("readonly", true);
        $(".percentage").val('0');
    }
}

// end of add affiliates

// ----------------------------------------------------------------------------------------------------------------------------- //

// AffiliateManagementProcess
$(document).ready(function() {
    AffiliateManagementProcess.initialize();
});

var AffiliateManagementProcess = {
    initialize : function() {
      // console.log("initialized now!");
      //tooltip
      $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
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

    $(".letters_numbers_hypen_only").keydown(function (e) {
            var code = e.keyCode || e.which;
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(code, [46, 8, 9, 27, 32, 13, 110, 190, 189]) !== -1 ||
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

    $(".letters_numbers_hypen_only").keydown(function (e) {
            var code = e.keyCode || e.which;
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(code, [46, 8, 9, 27, 32, 13, 110, 190, 189]) !== -1 ||
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

    $(".letters_numbers_hypen_nospace_only").keydown(function (e) {
            var code = e.keyCode || e.which;
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(code, [46, 8, 9, 27, 13, 110, 190, 189]) !== -1 ||
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
    },



    getTagDetails : function(tag_id) {
        $.ajax({
            'url' : base_url + 'affiliate_management/getTagDetails/' + tag_id,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                     // console.log(data[0]);
                     $('#tagId').val(data[0].tagId);
                     $('#tagName').val(data[0].tagName);
                     $('#tagDescription').val(data[0].tagDescription);
                        }
        },'json');
        return false;
    },


    getBannerDetails : function(banner_id) {
        $.ajax({
            'url' : base_url + 'affiliate_management/getBannerDetails/' + banner_id,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                         //console.log(data[0]);
                        $('#bannerId').val(data[0].bannerId);
                        $('#bannerName').val(data[0].bannerName);
                        $('#bannerLanguage').val(data[0].language);
                        $('#banner_url').val(data[0].bannerURL);
                        $('#img-prev').attr('src','/'+data[0].bannerURL).show() ;
                    }
        },'json');
        return false;
    },
}
// end of AffiliateManagementProcess

// ----------------------------------------------------------------------------------------------------------------------------- //

// affiliate tags
function sortTag(sort) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "affiliate_management/sortTag/" + sort;

    var div = document.getElementById("tag_table");

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

function searchTag() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var search = document.getElementById('search').value;

    url = base_url + "affiliate_management/searchTag/" + search;

    var div = document.getElementById("tag_table");

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

function get_tag_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "affiliate_management/get_tag_pages/" + segment;

    var div = document.getElementById("tag_table");

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
// end of affiliate tags

// ----------------------------------------------------------------------------------------------------------------------------- //

// terms default setup
function editTermsDefault() {
    $('.percentage').attr('readonly', false);
    $('.active_players').attr('readonly', false);

    $('#btn_submit').show();
    $('#btn_edit').hide();
}

function setTermsDefault() {
    $('#percentage').attr('readonly', true);
    $('#active_players').attr('readonly', true);

    $('#btn_submit').hide();
    $('#btn_edit').show();
}
// end of terms default setup

// ----------------------------------------------------------------------------------------------------------------------------- //

// affiliate statistics
function searchStatistics() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var period = document.getElementById('period').value;
    var start_date = document.getElementById('start_date').value;
    var end_date = document.getElementById('end_date').value;

    if(period == 'daily') {
        url = base_url + "affiliate_management/viewAffiliateStatisticsDaily";
    } else {
        return;
    }

    var div = document.getElementById("statisticsList");

    var poststr =
        "&period=" + encodeURI(period) +
        "&start_date=" + encodeURI(start_date) +
        "&end_date=" + encodeURI(end_date);

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }

        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(poststr);
}

function viewAffiliateStatisticsToday(date) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "affiliate_management/viewAffiliateStatisticsToday";

    var div = document.getElementById("statisticsList");

    var poststr =
        "&date=" + encodeURI(date);

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
        }

        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(poststr);
}
// end of affiliate statistics

// ----------------------------------------------------------------------------------------------------------------------------- //

// affiliate monthly earnings
function get(elem) { return parseFloat(document.getElementById(elem).value) || 0; }

function computeClosingBalance() {
    var opening_balance = get('opening_balance');
    var earnings = get('earnings');
    var approved = get('approved');
    var balance = opening_balance + earnings;

    if (approved > balance) {
        document.getElementById('approved_error').innerHTML = 'Approved Earnings should be <= Current Earnings!';
        document.getElementById('approved').value = 0;
        document.getElementById('approved').focus();
    } else {
        var closing_balance = balance - approved;

        document.getElementById('closing_balance').value = closing_balance;
    }
}
// end of affiliate monthly earnings



// ----------------------------------------------------------------------------------------------------------------------------- //

// ----------------------------------------------------------------------------------------------------------------------------- //

//  domain

function activateDomain(domain_id, domain_name) {
    if (confirm('Are you sure you want to activate this domain: ' + domain_name + '?')) {
        window.location = base_url + "affiliate_management/activateDomain/" + domain_id + "/" + encode64(domain_name);
    }
}

function deactivateDomain(domain_id, domain_name) {
    if (confirm('Are you sure you want to deactivate this domain: ' + domain_name + '?')) {
        window.location = base_url + "affiliate_management/deactivateDomain/" + domain_id + "/" + encode64(domain_name);
    }
}

function encode64(input) {
     input = escape(input);
     var output = "";
     var chr1, chr2, chr3 = "";
     var enc1, enc2, enc3, enc4 = "";
     var i = 0;

     var keyStr = "ABCDEFGHIJKLMNOP" +
               "QRSTUVWXYZabcdef" +
               "ghijklmnopqrstuv" +
               "wxyz0123456789";

     do {
        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
           enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
           enc4 = 64;
        }

        output = output +
           keyStr.charAt(enc1) +
           keyStr.charAt(enc2) +
           keyStr.charAt(enc3) +
           keyStr.charAt(enc4);
        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";
     } while (i < input.length);

     return output;
  }


function autogrow(textarea){
    var adjustedHeight = textarea.clientHeight;

    adjustedHeight = Math.max(textarea.scrollHeight,adjustedHeight);
    if (adjustedHeight>textarea.clientHeight){
        textarea.style.height = adjustedHeight + 'px';
    }
}

// end of domain
// ----------------------------------------------------------------------------------------------------------------------------- //