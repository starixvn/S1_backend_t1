<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_currency_to_gl_game_logs_20181025 extends CI_Migration{
    private $tableName = 'gl_game_logs';

    public function up(){
        if(!$this->db->field_exists('currency', $this->tableName)){
            $this->dbforge->add_column($this->tableName, [
                'currency' => [
                    'type' => 'VARCHAR',
                    'constraint' => '50',
                    'null' => true,
                ],
            ]);
        }
    }

    public function down(){
        if($this->db->field_exists('currency', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'currency');
        }
    }
}
