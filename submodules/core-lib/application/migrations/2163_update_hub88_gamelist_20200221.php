<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_hub88_gamelist_20200221 extends CI_Migration {
    
	private $tableName = 'hub88_gamelist';

    public function up() {

        $fields = array(
            'Provider' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            )
        );

        if($this->db->field_exists('Provider', $this->tableName)){
            $this->dbforge->modify_column($this->tableName, $fields);
        }
    }

    public function down() {
    
    }
}
