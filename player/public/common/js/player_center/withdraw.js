// Load bank info into display
function loadPlayerWithdrawalBank(bank) {
    if(bank.isCrypto) {
        bank.accName = '';
        bank.province = '';
        bank.city = '';
        bank.address = '';
        bank.branch = '';
        bank.mobileNum = '';
    }

    var data = [
        // display element, form field element, data
        [null, $('#activeBankTypeIdField'), bank.bankTypeId],
        [null, $('#activeBankDetailsIdField'), bank.bankDetailsId],
        [$('#activeAccNum'), $('#activeAccNumField'), bank.accNum],
        [$('#activeBankName'), null, '&nbsp;'+bank.bankName],
        [$('#activeAccName'), $('#activeAccNameField'), bank.accName],
        [$('#activeProvince'), $('#activeProvinceField'), bank.province],
        [$('#activeCity'), $('#activeCityField'), bank.city],
        [$('#activeBankAddress'), $('#activeBankAddressField'), bank.address],
        [$('#activeBranch'), $('#activeBranchField'), bank.branch],
        [$('#activeMobileNum'), $('#activeMobileNumField'), bank.mobileNum],
    ];

    // Load new data
    for (var i = 0, len = data.length; i < len; i++) {
        var d = data[i];
        if(d[2]) {
            d[0] && d[0].html(d[2]);
            d[0] && d[0].parent().removeClass('hide');
            d[1] && d[1].val(d[2]);
        } else {
            d[0] && d[0].html('');
            d[0] && d[0].parent().addClass('hide');
            d[1] && d[1].val('');
        }
    }

    // Remove all bank icon classes (OGP-2513)
    $('#activeBankName').removeClass(function(index, name) {
        return (name.match(/b-icon|b-icon-custom|bank_[0-9]+/g) || []).join(' ');
    });
    // Remove custom bank icon image
    $('#activeBankName').find('img').remove();

    // Add custom bank icon image (if present) or bank icon class back
    if (bank.icon_url) {
        $('#activeBankName').addClass('b-icon-custom').prepend($('<img />').attr('src', bank.icon_url));
    }
    else {
        // Remove previous bank icon class
        $('.dispBankInfo').removeClass('hide');

        // Bank icon and name
        $('#activeBankName').addClass('b-icon').addClass('bank_' + bank.bankTypeId);
    }

    // Trigger re-evaluation of submit button disable status
    $('#amount').trigger('change');
}

function setupSelectPlayerWithdrawalBank(bank_data){
    var load_bank_data;
    if(bank_data === undefined){
        var $chosenBank = $('#currentBankList li.active');
        // Hack (append to last event.) - for waiting other jquery events.
        $(function(){
            // Check the player withdrawal bank is empty.
            if($chosenBank.length <= 0){
                MessageBox.info(lang('cashier.withdrawal.force_setup_player_withdrawal_bank_hint'), lang('cashier.withdrawal.player_center_withdrawal_bank_messagebox_title_info'), function(){
                    document.location.href="/player_center2/bank_account#bank_account_withdrawal";
                    // $('#add-bank-acc').data('backdrop', 'static').data('keyboard', false);
                    // $('#add-bank-acc .close-add-bank-account').hide();
                    // $('#add-bank-acc .close').hide();
                    // $('.add-bank-account[data-bank-type="withdrawal"]').trigger('click');
                }, [
                    {
                        'attr': {
                            'class': 'btn btn-primary'
                        },
                        'text': lang('pay.reg')
                    }
                ]);
            }
        });

        if($chosenBank.length <= 0){
            return;
        }

        load_bank_data = {
            'bankTypeId' : $chosenBank.data('bank-type-id'),
            'bankDetailsId' : $chosenBank.data('id'),
            'bankName' : $('span', $chosenBank).html(),
            'bankCode' : $chosenBank.data('bank-code'),
            'isCrypto' : $chosenBank.data('is-crypto'),
            'branch' : $chosenBank.data('branch'),
            'province' : $chosenBank.data('province'),
            'city' : $chosenBank.data('city'),
            'accNum' : $chosenBank.data('acc-num'),
            'accName' : $chosenBank.data('acc-name'),
            'mobileNum' : $chosenBank.data('mobile-num')
        };
    }else{
        load_bank_data = {
            'bankTypeId' : bank_data['bankTypeId'],
            'bankDetailsId' : bank_data['bankDetailsId'],
            'bankName' : bank_data['bankName'],
            'isCrypto' : bank_data['isCrypto'],
            'branch' : bank_data['branch'],
            'province' : bank_data['province'],
            'city' : bank_data['city'],
            'accNum' : bank_data['bankAccountNumber'],
            'accName' : bank_data['bankAccountFullName'],
            'mobileNum' : bank_data['phone']
        };
    }
    // Get chosen bank info from data fields
    loadPlayerWithdrawalBank(load_bank_data);

    $('#player-withdrawal-banks').modal('hide');
}

