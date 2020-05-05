<?php
$enabled_add_withdraw_condition_as_bonus_condition=$this->utils->isEnabledPromotionRule('add_withdraw_condition_as_bonus_condition');
?>
<link rel="stylesheet" type="text/css" href="<?=$this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css')?>" />
<script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/js/bootstrap-switch.min.js');?>"></script>
<style type="text/css">
 .right_column{
    padding-left:10px;
 }
 .ace_editor{
    min-height: 150px;
 }
.border_box{
    margin:10px; border:1px solid #ddd; margin-bottom:15px; padding:10px;
}
.content-hide {
    display: none;
}
</style>
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i> <span id="title_promo_rule"><?=lang('cms.createNewPromo');?></span>
            <a href="<?=site_url('marketing_management/promoRuleManager/')?>" class="btn btn-xs pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-danger' : 'btn-info' ?>" data-toggle="tooltip" title="Close" data-placement="left">
                <span class="glyphicon glyphicon-remove"></span>
            </a>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <div class="row add_promo_sec">
            <div class="col-md-12">
                <div class="well" style="overflow: auto;padding:15px;">
                    <form class="form-inline" id="promoform" action="<?=site_url('marketing_management/preparePromo')?>" method="post" role="form" onsubmit="return valthis();" enctype="multipart/form-data">
                        <input type="hidden" id="next_action" name="next_action" value="back">
                            <!-- ******************************************
                                 | promo name, validity date, promo type  |
                                 ****************************************** -->
                            <div class="row">
                                <div class="form-group col-md-4">
                                        <label class="control-label">*<?=lang('cms.promoRuleName');?>: </label>
                                        <br/>
                                        <input type="hidden" name="promorulesId" class="form-control" id="promorulesId" >
                                        <input type="text" name="promoName" id="promoName" style="width:100%" class="form-control input-sm" required data-validation-error-msg="<?php echo lang('You did not enter a promo name!') ?>">
                                        <span id="promonameStatus" style="font-size:12px; color:#ff0000; font-style: italic;"></span>
                                </div>

                                <div class="form-group col-md-3">
                                    <label class="control-label">*<?=lang('cms.appStartDate');?>: </label>
                                    <input type="text" name="appStartDate" id="appStartDate" class="form-control input-sm dateInput" data-time="true" data-future="true" style="width:100%" required/>
                                </div>
                                <div class="form-group col-md-3">
                                    <input type="hidden" name="appEndDate" value="" style="width:100%" id="appEndDate" class="form-control input-sm" required>
                                    <label class="control-label">*<?=lang('promo.promoHideDate');?>: </label>
                                    <i class="btn btn-default btn-xs" data-toggle="tooltip" title="<?=lang('promo.promoHideDateDesc')?>" data-placement="right" style="height:18px;" rel="tooltip">?</i>
                                    <input type="text" name="hideDate" id="hideDate" class="form-control input-sm dateInput" data-time="true" data-future="true" style="width:100%" required/>
                                </div>
<!--                                <div class="form-group col-md-2">-->
<!--                                    <label class="control-label">--><?//=lang('Bonus Expire Days');?><!-- </label>-->
<!--                                    <input type="number" min='0' name="expire_days" id="expire_days" class="form-control input-sm"/>-->
<!--                                    <input type="hidden" value="true" name="promoPeriodEndCbxVal" id="promoPeriodEndCbxVal">-->
<!--                                </div>-->
                            </div>
                            <br/>
                            <!-- ***********************************************
                                 | application start and end date, promo code  |
                                 *********************************************** -->
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label class="control-label">*<?=lang('cms.promoCat');?>: </label>
                                    <br/>
                                    <select data-validation-error-msg="<?php echo lang('You did not select a promo category!') ?>" class="form-control input-sm" name="promoCategory" id="promoType" required style="width:100%;">
                                        <option value=""><?=lang('cms.selectPromoCat');?></option>
                                        <?php if (!empty($promoType)) {
                                            foreach ($promoType as $key) {?>
                                                <option value="<?=$key['promotypeId']?>"><?=lang($key['promoTypeName'])?></option>
                                        <?php }
                                        }
                                        ?>

                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="control-label"><?=lang('system.word3');?>: </label>
                                    <br/>
                                    <select class="form-control input-sm" name="language" id="language" style="width:100%;">
                                        <option value=""><?=lang('system.word57')?></option>
                                        <option value="1" >English</option>
                                        <option value="2" >中文</option>
                                        <option value="3" >Indonesian</option>
                                        <option value="4" >Vietnamese</option>
                                        <option value="5" >Korean</option>
                                        <option value="6" >Thai</option>
                                    </select>
                                </div>
<!--                                     <div class="form-group col-md-3">
                                    <label class="control-label"><?=lang('cms.promocode');?>: </label>
                                    <i class="btn btn-default btn-xs" data-toggle="tooltip" title="<?=lang('cms.promoCodeInfo')?>" data-placement="top" style="height:18px;" rel="tooltip">?</i>
                                    <input type="text" name="promoCode" id="promoCode" style="width:100%" class="form-control input-sm letters_numbers_only" minlength="6" maxlength="10">
                                    <span id="promocodeStatus" style="font-size:12px; color:#ff0000; font-style: italic;"></span>
                                </div>
 -->
                            </div>
                            <br/>

                        <div class="row">

                            <!-- **********************
                                 | promo request limit  |
                                 ********************** -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?=lang('Daily Request limit');?>: </label>
                                    <input type="number" name="request_limit" id="request_limit" class="form-control" value="<?=( ! empty( $promoRuleDetails['request_limit'] ) ) ? $promoRuleDetails['request_limit'] : '0'?>">
                                </div>
                            </div>

                            <!-- **********************
                                 | promo approved limit  |
                                 ********************** -->

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?=lang('Daily Approved limit');?>: </label>
                                    <input type="number" name="approved_limit" id="approved_limit" class="form-control" value="<?=( ! empty( $promoRuleDetails['approved_limit'] ) ) ? $promoRuleDetails['approved_limit'] : '0'?>">
                                </div>
                            </div>

                            <!-- **********************
                                 | promo total approved limit  |
                                 ********************** -->

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label"><?=lang('Total Approved limit');?>: </label>
                                    <input type="number" name="total_approved_limit" id="total_approved_limit" class="form-control" value="<?=( ! empty( $promoRuleDetails['total_approved_limit'] ) ) ? $promoRuleDetails['total_approved_limit'] : '0'?>">
                                </div>
                            </div>
                        </div>
                        <br/>

                            <!-- **********************
                                 | promo description  |
                                 ********************** -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group col-md-12">
                                        <label class="control-label"><?=lang('cms.promodesc');?>: </label>
                                        <textarea name="promoDesc" id="promoDesc" maxLength="500" class="form-control" style="width:100%;"></textarea>
                                    </div>
                                </div>
                            </div>
                            <br/>

                            <!-- **********************
                                 | promo workflow  |
                                 ********************** -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label"><?=lang('Workflow');?>: </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <td>
                                                <label><?php echo lang('Release Bonus'); ?>:</label>
                                                <input type="radio" name="bonusReleaseToPlayerOption" id="bonusReleaseToPlayerAuto" value="0">
                                                <label class="control-label" id="bonusReleaseToPlayerOption1Lbl"><?=lang('cms.auto');?> </label>
                                                <input type="radio" name="bonusReleaseToPlayerOption" id="bonusReleaseToPlayerManual" value="1">
                                                <label class="control-label" id="bonusReleaseToPlayerOption2Lbl"><?=lang('cms.manual');?> </label>
                                            </td>
                                            <td>
                                                <input type="checkbox" id="add_withdraw_condition_as_bonus_condition" name="add_withdraw_condition_as_bonus_condition" value="true">
                                                <label id="lbl_add_withdraw_condition_as_bonus_condition" class="control-label" style='font-size:10px;'><?php echo lang('Should be finished withdraw condition first'); ?></label>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="donot_allow_other_promotion" id="donot_allow_other_promotion" value="true">
                                                <label class="control-label" for="donot_allow_other_promotion"><?php echo lang('Do not allow other promotion'); ?></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <!--<td>
                                                <input type="checkbox" name="show_on_active_available" id="show_on_active_available" value="true">
                                                <label class="control-label" for="show_on_active_available"><?php //echo lang('Show this promotion when available'); ?></label>
                                            </td>-->
                                            <td>
                                                <input type="checkbox" name="disable_cashback_if_not_finish_withdraw_condition" id="disable_cashback_if_not_finish_withdraw_condition" value="true">
                                                <label class="control-label" for="disable_cashback_if_not_finish_withdraw_condition"><?php echo lang('promorule.disable_cashback_if_not_finish_withdraw_condition'); ?></label>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="always_join_promotion" id="always_join_promotion" value="true">
                                                <label class="control-label" for="always_join_promotion"><?php echo lang('Always join this promotion, allow multiple requst'); ?></label>
                                            </td>
                                            <td>
                                                <input type="checkbox" name="dont_allow_request_promo_from_same_ips" id="dont_allow_request_promo_from_same_ips" value="true">
                                                <label class="control-label" for="dont_allow_request_promo_from_same_ips"><?php echo lang('Dont allow reqeust promo from same ips'); ?></label>
                                            </td>
                                                <div class="hide">
                                                    <input type="checkbox" name="disabled_pre_application" id="disabled_pre_application" class="disabled" value="true" checked="checked" readonly="readonly" onclick="return false;">
                                                    <label class="control-label" for="disabled_pre_application"><?php echo lang('promorule.disabled_pre_application'); ?></label>
                                                </div>
                                        </tr>
                                        <tr id="subwallet_trigger">
                                            <td colspan="3">
                                <?php echo lang('Trigger on transfer to sub-wallet'); ?>:
                                                <input type="checkbox" id="trigger_on_transfer_to_subwallet" /><label for="trigger_on_transfer_to_subwallet"><strong><?php echo lang('Select All'); ?></strong></label>
                                <?php
                                if(!empty($subWallets)){
                                foreach ($subWallets as $subWallet) { ?>
                                <input type="checkbox" class="trigger_on_transfer_to_subwallet" name="trigger_on_transfer_to_subwallet[]" id="trigger_on_transfer_to_subwallet<?php echo $subWallet['id'];?>" value="<?php echo $subWallet['id'];?>">
                                <label class="control-label" for="trigger_on_transfer_to_subwallet<?php echo $subWallet['id'];?>"><?php echo $subWallet['system_code'];?></label>
                                <?php }
                                }?>
                                            <div>
                                    <?php echo lang('Release to same sub-wallet'); ?>
                                    <input type="checkbox" id="release_to_same_sub_wallet" name="release_to_same_sub_wallet" value="true">
                                            </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <br/>

                            <!-- **********************
                                 | promo type option  |
                                 ********************** -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group col-md-2">
                                        <input type="radio" name="promoType" id="promoTypeDeposit" value="0">
                                        <label for="promoTypeDeposit" class="control-label" style="font-size:14px;"><?=lang('cms.depPromo');?></label>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input type="radio" name="promoType" id="promoTypeNonDeposit" value="1">
                                        <label for="promoTypeNonDeposit" class="control-label" style="font-size:14px;"><?=lang('cms.nonDepPromo');?> </label>
                                    </div>
                                    <?php
                                        if( $this->utils->isEnabledFeature('enabled_freespin') ){

                                    ?>
                                            <div class="form-group col-md-3">
                                                <a href="<?=site_url('marketing_management/freeround')?>" class="btn btn-primary btn-sm btn-block" style="width: 135px;"><?=lang('cms.slotgamepromo');?></a>
                                            </div>
                                    <?php

                                        }

                                    ?>

                                </div>
                            </div>
                            <br/>
                            <!-- ********************************
                                 | deposit promo type container |
                                 ******************************** -->
                            <div class="row" style="padding:10px">
                                <!-- <div class="col-md-12"> -->
                                    <div class="col-md-12" style="border:1px solid #ddd; border-radius: 10px; padding:10px; background-color:#fff">
                                        <div class="form-group col-md-6 left_column" style="">
                                            <!-- **********************
                                                 | deposit condition  |
                                                 ********************** -->
                                            <h4 id="promoTypeDepositConditionLbl">1. *<?=lang('cms.depCon');?></h4>
                                            <h4 id="promoTypeNonDepositConditionLbl">1. *<?=lang('cms.nonDepCon');?></h4>
                                            <div class="form-group col-md-12" id="depositConditionSec">
                                                <div class="form-group col-md-12" style="border-top:1px solid #ddd;margin-bottom:15px; padding:10px 0px 10px; 0px;">
                                                     <!-- ************************************************
                                                          |  deposit condition type (min and max or any)  |
                                                          ************************************************ -->
                                                    <div class="col-md-12" style="margin-top:5px; border:1px solid #ddd; margin-bottom:15px; padding:10px;">
                                                        <input type="radio" name="depositConditionTypeOption" value="0" id="nonFixedDepositAmountConditionSecOption1">
                                                        <label class="control-label"><?=lang('report.p20');?> </label>
                                                        <input type="number" min='1' step='any' name="nonfixedDepositMinAmount" id="nonfixedDepositMinAmount" style="width:100px;" class="form-control input-sm amount_only">
                                                        <input type="number" min='1' step='any' name="nonfixedDepositMaxAmount" id="nonfixedDepositMaxAmount" style="width:100px;" class="form-control input-sm amount_only">
                                                        <input type="radio" name="depositConditionTypeOption" value="1" id="nonFixedDepositAmountConditionSecOption2">
                                                        <label class="control-label"><?=lang('cms.anyAmt');?> </label>

                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <input type="checkbox" name="donot_allow_any_withdrawals_after_deposit" id="donot_allow_any_withdrawals_after_deposit" value="true">
                                                        <label class="control-label" for="donot_allow_any_withdrawals_after_deposit"><?php echo lang('Do not allow exists other withdrawals records after deposit records'); ?></label>
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <input type="checkbox" name="donot_allow_any_despoits_after_deposit" id="donot_allow_any_despoits_after_deposit" value="true">
                                                        <label class="control-label" for="donot_allow_any_despoits_after_deposit"><?php echo lang('Do not allow exists other deposit records after deposit records'); ?></label>
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <input type="checkbox" name="donot_allow_any_available_bet_after_deposit" id="donot_allow_any_available_bet_after_deposit" value="true">
                                                        <label class="control-label" for="donot_allow_any_available_bet_after_deposit"><?php echo lang('Do not allow exists other availables bet records after deposit records'); ?></label>
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <input type="checkbox" name="donot_allow_any_transfer_after_deposit" id="donot_allow_any_transfer_after_deposit" value="true">
                                                        <label class="control-label" for="donot_allow_any_transfer_after_deposit"><?php echo lang('Do not allow exists other transfer records after deposit records'); ?></label>
                                                    </div>

                                                </div>
                                                <div class="form-group col-md-12" style="border-top:1px solid #ddd;margin-bottom:15px; padding:10px 0px 10px; 0px;">
                                                    <div class="form-group col-md-12">
                                                        <input type="checkbox" name="donot_allow_any_transfer_in_after_transfer" id="donot_allow_any_transfer_in_after_transfer" value="true">
                                                        <label class="control-label" for="donot_allow_any_transfer_in_after_transfer"><?php echo lang('Do not allow exists other transfer in records after transfer records'); ?></label>
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <input type="checkbox" name="donot_allow_any_transfer_out_after_transfer" id="donot_allow_any_transfer_out_after_transfer" value="true">
                                                        <label class="control-label" for="donot_allow_any_transfer_out_after_transfer"><?php echo lang('Do not allow exists other transfer out records after transfer records'); ?></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12" id="nonDepositConditionSec">
                                                <div class="row" style="border:1px solid #ddd; margin-bottom:15px; padding:10px 0px 10px; 0px;">
                                                    <div class="form-group col-md-6">
                                                        <input type="radio" name="nonDepositOption" value="<?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_EMAIL; ?>" data-toggle="tooltip" data-placement="right" rel="tooltip" title="<?=lang('cms.emailPromoInfo');?>" id="nonDepositByEmail">
                                                        <label for="nonDepositByEmail" class="control-label"><?=lang('cms.promoEmail');?> </label>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <input type="radio" name="nonDepositOption" value="<?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_MOBILE; ?>" data-toggle="tooltip" data-placement="right" rel="tooltip" title="<?=lang('cms.mobilePromoInfo');?>" id="nonDepositByMobile">
                                                        <label for="nonDepositByMobile" class="control-label"><?=lang('cms.promoMobilePhone');?> </label>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <input type="radio" name="nonDepositOption" value="<?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_REGISTRATION; ?>" data-toggle="tooltip" data-placement="right" rel="tooltip" title="<?=lang('cms.registeredAcctPromoInfo');?>" id="nonDepositByRegAcct">
                                                        <label for="nonDepositByRegAcct" class="control-label"><?=lang('cms.promoRegisteredAcct');?> </label>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <input type="radio" name="nonDepositOption" value="<?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_COMPLETE_PLAYER_INFO; ?>" data-toggle="tooltip" data-placement="right" rel="tooltip" title="<?=lang('cms.completeRegisterAcctPromoInfo');?>" id="nonDepositByComReg">
                                                        <label for="nonDepositByComReg" class="control-label"><?=lang('cms.promoCompleteRegistration');?> </label>
                                                    </div>
                                                    <div class="form-group col-md-6 disabled">
                                                        <input type="radio" name="nonDepositOption" value="<?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_RESCUE; ?>" data-toggle="tooltip" data-placement="right" rel="tooltip" title="<?=lang('promo.nondeposit_rescue_info');?>" id="nonDepositByRescue" readonly="readonly" class="disabled">
                                                        <label for="nonDepositByRescue" class="control-label"><del><?=lang('promo.nondeposit_rescue');?></del></label>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <input type="radio" name="nonDepositOption" value="<?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_CUSTOMIZE; ?>" data-toggle="tooltip" data-placement="right" rel="tooltip" title="<?=lang('Full Customized Promotion');?>" id="nonDepositByCustomized">
                                                        <label for="nonDepositByCustomized" class="control-label"><?=lang('Customized');?> </label>
                                                    </div>
                                                </div>

                                                <div class="col-md-12" id="betWinLossGamesSec">
                                                    <center><label><?=lang('cms.selectGames');?></label></center>
                                                    <div class="row" id="gameOptionWindow" style="border:1px solid #ddd; margin-bottom:15px; padding:10px 0px 10px; 0px; overflow-y:scroll; overflow-x:scroll;">
                                                        <div id="">
                                                            <?php echo lang('set_in_allowed_games'); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="border:1px solid #ddd; margin-bottom:15px; padding:10px 0px 10px; 0px; text-align:center;">
                                                        <div class="col-md-12">
                                                            <label class="control-label" id="reqamountLbl1"><?=lang('cms.requiredAmount');?> </label>
                                                            <input type="number" name="gameRequiredBet" id="gameRequiredBet" class="form-control number_only" style="width:100%" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-12 form-group">
                                                        <!-- bonusApplicationLimitDateType : 0=none, 1=daily, 2=weekly, 3=monthly, 4=yearly -->
                                                        <a href="#limit_date_type"><?php echo lang('Same with limit date type'); ?></a>
                                                        </div>

                                                        <div class="col-md-12" style="padding-top:5px;">
                                                            <label class="control-label"><?=lang('cms.gameRecordStartDate');?> </label>
                                                            <input type="datetime-local" name="gameRecordStartDate" id="gameRecordStartDate" value="<?=date("Y-m-d") . 'T00:00:00'?>" style="width:100%" class="form-control input-sm">
                                                        </div>
                                                        <div class="col-md-12" style="padding-top:5px;">
                                                            <label class="control-label"><?=lang('cms.gameRecordEndDate');?> </label>
                                                            <input type="datetime-local" name="gameRecordEndDate" id="gameRecordEndDate" value="<?=date("Y-m-d") . 'T23:59:59'?>" style="width:100%" class="form-control input-sm">
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                            <!-- **********************
                                                 |  bonus condition     |
                                                 ********************** -->
                                            <h4>2. *<?=lang('cms.applyCondition');?></h4>
                                            <div class="form-group col-md-12" style="border-top:1px solid #ddd; padding:10px 0px 10px; 0px;">
                                                <!-- ****************************
                                                     |  by deposit succession   |
                                                     **************************** -->
                                                <div class="col-md-12" id="bonusConditionBydepositSuccessionSec">
                                                    <!-- <input type="radio" name="depositSuccessionOption" value="0" id="bonusConditionBydepositSuccession"> -->
                                                    <label class="control-label"><?=lang('cms.depSuccession');?> </label>
                                                    <i class="btn btn-default btn-xs" data-toggle="tooltip" title="<?=lang('cms.depSuccessionInfo')?>" data-placement="top" rel="tooltip">?</i>
                                                    <br/>
                                                        <div class="col-md-12" style="margin-top:5px; border:1px solid #ddd; margin-bottom:15px; padding:10px;">
                                                            <input type="radio" name="bonusReleaseTypeOptionBySuccession" value="<?php echo Promorules::DEPOSIT_SUCCESION_TYPE_FIRST; ?>" id="bonusConditionBydepositSuccessionFirst">
                                                            <label class="control-label"><?=lang('cms.firstDep');?> </label>&nbsp;&nbsp;&nbsp;
                                                            <input type="radio" name="bonusReleaseTypeOptionBySuccession" value="<?php echo Promorules::DEPOSIT_SUCCESION_TYPE_NOT_FIRST; ?>" id="bonusConditionBydepositSuccessionNotFirst">
                                                            <label class="control-label"><?=lang('cms.not_first_deposit');?> </label>&nbsp;&nbsp;&nbsp;
                                                            <input type="radio" name="bonusReleaseTypeOptionBySuccession" value="<?php echo Promorules::DEPOSIT_SUCCESION_TYPE_ANY; ?>" id="bonusConditionBydepositSuccessionOthers">
                                                            <label class="control-label"><?=lang('cms.others');?> </label>
                                                            <input type="number" name="depositCnt" id="bonusConditionBydepositSuccessionOthersDepositCnt" style="width:60px;" class="form-control input-sm number_only" min="2">
                                                            <label class="control-label"><?=lang('cms.timesDep');?> </label>
                                                            <input type="radio" name="bonusReleaseTypeOptionBySuccession" value="<?php echo Promorules::DEPOSIT_SUCCESION_TYPE_EVERY_TIME; ?>" id="bonusConditionBydepositSuccessionEveryTime">
                                                            <label class="control-label"><?=lang('Every Time Deposit');?> </label>&nbsp;&nbsp;&nbsp;

                                                            <div class="col-md-12" style="margin:10px 0px 10px 0px;">
                                                                <fieldset style="padding:10px;">
                                                                    <legend style="font-size:12px"><?=lang('cms.inPeriodOf');?></legend>
                                                                        <input type="radio" name="depositSuccessionPeriodOption" value="1" id="depositSuccessionPeriodOptionStartFromReg">
                                                                        <label class="control-label"><?=lang('cms.startFromReg');?> </label>
                                                                        <input type="radio" name="depositSuccessionPeriodOption" value="4" id="depositSuccessionPeriodOptionBonusExpire">
                                                                        <label class="control-label"><?=lang('cms.bonusexp');?> </label>
                                                                 </fieldset>
                                                            </div>
                                                        </div>

                                                </div>
                                                <div class="col-md-12" id="customBonusConditionSec">
                                                    <label class="control-label" style='font-size:10px;'><?php echo lang('Customize Condition'); ?> (<?php echo lang('It will override other bonus conditions'); ?>)</label>
                                                    <input type="hidden" name="formula_bonus_condition">
                                                    <textarea id="txt_formula_bonus_condition" rows="16" cols="60"></textarea>
                                                </div>
                                               <!-- ****************************
                                                     |  application condition   |
                                                     **************************** -->
                                                <div class="col-md-12" id="bonusConditionByApplicationSec">
                                                    <a name="limit_date_type"></a>
                                                    <!-- <input type="radio" name="depositSuccessionOption" value="1" id="bonusConditionByApplication"> -->
                                                    <label class="control-label"><?=lang('cms.singleOrMultiple');?> </label>
                                                    <i class="btn btn-default btn-xs" data-toggle="tooltip" title="<?=lang('cms.nonDepSuccession')?>" data-placement="top" rel="tooltip">?</i>
                                                    <br/>
                                                         <!-- ************************
                                                             |  application limit   | = bonusApplicationLimitRule , bonusApplicationLimitRuleCnt
                                                             ************************ -->
                                                        <div class="col-md-12" style="margin-top:5px; border:1px solid #ddd; margin-bottom:15px; padding:10px;">
                                                            <div class="row">
                                                                <div class="col-md-12 form-group">
                                                                <!-- bonusApplicationLimitDateType : 0=none, 1=daily, 2=weekly, 3=monthly, 4=yearly -->
                                                                <?php echo lang('Limit Date Type'); ?>:
                                                                <input type="radio" name="bonusApplicationLimitDateType" value="0" id="bonusApplicationLimitDateTypeNone" class="form-control"> <?php echo lang('None'); ?>
                                                                <input type="radio" name="bonusApplicationLimitDateType" value="1" id="bonusApplicationLimitDateTypeDaily" class="form-control"> <?php echo lang('Daily'); ?>
                                                                <input type="radio" name="bonusApplicationLimitDateType" value="2" id="bonusApplicationLimitDateTypeWeekly" class="form-control"> <?php echo lang('Weekly'); ?>
                                                                <input type="radio" name="bonusApplicationLimitDateType" value="3" id="bonusApplicationLimitDateTypeMonthly" class="form-control"> <?php echo lang('Monthly'); ?>
                                                                <input type="radio" name="bonusApplicationLimitDateType" value="4" id="bonusApplicationLimitDateTypeYearly" class="form-control"> <?php echo lang('Yearly'); ?>
                                                                </div>
                                                                <div class="col-md-12 form-group">
                                                                <input type="radio" name="bonusReleaseTypeOptionByNonSuccessionLimitOption" value="1" id="bonusConditionByApplicationWithLimit">
                                                                <label class="control-label"><?=lang('cms.withLimit');?> </label>
                                                                <input type="number" step='1' name="limitCnt" id="bonusConditionByApplicationLimitCnt" min="1" style="width:15%; height:30px;" class="form-control input-sm number_only">
                                                                <input type="radio" name="bonusReleaseTypeOptionByNonSuccessionLimitOption" value="0" id="bonusConditionByApplicationNoLimit">
                                                                <label class="control-label"><?=lang('cms.noLimit');?> </label>&nbsp;&nbsp;&nbsp;
                                                                </div>

                                                            </div>
                                                        </div>
                                                </div>
                                                <div class="col-md-12 hide" id="noBonusConditionSec">
                                                    <label class="control-label"><?php echo lang('cms.once_and_no_condition'); ?></label>
                                                </div>
                                            </div>

                                            <!-- ********************
                                             |  bonus  release    |
                                             ******************** -->
                                            <h4>3. *<?=lang('cms.bonusRelease');?></h4>
                                            <div class="form-group col-md-12" style="margin-bottom:5px;">
                                                <div style="border-top:1px solid #ddd; padding:10px 0px 10px; 0px;">
                                                    <!-- Fixed Bonus Amount -->
                                                    <div class="col-md-12" id="bonusReleaseFixedAmountSec" style="margin-top:5px; border:1px solid #ddd; margin-bottom:15px; padding:10px;">
                                                        <input type="radio" name="bonusReleaseTypeOption" id="bonusReleaseTypeFixedAmount" value="<?php echo Promorules::BONUS_RELEASE_RULE_FIXED_AMOUNT; ?>">
                                                        <label class="control-label"><?=lang('cms.fixedBonusAmount');?></label>
                                                        <input type="number" step='any' name="bonusReleaseBonusAmount" id="bonusReleaseBonusAmount" style="width:100px;" class="form-control input-sm amount_only">
                                                    </div>
                                                    <!-- (x) % of deposit amount, up to (y) -->
                                                    <div class="col-md-12" id="depositPercentageSec" style="margin-top:5px; border:1px solid #ddd; margin-bottom:15px; padding:10px;">
                                                        <input type="radio" name="bonusReleaseTypeOption" id="bonusReleaseTypeDepositPercentage" value="<?php echo Promorules::BONUS_RELEASE_RULE_DEPOSIT_PERCENTAGE; ?>">
                                                        <input type="number" name="depositPercentage" id="depositPercentage" style="width:100px;" class="form-control input-sm amount_only" step="0.01" min="0" onkeyup="this.value=this.value.replace(/\.\d{2,}$/,value.substr(value.indexOf('.'),3));">
                                                        <label class="control-label" style='font-size:10px;'><?=lang('cms.percentageOfDepositAmt');?> </label>
                                                        <input type="number" step='any' min="1" name="bonusReleaseMaxBonusAmount" id="bonusReleaseMaxBonusAmount" style="width:100px;" class="form-control input-sm amount_only" onkeyup="this.value=this.value.replace(/^0/g,'');">
                                                        <br/>
                                                        <input type="checkbox" name="max_bonus_by_limit_date_type" id="max_bonus_by_limit_date_type" value="true">
                                                        <label class="control-label"><?=lang('By Limit Date Type');?></label>
                                                    </div>
                                                    <!-- % of bet -->
                                                    <div class="col-md-12" id="bonusReleaseBetPercentageSec" style="margin-top:5px; border:1px solid #ddd; margin-bottom:15px; padding:10px;">
                                                        <input type="radio" name="bonusReleaseTypeOption" id="bonusReleaseTypeBetPercentage" value="<?php echo Promorules::BONUS_RELEASE_RULE_BET_PERCENTAGE; ?>">
                                                        <input type="number" step='any' name="bonusReleaseBonusPercentage" id="bonusReleaseBonusPercentage" style="width:80px;" class="form-control input-sm amount_only bonusReleaseBonusPercentage">
                                                        <label class="control-label"><?=lang('cms.betPercentage');?> </label>
                                                        <i class="btn btn-default btn-xs bonusReleaseBonusPercentageLbl" id="bonusReleaseBonusPercentageLbl" data-toggle="tooltip" title="<?=lang('promo.promoNonDepositMessage')?>" data-placement="top" style="height:18px;" rel="tooltip">?</i>
                                                        <br/>
                                                    </div>
                                                    <!-- Bonus Games -->
                                                <?php if ($this->utils->isEnabledFeature('bonus_games__support_bonus_game_in_promo_rules_settings')) : ?>
                                                    <div class="col-md-12" id="bonus_game" style="margin-top:5px; border:1px solid #ddd; margin-bottom:15px; padding:10px; line-height: 32px;">
                                                        <div class="row">
                                                            <div class="col-xs-3">
                                                                <label>
                                                                    <input type="radio" name="bonusReleaseTypeOption" id="bonusReleaseTypeBonusGame" value="<?=  Promorules::BONUS_RELEASE_RULE_BONUS_GAME; ?>">
                                                                    <?= lang('Bonus Game') ?>
                                                                </label>
                                                            </div>
                                                            <div class="col-xs-6"div>
                                                                <select class="form-control input-sm" name="bg_game_id" id="bg_game_id"></select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-5">
                                                                <label for="bg_play_rounds"><?= lang('Number of Rounds (each player)') ?></label>
                                                            </div>
                                                            <div class="col-xs-2">
                                                                <input type="number" step='1' name="bg_play_rounds" id="bg_play_rounds" style="width:80px;" class="form-control input-sm amount_only bg_play_rounds">
                                                            </div>
                                                            <div class="col-xs-5">
                                                                <span class="field-error bg_play_rounds" style="display: none;"><?= lang('Please enter the number of rounds.') ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-5">
                                                                <label>
                                                                    <input type="checkbox" name="bg_budget_cash_enable" id="bg_budget_cash_enable" data-target="#bg_budget_cash" value="1" data-type="cash">
                                                                    <?= lang('Budget for cash bonus - up to') ?>
                                                                </label>
                                                            </div>
                                                            <div class="col-xs-2">
                                                                <input type="number" step='any' name="bg_budget_cash" id="bg_budget_cash" style="width:80px;" class="form-control input-sm amount_only bg_budget_cash" data-type="cash">
                                                            </div>
                                                            <div class="col-xs-5">
                                                                <span class="field-error bg_budget_cash" style="display: none;"><?= lang('Please enter the cash budget.') ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-xs-5">
                                                                <label>
                                                                    <input type="checkbox" name="bg_budget_vipexp_enable" id="bg_budget_vipexp_enable" data-target="#bg_budget_vipexp" value="1" data-type="vip_exp">
                                                                    <?= lang('Budget for VIP Exp. - up to') ?>
                                                                </label>
                                                            </div>
                                                            <div class="col-xs-2">
                                                                <input type="number" step='any' name="bg_budget_vipexp" id="bg_budget_vipexp" style="width:80px;" class="form-control input-sm amount_only bg_budget_vipexp" data-type="vip_exp">
                                                            </div>
                                                            <div class="col-xs-5">
                                                                <span class="field-error bg_budget_vipexp" style="display: none;"><?= lang('Please enter the VIP experience budget.') ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                    <!-- Customize (js) -->
                                                    <div class="col-md-12" id="bonusReleaseCustomSec" style="margin-top:5px; border:1px solid #ddd; margin-bottom:15px; padding:10px;">
                                                        <input type="radio" name="bonusReleaseTypeOption" id="bonusReleaseTypeCustom" value="<?php echo Promorules::BONUS_RELEASE_RULE_CUSTOM; ?>">
                                                        <label class="control-label" style='font-size:10px;'><?php echo lang('promo_rule.bonus_release_custom'); ?></label>
                                                        <br><br>
                                                        <input type="hidden" name="formula_bonus_release">
                                                        <textarea id="txt_formula_bonus_release" rows="16" cols="60"></textarea>
                                                        <br/>
                                                    </div>

                                                    <div class="col-md-12" id="releaseToSubWalletSec" style="margin-top:5px; border:1px solid #ddd; margin-bottom:15px; padding:10px;">
                                                        <input type="radio" name="releaseToSubWallet" id="releaseToSubWallet0" value="0">
                                                        <label class="control-label" for="releaseToSubWallet0"><?php echo lang('Main Wallet'); ?></label>
                                                        <?php
                                                        if(!empty($subWallets)){
                                                        foreach ($subWallets as $subWallet) { ?>
                                                        <input type="radio" name="releaseToSubWallet" id="releaseToSubWallet<?php echo $subWallet['id'];?>" value="<?php echo $subWallet['id'];?>">
                                                        <label class="control-label" for="releaseToSubWallet<?php echo $subWallet['id'];?>"><?php echo $subWallet['system_code'];?></label>
                                                        <?php }
                                                        }?>
                                                    </div>


