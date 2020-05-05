<?php
$allow_manually_deposit = TRUE;

/**
 * Compatible PC version of the code
 */
$this->CI->load->library(array('player_responsible_gaming_library'));
$deposit_process_mode = $this->operatorglobalsettings->getSettingIntValue('deposit_process', DEPOSIT_PROCESS_MODE2);
$system_feature_add_security_on_deposit_transaction = $this->utils->isEnabledFeature('add_security_on_deposit_transaction');
$system_feature_enabled_withdrawal_password = $this->utils->isEnabledFeature('enabled_withdrawal_password');
$system_feature_use_self_pick_promotion = $this->utils->isEnabledFeature('use_self_pick_promotion');
$system_feature_use_self_pick_subwallets = $this->utils->isEnabledFeature('use_self_pick_subwallets');
$force_setup_player_deposit_bank_if_empty = $this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_require_deposit_bank_account');
$system_feature_enable_mobile_manual_deposit_redirect_to_dashboard_after_deposit = $this->utils->isEnabledFeature('enable_mobile_manual_deposit_redirect_to_dashboard_after_deposit');
$system_feature_redirect_immediately_after_manual_deposit = $this->utils->isEnabledFeature('redirect_immediately_after_manual_deposit');

$big_wallet = $this->wallet_model->getOrderBigWallet($player['playerId']);
$pendingBalance = (object) ['frozen' => $big_wallet['main']['frozen']];
$walletinfo = array(
    'mainWallet' => $big_wallet['main']['total_nofrozen'],
    'frozen' => $big_wallet['main']['frozen'],
    'subwallets' => $big_wallet['sub']
);

$currency = $this->utils->getCurrentCurrency();

if ($system_feature_add_security_on_deposit_transaction && $system_feature_enabled_withdrawal_password ){
    $payment_manual_accounts = [];
}

$player_bank_accounts = $playerBankDetails;

