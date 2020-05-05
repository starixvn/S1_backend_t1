<ul class="sidebar-nav" id="sidebar">
    <?php
        $permissions = $this->permissions->getPermissions();
        //print_r($permissions);
        $active= isset($active) ? $active : '';
        if ($permissions != null) {
        	foreach ($permissions as $value) {
        		switch ($value) {
            		case 'report_transactions': ?>
                            <li>
                                <a class="list-group-item <?php echo $active=='view_transaction' ? 'active' : '';?>" id="view_transaction" style="border: 0px;margin-bottom:0.1px;" href="<?php echo site_url('payment_management/viewTransactionList/report'); ?>" data-toggle="tooltip" data-placement="right" title="<?=lang('pay.transactions');?>">
                                    <i class="fa fa-table <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('pay.transactions');?></span>
                                </a>
                            </li>
                        <?php break;
                    case 'report_transfer_request': ?>
                            <li>
                                <a class="list-group-item <?php echo $active=='transfer_request' ? 'active' : '';?>" id="transfer_request" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('payment_management/transfer_request/report')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Transfer Request');?>">
                                    <i class="glyphicon glyphicon-transfer <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> ><?=lang('Transfer Request');?></span>
                                </a>
                            </li>
                        <?php break;
                    case 'report_gamelogs': ?>
                            <li>
                                <a class="list-group-item <?php echo $active=='view_game_logs' ? 'active' : '';?>" id="view_game_logs" style="border: 0px;margin-bottom:0.1px;" href="<?php echo site_url('marketing_management/viewGameLogs/report');?>" data-toggle="tooltip" data-placement="right" title="<?=lang('role.157');?>">
                                    <i class="fa fa-list-alt <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                        <?=lang('role.157');?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
                    case 'promotion_report': ?>
                            <li>
                                <a class="list-group-item" id="promotion_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/viewReport/4')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('report.s02');?>">
                                    <i class="icon-bullhorn <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                                        <?=lang('report.s02');?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
            		case 'player_report': ?>
                        <!--
                            <li>
                                <a class="list-group-item" id="player_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/viewReport/2')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('report.s09');?>">
                                    <i class="icon-users <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                                       <?=lang('report.s09');?>
                                    </span>
                                </a>
                            </li>
                        -->
                            <li>
                                <a class="list-group-item" id="player_report2" style="border: 0;margin-bottom:0" href="<?=site_url('report_management/viewPlayerReport2')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('report.s09');?>">
                                    <i class="icon-users <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                                       <?=lang('report.s09');?>
                                    </span>
                                </a>
                            </li>
                            <?php break;
                    case 'player_balance_report': ?>
                            <li>
                                <a class="list-group-item" id="player_balance_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/playerRealtimeBalance?search_reg_date=on')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('role.259');?>">
                                    <i class="icon-users <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                                       <?=lang('role.259');?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
            		case 'payment_report': ?>
                            <li>
                                <a class="list-group-item" id="payment_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/viewPaymentReport?enable_date=true')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Payment Report');?>">
                                    <i class="icon-credit-card <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                        <?=lang('Payment Report');?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
                    case 'payment_status_history_report': ?>
                        <?php if($this->utils->isEnabledFeature('enable_payment_status_history_report')){?>
                            <li>
                                <a class="list-group-item" id="payment_status_history_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/viewPaymentStatusHistoryReport?enable_date=true')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('report.s10');?>">
                                    <i class="icon-credit-card <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                        <?=lang('report.s10');?>
                                    </span>
                                </a>
                            </li>
                        <?php }?>
                        <?php break;
            		case 'summary_report': ?>
                        <!--
                            <li>
                                <a class="list-group-item" id="summary_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/summary_report')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('report.s08');?>">
                                    <i class="icon-pie-chart <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                                        <?=lang('report.s08');?>
                                    </span>
                                </a>
                            </li>
                        -->
                        <?php break;
                    case 'summary_report_2': ?>
                            <li>
                                <a class="list-group-item" id="summary_report_2" data-referto="summary_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/summary_report_2')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('report.s08');?>">
                                    <i class="icon-pie-chart <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                                        <?=lang('report.s08');?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
            		case 'game_report': ?>
                            <li>
                                <a class="list-group-item" id="game_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/viewGamesReport')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('report.s07');?>">
                                    <i class="icon-dice <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                                        <?=lang('report.s07');?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
            		case 'cashback_report': ?>
                            <li>
                                <a class="list-group-item" id="cashback_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/cashback_report?enable_date=true')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Cashback Report');?>">
                                    <i class="fa fa-money <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:inline-block;"' : 'style="display:none;"';?> >
                                       <?php echo lang('Cashback Report'); ?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
            		case 'duplicate_account_report': ?>
                            <li>
                                <a class="list-group-item" id="duplicate_account_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/duplicate_account_report')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Duplicate Account Report');?>">
                                    <i class="icon-evil <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                       <?php echo lang('Duplicate Account Report'); ?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
                    case 'sms_report': ?>
                            <li>
                                <a class="list-group-item" id="sms_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/viewSmsReport')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('SMS Verification Code');?>">
                                    <i class="icon-mobile <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                       <?php echo lang('SMS Verification Code'); ?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
                    case 'active_player_report': ?>
                            <li>
                                <a class="list-group-item" id="active_player_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/viewActivePlayers')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Active Player Report')?>">
                                    <i class="icon-users <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                       <?=lang('Active Player Report')?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
                    case 'daily_player_balance_report': ?>
                        <!--
                            <li>
                                <a class="list-group-item" id="daily_player_balance_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/dailyPlayerBalanceReport')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Daily Player Balance Report')?>">
                                    <i class="fa fa-dollar fa-fw <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                       <?=lang('Daily Player Balance Report')?>
                                    </span>
                                </a>
                            </li>
                        -->
                            <li>
                                <a class="list-group-item" id="daily_player_balance_report_2" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/daily_player_balance_report')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Sidebar Daily Player Balance Report') . ' 2'?>">
                                    <i class="fa fa-dollar fa-fw <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                       <?=lang('Sidebar Daily Player Balance Report')?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
                    case 'responsible_gaming_report': ?>
                        <?php if($this->utils->isEnabledFeature('responsible_gaming')) :?>
                            <li>
                                <a class="list-group-item" id="responsible_gaming_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/responsibleGamingReport')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Daily Player Balance Report')?>">
                                    <i class="fa fa-dollar fa-fw <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                        <?=lang('Responsible Gaming Report')?>
                                    </span>
                                </a>
                            </li>
                        <?php endif;?>
                        <?php break;
                    case 'bonus_games_report' : ?>
                            <li>
                                <a class="list-group-item" id="bonus_games_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/bonusGamesReport')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Bonus Games Report')?>">
                                    <i class="fa fa-gamepad <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                        <?=lang('Bonus Games Report')?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
                    case 'player_analysis_report' : ?>
                            <li>
                                <a class="list-group-item" id="player_analysis_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/player_analysis_report')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Player Analysis Report')?>">
                                    <i class="fa fa-line-chart <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                        <?=lang('Player Analysis Report')?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
                    case 'grade_report': ?>
                            <li>
                                <a class="list-group-item" id="grade_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/viewGradeReport')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Grade Report')?>">
                                    <i class="fa fa-sort fa-fw <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                        <?=lang('Grade Report')?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
                    case 'view_communication_preference_report': ?>
                        <?php if ($this->utils->isEnabledFeature('enable_communication_preferences')): ?>
                            <li>
                                <a class="list-group-item" id="communication_preference_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/viewCommunicationPreferenceReport')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Grade Report')?>">
                                    <i class="fa fa-comments fa-fw <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                        <?=lang('Communication Preference Report')?>
                                    </span>
                                </a>
                            </li>
                        <?php endif ?>
                        <?php break;
                    case 'attached_file_list': ?>
                            <li>
                                <a class="list-group-item" id="attached_file_list" style="border: 0px;margin-bottom:0.1px;" href="<?php echo site_url('report_management/viewAttachedFileList');?>" data-toggle="tooltip" data-placement="right" title="<?=lang('role.404');?>">
                                    <i class="icon-profile <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                                    <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                        <?=lang('role.404');?>
                                    </span>
                                </a>
                            </li>
                        <?php break;
                    case 'conversion_rate_report': ?>
                    <li>
                        <a class="list-group-item" id="conversion_rate_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/conversion_rate_report')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Conversion Rate Report')?>">
                            <i id="icon" class="fa fa-crosshairs fa-fw <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                            <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                <?=lang('Conversion Rate Report')?>
                            </span>
                        </a>
                    </li>
                    <?php break;
                    default:
            			break;
            	}
            }

            if(in_array('view_income_access_signup_report', $permissions) || in_array('view_income_access_sales_report', $permissions)) { ?>
                <?php if ($this->utils->isEnabledFeature('enable_income_access')): ?>
                    <li>
                        <a class="list-group-item" id="income_access_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/viewIncomeAccessReport')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Income Access Report')?>">
                            <i class="fa fa-money <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                            <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                                <?=lang('Income Access Report')?>
                            </span>
                        </a>
                    </li>
                <?php endif ?>
            <?php
            }
        }
    ?>
    <?php $game_apis = $this->utils->getGameSystemMap(false);?>
    <?php if ($this->utils->isEnabledFeature('enabled_oneworks_game_report') && array_key_exists(ONEWORKS_API,$game_apis)) { ?>
        <li>
            <a class="list-group-item" id="oneworks_game_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/viewOneworksGameReport')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Oneworks Game Report')?>">
                <i class="icon-dice <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" ></i>
                <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                    <?=lang('Oneworks Game Report')?>
                </span>
            </a>
        </li>
    <?php }?>
    <?php $game_apis = $this->utils->getGameSystemMap(false);?>
    <?php if ($this->utils->isEnabledFeature('enabled_vr_game_report') && array_key_exists(VR_API,$game_apis)) { ?>
        <li>
            <a class="list-group-item" id="oneworks_game_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/viewVRGameReport')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('VR Games Report')?>">
                <i class="icon-dice <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" ></i>
                <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                    <?=lang('VR Games Report')?>
                </span>
            </a>
        </li>
    <?php }?>
    <?php if ($this->utils->isEnabledFeature('enabled_sbobet_sports_game_report') && array_key_exists(SBOBET_API,$game_apis)) { ?>
        <li>
            <a class="list-group-item" id="oneworks_game_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/viewSbobetGameReport')?>" data-toggle="tooltip" data-placement="right" title="<?=lang('Oneworks Game Report')?>">
                <i class="icon-dice <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" ></i>
                <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                    <?=lang('SBObet Game Report')?>
                </span>
            </a>
        </li>
    <?php }?>
    <?php if ($this->utils->isEnabledFeature('enabled_afb88_sports_game_report') && array_key_exists(AFB88_API,$game_apis)) { ?>
        <li>
            <a class="list-group-item" id="communication_preference_report" style="border: 0px;margin-bottom:0.1px;" href="<?=site_url('report_management/view_gameprovider_report_by_platform/'.AFB88_API)?>" data-toggle="tooltip" data-placement="right" title="<?=lang('AFB88 Game Report')?>">
                <i class="icon-dice <?=($this->session->userdata('sidebar_status') == 'active') ? '' : 'pull-right';?>" id="icon"></i>
                <span id="hide_text" <?=($this->session->userdata('sidebar_status') == 'active') ? 'style="display:initial;"' : 'style="display:none;"';?> >
                    <?=lang('AFB88 Game Report')?>
                </span>
            </a>
        </li>
    <?php }?>
