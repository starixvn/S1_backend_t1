<?php
/**
 *   filename:   create_structure.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-03
 *   @brief:     view for structure creating
 */
?>


<div class="content-container">
<!-- form create-structure {{{1 -->
<form method="POST" id="agency_main_form" action="<?=$form_url?>">
    <div class="panel panel-primary ">
        <!-- panel heading of create-structure {{{2 -->
        <div class="panel-heading">
            <div class="pull-right ">
                <span>
                    <?=lang('Fields with')?> (<font style="color:red;">*</font>) <?=lang('are required')?>
                </span>
            </div>
            <h4 class="panel-title">
                <i class="glyphicon glyphicon-list-alt"></i> <?php if (isset($is_agent) && $is_agent){echo lang('Agent Form');}else{ echo lang('Agent Template Name'); } ?>
            </h4>
        </div> <!-- panel heading of create-structure }}}2 -->

        <!-- panel body of create-structure  {{{2 -->
        <div class="panel panel-body" id="create-structure_panel_body">

            <div class="row">

                <!-- Basic Info {{{3 -->
                <div class="col-md-12 form-horizontal">
                    <div class="form-group">
                        <input type="hidden" name="status" value="<?=$conditions['status']?>" />

                        <?php if (isset($is_agent) && $is_agent): ?>

                            <?php if (isset($is_edit) && $is_edit): ?>
                                <input type="hidden" name="agent_id" value="<?=$conditions['agent_id']?>" />
                            <?php endif ?>

                            <?php if (isset($conditions['structure_id'])): ?>
                                <input type="hidden" id="structure_id" name="structure_id" value="<?=$conditions['structure_id']?>" />
                            <?php endif ?>

                            <?php if (isset($conditions['parent_id']) && $conditions['parent_id']): ?>
                            <input type="hidden" id="parent_id" name="parent_id" value="<?=$conditions['parent_id']?>" />
                            <?php endif ?>
                            <?php if ( ! isset($is_batch) || ! $is_batch): ?>
                                <input type="hidden" id="agent_count" name="agent_count" value="<?=$conditions['agent_count']?>" />
                            <?php endif ?>
                            <input type="hidden" id="before_credit" name="before_credit" value="<?=$conditions['before_credit']?>" />

                            <div class="col-md-3">
                                <label for="agent_name"><font style="color:red;">*</font> <?=lang('Agent Username');?></label>
                                <input type="text" id="agent_name" class="form-control input-sm" value="<?=set_value('agent_name', $conditions['agent_name']);?>" data-toggle="tooltip" title="<?=lang('Agent Username');?>" required="required" <?=isset($is_edit) && $is_edit ? 'readonly="readonly"':'name="agent_name"'?>>
                                <span class="errors"><?php echo form_error('agent_name'); ?></span>
                                <span id="error-agent_name" class="errors"></span>
                            </div>

                            <?php if (isset($is_batch) && $is_batch): ?>
                                <div class="col-md-3">
                                    <label for="agent_count"><font style="color:red;">*</font> <?=lang('Count');?></label>
                                    <input type="text" name="agent_count" id="agent_count" class="form-control input-sm" value="<?=set_value('agent_count', $conditions['agent_count']);?>" data-toggle="tooltip" title="<?=lang('number of agents');?>">
                                    <span class="errors"><?php echo form_error('agent_count'); ?></span>
                                    <span id="error-agent_count" class="errors"></span>
                                </div>
                            <?php endif ?>

                            <div class="col-md-3">
                                <label for="password"><font style="color:red;">*</font> <?=lang('reg.05');?></label>
                                <input type="password" name="password" id="password" class="form-control input-sm" value="<?=set_value('password', $conditions['password']);?>" data-toggle="tooltip" title="<?=lang('reg.a06');?>" required="required">
                                <span class="errors"><?php echo form_error('password'); ?></span>
                                <span id="error-password" class="errors"></span>
                            </div>

                            <div class="col-md-3">
                                <label for="confirm_password"><font style="color:red;">*</font> <?=lang('reg.07');?></label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control input-sm" value="<?=set_value('confirm_password', $conditions['confirm_password']);?>" data-toggle="tooltip" title="<?=lang('Reenter the same password');?>" required="required">
                                <span class="errors"><?php echo form_error('confirm_password'); ?></span>
                                <span id="error-confirm_password" class="errors"></span>
                            </div>

                            <?php if ( ! isset($is_batch) || ! $is_batch): ?>
                                <?php if (isset($is_create) && $is_create): ?>
                                    <div class="col-md-3">
                                        <label for="tracking_code"><font style="color:red;">*</font> <?=lang('Tracking Code');?></label>
                                        <input type="text" name="tracking_code" id="tracking_code" class="form-control input-sm <?=$this->utils->isEnabledFeature('agent_tracking_code_numbers_only') ? 'number_only' : ''?>" value="<?=$conditions['tracking_code'];?>"
                                            data-toggle="tooltip" title="<?=lang('Tracking Code');?>" required="required" minlength="3" maxlength="20">
                                        <span class="errors"><?php echo form_error('tracking_code'); ?></span>
                                        <span id="error-tracking_code" class="errors"></span>
                                    </div>
                                <?php else: ?>
                                    <div class="col-md-3">
                                        <label for="tracking_code"><?=lang('Tracking Code');?></label>
                                        <input type="text" id="tracking_code" class="form-control input-sm" value="<?=$conditions['tracking_code'];?>" readonly
                                            data-toggle="tooltip" title="<?=lang('Tracking Code');?>">
                                    </div>
                                <?php endif ?>
                            <?php endif ?>

                        <?php else: ?>

                            <?php if (isset($conditions['structure_id'])): ?>
                                <input type="hidden" id="structure_id" name="structure_id" value="<?=$conditions['structure_id']?>" />
                            <?php endif ?>
                            <div class="col-md-3">
                                <label for="structure_name"><font style="color:red;">*</font> <?=lang('Agent Template Name');?></label>
                                <input type="text" name="structure_name" id="structure_name" class="form-control input-sm" value="<?=set_value('structure_name', $conditions['structure_name']);?>" data-toggle="tooltip" title="<?=lang('Agent Template Name');?>" required="required">
                                <span class="errors"><?php echo form_error('structure_name'); ?></span>
                                <span id="error-structure_name" class="errors"></span>
                            </div>

                        <?php endif ?>

                    </div>

                    <div class="form-group">
                        <?php if (isset($is_agent) && $is_agent): ?>
                        <div class="col-md-3">
                            <label for="firstname"><?=lang('First Name');?></label>
                            <input type="text" name="firstname" id="firstname" class="form-control input-sm" value="<?=set_value('firstname', $conditions['firstname']);?>" data-toggle="tooltip" title="<?=lang('First Name');?>"/>
                            <span class="errors"><?php echo form_error('firstname'); ?></span>
                            <span id="error-firstname" class="errors"></span>
                        </div>

                        <div class="col-md-3">
                            <label for="lastname"><?=lang('Last Name');?></label>
                            <input type="text" name="lastname" id="lastname" class="form-control input-sm" value="<?=set_value('lastname', $conditions['lastname']);?>" data-toggle="tooltip" title="<?=lang('Last Name');?>"/>
                            <span class="errors"><?php echo form_error('lastname'); ?></span>
                            <span id="error-lastname" class="errors"></span>
                        </div>
                        <?php endif ?>

                        <?php # Validate credit only when we are not using wallet
                        if (!$this->utils->isEnabledFeature('agent_settlement_to_wallet')): ?>
                            <div class="col-md-3">
                                <label for="credit_limit"><font style="color:red;">*</font> <?=lang('Credit Limit');?></label>
                                <input type="number" name="credit_limit" id="credit_limit" class="form-control input-sm" value="<?=set_value('credit_limit', $conditions['credit_limit']);?>" data-toggle="tooltip" title="<?=lang('Credit Limit');?>" min="0" step="any"/>
                                <span class="errors"><?php echo form_error('credit_limit'); ?></span>
                                <span id="error-credit_limit" class="errors"></span>
                            </div>

                            <?php if (isset($is_agent) && $is_agent): ?>
                                <div class="col-md-3" >
                                    <label for="available_credit"><font style="color:red;">*</font> <?=lang('Available Credit');?></label>
                                    <?php if (isset($is_edit) && $is_edit): ?>
                                        <div class="input-group" style="cursor: pointer;">
                                            <input type="text"
                                                   class="form-control input-sm"
                                                   value="<?=set_value('available_credit', $conditions['available_credit']);?>" readonly/>
                                            <div class="input-group-addon"
                                                 data-toggle="tooltip" title="Adjust Credits" data-placement="left"
                                                 onclick="agent_adjust_credit('<?=$agent_id?>')"><span class="glyphicon glyphicon-edit"></span></div>
                                        </div>
                                    <?php else: ?>
                                        <input type="text" name="available_credit" id="available_credit" class="form-control input-sm" value="<?=set_value('available_credit', $conditions['available_credit']);?>" data-toggle="tooltip" title="<?=lang('Available Credit');?>" required="required">
                                    <?php endif ?>
                                    <span class="errors"><?php echo form_error('available_credit'); ?></span>
                                    <span id="error-available_credit" class="errors"></span>
                                </div>
                            <?php endif ?>
                        <?php endif ?>

                    </div>

                    <?php if (isset($is_agent) && $is_agent): ?>
                        <div class="form-group">
                            <div class="col-md-3">
                                <label for="note"><?=lang('Note');?></label>
                                <textarea name="note" id="note" class="form-control input-sm" maxlength="100" style="height: 37px;resize: none;"><?=set_value('note', $conditions['note']);?></textarea>
                                <span class="errors"><?php echo form_error('note'); ?></span>
                                <span id="error-note" class="errors"></span>
                            </div>
                            <div class="col-md-3">
                                <label for="copy_template"><?=lang('Copy settings from template');?></label>
                                <select name="copy_template" id="copy_template" class="form-control input-sm input-sm">
                                    <option value="" selected>--  <?=lang('None');?> --</option>
                                    <?php foreach ($agent_templates as $tmpl): ?>
                                    <option value=<?=$tmpl['structure_id']?>><?=$tmpl['structure_name'];?></option>
                                    <?php endforeach ?>
                                </select>
                                <span class="errors"><?php echo form_error('copy_template'); ?></span>
                                <span id="error-copy_template" class="errors"></span>
                            </div>

                            <?php if (isset($is_edit) && $is_edit): ?>
                                 <div class="col-md-3">
                                    <label for="player_prefix"><?=lang('Player Prefix');?></label>
                                    <input type="text" class="form-control input-sm" value="<?=set_value('player_prefix', $conditions['player_prefix']);?>" readonly/>
                                </div>
                            <?php else: ?>
                               <div class="col-md-3">
                                    <label for="player_prefix"><?=lang('Player Prefix');?></label>
                                    <input type="text"
                                        name="player_prefix" id="player_prefix"
                                        class="form-control input-sm"
                                        value="<?=set_value('player_prefix', $conditions['player_prefix']);?>"
                                        data-toggle="tooltip"
                                        title="<?=lang('Player Prefix');?>"
                                        maxlength="5"
                                        size="5"
                                    />
                                    <span class="errors"><?php echo form_error('player_prefix'); ?></span>
                                    <span id="error-player_prefix" class="errors"></span>
                                </div>
                            <?php endif ?>

                            <?php if (isset($show_currency_options) && $show_currency_options == true): ?>
                            <div class="col-md-3" style="margin-top:-3.5px;">
                                <?php include __DIR__.'/../includes/agency_form_currency.php'; ?>
                            </div>
                            <?php endif ?>

                        </div>

                        <?php if($this->config->item('enable_gamegateway_api')) : ?>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="radio-inline"><input class="live_mode" type="radio" name="live_mode" value="1" <?= isset($conditions['live_mode']) && $conditions['live_mode']==1 ? 'checked' : '' ?> > Use Live Keys </label>
                                    <label class="radio-inline"><input class="live_mode" type="radio" name="live_mode" value="0" <?= isset($conditions['live_mode']) && $conditions['live_mode']==0 ? 'checked' : '' ?>> Use Staging Keys </label>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php endif ?>

                </div> <!-- Basic Info }}}3 -->

                <!-- AGENCY COMMISSION SETTINGS -->
                <?php if ($this->utils->isEnabledFeature('enable_agency_prefix_for_game_account') && isset($is_edit) && $is_edit){ ?>
                <div class="col-md-12">
                    <?=$this->load->view('includes/agent_prefix_of_game_account', array('agent' => $conditions), TRUE)?>
                </div>
                <?php }?>

                <?php if (!isset($conditions['agent_level']) || $conditions['agent_level'] == 0 || $this->utils->isEnabledFeature('variable_agent_fee_rate')): ?>
                <div class="col-md-12">
                    <?=$this->load->view('includes/agent_commission_fees', array('agent' => $conditions), TRUE)?>
                </div>
                <?php endif ?>

                <!-- AGENCY PERMISSION SETTINGS -->
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?=lang('Permission Setting');?></h3>
                        </div>
                        <div class="panel-body">
                            <?php if (isset($is_agent) && $is_agent): ?>
                                <?php if ((isset($is_edit) && $is_edit) || (isset($parent_id) && $parent_id)): ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <?=lang('Agent Level');?>
                                                </div>
                                                <input type="text" name="agent_level" id="agent_level" class="form-control " value="<?=set_value('agent_level', $conditions['agent_level']);?>" data-toggle="tooltip" title="<?=lang('Agent Level');?>" readonly="readonly">
                                                <input type="hidden" name="agent_level_name" id="agent_level_name" value="<?=$conditions['agent_level_name']?>" />
                                            </div>
                                        </div>
                                        <span class="errors"><?php echo form_error('agent_level'); ?></span>
                                        <span id="error-agent_level" class="errors"></span>
                                    </div>
                                <?php else: ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"><?=lang('Select Parent Agent');?></label>
                                            <select name="parent_id" id="parent_id" class="form-control input-sm">
                                                <option value=""><?=lang('NONE');?></option>
                                                <?php foreach($all_agents as $rec) { ?>
                                                    <option value='<?=$rec['agent_id']?>' <?=($conditions['parent_id'] == $rec['agent_id']) ? 'selected' : ''?>><?=$rec['agent_name']?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="errors"><?php echo form_error('parent_id'); ?></span>
                                        </div>
                                    </div>
                                <?php endif ?>
                            <?php else: ?>
                            <?php endif ?>

                            <!-- checkboxes for agent type {{{4 -->
                            <div class="col-md-6">
                                <label><input type="checkbox" name="agent_type[]" value="can-have-sub-agents"
                                <?=isset($conditions['enabled_can_have_sub_agent']) ? ($conditions['can_have_sub_agent'] ? 'checked' : '') : 'disabled' ?>>
                                <?=lang('Can Have sub-agent');?>
                                </label>
                                <br>
                                <label><input type="checkbox" name="agent_type[]" value="can-have-players"
                                <?=isset($conditions['enabled_can_have_players']) ? ($conditions['can_have_players'] ? 'checked' : '') : 'disabled' ?>>
                                <?=lang('Can Have Players');?>
                                </label>
                                <?php if (!$this->utils->isEnabledFeature('hide_bet_limit_on_agency')): ?>
                                    <br>
                                    <label><input type="checkbox" name="agent_type[]" value="show-bet-limit-template"
                                    <?= $conditions['enabled_show_bet_limit_template'] ? ($conditions['show_bet_limit_template'] ? 'checked' : '') : 'disabled' ?>>
                                    <?=lang('Show Bet Limit Template');?>
                                    </label>
                                <?php endif ?>
                                <?php if ($this->utils->isEnabledFeature('rolling_comm_for_player_on_agency')): ?>
                                <br>
                                <label><input type="checkbox" name="agent_type[]" value="show-rolling-commission"
                                <?=isset($conditions['enabled_show_rolling_commission']) ? ($conditions['show_rolling_commission'] ? 'checked' : '') : 'disabled' ?>>
                                <?=lang('Show Rolling Commission');?>
                                </label>
                                <?php endif ?>
                                <br>
                                <label><input type="checkbox" name="agent_type[]" value="can-view-agents-list-and-players-list"
                                <?=isset($conditions['enabled_can_view_agents_list_and_players_list']) ? ($conditions['can_view_agents_list_and_players_list'] ? 'checked' : '') : 'disabled' ?>>
                                <?=lang('Can View Agents List and Players List');?>
                                </label>
                                <br/>
                                <label><input type="checkbox" name="agent_type[]" value="can-do-settlement"
                                <?=isset($conditions['enabled_can_do_settlement']) ? ($conditions['can_do_settlement'] ? 'checked' : '') : 'disabled'?>>
                                <?=lang('Can Do Settlement');?>
                                </label>
                            </div> <!-- checkboxes }}}4 -->

                        </div> <!-- fieldset permission-setting }}}3 -->
                    </div> <!-- panel permission-setting }}}3 -->

                    <!--  modal for level name setting {{{4 -->
                    <div class="modal fade in" id="level_name_modal"
                        tabindex="-1" role="dialog" aria-labelledby="label_level_name_modal">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="label_level_name_modal"></h4>
                                </div>
                                <div class="modal-body"></div>
                                <div class="modal-footer"></div>
                            </div>
                        </div>
                    </div> <!--  modal for level name setting }}}4 -->
                </div> <!-- END AGENCY PERMISSION SETTINGS -->

                <!-- AGENCY GAME PLATFORM SETTINGS -->
                <div class="col-md-12">
                    <?php if ($this->utils->isEnabledFeature('agent_tier_comm_pattern')) { ?>
                    <?=$this->load->view('includes/game_platform_settings_tier_comm', $game_platform_settings, TRUE)?>
                    <?php } else { ?>
                    <?=$this->load->view('includes/game_platform_settings', $game_platform_settings, TRUE)?>
                    <?php } ?>
                </div> <!-- END AGENCY GAME PLATFORM SETTINGS -->

                <?php if (isset($controller_name) && $controller_name == 'agency'): ?>
                    <?php if ($conditions['vip_level']) : ?>
                    <!-- Default player vip level follows that of parent's -->
                    <input name="vip_level" type="hidden" value="<?=$conditions['vip_level']?>" />
                    <?php endif; ?>
                <?php else: ?>
                    <!-- AGENCY VIP LEVEL SETTINGS -->
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title"><font style="color:red;">*</font> <?=lang('Default Player VIP Level');?></h3>
                            </div>
                            <div class="panel-body">
                                <div class="col-md-6">
                                <select name="vip_level" id="vip_level" class="form-control">
                                    <?php foreach ($vip_levels as $vip_level): ?>
                                        <optgroup label="<?=lang($vip_level['name'])?>">
                                            <?php foreach ($vip_level['levels'] as $level): ?>
                                                <option value="<?=$level['id']?>" <?=$conditions['vip_level'] == $level['id'] ? 'selected="selected"':''?>><?=$level['name']?></option>
                                            <?php endforeach ?>
                                        </optgroup>
                                    <?php endforeach ?>
                                </select>
                                <span class="errors"><?php echo form_error('player_vip_level'); ?></span>
                                <span id="error-player_vip_level" class="errors"></span>
                                </div>
                            </div>
                        </div>
                    </div> <!-- END AGENCY VIP LEVEL SETTINGS -->
                <?php endif ?>

                <!-- AGENCY SETTLEMENT SETTINGS -->
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><font style="color:red;">*</font> <?=lang('Settlement Setting');?></h3>
                        </div>
                        <div class="panel-body">
                            <!-- checkboxes for settlement period {{{4 -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="input-group pull-right">
                                            <?=lang('Settlement Period');?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="radio-inline">
                                            <input type="radio" name="settlement_period"
                                            value="Daily"
                                            <?=$conditions['settlement_period'] == 'Daily' ? 'checked' : ''?>
                                            onclick="setDisplayWeeklyStartDay()">
                                            <?=lang('Daily');?>
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="settlement_period"
                                            id="settlement_period_weekly" value="Weekly"
                                            <?=$conditions['settlement_period'] == 'Weekly' ? 'checked' : ''?>
                                            onclick="setDisplayWeeklyStartDay()">
                                            <?=lang('Weekly');?>
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="settlement_period"
                                            value="Monthly"
                                            <?=$conditions['settlement_period'] == 'Monthly' ? 'checked' : ''?>
                                            onclick="setDisplayWeeklyStartDay()">
                                            <?=lang('Monthly');?>
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="settlement_period"
                                            value="Manual"
                                            <?=$conditions['settlement_period'] == 'Manual' ? 'checked' : ''?>
                                            onclick="setDisplayWeeklyStartDay()">
                                            <?=lang('Manual');?>
                                        </label>
                                    </div>
                                    <span class="errors"><?php echo form_error('settlement_period[]'); ?></span>
                                    <span id="error-settlement_period" class="errors"></span>
                                </div>
                            </div> <!-- checkboxes for settlement period }}}4 -->
                            <!-- radioes for weekly_start_day (only shown when Weekly is selected) {{{4 -->
                            <div id="weekly_start_day" class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="input-group pull-right">
                                            <?=lang('Start Day for Weekly Settlement:');?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="radio-inline">
                                            <input type="radio" name="start_day" value="Monday"
                                            <?=$conditions['start_day']=='Monday'?'checked':''?>>
                                            <?=lang('Monday');?>
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="start_day" value="Tuesday"
                                            <?=$conditions['start_day']=='Tuesday'?'checked':''?>>
                                            <?=lang('Tuesday');?>
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="start_day" value="Wednesday"
                                            <?=$conditions['start_day']=='Wednesday'?'checked':''?>>
                                            <?=lang('Wednesday');?>
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="start_day" value="Thursday"
                                            <?=$conditions['start_day']=='Thursday'?'checked':''?>>
                                            <?=lang('Thursday');?>
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="start_day" value="Friday"
                                            <?=$conditions['start_day']=='Friday'?'checked':''?>>
                                            <?=lang('Friday');?>
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="start_day" value="Saturday"
                                            <?=$conditions['start_day']=='Saturday'?'checked':''?>>
                                            <?=lang('Saturday');?>
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="start_day" value="Sunday"
                                            <?=$conditions['start_day']=='Sunday'?'checked':''?>>
                                            <?=lang('Sunday');?>
                                        </label>
                                    </div>
                                    <span class="errors"><?php echo form_error('weekly_start_day'); ?></span>
                                    <span id="error-weekly_start_day" class="errors"></span>
                                </div>
                            </div> <!-- radioes for weekly_start_day  }}}4 -->
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <span class="errors"><?php echo form_error('agent_settlement_period'); ?></span>
                                    <span id="error-agent_settlement_period" class="errors"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- END AGENCY SETTLEMENT SETTINGS -->

                <!-- button row {{{3 -->
                <div class="col-md-12 text-center">
                    <?php if(isset($is_edit) && $is_edit){ ?>
                        <a href="javascript:window.history.back()" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?=lang('Back')?></a>
                    <?php } ?>
                    <input type="submit" id="submit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" value="<?=lang('Save')?>" />
                </div>
                <!-- button row }}}3 -->
            </div>
        </div> <!-- panel body of create-structure  }}}2 -->
    </div>
