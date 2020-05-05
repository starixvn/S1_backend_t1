var Register = {
    $form: '',
    usernameMinLength: 8,
    usernameMaxLength: 16,
    passwordMinLength: 7,
    passwordMaxLength: 20,
    restrictUsername: 1,
    restrictPassword: 1,
    usernameRegEx: '',
    passwordRegEx: '',
    emailRegEx: '',
    firstNameRegEx: '',
    lastNameRegEx: '',
    contactNumberRegex: '',
    validIcon: 'icon-checked green',
    invalidIcon: 'icon-warning red',
    idCardNumberLenght: '',
    idCardNumberValidate: false,
    idCardNumberRequired: 0,
    chooseDialingCode: 0,
    countryNumList: {},
    ismobile: 0,
    restrictMinLengthOnFirstName: 0,
    verificodeLength: 4,

    init: function(){
        /* ================================
        For Registration
        ================================== */
        $('.registration-field').on('click focus', function() {
			$('.registration-field-note').addClass('hide');
			if (!(this.name == "terms" && this.checked) ) {
				$(this).closest('.form-group').next('.registration-field-note').removeClass('hide');
			}
        });

        $('.registration-field[required]').on('focus blur keyup change', function() {
            Register.validateRequired(this, $(this).val());
        });

        var self = this;

        if (self.usernameRegEx.length == 0) {
            if (self.restrictUsername) {
                self.usernameRegEx = /^(?=.*[a-z])(?=.*[0-9])[a-z0-9]+$/;
            } else {
                self.usernameRegEx = /^[a-z0-9]+$/;
            }
        }

        if (self.passwordRegEx.length == 0) {
            if (self.restrictPassword) {
                self.passwordRegEx = /^(?=.*[a-z])(?=.*[0-9])[a-z0-9]+$/;
            } else {
                self.passwordRegEx = /^[A-Za-z0-9]+$/;
            }
        }

        if (self.idCardNumberRequired === 1) {
            self.idCardNumberValidate = true;
        }

        self.firstNameRegEx = /^[A-Za-z]+$/;
        self.lastNameRegEx = /^[A-Za-z]+$/;
        self.emailRegEx = /^\w+([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/;

        $.validator.addMethod(
            "regex",
            function(value, element, regexp) {
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            },
            "Please check your input."
        );

        $.validator.addMethod("validateDOB", function (value, element) {
            if ($("input[name='birthdate'][required]").length) {
                // OGLANG-65, switch between dropdown/select versions
                var use_dropdown = $('.birthday-option select').length == 0;
                console.log('use_dropdown', use_dropdown);
                return use_dropdown ? self.validateDOB_dropdowns() : self.validateDOB();
            } else {
                return true;
            }
        });

        $.validator.addMethod("validateIdCardNumber", function (value, element) {
            return self.idCardNumberValidate;
        });

        $.validator.addMethod("validate_check_str", function (value, element) {
            var elementName = $(element).attr('name');
            if(elementName) {
                var fieldValue = $("input[name='" + elementName + "']").val();
                return self.validate_check_str(fieldValue);
            }
            return true;
        });

        $('#dialing_code').on('changed.bs.select', function (e) {
            Register.showRegistrationFieldNote($('#dialing_code'));
            Register.validateRequired($('#dialing_code'), $('#dialing_code').val());
        });

        // Show/hide error panel for birthday dropdowns
        if ($('.birthday-option select').length == 0) {
            var err_panel = $('.birthday-option').siblings('.registration-field-note');
            $('.birthday-option, .birthday-option ~ .registration-field-note').mouseover(function () {
                $('.registration-field-note').addClass('hide');
                $(err_panel).removeClass('hide');
            })
            .mouseout(function () {
                $(err_panel).addClass('hide');
            });
        }

        // Show/hide error panel for birthday dropdowns
        if ($('.resident_country_option select').length == 0) {
            var resident_country_err_panel = $('.registration-field-note.countryField');
            $('.resident_country_option, .resident_country_option ~ .registration-field-note').mouseover(function () {
                $('.registration-field-note').addClass('hide');
                $(resident_country_err_panel).removeClass('hide');
            })
            .mouseout(function () {
                $(resident_country_err_panel).addClass('hide');
            });
        }


        self.$form = $('#registration_form');

        self.rules();
    },

    remoteInvokeTimer: {},
    remoteInvoke: function(kind_id, callback){
        if(this.remoteInvokeTimer.hasOwnProperty(kind_id)){
            clearTimeout(this.remoteInvokeTimer[kind_id]);
        }

        this.remoteInvokeTimer[kind_id] = setTimeout(function(){
            if(typeof callback === "function"){
                callback();
            }
        }, 200);
    },

    rules: function(){

        var self = this;

        var validator = self.$form.validate({
            rules: {
                username: {
                    required: true,
                    minlength: self.usernameMinLength,
                    maxlength: self.usernameMaxLength,
                    regex: self.usernameRegEx,
                    remote: {
                        url: '/api/playerUsernameExist',
                        type: "POST",
                        dataType: 'json',
                        data: {
                            username: function() {
                                return $('input[name="username"]').val();
                            }
                        },
                        dataFilter: function(data) {
                            if (data == "true") {
                                $('#username_exist_failed').show();
                                $('#username_exist_available').hide();
                                return "false";
                            } else {
                                $('#username_exist_failed').hide();
                                $('#username_exist_available').show();
                                return "true";
                            }
                        }
                    },
                },
                password: {
                    required: true,
                    minlength: self.passwrodMinLength,
                    maxlength: self.passwordMaxLength,
                    regex: self.passwordRegEx,
                },
                cpassword: {
                    required: true,
                    equalTo: '#password'
                },
                firstName: {
                    validate_check_str: true
                },
                lastName: {
                    validate_check_str: true
                },
                contactNumber: {
                    number: true
                },
                birthdate: {
                    required: function () {
                        return ($("input[name='birthdate'][required]").length) ? true : false;
                    },
                    validateDOB: true
                },
                residentCountry: {
                    required: function () {
                        return ($("[name='residentCountry'][required]").length) ? true : false;
                    },
                },
                referral_code: 'required',
                regex: self.emailRegEx,
                retyped_email: {
                    required: function() {
                        return ($("input[name='email'][required]").length) ? true : false;
                    },
                    equalTo: '#email'
                },
                id_card_number: {
                    validateIdCardNumber: true
                },
                dialing_code: {
                    required: function () {
                        var dialingCode = $('#dialing_code');
                        if (dialingCode.length && dialingCode.hasClass('required')) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                },
                terms: {
                    required: function () {
                        return ($("input[name='terms'][required]").length) ? true : false;
                    }
                }
            },
            ignore: ":hidden:not(input[name='birthdate']):not(input[name='residentCountry']):not(input[name='terms'])",
            messages: {
            },
            onkeyup: false,
            focusInvalid: true,
            errorElement: 'span',
            errorPlacement: function(error, element) {
                // $(error).insertAfter(element);
            },
            submitHandler: function(form) {
                $('.registration-field-note').addClass('hide');
                form.submit();
            },
            invalidHandler: function (e, validator) {
                self.$form.find(':submit').removeAttr('disabled').prop('disabled', false);
                var num = validator.numberOfInvalids();
                if (self.ismobile && num) {
                    if ($(validator.errorList[0].element).offset().top <= 60) {
                        $("html, body").animate({ scrollTop: 60 }, 1000);
                    }
                }

                if (num == 1 && Object.keys(validator.errorMap)[0] == "terms") {
                    if (($("input[name='terms'][required]").length)) {
                        $('.registration-field-note').addClass('hide');
                        $(".fterms").removeClass('hide')
                        console.log($("input[name='terms']").attr('checked'))
                        Register.validateTerms($("input[name='terms']").attr('checked'))
                    }
                }
            }
        });

        var old_form = validator.form;
        validator.form = function(){
            self.$form.find(':submit').attr('disabled', 'disabled').prop('disabled', true);

            return old_form.call(validator);
        };

    },
    validateUsernameRequirements: function(fieldValue, remote){

        var self = this;

        if( fieldValue.length >= self.usernameMinLength && fieldValue.length <= self.usernameMaxLength ){
            $('#username_len').removeClass(self.invalidIcon).addClass(self.validIcon);
        }else{
            $('#username_len').removeClass(self.validIcon).addClass(self.invalidIcon);
        }

        var re = new RegExp(self.usernameRegEx);

        if( re.test(fieldValue) ){
            $('#username_charcombi').removeClass(self.invalidIcon).addClass(self.validIcon);
        }else{
            $('#username_charcombi').removeClass(self.validIcon).addClass(self.invalidIcon);
        }

        // check fieldValue not empty and space string
        if (!/^ *$/.test(fieldValue)) {
            this.remoteInvoke('username', function(){
                $.ajax({
                    url: '/api/playerUsernameExist',
                    type: "POST",
                    dataType: 'json',
                    data: {
                        username: function() {
                            return $('input[name="username"]').val();
                        }
                    },
                    success: function(response) {
                        if (response) {
                            $('#username_exist_failed').show();
                            $('#username_exist_available').hide();
                            return "false";
                        } else {
                            $('#username_exist_failed').hide();
                            $('#username_exist_available').show();
                            return "true";
                        }
                    }
                });
            });
        } else {
            $('#username_exist_failed').hide();
            $('#username_exist_available').hide();
        }
    },
    validatePasswordRequirements: function(fieldValue, remote){

        var self = this;
        var result = true;

        if( fieldValue.length >= self.passwordMinLength && fieldValue.length <= self.passwordMaxLength ){
            $('#password_len').removeClass(self.invalidIcon).addClass(self.validIcon);
        }else{
            $('#password_len').removeClass(self.validIcon).addClass(self.invalidIcon);
            result = false;
        }

        var re = new RegExp(self.passwordRegEx);

        if( re.test(fieldValue) ){
            $('#password_regex').removeClass(self.invalidIcon).addClass(self.validIcon);
        }else{
            $('#password_regex').removeClass(self.validIcon).addClass(self.invalidIcon);
            result = false;
        }

        if( fieldValue != $('#username').val() ){
            if(fieldValue.length != 0){
                $('#password_not_username').removeClass(self.invalidIcon).addClass(self.validIcon);
            } else {
                $('#password_not_username').removeClass(self.validIcon).addClass(self.invalidIcon);
                result = false;
            }
        }else{
            $('#password_not_username').removeClass(self.validIcon).addClass(self.invalidIcon);
            result = false;
        }

        return result;
    },
    validateConfirmPassword: function( fieldValue ){
        var self = this;
        if (fieldValue.length <= 0) {
            $('#cpassword_reenter').removeClass(self.validIcon).addClass(self.invalidIcon);
        } else {
            if( fieldValue == $('#password').val() ){
                $('#cpassword_reenter').removeClass(self.invalidIcon).addClass(self.validIcon);
                $('#cpassword_reenter_msg').html(self.retypePasswordCorrect);
            }else{
                $('#cpassword_reenter').removeClass(self.validIcon).addClass(self.invalidIcon);
                $('#cpassword_reenter_msg').html(self.retypePassword);
            }
        }
    },
    validateFirstName: function (element, rule) {
        var self = this;
        var re = self.firstNameRegEx;
        var fieldValue = $(element).val();

        var check_str=self.validate_check_str(fieldValue, element);

        if (check_str && fieldValue.length != 0 ) {
            $('#firstNameRegex').removeClass(self.invalidIcon).addClass(self.validIcon);
        } else {
            $('#firstNameRegex').removeClass(self.validIcon).addClass(self.invalidIcon);
        }

        var regexStatus = true;
        if (rule && rule != "") {

            if(!self.restrictMinLengthOnFirstName){
                return regexStatus;
            }

            var firstNameRule = JSON.parse(rule),
                minlength = firstNameRule.min,
                rulesObj = {};

            if (self.restrictMinLengthOnFirstName) {
                if (minlength) rulesObj["minlength"] = minlength;
                $('input[name="firstName"]').rules("add", rulesObj);
            }

            if (minlength) {
                if (minlength <= fieldValue.length) {
                    $('#firstNameRestrictMinChars').removeClass(self.invalidIcon).addClass(self.validIcon);
                } else {
                    $('#firstNameRestrictMinChars').removeClass(self.validIcon).addClass(self.invalidIcon);
                    regexStatus = false;
                }
            }
        }

        if(regexStatus == false) return false;
    },
    validateLastName: function (element) {
        var self = this;
        var re = self.lastNameRegEx;
        var fieldValue = $(element).val();

        var check_str=self.validate_check_str(fieldValue, element);

        if (check_str && fieldValue.length != 0 ) {
            $('#lastNameRegex').removeClass(self.invalidIcon).addClass(self.validIcon);
        } else {
            $('#lastNameRegex').removeClass(self.validIcon).addClass(self.invalidIcon);
        }
    },

    /**
     * Determines if string s contains any emoji chars, OGP-12268
     * @param   string  s
     * @return  bool    true if s contains emoji; otherwise false.
     */
    match_emoji: function(s) {
        var emoji_regex = /(\u00a9|\u00ae|[\u2000-\u3300]|\ud83c[\ud000-\udfff]|\ud83d[\ud000-\udfff]|\ud83e[\ud000-\udfff])/;
        return emoji_regex.test(s);
    },
    validate_check_str: function(fieldValue, element){
        // Also check emoji if feature enabled, OGP-12268
        if (sys_feature_block_emoji == true) {
            if (this.match_emoji(fieldValue) == true) {
                return false;
            }
        }

        var invalid_chars =  '~!@#$%^&*():<>{}();+-_0123456789[]/，、！：；？（）…｛｝‧．。\\.|\'"=?,`';

        if (fieldValue.length !=0 ){
            for (var i = 0; i < fieldValue.length; i++){
                if (invalid_chars.indexOf(fieldValue[i]) >= 0) {
                    return false;
                }
            }
        }

        return true;
    },
    validateEmail: function( fieldValue ) {
        var self = this;
        var re = self.emailRegEx;
        if( re.test(fieldValue) ){
            $('#email_required').removeClass(self.invalidIcon).addClass(self.validIcon);
        }else{
            $('#email_required').removeClass(self.validIcon).addClass(self.invalidIcon);
        }
    },
    validateConfirmEmail: function( fieldValue ){

        var self = this;
        if (fieldValue.length <= 0) {
            $('#retyped_email_required').removeClass(self.validIcon).addClass(self.invalidIcon);
        } else {
            if( fieldValue == $('#email').val() ){
                $('#retyped_email_required').removeClass(self.invalidIcon).addClass(self.validIcon);
            }else{
                $('#retyped_email_required').removeClass(self.validIcon).addClass(self.invalidIcon);
            }
        }
    },
    validateContactNumber: function (fieldValue, rule){

        var self = this;
        var regexStatus = true;

        $('#mobile_format').siblings('.validate-mesg').hide();
        $('#mobile_format').siblings('.validate-mesg.format').show();

        if (/^[0-9]+$/.test(fieldValue)){
            $('#mobile_format').removeClass(self.invalidIcon).addClass(self.validIcon);
        }else{
            $('#mobile_format').removeClass(self.validIcon).addClass(self.invalidIcon);
            return false;
        }

        if(self.contactNumberRegex) {
            var re = new RegExp(self.contactNumberRegex);
            if( re.test(fieldValue) ){
                $('#mobile_format').removeClass(self.invalidIcon).addClass(self.validIcon);
                regexStatus = true;
            }else{
                $('#mobile_format').removeClass(self.validIcon).addClass(self.invalidIcon);
                regexStatus = false;
            }
        }

        if (rule && rule != "") {
            var rule = JSON.parse(rule),
                min = rule.min,
                max = rule.max,
                same = (min == max),
                addRule = false,
                rulesObj = {};

            if (!addRule) {
                addRule = true;
                if (min) rulesObj["minlength"] = min;
                if (max) rulesObj["maxlength"] = max;
                $('#contactNumber').rules("add", rulesObj)
            }

            if (same) {
                if (min == fieldValue.length) {
                    $('#contact_len_same').removeClass(self.invalidIcon).addClass(self.validIcon);
                } else {
                    $('#contact_len_same').removeClass(self.validIcon).addClass(self.invalidIcon);
                    return false;
                }
            }

            if (!same && max && min) {
                if (min <= fieldValue.length && max >= fieldValue.length) {
                    $('#contact_len_between').removeClass(self.invalidIcon).addClass(self.validIcon);
                } else {
                    $('#contact_len_between').removeClass(self.validIcon).addClass(self.invalidIcon);
                    return false;
                }
            }

            if (!same && min) {
                if (min <= fieldValue.length) {
                    $('#contact_len_min').removeClass(self.invalidIcon).addClass(self.validIcon);
                } else {
                    $('#contact_len_min').removeClass(self.validIcon).addClass(self.invalidIcon);
                    return false;
                }
            }

            if (!same && max) {
                if (max >= fieldValue.length) {
                    $('#contact_len_max').removeClass(self.invalidIcon).addClass(self.validIcon);
                } else {
                    $('#contact_len_max').removeClass(self.validIcon).addClass(self.invalidIcon);
                    return false;
                }
            }
        }

        if(regexStatus == false) return false;
    },
    validateReferralCode: function( fieldValue ){

        var self = this;

        if( fieldValue != "" ){
            $('#referral_code').removeClass(self.invalidIcon).addClass(self.validIcon);
        }else{
            $('#referral_code').removeClass(self.validIcon).addClass(self.invalidIcon);
        }

    },
    validateRequired: function( selector , fieldValue ){

        var self = this,
            selectorAddClass = "";

        if ($(selector).attr('data-requiredfield')) {
            selectorAddClass += "." + $(selector).data('requiredfield');
        }

        var isValidate = $(selector).attr('data-validateRequired');
        if (typeof isValidate === 'undefined') {
            isValidate = 1;
        }

        if (!parseInt(isValidate)) {
             return;
        }

        if( fieldValue != "" ){
            $(selector).closest('.form-group').next('.registration-field-note').find('.registration-field-required-icon' + selectorAddClass).removeClass(self.invalidIcon).addClass(self.validIcon);
        } else{
            $(selector).closest('.form-group').next('.registration-field-note').find('.registration-field-required-icon' + selectorAddClass).removeClass(self.validIcon).addClass(self.invalidIcon);
        }
    },
    validateDOB: function(){
        var self = this;

        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear() - 18;
        var post_format = null;
        var bdate = null;

        if(dd<10) {
            dd = '0'+dd
        }

        if(mm<10) {
            mm = '0'+mm
        }

        today = dd + '/' + mm + '/' + yyyy ;

        if( $('#day').val() != "" && $('#month').val() != "" && $('#year').val() != "" ) {
            //dd/mm/yyyy
            var bday = $("#year").val() + '/' + $("#month").val() + '/' + $("#day").val();
            $('#val_dob').removeClass(self.invalidIcon).addClass(self.validIcon);
            if (this.validateDOBProcess(bday)) { //if(this.validateDOBProcess(today) >= this.validateDOBProcess(bday)){
                $('#val_dob18').removeClass(self.invalidIcon).addClass(self.validIcon);
            } else {
                $('input[name="birthdate"]').val("");
                $('#val_dob18').removeClass(self.validIcon).addClass(self.invalidIcon);
                return false;
            }
            var monthVal = $("#month").val();
            bdate = $("#month option[value='"+monthVal+"']").text()+' '+$("#day").val()+', '+$("#year").val();
            post_format = $("#year").val()+'-'+$("#month").val()+'-'+$("#day").val();
        } else {
            $('#val_dob').removeClass(self.invalidIcon).addClass(self.validIcon);
            $('#val_dob').removeClass(self.validIcon).addClass(self.invalidIcon);
            if( $('#day').val() != "" || $('#month').val() != "" || $('#year').val() != "" ) {
                $('#val_dob18').removeClass(self.validIcon).addClass(self.invalidIcon);
            }
            return false;
        }

        $('input[name="birthdate"]').val(post_format);
        return true;
    },
    validateTerms: function(bool) {
        var self = this,
            termsErrBlock = $(".terms_required")

        if( bool ){
			termsErrBlock.removeClass(self.invalidIcon).addClass(self.validIcon);
        }else{
            termsErrBlock.removeClass(self.validIcon).addClass(self.invalidIcon);
        }
    },
    // OGLANG-65: DOB validator for dropdown only (while older validateDOB() is for selects)
    // For reg template 4, built for entaplay series sites
    validateDOB_dropdowns: function () {
        var self = this;
        var err_dob = true, err_dob18 = true;
        var dd = $('#day').val(), mm = $('#month').val(), yy = $('#year').val();
        var bday = yy + '-' + mm + '-' + dd;

        // Set/clear dob/dob18
        if (dd != '' && mm != '' && yy != '') {
            err_dob_clear();
            if (this.validateDOBProcess(bday)) {
                err_dob18_clear();
            }
            else {
                err_dob18_set();
            }
        }
        else {
            err_dob_set();
        }

        // If error-free, set birthdate input, and report true
        if (!err_dob && !err_dob18) {
            var dob_post = yy + '-' + mm + '-' + dd;
            $('input[name="birthdate"]').val(dob_post);
            return true;
        }
        // Or, clear birthdate input, report false
        else {
            $('input[name="birthdate"]').val('');
            return false;
        }

        // Error operation wrappers
        function err_dob18_clear() {
            $('#val_dob18').removeClass(self.invalidIcon).addClass(self.validIcon);
            err_dob18 = false;
        }

        function err_dob_clear() {
            $('#val_dob').removeClass(self.invalidIcon).addClass(self.validIcon);
            err_dob = false;
        }

        function err_dob18_set() {
            $('#val_dob18').removeClass(self.validIcon).addClass(self.invalidIcon);
            err_dob18 = true;
        }

        function err_dob_set() {
            $('#val_dob').removeClass(self.validIcon).addClass(self.invalidIcon);
            err_dob = true;
        }
    },
    // OGLANG-65: Make dob checking more precise
    validateDOBProcess: function(DOB){
        // Calculate the day of player's 18th birthday
        var dob_18yo = new Date(DOB), today = new Date();
        dob_18yo.setFullYear(dob_18yo.getFullYear() + 18);
        dob_18yo.setHours(0);

        // Determine if the day is in the past
        return today > dob_18yo;
    },
    validateIdCardNumber: function( fieldValue , required ) {
        var self = this;
        var re = new RegExp(/^([0-9]|[a-z])+([0-9a-z]+)$/i);
        if( re.test(fieldValue) && fieldValue.length == self.idCardNumberLenght){
            $('#val_id_card_num').removeClass(self.invalidIcon).addClass(self.validIcon);
            self.idCardNumberValidate = true;
        }else{
            if( fieldValue.length == 0 && required == 1 ){
                self.idCardNumberValidate = true;
            } else {
                $('#val_id_card_num').removeClass(self.validIcon).addClass(self.invalidIcon);
                self.idCardNumberValidate = false;
            }
        }
    },
    lowerCase: function(str) {
        str.value = str.value.toLowerCase();
        Register.validateUsernameRequirements(str.value);
    },
    chosenCountry: function (element) {
    },
    showRegistrationFieldNote: function (element) {
        $('.registration-field-note').addClass('hide');
        $(element).closest('.form-group').next('.registration-field-note').removeClass('hide');
    },
    validateVerificodeLength: function(fieldValue){
        var self = this;

        if( fieldValue.length == self.verificodeLength && !/^ *$/.test(fieldValue)){
            $('#verifi_code_len').removeClass(self.invalidIcon).addClass(self.validIcon);
        }else{
            $('#verifi_code_len').removeClass(self.validIcon).addClass(self.invalidIcon);
        }
    },
}