<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_customer_id_in_booming_seamless_game_logs_20190823 extends CI_Migration {
	private $tableName = 'boomingseamless_game_logs';

    public function up() {

        $update_fields = array(
	        'custome_id' => array(
                'name' => 'customer_id',
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => true,
	        ),
        );

        if($this->db->field_exists('custome_id', $this->tableName)) {
            $this->dbforge->modify_column($this->tableName, $update_fields); 
        }

        if(!$this->db->field_exists('created_at', $this->tableName)){
            $this->dbforge->add_column($this->tableName, [
                'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
               		'null' => false,
            	)
    		]);
        }

        if(!$this->db->field_exists('updated_at', $this->tableName)){
            $this->dbforge->add_column($this->tableName, [
            	'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' => array(
                	'null' => false,
            	)
    		]);
   		}
    }

    public function down() {
        if($this->db->field_exists('created_at', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'created_at');
        }
        if($this->db->field_exists('updated_at', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'updated_at');
        }
    }
}
