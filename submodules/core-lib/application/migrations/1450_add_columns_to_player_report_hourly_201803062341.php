<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Migration_add_columns_to_player_report_hourly_201803062341 extends CI_Migration
{
    private $tableName = 'player_report_hourly';

    public function up()
    {
        $fields = array(
            'first_deposit_amount' => array(
                'type' => 'DOUBLE',
                'default' => 0,
                'null' => true,
            ),
            'first_deposit_datetime' => array(
                'type' => 'DATETIME',
                'default' => 0,
                'null' => true,
            ),
            'second_deposit_amount' => array(
                'type' => 'DOUBLE',
                'default' => 0,
                'null' => true,
            ),
            'second_deposit_datetime' => array(
                'type' => 'DATETIME',
                'default' => 0,
                'null' => true,
            ),
        );

        $this->dbforge->add_column($this->tableName, $fields);

        $this->load->model(['player_model']);
        $this->player_model->addIndex('player_report_hourly', 'idx_player_id', 'player_id');
        $this->player_model->addIndex('player_report_hourly', 'idx_date_hour', 'date_hour');
    }

    public function down()
    {
        $this->dbforge->drop_column($this->tableName, 'first_deposit_amount');
        $this->dbforge->drop_column($this->tableName, 'first_deposit_datetime');
        $this->dbforge->drop_column($this->tableName, 'second_deposit_amount');
        $this->dbforge->drop_column($this->tableName, 'second_deposit_datetime');
    }
}
