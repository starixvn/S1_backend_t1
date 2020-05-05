<?php

//Basic authentication credentials
// $username = "";
// $password = "";

//class ProxySoapClient extends SoapClient {
//	protected function callCurl($url, $data, $action) {
//		//var_dump($url);
//		$handle = curl_init();
//		curl_setopt($handle, CURLOPT_URL, $url);
//
//		// If you need to handle headers like cookies, session id, etc. you will have
//		// to set them here manually
//		$headers = array("Content-Type: text/xml");
//		curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
//
//		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
//		curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
//		curl_setopt($handle, CURLOPT_FRESH_CONNECT, true);
//		curl_setopt($handle, CURLOPT_HEADER, true);
//
//		//if (ENVIRONMENT == 'development') {
//		curl_setopt($handle, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
//		curl_setopt($handle, CURLOPT_PROXY, MG_PROXY_HOST . ":" . MG_PROXY_PORT); // 1080 is your -D parameter
//		//}
//
//		$response = curl_exec($handle);
//		curl_close($handle);
//
//		list($headers, $content) = explode("\r\n\r\n", $response, 2);
//
//		// If you need headers for something, it's not too bad to
//		// keep them in e.g. $this->headers and then use them as needed
//
//		return $content;
//	}
//
//	public function __doRequest($request, $location, $action, $version, $one_way = 0) {
//		return $this->callCurl($location, $request, $action);
//	}
//}

/**
 *  Setting WS url
 */
//$url = "https://entservices.totalegame.net?wsdl";

/**
 *  Create new SOAP Object
 */
// $client = new ProxySoapClient($url, array('compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
// 	'proxy_host' => '103.224.83.131',
// 	'proxy_port' => 3188));

/**
 * Try to login
 */
// try {
// 	$result = $client->IsAuthenticate(
// 		array(
// 			'loginName' => $postArr['loginName'],
// 			'pinCode' => $postArr['pinCode'],
// 		)
// 	);
// } catch (Exception $e) {
// 	/**
// 	 * Setting error output to Session
// 	 */
// 	print_r($e);
// 	exit;
// }

// if ($result->IsAuthenticateResult->ErrorCode == 0) {
// 	$sessionGUID = $result->IsAuthenticateResult->SessionGUID;
// //         $_SESSION['login'] = $_POST['login'];

// 	/**
// 	 *  Setup for header
// 	 */
// 	$xml = '
//             <AgentSession xmlns="https://entservices.totalegame.net">
//                 <SessionGUID>' . $sessionGUID . '</SessionGUID>
//                 <IPAddress>' . '103.224.83.131' . '</IPAddress>
//             </AgentSession>
//         ';

// 	$xmlvar = new SoapVar($xml, XSD_ANYXML);
// 	$header = new SoapHeader('https://entservices.totalegame.net', 'AgentSession', $xmlvar);
// 	$client->__setSoapHeaders($header);
// 	*
// 	 *  Set currency list for deposit to session

// 	try {
// 		$result = $client->GetCurrenciesForDeposit();

// 		if (count($result->GetCurrenciesForDepositResult->Currency) >= 1) {
// 			foreach ($result->GetCurrenciesForDepositResult->Currency as $v) {
// 				$arr[$v->CurrencyId] = $v->IsoCode . " - " . $v->IsoName;
// 			}
// 			var_export($arr);
// //                 $_SESSION['currency'] = $arr;
// 		}

// 		var_dump($client->GetBettingProfileList());
// //             header('Location: /deposit.php');
// 	} catch (Exception $e) {
// 		/**
// 		 * Setting error output to Session
// 		 */
// 		print_r($e);
// //             $_SESSION['error'] = '<pre>' . print_r($e, true) . '</pre>';
// 		//exit;
// 	}

// } else {
// 	echo "Your login or password is incorect.\nTry again, please.";
// //       $_SESSION['error'] = "Your login or password is incorect. <br />Try again, please.";
// 	//exit;
// }

// echo "end\n";

///END OF FILE////