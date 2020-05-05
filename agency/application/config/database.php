<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

// $active_group = 'default';
$active_record = TRUE;

$CI = &get_instance();
add_config_to_db($CI, $db);

/* End of file database.php */
/* Location: ./application/config/database.php */