$deposit_step = 1;
$upload_image_max_size =$this->utils->getMaxUploadSizeByte();
?>
<script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('clipboard/clipboard.min.js')?>"></script>
<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/common/js/player_center/deposit.js')?>"></script>
<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/common/js/player_center/player-bank-account.js')?>"></script>
<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/common/js/plugins/province_city_select.js')?>"></script>
<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/common/js/player_center/player-cashier.js')?>"></script>
<div id="deposit-tab-content-manual">
    <div class="panel">
        <?php include __DIR__ . '/../../cashier/deposit/manual/last_order_status.php'; ?>
        <form id="form-deposit" class="deposit-form <?=($allow_manually_deposit) ? '' : 'hide_deposit_form'?>" role="form" enctype="multipart/form-data">
            <div id="deposit-mode-1-step-1">
                <input type="hidden" id="deposit_time">
                <input type="hidden" id="deposit_time_out">
                <input type="hidden" id="secure_id">
                <input type="hidden" id="deposit_process_mode" value="<?=$deposit_process_mode?>">
                <input type="hidden" id="force_setup_player_deposit_bank_if_empty" value="<?=$force_setup_player_deposit_bank_if_empty?>">
                <input type="hidden" id="system_feature_enable_mobile_manual_deposit_redirect_to_dashboard_after_deposit" value="<?=$system_feature_enable_mobile_manual_deposit_redirect_to_dashboard_after_deposit?>">
                <input type="hidden" id="system_feature_redirect_immediately_after_manual_deposit" value="<?=$system_feature_redirect_immediately_after_manual_deposit?>">
                <div class="panel-body">
                    <?php
                        if($this->utils->isEnabledFeature('responsible_gaming') && (FALSE !== $depositLimitHint = $this->CI->player_responsible_gaming_library->displayDepositLimitHint())){
                            echo $depositLimitHint;
                        }
                    ?>
                    <?php
                    switch($deposit_process_mode){
                        case DEPOSIT_PROCESS_MODE2:
                        case DEPOSIT_PROCESS_MODE3:
                            include __DIR__ . '/../../cashier/deposit/manual/select_deposit_bank.php';
                            include __DIR__ . '/../../cashier/deposit/manual/secure_id.php';

                            if($this->utils->isEnabledFeature('enable_using_last_deposit_account')) {
                                include __DIR__ . '/../../cashier/deposit/manual/player_deposit_bank_enable_last_account.php';
                            }
                            else {
                                include __DIR__ . '/../../cashier/deposit/manual/player_deposit_bank_account.php';
                            }

                            include __DIR__ . '/../../cashier/deposit/manual/deposit_realname.php';
                            include __DIR__ . '/../../cashier/deposit/manual/deposit_amount.php';
                            include __DIR__ . '/../../cashier/deposit/manual/select_promo.php';
                            include __DIR__ . '/../../cashier/deposit/manual/select_wallet.php';
                            include __DIR__ . '/../../cashier/deposit/manual/deposit_datetime.php';
                            include __DIR__ . '/../../cashier/deposit/manual/attached_documents.php';
                            include __DIR__ . '/../../cashier/deposit/manual/note.php';
                        break;
                        case DEPOSIT_PROCESS_MODE1:
                        default:
                            include __DIR__ . '/../../cashier/deposit/manual/select_deposit_bank.php';
                            include __DIR__ . '/../../cashier/deposit/manual/player_deposit_bank_account.php';
                            include __DIR__ . '/../../cashier/deposit/manual/deposit_realname.php';
                            include __DIR__ . '/../../cashier/deposit/manual/deposit_amount.php';
                            include __DIR__ . '/../../cashier/deposit/manual/select_promo.php';
                            include __DIR__ . '/../../cashier/deposit/manual/select_wallet.php';
                            include __DIR__ . '/../../cashier/deposit/manual/deposit_datetime.php';

                            if(!$this->config->item('enable_deposit_mode_1_two_steps_flow')) {
                                include __DIR__ . '/../../cashier/deposit/manual/attached_documents.php';
                            }

                            include __DIR__ . '/../../cashier/deposit/manual/note.php';
                        break;
                    }
                    ?>

                    <div class="form-inline deposit-process-mode-<?=$deposit_process_mode?> submit-deposit-order">
                    <?php if($deposit_process_mode == DEPOSIT_PROCESS_MODE1 && $this->config->item('enable_deposit_mode_1_two_steps_flow')) { ?>
                        <button id="deposit-mode-1-show-step-2" type="button" class="btn btn-submit mc-btn mc-btn-confirm form-control"><?=lang('Next Step')?></button>
                    <?php } else { ?>
                        <?php if($isAlipay):?>
                            <div class="helper-content text-danger font-weight-bold">
                                <?=lang('manual.alipay.submit.hint')?>
                            </div>
                         <?php elseif($isUnionpay || $isWechat):?>
                            <div class="helper-content text-danger font-weight-bold">
                            </div>
                        <?php else:?>
                            <div class="manual-deposit-submit-hint text-danger font-weight-bold">
                            <?=lang('manual.deposit_submit.hint')?>
                            </div>
                        <?php endif;?>
                        <?php if($isUnionpay || $isWechat):?>
                        <button type="button" class="btn btn-submit form-control"><?=lang('Deposit_submit_transfer')?></button>
                        <?php else:?>
                        <button type="button" class="btn btn-submit form-control"><?=lang('Deposit_submit')?></button>
                        <?php endif;?>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <?php if($deposit_process_mode == DEPOSIT_PROCESS_MODE1 && $this->config->item('enable_deposit_mode_1_two_steps_flow')) { ?>
            <div id="deposit-mode-1-step-2" style="display: none">
                <?php include __DIR__ . '/../../cashier/deposit/manual/attached_documents_2.php'; ?>
                <div class="form-inline deposit-process-mode-<?=$deposit_process_mode?> submit-deposit-order">
                    <button id="step-2-upload-files-submit-btn" style="margin-top: 100px" type="button" class="btn mc-btn mc-btn-confirm form-control"><?=lang('Deposit_submit')?></button>
                </div>
            </div>
            <?php } ?>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../../cashier/deposit/modal.php'; ?>
<?php include __DIR__ . '/../../bank_account/content/modal.php'; ?>

<script type="text/javascript">
    var DEPOSIT_PROCESS_MODE1 = parseInt("<?=DEPOSIT_PROCESS_MODE1?>");
    var DEPOSIT_PROCESS_MODE2 = parseInt("<?=DEPOSIT_PROCESS_MODE2?>");
    var DEPOSIT_PROCESS_MODE3 = parseInt("<?=DEPOSIT_PROCESS_MODE3?>");
    var ENABLE_ATTACHED_DOCUMENTS = "<?= $enable_deposit_upload_documents ? '1' : '0' ?>";
    var ALLOWED_UPLOAD_FILE = "<?= $this->config->item('allowed_upload_file') ?>";
    var LANG_UPLOAD_IMAGE_MAX_SIZE = "<?= $upload_image_max_size ?>";
    var LANG_UPLOAD_FILE_ERRMSG = "<?= sprintf(lang('upload image limit and format'),$upload_image_max_size/1000000,$this->config->item('allowed_upload_file')) ?>";
    var ENABLE_USING_LAST_DEPOSIT_ACCOUNT = "<?= $this->utils->isEnabledFeature('enable_using_last_deposit_account') ?>";
    var ENABLE_DEPOSIT_CATEGORY_VIEW = "<?= $this->utils->isEnabledFeature('enable_deposit_category_view') ?>";
    var payment_account_id = "<?=$payment_account_id ?>";
    var IS_MOBILE = "<?= $this->CI->utils->is_mobile() ?>";
    var DISABLE_PLAYER_DEPOSIT_BANK = "<?=!$this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_enable_deposit_bank'); ?>";
    var ENABLE_DEPOSIT_MODE_1_TWO_STEPS_FLOW = "<?= $this->config->item('enable_deposit_mode_1_two_steps_flow') ?>";

    var LANG_UPLOAD_FILE_REQUIRED_ERRMSG = "<?=lang('Please upload at least one file when using ATM/Cashier payment account.')?>";

    var required_deposit_upload_file_1 = <?= $required_deposit_upload_file_1 ? 'true' : 'false' ?>;
    var system_feature_redirect_immediately_after_manual_deposit = <?=$this->utils->isEnabledFeature('redirect_immediately_after_manual_deposit') ? 'true' : 'false'?>;
    var CURRENCY_CONVERSION_RATE = "<?=isset($currency_conversion_rate) ? $currency_conversion_rate : '0' ?>";
</script>