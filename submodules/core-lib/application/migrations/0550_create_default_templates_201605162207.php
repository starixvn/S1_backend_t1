<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_default_templates_201605162207 extends CI_Migration {

	public function up() {
		$this->load->model(array('promo_rule_templates'));
		$this->promo_rule_templates->fixDefaultTemplates();
	}

	public function down() {
		$this->db->empty_table('promo_rule_templates');
	}
}