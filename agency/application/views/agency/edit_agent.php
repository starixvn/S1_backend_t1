<?php
/**
 *   filename:   edit_agent.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-03
 *   @brief:     view for agent creating
 */

$agent_id = $conditions['agent_id'];
$parent_id = $conditions['parent_id'];
?>

<!-- form edit-agent {{{1 -->
<form method="POST" id="agency_main_form"  style="margin: 4px;"
    action="<?=site_url('agency/verify_update_agent/'. $check)?>" accept-charset="utf-8">
    <input type="hidden" name="agent_id" value="<?=$agent_id?>" />
    <input type="hidden" id="parent_id" name="parent_id" value="<?=$conditions['parent_id']?>" />
    <input type="hidden" id="agent_count" name="agent_count" value="<?=$conditions['agent_count']?>" />
    <input type="hidden" id="before_credit" name="before_credit" value="<?=$conditions['before_credit']?>" />
    <div class="panel panel-primary ">
        <!-- panel heading of edit-agent {{{2 -->
        <div class="panel-heading">
            <h4 class="panel-title pull-left">
                <i class="glyphicon glyphicon-list-alt"></i>
                <?php if($check) { ?>
                <?=lang('Check Agent Info');?>
                <?php } else { ?>
                <?=lang('Edit Agent');?>
                <?php } ?>
            </h4>
            <div class="pull-right">
                <?php if($check) { ?>
                <span style="color: red;"><?=$check_info ?></span>
                <?php } ?>
                <?=lang('Fields with')?> (<font style="color:red;">*</font>) <?=lang('are required.')?>
            </div>
            <div class="clearfix"></div>
        </div> <!-- panel heading of edit-agent }}}2 -->

        <!-- panel body of edit-agent  {{{2 -->
        <div class="panel panel-body" id="edit-agent_panel_body">
            <!-- Basic Info {{{3 -->
            <div class="col-md-12">
                <!-- input agent_name (required) {{{4 -->
                <div class="col-md-3 fields">
                    <label for="agent_name">
                        <font style="color:red;">*</font>
                        <?=lang('Agent Username');?>
                    </label>

                    <input type="text" id="agent_name" class="form-control "
                    value="<?=$conditions['agent_name'];?>" data-toggle="tooltip"
                    title="<?=lang('Agent Username');?>" readonly="readonly">

                    <span class="errors"><?php echo form_error('agent_name'); ?></span>
                    <span id="error-agent_name" class="errors"></span>
                </div> <!-- input agent_name (required) }}}4 -->
                <!-- input password (required) {{{4 -->
                <div class="col-md-3 fields">
                    <label for="password">
                        <font style="color:red;">*</font>
                        <?=lang('reg.05');?>
                    </label>
                    <input type="password" name="password" id="password" class="form-control"
                    value="<?=$conditions['password'];?>" data-toggle="tooltip" title="<?=lang('reg.a06');?>">
                    <span class="errors"><?php echo form_error('password'); ?></span>
                    <span id="error-password" class="errors"></span>
                </div>

                <div class="col-md-3 fields">
                    <label for="confirm_password">
                        <font style="color:red;">*</font>
                        <?=lang('reg.07');?>
                    </label>

                    <input type="password" name="confirm_password" id="confirm_password"
                    class="form-control" value="<?=$conditions['password'];?>"
                    data-toggle="tooltip" title="<?=lang('Reenter the same password');?>">
                    <span class="errors"><?php echo form_error('confirm_password'); ?></span>
                    <span id="error-confirm_password" class="errors"></span>
                </div>
                <!-- input password (required) }}}4 -->
            </div>
            <div class="col-md-12">
                <!-- select currency {{{4 -->
                <div class="col-md-3 col-lg-3">
                    <?php include __DIR__.'/../includes/agency_form_currency.php'; ?>
                </div> <!-- end of select currency }}}4 -->
                <!-- select status {{{4 -->
                <div class="col-md-3 col-lg-3">
                    <label class="control-label">
                        <font style="color:red;">*</font>
                        <?=lang('Status');?>
                    </label>
                    <select name="status" id="status" class="form-control input-sm">
                        <option value="" <?=empty($conditions['status']) ? 'selected' : ''?>>
                        --  <?=lang('None');?> --
                        </option>
                        <option value="active" <?=($conditions['status'] == "active") ? 'selected' : ''?> >
                        <?=lang('Active');?>
                        </option>
                        <option value="suspended" <?=($conditions['status'] == "suspended") ? 'selected' : ''?> >
                        <?=lang('Suspended');?>
                        </option>
                        <option value="frozen" <?=($conditions['status'] == "frozen") ? 'selected' : ''?> >
                        <?=lang('Frozen');?>
                        </option>
                    </select>
                    <span class="errors"><?php echo form_error('status'); ?></span>
                    <span id="error-status" class="errors"></span>
                </div> <!-- end of select status }}}4 -->
                <!-- input credit_limit {{{4 -->
                <div class="col-md-3 fields">
                    <label for="credit_limit">
                        <font style="color:red;">*</font>
                        <?=lang('Credit Limit');?>
                    </label>

                    <input type="text" name="credit_limit" id="credit_limit" class="form-control "
                    value="<?=set_value('credit_limit', $conditions['credit_limit']);?>" data-toggle="tooltip"
                    title="<?=lang('Credit Limit');?>" >

                    <span class="errors"><?php echo form_error('credit_limit'); ?></span>
                    <span id="error-credit_limit" class="errors"></span>
                </div> <!-- input credit_limit (required) }}}4 -->
                <!-- input available_credit {{{4 -->
                <div class="col-md-3 fields">
                    <label for="available_credit">
                        <font style="color:red;">*</font>
                        <?=lang('Available Credit');?>
                    </label>

                    <div><?php echo $conditions['available_credit'];?>
                    <a href="javascript:void(0)" class="btn btn-primary btn-xs"
                            onclick="agent_adjust_credit('<?=$agent_id?>')">
                            <?=lang('Adjust Credit')?>
                        </a>
                    </div>

                    <span class="errors"><?php echo form_error('available_credit'); ?></span>
                    <span id="error-available_credit" class="errors"></span>
                </div>
                <!-- input available_credit (required) }}}4 -->
                <!-- input note {{{4 -->
                <div class="col-md-6 form-group">
                    <label for="note">
                        <font style="color:red;">*</font>
                        <?=lang('Note');?>
                    </label>

                    <textarea name="note" id="note" class="form-control" maxlength="100"><?=set_value('note', $conditions['note']);?></textarea>

                    <span class="errors"><?php echo form_error('note'); ?></span>
                    <span id="error-note" class="errors"></span>
                </div>
                <!-- input note (required) }}}4 -->
            </div>
            <!-- Basic Info }}}3 -->
            <!-- fieldset commission-setting {{{3 -->
            <?php include APPPATH . 'views/includes/agent_commission_setting.php'; ?>
            <!-- fieldset commission-setting }}}3 -->
            <!-- fieldset permission-setting {{{3 -->
            <div class="col-md-12">
                <label for="permission-setting">
                    <?=lang('Permission Setting');?>
                </label>
                <fieldset style='margin-bottom: 6px;'>
                    <!-- checkboxes for agent type {{{4 -->
                    <div class="col-md-6">
                        <label>
                            <input type="checkbox" name="agent_type[]" value="can-have-sub-agents"
                            <?php echo isset($conditions['can_have_sub_agent']) && $conditions['can_have_sub_agent'] ?'checked':''; ?>>
                            <?=lang('Can Have sub-agent');?>
                        </label>
                        <br>
                        <label>
                            <input type="checkbox" name="agent_type[]" value="can-have-players"
                            <?php echo isset($conditions['can_have_players']) && $conditions['can_have_players'] ?'checked':''; ?>>
                            <?=lang('Can Have Players');?>
                        </label>
                        <?php if ($this->agency_model->get_agent_by_id($this->session->userdata('agent_id'))['show_bet_limit_template']): ?>
                        <br>
                        <label>
                            <input type="checkbox" name="agent_type[]" value="show-bet-limit-template"
                            <?php echo isset($conditions['show_bet_limit_template']) && $conditions['show_bet_limit_template'] ?'checked':''; ?>>
                            <?=lang('Show Bet Limit Template');?>
                        </label>
                        <?php endif ?>
                        <?php //if ($this->agency_model->get_agent_by_id($this->session->userdata('agent_id'))['show_rolling_commission']): ?>
                        <br>
                        <label>
                            <input type="checkbox" name="agent_type[]" value="show-rolling-commission"
                            <?php echo isset($conditions['show_rolling_commission']) && $conditions['show_rolling_commission'] ?'checked':''; ?>>
                            <?=lang('Show Rolling Commission');?>
                        </label>
                        <?php //endif ?>
                        <br>
                        <label><input type="checkbox" name="agent_type[]" value="can-view-agents-list-and-players-list"
                        <?=isset($conditions['can_view_agents_list_and_players_list']) && $conditions['can_view_agents_list_and_players_list'] ? 'checked' : ''?>>
                        <?=lang('Can View Agents List and Players List');?>
                        </label>
                    </div> <!-- checkboxes }}}4 -->
                </fieldset>
            </div> <!-- fieldset permission-setting }}}3 -->
            <!-- fieldset vip_level {{{3 -->
            <div class="col-md-12">
                <label for="vip_level">
                    <font style="color:red;">*</font>
                    <?=lang('VIP Level Setting (Choose at least one or more VIP level)');?>
                </label>
                <fieldset>
                    <!-- vip_level tree {{{4 -->
                    <div class="col-md-6 player_vip_level_tree">
                        <input type="hidden" id="selected_vip_levels" name="selected_vip_levels" value="">
                        <div class="row">
                            <div id="vip_level_tree" class="col-xs-12">
                            </div>
                        </div>
                        <span class="errors"><?php echo form_error('player_vip_levels'); ?></span>
                        <span id="error-player_vip_levels" class="errors"></span>
                    </div> <!-- vip_level tree }}}4 -->
                </fieldset>
            </div> <!-- fieldset vip_level }}}3 -->
            <!-- fieldset settlement-setting {{{3 -->
            <div class="col-md-12">
                <label for="settlement-setting">
                    <font style="color:red;">*</font>
                    <?=lang('Settlement Setting');?>
                </label>
                <fieldset>
                    <br>
                    <!-- checkboxes for settlement period {{{4 -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="input-group pull-right">
                                    <?=lang('Settlement Period');?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <?php include __DIR__.'/../includes/agency_form_settlement.php'; ?>
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
                                <div class="input-group">
                                    <div class="col-md-3">
                                        <input type="radio" name="start_day" value="Monday"
                                        <?=$conditions['start_day'] == 'Monday'?'checked':''?>>
                                        <?=lang('Monday');?>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="radio" name="start_day" value="Tuesday"
                                        <?=$conditions['start_day'] == 'Tuesday'?'checked':''?>>
                                        <?=lang('Tuesday');?>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="radio" name="start_day" value="Wednesday"
                                        <?=$conditions['start_day'] == 'Wednesday'?'checked':''?>>
                                        <?=lang('Wednesday');?>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="radio" name="start_day" value="Thursday"
                                        <?=$conditions['start_day'] == 'Thursday'?'checked':''?>>
                                        <?=lang('Thursday');?>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="radio" name="start_day" value="Friday"
                                        <?=$conditions['start_day'] == 'Friday'?'checked':''?>>
                                        <?=lang('Friday');?>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="radio" name="start_day" value="Saturday"
                                        <?=$conditions['start_day'] == 'Saturday'?'checked':''?>>
                                        <?=lang('Saturday');?>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="radio" name="start_day" value="Sunday"
                                        <?=$conditions['start_day'] == 'Sunday'?'checked':''?>>
                                        <?=lang('Sunday');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- radioes for weekly_start_day  }}}4 -->
                    <div class="row">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-9">
                            <span class="errors"><?php echo form_error('agent_settlement_period'); ?></span>
                            <span id="error-agent_settlement_period" class="errors"></span>
                        </div>
                    </div>
                </fieldset>
            </div> <!-- fieldset settlement-setting }}}3 -->
            <!-- button row {{{3 -->
            <div class="row">
                <div class="col-md-5 col-lg-5" style="padding: 10px;">
                </div>
                <div class="col-md-6 col-lg-6" style="padding: 10px;">
                    <?php $reset_url=site_url('agency/edit_agent/'.$agent_id);?>
                    <?php $return_url=site_url('agency/sub_agents_list');?>
                    <input type="button" class="btn btn-warning btn-sm" value="<?=lang('Return');?>"
                    onclick="window.location.href='<?php echo $return_url; ?>'">
                    <input type="button" id="cancel" class="btn btn-default btn-sm" value="<?=lang('lang.reset');?>"
                    onclick="window.location.href='<?php echo $reset_url; ?>'">
                    <input type="submit" id="submit" class="btn btn-sm btn-primary" value="<?=lang('Save');?>" />
                </div>
            </div>
            <!-- button row }}}3 -->
        </div> <!-- panel body of edit-agent  }}}2 -->
    </div>
</form> <!-- end of form edit-agent }}}1 -->

<?php
$vip_levels_str = str_replace(' ', '', $conditions['vip_levels']);
$vip_levels_str = str_replace(',', '_', $vip_levels_str);
?>
<script>
$(document).ready(function(){
    //var ajax_url = "<?=site_url('agency/structure_validation_ajax')?>";
    var ajax_url = "<?=site_url('agency/edit_agent_validation_ajax')?>";
    var labels = '<?=json_encode($labels)?>';
    var fields = '<?=json_encode($fields)?>';
    //agency_form_validation(ajax_url, fields, labels);
    agency_form_validation_edit(ajax_url, fields, labels);

    var data_url = '<?= site_url('api/get_player_vip_level_tree/'. $vip_levels_str); ?>';
    set_player_vip_level_tree(data_url);

    var agent_level = $('#agent_level').val();
    if (agent_level != '') {
        $('#currency').prop('disabled', true);
    } else {
        $('#currency').prop('disabled', false);
    }
});
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of edit_agent.php
