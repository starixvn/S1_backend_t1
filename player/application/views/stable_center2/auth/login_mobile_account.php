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
                <p class="mb0">
                    <a href="<?=site_url('iframe/auth/login')?>"><?=lang('Login username account')?></a>
                </p>
            </div>
            <div class="modal-body">
                <form id="frm_login">
                    <div class="alert alert-danger hide" role="alert"><strong>Error</strong> Message</div>

                    <div class="form-group form-inline relative">
                        <label><i class="fa fa-mobile-phone"></i></label>
                        <a href="javascript:void(0);" class="send-sms" onclick="PlayerLoginMobileAcct.sendVerificationCode()" tabindex="-1" id="btn_send_sms_code"><?=lang('Send SMS Code')?></a>
                        <input type="text" name="login_mobile_number" id="login_mobile_number" class="form-control" placeholder="<?=lang('Mobile Number')?>" value="<?=set_value('login_mobile_number');?>">
                    </div>
                    <div class="form-group form-inline relative">
                        <label><i class="fa fa-key"></i></label>
                        <input type="text" name="sms_verify_code" id="sms_verify_code" class="form-control" placeholder="<?=lang('SMS Code')?>">
                    </div>
                        
                    <div class="error-message <?=(! empty($messages)) ? '' : 'hide'?>"><?=$messages?></div>
                    <button type="submit" class="btn btn-primary login" onclick="PlayerLoginMobileAcct.loginMobileAcct()"><?php echo lang('Login Now');?>!</button>
                </form>
                <div class="row">
                    <div class="col-md-6">
                        <?php if ($remember_password_enabled):?>
                            <div class="checkbox pl10">
                                <label>
                                    <input type="checkbox" name="remember_me" id="remember_me"> <?php echo lang('Remember me');?>
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="pr10">
                            <a href="<?=site_url('player_center/iframe_register')?>" class="mt10 block text-right"><?php echo lang('Free Registration');?></a>
                        </div>
                    </div>
                </div>
                <p class="pl10 pr10 pt20"><?=lang('login.hint')?><a id="contact_customer_service" href="javascript:void(0)" onclick="<?=$this->utils->getLiveChatOnClick();?>"><?=lang('Contact Customer Service')?></a></p>
            </div>
        </div>
    </div>
</div>

<?php if ( ! isset($preview) || ! $preview): ?>
<script type="text/javascript" src="/resources/third_party/jquery-validate/1.6.0/jquery.validate.min.js"></script>
<script type="text/javascript" src="/resources/third_party/jquery-form/3.20/jquery.form.min.js"></script>
<script type="text/javascript" src="<?=base_url() . $this->utils->getPlayerCenterTemplate()?>/js/player_center/player_login_mobile_account.js"></script>
<script type="text/javascript">
    $(function(){
        PlayerLoginMobileAcct.msgSendCodeSuccess = "<?=lang('Sent code to SMS')?>";
        PlayerLoginMobileAcct.msgSendAgain = "<?=lang('Send Again')?>";
        PlayerLoginMobileAcct.msgEmptyNumber = "<?=lang('Please fill in mobile number')?>";
        PlayerLoginMobileAcct.msgSendCodeFail = "<?=lang('Send SMS code failed')?>";
        PlayerLoginMobileAcct.msgLoginFail = "<?=lang('Login failed, try it later')?>";
        PlayerLoginMobileAcct.smsCooldownTime = "<?=$this->utils->getConfig('sms_cooldown_time')?>";
        PlayerLoginMobileAcct.init();
    });
</script>
<?php endif ?>