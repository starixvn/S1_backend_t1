<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="renderer" content="webkit" />
<?php
$login = array(
    'name' => 'login',
    'id' => 'login',
    'value' => set_value('login'),
    'maxlength' => 80,
    'size' => 30,
);
$password = array(
    'name' => 'password',
    'id' => 'password',
    'size' => 30,
);
$language = array(
    'name' => 'language',
    'id' => 'language',
    'size' => 30,
);
$otp_code = array(
    'name' => 'otp_code',
    'id' => 'otp_code',
    'value' => set_value('otp_code'),
    'maxlength' => 80,
    'size' => 30,
);
?>
<title><?= htmlspecialchars($company_title); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<script type="text/javascript" src="<?=$this->utils->jsUrl('jquery-2.1.4.min.js')?>"></script>
<script type="text/javascript" src="<?=$this->utils->jsUrl('bootstrap.min.js')?>"></script>
<script type="text/javascript" src="<?=$this->utils->jsUrl('pub/pubutils.js')?>"></script>

<?php
    $user_theme = !empty($this->session->userdata('admin_theme')) ? $this->session->userdata('admin_theme') : $this->config->item('sbe_default_theme');
    if($_SERVER['SERVER_NAME']=='admin.vip-win007.com'){
       $user_theme = $this->session->userdata['admin_theme'] = "win007";
    }
?>
<link href="<?=$this->utils->cssUrl('themes/bootstrap.' . $user_theme . '.css')?>" rel="stylesheet">
<link href="<?=$this->utils->cssUrl('font-awesome.min.css')?>" rel="stylesheet">
<link href="<?=$this->utils->cssUrl('template.css')?>" rel="stylesheet">

<?php if($this->utils->getConfig('use_new_sbe_color')){?>
    <link rel="stylesheet" type="text/css" href="<?=$this->utils->cssUrl('newSBEColor.css')?>">
<?php }?>

<script type="text/javascript">

//  var _paq = _paq || [];
//  _paq.push(['trackPageView']);
//  _paq.push(['enableLinkTracking']);
//  (function() {
//    var u="//stat.tripleonetech.net/";
//    _paq.push(['setTrackerUrl', u+'piwik.php']);
//    _paq.push(['setSiteId', '<?php echo $this->utils->getConfig("admin_stat_site_id"); ?>']);
//    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
//    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
//  })();

</script>


</head>
<body data-theme="<?=$user_theme?>">
<div class="container" style="padding-top:5%; ">
    <div class="col-md-4 col-md-offset-4">
<?php

