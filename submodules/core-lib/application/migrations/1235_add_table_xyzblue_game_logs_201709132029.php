<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Migration_add_table_xyzblue_game_logs_201709132029 extends CI_Migration {

        private $tableName = 'xyzblue_game_logs';

        public function up() {
            $fields = array(
                'id' => array(
                    'type' => 'INT',
                    'null' => false,
                    'auto_increment' => TRUE,
                ),
                'PlayerId' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => false,
                ),
                'Username' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => false,
                ),
                //game column
                'roundid' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'startdate' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'enddate' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'result' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'userid' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'currency' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'gamecode' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'betid' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'idx' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'val' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'amount' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'winrate' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'winamount' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'registerdate' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                //fix parameter
                'external_uniqueid' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '300',
                    'null' => true,
                ),
                'response_result_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '300',
                    'null' => true,
                )
            );


            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);

            $this->dbforge->create_table($this->tableName);
        }

        public function down() {
            $this->dbforge->drop_table($this->tableName);
        }
    }
