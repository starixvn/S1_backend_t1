<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_yungu_game_logs_20170926 extends CI_Migration {

    private $tableName = 'yungu_game_logs';

    /*
     * 数据，参数说明：
     * betId    : 注单 ID
     * user     : 会员帐号
     * gameId   : 游戏 ID
     * phaseNum : 期数，1.5.5 版本中新加
     * gameName : 游戏名称，为减小数据长度，2016-11-01 起在 1.5.5 版本中删除此参数，可在新加参数 gameInfo 中获取
     * money    : 下注金额
     * betType  : 下注内容
     * status   : 注单状态，2：已结算；1：未结算；0：已注销；
     * time     : 下注时间
     * result   : 输赢结果，正数为会员所赢的钱数，负数为会员输的钱数，0 为打和，空字符串为此注单还未结算
     */

    public function up() {
        if(!$this->db->table_exists($this->tableName)){
            $fields = array(
                    'id' => array(
                        'type' => 'INT',
                        'null' => false,
                        'auto_increment' => TRUE,
                        ),
                    'betId' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '100',
                        'null' => true,
                        ),
                    'user' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '100',
                        'null' => true,
                        ),
                    'gameId' => array(
                        'type' => 'VARCHAR',
                        'constraint' => '100',
                        'null' => true,
                        ),
                    'phaseNum' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => true,
                            ),
                    'money' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => true,
                            ),
                    'betType' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => true,
                            ),
                    'status' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => true,
                            ),
                    'time' => array(
                            'type' => 'VARCHAR',
                            'constraint' => '100',
                            'null' => true,
                            ),
                    'result' => array(
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
            $this->db->query( sprintf( 'create unique index %s on %s(%s)', "idx_external_uniqueid", $this->tableName, "external_uniqueid" ) );
        }

    }

    public function down() {
        $this->dbforge->drop_table($this->tableName);
    }
}
