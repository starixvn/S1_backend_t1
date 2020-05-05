<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_affiliate_static_report_20180611 extends CI_Migration {

    private $tableName = 'affiliate_static_report';

    public function up() {

        $fields = array(
            'id' => array(
                'type' => 'int',
                'auto_increment' => TRUE,
                'unsigned' => TRUE,
            ),
            'affiliate_id' => array(
                'type' => 'int',
                'unsigned' => TRUE,
            ),
            'aff_username' => array(
                'type' => 'VARCHAR',
                'constraint' => 100
            ),
            'real_name' => array(
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ),
            'affiliate_level' => array(
                'type' => 'INT',
                'default' => 0,
            ),
            'total_sub_affiliates' => array(
                'type' => 'INT',
                'default' => 0,
            ),
            'total_registered_players' => array(
                'type' => 'INT',
                'default' => 0,
            ),
            'total_deposited_players' => array(
                'type' => 'INT',
                'default' => 0,
            ),
            'total_bet' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_win' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_loss' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'company_win_loss' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'company_income' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_cashback' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_bonus' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_deposit' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'total_withdraw' => array(
                'type' => 'DOUBLE',
                'default' => 0,
            ),
            'report_date' => array(
                'type' => 'DATE',
            ),
            'report_timezone' => array(
                'type' => 'INT',
            ),
            'created_at' => array(
                'type' => 'DATETIME',
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
            )
        );

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('affiliate_id');
        $this->dbforge->add_key('report_date');
        $this->dbforge->create_table($this->tableName);
    }

    public function down() {

        $this->dbforge->drop_table($this->tableName);

    }
}
