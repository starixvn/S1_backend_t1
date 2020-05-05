var deposit_process_mode;
var force_setup_player_deposit_bank_if_empty;
var has_init = false;
var payment_account_details = {};
var promotion_details = {};
var system_feature_enable_mobile_manual_deposit_redirect_to_dashboard_after_deposit;

function initialize_payment_account() {
    $('#deposit-tab-content-manual .bank-list a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var entry = $(e.target).closest('li');
        if (entry.length <= 0) {
            return false;
        }

        if ((ENABLE_DEPOSIT_CATEGORY_VIEW === "1")) {
            $('#DepositPaymentAccount').val(payment_account_id);
        }
        else {
            $('#DepositPaymentAccount').val(entry.data('payment_account_id')).trigger('change').trigger('keyup');
        }

        // Display the min and max hint for the payment amount.
        var min_deposit_trans = _export_sbe_t1t.utils.formatCurrency(entry.data('min_deposit_trans'));
        var max_deposit_trans = _export_sbe_t1t.utils.formatCurrency(entry.data('max_deposit_trans'));
        $('.deposit-limit.min span').html(min_deposit_trans);
        $('.deposit-limit.max span').html(max_deposit_trans);

        // Setup the deposit amount condition.
        var bankDepositAmount = $('#bankDepositAmount');
        bankDepositAmount.attr({
            'min': entry.data('min_deposit_trans'),
            'max': entry.data('max_deposit_trans'),
            'data-min-error': bankDepositAmount.attr('lang-min-error').replace('{0}', min_deposit_trans),
            'data-max-error': bankDepositAmount.attr('lang-max-error').replace('{0}', max_deposit_trans)
        });

        if (has_init) {
            // Don't focus on the wrong field when you switch bank accounts
            var deposit_validator = $('#form-deposit').data('bs.validator');
            var old_focus = deposit_validator.options.focus;
            deposit_validator.options.focus = false;
            deposit_validator.validate(function () {
                deposit_validator.options.focus = old_focus;
            });
        }

        if ((deposit_process_mode == DEPOSIT_PROCESS_MODE2) || (deposit_process_mode == DEPOSIT_PROCESS_MODE3)) {
            show_dropdown_payment_account_list('close');

            $('.deposit-process-mode-2.select-payment-account .bank-entry .active-payment-account-info').html($("a", entry).html());

            if ((ENABLE_DEPOSIT_CATEGORY_VIEW === "1")) {
                if (payment_account_id == '') payment_account_id = entry.data('payment_account_id')
                loadPaymentAccountDetail(payment_account_id);
            }
            else {
                loadPaymentAccountDetail(entry.data('payment_account_id'));
            }
        }

        if ((deposit_process_mode == DEPOSIT_PROCESS_MODE1) && (ENABLE_DEPOSIT_CATEGORY_VIEW === "1")) {
            if (payment_account_id == '') payment_account_id = entry.data('payment_account_id')
            $('#bankDepositPanel li.active').removeClass('active');
            $('#bankDepositPanel li[data-payment_account_id=' + payment_account_id + ']').addClass('active');
            var chosenBank = $('#bankDepositPanel li.active a').attr('title');
            $('#selected-payment-account-name').text(chosenBank);
            $('.deposit-payment-account-info').hide();
        }

        has_init = true;
    });

    // Select manual deposit bank
    $('#deposit-tab-content-manual #bankDepositPanel a:first').tab('show');

    // Adjust the bank list block height.
    $('#bankDepositPanel').data('height', $('#bankDepositPanel').outerHeight()).attr('data-height', $('#bankDepositPanel').outerHeight());

    $('.show-btn').off('click').on('click', function () {
        var bankDepositPanel = $('#bankDepositPanel');
        var curHeight = bankDepositPanel.height();
        var autoHeight = bankDepositPanel.css('height', 'auto').outerHeight();
        bankDepositPanel.height(curHeight).animate({ height: autoHeight + 'px' }, { duration: 300 });

        $('.show-btn').hide();
        $('.hide-btn').show();
    });

    $('.hide-btn').off('click').on('click', function () {
        $('#bankDepositPanel').animate({ height: $('#bankDepositPanel').data('height') + 'px' }, { duration: 300 });
        $('.show-btn').show();
        $('.hide-btn').hide();
    });
}

function is360browser() {
    var ua = navigator.userAgent;
    if (ua.match(/QihooBrowser/) != null) {
        return true;
    }
    else if (ua.match(/Chrome/) != null) {
        function _mime(option, value) {
            var mimeTypes = navigator.mimeTypes;
            for (var mt in mimeTypes) {
                if (mimeTypes[mt][option] == value) {
                    return true;
                }
            }
            return false;
        }
        var chrome_version = ua.replace(/^.*Chrome\/([\d]+).*$/, '$1');
        if (chrome_version > 36 && this.showModalDialog) {
            return true;
        } else if (chrome_version > 45) {
            return _mime('type', 'application/vnd.chromium.remoting-viewer');
        }
    } else {
        return false;
    }
}

