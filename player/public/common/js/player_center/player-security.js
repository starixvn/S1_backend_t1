var PlayerSecurity = {

	site_url: document.location.origin,
    LANG_UPLOAD_IMAGE_MAX_SIZE: "",
    LANG_UPLOAD_FILE_ERRMSG: "",
    LANG_UPLOAD_REAL_NAME: "",
    ALLOWED_UPLOAD_FILE: "",
    IS_EMAIL_FILLED_IN: "",
    LANG_PleaseTryAgain: "",
    LANG_CompressFailed: "",

    game_platforms: [],

    game_count: "",

    MAX_CHANGE_GAME_PLATFORM_COUNT : "50",
    run_count : "0",
    success_count : "0",
    fail_count : "0",
    GamePasswordProcessing: "",
    game_fail_msg: "",
    game_npassword:"",
    validIcon: 'icon-checked green',
    invalidIcon: 'icon-warning red',
    contactNumberRegex: '',

    mobileRegisteredBySecurity : false,
    mobileNumRegisteredBySecurity : "",
    Verification_Photo_ID: "",

	uploadImage: function () {
        show_loading();
        $("#form_upload_image").submit();
    },
	uploadId: function ( event , targetForm ,realname ) {

        var flg =1;
        var self = this;

        var allowedUploadFile = self.ALLOWED_UPLOAD_FILE.split("|");
        for (var i = 0; i < allowedUploadFile.length; i++) {
            allowedUploadFile[i] = 'image/'+allowedUploadFile[i];
        }

        $('#errfm-txtImage_'+targetForm).text("");
        var fileErrMsg = self.LANG_UPLOAD_FILE_ERRMSG;

        var allowUploadWithoutFile = false;
        if(targetForm == self.Verification_Photo_ID){
            var id_card_number = $('input[name="id_card_number"]').val();
            if($.trim(id_card_number) != ''){
                allowUploadWithoutFile = true;
            }
        }
        var fp = $(event).parent().prev().find('p > label > .txtImage');
        var lg = fp[0].files.length; // get length

        var items = fp[0].files;
        if (lg > 0) {
            for (var i = 0; i < lg; i++) {

                var fileSize = items[i].size; // get file size
                var fileType = items[i].type; // get file type
            }
            allowUploadWithoutFile = false;
        }

        var limitSize = self.LANG_UPLOAD_IMAGE_MAX_SIZE;

        if(allowUploadWithoutFile){
            show_loading();
            return $("#form_upload_image_"+targetForm).submit();
        }

        console.log(fileType);
        console.log(allowedUploadFile);
        console.log(fileType = Array.isArray(allowedUploadFile));
        console.log('actual size = '+fileSize+' / limit = '+limitSize);
        if(fileSize<=limitSize){
            if(fileType != Array.isArray(allowedUploadFile))
            {
                flg=0;
                $('#errfm-txtImage_'+targetForm).text(fileErrMsg);
            }

        }else{
            flg=0;
            $('#errfm-txtImage_'+targetForm).text(fileErrMsg);
        }

        if(flg===1){

            show_loading();
            $("#form_upload_image_"+targetForm).submit();
        }else{
            return false;
        }
    },

    readURL: function ( input ) {
        $('.preview-area').empty();
        var fileList = input.files;

        var anyWindow = window.URL || window.webkitURL;

        for(var i = 0; i < fileList.length; i++){
            //get a blob to play with
            var objectUrl = anyWindow.createObjectURL(fileList[i]);
            // for the next line to work, you need something class="preview-area" in your html
            $(input).parent().parent().next().append($('<div/>',{'class' :'file'}).append('<img src="' + objectUrl + '" />'));
            //$('.preview-area').append($('<div/>',{'class' :'file'}).append('<img src="' + objectUrl + '" />'));
            // get rid of the blob
            window.URL.revokeObjectURL(fileList[i]);
        }
    },

    validateContactNumber: function ( fieldValue, rule ) {

        var self = this;
        var regexStatus = true;

        $('#mobile_format').siblings('.validate-mesg').hide();
        $('#mobile_format').siblings('.validate-mesg.format').show();

        if (/^[0-9]+$/.test(fieldValue)){
            $('#mobile_format').removeClass(self.invalidIcon).addClass(self.validIcon);
        }else{
            $('#mobile_format').removeClass(self.validIcon).addClass(self.invalidIcon);
            regexStatus = false;
        }

        if(self.contactNumberRegex) {
            var re = new RegExp(self.contactNumberRegex);
            if( re.test(fieldValue) ){
                $('#mobile_format').removeClass(self.invalidIcon).addClass(self.validIcon);
            }else{
                $('#mobile_format').removeClass(self.validIcon).addClass(self.invalidIcon);
                regexStatus = false;
            }
        }

        if (rule && rule != "") {
            var rule = JSON.parse(rule),
                min = rule.min,
                max = rule.max,
                same = (min == max);

            if (same) {
                if (min == fieldValue.length) {
                    $('#contact_len_same').removeClass(self.invalidIcon).addClass(self.validIcon);
                } else {
                    $('#contact_len_same').removeClass(self.validIcon).addClass(self.invalidIcon);
                    regexStatus = false;
                }
            }

            if (!same && min) {
                if (min <= fieldValue.length) {
                    $('#contact_len_min').removeClass(self.invalidIcon).addClass(self.validIcon);

                } else {
                    $('#contact_len_min').removeClass(self.validIcon).addClass(self.invalidIcon);
                    regexStatus = false;
                }
            }

            if (!same && max) {
                if (max >= fieldValue.length) {
                    $('#contact_len_max').removeClass(self.invalidIcon).addClass(self.validIcon);
                } else {
                    $('#contact_len_max').removeClass(self.validIcon).addClass(self.invalidIcon);
                    regexStatus = false;
                }
            }

            if (!same && max && min) {
                if (min <= fieldValue.length && max >= fieldValue.length) {
                    $('#contact_len_between').removeClass(self.invalidIcon).addClass(self.validIcon);
                } else {
                    $('#contact_len_between').removeClass(self.validIcon).addClass(self.invalidIcon);
                    regexStatus = false;
                }
            }
        }

        $('input[name="validContactNumber"]').val(regexStatus);


    },

    resetSecurityContactNumberModal: function () {
        $("input[name='securityContactNumber']").val('');
        $('.security-contact-number-field-note').addClass('hide');
        $('.icon-checked').removeClass('icon-checked green').addClass('icon-warning red');
    },

    inputMobileNum: function (successMsg, numEmptyMessage) {
        $('#security-mobile-input').modal('show');
        $('#security_input_mobile_verification_code_msg').empty();

        var contactNumber = $("input[name='securityContactNumber']").val();

        if (contactNumber) {
            var is_valid = $('input[name="validContactNumber"]').val();
            if(is_valid == 'false'){
                return;
            }

            $("#mobile_format").parents('form').find('.security-contact-number-field-note').addClass('hide');
            $.post(this.site_url + '/player_center/postEditPlayer', {
                contact_number: contactNumber
            }).done(function(data) {

                if (data.status == "success") {
                    /** modified contact_number in account information */
                    var contant = $("input[name='contact_number']").prev('p');
                    var helpBlock = $("input[name='contact_number']").next('span');
                    $("input[name='contact_number']").remove();
                    contant.after(contactNumber);
                    helpBlock.removeClass('hidden');

                    $('#security-mobile-input').modal('hide');
                    PlayerSecurity.mobileRegisteredBySecurity = true;
                    PlayerSecurity.mobileNumRegisteredBySecurity = contactNumber;
                    PlayerSecurity.sendMobileVerification('', successMsg.replace("%s", contactNumber), numEmptyMessage);
                } else {
                    $('#security_input_mobile_verification_code_msg').append($('<div />', { 'class': 'alert alert-danger', 'role': 'alert', 'html': data.msg}));
                }

            }).always(function() {
                $("input[name='securityContactNumber']").val("");
            });
        }
    },

    sendMobileVerification: function ( contactNumber , successMsg , numEmptyMessage) {
        var mobileNumber = contactNumber;
        var self = this;

        if (PlayerSecurity.mobileRegisteredBySecurity) {
            mobileNumber = PlayerSecurity.mobileNumRegisteredBySecurity;
        }

        $('#security_sms_verification_msg').empty();
        //show_loading();
        if(!mobileNumber || mobileNumber == '') {
            //stop_loading();
            PlayerSecurity.inputMobileNum();
            return;
        }
        // Init sms-verify-model
        SMS_SendVerify(function(sms_captcha_val) {
            show_loading();
            var smsUrl = self.site_url + '/iframe_module/iframe_register_send_sms_verification/' + mobileNumber + "/1";
            $('#security_sms_verification_msg').text("");
            $.post(smsUrl, {
                sms_captcha: sms_captcha_val
            }).done(function (data) {
                var msg = (data.success) ? successMsg : data.message;
                $('#security_sms_verification_msg').text(msg);
                stop_loading();
            }).fail(function () {
                $('#security_sms_verification_msg').text(data.message);
            }).always(function () {
                stop_loading();
                $('#security-mobile').modal('show');
            });
        });
    },

    submitVerificationCode: function ( contactNumber , numEmptyMessage ){
    	var self = this;
        var mobileNumber = contactNumber;
        var sms_verification_code = $('#fm-verification-code').val();
        $('#security_verification_code_msg').empty();
        show_loading();

        if (PlayerSecurity.mobileRegisteredBySecurity) {
            mobileNumber = PlayerSecurity.mobileNumRegisteredBySecurity;
        }

        if(!mobileNumber || mobileNumber == '' || !sms_verification_code || sms_verification_code == '') {
            $('#security_verification_code_msg').append($('<div />', { 'class':'alert alert-danger', 'role':'alert' , 'html': numEmptyMessage }));
            stop_loading();
            return;
        }

        $.getJSON(self.site_url +'/iframe_module/update_sms_verification/' + mobileNumber +'/'+ sms_verification_code + '/1', function(data){
            if(data.success) {
                window.location.reload();
            }
            else {
                $('#security_verification_code_msg').append($('<div />', { 'class':'alert alert-danger', 'role':'alert' , 'html': data.message }));
            }
        }).always(function(){
            stop_loading();
        });
    },

    resendEmail: function (){
	    if(!PlayerSecurity.IS_EMAIL_FILLED_IN){
	        return $('#security-email').modal('show');
        }

        show_loading();
        var self = this;
        $.getJSON(self.site_url +'/iframe_module/resendEmail/true', function(data){
            if(data.success){
                $('#security-email').modal('show');
            }
            else{
                window.location.reload();
            }
        }).always(function(){
            stop_loading();
        });
    },

    changeWithdrawalPassword: function (){
        show_loading();
        var self = this;
        $('#div_withrawal_pass_msg').empty();
        var current_password = $('#current_password').val();
        var new_password = $('#new_password').val();
        var confirm_new_password = $('#confirm_new_password').val();
        $.post(self.site_url +'/player_center/postResetWithdrawPassword/', {'current_password':current_password,'new_password':new_password,'confirm_new_password':confirm_new_password}, function(data){

            if(data.status == "error") {
                // Show error message
                $('#div_withrawal_pass_msg').append($('<div />', { 'class':'alert alert-danger', 'role':'alert' , 'html': data.msg }));
                // Empty all inputs
                $('#current_password').val("");
                $('#new_password').val("");
                $('#confirm_new_password').val("");
                stop_loading();
            } else {
                $('#div_withrawal_pass_msg').append($('<div />', { 'class':'alert alert-success', 'role':'alert' , 'html': data.msg }));
                $('.deposit-notice').addClass('hide');
                // OGP-2313
                stop_loading();
                // Hide password chane modal
                $('#security-withdrawal').modal('hide');
                // Show complete message modal
                $('#security-general-complete').on('shown.bs.modal', function () {
                    $('#security-general-complete .mesg_success').fadeIn();
                });
                $('#security-general-complete').modal('show');
                // Reload the page
                setTimeout(function () { window.location.reload(); }, 3500);
            }

        });
    },

    changeLoginUserPassword: function (){
        show_loading();
        var self = this;
        $('#div_user_pass_msg').empty();
        var opassword = $('#opassword').val();
        var npassword = $('#npassword').val();
        var cpassword = $('#cpassword').val();

        $.post(self.site_url +'/player_center/postResetPassword/', {'opassword':opassword,'password':npassword,'cpassword':cpassword}, function(data){

            if(data.status == "error") {
                $('#div_user_pass_msg').append($('<div />', { 'class':'alert alert-danger', 'role':'alert' , 'html': data.msg }));
            } else {
                $('#div_user_pass_msg').append($('<div />', { 'class':'alert alert-success', 'role':'alert' , 'html': data.msg }));
                window.location.reload();
            }
            $('#opassword').val("");
            $('#npassword').val("");
            $('#cpassword').val("");
            stop_loading();
        });
    },

    ResetPassword: function (){
        var self = this;
        $('#div_user_pass_msg').empty();
        var opassword = $('#opassword').val();
        var npassword = $('#npassword').val();
        var cpassword;
        this.game_npassword = cpassword = $('#cpassword').val();

        $.post(self.site_url +'/player_center/postResetMainPassword/', {'opassword':opassword,'password':npassword,'cpassword':cpassword}, function(data){

            if(data.status == "error") {
                $('#div_user_pass_msg').append($('<div />', { 'class':'alert alert-danger', 'role':'alert' , 'html': data.msg }));
            } else {
                $('#div_user_pass_msg').append($('<div />', { 'class':'alert alert-success', 'role':'alert' , 'html': data.msg }));
                self.openDialog_gamepass();
            }
        });
    },

    openDialog_gamepass: function (){
        var self = this;
        $("#security-loginpassword").modal('hide');
        $("#security-gamepassword .modal-title").html();
        $("#security-gamepassword").modal('show');
        $('#game_changpass_btn').attr('disabled', true);

        self.processGamePlatfomPwd();
    },

    processGamePlatfomPwd: function (){
        var self = this;
        var _game_platforms = this.game_platforms;
        var processingMsg =this.GamePasswordProcessing;
        var game_failmsg= this.game_fail_msg;
        this.run_count++;
        if(this.run_count >= this.MAX_CHANGE_GAME_PLATFORM_COUNT){
            return self.showChangeResult();
        }

        if(_game_platforms.length <= 0){
            return self.showChangeResult();
        }

        var game_platform_id = parseInt(_game_platforms.pop());
        if(isNaN(game_platform_id)){
            self.processGamePlatfomPwd();
        }

        $("#game_platforms_"+ game_platform_id).val(processingMsg);

        self.changeGamePlatformPwd(game_platform_id, function(data){
            if(data.status == "error"){
                this.fail_count++;
                $("#game_platforms_"+ game_platform_id).val(data.msg);
            }else{
                this.success_count++;
                $("#game_platforms_"+ game_platform_id).val(data.msg);
            }
            self.processGamePlatfomPwd();
        }, function(){
            this.fail_count++;

            $("#game_platforms_"+ game_platform_id).val(game_failmsg);
            self.processGamePlatfomPwd();
        });
    },

    changeGamePlatformPwd: function (game_platform_id, success_callback, fail_callback){
        var self = this;
        // console.log("passord: "+this.game_npassword);
        $.ajax({
            url: "/player_center/gameResetPassword" ,
            type: 'POST',
            data: {
                password : this.game_npassword,//$('#npassword').val(),
                api_id: game_platform_id,
            },
            timeout: 10000,
            success: function (data) {
                success_callback(data);
            },
            error: function (){
                fail_callback();
            }
        });
    },

    showChangeResult: function (){
        var self = this;
        var _game_count= this.game_count;
        var _run_count= this.run_count;
        if(( _run_count -1) == _game_count){
            $('#game_changpass_btn').attr('disabled', false);
            $('#game_changpass_btn').removeClass("btn btn-default").addClass("btn btn-success")
        }
        $('#opassword').val("");
        $('#npassword').val("");
        $('#cpassword').val("");
    },

    forgotWithdrawalPassword: function ( emailEmptyMsg ){
        var email = $('#email').val();
        show_loading();
        var self = this;
        $('#div_forgot_withdrawal_pass_msg').empty();
        if(!email || email == '') {
            $('#div_forgot_withdrawal_pass_msg').append($('<div />', { 'class':'alert alert-danger', 'role':'alert' , 'html': emailEmptyMsg }));
            stop_loading();
            return;
        }

        $.post(self.site_url +'/player_center/forgotWithdrawalPassworSendEmail', {'email' : email} ,function(data){
            window.location.reload();
            stop_loading();
        });
    },

    setOverlayImg: function (image,targetOverlay){
    	var self = this;
        $('#img_overlay_'+targetOverlay).attr("src", image);
        self.openNav(targetOverlay);
    },

    updateSecretQuestion: function ( emptyFieldMsg ){
        var security_question = $("#security_question").val();
        var security_answer = $("#security_answer").val();
        var self = this;
        show_loading();
        $('#div_secret_ques_msg').empty();

        if(!security_question || security_question == '' || !security_answer || security_answer == '' || security_answer.trim() == '') {
            $('#div_secret_ques_msg').append($('<div />', { 'class':'alert alert-danger', 'role':'alert' , 'html': emptyFieldMsg }));
            stop_loading();
            return;
        }

        $.ajax({
            "url": self.site_url + '/player_center2/security/ajax_update_secret_question',
            "type": 'POST',
            "data": {
                "security_question": security_question,
                "security_answer": security_answer
            },
            "success": function(data){
                if(data.status) {
                    $('#div_secret_ques_msg').append($('<div />', { 'class':'alert alert-success', 'role':'alert' , 'html': data.message }));
                    window.location.reload();
                } else {
                    $('#div_secret_ques_msg').append($('<div />', { 'class':'alert alert-danger', 'role':'alert' , 'html': data.message }));
                }
                stop_loading();
            },
            "error": function(){
                stop_loading();
            }
        });
    },

    openNav: function(targetOverlay) {
        var self = this;
        document.getElementById("overlay_"+targetOverlay).style.width = "100%";
    },

    closeNav: function(event) {
        var self = this;
        $(event).parent().css({'width': '0%'});
    },
}; // EOF PlayerSecurity


