<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Lib_cloudflare {

	private $CI;

	public $last_result;

	public $api_url='https://api.cloudflare.com/client/v4';

	public function __construct() {
		$this->CI = &get_instance();

		$this->utils=$this->CI->utils;

		$this->init();
	}

	public function init(){
		//load api and account list

		$this->cloudflare_email=$this->CI->utils->getConfig('cloudflare_email');
		$this->cloudflare_api_key=$this->CI->utils->getConfig('cloudflare_api_key');

		$this->cloudflare_t1t_email=$this->CI->utils->getConfig('cloudflare_t1t_email');
		$this->cloudflare_t1t_api_key=$this->CI->utils->getConfig('cloudflare_t1t_api_key');
		$this->cloudflare_t1t_games_zone_id=$this->CI->utils->getConfig('cloudflare_t1t_games_zone_id');

		$this->cloudflare_default_source_ip=$this->CI->utils->getConfig('cloudflare_default_source_ip');

		$this->last_result=[];
	}

	public function addStandardDnsRecrods($zone_id, $domain){

		$new_domain=$domain;
		$dnsParams=[
			'type'=>'A',
			'name'=>$new_domain,
			'content'=>$this->cloudflare_default_source_ip,
			'proxied'=>true,
		];
		$succ=$this->callApi('/zones/'.$zone_id.'/dns_records', 'POST', $dnsParams);
		$this->last_result[]=[
			'domain'=>$new_domain,
			'dns_result'=>$succ,
		];

		$new_domain='www.'.$domain;
		$dnsParams=[
			'type'=>'CNAME',
			'name'=>$new_domain,
			'content'=>$domain,
			'proxied'=>true,
		];
		$succ=$this->callApi('/zones/'.$zone_id.'/dns_records', 'POST', $dnsParams);
		$this->last_result[]=[
			'domain'=>$new_domain,
			'dns_result'=>$succ,
		];

		$new_domain='m.'.$domain;
		$dnsParams=[
			'type'=>'CNAME',
			'name'=>$new_domain,
			'content'=>$domain,
			'proxied'=>true,
		];
		$succ=$this->callApi('/zones/'.$zone_id.'/dns_records', 'POST', $dnsParams);
		$this->last_result[]=[
			'domain'=>$new_domain,
			'dns_result'=>$succ,
		];

		$new_domain='player.'.$domain;
		$dnsParams=[
			'type'=>'CNAME',
			'name'=>$new_domain,
			'content'=>$domain,
			'proxied'=>true,
		];
		$succ=$this->callApi('/zones/'.$zone_id.'/dns_records', 'POST', $dnsParams);
		$this->last_result[]=[
			'domain'=>$new_domain,
			'dns_result'=>$succ,
		];

		$new_domain='aff.'.$domain;
		$dnsParams=[
			'type'=>'CNAME',
			'name'=>$new_domain,
			'content'=>$domain,
			'proxied'=>true,
		];
		$succ=$this->callApi('/zones/'.$zone_id.'/dns_records', 'POST', $dnsParams);
		$this->last_result[]=[
			'domain'=>$new_domain,
			'dns_result'=>$succ,
		];

		return true;
	}

	public function addDomainList($domainList){

		if(!is_array($domainList)){
			$domainList=[$domainList];
		}

		$success=false;

		if(!empty($domainList)){
			$success=true;

			// $result=[];
			foreach ($domainList as $domain) {

				$params=[
					'name'=> $domain,
					'jump_start'=>false,
				];
				$respJson=null;
				$succ=$this->callApi('/zones', 'POST', $params, 'default', $respJson);

				$this->last_result[]=[
					'domain'=>$domain,
					'zone_result'=>$succ,
				];

				if($succ && !empty($respJson)){
					//add dns
					$zone_id=$respJson['result']['id'];
					if(!empty($zone_id)){

						$succ=$this->addStandardDnsRecrods($zone_id, $domain);
						if(!$succ){
							$this->CI->utils->error_log('add dns to '.$domain.' failed', $this->last_result);
						}
					}else{
						$this->CI->utils->error_log('ignore '.$domain.' no zone id', $respJson);
					}

				}

				// $result[$domain]=$succ;
			}
		}

		return $success;

	}

	public function clearCache(){

	}

	public function clearT1TCache(&$resp=null){

		// $params=['purge_everything'=>true];

		$url=$this->api_url.'/zones/'.$this->cloudflare_t1t_games_zone_id.'/purge_cache';

		$cloudflare_email=$this->cloudflare_t1t_email;
		$cloudflare_api_key=$this->cloudflare_t1t_api_key;

		$callApiCmd=<<<EOD
curl -X DELETE "{$url}" -H "X-Auth-Email: {$cloudflare_email}" -H "X-Auth-Key: {$cloudflare_api_key}" -H "Content-Type: application/json" --data '{"purge_everything":true}'
EOD;
		$is_blocked=true;
		$func='clear_t1t_cache';
		$cmd=$this->CI->utils->generateCommonLine($callApiCmd, $is_blocked, $func);

		$resp=pclose(popen($cmd, 'r'));
        $this->CI->utils->debug_log($resp);

        return true;
	}

	public function callApi($uri, $method, $params, $account='default', &$respJson=null){

		$cloudflare_email=$this->cloudflare_email;
		$cloudflare_api_key=$this->cloudflare_api_key;
		if($account=='t1t'){
			$cloudflare_email=$this->cloudflare_t1t_email;
			$cloudflare_api_key=$this->cloudflare_t1t_api_key;
		}

		if(empty($params) || empty($cloudflare_email) || empty($cloudflare_api_key)){
			return false;
		}

		$params=$this->CI->utils->encodeJson($params);

		$headers=[
			'X-Auth-Email'=>$cloudflare_email,
			'X-Auth-Key'=>$cloudflare_api_key,
			'Content-Type'=>'application/json',
		];

		// $postJson=true;
		$url=$this->api_url.$uri;

		$this->CI->utils->debug_log('call api ', $url, $method, $params, $headers);
		// $resp=null;
		list($header, $content, $statusCode, $statusText, $errCode, $error, $respObj)=
			$this->CI->utils->callHttp($url, $method, $params, null, $headers);

		if(!empty($content)){
			$respJson=$this->CI->utils->decodeJson($content);

			$this->CI->utils->debug_log('call CF', $params, $header, $content, $statusCode, $statusText,
				$errCode, $error, $respJson);

			return !empty($respJson);
		}

		return false;
	}

}