function show_dropdown_payment_account_list(method) {
    if (method == 'close') {
        $("#deposit-tab-content-manual .bank-list").removeClass('dropdown-show');
    } else if (method == 'open') {
        $("#deposit-tab-content-manual .bank-list").removeClass('dropdown-show').addClass('dropdown-show');
    } else {
        $("#deposit-tab-content-manual .bank-list").toggleClass('dropdown-show');
    }
}

function loadPaymentAccountDetail(payment_account_id) {
    if (payment_account_details.hasOwnProperty(payment_account_id)) {
        renderPaymentAccountDetail(payment_account_details[payment_account_id]);
        return;
    }

    Loader.show();
    $.get('/ajax/deposit/PaymentAccountDetail/' + payment_account_id, function (data) {
        Loader.hide();

        if (data.status != "valid") {
            payment_account_details[payment_account_id] = false;

            MessageBox.danger(data.msg);
            return false;
        }
        payment_account_details[data.payment_account_data.payment_account_id] = data.payment_account_data;
        renderPaymentAccountDetail(data.payment_account_data);
    });
}

function renderPaymentAccountDetail(payment_account_data) {
    if (typeof payment_account_data != "object") {
        return false;
    }
    if (payment_account_data.second_category_flag != '9') {
        $('.payment-account-detail #active-payment-account-bank-name').empty().html(payment_account_data.payment_account_title);
    }
    else {
        $('.payment-account-detail #active-payment-account-bank-name').empty();
        $('.payment-account-detail #active-payment-account-bank-name-block').hide();
    }

    $('.payment-account-detail #active-payment-account-number').empty().html(payment_account_data.payment_account_number);
    $('.payment-account-detail #active-payment-account-name').empty().html(payment_account_data.payment_account_name);
    $('.payment-account-detail #active-payment-account-branch-name').empty().html(payment_account_data.payment_branch_name);
    $('#selected-payment-account-name').text(payment_account_data.payment_account_title);

    var account_image_url = payment_account_data.account_image_url;
    if (account_image_url !== null && account_image_url.length > 0) {
        $(".payment-account-detail #active-payment-account-image img").attr('src', account_image_url);
        $(".payment-account-detail #active-payment-account-image img").show();
    } else {
        $(".payment-account-detail #active-payment-account-image img").attr('src', "");
        $(".payment-account-detail #active-payment-account-image img").hide();
    }
}


function showDepositVerifyModal() {
    var modal = $('#deposit-verify-modal');

    var form = $('form', modal);
    var submit_btn = $('.submit-btn', modal);

    modal.off('hidden.bs.modal').on('hidden.bs.modal', function () {
        form[0].reset();
    });

    modal.off('shown.bs.modal').on('shown.bs.modal', function () {
        $('#deposit_verify_withdrawal_password', modal).focus();

        form.off('submit').on('submit', function (e) {
            form.validator('validate', function (e, validator) {
                if (!validator.isIncomplete() && !validator.hasErrors()) {
                    submitDepositVerify($('#deposit_verify_withdrawal_password', modal).val());

                    modal.modal('hide');
                }
            });

            return false;
        });

        submit_btn.off('click').on('click', function () {
            form.submit();
        });

        form.validator();
    });

    modal.modal('show');
}

function submitDepositVerify(withdrawal_password) {
    var data = {
        withdrawal_password: withdrawal_password
    };

    Loader.show();
    $.post('/ajax/deposit/ManualDepositBanks', data, function (data) {
        Loader.hide();

        if (data.status != "success") {
            MessageBox.danger(data.msg, null, function () {
                showDepositVerifyModal();
            });
            return false;
        }

        $('#showDepositVerifyModal').hide();

        var payment_accounts = data['payment_accounts'];
        if (payment_accounts.length <= 0) {
            $('#bankDepositUnAvailable').removeClass('hidden');

            return false;
        }

        var ul = $('#bankDepositPanel');
        ul.removeClass('hidden').empty();

        $.each(payment_accounts, function (idx, payment_account) {
            var li = $('<li>');
            li.attr('data-payment_account_id', payment_account.payment_account_id);
            li.attr('data-bank', payment_account.bankTypeId);
            li.attr('data-flag', payment_account.flag);
            li.attr('data-min_deposit_trans', payment_account.min_deposit_trans);
            li.attr('data-max_deposit_trans', payment_account.max_deposit_trans);

            var a = $('<a>').attr('href', '').attr('data-toggle', 'tab').attr('title', payment_account.payment_account_title).html('<i class="fa fa-check-circle" aria-hidden="true"></i>');
            a.appendTo(li);

            var span;
            if ($.isEmptyObject(payment_account.account_icon_url)) {
                span = $('<span>').addClass('b-icon').addClass('bank_' + payment_account.bankTypeId).text(payment_account.payment_account_title);
            } else {
                span = $('<span>').addClass('b-icon-custom').html($('<img>').attr('src', payment_account.account_icon_url.length).html() + payment_account.payment_account_title);
            }

            span.appendTo(a);

            li.appendTo(ul);
        });

        initialize_payment_account();

        $('#deposit-payment-account-security-verify').remove();
        $('.select-payment-account .deposit-payment-account-info').removeClass('hidden');
    });
}

