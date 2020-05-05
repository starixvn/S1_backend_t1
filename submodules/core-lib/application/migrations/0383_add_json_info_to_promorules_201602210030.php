<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_json_info_to_promorules_201602210030 extends CI_Migration {

	private $tableName = 'promorules';

	public function up() {

		$this->dbforge->add_column($this->tableName, array(
			'json_info' => array(
				'type' => 'TEXT',
				'null' => true,
			),
		));

		//write to json info
		$this->load->model(array('promorules'));
		$this->promorules->startTrans();
		$rows = $this->promorules->getAll();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$this->promorules->syncToJsonInfo($row);
			}
		}
		if (!$this->promorules->endTransWithSucc()) {
			throw new Exception('save to json failed');
		}
	}

	public function down() {
		$this->dbforge->drop_column($this->tableName, 'json_info');
	}
}
////END OF FILE///////////