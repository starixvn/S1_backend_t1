<?php

function createPromoDetailButton($promoRuleId, $promoName) {
    $showPromoRuleDetailsText = lang("cms.showPromoRuleDetails");
    $html = <<<EOD
<a href='#' data-toggle='modal' data-target='#promoDetails' onclick='return viewPromoRuleDetails($promoRuleId)'>
<span data-toggle='tooltip' data-original-title='$showPromoRuleDetailsText' data-placement="right">
    $promoName
</span>
</a>
EOD;
    return $html;
}

?>

<div class="modal fade" id="promoDetails" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="margin: 0 10px;"><?= lang('cms.promoRuleDetails'); ?>: </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table">
                            <tr>
                                <td style="width:15%; border:0px; text-align:right;">
                                    <?= lang('cms.promoType'); ?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="promoType" readonly>
                                </td>

                                <td style="width:15%; border:0px; text-align:right;">
                                    <?= lang('cms.promoname'); ?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="promoName" readonly>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:0px; text-align:right;">
                                    <?= lang('cms.promoCat'); ?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="promoCat" readonly>
                                </td>

                                <td style="border:0px; text-align:right;">
                                    <?= lang('cms.promocode'); ?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="promoCode" readonly>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:0px; text-align:right;">
                                    <?= lang('cms.appStartDate'); ?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="applicationPeriodStart" readonly>
                                </td>

                                <td style="border:0px; text-align:right;">
                                    <?= lang('promo.promoHideDate'); ?>
                                </td>
                                <td style="text-align:left; border:0px;">
                                    <input type="text" class="form-control input-sm" id="hide_date" readonly>
                                </td>
                            </tr>

                            <tr>
                                <td style="border:0px; text-align:right;">
                                    <?= lang('cms.promodesc'); ?>
                                </td>
                                <td style="text-align:left; border:0px;" colspan="4">
                                    <textarea class="form-control  input-sm" readonly id="promoDesc"></textarea>
                                </td>
                            </tr>

                        </table>
                        <hr />
                        <!-- **********************
                            | deposit condition  |
                            ********************** -->
                        <div class="row depositCondSec">
                            <div class="col-md-12">
                                <h4><?= lang('cms.depCon'); ?></h4>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control input-sm" id="depositConditionType" readonly>
                            </div>
                        </div>

                        <!-- **********************
                            | bonus condition sec |
                            ********************** -->
                        <div class="row depositSuccesionSec">
                            <div class="col-md-12">
                                <h4><?= lang('cms.depSuccession') . " <span style='font-size:12px;'>(" . lang('cms.depSuccessionInfo') . ")</span>"; ?></h4>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control input-sm" id="depSuccessionType" readonly>
                            </div>
                        </div>

                        <div class="row applicationSec">
                            <div class="col-md-12">
                                <h4><?= lang('cms.singleOrMultiple'); ?></h4>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control input-sm" id="singleOrMultiple" readonly>
                                <input type="text" class="form-control input-sm" id="repeatCondition" readonly>
                            </div>
                        </div>

                        <!-- ******************
                            | bonus release  |
                            ****************** -->
                        <div class="row bonusReleaseSec">
                            <div class="col-md-12">
                                <h4><?= lang('cms.bonusRelease'); ?></h4>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control input-sm" id="bonusRelease" readonly>
                            </div>
                        </div>

                        <!-- ************************
                            | withdraw requirement |
                            ************************ -->
                        <div class="row withdrawRequirementSec">
                            <div class="col-md-12">
                                <h4><?= lang('cms.withdrawRequirement'); ?></h4>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control input-sm" id="withdrawRequirement" readonly>
                            </div>
                        </div>
                        <!-- *********************
                            | allowed game type |
                            ********************* -->
                        <div class="row allowedGameTypeSec">
                            <br />
                            <div class="col-md-12">
                                <center>
                                    <h4><?= lang('cms.allowedGameType'); ?></h4>
                                </center>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-md-10" style="margin:5px; border:1px solid #ddd; border-radius: 10px; padding:10px; background-color:#fff; overflow-y:scroll; max-height:250px;">
                                    <ul id="allowedGameTypeItemSec"></ul>
                                </div>
                            </div>
                        </div>

                        <!-- *********************
                            | game bet condition |
                            ********************* -->
                        <div class="row gameBetConditionSec">
                            <div class="col-md-12">
                                <h4><?= lang('cms.gameRequirement'); ?></h4>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control input-sm" id="requiredGameBetAmount" readonly>
                            </div>
                            <div class="col-md-12">
                                <hr />
                                <center>
                                    <h4><?= lang('cms.gameBetCondition'); ?></h4>
                                </center>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-md-10" style="margin:5px; border:1px solid #ddd; border-radius: 10px; padding:10px; background-color:#fff; overflow-y:scroll; max-height:250px;">
                                    <ul id="gameBetConditionItemSec"></ul>
                                </div>
                            </div>
                        </div>
                        <!-- ****************
                            | player level |
                            **************** -->
                        <div class="row playerLevelSec">
                            <div class="col-md-12">
                                <center>
                                    <h4><?= lang('Allowed Player Levels'); ?></h4>
                                </center>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-md-10" style="margin:5px; border:1px solid #ddd; border-radius: 10px; padding:10px; background-color:#fff; overflow-y:scroll; max-height:250px;">
                                    <ul id="playerLevelItemSec"></ul>
                                </div>
                            </div>
                        </div>
                        <!-- ****************
                            | affiliate |
                            **************** -->
                        <div class="row affiliateSec">
                            <div class="col-md-12">
                                <center>
                                    <h4><?= lang('Allowed Affiliates'); ?></h4>
                                </center>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-md-10" style="margin:5px; border:1px solid #ddd; border-radius: 10px; padding:10px; background-color:#fff; overflow-y:scroll; max-height:250px;">
                                    <ul id="affiliateItemSec"></ul>
                                </div>
                            </div>
                        </div>
                        <!-- **********
                            | player |
                            ********** -->
                        <div class="row playerSec">
                            <div class="col-md-12">
                                <center>
                                    <h4><?= lang('Allowed Players'); ?></h4>
                                </center>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>
                                <div class="col-md-10" style="margin:5px; border:1px solid #ddd; border-radius: 10px; padding:10px; background-color:#fff; overflow-y:scroll; max-height:250px;">
                                    <ul id="playerItemSec"></ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <center>
                                <br />
                                <button class="btn btn-primary btn-md btn-block" style="width:30%" data-dismiss="modal">OK</button>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    <?php echo $this->utils->generateLangArray(array(
        'cms.depPromo', 'cms.nonDepPromo', 'cms.promoEmail', 'cms.promoMobilePhone', 'cms.promoRegisteredAcct',
        'cms.promoCompleteRegistration', 'cms.promoByBetting', 'cms.promoByLoss', 'cms.promoByWinning',
        'report.p20', 'cms.anyAmt', 'cms.firstDep', 'cms.not_first_deposit', 'cms.startFromReg', 'cms.bonusexp', 'promo.nondeposit_rescue',
        'cms.noLimit', 'cms.withLimit', 'cms.repeat', 'cms.auto', 'cms.manual', 'cms.fixedBonusAmount',
        'cms.percentageOfDepositAmt', 'cms.maxbonusamt', 'cms.percentageOfBetAmt', 'cms.withBetAmtCond',
        'cms.betAmountCondition2', 'cms.betAmountCondition1', 'cms.noBetRequirement', 'cms.only_once',
        'promo_rule.bonus_release_custom', 'promo_rule.withdraw_condition_custom',
    ), 'lang_promo'); ?>

    <?php echo $this->utils->getPromoConstJS(); ?>

    function viewPromoRuleDetails(promotypeId) {
        $.ajax({
            'url': site_url('marketing_management/viewPromoRuleDetails/' + promotypeId),
            'type': 'GET',
            'dataType': "json",
            'success': function(data) {
                if (data) {
                    var promorule = data;

                    $('#promoName').val(promorule.promoName);
                    $('#promoDesc').val(promorule.promoDesc);
                    $('#promoCode').val(promorule.promoCode);
                    $('#promoStatus').val(promorule.promoStatus);

                    var promoType = '';
                    //promo type
                    if (promorule.promoType == PROMO_CONST['PROMO_TYPE_DEPOSIT']) { //deposit promo
                        $('.depositCondSec').show();
                        $('.allowedGameTypeSec').show();
                        $('.gameBetConditionSec').hide();
                        promoType = lang_promo['cms.depPromo'];
                    } else if (promorule.promoType == PROMO_CONST['PROMO_TYPE_NON_DEPOSIT']) { //non deposit promo
                        $('.depositCondSec').hide();
                        $('.depositSuccesionSec').hide();

                        nonDepPromoType = promorule.nonDepositPromoType;
                        if (nonDepPromoType == PROMO_CONST['NON_DEPOSIT_PROMO_TYPE_EMAIL']) {
                            promoType = lang_promo['cms.nonDepPromo'] + ' (' + lang_promo['cms.promoEmail'] + ')';
                            $('.gameBetConditionSec').hide();
                            $('.allowedGameTypeSec').show();
                        } else if (nonDepPromoType == PROMO_CONST['NON_DEPOSIT_PROMO_TYPE_MOBILE']) {
                            promoType = lang_promo['cms.nonDepPromo'] + ' (' + lang_promo['cms.promoMobilePhone'] + ')';
                            $('.gameBetConditionSec').hide();
                            $('.allowedGameTypeSec').show();
                        } else if (nonDepPromoType == PROMO_CONST['NON_DEPOSIT_PROMO_TYPE_REGISTRATION']) {
                            promoType = lang_promo['cms.nonDepPromo'] + ' (' + lang_promo['cms.promoRegisteredAcct'] + ')';
                            $('.gameBetConditionSec').hide();
                            $('.allowedGameTypeSec').show();
                        } else if (nonDepPromoType == PROMO_CONST['NON_DEPOSIT_PROMO_TYPE_COMPLETE_PLAYER_INFO']) {
                            promoType = lang_promo['cms.nonDepPromo'] + ' (' + lang_promo['cms.promoCompleteRegistration'] + ')';
                            $('.gameBetConditionSec').hide();
                            $('.allowedGameTypeSec').show();
                        } else if (nonDepPromoType == PROMO_CONST['NON_DEPOSIT_PROMO_TYPE_RESCUE']) {
                            promoType = lang_promo['cms.nonDepPromo'] + ' (' + lang_promo['promo.nondeposit_rescue'] + ')';
                            $('.gameBetConditionSec').hide();
                            $('.allowedGameTypeSec').show();
                        } else if (nonDepPromoType == PROMO_CONST['NON_DEPOSIT_PROMO_TYPE_BETTING']) {
                            promoType = lang_promo['cms.nonDepPromo'] + ' (' + lang_promo['cms.promoByBetting'] + ')';
                            $('.gameBetConditionSec').show();
                            $('.allowedGameTypeSec').hide();
                        } else if (nonDepPromoType == PROMO_CONST['NON_DEPOSIT_PROMO_TYPE_LOSS']) {
                            promoType = lang_promo['cms.nonDepPromo'] + ' (' + lang_promo['cms.promoByLoss'] + ')';
                            $('.gameBetConditionSec').show();
                            $('.allowedGameTypeSec').hide();
                        } else if (nonDepPromoType == PROMO_CONST['NON_DEPOSIT_PROMO_TYPE_WINNING']) {
                            promoType = lang_promo['cms.nonDepPromo'] + ' (' + lang_promo['cms.promoByWinning'] + ')';
                            $('.gameBetConditionSec').show();
                            $('.allowedGameTypeSec').hide();
                        }
                    }
                    $('#promoType').val(promoType);
                    $('#applicationPeriodStart').val(promorule.applicationPeriodStart);
                    $('#hide_date').val(promorule.hide_date);
                    $('#createdBy').val(promorule.createdBy);
                    $('#createdOn').val(promorule.createdOn);

                    if (promorule.depositConditionNonFixedDepositAmount == PROMO_CONST['NON_FIXED_DEPOSIT_MIN_MAX']) {
                        depositCondition = lang_promo['report.p20'] + " (" + promorule['nonfixedDepositMinAmount'] + " - " + promorule['nonfixedDepositMaxAmount'] + ")";
                    } else {
                        depositCondition = lang_promo['cms.anyAmt'];
                    }

                    $('#depositConditionType').val(depositCondition);
                    $('#depositConditionDepositAmount').val(promorule.depositConditionDepositAmount);

                    //bonus succession and application

                    $('.depositSuccesionSec').show();
                    var depSuccessionType = '';
                    if (promorule.depositSuccesionType == PROMO_CONST['DEPOSIT_SUCCESION_TYPE_FIRST']) {
                        depSuccessionType = lang_promo['cms.firstDep'];
                    } else if (promorule.depositSuccesionType == PROMO_CONST['DEPOSIT_SUCCESION_TYPE_NOT_FIRST']) {
                        depSuccessionType = lang_promo['cms.not_first_deposit'];
                    } else if (promorule.depositSuccesionType == PROMO_CONST['DEPOSIT_SUCCESION_TYPE_ANY']) {
                        depSuccessionType = promorule.depositSuccesionCnt;
                    }

                    var depSuccessionPeriod = '';
                    if (promorule.depositSuccesionPeriod == PROMO_CONST['DEPOSIT_SUCCESION_PERIOD_START_FROM_REG']) {
                        depSuccessionPeriod = lang_promo['cms.startFromReg'];
                    } else if (promorule.depositSuccesionPeriod == PROMO_CONST['DEPOSIT_SUCCESION_PERIOD_BONUS_EXPIRE']) {
                        depSuccessionPeriod = lang_promo['cms.bonusexp'];
                    }

                    $('#depSuccessionType').val(depSuccessionType + ', ' + depSuccessionPeriod);


                    $('.applicationSec').show();
                    $('#repeatCondition').hide();

                    var singleOrMultiple = '';
                    if (promorule.bonusApplicationLimitRule == PROMO_CONST['BONUS_APPLICATION_LIMIT_RULE_NO_LIMIT']) { //limit
                        singleOrMultiple = lang_promo['cms.repeat'] + ', ' + lang_promo['cms.noLimit'];
                    } else {
                        if (promorule.bonusApplicationLimitRuleCnt <= 1) {
                            singleOrMultiple = lang_promo['cms.only_once'];
                        } else {
                            singleOrMultiple = lang_promo['cms.repeat'] + ', ' + lang_promo['cms.withLimit'] + ' ' + promorule.bonusApplicationLimitRuleCnt;
                        }
                    }

                    $('#singleOrMultiple').val(singleOrMultiple);

                    if (promorule.bonusReleaseToPlayer == PROMO_CONST['BONUS_RELEASE_TO_PLAYER_AUTO']) {
                        bonusReleaseToPlayer = lang_promo['cms.auto'];
                    } else {
                        bonusReleaseToPlayer = lang_promo['cms.manual'];
                    }

                    var bonusReleaseRule = '';
                    //bonusReleaseSec
                    if (promorule.bonusReleaseRule == PROMO_CONST['BONUS_RELEASE_RULE_FIXED_AMOUNT']) {
                        bonusReleaseRule = lang_promo['cms.fixedBonusAmount'] + " = " + promorule.bonusAmount + " (" + bonusReleaseToPlayer + ")";
                    } else if (promorule.bonusReleaseRule == PROMO_CONST['BONUS_RELEASE_RULE_DEPOSIT_PERCENTAGE']) {
                        bonusReleaseRule = promorule.depositPercentage + lang_promo['cms.percentageOfDepositAmt'] + " " + promorule.maxBonusAmount + " " + lang_promo['cms.maxbonusamt'] + " (" + bonusReleaseToPlayer + ")";
                    } else if (promorule.bonusReleaseRule == PROMO_CONST['BONUS_RELEASE_RULE_BET_PERCENTAGE']) {
                        bonusReleaseRule = promorule.depositPercentage + lang_promo['cms.percentageOfBetAmt'] + " (" + bonusReleaseToPlayer + ")";
                    } else if (promorule.bonusReleaseRule == PROMO_CONST['BONUS_RELEASE_RULE_CUSTOM']) {
                        bonusReleaseRule = lang_promo['promo_rule.bonus_release_custom'];
                    }
                    $('#bonusRelease').val(bonusReleaseRule);

                    //withdrawRequirement
                    if (promorule.withdrawRequirementConditionType == PROMO_CONST['WITHDRAW_CONDITION_TYPE_FIXED_AMOUNT']) {
                        withdrawRequirement = lang_promo['cms.withBetAmtCond'] + " >= " + promorule.withdrawRequirementBetAmount;
                    } else if (promorule.withdrawRequirementConditionType == PROMO_CONST['WITHDRAW_CONDITION_TYPE_BETTING_TIMES']) {
                        if (promorule.promoType == PROMO_CONST['PROMO_TYPE_NON_DEPOSIT']) {
                            withdrawRequirement = lang_promo['cms.withBetAmtCond'] + ' ' + lang_promo['cms.betAmountCondition2'] + " " + promorule.withdrawRequirementBetCntCondition;
                        } else {
                            withdrawRequirement = lang_promo['cms.withBetAmtCond'] + ' ' + lang_promo['cms.betAmountCondition1'] + " " + promorule.withdrawRequirementBetCntCondition;
                        }
                    } else if (promorule.withdrawRequirementConditionType == PROMO_CONST['WITHDRAW_CONDITION_TYPE_CUSTOM']) {
                        withdrawRequirement = lang_promo['promo_rule.withdraw_condition_custom'];
                    } else {
                        withdrawRequirement = lang_promo['cms.noBetRequirement'];
                    }
                    $('#withdrawRequirement').val(withdrawRequirement);

                    $('#promoCat').val(promorule.promoTypeName);
                    $('#promorulesId').val(promorule.promorulesId);
                    $('#status').val(promorule.status);
                    $('#updatedBy').val(promorule.updatedBy);
                    $('#updatedOn').val(promorule.updatedOn);

                    //player levels
                    $('#playerLevelItemSec').empty();
                    if (promorule['playerLevels'].length > 0) {
                        for (var i = 0; i < promorule['playerLevels'].length; i++) {
                            html = '';
                            html += '<li>';
                            html += '' + promorule['playerLevels'][i].groupName + ' ' + promorule['playerLevels'][i].vipLevelName + '';
                            html += '</li>';
                            $('#playerLevelItemSec').append(html);
                        }
                    }

                    // affiliates
                    $('#affiliateItemSec').empty();
                    if (promorule['affiliates'].length > 0) {
                        for (var i = 0; i < promorule['affiliates'].length; i++) {
                            html = '<li>' + promorule['affiliates'][i].username + '</li>';
                            $('#affiliateItemSec').append(html);
                        }
                    }

                    // players
                    $('#playerItemSec').empty();
                    if (promorule['players'].length > 0) {
                        for (var i = 0; i < promorule['players'].length; i++) {
                            html = '<li>' + promorule['players'][i].username + '</li>';
                            $('#playerItemSec').append(html);
                        }
                    }

                    //game type
                    $('#allowedGameTypeItemSec').empty();
                    if (promorule['gameBetCondition'].length > 0) {
                        for (var i = 0; i < promorule['gameBetCondition'].length; i++) {
                            html = '';
                            html += '<li>';
                            html += promorule['gameBetCondition'][i].game + ' - ' + promorule['gameBetCondition'][i].gameName + ' (' + promorule['gameBetCondition'][i].gameCode + ')';
                            html += '</li>';
                            $('#allowedGameTypeItemSec').append(html);
                        }
                    }

                    //game bet condition
                    $('#gameBetConditionItemSec').empty();
                    if (promorule['gameBetCondition'].length > 0) {
                        for (var i = 0; i < promorule['gameBetCondition'].length; i++) {
                            html = '';
                            html += '<li>';
                            html += promorule['gameBetCondition'][i].game + ' - ' + promorule['gameBetCondition'][i].gameName + ' (' + promorule['gameBetCondition'][i].gameCode + ')';
                            html += '</li>';
                            $('#gameBetConditionItemSec').append(html);
                        }
                    }
                    //required game bet

                    $('#requiredGameBetAmount').val(promorule.gameRequiredBet + ', ' + promorule.gameRecordStartDate + ' - ' + promorule.gameRecordEndDate);
                }
            }
        });
        return false;
    }
</script>