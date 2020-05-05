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
//$route['www.lg.com/(:any)'] = "online/casino";
$route['default_controller'] = "player_center/home";
$route['404_override'] = 'player_center/show_404';

$route['iframe_module/(:any).js'] = 'iframe_module/$1_js';
$route['player_center/(:any).js'] = 'player_center/$1_js';
$route['pub/(:any)/(:any).js'] = 'pub/$2_js/$1';
$route['player_center2/pub/(:any).js'] = 'player_center2/pub/$1_js';
$route['pub/player_main/(:any)/(:num)'] = 'pub/player_main_js/$1/true';
$route['player/upload/(:any)'] = 'player_internal/player/$1';
// $route['iframe_module/(:any)'] = 'iframe_module/$1';

// player registration tracking route
// $route['aff/(:any)'] = 'iframe_module/iframe_register/$1';

// template routes
// $route['ttt/(:any)'] = 'iframe/$1';

$route['(:any)/(:any)/(:any)/refresh_session.gif'] = 'pub/refresh_session/$1/$2/$3';

// $route['__clockwork/(.*)'] = 'clockwork_controller/$1';


#WEBET ENGLISH
$route['player_center/cashier'] = 'player_center/dashboard';
$route['player_center/my-promotions'] = 'player_center/iframe_promos';
$route['player_center/deposit-online'] = 'player_center/iframe_makeDeposit';
$route['player_center/transfer'] = 'player_center/dashboard';
$route['player_center/messages'] = 'player_center2/messages';

$route['player-center/cashier'] = 'player_center/dashboard';
$route['player-center/my-promotions'] = 'player_center/iframe_promos';
$route['player-center/deposit-online'] = 'player_center/iframe_makeDeposit';
$route['player-center/transfer'] = 'player_center/dashboard';
$route['player-center/messages'] = 'player_center2/messages';
$route['player-center/profile'] = 'player_center/profile';
$route['player-center/transfer'] = 'player_center/money_transfer';
$route['player-center/promotion/(:any)'] = 'player_center/show_promo_from_site/$1';
$route['rwb_service_api/(:any)'] = 'rwb_service_api/transaction/$1';

// #WEBET CHINESE
// $route['player_center/zh/cashier'] = 'player_center/dashboard';
// $route['player_center/zh/profile'] = 'player_center/profile';
// $route['player_center/zh/my-promotions'] = 'player_center/iframe_promos';
// $route['player_center/zh/deposit-online'] = 'player_center/iframe_makeDeposit';
// $route['player_center/zh/transfer'] = 'player_center/dashboard';
// $route['player_center/zh/withdraw'] = 'player_center/withdraw';

// $route['player_center/zh/transactions'] = 'player_center/dashboard';
// $route['player_center/zh/dashboard'] = 'player_center/dashboard';
// $route['player_center/zh/messages'] = 'player_center2/messages';
// $route['player_center/zh/money_transfer'] = 'player_center/money_transfer';
// $route['player_center/zh/iframe_changePassword'] = 'player_center/iframe_changePassword';


$route['aff/(:any)'] = 'pub/aff/$1';
$route['ag/(:any)'] = 'pub/ag/$1';
$route['api/yongji/(:any)'] = 'customer_api/yongji/$1';
$route['api/fcm/(:any)'] = 'customer_api/fcm/$1';
$route['api/youhu/(:any)'] = 'customer_api/youhu/$1';
$route['api/lanhai/(:any)'] = 'customer_api/lanhai/$1';
$route['api/tailai/(:any)'] = 'customer_api/tailai/$1';
$route['api/testground/(:any)'] = 'customer_api/testground/$1';
$route['api/lequ/(:any)'] = 'customer_api/lequ/$1';
$route['api/player_center/(:any)'] = 'customer_api/player_center/$1';
$route['api/aff/(:any)'] = 'customer_api/aff/$1';
$route['api/ole777/(:any)'] = 'customer_api/ole777/$1';
$route['api/b9b/(:any)'] = 'customer_api/b9b/$1';
$route['api/laba360/(:any)'] = 'customer_api/laba360/$1';

$route['elg_service_api/Users/(:any)/Tokens/(:any)/KeepAlive'] = 'extremelivegaming_service_api/prolongToken/$1/$2';
$route['elg_service_api/Cash/Users/(:any)/Transactions'] = 'extremelivegaming_service_api/transaction/$1';
$route['elg_service_api/Cash/Users/(:any)'] = 'extremelivegaming_service_api/getUserInfo/$1';
$route['ls_service_api/apikey/(:any)'] = 'ls_casino_service_api/index/$1';
$route['Xpress/(:any)'] = 'golden_race_service_api/$1';
$route['habanero_service_api/(:any)'] = 'habanero_service_api/$1';

$route['player_center/goto_sagaming_logout'] = 'player_center/goto_sagaming_logout';


/* End of file routes.php */
/* Location: ./application/config/routes.php */