PlayerSecurity.uploadfile = function() {
    this.targetModal = function(field) {
        initialUpload(field)
    }
    this.filesHandler = function(e) {
        assginFileList(e.files)
        e.form.reset()
    }
    this.dragHandler =function(e) {
        e.preventDefault() ;
    }
    this.dropImage = function(e) {
        e.preventDefault() ;
        assginFileList(e.dataTransfer.files) ;
    }
    this.confirmCheck = function($type) {
        if ($type == 'confirm') {

            // replace to compressed
            var thumbImage$El = $('.image-confirm-display img.thumb-image');
            var src = thumbImage$El.attr('src');
            var name = thumbImage$El.data('name');
            PlayerSecurity.uploadfile.urltoFile(src, name)
                .then(function(file){
                    fileListObj[0] = file; // replace to compressed
                }) // EOF PlayerSecurity.uploadfile.urltoFile().then()
                .then(function(file){
                    confirmFileObj.push(fileListObj[0])
                    totalImgCount += 1

                    let reader = new FileReader()
                    reader.onload = function (e) {
                        let imageBlock = $('<div>', {
                                class: "img-area new"
                            }),
                            removeImgIcon = $('<span>', {
                                class:"image-close",
                                'data-key': (confirmFileObj.length -1),
                                css:{
                                    cursor: 'pointer'
                                }
                            }).text('x'),
                            img = $('<img />', {
                                src: e.target.result,
                                class: "show-uploaded-image",
                            });
                        img.appendTo(imageBlock)
                        img.click(function(){
                            let src = $(this).attr('src')
                            PlayerSecurity.setOverlayImg(src, 'photo_id'); // Patch for preview after confirm.
                        })
                        removeImgIcon.appendTo(imageBlock)
                        removeImgIcon.click(_removeImg)
                        imageBlock.appendTo($('.image-uploaded-file'))
                    } // EOF reader.onload
                    reader.readAsDataURL(fileListObj[0])

                    fileListObj.shift();
                    showConfirmImage();
                }); // EOF PlayerSecurity.uploadfile.urltoFile().then()
        }else{
            fileListObj.shift();
            showConfirmImage();
        } // EOF if ($type == 'confirm')
    } // EOF confirmCheck

    this.accountInfoUploadFileSubmit = function () {
        $('#div_real_name_verification_msg').empty();
        $('#div_real_name_verification_pass_msg').empty();
        showUploadArea()

        var form_data = new FormData()

        $.each(confirmFileObj, function (k, v) {
            form_data.append('txtImage[]', v);
        })

        var valid = true;
        var msg$El;
        var id_card_number_error = "";
        var id_card_number = uploadForm.find("input[name='id_card_number']").val();
        if (confirmFileObj.length <= 0) {
            $('#div_real_name_verification_pass_msg').append($('<div />', { 'class': 'alert alert-danger', 'role': 'alert', 'html': PlayerSecurity.LANG_UploadAtLeastOneFile }));
            valid = false;
        }

        if (!id_card_number) {
            $('#div_real_name_verification_msg').append($('<p class="pl15 mb0"><i id="username_len" class="icon-warning red f16 mr5"></i> ' + PlayerSecurity.LANG_EmptyIdCardNumber +'</p>'));
            valid = false;
        } else {
            if (/^[a-z0-9\s\(\)\-]+$/.exec(id_card_number) == null) {
                $('#div_real_name_verification_msg').append($('<p class="pl15 mb0"><i id="username_len" class="icon-warning red f16 mr5"></i> ' + PlayerSecurity.LANG_WrongFormatIdCardNumber + '</p>'));
                valid = false;
            }

            if (id_card_number.length>36) {
                $('#div_real_name_verification_msg').append($('<p class="pl15 mb0"><i id="username_len" class="icon-warning red f16 mr5"></i> ' + PlayerSecurity.LANG_MaxLengthIdCardNumber + '</p>'));
                valid = false;
            }
        }


        // if (currentTag == 'photo_id' && !uploadForm.find("input[name='id_card_number']").val()) {
        //     $('#div_real_name_verification_pass_msg').empty();
        //     $('#div_real_name_verification_msg').empty();
        //     // $(".realname-wrong-msg").show()
        //     // msg$El = $('<ul>');
        //     // msg$El.append( $('<li>').html(PlayerSecurity.LANG_ValidIdCardNumberIsInput) );
        //     // msg$El.append( $('<li>').html(PlayerSecurity.LANG_UploadAtLeastOneFile) );
        //     $('#div_real_name_verification_pass_msg').append($('<div />', { 'class': 'alert alert-danger', 'role': 'alert', 'html': PlayerSecurity.LANG_UploadAtLeastOneFile }));
        //     $('#div_real_name_verification_msg').append($('<div />', { 'class': 'alert alert-danger', 'role': 'alert', 'html': PlayerSecurity.LANG_ValidIdCardNumberIsInput }));
        //     valid = false;
        // }

        if (!valid) {

            // MessageBox.success(  $('<div>').append(msg$El.clone()).html() // jQuery: outer html(), Ref.to https://stackoverflow.com/a/5744246
            //     , PlayerSecurity.LANG_PleaseConfirm
            //     , function(){ //callback after close

            // });

            // showFormatError()
            return false;
        }

        form_data.append('tag', currentTag)
        if (currentTag == 'photo_id') {
            form_data.append('id_card_number', uploadForm.find("input[name='id_card_number']").val())
        }

        show_loading();
        $.ajax({
            type: 'POST',
            url: fieldOptions.form_action,
            contentType: false,
            processData: false,
            data: form_data,
            success:function(response) {
                stop_loading();
                msg$El = $('<ul>');

                if(response.status == 'success') {
                    msg$El.append($('<li>').html(response.message) );
                    MessageBox.success($('<div>').append(msg$El.clone()).html() // jQuery: outer html(), Ref.to https://stackoverflow.com/a/5744246
                        , PlayerSecurity.LANG_PleaseConfirm
                        , function () { //callback after close
                            show_loading();
                            window.location.reload();
                        });
                } else {
                    msg$El.append($('<li>').html(PlayerSecurity.LANG_UpdateFailed));
                    // $('#div_real_name_verification_pass_msg').append($('<div />', { 'class': 'alert alert-danger', 'role': 'alert', 'html': response.message }));
                    MessageBox.danger($('<div>').append(msg$El.clone()).html() // jQuery: outer html(), Ref.to https://stackoverflow.com/a/5744246
                        , PlayerSecurity.LANG_PleaseConfirm
                        , function () { //callback after close
                        });
                }
                
            }
        }).always(function(){
            // window.location.reload()
        });
    }

    this.resetSetModal = function() {
        totalImgCount  = 0
        fileListObj    = []
        confirmFileObj = []
        $(".img-area").remove()
        $(".image-confirm-display > img").remove()
        $(".image-upload-confirm").removeClass('show-confirm')
    }

    let uploadModal,
        uploadForm     = '',
        currentTag     = '',
        fileListObj    = [],
        confirmFileObj = [],
        fieldOptions   = [],
        totalImgCount  = 0,
        ALLOWED_UPLOAD_FILE_FORMAT,
        LANG_UPLOAD_IMAGE_MAX_SIZE,
        UPLOAD_IMGAGES_LIMIT_COUNT

    function initialUpload(field) {
        this.resetSetModal()
        uploadForm    = $("#form_upload_image")
        uploadModal   = $("#uploadFile-modal")
        fieldOptions  = this.options.field_options[field]
        currentTag    = fieldOptions.server_tag
        serverImages  = fieldOptions.server_img
        totalImgCount = fieldOptions.server_img.length
        LANG_UPLOAD_IMAGE_MAX_SIZE = this.options.default_options.upload_img_max_size
        ALLOWED_UPLOAD_FILE_FORMAT = this.options.default_options.allowd_upload_file_format
        UPLOAD_IMGAGES_LIMIT_COUNT = this.options.default_options.upload_img_count_limit
        DoCompressImagesUri = this.options.field_options.doCompressImages.uri
        if (field == 'realname') {
            $(".realname-area").show()
        } else {
            $(".realname-area").hide()
        }

        $(".realname-wrong-msg").hide()

        if (totalImgCount > 0) {
            for (let i in serverImages) {
                let imageBlock = $('<div>', {
                        class: "img-area"
                    }),
                    img = $('<img />', {
                        src: serverImages[i],
                        class: "show-uploaded-image"
                    })

                img.click(function(){
                    let src = $(this).attr('src')
                    PlayerSecurity.setOverlayImg(src, 'photo_id')
                })
                img.appendTo(imageBlock)
                imageBlock.appendTo($('.image-uploaded-file'))
            }
        }
        if(fieldOptions.is_verified == '1') {
            showUploadedArea()
            showUploadArea(false)
        } else {
            showUploadArea()
        }

        uploadModal.find('.modal-title > span').text(fieldOptions['modal-title'])
        uploadModal.find('.modal-title > small').text('- ' + fieldOptions['modal-title-tip'])
        uploadModal.modal('show')
    }

    function assginFileList(fileList) {
        let checkFormat = true,
            fileLen = fileList.length
        var callbackOnDisallowCount = function(){
            // The total number of uploaded files exceeds 8.
            MessageBox.warning(PlayerSecurity.LANG_AlreadyReachMaximumFileNumber
                    , PlayerSecurity.LANG_Note
                    , function(){

            });// EOF MessageBox.warning

        };
        var callbackOnDisallowType = function(){

            // When the file over 10mb
            MessageBox.warning(PlayerSecurity.LANG_PleaseSelectFileTypeAndSize
                    , PlayerSecurity.LANG_Note
                    , function(){

            });// EOF MessageBox.warning

        };
        var callbackOnOverFilesize = callbackOnDisallowType;
        $.each(fileList, function (key, file) {
            fileListObj.push(file)
            if( ! checkFileFormat(file, fileLen, callbackOnDisallowCount ,callbackOnDisallowType, callbackOnOverFilesize) ) {
                checkFormat = false
                return false; // break
            }
        })

        if (checkFormat) {
            showConfirmImage()
        } else {
            clearFileListObjThenShowFormatError();
        }
    }

    function checkFileFormat(file
                , fileLen
                , callbackOnDisallowCount
                , callbackOnDisallowType
                , callbackOnOverFilesize
    ) {
        let ALLOW_TYPE = ALLOWED_UPLOAD_FILE_FORMAT,
            ALLOW_TYPE_FORMAT = ALLOW_TYPE.split("|")

        for (var i = 0; i < ALLOW_TYPE_FORMAT.length; i++) {
            ALLOW_TYPE_FORMAT[i] = 'image/'+ALLOW_TYPE_FORMAT[i];
        }

        let allowType = (ALLOW_TYPE_FORMAT.indexOf(file.type) > -1),
            allowSize = file.size < PlayerSecurity.upload_max_filesize,
            allowCount = (totalImgCount + fileLen) <= parseInt(UPLOAD_IMGAGES_LIMIT_COUNT)

        if( typeof(callbackOnDisallowCount) !== 'undefined'){
            if( ! allowCount){ // while file count over than limit.
                callbackOnDisallowCount();
            }
        }
        if( typeof(callbackOnDisallowType) !== 'undefined'){
            if( ! allowType){// while file type not configured.
                callbackOnDisallowType();
            }
        }

        if( typeof(callbackOnOverFilesize) !== 'undefined'){
            if( ! allowSize){// while file type not configured.
                callbackOnOverFilesize();
            }
        }


        return ( ! allowSize
                || ! allowType
                || ! allowCount) ? false : true
    } // EOF function checkFileFormat()

    function showFormatError() {
        /// Patch for OGP-13819, 6.4 The message should display on the page directly.
        // $(".confirm-error").addClass('show-error').delay(3000).fadeOut('slow', function(){
        //     $(this).removeClass('show-error')
        //     $(".realname-wrong-msg").hide()
        // })
    }

    function clearFileListObjThenShowFormatError(){
        fileListObj=[]
        showFormatError()
    }

    function showConfirmImage(){

        $(".image-upload-confirm").addClass('show-confirm')

        if (fileListObj.length >= 1) {
            let file = fileListObj[0],
                reader = new FileReader()

            $('.image-confirm-display > img').remove()

            reader.onload = function (e) {

                var formData = new FormData();
                formData.append('upload-files[]', file); // upload input name

                var ajax4DCIBS = $.ajax({// ajax4DCIBS, DCIBS = doCompressImagesBySize
                    type: 'POST',
                    url: DoCompressImagesUri,
                    contentType: false,
                    processData: false,
                    beforeSend : function(xhr, settings){
                        // show compressing mask
                        $('.compressing').removeClass('hide');
                        return true;
                    },
                    data: formData
                });

                ajax4DCIBS.done(function(data, textStatus, jqXHR){
                    $.each(data.compressed['upload-files'], function(indexNumber, currValue){ // should only one
                        // check size while showConfirmImage()
                        var allowSize = (parseInt(currValue.binaryLength) <= parseInt(LANG_UPLOAD_IMAGE_MAX_SIZE) );
                        if(allowSize){
                            var src = 'data:'+ currValue.mime+ ';base64, '+ currValue.base64Binary;
                            let img = $('<img />', {
                                "src": src,
                                "class": "thumb-image",
                            });
                            img.attr('data-name', currValue.name);
                            img.data('name', currValue.name);
                            img.hide().appendTo($('.image-confirm-display')).fadeIn();
                        }else{
                            // file size over limit after compressed.
                            MessageBox.danger(PlayerSecurity.LANG_PleaseTryAgain
                                    , PlayerSecurity.LANG_CompressFailed
                                    , function(){
                                        PlayerSecurity.uploadfile.confirmCheck('cancel');
                                        $('#uploadFile-tab').tab('show');
                            });// EOF MessageBox.warning

                            clearFileListObjThenShowFormatError();
                        }
                    }); // EOF $.each(data.compressed['upload-files'], funciton(){...})
                }); // EOF ajax4DCIBS.done()

                ajax4DCIBS.fail(function(jqXHR, textStatus, errorThrown){

                    MessageBox.danger(  PlayerSecurity.LANG_PleaseTryAgain // msg
                        , PlayerSecurity.LANG_CompressFailed // title
                        , function(){ //callback after close
                            // cancel confirm preview UI
                            PlayerSecurity.uploadfile.confirmCheck('cancel');
                            $('#uploadFile-tab').tab('show');
                            // orig event
                            clearFileListObjThenShowFormatError();
                    }); // EOF MessageBox.danger()

                }); // EOF ajax4DCIBS.fail()
                ajax4DCIBS.always(function(){
                    // hidden compressing mask
                    $('.compressing').addClass('hide');
                }); // EOF ajax4DCIBS.always()
            } // EOF reader.onload()
            reader.readAsDataURL(file);

        } else {
            showUploadedArea()
            $(".image-upload-confirm").removeClass('show-confirm')
        }
    }

    function _removeImg() {
        let datAttr = $(this).data(),
            imgBlock = $(this).parent()

        confirmFileObj.splice(datAttr.key, 1)
        imgBlock.remove()
        totalImgCount-=1
    }

    function showUploadArea(show = true) {
        if (show) {
            $(".upload-submit-btn").show()
            $(".nav-tabs.image-upload > li > a#uploadFile-tab").show().tab('show')
        } else {
            $(".upload-submit-btn").hide()
            $(".nav-tabs.image-upload > li > a#uploadFile-tab").hide()
            showUploadedArea()
        }
    }

    function showUploadedArea() {
        $(".nav-tabs.image-upload > li > a#uploadedFile-tab").tab('show')
    }


    /**
     * return a promise that resolves with a File instance
     *
     * @usage Usage example:
     * <code>
     * urltoFile('data:image/png;base64,......', 'a.png')
     * .then(function(file){
     *      console.log(file);
     * })
     * </code>
     */
    this.urltoFile = function(url, filename, mimeType){

        mimeType = mimeType || (url.match(/^data:([^;]+);/)||'')[1];
        return (fetch(url)
            .then(function(res){return res.arrayBuffer();})
            .then(function(buf){return new File([buf], filename, {type:mimeType});})
        );
    }// EOF urltoFile


    /**
     * Creating a Blob from a base64 string in JavaScript
     * use canvas.toBlob (asynchronous)
     *
     * Ref. to https://stackoverflow.com/a/24785136
     *
     * <code>
     * var base64Data = 'data:image/jpg;base64,/9j/4AAQSkZJRgABAQA...';
     * b64toBlob(base64Data, function(blob) {
     *      var url = window.URL.createObjectURL(blob);
     *      // do something with url
     * }, function(error) {
     *      // handle error
     * });
     * </code>
     *
     * @param {string} b64 The image binary content after base64 encode.
     * @param {script|funciton} onsuccess The script at converted while success.
     * @param {script|funciton} onerror The script at converted while fail.
     */
    this.b64toBlob = function(b64, onsuccess, onerror) {
        var img = new Image();

        img.onerror = onerror;

        img.onload = function onload() {
            var canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;

            var ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(onsuccess);
        };

        img.src = b64;
    }

    return this
}();// EOF PlayerSecurity.uploadfile

$(".txtImage").change(function(){
    PlayerSecurity.readURL(this);
});