<?php
	$client = new Soapclient('https://entservices.totalegame.net/EntServices.asmx?wsdl');
	class AgentSession
	{
	public $param = array
	( 'loginName' => 'blmapi201318', 'pinCode' => 'c82cf0' );
	}
	$add = new AgentSession();
	$result = $client -> IsAuthenticate($add -> param);
	if($result -> IsAuthenticateResult -> ErrorCode == 0){
	$_SESSION['SessionGUID'] = $result -> IsAuthenticateResult -> SessionGUID;
	$_SESSION['IPAddress'] = $result -> IsAuthenticateResult -> IPAddress;
	}
	$xml = '
	<AgentSession xmlns="https://entservices.totalegame.net">
	<SessionGUID>' . $_SESSION['SessionGUID'] . '</SessionGUID>
	<IPAddress>' . $_SERVER['REMOTE_ADDR'] . '</IPAddress>
	</AgentSession>
	';
	$xmlvar = new SoapVar($xml, XSD_ANYXML);
	$header = new SoapHeader('https://entservices.totalegame.net', 'AgentSession', $xmlvar);
	$client -> __setSoapHeaders($header);
	print_r($result); //显示结果
?>