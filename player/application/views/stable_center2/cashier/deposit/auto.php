<!--- ===========================================================
3rd Party Payment
=========================================================== -->
<?php
$this->load->model(array('external_system'));
$this->load->library(array('player_responsible_gaming_library'));

$from_url = site_url('player_center/autoDeposit3rdParty');
$form_method = 'POST';
$form_target = $external_system_api->getPaymentFormTarget();

/* @var $external_system_api Abstract_payment_api */

if ($external_system_api->showPaymentPopWindowStatus()){
    $form_target = 'iframePost';
}
?>
<div class="panel panel-borderless">
    <div class="panel-body nopadding">
        <?php
            if($this->utils->isEnabledFeature('responsible_gaming') && (FALSE !== $depositsLimitHint = $this->player_responsible_gaming_library->displayDepositLimitHint())){
                echo $depositsLimitHint;
            }
        ?>
        <?php if(isset($in_cool_down_time) && $in_cool_down_time){?>
        <div class="has-error">
            <p class="help-block with-errors">
                    <?=sprintf(lang('notify.still_in_cooldown_time'), $auto_deposit_cool_down_minutes)?>
            </p>
        </div>
        <?php }?>
        <?php if ($exists_payment_account && !$disable_form):?>
            <form id="form-deposit" class="deposit-form" action="<?=$from_url?>" method="<?=$form_method?>" target="<?=$form_target?>" autocomplete="off" data-paymenttype='auto'>
                <input type="hidden" name="bankTypeId" value="<?=$payment_account->bankTypeId?>" />
                <input type="hidden" name="deposit_from" id="deposit_from" value="<?=$payment_account->external_system_id?>" />
                <div class="input_main">
                    <?php $this->load->view('/resources/common/cashier/auto/input_type', $_ci_vars); ?>

                    <?php include 'auto/select_promo.php'; ?>

                    <?php include 'auto/select_wallet.php'; ?>

                    <?php if (isset($vipsettings)): ?>
                        <div class="input_name_2">
                            <div class="input_name_text"><?=lang('player.groupLevel')?></div>
                            <div class="select_form">
                                <div class="zizhulist dropdown show">
                                    <button
                                        class="btn btn-primary dropdown-toggle moneyv"
                                        type="button" id="dropdownMenu4"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                    >
                                        <span><?=lang('please_select') . lang('player.groupLevel')?></span>
                                        <span class="caret"></span>
                                    </button>

                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu4">
                                        <?php foreach ($vipsettings as $key => $value) : ?>
                                            <li val="<?=$key?>">
                                                <a href="javascript: void(0);"><?=$value['name']?></a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <input type="hidden" name="group_level_id" class="field" value="" />
                            </div>
                        </div>
                    <?php endif;?>
                </div>
                <hr />
                <div class="row nopadding">
                    <button type="button" class="btn btn-primary" id="auto_payment_submit" disabled="disabled"><?=lang('Deposit_submit')?></button>
                    <button type="submit" class="btn btn-primary btn-submit mc-btn mc-btn-confirm" disabled="disabled" style="display: none"><?=lang('Deposit_submit')?></button>
                </div>

                <div class="row nopadding">
                    <div class="text-danger font-weight-bold float_amount_limit_hint" style="display: none"><?=isset($inputInfo['float_amount_limit_msg'])?$inputInfo['float_amount_limit_msg']:''?></div>
                </div>
            </form>
            <div class="line_bottom"></div>
        <?php else:?>
            <div id="deposit_content">
                <?php if($disable_form): ?>
                    <div class="text-danger font-weight-bold disable_form_msg"><?=$disable_form_msg?></div>
                <?php else: ?>
                    <div class="divtradetip">
                        <ul class="">
                            <li class=""><?php echo lang('online_payment_is_not_available');?></li>
                        </ul>
                    </div>
                <?php endif ?>
            </div>
        <?php endif;?>
    </div>
</div>

<div class="modal fade" id="dialog-trade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?=lang('Submitted successfully')?></h4>
            </div>
            <div class="modal-body">
                <div class="actions">
                    <div class="mover__element">
                        <section  class="action">
                            <ul class="binding_cont bpstep1">
                                <li class=""><img class="loading_img" height="200px" width="200px" src="<?=$this->CI->config->item('deposit_loading_gif')?>"></li>
                                <li class=""><strong><?=lang('Please operate on third-party website')?></strong></li>
                                <li class=""><span><?=lang('Please click respective buttons below')?></span></li>
                            </ul>
                        </section>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-confirm"><?=lang('Deposit successful')?></button>
                <button type="button" class="btn btn-default btn-close"><?=lang('Cancel deposit')?></button>
            </div>
        </div>
    </div>
</div>

<?php if ($external_system_api->showPaymentPopWindowStatus()) :?>
<div id="postIframe" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?=lang('lang.submit')?></h4>
            </div>
            <div class="modal-body">
                <iframe id="iframePost" style="zoom:0.60" width="99.6%" height="500" frameborder="0"  name="iframePost"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-confirm" onClick="window.top.location.reload();" ><?=lang('Deposit successful')?></button>
                <button type="button" class="btn btn-default btn-close" data-dismiss="modal"><?=lang('Cancel deposit')?></button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="depositUrl" value="<?=site_url('iframe_module/autoDeposit3rdParty')?>">
<?php endif; ?>

<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/common/js/player_center/player-cashier-auto.js')?>"></script>

<script type="text/javascript">
    PlayerCashierAuto.init({
        "form_selector": '#form-deposit',
        "submit_modal_selector": '#dialog-trade',
        "finish_payment_url": "<?=($this->utils->isEnabledFeature('enable_pc_player_back_to_dashboard_after_submit_deposit')) ? $this->utils->getPlayerHomeUrl() : $_SERVER['REQUEST_URI'] ?>"
    });
    var _360browser_hint = '<?=lang("Please go back to previous tab for deposit.")?>';

$(function () {
    var defaultScrolltop = "<?php echo $this->external_system->getPaymentInPopWindowDefaultScrolltopById($payment_account->external_system_id) ?>";
    $("#iframePost").contents().scrollTop(defaultScrolltop);
});

</script>