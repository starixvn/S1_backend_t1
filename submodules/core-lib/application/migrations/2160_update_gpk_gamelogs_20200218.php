<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_gpk_gamelogs_20200218 extends CI_Migration {
    
	private $tableName = 'gpk_gamelogs';

    public function up() {

        $fields = array(
            'WagersId' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            )
        );

        if($this->db->field_exists('WagersId', $this->tableName)){
            $this->dbforge->modify_column($this->tableName, $fields);
        }
    }

    public function down() {
    
    }
}
