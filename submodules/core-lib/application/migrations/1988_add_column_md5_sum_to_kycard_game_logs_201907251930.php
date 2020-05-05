<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_md5_sum_to_kycard_game_logs_201907251930 extends CI_Migration {

    private $tableName = 'kycard_game_logs';

    public function up()
    {
        # Add column
        $fields = array(
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            )
        );
        if(!$this->db->field_exists('md5_sum', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields);
        }
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'md5_sum');
    }
}
