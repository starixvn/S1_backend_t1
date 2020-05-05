<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_generate_tracking_code_on_agent_201709241448 extends CI_Migration {

	public function up() {

		$this->load->model(['agency_model']);

		if(method_exists($this->agency_model, 'generate_empty_tracking_code')){

			$this->agency_model->generate_empty_tracking_code();

		}

	}

	public function down() {

	}
}