<!--                                                     <div class="col-md-12" id="release_when_finish_withdraw_cond_sec" style="margin-top:5px; border:1px solid #ddd; margin-bottom:15px; padding:10px;">
                                                        <input type="radio" name="release_when_finish_withdraw_cond" id="release_when_finish_withdraw_cond_false" value="false">
                                                        <label class="control-label" for="release_when_finish_withdraw_cond_false"></label>
                                                        <input type="radio" name="release_when_finish_withdraw_cond" id="release_when_finish_withdraw_cond_true" value="true">
                                                        <label class="control-label" for="release_when_finish_withdraw_cond_true"></label>
                                                    </div>
 -->
                                                </div>
                                            </div>
                                        </div> <!-- left_column end-->

                                        <!-- **********************
                                             |  allowed game type |
                                             ********************** -->
                                        <div class="form-group col-md-6 right_column">
                                            <div class="row">

                                            <h4 class="allowedGameTypeLbl col-md-9">4. <?=lang('cms.allowedGameType');?></h4>
                                            <?php if($this->utils->isEnabledFeature('enable_isolated_promo_game_tree_view')) { ?>
                                            <div class="col-md-3">
                                                <button type="button" id="edit_allowed_game_list_btn" class="btn btn-primary">
                                                    <i class="fa fa-plus-circle" aria-hidden="true"></i> <?=lang('Edit Setting');?>
                                                </button>
                                            </div>
                                            <?php } ?>
                                            <span id="game_tree_by_promo" style="color:red; font-size:10px;"></span>
                                            <div style="border-top:1px solid #ddd; padding:10px 0px 10px; 0px;">

                                                <div class="form-group col-md-12" id='treeAGT_sec'>
                                                    <div class="form-group col-md-12" style="border:1px solid #ddd; margin-bottom:15px; padding:10px 0px 10px; 0px; overflow-y:scroll; overflow-x:scroll;">
                                                        <?php include APPPATH . "/views/marketing_management/promorules/promo_game_list.php"; ?>
                                                        <?php //include APPPATH . "/views/includes/game_tree.php";?>
                                                        <input type="hidden" name="selected_game_tree" value="">
                                                        <div id="allowedGameTypeTree"></div>
                                                        <div id="allowed-promo-game-list-table" class="col-md-12"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            </div> <!-- row end -->

                                        <div class="row">
                                        <!-- ******************
                                             |  player levels |
                                             ****************** -->
                                            <div class="col-md-12" style="padding-top:10px;padding-left:5px;border-bottom:1px solid #ddd;margin-bottom:10px;">
                                                <h4>5. *<?=lang('Allowed Scope');?></h4>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group col-md-12">
                                                    <label>
                                                        <?php echo lang('Hide if not allow');?>
                                                        <input type="checkbox" name="hide_if_not_allow" id="hide_if_not_allow" value="true" data-on-text="Yes" data-off-text="No">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12" style="padding-top:10px;padding-left:5px;border-bottom:1px solid #ddd;margin-bottom:10px;">
                                                <h4>5.1 *<?=lang('Allowed Scope Condition');?></h4>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group col-md-12">
                                                    <label>
                                                        <?php echo lang('Allowed Scope Condition');?>
                                                        <input type="checkbox" name="allowed_scope_condition" id="allowed_scope_condition" value="true" data-on-text="AND" data-off-text="OR">
                                                    </label>
                                                </div>
                                            </div>
                                        <!-- ******************
                                             |  player levels |
                                             ****************** -->
                                            <div class="col-md-12" style="padding-top:10px;padding-left:5px;border-bottom:1px solid #ddd;margin-bottom:10px;">
                                                <h4>5.1.1 *<?=lang('cms.allowedPlayerLevel');?></h4>
                                                <span id="allowedPlayerLevelTxt" style="color:red; font-size:10px;"></span>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group col-md-12" style="border:1px solid #ddd; margin-bottom:15px; padding:10px 0px 10px; 0px; overflow-y:scroll; overflow-x:scroll;">
                                                    <?php //include APPPATH . "/views/includes/playerlevel_tree.php";?>
                                                    <input type="hidden" name="allowed_player_level" value="">
                                                    <div id="allowedPlayerLevel" class="col-md-12"></div>
                                                    <script type="text/javascript">
                                                        $(function(){
                                                            $('#allowedPlayerLevel').jstree({
                                                                'core' : {
                                                                    'data' : {
                                                                        "url" : "<?php echo site_url('/api/get_allowed_player_level/'. $this->uri->segment(3) ); ?>",
                                                                        "dataType" : "json"
                                                                    }
                                                                },
                                                                "checkbox":{
                                                                    "tie_selection": false,
                                                                },
                                                                "plugins":[
                                                                    "search","checkbox"
                                                                ]
                                                            });

                                                            $('#promoform').submit(function(e){
                                                                var selected_game=$('#allowedPlayerLevel').jstree('get_checked');
                                                                if(selected_game.length>0){
                                                                    $('#promoform input[name=allowed_player_level]').val(selected_game.join());
                                                                }else{
                                                                    $('#allowedPlayerLevelTxt').text("<?php echo lang('Please select player level!') ?>");
                                                                    e.preventDefault();
                                                                }
                                                            });
                                                        })
                                                    </script>
                                                </div>
                                            </div>


                                        <!-- **********************
                                             | allowed affiliates |
                                             ********************** -->
                                            <div class="col-md-12" style="padding-top:10px;padding-left:5px;border-bottom:1px solid #ddd;margin-bottom:10px;">
                                                <h4>5.1.2 <?=lang('Allowed Affiliates');?></h4>
                                                <label class="control-label" class="control-label" style="font-size:12px;"><?=lang('Affiliates');?>:*</label>
                                                <?php echo form_multiselect('affiliates[]', is_array($affiliates) ? $affiliates : array(), array_column($promoRuleDetails['affiliates'], 'affiliateId'), ' class="form-control input-sm chosen-select affiliates" id="addAffiliates" data-placeholder="' . lang("Select new applicable affiliates") . '" data-untoggle="checkbox" data-target="#form_add .toggle-checkbox .affiliates" ') ?>

                                                <div style="border: 1px solid #ccc;border-radius: 4px;margin-top: 10px;padding: 5px;">

                                                    <input type="file" name="csv_affiliates" id="csv_affiliates" onchange="return isValidFile(this)">
                                                    <span style="font-size:11px; font-style:italic; color: #919191;"><?=lang('CSV file note')?></span>
                                                </div>
                                                <p class="help-block affiliates-help-block pull-left"><i style="font-size:12px;color:#919191;"><?=lang('Applicable Affiliates for this Promotion');?></i></p>
                                            </div>


                                        <!-- **********************
                                             | allowed agents |
                                             ********************** -->
                                            <div class="col-md-12" style="padding-top:10px;padding-left:5px;border-bottom:1px solid #ddd;margin-bottom:10px;">
                                                <h4>5.1.3 <?=lang('Allowed Agents');?></h4>
                                                <label class="control-label" class="control-label" style="font-size:12px;"><?=lang('Agents');?>:*</label>
                                                <?php echo form_multiselect('agents[]', is_array($agents) ? $agents : array(), array_column($promoRuleDetails['agents'], 'agent_id'), ' class="form-control input-sm chosen-select agents" id="addAgents" data-placeholder="' . lang("Select new applicable agents") . '" data-untoggle="checkbox" data-target="#form_add .toggle-checkbox .agents" ') ?>

                                                <div style="border: 1px solid #ccc;border-radius: 4px;margin-top: 10px;padding: 5px;">

                                                    <input type="file" name="csv_agents" id="csv_agents" onchange="return isValidFile(this)">
                                                    <span style="font-size:11px; font-style:italic; color: #919191;"><?=lang('CSV file note')?></span>
                                                </div>
                                                <p class="help-block agents-help-block pull-left"><i style="font-size:12px;color:#919191;"><?=lang('Applicable Agents for this Promotion');?></i></p>
                                            </div>


                                        <!-- *******************
                                             | allowed players |
                                             ******************* -->
                                            <div class="col-md-12" style="padding-top:10px;padding-left:5px;border-bottom:1px solid #ddd;margin-bottom:10px;">
                                                <h4>5.2 <?=lang('Allowed Players');?></h4>
                                                <label class="control-label" class="control-label" style="font-size:12px;"><?=lang('Player');?>:*</label>
                                                <select class="js-data-example-ajax" id="addPlayers" name="players[]" multiple="multiple" style="width: 100%;">
                                                    <?php foreach ($promoRuleDetails['players'] as $player): ?>
                                                        <option value="<?=$player['playerId']?>" selected="selected"><?=$player['username']?></option>
                                                    <?php endforeach?>
                                                </select>

                                                <div style="border: 1px solid #ccc;border-radius: 4px;margin-top: 10px;padding: 5px;">

                                                    <input type="file" name="csv_players" id="csv_players" onchange="return isValidFile(this)">
                                                    <span style="font-size:11px; font-style:italic; color: #919191;"><?=lang('CSV file note')?></span>

                                                </div>
                                                <p class="help-block players-help-block pull-left"><i style="font-size:12px;color:#919191;"><?=lang('Applicable Players for this Promotion');?></i></p>
                                            </div>
                                        </div>

                                        <!-- ************************
                                             | withdraw requirement |
                                             ************************ -->
                                        <div class="row">
                                        <!-- <div class="col-md-7"> -->
                                            <h4>6. *<?=lang('Generate Withdraw Condition');?></h4>
                                            <div class="form-group col-md-12" style="border-top:1px solid #ddd; margin-bottom:15px; padding:10px 0px 10px; 0px;">
                                                <div class="col-md-12">
                                                    <fieldset>
                                                        <legend><h4>* <?=lang('cms.betCondition')?></h4></legend>
                                                        <div id="withdrawRequirementBettingConditionOption2Sec">
                                                            <input type="radio" name="withdrawRequirementBettingConditionOption" value="<?php echo Promorules::WITHDRAW_CONDITION_TYPE_BETTING_TIMES; ?>" id="withdrawRequirementBettingConditionOption2">
                                                            <label class="control-label bonusCondLbl1" style="font-size:10px;"><?=lang('cms.betAmountCondition1');?> </label>
                                                            <input type="number" step='any' name="withdrawReqBettingTimes"  min="1" id="withdrawReqBettingTimes" style="width:80px;" class="form-control input-sm number_only">
                                                            <label class="control-label"><?=lang('cms.bettingTimes');?> </label>
                                                            <input type="checkbox" name="withdrawShouldMinusDeposit" id="withdrawShouldMinusDeposit" value="true">
                                                            <label class="control-label"><?php echo lang('Minus Withdraw Condition of Deposit'); ?> = (<?php echo lang('Deposit'); ?> + <?php echo lang('Bonus'); ?>) x <?php echo lang('Times'); ?> - <?php echo lang('Deposit'); ?> x <?php echo lang('Deposit Times'); ?></label>
                                                            <br/><br/>
                                                        </div>

                                                        <input type="radio" name="withdrawRequirementBettingConditionOption" value="<?php echo Promorules::WITHDRAW_CONDITION_TYPE_BONUS_TIMES; ?>" id="withdrawRequirementBettingConditionOption5">
                                                        <label class="control-label bonusCondLbl1" style="font-size:10px;"><?=lang('cms.betAmountCondition2');?> </label>
                                                        <input type="number" step='any' name="withdrawReqBonusTimes"  min="1" id="withdrawReqBonusTimes" style="width:80px;" class="form-control input-sm number_only">
                                                        <br/><br/>

                                                        <input type="radio" name="withdrawRequirementBettingConditionOption" value="<?php echo Promorules::WITHDRAW_CONDITION_TYPE_FIXED_AMOUNT; ?>" id="withdrawRequirementBettingConditionOption1">
                                                        <label class="control-label"><span title="<?=lang('cms.greaterThan');?>" data-toggle="tooltip" rel="tooltip">(&#8805;)</span> <?=lang('cms.betAmount');?> </label>
                                                        <input type="number" min="1" step="any" name="withdrawReqBetAmount" id="withdrawReqBetAmount" style="width:120px;" class="form-control input-sm amount_only">
                                                        <br/><br/>

                                                        <div id="withdrawRequirementBettingConditionOption6Sec">
                                                            <input type="radio" name="withdrawRequirementBettingConditionOption" value="<?php echo Promorules::WITHDRAW_CONDITION_TYPE_BETTING_TIMES_CHECK_WITH_MAX_BONUS; ?>" id="withdrawRequirementBettingConditionOption6">
                                                            <label class="control-label bonusCondLbl1" style="font-size:10px;"><?=lang('cms.betAmountCondition1');?> </label>
                                                            <input type="number" step='any' name="withdrawReqBettingTimesCheckWithMaxBonus"  min="0.1" id="withdrawReqBettingTimesCheckWithMaxBonus" style="width:80px;" class="form-control input-sm number_only">
                                                            <i class="glyphicon glyphicon-info-sign wopt6-tooltip" data-toggle="tooltip" data-placement="auto" data-html="true" data-original-title='<?=lang('deposit amount <= max bonus divide by percent of bonus').lang('deposit amount > max bonus divide by percent of bonus');?>'></i>
                                                            <label class="control-label"><?=lang('cms.bettingTimes');?> </label><br>
                                                            <label class="control-label"><?=lang('and if Bonus > Maximum Bonus Setting , will times Only the Maximum Bonus Deposit Amount');?></label>
                                                            <br/><br/>
                                                        </div>

                                                        <input type="radio" name="withdrawRequirementBettingConditionOption" value="<?php echo Promorules::WITHDRAW_CONDITION_TYPE_CUSTOM; ?>" id="withdrawRequirementBettingConditionOption4">
                                                        <label class="control-label"><?=lang('promo_rule.withdraw_condition_custom');?> </label>
                                                        <br/><br/>
                                                        <input type="hidden" name="formula_withdraw_condition">
                                                        <textarea id="txt_formula_withdraw_condition" rows="10" cols="60"></textarea>
                                                        <br/>

                                                        <input class="control-label bonusCondLbl1" type="radio" name="withdrawRequirementBettingConditionOption" value="<?php echo Promorules::WITHDRAW_CONDITION_TYPE_NOTHING; ?>" id="withdrawRequirementBettingConditionOption3" data-toggle="tooltip" title="<?=lang('cms.noWithdrawReqRisk')?>" data-placement="top" rel="tooltip">
                                                        <label class="control-label"><?=lang('cms.no_any_withdraw_condtion');?> </label>
                                                        <br/><br/>
                                                    </fieldset>
                                                    <br/>
                                                    <!-- **********************************
                                                         |     deposit limit condition    |
                                                         ********************************* -->
                                                    <fieldset>
                                                        <legend><h4>* <?=lang('Minimum Deposit Requirements')?></h4></legend>
                                                        <input type="radio" name="withdrawRequirementDepositConditionOption" value="<?php echo Promorules::DEPOSIT_CONDITION_TYPE_MIN_LIMIT; ?>" id="withdrawRequirementDepositConditionOption2">
                                                        <label class="control-label"><?=lang('pay.mindepamt');?> <span title="<?=lang('cms.greaterThan');?>" data-toggle="tooltip" rel="tooltip">(&#8805;)</span></label>
                                                        <input type="number" min="1" step="any" name="withdrawReqDepMinLimit" id="withdrawReqDepMinLimit" style="width:120px;" class="form-control input-sm amount_only">
                                                        <br/><br/>

                                                        <input type="radio" name="withdrawRequirementDepositConditionOption" value="<?php echo Promorules::DEPOSIT_CONDITION_TYPE_MIN_LIMIT_SINCE_REGISTRATION; ?>" id="withdrawRequirementDepositConditionOption3">
                                                        <label class="control-label"><?=lang('cms.totalDepAmountSinceRegistration');?> <span title="<?=lang('cms.greaterThan');?>" data-toggle="tooltip" rel="tooltip">(&#8805;)</span></label>
                                                        <input type="number" min="1" step="any" name="withdrawReqDepMinLimitSinceRegistration" id="withdrawReqDepMinLimitSinceRegistration" style="width:120px;" class="form-control input-sm amount_only">
                                                        <br/><br/>

                                                        <input type="radio" name="withdrawRequirementDepositConditionOption" value="<?php echo Promorules::DEPOSIT_CONDITION_TYPE_NOTHING; ?>" id="withdrawRequirementDepositConditionOption1">
                                                        <label class="control-label"><?=lang('cms.no_any_deposit_condtion');?> </label>
                                                        <br/><br/>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>
<!--                                    <div class="row">
                                            <h4>7. *<?=lang('cms.withdrawalLimit');?></h4>
                                            <div class="form-group col-md-12" style="border-top:1px solid #ddd; margin-bottom:15px; padding:10px 0px 10px; 0px;">
                                                <div class="col-md-12" style="margin-top:5px; border:1px solid #ddd; margin:5px 0px 15px 10px; padding:10px;">
                                                    <label class="control-label"><?=lang('Max Limit of Withdrawal')?></label>: <input type="number" min="0" step="any" name="withdrawal_max_limit" id="withdrawal_max_limit" style="width:120px;" class="form-control input-sm amount_only">
                                                    <br/><br/>
                                                    <input class="control-label bonusCondLbl1" type="checkbox" name="ignore_withdrawal_max_limit_after_first_deposit" value="true" id="ignore_withdrawal_max_limit_after_first_deposit">
                                                    <label class="control-label"><?=lang('Ignore Withdrawal Max Limit After First Deposit').' ('.lang('Trigger On Withdrawal').')';?> </label>
                                                    <br/><br/>
                                                    <input class="control-label bonusCondLbl1" type="checkbox" name="always_apply_withdrawal_max_limit_when_first_deposit" value="true" id="always_apply_withdrawal_max_limit_when_first_deposit">
                                                    <label class="control-label"><?=lang('Always Apply Withdrawal Max Limit When First Deposit').' ('.lang('Trigger On First Deposit').')';?> </label>
                                                    <br/><br/>
                                                </div>
                                            </div>
                                        </div>
-->
                                        <?php if($this->utils->isEnabledFeature('enabled_transfer_condition')): ?>
                                        <!--********************
                                         | Transfer Conditions  |
                                         ********************* -->
                                        <div class="row">
                                            <h4>７. *<?=lang('cms.gen.transfer.condition');?></h4>
                                            <div class="form-group col-md-12" style="border-top:1px solid #ddd; margin-bottom:15px; padding:10px 0px 10px; 0px;">
                                                <div class="col-md-12">
                                                    <fieldset>
                                                        <legend><h4>* <?=lang('cms.transCon')?></h4></legend>
                                                        <?php if(!empty($subWallets)):?>
                                                        <div class="row" style="border-bottom:1px solid #ddd; margin-bottom:10px; padding:10px 0px 10px; 0px;">
                                                            <div class="form-group col-md-4">
                                                                <p><?=lang('cms.disallow_wallet_transfer_in')?></p>
                                                                <?php foreach ($subWallets as $subWallet):?>
                                                                    <input type="checkbox" class="transfer_condition_disallow_transfer_in_wallet" name="transfer_condition_disallow_transfer_in_wallet[]" id="transfer_condition_disallow_transfer_in_wallet<?=$subWallet['id'];?>" value="<?=$subWallet['id'];?>">
                                                                    <label class="control-label" for="transfer_condition_disallow_transfer_in_wallet<?=$subWallet['id'];?>"><?=$subWallet['system_code'];?></label>
                                                                    <br>
                                                                <?php endforeach;?>
                                                            </div>
                                                            <div class="form-group col-md-4">
                                                                <p><?=lang('cms.disallow_wallet_transfer_out')?></p>
                                                                <?php foreach ($subWallets as $subWallet):?>
                                                                    <input type="checkbox" class="transfer_condition_disallow_transfer_out_wallet" name="transfer_condition_disallow_transfer_out_wallet[]" id="transfer_condition_disallow_transfer_out_wallet<?=$subWallet['id'];?>" value="<?=$subWallet['id'];?>">
                                                                    <label class="control-label" for="transfer_condition_disallow_transfer_out_wallet<?=$subWallet['id'];?>"><?=$subWallet['system_code'];?></label>
                                                                    <br>
                                                                <?php endforeach;?>
                                                            </div>
                                                        </div>
                                                        <?php endif;?>
                                                        <div class="col-md-12" style="padding:10px 0px;">
                                                            <input type="radio" name="transferRequirementBettingConditionOption" value="<?php echo Promorules::TRANSFER_CONDITION_TYPE_BETTING_TIMES; ?>" id="transferRequirementBettingConditionOption3">
                                                            <label class="control-label bonusCondLbl1" style="font-size:10px;"><?=lang('cms.betAmountCondition1');?> </label>
                                                            <input type="number" step='any' name="transferReqBettingTimes"  min="0.1" id="transferReqBettingTimes" style="width:80px;" class="form-control input-sm number_only">
                                                            <label class="control-label"><?=lang('cms.bettingTimes');?> </label>
                                                            <input type="checkbox" name="transferShouldMinusDeposit" id="transferShouldMinusDeposit" value="true">
                                                            <label class="control-label"><?php echo lang('Minus Transfer Condition of Deposit'); ?> = (<?php echo lang('Deposit'); ?> + <?php echo lang('Bonus'); ?>) x <?php echo lang('Times'); ?> - <?php echo lang('Deposit'); ?> x <?php echo lang('Deposit Times'); ?></label>
                                                        </div>
                                                        <div class="col-md-12" style="padding:10px 0px;">
                                                            <input type="radio" name="transferRequirementBettingConditionOption" value="<?=Promorules::TRANSFER_CONDITION_TYPE_BONUS_TIMES; ?>" id="transferRequirementBettingConditionOption2">
                                                            <label class="control-label bonusCondLbl1" style="font-size:10px;"><?=lang('cms.betAmountCondition2');?> </label>
                                                            <input type="number" step='any' name="transferReqBonusTimes"  min="0.1" id="transferReqBonusTimes" style="width:80px;" class="form-control input-sm number_only">
                                                        </div>
                                                        <div class="col-md-12" style="padding:15px 0px;">
                                                            <input type="radio" name="transferRequirementBettingConditionOption" value="<?php echo Promorules::TRANSFER_CONDITION_TYPE_FIXED_AMOUNT; ?>" id="transferRequirementBettingConditionOption4">
                                                            <label class="control-label"><span title="<?=lang('cms.greaterThan');?>" data-toggle="tooltip" rel="tooltip">(&#8805;)</span> <?=lang('cms.betAmount');?> </label>
                                                            <input type="number" min="1" step="any" name="transferReqBetAmount" id="transferReqBetAmount" style="width:120px;" class="form-control input-sm amount_only">
                                                        </div>
                                                        <div class="col-md-12" style="padding:15px 0px;">
                                                            <input type="radio" name="transferRequirementBettingConditionOption" value="<?php echo Promorules::TRANSFER_CONDITION_TYPE_CUSTOM; ?>" id="transferRequirementBettingConditionOption5">
                                                            <label class="control-label"><?=lang('promo_rule.transfer_condition_custom');?> </label>
                                                            <br><br>
                                                            <input type="hidden" name="formula_transfer_condition">
                                                            <textarea id="txt_formula_transfer_condition" rows="10" cols="60"></textarea>
                                                        </div>
                                                        <div class="col-md-12" style="padding:15px 0px;">
                                                            <input class="control-label bonusCondLbl1" type="radio" name="transferRequirementBettingConditionOption" value="<?=Promorules::TRANSFER_CONDITION_TYPE_NOTHING; ?>" id="transferRequirementBettingConditionOption1">
                                                            <label class="control-label"><?=lang('cms.no_any_transfer_condtion');?> </label>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif ?>
                                    </div> <!-- right_column end-->
                                </div>
                            </div>

                            <div class="row">
                                    <br/>
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                        </div>
                                            <?php if ($promoRuleDetails['enable_edit'] == 1 || empty($promoRuleDetails['promorulesId'])) {?>
                                                <div class="col-md-3">
                                                    <input type="button" value="<?=lang('button.back')?>" class="btn_cancel btn btn-sm btn-block <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-primary' ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="button" id="save_promo_rule_btn" value="<?=lang('Save')?>" class="btn_submit btn btn-sm btn-block <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-info' ?>">
                                                    <input type="submit" value="<?=lang('Save & Back')?>" class="btn_submit btn btn-sm btn-block <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-primary' ?>" style="display:none">
                                                </div>
                                            <?php } else { ?>
                                        <div class="col-md-6">
                                            <label class="control-label"><i class="fa fa-info-circle" aria-hidden="true"></i>  <?=lang('cms.no_save_permission');?> </label>
                                        </div>
                                            <?php } ?>
                                        <div class="col-md-3">
                                        </div>
                                    </div>
                            </div>
                    </form>
                </div>
                <hr/>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $form = $("#promoform");

    $('#save_promo_rule_btn').click(function(){
        // use html5 validation
        if(!$form[0].checkValidity()) {
            $form.find(':submit').click();
        } else {
            if(valthis()){
                $('#promoform').submit();
            }
        }
    });

    $('.btn_cancel').click(function(){
        window.history.back(-1);
    });

    //for promo code
    var promoRuleDetails = "<?=$promoRuleDetails ? 'true' : 'false';?>";
    var FLAG_TRUE = 'true';
    var FLAG_FALSE = 'false';

    var PROMO_TYPE_DEPOSIT = 0;
    var PROMO_TYPE_NONDEPOSIT = 1;
    var PROMO_TYPE_DEPOSIT_RESET_UI = 2;
    var PROMO_TYPE_NONDEPOSIT_RESET_UI = 3;

    <?php echo $this->utils->getJSNumberVariableDefineList($promoRuleDetails, array(
	'promorulesId', 'promoType', 'nonfixedDepositMinAmount',
	'nonfixedDepositMaxAmount', 'depositConditionNonFixedDepositAmount', 'withdrawRequirementBetAmount',
	'withdrawRequirementBetCntCondition', 'withdrawShouldMinusDeposit', 'withdrawRequirementConditionType',
    'transferRequirementWalletsInfo', 'transferRequirementConditionType', 'transferRequirementBetCntCondition',
    'transferShouldMinusDeposit', 'transferRequirementBetAmount',
    'withdrawRequirementDepositConditionType', 'withdrawRequirementDepositAmount', 'depositSuccesionType',
	'depositSuccesionCnt', 'bonusApplicationLimitRule', 'bonusApplicationLimitRuleCnt', 'bonusApplicationLimitDateType', 'depositSuccesionPeriod',
	'nonDepositPromoType', 'gameRequiredBet', 'bonusReleaseRule',
	'depositPercentage', 'rescue_min_balance')); ?>

    var NONDEPOSIT_BY_EMAIL = <?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_EMAIL; ?>;
    var NONDEPOSIT_BY_MOBILE = <?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_MOBILE; ?>;
    var NONDEPOSIT_BY_REGACCT = <?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_REGISTRATION; ?>;
    var NONDEPOSIT_BY_COMREG = <?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_COMPLETE_PLAYER_INFO; ?>;
    var NONDEPOSIT_BY_BETTING = <?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_BETTING; ?>;
    var NONDEPOSIT_BY_LOSS = <?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_LOSS; ?>;
    var NONDEPOSIT_BY_LOSS_MINUS_WIN = <?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_LOSS_MINUS_WIN; ?>;
    var NONDEPOSIT_BY_WINNING = <?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_WINNING; ?>;
    var NONDEPOSIT_BY_RESCUE = <?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_RESCUE; ?>;
    var NONDEPOSIT_BY_CUSTOMIZE = <?php echo Promorules::NON_DEPOSIT_PROMO_TYPE_CUSTOMIZE; ?>;

    var NONFIXED_DEPOSIT_CONDITION_OPTION1 = 0;
    var NONFIXED_DEPOSIT_CONDITION_OPTION2 = 1;

    var BONUSCONDITION_BY_DEPOSIT_SUCCESSION_FIRST = 0;
    // var BONUSCONDITION_BY_DEPOSIT_SUCCESSION_SECOND = 1;
    // var BONUSCONDITION_BY_DEPOSIT_SUCCESSION_THIRD = 2;
    var BONUSCONDITION_BY_DEPOSIT_SUCCESSION_OTHERS = 3;
    var BONUSCONDITION_BY_DEPOSIT_SUCCESSION_NOT_FIRST = 4;
    var BONUSCONDITION_BY_DEPOSIT_SUCCESSION_EVERY_TIME = 5;

    var DEPOSIT_SUCCESSION_PERIOD_OPTION_STARTFROMREG = 1;
    // var DEPOSIT_SUCCESSION_PERIOD_OPTION_THISWEEK = 2;
    // var DEPOSIT_SUCCESSION_PERIOD_OPTION_THISMONTH = 3;
    var DEPOSIT_SUCCESSION_PERIOD_OPTION_AVAILABLE_PROMOTION = 4;

    var BONUS_CONDITION_BY_APPLICATION_NOLIMIT = 0;
    var BONUS_CONDITION_BY_APPLICATION_WITHLIMIT = 1;

    var BONUS_APPLICATION_LIMIT_DATE_TYPE_NONE = <?php echo Promorules::BONUS_APPLICATION_LIMIT_DATE_TYPE_NONE; ?>;
    var BONUS_APPLICATION_LIMIT_DATE_TYPE_DAILY = <?php echo Promorules::BONUS_APPLICATION_LIMIT_DATE_TYPE_DAILY; ?>;
    var BONUS_APPLICATION_LIMIT_DATE_TYPE_WEEKLY = <?php echo Promorules::BONUS_APPLICATION_LIMIT_DATE_TYPE_WEEKLY; ?>;
    var BONUS_APPLICATION_LIMIT_DATE_TYPE_MONTHLY = <?php echo Promorules::BONUS_APPLICATION_LIMIT_DATE_TYPE_MONTHLY; ?>;
    var BONUS_APPLICATION_LIMIT_DATE_TYPE_YEARLY = <?php echo Promorules::BONUS_APPLICATION_LIMIT_DATE_TYPE_YEARLY; ?>;

    var BONUS_RELEASE_TYPE_FIXED_AMOUNT = <?php echo Promorules::BONUS_RELEASE_RULE_FIXED_AMOUNT; ?>;
    var BONUS_RELEASE_TYPE_DEPOSIT_PERCENTAGE = <?php echo Promorules::BONUS_RELEASE_RULE_DEPOSIT_PERCENTAGE; ?>;
    var BONUS_RELEASE_TYPE_BET_PERCENTAGE = <?php echo Promorules::BONUS_RELEASE_RULE_BET_PERCENTAGE; ?>;
    var BONUS_RELEASE_RULE_CUSTOM = <?php echo Promorules::BONUS_RELEASE_RULE_CUSTOM; ?>;
    var BONUS_RELEASE_RULE_BONUS_GAME = <?php echo Promorules::BONUS_RELEASE_RULE_BONUS_GAME; ?>;

    var BONUS_RELEASE_TO_PLAYER_AUTO = 0;
    var BONUS_RELEASE_TO_PLAYER_MANUAL = 1;

    var WITHDRAW_REQUIREMENT_BETTING_CONDITION_OPTION1 = <?php echo Promorules::WITHDRAW_CONDITION_TYPE_FIXED_AMOUNT; ?>;
    var WITHDRAW_REQUIREMENT_BETTING_CONDITION_OPTION2 = <?php echo Promorules::WITHDRAW_CONDITION_TYPE_BETTING_TIMES; ?>;
    var WITHDRAW_REQUIREMENT_BETTING_CONDITION_OPTION3 = <?php echo Promorules::WITHDRAW_CONDITION_TYPE_NOTHING; ?>;
    var WITHDRAW_REQUIREMENT_BETTING_CONDITION_OPTION4 = <?php echo Promorules::WITHDRAW_CONDITION_TYPE_CUSTOM; ?>;
    var WITHDRAW_REQUIREMENT_BETTING_CONDITION_OPTION5 = <?php echo Promorules::WITHDRAW_CONDITION_TYPE_BONUS_TIMES; ?>;
    var WITHDRAW_REQUIREMENT_BETTING_CONDITION_OPTION6 = <?php echo Promorules::WITHDRAW_CONDITION_TYPE_BETTING_TIMES_CHECK_WITH_MAX_BONUS; ?>;

    var WITHDRAW_REQUIREMENT_DEPOSIT_CONDITION_OPTION1 = <?php echo Promorules::DEPOSIT_CONDITION_TYPE_NOTHING; ?>;
    var WITHDRAW_REQUIREMENT_DEPOSIT_CONDITION_OPTION2 = <?php echo Promorules::DEPOSIT_CONDITION_TYPE_MIN_LIMIT; ?>;
    var WITHDRAW_REQUIREMENT_DEPOSIT_CONDITION_OPTION3 = <?php echo Promorules::DEPOSIT_CONDITION_TYPE_MIN_LIMIT_SINCE_REGISTRATION; ?>;

    var TRANSFER_REQUIREMENT_BETTING_CONDITION_OPTION1 = <?php echo Promorules::TRANSFER_CONDITION_TYPE_NOTHING; ?>;
    var TRANSFER_REQUIREMENT_BETTING_CONDITION_OPTION2 = <?php echo Promorules::TRANSFER_CONDITION_TYPE_BONUS_TIMES; ?>;
    var TRANSFER_REQUIREMENT_BETTING_CONDITION_OPTION3 = <?php echo Promorules::TRANSFER_CONDITION_TYPE_BETTING_TIMES; ?>;
    var TRANSFER_REQUIREMENT_BETTING_CONDITION_OPTION4 = <?php echo Promorules::TRANSFER_CONDITION_TYPE_FIXED_AMOUNT; ?>;
    var TRANSFER_REQUIREMENT_BETTING_CONDITION_OPTION5 = <?php echo Promorules::TRANSFER_CONDITION_TYPE_CUSTOM; ?>;

    var CURRENT_PROMO_RULE=<?php echo json_encode($promoRuleDetails); ?>;
    var FORMULA=<?php echo !empty($promoRuleDetails['formula']) ? $promoRuleDetails['formula'] : '{}'; ?>;

    var PROMO_UI_SETTINGS={};
    PROMO_UI_SETTINGS[PROMO_TYPE_DEPOSIT]={
            'promoTypeDepositConditionLbl':true,
            'promoTypeNonDepositConditionLbl':false,
            'subwallet_trigger':true,
            'add_withdraw_condition_as_bonus_condition':true,
            'lbl_add_withdraw_condition_as_bonus_condition':true,
            'depositConditionSec':true,
            'nonDepositConditionSec':false,
            'betWinLossGamesSec':false,

            'bonusConditionBydepositSuccessionSec':true,
            'bonusConditionByApplicationSec':true,
            'noBonusConditionSec':false,
            'addWithdrawConditionAsBonusConditionSec': true,

            'bonusReleaseFixedAmountSec':true,
            'depositPercentageSec':true,
            'bonusReleaseBetPercentageSec':false,
            'bonusReleaseCustomSec':true,

            'autoManualReleaseSec':true,

            'treeAGT_sec':true,
            'withdrawRequirementBettingConditionOption2':true,
            'withdrawRequirementBettingConditionOption2Sec':true,
            'withdrawRequirementBettingConditionOption5':true,
            'withdrawShouldMinusDeposit':true,
            'withdrawRequirementBettingConditionOption1':true,
            'withdrawRequirementBettingConditionOption3':true,
            'withdrawRequirementBettingConditionOption6Sec':true,
            'withdrawRequirementBettingConditionOption6':true,

            'withdrawRequirementDepositConditionOption2':true,
            'withdrawReqDepMinLimit':true,
            'withdrawRequirementDepositConditionOption3':true,
            'withdrawReqDepMinLimitSinceRegistration':true,
            'withdrawRequirementDepositConditionOption1':true,

            'transferRequirementBettingConditionOption1':true,
            'transferReqBonusTimes':true,
            'transferRequirementBettingConditionOption2':true,
            'transferRequirementBettingConditionOption3':true,
            'transferRequirementBettingConditionOp5ion3':true,
        };

    PROMO_UI_SETTINGS[PROMO_TYPE_NONDEPOSIT+'-'+NONDEPOSIT_BY_EMAIL]={
            'promoTypeDepositConditionLbl':false,
            'promoTypeNonDepositConditionLbl':true,
            'subwallet_trigger':false,
            'add_withdraw_condition_as_bonus_condition':false,
            'lbl_add_withdraw_condition_as_bonus_condition':false,
            'depositConditionSec':false,
            'nonDepositConditionSec':true,
            'betWinLossGamesSec':false,

            'bonusConditionBydepositSuccessionSec':false,
            'bonusConditionByApplicationSec':false,
            'noBonusConditionSec':true,
            'addWithdrawConditionAsBonusConditionSec': true,

            'bonusReleaseFixedAmountSec':true,
            'depositPercentageSec':false,
            'bonusReleaseBetPercentageSec':false,
            'bonusReleaseCustomSec':true,

            'autoManualReleaseSec':true,

            'treeAGT_sec':true,
            'withdrawRequirementBettingConditionOption2':false,
            'withdrawRequirementBettingConditionOption2Sec':false,
            'withdrawRequirementBettingConditionOption5':true,
            'withdrawShouldMinusDeposit':true,
            'withdrawRequirementBettingConditionOption1':true,
            'withdrawRequirementBettingConditionOption3':true,
            'withdrawRequirementBettingConditionOption6':false,
            'withdrawRequirementBettingConditionOption6Sec':false,

            'withdrawRequirementDepositConditionOption2':true,
            'withdrawReqDepMinLimit':true,
            'withdrawRequirementDepositConditionOption3':true,
            'withdrawReqDepMinLimitSinceRegistration':true,
            'withdrawRequirementDepositConditionOption1':true,

            'transferRequirementBettingConditionOption1':true,
            'transferReqBonusTimes':true,
            'transferRequirementBettingConditionOption2':true,
            'transferRequirementBettingConditionOption3':true,
            'transferRequirementBettingConditionOp5ion3':true,
        };

    PROMO_UI_SETTINGS[PROMO_TYPE_NONDEPOSIT+'-'+NONDEPOSIT_BY_MOBILE]={
            'promoTypeDepositConditionLbl':false,
            'promoTypeNonDepositConditionLbl':true,
            'subwallet_trigger':false,
            'add_withdraw_condition_as_bonus_condition':false,
            'lbl_add_withdraw_condition_as_bonus_condition':false,
            'depositConditionSec':false,
            'nonDepositConditionSec':true,
            'betWinLossGamesSec':false,

            'bonusConditionBydepositSuccessionSec':false,
            'bonusConditionByApplicationSec':false,
            'noBonusConditionSec':true,
            'addWithdrawConditionAsBonusConditionSec': true,

            'bonusReleaseFixedAmountSec':true,
            'depositPercentageSec':false,
            'bonusReleaseBetPercentageSec':false,
            'bonusReleaseCustomSec':true,

            'autoManualReleaseSec':true,

            'treeAGT_sec':true,
            'withdrawRequirementBettingConditionOption2':false,
            'withdrawRequirementBettingConditionOption2Sec':false,
            'withdrawRequirementBettingConditionOption5':true,
            'withdrawShouldMinusDeposit':true,
            'withdrawRequirementBettingConditionOption1':true,
            'withdrawRequirementBettingConditionOption3':true,
            'withdrawRequirementBettingConditionOption6':false,
            'withdrawRequirementBettingConditionOption6Sec':false,

            'withdrawRequirementDepositConditionOption2':true,
            'withdrawReqDepMinLimit':true,
            'withdrawRequirementDepositConditionOption3':true,
            'withdrawReqDepMinLimitSinceRegistration':true,
            'withdrawRequirementDepositConditionOption1':true,

            'transferRequirementBettingConditionOption1':true,
            'transferReqBonusTimes':true,
            'transferRequirementBettingConditionOption2':true,
            'transferRequirementBettingConditionOption3':true,
            'transferRequirementBettingConditionOp5ion3':true,
        };

    PROMO_UI_SETTINGS[PROMO_TYPE_NONDEPOSIT+'-'+NONDEPOSIT_BY_REGACCT]={
            'promoTypeDepositConditionLbl':false,
            'promoTypeNonDepositConditionLbl':true,
            'subwallet_trigger':false,
            'add_withdraw_condition_as_bonus_condition':false,
            'lbl_add_withdraw_condition_as_bonus_condition':false,
            'depositConditionSec':false,
            'nonDepositConditionSec':true,
            'betWinLossGamesSec':false,

            'bonusConditionBydepositSuccessionSec':false,
            'bonusConditionByApplicationSec':false,
            'noBonusConditionSec':true,
            'addWithdrawConditionAsBonusConditionSec': true,

            'bonusReleaseFixedAmountSec':true,
            'depositPercentageSec':false,
            'bonusReleaseBetPercentageSec':false,
            'bonusReleaseCustomSec':true,

            'autoManualReleaseSec':true,

            'treeAGT_sec':true,
            'withdrawRequirementBettingConditionOption2':false,
            'withdrawRequirementBettingConditionOption2Sec':false,
            'withdrawRequirementBettingConditionOption5':true,
            'withdrawShouldMinusDeposit':true,
            'withdrawRequirementBettingConditionOption1':true,
            'withdrawRequirementBettingConditionOption3':true,
            'withdrawRequirementBettingConditionOption6':false,
            'withdrawRequirementBettingConditionOption6Sec':false,

            'withdrawRequirementDepositConditionOption2':true,
            'withdrawReqDepMinLimit':true,
            'withdrawRequirementDepositConditionOption3':true,
            'withdrawReqDepMinLimitSinceRegistration':true,
            'withdrawRequirementDepositConditionOption1':true,

            'transferRequirementBettingConditionOption1':true,
            'transferReqBonusTimes':true,
            'transferRequirementBettingConditionOption2':true,
            'transferRequirementBettingConditionOption3':true,
            'transferRequirementBettingConditionOp5ion3':true,
        };

    PROMO_UI_SETTINGS[PROMO_TYPE_NONDEPOSIT+'-'+NONDEPOSIT_BY_COMREG]={
            'promoTypeDepositConditionLbl':false,
            'promoTypeNonDepositConditionLbl':true,
            'subwallet_trigger':false,
            'add_withdraw_condition_as_bonus_condition':false,
            'lbl_add_withdraw_condition_as_bonus_condition':false,
            'depositConditionSec':false,
            'nonDepositConditionSec':true,
            'betWinLossGamesSec':false,

            'bonusConditionBydepositSuccessionSec':false,
            'bonusConditionByApplicationSec':false,
            'noBonusConditionSec':true,
            'addWithdrawConditionAsBonusConditionSec': true,

            'bonusReleaseFixedAmountSec':true,
            'depositPercentageSec':false,
            'bonusReleaseBetPercentageSec':false,
            'bonusReleaseCustomSec':true,

            'autoManualReleaseSec':true,

            'treeAGT_sec':true,
            'withdrawRequirementBettingConditionOption2':false,
            'withdrawRequirementBettingConditionOption2Sec':false,
            'withdrawRequirementBettingConditionOption5':true,
            'withdrawShouldMinusDeposit':true,
            'withdrawRequirementBettingConditionOption1':true,
            'withdrawRequirementBettingConditionOption3':true,
            'withdrawRequirementBettingConditionOption6':false,
            'withdrawRequirementBettingConditionOption6Sec':false,

            'withdrawRequirementDepositConditionOption2':true,
            'withdrawReqDepMinLimit':true,
            'withdrawRequirementDepositConditionOption3':true,
            'withdrawReqDepMinLimitSinceRegistration':true,
            'withdrawRequirementDepositConditionOption1':true,

            'transferRequirementBettingConditionOption1':true,
            'transferReqBonusTimes':true,
            'transferRequirementBettingConditionOption2':true,
            'transferRequirementBettingConditionOption3':true,
            'transferRequirementBettingConditionOp5ion3':true,
        };

    PROMO_UI_SETTINGS[PROMO_TYPE_NONDEPOSIT+'-'+NONDEPOSIT_BY_RESCUE]={
            'promoTypeDepositConditionLbl':false,
            'promoTypeNonDepositConditionLbl':true,
            'subwallet_trigger':false,
            'add_withdraw_condition_as_bonus_condition':false,
            'lbl_add_withdraw_condition_as_bonus_condition':false,
            'depositConditionSec':false,
            'nonDepositConditionSec':true,
            'betWinLossGamesSec':false,

            'bonusConditionBydepositSuccessionSec':false,
            'bonusConditionByApplicationSec':true,
            'noBonusConditionSec':false,
            'addWithdrawConditionAsBonusConditionSec': true,

            'bonusReleaseFixedAmountSec':true,
            'depositPercentageSec':false,
            'bonusReleaseBetPercentageSec':false,
            'bonusReleaseCustomSec':true,

            'autoManualReleaseSec':true,

            'treeAGT_sec':true,
            'withdrawRequirementBettingConditionOption2':false,
            'withdrawRequirementBettingConditionOption2Sec':false,
            'withdrawRequirementBettingConditionOption5':true,
            'withdrawShouldMinusDeposit':true,
            'withdrawRequirementBettingConditionOption1':true,
            'withdrawRequirementBettingConditionOption3':true,
            'withdrawRequirementBettingConditionOption6':false,
            'withdrawRequirementBettingConditionOption6Sec':false,

            'withdrawRequirementDepositConditionOption2':true,
            'withdrawReqDepMinLimit':true,
            'withdrawRequirementDepositConditionOption3':true,
            'withdrawReqDepMinLimitSinceRegistration':true,
            'withdrawRequirementDepositConditionOption1':true,

            'transferRequirementBettingConditionOption1':true,
            'transferReqBonusTimes':true,
            'transferRequirementBettingConditionOption2':true,
            'transferRequirementBettingConditionOption3':true,
            'transferRequirementBettingConditionOption5':true,
        };

    PROMO_UI_SETTINGS[PROMO_TYPE_NONDEPOSIT+'-'+NONDEPOSIT_BY_CUSTOMIZE]={
            'promoTypeDepositConditionLbl':false,
            'promoTypeNonDepositConditionLbl':true,
            'subwallet_trigger':false,
            'add_withdraw_condition_as_bonus_condition':false,
            'lbl_add_withdraw_condition_as_bonus_condition':false,
            'depositConditionSec':false,
            'nonDepositConditionSec':true,
            'betWinLossGamesSec':false,

            'bonusConditionBydepositSuccessionSec':false,
            'bonusConditionByApplicationSec':true,
            'noBonusConditionSec':false,
            'addWithdrawConditionAsBonusConditionSec': true,

            'bonusReleaseFixedAmountSec':true,
            'depositPercentageSec':false,
            'bonusReleaseBetPercentageSec':false,
            'bonusReleaseCustomSec':true,

            'autoManualReleaseSec':true,

            'treeAGT_sec':true,
            'withdrawRequirementBettingConditionOption2':false,
            'withdrawRequirementBettingConditionOption2Sec':false,
            'withdrawRequirementBettingConditionOption5':true,
            'withdrawShouldMinusDeposit':true,
            'withdrawRequirementBettingConditionOption1':true,
            'withdrawRequirementBettingConditionOption3':true,
            'withdrawRequirementBettingConditionOption6':false,
            'withdrawRequirementBettingConditionOption6Sec':false,

            'withdrawRequirementDepositConditionOption2':true,
            'withdrawReqDepMinLimit':true,
            'withdrawRequirementDepositConditionOption3':true,
            'withdrawReqDepMinLimitSinceRegistration':true,
            'withdrawRequirementDepositConditionOption1':true,

            'transferRequirementBettingConditionOption1':true,
            'transferReqBonusTimes':true,
            'transferRequirementBettingConditionOption2':true,
            'transferRequirementBettingConditionOption3':true,
            'transferRequirementBettingConditionOption5':true,
        };

    PROMO_UI_SETTINGS[PROMO_TYPE_NONDEPOSIT+'-'+NONDEPOSIT_BY_BETTING]={
            'promoTypeDepositConditionLbl':false,
            'promoTypeNonDepositConditionLbl':true,
            'subwallet_trigger':false,
            'add_withdraw_condition_as_bonus_condition':false,
            'lbl_add_withdraw_condition_as_bonus_condition':false,
            'depositConditionSec':false,
            'nonDepositConditionSec':true,
            'betWinLossGamesSec':true,

            'bonusConditionBydepositSuccessionSec':false,
            'bonusConditionByApplicationSec':true,
            'noBonusConditionSec':true,
            'addWithdrawConditionAsBonusConditionSec': true,

            'bonusReleaseFixedAmountSec':true,
            'depositPercentageSec':false,
            'bonusReleaseBetPercentageSec':true,
            'bonusReleaseCustomSec':true,

            'autoManualReleaseSec':true,

            'treeAGT_sec':true,
            'withdrawRequirementBettingConditionOption2':false,
            'withdrawRequirementBettingConditionOption2Sec':false,
            'withdrawRequirementBettingConditionOption5':true,
            'withdrawShouldMinusDeposit':true,
            'withdrawRequirementBettingConditionOption1':true,
            'withdrawRequirementBettingConditionOption3':true,
            'withdrawRequirementBettingConditionOption6':false,
            'withdrawRequirementBettingConditionOption6Sec':false,

            'withdrawRequirementDepositConditionOption2':true,
            'withdrawReqDepMinLimit':true,
            'withdrawRequirementDepositConditionOption3':true,
            'withdrawReqDepMinLimitSinceRegistration':true,
            'withdrawRequirementDepositConditionOption1':true,

            'transferRequirementBettingConditionOption1':true,
            'transferReqBonusTimes':true,
            'transferRequirementBettingConditionOption2':true,
            'transferRequirementBettingConditionOption3':true,
            'transferRequirementBettingConditionOption5':true,
        };

    PROMO_UI_SETTINGS[PROMO_TYPE_NONDEPOSIT+'-'+NONDEPOSIT_BY_LOSS]={
            'promoTypeDepositConditionLbl':false,
            'promoTypeNonDepositConditionLbl':true,
            'subwallet_trigger':false,
            'add_withdraw_condition_as_bonus_condition':false,
            'lbl_add_withdraw_condition_as_bonus_condition':false,
            'depositConditionSec':false,
            'nonDepositConditionSec':true,
            'betWinLossGamesSec':true,

            'bonusConditionBydepositSuccessionSec':false,
            'bonusConditionByApplicationSec':true,
            'noBonusConditionSec':false,
            'addWithdrawConditionAsBonusConditionSec': true,

            'bonusReleaseFixedAmountSec':true,
            'depositPercentageSec':false,
            'bonusReleaseBetPercentageSec':true,
            'bonusReleaseCustomSec':true,

            'autoManualReleaseSec':true,

            'treeAGT_sec':true,
            'withdrawRequirementBettingConditionOption2':false,
            'withdrawRequirementBettingConditionOption2Sec':false,
            'withdrawRequirementBettingConditionOption5':true,
            'withdrawShouldMinusDeposit':true,
            'withdrawRequirementBettingConditionOption1':true,
            'withdrawRequirementBettingConditionOption3':true,
            'withdrawRequirementBettingConditionOption6':false,
            'withdrawRequirementBettingConditionOption6Sec':false,

            'withdrawRequirementDepositConditionOption2':true,
            'withdrawReqDepMinLimit':true,
            'withdrawRequirementDepositConditionOption3':true,
            'withdrawReqDepMinLimitSinceRegistration':true,
            'withdrawRequirementDepositConditionOption1':true,

            'transferRequirementBettingConditionOption1':true,
            'transferReqBonusTimes':true,
            'transferRequirementBettingConditionOption2':true,
            'transferRequirementBettingConditionOption3':true,
            'transferRequirementBettingConditionOption5':true,
        };

    PROMO_UI_SETTINGS[PROMO_TYPE_NONDEPOSIT+'-'+NONDEPOSIT_BY_WINNING]={
            'promoTypeDepositConditionLbl':false,
            'promoTypeNonDepositConditionLbl':true,
            'subwallet_trigger':false,
            'add_withdraw_condition_as_bonus_condition':false,
            'lbl_add_withdraw_condition_as_bonus_condition':false,
            'depositConditionSec':false,
            'nonDepositConditionSec':true,
            'betWinLossGamesSec':true,

            'bonusConditionBydepositSuccessionSec':false,
            'bonusConditionByApplicationSec':true,
            'noBonusConditionSec':false,
            'addWithdrawConditionAsBonusConditionSec': true,

            'bonusReleaseFixedAmountSec':true,
            'depositPercentageSec':false,
            'bonusReleaseBetPercentageSec':true,
            'bonusReleaseCustomSec':true,

            'autoManualReleaseSec':true,

            'treeAGT_sec':true,
            'withdrawRequirementBettingConditionOption2':false,
            'withdrawRequirementBettingConditionOption2Sec':false,
            'withdrawRequirementBettingConditionOption5':true,
            'withdrawShouldMinusDeposit':true,
            'withdrawRequirementBettingConditionOption1':true,
            'withdrawRequirementBettingConditionOption3':true,
            'withdrawRequirementBettingConditionOption6':false,
            'withdrawRequirementBettingConditionOption6Sec':false,

            'withdrawRequirementDepositConditionOption2':true,
            'withdrawReqDepMinLimit':true,
            'withdrawRequirementDepositConditionOption3':true,
            'withdrawReqDepMinLimitSinceRegistration':true,
            'withdrawRequirementDepositConditionOption1':true,

            'transferRequirementBettingConditionOption1':true,
            'transferReqBonusTimes':true,
            'transferRequirementBettingConditionOption2':true,
            'transferRequirementBettingConditionOption3':true,
            'transferRequirementBettingConditionOption5':true,
        };

    PROMO_UI_SETTINGS[PROMO_TYPE_NONDEPOSIT+'-'+NONDEPOSIT_BY_LOSS_MINUS_WIN]={
            'promoTypeDepositConditionLbl':false,
            'promoTypeNonDepositConditionLbl':true,
            'subwallet_trigger':false,
            'add_withdraw_condition_as_bonus_condition':false,
            'lbl_add_withdraw_condition_as_bonus_condition':false,
            'depositConditionSec':false,
            'nonDepositConditionSec':true,
            'betWinLossGamesSec':true,

            'bonusConditionBydepositSuccessionSec':false,
            'bonusConditionByApplicationSec':true,
            'noBonusConditionSec':false,
            'addWithdrawConditionAsBonusConditionSec': true,

            'bonusReleaseFixedAmountSec':true,
            'depositPercentageSec':false,
            'bonusReleaseBetPercentageSec':true,
            'bonusReleaseCustomSec':true,

            'autoManualReleaseSec':true,

            'treeAGT_sec':true,
            'withdrawRequirementBettingConditionOption2':false,
            'withdrawRequirementBettingConditionOption2Sec':false,
            'withdrawRequirementBettingConditionOption5':true,
            'withdrawShouldMinusDeposit':true,
            'withdrawRequirementBettingConditionOption1':true,
            'withdrawRequirementBettingConditionOption3':true,
            'withdrawRequirementBettingConditionOption6':false,
            'withdrawRequirementBettingConditionOption6Sec':false,

            'withdrawRequirementDepositConditionOption2':true,
            'withdrawReqDepMinLimit':true,
            'withdrawRequirementDepositConditionOption3':true,
            'withdrawReqDepMinLimitSinceRegistration':true,
            'withdrawRequirementDepositConditionOption1':true,

            'transferRequirementBettingConditionOption1':true,
            'transferReqBonusTimes':true,
            'transferRequirementBettingConditionOption2':true,
            'transferRequirementBettingConditionOption3':true,
            'transferRequirementBettingConditionOption5':true,
        };

    var allOptions="#promoTypeDeposit, #promoTypeNonDeposit, #withdrawRequirementBettingConditionOption1 , #withdrawRequirementBettingConditionOption2, #withdrawRequirementBettingConditionOption5, #withdrawShouldMinusDeposit, #withdrawRequirementBettingConditionOption3, #withdrawRequirementBettingConditionOption4, #withdrawRequirementBettingConditionOption6, #bonusReleaseTypeFixedAmount, #bonusReleaseTypeDepositPercentage, #bonusReleaseTypeBetPercentage, #bonusReleaseTypeCustom, #nonDepositByMobile, #nonDepositByEmail, #nonDepositByRegAcct, #nonDepositByComReg, #nonDepositByRescue, #nonDepositByCustomized, #nonDepositByPromoBetAmt, #nonDepositByLoss, #nonDepositByWin, #nonDepositByLossMinusWin, #bonusConditionByApplicationNoLimit, #bonusConditionByApplicationWithLimit , #bonusApplicationLimitDateTypeDaily, #bonusApplicationLimitDateTypeWeekly, #bonusApplicationLimitDateTypeMonthly, #bonusApplicationLimitDateTypeYearly, #bonusConditionBydepositSuccessionFirst, #bonusConditionBydepositSuccessionOthers, #bonusConditionBydepositSuccessionNotFirst, #bonusConditionBydepositSuccessionEveryTime, #nonFixedDepositAmountConditionSecOption1, #nonFixedDepositAmountConditionSecOption2, #bonus_game, #withdrawRequirementDepositConditionOption2, #withdrawReqDepMinLimit, #withdrawRequirementDepositConditionOption3, #withdrawReqDepMinLimitSinceRegistration, #withdrawRequirementDepositConditionOption1, #transferRequirementBettingConditionOption1, #transferRequirementBettingConditionOption2, #transferRequirementBettingConditionOption3, #transferRequirementBettingConditionOption4, #transferRequirementBettingConditionOption5, #transferReqBonusTimes";

    function isValidFile(field) {

        var value = field.value;
        var res = value.split('.').pop();

        var oFile = document.getElementById(field.id).files[0];

        //maximum file size if 2MB
        if( oFile.size > 2000 ){
            $('#' + field.id).val('');
            return alert('<?=lang('Maximum file size should be less than 2MB')?>');
        }

        if( res != 'csv' ){
            $('#' + field.id).val('');
            return alert('<?=lang('Please enter valid File')?>');
        }
    }

    function setDisable(id){
        $(id).prop('disabled',true);
        $(id).prop('required',false);
    }

    function setEnable(id){
        $(id).prop('disabled',false);
        $(id).prop('required',true);
    }

    function setCheckAndUncheck(selCheck,selUncheck){
        setUncheck(selUncheck);
        setCheck(selCheck);
    }

    function setCheck(sel){
        $(sel).prop('checked',true);
    }

    function setUncheck(sel){
        $(sel).prop('checked',false);
    }

    function isCheckedById(id){
        return $('#'+id).is(':checked') && !$('#'+id).is(':disabled');
    }

    function renderPromoRule(){
        //by promotion
        var promoType=$("input[name=promoType]:checked").val();
        var settings;
        if(promoType==''+PROMO_TYPE_DEPOSIT){
            settings=PROMO_UI_SETTINGS[promoType];
        }else{
            var nonDepositType=$("input[name=nonDepositOption]:checked").val();
            settings=PROMO_UI_SETTINGS[promoType+'-'+nonDepositType];
        }
        // utils.safelog(settings);
        if(settings){
            $.each(settings,function(k,v){
                // utils.safelog(k+','+v);
                if(v){
                    $('#'+k).show();
                }else{
                    $('#'+k).hide();
                }

            });

            if(isCheckedById('promoTypeNonDeposit')){
                if(isCheckedById('withdrawRequirementBettingConditionOption2') || isCheckedById('withdrawRequirementBettingConditionOption6')){
                    setCheckAndUncheck('#withdrawRequirementBettingConditionOption5',
                        '#withdrawRequirementBettingConditionOption1, #withdrawRequirementBettingConditionOption4, #withdrawRequirementBettingConditionOption3');
                }
                setDisable('#nonfixedDepositMinAmount, #nonfixedDepositMaxAmount');
            }else{
                if(isCheckedById('nonFixedDepositAmountConditionSecOption1')){
                    setEnable('#nonfixedDepositMinAmount, #nonfixedDepositMaxAmount');
                }else if(isCheckedById('nonFixedDepositAmountConditionSecOption2')){
                    setDisable('#nonfixedDepositMinAmount, #nonfixedDepositMaxAmount');
                }
            }

            if(isCheckedById('nonDepositByPromoBetAmt') || isCheckedById('nonDepositByWin') ||
                isCheckedById('nonDepositByLoss')){
                setEnable('#gameRequiredBet, #gameRecordStartDate, #gameRecordEndDate');
            }else if(isCheckedById('nonDepositByLossMinusWin')){
                setEnable('#gameRequiredBet, #gameRequiredDateType');
                setDisable('#gameRecordStartDate, #gameRecordEndDate');
            }else if(isCheckedById('nonDepositByEmail') || isCheckedById('nonDepositByMobile') ||
                isCheckedById('nonDepositByRegAcct') || isCheckedById('nonDepositByComReg')
                ) {
                setDisable('#gameRequiredBet, #gameRecordStartDate, #gameRecordEndDate, #bonusConditionBydepositSuccessionOthersDepositCnt');
                setDisable('#bonusConditionByApplicationLimitCnt');
            }else if(isCheckedById('nonDepositByRescue')){
                setDisable('#gameRequiredBet, #gameRecordStartDate, #gameRecordEndDate, #bonusConditionBydepositSuccessionOthersDepositCnt');
            }else if(isCheckedById('nonDepositByCustomized')){
                setDisable('#gameRequiredBet, #gameRecordStartDate, #gameRecordEndDate, #bonusConditionBydepositSuccessionOthersDepositCnt');
            }
            if(isCheckedById('bonusConditionBydepositSuccessionFirst')){
                setDisable('#bonusConditionBydepositSuccessionOthersDepositCnt');
            }else if(isCheckedById('bonusConditionBydepositSuccessionNotFirst')){
                setDisable('#bonusConditionBydepositSuccessionOthersDepositCnt');
            }else if(isCheckedById('bonusConditionBydepositSuccessionEveryTime')){
                setDisable('#bonusConditionBydepositSuccessionOthersDepositCnt');
            }else if(isCheckedById('bonusConditionBydepositSuccessionOthers')){
                setEnable('#bonusConditionBydepositSuccessionOthersDepositCnt');
            }

            if(isCheckedById('bonusConditionByApplicationWithLimit')){
                setEnable('#bonusConditionByApplicationLimitCnt');
            }else if(isCheckedById('bonusConditionByApplicationNoLimit')){
                setDisable('#bonusConditionByApplicationLimitCnt');
            }

            // Section: Bonus Release panels on/off
            if(isCheckedById('bonusReleaseTypeFixedAmount')){
                setEnable('#bonusReleaseBonusAmount');
                setDisable('#depositPercentage, #bonusReleaseMaxBonusAmount, #bonusReleaseBonusPercentage, #txt_formula_bonus_release, #max_bonus_by_limit_date_type');

                //add fool proof with select withdrawRequirementBettingConditionOption6
                if(isCheckedById('withdrawRequirementBettingConditionOption6')){
                    setCheckAndUncheck('#withdrawRequirementBettingConditionOption1','#withdrawRequirementBettingConditionOption6');
                    setDisable('#withdrawReqBettingTimesCheckWithMaxBonus, #withdrawRequirementBettingConditionOption6');
                    $('#withdrawReqBettingTimesCheckWithMaxBonus').val('');
                }

            }else if(isCheckedById('bonusReleaseTypeDepositPercentage')){
                setEnable('#depositPercentage, #bonusReleaseMaxBonusAmount, #max_bonus_by_limit_date_type, #withdrawRequirementBettingConditionOption6, #withdrawReqBettingTimesCheckWithMaxBonus');
                $("#max_bonus_by_limit_date_type").prop('required', false);
                setDisable('#bonusReleaseBonusAmount, #bonusReleaseBonusPercentage, #bonusReleaseBonusPercentage, #txt_formula_bonus_release');
            }else if(isCheckedById('bonusReleaseTypeBetPercentage')){
                setEnable('#bonusReleaseBonusPercentage');
                setDisable('#depositPercentage, #bonusReleaseMaxBonusAmount, #bonusReleaseBonusAmount, #txt_formula_bonus_release, #max_bonus_by_limit_date_type');
                formulaBonusReleaseEditor ? formulaBonusReleaseEditor.setReadOnly(true) : null;
            }else if(isCheckedById('bonusReleaseTypeCustom')){
                setEnable('#txt_formula_bonus_release');
                setDisable('#depositPercentage, #bonusReleaseMaxBonusAmount, #bonusReleaseBonusAmount, #bonusReleaseBonusPercentage, #max_bonus_by_limit_date_type');
                formulaBonusReleaseEditor ? formulaBonusReleaseEditor.setReadOnly(false) : null;
            }
            else if (isCheckedById('bonusReleaseTypeBonusGame')) {
                setDisable('#bonusReleaseBonusAmount, #bonusReleaseBonusPercentage, #bonusReleaseBonusPercentage, #txt_formula_bonus_release, #depositPercentage, #bonusReleaseMaxBonusAmount, #max_bonus_by_limit_date_type');
            }

            if(isCheckedById('withdrawRequirementBettingConditionOption2')){
                setEnable('#withdrawReqBettingTimes');
                $('#withdrawShouldMinusDeposit').prop('disabled',false);
                setDisable('#withdrawReqBetAmount, #txt_formula_withdraw_condition, #withdrawReqBonusTimes, #withdrawReqBettingTimesCheckWithMaxBonus');
                formulaWithdrawConditionEditor ? formulaWithdrawConditionEditor.setReadOnly(true) : null;
            }else if(isCheckedById('withdrawRequirementBettingConditionOption6')){
                setEnable('#withdrawReqBettingTimesCheckWithMaxBonus');
                setDisable('#withdrawReqBonusTimes, #withdrawReqBettingTimes, #txt_formula_withdraw_condition, #withdrawShouldMinusDeposit, #withdrawReqBetAmount');
                formulaWithdrawConditionEditor ? formulaWithdrawConditionEditor.setReadOnly(true) : null;
            }else if(isCheckedById('withdrawRequirementBettingConditionOption1')){
                setEnable('#withdrawReqBetAmount');
                setDisable('#withdrawReqBettingTimes, #txt_formula_withdraw_condition, #withdrawShouldMinusDeposit, #withdrawReqBonusTimes, #withdrawReqBettingTimesCheckWithMaxBonus');
                formulaWithdrawConditionEditor ? formulaWithdrawConditionEditor.setReadOnly(true) : null;
            }else if(isCheckedById('withdrawRequirementBettingConditionOption5')){
                setEnable('#withdrawReqBonusTimes');
                setDisable('#withdrawReqBettingTimes, #txt_formula_withdraw_condition, #withdrawShouldMinusDeposit, #withdrawReqBetAmount, #withdrawReqBettingTimesCheckWithMaxBonus');
                formulaWithdrawConditionEditor ? formulaWithdrawConditionEditor.setReadOnly(true) : null;
            }else if(isCheckedById('withdrawRequirementBettingConditionOption3')){
                setDisable('#withdrawReqBetAmount, #withdrawReqBettingTimes, #txt_formula_withdraw_condition, #withdrawShouldMinusDeposit, #withdrawReqBonusTimes, #withdrawReqBettingTimesCheckWithMaxBonus');
                formulaWithdrawConditionEditor ? formulaWithdrawConditionEditor.setReadOnly(true) : null;
            }else if(isCheckedById('withdrawRequirementBettingConditionOption4')){
                setEnable('#txt_formula_withdraw_condition');
                setDisable('#withdrawReqBetAmount, #withdrawReqBettingTimes, #withdrawShouldMinusDeposit, #withdrawReqBonusTimes, #withdrawReqBettingTimesCheckWithMaxBonus');
                formulaWithdrawConditionEditor ? formulaWithdrawConditionEditor.setReadOnly(false) : null;
            }

            <?php if($this->utils->isEnabledFeature('enabled_transfer_condition')): ?>
                if(isCheckedById('transferRequirementBettingConditionOption3')){
                    setEnable('#transferReqBettingTimes');
                    $('#transferShouldMinusDeposit').prop('disabled',false);
                    setDisable('#transferReqBonusTimes, #transferReqBetAmount, #txt_formula_transfer_condition');
                    formulaTransferConditionEditor ? formulaTransferConditionEditor.setReadOnly(true) : null;
                }else if(isCheckedById('transferRequirementBettingConditionOption2')){
                    setEnable('#transferReqBonusTimes');
                    setDisable('#transferReqBettingTimes, #transferShouldMinusDeposit, #transferReqBetAmount, #txt_formula_transfer_condition');
                    formulaTransferConditionEditor ? formulaTransferConditionEditor.setReadOnly(true) : null;
                }else if(isCheckedById('transferRequirementBettingConditionOption4')){
                    setEnable('#transferReqBetAmount');
                    setDisable('#transferReqBettingTimes, #transferShouldMinusDeposit, #transferReqBonusTimes, #txt_formula_transfer_condition');
                    formulaTransferConditionEditor ? formulaTransferConditionEditor.setReadOnly(true) : null;
                }else if(isCheckedById('transferRequirementBettingConditionOption5')){
                    setEnable('#txt_formula_transfer_condition');
                    setDisable('#transferReqBettingTimes, #transferShouldMinusDeposit, #transferReqBonusTimes, #transferReqBetAmount');
                    formulaTransferConditionEditor ? formulaTransferConditionEditor.setReadOnly(false) : null;
                }else if(isCheckedById('transferRequirementBettingConditionOption1')){
                    setDisable('#transferReqBetAmount, #transferReqBettingTimes, #transferShouldMinusDeposit, #transferReqBonusTimes, #txt_formula_transfer_condition');
                    formulaTransferConditionEditor ? formulaTransferConditionEditor.setReadOnly(true) : null;
                }
            <?php endif; ?>

            if(isCheckedById('withdrawRequirementDepositConditionOption2')){
                setEnable('#withdrawReqDepMinLimit');
                setDisable('#withdrawReqDepMinLimitSinceRegistration');
            }else if(isCheckedById('withdrawRequirementDepositConditionOption3')){
                setEnable('#withdrawReqDepMinLimitSinceRegistration');
                setDisable('#withdrawReqDepMinLimit');
            }else if(isCheckedById('withdrawRequirementDepositConditionOption1')){
                setDisable('#withdrawReqDepMinLimit, #withdrawReqDepMinLimitSinceRegistration');
            }

        }


    }

    function initValues(){
        var promorulesId = "<?=$promoRuleDetails['promorulesId'] ? $promoRuleDetails['promorulesId'] : '';?>";
        $('#promorulesId').val(promorulesId);
        if(promorulesId!=""){
            //change to edit promo rule
            $('#title_promo_rule').html("<?php echo lang('promo.editPromoRule'); ?>");
        }
        // utils.safelog('promorulesId:'+promorulesId);
        // if(promorulesId!=""){
            //set value
            <?php $hideDate = $this->utils->getNowForMysql();?>
            $('#promoName').val(CURRENT_PROMO_RULE['promoName']);
            $('#promoCode').val(CURRENT_PROMO_RULE['promoCode']);
            $('#promoType').val(CURRENT_PROMO_RULE['promoCategory']);
            $('#language').val((CURRENT_PROMO_RULE['language'] != 0)? CURRENT_PROMO_RULE['language']:'');
            var appStartDate=CURRENT_PROMO_RULE['applicationPeriodStart'];

            if (appStartDate)
                $('#appStartDate').data('daterangepicker').setStartDate(appStartDate);
            var hideDate=CURRENT_PROMO_RULE['hide_date'];
            if (hideDate && hideDate != "Invalid date"){
               $('#hideDate').data('daterangepicker').setStartDate(hideDate);
            }else{
                $('#hideDate').data('daterangepicker').setStartDate('<?=date('Y', strtotime('+10 years'))?>');
            }
            $('#promoDesc').val(CURRENT_PROMO_RULE['promoDesc']);

            if(CURRENT_PROMO_RULE['depositConditionNonFixedDepositAmount'] == NONFIXED_DEPOSIT_CONDITION_OPTION2){
                $('#nonFixedDepositAmountConditionSecOption1').prop('checked',false);
                $('#nonFixedDepositAmountConditionSecOption2').prop('checked',true);
                $('#nonfixedDepositMinAmount').val('');
                $('#nonfixedDepositMaxAmount').val('');
            }else{
                //default
                $('#nonFixedDepositAmountConditionSecOption1').prop('checked',true);
                $('#nonFixedDepositAmountConditionSecOption2').prop('checked',false);
                $('#nonfixedDepositMinAmount').val(CURRENT_PROMO_RULE['nonfixedDepositMinAmount']);
                $('#nonfixedDepositMaxAmount').val(CURRENT_PROMO_RULE['nonfixedDepositMaxAmount']);
            }

            if(CURRENT_PROMO_RULE['depositSuccesionType'] == BONUSCONDITION_BY_DEPOSIT_SUCCESSION_OTHERS){
                setCheckAndUncheck('#bonusConditionBydepositSuccessionOthers',
                    '#bonusConditionBydepositSuccessionNotFirst, #bonusConditionBydepositSuccessionFirst, #bonusConditionBydepositSuccessionEveryTime');

                $('#bonusConditionBydepositSuccessionOthersDepositCnt').val(CURRENT_PROMO_RULE['depositSuccesionCnt']);
            }else if(CURRENT_PROMO_RULE['depositSuccesionType'] == BONUSCONDITION_BY_DEPOSIT_SUCCESSION_NOT_FIRST){
                setCheckAndUncheck('#bonusConditionBydepositSuccessionNotFirst',
                    '#bonusConditionBydepositSuccessionFirst, #bonusConditionBydepositSuccessionOthers, #bonusConditionBydepositSuccessionEveryTime');

                $('#bonusConditionBydepositSuccessionOthersDepositCnt').val('');
            }else if(CURRENT_PROMO_RULE['depositSuccesionType'] == BONUSCONDITION_BY_DEPOSIT_SUCCESSION_EVERY_TIME){
                setCheckAndUncheck('#bonusConditionBydepositSuccessionEveryTime',
                    '#bonusConditionBydepositSuccessionFirst, #bonusConditionBydepositSuccessionOthers, #bonusConditionBydepositSuccessionNotFirst');

                $('#bonusConditionBydepositSuccessionOthersDepositCnt').val('');
            }else{
                //default is first time
                setCheckAndUncheck('#bonusConditionBydepositSuccessionFirst',
                    '#bonusConditionBydepositSuccessionNotFirst, #bonusConditionBydepositSuccessionOthers, #bonusConditionBydepositSuccessionEveryTime');

                // $('#bonusConditionBydepositSuccessionFirst').prop('checked',true);
                // $('#bonusConditionBydepositSuccessionOthers').prop('checked',false);
                $('#bonusConditionBydepositSuccessionOthersDepositCnt').val('');
            }

            if(CURRENT_PROMO_RULE['depositSuccesionPeriod'] == DEPOSIT_SUCCESSION_PERIOD_OPTION_STARTFROMREG){
                setCheckAndUncheck('#depositSuccessionPeriodOptionStartFromReg',
                    '#depositSuccessionPeriodOptionBonusExpire');

                // $('#depositSuccessionPeriodOptionStartFromReg').prop('checked',true);
                // $('#depositSuccessionPeriodOptionBonusExpire').prop('checked',false);
            }else {
                //default
                setCheckAndUncheck('#depositSuccessionPeriodOptionBonusExpire',
                    '#depositSuccessionPeriodOptionStartFromReg');
            }

            //set add_withdraw_condition_as_bonus_condition
            if(CURRENT_PROMO_RULE['add_withdraw_condition_as_bonus_condition']=='1'){
                $('#add_withdraw_condition_as_bonus_condition').prop('checked', true);
            }else{
                $('#add_withdraw_condition_as_bonus_condition').prop('checked', false);
            }
            if(CURRENT_PROMO_RULE['donot_allow_other_promotion']=='1'){
                $('#donot_allow_other_promotion').prop('checked', true);
            }else{
                $('#donot_allow_other_promotion').prop('checked', false);
            }
            //if(CURRENT_PROMO_RULE['show_on_active_available']=='1'){
            //    $('#show_on_active_available').prop('checked', true);
            //}else{
            //    $('#show_on_active_available').prop('checked', false);
            //}
            if(CURRENT_PROMO_RULE['disable_cashback_if_not_finish_withdraw_condition']=='1'){
                $('#disable_cashback_if_not_finish_withdraw_condition').prop('checked', true);
            }else{
                $('#disable_cashback_if_not_finish_withdraw_condition').prop('checked', false);
            }
            if(CURRENT_PROMO_RULE['hide_if_not_allow']=='1'){
                $('#hide_if_not_allow').prop('checked', true);
            }else{
                $('#hide_if_not_allow').prop('checked', false);
            }
            $('#hide_if_not_allow').bootstrapSwitch();

            if(CURRENT_PROMO_RULE['allowed_scope_condition']=='1'){
                $('#allowed_scope_condition').prop('checked', true);
            }else{
                $('#allowed_scope_condition').prop('checked', false);
            }
            $('#allowed_scope_condition').bootstrapSwitch();

//            if(CURRENT_PROMO_RULE['disabled_pre_application']=='1'){
//                $('#disabled_pre_application').prop('checked', true);
//            }else{
//                $('#disabled_pre_application').prop('checked', false);
//            }
            if(CURRENT_PROMO_RULE['always_join_promotion']=='1'){
                $('#always_join_promotion').prop('checked', true);
            }else{
                $('#always_join_promotion').prop('checked', false);
            }
            if(CURRENT_PROMO_RULE['dont_allow_request_promo_from_same_ips']=='1'){
                $('#dont_allow_request_promo_from_same_ips').prop('checked', true);
            }else{
                $('#dont_allow_request_promo_from_same_ips').prop('checked', false);
            }
            if(CURRENT_PROMO_RULE['donot_allow_any_withdrawals_after_deposit']=='1'){
                $('#donot_allow_any_withdrawals_after_deposit').prop('checked', true);
            }else{
                $('#donot_allow_any_withdrawals_after_deposit').prop('checked', false);
            }
            if(CURRENT_PROMO_RULE['donot_allow_any_despoits_after_deposit']=='1'){
                $('#donot_allow_any_despoits_after_deposit').prop('checked', true);
            }else{
                $('#donot_allow_any_despoits_after_deposit').prop('checked', false);
            }
            if(CURRENT_PROMO_RULE['donot_allow_any_available_bet_after_deposit']=='1'){
                $('#donot_allow_any_available_bet_after_deposit').prop('checked', true);
            }else{
                $('#donot_allow_any_available_bet_after_deposit').prop('checked', false);
            }
            if(CURRENT_PROMO_RULE['donot_allow_any_transfer_after_deposit']=='1'){
                $('#donot_allow_any_transfer_after_deposit').prop('checked', true);
            }else{
                $('#donot_allow_any_transfer_after_deposit').prop('checked', false);
            }
            if(CURRENT_PROMO_RULE['donot_allow_any_transfer_in_after_transfer']=='1'){
                $('#donot_allow_any_transfer_in_after_transfer').prop('checked', true);
            }else{
                $('#donot_allow_any_transfer_in_after_transfer').prop('checked', false);
            }
            if(CURRENT_PROMO_RULE['donot_allow_any_transfer_out_after_transfer']=='1'){
                $('#donot_allow_any_transfer_out_after_transfer').prop('checked', true);
            }else{
                $('#donot_allow_any_transfer_out_after_transfer').prop('checked', false);
            }
            // $('#expire_days').val(CURRENT_PROMO_RULE['expire_days']);

			// $('#withdrawal_max_limit').val(CURRENT_PROMO_RULE['withdrawal_max_limit']);
			// if(CURRENT_PROMO_RULE['ignore_withdrawal_max_limit_after_first_deposit']=='1'){
			// 	$('#ignore_withdrawal_max_limit_after_first_deposit').prop('checked', true);
			// }else{
			// 	$('#ignore_withdrawal_max_limit_after_first_deposit').prop('checked', false);
			// }
			// if(CURRENT_PROMO_RULE['always_apply_withdrawal_max_limit_when_first_deposit']=='1'){
			// 	$('#always_apply_withdrawal_max_limit_when_first_deposit').prop('checked', true);
			// }else{
			// 	$('#always_apply_withdrawal_max_limit_when_first_deposit').prop('checked', false);
			// }

			if(CURRENT_PROMO_RULE['bonusApplicationLimitRule'] == BONUS_CONDITION_BY_APPLICATION_NOLIMIT){
                $('#bonusConditionByApplicationNoLimit').prop('checked',true);
                $('#bonusConditionByApplicationWithLimit').prop('checked',false);
                $('#bonusConditionByApplicationLimitCnt').val("");
                $('#bonusConditionByApplicationLimitCnt').prop('disabled',true);
                $('#bonusConditionByApplicationLimitCnt').prop('required',false);
            }else{
                //default
                $('#bonusConditionByApplicationNoLimit').prop('checked',false);
                $('#bonusConditionByApplicationWithLimit').prop('checked',true);
                $('#bonusConditionByApplicationLimitCnt').prop('disabled',false);
                $('#bonusConditionByApplicationLimitCnt').val(CURRENT_PROMO_RULE['bonusApplicationLimitRuleCnt']);
                $('#bonusConditionByApplicationLimitCnt').prop('required',true);
            }

            if(CURRENT_PROMO_RULE['bonusApplicationLimitDateType'] == BONUS_APPLICATION_LIMIT_DATE_TYPE_NONE){
                $('#bonusApplicationLimitDateTypeNone').prop('checked',true);
            }else if(CURRENT_PROMO_RULE['bonusApplicationLimitDateType'] == BONUS_APPLICATION_LIMIT_DATE_TYPE_DAILY){
                $('#bonusApplicationLimitDateTypeDaily').prop('checked',true);
            }else if(CURRENT_PROMO_RULE['bonusApplicationLimitDateType'] == BONUS_APPLICATION_LIMIT_DATE_TYPE_WEEKLY){
                $('#bonusApplicationLimitDateTypeWeekly').prop('checked',true);
            }else if(CURRENT_PROMO_RULE['bonusApplicationLimitDateType'] == BONUS_APPLICATION_LIMIT_DATE_TYPE_MONTHLY){
                $('#bonusApplicationLimitDateTypeMonthly').prop('checked',true);
            }else if(CURRENT_PROMO_RULE['bonusApplicationLimitDateType'] == BONUS_APPLICATION_LIMIT_DATE_TYPE_YEARLY){
                $('#bonusApplicationLimitDateTypeYearly').prop('checked',true);
            }

            if(CURRENT_PROMO_RULE['release_to_same_sub_wallet']=='1'){
                $('#release_to_same_sub_wallet').prop('checked', true);
            }else{
                $('#release_to_same_sub_wallet').prop('checked', false);
            }

            //set trigger_on_transfer_to_subwallet
            trigger_wallets_ids = CURRENT_PROMO_RULE['trigger_wallets'] ? CURRENT_PROMO_RULE['trigger_wallets'].split(',') : [];
        // console.log(trigger_wallets_ids);
            for(var i=0; i<trigger_wallets_ids.length; i++){
                $('#trigger_on_transfer_to_subwallet'+trigger_wallets_ids[i]).prop('checked', true).trigger('change');
            }

            $('.trigger_on_transfer_to_subwallet').trigger('change');
//            $('#trigger_on_transfer_to_subwallet'+CURRENT_PROMO_RULE['trigger_on_transfer_to_subwallet']).prop('checked', true);
            // if(CURRENT_PROMO_RULE['trigger_on_transfer_to_subwallet']=='1'){
            // }else{
            //     $('#trigger_on_transfer_to_subwallet').prop('checked', false);
            // }

            $('#txt_formula_bonus_condition').val(FORMULA['bonus_condition']);

            // utils.safelog(CURRENT_PROMO_RULE['bonusReleaseRule']);
            if(CURRENT_PROMO_RULE['bonusReleaseRule'] == BONUS_RELEASE_TYPE_FIXED_AMOUNT){

                setCheckAndUncheck('#bonusReleaseTypeFixedAmount',
                    '#bonusReleaseTypeDepositPercentage, #bonusReleaseTypeBetPercentage, #bonusReleaseTypeCustom, #withdrawRequirementBettingConditionOption6');

                $('#bonusReleaseBonusAmount').val(CURRENT_PROMO_RULE['bonusAmount']);
                setEnable('#bonusReleaseBonusAmount');

                setDisable('#depositPercentage, #bonusReleaseMaxBonusAmount, #bonusReleaseBonusPercentage, #bonusReleaseCustom, #txt_formula_bonus_release, #max_bonus_by_limit_date_type, #withdrawReqBettingTimesCheckWithMaxBonus, #withdrawRequirementBettingConditionOption6');
                formulaBonusReleaseEditor ? formulaBonusReleaseEditor.setReadOnly(true) : null;
            }else if(CURRENT_PROMO_RULE['bonusReleaseRule'] == BONUS_RELEASE_TYPE_BET_PERCENTAGE){

                setCheckAndUncheck('#bonusReleaseTypeBetPercentage',
                    '#bonusReleaseTypeDepositPercentage, #bonusReleaseTypeFixedAmount, #bonusReleaseTypeCustom');

                $('#bonusReleaseBonusPercentage').val(CURRENT_PROMO_RULE['depositPercentage']);
                setEnable('#bonusReleaseBonusPercentage');

                setDisable('#depositPercentage, #bonusReleaseMaxBonusAmount, #bonusReleaseBonusAmount, #bonusReleaseCustom, #txt_formula_bonus_release, #max_bonus_by_limit_date_type');
                formulaBonusReleaseEditor ? formulaBonusReleaseEditor.setReadOnly(true) : null;

            }else if(CURRENT_PROMO_RULE['bonusReleaseRule'] == BONUS_RELEASE_RULE_CUSTOM){

                setCheckAndUncheck('#bonusReleaseTypeCustom',
                    '#bonusReleaseTypeDepositPercentage, #bonusReleaseTypeFixedAmount, #bonusReleaseTypeBetPercentage');

                setEnable('#bonusReleaseCustom, #txt_formula_bonus_release');

                setDisable('#depositPercentage, #bonusReleaseMaxBonusAmount, #bonusReleaseBonusAmount, #bonusReleaseBonusPercentage');
                // utils.safelog(FORMULA['bonus_release']);
                $('#txt_formula_bonus_release').val(FORMULA['bonus_release']);
                formulaBonusReleaseEditor ? formulaBonusReleaseEditor.setReadOnly(false) : null;
            }
            // OGP-3381
            else if (CURRENT_PROMO_RULE['bonusReleaseRule'] == BONUS_RELEASE_RULE_BONUS_GAME) {

                setCheckAndUncheck('#bonusReleaseTypeBetPercentage',
                    '#bonusReleaseTypeDepositPercentage, #bonusReleaseTypeFixedAmount, #bonusReleaseTypeCustom');

                $('#bg_game_id, #bg_play_rounds, #bg_budget_cash_enable, #bg_budget_vipexp_enable').removeAttr('disabled');
                fill_bg_game_id('init');


                setDisable('#depositPercentage, #bonusReleaseMaxBonusAmount, #bonusReleaseBonusAmount, #bonusReleaseCustom, #txt_formula_bonus_release');
                formulaBonusReleaseEditor ? formulaBonusReleaseEditor.setReadOnly(true) : null;
            }
            else{// if(CURRENT_PROMO_RULE['bonusReleaseRule'] == BONUS_RELEASE_TYPE_DEPOSIT_PERCENTAGE){
                //default
                setCheckAndUncheck('#bonusReleaseTypeDepositPercentage',
                    '#bonusReleaseTypeCustom, #bonusReleaseTypeFixedAmount, #bonusReleaseTypeBetPercentage, #withdrawRequirementBettingConditionOption6');

                $('#depositPercentage').val(CURRENT_PROMO_RULE['depositPercentage']);
                $('#bonusReleaseMaxBonusAmount').val(CURRENT_PROMO_RULE['maxBonusAmount']);
                setEnable('#depositPercentage, #bonusReleaseMaxBonusAmount, #max_bonus_by_limit_date_type');
                setDisable('#withdrawReqBettingTimesCheckWithMaxBonus, #withdrawRequirementBettingConditionOption6');
                $("#max_bonus_by_limit_date_type").prop('required', false);
                $('#max_bonus_by_limit_date_type').prop('checked', CURRENT_PROMO_RULE['max_bonus_by_limit_date_type']=='1' );

                setDisable('#bonusReleaseBonusPercentage, #bonusReleaseBonusAmount, #bonusReleaseCustom, #txt_formula_bonus_release');
                formulaBonusReleaseEditor ? formulaBonusReleaseEditor.setReadOnly(true) : null;
            }

            if(CURRENT_PROMO_RULE['bonusReleaseToPlayer'] == BONUS_RELEASE_TO_PLAYER_AUTO){
                $('#bonusReleaseToPlayerAuto').prop('checked',true);
                $('#bonusReleaseToPlayerManual').prop('checked',false);
            }else{
                //default is manual
                $('#bonusReleaseToPlayerAuto').prop('checked',false);
                $('#bonusReleaseToPlayerManual').prop('checked',true);
            }

            $('#releaseToSubWallet'+CURRENT_PROMO_RULE['releaseToSubWallet']).prop('checked',true);

            if(CURRENT_PROMO_RULE['withdrawRequirementConditionType'] == WITHDRAW_REQUIREMENT_BETTING_CONDITION_OPTION3){
                //no condition
                setCheckAndUncheck('#withdrawRequirementBettingConditionOption3',
                    '#withdrawRequirementBettingConditionOption1, #withdrawRequirementBettingConditionOption2, #withdrawRequirementBettingConditionOption4');

                setDisable('#withdrawReqBetAmount, #withdrawReqBettingTimes, #txt_formula_withdraw_condition, #withdrawShouldMinusDeposit');
                formulaWithdrawConditionEditor ? formulaWithdrawConditionEditor.setReadOnly(true) : null;

            }else if(CURRENT_PROMO_RULE['withdrawRequirementConditionType'] == WITHDRAW_REQUIREMENT_BETTING_CONDITION_OPTION2){
                setCheckAndUncheck('#withdrawRequirementBettingConditionOption2',
                    '#withdrawRequirementBettingConditionOption1, #withdrawRequirementBettingConditionOption3, #withdrawRequirementBettingConditionOption4');

                $('#withdrawReqBettingTimes').val(withdrawRequirementBetCntCondition);
                $('#withdrawShouldMinusDeposit').prop('checked', withdrawShouldMinusDeposit=='1');
                setEnable('#withdrawReqBettingTimes');
                $('#withdrawShouldMinusDeposit').prop('disabled',false);

                setDisable('#withdrawReqBetAmount, #txt_formula_withdraw_condition');
                formulaWithdrawConditionEditor ? formulaWithdrawConditionEditor.setReadOnly(true) : null;

            }else if(CURRENT_PROMO_RULE['withdrawRequirementConditionType'] == WITHDRAW_REQUIREMENT_BETTING_CONDITION_OPTION6){
                setCheckAndUncheck('#withdrawRequirementBettingConditionOption6',
                    '#withdrawRequirementBettingConditionOption1, #withdrawRequirementBettingConditionOption3, #withdrawRequirementBettingConditionOption4, #withdrawRequirementBettingConditionOption2');

                $('#withdrawReqBettingTimesCheckWithMaxBonus').val(withdrawRequirementBetCntCondition);
                setEnable('#withdrawReqBettingTimesCheckWithMaxBonus');

                setDisable('#withdrawReqBettingTimes, #withdrawReqBonusTimes, #withdrawReqBetAmount, #withdrawShouldMinusDeposit, #txt_formula_withdraw_condition');
                formulaWithdrawConditionEditor ? formulaWithdrawConditionEditor.setReadOnly(true) : null;

            }else if(CURRENT_PROMO_RULE['withdrawRequirementConditionType'] == WITHDRAW_REQUIREMENT_BETTING_CONDITION_OPTION5){
                setCheckAndUncheck('#withdrawRequirementBettingConditionOption5',
                    '#withdrawRequirementBettingConditionOption1, #withdrawRequirementBettingConditionOption3, #withdrawRequirementBettingConditionOption4, #withdrawRequirementBettingConditionOption2');

                $('#withdrawReqBonusTimes').val(withdrawRequirementBetCntCondition);
                setEnable('#withdrawReqBonusTimes');
                $('#withdrawShouldMinusDeposit').prop('disabled',false);

                setDisable('#withdrawReqBetAmount, #txt_formula_withdraw_condition, #withdrawReqBettingTimes');
                formulaWithdrawConditionEditor ? formulaWithdrawConditionEditor.setReadOnly(true) : null;

            }else if(CURRENT_PROMO_RULE['withdrawRequirementConditionType'] == WITHDRAW_REQUIREMENT_BETTING_CONDITION_OPTION4){
                setCheckAndUncheck('#withdrawRequirementBettingConditionOption4',
                    '#withdrawRequirementBettingConditionOption1, #withdrawRequirementBettingConditionOption3, #withdrawRequirementBettingConditionOption2');
                setDisable('#withdrawReqBetAmount, #withdrawReqBettingTimes, #withdrawShouldMinusDeposit');
                $('#txt_formula_withdraw_condition').val(FORMULA['withdraw_condition']);
                formulaWithdrawConditionEditor ? formulaWithdrawConditionEditor.setReadOnly(false) : null;
            }else{
                //default
                setCheckAndUncheck('#withdrawRequirementBettingConditionOption1',
                    '#withdrawRequirementBettingConditionOption4, #withdrawRequirementBettingConditionOption3, #withdrawRequirementBettingConditionOption2');

                $('#withdrawReqBetAmount').val(withdrawRequirementBetAmount);
                setEnable('#withdrawReqBetAmount, #withdrawShouldMinusDeposit');
                $('#withdrawShouldMinusDeposit').prop('disabled',false);
                $('#withdrawShouldMinusDeposit').prop('checked',withdrawShouldMinusDeposit=='1');
                setDisable('#withdrawReqBettingTimes, #txt_formula_withdraw_condition');
                formulaWithdrawConditionEditor ? formulaWithdrawConditionEditor.setReadOnly(true) : null;

            }

            <?php if($this->utils->isEnabledFeature('enabled_transfer_condition')): ?>
                var transferRequirementDisallowTransferIn = null;
                var transferRequirementDisallowTransferOut = null;
                transferRequirementWalletsInfo = CURRENT_PROMO_RULE['transferRequirementWalletsInfo'] ? JSON.parse(CURRENT_PROMO_RULE['transferRequirementWalletsInfo']) : [];

                if(transferRequirementWalletsInfo){
                    transferRequirementDisallowTransferIn = !!transferRequirementWalletsInfo.disallow_transfer_in_wallets ? transferRequirementWalletsInfo.disallow_transfer_in_wallets.wallet_id : [];
                    transferRequirementDisallowTransferOut = !!transferRequirementWalletsInfo.disallow_transfer_out_wallets ? transferRequirementWalletsInfo.disallow_transfer_out_wallets.wallet_id : [];

                    if( transferRequirementDisallowTransferIn.length > 0 ){
                        for(var i=0; i<transferRequirementDisallowTransferIn.length; i++){
                            $('#transfer_condition_disallow_transfer_in_wallet'+transferRequirementDisallowTransferIn[i]).prop('checked', true).trigger('change');
                        }
                        $('.transfer_condition_disallow_transfer_in_wallet').trigger('change');
                    }

                    if( transferRequirementDisallowTransferOut.length > 0 ){
                        for(var i=0; i<transferRequirementDisallowTransferOut.length; i++){
                            $('#transfer_condition_disallow_transfer_out_wallet'+transferRequirementDisallowTransferOut[i]).prop('checked', true).trigger('change');
                        }
                        $('.transfer_condition_disallow_transfer_out_wallet').trigger('change');
                    }
                }

                if(CURRENT_PROMO_RULE['transferRequirementConditionType'] == TRANSFER_REQUIREMENT_BETTING_CONDITION_OPTION2) {
                    setCheckAndUncheck('#transferRequirementBettingConditionOption2',
                        '#transferRequirementBettingConditionOption1, #transferRequirementBettingConditionOption3, #transferRequirementBettingConditionOption4, #transferRequirementBettingConditionOption5');

                    $('#transferReqBonusTimes').val(transferRequirementBetCntCondition);
                    setEnable('#transferReqBonusTimes');
                    $('#transferShouldMinusDeposit').prop('disabled',false);

                    setDisable('#transferReqBetAmount, #txt_formula_transfer_condition, #transferReqBettingTimes');
                    formulaTransferConditionEditor ? formulaTransferConditionEditor.setReadOnly(true) : null;

                }else if(CURRENT_PROMO_RULE['transferRequirementConditionType'] == TRANSFER_REQUIREMENT_BETTING_CONDITION_OPTION3){
                    setCheckAndUncheck('#transferRequirementBettingConditionOption3',
                        '#transferRequirementBettingConditionOption1, #transferRequirementBettingConditionOption2, #transferRequirementBettingConditionOption4, #transferRequirementBettingConditionOption5');

                    $('#transferReqBettingTimes').val(transferRequirementBetCntCondition);
                    $('#transferShouldMinusDeposit').prop('checked', transferShouldMinusDeposit=='1');
                    setEnable('#transferReqBettingTimes');
                    $('#transferShouldMinusDeposit').prop('disabled',false);

                    setDisable('#transferReqBetAmount, #txt_formula_transfer_condition');
                    formulaTransferConditionEditor ? formulaTransferConditionEditor.setReadOnly(true) : null;

                }else if(CURRENT_PROMO_RULE['transferRequirementConditionType'] == TRANSFER_REQUIREMENT_BETTING_CONDITION_OPTION4){
                    setCheckAndUncheck('#transferRequirementBettingConditionOption4',
                        '#transferRequirementBettingConditionOption1, #transferRequirementBettingConditionOption2, #transferRequirementBettingConditionOption3, #transferRequirementBettingConditionOption5');

                    $('#transferReqBetAmount').val(transferRequirementBetAmount);
                    setEnable('#transferReqBetAmount, #transferShouldMinusDeposit');
                    $('#transferShouldMinusDeposit').prop('disabled',false);
                    $('#transferShouldMinusDeposit').prop('checked',transferShouldMinusDeposit=='1');
                    setDisable('#transferReqBettingTimes, #txt_formula_transfer_condition');
                    formulaWithdrawConditionEditor ? formulaWithdrawConditionEditor.setReadOnly(true) : null;

                }else if(CURRENT_PROMO_RULE['transferRequirementConditionType'] == TRANSFER_REQUIREMENT_BETTING_CONDITION_OPTION5){
                    setCheckAndUncheck('#transferRequirementBettingConditionOption5',
                        '#transferRequirementBettingConditionOption1, #transferRequirementBettingConditionOption2, #transferRequirementBettingConditionOption3, #transferRequirementBettingConditionOption4');
                    setDisable('#transferReqBetAmount, #transferReqBettingTimes, #transferShouldMinusDeposit');
                    $('#txt_formula_transfer_condition').val(FORMULA['transfer_condition']);
                    formulaTransferConditionEditor ? formulaTransferConditionEditor.setReadOnly(false) : null;

                }else{
                    //default
                    setCheckAndUncheck('#transferRequirementBettingConditionOption1',
                        '#transferRequirementBettingConditionOption2, #transferRequirementBettingConditionOption3, #transferRequirementBettingConditionOption4, #transferRequirementBettingConditionOption5');
                    setDisable('#transferReqBonusTimes, #transferReqBettingTimes, #transferShouldMinusDeposit, #transferReqBetAmount, #txt_formula_transfer_condition');
                    formulaTransferConditionEditor ? formulaTransferConditionEditor.setReadOnly(true) : null;
                }
            <?php endif;?>

            if(CURRENT_PROMO_RULE['withdrawRequirementDepositConditionType'] == WITHDRAW_REQUIREMENT_DEPOSIT_CONDITION_OPTION2){
                setCheckAndUncheck('#withdrawRequirementDepositConditionOption2',
                    '#withdrawRequirementDepositConditionOption1, #withdrawRequirementDepositConditionOption3');
                $('#withdrawReqDepMinLimit').val(withdrawRequirementDepositAmount);
                setDisable('#withdrawReqDepMinLimitSinceRegistration');
            }else if(CURRENT_PROMO_RULE['withdrawRequirementDepositConditionType'] == WITHDRAW_REQUIREMENT_DEPOSIT_CONDITION_OPTION3){
                setCheckAndUncheck('#withdrawRequirementDepositConditionOption3',
                    '#withdrawRequirementDepositConditionOption1, #withdrawRequirementDepositConditionOption2');
                $('#withdrawReqDepMinLimitSinceRegistration').val(withdrawRequirementDepositAmount);
                setDisable('#withdrawReqDepMinLimit');
            }else{
                //default
                setCheckAndUncheck('#withdrawRequirementDepositConditionOption1',
                    '#withdrawRequirementDepositConditionOption2, #withdrawRequirementDepositConditionOption3');
                setDisable('#withdrawReqDepMinLimit, #withdrawReqDepMinLimitSinceRegistration');
            }

            $('#gameRequiredBet').val(CURRENT_PROMO_RULE['gameRequiredBet']);
            $('#gameRecordStartDate').val(CURRENT_PROMO_RULE['gameRecordStartDate']);
            $('#gameRecordEndDate').val(CURRENT_PROMO_RULE['gameRecordEndDate']);

        // }

        renderPromoRule();
    }

    function initEvents(){
        $(".chosen-select").chosen({
            disable_search: true
        });

        $(allOptions).change(function() {
            renderPromoRule();
        });

        $('#trigger_on_transfer_to_subwallet').on('click', function(){
            checkAll($(this).attr('id'));
        });

        $('.trigger_on_transfer_to_subwallet').change(function(){
            var checked = false;
            $('.trigger_on_transfer_to_subwallet').each(function(){
                checked = $(this).is(':checked') ? true : checked;
            });

            if(!checked){
                disabled_element('#donot_allow_any_transfer_in_after_transfer', 'readonly', null, true);
                disabled_element('#donot_allow_any_transfer_out_after_transfer', 'readonly', null, true);
                enabled_element('#donot_allow_any_transfer_after_deposit', 'readonly', null, true);
            }else{
                enabled_element('#donot_allow_any_transfer_in_after_transfer', 'readonly', null, true);
                enabled_element('#donot_allow_any_transfer_out_after_transfer', 'readonly', null, true);
                disabled_element('#donot_allow_any_transfer_after_deposit', 'readonly', false, true);
            }
        });
    }

    function disabled_element(selector, type, value, inclue_row){
        if($(selector).length <= 0){
            return;
        }

        switch(type){
            case 'readonly':
                $(selector).addClass('readonly').attr('readonly', 'readonly').prop('readonly', true);
                break;
            case 'disabled':
            default:
                $(selector).addClass('disabled').attr('disabled', 'disabled').prop('disabled', true);
                break;
        }

        if(value !== null && $(selector).prop('tagName').toLowerCase() === 'input'){
            switch($(selector).attr('type').toLowerCase()){
                case 'radio':
                case 'checkbox':
                    $(selector).prop('checked', value);
                    break;
                case 'text':
                default:
                    $(selector).value(value);
                    break;
            }
        }

        if(inclue_row){
            $(selector).parent().addClass('bg-muted disabled');
        }
    }

    function enabled_element(selector, type, value, inclue_row){
        if($(selector).length <= 0){
            return;
        }

        switch(type){
            case 'readonly':
                $(selector).removeClass('readonly').removeAttr('readonly').prop('readonly', false);
                break;
            case 'disabled':
            default:
                $(selector).removeClass('disabled').removeAttr('disabled').prop('disabled', false);
                break;
        }

        if(value !== null && $(selector).prop('tagName').toLowerCase() === 'input'){
            switch($(selector).attr('type').toLowerCase()){
                case 'radio':
                case 'checkbox':
                    $(selector).prop('checked', value);
                    break;
                case 'text':
                default:
                    $(selector).value(value);
                    break;
            }
        }

        if(inclue_row){
            $(selector).parent().removeClass('bg-muted disabled');
        }
    }

    $(function() {

        $(".js-data-example-ajax").select2({
            placeholder: '<?=lang('Select new applicable players')?>',
            ajax: {
                url: '/payment_account_management/players',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.items,
                    };
                },
                cache: true
            },
            templateResult: function (option) {
                return option.text;
            },
            templateSelection: function (option) {
                return option.text;
            },
            minimumInputLength: 3,
        });

<?php
if (isset($promoRuleDetails['promoType'])) {
	if ($promoRuleDetails['promoType'] == Promorules::PROMO_TYPE_DEPOSIT) {
		?>
        $("#promoTypeDeposit").prop("checked", true);
<?php
} else {
		?>
        $("#promoTypeNonDeposit").prop("checked", true);
<?php
}
}
?>

<?php
if (isset($promoRuleDetails['nonDepositPromoType'])) {
	$nonDepositPromoType = $promoRuleDetails['nonDepositPromoType'];
	if ($nonDepositPromoType == Promorules::NON_DEPOSIT_PROMO_TYPE_EMAIL) {
		?>
        $("#nonDepositByEmail").prop("checked", true);
<?php

	} else if ($nonDepositPromoType == Promorules::NON_DEPOSIT_PROMO_TYPE_MOBILE) {
		?>
        $("#nonDepositByMobile").prop("checked", true);

<?php

	} else if ($nonDepositPromoType == Promorules::NON_DEPOSIT_PROMO_TYPE_REGISTRATION) {
		?>
        $("#nonDepositByRegAcct").prop("checked", true);

<?php

	} else if ($nonDepositPromoType == Promorules::NON_DEPOSIT_PROMO_TYPE_COMPLETE_PLAYER_INFO) {
		?>
        $("#nonDepositByComReg").prop("checked", true);

<?php

	} else if ($nonDepositPromoType == Promorules::NON_DEPOSIT_PROMO_TYPE_BETTING) {
		?>
        $("#nonDepositByPromoBetAmt").prop("checked", true);

<?php

	} else if ($nonDepositPromoType == Promorules::NON_DEPOSIT_PROMO_TYPE_LOSS) {
		?>
        $("#nonDepositByLoss").prop("checked", true);

<?php

	} else if ($nonDepositPromoType == Promorules::NON_DEPOSIT_PROMO_TYPE_WINNING) {
		?>
        $("#nonDepositByWin").prop("checked", true);

<?php

    } else if ($nonDepositPromoType == Promorules::NON_DEPOSIT_PROMO_TYPE_LOSS_MINUS_WIN) {
        ?>
        $("#nonDepositByLossMinusWin").prop("checked", true);

<?php

	} else if ($nonDepositPromoType == Promorules::NON_DEPOSIT_PROMO_TYPE_RESCUE) {
		?>
        $("#nonDepositByRescue").prop("checked", true);

<?php

	} else if ($nonDepositPromoType == Promorules::NON_DEPOSIT_PROMO_TYPE_CUSTOMIZE) {
		?>
        $("#nonDepositByCustomized").prop("checked", true);
<?php

	}
}
?>
        initEvents();
        initValues();

    });

    function setBettingGames(flag){
        if(flag == FLAG_TRUE){
            $('#betWinLossGamesSec').show();
        }else{
            $('#betWinLossGamesSec').hide();
        }
    }

    function seAllowedGameType(flag){
        // if(flag == FLAG_TRUE){
        //     $('#treeAGT_sec').show();
        //     $("#allowedGameTypeMsg").text("");
        // }else{
        //     $('#treeAGT_sec').hide();
        // }
    }

    var BONUSCONDITION_BY_DEPOSIT_SUCCESSION = 0;
    var BONUSCONDITION_BY_DEPOSIT_APPLICATION = 1;

    var BONUS_RELEASE_TYPE_UI1 = 1;
    var BONUS_RELEASE_TYPE_UI2 = 2;
    var BONUS_RELEASE_TYPE_UI3 = 3;
    var BONUS_RELEASE_TYPE_UI4 = 4;
    var BONUS_RELEASE_TYPE_UI5 = 5;

    var RESET_UI = 3;

    function checkAll(id) {
        var list = document.getElementsByClassName(id);
        var all = document.getElementById(id);

        if (all.checked) {
            for (i = 0; i < list.length; i++) {
                list[i].checked = 1;
                $(list[i]).trigger('change');
            }
        } else {
            all.checked;

            for (i = 0; i < list.length; i++) {
                list[i].checked = 0;
                $(list[i]).trigger('change');
            }
        }
    }

    function checkAllGameType(id) {
        var list = document.getElementsByClassName(id);
        var all = document.getElementById(id);

        if (all.checked) {
            for (i = 0; i < list.length; i++) {
                list[i].checked = 1;
            }
        } else {
            all.checked;

            for (i = 0; i < list.length; i++) {
                list[i].checked = 0;
            }
        }
    }

    function resizeGameOptionWindow(windowSize){
        if(windowSize == 'small'){
            $("#gameOptionWindow").css("height","99px");

            //toggle treeview
            $( '.tree li' ).each( function() {
                $( this ).toggleClass( 'active' );
                $( this ).children( 'ul' ).slideToggle( 'fast' );
            });
        }else if(windowSize == 'medium'){
            $("#gameOptionWindow").css("height","500px");
        }else if(windowSize == 'large'){
            $("#gameOptionWindow").css("height","1000px");
        }
        else{
            //toggle treeview
            $("#gameOptionWindow").css("height","100%");

            $( '.tree li' ).each( function() {
                $( this ).toggleClass( 'active' );
                $( this ).children( 'ul' ).slideToggle( 'fast' );
            });
        }
    }

    //validation
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

    $(".amount_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
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

    $(".letters_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 32, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if (e.ctrlKey === true || code < 65 || code > 90) {
            e.preventDefault();
        }
    });

    $(".letters_numbers_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 32, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105) && (e.ctrlKey === true || code < 65 || code > 90)) {
            e.preventDefault();
        }
    });

    //for promo code
    $("#promoCode").keyup(function(){
        var promoCode = $(this).val();
        if(promoCode.length < 6){
            $("#promocodeStatus").text("<?=lang('con.d10')?>");
        }else{

            $.ajax({
                'url' : base_url +'marketing_management/isPromoCodeExists/'+$(this).val(),
                'type' : 'GET',
                'dataType' : "json",
                'success' : function(data){
                               if(data == true){
                                  $("#promocodeStatus").text("<?=lang('con.d11')?>");
                               }else{
                                  $("#promocodeStatus").text("");
                               }
                            }
                },'json');
                    return false;
            }
    });

    $('#tree').checktree();
    $('#treeAGT').checktree();
    $('#playerLvltree').checktree();


    var formulaBonusReleaseEditor=null, formulaWithdrawConditionEditor=null, formulaTransferConditionEditor=null, formulaBonusConditionEditor=null;
    $( document ).ready( function( ) {

            formulaBonusConditionEditor = ace.edit("txt_formula_bonus_condition");
            formulaBonusConditionEditor.setTheme("ace/theme/tomorrow");
            formulaBonusConditionEditor.getSession().setMode("ace/mode/javascript");
            // formulaBonusConditionEditor.setOption("minLines", 16);
            // formulaBonusConditionEditor.setAutoScrollEditorIntoView(true);

            // hljs.initHighlightingOnLoad();
            formulaBonusReleaseEditor = ace.edit("txt_formula_bonus_release");
            formulaBonusReleaseEditor.setTheme("ace/theme/tomorrow");
            formulaBonusReleaseEditor.getSession().setMode("ace/mode/javascript");
            // formulaBonusReleaseEditor.setOption("minLines", 16);
            // formulaBonusReleaseEditor.setAutoScrollEditorIntoView(true);
            if(CURRENT_PROMO_RULE['bonusReleaseRule'] != BONUS_RELEASE_RULE_CUSTOM){
                formulaBonusReleaseEditor.setReadOnly(true);
            }
            // formulaBonusReleaseEditor.setAutoScrollEditorIntoView(true);
            // formulaBonusReleaseEditor.setOption("minLines", 20);


            formulaWithdrawConditionEditor = ace.edit("txt_formula_withdraw_condition");
            formulaWithdrawConditionEditor.setTheme("ace/theme/tomorrow");
            formulaWithdrawConditionEditor.getSession().setMode("ace/mode/javascript");
            // formulaWithdrawConditionEditor.setOption("minLines", 16);
            // formulaWithdrawConditionEditor.setAutoScrollEditorIntoView(true);
            if(CURRENT_PROMO_RULE['withdrawRequirementConditionType'] != WITHDRAW_REQUIREMENT_BETTING_CONDITION_OPTION4){
                formulaWithdrawConditionEditor.setReadOnly(true);
            }

            <?php if($this->utils->isEnabledFeature('enabled_transfer_condition')): ?>
                formulaTransferConditionEditor = ace.edit("txt_formula_transfer_condition");
                formulaTransferConditionEditor.setTheme("ace/theme/tomorrow");
                formulaTransferConditionEditor.getSession().setMode("ace/mode/javascript");
                if(CURRENT_PROMO_RULE['transferRequirementConditionType'] != TRANSFER_REQUIREMENT_BETTING_CONDITION_OPTION5){
                    formulaTransferConditionEditor.setReadOnly(true);
                }
            <?php endif;?>

            $( '.tree li' ).each( function() {
                    if( $( this ).children( 'ul' ).length > 0 ) {
                            $( this ).addClass( 'parent' );
                    }
            });

            $( '.tree li.parent > a' ).click( function( ) {
                    $( this ).parent().toggleClass( 'active' );
                    $( this ).parent().children( 'ul' ).slideToggle( 'fast' );
            });

            $( '.playerLvltree li' ).each( function() {
                    if( $( this ).children( 'ul' ).length > 0 ) {
                            $( this ).addClass( 'parent' );
                    }
            });

            $( '.playerLvltree li.parent > a' ).click( function( ) {
                    $( this ).parent().toggleClass( 'active' );
                    $( this ).parent().children( 'ul' ).slideToggle( 'fast' );
            });

            $( '.treeAGT li' ).each( function() {
                    if( $( this ).children( 'ul' ).length > 0 ) {
                            $( this ).addClass( 'parent' );
                    }
            });

            $( '.treeAGT li.parent > a' ).click( function( ) {
                    $( this ).parent().toggleClass( 'active' );
                    $( this ).parent().children( 'ul' ).slideToggle( 'fast' );
            });
    });

    //checkbox validation
    function valthis() {
        var succ=true;
        //check max min

        // var allowedGameType = document.getElementsByClassName( 'allowedGameType' );
        // var isChecked_allowedGameType = false;
        // for (var i = 0; i < allowedGameType.length; i++) {
        //     if ( allowedGameType[i].checked ) {
        //         isChecked_allowedGameType = true;
        //     };
        // };
        // if ( isChecked_allowedGameType ) {
        //     $('.allowedGameType').attr('required',false);
        // } else {
        //     //alert( 'Please, check at least one checkbox!' );
        //     $('.allowedGameType').attr('required',true);
        // }

        var playerLvl = document.getElementsByClassName( 'playerLvl' );
        var isChecked_playerLvl = false;
        for (var i = 0; i < playerLvl.length; i++) {
            if ( playerLvl[i].checked ) {
                isChecked_playerLvl = true;
            };
        };
        // if ( isChecked_playerLvl ) {
            // $('.playerLvl').attr('required',false);
        // } else {
            //alert( 'Please, check at least one checkbox!' );
            // $('.playerLvl').attr('required',true);
            // e.preventDefault();
            // return false;
        // }

        // var nonDepositPromoGameProvider = document.getElementsByClassName( 'nonDepositPromoGameProvider' );
        // var isChecked_nonDepositPromoGameProvider = false;
        // for (var i = 0; i < nonDepositPromoGameProvider.length; i++) {
        //     if ( nonDepositPromoGameProvider[i].checked ) {
        //         isChecked_nonDepositPromoGameProvider = true;
        //     };
        // };
        // if ( isChecked_nonDepositPromoGameProvider ) {
        //     $('.nonDepositPromoGameProvider').attr('required',false);
        // } else {
        //     //alert( 'Please, check at least one checkbox!' );
        //     $('.nonDepositPromoGameProvider').attr('required',true);
        // }

        // var gamePlatformAGT = document.getElementsByClassName( 'gamePlatformAGT' );
        // var isChecked_gamePlatformAGT = false;
        // for (var i = 0; i < gamePlatformAGT.length; i++) {
        //     if ( gamePlatformAGT[i].checked ) {
        //         isChecked_gamePlatformAGT = true;
        //     };
        // };
        var isChecked_gamePlatformAGT = $("input[name='selectedGamePlatforms[]']:checked").length>0;

        // utils.safelog('isChecked_gamePlatformAGT:'+isChecked_gamePlatformAGT);

        if ( isChecked_gamePlatformAGT ) {
            $("input[name='selectedGamePlatforms[]']").prop('required',false);
        } else {
            //alert( 'Please, check at least one checkbox!' );
            $("input[name='selectedGamePlatforms[]']").prop('required',true);
            succ=true;
        }

        //set custom value
        if($('#bonusReleaseTypeCustom').is(":checked")){
            var val=formulaBonusReleaseEditor.getValue();
            if(val==''){
                succ=false;
                alert("<?php echo lang('error.require.js'); ?>");
                formulaBonusReleaseEditor.focus();
            }else{
                $("input[name=formula_bonus_release]").val(val);
            }
        }
        if($('#withdrawRequirementBettingConditionOption4').is(":checked")){
            var val=formulaWithdrawConditionEditor.getValue();
            // utils.safelog("val:["+val+"]");
            if(val==''){
                succ=false;
                alert("<?php echo lang('error.require.js'); ?>");
                formulaWithdrawConditionEditor.focus();
            }else{
                $("input[name=formula_withdraw_condition]").val(val);
            }
        }

        if($('#bonusReleaseTypeDepositPercentage').is(":checked")){
            var depPercentage = $('#depositPercentage').val();
            if(depPercentage==''){
                success=false;
                alert("<?=lang('cms.percentage').lang('cannot_be_left_blank')?>");
            }
        }

        <?php if($this->utils->isEnabledFeature('enabled_transfer_condition')): ?>
            if($('#transferRequirementBettingConditionOption5').is(":checked")){
                var val=formulaTransferConditionEditor.getValue();

                if(val==''){
                    succ=false;
                    alert("<?php echo lang('error.require.js'); ?>");
                    formulaTransferConditionEditor.focus();
                }else{
                    $("input[name=formula_transfer_condition]").val(val);
                }
            }
        <?php endif;?>

        $("input[name=formula_bonus_condition]").val(formulaBonusConditionEditor.getValue());

        return succ;
    }


