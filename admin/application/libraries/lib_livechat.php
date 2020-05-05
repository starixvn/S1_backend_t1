<?php
/**
 *
 * required mod: mcrypt
 *
	$chat_options =[
	    'admin_login_url'=>'https://live.chatchat365.local/index.php/user/autologin',
	    'admin_login_secret'=>'',
	    'encrypt_key1'=>'',  // Encryption key, min length 40 in Start a chat form settings
-> Additional form settings
	    'encrypt_key2'=>'',  // Additional encryption key, min length 40 in Start a chat form settings
-> Additional form settings
	    'api_key'=>'', Rest API -> API Key
	    'api_secret'=>'', Rest API -> API Key
	    'www_chat_options'=>[
	        'widget_height' => 340, 'widget_width' => 300,
	        'popup_height' => 520, 'popup_width' => 500,
	        'theme'=>1, 'department'=>1, "survey_id"=>null,
	        'lang'=>'chn', // chn, eng
		    'onlylink'=>false,
		    'load_livechat_link_selector'=>'.load_livechat',
		    'popup_livechat_link_selector'=>'.popup_livechat',
	    ],
	    'www_chat_host'=> 'live.chatchat365.local',
	    'www_chat_https_enabled'=>false,
	];
 */
class Lib_livechat{

	public function __construct() {
		parent::__construct();
	}

	# Private key
	public static $salt = 'ZfTfbip&_F-f8_df)Ha0gahptzLN7ROi%gy';

	public static $secretHash = '';

	public static function setSecretHash($secretHash) {
		self::$secretHash = $secretHash;
	}

	public static function encryptBase64($plain, $key = null, $hmacSalt = null) {
		return base64_encode(self::encrypt($plain, $key, $hmacSalt));
	}

	# Encrypt a value using AES-256.
	public static function encrypt($plain, $key, $hmacSalt = null) {

		// if (empty($key)) {
		// 	$key = sha1(self::$secretHash . self::$salt);
		// }

		self::_checkKey($key, 'encrypt()');

		if ($hmacSalt === null) {
			$hmacSalt = sha1(self::$salt . self::$secretHash . self::$salt);
		}

		$key = substr(hash('sha256', $key . $hmacSalt), 0, 32); # Generate the encryption and hmac key

		$algorithm = MCRYPT_RIJNDAEL_128; # encryption algorithm
		$mode = MCRYPT_MODE_CBC; # encryption mode

		$ivSize = @mcrypt_get_iv_size($algorithm, $mode); # Returns the size of the IV belonging to a specific cipher/mode combination
		$iv = @mcrypt_create_iv($ivSize, MCRYPT_DEV_URANDOM); # Creates an initialization vector (IV) from a random source
		$ciphertext = $iv . @mcrypt_encrypt($algorithm, $key, $plain, $mode, $iv); # Encrypts plaintext with given parameters
		$hmac = hash_hmac('sha256', $ciphertext, $key); # Generate a keyed hash value using the HMAC method
		return $hmac . $ciphertext;
	}

	# Check key
	protected static function _checkKey($key, $method) {
		if (strlen($key) < 32) {
			echo "Invalid key $key, key must be at least 256 bits (32 bytes) long.";die();
		}
	}

