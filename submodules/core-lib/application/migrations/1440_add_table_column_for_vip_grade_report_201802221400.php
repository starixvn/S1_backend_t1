<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_column_for_vip_grade_report_201802221400 extends CI_Migration 
{
    private $tableName = 'vip_grade_report';

    public function up() 
    {
        if ($this->db->table_exists($this->tableName)) {
            return;
        }
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'null' => false,
                'auto_increment' => TRUE,
            ),
            'player_id' => array(
                'type' => 'INT',
                'null' => false,
                'default' => 0,
            ),
            'request_time' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'request_type' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'level_from' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'level_to' => array(
                'type' => 'INT',
                'null' => false,
            ),
            'period_start_time' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'period_end_time' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'pgrm_start_time' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'pgrm_end_time' => array(
                'type' => 'DATETIME',
                'null' => false,
            ),
            'updated_by' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'remark' => array(
                'type' => 'VARCHAR', 
                'constraint' => '100', 
                'null' => true
            ),
            'newvipId' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'vipsettingId' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'vipsettingcashbackruleId' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'vipupgradesettingId' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'vipupgradesettinginfo' => array(
                'type' => 'VARCHAR', 
                'constraint' => '255', 
                'null' => true
            ),
            'vipsettingcashbackruleinfo' => array(
                'type' => 'VARCHAR',
                'constraint' => '255', 
                'null' => true
            ),
            'request_grade' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'status' => array(
                'type' => 'INT',
                'null' => true,
            )
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->tableName);
    }

    public function down() 
    {
        if ($this->db->table_exists($this->tableName)) {
            $this->dbforge->drop_table($this->tableName);
        }
    }
}