function showSelectPlayerBankAccount() {
    $("#player-deposit-banks").modal("show");
}

function setupSelectPlayerDepositBank(bank_data) {
    var load_bank_data;
    if (bank_data === undefined) {
        var $chosenBank = $('#currentBankList li.active');
        //Add chosen bank when no default account in the payment type
        if ($chosenBank.length <= 0 && $('#currentBankList').length > 0) {
            $chosenBank = $('#currentBankList li:nth-child(1)');
            $('#currentBankList li:nth-child(1)').addClass('active');
        }
        // Hack (append to last event.) - for waiting other jquery events.
        if (force_setup_player_deposit_bank_if_empty == 1) {
            $(function () {
                // Check the player deposit bank is empty.
                if ($chosenBank.length <= 0) {
                    MessageBox.info(
                        lang('cashier.deposit.force_setup_player_deposit_bank_hint'), null, function () {
                            document.location.href = "/player_center2/bank_account#bank_account_deposit";
                        },
                        [{
                            'attr': {'class': 'btn btn-primary'},
                            'text': lang('pay.reg')
                        }]
                    );
                }
            });
        }

        if ($chosenBank.length <= 0) {
            return;
        }

        load_bank_data = {
            'bankTypeId': $chosenBank.data('bank-type-id'),
            'bankDetailsId': $chosenBank.data('id'),
            'bankName': $('span', $chosenBank).html(),
            'branch': $chosenBank.data('branch'),
            'province': $chosenBank.data('province'),
            'city': $chosenBank.data('city'),
            'accNum': $chosenBank.data('acc-num'),
            'accName': $chosenBank.data('acc-name'),
            'mobileNum': $chosenBank.data('mobile-num')
        };
    } else {
        load_bank_data = {
            'bankTypeId': bank_data['bankTypeId'],
            'bankDetailsId': bank_data['bankDetailsId'],
            'bankName': bank_data['bankName'],
            'branch': bank_data['branch'],
            'province': bank_data['province'],
            'city': bank_data['city'],
            'accNum': bank_data['bankAccountNumber'],
            'accName': bank_data['bankAccountFullName'],
            'mobileNum': bank_data['phone']
        };
    }
    // Get chosen bank info from data fields
    loadPlayerDepositBank(load_bank_data);

    $('#player-deposit-banks').modal('hide');
}

function setCorrespondPaymentAccount(bank_data) {
    $.ajax({
        url: "/ajax/deposit/CorrespondPaymentAccounts",
        type: "POST",
        data: bank_data,
        dataType: 'json',
        success: function (correspond_payment_accounts) {
            refreshBankDepositPanel(correspond_payment_accounts);
        }
    });
}

function refreshBankDepositPanel(payment_accounts) {
    $('#bankDepositPanel').empty();
    if (payment_accounts.length == 0) {
        return false;
    }

    for (var i = 0; i < payment_accounts.length; i++) {
        var each_item_li_html =
            '<li data-payment_account_id="' + payment_accounts[i].payment_account_id + '" ' +
            'data-bank="' + payment_accounts[i].bankTypeId + '" ' +
            'data-flag="' + payment_accounts[i].flag + '" ' +
            'data-min_deposit_trans="' + payment_accounts[i].vip_rule_min_deposit_trans + '" ' +
            'data-max_deposit_trans="' + payment_accounts[i].vip_rule_max_deposit_trans + '" >' +
            '<a href="" data-toggle="tab" title="' + payment_accounts[i].payment_account_title + '">' +
            '<i class="fa fa-check-circle" aria-hidden="true"></i>' +
            '</a>' +
            '</li>';

        var each_item_span_html = '';
        if (payment_accounts[i].account_icon_url !== null) {
            each_item_span_html =
                '<span class="b-icon-custom"><img src="' + payment_accounts[i].account_icon_url + '" />' + payment_accounts[i].payment_account_title + '</span>';
        }
        else {
            each_item_span_html =
                '<span class="b-icon bank_' + payment_accounts[i].bankTypeId + '">' + payment_accounts[i].payment_account_title + '</span>';
        }

        $('#bankDepositPanel').append(each_item_li_html);
        $('#bankDepositPanel').find('li[data-payment_account_id="' + payment_accounts[i].payment_account_id + '"]').append(each_item_span_html);
    }

    var icon_html = $('#bankDepositPanel li:first a').html();
    $('#bankDepositPanel li:first a').remove();
    var span_html = $('#bankDepositPanel li:first').html();
    console.log(icon_html + span_html);
    $('.deposit-process-mode-2.select-payment-account .bank-entry .active-payment-account-info').empty();
    $('.deposit-process-mode-2.select-payment-account .bank-entry .active-payment-account-info').html(icon_html + span_html);
}

