<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_cashback_report_daily_20190124 extends CI_Migration {

    private $tableName='cashback_report_daily';

    public function up() {

        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'BIGINT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'cashback_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'betting_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'original_betting_amount' => array(
                'type' => 'DOUBLE',
                'null' => TRUE,
            ),
            'paid_amount' => array(
                'type' => 'DOUBLE',
                'null' => TRUE,
            ),
            'paid_date' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'paid_flag' => array(
                'type' => 'int',
                'null' => TRUE,
            ),
            'cashback_type' => array(
                'type' => 'int',
                'null' => TRUE,
            ),
            'invited_player_id' => array(
                'type' => 'int',
                'null' => TRUE,
            ),
            'invited_player_username' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ),
            'withdraw_condition_amount' => array(
                'type' => 'DOUBLE',
                'null' => TRUE,
            ),
            'max_bonus' => array(
                'type' => 'DOUBLE',
                'null' => TRUE,
            ),
            'level_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'player_group_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null'=> false
            ),
            'player_group_level_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null'=> false
            ),
            'player_group_and_level' => array(
                'type' => 'VARCHAR',
                'constraint' => '400',
                'null'=> false
            ),
            'player_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'player_username' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ),
            'player_realname' => array(
                'type' => 'VARCHAR',
                'constraint' => 300,
                'null' => false,
            ),
            'game_platform_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'game_type_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'game_description_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'external_game_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null'=> false
            ),
            'game_type_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null'=> false
            ),
            'unique_key' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
        ));
        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table($this->tableName);
        $this->load->model(['player_model']);
        $this->player_model->addIndex($this->tableName, 'idx_unique_key', 'unique_key',true);
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
