<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

//load common default first
require_once dirname(__FILE__) . '/../../../config_default_common.php';

//load default first
require_once dirname(__FILE__) . '/config_default.php';

if (file_exists(dirname(__FILE__) . '/../../../secret_keys/config_secret_local.php')) {
	require_once dirname(__FILE__) . '/../../../secret_keys/config_secret_local.php';
}

if (file_exists(dirname(__FILE__) . '/config_local.php')) {
	require_once dirname(__FILE__) . '/config_local.php';
}

/* End of file config.php */
/* Location: ./application/config/config.php */