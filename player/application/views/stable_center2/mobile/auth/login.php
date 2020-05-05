<div class="panel">
    <div class="panel-body">
        <form method="POST" action="<?=site_url('iframe/auth/login')?>" role="form" id="frm_login">
            <div class="login-banner"><!-- Provide designer use css to add banner --></div>
            <div class="input">
                <input type="hidden" name="referer" id="referer" class="form-control"  value="<?=$referer_page;?>">
                <input id="dl1" name="username" placeholder="<?php echo lang('login.Username'); ?>" type="text" class="input_01" value="<?= isset($username) ? $username : ''; ?>">
                <ul id="dltext1"><i></i><?php echo lang("Username can't be empty");?></ul>
                <input id="dl2" name="password" placeholder="<?php echo lang('login.Password'); ?>" type="password" class="input_01" value="<?= $password_holder ?>" >
                <ul id="dltext2"><i></i><?php echo lang("Password can't be empty");?></ul>
                <?php if(!empty($currency_select_html)){ ?>
                <?=$currency_select_html?>
                <?php }?>
                <?php if ($this->operatorglobalsettings->getSettingIntValue('captcha_login')):?>
                    <div style="position: relative" class="captcha hide">
                        <img id='image_captcha' src='<?php echo site_url(' /iframe/auth/captcha? ' . random_string('alnum ')); ?>' onclick="refreshCaptcha()">
                        <input required name='login_captcha' id='captcha' disabled="disabled" type="text" class="input_01" placeholder="<?php echo lang('label.captcha'); ?>">
                    </div>
                    <ul id="dltext3"><i></i><?=lang('captcha.required')?></ul>
                <?php endif; ?>
            </div>
            <div class="show">
                <?php if ($remember_password_enabled) : ?>
                <div class="remember-password-container">
                    <input type="checkbox" name="remember_me" id="remember_me" value="1" <?= $remember_me ? ' checked="1" ' : '' ?> > <?php echo lang('Remember me');?>
                </div>
                <?php endif; ?>
                <?php if ($forget_password_enabled):?>
                <div class="forgot-password-container">
                    <a href="/player_center/forget_password_select" class="forgot-pass block text-right" ><?=lang('lang.forgotpasswd')?></a>
                </div>
                <?php endif; ?>
                <div class="clearfix"></div>
            </div>

            <div class="login_btn hy" id="login"><?=lang('Login Now')?></div>
            <?php if($this->utils->isEnabledFeature('enable_mobile_acct_login')):?>
            <div class="lefttext">
                <p>
                    <a class="threepage" href="<?=site_url('/iframe/auth/login_by_mobile')?>" style="cursor:pointer;"><?php echo lang('Mobile number login');?></a>
                </p>
            </div>
            <?php endif; ?>

            <div class="show">
                <?php if(!$this->utils->isEnabledFeature('hidden_login_page_contact_customer_service_area')):?>
                <div class="contact-customer-service-container">
                    <?=lang('login.hint')?>
                    <a id="contact_customer_service" href="javascript:void(0)" onclick="<?=$this->utils->getLiveChatOnClick();?>"><?=lang('Contact Customer Service')?></a>
                </div>
                <?php endif;?>
                <div class="register-container">
                    <a class="block text-right" href="<?=site_url('player_center/iframe_register')?>"><?=lang('Free Registration')?></a>
                </div>
                <div class="clearfix"></div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="login-modal" role='dialog'>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-close" data-dismiss="modal"><?php echo lang('Close'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<style type="text/css">
#image_captcha{
    width: 39%;
    position: absolute;
    top: 9px;
    right: 0;
}
</style>
<?php
if(empty($referer_page)){
    $referer_page = $this->utils->getConfig("mobile_lgoin_redirect_url");
    if(empty($referer_page)){
        $referer_page=$this->utils->getSystemUrl('m');
    }
}
?>
<script>
$(document).ready(function () {
    var btn = $(".user_btn,.menu_btn,.menu_btn_two");
    var menu = $(".menu_right,.menu_left");
    var leftmove = "left_move";
    var mmove = "menu_move";
    var leftm = $(".menu_left");
    var rightm = $(".menu_right");
    var tap = "click"; //"touchstart";//
    //换页效果

    var modal = $('#login-modal').modal({
        'show': false
    });

    $("#login").click(function () {
        var dltext1 = $("#dl1").val();
        var dltext2 = $("#dl2").val();
        var captcha = ($("#captcha").length) ? $("#captcha").val() : '';

        if (dltext1.length == 0) {
            $("#dltext1").show();
            return false;
        } else {
            $("#dltext1").hide();
        }
        if (dltext2.length == 0) {
            $("#dltext2").show();
            return false;
        } else {
            $("#dltext2").hide();
        }

        if($("#captcha").length){
            if (!$('.captcha').hasClass('hide') && captcha.length == 0) {
                $("#dltext3").show();
                return false;
            } else {
                $("#dltext3").hide();
            }
        }

        var ajax_options = {
            url: $('#frm_login').attr('action'),
            type: 'POST',
            dataType: 'json',
            data: $('#frm_login').serialize(),
            success: function (data) {
                if (data.status == "error") {
                    var message = JSON.parse(data.msg);
                    show_error_in_modal();
                    $('button.btn-close', modal).removeClass('disabled');
                    $('button.btn-close', modal).prop('disabled', false);
                    $('button.btn-close', modal).removeAttr('disabled');

                    if(typeof message == "object"){
                        var ul = $('<ul>');

                        $.each(message, function(key, value){
                            switch(key){
                                case "login":
                                    <?php if($this->utils->isEnabledFeature('responsible_gaming')):?>
                                    ul.append($('<li>').text(value));
                                    <?php else:?>
                                    ul.append($('<li>').text("<?=lang('macaopj.auth.user_not_exists')?>"));
                                    <?php endif;?>
                                break;
                                case "password":
                                    ul.append($('<li>').text("<?=lang('macaopj.auth.password_incorrect')?>"));
                                break;
                                default:
                                    ul.append($('<li>').text(value));
                                break;
                            }
                        });

                        $('.modal-body', modal).html(ul);
                    }else{
                        $('.modal-body', modal).html(data.msg);
                    }

                    $('.captcha').removeClass('hide');
                    $('#captcha').val('');
                    $('#captcha').prop('disabled', false);
                    $('#image_captcha').attr('src', '<?=site_url('/iframe/auth/captcha')?>?' + Math.random());
                    // location.reload(true);
                    return;
                }else{
                    //success and redirect
                    window.location.href = "<?=$referer_page?>";
                }
            }
        };

        function show_error_in_modal () {
            modal.off('show.bs.modal').on('show.bs.modal', function(){
                $('button.btn-close', modal).addClass('disabled');
                $('button.btn-close', modal).prop('disabled', true);
                $('button.btn-close', modal).attr('disabled', 'disabled');
                $('.modal-body', modal).html('<img class="loading" src="/<?=$this->utils->getPlayerCenterTemplate(FALSE)?>/images/loading.gif">');
            });
            modal.off('hide.bs.modal').on('hide.bs.modal', function(){
                $('.modal-body', modal).html('');
            });
            modal.modal('show');
        }

        $.ajax(ajax_options);
    });
});
</script>