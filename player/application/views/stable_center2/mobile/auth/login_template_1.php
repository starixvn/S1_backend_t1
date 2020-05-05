<link href="<?= $this->utils->getPlayerCmsUrl('/' . $this->utils->getPlayerCenterTemplate(FALSE) . '/css/style-mobile-login-template-1.css') ?>" rel="stylesheet"/>
<div id="login" class="login-mod">
    <div class="custom-header">
        <a href="javascript::void(0);" class="back" onclick="window.history.go(-1); return false;"><i class="icon-left-arrow"></i><?=lang("button.back")?></a>
        <div class="logo"><img src="<?=$this->utils->getAnyCmsUrl('includes/images/logo.png')?>" alt=""></div>
        <h1><?= lang('Keep up the fun and keep on winning!') ?></h1>
    </div>
    <div class="body">
        <form method="POST" action="<?=site_url('iframe/auth/login')?>" role="form" id="frm_login">
            <div class="form-group">
                <i class="icon-user1"></i>
                <input type="text" class="form-control" name="username" id="username" placeholder="<?= lang('login.Username'); ?>">
            </div>
            <ul id="dltext1" style="display:none;"><i></i><?php echo lang("Username can't be empty");?></ul>

            <div class="form-group">
                <i class="icon-password"></i>
                <input type="password" class="form-control" name="password" id="password" placeholder="<?= lang('login.Password'); ?>">
            </div>
            <ul id="dltext2" style="display:none;"><i></i><?php echo lang("Password can't be empty");?></ul>

            <div class="form-group form-btn-group">
                <button type="button" id="login_submit" class="btn btn-primary">
                    <i class="icon-login2"></i>
                    <span><?=lang('lang.logIn')?></span>
                </button>
                <?php if(!$this->utils->isEnabledFeature('hidden_login_page_contact_customer_service_area')):?>
                <div class="form-group form-link live-chat-box">
                    <?=lang('login.hint')?>
                    <a id="contact_customer_service" href="javascript:void(0)" onclick="<?=$this->utils->getLiveChatOnClick();?>"><i class="icon-bubble"></i> <?=lang('Live Chat')?></a>
                </div>
                <?php endif;?>
            </div>
            <?php if ($forget_password_enabled):?>
                <button type="button" id="forgot_pass" class="btn forgotpassword-btn">
                    <i class="icon-info"></i>
                    <span><?=lang('Forgot Password')?></span>
                </button>
            <?php endif; ?>
            <div class="form-group form-link registration-btn">
                <a href="<?=site_url('player_center/iframe_register')?>#main-content"><i class="icon-register"></i> <?=lang('lang.aregister')?></a>
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

<script type="text/javascript">
$( document ).ready(function() {
    $("#header_template").hide();
    $(".quickButtonBar").hide();

    if ($("#login").hasClass("login-mod")) {
        $("body").attr("style","overflow-y:hidden;");
    }

    var modal = $('#login-modal').modal({
        'show': false
    });

    $("#login_submit").click(function () {
        console.log("login submit");
        var dltext1 = $("#username").val();
        var dltext2 = $("#password").val();

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

                    return;
                }

                var redirect_url = "<?= (empty($redirect_url = $this->utils->getConfig("mobile_lgoin_redirect_url"))) ? $this->utils->getSystemUrl('m', '/') : site_url($redirect_url) ?>";
                //success and redirect 
                    window.location = redirect_url;
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

    $("#forgot_pass").click(function () {
        window.location = '/player_center/forgotPassword';
    });
});
</script>