<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_fish_columns_to_tpg_game_logs_20200106 extends CI_Migration {

    private $tableName = 'tpg_game_logs';

    public function up() {

        $fields = array(
            'bet_line_count' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'booster_price' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'minigame_won' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
            ),
            'winning_lines' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'match_count' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),

            'sum_deduct_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'sum_payout_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'sum_jackpot_contribute' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'sum_jackpot_won' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'total_transaction' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
        );

        if(!$this->db->field_exists('bet_line_count', $this->tableName) &&
        !$this->db->field_exists('booster_price', $this->tableName) &&
        !$this->db->field_exists('minigame_won', $this->tableName) &&
        !$this->db->field_exists('winning_lines', $this->tableName) &&
        !$this->db->field_exists('match_count', $this->tableName) &&
        !$this->db->field_exists('sum_deduct_amount', $this->tableName) &&
        !$this->db->field_exists('sum_payout_amount', $this->tableName) &&
        !$this->db->field_exists('sum_jackpot_contribute', $this->tableName) &&
        !$this->db->field_exists('sum_jackpot_won', $this->tableName) &&
        !$this->db->field_exists('total_transaction', $this->tableName)){
            $this->dbforge->add_column($this->tableName, $fields, 'transaction_type');
        }

    }

    public function down() {
        if($this->db->field_exists('bet_line_count', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'bet_line_count');
        }
        if($this->db->field_exists('booster_price', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'booster_price');
        }
        if($this->db->field_exists('winning_lines', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'winning_lines');
        }
        if($this->db->field_exists('minigame_won', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'minigame_won');
        }
        if($this->db->field_exists('match_count', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'match_count');
        }
        if($this->db->field_exists('sum_deduct_amount', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'sum_deduct_amount');
        }
        if($this->db->field_exists('sum_payout_amount', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'sum_payout_amount');
        }
        if($this->db->field_exists('sum_jackpot_contribute', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'sum_jackpot_contribute');
        }
        if($this->db->field_exists('sum_jackpot_won', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'sum_jackpot_won');
        }
        if($this->db->field_exists('total_transaction', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'total_transaction');
        }
    }

}