	# Decrypt a value using AES-256.
	public static function decrypt($cipher, $key, $hmacSalt = null) {
		// if (empty($key)) {
		// 	$key = sha1(self::$secretHash . self::$salt);
		// }

		self::_checkKey($key, 'decrypt()');

		if (empty($cipher)) {
			echo 'The data to decrypt cannot be empty.';die();
		}

		if ($hmacSalt === null) {
			$hmacSalt = sha1(self::$salt . self::$secretHash . self::$salt);
		}

		$key = substr(hash('sha256', $key . $hmacSalt), 0, 32); # Generate the encryption and hmac key.

		# Split out hmac for comparison
		$macSize = 64;
		$hmac = substr($cipher, 0, $macSize);
		$cipher = substr($cipher, $macSize);

		$compareHmac = hash_hmac('sha256', $cipher, $key);
		if ($hmac !== $compareHmac) {
			return false;
		}

		$algorithm = MCRYPT_RIJNDAEL_128; # encryption algorithm
		$mode = MCRYPT_MODE_CBC; # encryption mode
		$ivSize = @mcrypt_get_iv_size($algorithm, $mode); # Returns the size of the IV belonging to a specific cipher/mode combination

		$iv = substr($cipher, 0, $ivSize);
		$cipher = substr($cipher, $ivSize);
		$plain = @mcrypt_decrypt($algorithm, $key, $cipher, $mode, $iv);
		return rtrim($plain, "\0");
	}
	/**
	 *
	 *
	 *
	 */
	public static function getChatJs($chat_options, $userInfo=null){

		$username = 'Guest' . self::randomString();
		$userInfoEncypted = null;

		if($userInfo){
			$username=$userInfo['username'];
			$userInfoEncypted = self::encryptBase64(json_encode($userInfo), $chat_options['encrypt_key1'], $chat_options['encrypt_key2']);
		}

		// $username=$userInfo['username'];
		// $userInfoEncypted = self::encryptBase64(json_encode($userInfo), $chat_options['encrypt_key1'], $chat_options['encrypt_key2']);
		// $position = $this->utils->getConfig('live_chat_options');
		// $options=$position
		$options=$chat_options['www_chat_options'];
		$position=$options;

		$host = $chat_options['www_chat_host']; //'livechat.smartbackend.com';

		$survey_id=null;
		$theme=null;
		$department=null;
		if(empty($theme) && isset($options['theme']) && !empty($options['theme'])){
			$theme=$options['theme'];
		}
		if(empty($department) && isset($options['department']) && !empty($options['department'])){
			$department=$options['department'];
		}
		if(empty($survey_id) && isset($options['survey_id']) && !empty($options['survey_id'])){
			$survey_id=$options['survey_id'];
		}
		$lang=$options['lang'];

		$default_theme= $theme ? "/(theme)/".$theme : '' ;
		$default_department = $department ? "/(department)/".$department : '';
		$default_survey=$survey_id ? '/(survey)/'.$survey_id : '';
		$onlylink= isset($options['onlylink']) && $options['onlylink'] ? 'true' : 'false';
		$load_livechat_link_selector= isset($options['load_livechat_link_selector']) ? $options['load_livechat_link_selector'] : '.load_livechat' ;
		$popup_livechat_link_selector= isset($options['popup_livechat_link_selector']) ? $options['popup_livechat_link_selector'] : '.popup_livechat' ;
		$pop_embed_livechat_link_selector=isset($options['pop_embed_livechat_link_selector']) ? $options['pop_embed_livechat_link_selector'] : '.pop_embed_livechat' ;
		// $this->debug_log('username', $username, 'userInfo', $userInfo, 'position', $position,
			// 'default_theme',$default_theme, 'host', $host);

		$link='//'.$host.'/index.php/'.$lang.'/chat/getstatus/(click)/internal/(position)/bottom_right/(ma)/br/(top)/350/(units)/pixels/(leaveamessage)/true'.$default_department.$default_theme.$default_survey;

		$standalone_link=self::getStandaloneChatLink($chat_options, $userInfo);

		//http://zeptojs.com/
		$livechatjs = <<<EOD

var LHCChatOptions = {};

LHCChatOptions.$=function(p,e,l,h,q,n,k,b,f,g,d,c){c=function(a,b){return new c.i(a,b)};c.i=function(a,m){l.push.apply(this,a?a.nodeType||a==p?[a]:""+a===a?/</.test(a)?((b=e.createElement(m||"q")).innerHTML=a,b.children):(m&&c(m)[0]||e).querySelectorAll(a):/f/.test(typeof a)?/c/.test(e.readyState)?a():c(e).on("DOMContentLoaded",a):a:l)};c.i[d="prototype"]=(c.extend=function(a){g=arguments;for(b=1;b<g.length;b++)if(d=g[b])for(f in d)a[f]=d[f];return a})(c.fn=c[d]=l,{on:function(a,c){a=a.split(h);this.map(function(d){(h[b=a[0]+(d.b$=d.b$||++q)]=h[b]||[]).push([c,a[1]]);d["add"+n](a[0],c)});return this},off:function(a,c){a=a.split(h);d="remove"+n;this.map(function(e){if(b=(g=h[a[0]+e.b$])&&g.length)for(;f=g[--b];)c&&c!=f[0]||a[1]&&a[1]!=f[1]||(e[d](a[0],f[0]),g.splice(b,1));else!a[1]&&e[d](a[0],c)});return this},is:function(a){b=this[0];return(b.matches||b["webkit"+k]||b["moz"+k]||b["ms"+k]||b["o"+k]).call(b,a)}});return c}(window,document,[],/\.(.+)/,0,"EventListener","MatchesSelector");

LHCChatOptions.attr_prefill = new Array();
LHCChatOptions.attr_prefill.push({'name':'username','value':'{$username}'});

LHCChatOptions.attr = new Array();
LHCChatOptions.attr.push({'name':'userjsoninfo','value':'{$userInfoEncypted}','type':'hidden','size':0,'encrypted':true});

LHCChatOptions.attr_online = new Array();
LHCChatOptions.attr_online.push({'name':'username','value':'{$username}'});

LHCChatOptions.opt = {widget_height:{$position['widget_height']},
  widget_width:{$position['widget_width']},
  popup_height:{$position['popup_height']},
  popup_width:{$position['popup_width']}
};

LHCChatOptions.loadLivechat=function(start_on_loaded){
var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
var referrer = (document.referrer) ? encodeURIComponent(document.referrer.substr(document.referrer.indexOf('://')+1)) : '';
var location  = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : '';
po.src = '{$link}?r='+referrer+'&l='+location;
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
if(start_on_loaded){
	LHCChatOptions.start_on_loaded=true;
	// LHCChatOptions.$(s).trigger('click');
	// LHCChatOptions.$(s).on('ready', function(){
	// 	console.log('ready');
	// });
	// console.log($("#lhc_status_container #online-icon"));
}
};

LHCChatOptions.openStandaloneLink=function(){
	window.open("{$standalone_link}");
};

(function() {

	var onlylink={$onlylink};
	if(onlylink){
		//add event
		LHCChatOptions.$('{$load_livechat_link_selector}').on('click',function(e){
			//load on page
			LHCChatOptions.loadLivechat(false);
			e.preventDefault();
		});
		LHCChatOptions.$('{$popup_livechat_link_selector}').on('click',function(e){
			//popup
			LHCChatOptions.openStandaloneLink();
		});

		LHCChatOptions.$('{$pop_embed_livechat_link_selector}').on('click',function(e){
			//load on page
			LHCChatOptions.loadLivechat(true);
			e.preventDefault();
		});
	}else{
		LHCChatOptions.loadLivechat();
	}

})();

EOD;

		return $livechatjs;
	}