if ($this->session->userdata('result') == 'warning') {
    ?>
    <div class="alert alert-warning alert-dismissible alert_float" role="alert" id="alert-success">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <?=$this->session->userdata('message')?>
    </div>
<?php

    $this->session->unset_userdata('result');
}
?>
        <div class="panel <?=$this->utils->getConfig('use_new_sbe_color') ? 'login-border' : 'panel-primary' ?>">
            <div class="panel-heading"  <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="padding-top:8px; padding-bottom:9px;"' : '' ?>>
                <b style="<?=$this->utils->getConfig('use_new_sbe_color') ? 'font: Bold 22px/20px Athelas' : 'font-size:1.6em;font-family:calibri' ?>;">
                    <?php  if (!empty($logo_icon)): ?>
                        <?php if($this->utils->getConfig('use_new_sbe_color')){?>
                            <img src='<?=$this->utils->imageUrl($logo_icon)?>' height="38px;"/>
                        <?php }else{?>
                            <img src='<?=$this->utils->imageUrl($logo_icon)?>' width="30px;"/><?= htmlspecialchars($company_title); ?>
                        <?php }?>
                        <?php else: ?>
                        <img src='<?=$this->utils->getDefaultLogoUrl()?>' width="<?=$this->utils->getConfig('use_new_sbe_color') ? '47' : '30' ?>px;"/>&nbsp;<?= htmlspecialchars($company_title); ?>
                    <?php endif; ?>
                </b>
            </div>
            <div class="panel-body" style="padding-bottom:0;">
                <?php echo form_open($this->uri->uri_string(), ['onsubmit'=>'return validateForm();']); ?>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-user"></span>
                        </div>
                            <?php echo form_input($login, "", "class='form-control' placeholder='" . lang('sys.vu18') . "'", ""); ?>
                    </div>
                    <?php echo form_error($login['name'], '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-lock" id="showVisible" value="0"> </span>
                        </div>
                        <?php echo form_password($password, "", "class='form-control' placeholder='" . lang('sys.em3') . "'", ""); ?>
                    </div>
                    <?php echo form_error($password['name'], '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-globe"></span>
                        </div>

                        <select class="form-control" onchange="changeLanguage(this.value);">
                            <option value="1" <?php echo ($this->session->userdata('login_lan') == "1") ? 'selected' : '' ?> >English</option>
                            <option value="2" <?php echo ($this->session->userdata('login_lan') == "2") ? 'selected' : '' ?> >中文</option>
                            <option value="3" <?php echo ($this->session->userdata('login_lan') == "3") ? 'selected' : '' ?> >Indonesian</option>
                            <option value="4" <?php echo ($this->session->userdata('login_lan') == "4") ? 'selected' : '' ?> >Vietnamese</option>
                            <option value="5" <?php echo ($this->session->userdata('login_lan') == "5") ? 'selected' : '' ?> >Korean</option>
                            <option value="6" <?php echo ($this->session->userdata('login_lan') == "6") ? 'selected' : '' ?> >Thai</option>
                        </select>
                    </div>

                </div>
                <?php
                if(!empty($currency_select_html)){
                ?>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <span class="fa fa-money"></span>
                        </div>
                        <?=$currency_select_html?>
                    </div>
                </div>
                <?php
                }
                ?>
                <?php
                if($this->utils->isEnabledFeature('enable_otp_on_adminusers')){
                ?>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <span class="fa fa-key"></span>
                        </div>
                        <?php echo form_input($otp_code, "", "class='form-control' placeholder='" . lang('2FA Code') . "'", ""); ?>
                    </div>
                    <?php echo form_error($otp_code['name'], '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                </div>
                <?php
                }
                ?>

                <span class="help-block" style="color:#ff6666;"><?=isset($errors['login']) ? $errors['login'] : ''?></span>
                <span class="help-block" style="color:#ff6666;"><?=isset($errors['password']) ? $errors['password'] : ''?></span>
                <span class="help-block" style="color:#ff6666;"><?=isset($errors['currency']) ? $errors['currency'] : ''?></span>

                <center>
                    <div class="form-group">
                        <?php if($this->utils->getConfig('use_new_sbe_color')){
                            echo form_submit('submit', lang('lang.logIn'), "style='text-transform:uppercase; width:30%;' class='btn btn-scooter btn-block' $disabled", "");
                        }else{
                            echo form_submit('submit', lang('lang.logIn'), "style='text-transform:uppercase; width:30%;' class='btn btn-primary btn-block' $disabled", "");
                        }?>
                    </div>
                </center>

                <?php echo form_close(); ?>
            </div>

        </div>
        <div class="well-sm text-center" style="margin-bottom:21px;"><small><strong><?php echo lang('best_user_experience_browser'); ?></strong></small></div>
    </div>

    <?php if (isset($contact_email) || isset($contact_skype) || !empty($contact_website)): ?>
        <div class="col-md-4 col-md-offset-4">

        <div class="panel <?=$this->utils->getConfig('use_new_sbe_color') ? 'login-border' : 'panel-primary' ?>
                  <i class="glyphicon glyphicon-chevron-up" text-center contact">
                <div class="panel-heading" <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="padding-top:6px; padding-bottom:7px; "' : '' ?>>
                    <h4><b <?=$this->utils->getConfig('use_new_sbe_color') ? 'style="font:Bold 18px/20px Athelas;"' : '' ?>><?php echo lang('header.contactus'); ?></b></h4>
                </div>
                <div class="panel-body" style="padding-bottom:0;">
                    <?php if (isset($contact_email)): ?>
                        <p class='email'><?php echo lang('lang.email'); ?>: <b class="<?=$this->utils->getConfig('use_new_sbe_color') ? 'login-text-info' : 'text-info' ?>"><?= htmlspecialchars($contact_email) ?></b></p>
                    <?php endif ?>
                    <?php if (isset($contact_skype)): ?>
                        <p class='skype'><?php echo lang('aff.ai78'); ?>: <b class="<?=$this->utils->getConfig('use_new_sbe_color') ? 'login-text-info' : 'text-info' ?>"><?= htmlspecialchars($contact_skype) ?></b></p>
                    <?php endif ?>
                    <?php if (!empty($contact_website)): ?>
                        <?php if (isset($contact_website['display_name']) && isset($contact_website['url'])): ?>
                        <p class='website'><?php echo lang('aff.ai21'); ?>: <b class="<?=$this->utils->getConfig('use_new_sbe_color') ? 'login-text-info' : 'text-info' ?>"><a href="<?= $contact_website['url'] ?>" target="_blank"><?= htmlspecialchars($contact_website['display_name']) ?></a></b></p>
                        <?php endif ?>
                    <?php endif ?>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<?php echo $this->utils->getDevVersionInfo(); ?>
<style type="text/css">
    body{
        padding-top: 0px !important;
    }
</style>
<script type="text/javascript">
var base_url = "<?php echo site_url("/") ?>";

<?php
$langKeyList=['Changing Currency', 'Change Currency Failed'];
?>
(function(){
    <?php foreach ($langKeyList as $key) { ?>
    _pubutils.lang['<?=$key?>']="<?=lang($key)?>";
    <?php }?>
})();

function changeLanguage(value) {
    window.location = base_url + "auth/setCurrentLanguage/" + value;
}

function validateForm(){
    // if(changing_currency){
    //  alert("<?=lang('Sorry, still waiting for changing currency.please try again later')?>");
    //  return false;
    // }
    return true;
}

//show password
$('#showVisible').click(function() {
    var check = document.getElementById('showVisible');
    var password = document.getElementById('password');

    if(check.value == 0) {
        check.value = 1;
        password.setAttribute("type", "text");
    } else {
        check.value = 0;
        password.setAttribute("type", "password");
    }
});

//should be array
var donot_auto_redirect_to_https_list=<?=json_encode($this->utils->getConfig('donot_auto_redirect_to_https_list'))?>;
var auto_redirect_to_https_list=<?=json_encode($this->utils->getConfig('auto_redirect_to_https_list'))?>;

_pubutils.checkAndGoHttps(auto_redirect_to_https_list, donot_auto_redirect_to_https_list);

</script>
</body>
</html>
