<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> <?=lang('Default Collection Account');?></h4>
        <div class="pull-right">
            <input type="button" id="editSettingBtn" value="<?=lang('player.editset')?>" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary btn-xs' : 'btn-info btn-md'?>" onclick="showSetting('show')">
        </div>

        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="col-md-12" id="previewPaymentAcctSettingSec">
            <label>
                <strong><?=lang('Payment Account Types')?>:</strong>
            </label>
            <ul>
                <?php if (@$payment_account_types["1"]["enabled"]) : ?>
                    <li>
                        <label><?=lang("pay.manual_online_payment")?></label>
                    </li>
                <?php endif; ?>
                <?php if (@$payment_account_types["2"]["enabled"]) : ?>
                    <li>
                        <label><?=lang("pay.auto_online_payment")?></label>
                    </li>
                <?php endif; ?>
                <?php if (@$payment_account_types["3"]["enabled"]) : ?>
                    <li>
                        <label><?=lang("pay.local_bank_offline")?></label>
                    </li>
                <?php endif; ?>
            </ul>
            <label>
                <strong><?=lang('Payment List')?>:</strong>
            </label>
            <?php if(!isset($payment_account_list) || empty($payment_account_list) || $special_payment_list[0] == '0') : ?>
                <ul>
                    <li>
                        <?=lang("No 3rd party payment configured.");?>
                    </li>
                </ul>
            <?php else : ?>
            <ul>
                <?php foreach($special_payment_list as $payment_account_id) : ?>
                    <?php @$payment_account = $payment_account_list[$payment_account_id];?>
                    <?php if(isset($payment_account_list[$payment_account_id])): ?>
                        <li>
                            <?php if ($payment_account->payment_account_name == lang($payment_account->payment_type)) : ?>
                                <?=$payment_account_id?> - <?=isset($payment_account->payment_account_name) ? $payment_account->payment_account_name : ''?>
                            <?php else : ?>
                                <?=$payment_account_id?> - <?=$payment_account->payment_account_name?> - <?=lang($payment_account->payment_type)?>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <?php # This list is not displayed if the two configs are the same
            if ($special_payment_list !== $special_payment_list_mobile) : ?>
                <label>
                    <strong><?=lang('Payment List (Mobile)')?>:</strong>
                </label>
                <?php if(empty($payment_account_list) || $special_payment_list[0] == '0') : ?>
                    <ul>
                        <li>
                            <?=lang("No 3rd party payment configured.");?>
                        </li>
                    </ul>
                <?php else : ?>
                    <ul>
                    <?php foreach($special_payment_list_mobile as $payment_account_id) : ?>
                        <?php @$payment_account = $payment_account_list[$payment_account_id];?>
                        <?php if(isset($payment_account_list[$payment_account_id])): ?>
                            <li>
                                <?php if ($payment_account->payment_account_name == lang($payment_account->payment_type)) : ?>
                                    <?=$payment_account_id?> - <?=isset($payment_account->payment_account_name) ? $payment_account->payment_account_name : ''?>
                                <?php else : ?>
                                    <?=$payment_account_id?> - <?=$payment_account->payment_account_name?> - <?=lang($payment_account->payment_type)?>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="col-md-12" id="paymentAcctSettingSec">
            <form action="<?php echo site_url('payment_management/savePaymentAccountSetting/');?>" method="post" role="form">
                <div class="row">
                    <br/>
                    <b><?=lang('Payment Account Types')?></b>
                    <ul>
                        <li>
                            <input id="paymentAccountTypeSelect_1" type="checkbox" name="selectedPaymentAccountType[]" value="1"
                                <?php echo @$payment_account_types["1"]["enabled"] ? "checked" : ""; ?>/>
                            <label for="paymentAccountTypeSelect_1"><?=lang("pay.manual_online_payment")?></label>
                        </li>
                        <li>
                            <input id="paymentAccountTypeSelect_2" type="checkbox" name="selectedPaymentAccountType[]" value="2"
                                <?php echo @$payment_account_types["2"]["enabled"] ? "checked" : ""; ?>/>
                            <label for="paymentAccountTypeSelect_2"><?=lang("pay.auto_online_payment")?></label>
                        </li>
                        <li>
                            <input id="paymentAccountTypeSelect_3" type="checkbox" name="selectedPaymentAccountType[]" value="3"
                                <?php echo @$payment_account_types["3"]["enabled"] ? "checked" : ""; ?>/>
                            <label for="paymentAccountTypeSelect_3"><?=lang("pay.local_bank_offline")?></label>
                        </li>
                    </ul>
                </div>
                <div class="row">
                    <br/>
                    <strong><?=lang('Payment List')?></strong>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="col col-1"><?=lang('ID')?>
                                <th class="col col-2"><?=lang('pay.payment_order');?></th>
                                <th class="col col-2"><?=lang('pay.payment_account_flag')?></th>
                                <th class="col col-2"><?=lang('pay.payment_name')?></th>
                                <th class="col col-3"><?=lang('pay.payment_account_name')?></th>
                                <th class="col col-1"><?=lang('PC Browser')?></th>
                                <th class="col col-1"><?=lang('Mobile client')?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(isset($payment_account_list) && is_array($payment_account_list)): ?>
                        <?php foreach($payment_account_list as $payment_account_id => $payment_account) : ?>
                        <tr>
                            <td><?=$payment_account_id?></td>
                            <td><?=$payment_account->payment_order?></td>
                            <td><?=$payment_account->flag_name?></td>
                            <td><?=lang($payment_account->payment_type)?></td>
                            <td><?=$payment_account->payment_account_name?></td>
                            <td>
                                <input id="thirdparty_payment_list_<?=htmlspecialchars($payment_account_id)?>"
                                       type="checkbox" name="special_payment_list[]"
                                       value="<?=htmlspecialchars($payment_account_id)?>"
                                    <?php echo in_array($payment_account_id, $special_payment_list) ? "checked" : ""; ?>
                                />
                            </td>
                            <td>
                                <input id="thirdparty_payment_list_mobile_<?=htmlspecialchars($payment_account_id)?>"
                                       type="checkbox" name="special_payment_list_mobile[]"
                                       value="<?=htmlspecialchars($payment_account_id)?>"
                                    <?php echo in_array($payment_account_id, $special_payment_list_mobile) ? "checked" : ""; ?>
                                />
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif;?>
                        </tbody>
                    </table>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-10 col-md-offset-1">
                    <br/>
                    <a href="<?php echo site_url()?>payment_management/defaultCollectionAccount" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>"><?=lang('pay.bt.cancel')?></a>
                    <input type="submit" value="<?=lang('player.saveset')?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>">
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#paymentAcctSettingSec').hide();
    function showSetting(type){
        if(type == 'show'){
            $('#previewPaymentAcctSettingSec').hide();
            $('#paymentAcctSettingSec').show();
            $('#editSettingBtn').hide();
        }
    }

    $(document).ready(function(){
        $('#collapseSubmenu').addClass('in');
        $('#view_payment_settings').addClass('active');
        $('#defaultCollectionAccount').addClass('active');

        $('#paymentAcctSettingSec table').DataTable({
            "columnDefs": [
                { "sSortDataType": "dom-checkbox", targets: [5, 6] }
            ],
            dom: 'rt',
            "pageLength": 1000, /* Don't ever change that */
            "order": [ 1, 'asc' ]
        });
    });
</script>