</ul>

<ul id="sidebar_menu" class="sidebar-nav">
    <li class="sidebar-brand">
        <a id="menu-toggle" href="#" onclick="Template.changeSidebarStatus();">
            <span id="main_icon" class="icon-arrow-<?=($this->session->userdata('sidebar_status') == 'active') ? 'left' : 'right';?> pull-right"></span></span>
        </a>
    </li>
</ul>

<script type="text/javascript">
    $( document ).ready(function() {
        $('#main_icon').on('click',function(){
            if($("#wrapper").hasClass("active")){
                $.each($('.sidebar-nav li a i'),function( index, value ){
                    $(value).addClass('pull-right');
                });
            }else{
                $.each($('.sidebar-nav li a i'),function( index, value ){
                    $(value).removeClass('pull-right');
                });
            }
        });

        $side1 = $("#sidebar li").has("#summary_report_2").html();
        $side3 = $("#sidebar li").has("#summary_report").html();
        if($side1 && $side3){
            $("#sidebar li").has("#summary_report_2").remove();
        }

        $side2 = $("#sidebar li").has("#summary_report").after($('<li />').append($side1));

        //OGP-11118 Putting Summary Report 2 if Summary Report is disabled
        var $summary_report_2 = $("a[id='summary_report_2']").parent('li');
        var $summary_report_1 = $("a[id='summary_report']").parent('li');

        if($summary_report_2.length > 0 && $summary_report_1.length == 0){
            var $prepend_element = $summary_report_2.html();
            $summary_report_2.remove();
            $('#sidebar').prepend("<li>"+$prepend_element+"</li>");
        }
    });
</script>
