<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// if ( ! function_exists('getPlayerTotalWallet'))
// {
//     function getPlayerTotalWalletBalance($player_id = '')
//     {
// 		$CI =& get_instance();
// 		$CI->load->model(array('player_model', 'wallet_model'));

// 		$playerTotalBalance=0;
// 		if(!empty($player_id)){
// 			$bigWallet=$CI->utils->getBigWalletByPlayerId($player_id);
// 			if(!empty($bigWallet)){
// 			    $playerTotalBalance = $CI->utils->formatCurrencyNoSym( $bigWallet['total_nofrozen'] );
// 			}
// 		}

//     	// if(!$playerMainWalletBalance = $CI->player_model->getMainWalletBalance($player_id)) $getMainWalletBalance = 0;
//     	// if(!$playerGameWalletBalance = $CI->wallet_model->sumSubWalletBy($player_id)) $playerGameWalletBalance = 0;

//     	return $playerTotalBalance;
//     }
// }

if( ! function_exists('playerTotalMainBalance') ){

	function playerTotalMainBalance( $balance_type = 'main_wallet' ){

		$loggedPlayerId = $this->utils->getLoggedPlayerId();

	    $bigWallet = $this->utils->getBigWalletByPlayerId($loggedPlayerId);

	    $balance = 0;

	    switch( $balance_type ){

	    	case 'frozen_balance':

	    		$balance = $bigWallet['main']['frozen'];

	    	break;

	    	case 'total_nofrozen':

	    		$balance = $bigWallet['main']['total_nofrozen'];

	    	break;

	    	case 'total_wallet_balance':

	    		$subwalletsBalance = array();

			    foreach ($bigWallet['sub'] as $apiId=>$subWallet) {
			        $subwalletsBalance[$apiId]=$subWallet['total_nofrozen'];
			    }

	    		$balance = array_sum($subwalletsBalance);

	    	break;

	    	default:

	    		$balance = $bigWallet['main']['total_nofrozen'] + array_sum($subwalletsBalance) + $bigWallet['main']['frozen'];

	    }

	    return $balance;

	}

}

if( ! function_exists('playerProperty') ){
    function playerProperty($player, $property){
        return (empty($player)) ? NULL : ((is_array($player) && isset($player[$property])) ? $player[$property] : $player->$property);
    }
}

if ( ! function_exists('validator_rule_builder')){
    function validator_rule_builder($column, $column_name, $rules, $type = 'ci', $errors = NULL){
        if(!is_array($rules)){
            $CI = &get_instance();

            $tmp = explode('.', $rules);
            $config = null;
            foreach($tmp as $config_key){
                $config = (NULL === $config) ? $CI->config->item($config_key) : (isset($config[$config_key]) ? $config[$config_key] : null);
            }
            $rules = $config;
        }

        if(empty($rules)){
            return NULL;
        }

        $result = [];

        foreach($rules as $rule_key => $rule_value){
            if(preg_match('/^error.*/', $rule_key)){
                continue;
            }
            $tmp_rule = NULL;
            $tmp_error = NULL;
            $tmp_attr = '';

            $tmp_error = (isset($rules['error_' . $rule_key])) ? $rules['error_' . $rule_key] : 'form.validation.invalid_' . $rule_key;
            $tmp_error = (isset($errors[$rule_key])) ? $errors[$rule_key] : $tmp_error;
            if(is_array($tmp_error)){
                if(isset($tmp_error['invalid']) && isset($tmp_error['valid'])){
                    $tmp_error = $tmp_error['invalid'];
                }else{
                    $tmp_error = 'form.validation.invalid_' . $rule_key;
                }
            }

            if(is_array($rule_value)){
                $format_args = $rule_value;
                array_unshift($format_args, $column_name);
            }else{
                $format_args = [$column_name, $rule_value];
            }

            switch($rule_key){
                case 'required':
                    $tmp_rule = ($type == 'html') ? 'required="required"' : 'required';
                    $tmp_attr = 'data-' . $rule_key . '-error="' . vsprintf(lang($tmp_error), $format_args) .'"';
                    break;
                case 'min':
                    $tmp_rule = ($type == 'html') ? 'min="' . $rule_value . '"' : 'greater_than[' . $rule_value . ']';
                    $tmp_attr = 'data-' . $rule_key . '-error="' . vsprintf(lang($tmp_error), $format_args) .'"';
                    break;
                case 'max':
                    $tmp_rule = ($type == 'html') ? 'max="' . $rule_value . '"' : 'less_than[' . $rule_value . ']';
                    $tmp_attr = 'data-' . $rule_key . '-error="' . vsprintf(lang($tmp_error), $format_args) .'"';
                    break;
                case 'min_max':
                    $tmp_rule = ($type == 'html') ? 'min="' . $rule_value[0] . '"' : 'min_length[' . $rule_value[0] . ']';
                    $tmp_rule .= ($type == 'html') ? ' max="' . $rule_value[1] . '"' : '|max_length[' . $rule_value[1] . ']';
                    $tmp_attr = 'data-min-error="' . vsprintf(lang($tmp_error), $format_args) .'"';
                    $tmp_attr .= 'data-max-error="' . vsprintf(lang($tmp_error), $format_args) .'"';
                    break;
                case 'minlength':
                    $tmp_rule = ($type == 'html') ? 'data-minlength="' . $rule_value . '"' : 'min_length[' . $rule_value . ']';
                    $tmp_attr = 'data-' . $rule_key . '-error="' . vsprintf(lang($tmp_error), $format_args) .'"';
                    break;
                case 'maxlength':
                    $tmp_rule = ($type == 'html') ? 'data-maxlength="' . $rule_value . '"' : 'max_length[' . $rule_value . ']';
                    $tmp_attr = 'data-' . $rule_key . '-error="' . vsprintf(lang($tmp_error), $format_args) .'"';
                    break;
                case 'min_max_length':
                    $tmp_rule = ($type == 'html') ? 'data-minlength="' . $rule_value[0] . '"' : 'min_length[' . $rule_value[0] . ']';
                    $tmp_rule .= ($type == 'html') ? ' data-maxlength="' . $rule_value[1] . '"' : '|max_length[' . $rule_value[1] . ']';
                    $tmp_attr = 'data-minlength-error="' . vsprintf(lang($tmp_error), $format_args) .'"';
                    $tmp_attr .= 'data-maxlength-error="' . vsprintf(lang($tmp_error), $format_args) .'"';
                    break;
                case 'regex':
                    $tmp_rule = ($type == 'html') ? 'data-regex="' . $rule_value . '"' : NULL;
                    $tmp_attr = 'data-' . $rule_key . '-error="' . vsprintf(lang($tmp_error), $format_args) .'"';
                    break;
                case 'remote':
                    $tmp_rule = ($type == 'html') ? 'data-remote="' . $rule_value . '"' : NULL;
                    $tmp_attr = 'data-' . $rule_key . '-error="' . vsprintf(lang($tmp_error), $format_args) .'"';
                    break;
                default:
                    $tmp_rule = ($type == 'html') ? 'data-' . $rule_key . '="' . $rule_value . '"' : ((empty($rule_value)) ? $rule_key : $rule_key . '[' . $rule_value . ']');
                    $tmp_attr = 'data-' . $rule_key . '-error="' . vsprintf(lang($tmp_error), $format_args) .'"';
                    break;
            }

            $result[] = $tmp_rule;
            if($type == 'html'){
                $result[] = $tmp_attr;
            }
        }

        return implode(($type == 'ci') ? '|' : ' ', $result);
    }
}

