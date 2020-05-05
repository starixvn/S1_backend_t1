<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_table_column_for_new_vip_201712141434 extends CI_Migration {
	public function up() {
		//add column vipLevelExp to vipsettingcashbackrule 
		if (!$this->db->field_exists('vipLevelExp', 'vipsettingcashbackrule')) {
			$field = Array(
				'vipLevelExp' => array(
					'type' => 'INT',
					'null' => false,
					'default' => 0,
				)
			);
			$this->dbforge->add_column('vipsettingcashbackrule', $field);
		}
		//add column vipExp to player
		if (!$this->db->field_exists('vipExp', 'player')) {
			$field = Array(
				'vipExp' => array(
					'type' => 'DOUBLE',
					'null' => false,
					'default' => 0,
				)
			);
			$this->dbforge->add_column('player', $field);
		}
		//add column vipMechanics to player
		if (!$this->db->field_exists('vipMechanics', 'player')) {
			$field = Array(
				'vipMechanics' => array(
					'type' => 'INT',
					'null' => false,
					'default' => 0,
				)			
			);
			$this->dbforge->add_column('player', $field);
		}
		//add column depositSuccesionFrom to promorules
		if (!$this->db->field_exists('depositSuccesionFrom', 'promorules')) {
			$field = Array(
				'depositSuccesionFrom' => array(
					'type' => 'INT',
					'null' => false,
					'default' => 0,
				)
			);
			$this->dbforge->add_column('promorules', $field);
		}
		//add column depositSuccesionTo to promorules
		if (!$this->db->field_exists('depositSuccesionTo', 'promorules')) {
			$field = Array(
				'depositSuccesionTo' => array(
					'type' => 'INT',
					'null' => false,
					'default' => 0,
				)
			);
			$this->dbforge->add_column('promorules', $field);
		}

		//add table vipsettingrule
		if (!$this->db->table_exists('vipsettingrule')) {
			$fields = Array(
	            'id' => array(
	                'type' => 'INT',
	                'null' => false,
	                'auto_increment' => TRUE,
	            ),
	            'vipBadge' => array(
	                'type' => 'VARCHAR',
	                'constraint' => '200',
	                'null' => TRUE
	            ),
	            'VipRebateRuleTemplate_Id' => array(
	                'type' => 'INT',
	                'null' => TRUE
	            ),
	            'vipsettingcashbackruleId' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'PointSetting' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'PointSettingRule' => array(
	            	'type' => 'TEXT',
	            	'null' => true,
	            ),
	            'VipLevelBonus' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'VipLevelBonusRule' => array(
	            	'type' => 'TEXT',
	            	'null' => true,
	            ),
	            'VipDepositBonus' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'VipDepositBonusRule' => array(
	            	'type' => 'TEXT',
	            	'null' => true,
	            ),
	            'BirthdayBonus' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'BirthdayBonusRule' => array(
	            	'type' => 'TEXT',
	            	'null' => true,
	            ),
	            'VipRebateBonus' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'VipRebateBonusRule' => array(
	            	'type' => 'TEXT',
	            	'null' => true,
	            ),
	            'ReturnRebatPeriod' => array(
	            	'type' => 'TEXT',
	            	'null' => true,
	            ),
	            'UpgradeSettingId' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'UpgradeSettingDescription' => array(
	            	'type' => 'varchar',
	                'constraint' => '200',
    	            'null' => true,
	            ),
	            'DowngradeSettingId' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'DowngradeSettingDescription' => array(
	            	'type' => 'varchar',
	                'constraint' => '200',
    	            'null' => true,
	            )
			);
	        $this->dbforge->add_field($fields);
	        $this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table('vipsettingrule');
		}
		//add table vipupgradesetting
		if (!$this->db->table_exists('vipupgradesetting')) {
			$fields = Array(
	            'id' => array(
	                'type' => 'INT',
	                'null' => false,
	                'auto_increment' => TRUE,
	            ),
	            'name' => array(
	            	'type' => 'VARCHAR',
	            	'constraint' => 200,
	            	'null' => false,
	            ),
	            'description' => array(
	            	'type' => 'TEXT',
	            	'null' => true,
	            ),
	            'BetAmount' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'BetAmountPercent' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'DepositAmount' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'DepositAmountPercent' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'PlayerNetWinLossAmount' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'PlayerNetWinLossAmountPercent' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'DailyPlayerBet' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'DailyPlayerBetValue' => array(
	            	'type' => 'TEXT',
	            	'null' => false,
	            	'default' => ''
	            ),
	            'DailyPlayerLogin' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'DailyPlayerLoginValue' => array(
	            	'type' => 'TEXT',
	            	'null' => false,
	            	'default' => ''
	            ),
	            'upgrade_method' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 1,
	            ),
	            'upgrade_period' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 1,
	            ),
	            'upgrade_period_daily_from' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'upgrade_period_daily_to' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'upgrade_period_daily_at' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'upgrade_period_weekly_from' => array(
	            	'type' => 'VARCHAR',
	            	'constraint' => 20,
	            	'null' => true,
	            ),
	            'upgrade_period_weekly_to' => array(
	            	'type' => 'VARCHAR',
	            	'constraint' => 20,
	            	'null' => true,
	            ),
	            'upgrade_period_weekly_time_from' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'upgrade_period_weekly_time_to' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'upgrade_period_weekly_at' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'status' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 1
	            ),
	            'created_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'created_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'updated_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'updated_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
			);
	        $this->dbforge->add_field($fields);
	        $this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table('vipupgradesetting');
		}
		//add table vipupgradelock
		if (!$this->db->table_exists('vipupgradelock')) {
			$fields = array(
	            'id' => array(
	                'type' => 'INT',
	                'null' => false,
	                'auto_increment' => TRUE,
	            ),
	            'upgradesetting_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'upgrade_lock_date' => array(
	            	'type' => 'DATETIME',
	            	'null' => false,
	            ),
	            'upgrade_lock_player_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'created_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'created_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'updated_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'updated_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
			);
	        $this->dbforge->add_field($fields);
	        $this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table('vipupgradelock');
		}
		//add table vipdowngradesetting
		if (!$this->db->table_exists('vipdowngradesetting')) {
			$fields = array(
	            'id' => array(
	                'type' => 'INT',
	                'null' => false,
	                'auto_increment' => TRUE,
	            ),
	            'name' => array(
	            	'type' => 'VARCHAR',
	            	'constraint' => 200,
	            	'null' => false,
	            ),
	            'description' => array(
	            	'type' => 'TEXT',
	            	'null' => true,
	            ),
	            'BetAmount' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'BetAmountLessEqual' => array(
	            	'type' => 'DOUBLE',
	            	'null' => true,
	            	'default' => 0.00,
	            ),
	            'BetAmountMinus' => array(
	            	'type' => 'DOUBLE',
	            	'null' => true,
	            	'default' => 0.00,
	            ),
	            'DepositAmount' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'DepositAmountLessEqual' => array(
	            	'type' => 'DOUBLE',
	            	'null' => true,
	            	'default' => 0.00,
	            ),
	            'DepositAmountMinus' => array(
	            	'type' => 'DOUBLE',
	            	'null' => true,
	            	'default' => 0.00,
	            ),
	            'NotLogin' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'NotLoginMinus' => array(
	            	'type' => 'DOUBLE',
	            	'null' => true,
	            	'default' => 0.00,
	            ),
	            'QualifyingPeriod' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'QualifyingPeriod_daily_start_time' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'QualifyingPeriod_daily_end_time' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'QualifyingPeriod_weekly_start_day' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'QualifyingPeriod_weekly_start_time' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'QualifyingPeriod_weekly_end_day' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'QualifyingPeriod_weekly_end_time' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'QualifyingPeriod_monthly_start_time' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'QualifyingPeriod_monthly_end_time' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'DowngradePeriod' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'DowngradePeriod_hourly_downgrade_at' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'DowngradePeriod_daily_downgrade_at' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'DowngradePeriod_weekly_downgrade_day' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'DowngradePeriod_weekly_downgrade_time' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'DowngradePeriod_monthly_downgrade_date' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'DowngradePeriod_monthly_downgrade_time' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'GracePeriod' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
            	'GracePeriod_days' => array(
            		'type' => 'INT',
            		'null' => true,
	            ),
	            'status' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 1
	            ),
	            'created_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'created_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'updated_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'updated_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
			);
	        $this->dbforge->add_field($fields);
	        $this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table('vipdowngradesetting');
		}
		//add table vipdowngradegraceperiodlock
		if (!$this->db->table_exists('vipdowngradegraceperiodlock')) {
			$fields = array(
	            'id' => array(
	                'type' => 'INT',
	                'null' => false,
	                'auto_increment' => TRUE,
	            ),
	            'downgradesetting_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'downgrade_grace_period_lock_date' => array(
	            	'type' => 'DATETIME',
	            	'null' => false,
	            ),
	            'downgrade_grace_period_player_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'created_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'created_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'updated_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'updated_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
			);
	        $this->dbforge->add_field($fields);
	        $this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table('vipdowngradegraceperiodlock');
		}
		//add table vipdowngradelock
		if (!$this->db->table_exists('vipdowngradelock')) {
			$fields = array(
	            'id' => array(
	                'type' => 'INT',
	                'null' => false,
	                'auto_increment' => TRUE,
	            ),
	            'downgradesetting_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'downgrade_lock_date' => array(
	            	'type' => 'DATETIME',
	            	'null' => false,
	            ),
	            'downgrade_player_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'created_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'created_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'updated_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'updated_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
			);
	        $this->dbforge->add_field($fields);
	        $this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table('vipdowngradelock');
		}
		//add table viprebateruletemplate
		if (!$this->db->table_exists('viprebateruletemplate')) {
			$fields = Array(
	            'id' => array(
	                'type' => 'INT',
	                'null' => false,
	                'auto_increment' => TRUE,
	            ),
	            'vip_rebate_name' => array(
	            	'type' => 'VARCHAR',
	            	'constraint' => 50,
	            	'null' => false,
	            ),
	            'status' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'VipRebateBonus' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'max_daily_rebate_bonus_amount_all_player' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'max_daily_rebate_bonus_amount_per_player' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'max_weekly_rebate_bonus_amount_all_player' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'max_weekly_rebate_bonus_amount_per_player' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'max_monthly_rebate_bonus_amount_all_player' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'max_monthly_rebate_bonus_amount_per_player' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'rebate_approval_method' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'rebate_approval_period' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'rebate_approval_period_daily_from' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'rebate_approval_period_daily_to' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'rebate_approval_period_daily_release_at' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'rebate_approval_period_weekly_from' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'rebate_approval_period_weekly_time_from' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'rebate_approval_period_weekly_to' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'rebate_approval_period_weekly_time_to' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
	            'rebate_approval_period_weekly_release_at' => array(
	            	'type' => 'TIME',
	            	'null' => true,
	            ),
				'withdrawal_condition' => array(
					'type' => 'INT',
					'null' => false,
					'default' => 0,
				),
	            'created_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'created_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'updated_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'updated_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            )
	        );
	        $this->dbforge->add_field($fields);
	        $this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table('viprebateruletemplate');
		}
		//add table viprebateruleallowgametemplate
		if (!$this->db->table_exists('viprebateruleallowgametemplate')) {
			$fields = Array(
	            'id' => array(
	                'type' => 'INT',
	                'null' => false,
	                'auto_increment' => TRUE,
	            ),
	            'viprebateruletemplate_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0
	            ),
	            'gameplatform_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'gametype_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'gamedescription_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'checked' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'percentage' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	        );
	        $this->dbforge->add_field($fields);
	        $this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table('viprebateruleallowgametemplate');
	    }
	    //add table vipsettingruleallowgame
		if (!$this->db->table_exists('vipsettingruleallowgame')) {
			$fields = Array(
	            'id' => array(
	                'type' => 'INT',
	                'null' => false,
	                'auto_increment' => TRUE,
	            ),
	            'vipsettingcashbackruleId' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0
	            ),
	            'gameplatform_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'gametype_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'gamedescription_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'checked' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'percentage' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'created_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'created_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'updated_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'updated_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            )
	        );
	        $this->dbforge->add_field($fields);
	        $this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table('vipsettingruleallowgame');
	    }
	    //add table vipsettingruleallowgame_history
		if (!$this->db->table_exists('vipsettingruleallowgame_history')) {
			$fields = Array(
	            'id' => array(
	                'type' => 'INT',
	                'null' => false,
	                'auto_increment' => TRUE,
	            ),
	            'history_index' => array(
	                'type' => 'INT',
	                'null' => false,
	            ),
	            'vipsettingcashbackruleId' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0
	            ),
	            'gameplatform_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'gametype_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'gamedescription_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'checked' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'percentage' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'updated_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'updated_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            )
	        );
	        $this->dbforge->add_field($fields);
	        $this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table('vipsettingruleallowgame_history');
	    }
	    //add table vipsettingavgpayout
		if (!$this->db->table_exists('vipsettingavgpayout')) {
			$fields = Array(
	            'id' => array(
	                'type' => 'INT',
	                'null' => false,
	                'auto_increment' => TRUE,
	            ),
	            'gameplatform_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'gametype_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'gamedescription_id' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'avg_payout' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'total' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0
	            ),
	            'hit' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0
	            ),
	            'updated_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'updated_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            )
			);
	        $this->dbforge->add_field($fields);
	        $this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table('vipsettingavgpayout');
		}	    
		//add table vipplayerexphistory
		if (!$this->db->table_exists('vipplayerexphistory')) {
			$fields = Array(
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
	            'action_type' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            	'default' => 0,
	            ),
	            'value' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'experience' => array(
	            	'type' => 'DOUBLE',
	            	'null' => false,
	            	'default' => 0.00
	            ),
	            'extra_id' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            ),
	            'extra_info' => array(
	            	'type' => 'TEXT',
	            	'null' => true,
	            ),
	            'created_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            )
			);
	        $this->dbforge->add_field($fields);
	        $this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table('vipplayerexphistory');
		}		
		//add table vipplayerlevelrequest
		if (!$this->db->table_exists('vipplayerlevelrequest')) {
			$fields = Array(
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
	            'oldLevelId' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'newLevelId' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'request_type' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'request_date' => array(
	            	'type' => 'DATETIME',
	            	'null' => false,
	            ),
	            'request_status' => array(
	            	'type' => 'INT',
	            	'null' => false,
	            ),
	            'oldLevelRule' => array(
	            	'type' => 'TEXT',
	            	'null' => true,
	            ),
	            'remark' => array(
	            	'type' => 'TEXT',
	            	'null' => true,
	            ),
	            'created_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'processed_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'updated_at' => array(
	            	'type' => 'DATETIME',
	            	'null' => true,
	            ),
	            'updated_by' => array(
	            	'type' => 'INT',
	            	'null' => true,
	            )
			);
	        $this->dbforge->add_field($fields);
	        $this->dbforge->add_key('id', TRUE);
	        $this->dbforge->create_table('vipplayerlevelrequest');
		}		
	}

	public function down() {
		//remove column vipLevelExp from vipsettingcashbackrule
		$this->dbforge->drop_column('vipsettingcashbackrule', 'vipLevelExp');
		//remove column vipExp from player
		$this->dbforge->drop_column('player', 'vipExp');
		//remove column vipMechanics from player
		$this->dbforge->drop_column('player', 'vipMechanics');
		//remove column depositSuccesionFrom from promorules
		$this->dbforge->drop_column('promorules', 'depositSuccesionFrom');
		//remove column depositSuccesionTo from promorules
		$this->dbforge->drop_column('promorules', 'depositSuccesionTo');
		//remove table vipsettingrule
        $this->dbforge->drop_table('vipsettingrule');
		//remove table vipupgradesetting
        $this->dbforge->drop_table('vipupgradesetting');
		//remove table vipupgradelock
        $this->dbforge->drop_table('vipupgradelock');
		//remove table vipdowngradesetting
        $this->dbforge->drop_table('vipdowngradesetting');
		//remove table vipdowngradegraceperiodlock
        $this->dbforge->drop_table('vipdowngradegraceperiodlock');
		//remove table vipdowngradelock
        $this->dbforge->drop_table('vipdowngradelock');
		//remove table viprebateruletemplate
        $this->dbforge->drop_table('viprebateruletemplate');
		//remove table viprebateruleallowgametemplate
        $this->dbforge->drop_table('viprebateruleallowgametemplate');
		//remove table vipsettingruleallowgame
        $this->dbforge->drop_table('vipsettingruleallowgame');
		//remove table vipsettingruleallowgame_history
        $this->dbforge->drop_table('vipsettingruleallowgame_history');
        //remove table vipsettingavgpayout
        $this->dbforge->drop_table('vipsettingavgpayout');
        //remove table vipplayerexphistory
        $this->dbforge->drop_table('vipplayerexphistory');
        //remove table vipplayerlevelrequest
        $this->dbforge->drop_table('vipplayerlevelrequest');
	}
}