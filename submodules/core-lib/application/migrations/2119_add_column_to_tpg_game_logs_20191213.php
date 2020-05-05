<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_column_to_tpg_game_logs_20191213 extends CI_Migration {

    private $tableName = 'tpg_game_logs';

    public function up() {

        $fields = array(
            'transaction_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'bet_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'payout_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'round_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'null' => true,
            ),
            'room_rate' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'jackpot_commission' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'jackpot_commission_rate' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'fish_caught' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'fish_url' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('transaction_type', $this->tableName)
            && !$this->db->field_exists('bet_amount', $this->tableName)
            && !$this->db->field_exists('payout_amount', $this->tableName)
            && !$this->db->field_exists('round_id', $this->tableName)
            && !$this->db->field_exists('room_rate', $this->tableName)
            && !$this->db->field_exists('jackpot_commission', $this->tableName)
            && !$this->db->field_exists('jackpot_commission_rate', $this->tableName)
            && !$this->db->field_exists('fish_caught', $this->tableName)
            && !$this->db->field_exists('fish_url', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields, 'total_payout_amount');
        }

    }

    public function down() {
        if($this->db->field_exists('transaction_type', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'transaction_type');
        }
        if($this->db->field_exists('bet_amount', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'bet_amount');
        }
        if($this->db->field_exists('payout_amount', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'payout_amount');
        }
        if($this->db->field_exists('round_id', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'round_id');
        }
        if($this->db->field_exists('room_rate', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'room_rate');
        }
        if($this->db->field_exists('jackpot_commission', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'jackpot_commission');
        }
        if($this->db->field_exists('jackpot_commission_rate', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'jackpot_commission_rate');
        }
        if($this->db->field_exists('fish_caught', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'fish_caught');
        }
        if($this->db->field_exists('fish_url', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'fish_url');
        }
    }
    
}