</form> <!-- end of form create-structure }}}1 -->
</div>

<script>
$(document).ready(function(){
    var ajax_url = "<?=$validate_ajax_url?>";
    var labels = '<?=json_encode($labels)?>';
    var fields = '<?=json_encode($fields)?>';

    <?= isset($is_edit) && $is_edit ? 'agency_form_validation_edit(ajax_url, fields, labels);' : 'agency_form_validation(ajax_url, fields, labels);'?>

    var parent_game_ajax_url = "<?=$parent_game_ajax_url?>";
    if ($('#parent_id').val()){
        getParentGameTypeAjax(parent_game_ajax_url, $('#parent_id').val());
    } else {
        $('#parent_id').blur(function(){
            if ($(this).val()){
                getParentGameTypeAjax(parent_game_ajax_url, $(this).val());
            }
        });
    }

    $('.live_mode').on('click', function(){
        $('.live_mode').not(this).prop("checked", false);
    });

    $('input[name="settlement_period"][value="' + '<?=$conditions['settlement_period']?>' + '"]').click();

    $(".number_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110]) !== -1 ||
            // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
            // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                // let it happen, don't do anything
                return;
            }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105)) {
            e.preventDefault();
        }
    });

    function setTemplateValues(data) {
        $('#credit_limit').val(data.structure_details.credit_limit);
        $('#min_rolling_comm').val(data.structure_details.min_rolling_comm);
        $('#admin_fee').val(data.structure_details.admin_fee);
        $('#bonus_fee').val(data.structure_details.bonus_fee);
        $('#cashback_fee').val(data.structure_details.cashback_fee);
        $('#transaction_fee').val(data.structure_details.transaction_fee);

        var d = data.structure_details;
        // console.log(d);

        // set agent type
        var chks = document.getElementsByName('agent_type[]');
        //console.log(chks);

        for (var i = 0; i < chks.length; i++) {
            chk = chks[i];
            var v = chk.getAttribute('value').replace(/-/g, '_');
            if (v == 'can_have_sub_agents') {
                v = 'can_have_sub_agent';
            }
            //console.log(v);
            //console.log(d[v]);
            if (d[v] == 1) {
                chk.checked = true;
                //console.log(chk);
            } else {
                chk.checked = false;
            }
        }
        // set vip level - removed in OGP-12479
        //$('#vip_level').val(d['vip_level']);

        // set settlement period
        $('input[name="settlement_period"][value="' + d['settlement_period'] + '"]').attr("checked", true);
        $('input[name="settlement_period"][value="' + d['settlement_period'] + '"]').click();
        $('input[name="start_day"][value="' + d['settlement_start_day'] + '"]').attr("checked", true);

        // set game comm settings
        var g = data.structure_game_types;
        for (var i in g) {
            //console.log(i);
            //console.log(g[i]);
            for (k in g[i]){
                id = 'game_types-' + i + '-' + k;
                $('#' + id).val(g[i][k]);
                if (k == 'game_platform_id') {
                    var game_platform_checkbox = '#game-platform-' + g[i][k];
                    if(!$(game_platform_checkbox).is(':checked')){
                        $(game_platform_checkbox).attr('checked', true);
                        $('.platform-field-' + g[i][k]).prop('disabled', '').trigger('change');
                    }
                }
            }
        }
    }
    $('#copy_template').change(function(){
        $('#error-copy_template').html('');
        v = $(this).val();
        if (v == "") {
            window.location.reload();
            return true;
        }
        var data={};
        data["structure_id"] = $(this).val();
        data["parent_id"] = <?=(isset($conditions['parent_id']) && $conditions['parent_id'])?$conditions['parent_id']:"''" ?>;
        if(data){
            $.ajax({
                url : "<?=isset($copy_template_ajax_url)?$copy_template_ajax_url:'';?>",
                type : 'POST',
                data : data,
                dataType : "json",
                cache : false,
        }).done(function (data) {
            // console.log(data);
            if (data.status == "success") {
                //alert("set template successfully!");
                setTemplateValues(data);
            }
            if (data.status == "failed") {
                var message = data.msg;
                $('#error-copy_template').html(message);
            }
        }).fail(function (jqXHR, textStatus) {
            /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
            // location.reload();
        });
        }
    });

    $('#agent_name').on('input', function() {
        var e = $('#tracking_code');
        if (e.length != 0) {
            e.val($(this).val());
        }
    });
});
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of create_structure.php
