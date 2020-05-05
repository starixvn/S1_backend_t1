<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_gl_game_logs_20180925 extends CI_Migration {

    private $tableName = 'gl_game_logs';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),

            'project_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'user_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'package_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'task_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'lottery_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'method_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'issue' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'bonus' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'winbonus' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'code_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'single_price' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'multiple' => array(
                'type' => 'VARCHAR',
                'constraint' => '5',
            ),
            'total_price' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'write_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'scode' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'update_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'deduct_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'bonus_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'cancel_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'is_deduct' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'is_cancel' => array(
                'type' => 'INT',
            ),
            'is_getprize' => array(
                'type' => 'INT',
            ),
            'prize_status' => array(
                'type' => 'INT',
            ),
            'modes' => array(
                'type' => 'INT',
            ),
            'hashvar' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'user_point' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'is_new' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'comefrom' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'point_status' => array(
                'type' => 'BOOLEAN',
                'null' => false,
                'default' => 0,
            ),
            'third_party_trx_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'platform' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'write_microtime' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'created_at' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'updated_at' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'pointinfo' => array(
                'type' => 'VARCHAR',
                'constraint' => '200'
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'cnname' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'enanme' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'method_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'bingo_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),

            'sale_start' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'sale_end' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'project_display_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'i18n_lottery_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'i18n_method_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'i18n_method_lv1_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'status_flag' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),
            'i18n_status_flag' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
            ),

            // SBE data
            'player_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false,
            ),
            'response_result_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'last_updated_time' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'last_sync_time' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ),
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('external_uniqueid');
        $this->dbforge->create_table($this->tableName);
        $this->player_model->addIndex($this->tableName,'idx_last_updated_time' , 'last_updated_time');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}