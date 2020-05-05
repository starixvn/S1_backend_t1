<?php if (isset($preview) && $preview): ?>
    <style type="text/css">
        .mt100 {
            margin-top: 0 !important;
        }
    </style>
<?php endif ?>
<?php
    $messages = '';
    if (!empty($snackbar)) foreach ($snackbar as $message) $messages .= $message;
?>
<div class="container">
    <div class="cstm-mod login-mod" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h4 class="modal-title f24" id="myModalLabel"><?=lang('Login Your Account')?></h4>
                <?php
                if(!empty($currency_select_html)){
                ?>
                <?=$currency_select_html?>
                <?php
                }
                ?>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?=site_url('iframe/auth/login')?>" role="form" id="frm_login">
                    <div class="alert alert-danger hide" role="alert"><strong>Error</strong> Message</div>
                    <input type="hidden" name="referer" id="referer" class="form-control"  value="<?=isset($referer_page) ? $referer_page : null;?>">
                    <div class="form-group form-inline relative">
                        <label><i class="icon-user"></i></label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="<?=lang('system.word38')?>" value="<?= isset($username) ? $username : set_value('username');?>">
                    </div>
                    <div class="form-group form-inline relative">
                        <label><i class="icon-pass"></i></label>
                        <!-- <?php if ($forget_password_enabled):?>
                            <a href="/player_center/forgotPassword" class="forgot-pass" tabindex="-1"><?=lang('lang.forgotpasswd')?></a>
                        <?php endif; ?> -->
                        <input type="password" name="password" id="password" class="form-control" placeholder="<?=lang('sys.em3')?>" value="<?= $password_holder ?>"
                        >
                    </div>
                    <?php if ($login_captcha_enabled):?>
                    <div class="form-group form-inline relative">
                        <label><i class="glyphicon glyphicon-qrcode"></i></label>
                        <input required name='login_captcha' id='captcha' type="text" maxlength="4" class="form-control fcrecaptcha" placeholder="<?php echo lang('label.captcha'); ?>" style="width:95%">
                        <i class="fa fa-refresh" style="cursor:pointer; float: right; font-size: 1.4em; color: #888; margin-left: 3px; " aria-hidden="true" onclick="refreshCaptcha()"></i>
                        <img class="captcha" id='image_captcha' src='<?php echo site_url('/iframe/auth/captcha?' . random_string('alnum')); ?>' onclick="refreshCaptcha()">
                    </div>
                    <div class="fcrecaptcha-note hide mb20">
                        <p class="pl15 mb0">
                            <i id="referral_code" class="icon-warning red f16 mr5"></i>
                            <?=lang('captcha.required')?>
                        </p>
                    </div>
                   <?php endif; ?>

                    <div class="show">
                        <?php if ($remember_password_enabled):?>
                        <div class="remember-password-container">
                            <input type="checkbox" name="remember_me" id="remember_me" value="1" <?= $remember_me ? ' checked="1" ' : '' ?> > <?php echo lang('Remember me');?>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($forget_password_enabled['enabled']) && $forget_password_enabled['enabled']):?>
                        <div class="forgot-password-container">
                            <a href="<?=site_url('iframe_module/forget_password_select');?>" class="block text-right"><?=lang('lang.forgotpasswd');?></a>
                        </div>
                        <?php endif; ?>
                        <div class="clearfix"></div>
                    </div>

                    <br>
                    <div class="error-message <?=(! empty($messages)) ? '' : 'hide'?>"><?=$messages?></div>
                    <div class="error-message pre-submit hide"><?= lang('Login and password are both required') ?></div>
                    <button id="login_now_btn" type="submit" class="btn btn-primary login"><?php echo lang('Login Now');?>!</button>
                    <br><br>

                    <?php if($this->utils->isEnabledFeature('enable_mobile_acct_login')):?>
                    <div class="text-center">
                        <p>
                            <a href="<?=site_url('player_center/view_mobile_login')?>"><?=lang('Mobile number login')?></a>
                        </p>
                    </div>
                    <?php endif; ?>

                    <div class="show">
                        <?php if(!$this->utils->isEnabledFeature('hidden_login_page_contact_customer_service_area')):?>
                        <div class="contact-customer-service-container">
                            <?=lang('login.hint')?>
                            <a id="contact_customer_service" href="javascript:void(0)" onclick="<?=$this->utils->getLiveChatOnClick();?>"><?=lang('Contact Customer Service')?></a>
                        </div>
                        <?php endif; ?>
                        <div class="register-container">
                            <a href="<?=site_url('player_center/iframe_register')?>" class="block text-right"><?php echo lang('Free Registration');?></a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </form>
            </div>
        </div>
        <?php if( $this->utils->isEnabledFeature('enable_login_go_back_to_homepage') ):?>
            <div class="goback-home">
                <a href="<?=$this->utils->getSystemUrl('www')?>" class="goback-home-link"><i class="icon-home"></i> <?= lang('Go Back to Homepage') ?></a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ( ! isset($preview) || ! $preview): ?>
<script type="text/javascript" src="/resources/third_party/jquery-validate/1.6.0/jquery.validate.min.js"></script>
<script type="text/javascript" src="/resources/third_party/jquery-form/3.20/jquery.form.min.js"></script>
<script type="text/javascript" src="/common/js/player_center/player_login.js?v=<?= PRODUCTION_VERSION ?>"></script>
<script type="text/javascript">
    $(function(){
        PlayerLogin.init();

    });

    function callback_after_login(){
        window.location.href = window.location.href;
    }

    function refreshCaptcha(){
        //refresh
        $('#image_captcha').attr('src','<?php echo site_url('/iframe/auth/captcha'); ?>?'+Math.random());
    }


</script>
<?php endif ?>