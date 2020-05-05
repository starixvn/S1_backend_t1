
<div class="container">

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><span style="font-weight: bold;"><?php echo lang('Add Bonus');?></span></h4>
    </div>
    <div class="panel-body">

            <form class="form-horizontal" action="<?php echo site_url('/marketing_management/post_manually_add_bonus');?>" method="post" id="manual_add_bonus">
            <input type="hidden" name="player_id" value="<?php echo $player_id;?>">
            <?=$double_submit_hidden_field?>

            <div class="form-group">
                <label class="col-md-3"><?=lang('Username')?></label>
                <div class="col-md-3">
                    <input type="text" name="username" class="form-control" <?php if($player_id){?>disabled="disabled"<?php }?> required="required" value="<?php echo $username; ?>"/>
                </div>
            </div>

            <div class="form-group required">
                <label class="col-md-3"><?=lang('pay.withdrawalCondition')?></label>
                <div class="col-md-9 form-inline">
                    ( <?php echo lang('Bonus');?>
                    <input type="number" class="form-control" name="amount" step='any' min="0.01" required="required" style="width: 140px;">
                    + <?php echo lang('Deposit');?>
                    <input type="number" class="form-control" name="depositAmtCondition" step='any' min="0" required="required" style="width: 140px;">
                    ) x
                    <input type="number" class="form-control" name="betTimes" step='any' min="0" required="required" style="width: 80px;">
                    <label><?=lang('operator.times')?></label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3"></label>
                    <div class="col-md-9 form-inline">
                        <input type="checkbox" value="1" name="deductDeposit">
                        <?=lang('payment.deductDeposit')?>
                    </div>
            </div>
            <div class="form-group">
                <label class="col-md-3"><?=lang('cms.06');?></label>
                    <div class="col-md-9 form-inline">
                        <select class="form-control" name="promo_cms_id" required>
                        <?php foreach ($promoCms as $v): ?>
                                <option value="<?php echo $v['promoCmsSettingId']; ?>"><?php echo $v['promoName'] ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
            </div>
            <div class="form-group">
                <label class="col-md-3"><?=lang('Deposit');?></label>
                    <div class="col-md-9 form-inline">
                        <select class="form-control" name="transaction_id">
                            <option value=""><?=lang('NONE')?></option>
                        <?php foreach ($transaction_list as $trans): ?>
                                <option value="<?php echo $trans['id']; ?>"><?=$trans['created_at']?> <?=lang('Deposit')?> <?=$trans['amount']?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
            </div>

            <div class="form-group">
                <label class="col-md-3"><?=lang('Release Date')?></label>
                <div class="col-md-9 form-inline">
                <input type="text" name="release_date" value="<?php echo $this->utils->getNowForMysql(); ?>" class="form-control dateInput" data-time='true'>
                </div>
            </div>
            <?php if($this->permissions->checkPermissions('manually_add_bonus_with_settled_option')): ?>
            <div class="form-group">
                <label class="col-md-3"><?=lang('Status')?></label>
                <div class="col-md-9 form-inline">
                    <label class="radio-inline"><input type="radio" name="status" value="0" checked="checked"   required="required" aria-required="true"><?=lang('sale_orders.status.3')?></label>
                    <label class="radio-inline"><input type="radio" name="status" value="1" required="required"  aria-required="true"><?=lang('sale_orders.status.5')?></label>
                </div>
            </div>
            <?php else: ?>
            <div class="form-group">
                <label class="col-md-3"><?=lang('Status')?></label>
                <div class="col-md-9 form-inline">
                    <label class="radio-inline"><input type="radio" name="status" value="0" checked="checked"  disabled  required="required" aria-required="true"><?=lang('sale_orders.status.3')?></label>
                </div>
            </div>
            <?php endif; ?>
            <div class="form-group required">
                <label class="col-md-3"><?=lang('pay.reason')?></label>
                <div class="col-md-9">
                    <textarea name="reason" class="form-control" rows="5" required="required"></textarea>
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-offset-3 col-md-9">
                    <button type="button" class="btn btn-primary btn_submit" onclick="checkBonus();"><?=lang('lang.submit')?></button>
                    <button type='reset' class="btn btn-default"><?=lang('lang.reset')?></button>
                    <span class="error_msg" style="color: red;"></span>
                </div>
            </div>

        </form>
    </div>
    <div class="panel-footer"></div>
