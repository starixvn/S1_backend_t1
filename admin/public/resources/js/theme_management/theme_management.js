// sidebar.php
$(document).ready(function () {
	//tooltip
	$('body').tooltip({
		selector: '[data-toggle="tooltip"]'
	});
		  
	var url = document.location.pathname;
	var res = url.split("/theme_management/");
	for (i = 0; i < res.length; i++) {
		switch (res[i]) {
			case 'index':
					$("a#theme_management").addClass("active");
				break;
			case 'headerIndex':
					$("a#header_template").addClass("active");
				break;
			case 'footerIndex':
					$("a#footer_template").addClass("active");
				break;
			case 'registerIndex':
					$("a#register_template").addClass("active");
				break;
			case 'otherJsIndex':
					$("a#js_template").addClass("active");
				break;
			case 'mobileLoginIndex':
					$("a#mobile_login_template").addClass("active");
				break;
			default:
				break;
		}
	}
});
// end of sidebar.php