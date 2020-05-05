<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Migration_add_table_vr_game_logs_2017071215000000 extends CI_Migration {

        private $tableName = 'vr_game_logs';

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
                'cost' => array(
                    'type' => 'DOUBLE',
                    'null' => true,
                ),
                'unit' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => true,
                ),
                'lossPrize' => array(
                    'type' => 'DOUBLE',
                    'null' => true,
                ),
                'playerPrize' => array(
                    'type' => 'DOUBLE',
                    'null' => true,
                ),
                'merchantPrize' => array(
                    'type' => 'DOUBLE',
                    'null' => true,
                ),
                'state' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => false,
                ),
                'count' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => true,
                ),
                'multiple' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => true,
                ),
                'channelId' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => true,
                ),
                'subState' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => true,
                ),
                'prizeDetail' => array(
                    'type' => 'TEXT',
                    'null' => true,
                ),
                'updateTime' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '25',
                    'null' => true,
                ),
                'createTime' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '25',
                    'null' => true,
                ),
                'note' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '300',
                    'null' => true,
                ),
                'winningNumber' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'issueNumber' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'betTypeName' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'channelName' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'position' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'number' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'odds' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'playerName' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'serialNumber' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'merchantCode' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
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
