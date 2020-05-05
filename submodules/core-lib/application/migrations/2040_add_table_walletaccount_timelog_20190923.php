<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_walletaccount_timelog_20190923 extends CI_Migration {

	private $tableName = 'walletaccount_timelog';

	public function up() {
		$fields=array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'walletAccountId' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => false,
            ),
            'create_date' => array(
                'type' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
                'null' => false,
            ),
			'create_type' => array(
                'type' => 'TINYINT',
                'null' => false,
            ),
            'created_by' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => true,
            ),
            'before_status' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => true,
            ),
            'after_status' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => true,
            ),
        );

        if(!$this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);
            # Add Index
            $this->load->model('player_model');
            $this->player_model->addIndex('walletaccount_timelog','idx_walletAccountId' , 'walletAccountId');
            $this->player_model->addIndex('walletaccount_timelog','idx_create_type' , 'create_type');
            $this->player_model->addIndex('walletaccount_timelog','idx_created_by' , 'created_by');
            $this->player_model->addIndex('walletaccount_timelog','idx_create_date' , 'create_date');
        }
	}

	public function down() {
        if($this->db->table_exists($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
    }
}
