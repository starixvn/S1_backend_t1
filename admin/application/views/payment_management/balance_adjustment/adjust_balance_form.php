<?=$this->load->view("resources/third_party/bootstrap-tagsinput")?>
<div >
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><span class="text-danger" style="font-weight: bold;"><?=$platform_name?></span></h4>
    </div>
    <div class="panel-body">

        <?php if ($player_id): ?>
            <form class="form-horizontal" action="/payment_management/adjust_balance_post/<?=implode('/', array($platform_id, $transaction_type, $player_id))?>" method="post" onsubmit="return submitForm();">
        <?php else: ?>
            <form class="form-horizontal" action="/payment_management/doBatchBalanceAdjustment/<?=implode('/', array($platform_id, $transaction_type))?>" method="post" accept-charset="utf-8" enctype="multipart/form-data" onsubmit="return submitForm();">
                <div class="form-group">
                    <label class="col-md-5"><?php echo lang("hint.download_sample");?> <a href="/sample_usernames.csv" target="_blank"><?php echo lang("reg.clickHere");?></a></label>
                </div>
        <?php endif?>
            <?=$double_submit_hidden_field?>
            <div class="form-group">
                <label class="col-md-3"><?=lang('pay.transactionType')?></label>
                <div class="col-md-9">
                    <span class="bg-danger"><?=lang('transaction.transaction.type.' . $transaction_type)?></span>
                </div>
            </div>
            <?php if ($transaction_type == Transactions::AUTO_ADD_CASHBACK_TO_BALANCE): ?>
            <div class="form-group">
                <div class="col-md-3"></div>
                <div class="col-md-9">
                    <label>
                        <input type="checkbox" name="generate_withdrawal_condition" value="1" checked>
                        <span class=""><?=lang('Generate withdrawal condition')?></span>
                    </label>
                </div>
            </div>
            <?php endif ?>

            <?php if (!$player_id): ?>
                <div class="form-group">
                    <label class="col-md-3"><?php echo lang('Usernames'); ?></label>
                    <div class="col-md-9">
                        <div class="">
                            <input type="file" name="usernames" class="form-control input-sm" required="required" accept=".csv"/>
                        </div>
                    </div>
                </div>
            <?php endif?>

            <?php if ($this->utils->isEnabledFeature('enable_adjustment_category') && $transaction_type != Transactions::SUBTRACT_BONUS) : ?>
                <div class="form-group">
                    <label class="col-md-3"><?=lang('Adjustment Category');?></label>
                    <div class="col-md-9 form-inline">
                        <select class="form-control" name="adjustment_category_id" required="required">
                        <option value=""><?php echo lang("None");?></option>
                        <?php if(!empty($adjustmentCategory)): ?>
                            <?php foreach ($adjustmentCategory as $key => $value): ?>
                                  <option value="<?= $value['id']; ?>"><?= lang($value['category_name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif;?>
                        </select>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($transaction_type == Transactions::MANUAL_SUBTRACT_BALANCE && !$this->utils->isEnabledFeature('enable_adjustment_category') ) { ?>
            <div class="form-group">
                <label class="col-md-3"><?=lang('player.tp04')?></label>
                <div class="col-md-9">
                    <input type="hidden" id="manual_subtract_balance_tag_id" name="manual_subtract_balance_tag_id" sbe-ui-toogle="tagsinput" data-freeInput="false" />
                    <div style="margin-top: 5px;">
                        <select id="tag-options" class="form-control input-sm user-success">
                            <option value=""><?=lang('select.empty.line');?></option>
                            <?php foreach($manual_subtract_balance_tags as $tag) { ?>
                            <option value="<?=$tag['id']?>"><?=lang($tag['adjust_tag_name'])?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php } ?>

            <div class="form-group">
                <label class="col-md-3"><?=lang('pay.amt')?></label>
                <div class="col-md-9">
                    <input type="number" class="form-control" name="amount" step='any' min="0.01" required="required">
                </div>
            </div>

            <?php if ($transaction_type == Transactions::ADD_BONUS):?>
               <div class="form-group">
                <label class="col-md-3"><?=lang('pay.withdrawalCondition')?></label>
                <div class="col-md-9 form-inline">
                    <input type="number" class="form-control" name="depositAmtCondition" placeHolder="<?=lang('cms.enterdepamt')?>" step='any' min="0" required="required">
                    <input type="number" class="form-control" name="betTimes" step='any' min="0" required="required" style="width:20%">
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
            <?php endif;?>
             <?php if (!$this->utils->isEnabledFeature('enable_adjustment_category') || $transaction_type == Transactions::SUBTRACT_BONUS) : ?>
                <div class="form-group">
                    <label class="col-md-3"><?=lang('cms.06');?></label>
                    <div class="col-md-9">
                        <select class="form-control" name="promo_cms_id">
                            <option value=""><?php echo lang("None");?></option>
                        <?php foreach ($promoCms as $v): ?>

                              <option value="<?php echo $v['promoCmsSettingId']; ?>"><?php echo $v['promoName'] ?></option>

                        <?php endforeach; ?>

                        </select>
                    </div>
                </div>
            <?php endif; ?>
             <?php if ($transaction_type == Transactions::ADD_BONUS):?>
                <div class="form-group">
                    <label class="col-md-3"><?=lang('Status')?></label>
                    <div class="col-md-9 form-inline">
                        <label class="radio-inline"><input type="radio" name="status" value="0" checked="checked" required="required" aria-required="true"><?=lang('sale_orders.status.3')?></label>
                        <label class="radio-inline"><input type="radio" name="status" value="1" required="required" aria-required="true"><?=lang('sale_orders.status.5')?></label>
                    </div>
                    </div>
              <?php endif;?>

              <div class="form-group">
                <label class="col-md-3"><?=lang('pay.reason')?></label>
                <div class="col-md-9">
                    <textarea name="reason" class="form-control" rows="5" required="required"></textarea>
                    <?php if ($platform_id != 0) {?>
                    <div class="checkbox" id="show_in_front_end">
                        <label>
                            <input type="checkbox" value="1" name="show_in_front_end"> <?=lang('pay.showtoplayr')?>
                        </label>
                    </div>
                    <?php }
                    ?>
                </div>
            </div>
                <?php if ($platform_id != 0 && $this->permissions->checkPermissions('make_up_transfer_record')) {?>
                <div class="form-group">
                    <label class="col-md-12 text-danger">
                    <input type="checkbox" name="make_up_only" value="true">
                    <?php echo lang('Make up only'); ?> (<?php echo lang('only fix record, not really transfer'); ?>)
                    </label>
                    <label class="col-md-12 text-danger">
                    <input type="checkbox" name="really_fix_balance" value="true">
                    <?php echo lang('Really fix balance'); ?> (<?php echo lang('should really add balance to main wallet or sub wallet(depends deposit or withdraw) if you don\'t add balance yet'); ?>)
                    </label>
                </div>
                <div class="form-group">
                    <label class="col-md-3"><?php echo lang('Date') ?></label>
                    <div class="col-md-9">
                    <input type="text" name="make_up_date" value="<?php echo $this->utils->getNowForMysql(); ?>" class="form-control dateInput" data-time='true'>
                    </div>
                </div>
                <?php }?>

            <div class="form-group">
                <div class="col-md-offset-3 col-md-9">
                    <div role="toolbar" class="btn-toolbar">
                        <button type='reset' class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default' ?>"><?=lang('lang.reset')?></button>
                        <button type="submit" class="btn btn_submit <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary' ?>"><?=lang('lang.submit')?></button>
                    </div>
                </div>
            </div>

        </form>
    </div> <!-- EOF .panel-body -->
    <div class="panel-footer hidden"></div>
</div>


</div>
<script type="text/javascript">

$('.dateInput').each( function() {
    initDateInput($(this));
});

function submitForm(){
    if(confirm('<?=lang('balanceadjustment.confirm.adjustbalance')?>')){
        $('.btn_submit').prop('disabled',true);
        return true;

    }
    return false;
}

$(document).ready(function(){
    $(document).find('#tag-options').on('change', function(){
        var option = $(this).find('option:selected');
        //alert(JSON.stringify(option));
        if ( option.val() ){
            $(document).find('#manual_subtract_balance_tag_id').tagsinput('add', {
                                                        id: option.val()
                                                        , text: option.text()
                                                        , 'extra-class':'extra-tag'
                                                    });
        }
        $(this).val('');
    });

    $("textarea[name='reason']").on('keyup', function(e) {
        console.log(this.value);

        var reason = this.value;
        console.log(reason.length);
        if (reason.length > 120) {
            console.log(reason.length);
            $("textarea[name='reason']").val(reason.substring(0, 120));
        }
    })
});
</script>