function loadPlayerDepositBank(bank) {
    var data = [
        // display element, form field element, data
        [null, $('#activeBankTypeIdField'), bank.bankTypeId],
        [null, $('#activeBankDetailsIdField'), bank.bankDetailsId],
        [$('span#activeBankName'), null, '&nbsp;' + bank.bankName],
        [$('#activeAccNum'), $('#activeAccNumField'), bank.accNum],
        [$('#activeAccName'), $('#activeAccNameField'), bank.accName],
        [$('#activeProvince'), $('#activeProvinceField'), bank.province],
        [$('#activeCity'), $('#activeCityField'), bank.city],
        [$('#activeBankAddress'), $('#activeBankAddressField'), bank.address],
        [$('#activeBranch'), $('#activeBranchField'), bank.branch],
        [$('#activeMobileNum'), $('#activeMobileNumField'), bank.mobileNum],
    ];

    // Remove previous bank icon class
    $('.dispBankInfo').removeClass('hide');
    $('span#activeBankName').removeClass(function (index, name) {
        var classNames = name.split(' ');
        var classNamesToRemove = '';
        // Remove old bank icon class
        $.each(classNames, function (index, val) {
            classNamesToRemove += (val.startsWith('bank_') ? val : '') + ' ';
        });
        return classNamesToRemove;
    });
    // Bank icon and name
    $('span#activeBankName').addClass('bank_' + bank.bankTypeId);

    // Load new data
    for (var i = 0, len = data.length; i < len; i++) {
        var d = data[i];
        if (d[2]) {
            d[0] && d[0].html(d[2]);
            d[1] && d[1].val(d[2]).trigger('change').trigger('keyup');
        } else {
            d[0] && d[0].html('');
            d[1] && d[1].val('').trigger('change').trigger('keyup');
        }
    }
};

function fetch_form_data() {
    var payment_account_id = $('#DepositPaymentAccount').val();
    var deposit_secure_id = $('#deposit_secure_id').val();
    var bankDepositAmount = $("#bankDepositAmount").val();
    var playerBankDetailsId = $('#activeBankDetailsIdField').val();
    var deposit_notes = $('#deposit_notes').val();
    var promoCmsId = $('#promo_cms_id').val();
    var wallet_id = $('#deposit_target_wallet').val();
    var deposit_datetime = $('#deposit_datetime').val();
    var mode_of_deposit = $('#mode_of_deposit').val();
    var depositor_name = $('#depositor_name').val();

    var data = {
        "payment_account_id": payment_account_id,
        "secure_id": deposit_secure_id,
        "depositAmount": bankDepositAmount,
        "playerBankDetailsId": playerBankDetailsId,
        "deposit_notes": deposit_notes,
        "promo_cms_id": promoCmsId,
        "wallet_id": wallet_id,
        "deposit_datetime": deposit_datetime,
        "mode_of_deposit": mode_of_deposit,
        "depositor_name": depositor_name,
    };

    return data;
}

function fetch_player_bank_details() {
    var bankTypeId = $('#activeBankName').val();
    var bankAccountNumber = $('#activeAccNum').val();
    var bankAccountFullName = $('#activeAccName').val();
    var province = $("#activeProvince").val();
    var city = $('#activeCity').val();
    var branch = $('#activeBranch').val();

    var data = {
        "input-bank-type-id": bankTypeId,
        "input-acct-num": bankAccountNumber,
        "input-acct-name": bankAccountFullName,
        "input-province": province,
        "input-city": city,
        "input-branch": branch,
        "set-default-bank": true
    };

    return data;
}

function getFileData(myFile) {
    var file = myFile.files[0];
    var filename = file.name;
    if (myFile.id == 'file1') {
        var add_remove_btn = '<button type="button" id="remove_file_btn1" class="filedata remove_btn" onclick="removeImage(this)"><i class="glyphicon glyphicon-remove-sign"></i></button>';
    } else {
        var add_remove_btn = '<button type="button" id="remove_file_btn2" class="filedata remove_btn" onclick="removeImage(this)"><i class="glyphicon glyphicon-remove-sign"></i></button>';
    }
    $(myFile).parent().parent().find("span").html(filename + add_remove_btn);
}

