<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> <?=lang('Withdrawal Processing Stages Setting');?></h4>
        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-6" id="previewPaymentAcctSettingSec">
                <!-- Display -->
                <form action="<?php echo site_url('payment_management/saveCustomWithdrawalProcessingStageSetting'); ?>" method="post" role="form">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%"><?=lang('Enable')?></th>
                        <th><?=lang('Stage Name')?></th>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="checkbox" checked disabled />
                            <label><?=lang('pay.penreq')?></label>
                        </td>
                    </tr>
                    <?php if($this->utils->isEnabledFeature('enable_withdrawal_pending_review')){ ?>
                        <tr>
                            <!-- OGP-3531 -->
                            <?php $this->load->view('payment_management/withdraw_flow/pending_review'); ?>
                        </tr>
                    <?php } ?>
                    <?php for($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) : ?>
                    <tr>
                        <td>
                            <input id="cb<?=$i?>" type="checkbox" data-index="<?=$i?>" name="enabled_<?=$i?>" value="1" <?=$setting[$i]['enabled'] ? 'checked' : ''?> />
                            <label for="cb<?=$i?>"><?= lang('Custom Stage').' '.($i+1) ?></label>
                        </td>
                        <td><input id="name<?=$i?>" type="text" class="form-control" name="name_<?=$i?>" value="<?=$setting[$i]['name'] ?: ''?>" maxlength="20" /></td>
                    </tr>
                    <?php endfor; ?>
                    <tr>
                        <td colspan="2">
                            <input id="payProc" type="checkbox" name="enabled_payProc" value="1"
                                <?=$setting['payProc']['enabled'] ? 'checked' : ''?>
                                <?=array_key_exists('mustEnable', $setting['payProc']) && $setting['payProc']['mustEnable'] ? "disabled" : ""?> />
                            <label for="payProc"><?=lang('pay.processing')?></label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="checkbox" checked disabled />
                            <label><?=lang('pay.paid')?></label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="checkbox" checked disabled />
                            <label><?=lang('pay.decreq')?></label>
                        </td>
                    </tr>
                </table>
                <input type="submit" value="<?=lang('player.saveset')?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>">
                </form>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> <?=lang('con.pym28');?></h4>
        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body player_center_withdrawal_page_setting_panel">
        <div class="row">
            <div class="col-md-6" id="">
                <form action="<?php echo site_url('payment_management/savePlayerCenterWithdrawalPageSetting'); ?>" method="post" role="form">
                    <table class="table table-hover">
                        <tr>
                            <td>
                                <input type="hidden" name="showMaxWithdrawalPerTransaction" value="0" />
                                <input id="showMaxWithdrawalPerTransaction" type="checkbox" class="showMaxWithdrawalPerTransaction" name="showMaxWithdrawalPerTransaction" value="1" <?php echo (int)$withdrawal_setting['showMaxWithdrawalPerTransaction']?'checked':''?>  />
                            </td>
                            <td>
                                <label for="showMaxWithdrawalPerTransaction"><b><?=lang('con.pym29')?></b></label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="hidden" name="showDailyMaxWithdrawalAmount" value="0" />
                                <input id="showDailyMaxWithdrawalAmount" type="checkbox" class="showDailyMaxWithdrawalAmount" name="showDailyMaxWithdrawalAmount" value="1" <?php echo (int)$withdrawal_setting['showDailyMaxWithdrawalAmount']?'checked':''?>  />
                            </td>
                            <td>
                                <label for="showDailyMaxWithdrawalAmount"><b><?=lang('con.pym30')?></b></label>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" value="<?=lang('player.saveset')?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>">
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var numCustomStages = <?=CUSTOM_WITHDRAWAL_PROCESSING_STAGES?>;
$(function(){
    var fillDefaultName = function() {
        var index = $(this).data('index');
        var checked = $('#cb'+index).prop('checked');
        var name = $('#name'+index).val();
        if(checked && name == '') {
            $('#name'+index).val($('label[for="cb'+index+'"]').text());
        }
    }

    for(i = 0; i <= numCustomStages; i++) {
        $('#cb' + i).click(fillDefaultName);
    }

    // -- control sidebar --
    $('#collapseSubmenu').addClass('in');
    $('#view_payment_settings').addClass('active');
    $('#withdrawalProcessingStagesSetting').addClass('active');



});

</script>
