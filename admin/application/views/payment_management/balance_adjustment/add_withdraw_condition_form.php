<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><span class="text-danger" style="font-weight: bold;"><?=$platform_name?></span></h4>
    </div>
    <div class="panel-body">

            <form class="form-horizontal" action="<?php echo site_url('/payment_management/add_withdraw_condition_post/'.$player_id); ?>" method="post">
            <?=$double_submit_hidden_field?>
            <div class="form-group">
                <label class="col-md-3"><?=lang('pay.transactionType')?></label>
                <div class="col-md-9">
                    <span class="bg-danger"><?=lang('Add Withdraw Condition')?></span>
                    <small class="text-danger"><?php echo lang('Only withdraw condtion, no bonus');?></small>
                </div>
            </div>

            <?php if (!$player_id): ?>
                <div class="form-group">
                    <label class="col-md-3"><?php echo lang('Usernames'); ?></label>
                    <div class="col-md-9">
                        <input type="file" name="usernames" class="form-control" required="required" />
                    </div>
                </div>
            <?php endif?>

            <div class="form-group">
                <label class="col-md-3"><?=lang('Bonus Amount')?></label>
                <div class="col-md-6">
                    <input type="number" class="form-control" name="amount" id="txtBonusAmount" step='any' min="0.01" required="required">
                </div>
            </div>

                <div class="form-group">
                    <label class="col-md-3"><?=lang('pay.withdrawalCondition')?></label>
                    <div class="col-md-9 form-inline">
                        <input type="number" class="form-control" name="depositAmtCondition" placeHolder="<?=lang('cms.enterdepamt')?>" step='any' min="0" required="required">
                        <input type="number" class="form-control" name="betTimes" step='any' min="0" required="required" style="width:20%">
                        <label><?=lang('operator.times')?></label>
                    </div>


                </div>
                <div class="form-group">
                    <label class="col-md-3"><?php echo lang('Options'); ?></label>
                        <div class="col-md-9 form-inline">
                            <ul>
                            <li>
                            <input type="radio" value="0" name="adjustWithdrawCondType" required>
                            <?php echo lang('Bonus')?> + <?php echo lang('Deposit');?>
                            </li>
                            <li>
                            <input type="radio" value="1" name="adjustWithdrawCondType" required>
                            <?=lang('payment.deductDeposit')?>
                            </li>
                            <li>
                            <input type="radio" value="2" name="adjustWithdrawCondType" required>
                            <?=lang('Only Calculate Deposit')?>
                            </li>
                            <li>
                            <input type="radio" value="3" name="adjustWithdrawCondType" required>
                            <?= lang('Cashback Withdrawal Condition (no promo)')?>
                            </li>
                            </ul>
                        </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3"><?=lang('cms.06');?></label>
                        <div class="col-md-9 form-inline">
                            <select class="form-control" name="promoCmsSettingId" required>
                                <?php
								if (!empty($promoCmsSettings)) {
								        foreach ($promoCmsSettings as $val) {?>
                                    <option value="<?=$val['promoCmsSettingId']?>"><?=$val['promoName']?></option>
                                <?php }
    							}?>
                            </select>
                        </div>
                </div>

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
                        <?php } ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3"><?php echo lang('Date') ?></label>
                    <div class="col-md-9">
                    <input type="text" name="make_up_date" value="<?php echo $this->utils->getNowForMysql(); ?>" class="form-control dateInput" data-time='true'>
                    </div>
                </div>

            <div class="form-group">
                <div class="col-md-offset-3 col-md-9">
                    <button type="submit" class="btn btn-primary" onclick="return confirm('<?=lang('balanceadjustment.confirm.adjustbalance')?>')"><?=lang('lang.submit')?></button>
                    <button type='reset' class="btn btn-default"><?=lang('lang.reset')?></button>
                </div>
            </div>

        </form>
    </div>
    <div class="panel-footer"></div>
</div>
<script type="text/javascript">

    $('.dateInput').each( function() {
        initDateInput($(this));
    });

    var promoSettingId = $('select[name="promoCmsSettingId"]');
    var bonusAmount = $('#txtBonusAmount');

    $(document).ready(function(){
        $('input[name="adjustWithdrawCondType"]').on('click', function(){
            promoSettingId.prop("disabled", false);
            bonusAmount.prop("disabled", false);

            if($(this).val() == 3) {
                promoSettingId.prop("disabled", true);
            }

            if($(this).val() == 2) {
                promoSettingId.prop("disabled", true);
                bonusAmount.prop("disabled", true);
                bonusAmount.val("");
            }
        });
    });

</script>
