<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_on_response_results_20190612 extends CI_Migration {

    private $tableName='response_results';

    public function up()
    {
        # Add column
        $fields = array(
            'request_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            )
        );
        if(!$this->db->field_exists('request_id', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'request_id');
    }
}