function removeImage(mybutton) {
    if (mybutton.id == 'remove_file_btn1') {
        $('#file1').val('')
        $(mybutton).parent().parent().find("span").html(lang('File 1:'));
    } else {
        $('#file2').val('')
        $(mybutton).parent().parent().find("span").html(lang('File 2:'));
    }
}

function validateAttachedFile(file) {
    var fp = $(file);
    var lg = fp[0].files.length; // get length

    if (lg != 0) {
        var allowedUploadFile = ALLOWED_UPLOAD_FILE.split("|");
        for (var i = 0; i < allowedUploadFile.length; i++) {
            allowedUploadFile[i] = 'image/' + allowedUploadFile[i];
        }

        var fileErrMsg = LANG_UPLOAD_FILE_ERRMSG;

        var items = fp[0].files;
        if (lg > 0) {
            for (var i = 0; i < lg; i++) {

                var fileSize = items[i].size; // get file size
                var fileType = items[i].type; // get file type
            }
        }

        var limitSize = LANG_UPLOAD_IMAGE_MAX_SIZE;

        if (fileSize <= limitSize) {
            if (allowedUploadFile.indexOf(fileType) === -1) {
                flg = 0;
                $('#errfm_txtImage').text(fileErrMsg);
                return false;
            }

        } else {
            flg = 0;
            $('#errfm_txtImage').text(fileErrMsg);
            return false;
        }
    }

    return true;
}

function alreadySelectFile(file) {
    var fp = $(file);
    var lg = fp[0].files.length; // get length

    if (lg != 0) {
        return true;
    } else {
        return false;
    }
}

function validateFiles() {
    if (typeof ENABLE_ATTACHED_DOCUMENTS === 'undefined') {
        return true;
    }
    if (ENABLE_ATTACHED_DOCUMENTS === '1') {
        var file_1 = $('#file1');
        var file_2 = $('#file2');
        success = (validateAttachedFile(file_1) && validateAttachedFile(file_2));

        if (success && required_deposit_upload_file_1) {
            //should check required
            success = alreadySelectFile(file_1);
            if (!success) {
                $('#errfm_txtImage').text(LANG_UPLOAD_FILE_REQUIRED_ERRMSG);
            }
        }

        return success;
    } else {
        return true;
    }
}

function getPlayerInputBankDetails() {
    var player_bank_details_data = fetch_player_bank_details();
    var post_data = '';

    $.each(player_bank_details_data, function (index, value) {
        if (typeof value != "undefined") {
            post_data = post_data + index + '=' + encodeURI(value) + '&';
        }
    });
    post_data = post_data.slice(0, -1);

    return post_data;
}

function submitDepositOrder() {
    var form_data = fetch_form_data();

    Loader.show();

    var formData = new FormData();
    if (ENABLE_ATTACHED_DOCUMENTS === '1') {
        formData.append('file1[]', $('#file1')[0].files[0]);
        formData.append('file2[]', $('#file2')[0].files[0]);
    }

    $.each(form_data, function (index, value) {
        if (typeof value != "undefined") {
            formData.append(index, value);
        }
    });

    $.ajax({
        url: "/ajax/deposit/ManualDeposit",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {

            Loader.hide();

            if (data.status != "success") {
                if (data.status == 'failed') {

                    MessageBox.ajax(data, function () {
                        Loader.show();
                        window.location.href = window.location.href;
                    });
                } else {
                    MessageBox.danger(data.msg);
                }
                return false;
            }

            var order = data.order;

            // for printing
            $("#print_modal_order_id").html(order.secure_id);
            $("#print_modal_account_name").html(order.payment_account_name);
            $("#print_modal_account_number").html(order.payment_account_number);
            $("#print_modal_deposit_amount").html(form_data.depositAmount);
            $("#print_modal_requested_on").html(order.created_at);
            $("#print_modal_expires_on").html(order.timeout_at);
            $("#print_deposit_time").val(order.created_at);
            $("#print_deposit_time_out").val(order.timeout_at);
            $("#print_transaction_id").val(order.secure_id);
            $("#print_secure_id").val(order.secure_id);
            $("#print_confirmDeposit").attr("disabled", false);

            switch (deposit_process_mode) {
                case DEPOSIT_PROCESS_MODE2:
                    showDepositOrderResult(data, system_feature_redirect_immediately_after_manual_deposit);
                    break;
                case DEPOSIT_PROCESS_MODE3:
                    showDepositOrderResult(data);
                    break;
                case DEPOSIT_PROCESS_MODE1:
                default:
                    $("#submit-done-deposit").off('hidden.bs.modal').on('hidden.bs.modal', function () {
                        showDepositOrderResult(data);
                    });
                    $("#modal_min_deposit_trans").parent().hide();
                    $("#modal_max_deposit_trans").parent().hide();

                    $("#modal_order_id").html(order.secure_id);
                    $("#modal_account_name").html(order.payment_account_name);
                    $("#modal_account_number").html(order.payment_account_number);
                    $("#modal_bank_branch_name").html(order.payment_branch_name);
                    $("#modal_deposit_amount").html(form_data.depositAmount);
                    $("#modal_min_deposit_trans").html(data.payment_account_data.min_deposit_trans);
                    $("#modal_max_deposit_trans").html(data.payment_account_data.max_deposit_trans);
                    $("#modal_requested_on").html(order.created_at);
                    $("#modal_expires_on").html(order.timeout_at);
                    $("#modal_deposit_date_time").html(order.player_deposit_time);
                    $("#modal_mode_of_deposit").html(lang(order.player_mode_of_deposit));

                    $("#deposit_time").val(order.created_at);
                    $("#deposit_time_out").val(order.timeout_at);
                    $("#transaction_id").val(order.secure_id);

                    var account_image_url = data.payment_account_data.account_image_url;
                    if (account_image_url !== null && account_image_url.length > 0) {
                        $("#modalAccountImage img").attr('src', account_image_url);
                        $("#modalAccountImage img").show();
                    } else {
                        $("#modalAccountImage img").hide();
                    }

                    if (!ENABLE_DEPOSIT_MODE_1_TWO_STEPS_FLOW) {
                        $("#submit-done-deposit").modal("show");
                    }
                    else {
                        $('#deposit-mode-1-step-1').hide();
                        $('#deposit-mode-1-step-2').show();
                    }

                    break;
            }
        }
    });
}

