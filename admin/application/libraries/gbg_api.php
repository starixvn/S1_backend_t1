<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class gbg_api extends SoapHeader{

	public function __construct()
    {
        $this->ci = &get_instance();
        $this->api_url = $this->ci->utils->getConfig('gbg_api_url');
        $this->api_acct = $this->ci->utils->getConfig('gbg_account');
        $this->wss_ns = $this->ci->utils->getConfig('gbg_api_url')['wss_ns'];
    }

    public function setHeader(){
    	$auth = new stdClass();
		$auth->Username = new SoapVar($this->api_acct['AccountName'], XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
		$auth->Password = new SoapVar($this->api_acct['Password'], XSD_STRING, NULL, $this->wss_ns, NULL, $this->wss_ns);
		$username_token = new stdClass();
		$username_token->UsernameToken = new SoapVar($auth, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns);
		$security_sv = new SoapVar(
		new SoapVar($username_token, SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'UsernameToken', $this->wss_ns),
		SOAP_ENC_OBJECT, NULL, $this->wss_ns, 'Security', $this->wss_ns);
		return new SoapHeader($this->wss_ns, 'Security', $security_sv, true);
    }
    
    // this function authenticate Single Profile in GBG
    public function auth_sp($playerDetails){
    	if(empty($this->api_url) || empty($this->api_acct)) {
    		return array(
    			'status'	=> 'error',
    			'response' 	=> lang("GBG credential and API url not configure or invalid."),
    		);
    	}

    	$wsse_header = $this->setHeader();
    	$playerId = $playerDetails["playerId"];
    	$title = (!empty($playerDetails["gender"]) ? (($playerDetails["gender"] == "Male") ? "Mr" : "Ms") : '' );
    	$Forename = $playerDetails["firstName"];
    	$MiddleName = null;
    	$Surname = $playerDetails["lastName"];
    	$Gender = $playerDetails["gender"];
    	$DOBDay = date('d', strtotime($playerDetails["birthdate"]));
    	$DOBMonth = date('m', strtotime($playerDetails["birthdate"]));
    	$DOBYear = date('Y', strtotime($playerDetails["birthdate"]));
    	$Country = $playerDetails["residentCountry"];
    	$Street = "";
    	$City = $playerDetails["city"];
    	$ZipPostcode = $playerDetails["zipcode"];
    	$Building = "";
    	$Address1 = $playerDetails["address"];
    	$Address2 = $playerDetails["address2"];
    	$Address3 = $playerDetails["address3"];

    	$IDNumber = $playerDetails["id_card_number"];
    	$IDCountry = "";

		// This is currently linked to the pilot or test site.
		$wsdl = $this->ci->utils->getConfig('gbg_api_url')['wsdl'];
		$soapClient = new SoapClient($wsdl, $this->soapOptions(SOAP_1_1,true,1,true));
		$soapClient->__setSoapHeaders(array($wsse_header));
		$objParam = new stdClass();
		$objParam ->ProfileIDVersion = new stdClass();
		$objParam ->ProfileIDVersion ->ID =$this->api_acct['profile_id']; // This can be found via the admin portal of id3 global.
		$objParam ->ProfileIDVersion ->Version =0; // Setting this to zero will by default call the latest active version of the profile
		$objParam ->CustomerReference = $playerId;
		$objParam ->InputData = new stdClass();
		$objParam ->InputData->Personal = new stdClass();
		$objParam ->InputData->Personal->PersonalDetails = new stdClass();

		if(!empty($title)){
			$objParam ->InputData->Personal->PersonalDetails->Title = $title;
		}
		
		if(!empty($Forename)){
			$objParam ->InputData->Personal->PersonalDetails->Forename = $Forename;
		}

		if(!empty($MiddleName)){
			$objParam ->InputData->Personal->PersonalDetails->MiddleName = $MiddleName;
		}
		
		if(!empty($Surname)){
			$objParam ->InputData->Personal->PersonalDetails->Surname = $Surname;
		}
		
		if(!empty($Gender)){
			$objParam ->InputData->Personal->PersonalDetails->Gender = $Gender;
		}

		if(!empty($DOBDay) && !empty($DOBMonth) && !empty($DOBYear)){
			$objParam ->InputData->Personal->PersonalDetails->DOBDay = $DOBDay;
			$objParam ->InputData->Personal->PersonalDetails->DOBMonth = $DOBMonth;
			$objParam ->InputData->Personal->PersonalDetails->DOBYear = $DOBYear;
		}
		
		$objParam ->InputData->Addresses = new stdClass();
		$objParam ->InputData->Addresses->CurrentAddress = new stdClass();

		if(!empty($Country)){
			$objParam ->InputData->Addresses->CurrentAddress->Country = $Country;
		}

		if(!empty($Street)){
			$objParam ->InputData->Addresses->CurrentAddress->Street = $Street;
		}

		if(!empty($City)){
			$objParam ->InputData->Addresses->CurrentAddress->City = $City;	
		}

		if(!empty($ZipPostcode)){
			$objParam ->InputData->Addresses->CurrentAddress->ZipPostcode = $ZipPostcode;
		}

		if(!empty($Building)){
			$objParam ->InputData->Addresses->CurrentAddress->Building = $Building;
		}

		if(!empty($Address1)){
			$objParam ->InputData->Addresses->CurrentAddress->AddressLine1 = $Address1;
		}

		if(!empty($Address2)){
			$objParam ->InputData->Addresses->CurrentAddress->AddressLine2 = $Address2;
		}

		if(!empty($Address3)){
			$objParam ->InputData->Addresses->CurrentAddress->AddressLine3 = $Address3;
		}

		$objParam ->InputData->IdentityDocuments = new stdClass();
		$objParam ->InputData->IdentityDocuments->IdentityCard = new stdClass();

		if(!empty($IDNumber)){
			$objParam ->InputData->IdentityDocuments->IdentityCard->Number = $IDNumber;
		}

		if(!empty($IDCountry)){
			$objParam ->InputData->IdentityDocuments->IdentityCard->Country = $IDCountry;
		}

		if (is_soap_fault($soapClient))
		{
			throw new Exception(" {$soapClient->faultcode}: {$soapClient->faultstring} ");
		}

		$objRet = null;
		try
		{
			//var_dump($objParam);die();
			/*$objRet = $soapClient->AuthenticateSP($objParam);
			echo '<pre>';
			print"Decision Band :".($objRet->AuthenticateSPResult->BandText)."<br>";
			echo '</pre>';*/

			$objRet = $soapClient->AuthenticateSP($objParam);
			return array(
    			'status'	=> 'success',
    			'response' 	=> $objRet
    		);
			//$objRet->AuthenticateSPResult->BandText;
		}
		catch (Exception $e) {
			/*echo "<pre>";
			print_r($e);
			echo "</pre>";*/
			return array(
    			'status'	=> 'error',
    			'response' 	=> $e
    		);
			//return $e;
		}

		if (is_soap_fault($objRet))
		{
			throw new Expception(" {$objRet->faultcode}: {$objRet->faultstring} ");
		}
    }

    public function soapOptions($compression,$trace,$cache_wsdl)
    {
		return array(
			'soap_version' => $compression,
			'exceptions' => $trace, 
			'trace' => $cache_wsdl,
			'wdsl_local_copy' => $cache_wsdl,
		);
    }

    public function checkCredentials(){
    	$soap = new SoapClient($this->ci->utils->getConfig('gbg_api_url')['wsdl'], array('compression' => SOAP_COMPRESSION_ACCEPT, 'trace' => 1, 'cache_wsdl' => WSDL_CACHE_NONE));
		$objParam = $this->api_acct;
		if (is_soap_fault($soap))
		{
		throw new Exception(" {$soap->faultcode}: {$soap->faultstring} ");
		}
		try {
		$objRet = $soap->CheckCredentials($objParam);
		echo '<pre>';
		print"OrgID:".($objRet->CheckCredentialsResult->OrgID)."<br>";
		print"AccountID :".($objRet->CheckCredentialsResult->AccountID)."<br>";
		echo '</pre>';
		// exit('PHPcode check ok');
		}
		catch (Exception $e) {
		echo "<pre>";
		print_r($e);
		echo "</pre>";
		}
		if (is_soap_fault($objRet)) {
		throw new Expception(" {$objRet->faultcode}: {$objRet->faultstring} ");
		}
    }

}