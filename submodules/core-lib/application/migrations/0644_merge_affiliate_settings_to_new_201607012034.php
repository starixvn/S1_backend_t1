<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_merge_affiliate_settings_to_new_201607012034 extends CI_Migration {

	public function up() {
		$this->load->model(array('affiliatemodel'));
		$force=true;
		$this->affiliatemodel->startTrans();
		$this->affiliatemodel->mergeSettings($force);
		if(!$this->affiliatemodel->endTransWithSucc()){
			throw new Exception('update database failed: affiliatemodel->mergeSettings');
		}
	}

	public function down() {
	}
}