	public static function randomString($type = 'alnum', $len = 8)
	{
		switch($type)
		{
			case 'basic'	: return mt_rand();
				break;
			case 'alnum'	:
			case 'numeric'	:
			case 'nozero'	:
			case 'alpha'	:

					switch ($type)
					{
						case 'alpha'	:	$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
							break;
						case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
							break;
						case 'numeric'	:	$pool = '0123456789';
							break;
						case 'nozero'	:	$pool = '123456789';
							break;
					}

					$str = '';
					for ($i=0; $i < $len; $i++)
					{
						$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
					}
					return $str;
				break;
			case 'unique'	:
			case 'md5'		:

						return md5(uniqid(mt_rand()));
				break;
			case 'encrypt'	:
			case 'sha1'	:

						$CI =& get_instance();
						$CI->load->helper('security');

						return do_hash(uniqid(mt_rand(), TRUE), 'sha1');
				break;
		}
	}

	public static function getStandaloneChatLink($chat_options, $userInfo=null){

		$username = 'Guest' . self::randomString();
		$userInfoEncypted = null;

		if($userInfo){
			$username=$userInfo['username'];
			$userInfoEncypted = self::encryptBase64(json_encode($userInfo), $chat_options['encrypt_key1'], $chat_options['encrypt_key2']);
		}
		// $position = $this->utils->getConfig('live_chat_options');
		// $options=$position
		$options=$chat_options['www_chat_options'];
		$position=$options;

		$host = $chat_options['www_chat_host']; //'livechat.smartbackend.com';

		$survey_id=null;
		$theme=null;
		$department=null;
		if(empty($theme) && isset($options['theme']) && !empty($options['theme'])){
			$theme=$options['theme'];
		}
		if(empty($department) && isset($options['department']) && !empty($options['department'])){
			$department=$options['department'];
		}
		if(empty($survey_id) && isset($options['survey_id']) && !empty($options['survey_id'])){
			$survey_id=$options['survey_id'];
		}
		$lang=$options['lang'];

		// $default_theme= $theme ? "/(theme)/".$theme : '' ;
		// $default_department = $department ? "/(department)/".$department : '';
		// $default_survey=$survey_id ? '/(survey)/'.$survey_id : '';

		$params=[
			'theme'=>$theme,
			'department'=>$department,
			'survey'=>$survey_id,
			'lang'=>$lang,
			'widget_height'=>$position['widget_height'],
			'widget_width'=>$position['widget_width'],
			'username'=>$username,
		];

		if($userInfoEncypted){
			$params['userInfoEncypted']=$userInfoEncypted;
		}

		$link=($chat_options['www_chat_https_enabled'] ? 'https' :  'http' ).'://'.$host.'/standalone/index.php?'.http_build_query($params);

		return $link;
	}

