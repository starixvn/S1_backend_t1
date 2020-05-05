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

function get_vipgroupsetting_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "vipsetting_management/get_vipgroupsetting_pages/" + segment;

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

function sortVipGroup(sort) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "vipsetting_management/sortVipGroup/" + sort;

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

function searchVipGroup() {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var search = document.getElementById('search').value;

    url = base_url + "vipsetting_management/searchVipGroupList/" + search;

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
    PlayerManagementProcess.initialize();
});
//player management module
var PlayerManagementProcess = {

    initialize : function() {
      // console.log("initialized now!");

        //numeric only
        $("#groupLevelCount").numeric();

        //tooltip
        $('body').tooltip({
            selector: '[data-toggle="tooltip"]'
        });

        //for add vip group panel
        var is_addPanelVisible = false;

        //for ranking level edit form
        var is_editPanelVisible = false;

        if(!is_addPanelVisible){
            $('.add_vip_group_sec').hide();
        }else{
            $('.add_vip_group_sec').show();
        }

        if(!is_editPanelVisible){
            $('.edit_vip_group_sec').hide();
        }else{
            $('.edit_vip_group_sec').show();
        }

        //show hide add vip group panel
        $("#add_vip_group").click(function () {
            if(!is_addPanelVisible){
                is_addPanelVisible = true;
                $('.add_vip_group_sec').show();
                $('.edit_vip_group_sec').hide();
            }else{
                is_addPanelVisible = false;
                $('.add_vip_group_sec').hide();
            }
        });

        //show hide edit vip group panel
        $(".editVipGroupSettingBtn").click(function () {
                is_editPanelVisible = true;
                $('.add_vip_group_sec').hide();
                $('.edit_vip_group_sec').show();
        });

        //cancel add vip group
        $(".addvip-cancel-btn").click(function () {
                is_addPanelVisible = false;
                $('.add_vip_group_sec').hide();
        });

        //cancel add vip group
        $(".editvip-cancel-btn").click(function () {
                is_editPanelVisible = false;
                $('.edit_vip_group_sec').hide();
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
    },

    getVIPGroupDetails : function(vipsettingId,currentLang) {
        // console.log('test'+vipsettingId);
        is_editPanelVisible = true;
        $('.add_vip_group_sec').hide();
        $('.edit_vip_group_sec').show();
        $.ajax({
            'url' : base_url + 'player_management/getVIPGroupDetails/' + vipsettingId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                $('#editVipGroupId').val(vipsettingId);
                $('#editGroupLevelCount').val(data[0].groupLevelCount);
                if (data[0].groupName.toLowerCase().indexOf("_json:") >= 0){
                    var langConvert = jQuery.parseJSON(data[0].groupName.substring(6));
                    $('#editGroupNameView').val(langConvert[currentLang]);
                    $('#editGroupName').val(data[0].groupName);
                } else {
                    $('#editGroupNameView').val(data[0].groupName);
                    $('#editGroupName').val(data[0].groupName);
                }
                $('#editGroupDescription').val(data[0].groupDescription);
                $('#can_be_self_join_in').prop('checked', data[0].can_be_self_join_in == '1');
                $('#editImage').val(data[0].image);
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