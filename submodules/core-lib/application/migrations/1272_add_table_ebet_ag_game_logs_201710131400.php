<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ebet_ag_game_logs_201710131400 extends CI_Migration {

    private $tableName = 'ebet_ag_game_logs';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            // AGIN
            'bill_no' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'game_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'flag' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'agent_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'is_from_lost_and_found_folder' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'before_credit' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'platform_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'remark' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'result' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'valid_bet_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'third_party' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'recalculate_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'currency' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'tag' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'device_type' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => true,
            ),
            'net_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'player_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'data_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'table_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'bet_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'round' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'play_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'login_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'bet_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'game_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),

            // added fields for XIN
            'bet_amount_base' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'game_category' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'net_amount_bonus' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'net_amount_base' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'bet_amount_bonus' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'main_bill_no' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'slot_type' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),

            // added field for HUNTER

            /***
             * set original logs to this field to avoid duplicates
             *
             * tradeNo          = bill_no
             * creationTime     = bet_time
             * ip               = login_ip
             */

            'transfer_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'cost' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'room_bet' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'exchange_rate' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'earn' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'scene_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'hunter_id' => array(       // original field (ID)
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'current_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'room_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'jackpot_comm' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
            ),
            'scene_start_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'scene_end_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'previous_amount' => array(
                'type' => 'DOUBLE',
                'null' => true,
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
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);

        $this->db->query('create unique index idx_external_uniqueid on ebet_ag_game_logs(external_uniqueid)');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
        $this->db->query('drop index idx_external_uniqueid on ebet_ag_game_logs');
    }
}
