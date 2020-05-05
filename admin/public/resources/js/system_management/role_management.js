// general
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

$(document).ready(function() { // hide/show for add
    $("#button_add_toggle").click(function() {
        $("#add_panel_body").slideToggle();
        $("#button_span_add_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    $("#button_list_toggle").click(function() {
        $("#list_panel_body").slideToggle();
        $("#button_span_list_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

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
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });
    //end of scroll to top

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

function displayFunctionsGiving() {
    var item = document.getElementById('use_2');

    // -- check if role permission management version 2 was used
    if($('#version2').length > 0){
        if (item && item.checked) {
            $('.functions_giving').each(function(){ 
                $(this).show();
            });
        } else {
            $('.functions_giving').each(function(){ 
                $(this).hide();
            });
        }
    }
    else{
        if (item && item.checked) {
        $('#functions_giving').show();
        } else {
            $('#functions_giving').hide();
        }
    }
}
// end of general

// ----------------------------------------------------------------------------------------------------------------------------- //

// sidebar.php
$(document).ready(function() {
    var url = document.location.pathname;
    var res = url.split("/");

    for (i = 0; i < res.length; i++) {
        switch (res[i]) {
            case 'verifyAddRole':
                $("a#add_users").addClass("active");
                break;
            case 'addRole':
            case 'checkRole':
            case 'verifyEditRole':
            case 'editRole':
            case 'searchRole':
            case 'viewRoles':
                $("a#checkRole").addClass("active");
                break;

            default:
                break;
        }
    }

    displayFunctionsGiving();
});
// end of sidebar.php

// ----------------------------------------------------------------------------------------------------------------------------- //


function calculateActiveInactiveCounter(){

    var active_ctr = 0;
    var inactive_ctr = 0;

    $('.parent-panel').each(function(){
        $(this).find('.non-parent').each(function(){

            if($(this).is(':checked'))
                active_ctr++;
            else
                inactive_ctr++;
        });

        $(this).find('.active_count').text(active_ctr);
        $(this).find('.inactive_count').text(inactive_ctr);

        active_ctr = 0;
        inactive_ctr = 0;
    });

}

// add_role.php
function checkAll(id) {
    var list = document.getElementsByClassName(id);
    var all = document.getElementById(id);

    if (id !== 'all_use' || id !== 'give_use') {
        var mngclass = document.getElementById(id).className;
        var mnglist = document.getElementsByClassName(mngclass);
        var mng = document.getElementById(mngclass);

        var cnt = 0;
    }

    if (all.checked) {
        for (i = 0; i < list.length; i++) {
            list[i].checked = 1;
        }

        if (id !== 'all_use' || id !== 'give_use') {
            for (i = 0; i < mnglist.length; i++) {
                if (mnglist[i].checked) { cnt++; }
            }
            if (cnt == mnglist.length && mng !== null) { mng.checked = 1; }
        }

        displayFunctionsGiving();
    } else {
        for (i = 0; i < list.length; i++) {
            list[i].checked = 0;
        }

        if (mng !== null) { mng.checked = 0; }

        displayFunctionsGiving();
    }

    // -- check if role permission management version 2 was used
    if($('#version2').length > 0){
        calculateActiveInactiveCounter();
    }

}

function uncheckRole(id) {
    var list = document.getElementById(id).getAttribute("parent");
    var listclass = document.getElementById(list).className;

    var all = document.getElementById(list);
    var allclass = document.getElementById(listclass);

    var item = document.getElementById(id);
    var listitems = document.getElementsByClassName(list);
    var allitems = document.getElementsByClassName(listclass);
    var listcnt = 0;
    var allcnt = 0;
    
    // -- check if role permission management version 2 was used
    if($('#version2').length > 0){
        calculateActiveInactiveCounter();
    }

    if (item.checked) {
        for (i = 0; i < listitems.length; i++) {
            if (listitems[i].checked) { listcnt++; }
        }
        if (listcnt == listitems.length) { all.checked = 1; }

        for (i = 0; i < allitems.length; i++) {
            if (allitems[i].checked) { allcnt++; }
        }

        if($('#version2').length <= 0){

            if (allcnt == allitems.length) { allclass.checked = 1; }
        }

        displayFunctionsGiving();
    } else {
        all.checked = 0;

        if($('#version2').length <= 0){
            allclass.checked = 0;
        }

        displayFunctionsGiving();
    }
}

function uncheckAll(id) {
    var list = document.getElementById(id).className;
    var all = document.getElementById(list);

    var item = document.getElementById(id);
    var allitems = document.getElementsByClassName(list);
    var cnt = 0;

    // -- check if role permission management version 2 was used
    if($('#version2').length > 0){
        calculateActiveInactiveCounter();
    }

    if (item.checked) {
        for (i = 0; i < allitems.length; i++) {
            if (allitems[i].checked) { cnt++; }
        }

        if (cnt == allitems.length) { all.checked = 1; }
    } else {
        all.checked = 0;
    }
}
// end of add_role.php

// ----------------------------------------------------------------------------------------------------------------------------- //

// view_role.php
function get_roles_pages(segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "role_management/getRolesPages/" + segment;

    var div = document.getElementById("roles-container");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            //            alert(document.getElementById("ecodetails-container").innerHTML);
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function get_search_roles_pages(search, segment) {
    var xmlhttp = GetXmlHttpObject();

    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    url = base_url + "role_management/getSearchRolesPages/" + search + "/" + segment;

    var div = document.getElementById("roles-container");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            //            alert(document.getElementById("ecodetails-container").innerHTML);
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

    url = base_url + "role_management/getRolesPages/" + segment;

    var div = document.getElementById("roles-container");

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4) {
            div.innerHTML = xmlhttp.responseText;
            //            alert(document.getElementById("ecodetails-container").innerHTML);
        }
        if (xmlhttp.readyState != 4) {
            div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
        }
    }
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

$(document).ready(function() {
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
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });
    //end of scroll to top
});
// end of view_role.php