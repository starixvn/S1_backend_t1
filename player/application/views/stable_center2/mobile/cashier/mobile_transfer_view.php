<div class="panel panel-primary panel-player-transfer">
    <div class="panel-body">
        <div id="platform-credit-summary" class="player-transfer-summary">
            <div class="main-summary">
                <div class="balance-entry total-balance">
                    <div class="platform"><?=lang('cashier.05')?></div>
                    <div class="credit"></div>
                </div>
                <div class="balance-entry main-wallet-balance">
                    <div class="platform"><?=lang('cashier.02')?></div>
                    <div class="credit"></div>
                </div>
<!--
                <div class="balance-entry pending-withdraw-balance">
                    <div class="platform"><?=lang('cashier.pendingBalance')?></div>
                    <div class="credit"></div>
                </div>
                 <div class="main-actions">
                    <a href="javascript: void(0);" class="btn btn-default btn-transfer-all-to-main-wallet"><?=lang('Transfer Back All')?></a>
                    <a href="javascript: void(0);" class="btn btn-default btn-refresh-balance"><?=lang('lang.refreshbalance')?></a>
                </div>
                <div class="main-links">
                    <a href="<?=site_url('player_center2/deposit');?>" class="btn btn-default btn-deposit"><?=lang('Deposit')?></a>
                    <a href="<?=site_url('iframe_module/withdraw')?>" class="btn btn-default btn-withdraw"><?=lang('Withdrawal')?></a>
                </div>
-->
            </div>
            <div class="sup-summary-all">
                <div class="loading-container text-center">
                    <img class="loading" src="/<?=$this->utils->getPlayerCenterTemplate(FALSE)?>/images/loading.gif">
                </div>
            </div>
        </div>
    </div>
</div>
<!--
<div class="modal fade" tabindex="-1" data-backdrop="static" role="dialog" aria-hidden="true" id="transfer_adjust_amount">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo lang('cashier.enterAmount'); ?></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="transfer_subwalletid" name="transfer_subwalletid" value="">
                <input type="number" class="form-control" id="transfer_amount" name="transfer_amount" onkeyup="validate_amt_keyup(this)">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn form-control submit-btn"><?php echo lang('Transfer'); ?></button>
            </div>
        </div>
    </div>
</div>
-->
<script type="text/javascript">
    function refreshBalance_updateDisplay(wallet_info) {
        var custom_sub_wallet_id = <?= $custom_sub_wallet_id ?>;

        if(!wallet_info){
            return;
        }

        if (wallet_info['total_balance']) {
            var tot_balance = wallet_info['total_balance']['balance'];
            $('.player-transfer-summary .main-summary .total-balance .credit').html(_export_sbe_t1t.utils.displayCurrency(tot_balance));

        }
        $('.player-transfer-summary .main-summary .main-wallet-balance .credit').html(_export_sbe_t1t.utils.displayCurrency(wallet_info['main_wallet']['balance']));
        // $('.player-transfer-summary .main-summary .pending-withdraw-balance .credit').html(_export_sbe_t1t.utils.displayCurrency(wallet_info['main_wallet']['frozen']));

        var html = '';

        subwallets = wallet_info['sub_wallets'];
        $.each(subwallets, function(index, subwallet){
            if (subwallet['sub_wallet_id'] != custom_sub_wallet_id) {
                return;
            }
            html += (
                  '<div class="sup-summary" data-wallet-id="%id">'
                + '  <div class="subwallet-info">'
                + '    <div class="platform">%wallet</div>'
                + '    <div class="credit">%credit</div>'
                + '  </div>'
                + '  <div class="subwallet-actions">'
                + '    <a href="javascript: void(0);" class="btn btn-default btn-transfer-all-to-sub-wallet" data-wallet-id="%id"><?=lang('Mobile.Transfer.All')?></a>'
                + '  </div>'
                + '</div>'
                ).replace(/%id/g, subwallet['sub_wallet_id'])
                .replace('%wallet', subwallet['sub_wallet'])
                .replace('%credit', _export_sbe_t1t.utils.displayCurrency(subwallet['balance']))
            ;
        });

        $('.player-transfer-summary .sup-summary-all').html(html);
    }

    function validate_amt_keyup(e) {
        var v = $(e).val(),
            error = false,
            transfer_amt = $('#transfer_amount'),
            step = "0.01",
            decimal = step.split('.')[1],
            v_decimal = v.split('.')[1];

        try {
            parseFloat(v);
        } catch (err) {
            transfer_amt.val("");
        }

        if (transfer_amt.val() && typeof v_decimal != 'undefined' && v_decimal.length >= decimal.length) {
            transfer_amt.val(parseFloat(v).toFixed(decimal.length));
        }

        if (!v) {
            transfer_amt.val("");
        }
    }

    $(document).ready(function(){
        $('.quickButtonBar').remove();
        setTimeout(function() {
            $('.t1t-widget').hide();
        }, 200);

        $('.btn-refresh-balance').on('click', function(){
            $('.btn-refresh-balance').addClass('disabled').attr('disabled', 'disabled').prop('disabled', true);

            Loader.show();

            _export_sbe_t1t.player_wallet.refreshPlayerBalance(function(){
                $('.btn-refresh-balance').removeClass('disabled').removeAttr('disabled').prop('disabled', false);

                Loader.hide();
            });
        });

        $(document).on('click', '.btn-transfer-all-to-main-wallet', function(){
            Loader.show();

            _export_sbe_t1t.player_wallet.transferAllBalance(_export_sbe_t1t.variables.main_wallet_id, function(){
                Loader.hide();
            });
        });

        $(document).on('click', '.btn-transfer-all-to-sub-wallet', function(){
            var subWallet_id = $(this).data('wallet-id');

            Loader.show();

            _export_sbe_t1t.player_wallet.transferAllBalance(subWallet_id, function(){
                Loader.hide();
            });
        });

        $(document).on('click', '.btn-transfer-balance-to-sub-wallet', function(){
            var subWallet_id = $(this).data('wallet-id');

            $('#transfer_subwalletid').val('').val(subWallet_id);
            $('#transfer_amount').val(''); //clean

            $('#transfer_adjust_amount').modal('show');
        });

        $(document).on('click', '#transfer_adjust_amount .submit-btn', function(){
            var subWallet_id = $('#transfer_subwalletid').val();
            var amount = $('#transfer_amount').val();

            Loader.show();

            _export_sbe_t1t.player_wallet.transferBalance(_export_sbe_t1t.variables.main_wallet_id, subWallet_id, amount, function(){
                Loader.hide();

                $('#transfer_adjust_amount').modal('hide');
            });
        });

        _export_sbe_t1t.on('updated.t1t.player_wallet', function(e, wallet_info){
            refreshBalance_updateDisplay(wallet_info);
        });

    });

</script>