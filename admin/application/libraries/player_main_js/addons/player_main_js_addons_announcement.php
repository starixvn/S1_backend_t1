<?php
/**
 * player_main_js_addons_announcement.php
 *
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 */
class Player_main_js_addons_announcement extends Player_main_js_addons_abstract {
    public function isEnabled(){
        return TRUE;
    }

    public function variables(){
        $this->CI->load->model(['cms_model']);

        $variables['announcement'] = $this->CI->cms_model->getAllNews(NULL, NULL, 'date desc');
        $variables['announcement_option'] = $this->CI->operatorglobalsettings->getSettingIntValue('announcement_option');
        $variables['auto_popup_announcements_on_the_first_visit'] = $this->CI->utils->isEnabledFeature('auto_popup_announcements_on_the_first_visit');

        return $variables;
    }
}