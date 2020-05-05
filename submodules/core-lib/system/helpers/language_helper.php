<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Language Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/language_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Lang
 *
 * Fetches a language variable and optionally outputs a form label
 *
 * @access	public
 * @param	string	the language line
 * @param	string	the id of the form element
 * @return	string
 */
if ( ! function_exists('lang'))
{
	function lang($line, $lang = null)
	{
        static $og_current_lang = NULL;

		$CI =& get_instance();
		$CI->load->library(['language_function', 'session', 'utils']);

		$currentLang=null;
		if(empty($og_current_lang)){
            $og_current_lang = $CI->language_function->getCurrentLanguage(); # 1 = en, 2 = cn, 3 = id
            $currentLang = $og_current_lang;
        }else{
            $currentLang = $og_current_lang;
        }

		$actualLang = !empty($lang) ? $lang : $currentLang;

		if(substr($line, 0, 6) === '_json:') {
			$CI->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
			if($CI->cache->get($actualLang.'$$'.$line)){
				return $CI->cache->get($actualLang.'$$'.$line);
			}

			$jsonStr = substr($line, 6);
			$jsonArr = json_decode($jsonStr, true);

			if($jsonArr && json_last_error() == JSON_ERROR_NONE) {
				if(array_key_exists($actualLang, $jsonArr)) {
					$CI->cache->save($actualLang.'$$'.$line, $jsonArr[$actualLang]);
					return $jsonArr[$actualLang];
				} else {
					$CI->cache->save($currentLang.'$$'.$line, $jsonArr[language_function::INT_LANG_ENGLISH]);
					return $jsonArr[language_function::INT_LANG_ENGLISH];
				}
			}

			$CI->cache->save($currentLang.'$$'.$line, $jsonStr);

			return $jsonStr;

		} else {

			if (isset($lang)) {

				# GET TRANSLATION FOR THE PASSED LANGUAGE THEN RETURN BACK TO ORIGINAL LANGUAGE
				foreach (array($lang, $currentLang) as $lang_code) {

					$CI->lang->is_loaded = array();
					$CI->lang->language  = array();

					switch ($lang_code) {

						case language_function::INT_LANG_ENGLISH:
							$CI->lang->load('main', 'english'); # SETS LANGUAGE SESSION
							break;

						case language_function::INT_LANG_CHINESE:
							$CI->lang->load('main', 'chinese'); # SETS LANGUAGE SESSION
							break;

						case language_function::INT_LANG_INDONESIAN:
							$CI->lang->load('main', 'indonesian'); # SETS LANGUAGE SESSION
							break;

						case language_function::INT_LANG_VIETNAMESE:
							$CI->lang->load('main', 'vietnamese'); # SETS LANGUAGE SESSION
							break;

						case language_function::INT_LANG_KOREAN:
							$CI->lang->load('main', 'korean');
							break;

						case language_function::INT_LANG_THAI:
							$CI->lang->load('main', 'thai');
							break;

					}

					if ( ! isset($translation)) {
						$translation = $CI->lang->line($line);
					}

				}

			} else {
				$translation = $CI->lang->line($line);
			}

			return $translation;

		}

	}

	/**
	 * build lang array
	 * @param  string $en
	 * @param  string $cn
	 * @param  string $id
	 * @param  string $vt
	 * @param  string $kr
	 * @param  string $th
	 * @return array
	 */
	function buildLangDetail($en, $cn, $id=null, $vt=null, $kr=null, $th=null){
		$CI =& get_instance();
		$CI->load->library('language_function');

		if(empty($id)){
			$id=$en;
		}
		if(empty($vt)){
			$vt=$en;
		}
		if(empty($kr)){
			$kr=$en;
		}
		if(empty($th)){
			$th=$en;
		}
        return [
            Language_function::ISO2_LANG[Language_function::INT_LANG_ENGLISH]=>$en,
            Language_function::ISO2_LANG[Language_function::INT_LANG_CHINESE]=>$cn,
            Language_function::ISO2_LANG[Language_function::INT_LANG_INDONESIAN]=>$id,
            Language_function::ISO2_LANG[Language_function::INT_LANG_VIETNAMESE]=>$vt,
            Language_function::ISO2_LANG[Language_function::INT_LANG_KOREAN]=>$kr,
            Language_function::ISO2_LANG[Language_function::INT_LANG_THAI]=>$th,
        ];
	}

	function buildJsonLangFormat($en, $cn, $id=null, $vt=null, $kr=null, $th=null){
		$CI =& get_instance();
		$CI->load->library('language_function');

		if(empty($id)){
			$id=$en;
		}
		if(empty($vt)){
			$vt=$en;
		}
		if(empty($kr)){
			$kr=$en;
		}
		if(empty($th)){
			$th=$en;
		}
        $arr=[
            Language_function::INT_LANG_ENGLISH=>$en,
            Language_function::INT_LANG_CHINESE=>$cn,
            Language_function::INT_LANG_INDONESIAN=>$id,
            Language_function::INT_LANG_VIETNAMESE=>$vt,
            Language_function::INT_LANG_KOREAN=>$kr,
            Language_function::INT_LANG_THAI=>$th,
        ];

        return '_json:'.json_encode($arr, JSON_UNESCAPED_UNICODE);
	}

    function convertLangDetailToJsonLangFormat(array $langDetail){
        $en=$langDetail[Language_function::ISO2_LANG[Language_function::INT_LANG_ENGLISH]];
        $cn=$langDetail[Language_function::ISO2_LANG[Language_function::INT_LANG_CHINESE]];
        $id=$vt=$kr=$th=null;
        if(isset($langDetail[Language_function::ISO2_LANG[Language_function::INT_LANG_INDONESIAN]])){
            $id=$langDetail[Language_function::ISO2_LANG[Language_function::INT_LANG_INDONESIAN]];
        }
        if(isset($langDetail[Language_function::ISO2_LANG[Language_function::INT_LANG_VIETNAMESE]])){
            $vt=$langDetail[Language_function::ISO2_LANG[Language_function::INT_LANG_VIETNAMESE]];
        }
        if(isset($langDetail[Language_function::ISO2_LANG[Language_function::INT_LANG_KOREAN]])){
            $kr=$langDetail[Language_function::ISO2_LANG[Language_function::INT_LANG_KOREAN]];
        }
        if(isset($langDetail[Language_function::ISO2_LANG[Language_function::INT_LANG_THAI]])){
            $th=$langDetail[Language_function::ISO2_LANG[Language_function::INT_LANG_THAI]];
        }
        if(empty($id)){
            $id=$en;
        }
        if(empty($vt)){
            $vt=$en;
        }
        if(empty($kr)){
            $kr=$en;
        }
        if(empty($th)){
            $th=$en;
        }

        return buildJsonLangFormat($en, $cn, $id, $vt, $kr, $th);
    }
}

// ------------------------------------------------------------------------
/* End of file language_helper.php */
/* Location: ./system/helpers/language_helper.php */