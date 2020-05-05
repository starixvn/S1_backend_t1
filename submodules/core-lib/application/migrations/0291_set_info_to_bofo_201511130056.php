<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_set_info_to_bofo_201511130056 extends CI_Migration {

	private $tableName = 'external_system';

	public function up() {
		$this->db
			->set('sandbox_url', 'https://tgw.baofoo.com/payindex')
			->set('sandbox_account', '100000178')
			->set('sandbox_key', '10000001')
			->set('sandbox_secret', 'abcdefg')
			->set('live_url', 'https://gw.baofoo.com/payindex')
			->where('id', BOFO_PAYMENT_API)->update($this->tableName);
	}

	public function down() {
	}
}

///END OF FILE//////////