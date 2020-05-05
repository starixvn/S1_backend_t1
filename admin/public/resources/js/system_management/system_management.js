/**
 * System_management.js
 * Shared JS routine for various controllers in System_management, including:
 * 		system_settings()
 * 		system_Features()
 * 		game_wallet_settings()
 * 		view_smtp_api_report()
 */
// general
var base_url = "/";
var imgloader = "/resources/images/ajax-loader.gif";

$(document).ready(function sidebar_highlight() {
	var path_chunks = document.location.pathname.split('/');
	var path = path_chunks.slice(2).join('/');


	switch (path) {
		case 'system_features' :
			$('a#viewSystemFeatures').addClass('active');
			break;
		case 'view_smtp_api_report' :
			$('a#view_smtp_report').addClass('active');
			break;
		case 'game_wallet_settings' :
			$('a#game_wallet_settings').addClass('active');
			break;
		// ** System settings group
		case 'system_settings/smart_backend' :
			$('a#system_settings').click();
			$('a#sys_settings_smart_backend').addClass('active');
			break;
		case 'system_settings/player_center' :
			$('a#system_settings').click();
			$('a#sys_settings_player_center').addClass('active');
			break;
		default:
			break;
	} // End switch

}); // End function sidebar_hghlight


