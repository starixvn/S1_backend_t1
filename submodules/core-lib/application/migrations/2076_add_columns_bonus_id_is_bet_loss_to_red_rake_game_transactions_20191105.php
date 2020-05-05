<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_columns_bonus_id_is_bet_loss_to_red_rake_game_transactions_20191105 extends CI_Migration {

    private $tableName = 'red_rake_game_transactions';

    public function up() {

        $fields = array(
            'is_bet_loss' => array(
                'type' => 'TINYINT',
                'null' => true,
            ),
            'bonus_id' => array(
                'type' => 'INT',
                'constraint' => '25',
                'null' => true
            ),
            'is_bonus_win' => array(
                'type' => 'TINYINT',
                'null' => true
            )
        );

        if($this->db->table_exists($this->tableName)){

            if(! $this->db->field_exists('is_bet_loss', $this->tableName) && ! $this->db->field_exists('bonus_id', $this->tableName) && ! $this->db->field_exists('is_bonus_win', $this->tableName)){
                $this->dbforge->add_column($this->tableName, $fields);
            }

        }

    }

    public function down() {

        if($this->db->field_exists('is_bet_loss', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'is_bet_loss');
        }

        if($this->db->field_exists('bonus_id', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'bonus_id');
        }

        if($this->db->field_exists('is_bonus_win', $this->tableName)){
            $this->dbforge->drop_column($this->tableName, 'is_bonus_win');
        }
    }
}