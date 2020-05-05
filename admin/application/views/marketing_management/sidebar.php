<ul class="sidebar-nav" id="sidebar">

<?php
$permissions = $this->permissions->getPermissions();
if ($permissions != null) {
	foreach ($permissions as $value) {
		$icon_pull_right = ($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';
        $style_display_option =  ($this->session->userdata('sidebar_status') == 'active' ? 'initial' : 'none');
        $style_display = "style='display:{$style_display_option};' ";
		switch ($value) {
		case 'promo_category_setting': ?>
				<li>
					<a id="promoCategorySettings" class="list-group-item" style="border: 0px;" href="<?=site_url('/marketing_management/promoTypeManager')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('cms.promoCategorySettingsDesc');?>">
						<i class="icon-cog <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
						<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
							<?=lang('cms.promoCategorySettings');?>
						</span>
					</a>
				</li>
		<?php break;
		case 'promo_rules_setting': ?>
						<li>
							<a class="list-group-item" id="view_promorules_settings" style="border: 0px;line-height: 1.42857143;margin-bottom:0.1px;" href="																						<?=BASEURL . 'marketing_management/promoRuleManager'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('cms.promoRuleSettings');?>">
								<i class="fa fa-book <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
								<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
									<?=lang('cms.promoRuleSettings');?>
								</span>
							</a>
						</li>
			<?php break;
		case 'promocms':
			?>
						<li>
	                        <a class="list-group-item" id="view_promo_list" style="border: 0px;margin-bottom:0.1px;" href="																					<?=BASEURL . 'marketing_management/promoSettingList'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('cms.06');?>">
	                            <i class="icon-bullhorn <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
	                            <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
									<?=lang('cms.06');?>
								</span>
	                        </a>
                    	</li>
        <?php break;
		case 'shopping_center_manager':
				if ($this->utils->isEnabledFeature('enable_shop')) {
			?>
						<li>
	                        <a class="list-group-item" id="view_shopping_center_list" style="border: 0px;margin-bottom:0.1px;" href="																					<?=BASEURL . 'marketing_management/shoppingCenterItemList'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Shop Manager');?>">
	                            <i class="icon-bullhorn <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
	                            <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
									<?=lang('Shop Manager');?>
								</span>
	                        </a>
                    	</li>
            <?php
        		}
            break;
		case 'shopper_list':
				if ($this->utils->isEnabledFeature('enable_shop')) {
			?>
						<li>
	                        <a class="list-group-item" id="view_shopper_request_list" style="border: 0px;margin-bottom:0.1px;" href="																					<?=BASEURL . 'marketing_management/shoppingClaimRequestList'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Shopping Points Request List');?>">
	                            <i class="icon-bullhorn <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
	                            <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
									<?=lang('Shopping Points Request List');?>
								</span>
	                        </a>
                    	</li>
            <?php
            	}
            break;
		case 'promoapp_list':
			?>
						<li>
							<a class="list-group-item" id="view_promoapp_list" style="border: 0px;margin-bottom:0.1px;" href="																						<?=BASEURL . 'marketing_management/promoApplicationList'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('cms.promoReqAppList');?>">
								<i class="fa fa-hand-paper-o <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
								<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
									<?=lang('cms.promoReqAppList');?>
								</span>
							</a>
						</li>
			<?php break;
		case 'report_gamelogs':
			?>
						<li>
							<a class="list-group-item" id="view_game_logs" style="border: 0px;margin-bottom:0.1px;" href="																						<?=BASEURL . 'marketing_management/viewGameLogs'?>" data-toggle="tooltip" data-placement="right" title="<?=lang('role.157');?>">
								<i class="fa fa-table <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
								<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
									<?=lang('role.157');?>
								</span>
							</a>
						</li>
			<?php break;
		case 'cashback_setting': ?>
			<?php if(!$this->utils->isEnabledFeature('close_cashback')): ?>
			<li>
				<a id="cashbackSettings" class="list-group-item" style="border: 0px;" href="<?=site_url('/marketing_management/cashbackPayoutSetting')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('cms.cashbackSettingsDesc');?>">
					<i class="glyphicon glyphicon-usd <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
								<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
									<?=lang('cms.cashbackSettings');?>
								</span>
				</a>
			</li>
		<?php endif?>

		<?php break;
		case 'friend_referral_setting': ?>
			<li>
				<a id="friendReferralSettings" class="list-group-item" style="border: 0px;" href="<?=site_url('/marketing_management/friend_referral_settings')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('cms.friendReferralSettingsDesc');?>">
				<i class="fa fa-users <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
								<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
									<?=lang('cms.friendReferralSettings');?>
								</span>
				</a>
			</li>
		<?php break;
		case 'batch_adjust_balance':
			?>
						<li>
							<a class="list-group-item" id="view_batch_balance_adjustment" style="border: 0px;line-height: 1.42857143;margin-bottom:0.1px;" href="<?php echo site_url('marketing_management/batchBalanceAdjustment'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('role.96');?>">


				<i class="fa fa-users <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
								<span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
									<?=lang('role.96');?>
								</span>
							</a>
						</li>
		 	<?php break;
		case 'cashback_request':
			?>
			<?php if(!$this->utils->isEnabledFeature('close_cashback')): ?>
            <li>
                <a class="list-group-item" id="view_cashback_request_list" style="border: 0px;line-height: 1.42857143;margin-bottom:0.1px;" href="<?php echo site_url('marketing_management/manage_cashback_request'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('xpj.cashback.list');?>">
                <i class="glyphicon glyphicon-share <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                            <?=lang('xpj.cashback.list');?>
                    </span>
                </a>
            </li>
            <?php endif; ?>
            <?php break;
		case 'bonus_game_settings' :
			if ($this->utils->isEnabledFeature('bonus_games__enable_bonus_game_settings')) {
			?>
				<li>
	                <a class="list-group-item" id="bonus_game_settings" style="border: 0px;line-height: 1.4; margin-bottom:0.1px;" href="<?php echo site_url('marketing_management/bonusGameSettings'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Bonus Game Settings');?>">
	                <i class="fa fa-gamepad <?= $icon_pull_right ?>" id="icon"></i>
	                    <span id="hide_text" <?= $style_display ?> >
	                            <?=lang('Bonus Game Settings');?>
	                    </span>
	                </a>
	            </li>
			<?php
			}
			break;
		case 'ole777_wager_sync' :
		?>
			<?php if ($this->utils->isEnabledFeature('ole777_wager_sync')) : ?>
				<li>
	                <a class="list-group-item" id="ole777_wager_sync" style="border: 0px;line-height: 1.4; margin-bottom:0.1px;" href="<?= site_url('marketing_management/ole777_wager_sync'); ?>" data-toggle="tooltip" data-placement="right" title="<?= lang('OLE777 Wager Sync'); ?>">
	                <i class="fa fa-book <?= $icon_pull_right ?>" id="icon"></i>
	                    <span id="hide_text" <?= $style_display ?> >
	                          <?=lang('OLE777 Wager Sync');?>
	                    </span>
	                </a>
	            </li>
			<?php endif; ?>
			<?php
			break;
		default:
			break;
		} // end of switch
	} // end of foreach
} // end of if
?>
</ul>
<ul id="sidebar_menu" class="sidebar-nav">
    <li class="sidebar-brand">
        <a id="menu-toggle" href="#" onclick="Template.changeSidebarStatus();">
        	<span id="main_icon" class="icon-arrow-<?=($this->session->userdata('sidebar_status') == 'active') ? 'left' : 'right';?> pull-right"></span></span>
        </a>
    </li>
</ul>