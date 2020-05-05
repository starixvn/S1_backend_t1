<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_system_features_enable_201701101857 extends CI_Migration {

	public function up() {

		$features = array(
			'notification_withdraw',
			'notification_local_bank',
			'notification_promo'
		);

		$this->db->trans_start();

		foreach ($features as $features) {

			$this->db->where('name', $features)
						->update('system_features', array(
							'enabled' => 1
						));

		}

		$this->db->trans_complete();
	}

	public function down() {
	}
}
