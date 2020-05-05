//general
var base_url = "/";
var imgloader = "/resources/images/ajax-loader.gif";

function GetXmlHttpObject() {
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        return new XMLHttpRequest();
    }
    if (window.ActiveXObject) {
        // code for IE6, IE5
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
}

$(document).ready(function() {
    PaymentManagementProcess.initialize();
});

//payment management module
var PaymentManagementProcess = {

    initialize : function() {
      //paypal setting
      $('#editDivPaymentMethod').hide();
      $('.editpaypalsetting-cancel-btn').click(function () {
            $('#editDivPaymentMethod').hide();
      });

      //validation
      $(".number_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105)) {
            e.preventDefault();
        }
    });

    $(".amount_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105)) {
            e.preventDefault();
        }
    });

    $(".letters_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 32, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if (e.ctrlKey === true || code < 65 || code > 90) {
            e.preventDefault();
        }
    });

    $(".letters_numbers_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 32, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                 // let it happen, don't do anything
                return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105) && (e.ctrlKey === true || code < 65 || code > 90)) {
            e.preventDefault();
        }
    });

    $(".usernames_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40) ||
             // Allow: underscores
            (e.shiftKey && code == 189)) {
                 // let it happen, don't do anything
                return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105) && (e.ctrlKey === true || code < 65 || code > 90)) {
            e.preventDefault();
        }
    });

    $(".emails_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
             // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40) ||
             //Allow: Shift+2
            (e.shiftKey && code == 50)) {
                 // let it happen, don't do anything
                return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105) && (e.ctrlKey === true || code < 65 || code > 90)) {
            e.preventDefault();
        }
    });

    //cashback
    $('#personal_info_history_panel_body').show();

    //tooltip
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    // for notification board
    $("#notificationDashboard-request-deposit").click(function () {
        $('.notificationDashboard').removeClass('notDboard-active');
        $(this).addClass('notDboard-active');
        $('#dwStatus').val('request');
    });
    $("#notificationDashboard-approved-deposit").click(function () {
        $('.notificationDashboard').removeClass('notDboard-active');
        $(this).addClass('notDboard-active');
        $('#dwStatus').val('approved');
    });
    $("#notificationDashboard-declined-deposit").click(function () {
        $('.notificationDashboard').removeClass('notDboard-active');
        $(this).addClass('notDboard-active');
        $('#dwStatus').val('declined');
    });

    //balInfoNewCurrentBal
    $('#balInfoNewCurrentBal').focus();

    // initialise the visibility check
    var is_visible = true;

    if(!is_visible){
        $('#moreFilter').hide();
        $('.moreFilter-btn').text('[Show Filter]');
    }else{
        $('#moreFilter').show();
        $('.moreFilter-btn').text('[Hide Filter]');
    }

    //show hide more filter
    $("#moreFilterBtn").click(function () {
        if(!is_visible){
            is_visible = true;
            $('#moreFilter').show();
            $('.moreFilter-btn').text('[Hide Filter]');
        }else{
            is_visible = false;
            $('#moreFilter').hide();
            $('.moreFilter-btn').text('[Show Filter]');
        }
    });

    //for ranking level add form
    var is_addRankingVisible = false;

    //for ranking level edit form
    var is_editRankingVisible = false;

    if(!is_addRankingVisible){
        $('#addRankingLevelSetting').hide();
    }else{
        $('#addRankingLevelSetting').show();
    }

    //show hide
    $(".addRankingLevelSettingBtn").click(function () {
        // console.log('Log'+is_addRankingVisible);
        if(!is_addRankingVisible){
            is_addRankingVisible = true;
            $('#addRankingLevelSetting').show();
            $('.addRankingLevelSettingBtn').removeClass('glyphicon glyphicon-plus-sign');
            $('.addRankingLevelSettingBtn').addClass('glyphicon glyphicon-minus-sign');
            $('#editRankingLevelSetting').hide();
        }else{
            is_addRankingVisible = false;
            $('#addRankingLevelSetting').hide();
            $('.addRankingLevelSettingBtn').removeClass('glyphicon glyphicon-minus-sign');
            $('.addRankingLevelSettingBtn').addClass('glyphicon glyphicon-plus-sign');
        }
    });

    if(!is_editRankingVisible){
        $('#editRankingLevelSetting').hide();
    }else{
        $('#editRankingLevelSetting').show();
    }

    //show hide
    $(".editRankingLevelSettingBtn").click(function () {
        is_editRankingVisible = true;
        $('.addRankingLevelSettingBtn').removeClass('glyphicon glyphicon-minus-sign');
        $('.addRankingLevelSettingBtn').addClass('glyphicon glyphicon-plus-sign');

        $('#editRankingLevelSetting').show();
        $('#addRankingLevelSetting').hide();
    });

    //decline reason
    $('#remarks-sec').hide();

    // sidebar.php
    $(document).ready(function() {
        var url = document.location.pathname;
        var res = url.split("/");

        for (i = 0; i < res.length; i++) {
            switch(res[i]){
                case 'deposit_list':
                case 'getSaleOrderReport':
                    $("a#deposit_list").addClass("active");
                    break;

                case 'viewWithdrawalRequestList':
                case 'viewWithdrawalApprovedList':
                case 'viewWithdrawalDeclinedList':
                case 'getWithdrawReport':
                    $("a#view_withdrawal").addClass("active");
                    break;

                case 'newDeposit':
                    $("a#new_deposit").addClass("active");
                    break;

                case 'newWithdrawal':
                    $("a#new_withdrawal").addClass("active");
                    break;

                case 'viewDepositList':
                case 'viewApprovedList':
                case 'viewDeclinedList':
                    $("a#view_otc_deposit").addClass("active");
                    break;

                case 'viewCashcardDepositList':
                case 'viewCashcardApprovedList':
                case 'viewCashcardDeclinedList':
                    $("a#view_otc_management").addClass("active");
                    break;

                case 'viewThirdPartyDepositList':
                case 'viewThirdPartyDepositApprovedList':
                case 'viewThirdPartyDepositDeclinedList':
                    $("a#view_auto3rdparty_deposit").addClass("active");
                    break;

                case 'viewManualThirdPartyDepositRequestList':
                case 'viewManualThirdPartyDepositApprovedList':
                case 'viewManualThirdPartyDepositDeclinedList':
                    $("a#view_manual3rdparty_deposit").addClass("active");
                    break;

                case 'viewPromoBonusList':
                    $("a#view_bonus").addClass("active");
                    break;

                case 'viewPlayerBalanceAdjustmentForm':
                case 'viewPlayerBalance':
                case 'playerBalanceListSearch':
                case 'viewAdjustmentHistory':
                    $("a#view_player_balance").addClass("active");
                    break;

                case 'viewAffiliateProfit':
                    $("a#view_affiliate_profit").addClass("active");
                    break;

                case 'viewBankManagement':
                    $("a#view_bank_management").addClass("active");
                    break;

                case 'viewRankingSettings':
                    $("a#view_ranking_settings").addClass("active");
                    break;

                case 'viewMoneyTransfer':
                    $("a#view_money_transfer").addClass("active");
                    break;

                case 'viewPlayerCenterFinancialAccountSettings':
                case 'viewThirdPartyAccountManager':
                case 'previousBalanceSetting':
                case 'nonPromoWithdrawSetting':
                case 'view_payment_account':
                case 'bank3rdPaymentList':
                case 'minWithdrawSetting':
                    $("a#view_payment_settings").addClass("active");
                    break;

                case 'viewtransactionList':
                    $("a#view_transaction").addClass("active");
                    break;

                case 'adjust_balance':
                    $("a#view_player_balance").addClass("active");
                    break;

                case 'transfer_request':
                    $("a#transfer_request").addClass("active");
                    break;

                case 'exception_order_list':
                    $("a#exception_order_list").addClass("active");
                    break;

                case 'lockedDepositList':
                    $("a#lock_deposit").addClass("active");
                    break;

                case 'lockedWithdrawalList':
                    $("a#lock_withdrawal").addClass("active");
                    break;

                case 'batchDeposit':
                    $("a#batch_deposit").addClass("active");
                    break;

                case 'newInternalWithdrawal':
                    $("a#new_internal_withdrawal").addClass("active");
                    break;

                default:
                    break;
            }
        }
    });
    // end of sidebar.php

    //personal information in modal content
    $("#personal_info_panel_body").show();
    $("#hide_personal_info").click(function() {
        $("#personal_info_panel_body").slideToggle();
        $("#hide_personal_info_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    //player transaction in modal content
    $("#player_transac_panel_body").show();
    $("#hide_player_transac").click(function() {
        $("#player_transac_panel_body").slideToggle();
        $("#hide_player_transac_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    //player transaction in modal content
    $("#deposit_info_panel_body").show();
    $("#hide_deposit_info").click(function() {
        $("#deposit_info_panel_body").slideToggle();
        $("#hide_deposit_info_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    //player transaction in modal content
    $("#approved_deposit_transac_panel_body").show();
    $("#hide_approved_deposit_transac").click(function() {
        $("#approved_deposit_transac_panel_body").slideToggle();
        $("#hide_approved_deposit_transac_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    $("#approved_deposit_transac_pi_panel_body").show();
    $("#hide_approved_deposit_transac_pi").click(function() {
        $("#approved_deposit_transac_pi_panel_body").slideToggle();
        $("#hide_approved_deposit_transac_pi_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    $("#approved_withdrawal_transac_pi_panel_body").show();
    $("#hide_approved_withdrawal_transac_pi").click(function() {
        $("#approved_withdrawal_transac_pi_panel_body").slideToggle();
        $("#hide_approved_withdrawal_transac_pi_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    $("#declined_deposit_transac_pi_panel_body").show();
    $("#hide_declined_deposit_transac_pi").click(function() {
        $("#declined_deposit_transac_pi_panel_body").slideToggle();
        $("#hide_declined_deposit_transac_pi_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    //player transaction in modal content
    $("#player_approved_deposit_transac_panel_body").show();
    $("#hide_approved_deposit_player_transac").click(function() {
        $("#player_approved_deposit_transac_panel_body").slideToggle();
        $("#hide_approved_depositplayer_transac_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    //player transaction in modal content
    $("#declined_deposit_info_panel_body").show();
    $("#hide_player_transac_history").click(function() {
        $("#declined_deposit_info_panel_body").slideToggle();
        $("#hide_player_transac_history_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    //player transaction in modal content
    $("#player_declined_deposit_transac_panel_body").show();
    $("#hide_declined_deposit_player_transac").click(function() {
        $("#player_declined_deposit_transac_panel_body").slideToggle();
        $("#hide_declined_depositplayer_transac_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    //player transaction history
    $("#playerhistory_panel_body").show();
    $("#hide_playerhistory").click(function() {
        $("#playerhistory_panel_body").slideToggle();
        $("#hide_playerhistory_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    //for player info
    $("#player_transac_history_panel_body").show();
    $("#hide_personal_info_history").click(function() {
        $("#personal_info_history_panel_body").slideToggle();
        $("#hide_personal_info_history_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    //for bonus info
    $(".bonus_info_panel_body").show();
    $(".hide_bonus_info").click(function() {
        $(".bonus_info_panel_body").slideToggle();
        $(".hide_bonus_info_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    //for affiliate transaction history
    $(".affiliate_transac_panel_body").show();
    $("#hide_affiliate_transac").click(function() {
        $("#affiliate_transac_panel_body").slideToggle();
        $("#hide_affiliate_transac_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
    });

    $("#hide_main").click(function() {
        $("#main_panel_body").slideToggle();
        $("#hide_main_up", this).toggleClass("glyphicon glyphicon-chevron-up glyphicon glyphicon-chevron-down");
        return false;//prevent scroll up for iframe
    });

    //for payment method edit form
    var is_editDivPaymentMethodVisible = false;

    //for payment method edit form
    var is_addPaymentMethodVisible = false;
    },

    getDepositRequest : function(requestId,paymentMethodId) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';
        $('#playerRequestDetails').html(html);

        $.ajax({
            'url' : base_url +'payment_management/reviewDepositRequest/'+requestId+'/'+paymentMethodId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                html  = '';

                $('#playerRequestDetails').html(html);

                //personal info
                $('.playerId').val(data[0].playerId);
                $('.playerName').val(data[0].firstName+' '+data[0].lastName);
                $('.userName').val(data[0].playerName);
                $('.completeName').val(data[0].firstName+' '+data[0].lastName);
                $('.memberSince').val(data[0].createdOn);
                $('.walletAccountIdVal').val(data[0].walletAccountId);
                $('.depositCnt').val(data[0].depositCnt.dwCount);

                //deposit details
                $('.dateDeposited').val(data[0].dwDateTime);
                $('.playerLevel').val(data[0].groupName+' '+data[0].vipLevel);
                $('.depositMethod').val(data[0].paymentMethodName);
                $('.depositedAmount').val(data[0].amount);

                $('#amountReceived').val(Number(data[0].amount));
                var compensationFee = $('#compensationPercentage').val()/100;
                $('#compensationFee').val($('#amountReceived').val() * compensationFee);

                $('#finalPlayerAmt').val(data[0].amount);
                $('.ipLoc').val(data[0].dwIp+' - '+data[0].dwLocation);


                $('.promoName').val(data[0].promoName);
                $('.playerDepositPromoId').val(data[0].playerPromoId);
                $('#requestPlayerPromoBonusAmount').val(data[0].playerPromoBonusAmount);
                if(data[0].depositSlipName && data[0].depositSlipName!=''){
                    $('#playerDepositSlip').html('<img class="img-thumbnail" src="../../resources/depositslip/' + data[0].depositSlipName + '"/>');
                }
                $('.playerBonusInfoPanel').hide();
                //show/hide bonus details
                if(data[0].promoName != null){
                    $('.bonusInfoPanel').show();
                    if(data[0]['playerActivePromo'].length > 0){
                        $('.playerBonusInfoPanel').show();
                        for (var i = 0; i < data[0]['playerActivePromo'].length; i++) {
                            html  = '';
                            html += '<tr>';
                            html += '<td>'+data[0]['playerActivePromo'][i].promoName+'</td>';
                            html += '<td>'+data[0]['playerActivePromo'][i].promoCode+'</td>';
                            html += '<td>'+data[0]['playerActivePromo'][i].bonusAmount+'</td>';
                            html += '<td>'+data[0]['playerActivePromo'][i].dateJoined+'</td>';
                            html += '</tr>';
                            $('.playerBonusTable').append(html);
                        }
                    }
                }else{
                    $('.bonusInfoPanel').hide();
                }

                //payment method details
                paymentMethodId = data[0].paymentMethodId;
                if(paymentMethodId == 1){
                    $('.otcPaymentMethodSection').show();
                    $('.paypalPaymentMethodSection').hide();

                    $('.otcBankName').val(data[0].bankName);
                    $('.otcAccountName').val(data[0].bankAccountFullName);
                    $('.otcAccountNo').val(data[0].bankAccountNumber);
                    $('.otcTransacTime').val(data[0].dwDateTime);
                    $('.otcReferenceNo').val(data[0].transacRefCode);

                    $('.depotcBankName').val(data[0].depositedToBankName);
                    $('.depotcAccountNo').val(data[0].depositedToAcctNo);
                    $('.depotcBranchName').val(data[0].depositedToBranchName);
                    $('.depotcAccountName').val(data[0].depositedToAcctName);
                    $('.depLocBankType').val(data[0].localBankType);
                } else if(paymentMethodId == 2){
                    $('.otcPaymentMethodSection').hide();
                    $('.paypalPaymentMethodSection').show();

                    //paypal details
                    $('.paypalAccountName').val(data[0]['paymentmethoddetails'][0].firstName+' '+data[0]['paymentmethoddetails'][0].lastName);
                    $('.paypalEmail').val(data[0]['paymentmethoddetails'][0].email);
                    $('.paypalTransactionId').val(data[0]['paymentmethoddetails'][0].transactionId);
                    $('.paypalSecureMerchantAccountId').val(data[0]['paymentmethoddetails'][0].secureMerchantAccountId);
                    $('.paypalTransactionDateTime').val(data[0]['paymentmethoddetails'][0].transactionDatetime);
                    $('.paypalTransactionType').val(data[0]['paymentmethoddetails'][0].transactionType);
                    $('.paypalTransactionStatus').val(data[0]['paymentmethoddetails'][0].transactionStatus);
                }
            }
        },'json');
        return false;
    },

    getAutoThirdPartyDepositRequest : function(requestId,paymentMethodId) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';
        $('#playerRequestDetails').html(html);

        $.ajax({
            'url' : base_url +'payment_management/reviewDepositRequest/'+requestId+'/'+paymentMethodId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                html  = '';

                $('#playerRequestDetails').html(html);

                //personal info
                $('.playerId').val(data[0].playerId);
                $('.playerName').val(data[0].firstName+' '+data[0].lastName);
                $('.userName').val(data[0].playerName);
                $('.completeName').val(data[0].firstName+' '+data[0].lastName);
                $('.email').val(data[0].email);
                $('.memberSince').val(data[0].createdOn);
                $('.walletAccountIdVal').val(data[0].walletAccountId);
                $('.depositCnt').val(data[0].depositCnt.dwCount);

                //deposit details
                $('.dateDeposited').val(data[0].dwDateTime);
                $('.playerLevel').val(data[0].groupName+' '+data[0].vipLevel);
                $('.depositMethod').val(data[0].paymentMethodName);
                $('.depositedAmount').val(data[0].amount);
                $('.ipLoc').val(data[0].dwIp+' - '+data[0].dwLocation);

                $('.promoName').val(data[0].promoName);
                $('.playerDepositPromoId').val(data[0].playerDepositPromoId);
                $('#requestPlayerPromoBonusAmount').val(data[0].bonusAmount);
                //show/hide bonus details
                $('.playerBonusInfoPanel').hide();
                //show/hide bonus details
                if(data[0].promoName != null){
                    $('.bonusInfoPanel').show();

                    if(data[0]['playerActivePromo'].length > 0){
                        $('.playerBonusInfoPanel').show();
                        for (var i = 0; i < data[0]['playerActivePromo'].length; i++) {
                            html  = '';
                            html += '<tr>';
                            html += '<td>'+data[0]['playerActivePromo'][i].promoName+'</td>';
                            html += '<td>'+data[0]['playerActivePromo'][i].promoCode+'</td>';
                            html += '<td>'+data[0]['playerActivePromo'][i].bonusAmount+'</td>';
                            html += '<td>'+data[0]['playerActivePromo'][i].dateJoined+'</td>';
                            html += '<td>'+data[0]['playerActivePromo'][i].promoExpiration+'</td>';
                            html += '</tr>';
                            $('.playerBonusTable').append(html);
                        }
                    }
                }else{
                    $('.bonusInfoPanel').hide();
                }

                //payment method details
                paymentMethodId = data[0].paymentMethodId;
                if(paymentMethodId == 2){
                    $('.paypalPaymentMethodSection').show();
                    $('.netellerPaymentMethodSection').hide();

                    //paypal details
                    $('.paypalAccountName').val(data[0]['paymentmethoddetails'][0].firstName+' '+data[0]['paymentmethoddetails'][0].lastName);
                    $('.paypalEmail').val(data[0]['paymentmethoddetails'][0].email);
                    $('.paypalTransactionId').val(data[0]['paymentmethoddetails'][0].transactionId);
                    $('.paypalSecureMerchantAccountId').val(data[0]['paymentmethoddetails'][0].secureMerchantAccountId);
                    $('.paypalTransactionDateTime').val(data[0]['paymentmethoddetails'][0].transactionDatetime);
                    $('.paypalTransactionType').val(data[0]['paymentmethoddetails'][0].transactionType);
                    $('.paypalTransactionStatus').val(data[0]['paymentmethoddetails'][0].transactionStatus);
                }else if(paymentMethodId == 5){
                    $('.paypalPaymentMethodSection').hide();
                    $('.netellerPaymentMethodSection').show();

                    $('.netellerAccount').val(data[0]['paymentmethoddetails'][0].netAccount);
                    $('.netellerSecuredId').val(data[0]['paymentmethoddetails'][0].securedId);
                }
            }
        },'json');
        return false;
    },

    getManualThirdPartyDepositRequest : function(requestId,paymentMethodId) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';
        $('#playerRequestDetails').html(html);

        $.ajax({
            'url' : base_url +'payment_management/reviewManualThirdPartyDepositRequest/'+requestId+'/'+paymentMethodId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                html  = '';

                $('#playerRequestDetails').html(html);

                //personal info
                $('.playerId').val(data[0].playerId);
                $('.playerName').val(data[0].firstName+' '+data[0].lastName);
                $('.userName').val(data[0].playerName);
                $('.completeName').val(data[0].firstName+' '+data[0].lastName);
                $('.email').val(data[0].email);
                $('.memberSince').val(data[0].createdOn);
                $('.walletAccountIdVal').val(data[0].walletAccountId);
                $('.depositCnt').val(data[0].depositCnt.dwCount);

                //deposit details
                $('.dateDeposited').val(data[0].dwDateTime);
                $('.playerLevel').val(data[0].groupName+' '+data[0].vipLevel);
                $('.depositMethod').val(data[0].paymentMethodName);
                $('.depositedAmount').val(data[0].amount);
                $('.ipLoc').val(data[0].dwIp+' - '+data[0].dwLocation);
                if(data[0].depositSlipName && data[0].depositSlipName!=''){
                    $('#playerDepositSlip').html('<img class="img-thumbnail" src="../../resources/depositslip/' + data[0].depositSlipName + '"/>');
                }

                //promo details
                $('.promoName').val(data[0].promoName);
                $('.playerDepositPromoId').val(data[0].playerPromoId);
                $('#requestPlayerPromoBonusAmount').val(data[0].bonusAmount);
                $('.playerTotalBalanceAmount').val(data[0].playerTotalBalanceAmount+' '+data[0].promoCurrency);
                $('.currentBalAmount').val(data[0].currentBalAmount);
                $('.currentBalCurrency').val(data[0].currentBalCurrency);
                $('.playerBonusInfoPanel').hide();
                //show/hide bonus details
                if(data[0].promoName != null){
                    $('.bonusInfoPanel').show();
                    if(data[0]['playerActivePromo'].length > 0){
                        $('.playerBonusInfoPanel').show();
                        for (var i = 0; i < data[0]['playerActivePromo'].length; i++) {
                            html  = '';
                            html += '<tr>';
                            html += '<td>'+data[0]['playerActivePromo'][i].promoName+'</td>';
                            html += '<td>'+data[0]['playerActivePromo'][i].promoCode+'</td>';
                            html += '<td>'+data[0]['playerActivePromo'][i].bonusAmount+'</td>';
                            html += '<td>'+data[0]['playerActivePromo'][i].dateJoined+'</td>';
                            html += '</tr>';
                            $('.playerBonusTable').append(html);
                        }
                   }
                }else{
                    $('.bonusInfoPanel').hide();
                }

                $('.otcPaymentMethodSection').hide();
                $('.paypalPaymentMethodSection').show();

                //paypal details
                $('.mt_paymentMethodName').val(data[0].paymentMethodName);
                $('.mt_transacCode').val(data[0].transacRefCode);
                $('.mt_depositorName').val(data[0].depositorName);
                $('.mt_depositorAccount').val(data[0].depositorAccount);
            }
        },'json');
        return false;
    },

    getDepositApproved : function(walletAccountId) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        $('#playerApprovedDetails').html(html);

        $.ajax({
            'url' : base_url +'payment_management/reviewDepositApproved/'+walletAccountId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                html  = '';

                //bonus details
                if(data[0].promoName != null){
                    $('.bonusInfoPanel').show();
                }else{
                    $('.bonusInfoPanel').hide();
                }

                //clear previous transaction history
                $('.transacHistoryDetail').remove();

                //get transaction history
                for (var i = 0; i < data[0]['transacHistory'].length; i++) {
                    html  = '';
                    html += "<tr class='transacHistoryDetail'>";
                    html += '<td>'+data[0]['transacHistory'][i].transactionType+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].amount+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].currency+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].dwDateTime+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].paymentMethodName+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].dwStatus+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].processedByAdmin+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].processDatetime+'</td>';
                    html += '</tr>';
                    $('.transactionHistoryResult').append(html);
                };

                $('#playerApprovedDetails').html(html);
                $('#playerApprovedDetailsRepondBtn').hide();
                $('#playerApprovedDetailsCheckPlayer').hide();

                //personal info
                $('.playerId').val(data[0].playerId);
                $('.userName').val(data[0].playerName);
                $('.playerName').val(data[0].firstName+' '+data[0].lastName);
                $('.playerLevel').val(data[0].groupName+' '+data[0].vipLevel);
                $('.email').val(data[0].email);
                $('.memberSince').val(data[0].createdOn);
                $('.address').val(data[0].address);
                $('.city').val(data[0].city);
                $('.country').val(data[0].country);
                $('.birthday').val(data[0].birthdate);
                $('.gender').val(data[0].gender);
                $('.phone').val(data[0].phone);
                $('.cp').val(data[0].contactNumber);
                $('.walletAccountIdVal').val(data[0].walletAccountId);
                $('.ipLoc').val(data[0].dwIp+' - '+data[0].dwLocation);

                //depositDetails
                $('.dateDepositedApprovedDeposit').val(data[0].dwDateTime);
                $('.playerLevelApprovedDeposit').val(data[0].playerLevel);
                $('.depositMethodApprovedDeposit').val(data[0].paymentMethodName);
                $('.depositedAmountApprovedDeposit').val(data[0].amount);
                $('#depositMethodApprovedBy').val(data[0].processedByAdmin);
                $('#depositMethodDateApproved').val(data[0].processDatetime);
                $('.currentBalCurrency').val(data[0].currentBalCurrency);

                $('.promoName').val(data[0].promoName);
                $('#approvedPlayerPromoBonusAmount').val(data[0].bonusAmount);

                //payment method details
                paymentMethodId = data[0].paymentMethodId;
                if(paymentMethodId == 1){
                    $('.otcPaymentMethodSection').show();
                    $('.paypalPaymentMethodSection').hide();
                    $('.netellerPaymentMethodSection').hide();

                    $('.otcBankName').val(data[0].bankName);
                    $('.otcAccountName').val(data[0].bankAccountFullName);
                    $('.otcAccountNo').val(data[0].bankAccountNumber);
                    $('.otcTransacTime').val(data[0].dwDateTime);
                    $('.otcReferenceNo').val(data[0].transacRefCode);
                }
                else if(paymentMethodId == 2){
                    $('.otcPaymentMethodSection').hide();
                    $('.paypalPaymentMethodSection').show();
                    $('.netellerPaymentMethodSection').hide();

                    //paypal details
                    $('.paypalAccountName').val(data[0]['paymentmethoddetails'][0].firstName+' '+data[0]['paymentmethoddetails'][0].lastName);
                    $('.paypalEmail').val(data[0]['paymentmethoddetails'][0].email);
                    $('.paypalTransactionId').val(data[0]['paymentmethoddetails'][0].transactionId);
                    $('.paypalSecureMerchantAccountId').val(data[0]['paymentmethoddetails'][0].secureMerchantAccountId);
                    $('.paypalTransactionDateTime').val(data[0]['paymentmethoddetails'][0].transactionDatetime);
                    $('.paypalTransactionType').val(data[0]['paymentmethoddetails'][0].transactionType);
                    $('.paypalTransactionStatus').val(data[0]['paymentmethoddetails'][0].transactionStatus);
                }
                else if(paymentMethodId == 5){
                    $('.otcPaymentMethodSection').hide();
                    $('.paypalPaymentMethodSection').hide();
                    $('.netellerPaymentMethodSection').show();

                    $('.netellerAccount').val(data[0]['paymentmethoddetails'][0].netAccount);
                    $('.netellerSecuredId').val(data[0]['paymentmethoddetails'][0].securedId);
                }
            }
        },'json');
        return false;
    },

    getDepositApprovedLocalBank : function(walletAccountId) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        $('#playerApprovedDetails').html(html);

        $.ajax({
            'url' : base_url +'payment_management/reviewDepositApprovedLocalBank/'+walletAccountId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                html  = '';

                //bonus details
                if(data[0].promoName != null){
                    $('.bonusInfoPanel').show();
                }else{
                    $('.bonusInfoPanel').hide();
                }

                $('#playerApprovedDetails').html(html);
                $('#playerApprovedDetailsRepondBtn').hide();
                $('#playerApprovedDetailsCheckPlayer').hide();

                //personal info
                $('.playerId').val(data[0].playerId);
                $('.userName').val(data[0].playerName);
                $('.playerName').val(data[0].firstName+' '+data[0].lastName);
                $('.playerLevel').val(data[0].groupName+' '+data[0].vipLevel);
                $('.email').val(data[0].email);
                $('.memberSince').val(data[0].createdOn);
                $('.address').val(data[0].address);
                $('.city').val(data[0].city);
                $('.country').val(data[0].country);
                $('.birthday').val(data[0].birthdate);
                $('.gender').val(data[0].gender);
                $('.phone').val(data[0].phone);
                $('.cp').val(data[0].contactNumber);
                $('.walletAccountIdVal').val(data[0].walletAccountId);
                $('.ipLoc').val(data[0].dwIp+' - '+data[0].dwLocation);

                //depositDetails
                $('.dateDepositedApprovedDeposit').val(data[0].dwDateTime);
                $('.playerLevelApprovedDeposit').val(data[0].playerLevel);
                $('.depositMethodApprovedDeposit').val(data[0].paymentMethodName);
                $('.depositedAmountApprovedDeposit').val(data[0].amount);
                $('#depositMethodApprovedBy').val(data[0].processedByAdmin);
                $('#depositMethodDateApproved').val(data[0].processDatetime);
                $('.currentBalCurrency').val(data[0].currentBalCurrency);

                $('.promoName').val(data[0].promoName);
                $('#approvedPlayerPromoBonusAmount').val(data[0].bonusAmount);

                //payment method details
                paymentMethodId = data[0].paymentMethodId;
                if(paymentMethodId == 1){
                    $('.otcPaymentMethodSection').show();
                    $('.paypalPaymentMethodSection').hide();

                    $('.otcBankName').val(data[0].bankName);
                    $('.otcAccountName').val(data[0].bankAccountFullName);
                    $('.otcAccountNo').val(data[0].bankAccountNumber);
                    $('.otcTransacTime').val(data[0].dwDateTime);
                    $('.otcReferenceNo').val(data[0].transacRefCode);

                    $('.depotcBankName').val(data[0].depositedToBankName);
                    $('.depotcAccountNo').val(data[0].depositedToAcctNo);
                    $('.depotcBranchName').val(data[0].depositedToBranchName);
                    $('.depotcAccountName').val(data[0].depositedToAcctName);
                    $('.depLocBankType').val(data[0].localBankType);
                } else if(paymentMethodId == 2){
                    $('.otcPaymentMethodSection').hide();
                    $('.paypalPaymentMethodSection').show();

                    //paypal details
                    $('.paypalAccountName').val(data[0]['paymentmethoddetails'][0].firstName+' '+data[0]['paymentmethoddetails'][0].lastName);
                    $('.paypalEmail').val(data[0]['paymentmethoddetails'][0].email);
                    $('.paypalTransactionId').val(data[0]['paymentmethoddetails'][0].transactionId);
                    $('.paypalSecureMerchantAccountId').val(data[0]['paymentmethoddetails'][0].secureMerchantAccountId);
                    $('.paypalTransactionDateTime').val(data[0]['paymentmethoddetails'][0].transactionDatetime);
                    $('.paypalTransactionType').val(data[0]['paymentmethoddetails'][0].transactionType);
                    $('.paypalTransactionStatus').val(data[0]['paymentmethoddetails'][0].transactionStatus);
                }
            }
        },'json');
        return false;
    },

    getManualThirdPartyDepositApproved : function(walletAccountId) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';
        $('#playerApprovedDetails').html(html);

        $.ajax({
            'url' : base_url +'payment_management/reviewManualThirdPartyDepositApproved/'+walletAccountId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                html  = '';

                //bonus details
                if(data[0].promoName != null){
                    $('.bonusInfoPanel').show();
                }else{
                    $('.bonusInfoPanel').hide();
                }

                //clear previous transaction history
                $('.transacHistoryDetail').remove();

                //get transaction history
                for (var i = 0; i < data[0]['transacHistory'].length; i++) {
                    html  = '';
                    html += "<tr class='transacHistoryDetail'>";
                    html += '<td>'+data[0]['transacHistory'][i].transactionType+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].amount+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].currency+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].dwDateTime+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].paymentMethodName+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].dwStatus+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].processedByAdmin+'</td>';
                    html += '<td>'+data[0]['transacHistory'][i].processDatetime+'</td>';
                    html += '</tr>';
                    $('.transactionHistoryResult').append(html);
                };

                $('#playerApprovedDetails').html(html);
                $('#playerApprovedDetailsRepondBtn').hide();
                $('#playerApprovedDetailsCheckPlayer').hide();

                //personal info
                $('.playerId').val(data[0].playerId);
                $('.userName').val(data[0].playerName);
                $('.playerName').val(data[0].firstName+' '+data[0].lastName);
                $('.playerLevel').val(data[0].groupName+' '+data[0].vipLevel);
                $('.email').val(data[0].email);
                $('.memberSince').val(data[0].createdOn);
                $('.address').val(data[0].address);
                $('.city').val(data[0].city);
                $('.country').val(data[0].country);
                $('.birthday').val(data[0].birthdate);
                $('.gender').val(data[0].gender);
                $('.phone').val(data[0].phone);
                $('.cp').val(data[0].contactNumber);
                $('.walletAccountIdVal').val(data[0].walletAccountId);
                $('.ipLoc').val(data[0].dwIp+' - '+data[0].dwLocation);

                //depositDetails
                $('.dateDepositedApprovedDeposit').val(data[0].dwDateTime);
                $('.playerLevelApprovedDeposit').val(data[0].playerLevel);
                $('.depositMethodApprovedDeposit').val(data[0].paymentMethodName);
                $('.depositedAmountApprovedDeposit').val(data[0].amount);
                $('#depositMethodApprovedBy').val(data[0].processedByAdmin);
                $('#depositMethodDateApproved').val(data[0].processDatetime);
                $('.currentBalCurrency').val(data[0].currentBalCurrency);

                $('.promoName').val(data[0].promoName);
                $('#approvedPlayerPromoBonusAmount').val(data[0].bonusAmount);

                //3rd party account details
                $('.depositedAmount').val(data[0].amount);
                $('.dateDeposited').val(data[0].dwDateTime);
                $('.depositMethod').val(data[0].paymentMethodName);
                $('.mt_transacCode').val(data[0].transacRefCode);
                $('.mt_depositorName').val(data[0].depositorName);
                $('.mt_depositorAccount').val(data[0].depositorAccount);
            }
        },'json');
        return false;
    },

    getManualThirdPartyDepositDeclined : function(walletAccountId) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        $('#playerApprovedDetails').html(html);

        $.ajax({
            'url' : base_url +'payment_management/reviewManualThirdPartyDepositDeclined/'+walletAccountId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                html  = '';

                //bonus details
                if(data[0].promoName != null){
                  $('.bonusInfoPanel').show();
                }else{
                  $('.bonusInfoPanel').hide();
                }

                //clear previous transaction history
                $('.transacHistoryDetail').remove();

                $('#playerApprovedDetails').html(html);
                $('#playerApprovedDetailsRepondBtn').hide();
                $('#playerApprovedDetailsCheckPlayer').hide();

                //personal info
                $('.playerId').val(data[0].playerId);
                $('.userName').val(data[0].playerName);
                $('.playerName').val(data[0].firstName+' '+data[0].lastName);
                $('.playerLevel').val(data[0].groupName+' '+data[0].vipLevel);
                $('.email').val(data[0].email);
                $('.memberSince').val(data[0].createdOn);
                $('.address').val(data[0].address);
                $('.city').val(data[0].city);
                $('.country').val(data[0].country);
                $('.birthday').val(data[0].birthdate);
                $('.gender').val(data[0].gender);
                $('.phone').val(data[0].phone);
                $('.cp').val(data[0].contactNumber);
                $('.walletAccountIdVal').val(data[0].walletAccountId);
                $('.ipLoc').val(data[0].dwIp+' - '+data[0].dwLocation);

                //depositDetails
                $('.dateDepositedApprovedDeposit').val(data[0].dwDateTime);
                $('.playerLevelApprovedDeposit').val(data[0].playerLevel);
                $('.depositMethodApprovedDeposit').val(data[0].paymentMethodName);
                $('.depositedAmountApprovedDeposit').val(data[0].amount);
                $('#depositMethodApprovedBy').val(data[0].processedByAdmin);
                $('#depositMethodDateApproved').val(data[0].processDatetime);
                $('.currentBalCurrency').val(data[0].currentBalCurrency);

                $('.promoName').val(data[0].promoName);
                $('#declinedPlayerPromoBonusAmount').val(data[0].bonusAmount);
                $('.playerDepositPromoId').val(data[0].playerDepositPromoId);

                //3rd party account details
                $('.depositedAmount').val(data[0].amount);
                $('.dateDeposited').val(data[0].dwDateTime);
                $('.depositMethod').val(data[0].paymentMethodName);
                $('.mt_transacCode').val(data[0].transacRefCode);
                $('.mt_depositorName').val(data[0].depositorName);
                $('.mt_depositorAccount').val(data[0].depositorAccount);

                $('#depositMethodDeclinedBy').val(data[0].processedByAdmin);
                $('#depositMethodDateDeclined').val(data[0].processDatetime);
                $('#depositMethodReasonDeclined').val(data[0].notes);
            }
        },'json');
        return false;
    },

    getAutoThirdPartyDepositDeclined : function(walletAccountId) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        $('#playerApprovedDetails').html(html);

        $.ajax({
            'url' : base_url +'payment_management/reviewAuto3rdPartyDepositDeclined/'+walletAccountId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                html  = '';

                //bonus details
                if(data[0].promoName != null){
                  $('.bonusInfoPanel').show();
                }else{
                  $('.bonusInfoPanel').hide();
                }

                //clear previous transaction history
                $('.transacHistoryDetail').remove();

                $('#playerApprovedDetails').html(html);
                $('#playerApprovedDetailsRepondBtn').hide();
                $('#playerApprovedDetailsCheckPlayer').hide();

                //personal info
                $('.playerId').val(data[0].playerId);
                $('.userName').val(data[0].playerName);
                $('.playerName').val(data[0].firstName+' '+data[0].lastName);
                $('.playerLevel').val(data[0].groupName+' '+data[0].vipLevel);

                $('.email').val(data[0].email);
                $('.memberSince').val(data[0].createdOn);
                $('.address').val(data[0].address);
                $('.city').val(data[0].city);
                $('.country').val(data[0].country);
                $('.birthday').val(data[0].birthdate);
                $('.gender').val(data[0].gender);
                $('.phone').val(data[0].phone);
                $('.cp').val(data[0].contactNumber);
                $('.walletAccountIdVal').val(data[0].walletAccountId);
                $('.ipLoc').val(data[0].dwIp+' - '+data[0].dwLocation);

                //depositDetails
                $('.dateDeposited').val(data[0].dwDateTime);
                $('.depositMethod').val(data[0].paymentMethodName);
                $('.depositedAmount').val(data[0].amount);
                $('#depositMethodApprovedBy').val(data[0].processedByAdmin);
                $('#depositMethodDateApproved').val(data[0].processDatetime);
                $('.currentBalCurrency').val(data[0].currentBalCurrency);

                $('.promoName').val(data[0].promoName);
                $('#declinedPlayerPromoBonusAmount').val(data[0].bonusAmount);
                $('.playerDepositPromoId').val(data[0].playerDepositPromoId);

                //3rd party account details
                paymentMethodId = data[0].paymentMethodId;
                if(paymentMethodId == 2){
                    $('.paypalPaymentMethodSection').show();
                    $('.netellerPaymentMethodSection').hide();

                    //paypal details
                    $('.paypalAccountName').val(data[0]['paymentmethoddetails'][0].firstName+' '+data[0]['paymentmethoddetails'][0].lastName);
                    $('.paypalEmail').val(data[0]['paymentmethoddetails'][0].email);
                    $('.paypalTransactionId').val(data[0]['paymentmethoddetails'][0].transactionId);
                    $('.paypalSecureMerchantAccountId').val(data[0]['paymentmethoddetails'][0].secureMerchantAccountId);
                    $('.paypalTransactionDateTime').val(data[0]['paymentmethoddetails'][0].transactionDatetime);
                    $('.paypalTransactionType').val(data[0]['paymentmethoddetails'][0].transactionType);
                    $('.paypalTransactionStatus').val(data[0]['paymentmethoddetails'][0].transactionStatus);
                }else if(paymentMethodId == 5){
                    $('.paypalPaymentMethodSection').hide();
                    $('.netellerPaymentMethodSection').show();

                    $('.netellerAccount').val(data[0]['paymentmethoddetails'][0].netAccount);
                    $('.netellerSecuredId').val(data[0]['paymentmethoddetails'][0].securedId);
                }

                $('#depositMethodDeclinedBy').val(data[0].processedByAdmin);
                $('#depositMethodDateDeclined').val(data[0].processDatetime);
                $('#depositMethodReasonDeclined').val(data[0].notes);
            }
        },'json');
        return false;
    },

    getDepositDeclined : function(requestId) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        $('#playerDeclinedDetails').html(html);

        $.ajax({
            'url' : base_url +'payment_management/reviewDepositDeclined/'+requestId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                html  = '';
                $('#playerDeclinedDetails').html(html);

                //bonus details
                if(data[0].promoName != null){
                    $('.bonusInfoPanel').show();
                }else{
                    $('.bonusInfoPanel').hide();
                }

                //personal info
                $('.playerId').val(data[0].playerId);
                $('.userName').val(data[0].playerName);
                $('.playerName').val(data[0].firstName+' '+data[0].lastName);
                $('.playerLevel').val(data[0].groupName+' '+data[0].vipLevel);
                $('.email').val(data[0].email);
                $('.memberSince').val(data[0].createdOn);
                $('.address').val(data[0].address);
                $('.city').val(data[0].city);
                $('.country').val(data[0].country);
                $('.birthday').val(data[0].birthdate);
                $('.gender').val(data[0].gender);
                $('.phone').val(data[0].phone);
                $('.cp').val(data[0].contactNumber);
                $('.walletAccountIdVal').val(data[0].walletAccountId);
                $('.ipLoc').val(data[0].dwIp+' - '+data[0].dwLocation);

                //deposit details
                $('.dateDeposited').val(data[0].dwDateTime);
                $('.depositMethod').val(data[0].paymentMethodName);
                $('.depositedAmount').val(data[0].amount);
                $('.currentBalCurrency').val(data[0].currentBalCurrency);
                $('#depositMethodDeclinedBy').val(data[0].processedByAdmin);
                $('#depositMethodDateDeclined').val(data[0].processDatetime);
                $('#depositMethodReasonDeclined').val(data[0].notes);


                $('.promoName').val(data[0].promoName);
                $('.declinedPlayerPromoBonusAmount').val(data[0].bonusAmount);
                $('.playerDepositPromoId').val(data[0].playerDepositPromoId);

                //payment method details
                $('.otcPaymentMethodSection').show();
                $('.paypalPaymentMethodSection').hide();

                $('.otcBankName').val(data[0].bankName);
                $('.otcAccountName').val(data[0].bankAccountFullName);
                $('.otcAccountNo').val(data[0].bankAccountNumber);
                $('.otcTransacTime').val(data[0].dwDateTime);
                $('.otcReferenceNo').val(data[0].transacRefCode);

                $('.depotcBankName').val(data[0].depositedToBankName);
                $('.depotcAccountNo').val(data[0].depositedToAcctNo);
                $('.depotcBranchName').val(data[0].depositedToBranchName);
                $('.depotcAccountName').val(data[0].depositedToAcctName);
                $('.depLocBankType').val(data[0].localBankType);
            }
        },'json');
        return false;
    },

    respondToDepositRequest : function() {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

       $('#playerRequestDetails').html(html);

       var playerId = $('.playerId').val();
       var walletAccountIdVal = $('.walletAccountIdVal').val();
       var depositAmount = $('.depositedAmount').val();
       var playerDepositPromoId = $('.playerDepositPromoId').val();
       var bankDepositTransacFeeSettingVal = $('#bankDepositTransacFeeSettingVal').val();
       var bankDepositTransacFeeVal = $('#transactionFee').val();

       if (playerDepositPromoId == '') {
            playerDepositPromoId = null;
       }
        $.ajax({
            'url' : base_url +'payment_management/approveDepositRequest/'+walletAccountIdVal+'/'+depositAmount+'/'+playerId+'/'+playerDepositPromoId+'/'+bankDepositTransacFeeSettingVal+'/'+bankDepositTransacFeeVal+'/'+actualAmountReceived,
            'type' : 'GET',
            'success' : function(data){
                html  = '';
                html += '<p>';
                html += 'Deposit has been Approved!';
                html += '</p>';

                $('#playerRequestDetails').html(html);
                $('#repondBtn').hide();
                $('#transactionFeeSec').hide();
            }
        },'json');
        return false;
    },

    clearPlayerPromo : function(requestId) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        $('.clearPromoStatusMsg'+requestId).html(html);
        $.ajax({
            'url' : base_url +'payment_management/clearPlayerBonus/'+requestId,
            'type' : 'GET',
            'success' : function(data){
                html  = '';
                html += '<p>';
                html += 'Promo has been cleared!';
                html += '</p>';

                $('.clearPromoStatusMsg'+requestId).html(html);
                $('#clearPlayerBonusBtn'+requestId).hide();
            }
        },'json');
        return false;
    },

    clearPlayerBonus : function(requestId) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        $('.clearPromoStatusMsg').html(html);

        var playerDepositPromoId = $('.playerDepositPromoId').val();

        $.ajax({
            'url' : base_url +'payment_management/clearPlayerBonus/'+playerDepositPromoId,
            'type' : 'GET',
            'success' : function(data){
                html  = '';
                html += '<p>';
                html += 'Promo has been cleared!';
                html += '</p>';

                $('.clearPromoStatusMsg').html(html);
                $('#clearPlayerBonusBtn').hide();
            }
        },'json');
        return false;
    },

    depositDeclinedToApprove : function(requestId) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        //notification
        $('#playerRequestDetails').html(html);

        var playerId = $('.playerId').val();
        var walletAccountIdVal = $('.walletAccountIdVal').val();
        var depositAmount = $('.depositedAmount').val();
        var playerDepositPromoId = $('.playerDepositPromoId').val();

        $.ajax({
            'url' : base_url +'payment_management/approveDeclinedAlreadyRequest/'+walletAccountIdVal+'/'+depositAmount+'/'+playerId+'/'+playerDepositPromoId,
            'type' : 'GET',
            'success' : function(data){
                html  = '';
                html += '<p>';
                html += 'Deposit has been Approved!';
                html += '</p>';

               $('#playerDeclinedDetails').html(html);
               $('#playerDeclinedDetailsCheckPlayer').hide();
               $('#playerDeclinedDetailsRepondBtn').hide();
            }
        },'json');
        return false;
    },

    showRemarks : function(type) {
        $('#remarks-sec').show();
        $('#repondBtn').hide();

        if(type == 'approve'){
            $('#decline-button-sec').hide();
            $('#approve-button-sec').show();
        }else{
            $('#decline-button-sec').show();
            $('#approve-button-sec').hide();
        }
    },

    getRankingList : function(requestId) {
        $.ajax({
            'url' : base_url +'payment_management/getRankingList',
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                for (var i = 0; i < data.length; i++) {
                    html  = '';
                    html += '<option value="'+data[i].levelId+'" >'+data[i].levelGroup+'-'+data[i].levelName+'</option>';
                    $('#addRankingLevel').append(html);
                };
            }
        },'json');
        return false;
    },

    getRankingLevelSettingsDetail : function(requestId) {
        $.ajax({
            'url' : base_url +'payment_management/getRankingLevelSettingsDetail/'+requestId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                $('#editRankingLevelGroup').val(data[0].rankingLevelGroup);
                $('#editRankingLevel').val(data[0].rankingLevel);
                $('#editMinRequiredDeposit').val(data[0].minDepositRequirement);
                $('#editCurrency').val(data[0].currency);
                $('#editPointRequirement').val(data[0].pointRequirement);
                $('#editMaxDepositAmount').val(data[0].maxDepositAmount);
                $('#editMinDepositAmount').val(data[0].minDepositAmount);
                $('#editDailyMaxWithdrawal').val(data[0].dailyMaxWithdrawal);
                $('#editRankingLevelId').val(data[0].rankingLevelSettingId);
            }
        },'json');
        return false;
    },

    getPlayerBalanceDetails : function(playerId) {
        $('#personal_info_history_panel_body').show();
        $.ajax({
            'url' : base_url +'payment_management/getPlayerBalanceDetails/'+playerId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                $('#balInfoPlayerId').val(data[0].playerId);
                $('#balInfoPlayerAccountId').val(data[0].playerAccountId);
                $('#balInfoUserName').val(data[0].username);
                $('#balInfoPlayerLevel').val(data[0].playerLevel);
                $('#balInfoCurrentBal').val(data[0].totalBalanceAmount);
                $('#balInfoCurrency').val(data[0].currency);

                //player info
                $('#completeName').val(data[0].firstName+' '+data[0].lastName);
                $('#email').val(data[0].email);
                $('#memberSince').val(data[0].createdOn);
                $('#city').val(data[0].city);
                $('#country').val(data[0].country);
                $('#birthday').val(data[0].birthdate);
                $('#gender').val(data[0].gender);
                $('#phone').val(data[0].phone);
                $('#cp').val(data[0].cp);

                //balance
                $('#subwalletCurrentBal').val(data[0]['subwalletBalanceAmount'][0].subwalletBalanceAmount);
                $('#cashbackwalletCurrentBal').val(data[0]['cashbackwalletBalanceAmount'][0].cashbackwalletBalanceAmount);
            }
        },'json');
        return false;
    },

    setPlayerNewBalAmount : function() {
        var playerAccountId = $('#balInfoPlayerAccountId').val();
        var playerId = $('#balInfoPlayerId').val();
        var playerCurrentBal = $('#balInfoCurrentBal').val();
        var playerNewBal = $('#balInfoNewCurrentBal').val();

        $.ajax({
            'url' : base_url +'payment_management/setPlayerNewBalAmount/'+playerId+'/'+playerCurrentBal+'/'+playerNewBal,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                html  = '';
                html += '<span class="col-md-12">';
                html += 'Balance has been adjusted!';
                html += '</p>';

                $('#saveBtn').hide();
                $('#notificationMsg').html(html);
            }
        },'json');
        return false;
    },

    getPlayerTransactionHistory : function(playerAccountId) {
        $('#checkPlayerTransactionHistory').show();
        $.ajax({
            'url' : base_url +'payment_management/getPlayerTransactionHistory/'+playerAccountId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                for (var i = 0; i < data.length; i++) {
                    html  = '';
                    html += '<tr>';
                    html += '<td>'+data[i].transactionType+'</td>';
                    html += '<td>'+data[i].amount+'</td>';
                    html += '<td>'+data[i].currency+'</td>';
                    html += '<td>'+data[i].dwDateTime+'</td>';
                    html += '<td>'+data[i].paymentMethodName+'</td>';
                    html += '<td>'+data[i].dwStatus+'</td>';
                    html += '<td>'+data[i].processedByAdmin+'</td>';
                    html += '<td>'+data[i].processDatetime+'</td>';
                    html += '</tr>';
                    $('#transactionHistoryResult').append(html);
                };

                // //player info
                $('#completeNameHistory').val(data[0].firstName+' '+data[0].lastName);
                $('#emailHistory').val(data[0].email);
                $('#memberSinceHistory').val(data[0].createdOn);
                $('#cityHistory').val(data[0].city);
                $('#countryHistory').val(data[0].country);
                $('#birthdayHistory').val(data[0].birthdate);
                $('#genderHistory').val(data[0].gender);
                $('#phoneHistory').val(data[0].phone);
                $('#cpHistory').val(data[0].cp);
            }
        },'json');
        return false;
    },

    getPlayerTransactionLog : function(playerId) {
        $.ajax({
            'url' : base_url +'payment_management/getPlayerTransactionLog/'+playerId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                //player info
                $('#completeNameHistory').val(data[0].firstName+' '+data[0].lastName);
                $('#emailHistory').val(data[0].email);
                $('#memberSinceHistory').val(data[0].createdOn);
                $('#cityHistory').val(data[0].city);
                $('#countryHistory').val(data[0].country);
                $('#birthdayHistory').val(data[0].birthdate);
                $('#genderHistory').val(data[0].gender);
                $('#phoneHistory').val(data[0].phone);
                $('#cpHistory').val(data[0].cp);

                if(data.length != 0){
                    for (var i = 0; i < data.length; i++) {
                        html  = '';
                        html += '<tr>';
                        html += '<td>'+data[i].transactionType+'</td>';
                        html += '<td>'+data[i].amount+'</td>';
                        html += '<td>'+data[i].currency+'</td>';
                        html += '<td>'+data[i].dwDateTime+'</td>';
                        html += '<td>'+data[i].paymentMethodName+'</td>';
                        html += '<td>'+data[i].dwStatus+'</td>';
                        html += '<td>'+data[i].processedByAdmin+'</td>';
                        html += '<td>'+data[i].processDatetime+'</td>';
                        html += '</tr>';
                        $('#transactionHistoryResult').append(html);
                    }
                }else{
                    html  = '';
                    html += '<tr>';
                    html += "<td colspan='8' style='text-align:center'>No Records Found</td>";
                    html += '</tr>';
                }
            }
        },'json');
        return false;
    },

    getAffiliateRequest : function(affiliateId, paymentHistoryId) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        $('#affiliateRequestDetails').html(html);

        $.ajax({
            'url': base_url +'payment_management/getTransactionHistory/'+affiliateId,
            'type': "GET",
            'dataType': "html",
            'success' : function(data) {
              $( "#affiliate_transac_panel_body" ).html( data );
        }});

        $.ajax({
            'url' : base_url +'payment_management/reviewAffiliateRequest/'+affiliateId+'/'+paymentHistoryId,
            'type' : 'GET',
            'dataType' : "json",
            'success' : function(data){
                html  = '';

                $('#affiliateRequestDetails').html(html);

                //personal info
                $('#completeName').val(data[0].firstname+' '+data[0].lastname);
                $('#email').val(data[0].email);
                $('#memberSince').val(data[0].memberSince);
                $('#address').val(data[0].address);
                $('#city').val(data[0].city);
                $('#country').val(data[0].country);

                //bankinfo
                $('#affiliateName').val(data[0].firstname+' '+data[0].lastname);
                $('#currency').val(data[0].currency);
                $('#amount').val(data[0].amount);
                $('#period').val(data[0].period);
                $('#bank_name').val(data[0].bankName);
                $('#account_name').val(data[0].accountName);
                $('#account_number').val(data[0].accountNumber);

                //ids
                $('#payment_history_id').val(data[0].affiliatePaymentHistoryId);

                if(data[0].status == 1) {
                    $('#approved').show();
                } else {
                    $('#processing').show();
                }

                if(data[0].status == 3)  {
                    $('#declined').hide();
                }
            }
        },'json');
        return false;
    },

    respondToAffiliateRequest : function(request) {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        $('#affiliateRequestDetails').html(html);
        var payment_history_id = document.getElementById('payment_history_id').value;

        $.ajax({
            'url' : base_url +'payment_management/respondToAffiliateRequest/'+request+'/'+payment_history_id,
            'type' : 'GET',
            'success' : function(data) {
                html  = '';
                html += '<p>';

                if(request == "process") {
                    html += 'Processing Profit of Affiliate!';
                } else if(request == "approve") {
                    html += 'Deposited Profit of Affiliate!';
                }

                html += '</p>';

                $('#affiliateRequestDetails').html(html);
                $('#checkPlayer').hide();
                $('#repondBtn').hide();
            }
        },'json');
        return false;
    },

    respondToAffiliateDeclined : function() {
        html  = '';
        html += '<p>';
        html += 'Loading Data...';
        html += '</p>';

        $('#affiliateRequestDetails').html(html);
        var payment_history_id = document.getElementById('payment_history_id').value;
        var reason = document.getElementById('declinedReasonTxt').value;

        $.ajax({
            'url' : base_url +'payment_management/respondToAffiliateDeclined/'+payment_history_id+'/'+reason,
            'type' : 'GET',
            'success' : function(data){
                html  = '';
                html += '<p>';
                html += 'Declined Profit of Affiliate!';
                html += '</p>';

                $('#affiliateRequestDetails').html(html);
                $('#checkPlayer').hide();
                $('#repondBtn').hide();
            }
        },'json');
        return false;
    },

    setDepositSlipValue : function(imageName) {
        $(".depositSlipImage").attr('src', site_url(imageName));
    }
};
