$(document).ready(function () {

	// sidebar.php
	var url = document.location.pathname;
	var res = url.split("/");
	for (i = 0; i < res.length; i++) {

		switch (res[i]) {

		case 'viewGameApi':
			$("a#viewGameApi").addClass("active");
			break;	

		case 'viewGameApiUpdateHistory':
			$("a#viewGameApiHistory").addClass("active");
			break;	

		case 'viewGameMaintenanceSchedule':
			$("a#viewGameMaintenanceSchedule").addClass("active");
			break;	
		
		default:
			break;
		}
	}
	// end of sidebar.php

});
 