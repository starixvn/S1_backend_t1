<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_game_report_hourly_20190124 extends CI_Migration {

    private $tableName='game_report_hourly';

    public function up() {

        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'BIGINT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'level_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'player_group_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null'=> true
            ),
            'player_group_level_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null'=> true
            ),
            'player_group_and_level' => array(
                'type' => 'VARCHAR',
                'constraint' => '400',
                'null'=> true
            ),
            'player_id' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'player_username' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ),
            'player_realname' => array(
                'type' => 'VARCHAR',
                'constraint' => 300,
                'null' => true,
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
            'betting_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'real_betting_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'result_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'win_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'loss_amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'date_hour' => array(
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
            ),
            'currency_key' => array(
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => false,
            ),
            //<currency_key>-<date_hour>-<player_id>-<game_platform_id>-<game_description_id>
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
        $this->player_model->addIndex($this->tableName, 'idx_player_id', 'player_id');
        $this->player_model->addIndex($this->tableName, 'idx_game_platform_id', 'game_platform_id');
        $this->player_model->addIndex($this->tableName, 'idx_game_type_id', 'game_type_id');
        $this->player_model->addIndex($this->tableName, 'idx_game_description_id', 'game_description_id');
        $this->player_model->addIndex($this->tableName, 'idx_currency_key', 'currency_key');
        $this->player_model->addIndex($this->tableName, 'idx_date_hour', 'date_hour');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
