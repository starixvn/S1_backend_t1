<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_new_pt_game_description_201511041740 extends CI_Migration {

	private $tableName = 'game_description';
	private $tableName2 = 'mg_game_logs';
	private $games = array('ash3brg','s21','ashcpl','esm5','esm1','esm2','esmk1','esmk4','frtf','fmn1','ghlj1','jpgt1','jpgt2','jpgt3','jpgt6','mj1','photk4','photk5','photk6','qop1','qop2','sc1','sc4','sol1','sol2','tpd2','thtk01','thtk02','thtk03','thtk04','thtk05','thtk06','thtk09','tglalcs','pyrrk1','pyrrk3','pyrrk5','vcstd_3');

	public function up() {
		// foreach ($this->games as $game) {
		// 	$this->db->insert($this->tableName, array(
		// 		'game_code' => $game, 'game_name' => "pt.".$game, 'external_game_id' => $game,
		// 		'game_platform_id' => PT_API, 'game_type_id' => 7,
		// 	));
		// }
		$this->dbforge->add_column($this->tableName2, array(
			'module_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'client_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
			'transaction_id' => array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => true,
			),
		));
	}

	public function down() {
		$codes = $this->games;
		$this->db->where_in('game_code', $codes);
		$this->db->where('game_platform_id', PT_API);
		$this->db->delete($this->tableName);
		$this->dbforge->drop_column($this->tableName2, 'module_id');
		$this->dbforge->drop_column($this->tableName2, 'client_id');
		$this->dbforge->drop_column($this->tableName2, 'transaction_id');
	}
}