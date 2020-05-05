<?php
/*
echo "<pre>";
print_r($responsegame);
echo "</pre>";
*/
foreach($responsegame as $key=>$val){
    $$key =$val;
}
?>
<div id="responsible-gaming" class="panel responsible-gaming">
    <div class="panel-heading">
        <h1 class="hidden-xs hidden-sm"><?php echo lang("Responsible Gaming")?></h1>
    </div>
    <div class="panel-body">
        <ul class="row fm-ul" role="tablist">
            <li class="col-xs-4 col-sm-3 active">
                <a data-toggle="tab" href="#coolOffTab" id="cooloff_btn" aria-expanded="false"><?php echo lang("Time Out")?></a>
            </li>
<!--            <li class="col-xs-4 col-sm-3">-->
<!--                <a data-toggle="tab" href="#timeRemindersTab" id="timer_btn" aria-expanded="false">Time Reminders</a>-->
<!--            </li>-->
<!--            <li class="col-xs-4 col-sm-3">-->
<!--                <a data-toggle="tab" href="#sessionLimitTab" id="mini_btn" aria-expanded="false">Session limits</a>-->
<!--            </li>-->
<!--            <li class="col-xs-4 col-sm-3">-->
<!--                <a data-toggle="tab" href="#lossLimitTab" id="mini_btn" aria-expanded="false">Loss limits</a>-->
<!--            </li>-->
            <li class="col-xs-4 col-sm-3 ">
                <a data-toggle="tab" href="#depositLimitTab" id="depositlimit_btn" aria-expanded="true"><?php echo lang("Deposit Limits")?></a>
            </li>
            <?php if(!$respGameData['disable_and_hide_wagering_limits']):?>
            <li class="col-xs-4 col-sm-3 ">
                <a data-toggle="tab" href="#wageringLimitTab" id="wageringlimit_btn" aria-expanded="false"><?php echo lang("Wagering Limits")?></a>
            </li>
            <?php endif;?>
             <li class="col-xs-4 col-sm-3 " >
                <a data-toggle="tab" href="#selfExclusionTab" id="selfexec_btn"  aria-expanded="false"><?php echo lang("Self Exclusion")?></a>
            </li>
        </ul>
        <br>
        <div class="tab-content">
            <!-- SELF EXCLUSION SECTION START -->
            <div id="selfExclusionTab" class="tab-pane fade">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <?php
                            if (isset($selfExclusionRequestExists)) {
                                echo "<h5>" . lang("Your request has been sent.") . "</h5>";
                            }
                            ?>
                        </div>
                    </div>
                    <?php if(!isset($selfExclusionRequestExists)):?>
                        <div class="col-md-6 withBorder" id="selfExclusionSec">
                            <form id="gameRespForm" action="<?=site_url('/player_center2/responsible_game/postSelfExclusion')?>" method="post" role="form">
                                <h5><?php echo lang("Choose the type of exclusion") ?></h5>
                                <p>
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" name="selfExclusionType" id="" value="1" onclick="setSelfExclusionType('temporary')" class="" checked> <?php echo lang("Temporary") ?>
                                    </label>
                                    <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                                        <input type="radio" name="selfExclusionType" id="" value="2" onclick="setSelfExclusionType('permanent')" class="" > <?php echo lang("Permanent") ?>
                                    </label>
                                </p>

                                <!-- TEMPORARY EXCLUSTION SECTION START -->
                                <div id="temporaryExclusionSec">
                                    <hr/>
                                    <h4><?php echo lang("Enter the number of months your account will be temporarily closed") ?></h4>
                                    <div  class="col-md-6">
                                        <?php $tempReriodList = Responsible_gaming::getTempPeriodList(); ?>
                                        <select class="form-control" name="tempPeriodCount" id="tempPeriodCount">
                                            <option value="0"><?=lang("Please Select a Period")?></option>
                                            <?php foreach ($tempReriodList as $days => $content) : ?>
                                                <option value="<?= $days ?>"> <?= $content?> </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div  class="col-md-3">
                                        <input type="submit" name="submit" id="tempPerioddCountBtn" value="<?php echo lang("Send Request Now") ?>" class="btn btn-info btn-md">
                                    </div>
                                    <br/><br/><br/>
                                </div>
                                <!-- TEMPORARY EXCLUSTION SECTION END -->

                                <!-- PERMANENT EXCLUSTION SECTION START -->
                                <div class="withBorder" id="permanentExclusionSec">
                                    <h4><?php echo lang("You will be excluded from logging in indefinitely"); ?></h4>
                                    <br/>
                                    <div>
                                        <input type="submit" name="submit" value="<?php echo lang("Send Request Now") ?>" class="btn btn-info btn-md">
                                        <br/>
                                    </div>
                                </div>
                            </form>
                            <!-- PERMANENT EXCLUSTION SECTION END -->
                            <?php
                            $web_responsible_gaming_url = $this->utils->getConfig("web_responsible_gaming_url");
                            if (!empty($web_responsible_gaming_url)) {
                                $web_responsible_gaming_url = $this->utils->getSystemUrl("www", $web_responsible_gaming_url);
                            }
                            ?>
                            <div>
                                <span class="notes"><strong><?php echo lang("Note: During this time, you’ll be blocked from doing the following") ?></strong></span>
                                <ul>
                                    <li><span class="notes"><?php echo lang("Depositing any funds") ?></span></li>
                                    <li><span class="notes"><?php echo lang("Playing any online casino games for real money") ?></span></li>
                                    <li><span class="notes"><?php echo lang("Logging into your account") ?></span></li>
                                    <li><span class="notes"><?php echo lang("Please be sure to withdraw any remaining funds in your account prior to self-excluding yourself. Once your account has been locked, you won’t be able to log in to request a withdrawal.") ?>
                         </span>
                                    </li>
                                </ul>
                                <br/>
                                <?php if(empty($web_responsible_gaming_url)) : ?>
                                    <a class="notes" data-toggle="modal" data-target="#responsible_gaming" ><?php echo lang("Show Details >>")?></a>
                                <?php else: ?>
                                    <a class="notes" data-toggle="modal" data-target="#myurl_responsible_gaming" ><?php echo lang("Show Details >>")?></a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="modal fade myurl_responsible-gaming-modal" id="myurl_responsible_gaming" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-body ">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position:absolute;right:10px; top:10px; z-index:500000">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <iframe id="popup_myurl_responsible_gaming" src="<?=$web_responsible_gaming_url?>"  style="width:100%; height:470px; overflow-x:hidden" frameBorder="0"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="modal fade responsible-gaming-modal" id="responsible_gaming" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title"><?= lang("Self Exclusion:")?></h4>
                                    </div>
                                    <div class="modal-body">

                                        <p class="notes">
                                            <?= lang("On request, we will close any account for a minimum period of 6 months during which time it will not be possible for the account to be re-opened for any reason. You can close your account under our responsible gaming self-exclusion policy at anytime by contacting our Customer Support team by emailing helpdesk@smartbackend.com with the username or registered email details of the account you wish to close.")?>
                                        </p>
                                        <p class="notes">
                                            <?= lang("Upon expiry of the self-exclusion period you may request to re-open a closed account by contacting our Customer Support team by emailing helpdesk@smartbackend.com with the details of the account you wish to re-open. All requests to re-open a closed account will be reviewed by the Operator.")?>
                                        </p>
                                        <p class="notes">
                                            <?= lang("Accounts closed as part of our self-exclusion policy cannot be re-opened for any reason until the self-exclusion time period has expired. If you have requested us to close your account indefinitively your account cannot be opened for any reason whatsoever. We will use all reasonable endeavours to ensure compliance with our responsible gaming self-exclusion policy.")?>
                                        </p>
                                        <p class="notes">
                                            <?= lang("However, you accept that we have no responsibility or liability whatsoever if you continue to deposit and wager using additional not previously disclosed accounts or if you open up a new account with substantially the same personal registration information.")?>
                                        </p>
                                        <p class="notes">
                                            <?= lang("Upon self-exclusion all future wagers, Bonus funds and entries in any promotions will be forfeited. We will not be able to reinstate these if the account is reopened after the self-exclusion period. All remaining balances less any active bonuses will be transferred to your credit card, transferred to your bank account or any available payment method at the company's discretion.")?>
                                        </p>
                                        <p class="notes">
                                            <?= sprintf(lang("For the period of Self Exclusion we will endeavour to not send you any marketing material or promotional offers, %s day/s effectivity period to come into effect after you opt to self-exclude."),$respGameData['self_exclusion_approval_day_cnt'])?>
                                        </p>
                                    </div>
                                    <div class="modal-footer">

                                        <button type="button" class="btn form-control" data-dismiss="modal"><?= lang('confirm') ?></button>

                                    </div>

                                </div>
                            </div>
                        </div>
                    <?php endif;?>
                </div>
            </div>
            <!-- SELF EXCLUSION SECTION END -->
            <!-- COOL OFF SECTION START -->
            <div id="coolOffTab" class="tab-pane fade in active">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <?php
                            if (isset($coolingOffRequestExists)) {
                                echo "<h5>" . sprintf(lang("cool_off.remain_time_to_start"), $coolingOffRemainHourToStart) . "</h5>";
                            }
                            ?>
                        </div>
                    </div>
                    <?php if(!isset($coolingOffRequestExists)):?>
                        <div class="col-md-6 withBorder" id="coolOffSec">
                            <form id="gameRespForm" action="<?=site_url('/player_center2/responsible_game/postCoolOff')?>" method="post" role="form">
                                <br/>
                                <p><?= sprintf(lang("If you would like to take a temporary break you can use our Time out feature to take a break of between 1 day and 6 weeks. %s day/s effectivity period."), $respGameData['self_exclusion_approval_day_cnt']) ?></p>
                                <br/>
                                <p><?= lang("Once you begin your Time-out you will not be able to access the site again until your selected break has ended. Your account will automatically re-open after your break has finished.") ?></p>
                                <br/>
                                <p><?= lang("If you want to stop yourself gambling for a longer period of time you should use our Self-Exclusion tool.") ?></p>
                                <br/><br/>
                                <div class="col-md-6">
                                    <select name="coolOffPeriodCount" id="coolOffPeriodCount" class="form-control">
                                        <option value="0"><?=lang("Please Select a Period")?></option>
                                        <option value="1"><?=lang('24 Hours')?></option>
                                        <option value="7"><?=lang('One Week')?></option>
                                        <option value="30"><?=lang('One Month')?></option>
                                        <option value="42"><?=lang('6 Weeks')?></option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="submit" name="submit" id="coolOffBtn" value="<?php echo lang("Send Request Now") ?>" class="btn btn-info btn-md">

                                </div>
                                <br/>
                            </form>
                        </div>
                    <?php endif;?>
                </div>
            </div>
            <!-- COOL OFF SECTION END -->

            <!-- TIMER REMINDER SECTION START -->
            <div id="timeRemindersTab" class="tab-pane fade">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <?php
                            if (isset($timeReminders) && isset($timerReminderStatus) && $timerReminderStatus == Responsible_gaming::STATUS_APPROVED) {?>
                                <strong><?php echo lang("Current Settings: Will remind you of your playing time every ") . " " . @$timeReminders . " mins." ?></strong>
                                <a href="javascript:void(0);" id="timerReminderBtn" onClick=window.open("/player_center2/responsible_game/timeRemindersWindow?timeReminder=<?php echo $timeReminders ?>","Ratting","width=550,height=190,left=150,top=200,toolbar=0,status=0") class="btn btn-primary btn-sm" ><?php echo lang("Start Now") ?></a>
                                <br/><br/>
                            <?php }?>

                        </div>
                    </div>
                    <div class="col-md-6" id="coolOffSec">
                        <form id="gameRespForm" action="<?=site_url('/player_center2/responsible_game/postTimeReminders')?>" method="post" role="form">
                            <br/>
                            <p><?php echo lang("You can set a time reminder from when you are logged in, so that you can accurately keep track of how long you have been logged in for. This can be as low as 1 minute, and up to any time you specify. This can be activated by clicking the save button.") ?></p>
                            <br/>
                            <div>
                                <input type="number" value="<?php echo isset($timeReminders) ? @$timeReminders : "1" ?>" min="1" name="timeReminderPeriodCount" id="timeReminderPeriodCount" class="form-control" placeholder="<?php echo lang("Enter Number") ?>">
                                <input type="submit" name="submit" value="<?php echo lang("Save") ?>" class="btn btn-info btn-md">
                                <span class="notes"><?php echo lang("Enter the Number of minute/s") ?></span>
                            </div>
                            <br/>
                        </form>
                    </div>
                </div>
            </div>
            <!-- TIMER REMINDER SECTION END -->

            <!-- SESSION LIMIT SECTION START -->
            <div id="sessionLimitTab" class="tab-pane fade">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <?php
                            if (isset($sessionLimits) && isset($sessionLimitsStatus)) {
                                if (@$sessionLimitsStatus == Responsible_gaming::STATUS_APPROVED) {?>
                                    <strong><?php echo lang("Current Settings: Remind you every ") . " " . @$sessionLimits . " mins playing." ?></strong>
                                    <br/><br/>
                                <?php } elseif (@$sessionLimitsStatus == Responsible_gaming::STATUS_EXPIRED) {?>
                                    <strong><?php echo lang("You have reached your " . @$sessionLimits . " mins playing time already! You can reactivate or update your session limits by clicking save button.") ?></strong>
                                    <br/><br/>
                                <?php }
                            }?>
                        </div>
                    </div>
                    <div class="col-md-6" id="sessionLimitSection">
                        <form id="gameRespForm" action="<?=site_url('/player_center2/responsible_game/postSessionLimits')?>" method="post" role="form">
                            <br/>
                            <p><?php echo lang("You can set a session reminder that alerts you when you have been playing for the set of period and automatically logs you out. This can be as low as 1 minute, and up to any time you specify. These can be activated by clicking save button.") ?></p>
                            <br/><br/>
                            <div>
                                <input type="number" value="<?php echo isset($sessionLimits) ? @$sessionLimits : "1" ?>" min="1" name="sessionLimitPeriodCount" id="sessionLimitPeriodCount" class="form-control" placeholder="<?php echo lang("Enter Number") ?>">
                                <input type="submit" name="submit" value="<?php echo lang("Save") ?>" class="btn btn-info btn-md">
                                <span class="notes"><?php echo lang("Enter the Number of minute/s") ?></span>
                            </div>
                            <br/>
                        </form>
                    </div>
                </div>
            </div>
            <!-- SESSION LIMIT SECTION END -->

            <!-- LOSS LIMIT SECTION START -->
            <!--
        <div id="lossLimitTab" class="tab-pane fade">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <?php
                if (isset($lossLimitsAmount)) {?>
                            <strong>
                                <?php echo lang("Current Settings: System will stop you from playing and block you from the game and website once you reached ") . " " . @$lossLimitsAmount . " loss amount " . @$periodType . " and will reactivate your account on the reactivation period." ?>
                                <?php echo "(" . $loss_period_cnt . " " . lang("day/s after deactivation") . ")"; ?>
                                <a href="<?=site_url('/player_center2/responsible_game/cancelPlayerLimit/' . Responsible_gaming::LOSS_LIMITS)?>" class="btn btn-xs btn-success">
                                    Cancel Loss Limit
                                </a>
                            </strong>
                            <br/><br/>
                        <?php }?>
                    </div>
                </div>
                <div class="col-md-6" id="sessionLimitSection">
                    <form id="gameRespForm" action="<?=site_url('/player_center2/responsible_game/postLossLimits')?>" method="post" role="form">
                        <br/>
                        <p><?php echo lang("A Loss Limit enables you to set a limit to the amount of money you allow yourself to lose in a game. Connect this limit to your account on a daily, weekly or monthly basis and change your limit whenever you choose. " . $respGameData['loss_limit_approval_day_cnt'] . " day/s effectivity period.") ?></p>
                        <br/>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="number" value="<?php if(isset($lossLimitsAmount)){echo $lossLimitsAmount;}else{echo "100";}?>" min="10" name="lossLimitsAmount" id="lossLimitsAmount" class="form-control" placeholder="<?php echo lang("Enter Number") ?>">
                                <span class="notes"><?php echo lang("Enter the loss limit amount") ?></span>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" name="periodType">
                                    <option value="1"><?php echo lang("Daily") ?></option>
                                    <option value="2"><?php echo lang("Weekly") ?></option>
                                    <option value="3"><?php echo lang("Monthly") ?></option>
                                </select>
                                <span class="notes"><?php echo lang("Enter the loss limit period") ?></span>
                            </div>
                            <div class="col-md-3">
                                <input type="number" value="1" min="1" name="lossLimitsReactivationPeriodCnt" id="lossLimitsReactivationPeriodCnt" class="form-control" placeholder="<?php echo lang("Enter Number") ?>">
                                <span class="notes"><?php echo lang("Enter reactivation day\'s") ?></span>
                            </div>
                            <div class="col-md-2">
                                <input type="submit" name="submit" value="<?php echo lang("Save") ?>" class="btn btn-info btn-md">
                            </div>
                        </div>
                        <br/>
                    </form>
                </div>
            </div>
        </div>-->
            <!-- LOSS LIMIT SECTION END -->

            <!-- DEPOSIT LIMIT SECTION START -->
            <div id="depositLimitTab" class="tab-pane fade">
                <form id="gameRespFormDepositLimits" name="gameRespFormDepositLimits" action="<?=site_url('/player_center2/responsible_game/postDepositLimits')?>" method="post" role="form">
                    <input type="hidden" id="depositLimitsDefaultAmount" value="<?=($depositLimitsExists) ? $depositLimitsAmount : 0;?>">
                    <input type="hidden" id="overDepositLimitDefaultAmount" value="false">
                    <input type="hidden" id="hintBeforeSubmitDepositLimits">

                    <div class="deposit_limits_status">
                        <?php if ($depositLimitsExists):?>
                            <a href="#" class="btn btn-xs btn-success"><?php  echo lang("Deposit Limit Active")?></a>
                        <?php else: ?>
                            <a href="#" class="btn btn-xs btn-info"><?php  echo lang("Deposit Limit InActive")?></a>
                        <?php endif ?>
                    </div>
                    <br />
                    <div class="depositLimitSection">
                        <p><?php echo lang("A Deposit Limit enables you to set a limit on the amount of money you deposit into your online account."); ?></p>

                        <?php if($depositLimitsExists):?>
                        <div class="row">
                            <div class="col-xs-12 col-md-10">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th><?=lang('Deposit Limits')?></th>
                                        <th><?=lang('Relative Duration')?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th scope="row"><?=lang('Effective Currently')?></th>
                                        <td><?=$depositLimitRemainTotalAmount.'/'.$depositLimitsAmount?><h6><?='( '.lang('Remain Amount').' / '.lang('Max Amount').' )'?></h6></td>
                                        <td>
                                            <?=lang('FromCount').'：'.$depositLimitResetPeriodStart . '<br>' . lang('ToCount').'：'.$depositLimitResetPeriodEnd?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?=lang('Effective next cycle')?></th>
                                        <td><?=$depositLimits_latest_amount?><h6><?='( '.lang('Max Amount').' )'?></h6></td>
                                        <td>
                                            <?=lang('FromCount').'：'.$depositLimits_latest_date_from . '<br>' . lang('ToCount').'：'.$depositLimits_latest_date_to?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif;?>
                    </div>
                    <div class="row dep-limit">
                        <div class="col-md-4">
                            <label for="depositLimitsAmount"><span class="deposit_limit_amount_label"><?php echo lang("Set Deposit Amount"); ?></span><?=$this->CI->utils->displayCurrencyLabel()?></label>
                            <input type="number" class="form-control" id="depositLimitsAmount" name="depositLimitsAmount" placeholder="<?php echo lang("Please input valid deposit amount") ?>" style="text-align: right;" value="<?=($depositLimitsExists) ? $depositLimitsAmount : 0;?>">
                        </div>
                        <div class="col-md-6">
                            <?php echo lang("for next ").'('.implode('/',$deposit_limits_day_options).')'.lang(' days'); ?>
                            <select class="form-control" name="depositLimitsPeriodCnt" id="depositLimitsPeriodCnt">
                                <?php foreach ($deposit_limits_day_options as $days):?>
                                    <?php if($depositLimitsExists):?>
                                            <option value="<?= $days ?>" <?= ($days == $deposit_period_cnt)?'selected':''?>> <?= $days?> </option>
                                    <?php else:?>
                                        <option value="<?= $days ?>"> <?= $days?> </option>
                                    <?php endif;?>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-md-12">
                            <input type="submit" id="depositLimitsSubmit" style="margin-top: 18px" value="<?=($depositLimitsExists)?lang("lang.save"):lang("cashier.97");?>" class="btn btn-info btn-md l1-btn">
                        </div>
                    </div>
                </form>
            </div>
            <!-- DEPOSIT LIMIT SECTION END -->

            <!-- WAGERING LIMIT SECTION START -->
            <?php if(!$respGameData['disable_and_hide_wagering_limits']):?>
            <div id="wageringLimitTab" class="tab-pane fade">
                <form id="gameRespFormWagetingLimits" name="gameRespFormWagetingLimits" action="<?=site_url('/player_center2/responsible_game/postWageringLimits')?>" method="post" role="form">
                    <input type="hidden" id="wageringLimitsDefaultAmount" value="<?=($wageringLimitsExists) ? $wageringLimitsAmount : 0;?>">
                    <input type="hidden" id="overDefaultAmount" value="false">
                    <input type="hidden" id="hintBeforeSubmitWageringLimits">

                    <div class="wagering_limit_status">
                        <?php if ($wageringLimitsExists): ?>
                            <a href="#" class="btn btn-xs btn-success"><?php  echo lang("Wagering Limits Enabled")?></a>
                        <?php else: ?>
                            <a href="#" class="btn btn-xs btn-info"><?php  echo lang("Wagering Limits Not Enabled")?></a>
                        <?php endif ?>
                    </div>
                    <br/>
                    <div class="wageringLimitSection">
                        <p><?=lang('Wagering Limits feature allows you to control exactly how much money you wish to transfer to your “Games Wallet” in 1-30 days period.')?></p>
                        <p><?=lang('Please note it will take days you selected to wagering limit, a decrease will be effected immediately.')?></p>

                        <?php if($wageringLimitsExists):?>
                        <div class="row">
                            <div class="col-xs-12 col-md-10">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th><?=lang('Wagering Limits')?></th>
                                        <th><?=lang('Relative Duration')?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th scope="row"><?=lang('Effective Currently')?></th>
                                        <td><?=$wageringLimitRemainTotalAmount.'/'.$wageringLimitsAmount?><h6><?='( '.lang('Remain Amount').' / '.lang('Max Amount').' )'?></h6></td>
                                        <td>
                                            <?=lang('FromCount').'：'.$wageringLimitResetPeriodStart . '<br>' . lang('ToCount').'：'.$wageringLimitResetPeriodEnd?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?=lang('Effective next cycle')?></th>
                                        <td><?=$wageringLimits_latest_amount?><h6><?='( '.lang('Max Amount').' )'?></h6></td>
                                        <td>
                                            <?=lang('FromCount').'：'.$wageringLimits_latest_date_from . '<br>' . lang('ToCount').'：'.$wageringLimits_latest_date_to?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif;?>
                    </div>
                    <div class="row wagering-limit">
                        <div class="col-md-4">
                            <label for="wageringLimitsAmount"><span class="wagering_limit_amount_label"><?=lang("Set Wagering Limit"); ?></span><?=$this->CI->utils->displayCurrencyLabel()?></label>
                            <input type="number" class="form-control" id="wageringLimitsAmount" name="wageringLimitsAmount" placeholder="<?php echo lang("Please input valid deposit amount") ?>" style="text-align: right;" value="<?=($wageringLimitsExists) ? $wageringLimitsAmount : 0;?>">
                        </div>
                        <div class="col-md-6">
                            <?php echo lang("for next ").'('.implode('/',$wagering_limits_day_options).')'.lang(' days'); ?>
                            <select class="form-control" name="wageringLimitsPeriodCount" id="wageringLimitsPeriodCount">
                                <?php foreach ($wagering_limits_day_options as $days):?>
                                    <?php if($wageringLimitsExists):?>
                                            <option value="<?= $days ?>" <?= ($days == $wagering_limit_period_cnt)?'selected':'';?>> <?= $days?> </option>
                                    <?php else:?>
                                        <option value="<?= $days ?>"> <?= $days?> </option>
                                    <?php endif;?>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-md-12">
                            <input type="submit" id="wageringLimitsSubmit" style="margin-top: 18px" value="<?=($wageringLimitsExists)?lang("lang.save"):lang("cashier.97");?>" class="btn btn-info btn-md l1-btn">
                        </div>
                    </div>
                </form>
            </div>
            <!-- WAGERING LIMIT SECTION END -->
            <?php endif;?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#permanentExclusionSec").hide();

        $('form#gameRespFormDepositLimits *').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                $('#depositLimitsSubmit').trigger('click');
                return false;
            }
        });

        $('form#wageringLimitsAmount *').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                $('#depositLimitsSubmit').trigger('click');
                return false;
            }
        });

        //confirmation
        $('form#gameRespForm').submit(function() {
            var msg = "<?php echo lang('responsible_gaming.confirmation'); ?>";
            var c = confirm(msg);
            return c;
        });

        $('#depositLimitsSubmit').on('click', function(e){
            checkOverDepositLimitDefaultAmount();

            var msg = "<?php echo lang('Are you sure you want to continue?'); ?>";
            <?php if($depositLimitsExists):?>
            var deposit_litmits_submit_msg = $('#hintBeforeSubmitDepositLimits').val();
            msg = deposit_litmits_submit_msg + msg;
            <?php endif;?>

            MessageBox.confirm(msg, null, function(){
                $('form#gameRespFormDepositLimits').submit();
            }, function(){
            });

            e.preventDefault();

            return true;
        });

        <?php if(!$respGameData['disable_and_hide_wagering_limits']):?>
        $('#wageringLimitsSubmit').on('click', function(e){
            checkOverDefaultAmount();

            var msg = "<?php echo lang('Are you sure you want to continue?'); ?>";
            <?php if($wageringLimitsExists):?>
            var wagering_limits_submit_msg = $('#hintBeforeSubmitWageringLimits').val();
            msg = wagering_limits_submit_msg + msg;
            <?php endif;?>

            MessageBox.confirm(msg, null, function(){
                $('form#gameRespFormWagetingLimits').submit();
            }, function(){
            });

            e.preventDefault();

            return true;
        });
        <?php endif;?>

        $('#coolOffBtn').prop("disabled", true);
        $('#tempPerioddCountBtn').prop("disabled", true);
    });

    function setSelfExclusionType(type){
        if(type == "permanent"){
            $("#temporaryExclusionSec").hide();
            $("#permanentExclusionSec").show();
        }else{
            $("#temporaryExclusionSec").show();
            $("#permanentExclusionSec").hide();
        }
    }

    function checkOverDefaultAmount(){
        var currentTotalAmount = 0, newApplayAmount = 0, remianAmountInThisCycle = 0, usedAmountInThisCycle = 0, newTotalAmount = 0, currentPeriodCnt = 0, newApplyPeriodCnt = 0;
        var effective_cycle = null, hintBeforeSubmit = null;

        <?php if($wageringLimitsExists): ?>
            currentTotalAmount = parseInt('<?=$wageringLimitsAmount?>');
            newApplayAmount = parseInt($('#wageringLimitsAmount').val());
            remianAmountInThisCycle = parseInt(<?=$wageringLimitRemainTotalAmount?>);
            usedAmountInThisCycle = currentTotalAmount - remianAmountInThisCycle;
            newTotalAmount = usedAmountInThisCycle + newApplayAmount;
            currentPeriodCnt = parseInt(<?=$wagering_limit_period_cnt?>);
            newApplyPeriodCnt = parseInt($('#wageringLimitsPeriodCount :selected').val());

            if(newApplyPeriodCnt != currentPeriodCnt){
                effective_cycle = '<?php echo lang('Effective next cycle');?>';

                hintBeforeSubmit = '<?=sprintf(lang('rg_hint_with_diffetent_period_cnt_before_sumbit'), '{0}', '{1}')?>';
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{0\}/g , effective_cycle);
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{1\}/g , newApplayAmount);
            }else{
                if(newApplayAmount < remianAmountInThisCycle){
                    effective_cycle = '<?php echo lang('Effective Immediately');?>';
                }else{
                    effective_cycle = '<?php echo lang('Effective next cycle');?>';
                }

                hintBeforeSubmit = '<?=sprintf(lang('rg_hint_before_sumbit'), '{0}', '{1}', '{2}', '{3}', '{4}')?>';
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{0\}/g , newApplayAmount);
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{1\}/g , usedAmountInThisCycle);
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{2\}/g , effective_cycle);
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{3\}/g , effective_cycle);
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{4\}/g , newTotalAmount);
            }

            $('#hintBeforeSubmitWageringLimits').val(hintBeforeSubmit);
        <?php endif;?>

        var overDefaultAmount = $('#overDefaultAmount').val();
        $('#wageringLimitsPeriodCount').removeAttr('disabled');
        if (!overDefaultAmount) {
            return true;
        } else {
            return false;
        }
    }


    function checkOverDepositLimitDefaultAmount(){
        var currentTotalAmount = 0, newApplayAmount = 0, remianAmountInThisCycle = 0, usedAmountInThisCycle = 0, newTotalAmount = 0, currentPeriodCnt = 0, newApplyPeriodCnt = 0;
        var effective_cycle = null, hintBeforeSubmit = null;

        <?php if($depositLimitsExists): ?>
            currentTotalAmount = parseInt('<?=$depositLimitsAmount?>');
            newApplayAmount = parseInt($('#depositLimitsAmount').val());
            remianAmountInThisCycle = parseInt(<?=$depositLimitRemainTotalAmount?>);
            usedAmountInThisCycle = currentTotalAmount - remianAmountInThisCycle;
            newTotalAmount = usedAmountInThisCycle + newApplayAmount;
            currentPeriodCnt = parseInt(<?=$deposit_period_cnt?>);
            newApplyPeriodCnt = parseInt($('#depositLimitsPeriodCnt :selected').val());

            if(newApplyPeriodCnt != currentPeriodCnt){
                effective_cycle = '<?php echo lang('Effective next cycle');?>';

                hintBeforeSubmit = '<?=sprintf(lang('rg_hint_with_diffetent_period_cnt_before_sumbit'), '{0}', '{1}')?>';
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{0\}/g , effective_cycle);
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{1\}/g , newApplayAmount);
            }else {
                if(newApplayAmount < remianAmountInThisCycle){
                    effective_cycle = '<?php echo lang('Effective Immediately');?>';
                }else{
                    effective_cycle = '<?php echo lang('Effective next cycle');?>';
                }

                hintBeforeSubmit = '<?=sprintf(lang('rg_hint_before_sumbit'), '{0}', '{1}', '{2}', '{3}', '{4}')?>';
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{0\}/g, newApplayAmount);
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{1\}/g, usedAmountInThisCycle);
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{2\}/g, effective_cycle);
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{3\}/g, effective_cycle);
                hintBeforeSubmit = hintBeforeSubmit.replace(/\{4\}/g, newTotalAmount);
            }
            $('#hintBeforeSubmitDepositLimits').val(hintBeforeSubmit);
        <?php endif;?>

        var overDefaultAmount = $('#overDepositLimitDefaultAmount').val();
        if(!overDefaultAmount){
            return true;
        }else{
            return false;
        }
    }

    $('#timerReminderBtn').click(function() {
        var base_url = "<?=site_url()?>";
        $.ajax({
            'url' : base_url +'iframe_module/setStartTimeForTimerReminder/'+player_id,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                // utils.safelog(data);
            }
        },'json');
    });
    $('#coolOffPeriodCount').change(function () {
        var selval = parseInt(this.value);
        if(selval>0){
            $('#coolOffBtn').prop("disabled", false);
        }else{
            $('#coolOffBtn').prop("disabled", true);
        }

    });
    $('#tempPeriodCount').change(function () {
        var selval = parseInt(this.value);
        if(selval>0){
            $('#tempPerioddCountBtn').prop("disabled", false);
        }else{
            $('#tempPerioddCountBtn').prop("disabled", true);
        }
    });



</script>