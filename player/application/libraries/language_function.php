<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/**
 * Language function for player
 *
 * Language function library for FHI OG System
 *
 * @package		Language function
 * @author		ASRII
 * @version		1.0.0
 */

class Language_function {
	private $error = array();

	function __construct() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('session'));
		$this->ci->load->model(array('player'));
	}

	const INT_LANG_ENGLISH = '1';
	const INT_LANG_CHINESE = '2';
	const INT_LANG_INDONESIAN = '3';
	const INT_LANG_VIETNAMESE = '4';
	const INT_LANG_KOREAN = '5';
	const INT_LANG_THAI = '6';

	const PROMO_SHORT_LANG_ENGLISH = 'en';
	const PROMO_SHORT_LANG_CHINESE = 'ch';
	const PROMO_SHORT_LANG_INDONESIAN = 'id';
	const PROMO_SHORT_LANG_VIETNAMESE = 'vn';
	const PROMO_SHORT_LANG_KOREAN = 'kr';
	const PROMO_SHORT_LANG_THAI = 'th';

	const PLAYER_LANG_ENGLISH = 'English';
	const PLAYER_LANG_CHINESE = 'Chinese';
	const PLAYER_LANG_INDONESIAN = 'Indonesian';
	const PLAYER_LANG_VIETNAMESE = 'Vietnamese';
	const PLAYER_LANG_KOREAN = 'Korean';
	const PLAYER_LANG_THAI = 'Thai';

    const ISO2_LANG=[
        self::INT_LANG_ENGLISH=>'en',
        self::INT_LANG_CHINESE=>'cn',
        self::INT_LANG_INDONESIAN=>'id',
        self::INT_LANG_VIETNAMESE=>'vt',
        self::INT_LANG_KOREAN=>'kr',
        self::INT_LANG_THAI=>'th',
    ];

	/**
	 * get current language
	 *
	 * @param	data
	 * @return 	array
	 */
	function setCurrentLanguage($language) {
		$this->ci->session->set_userdata('lang', $language); //$this->ci->player->setCurrentLanguage($data);
		$this->ci->config->set_item('language', strtolower($this->getLanguage($language)));
	}

	/**
	 * get language code
	 *
	 * @return	rendered Template
	 */
	function getLanguageCode($lang) {
		return 'main';
	}

	/**
	 * get language
	 *
	 * @return	rendered Template
	 */
	function getLanguage($lang) {
        return strtolower(self::getLanguageName($lang));
	}

	/**
	 * get language
	 *
	 * @return	rendered Template
	 */
	function getIntLanguage($lang) {
		switch (strtolower($lang)) {
			case 'english':
				return self::INT_LANG_ENGLISH;
				break;
			case 'chinese':
				return self::INT_LANG_CHINESE;
				break;
			case 'indonesian':
				return self::INT_LANG_INDONESIAN;
				break;
			case 'vietnamese':
				return self::INT_LANG_VIETNAMESE;
				break;
			case 'korean':
				return self::INT_LANG_KOREAN;
				break;
			case 'thai':
				return self::INT_LANG_THAI;
				break;
			default:
				return self::INT_LANG_ENGLISH;
				break;
		}
	}

	/**
	 * get current language
	 *
	 * @param	int
	 * @return 	array
	 */
	function getCurrentLanguage() {
		$htmlLang=$this->ci->input->get('lang');
		// $htmlLang=$this->convertHtmlLang($htmlLang);

		if(!empty($htmlLang)){
			$language=$this->langStrToInt($this->convertHtmlLang($htmlLang));
		}else{
			$language = $this->ci->session->userdata('lang');
			$isForceToDefaultLanguage = $this->ci->utils->isForcePlayerCenterToDefaultLanguage();

			if (!$language || !empty($isForceToDefaultLanguage)) {
				$language = $this->ci->utils->getPlayerCenterLanguage();
			}

			if(empty($isForceToDefaultLanguage) && $this->ci->utils->isRetainCurrentLanguage()){
				$cookie_name = $this->ci->config->item('cookie_name_lang');
				if(!empty($cookie_name)){
					if(isset($_COOKIE[$cookie_name])){
						// $this->ci->utils->debug_log('cookie_name_lang', $_COOKIE[$cookie_name]);
						$language = $this->langStrToInt($_COOKIE[$cookie_name]);
					}
				}
			}
		}

		if (empty($language) || !self::isSupportLanguage($language)) {
			//default lang from config
			$default_lang = $this->ci->config->item('default_player_language');

			switch (ucfirst($default_lang)) {
				case self::PLAYER_LANG_ENGLISH:
					$this->ci->session->set_userdata('lang', '1');
					$language = '1';
					break;
				case self::PLAYER_LANG_CHINESE:
					$this->ci->session->set_userdata('lang', '2');
					$language = '2';
					break;
				case self::PLAYER_LANG_INDONESIAN:
					$this->ci->session->set_userdata('lang', '3');
					$language = '3';
					break;
				case self::PLAYER_LANG_VIETNAMESE:
					$this->ci->session->set_userdata('lang', '4');
					$language = '4';
					break;
				case self::PLAYER_LANG_KOREAN:
					$this->ci->session->set_userdata('lang', '5');
					$language = '5';
					break;
				case self::PLAYER_LANG_THAI:
					$this->ci->session->set_userdata('lang', '6');
					$language = '6';
					break;
				default:
					$this->ci->session->set_userdata('lang', '1');
					$language = '1';
					break;
			}

		}

		return $language;
	}

	function getCurrentLanguageName() {
		return $this->getLanguage($this->getCurrentLanguage());
	}

	function getCurrentLangForPromo() {
		$currLang = $this->getCurrentLanguage();
		switch ($currLang) {
			case self::INT_LANG_ENGLISH:
				$language = self::PROMO_SHORT_LANG_ENGLISH;
				break;
			case self::INT_LANG_CHINESE:
				$language = self::PROMO_SHORT_LANG_CHINESE;
				break;
			case self::INT_LANG_INDONESIAN:
				$language = self::PROMO_SHORT_LANG_INDONESIAN;
				break;
			case self::INT_LANG_VIETNAMESE:
				$language = self::PROMO_SHORT_LANG_VIETNAMESE;
				break;
			case self::INT_LANG_KOREAN:
				$language = self::PROMO_SHORT_LANG_KOREAN;
				break;
			case self::INT_LANG_THAI:
				$language = self::PROMO_SHORT_LANG_THAI;
				break;
			default:
				$language = self::PROMO_SHORT_LANG_CHINESE;
				break;
		}
		return $language;
	}

	function langStrToInt($langStr) {
		switch (strtolower($langStr)) {
			case 'english':
			case 'en':
				return '1';
				break;
			case 'chinese':
			case 'zh-cn':
				return '2';
				break;
			case 'indonesian':
			case 'idn':
			case 'in':
				return '3';
				break;
			case 'vietnamese':
			case 'vn':
				return '4';
				break;
			case 'korean':
			case 'kr':
				return '5';
				break;
			case 'thai':
			case 'th':
				return '6';
				break;
			default:
				# code...
				break;
		}
	}

	#-------------only used in player-------------
	function convertHtmlLang($htmlLang){
		switch (strtolower($htmlLang)) {
			case 'en':
				$lang = 'english';
				break;
			case 'zh-cn':
				$lang = 'chinese';
				break;
			case 'idn':
				$lang = 'indonesian';
				break;
			case 'vn':
				$lang = 'vietnamese';
				break;
			case 'kr':
				$lang = 'korean';
				break;
			case 'th':
				$lang = 'thai';
				break;
			default:
				$lang = null;
				break;
		}
		return $lang;
	}

    public function convertGameLauncherLanguage($language){
        $lang='';
        switch ($language) {
            case '1':
                $lang = 'en-us'; // english
                break;
            case '2':
                $lang = 'zh-cn'; // chinese
                break;
            case '3':
                $lang = 'id-id'; // indonesia
                break;
            case '4':
                $lang = 'vi-vn'; // vietnamese
                break;
            case '5':
                $lang = 'ko-kr'; // korean
                break;
            case '6':
                $lang = 'th-th'; // Thai
                break;
            default:
                $lang = 'en-us'; // default as english
                break;
        }
        return $lang;
    }

    static function PlayerSupportLanguages(){
        $CI = &get_instance();
        $enabled_language = $CI->operatorglobalsettings->getSettingJson('player_center_enabled_language');

        $default_languages = [
            self::INT_LANG_ENGLISH => lang(self::PLAYER_LANG_ENGLISH),
            self::INT_LANG_CHINESE => lang(self::PLAYER_LANG_CHINESE),
            self::INT_LANG_INDONESIAN => lang(self::PLAYER_LANG_INDONESIAN),
            self::INT_LANG_VIETNAMESE => lang(self::PLAYER_LANG_VIETNAMESE),
            self::INT_LANG_KOREAN => lang(self::PLAYER_LANG_KOREAN),
            self::INT_LANG_THAI => lang(self::PLAYER_LANG_THAI)
        ];

        $support_languages = [];
        if(!empty($enabled_language)){
            foreach($default_languages as $lang_code => $lang_value){
                if(!in_array($lang_code, $enabled_language)){
                    continue;
                }

                $support_languages[$lang_code] = $lang_value;
            }
        }

        return (empty($support_languages)) ? $default_languages : $support_languages;
    }

    static function isSupportLanguage($langCode){
        $support_langs = [
            self::INT_LANG_ENGLISH,
            self::INT_LANG_CHINESE,
            self::INT_LANG_INDONESIAN,
            self::INT_LANG_VIETNAMESE,
            self::INT_LANG_KOREAN,
            self::INT_LANG_THAI
        ];

        return (in_array((int)$langCode, $support_langs));
    }

    static function PlayerSupportLanguageNames(){
        $support_languages = self::PlayerSupportLanguages();
        if(empty($support_languages)) return [];

        $support_language_names = [];
        foreach($support_languages as $lang_code => $lang_value){
            $support_language_names[self::getLanguageName($lang_code)] = $lang_value;
        }

        return $support_language_names;
    }

    static public function getLanguageName($lang_code){
		switch ((int)$lang_code) {
			case self::INT_LANG_ENGLISH:
				return self::PLAYER_LANG_ENGLISH;
				break;
			case self::INT_LANG_INDONESIAN:
				return self::PLAYER_LANG_INDONESIAN;
				break;
			case self::INT_LANG_VIETNAMESE:
				return self::PLAYER_LANG_VIETNAMESE;
				break;
			case self::INT_LANG_KOREAN:
				return self::PLAYER_LANG_KOREAN;
				break;
			case self::INT_LANG_THAI:
				return self::PLAYER_LANG_THAI;
				break;
			case self::INT_LANG_CHINESE:
			default:
				return self::PLAYER_LANG_CHINESE;
				break;
		}
    }

}

/* End of file language_function.php */
/* Location: ./application/libraries/language_function.php */