</div>

<!-- Check Condition Number Modal Start -->
<div class="modal fade" id="checkConditionNumModal" tabindex="-1" role="dialog" aria-labelledby="checkConditionNumModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="checkConditionNumModalLabel"><?=lang('Add Bonus');?></h4>
            </div>
            <div class="modal-body checkConditionNumModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm-btn" data-dismiss="modal"></button>
                <button type="button" class="btn btn-primary cancel-btn" data-dismiss="modal"></button>
            </div>
        </div>
    </div>
</div>
<!-- Check Condition Number Modal End -->

</div>
<script type="text/javascript">

$('.dateInput').each( function() {
    initDateInput($(this));
});

function convertToFloat(x) {
    return Number.parseFloat(x);
}

function checkBonus(){
    var amount = $('input[name="amount"]').val();
    var depositAmtCondition = $('input[name="depositAmtCondition"]').val();
    var betTimes = $('input[name="betTimes"]').val();
    var deductDeposit = $('input[name="deductDeposit"]').is(':checked');
    var reason = $('textarea[name="reason"]').val();
    var calculation = null;
    var condition = 0;
    var hint = null;

    if(!amount){
        $('.error_msg').html('<?=lang('hint.add_bonus.lost.bonus');?>');
        return;
    }
    if(!depositAmtCondition){
        $('.error_msg').html('<?=lang('hint.add_bonus.lost.deposit');?>');
        return;
    }
    if(!betTimes){
        $('.error_msg').html('<?=lang('hint.add_bonus.lost.multiply');?>');
        return;
    }
    if(!reason){
        $('.error_msg').html('<?=lang('hint.add_bonus.lost.reason');?>');
        return;
    }

    if(amount && depositAmtCondition && betTimes && reason){
        $('.error_msg').html('');
    }

    amount = convertToFloat(amount);
    depositAmtCondition = convertToFloat(depositAmtCondition);
    betTimes = convertToFloat(betTimes);

    if(deductDeposit){
        deductDepositTxt = '<?=lang('lang.yes');?>';
        condition = ((amount + depositAmtCondition) * betTimes) - depositAmtCondition;
        calculation = '( ' + amount + ' + ' + depositAmtCondition + ' ) x ' + betTimes + ' - ' + depositAmtCondition + ' = ' + condition;

    }else{
        deductDepositTxt = '<?=lang('lang.no');?>';
        condition = (amount + depositAmtCondition) * betTimes;
        calculation = '( ' + amount + ' + ' + depositAmtCondition + ' ) x ' + betTimes + ' = ' + condition;
    }


    if(condition >= 0){
        var hint = '<?=lang('hint.add_bonus.condition.greaterThanOrEqualToZero');?>';
        $('.confirm-btn').html('<?=lang('Confirm')?>').show();
        $('.cancel-btn').html('<?=lang('lang.cancel')?>').show();
    }else{
        var hint = '<?=lang('hint.add_bonus.condition.lessThanZero');?>';
        $('.confirm-btn').hide();
        $('.cancel-btn').html('<?=lang('lang.cancel')?>').show();
    }

    hint = hint.replace('{bonus}', amount);
    hint = hint.replace('{deposit}', depositAmtCondition);
    hint = hint.replace('{timesTotalBets}', betTimes);
    hint = hint.replace('{deductDeposit}', deductDepositTxt);
    hint = hint.replace('{wc}', calculation);

    $('.checkConditionNumModalBody').html(hint);
    $('#checkConditionNumModal').modal('show');
}


$('.confirm-btn').click(function(){
    $('#manual_add_bonus').submit();
});
</script>
