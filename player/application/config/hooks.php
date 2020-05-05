<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

// require_once APPPATH . "/libraries/vendor/autoload.php";

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
 */

// $hook['pre_controller'] = array(
// 	'class' => 'Session_module',
// 	'function' => 'index',
// 	'filename' => 'session_module.php',
// 	'filepath' => 'hooks',
// 	'params' => [],
// );

$hook['post_controller_constructor'] = array(
	'class' => 'Auth_module',
	'function' => 'index',
	'filename' => 'auth_module.php',
	'filepath' => 'hooks',
	'params' => [],
);

$hook['post_system'] = array(
	'class' => 'Finish_module',
	'function' => 'index',
	'filename' => 'finish_module.php',
	'filepath' => 'hooks',
	'params' => [],
);

// $_CFG = &load_class('Config', 'core');
// if ($_CFG->item('enable_clockwork') && !((php_sapi_name() === 'cli' OR defined('STDIN')))) {

// 	Clockwork\Support\CodeIgniter\Register::registerHooks($hook);

// }

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */