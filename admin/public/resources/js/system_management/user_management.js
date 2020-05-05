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
// end of general

// ----------------------------------------------------------------------------------------------------------------------------- //

// sidebar.php
$(document).ready(function () {
	var url = document.location.pathname;
	var res = url.split("/");
	for (i = 0; i < res.length; i++) {

		switch (res[i]) {
		case 'viewLogs':
			$("a#view_logs").addClass("active");
			break;
		case 'viewUsers':
		case 'viewUser':
		case 'viewEditUser':
		case 'viewResetPassword':
		case 'postResetPassword':
		case 'postFilter':
			$("a#view_users").addClass("active");
			break;

		case 'viewAddUser':
		case 'nextAddUser':
		case 'postAddUser':
			$("a#add_users").addClass("active");
			break;

		case 'viewSetRole':
			$("a#checkRole").addClass("active");
			break;

		case 'viewGameDescription':
			$("a#viewGameDescription").addClass("active");
			break;

		case 'viewGameApi':
			$("a#viewGameApi").addClass("active");
			break;
		case 'viewPaymentApi':
			$("a#viewPaymentApi").addClass("active");
			break;			

		case 'viewCurrency':
		case 'deleteSelectedCurrency':
		case 'actionCurrency':
			$("a#view_currency").addClass("active");
			break;
		
		case 'viewDuplicateAccount':
			$("a#view_api_settings").addClass("active");
			break;
		default:
			break;
		}
	}
});
// end of sidebar.php

// ----------------------------------------------------------------------------------------------------------------------------- //

