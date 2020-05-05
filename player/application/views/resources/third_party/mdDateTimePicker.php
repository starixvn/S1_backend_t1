<?php
$current_lang = $this->language_function->getCurrentLanguage();

$mdtp_lang_js = 'zh-cn';
$moment_locale = 'zh-cn';
switch($current_lang){
    case Language_function::INT_LANG_ENGLISH:
        $mdtp_lang_js = NULL;
        $moment_locale = 'en';
        break;
    case Language_function::INT_LANG_CHINESE:
        $mdtp_lang_js = 'zh-cn';
        $moment_locale = 'zh-cn';
        break;
    case Language_function::INT_LANG_INDONESIAN:
        $mdtp_lang_js = 'id';
        $moment_locale = 'id';
        break;
    case Language_function::INT_LANG_VIETNAMESE:
        $mdtp_lang_js = 'vi';
        $moment_locale = 'vi';
        break;
    case Language_function::INT_LANG_KOREAN:
        $mdtp_lang_js = 'ko';
        $moment_locale = 'ko';
        break;
    case Language_function::INT_LANG_THAI:
        $mdtp_lang_js = 'th';
        $moment_locale = 'th';
        break;
}
?>
<link href="<?=$this->utils->cssUrl('mdDateTimePicker/css/themes/light/grey/mdDateTimePicker.css')?>" rel="stylesheet">
<script type="text/javascript" src="<?=$this->utils->jsUrl('mdDateTimePicker/moment.min.js') ?>"></script>
<script type="text/javascript" src="<?=$this->utils->jsUrl('mdDateTimePicker/draggabilly.pkgd.min.js') ?>"></script>
<?php if(!empty($mdtp_lang_js)): ?>
<script type="text/javascript" src="<?=$this->utils->jsUrl('mdDateTimePicker/lang/' . $mdtp_lang_js . '.js') ?>"></script>
<?php endif; ?>
<script type="text/javascript" src="<?=$this->utils->jsUrl('mdDateTimePicker/mdDateTimePicker.js') ?>"></script>
<script type="text/javascript">
    moment.locale('<?=$moment_locale?>');
</script>