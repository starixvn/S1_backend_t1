<?php 
$level_master = '';
$admin_fee = '';
$transaction_fee = '';
$bonus_fee = '';
$cashback_fee = '';
$min_monthly_pay = '';
$monthly_payday = '';
if(!empty($operator_settings)) {
    $obj = json_decode($operator_settings); 
    if($obj != null) {
        if (isset($obj->level_master)) $level_master = $obj->level_master;
        if (isset($obj->admin_fee)) $admin_fee = $obj->admin_fee;
        if (isset($obj->transaction_fee)) $transaction_fee = $obj->transaction_fee;
        if (isset($obj->bonus_fee)) $bonus_fee = $obj->bonus_fee;
        if (isset($obj->cashback_fee)) $cashback_fee = $obj->cashback_fee;
        if (isset($obj->min_monthly_pay)) $min_monthly_pay = $obj->min_monthly_pay;
        if (isset($obj->monthly_payday)) $monthly_payday = $obj->monthly_payday;
    }
}

$total_active_players = '';
$min_betting = '';
$min_deposit = '';
$game_providers = [];
if(!empty($agent_terms) || $agent_terms != 0) {
    $obj = json_decode($agent_terms);
    if($obj != null) {
        if (isset($obj->total_active_players)) $total_active_players = $obj->total_active_players;
        if (isset($obj->min_betting)) $min_betting = $obj->min_betting;
        if (isset($obj->min_deposit)) $min_deposit = $obj->min_deposit;
        if (isset($obj->game_providers)) $game_providers = $obj->game_providers;
    }
}

$sub_level_cnt = 0;
$sub_level_shares = [];
$manual_open = false;
$sub_link = false;

$this->utils->debug_log('Sub Agent Terms', $sub_agent_terms);
if(!empty($sub_agent_terms) || $sub_agent_terms != 0) {
    $obj= json_decode($sub_agent_terms);
    $this->utils->debug_log('Sub Agent Terms', $obj);
    if($obj != null) {
        if(isset($obj->sub_level_cnt)) $sub_level_cnt = $obj->sub_level_cnt;
        if(isset($obj->sub_level_shares)) $sub_level_shares = $obj->sub_level_shares;
        if(isset($obj->manual_open)) $manual_open = true;
        if(isset($obj->sub_link)) $sub_link = true;
    }
}
?>

<!-- custom style -->
<style>
    .btn_collapse {
        margin-left: 10px;
    }
</style>

