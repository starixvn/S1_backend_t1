<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_hb_incomplete_games_201904300218 extends CI_Migration {

    private $tableName = 'hb_incomplete_games';

    public function up() {

        $fields = array(
            'username_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '110',
                'null' => true,
            ),
        );

        if (!$this->db->field_exists('username_key', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_username_key', 'username_key');

    }

    public function down() {
        if ($this->db->field_exists('username_key', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'username_key');
        }
    }

}
