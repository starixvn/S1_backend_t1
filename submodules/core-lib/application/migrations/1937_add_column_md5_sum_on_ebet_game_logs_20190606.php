<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_md5_sum_on_ebet_game_logs_20190606 extends CI_Migration {

    private $tableName='ebet_usd_game_logs';
    private $tableName2='ebet_th_game_logs';

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
        if(!$this->db->field_exists('md5_sum', $this->tableName2)){
            $this->dbforge->add_column($this->tableName2, $fields);
        }
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'md5_sum');
        $this->dbforge->drop_column($this->tableName2, 'md5_sum');
    }
}
