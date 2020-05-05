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

function setIP(value) {
	document.getElementById('ip_name').value = value;
}

function IpList(value) {
	var xmlhttp = GetXmlHttpObject();

    if ( xmlhttp == null )
    {
        alert ("Browser does not support HTTP Request");
        return;
    }

	/*var item = document.getElementById(value);
	var checked;

	if (item.checked) {
		checked = true;
	} else {
		checked = false;
	}*/

    url = base_url + "ip_management/setIpList/"+value;

    var div = document.getElementById("ipList");

    xmlhttp.onreadystatechange = function(){
        if ( xmlhttp.readyState == 4 )
        {
    		div.innerHTML = xmlhttp.responseText;
        }
        if ( xmlhttp.readyState != 4 )
        {
			//div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="'+imgloader+'"><br/>Loading. Please wait.</td></tr></table>';
        }
    }

    xmlhttp.open("GET",url,true);
    xmlhttp.send(null);
}

$(document).ready(function() { // hide/show for add
	$("#button_list_toggle").click(function(){
		$("#list_panel_body").slideToggle();
		$("#button_span_list_up",this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
	});

    $("#IpList").tooltip({
        placement: "bottom",
        title: "Enabling this feature will cause any IP not in white list can not login to the system,please make sure you have put your own IP in to white list before open it.",
    });

});
// end of view_list.php

// ----------------------------------------------------------------------------------------------------------------------------- //

// sidebar.php
$(document).ready(function() {
	var url = document.location.pathname;
	var res = url.split("/");

	for (i = 0; i < res.length; i++) {
		switch(res[i]){
			case 'viewList':
			case 'editIp':
            case 'addIp':
				$("a#viewIp").addClass("active");
				break;

			case 'viewEmailSettings':
				$("a#viewEmailSettings").addClass("active");
				break;

			default:
				break;
		}
	}
});
// end of sidebar.php

// ----------------------------------------------------------------------------------------------------------------------------- //

// view_email.php
function emailEdit(value) {
	if(value == 0) {
		$('#save').show();
		$('#edit').hide();

		$('#email').removeAttr('readonly');
		$('#password').removeAttr('readonly');
		$('#password').val('');
	}else if(value == 1) {
		$('#edit').show();
		$('#save').hide();

		$('#email').addAttr('readonly');
		$('#password').addAttr('readonly');
	}
}
// end of view_email.php

// ----------------------------------------------------------------------------------------------------------------------------- //

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
});