function printDepositOrder(order) {
    var divToPrint = document.getElementById('printDoneDeposit');
    var newWin = window.open('', 'Print-Window');
    newWin.document.open();
    newWin.document.write('<html><head>' + '</head><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');
    newWin.document.close();
    setTimeout(function () {
        newWin.close();
    }, 1000);
}

function showDepositOrderResult(data, redirectImmediately) {
    if (data.status != "success") {
        MessageBox.danger(data.msg);
        return false;
    }

    $("#modal-deposit-confirmation-span").append(data.msg);

    var redirectFunc = function () {
        Loader.show();
        if (!system_feature_enable_mobile_manual_deposit_redirect_to_dashboard_after_deposit) {
            window.location.href = window.location.href;
        } else {
            window.location.href = '/player_center/menu';
        }

    };
    if (redirectImmediately) {
        redirectFunc();
    } else {
        $("#deposit-confirmation").modal("show");
        $("#deposit-confirmation").off('hidden.bs.modal').on('hidden.bs.modal', redirectFunc);
    }
}

var last_check_amount = 0;
function list_available_promotion(callback) {
    var disable_all_promo = function () {
        $('.setup-deposit-promo .dropdown-menu li').each(function () {
            if ($(this).attr('value')) {
                $(this).addClass('disabled').attr('disabled', 'disabled').prop('disabled', true);
                $(this).find('a').addClass('disabled').attr('disabled', 'disabled').prop('disabled', true);
            }
        });
        $('.setup-deposit-promo .dropdown-menu li:first').trigger('click');
    };

    var amount = 0;
    var bankDepositAmount = $(".deposit-form input[name=bankDepositAmount]");

    if (bankDepositAmount.length > 0) {
        amount = parseFloat(bankDepositAmount.val());
        amount = isNaN(amount) ? 0 : amount;
    }

    var deposit_amount = $(".deposit-form input[name=deposit_amount]");

    if (deposit_amount.length > 0) {
        amount = parseFloat(deposit_amount.val());
        amount = isNaN(amount) ? 0 : amount;
    }

    last_check_amount = amount;
    Loader.show(PROMO_LOADER_STR);

    var data = {
        deposit_amount: amount
    };

    var currentTime = (new Date()).getTime();
    $.post('/api/getPlayerPromoApplyable', data, function (response) {
        disable_all_promo();

        $.each(response, function (i, data) {
            $('.setup-deposit-promo .dropdown-menu li').each(function () {
                if ($(this).attr('value') && $(this).attr('value') == data['key']) {
                    $(this).removeClass('disabled').removeAttr('disabled').prop('disabled', false);
                    $(this).find('a').removeClass('disabled').removeAttr('disabled').prop('disabled', false);
                }
            });
        });
        setTimeout(function () {
            Loader.hide();

            if (typeof callback === "function") {
                callback();
            }
        }, 3000 - ((new Date()).getTime() - currentTime));
    }, 'json');

    return true;
}

