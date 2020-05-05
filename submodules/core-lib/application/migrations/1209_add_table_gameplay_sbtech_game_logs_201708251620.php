<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Migration_add_table_gameplay_sbtech_game_logs_201708251620 extends CI_Migration {

        private $tableName = 'gameplay_sbtech_game_logs';

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
                'idx' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'merchId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'memberId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'rowId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'agentId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'customerId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'merchantCustomerId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'betId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'purchaseId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'betTypeId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'betTypeName' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'lineId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'lineTypeId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'lineTypeName' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'rowTypeId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'branchId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'branchName' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'leagueId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'leagueName' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'eventName' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'creationDate' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'homeTeam' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'awayTeam' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'stake' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'odds' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'points' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'score' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'status' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'yourBet' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'isForEvent' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'eventTypeId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'eventTypeName' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'orderId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'updateDate' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'pl' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'teamMappingId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'liveScore1' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'liveScore2' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'eventDate' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'masterEventId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'commonStatusId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'webProviderId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'webProviderName' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'bonusAmount' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'domainId' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'regDate' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'parlayChecked' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => true,
                ),
                'parlays' => array(
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
