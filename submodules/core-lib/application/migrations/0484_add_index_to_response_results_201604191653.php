<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_index_to_response_results_201604191653 extends CI_Migration {

	public function up() {
		$this->db->query('create index idx_request_api on response_results(request_api)');
	}

	public function down() {
		$this->db->query('drop index idx_request_api on response_results');
	}
}

///END OF FILE//////////