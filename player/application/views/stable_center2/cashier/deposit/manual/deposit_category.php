<?php
$show_deposit_bank_details = $this->utils->isEnabledFeature('show_deposit_bank_details');

$payment_auto_account_list = [];
$payment_manual_account_list = [];
$payment_manual_min_deposit=$this->utils->getConfig('defaultMinDepositDaily'); //default
$payment_manual_max_deposit=$this->utils->getConfig('defaultMaxDepositDaily'); //default

foreach ($payment_auto_accounts as $payment_account) {
    $payment_account_data = [
        'payment_account_link_num' => $payment_account->id,
        'bankTypeId' => $payment_account->bankTypeId,
        'bankName' => lang($payment_account->payment_type),
        'minDeposit' => $payment_account->vip_rule_min_deposit_trans,
        'maxDeposit' => $payment_account->vip_rule_max_deposit_trans,
        'minDeposit_currency' => $this->utils->formatCurrency($payment_account->vip_rule_min_deposit_trans),
        'maxDeposit_currency' => $this->utils->formatCurrency($payment_account->vip_rule_max_deposit_trans),
        'flag' => $payment_account->flag,
        'second_category_flag' => $payment_account->second_category_flag,
        'payment_order'=> $payment_account->payment_order
    ];

    if($payment_account_data['maxDeposit'] <= 0){
        $payment_account_data['maxDeposit'] = $this->utils->getConfig('defaultMaxDepositDaily');
    }

    if($payment_account_data['minDeposit'] <= 0){
        $payment_account_data['minDeposit'] = 0;
    }

    if($show_deposit_bank_details){
        $payment_account_data['bankAccountName'] = $payment_account->payment_account_name;
        $payment_account_data['bankAccountNo'] = $payment_account->payment_account_number;
        $payment_account_data['bankCity'] = NULL;
        $payment_account_data['branchName'] = $payment_account->payment_branch_name;
    }

    if ($payment_account->flag == AUTO_ONLINE_PAYMENT) {
        $payment_auto_account_list['cid-' . $payment_account_data['payment_account_link_num']] = $payment_account_data;
    }
}

foreach ($payment_manual_accounts as $payment_manual) {
    $payment_manual_min_deposit = ($payment_manual_min_deposit === FALSE) ? $payment_manual->vip_rule_min_deposit_trans : $payment_manual_min_deposit;
    $payment_manual_min_deposit = ($payment_manual->vip_rule_min_deposit_trans < $payment_manual_min_deposit) ? $payment_manual->vip_rule_min_deposit_trans : $payment_manual_min_deposit;
    $payment_manual_max_deposit = ($payment_manual->vip_rule_max_deposit_trans > $payment_manual_max_deposit) ? $payment_manual->vip_rule_max_deposit_trans : $payment_manual_max_deposit;

    $payment_account_data = [
        'payment_account_link_num' => $payment_manual->id,
        'bankTypeId' => $payment_manual->bankTypeId,
        'bankName' => lang($payment_manual->payment_type),
        'minDeposit' => $payment_manual->vip_rule_min_deposit_trans,
        'maxDeposit' => $payment_manual->vip_rule_max_deposit_trans,
        'minDeposit_currency' => $this->utils->formatCurrency($payment_manual->vip_rule_min_deposit_trans),
        'maxDeposit_currency' => $this->utils->formatCurrency($payment_manual->vip_rule_max_deposit_trans),
        'flag' => $payment_manual->flag,
        'second_category_flag' => $payment_manual->second_category_flag,
        'payment_order'=> $payment_manual->payment_order
    ];

    if($payment_account_data['maxDeposit'] <= 0){
        $payment_account_data['maxDeposit'] = $this->utils->getConfig('defaultMaxDepositDaily');
    }

    if($payment_account_data['minDeposit'] <= 0){
        $payment_account_data['minDeposit'] = 0;
    }

    if($show_deposit_bank_details){
        $payment_account_data['bankAccountName'] = $payment_manual->payment_account_name;
        $payment_account_data['bankAccountNo'] = $payment_manual->payment_account_number;
        $payment_account_data['bankCity'] = NULL;
        $payment_account_data['branchName'] = $payment_manual->payment_branch_name;
    }

    if ($payment_manual->flag == MANUAL_ONLINE_PAYMENT || $payment_manual->flag == LOCAL_BANK_OFFLINE) {
        $payment_manual_account_list['cid-' . $payment_account_data['payment_account_link_num']] = $payment_account_data;
    }
}

