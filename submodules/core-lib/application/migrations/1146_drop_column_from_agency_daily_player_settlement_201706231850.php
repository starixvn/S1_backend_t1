<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_drop_column_from_agency_daily_player_settlement_201706231850 extends CI_Migration {

	private $tableName = 'agency_daily_player_settlement';

	public function up() {
        $this->db->query('drop index agency_daily_player_settlement_idx on agency_daily_player_settlement');
        $this->dbforge->drop_column($this->tableName, 'settlement_period');
        $this->dbforge->drop_column($this->tableName, 'settlement_date_from');
	}

	public function down() {
        $fields = array(
            'settlement_period' => array(
                'type' => 'VARCHAR',
                'constraint' => '36',
            ),
            'settlement_date_from' => array(
                'type' => 'DATETIME',
            ),
        );
        $this->dbforge->add_column($this->tableName, $fields);
        $this->db->query('ALTER TABLE `agency_daily_player_settlement` ADD UNIQUE INDEX `agency_daily_player_settlement_idx` (`player_id` ASC, `agent_id` ASC, `settlement_period` ASC, `settlement_date_from` ASC)');
	}
}
