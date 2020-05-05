<?php if ($this->permissions->checkPermissions('responsible_gaming_info') && $this->utils->isEnabledFeature('responsible_gaming')) { ?>
<div class="panel panel-primary" id="resp_gaming_info_form">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a href="#log_info" id="hide_resp_info" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-chevron-up" id="hide_resp_up"></i></a>
            <strong><?=lang('Responsible Gaming')?></strong>
        </h4>
    </div>
    <div class="panel panel-body" id="resp_game_panel_body" style="margin-bottom: 0;">
        <div class="col-md-6">
            <table class="table table-bordered" style="margin-bottom:0;">
                <tr>
                    <td style="width:20%">
                        <strong><?=lang('Self Exclusion, Temporary')?></strong>
                    </td>
                    <?php
                        if(isset($responsible_gaming['temp_self_exclusion']) && $responsible_gaming['temp_self_exclusion']->status != Responsible_gaming::STATUS_CANCELLED){
                            $temporaryStatus = @$statusType[$responsible_gaming['temp_self_exclusion']->status];
                            $temporaryCloseIn = lang('Temporary Close in:') . ' ' . @$responsible_gaming['temp_self_exclusion']->period_cnt . " " . $periodType[@$responsible_gaming['temp_self_exclusion']->period_type].'<br />From:' . @$responsible_gaming['temp_self_exclusion']->date_from . ' To: ' . @$responsible_gaming['temp_self_exclusion']->date_to;
                            ?>
                            <td><?=$temporaryStatus?></td>
                            <td><?=$temporaryCloseIn?></td>
                            <?php if($this->permissions->checkPermissions('cancel_responsible_gaming_request')){?>
                                <?php if($responsible_gaming['temp_self_exclusion']->status == Responsible_gaming::STATUS_COOLING_OFF && $responsible_gaming['use_uplift_instead_of_cancel_btn']):?>
                                    <td><button type="button" class="btn btn-xs btn-success pull-right rsp_cooling_off_expire_modal" data-rsp-type="<?=Responsible_gaming::SELF_EXCLUSION_TEMPORARY ?>" data-rsp-status="<?=Responsible_gaming::STATUS_COOLING_OFF?>"><?=lang('Uplift')?></button></td>
                                <?php else:?>
                                    <td><button type="button" class="btn btn-xs btn-success pull-right rsp_temp_cancel_modal" data-rsp-type="<?=Responsible_gaming::SELF_EXCLUSION_TEMPORARY ?>"><?=lang('lang.cancel')?></button></td>
                                <?php endif;?>
                            <?php }?>
                            <?php
                        }else {
                            echo "<td colspan='3'>" . lang('N/A') . "</td>";
                        }
                    ?>
                </tr>
                <tr>
                    <td style="width:20%">
                        <strong><?=lang('Self Exclusion, Permanent')?></strong>
                    </td>
                    <?php
                    if(isset($responsible_gaming['permanent_self_exclusion']) && @$responsible_gaming['permanent_self_exclusion']->status != Responsible_gaming::STATUS_CANCELLED){
                        $permanentStatus = @$statusType[$responsible_gaming['permanent_self_exclusion']->status];
                        $permanentCloseIn = lang('Permanently Closed');
                        ?>
                        <td><?=$permanentStatus?></td>
                        <td><?=$permanentCloseIn?></td>
                        <?php if(!$this->utils->isEnabledFeature('hide_permanent_self_exclusion_cancel_button') && $this->permissions->checkPermissions('cancel_responsible_gaming_request')):?>
                            <td>
                                <button type="button" class="btn btn-xs btn-success pull-right rsp_permanent_cancel_modal" data-rsp-type="<?= Responsible_gaming::SELF_EXCLUSION_PERMANENT ?>"><?= lang('lang.cancel') ?></button>
                            </td>
                        <?php endif;?>
                        <?php
                    }else {
                        echo "<td colspan='3'>" . lang('N/A') . "</td>";
                    }
                    ?>
                </tr>

                <tr>
                    <td>
                        <strong><?=lang('Cooling Off')?></strong>
                    </td>
                    <?php
                    if(isset($responsible_gaming['cool_off']) && $responsible_gaming['cool_off']->status != Responsible_gaming::STATUS_CANCELLED){
                        $coolOffStatus = @$statusType[$responsible_gaming['cool_off']->status];
                        $coolOffCloseIn = lang("Cooling off for:") . " " . @$responsible_gaming['cool_off']->period_cnt . " " . lang("day/s").'<br />From:' . @$responsible_gaming['cool_off']->date_from . ' To: ' . @$responsible_gaming['cool_off']->date_to;
                        ?>
                        <td><?=$coolOffStatus?></td>
                        <td><?=$coolOffCloseIn?></td>
                        <?php if($this->permissions->checkPermissions('cancel_responsible_gaming_request')){?>
                            <td>
                                <button type="button" class="btn btn-xs btn-success pull-right rsp_cooloff_cancel_modal" data-rsp-type="<?=Responsible_gaming::COOLING_OFF ?>"><?=lang('lang.cancel')?></button>
                            </td>
                        <?php }?>
                        <?php
                    }else {
                        echo "<td colspan='3'>" . lang('N/A') . "</td>";
                    }
                    ?>
                </tr>
                <tr>
                    <td>
                        <strong><?=lang('Deposit Limits')?></strong>
                    </td>
                    <?php
                        if(isset($responsible_gaming['deposit_limits']) && $responsible_gaming['deposit_limits']->status != Responsible_gaming::STATUS_CANCELLED){
                            $depositlimitAmount = @$responsible_gaming['deposit_limits']->amount;
                            $depositlimitPeriodCnt = @$responsible_gaming['deposit_limits']->period_cnt;
                            $depositLimitResetPeriodEnd = @$responsible_gaming['deposit_limits']->date_to;
                            $depositlimitsStatus = @$statusType[$responsible_gaming['deposit_limits']->status];
                            $depositlimitsCloseIn = lang("Deposit Limit Amount: ") . $depositlimitAmount . " for " . $depositlimitPeriodCnt . " Day";
                            ?>
                            <td><?=$depositlimitsStatus?></td>
                            <td><?=$depositlimitsCloseIn?></td>
                            <?php if($this->permissions->checkPermissions('cancel_responsible_gaming_request')){?>
                                <td><button type="button" class="btn btn-xs btn-success pull-right rsp_dptlimit_cancel_modal" data-rsp-type="<?=Responsible_gaming::DEPOSIT_LIMITS ?>"><?=lang('lang.cancel')?></button></td>
                            <?php }?>
                            <?php
                        }else {
                            echo "<td colspan='3'>" . lang('N/A') . "</td>";
                        }
                    ?>
                </tr>
                <?php if(!$disable_and_hide_wagering_limits):?>
                    <tr>
                        <td>
                            <strong><?=lang('Wagering Limits')?></strong>
                        </td>
                        <?php
                            if(isset($responsible_gaming['wagering_limits']) && $responsible_gaming['wagering_limits']->status != Responsible_gaming::STATUS_CANCELLED){
                                $wageringLimitAmount = @$responsible_gaming['wagering_limits']->amount;
                                $wageringLimitPeriodCnt = @$responsible_gaming['wagering_limits']->period_cnt;
                                $wageringLimitResetPeriodEnd = @$responsible_gaming['wagering_limits']->date_to;
                                $wageringLimitsStatus = @$statusType[$responsible_gaming['wagering_limits']->status];
                                $wageringLimitsCloseIn = lang("Wagering Limit Amount: ") . @$responsible_gaming['wagering_limits']->amount . " for " . $wageringLimitPeriodCnt . " Day";
                                ?>
                                <td><?=$wageringLimitsStatus?></td>
                                <td><?=$wageringLimitsCloseIn?></td>
                                <?php if($this->permissions->checkPermissions('cancel_responsible_gaming_request')){?>
                                    <td><button type="button" class="btn btn-xs btn-success pull-right rsp_wgrlimit_cancel_modal" data-rsp-type="<?=Responsible_gaming::WAGERING_LIMITS ?>"><?=lang('lang.cancel')?></button></td>
                                <?php }?>
                                <?php
                            }else {
                                echo "<td colspan='3'>" . lang('N/A') . "</td>";
                            }
                        ?>
                    </tr>
                <?php endif;?>
            </table>

            <p></p>
            <table  class="table table-bordered" style="margin-bottom:0;">
                <tr>
                    <td colspan="3"><strong><?=lang('Responsible Gaming')?></strong></td>
                </tr>
                <tr>
                    <td style="width:20%"><strong><?=lang('Self Exclusion, Temporary')?></strong></td>
                    <td><?php echo lang("Enter the number of months your account will be temporarily closed")?><br>
                        <?php $tempReriodList = Responsible_gaming::getTempPeriodList(); ?>
                        <select id="res_selfexclusion" style="height: 30px; width:145px;line-height: 16px;">
                            <option value="0"><?=lang("Please Select a Period")?></option>
                            <?php foreach ($tempReriodList as $days => $content) : ?>
                                <option value="<?= $days ?>"> <?= $content?> </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if(!$selfexclusionFlag):?>
                            <button id="res_selfexclusion_btn" onclick="postSelfExcsion();" class="btn  btn-primary  btn-xs"><?=lang('Send Request Now')?></button>
                        <?php endif;?>

                    </td>
                </tr>
                <tr>
                    <td style="width:20%"><strong><?=lang('Self Exclusion, Permanent')?></strong></td>
                    <td><br>
                        <?php if(!$selfexclusionFlag):?>
                            <button onclick="postSelfExcsionPermanent();" class="btn  btn-primary  btn-xs"><?=lang('Send Request Now')?></button>
                        <?php endif;?>
                    </td>
                </tr>
                <tr>
                    <td style="width:20%"><strong><?=lang('Cooling Off')?></strong></td>
                    <td>
                        <select  id="coolOffPeriodCount" style="height: 30px;width:145px;line-height: 16px;">
                            <option value="0"><?=lang("Please Select a Period")?></option>
                            <option value="1"><?=lang('24 Hours')?></option>
                            <option value="7"><?=lang('One Week')?></option>
                            <option value="30"><?=lang('One Month')?></option>
                            <option value="42"><?=lang('6 Weeks')?></option>
                        </select>
                        <?php if(!$timeoutFlag):?>
                            <button id="coolOffPeriodCountBtn" onclick="postCooloff();" class="btn  btn-primary  btn-xs"><?=lang('Send Request Now')?></button>
                        <?php endif;?>

                    </td>
                </tr>
                <tr>
                    <td style="width:20%">
                        <strong><?=lang('Deposit Limits')?></strong>
                        <?php if ($depositlimitFlag) {?>
                            <a href="javacript:void(0)" class="btn btn-xs btn-success"><?php  echo lang("Deposit Limit Active")?></a>
                        <?php }else{?>
                            <a href="javacript:void(0)" class="btn btn-xs btn-info"><?php  echo lang("Deposit Limit InActive")?></a>
                        <?php }?>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td>
                                    <?php echo lang("Set Deposit Amount"); ?>
                                </td>
                                <td>
                                    <input type="number" id="depositLimitsAmount" name="depositLimitsAmount" placeholder="<?php echo lang("Please input valid deposit amount") ?>" style="width: 50%;text-align: right;" value="<?=($depositlimitFlag) ? $depositlimitAmount : 0;?>">
                                    <input type="hidden" id="depositLimitsDefaultAmount" value="<?=($depositlimitFlag) ? $depositlimitAmount : 0;?>">
                                </td>
                            <tr>
                                <td></td>
                                <td>(>=0)</td>
                            </tr>
                            <tr>
                                <td><?php echo lang("for next "); ?></td>
                                <td>
                                    <select name="depositLimitsReactivationPeriodCnt" id="depositLimitsReactivationPeriodCnt">
                                        <?php foreach ($deposit_limits_day_options as $days):?>
                                            <?php if($depositlimitFlag):?>
                                                <option value="<?= $days ?>" <?=($days == $depositlimitPeriodCnt)?'selected':'';?>> <?= $days?> </option>
                                            <?php else:?>
                                                <option value="<?= $days ?>"> <?= $days?> </option>
                                            <?php endif;?>
                                        <?php endforeach;?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><?='('.implode('/',$deposit_limits_day_options).')'.lang(' days')?></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <?php if((!$selfexclusionFlag)&&(!$timeoutFlag)):?>
                                        <button id="depositLimitsSubmit" onclick="postDepositlimit();" class="btn  btn-primary  btn-xs"><?=($depositlimitFlag)?lang('lang.save'):lang('Send Request Now')?></button>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <?php if($depositlimitFlag):?>
                                        <span><?=lang('Current Limit :').$depositlimitAmount?></span><br>
                                        <span><?=lang('Current Amount Remaining :').$depositLimitRemainTotalAmount?></span><br>
                                        <span><?=lang('Reset Period End :').$depositLimitResetPeriodEnd?></span>
                                    <?php endif;?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php if(!$disable_and_hide_wagering_limits):?>
                <tr>
                    <td style="width:20%">
                        <strong><?=lang('Wagering Limits')?></strong>
                        <?php
                        if ($wageringLimitsFlag) {?>
                            <a href="javascript:void(0)" class="btn btn-xs btn-success"><?php  echo lang("Wagering Limit Active")?></a>
                        <?php }else{?>
                            <a href="javascript:void(0)" class="btn btn-xs btn-info"><?php  echo lang("Wagering Limit InActive")?></a>
                        <?php }?>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td>
                                    <?php echo lang("Set Wagering Limit"); ?>
                                </td>
                                <td>
                                    <input type="number" id="wageringLimitsAmount" name="wageringLimitsAmount" placeholder="<?php echo lang("Please input valid deposit amount") ?>" style="width: 50%;text-align: right;" value="<?=($wageringLimitsFlag) ? $wageringLimitAmount : 0;?>">
                                    <input type="hidden" id="wageringLimitsDefaultAmount" value="<?=($wageringLimitsFlag) ? $wageringLimitAmount : 0;?>">
                                </td>
                            <tr>
                                <td></td>
                                <td>(>=0)</td>
                            </tr>
                            <tr>
                                <td><?php echo lang("for next "); ?></td>
                                <td>
                                    <select name="wageringLimitsReactivationPeriodCnt" id="wageringLimitsReactivationPeriodCnt">
                                        <?php foreach ($wagering_limits_day_options as $days):?>
                                            <?php if($wageringLimitsFlag):?>
                                                <option value="<?= $days ?>" <?=($days == $wageringLimitPeriodCnt)?'selected':'';?>> <?= $days?> </option>
                                            <?php else:?>
                                                <option value="<?= $days ?>"> <?= $days?> </option>
                                            <?php endif;?>
                                        <?php endforeach;?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><?='('.implode('/',$wagering_limits_day_options).')'.lang(' days')?></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <?php if((!$selfexclusionFlag)&&(!$timeoutFlag)):?>
                                        <button id="wageringLimitsSubmit" onclick="postWageringlimit();" class="btn  btn-primary  btn-xs"><?=($wageringLimitsFlag)?lang("lang.save"):lang('Send Request Now')?></button>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <?php if($wageringLimitsFlag):?>
                                        <span><?=lang('Current Limit :').$wageringLimitAmount?></span><br>
                                        <span><?=lang('Current Amount Remaining :').$wageringLimitRemainTotalAmount?></span><br>
                                        <span><?=lang('Reset Period End :').$wageringLimitResetPeriodEnd?></span>
                                    <?php endif;?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php endif;?>
            </table>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>
<?php }?>