<div class="row">
    <!-- panel for Operator Settings {{{2 -->
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cog"></i> <?= lang('Operator Settings'); ?>
                </h4>
                <div class="clearfix"></div>
            </div><!-- end panel-heading -->

            <div class="panel-body collapse in" id="agency_main_panel_body">
                <div class="col-md-12">
                    <!-- operator_settings form {{{3 -->
                    <form id="form_operator" method="post">
                        <input type="hidden" name="set_type" value="operator_settings">
                        <div class="row">
                            <!-- level 0 (master) share {{{4 -->
                            <div class="col-md-12">
                                <label><b><?= lang('Default Shares Percentage'); ?></b></label>
                                <fieldset>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <?=lang('lang.level');?> 0: <?=lang('lang.master');?>
                                                    </div>
                                                    <input type="number" class="form-control" 
                                                    name="level_master" id="total_shares" 
                                                    value="<?=set_value('level_master', $level_master)?>" required />
                                                    <div class="input-group-addon">%</div>
                                                </div>
                                            </div>
                                        </div><!-- end col-md-12 -->	
                                    </div>
                                </fieldset>
                                <br>
                            </div><!-- end level 0 share }}}4 -->	
                            <!-- cost/fee settings {{{4 -->
                            <div class="col-md-12">
                                <label for="fee"><b><?=lang('earnings.cost');?></b></label>
                                <fieldset>
                                    <br>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <input type="checkbox" name="allowed_fee[]" value="admin_fee"  checked> 
                                                <?=lang('Admin Fee');?>
                                            </div>
                                            <input type="number" class="form-control" 
                                            name="admin_fee" value="<?=set_value('admin_fee', $admin_fee);?>"/>
                                            <div class="input-group-addon">%</div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <input type="checkbox" name="allowed_fee[]" 
                                                value="transaction_fee" checked> 
                                                <?=lang('Transaction Fee');?>
                                            </div>
                                            <input type="number" class="form-control" 
                                            name="transaction_fee" value="<?=set_value('transaction', $transaction_fee)?>"/>
                                            <div class="input-group-addon">%</div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <input type="checkbox" name="allowed_fee[]" value="bonus_fee" checked> 
                                                <?=lang('Bonus Fee');?>
                                            </div>
                                            <input type="number" class="form-control" name="bonus_fee" 
                                            value="<?=set_value('bonus_fee',$bonus_fee)?>" />
                                            <div class="input-group-addon">%</div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <input type="checkbox" name="allowed_fee[]" value="cashback_fee" checked> 
                                                <?=lang('Cashback Fee');?>
                                            </div>
                                            <input type="text" class="form-control" name="cashback_fee" 
                                            value="<?=set_value('cashback_fee',$cashback_fee) ?>" />
                                            <div class="input-group-addon">%</div>
                                        </div>
                                    </div>
                                </fieldset>
                                <br>
                            </div><!-- end of cost/fee settings }}}4 -->
                            <!-- Payment settings {{{4 -->
                            <div class="col-md-12">
                                <label for="fee"><b><?=lang('Payment Settings');?></b></label>
                                <fieldset>
                                    <br>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon"><?=lang('Minimum Monthly Pay');?></div>
                                            <input type="number" class="form-control" name="min_monthly_pay" 
                                            value="<?=set_value('min_monthly_pay', $min_monthly_pay) ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon"><?=lang('earnings.payDay');?></div>
                                            <select class="form-control" name="monthly_payday">
                                                <option value="0" <?php if($monthly_payday == 0)echo 'selected';?>>
                                                <?=lang('earnings.selectDay');?>
                                                </option>
                                                <?php for($i=1; $i<=31; $i++) { ?>
                                                <option value="<?=$i?>"
                                                <?php if($monthly_payday == $i) echo 'selected'; ?>>
                                                <?php echo $i; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </fieldset>
                            </div><!-- end of Payment Settings }}}4 -->
                        </div><!-- end row -->
                        <br>
                        <button type="submit" id="btn_form_operator" class="btn btn-primary pull-right">
                            <i class="fa fa-floppy-o"></i> 
                            <?=lang('sys.vu70'); ?>
                        </button>
                    </form>
                    <!-- end of operator_settings form }}}3 -->
                </div>
            </div><!-- end panel-body -->
            <!--div class="panel-footer"></div-->
        </div>  
    </div>
    <!-- end of panel for Operator Settings }}}2 -->
    <!-- agent Commission Settings {{{2 -->
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cog"></i> <?= lang('Agent Commission Settings'); ?>
                </h4>
                <div class="clearfix"></div>
            </div><!-- end panel-heading -->

            <div class="panel-body collapse in" id="agency_main_panel_body">
                <div class="col-md-12">
                    <!-- form for agent Commission Settings {{{3 -->
                    <form id="form_option_1" method="post">
                        <input type="hidden" name="set_type" value="agent_terms">
                        <div class="row">
                            <div class="col-md-12">
                                <label><b><?=lang('Agent Settings');?></b></label>
                                <fieldset>
                                    <br>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon"><?=lang('Total Active Players');?></div>
                                            <input type="number" class="form-control" name="total_active_players" 
                                            value="<?=set_value('total_active_players', $total_active_players);?>" 
                                            required />
                                            <div class="input-group-addon">#</div>
                                        </div>
                                    </div>
                                </fieldset>
                                <br>
                                <div class="form-group">
                                    <label><b><?=lang('Game Providers');?></b></label>
                                    <fieldset>
                                        <br>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <?php foreach($games as $g) { ?>
                                                <div class="col-xs-4">
                                                    <div class="form-group">
                                                        <label for="pt">
                                                            <input type="checkbox" name="game_providers[]" 
                                                            value="<?=$g['id'];?>" 
                                                            <?php if(in_array($g['id'], $game_providers))echo 'checked'?>> 
                                                            <?=$g['system_code'];?>
                                                        </label>
                                                    </div>
                                                </div>										
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <?=lang('Minimum Betting Amount');?>
                                                    </div>
                                                    <input type="number" class="form-control input-lg" 
                                                    name="min_betting" value="<?=set_value('min_betting',$min_betting)?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                    </fieldset>	
                                </div>
                                <label><b><?=lang('Minimum Deposit Per Player');?></b></label>
                                <fieldset>
                                    <br>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-money"></i></div>
                                            <input type="number" class="form-control" name="min_deposit" 
                                            value="<?=set_value('min_deposit',$min_deposit) ?>" />
                                        </div>
                                    </div>
                                </fieldset>
                            </div><!-- end col-md-6 -->
                        </div><!-- end row -->
                        <button type="submit" id="option_1_submit" class="btn btn-primary pull-right">
                            <i class="fa fa-floppy-o"></i> 
                            <?=lang('sys.vu70'); ?>
                        </button>
                    </form> <!-- end of form for agent Commission Settings }}}3 -->
                </div>
            </div><!-- end panel-body -->
            <!--div class="panel-footer"></div-->
        </div>      
    </div> <!-- agent Commission Settings }}}2 -->
    <!-- sub agent Commission Settings {{{2 -->
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cogs"></i> <?= lang('Sub Agent Commission Setting'); ?>
                </h4>
                <div class="clearfix"></div>
            </div><!-- end panel-heading -->
            <div class="panel-body collapse in" id="sub_agent_main_panel_body" >
                <!-- sub agent Commission setting form {{{3 -->
                <form id="frm_sub_option" method="post">	
                    <input type="hidden" name="set_type" value="sub_agent_terms">
                    <div class="sub-agent-options">
                        <!-- sub options {{{4
                        <div class="col-xs-12" id="btn_group_sub_allowed">
                            <div class="form-group">
                                <fieldset>
                                    <br>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="pt">
                                                <input type="checkbox" name="manual_open" value="manual" 
                                                <?php echo $manual_open?'checked':''; ?>> 
                                                <?= lang('Manual Open Sub Agents'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="pt">
                                                <input type="checkbox" name="subLink" value="link" 
                                                <?php  echo $sub_link?'checked':''; ?>> 
                                                <?= lang('Sub Agent Link'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>	
                            </div>	
                        </div>	   sub options }}}4 -->    
                    </div>
                    <div class="sub-agent-options">
                        <!-- sub_level_shares {{{4 -->
                        <div class="col-md-12">
                            <fieldset>
                                <br>
                                <div class="col-md-12">	
                                    <label id="sub_level_label"><?= lang('Sub Agent Count'); ?></label>
                                    <input id="sub_level_cnt" name="sub_level_cnt" value="<?php echo $sub_level_cnt;?>">
                                    <br>
                                    <div class="row" id="sub_level_container">
                                        <?php for($i = 0; $i < $max_sub_levels; $i++) { ?>
                                        <?php $sub_id = 'sub_level_'.$i; ?>
                                        <?php $sub_share_id = 'sub_share_'.$i; ?>
                                        <?php $sub_share = 0; ?>
                                        <?php if(isset($sub_level_shares[$i])) $sub_share = $sub_level_shares[$i]; ?>
                                        <div class="col-md-6" id="<?=$sub_id?>">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <?=lang('Level').' '. ($i + 1).':'?>:
                                                    </div>
                                                    <input type="number" class="form-control" name="sub_level_shares[]" 
                                                    id="<?=$sub_share_id?>" value="<?php echo $sub_share?>"/>
                                                    <div class="input-group-addon">%</div>
                                                </div>
                                            </div>
                                        </div><!-- end col-md-6 -->	
                                        <?php } ?>
                                    </div><!-- sub_level_container -->	
                                </div><!-- end col-md-12 -->
                            </fieldset>
                        </div> <!-- sub_level_shares }}}4 -->
                        <div class="row">
                            <div class="col-md-12">
                                <br>
                                <div class="btn-group pull-right" role="group">
                                    <button type="submit" id="sub_option_submit" class="btn btn-primary">
                                        <i class="fa fa-floppy-o"></i> <?=lang('sys.vu70'); ?></button>
                                </div>
                                <div class="clearfix"></div>	
                            </div>
                        </div>
                    </div>
                </form> <!-- sub agent Commission setting form }}}3 -->
            </div><!-- end panel-body -->
        </div>
    </div> <!-- sub agent Commission Settings }}}2 -->
</div>

<script>
$('#sub_option_submit').on('click', function(){
    $('#frm_sub_option').submit();
});

// prevent negative value
$('input[type="number"]').on('change', function(){
    if($(this).val() < 0) $(this).val(0);
});


// START DEFAULT AFFILIATE SHARES JS ===============================================

$('#form_option_1 input').on('change', function(){
    // enable save button
    $('#btn_save').prop('disabled', false);
    $('#option_1_submit').prop('disabled', false);
    // get option and enable it
    var option = $(this).closest('.panel').find('.panel-heading').find('.option').prop('checked', 'checked');
});

$('#form_option_2 input').on('change', function(){
    // enable save button
    $('#btn_save').prop('disabled', false);
    $('#option_2_submit').prop('disabled', false);

    // get option and enable it
    var option = $(this).closest('.panel').find('.panel-heading').find('.option').prop('checked', 'checked');
});

$('.btn_collapse').on('click', function(){
    // get current state
    var child = $(this).find('i');

    // change ui
    if(child.hasClass('glyphicon-chevron-down')) {
        child.removeClass('glyphicon-chevron-down');
        child.addClass('glyphicon-chevron-up') 
    } else {      
        child.removeClass('glyphicon-chevron-up');
        child.addClass('glyphicon-chevron-down') 
    }
}); 

$('#btn_save').on('click', function(){		
    // check which option is selected
    var option = $('input[name=option]:checked').val();

    switch(option) {
    case 'option1':
        $('#option_1_submit').trigger('click');
        break;
    case 'option2':
        $('#option_2_submit').trigger('click');
        break;
    }
});

// END DEFAULT AFFILIATE SHARES JS ====================================================

// START DEFAULT SUB AFFILIATES SHARES JS =============================================

var sub_level_shares = 0;

$('#sub_agent_main_panel_body input').on('change', function(){
    $('#btn_save_sub').prop("disabled", false);
});

// main option
$('#btn_sub_allow').parent().on('click', function(){
    $('#btn_group_sub_allowed').removeClass('hide');
    $('.sub-agent-options').removeClass('hide');
    $('#sub_option_submit').prop('disabled', false);
});
$('#btn_sub_anallow').parent().on('click', function(){
    $('btn_group_sub_allowed label').removeClass('active');
    $('btn_group_sub_allowed input').prop('checked', false);
    $('#btn_group_sub_allowed').addClass('hide');
    $('.sub-agent-options').addClass('hide');
    $('#sub_option_submit').prop('disabled', false);

    // $('#sub_option1_body input[type="number"]').prop('disabled', 'disabled');
    // $('#sub_option2_body input[type="number"]').prop('disabled', 'disabled');
});

// sub option
$('#btn_sub_all').parent().on('click', function(){
    $('#sub_option1_body input[type="number"]').prop('disabled', false);
    $('#sub_option2_body input[type="number"]').prop('disabled', false);
});

$('#btn_sub_manual').parent().on('click', function(){
    $('#sub_option1_body input[type="number"]').prop('disabled', false);
    // $('#sub_option2_body input').prop('disabled', 'disabled');		
});

$('#btn_sub_link').parent().on('click', function(){
    // $('#sub_option1_body input').prop('disabled', 'disabled');
    $('#sub_option2_body input[type="number"]').prop('disabled', false);		
});

// main setting
$('#sub_option1_body input').on('change', function(){
    $('#btn_save_sub').prop('disabled', false);
    $('#frm_sub_option input[type="submit"]').prop('disabled', false);
    $('#frm_sub_option input[type="hidden"]').prop('disabled', false);
});
$('#sub_option2_body input').on('change', function(){
    $('#btn_save_sub').prop('disabled', false);	
    $('#frm_sub_option input[type="submit"]').prop('disabled', false);	
    $('#frm_sub_option input[type="hidden"]').prop('disabled', false);	
});

// trigger save
$('#btn_save_sub').on('click', function(){
    $('#sub_option_submit').trigger('click');
});

// END DEFAULT SUB AFFILIATES SHARES JS ===============================================


// START OPERATOR SETTINGS ============================================================
$('#btn_form_operator').on('click', function(){
    $('#form_operator').submit();
});
$('#form_operator').on('submit', function(){
    //console.log($(this).serializeArray());
    //return false;
});

</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agency_setting.php
