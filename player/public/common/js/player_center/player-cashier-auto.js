(function(window){
    function PlayerCashierAuto(){
        this.options = {
            "form_selector": '#form-deposit',
            "submit_modal_selector": '#dialog-trade',
            "finish_payment_url": "/player_center/dashboard"
        };

        this.form = null;
    }

    PlayerCashierAuto.prototype.init = function(option){
        this.options = $.extend({}, this.options, option);

        this.initUI();
        this.initEvent();
    };

    PlayerCashierAuto.prototype.initUI = function(){
        this.form = $(this.options.form_selector);

        this.submit_modal = $(this.options.submit_modal_selector);
    };

    PlayerCashierAuto.prototype.initEvent = function(){
        var self = this;

        /* init dropdown menu */
        $('ul.dropdown-menu').on('click', 'li', function(){
            var dropdown = $(this).closest('.dropdown');
            if(dropdown.length <= 0){
                return;
            }

            var text = $(this).data('text');
            var value = $(this).data('value');

            $(".dropdown-toggle span:first", dropdown).html(text);
            $(".field", dropdown).val(value);

            $("li a", dropdown).removeClass("cur");
            $("a", $(this)).addClass("cur");

            $('.dropdown-menu', dropdown).trigger($.Event("selected.t1t.dropdown", {
                "relatedTarget": this
            }), {
                "text": text,
                "value": value
            });
        });

        /* dropdown menu - common setup */
        $('.dropdown').each(function(){
            var dropdown = $(this);
            var span_text = $('.dropdown-toggle span:first', dropdown).text();
            var default_value = $(".field", dropdown).val();

            /* default option */
            $('li[value="' + default_value + '"]').trigger('click');

            /* reset */
            $('.dropdown-menu', this).on('reset.t1t.dropdown', function(){
                $(".dropdown-toggle span:first", dropdown).html(span_text);
                $(".field", dropdown).val(null);
            });
        });

        $('.btn-confirm', this.submit_modal).on('click', function(){
            self.finishedPayment();
        });

        $('.btn-close', this.submit_modal).on('click', function(){
            self.submit_modal.modal('hide');
        });

        $('input, select, .dropdown-menu', self.form).on('keyup keypress blur change selected.t1t.dropdown', function(){
            self.validateAuto3rdPaymentCheck()
        });

        $('[type="submit"]', self.form).on('click', function(){
            self.submit();
        });
    };

    PlayerCashierAuto.prototype.isUseIframe = function(){
        return (this.form.attr('target') === 'iframePost');
    };

    PlayerCashierAuto.prototype.validateAuto3rdPaymentCheck = function(){
        var self = this;

        var deposit_amount = parseFloat($('input[name="deposit_amount"]').val()),
            minDeposit = parseFloat($('input[name="minDeposit"]').val()),
            maxDeposit = parseFloat($('input[name="maxDeposit"]').val());
            special_limit_rules = new Array;
            $('input[name^="special_limit_rules"]').each(function() {
                special_limit_rules.push($(this).val());
            });


        var not_allowed_empty_field = ["deposit_amount"];

        for(var key in not_allowed_empty_field){
            var field = not_allowed_empty_field[key];

            if($('input[name="' + field + '"]').length > 0){
                var field_value = $('input[name="' + field + '"]').val();

                if(field_value == ""){
                    $('[type="submit"]', self.form).attr('disabled', true);
                    $('#auto_payment_submit', self.form).attr('disabled', true);
                    return false;
                }
            }
        }

        var not_allowed_empty_keyword_value = $('input[name^="field_required_"]').val();
        if(not_allowed_empty_keyword_value == ""){
            $('[type="submit"]', self.form).attr('disabled', true);
            $('#auto_payment_submit', self.form).attr('disabled', true);
            return false;
        }

        if($('#third_payment-input_type-bank').is(':visible') && $('li', $('#third_payment-input_type-bank')).length > 0){
            var bank = $('.field', $('#third_payment-input_type-bank')).val();

            if(bank.length <= 0 || bank == ""){
                $('[type="submit"]', self.form).attr('disabled', true);
                $('#auto_payment_submit', self.form).attr('disabled', true);
                return false;
            }
        }

        if($('#bank_type').is(':visible') && $('li', $('#bank_type')).length > 0){
            var bankType = $('.field', $('#bank_type')).val();

            if(bankType.length <= 0 || bankType == ""){
                $('[type="submit"]', self.form).attr('disabled', true);
                $('#auto_payment_submit', self.form).attr('disabled', true);
                return false;
            }
        }

        minDeposit = (isNaN(minDeposit)) ? 0 : minDeposit;
        maxDeposit = (isNaN(maxDeposit)) ? 9999999 : maxDeposit;
        deposit_amount = (isNaN(deposit_amount)) ? 0 : deposit_amount;

        var valid_rule = true;
        var amount_string = deposit_amount.toString().split(".");
        for(var i = 0; i < special_limit_rules.length; i++) {
            if(special_limit_rules[i] == "only_integer"){
                if(amount_string[1] != undefined){
                    valid_rule = false;
                    break;
                }
            }
            else if(special_limit_rules[i] == "max_one_decimal_digit"){
                if(amount_string[1] != undefined && amount_string[1].length > 1){
                    valid_rule = false;
                    break;
                }
            }
            else if(special_limit_rules[i] == "max_two_decimal_digits"){
                if(amount_string[1] != undefined && amount_string[1].length > 2){
                    valid_rule = false;
                    break;
                }
            }
            else if(special_limit_rules[i] == "with_two_decimal_digits"){
                if(amount_string[1] == undefined || amount_string[1].length != 2){
                    valid_rule = false;
                    break;
                }
            }
        }

        if(deposit_amount == "" || deposit_amount <= 0 || deposit_amount < minDeposit || deposit_amount > maxDeposit || !valid_rule){
            $('[type="submit"]', self.form).attr('disabled', true);
            $('#auto_payment_submit', self.form).attr('disabled', true);
            return false;
        }


        $('[type="submit"]', self.form).attr('disabled', false);
        $('#auto_payment_submit', self.form).attr('disabled', false);

        return true;
    };

    PlayerCashierAuto.prototype.finishedPayment = function(){
        window.location.href = this.options.finish_payment_url;
    };

    PlayerCashierAuto.prototype.submit = function(){
        var self = this;

        if(self.isUseIframe()){
            $('#postIframe').modal();
            $('#iframePost').attr('src', self.options.finish_payment_url);
            $('#form-deposit').submit();
        }else{
            if(!this.validateAuto3rdPaymentCheck()){
                return false;
            }

            var pattern = $('input[name="deposit_amount"]', self.form).attr('pattern');
            var deposit_amount_val = $('input[name="deposit_amount"]', self.form).val();
            if(pattern != undefined){
                var new_pattern = pattern.slice(1, -1);
                var limit_amount = new_pattern.split('|');
                if(limit_amount.indexOf(deposit_amount_val) === -1){
                    $('.float_amount_limit_hint').css('display', '');
                    return;
                }else{
                    $('.float_amount_limit_hint').css('display', 'none');
                }
            }

            self.submit_modal.modal('show');

            self.form.submit();
        }
    };

    window.PlayerCashierAuto = new PlayerCashierAuto();
})(window);