// add_role.php
$(document).ready(function () { // hide/show for add
	$("#button_list_toggle").click(function () {
		$("#list_panel_body").slideToggle();
		$("#button_span_list_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
	});
});
// end of add_role.php

// view_user_setting.php
$(document).ready(function () { // hide/show for add
	$("#email_toggle").click(function () {
		$("#email_panel_body").slideToggle();
		$("#button_span_email_up", this).toggleClass("glyphicon glyphicon-chevron-down glyphicon glyphicon-chevron-up");
	});
});

$(document).ready(function () { // hide/show for add
	$("#password_toggle").click(function () {
		$("#password_panel_body").slideToggle();
		$("#button_span_password_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
	});
});

$(document).ready(function () { // hide/show for add
	$("#safety_question_toggle").click(function () {
		$("#safety_question_panel_body").slideToggle();
		$("#button_span_safety_question_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
	});
});

$(document).ready(function () { // hide/show for add
	$("#button_add_toggle").click(function () {
		$("#add_panel_body").slideToggle();
		$("#button_span_list_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
	});
});
// end of view_user_setting.php

$(document).ready(function () {
	$(".number_only").keydown(function (e) {
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

// ----------------------------------------------------------------------------------------------------------------------------- //

//checkbox all
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

function get_user_pages(segment) {
	var xmlhttp = GetXmlHttpObject();

	if (xmlhttp == null) {
		alert("Browser does not support HTTP Request");
		return;
	}

	url = base_url + "user_management/get_user_pages/" + segment;

	var div = document.getElementById("user-container");

	xmlhttp.onreadystatechange = function () {
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

function get_currency_pages(segment) {
	var xmlhttp = GetXmlHttpObject();

	if (xmlhttp == null) {
		alert("Browser does not support HTTP Request");
		return;
	}

	url = base_url + "user_management/get_currency_pages/" + segment;

	var div = document.getElementById("currency_table");

	xmlhttp.onreadystatechange = function () {
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

	xmlhttp.onreadystatechange = function () {
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

function clearUser() {
	$('#username').val('');
	$('#realname').val('');
	$('#department').val('');
	$('#position').val('');
	$('#email').val('');
}

// $(document).ready(function () {
//     $('.userName').live('click', function() {
//         alert('click');
//         console.log('click');
//     });
// });

$(document).on("click", ".userName", function () {
	// console.log('click');
});

$(".alert-message").alert();
window.setTimeout(function () {
	$(".alert-message").alert('close');
}, 2000);

$(function () {
	$('span#description').popover({
		html : true
	});
});

$(document).ready(function () {
	$('#randomPassword').click(function () {
		if (this.checked) {
			$("#passwordField").hide();
			$("#hiddenField").show();
			$("#password").prop('disabled', true);
			$("#cpassword").prop('disabled', true);
			$("#hiddenPassword").prop('disabled', false);
		} else {
			$("#passwordField").show();
			$("#hiddenField").hide();
			$("#password").prop('disabled', false);
			$("#cpassword").prop('disabled', false);
			$("#hiddenPassword").prop('disabled', true);
		}
	});
});

$(document).ready(function () {
	$('#checkCustomize').click(function () {
		if ($(this).attr("value") == "checkCustomize") {
			$("#safety_question_field").toggle();
			$("#csafety_question_field").toggle();
		}
	});
});

function sortBy(sortName) {
	var xmlhttp = GetXmlHttpObject();
	var order = {
		username : ""
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

	xmlhttp.onreadystatechange = function () {
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

function sortCurrency(sort) {
	var xmlhttp = GetXmlHttpObject();

	if (xmlhttp == null) {
		alert("Browser does not support HTTP Request");
		return;
	}

	url = base_url + "user_management/sortCurrency/" + sort;

	var div = document.getElementById("currency_table");

	xmlhttp.onreadystatechange = function () {
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

function searchCurrency() {
	var xmlhttp = GetXmlHttpObject();

	if (xmlhttp == null) {
		alert("Browser does not support HTTP Request");
		return;
	}

	var search = document.getElementById('search').value;

	url = base_url + "user_management/searchCurrency/" + search;

	var div = document.getElementById("currency_table");

	xmlhttp.onreadystatechange = function () {
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

//add_users.php

$(document).ready(function () {
	$("#cpassword").keyup(checkPass);
});

$(document).ready(function () {
	$("#password").keyup(checkPass);
});

function checkPass() {
	//Store the password field objects into variables ...
	var pass1 = document.getElementById('password');
	var pass2 = document.getElementById('cpassword');
	//Store the Confimation Message Object ...
	var message = document.getElementById('lcpassword');
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
		message.innerHTML = "Passwords Match!"
	} else {
		//The passwords do not match.
		//Set the color to the bad color and
		//notify the user.
		//pass2.style.backgroundColor = badColor;
		message.style.color = badColor;
		message.innerHTML = "Passwords Do Not Match!"
	}

}

$(document).ready(function () {
	//scroll to top
	var offset = 100;
	var duration = 500;
	jQuery(window).scroll(function () {
		if (jQuery(this).scrollTop() > offset) {
			jQuery('.custom-scroll-top').fadeIn(duration);
		} else {
			jQuery('.custom-scroll-top').fadeOut(duration);
		}
	});

	$('.custom-scroll-top').on('click', function (event) {
		event.preventDefault();
		$('html, body').animate({
			scrollTop : 0
		}, 'slow');
	});
	//end of scroll to top

	$('#save_changes').on("click", function (e) {
		e.preventDefault();
		$("form#modal_column_form").submit();
	});

	$("#edit_column").tooltip({
		placement : "right",
		title : "Edit columns",
	});
});
//end of add_users.php

//---------------
$(document).ready(function () {
	UserManagementProcess.initialize();
});

//user management module
var UserManagementProcess = {

	initialize : function () {
		// console.log("initialized now!");
		$("#currency-panel").hide();
		//tooltip
		$('body').tooltip({
			selector : '[data-toggle="tooltip"]'
		});

	},

	getCurrencyDetails : function (currency_id) {
        if(!currency_id){
            if($("#currency-panel").is(':visible')){
                $("#currency-panel").hide();
            }else{
                $("#currency-panel").show();
            }

            $('#currencyId').val('');
            $('#currencyCode').val('');
            $('#currencyName').val('');
            $('#currencySymbol').val('');
            return false;
		}

        $("#currency-panel").show();

        $.ajax({
            'url' : base_url + 'user_management/getCurrencyDetails/' + currency_id,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function (data) {
                // console.log(data[0]);
                $('#currencyId').val(data[0].currencyId);
                $('#currencyName').val(data[0].currencyName);
                $('#currencyShortName').val(data[0].currencyShortName);
                $('#currencyCode').val(data[0].currencyCode);
                $('#currencySymbol').val(data[0].currencySymbol);
            }
        }, 'json');
		return false;
	},
};

//show password
function showPassword(id) {
	var check = document.getElementById(id);
	var password = document.getElementById('password');
	var cpassword = document.getElementById('cpassword');

	if (check.checked) {
		password.setAttribute("type", "text");
		cpassword.setAttribute("type", "text");
	} else {
		password.setAttribute("type", "password");
		cpassword.setAttribute("type", "password");
	}
}

//show password
function showSettPassword(id) {
	var check = document.getElementById(id);
	var opassword = document.getElementById('opassword');
	var npassword = document.getElementById('npassword');
	var ncpassword = document.getElementById('ncpassword');

	if (check.checked) {
		opassword.setAttribute("type", "text");
		npassword.setAttribute("type", "text");
		ncpassword.setAttribute("type", "text");
	} else {
		opassword.setAttribute("type", "password");
		npassword.setAttribute("type", "password");
		ncpassword.setAttribute("type", "password");
	}
}

function get_log_pages(segment) {
	var xmlhttp = GetXmlHttpObject();

	if (xmlhttp == null) {
		alert("Browser does not support HTTP Request");
		return;
	}

	url = base_url + "report_management/get_log_pages/" + segment;

	var div = document.getElementById("logList");

	xmlhttp.onreadystatechange = function () {
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

/* API Settings */

function changeAPISettings(type) {
	var xmlhttp = GetXmlHttpObject();

	if (xmlhttp == null) {
		alert("Browser does not support HTTP Request");
		return;
	}

	url = base_url + "user_management/changeAPISettings/" + type;

	var div = document.getElementById("nav_content");

	xmlhttp.onreadystatechange = function () {
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

function editAPISettings(type) {
	var xmlhttp = GetXmlHttpObject();

	if (xmlhttp == null) {
		alert("Browser does not support HTTP Request");
		return;
	}

	url = base_url + "user_management/editAPISettings/" + type;

	var div = document.getElementById("nav_content");

	xmlhttp.onreadystatechange = function () {
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

function saveAPISettings(type) {
	this
}


$(document).ready(function(){

	$('#reset-btn').click(function(){

     $('#duplicate-setting-form input').val(0);

	});
});
/* end of API Settings */