<input type="hidden" id="rsp_status">
<input type="hidden" id="rsp_type">

<script type="text/javascript">
    function postSelfExcsion(){
        var selfexec = Number.parseInt($('#res_selfexclusion').val());
        var url = '/player_management/postSelfExecResponsibleGaming/'+playerId+'/'+selfexec

        if(confirm("<?=lang('confirm.request'); ?>")){
            window.location.href=url;
        }
    }

    function postSelfExcsionPermanent(){
        var selfexec = null;
        var url = '/player_management/postSelfExecResponsibleGaming/'+playerId+'/'+selfexec;

        if(confirm("<?=lang('confirm.request'); ?>")){
            window.location.href=url;
        }
    }

    function postCooloff(){
        var coolOffPeriodCount = $('#coolOffPeriodCount').val();
        var url = '/player_management/postCooloffResponsibleGaming/'+playerId+'/'+coolOffPeriodCount

        if(confirm("<?=lang('confirm.request'); ?>")){
            window.location.href=url;
        }
    }

    function postDepositlimit(){
        var flg =1;
        var periodCnt  = $('#depositLimitsReactivationPeriodCnt').val();
        var amount = $('#depositLimitsAmount').val();


        if(!(/^(?:[1-9]|[1-2][0-9]|30)$/.test(periodCnt))){
            flg=0;
            alert("<?=lang('text.error')?>-<?=lang('Deposit Limits')?><?=lang(' days')?>");
        }

        var chkamount = parseInt(amount);
        if(chkamount=="NaN"){
            flg=0;
            alert("<?=lang('text.error')?>-<?=lang('Deposit Limits')?> <?=lang('player.ut05')?>");
        }

        if(flg){
            var url = '/player_management/postDepositlimitResponsibleGaming/'+playerId+'/'+periodCnt+'/'+chkamount
            if(confirm("<?=lang('confirm.request'); ?>")){
                $.ajax({
                    "url": url
                }).done(function(result){
                    if(result['status'] === 'success'){
                        BootstrapDialog.show({
                            "type": BootstrapDialog.TYPE_SUCCESS,
                            "message": result['message'],
                            "onhide": function(){
                                window.location.reload(true);
                            }
                        });
                    }else{
                        BootstrapDialog.show({
                            "type": BootstrapDialog.TYPE_DANGER,
                            "message": result['message'],
                            "onhide": function(){
                                window.location.reload(true);
                            }
                        });
                    }
                }).fail(function(){
                    BootstrapDialog.danger({
                        "type": BootstrapDialog.TYPE_DANGER,
                        "message": '<?=lang('error.default.db.message')?>',
                        "onhide": function(){
                            window.location.reload(true);
                        }
                    });
                });
            }
        }
    }

    function postWageringlimit(){
        var flg =1;
        var periodCnt  = $('#wageringLimitsReactivationPeriodCnt').val();
        var amount = $('#wageringLimitsAmount').val();

        if(!(/^(?:[1-9]|[1-2][0-9]|30)$/.test(periodCnt))){
            flg=0;
            alert("<?=lang('text.error')?>-<?=lang('Wagering Limits')?><?=lang(' days')?>");
        }

        var chkamount = parseInt(amount);
        if(chkamount=="NaN"){
            flg=0;
            alert("<?=lang('text.error')?>-<?=lang('Wagering Limits')?> <?=lang('player.ut05')?>");
        }

        if(flg){
            var url = '/player_management/postWageringlimitResponsibleGaming/'+playerId+'/'+periodCnt+'/'+chkamount
            if(confirm("<?=lang('confirm.request'); ?>")){
                $.ajax({
                    "url": url
                }).done(function(result){
                    if(result['status'] === 'success'){
                        BootstrapDialog.show({
                            "type": BootstrapDialog.TYPE_SUCCESS,
                            "message": result['message'],
                            "onhide": function(){
                                window.location.reload(true);
                            }
                        });
                    }else{
                        BootstrapDialog.show({
                            "type": BootstrapDialog.TYPE_DANGER,
                            "message": result['message'],
                            "onhide": function(){
                                window.location.reload(true);
                            }
                        });
                    }
                }).fail(function(){
                    BootstrapDialog.danger({
                        "type": BootstrapDialog.TYPE_DANGER,
                        "message": '<?=lang('error.default.db.message')?>',
                        "onhide": function(){
                            window.location.reload(true);
                        }
                    });
                });
            }
        }
    }

    $(document).ready(function() {
        var rsp_cancel_modal = $('#rsp_cancel_modal');
        $(".rsp-reset-reason", rsp_cancel_modal).click(function(){
            new resetRspCancelModalFields();
        });

        $('.rsp_temp_cancel_modal, .rsp_permanent_cancel_modal, .rsp_cooloff_cancel_modal, .rsp_dptlimit_cancel_modal, .rsp_wgrlimit_cancel_modal').on('click', function(){
            new resetRspCancelModalFields();
            $('#rsp_type').val($(this).attr('data-rsp-type'));
            rsp_cancel_modal.modal('show');
        });

        $('.btn-submit', rsp_cancel_modal).on('click', function(){
            var reason_exist = false;
            var rsp_cancel_reason = $('.rsp_cancel_reason').val();
            var cancel_type = $('#rsp_type').val();

            if(rsp_cancel_reason == '' || rsp_cancel_reason.trim() == ''){
                $('.rsp_cancel_reason').next('span').html('<?=lang("Reason")?><?=lang("is_required")?>');
                reason_exist = false;
            }else{
                $('.rsp_cancel_reason').next('span').html('');
                reason_exist = true;
            }

            if(!reason_exist){
                return;
            }

           $('.btn-submit', rsp_cancel_modal).attr("disabled", true);
           cancelPlayerResponsibleGaming(cancel_type, rsp_cancel_reason);
        });


        var rsp_expire_modal = $('#rsp_expire_modal');
        $(".rsp-reset-reason", rsp_expire_modal).click(function(){
            new resetRspExpireModalFields();
        });

        $('.rsp_cooling_off_expire_modal').on('click', function (){
            new resetRspExpireModalFields();
            $('#rsp_type').val($(this).attr('data-rsp-type'));
            $('#rsp_status').val($(this).attr('data-rsp-status'));
            rsp_expire_modal.modal('show');
        });

        $('.btn-submit', rsp_expire_modal).on('click',function(){
            var rsp_status_exist = false;
            var rsp_expire_reason = $('.rsp_expire_reason').val();
            var rsp_status = $('#rsp_status').val();
            var cancel_type = $('#rsp_type').val();

            if(rsp_expire_reason == '' || rsp_expire_reason.trim() == ''){
                $('.rsp_expire_reason').next('span').html('<?=lang("Reason")?><?=lang("is_required")?>');
                reason_exist = false;
            }else{
                $('.rsp_expire_reason').next('span').html('');
                reason_exist = true;
            }

            if(rsp_status == '<?=Responsible_gaming::STATUS_COOLING_OFF?>'){
                rsp_status_exist = true;
            }else{
                rsp_status_exist = false;
            }

            if(!reason_exist || !rsp_status_exist){
                return;
            }

            $('.btn-submit', rsp_expire_modal).attr("disabled", true);
            expireCoolingOffPlayer(cancel_type, rsp_expire_reason, rsp_status);
        });
    });

    function cancelPlayerResponsibleGaming(cancel_type, notes){
        $('#rsp_cancel_modal').modal('hide');

        var notify_prop = {type: 'success',timer: 500, delay: 1000 ,  spacing: 10, offset:{y:100,x:20} };
        notify_prop['onClose'] = function(){
            window.location.reload(true);
        };
        $.ajax({
            url: '/player_management/cancelPlayerResponsibleGaming',
            type: 'POST',
            data: {
                player_id: playerId,
                cancel_type: cancel_type,
                notes: notes
            },
            dataType: "json"
        }).done(function(data) {
            if(data.status == 'success'){
                $.notify({ message: data.message}, notify_prop);
            }else{
                notify_prop['type'] = 'danger';
                $.notify({ message: data.message},notify_prop );
            }
        }).fail(function(jqXHR, textStatus) {
            notify_prop['type'] = 'danger';
            $.notify({ message: textStatus},notify_prop);
        });
    }

    function resetRspCancelModalFields(){
        $('.rsp_cancel_reason')
            .val("")
            .next('span').html('');
    }

    function expireCoolingOffPlayer(cancel_type, notes, status){
        $('#rsp_cancel_modal').modal('hide');

        var notify_prop = {type: 'success',timer: 500, delay: 1000 ,  spacing: 10, offset:{y:100,x:20} };
        notify_prop['onClose'] = function(){
            window.location.reload(true);
        };
        $.ajax({
            url: '/player_management/expireCoolingOffPlayer',
            type: 'POST',
            data: {
                player_id: playerId,
                cancel_type: cancel_type,
                notes: notes,
                rsp_status: status
            },
            dataType: "json"
        }).done(function(data) {
            if(data.status == 'success'){
                $.notify({ message: data.message}, notify_prop);
            }else{
                notify_prop['type'] = 'danger';
                $.notify({ message: data.message},notify_prop );
            }
        }).fail(function(jqXHR, textStatus) {
            notify_prop['type'] = 'danger';
            $.notify({ message: textStatus},notify_prop);
        });
    }

    function resetRspExpireModalFields(){
        $('.rsp_expire_reason')
            .val("")
            .next('span').html('');
    }
</script>
