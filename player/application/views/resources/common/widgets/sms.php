<script>
var modalHtml =
    '<div class="modal fade" id="sms-verify-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">' +
    '    <div class="modal-dialog modal-sm" role="document">' +
    '        <form id="smsVerifyForm">' +
    '            <div class="modal-content">' +
    '                <div class="modal-header">' +
    '                    <span class="modal-title" id="exampleModalLabel"><?php echo lang('Please validate sms captcha') ?></span>' +
    '                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">' +
    '                        <span aria-hidden="true">&times;</span>' +
    '                    </button>' +
    '                </div>' +
    '                <div class="modal-body">' +
    '                    <div class="row">' +
    '                        <div class="col col-md-6 col-sm-6 col-xs-6">' +
    '                            <div class="form-group">' +
    '                                <input id="sms_captcha" name="sms_captcha" type="text" class="form-control registration-field fcrecaptcha" placeholder="<?php echo lang('label.captcha'); ?>" maxlength="4" required oninvalid="this.setCustomValidity(\'<?=lang('default_html5_required_error_message')?>\')" oninput="setCustomValidity(\'\')">' +
    '                            </div>' +
    '                        </div>' +
    '                        <div class="col col-md-6 col-sm-6 col-xs-6">' +
    '                            <img id="image_sms_captcha" src="<?php echo site_url("/iframe/auth/smsCaptcha/90/40?" . random_string('alnum')); ?>" onclick="refreshSMSCaptcha();" />' +
    '                        </div>' +
    '                    </div>' +
    '                    <div class="clearfix"></div>' +
    '                </div>' +
    '                <div class="modal-footer">' +
    '                     <button type="submit" class="btn btn-primary"> <?php echo lang('Confirm submit') ?> </button>' +
    '                </div>' +
    '            </div>' +
    '        </form>' +
    '    </div>' +
    '</div>';

function refreshSMSCaptcha(){
    $('#image_sms_captcha').attr('src','<?php echo site_url('/iframe/auth/smsCaptcha/90/40'); ?>?'+ Math.random());
}

function SMS_SendVerify(callback) {
    // Init sms-verify-model
    if ($("#sms-verify-modal").length <= 0) {
        $("body").append(modalHtml);
        $('#sms-verify-modal').on('shown.bs.modal', function () {
            var modal = $(this);
            $('input', modal).focus();
        });
    } else {
        $("#sms_captcha").val("");
    }

    refreshSMSCaptcha();

    $("#sms-verify-modal").modal();

    $("#smsVerifyForm").off('submit').on('submit', function(e) {
        e.preventDefault();
        callback($("#sms_captcha").val());

        $("#sms-verify-modal").modal('hide');
    });
}

</script>

