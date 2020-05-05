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
<form method="POST" id="edit-agent-form" 
    action="<?=site_url('agency/verify_update_agent')?>" accept-charset="utf-8">
    <input type="hidden" name="parent_id" value="<?=$parent_id?>" />
    <input type="hidden" name="agent_id" value="<?=$agent_id?>" />
    <div class="panel panel-primary ">
        <!-- panel heading of edit-agent {{{2 -->    
        <div class="panel-heading">
            <h4 class="panel-title pull-left">
                <i class="glyphicon glyphicon-list-alt"></i> 
                <?=lang('Create Agent');?> 
            </h4>
            <div class="pull-right">Fields with (<font style="color:red;">*</font>) are required.</div>
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
                        <?=lang('Agent Name');?>
                    </label>

                    <input type="text" name="agent_name" id="agent_name" class="form-control " 
                    value="<?=$conditions['agent_name'];?>" data-toggle="tooltip" 
                    title="<?=lang('Agent Name');?>">

                    <span class="errors"><?php echo form_error('agent_name'); ?></span>
                    <span id="error-agent_name" class="errors"></span>
                </div> <!-- input agent_name (required) }}}4 -->
                <!-- select currency {{{4 -->
                <div class="col-md-3 col-lg-3">
                    <label class="control-label">
                        <font style="color:red;">*</font> 
                        <?=lang('Currency');?>
                    </label>
                    <select name="currency" id="currency" class="form-control input-sm"
                        title="<?=lang('Currency')?>">
                        <option value="" <?=empty($conditions['currency']) ? 'selected' : ''?>>
                        --  <?=lang('None');?> --
                        </option>
                        <option value="CNY" <?=($conditions['currency'] == "CNY") ? 'selected' : ''?> >
                        <?=lang('CNY');?>
                        </option>
                        <option value="USD" <?=($conditions['currency'] == "USD") ? 'selected' : ''?> >
                        <?=lang('USD');?>
                        </option>
                        <option value="EUR" <?=($conditions['currency'] == "EUR") ? 'selected' : ''?> >
                        <?=lang('EUR');?>
                        </option>
                        <option value="GBP" <?=($conditions['currency'] == "GBP") ? 'selected' : ''?> >
                        <?=lang('GBP');?>
                        </option>
                    </select>
                    <span class="errors"><?php echo form_error('currency'); ?></span>
                    <span id="error-currency" class="errors"></span>
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
            </div>
            <div class="col-md-12">
                <!-- input credit_limit {{{4 -->
                <div class="col-md-3 fields">
                    <label for="credit_limit">
                        <font style="color:red;">*</font> 
                        <?=lang('Credit Limit');?>
                    </label>

                    <input type="text" name="credit_limit" id="credit_limit" class="form-control " 
                    value="<?=set_value('credit_limit', $conditions['credit_limit']);?>" data-toggle="tooltip" 
                    title="<?=lang('Credit Limit');?>" readonly="true">

                    <span class="errors"><?php echo form_error('credit_limit'); ?></span>
                    <span id="error-credit_limit" class="errors"></span>
                </div> <!-- input credit_limit (required) }}}4 -->
                <!-- input available_credit {{{4 -->
                <div class="col-md-3 fields">
                    <label for="available_credit">
                        <font style="color:red;">*</font> 
                        <?=lang('Available Credit');?>
                    </label>

                    <input type="text" name="available_credit" id="available_credit" class="form-control " 
                    value="<?=set_value('available_credit', $conditions['available_credit']);?>" data-toggle="tooltip" 
                    title="<?=lang('Available Credit');?>">

                    <span class="errors"><?php echo form_error('available_credit'); ?></span>
                    <span id="error-available_credit" class="errors"></span>
                </div>
                <!-- input available_credit (required) }}}4 -->
            </div>
            <!-- Basic Info }}}3 -->    
            <!-- fieldset commission-setting {{{3 -->
            <div class="col-md-12">
                <label for="commission-setting">
                    <font style="color:red;">*</font> 
                    <?=lang('Commission Setting');?>
                </label>
                <fieldset>
                    <br>
                    <!-- input number rev_share (required) {{{4 -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?=lang('Rev Share');?>
                                </div>
                                <input type="number" class="form-control" name="rev_share" 
                                value="<?=set_value('rev_share',$conditions['rev_share'])?>"
                                title="<?=lang('Input a number between 0~90')?>"/>
                                <div class="input-group-addon">%</div>
                            </div>
                        </div>
                    </div> <!-- input number rev_share (required) }}}4 -->
                    <!-- input number Rolling Comm (required) {{{4 -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?=lang('Rolling Comm');?>
                                </div>
                                <input type="number" class="form-control" name="rolling_comm" 
                                value="<?=set_value('rolling_comm', $conditions['rolling_comm'])?>"
                                title="<?=lang('Input a number between 0~3')?>" />
                                <div class="input-group-addon">%</div>
                            </div>
                        </div>
                    </div> <!-- input number Rolling Comm (required) }}}4 -->
                    <!-- Select Rolling Comm Basis (required) {{{4 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?=lang('Rolling Comm Basis');?>
                                </div>
                                <select name="rolling_comm_basis" id="rolling_comm_basis" 
                                    class="form-control input-sm" 
                                    title="<?=lang('Select Rolling Comm Basis')?>"
                                    onclick="setDisplayTotalBetsExcept()">
                                    <option value="" <?=empty($conditions['rolling_comm_basis'])? 'selected':''?>>
                                    --  <?=lang('None');?> --
                                    </option>
                                    <option id="basis_total_bets" value="total_bets" 
                                    <?=($conditions['rolling_comm_basis'] == 'total_bets')?'selected':''?> >
                                    <?=lang('Total Bets');?>
                                    </option>
                                    <option value="total_lost_bets" 
                                    <?=($conditions['rolling_comm_basis'] == 'total_lost_bets')?'selected':''?> >
                                    <?=lang('Total Lost Bets');?>
                                    </option>
                                    <option value="total_bets_except_tie_bets" 
                                    <?=($conditions['rolling_comm_basis'] == 'total_bets_except_tie_bets')?'selected':''?> >
                                    <?=lang('Total Bets Except Tie Bets');?>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div> <!-- Select Rolling Comm Basis (required) }}}4 -->
                    <!-- Select game type (display only when 'Total Bets' is selected) {{{4 -->
                    <div class="col-md-6" id="total_bets_except">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?=lang('Except Game Type');?>
                                </div>
                                <select name="except_game_type" id="except_game_type" 
                                    class="form-control input-sm" 
                                    title="<?=lang('Select a game type')?>">
                                    <option value="" <?=($conditions['total_bets_except'] == '')?'selected':''?> >
                                    --  <?=lang('None');?> --
                                    </option>
                                    <?php for($i = 0; $i < count($game_types); $i++) { ?>
                                    <option value="<?=$game_types[$i]['game_type']?>" 
                                    <?=($conditions['total_bets_except'] == $game_types[$i]['game_type'])?'selected':''?> >
                                    <?=lang($game_types[$i]['game_type_lang']);?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div> <!-- Select Rolling Comm Basis (required) }}}4 -->
                </fieldset>
            </div> <!-- fieldset commission-setting }}}3 -->
            <!-- fieldset permission-setting {{{3 -->
            <div class="col-md-12">
                <label for="permission-setting">
                    <?=lang('Permission Setting');?>
                </label>
                <fieldset>
                    <br>
                    <!-- input agent_level {{{4 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?=lang('Agent Level');?>
                                </div>
                                <input type="text" name="agent_level" id="agent_level" 
                                class="form-control " value="<?=set_value('agent_level', $conditions['agent_level']);?>" 
                                data-toggle="tooltip" title="<?=lang('Allowed Level');?>">
                            </div>
                        </div>
                    </div>
                    <!-- input agent_level  }}}4 -->
                    <!-- checkboxes for agent type {{{4 -->
                    <div class="col-md-6">
                        <input type="checkbox" name="agent_type[]" value="can-have-sub-agents" 
                        <?php echo ($conditions['can_have_sub_agent'])?'checked':''; ?>>
                        <?=lang('Can Have sub-agent');?>
                        <br>
                        <input type="checkbox" name="agent_type[]" value="can-have-players" 
                        <?php echo ($conditions['can_have_players'])?'checked':''; ?>>
                        <?=lang('Can Have Players');?>
                    </div> <!-- checkboxes }}}4 -->
                    <?php $level_id = 'allowed_level_' . $conditions['agent_level']; ?>
                    <div class="col-md-12" id="<?=$level_id?>" name="<?=$level_id?>" >
                        <?php echo 'Level '.$conditions['agent_level'].': '. $conditions['agent_level_name'];?>
                    </div> 
                </fieldset>
            </div> <!-- fieldset permission-setting }}}3 -->
            <!-- fieldset vip_level {{{3 -->
            <div class="col-md-12">
                <label for="vip_level">
                    <?=lang('VIP Level Setting (Choose at least one or more VIP level)');?>
                </label>
                <fieldset>
                    <br>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <?=lang('VIP Level');?>
                                </div>
                                <select name="vip_level" id="vip_level" multiple="multiple"
                                    class="form-control input-sm">
                                    <option value="" <?=set_select('vip_level','',TRUE)?>>
                                    --  <?=lang('None');?> --
                                    </option>
                                    <?php foreach ($vipgrouplist as $key => $value) {?>
                                    <option value="<?=$value['vipsettingId']?>" 
                                    <?=$conditions['vip_level'] == $value['vipsettingId'] ? 'selected' : ''?>>
                                    <?=$value['groupName']?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
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
                    <!-- checkboxes for weekly_start_day (only shown when Weekly is selected) {{{4 -->
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
                                        <input type="checkbox" name="start_day[]" value="Monday" 
                                        <?=in_array('Monday', $conditions['start_day'])?'checked':''?>>
                                        <?=lang('Monday');?>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="checkbox" name="start_day[]" value="Tuesday" 
                                        <?=in_array('Tuesday', $conditions['start_day'])?'checked':''?>>
                                        <?=lang('Tuesday');?>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="checkbox" name="start_day[]" value="Wednesday" 
                                        <?=in_array('Wednesday', $conditions['start_day'])?'checked':''?>>
                                        <?=lang('Wednesday');?>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="checkbox" name="start_day[]" value="Thursday" 
                                        <?=in_array('Thursday', $conditions['start_day'])?'checked':''?>>
                                        <?=lang('Thursday');?>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="checkbox" name="start_day[]" value="Friday" 
                                        <?=in_array('Friday', $conditions['start_day'])?'checked':''?>>
                                        <?=lang('Friday');?>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="checkbox" name="start_day[]" value="Saturday" 
                                        <?=in_array('Saturday', $conditions['start_day'])?'checked':''?>>
                                        <?=lang('Saturday');?>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="checkbox" name="start_day[]" value="Sunday" 
                                        <?=in_array('Sunday', $conditions['start_day'])?'checked':''?>>
                                        <?=lang('Sunday');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- checkboxes for weekly_start_day  }}}4 -->
                </fieldset>
            </div> <!-- fieldset settlement-setting }}}3 -->
            <!-- button row {{{3 -->
            <div class="row">
                <div class="col-md-5 col-lg-5" style="padding: 10px;">
                </div>
                <div class="col-md-6 col-lg-6" style="padding: 10px;">
                    <?php $reset_url=site_url('agency/edit_agent/'.$agent_id);?>
                    <input type="button" class="btn btn-default btn-sm" value="<?=lang('lang.reset');?>" 
                    onclick="window.location.href='<?php echo $reset_url; ?>'">
                    <input type="submit" class="btn btn-sm btn-primary" value="<?=lang('Save');?>" />
                </div>
            </div>
            <!-- button row }}}3 -->
        </div> <!-- panel body of edit-agent  }}}2 -->    
    </div>
</form> <!-- end of form edit-agent }}}1 -->    

<script>
$(document).ready(function(){
    //ajax_url = "<?=site_url('agency/structure_validation_ajax')?>";
    var ajax_url = "<?=site_url('agency/edit_agent_validation_ajax')?>";
    labels = '<?=json_encode($labels)?>';
    fields = '<?=json_encode($fields)?>';
    //agency_form_validation(ajax_url, fields, labels);
    agency_form_validation_edit(ajax_url, fields, labels);
});
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of edit_agent.php
