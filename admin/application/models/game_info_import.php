<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

require_once dirname(__FILE__) . '/modules/onesgame_list.php';

/**
 * import
 */
class Game_info_import extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	use onesgame_list;

}

////END OF FILE//////////////