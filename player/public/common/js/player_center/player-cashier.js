var PlayerCashier = {
    setQuickTransfer: function (gameId){
        $(".right-side").addClass("active");
        $(".right-menu li").addClass("active");

        var mainWallet = 0;
        $("a[href$='#quickTransfer']").click();
        $('#transfer_amount').setCustomValidity('');
        $("#transfer_from option").removeClass("hide");
        $("#transfer_to option").removeClass("hide");
        $("#transfer_to").val(gameId).trigger('change');
        $("#transfer_from").val(mainWallet);

        return false;
    },

    manuallyRefreshBalance: function (){
        var self=this;
        self.refreshBalance(null, false);
    },

    refreshBalance: function (){
        $('a.refreshBalanceButton').attr('disabled', 'disabled').prop('disabled', true);
        $('a.quick-refresh').attr('disabled', 'disabled').prop('disabled', true);

        _export_sbe_t1t.player_wallet.refreshPlayerBalance();
    },

    showBalance: function(wallet_info) {
        var mainWalletTotal = wallet_info['main_wallet']['balance'];
        var pendingTotal = wallet_info['main_wallet']['frozen'];
        if(wallet_info['sub_wallets']) {
            for (var i in wallet_info.sub_wallets) {
                var api_id = wallet_info.sub_wallets[i]['sub_wallet_id'];
                var subwallet_amount = parseFloat(wallet_info.sub_wallets[i]['balance']);

                $("div.subwallet[data-typeid=" + api_id +"] .amount").html(_export_sbe_t1t.utils.displayCurrency(subwallet_amount));
                $('#quickTransfer').find('div.wallet span.wallet_' + api_id).html(_export_sbe_t1t.utils.displayCurrency(subwallet_amount));
            }
        }

        var totalBalance = mainWalletTotal + wallet_info['game_total'];
        $('.main-total').html(_export_sbe_t1t.utils.displayCurrency(parseFloat(mainWalletTotal)));
        //quick transfer
        $(document).find('div.wallet p.mw-yen').html(_export_sbe_t1t.utils.displayCurrency(parseFloat(mainWalletTotal)));
        $('.game-total').html(_export_sbe_t1t.utils.displayCurrency(parseFloat(wallet_info['game_total'])));
        $('.total-balance').html(_export_sbe_t1t.utils.displayCurrency(parseFloat(totalBalance)));
        $('.pending-total').html(_export_sbe_t1t.utils.displayCurrency(parseFloat(pendingTotal)));

        $('a.refreshBalanceButton').removeAttr('disabled').prop('disabled', false);
        $('a.quick-refresh').removeAttr('disabled').prop('disabled', false);
    }
};

$(document).ready( function () {
    _export_sbe_t1t.on('updated.t1t.player_wallet', function(e, wallet_info){
        PlayerCashier.showBalance(wallet_info);
    });

    $('#transferAllToMainBtn').on('click', function(){
        $('#transferAllToMainBtn').attr('disabled', 'disabled').prop('disabled', true);

        Loader.show();

        _export_sbe_t1t.player_wallet.transferAllBalance(_export_sbe_t1t.variables.main_wallet_id, function(){
            $('#transferAllToMainBtn').removeAttr('disabled').prop('disabled', false);

            Loader.hide();
        });
    });

    $(".wallet-ui-modal .transfer-fund-btn").click(function(e){
        var sub_wallet_id = $(this).data('sub-wallet-id');
        var wallet_info = _export_sbe_t1t.player_wallet.getWalletInfo();
        var subwallet_info = _export_sbe_t1t.player_wallet.getSubWalletInfo(sub_wallet_id);

        if(wallet_info.main_wallet.balance < subwallet_info.balance){
            _export_sbe_t1t.player_wallet.showTransferWalletModal(sub_wallet_id, _export_sbe_t1t.variables.main_wallet_id, subwallet_info.balance);
        }else{
            _export_sbe_t1t.player_wallet.showTransferWalletModal(_export_sbe_t1t.variables.main_wallet_id, sub_wallet_id, wallet_info.main_wallet.balance);
        }
        e.preventDefault();
    });
});