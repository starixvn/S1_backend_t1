<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ole777_reward_wagers_20181012 extends CI_Migration {

    private $tableName = 'ole777_reward_wagers';

    public function up() {
        if ($this->db->table_exists($this->tableName)) {
            return;
        }

        $fields = [
            'id'                => [ 'type' => 'BIGINT' , 'auto_increment' => TRUE, 'unsigned' => TRUE ],
            'Date'              => [ 'type' => 'VARCHAR', 'constraint' => '20' ],
            'ProductID'         => [ 'type' => 'INT'    , 'null' => true ],
            'GameTypeID'        => [ 'type' => 'INT'    , 'null' => true ],
            'UserCode'          => [ 'type' => 'VARCHAR', 'constraint' => '30' ],
            'WagerCount'        => [ 'type' => 'DECIMAL', 'constraint' => '19,6', 'null' => true ],
            'BetAmount'         => [ 'type' => 'DECIMAL', 'constraint' => '19,6', 'null' => true ],
            'EffectiveAmount'   => [ 'type' => 'DECIMAL', 'constraint' => '19,6', 'null' => true ],
            'WinLoss'           => [ 'type' => 'DECIMAL', 'constraint' => '19,6', 'null' => true ],
            'sync_datetime'     => [ 'type' => 'datetime' , 'null' => true ],
            'notes'             => [ 'type' => 'TEXT'     , 'null' => true ],
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table($this->tableName);

		$this->db->query("ALTER TABLE {$this->tableName} ADD INDEX date_0 (`Date`)");
		$this->db->query("ALTER TABLE {$this->tableName} ADD INDEX date_ProductID_0 (`Date`, `ProductID`)");
    }

    public function down() {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
    }
}
