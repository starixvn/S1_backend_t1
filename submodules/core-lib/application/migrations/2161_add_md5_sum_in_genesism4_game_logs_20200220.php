<?php
if(! defined('BASEPATH')){
    exit('No direct script access allowed');
}

class Migration_add_md5_sum_in_genesism4_game_logs_20200220 extends CI_Migration
{
    private $tableName = 'genesism4_game_logs';

    public function up()
    {
        $fields = [
            'md5_sum' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true
            ]
        ];

        if(! $this->db->field_exists('md5_sum',$this->tableName)){
            $this->dbforge->add_column($this->tableName,$fields);
        }
    }

    public function down()
    {
        if($this->db->field_exists('md5_sum',$this->tableName)){
            $this->dbforge->drop_column($this->tableName,'md5_sum');
        }
    }

}