<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_merge_affiliate_settings_to_new_201607020006 extends CI_Migration {

	public function up() {
		$this->db->query('create unique index idx_aff_id_option_type on affiliate_terms(affiliateId,optionType)');

		$this->load->model(array('affiliatemodel'));
		$force=true;
		$this->affiliatemodel->startTrans();
		$this->affiliatemodel->mergeAffTermSettings($force);
		if(!$this->affiliatemodel->endTransWithSucc()){
			throw new Exception('update database failed: affiliatemodel->mergeAffTermSettings');
		}
	}

	public function down() {
		$this->db->query('drop index idx_aff_id_option_type on affiliate_terms');
	}
}