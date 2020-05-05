<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_promotion_report_details_20190124 extends CI_Migration {

    private $tableName='promotion_report_details';

    public function up() {

        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'BIGINT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'amount' => array(
                'type' => 'DOUBLE',
                'null' => false,
            ),
            'transaction_type' => array(
                'type' => 'INT',
                'null' => false,
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
            'promo_type' => array(
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ),
            'promo_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ),
            'promo_status' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'promotion_datetime' => array(
                'type' => 'DATETIME',
                'null' => false,
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
