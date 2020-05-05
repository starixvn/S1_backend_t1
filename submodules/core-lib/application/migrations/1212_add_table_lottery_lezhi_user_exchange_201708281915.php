<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Migration_add_table_lottery_lezhi_user_exchange_201708281915 extends CI_Migration {

        private $tableName = 'lottery_lezhi_user_exchange';

        public function up() {
            $fields = array(
                'id' => array(
                    'type' => 'INT',
                    'null' => false,
                    'auto_increment' => TRUE,
                ),
                'player_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => false,
                ),
                'extcredits_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => false,
                ),
                'num' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'null' => false,
                ),
                'created_at' => array(
                    'type' => 'DATETIME',
                    'null' => true,
                ),
                'updated_at' => array(
                    'type' => 'DATETIME',
                    'null' => true,
                )
            );


            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('id', TRUE);

            $this->dbforge->create_table($this->tableName);
            $this->db->query('create index idx_player_id on '.$this->tableName.'(player_id)');
        }

        public function down() {
            $this->dbforge->drop_table($this->tableName);
        }
    }
