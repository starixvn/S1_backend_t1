<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_gd_seamless_wallet_transactions_20191203 extends CI_Migration {

    private $tableName = 'gd_seamless_wallet_transactions';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'BIGINT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'user_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'game_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'game_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true,
            ),
            'transaction_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'amount' => array(
                'type' => 'double',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => true,
            ),
            'game_view' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => true,
            ),
            'client_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => true,
            ),
            'valid_betAmount' => array(
                'type' => 'double',
                'null' => true,
            ),
            'bet_info' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'login_token' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true,
            ),
            'tip_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'anchor_id' => array(
                'type' => 'INT',
                'null' => true,
            ),
            "date_time" => [
                "type" => "DATETIME",
                "null" => true
            ],

            # SBE additional info
            'before_balance' => array(
                'type' => 'double',
                'null' => true,
            ),
            'after_balance' => array(
                'type' => 'double',
                'null' => true,
            ),
            'response_result_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'external_uniqueid' => array(
                'type' => 'VARCHAR',
                'constraint' => '150',
                'null' => true,
            ),
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP' => array(
                'null' => false,
            ),
            'md5_sum' => array(
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            )
        );

        if(!$this->db->table_exists($this->tableName)){
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key("id",true);
            $this->dbforge->create_table($this->tableName);

            # add index
            $indexPreStr = 'idx_';
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'date_time', 'date_time');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'transaction_id', 'transaction_id');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'game_id', 'game_id');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'user_id', 'user_id');
            $this->player_model->addUniqueIndex($this->tableName, $indexPreStr. 'external_uniqueid', 'external_uniqueid');
            $this->player_model->addIndex($this->tableName, $indexPreStr. 'created_at', 'created_at');
        }
    }

    public function down() {
        if($this->db->table_exists($this->tableName)){
            $this->dbforge->drop_table($this->tableName);
        }
    }
}
