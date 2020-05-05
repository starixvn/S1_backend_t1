<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_new_player_tutorial_201705121655 extends CI_Migration {

	public function up() {
		$fields = array(
			'description_english' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
			),
			'description_chinese' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
			),
			'description_default_lang' => array(
				'type' => 'INT',
				'constraint' => '2',
			),
		);

		$this->dbforge->add_column('new_player_tutorial', $fields);

		$datas = [
			"1" =>array(
				"id" => "1",
				'description_english' => "This section will have your Profile Picture, the last date and time you logged in and a percentage bar to show progress of Acount information completion",
				"description_chinese" => "这边显示您的个人照片,最近登入时间,以及您个人信息填写的完整度百分比",
				"description_default_lang" => "1",
			),
			"2" =>array(
				"id" => "2",
				'description_english' => "This section shows you how many Available points you have and what are your Total Points. It also shows you your Rebate Balance.",
				"description_chinese" => "这边显示您有多少可用积分以及总分数。 同时也显示您的折扣余额。",
				"description_default_lang" => "1",
			),
			"3" =>array(
				"id" => "3",
				'description_english' => "This section let's you know many percent you need to get to the next VIP Level. For example, how much more Deposit you need to make.",
				"description_chinese" => "这边显示您升级VIP所需的各项指标,例如：再存款多少便能升级",
				"description_default_lang" => "1",
			),
			"4" =>array(
				"id" => "4",
				'description_english' => "This section is where you can Deposit, Withdraw and Transfer funds from your Main Wallet to your Game Wallets. You can also transfer all the money in your Game Wallet back to you Main wallet in just one click.",
				"description_chinese" => "这边显示您可以从您的主钱包存款，提取和转账资金到游戏钱包的地方。 您也可以将游戏钱包中的所有款项一键转回主钱包。",
				"description_default_lang" => "1",
			),
			
		]; 

		$this->db->update_batch('new_player_tutorial', $datas, 'id');
	}

	public function down() {
		$this->dbforge->drop_column('new_player_tutorial', 'description_english');
        $this->dbforge->drop_column('new_player_tutorial', 'description_chinese');
        $this->dbforge->drop_column('new_player_tutorial', 'description_default_lang');
	}
}