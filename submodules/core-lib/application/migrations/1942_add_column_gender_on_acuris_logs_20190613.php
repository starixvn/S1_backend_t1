<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_gender_on_acuris_logs_20190613 extends CI_Migration {

    private $tableName='acuris_logs';

    public function up()
    {
        # Add column
        $fields = array(
            'gender' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            )
        );
        
        if(!$this->db->field_exists('gender', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'gender');
    }
}