</script>

<script>
  $(document).ready(function () {
        var $form = $('#promoform');
        var $checkbox = $('.playerlvl');

        $form.on('submit', function() {
//            if(!$checkbox.is(':checked')) {
//                $('#allowedPlayerLevelTxt').text("<?php //echo lang('Please select player level!') ?>//");
//                e.preventDefault();
//            }else{
//                $('#allowedPlayerLevelTxt').text("");
//            }
        });

        window.onbeforeunload = function(){
            $('.btn_submit').prop('disabled', true);
        }

    });
</script>

<script type="text/javascript">
    // OGP-3558/3381
    // Public variable game_prizes
    //  Will be populated on fill_bg_game_id() ajax success
    var game_prizes;

    //  Other bonus panel common variables
    var panel = '#bonus_game';
    var panel_controls = $('#bg_game_id,#bg_play_rounds,#bg_budget_cash_enable,#bg_budget_vipexp_enable');
    var budget_checkboxes = $('#bg_budget_cash_enable,#bg_budget_vipexp_enable');
    var budget_inputs = $('#bg_budget_cash,#bg_budget_vipexp');
    var sel = $(panel).find('#bg_game_id');
    var radio_group = 'input[name="bonusReleaseTypeOption"]';
    var this_radio = '#bonusReleaseTypeBonusGame';

    /**
     * Read available game_id and populate the bonus game select
     * @param   bool    init    Will run page loading initializations after populating the select
     * @return  none
     */
    function fill_bg_game_id(init) {
        $.get(
            '/marketing_management/bonusGames_get_avail_games_for_promorules' ,
            function (resp) {
                if (!resp.success) {
                    console.log('fill_bg_game_id: error', resp.mesg);
                }
                else {
                    var sel = $('#bg_game_id');
                    var res = resp.result;
                    $(sel).html('');
                    for (var i in res) {
                        var row = res[i];
                        $(sel).append(
                            $('<option />').val(row.id).text(row.gamename)
                        );
                        game_prizes = res;
                    }
                    init_bonus_games_panel_ops(init);
                }
            }
        );
    };

    /**
     * Enable/disable bonus game panel inputs
     * @uses    bonus_game_panel_enabling(), bind to radio_group click event
     *
     * @return  none
     */
    function init_bonus_games_panel_ops(init) {

        $(panel_controls).attr('disabled', 1);
        $(budget_inputs).attr('disabled', 1);


        if ($(sel).find('option').length == 0) {
            // If no game available - disable the panel
            $(this_radio).attr('disabled', 1);
        }
        else {
            // Game is present - enable/disable inputs by prizes
            // bind event
            $(radio_group).click( bonus_game_panel_enabling );
            // workaround
            $('#bonusReleaseMaxBonusAmount').removeAttr('required');

            // if a bonus game is bound to curret promorules, fill its details into the panel
            <?php if (!empty($promo_game) && is_array($promo_game)) : ?>
            if (init != undefined) {
                // Workaround: as bg_game_id select value setting mysteriously fails
                // Also does the radio button clicking, so, add some delay here
                setTimeout(function () {
                    $('#bg_game_id').val(<?= $promo_game['game_id'] ?>);
                    $('#bg_play_rounds').val(<?= $promo_game['play_rounds'] ?>);
                    $('#bonusReleaseTypeBonusGame').click();

                    // budget_cash, budget_vipexp (and associated checkboxes)
                    var budget_cash = parseFloat('<?= $promo_game['budget_cash'] ?>'), budget_vipexp = parseInt('<?= $promo_game['budget_vipexp'] ?>');
                    $('#bg_budget_cash,#bg_budget_vipexp').attr('disabled', 1);
                    if (budget_cash > 0) {
                        // $('#bg_budget_cash').removeAttr('disabled');
                        $('#bg_budget_cash').val(budget_cash).removeAttr('disabled');
                        $('#bg_budget_cash_enable').attr('checked', 1);
                    }
                    if (budget_vipexp > 0) {
                        // $('#bg_budget_vipexp').removeAttr('disabled');
                        $('#bg_budget_vipexp').val(budget_vipexp).removeAttr('disabled');
                        $('#bg_budget_vipexp_enable').attr('checked', 1);
                    }
                } , 1000);
            }
            <?php endif; ?>
        }
    }

    /**
     * Enable bonus game panel when bonus game radio button is clicked
     * @uses    bg_game_id_link_to_budget_inputs()
     *
     * @return  none
     */
    function bonus_game_panel_enabling() {
        if (!$(this).is(this_radio)) {
            $(panel_controls).attr('disabled', 1);
            $(budget_inputs).attr('disabled', 1);
            // Also force 'Release Bonus' to 'Automatic'
            $('#bonusReleaseToPlayerAuto').click();
        }
        else {
            $(panel_controls).removeAttr('disabled');
            bg_game_id_link_to_budget_inputs();
        }
    }

    /**
     * Enable budget checkboxes/inputs by selected game
     * bound to 1) radio_group click, in bonus_game_panel_enabling()
     *          2) sel change
     *
     * @return  none
     */
    function bg_game_id_link_to_budget_inputs(init) {
        var game_id = $(sel).val();
        var prizes = game_prizes[game_id].prizes;
        $(panel).find('input').each(function () {
            var prize_type = $(this).data('type');
            if (prize_type) {
                if (prizes[prize_type].has_prize) {
                    // Unlock budget checkboxes/inputs
                    $(this).removeAttr('disabled');
                }
                else {
                    // Or disable
                    $(this).attr('disabled', 1);
                }
            }
        });
    }


    /**
     * Event binders
     */
    $(document).ready(function () {
        if ($(sel).find('option').length == 0) {
            fill_bg_game_id();
        }

        // Bind bg_game_id_link_to_budget_inputs to bonus game select change events
        (function init_bg_game_id_link_to_budget_inputs() {
            $(sel).change( bg_game_id_link_to_budget_inputs );
        })();

        /**
         * Form-submit time error check event
         * @param   object  e       DOM event object
         * @return  none
         */
        $('#promoform').submit( function(e) {

            // If bonus release/bonus game radio button is not checked - skip checks
            if (!$(this_radio).is(':checked')) {
                return;
            }

            var errors = 0;
            $('.field-error').hide();

            // Check for play_rounds
            var play_rounds = parseInt($('#bg_play_rounds').val());
            if (isNaN(play_rounds) || play_rounds <= 0) {
                $('.field-error.bg_play_rounds').show();
                ++errors;
            }

            // Check for budget_cash, budget_vipexp
            optional_input_checker('#bg_budget_cash_enable', '#bg_budget_cash', '.field-error.bg_budget_cash');
            optional_input_checker('#bg_budget_vipexp_enable', '#bg_budget_vipexp', '.field-error.bg_vipexp_cash');

            // Post-check, stop submitting if any error presents
            if (errors > 0) {
                e.preventDefault();
            }

            /**
             * Check reoutine for budget_cash and budget_vipexp optional value blocks
             * @param   string  sel_checkbox    selector for checkbox in block
             * @param   string  sel_input       Selector for major inputbox in block
             * @param   string  sel_error       Selector for error messages
             * @uses    'errors', external variable, write scheme
             *
             * @return  none
             */
            function optional_input_checker(sel_checkbox, sel_input, sel_error) {
                if ($(sel_checkbox).is(':checked')) {
                    var val = parseFloat($(sel_input).val());
                    if (isNaN(val)) {
                        $(sel_error).show();
                        ++errors;
                    }
                }
            }
        });

        var ENABLE_ISOLATED_PROMO_GAME_TREE_VIEW = "<?=$this->utils->isEnabledFeature('enable_isolated_promo_game_tree_view')?>";

        $('#edit_allowed_game_list_btn').on('click', function(){
            $('#promoGameListModal').modal('show');
        });

        if(ENABLE_ISOLATED_PROMO_GAME_TREE_VIEW) {
            loadJstreeTable(
                tree_dom_id = '#gameTree',
                outer_tale_id = '#allowed-promo-game-list-table',
                summarize_table_id = '#summarize-table',
                get_data_url = "<?php echo site_url('/api/get_game_tree_by_promo/'. $this->uri->segment(3) ); ?>",
                input_number_form_sel = '#settingForm',
                default_num_value = "0",
                generate_filter_column = {
                    'Download Enabled': 'dlc_enabled',
                    'Mobile Enabled':   'mobile_enabled',
                    'progressive':      'progressive',
                    'Android Enabled':  'enabled_on_android',
                    'IOS Enabled':      'enabled_on_ios',
                    'Flash Enabled':    'flash_enabled',
                    'HTML5 Enabled':    'html_five_enabled'
                },
                filter_col_id = '#filter_col',
                filter_trigger_id = '#filterTree',
                use_input_number = false
            );
        }
        else {
            $('#allowedGameTypeTree').jstree({
                'core' : {
                    'data' : {
                        "url" : "<?php echo site_url('/api/get_game_tree_by_promo/'. $this->uri->segment(3) ); ?>",
                        "dataType" : "json"
                    }
                },
                "input_number":{
                    "form_sel": '#promoform'
                },
                "checkbox":{
                    "tie_selection": false,
                },
                "plugins":[
                    "search","checkbox"
                ]
            });

            $('#promoform').submit(function(e){
                var selected_game=$('#allowedGameTypeTree').jstree('get_checked');
                if(selected_game.length>0){
                    $('#promoform input[name=selected_game_tree]').val(selected_game.join());
                }else{
                    $('#game_tree_by_promo').text("<?php echo lang('Please choose one game at least') ?>");
                    e.preventDefault();
                }
            });
        }
    });
</script>