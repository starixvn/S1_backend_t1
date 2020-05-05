<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_lebo_game_logs_20170904 extends CI_Migration {

    private $tableName = 'lebo_game_logs';

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
                    'game_code' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '100',
                        'null' => true,
                        ),
                    'key_id' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '100',
                        'null' => true,
                        ),
                    'uno' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '100',
                        'null' => true,
                        ),
                    'period_num' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => true,
                            ),
                    'bet_content' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => true,
                            ),
                    'odds' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => true,
                            ),
                    'bet_amount' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => true,
                            ),
                    'bet_result' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => true,
                            ),
                    'order_time' => array(
                            'type' => 'TIMESTAMP',
                            'null' => true,
                            ),
                    'settlement_flag' => array(
                            'type' => 'INT',
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
            $this->db->query( sprintf( 'create unique index %s on %s(%s)', "idx_external_uniqueid", $this->tableName, "external_uniqueid" ) );
        }

    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
