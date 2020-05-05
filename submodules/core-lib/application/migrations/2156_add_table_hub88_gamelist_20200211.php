<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_table_hub88_gamelist_20200211 extends CI_Migration {

    private $tableName = 'hub88_gamelist';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'game_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'game_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'game_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'game_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'category' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'enabled' => array(
                'type' => 'BOOLEAN',
                'null' => true,
            ),
            'product' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'platforms' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'url_background' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'url_thumb' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'blocked_countries' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            )
        );


        if(!$this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table($this->tableName);
            # Add Index
            $this->load->model('player_model');
            $this->player_model->addUniqueIndex($this->tableName, 'idx_as_external_uniqueid', 'external_uniqueid');

        }
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
