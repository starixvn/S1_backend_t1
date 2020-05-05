<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_extreme_live_gaming_201710231442 extends CI_Migration {

    private $tableName = 'extreme_live_gaming_game_logs';

    public function up() {
        $fields = array(
            'id' => array(
                'type' => 'int',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'userId' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'nrgsUserToken' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'transactionType' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'transactionReferenceId' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'transactionReferenceCreationDate' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'transactionId' => array(
                'type' => 'int',
                'unsigned' => TRUE,
                'null' => true,
            ),
            'transactionCreationDate' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'userIpAddress' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'realMoney' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'realRake' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'bonusMoney' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'bonusRake' => array(
                'type' => 'DOUBLE',
                'null' => true,
            ),
            'currencyCode' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'entityReferences' => array(
                'type' => 'TEXT',
            ),
            'game' => array(
                'type' => 'TEXT',
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ),
            'playerId' => array(
                'type' => 'int',
                'unsigned' => TRUE,
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
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}