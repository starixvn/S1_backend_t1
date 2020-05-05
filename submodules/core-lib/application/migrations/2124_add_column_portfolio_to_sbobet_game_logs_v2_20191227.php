<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_portfolio_to_sbobet_game_logs_v2_20191227 extends CI_Migration {

    private $tableName = 'sbobet_game_logs_v2';

    public function up() {

        $fields = array(
            'portfolio' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('portfolio', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields, 'product_type');
        }

    }

    public function down() {
        if($this->db->field_exists('portfolio', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'portfolio');
        }
    }

}