if((count($payment_auto_account_list) <= 0) && (count($payment_manual_accounts) <= 0)){
    $this->utils->show_message('danger', null, lang('No Deposit account Available'), '/player_center/dashboard');
    exit(0);
}
?>

<?php
    $this->load->library('player_responsible_gaming_library');
    if($this->utils->isEnabledFeature('responsible_gaming') && (FALSE !== $depositsLimitHint = $this->player_responsible_gaming_library->displayDepositLimitHint())) {
        echo $depositsLimitHint;
    }
?>
<div class="customize-content">
   <p><?=lang('deposit_category_customize_content')?></p>
</div>
<?php if(count($payment_auto_account_list)): ?>
<!-- draw auto list second category static html. Ready for put payment accounts such like: online_bank, bank_transfer, alipay...etc -->
<div class="panel no-gutter">
    <div class="panel-title">
        <?=lang('xpj.deposit.onlinepayment')?>
    </div>
    <div class="panel-body row">
        <div class="col-md-2 mc-ul">
            <ul id="second_category_auto_list" class="main-menu-nav player-center-navigation">
                <!-- append data by js function: renderSecondCategoryBtnAndGetDefaultNum(payment_account_list, payment_flag) -->
            </ul>
        </div>

        <div class="col-md-9 tab-content mc-content">
            <div class="table-responsive">
                <table class="table-deposit-option table">
                    <tbody id='accout_item_auto_list'>
                        <!-- append data by js function: refreshChoosenPaymentAccountType(chosen_id, payment_flag) -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif ?>
