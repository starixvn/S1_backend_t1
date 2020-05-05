<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_rows_to_kyc_status_20170404 extends CI_Migration {

	public function up() {
 		$this->db->query("INSERT INTO `kyc_status` (`rate_code`, `description_english`, `description_chinese`, `description_indonesian`, `description_vietnamese`, `created_at`)
		VALUES
		 ('A', 'Not certified: Player not yet make any deposit or deposit account name is not consistent with the registered actual name.', '未认证：玩家未存款或存款账户姓名与注册姓名不符', 'Not certified: Player not yet make any deposit or deposit account name is not consistent with the registered actual name.', 'Not certified: Player not yet make any deposit or deposit account name is not consistent with the registered actual name.', '2017-04-04 21:00:00'),
		 ('B', 'Low: KYC identity verification (deposit account name and registration name is the same)', '低: 存款账户姓名与注册姓名相符', 'Low: KYC identity verification (deposit account name and registration name is the same)', 'Low: KYC identity verification (deposit account name and registration name is the same)', '2017-04-04 21:00:00'),
		 ('C', 'Medium: provide proof of valid documents', '中：提供个人资料证明', 'Medium: provide proof of valid documents', 'Medium: provide proof of valid documents', '2017-04-04 21:00:00'),
		 ('D', 'High: proof of identity valid certificate + proof of address', '中：提供个人资料证明', 'High: proof of identity valid certificate + proof of address', 'High: proof of identity valid certificate + proof of address', '2017-04-04 21:00:00');
		 ");
	}

	public function down() {
	}
}