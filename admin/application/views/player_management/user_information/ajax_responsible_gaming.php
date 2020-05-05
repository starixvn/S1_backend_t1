<div role="tabpanel" class="tab-pane active" id="responsibleGaming">
    <div class="row">
        <div class="col-md-6">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td><b><?=lang('Self Exclusion, Temporary')?></b></td>
                        <?php $TSE = empty($responsible_gaming['temp_self_exclusion']) ? '' : $responsible_gaming['temp_self_exclusion'];?>
                        <?php if(!empty($TSE) && $TSE->status != Responsible_gaming::STATUS_CANCELLED): ?>
                            <td><?=$statusType[$TSE->status]?></td>
                            <td>
                                <?php
                                    $temporaryCloseIn  = lang('Temporary Close in') . ': ' . $TSE->period_cnt . " " . $periodType[$TSE->period_type];
                                    $temporaryDuration = lang('player.80').': ' . $TSE->date_from . ' ' . lang('player.81'). ': ' .$TSE->date_to;
                                ?>
                                <?=$temporaryCloseIn?><br/>
                                <?=$temporaryDuration?>
                            </td>
                            <?php if($this->permissions->checkPermissions('cancel_responsible_gaming_request')): ?>
                                <td>
                                    <?php if($TSE->status == Responsible_gaming::STATUS_COOLING_OFF && $responsible_gaming['use_uplift_instead_of_cancel_btn']): ?>
                                        <button type="button" class="btn btn-sm btn-linkwater rsp_cooling_off_expire_modal" data-rsp-type="<?=Responsible_gaming::SELF_EXCLUSION_TEMPORARY ?>" data-rsp-status="<?=Responsible_gaming::STATUS_COOLING_OFF?>">
                                            <?=lang('Uplift')?>
                                        </button>
                                    <?php else:?>
                                        <button type="button" class="btn btn-sm btn-linkwater rsp_temp_cancel_modal" data-rsp-type="<?=Responsible_gaming::SELF_EXCLUSION_TEMPORARY ?>">
                                            <?=lang('lang.cancel')?>
                                        </button>
                                    <?php endif;?>
                                </td>
                            <?php endif;?>
                        <?php else: ?>
                            <td colspan='3'><?=lang('N/A')?></td>
                        <?php endif;?>
                    </tr>
                    <tr>
                        <td><b><?=lang('Self Exclusion, Permanent')?></b></td>
                        <?php $PSE = empty($responsible_gaming['permanent_self_exclusion']) ? '' : $responsible_gaming['permanent_self_exclusion'];?>
                        <?php if(!empty($PSE) && $PSE->status != Responsible_gaming::STATUS_CANCELLED): ?>
                            <td><?=$statusType[$PSE->status]?></td>
                            <td>
                                <?=lang('Permanently Closed')?>
                            </td>
                            <?php if(!$this->utils->isEnabledFeature('hide_permanent_self_exclusion_cancel_button') && $this->permissions->checkPermissions('cancel_responsible_gaming_request')):?>
                                <td>
                                    <button type="button" class="btn btn-sm btn-linkwater rsp_permanent_cancel_modal" data-rsp-type="<?= Responsible_gaming::SELF_EXCLUSION_PERMANENT ?>">
                                        <?= lang('lang.cancel') ?>
                                    </button>
                                </td>
                            <?php endif;?>
                        <?php else: ?>
                            <td colspan='3'><?=lang('N/A')?></td>
                        <?php endif;?>
                    </tr>
                    <tr>
                        <td><b><?=lang('Cooling Off')?></b></td>
                        <?php $CO = empty($responsible_gaming['cool_off']) ? '' : $responsible_gaming['cool_off'];?>
                        <?php if(!empty($CO) && $CO->status != Responsible_gaming::STATUS_CANCELLED): ?>
                            <td><?=$statusType[$CO->status]?></td>
                            <td>
                                <?php
                                    $coolOffCloseIn  = lang("Cooling off for") . ': ' . $CO->period_cnt . " " . lang("day/s");
                                    $coolOffDuration = lang('player.80').': ' . $CO->date_from . ' ' . lang('player.81'). ': ' .$CO->date_to;
                                ?>
                                <?=$coolOffCloseIn?><br/>
                                <?=$coolOffDuration?>
                            </td>
                            <?php if($this->permissions->checkPermissions('cancel_responsible_gaming_request')):?>
                                <td>
                                    <button type="button" class="btn btn-sm btn-linkwater rsp_cooloff_cancel_modal" data-rsp-type="<?= Responsible_gaming::COOLING_OFF ?>">
                                        <?= lang('lang.cancel') ?>
                                    </button>
                                </td>
                            <?php endif;?>
                        <?php else: ?>
                            <td colspan='3'><?=lang('N/A')?></td>
                        <?php endif;?>
                    </tr>
                    <tr>
                        <td><b><?=lang('Deposit Limits')?></b></td>
                        <?php $DL = empty($responsible_gaming['deposit_limits']) ? '' : $responsible_gaming['deposit_limits'];?>
                        <?php if(!empty($DL) && $DL->status != Responsible_gaming::STATUS_CANCELLED): ?>
                            <td><?=$statusType[$DL->status]?></td>
                            <td>
                                <?php
                                    $depositlimitsCloseIn = lang("Deposit Limit Amount").': ' . $DL->amount . " for " . $DL->period_cnt . " Day";
                                ?>
                                <?=$depositlimitsCloseIn?>
                            </td>
                            <?php if($this->permissions->checkPermissions('cancel_responsible_gaming_request')):?>
                                <td>
                                    <button type="button" class="btn btn-sm btn-linkwater rsp_dptlimit_cancel_modal" data-rsp-type="<?= Responsible_gaming::DEPOSIT_LIMITS ?>">
                                        <?= lang('lang.cancel') ?>
                                    </button>
                                </td>
                            <?php endif;?>
                        <?php else: ?>
                            <td colspan='3'><?=lang('N/A')?></td>
                        <?php endif;?>
                    </tr>
                    <?php if(!$disable_and_hide_wagering_limits):?>
                        <tr>
                            <td><b><?=lang('Wagering Limits')?></b></td>
                            <?php $WL = empty($responsible_gaming['wagering_limits']) ? '' : $responsible_gaming['wagering_limits'];?>
                            <?php if(!empty($WL) && $WL->status != Responsible_gaming::STATUS_CANCELLED): ?>
                                <td><?=$statusType[$WL->status]?></td>
                                <td>
                                    <?php
                                        $wageringLimitsCloseIn = lang("Wagering Limit Amount").': ' . $WL->amount . " for " . $WL->period_cnt . " Day";
                                    ?>
                                    <?=$wageringLimitsCloseIn?>
                                </td>
                                <?php if($this->permissions->checkPermissions('cancel_responsible_gaming_request')):?>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-linkwater rsp_wgrlimit_cancel_modal" data-rsp-type="<?= Responsible_gaming::WAGERING_LIMITS ?>">
                                            <?= lang('lang.cancel') ?>
                                        </button>
                                    </td>
                                <?php endif;?>
                            <?php else: ?>
                                <td colspan='3'><?=lang('N/A')?></td>
                            <?php endif;?>
                        </tr>
                    <?php endif;?>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td colspan="3"><b><?=lang('Responsible Gaming')?></b></td>
                    </tr>
                    <tr>
                        <td><b><?=lang('Self Exclusion, Temporary')?></b></td>
                        <td>
                            <div class="form-horizontal">
                                <label><?=lang("Enter the number of months your account will be temporarily closed")?></label>
                                <br>
                                <select id="res_selfexclusion">
                                    <?php foreach (Responsible_gaming::getTempPeriodList() as $days => $content) : ?>
                                        <option value="<?= $days ?>"> <?= $content?> </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <br>
                            <?php if(!$selfexclusionFlag):?>
                                <button onclick="postSelfExcsion()" class="btn btn-portage btn-xs pull-right">
                                    <?=lang('Send Request Now')?>
                                </button>
                            <?php endif;?>
                        </td>
                    </tr>
                    <tr>
                        <td><b><?=lang('Self Exclusion, Permanent')?></b></td>
                        <td>
                            <?php if(!$selfexclusionFlag):?>
                                <button onclick="postSelfExcsionPermanent()" class="btn btn-portage btn-xs pull-right">
                                    <?=lang('Send Request Now')?>
                                </button>
                            <?php endif;?>
                        </td>
                    </tr>
                    <tr>
                        <td><b><?=lang('Cooling Off')?></b></td>
                        <td>
                            <select id="coolOffPeriodCount">
                                <option value="1" selected><?=lang('24 Hours')?></option>
                                <option value="7" ><?=lang('One Week')?></option>
                                <option value="30"><?=lang('One Month')?></option>
                                <option value="42"><?=lang('6 Weeks')?></option>
                            </select>
                            <?php if(!$timeoutFlag):?>
                                <button onclick="postCooloff()" class="btn btn-portage btn-xs pull-right">
                                    <?=lang('Send Request Now')?>
                                </button>
                            <?php endif;?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b><?=lang('Deposit Limits')?></b>
                            <?php if($depositlimitFlag): ?>
                                <a class="pull-right text-success">(<?=lang("lang.activated")?>)</a>
                            <?php else: ?>
                                <a class="pull-right">(<?=lang("lang.deactivated")?>)</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="form-horizontal">
                                <label><?=lang("Set Deposit Amount");?></label>
                                <input type="number" id="depositLimitsAmount" name="depositLimitsAmount" value="<?=($depositlimitFlag) ? $DL->amount : 0;?>">
                                <input type="hidden" id="depositLimitsDefaultAmount" value="<?=($depositlimitFlag) ? $DL->amount : 0;?>">

                                <label><?=lang("for next "); ?></label>
                                <select name="depositLimitsReactivationPeriodCnt" id="depositLimitsReactivationPeriodCnt">
                                    <?php foreach ($deposit_limits_day_options as $days):?>
                                        <?php if($depositlimitFlag):?>
                                            <option value="<?=$days?>" <?=($days == $DL->period_cnt) ? 'selected' : ''; ?>><?=$days?></option>
                                        <?php else:?>
                                            <option value="<?=$days?>"><?=$days?></option>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                </select>
                                <label><?=lang(' days')?></label>
                            </div>
                            <br>
                            <?php if($depositlimitFlag):?>
                                <span class="pull-right"><?=lang('Current Limit :').$DL->amount; ?></span><br>
                                <span class="pull-right"><?=lang('Current Amount Remaining :').$depositLimitRemainTotalAmount; ?></span><br>
                                <span class="pull-right"><?=lang('Reset Period End :').$DL->date_to; ?></span>
                            <?php endif;?>
                            <br>
                            <?php if(!$selfexclusionFlag && !$timeoutFlag): ?>
                                <button id="depositLimitsSubmit" onclick="postDepositlimit()" class="btn btn-portage btn-xs pull-right">
                                    <?=($depositlimitFlag) ? lang('lang.save') : lang('Send Request Now'); ?>
                                </button>
                            <?php endif;?>
                        </td>
                    </tr>
                    <?php if(!$disable_and_hide_wagering_limits):?>
                        <tr>
                            <td>
                                <b><?=lang('Wagering Limits')?></b>

                                <?php if ($wageringLimitsFlag): ?>
                                    <a class="pull-right text-success">(<?=lang("lang.activated")?>)</a>
                                <?php else: ?>
                                    <a class="pull-right">(<?=lang("lang.deactivated")?>)</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="form-horizontal">
                                    <label><?=lang("Set Wagering Limit"); ?></label>
                                    <input type="number" id="wageringLimitsAmount" name="wageringLimitsAmount" value="<?=($wageringLimitsFlag) ? $WL->amount : 0;?>">
                                    <input type="hidden" id="wageringLimitsDefaultAmount" value="<?=($wageringLimitsFlag) ? $WL->amount : 0;?>">

                                    <label><?=lang("for next "); ?></label>
                                    <select name="wageringLimitsReactivationPeriodCnt" id="wageringLimitsReactivationPeriodCnt">
                                        <?php foreach ($wagering_limits_day_options as $days):?>
                                            <?php if($wageringLimitsFlag):?>
                                                <option value="<?=$days?>" <?=($days == $WL->period_cnt) ? 'selected' : ''; ?>><?=$days?></option>
                                            <?php else:?>
                                                <option value="<?=$days?>"><?=$days?></option>
                                            <?php endif;?>
                                        <?php endforeach;?>
                                    </select>
                                    <label><?=lang(' days')?></label>
                                </div>
                                <br>
                                <?php if($wageringLimitsFlag):?>
                                    <span class="pull-right"><?=lang('Current Limit :').$WL->amount; ?></span><br>
                                    <span class="pull-right"><?=lang('Current Amount Remaining :').$wageringLimitRemainTotalAmount; ?></span><br>
                                    <span class="pull-right"><?=lang('Reset Period End :').$WL->date_to; ?></span>
                                <?php endif;?>
                                <br>
                                <?php if(!$selfexclusionFlag && !$timeoutFlag):?>
                                    <button id="wageringLimitsSubmit" onclick="postWageringlimit()" class="btn btn-portage btn-xs pull-right">
                                        <?=($wageringLimitsFlag) ? lang("lang.save") : lang('Send Request Now'); ?>
                                    </button>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
</div>


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
