<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_ebetqt_game_logs_20170822 extends CI_Migration {

    private $tableName = 'ebetqt_game_logs';

    const FLAG_TRUE = 1;
    const FLAG_FALSE = 0;

    public function up() {
        if(!$this->db->table_exists($this->tableName)){

            $fields = array(
                'id' => array(
                    'type' => 'INT',
                    'null' => false,
                    'auto_increment' => TRUE,
                ),
                'ebet_qt_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                ),
                'gameUniqueId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                ),
                'status' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'totalBet' => array(
                    'type' => 'DOUBLE',
                    'null' => true,
                ),
                'totalPayout' => array(
                    'type' => 'DOUBLE',
                    'null' => true,
                ),
                'currency' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'initiated' => array(
                    'type' => 'TIMESTAMP',
                    'null' => true,
                ),
                'completed' => array(
                    'type' => 'TIMESTAMP',
                    'null' => true,
                ),
                'operatorId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'device' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'gameProvider' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'gameId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'gameCategory' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'gameClientType' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'comment' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'thirdParty' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'tag' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'ebet_qt_playerId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'playerId' => array(
                    'type' => 'INT',
                    'constraint' => '10',
                    'null' => false,
                ),
                'uniqueid' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'external_uniqueid' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'response_result_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
            );

            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);

            $this->dbforge->create_table($this->tableName);

            $this->db->query('create unique index idx_uniqueid on ebetqt_game_logs(uniqueid)');
            $this->db->query('create unique index idx_external_uniqueid on ebetqt_game_logs(external_uniqueid)');

            $data = array(
                array(
                    'game_type' => 'EBET QT UNKNOWN',
                    'game_type_lang' => 'EBET QT UNKNOWN',
                    'status' => self::FLAG_TRUE,
                    'flag_show_in_site' => self::FLAG_FALSE,
                    'game_description_list' => array(
                        array(
                            'game_name' => '_json:{"1":"EBET QT UNKNOWN GAME","2":"EBET QT 未知游戏"}',
                            'english_name' => 'EBET QT UNKNOWN GAME',
                            'external_game_id' => 'unknown',
                            'game_code' => 'unknown'
                        )
                    )
                )
            );

            $game_description_list = array();
            foreach ($data as $game_type) {
                $this->db->insert('game_type', array(
                    'game_platform_id' => EBET_QT_API,
                    'game_type' => $game_type['game_type'],
                    'game_type_lang' => $game_type['game_type_lang'],
                    'status' => $game_type['status'],
                    'flag_show_in_site' => $game_type['flag_show_in_site'],
                ));

                $game_type_id = $this->db->insert_id();
                foreach ($game_type['game_description_list'] as $game_description) {
                    $game_description_list[] = array_merge(array(
                        'game_platform_id' => EBET_QT_API,
                        'game_type_id' => $game_type_id,
                    ), $game_description);
                }
            }

            $this->db->insert_batch('game_description', $game_description_list);

        }

    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
        $this->db->query('drop index idx_uniqueid on ebetqt_game_logs');
        $this->db->query('drop index idx_external_uniqueid on ebetqt_game_logs');

        $this->db->delete('game_type', array('game_platform_id' =>  EBET_QT_API));
        $this->db->delete('game_description', array('game_platform_id' =>  EBET_QT_API));

    }
}
