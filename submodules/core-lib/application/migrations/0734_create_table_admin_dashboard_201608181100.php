<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_table_admin_dashboard_201608181100 extends CI_Migration {

	private $tableName = 'admin_dashboard';

	public function up() {

        $fields=array(
            'id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ),
            'today_member_count' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'yesterday_member_count' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'today_deposit_sum' => array(
                'type' => 'INT',
                'constraint' => '200',
                'null' => true,
            ),
            'today_deposited_player' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'today_withdrawal_sum' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'today_withdrawed_player' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'all_member_count' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'all_member_deposited' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'all_member_deposited' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'all_member_balance' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'today_last_deposit_list' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'today_max_deposit_list' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'all_max_deposit_list' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'weekly_deposit_list' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'weekly_withdraw_list' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'weekly_member_list' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'transactions' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'countPlayerSession' => array(
                'type' => 'INT',
                'null' => true,
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        );

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);

		$this->dbforge->create_table($this->tableName);

		//index
		//$this->db->query('create index idx_game_platform_id on bet_limit_template_list(game_platform_id)');
		//$this->db->query('create index idx_agent_id on bet_limit_template_list(agent_id)');

	}

	public function down() {
		$this->dbforge->drop_table($this->tableName);
	}
}

///END OF FILE//////////