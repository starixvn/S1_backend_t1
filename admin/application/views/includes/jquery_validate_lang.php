<?php

if(isset($this->language_function)){
	$session_lang=$this->language_function->getCurrentLanguage();
	$jquery_validate_lang=$this->utils->convertLangToJqueryValidateLang($session_lang);
	// $this->utils->debug_log('session_lang',$session_lang,'jquery_validate_lang',$jquery_validate_lang);
	if(!empty($jquery_validate_lang)){
		echo '<script type="text/javascript" src="'.$this->utils->thirdpartyUrl('jquery-validate/localization/messages_'.$jquery_validate_lang.'.min.js').'"></script>';
	}
}
