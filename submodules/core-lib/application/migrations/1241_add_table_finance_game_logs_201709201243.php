<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_finance_game_logs_201709201243 extends CI_Migration {

    private $tableName = 'finance_game_logs';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'OrderNo' => array(                 // unique id
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'PlatformAccountId' => array(       // player game name
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'ProCNName' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'ProENName' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'RuleType' => array(            // 玩法，1上下；2趋势；3界点；4范围
                'type' => 'INT',
                'null' => true,
            ),
            'UpDown' => array(              // 看涨看跌，1看涨；2看跌
                'type' => 'INT',
                'null' => true,
            ),
            'Odds' => array(                // 赔率，以分为单位，即188表示1.88
                'type' => 'INT',
                'null' => true,
            ),
            'BetAmount' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'BetTime' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'CurrentPrice' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'ExpirePrice' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'EndTime' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'WinLose' => array(            // 输赢，1赢；2输；4打和
                'type' => 'INT',
                'null' => true,
            ),
            'PayoutAmount' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'OrderNote' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ),
            'IsDouble' => array(            // 是否加倍，1是；2否
                'type' => 'INT',
                'null' => true,
            ),
            'IsDelay' => array(             // 是否延迟，1是；2否
                'type' => 'INT',
                'null' => true,
            ),
            'IsClose' => array(             // 是否提前关闭，1是；2否
                'type' => 'INT',
                'null' => true,
            ),

            // SBE data
            'player_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => false,
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
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

        $this->db->query('create unique index idx_external_uniqueid on finance_game_logs(external_uniqueid)');
    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
        $this->db->query('drop index idx_external_uniqueid on finance_game_logs');
    }
}