	/**
	 * SSO link
	 *
	 */
	public static function getAutoLoginUrl($urlPrefix, $params, $key1, $key2){
		return $urlPrefix.'/'.rawurlencode(bin2hex(self::encrypt(json_encode($params), $key1, $key2)));
	}

	/**
	 * API check tip if valid
	 *
	 */
	public function checkTipIfValid($username, $tipAmount, $operatorName){
		$this->CI->load->model(['wallet_model','player','transactions','livechat_setting_model']);
		$tipResult = 0;

		$playerId = $this->CI->player->getPlayerIdByUsername($username);

		$wallet = $this->CI->wallet_model->getMainWalletBy($playerId);
		$amount = $wallet->totalBalanceAmount;

		if ($tipAmount != 0){
			if ($amount >= $tipAmount){

				$maximumTip = $this->CI->livechat_setting_model->getMaxTip('maximum_tip');

				if ($maximumTip['livechatData'] >= $tipAmount){
					# GET PARAMETERS FROM SYSTEM
					$current_timestamp = $this->CI->utils->getNowForMysql();
					$transaction_type = Transactions::LIVECHAT_TIP;
					$note = 'Tip from Player';

					//fix balance
					$this->CI->startTrans();
					$transId = $this->CI->transactions->livechatTransferTransaction($playerId, $transaction_type, $tipAmount, $note, $current_timestamp, $operatorName);
					$success = !empty($transId);

					if ($success) {
						$this->CI->endTransWithSucc();
						$tipResult = 1;
					} else {
						//rollback
						$this->CI->rollbackTrans();
					}
				}else{
					$tipResult = 0;
				}
			}
		}

		return $tipResult;
	}
}

////END OF FILE/////