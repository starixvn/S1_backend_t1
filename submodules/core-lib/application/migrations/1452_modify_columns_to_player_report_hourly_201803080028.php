<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_modify_columns_to_player_report_hourly_201803080028 extends CI_Migration
{
    private $tableName = 'player_report_hourly';

    public function up()
    {
        $fields = array(
            'affiliate_id' => array(
                'name' => 'affiliate_id',
                'type' => 'INT',
                'null' => true,
            ),
            'agent_id' => array(
                'name' => 'agent_id',
                'type' => 'INT',
                'null' => true,
            ),
        );

        $this->dbforge->modify_column($this->tableName, $fields);

        //change name
        if($this->db->field_exists('total_manaual', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'total_manaual');
        }

        if(!$this->db->field_exists('total_manual', $this->tableName)){
            $fields = array(
                'total_manual' => array(
                    'type' => 'DOUBLE',
                    'null' => false,
                    'default' => 0,
                ),
            );
            $this->dbforge->add_column($this->tableName, $fields);
        }

        $this->load->model(['player_model']);
        $this->player_model->addIndex('player_report_hourly', 'idx_date_hour_player_id', 'date_hour,player_id');
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'total_manual');
    }
}
