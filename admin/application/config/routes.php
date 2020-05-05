<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
 */

$route['default_controller'] = "auth";
$route['404_override'] = '';

$route['pub/(:any)/(:any).js'] = 'pub/$2_js/$1';
$route['player/upload/(:any)'] = 'player_internal/player/$1';

$route['(:any)/(:any)/(:any)/refresh_session.gif'] = 'async/refresh_session/$1/$2/$3';

$route['validate.aspx'] = 'callback/game/10/ValidateMember';

// $route['__clockwork/(.*)'] = 'clockwork_controller/$1';

$route['player_management/usernames.csv'] = 'player_management/exportUsernames';
$route['ultraplay_service_api/(:any)'] = 'ultraplay_service_api/index/$1';

$route['elg_service_api/Users/(:any)/Tokens/(:any)/KeepAlive'] = 'extremelivegaming_service_api/prolongToken/$1/$2';
$route['elg_service_api/Cash/Users/(:any)/Transactions'] = 'extremelivegaming_service_api/transaction/$1';
$route['elg_service_api/Cash/Users/(:any)'] = 'extremelivegaming_service_api/getUserInfo/$1';
$route['rwb_service_api/(:any)'] = 'rwb_service_api/transaction/$1';
$route['ls_service_api/apikey/(:any)'] = 'ls_casino_service_api/index/$1';
$route['redtiger_service_api/(:any)'] = 'redtiger_service_api/index/$1';
$route['redtiger_service_api/(:any)/(:any)'] = 'redtiger_service_api/index/$1/$2';
$route['Xpress/(:any)'] = 'golden_race_service_api/$1';
$route['red_rake_service_api/'] = 'red_rake_service_api/index';
$route['habanero_service_api/(:any)'] = 'habanero_service_api/$1';
$route["evolution_service_api/(:any)"] = "evolution_seamless_service_api/index/$1";
$route['slot_factory_service_api/cmwallet'] = 'slot_factory_service_api/index';


$route['dg_service_api/user/(:any)/(:any)'] = 'dg_service_api/user/$1/$2';
$route['dg_service_api/account/(:any)/(:any)'] = 'dg_service_api/account/$1/$2';

$route['pragmatic_play_service_api/(:any).html'] = 'pragmaticplay_service_api/$1';


$route['remote_logs/(:any)'] = 'player_internal/remote_logs/$1';
/* End of file routes.php */
/* Location: ./application/config/routes.php */
