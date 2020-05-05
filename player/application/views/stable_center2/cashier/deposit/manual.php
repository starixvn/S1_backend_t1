<?php
$allow_manually_deposit = TRUE;

$this->load->library(array('player_responsible_gaming_library'));
$deposit_step = 1;
$upload_image_max_size =$this->utils->getMaxUploadSizeByte();
$system_feature_redirect_immediately_after_manual_deposit = $this->utils->isEnabledFeature('redirect_immediately_after_manual_deposit');
?>
<!--- ===========================================================
Bank Deposit
=========================================================== -->
<?php include 'manual/last_order_status.php'; ?>
<div id="deposit-tab-content-manual" class="tab-pane fade in <?=($allow_manually_deposit) ? '' : 'hide_deposit_form'?>">
    <form id="form-deposit" class="fmd-step1 deposit-form" role="form" enctype="multipart/form-data">
        <div id="deposit-mode-1-step-1">
            <input type="hidden" id="deposit_time">
            <input type="hidden" id="deposit_time_out">
            <input type="hidden" id="deposit_process_mode" value="<?=$deposit_process_mode?>">
            <input type="hidden" id="force_setup_player_deposit_bank_if_empty" value="<?=$force_setup_player_deposit_bank_if_empty?>">
            <?php if(isset($in_cool_down_time) && $in_cool_down_time){?>
            <div class="has-error">
                <p class="help-block with-errors">
                    <?=sprintf(lang('hint.manually.deposit.cool.down'), $manually_deposit_cool_down_minutes)?>
                </p>
            </div>
            <?php }?>
            <?php
                if($this->utils->isEnabledFeature('responsible_gaming') && (FALSE !== $depositsLimitHint = $this->player_responsible_gaming_library->displayDepositLimitHint())){
                    echo $depositsLimitHint;
                }
            ?>
            <?php
            switch($deposit_process_mode){
                case DEPOSIT_PROCESS_MODE2:
                case DEPOSIT_PROCESS_MODE3:
                    include 'manual/select_deposit_bank.php';

                    if($this->utils->isEnabledFeature('enable_using_last_deposit_account')) {
                        include 'manual/player_deposit_bank_enable_last_account.php';
                    }
                    else {
                        include 'manual/player_deposit_bank_account.php';
                    }

                    include 'manual/secure_id.php';
                    include 'manual/deposit_realname.php';
                    include 'manual/deposit_amount.php';
                    include 'manual/select_promo.php';
                    include 'manual/select_wallet.php';
                    include 'manual/deposit_datetime.php';
                    include 'manual/attached_documents.php';
                    include 'manual/note.php';
                break;
                case DEPOSIT_PROCESS_MODE1:
                default:
                    include 'manual/select_deposit_bank.php';
                    include 'manual/player_deposit_bank_account.php';
                    include 'manual/deposit_realname.php';
                    include 'manual/deposit_amount.php';
                    include 'manual/select_promo.php';
                    include 'manual/select_wallet.php';
                    include 'manual/deposit_datetime.php';

                    if(!$this->config->item('enable_deposit_mode_1_two_steps_flow')) {
                        include 'manual/attached_documents.php';
                    }

                    include 'manual/note.php';
                break;
            }
            ?>
            <div class="form-inline deposit-process-mode-<?=$deposit_process_mode?> submit-deposit-order">
            <?php if($deposit_process_mode == DEPOSIT_PROCESS_MODE1 && $this->config->item('enable_deposit_mode_1_two_steps_flow')) { ?>
                <button id="deposit-mode-1-show-step-2" type="button" class="btn btn-submit form-control"><?=lang('Next Step')?></button>
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

        <?php if($deposit_process_mode == DEPOSIT_PROCESS_MODE1 && $this->config->item('enable_deposit_mode_1_two_steps_flow')) { ?>
        <div id="deposit-mode-1-step-2" style="display: none">
            <?php include 'manual/attached_documents_2.php'; ?>
            <div class="form-inline deposit-process-mode-<?=$deposit_process_mode?> submit-deposit-order">
                <button type="button" id="step-2-upload-files-submit-btn" class="btn form-control"><?=lang('Deposit_submit')?></button>
            </div>
        </div>
        <?php } ?>
    </form>
</div>
<script type="text/javascript">
    var DEPOSIT_PROCESS_MODE1 = parseInt("<?=DEPOSIT_PROCESS_MODE1?>");
    var DEPOSIT_PROCESS_MODE2 = parseInt("<?=DEPOSIT_PROCESS_MODE2?>");
    var DEPOSIT_PROCESS_MODE3 = parseInt("<?=DEPOSIT_PROCESS_MODE3?>");
    var ALLOWED_UPLOAD_FILE = "<?= $this->config->item('allowed_upload_file') ?>";
    var LANG_UPLOAD_IMAGE_MAX_SIZE = "<?= $upload_image_max_size ?>";
    var LANG_UPLOAD_FILE_ERRMSG = "<?= sprintf(lang('upload image limit and format'),$upload_image_max_size/1000000,$this->config->item('allowed_upload_file')) ?>";
    var ENABLE_ATTACHED_DOCUMENTS = "<?= $enable_deposit_upload_documents ? '1' : '0' ?>";
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