if ( ! function_exists('validator_input_tip_builder')){
    function validator_input_tip_builder($column, $column_name, $rules, $errors = NULL){
        if(!is_array($rules)){
            $CI = &get_instance();

            $tmp = explode('.', $rules);
            $config = null;
            foreach($tmp as $config_key){
                $config = (NULL === $config) ? $CI->config->item($config_key) : (isset($config[$config_key]) ? $config[$config_key] : null);
            }
            $rules = $config;
        }

        if(empty($rules)){
            return NULL;
        }

        $html = '<div class="help-block with-input-tip">';

        foreach($rules as $rule_key => $rule_value){
            if(preg_match('/^error.*/', $rule_key)){
                continue;
            }

            $tmp_error = NULL;

            $tmp_error = (isset($rules['error_' . $rule_key])) ? $rules['error_' . $rule_key] : 'form.validation.invalid_' . $rule_key;
            $tmp_error = (isset($errors[$rule_key])) ? $errors[$rule_key] : $tmp_error;

            if(is_array($rule_value)){
                $format_args = $rule_value;
                array_unshift($format_args, $column_name);
            }else{
                $format_args = [$column_name, $rule_value];
            }

            switch($rule_key){
                case 'min_max_length':
                    $html .= '<div class="with_minlength with_maxlength">';
                    if(is_array($tmp_error)){
                        if(isset($tmp_error['invalid']) && isset($tmp_error['valid'])){
                            $html .= '<span class="valid-text">' . vsprintf(lang($tmp_error['valid']), $format_args) . '</span>';
                            $html .= '<span class="invalid-text">' . vsprintf(lang($tmp_error['invalid']), $format_args) . '</span>';
                        }
                    }else{
                        $html .= '<span class="valid-text">' . vsprintf(lang($tmp_error), $format_args) . '</span>';
                        $html .= '<span class="invalid-text">' . vsprintf(lang($tmp_error), $format_args) . '</span>';
                    }
                    $html .= '</div>';
                    break;
                default:
                    $html .= '<div class="with_' . $rule_key . '">';
                    if(is_array($tmp_error)){
                        if(isset($tmp_error['invalid']) && isset($tmp_error['valid'])){
                            $html .= '<span class="valid-text">' . vsprintf(lang($tmp_error['valid']), $format_args) . '</span>';
                            $html .= '<span class="invalid-text">' . vsprintf(lang($tmp_error['invalid']), $format_args) . '</span>';
                        }
                    }else{
                        $html .= '<span class="valid-text">' . vsprintf(lang($tmp_error), $format_args) . '</span>';
                        $html .= '<span class="invalid-text">' . vsprintf(lang($tmp_error), $format_args) . '</span>';
                    }
                    $html .= '</div>';
                    break;
            }
        }

        $html .= '</div>';

        return $html;
    }
}