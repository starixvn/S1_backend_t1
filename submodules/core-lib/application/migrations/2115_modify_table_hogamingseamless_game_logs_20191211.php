<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_modify_table_hogamingseamless_game_logs_20191211 extends CI_Migration
{
    private $tableName = 'hogamingseamless_game_logs';

    public function up()
    {
        $this->load->model("player_model");
        
        $fields = array(
            'uname' => array(
                    'name' => 'bet_start_date',
                    'type' => 'DATETIME',
                    'null' => true
            ),
            'cur' => array(
                    'name' => 'bet_end_date',
                    'type' => 'DATETIME',
                    'null' => true
            ),
            'amt' => array(
                    'name' => 'account_id',
                    'type' => 'VARCHAR',
                    'constraint' => '70',
                    'null' => true
            ),
            'txnid' => array(
                    'name' => 'table_id',
                    'type' => 'VARCHAR',
                    'constraint' => '30',
                    'null' => true
            ),
            'gametypeid' => array(
                    'name' => 'table_name',
                    'type' => 'VARCHAR',
                    'constraint' => '30',
                    'null' => true
            ),
            'txnsubtypeid' => array(
                    'name' => 'game_id',
                    'type' => 'VARCHAR',
                    'constraint' => '30',
                    'null' => true
            ),
            'bAmt' => array(
                    'name' => 'bet_id',
                    'type' => 'VARCHAR',
                    'constraint' => '30',
                    'null' => true
            ),
            'txn_reverse_id' => array(
                    'name' => 'bet_amount',
                    'type' => 'DOUBLE',
                    'null' => true
            ),
            'category' => array(
                    'name' => 'payout',
                    'type' => 'DOUBLE',
                    'null' => true
            ),
            'operator' => array(
                    'name' => 'currency',
                    'type' => 'VARCHAR',
                    'constraint' => '5',
                    'null' => true
            ),
            'gameid' => array(
                    'name' => 'game_type',
                    'type' => 'VARCHAR',
                    'constraint' => '25',
                    'null' => true
            )
        );

        if ($this->db->field_exists('uname', $this->tableName) && $this->db->field_exists('cur', $this->tableName) && $this->db->field_exists('amt', $this->tableName) && $this->db->field_exists('txnid', $this->tableName) && $this->db->field_exists('gametypeid', $this->tableName) && $this->db->field_exists('txnsubtypeid', $this->tableName) && $this->db->field_exists('bAmt', $this->tableName) && $this->db->field_exists('txn_reverse_id', $this->tableName) && $this->db->field_exists('category', $this->tableName) && $this->db->field_exists('operator', $this->tableName) && $this->db->field_exists('gameid', $this->tableName)) {
            $this->dbforge->modify_column($this->tableName, $fields);

            #remove regular index
            $this->player_model->dropIndex($this->tableName, 'idx_hg_txnid');
            $this->player_model->dropIndex($this->tableName, 'idx_hg_txn_reverse_id');
            $this->player_model->dropIndex($this->tableName, 'idx_hg_uname');
            $this->player_model->dropIndex($this->tableName, 'idx_hg_gameid');
            $this->player_model->dropIndex($this->tableName, 'idx_hg_category');
            $this->player_model->dropIndex($this->tableName, 'idx_hg_operator');
        }

        $fields = array(
            'bet_spot' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
                'null' => true
            ),
            'bet_no' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
                'null' => true
            ),
            'bet_mode' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true
            )
        );
        
        if (!$this->db->field_exists('bet_spot', $this->tableName) && !$this->db->field_exists('bet_no', $this->tableName) && !$this->db->field_exists('bet_mode', $this->tableName) && !$this->db->field_exists('status', $this->tableName)) {
            $this->dbforge->add_column($this->tableName, $fields);

            #add regular index
            $this->player_model->addIndex($this->tableName, 'idx_hg_table_id', 'table_id');
            $this->player_model->addIndex($this->tableName, 'idx_hg_table_name', 'table_name');
            $this->player_model->addIndex($this->tableName, 'idx_hg_game_id', 'game_id');
            $this->player_model->addIndex($this->tableName, 'idx_hg_game_type', 'game_type');
            $this->player_model->addIndex($this->tableName, 'idx_hg_account_id', 'account_id');
        }
    }

    public function down()
    {
    }
}