<?php if (!empty($payment_manual_accounts)) { ?>
<!-- draw manual list second category static html. Ready for put payment accounts such like: online_bank, bank_transfer, alipay...etc -->
<div class="panel no-gutter">
    <div class="panel-title">
        <?=lang('xpj.deposit.companypayment')?>
    </div>
    <div class="panel-body row">
        <div class="col-md-2 mc-ul">
            <ul id="second_category_manual_list" class="main-menu-nav player-center-navigation">
                <!-- append data by js function: renderSecondCategoryBtnAndGetDefaultNum(payment_account_list, payment_flag) -->
            </ul>
        </div>

        <div class="col-md-9 tab-content mc-content">
            <div class="table-responsive">
                <table class="table-deposit-option table">
                    <tbody id='accout_item_manual_list'>
                        <!-- append data by js function: refreshChoosenPaymentAccountType(chosen_id, payment_flag) -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<div class="panel no-gutter hidden">
    <div class="panel-body">
        <div style="padding: 6px; background: #E49F16; color: #FFF; margin: 5px 0px; border-radius: 3px; border: 1px solid #FFD45A; letter-spacing: 1px;"><?=lang
        ('xpj.deposit.note')?></div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    var payment_auto_account_list = JSON.parse('<?=json_encode($payment_auto_account_list);?>');
    var payment_manual_account_list = JSON.parse('<?=json_encode($payment_manual_account_list);?>');
    initRenderDepositCategoryContent();

    $('.account-type').on('click', function(e){
        e.preventDefault();
        var payment_flag = this.id.split('account-type-')[1].charAt(0);
        refreshChoosenPaymentAccountType(this.id, payment_flag);
    });

    function initRenderDepositCategoryContent() {
        var default_auto_second_category_num = renderSecondCategoryBtnAndGetDefaultNum(payment_auto_account_list, <?=AUTO_ONLINE_PAYMENT?>);
        var default_manual_second_category_num = renderSecondCategoryBtnAndGetDefaultNum(payment_manual_account_list, <?=MANUAL_ONLINE_PAYMENT?>);

        refreshChoosenPaymentAccountType('account-type-2-' + default_auto_second_category_num, <?=AUTO_ONLINE_PAYMENT?>);
        refreshChoosenPaymentAccountType('account-type-1-' + default_manual_second_category_num, <?=MANUAL_ONLINE_PAYMENT?>);
    }

    function renderSecondCategoryBtnAndGetDefaultNum(payment_account_list, payment_flag) {
        var account_second_category_list = $('#second_category_manual_list');
        if(payment_flag == 2) {
            account_second_category_list = $('#second_category_auto_list');
        }

        var second_category_list = [];
        var second_category_account_type_item_id_list = [];
        var second_category_account_type_item_name_list = [];

        $.each(payment_account_list, function(key, value){
            var check_id = '#accout-type-' + payment_flag + '-' + value['second_category_flag'];

            var find_second_category_type_num = $(check_id).length;
            if(second_category_account_type_item_id_list.indexOf('account-type-' + payment_flag + '-' + value['second_category_flag']) == -1) {

                second_category_account_type_item_id_list.push('account-type-' + payment_flag + '-' + value['second_category_flag']);
                second_category_list.push(value['second_category_flag']);

                switch(value['second_category_flag']) {
                    case '<?=SECOND_CATEGORY_ONLINE_BANK?>':
                        second_category_account_type_item_name_list.push('<?=lang("pay.second_category_online_bank")?>');
                        break;
                    case '<?=SECOND_CATEGORY_ALIPAY?>':
                        second_category_account_type_item_name_list.push('<?=lang("pay.second_category_alipay")?>');
                        break;
                    case '<?=SECOND_CATEGORY_WEIXIN?>':
                        second_category_account_type_item_name_list.push('<?=lang("pay.second_category_weixin")?>');
                        break;
                    case '<?=SECOND_CATEGORY_QQPAY?>':
                        second_category_account_type_item_name_list.push('<?=lang("pay.second_category_qqpay")?>');
                        break;
                    case '<?=SECOND_CATEGORY_UNIONPAY?>':
                        second_category_account_type_item_name_list.push('<?=lang("pay.second_category_unionpay")?>');
                        break;
                    case '<?=SECOND_CATEGORY_QUICKPAY?>':
                        second_category_account_type_item_name_list.push('<?=lang("pay.second_category_quickpay")?>');
                        break;
                    case '<?=SECOND_CATEGORY_JDPAY?>':
                        second_category_account_type_item_name_list.push('<?=lang("pay.second_category_jdpay")?>');
                        break;
                    case '<?=SECOND_CATEGORY_BANK_TRANSFER?>':
                        second_category_account_type_item_name_list.push('<?=lang("pay.second_category_bank_transfer")?>');
                        break;
                    case '<?=SECOND_CATEGORY_ATM_TRANSFER?>':
                        second_category_account_type_item_name_list.push('<?=lang("pay.second_category_atm_transfer")?>');
                        break;
                    case '<?=SECOND_CATEGORY_CRYPTOCURRENCY?>':
                        second_category_account_type_item_name_list.push('<?=lang("pay.second_category_cryptocurrency")?>');
                        break;
                    default:
                        second_category_account_type_item_name_list.push('<?=lang("pay.second_category_bank_transfer")?>');
                }
            }
        });

        console.log({ 'ids': second_category_account_type_item_id_list, 'names': second_category_account_type_item_name_list, 'cat': second_category_list });

        $.each(second_category_account_type_item_id_list, function(key, account_type_id){
            var use_active = '';
            if(account_type_id == 'account-type-' + payment_flag + '-1') use_active = 'active';
            var second_category_account_type_item =
                '<li id="' + account_type_id + '" class="account-type ' + use_active + '">' +
                    '<a href="#">' + second_category_account_type_item_name_list[key] + '</a>' +
                '</li>';
            account_second_category_list.append(second_category_account_type_item);
        });

        if(second_category_list.length > 0) {
            return second_category_list[0];
        }
    }

    function refreshChoosenPaymentAccountType(chosen_id, payment_flag) {
        var accout_item_list = $('#accout_item_manual_list');
        var payment_account_list = payment_manual_account_list;
        var flag_type = 'manual_payment';

        if(payment_flag == <?=AUTO_ONLINE_PAYMENT?>) {
            accout_item_list = $('#accout_item_auto_list');
            payment_account_list = payment_auto_account_list;
            flag_type = 'auto_payment';
        }

        setChosenPaymentAccountTypeBtnActive(payment_flag, chosen_id);
        renderPaymentAccountTitle(accout_item_list);
        renderPaymentAccountContent(payment_account_list, accout_item_list, chosen_id, flag_type, payment_flag);
    }

    function setChosenPaymentAccountTypeBtnActive(payment_flag, chosen_id) {
        $('.account-type.active').each(function(key, value) {
            if(value.id.split('account-type-')[1].charAt(0) == payment_flag) {
                $('#' + value.id).removeClass('active');
            }
        });
        $('#'+chosen_id).addClass('active');
    }

    function renderPaymentAccountTitle(accout_item_list) {
        accout_item_list.empty();
        <?php if($this->utils->is_mobile()){?>
        var th_title =
            '<tr class="title">' +
                '<th class="mc-table-title"><?=lang("pay.paymethod")?></th>' +
                '<th class="mc-table-title"><?=lang("deposit_category_deposit")?></th>' +
                '<th class="mc-table-title"><?=lang("Amount Limit")?></th>' +
            '</tr>';
        <?php } else { ?>
        var th_title =
            '<tr class="title">' +
                '<th class="mc-table-title"><?=lang("pay.paymethod")?></th>' +
                '<th class="mc-table-title"><?=lang("xpj.deposit.single_minimum_payment")?></th>' +
                '<th class="mc-table-title"><?=lang("xpj.deposit.single_highest_payment")?></th>' +
                '<th class="mc-table-title"></th>' +
            '</tr>';
        <?php }?>
        accout_item_list.append(th_title);
    }

    function renderPaymentAccountContent(payment_account_list, accout_item_list, chosen_id, flag_type, payment_flag) {
        var choose_account_type_id = chosen_id;
        var account_type_num = choose_account_type_id.split("-");
        if (account_type_num.length==4) {
            var account_type_num_arr = account_type_num[3];
        }else{
            console.log(account_type_num);//如果account_type_num array的長度不等於4就return並印出account_type_num的長度
            return;
        }
        var choosen_accout_type_list = [];

        $.each(payment_account_list, function(key, payment_account){
            if(payment_account.second_category_flag == account_type_num_arr) {
                choosen_accout_type_list.push(payment_account);
            }
        });

        choosen_accout_type_list.map(function(each_payment_account) {
            <?php if($this->utils->is_mobile()){?>
            var url='/player_center/iframe_makeDeposit/' + payment_flag + '/' + each_payment_account.bankTypeId+ '/'+ each_payment_account.payment_account_link_num;
            var each_payment_account_item =
                '<tr>' +
                    '<td>' + each_payment_account.bankName + '</td>' +
                    '<td class="mc-table-title col-deposit-option">' +
                        '<a class="btn btn-primary" data-toggle="online-deposit" title="<?=lang("deposit_category_deposit")?>" href="'+url+'"><?=lang("deposit_category_deposit")?></a>' +
                    '</td>' +
                    '<td>' + each_payment_account.minDeposit_currency + ' - ' + each_payment_account.maxDeposit_currency + '</td>' +
                '</tr>';
            <?php } else {?>
            var url='/player_center2/deposit/' + flag_type + '/' + each_payment_account.payment_account_link_num;
            var each_payment_account_item =
                '<tr>' +
                    '<td>' + each_payment_account.bankName + '</td>' +
                    '<td>' + each_payment_account.minDeposit_currency + '</td>' +
                    '<td>' + each_payment_account.maxDeposit_currency + '</td>' +
                    '<td class="mc-table-title col-deposit-option">' +
                        '<a class="btn btn-primary" data-toggle="online-deposit" title="<?=lang("deposit_category_deposit")?>" href="'+url+'"><?=lang("deposit_category_deposit")?></a>' +
                    '</td>' +
                '</tr>';
            <?php }?>
            accout_item_list.append(each_payment_account_item);
        });
    }
});

var EMPTY_ACCOUNT_NAME_REDIRECT_URL = '<?=(!$this->utils->is_mobile()) ? '/player_center/dashboard/index#accountInformation' : '/player_center/profile'?>';
</script>