function show_promotion_detail() {
    var promo_cms_id = parseInt($('#promo_cms_id').val());
    promo_cms_id = (isNaN(promo_cms_id)) ? 0 : promo_cms_id;

    if (promotion_details.hasOwnProperty(promo_cms_id) || (promo_cms_id <= 0)) {
        render_promotion_detail();
        return;
    }

    Loader.show();
    $.ajax({
        "url": '/api/getPromoCmsItemDetails/' + promo_cms_id
    }).done(function (data) {
        Loader.hide();
        if (data['status'] != "success") {
            MessageBox.danger(data['msg']);
            $('#promo_cms_id').val('');
        } else {
            promotion_details[promo_cms_id] = data['promo_data'];
        }
        render_promotion_detail();
    });
}

function render_promotion_detail() {
    var promo_cms_id = parseInt($('#promo_cms_id').val());
    promo_cms_id = (isNaN(promo_cms_id)) ? 0 : promo_cms_id;

    var promo_data = null;
    if (!promotion_details.hasOwnProperty(promo_cms_id)) {
        promo_cms_id = 0;

        promo_data = {
            'promoName': '',
            'promoThumbnail': '',
            'promoTypeName': '',
            'promoDetails': '',
            'promoDescription': ''
        };

        $('.setup-deposit-promo .show-detail').removeClass('hidden').addClass('hidden');
    } else {
        promo_data = promotion_details[promo_cms_id];

        $('.setup-deposit-promo .show-detail').removeClass('hidden');
    }

    $('.setup-deposit-wallet-dropdown .dropdown-menu li').each(function () {
        $(this).removeClass('disabled').removeAttr('disabled').prop('disabled', false);
        $(this).find('a').removeClass('disabled').removeAttr('disabled').prop('disabled', false);
    });
    if (promo_data.hasOwnProperty('promorule') && promo_data.promorule.hasOwnProperty('trigger_wallets')) {
        var trigger_wallets = (promo_data.promorule.trigger_wallets === null) ? [] : promo_data.promorule.trigger_wallets.split(",");

        var select_val = null;
        $('.setup-deposit-wallet-dropdown .dropdown-menu li').each(function () {
            if (trigger_wallets.indexOf($(this).attr('value')) === -1) {
                $(this).addClass('disabled').attr('disabled', 'disabled').prop('disabled', true);
                $(this).find('a').addClass('disabled').attr('disabled', 'disabled').prop('disabled', true);
            } else {
                select_val = (select_val) ? select_val : $(this);
            }
        });

        if (select_val === null) {
            select_val = $($('.setup-deposit-wallet-dropdown .dropdown-menu li')[0]);
            select_val.removeClass('disabled').removeAttr('disabled').prop('disabled', false);
            select_val.find('a').removeClass('disabled').removeAttr('disabled').prop('disabled', false);
        }
        select_val.trigger('click');
    } else {
        $('.setup-deposit-wallet-dropdown .dropdown-menu li:first').trigger('click');
    }

    var modal = $('#deposit-promo-detail-modal');

    modal.off('hide.bs.modal').on('hide.bs.modal', function () {
        $('.modal-title span', modal).empty();
        $('.modal-header #promoItemPreviewImg', modal).attr('src', 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D');
        $('.modal-body #promoCmsPromoType', modal).empty();
        $('.modal-body #promoCmsPromoDetails', modal).empty();
    });

    modal.off('show.bs.modal').on('show.bs.modal', function () {
        $('.modal-title span', modal).html(promo_data['promoName']);
        $('.modal-header #promoItemPreviewImg', modal).attr('src', promo_data['promoThumbnail']);
        $('.modal-body #promoCmsPromoType', modal).html(promo_data['promoTypeName']);
        $('.modal-body #promoCmsPromoDetails', modal).html(_export_sbe_t1t.utils.decodeHtmlEntities(promo_data['promoDetails']));
    });

    $('.setup-deposit-promo .promotion_select_description').html(promo_data['promoDescription']);
}

function show_promo_detail() {
    $('#deposit-promo-detail-modal').modal('show');
}

function currency_rate_converter(decimal_digit) {
    var bankDepositAmount = $('#bankDepositAmount').val();
    var rateAmount = (bankDepositAmount * CURRENCY_CONVERSION_RATE);
    if (decimal_digit <= 3) {
        var num = rateAmount.toFixed(decimal_digit);
        num = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    else {
        var num = rateAmount.toFixed(decimal_digit);
    }
    $('.currency_conversion_result').text(num);
}

$(function () {
    // Initialize system setup
    deposit_process_mode = parseInt($('#deposit_process_mode').val());
    deposit_process_mode = (isNaN(deposit_process_mode)) ? 1 : deposit_process_mode;
    force_setup_player_deposit_bank_if_empty = $('#force_setup_player_deposit_bank_if_empty').val();
    system_feature_enable_mobile_manual_deposit_redirect_to_dashboard_after_deposit = $('#system_feature_enable_mobile_manual_deposit_redirect_to_dashboard_after_deposit').val();

    // Initialize payment account
    initialize_payment_account();

    // Open payment account dropdown menu
    $('.deposit-process-mode-2.select-payment-account .bank-entry').off('click').on('click', function (e) {
        show_dropdown_payment_account_list();

        e.stopPropagation();
    });
    // Close payment account dropdown menu
    $('body').click(function (e) {
        if ($(e.target).closest('.bank-entry.active').length <= 0) {
            show_dropdown_payment_account_list('close');
        }
    });

    setupSelectPlayerDepositBank();

    $('.btn.btn-copy').tooltip({
        "trigger": "manual",
        "offset": "0 75%"
    });

    var clipboard = new Clipboard('.btn.btn-copy');

    clipboard.on('success', function (e) {
        var self = $(e.trigger);

        self.tooltip('show');

        setTimeout(function () {
            self.tooltip('hide');
        }, 800);

        e.clearSelection();
    });

    // Form validation
    var deposit_form = $('#form-deposit');

    $('[required]', deposit_form).closest('.form-group').find('.control-label').each(function () {
        $(this).append($('<em style="color:red">*</em>'));
    });

    if (is360browser()) {
        deposit_form.attr("target", '_self');
    }

    $('#auto_payment_submit', deposit_form).on('click', function () {
        if (is360browser()) {
            MessageBox.info(_360browser_hint, "", function () {
                window.open(location.href, '_blank');
                $('button.btn-submit').click();
            });
        }
        else {
            $('button.btn-submit').click();
        }
    });

    var submit_timer = null;
    $('button.btn-submit', deposit_form).on('click', function () {
        if (validateFiles()) {
            var submit_btn = $(this);
            submit_btn.addClass('disabled').attr('disabled', 'disabled').prop('disabled', true);

            deposit_form.validator('validate', function (e, validator) {
                var paymenttype = deposit_form.data('paymenttype');
                if (!validator.isIncomplete() && !validator.hasErrors() && paymenttype != 'auto') {
                    if (submit_timer !== null) {
                        clearTimeout(submit_timer);
                    }

                    submit_timer = setTimeout(function () {
                        if ((ENABLE_USING_LAST_DEPOSIT_ACCOUNT) && (!DISABLE_PLAYER_DEPOSIT_BANK)) {
                            var post_bank_details_data = getPlayerInputBankDetails();

                            var bind_player_bank_details_promise = $.ajax({
                                "method": "POST",
                                "url": '/ajax/bank_account/AddDeposit/',
                                "data": post_bank_details_data
                            });

                            bind_player_bank_details_promise.done(function (msg, status, xhr) {
                                if (xhr.hasOwnProperty('responseJSON')) {
                                    if (msg.status == 'success') {
                                        $('#activeBankDetailsIdField').val(msg.bank_detail.bankDetailsId);

                                        submitDepositOrder();
                                    }
                                    else {
                                        MessageBox.danger(msg.msg);
                                        submit_btn.removeClass('disabled').removeAttr('disabled').prop('disabled', false);
                                    }
                                }
                            });
                        }
                        else {
                            submitDepositOrder();
                        }
                    }, 300);
                } else {
                    submit_btn.removeClass('disabled').removeAttr('disabled').prop('disabled', false);
                }
            });

            return false;
        }
    });

    deposit_form.validator({
        disable: false
    });

    $('.select-payment-account').focus();

    $('#step-2-upload-files-submit-btn').click(function () {
        var form_data = new Array();

        Loader.show();

        var formData = new FormData();
        if (ENABLE_ATTACHED_DOCUMENTS === "1") {
            formData.append('secure_id', $('#modal_order_id').text());
            formData.append('file1[]', $('#file1')[0].files[0]);
            formData.append('file2[]', $('#file2')[0].files[0]);
        }

        $.ajax({
            url: "/ajax/deposit/UploadAttachedDocupent",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log(data);
                Loader.hide();

                if (data.status == "success") {
                    $("#modal-deposit-confirmation-span").append(data.msg);
                    $("#deposit-confirmation").modal("show");
                    $("#deposit-confirmation").off('hidden.bs.modal').on('hidden.bs.modal', function () {
                        Loader.show();
                        if (!system_feature_enable_mobile_manual_deposit_redirect_to_dashboard_after_deposit) {
                            window.location.href = window.location.href;
                        } else {
                            window.location.href = '/player_center/menu';
                        }
                    });
                }
                else {
                    MessageBox.danger(data.msg);
                    return false;
                }
            }
        });
    });
});