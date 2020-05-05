<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_fg_table_201902150200 extends CI_Migration 
{
	const FG_TABLES = ['fg_entaplay_game_logs', 'fg_game_logs'];

    public function up() 
    {
        $fieldsToAdd = array(
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => false
            ),
        );

        $fieldsToUpate = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'trans_id' => array(
                'type' => 'BIGINT',
                'null' => false,
            ),
        );

        foreach (self::FG_TABLES as $table) {
            $this->dbforge->add_column($table, $fieldsToAdd);
       		$this->dbforge->modify_column($table, $fieldsToUpate);

            # create index for common search column
            $this->load->model('original_game_logs_model');
            $this->original_game_logs_model->addIndex($table, 'idx_date_time', 'date_time');
            $this->original_game_logs_model->addIndex($table, 'idx_win_flag', 'win_flag');
            $this->original_game_logs_model->addIndex($table, 'idx_game_tran_id', 'game_tran_id');            
            $this->original_game_logs_model->addIndex($table, 'idx_user_id', 'user_id');
        }
    
    }

    public function down()
    {
    	//
    }

}