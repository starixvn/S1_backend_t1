<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_ultraplay_game_logs_table_20170629 extends CI_Migration {

	private $tableName = 'ultraplay_game_logs';

	public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'PlayerId' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'IsAccepted' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'Stake' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'TicketID' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'GroupTicketID' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'Odds' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'OddsFormat' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'FormattedOdds' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'IsCombo' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'DeviceType' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'SelectionsDetails' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'Description' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'CashoutAmount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'CashoutOdds' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'FormattedCashoutOdds' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'ActiveStake' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'Payout' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'CommitDate' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'Status' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'SelectionsStatus' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table($this->tableName);

	}

	public function down() {
        $this->dbforge->drop_table($this->tableName);
	}

}
