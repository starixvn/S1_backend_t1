<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_affiliate_read_only_account_20180427 extends CI_Migration {

    private $tableName = 'affiliate_read_only_account';

    public function up() {

        if(!$this->db->table_exists($this->tableName)){

            $fields = array(
                'id' => array(
                    'type' => 'INT',
                    'null' => false,
                    'auto_increment' => TRUE,
                ),
    			'affiliate_id' => array(
    				'type' => 'INT',
    				'null' => false,
    			),
                'username' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => false,
                ),
                'password' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => false,
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
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