// Custom javascripts used by withdraw page
$(function(){
	// Simulates the click on first bank upon page load
    $('#currentBankList li').each(function(){
        if($(this).data('default')){
            $('a', $(this)).trigger('click');
        }
    });

    if($('#currentBankList li.active').length <= 0){
        $('#currentBankList li:first a').trigger('click');
    }

    setupSelectPlayerWithdrawalBank();

	// Controls bank list expand and collapse
	$('.show-btn').click(function(){
		$('#allBankList').css( { height: 'auto' } );
		$('.show-btn').hide();
		$('.hide-btn').show();
	});
	$('.hide-btn').click(function(){
		$('#allBankList').animate( { height: $('#allBankList').data('height')+'px' }, { duration:300 });
		$('.show-btn').show();
		$('.hide-btn').hide();
	});

    // Selected withdrawal bank account
	$('#saveChosenBank').click(function(){
		setupSelectPlayerWithdrawalBank();
	});

	// Submit form
	$('#submitBtn').click(function(){
        $('#submitBtn').addClass('disabled').attr('disabled', 'disabled').prop('disabled', true);
        $("form[name='withdrawForm']").submit();
	});

    // Form validation
    var form = $('#fm-withdrawal form');
    $('[required]', form).closest('.form-group').find('.control-label').each(function(){
        $(this).append($('<em style="color:red">*</em>'));
    });

	// Disable submit btn when form is submitting
    var submit_timer = null;
	form.on('submit', function() {
        form.validator('validate', function(e, validator){
            if(!validator.isIncomplete() && !validator.hasErrors()){
                Loader.show();
                $('#submitBtn').addClass('disabled');
                var inputAmt = $('input[name="amount"]').val();
                if (_withdraw.checkReachAutomaticTransferConditions(inputAmt)) {
                    _withdraw.autoTransferAll();
                }

                if(submit_timer !== null){
                    clearTimeout(submit_timer);
                }
                submit_timer = setTimeout(function(){
                    form.off('submit').submit();
                }, 300);
            }else{
                $('#submitBtn').removeClass('disabled').removeAttr('disabled').prop('disabled', false);
            }
        });

        return false;
	});

    form.validator({
        disable: false
    });
});

var _withdraw = function () {

    var wallInfo = {},
        isAutoTransferAll = false;

    autoTransferAll = function()
    {
        if (wallInfo) {
            $.ajax({
                type: "post",
                url: "/api/retrieveAllSubWalletBalanceToMainBallance",
                async: false,
            });
        }
    }

    checkReachAutomaticTransferConditions = function (withdrawAmt)
    {
        initCurrentWalletInfo();
        if (!_withdraw.isAutoTransferAll) { return false }
        if (checkMainWalletAmtCanWithdrawal(withdrawAmt)) { return false; }
        if (!checkTotalBalanceCanWithdrawal(withdrawAmt)) { return false; }
        return true;
    }

    checkMainWalletAmtCanWithdrawal = function (amt)
    {
        return parseFloat(amt) <= parseFloat(wallInfo.main_balance);
    }

    checkTotalBalanceCanWithdrawal = function(amt)
    {
        return parseFloat(amt) <= parseFloat(wallInfo.total_balance);
    }

    initCurrentWalletInfo = function ()
    {
        var _wallinfo = _export_sbe_t1t.variables.walletInfo;

        if (!_wallinfo) {return false}
        wallInfo.main_balance  = _wallinfo.main_wallet.balance
        wallInfo.total_balance = _wallinfo.total_balance.balance
    }

    return {
        isAutoTransferAll: isAutoTransferAll,
        autoTransferAll  : function () { return autoTransferAll(); },
        checkReachAutomaticTransferConditions : function (withdrawAmt) { return checkReachAutomaticTransferConditions(withdrawAmt); }
    };

}()