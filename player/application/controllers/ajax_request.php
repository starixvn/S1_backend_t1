<?php

require_once dirname(__FILE__) . '/super_controller.php';

class Ajax_request extends Super_controller {

	public function __construct(){
		parent::__construct();

		$this->view_template = $this->utils->getPlayerCenterTemplate();

	}

	public function member_center(){



	}

	public function account_information(){



	}

	public function messages(){



	}

	public function promotions(){



	}

	public function security(){



	}

	public function account_history(){



	}

}

/* End of file ajax_request.php */
/* Location: ./application/controllers/ajax_request.php */