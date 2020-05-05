<?php

require_once dirname(__FILE__) . '/abstract_affiliate_external_login_api.php';

/**
 * external_login_settings:
 * ole_sign_key
 * ole_api_url
 */
class External_login_api_ole_affiliate extends Abstract_affiliate_external_login_api{

	const SUCCESS_CODE='1';

	public function validateUsernamePassword($affiliateId, $username, $password, &$message=''){

		$success=false;

		$this->CI->utils->debug_log('username: '.$username.', password:'.$password);

		$settings=$this->getSettings();
		$key=$settings['ole_sign_key'];
		$url=$settings['ole_api_url'];
		$method='POST';

		$hash=md5($username.'|'.$password.'|'.$key);
		$params=['AffiliateCode'=>$username, 'Password'=>$password, 'Hash'=>$hash];
		$headers=['Content-Type'=>'application/json'];
		list($header, $content, $statusCode, $statusText, $errCode, $error, $obj)=$this->callHttpApi(
			$affiliateId, $url, $method, $params, null, $headers, true);

		if($statusCode>=400 || $errCode!=0){
			//error
			$message='Network Error';
		}else{
			$jsonArr=$this->CI->utils->decodeJson($content);
			if(!empty($jsonArr)){
				$returnCode=$jsonArr['ReturnCode'];
				if($returnCode==self::SUCCESS_CODE){
					//check currency?
					$success=true;
				}else{
					switch ($returnCode) {
						case 104:
							$message='One of request parameter is empty';
							break;
						case 105:
							$message='Invalid Hash';
							break;
						case 115:
							$message='Affiliate status is not active';
							$success=true;
							break;
						// case 109:
						// 	$message='Member is not exist';
						// 	break;
						case 113:
							$message='Password is not correct';
							break;
						case 999:
							$message='General Error';
							break;
					}
				}
			}
		}

		return $success;
	}

}
