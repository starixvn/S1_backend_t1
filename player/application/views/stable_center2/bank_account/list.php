<style>
    .invalid_bank {
        background-color: #C6C6C6 !important;
        opacity:0.6;
    }
    .bank_account_info {
        height: 168px;
        width: 333px;
    }
}
</style>
<div id="bank_account" class="panel">
    <div class="panel-heading">
        <h1 class="hidden-xs hidden-sm"><?= lang('cashier.16') ?></h1>
    </div>
    <div class="panel-body bank-account-list sub_content">
        <?php if($this->operatorglobalsettings->getSettingValueWithoutCache('financial_account_enable_deposit_bank')): ?>
            <ul id="bank_account_tab_nav" class="nav nav-tabs nav-justified fm-ul">
                <li><a href="#bank_account_deposit" class="add-deposit-bank-account" data-toggle="tab"><?=lang('xpj.iframe_module.Deposit_Bank')?></a></li>
                <li><a href="#bank_account_withdrawal" class="add-withdrawal-bank-account" data-toggle="tab"><?=lang('xpj.iframe_module.Withdraw_Bank')?></a></li>
            </ul>
            <div class="bank_account_tab_content tab-content fm-content">
                <div id="bank_account_deposit" class="tab-pane fade in">
                    <?php include 'content/deposit.php'; ?>
                </div>

                <div id="bank_account_withdrawal" class="tab-pane fade">
                    <?php include 'content/withdrawal.php'; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="bank_account_tab_content tab-content fm-content">
                <div id="bank_account_withdrawal" class="tab-pane fade in active">
                    <?php include 'content/withdrawal.php'; ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>

<script type="text/javascript">
var EMPTY_ACCOUNT_NAME_REDIRECT_URL = '<?=(!$this->utils->is_mobile()) ? '/player_center/dashboard/index#accountInformation' : '/player_center/profile'?>';
</script>

<?php include 'content/modal.php'; ?>