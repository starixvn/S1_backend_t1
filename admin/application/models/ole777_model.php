<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * General behaviors include :
 *
 * * Get player details
 * * Get/insert/update/ players
 * * Check active player
 * * Get player total bets
 * * Check referrals
 * * Get sub wallet accounts
 * * Deposit history
 * * Get pending balance
 * * Update total deposit/total betting amount
 *
 * @category Player Management
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
class Ole777_model extends BaseModel {


	public function __construct() {
		parent::__construct();

	}

    const REGISTERED_BY_IMPORTER = 'importer';

	/**
	 * Import ole player from external source.
	 * Updated 20181011 for function import OLE
	 *
	 * @param int		$externalId
	 * @param int		$levelId
	 * @param string	$username
	 * @param string	$password
	 * @param double 	$balance
	 * @param array		$extra
	 * @param string	$details
	 * @param string	$message
	 * @param string	$playerTagName
	 * @param string	$assigned_game_apis_map
	 * @param object	$group_level
	 * @param object	$wallet_model,
	 * @param object	$player_model
	 * @param object	$$http_request
	 * @return int
	 */
	public function importPlayer($externalId, $levelId, $username, $password, $balance, $extra = null, $details = null,
		&$message = null, $playerTagName = null, $assigned_game_apis_map, $group_level,
		$wallet_model, $player_model, $http_request) {

		$this->load->library(array('salt'));

		if (empty($username)) {
			$message = "Empty username: [$username]";
			return false;
		}
		if(empty($password)){
			$password='';
		}

		# Basic player fields
		$data = array(
			'playerId' => $externalId,
			'username' => $username,
			'gameName' => $username,
			'password' => empty($password) ? '' : $this->salt->encrypt($password, $this->getDeskeyOG()),
			'active' => Player_model::OLD_STATUS_ACTIVE,
			'blocked' => Player_model::DB_FALSE,
			'status' => Player_model::OLD_STATUS_ACTIVE,
			'registered_by' => Player_model::REGISTERED_BY_IMPORTER,
			'enabled_withdrawal' => Player_model::DB_TRUE,
			'levelId' => $levelId,
			'external_id' => $externalId,
			'codepass' => $password,
		);

		$data = array_merge($data, $extra);

		$this->db->select('playerId')->from('player')->where('playerId', $externalId);
		$playerId = $this->runOneRowOneField('playerId');

		$this->db->select('playerId')->from('player')->where('username', $username);
		$playerId2 = $this->runOneRowOneField('playerId');
		// duplicate remedy
		if(empty($playerId)){
			if(!empty($playerId2)){
				$playerId = $playerId2;
			}
		}

		$exists=false;
		# Create / Update the player record
		if (!empty($playerId)) {
			$exists=true;
			$this->db->set($data)->where('playerId', $playerId)->update('player');
		} else {
			$exists=false;
			$this->db->set($data)->insert('player');
			$playerId = $this->db->insert_id();
		}

		# Player level
		$group_level->adjustPlayerLevel($playerId, $levelId);

		# Playerdetails fields
		$this->db->select('playerDetailsId')->from('playerdetails')->where('playerId', $playerId);
		$playerDetailsId = $this->runOneRowOneField('playerDetailsId');
		$data = $details;
		$data['playerId'] = $playerId;
		if (!empty($playerDetailsId)) {
			$this->db->set($data)->where('playerDetailsId', $playerDetailsId)->update('playerdetails');
		} else {
			$this->db->set($data)->insert('playerdetails');
		}

		$lastActivityTime = (!empty($extra['lastLogoutTime'])) ? $extra['lastLogoutTime'] : '';
		$lastLogoutTime = (!empty($extra['lastLogoutTime'])) ? $extra['lastLogoutTime'] : '';
		$lastLoginTime = (!empty($extra['lastLoginTime'])) ? $extra['lastLoginTime'] : '';
		$lastLoginIp =  (!empty($extra['lastLoginIp'])) ? $extra['lastLoginIp'] : '';
		$lastLogoutIp = (!empty($extra['lastLogoutIp'])) ? $extra['lastLogoutIp'] : '';

		$this->utils->debug_log('player_runtime_data'.$playerId, $lastActivityTime, $lastLogoutTime, $lastLoginTime, $lastLoginIp, $lastLogoutIp);
		$player_model->updateLastActivity($playerId, $lastActivityTime, $lastLoginTime, $lastLogoutTime, $lastLoginIp, $lastLogoutIp);


		if(!empty($playerTagName)){

			$playerTags = [];

			if(is_array($playerTagName)){
				$playerTags = $playerTagName;
			}else{
				$playerTags=[$playerTagName];
			}
			$tagColors = ['#398f47','#e22553','#8ed533', '#3945ec','#ece539', '#39ecd7'];
			$today = date("Y-m-d H:i:s");
			//$this->utils->debug_log('PLAYERTAGS',$playerTags);
			foreach ($playerTags as $tagName) {
				$tagName = ucfirst($tagName);
				$tagId = $player_model->getTagIdByTagName($tagName);
				$playerTagData = array(
					'playerId' => $playerId,
					'taggerId' => '1',
					'tagId' => $tagId,
					'createdOn' => $today,
					'updatedOn' => $today,
					'status' => 1,
				);

				//$this->utils->debug_log('PLAYERTAGDATA',$playerTagData);
				if(empty($tagId)) {
					$tagcolor_count = count($tagColors);
					$tagData = array(
						'tagName' => $tagName,
						'tagDescription' => $tagName, //no description use tagname
						'tagColor' => $tagColors[rand(0, $tagcolor_count-1)],
						'createBy' => '1',
						'createdOn' => $today,
						'status' => 0,
					);
					//$this->utils->debug_log('TAGGGID',$tagId);
					//create tag
					$tagId = $player_model->insertNewTag($tagData);
					$playerTagData['tagId'] = $tagId;
					$player_model->insertPlayerTag($playerTagData);
				}else{
				 //check player tag
					if($player_model->checkIfPlayerIsTagged($playerId,$tagId)){
						$player_model->updatePlayerTag($playerTagData,$playerId,$tagId);
					}else{
						$player_model->insertPlayerTag($playerTagData);
					}
				}
			}
		}

		if(!$exists){

			if(!empty($extra['lastLoginIp'])){
				$http_request_data=[
					'playerId'=>$playerId,
					'ip'=>$extra['lastLoginIp'],
					'createdat'=>$extra['createdOn'],
					'type'=>Http_request::TYPE_LAST_LOGIN, //login
				];

				$http_request_id=$http_request->insertHttpRequest($http_request_data);
			}
			if(!empty($details['registrationIP'])){
				$http_request_data=[
					'playerId'=>$playerId,
					'ip'=>$details['registrationIP'],
					'createdat'=>$extra['createdOn'],
					'type'=>Http_request::TYPE_REGISTRATION, //reg
				];

				$http_request_id=$http_request->insertHttpRequest($http_request_data);
			}

			if(!empty($extra['lastLoginIp'])){

				$last_request=[
					'player_id'=>$playerId,
					'ip'=>$extra['lastLoginIp'],
					'last_datetime'=>$extra['createdOn'],
					'http_request_id'=>$http_request_id,
				];

				$http_request->insertData('player_ip_last_request', $last_request);
			}else{
				//convert ip to http_request and player_ip_last_request
				if(!empty($details['registrationIP'])){

					$last_request=[
						'player_id'=>$playerId,
						'ip'=>$details['registrationIP'],
						'last_datetime'=>$extra['createdOn'],
						'http_request_id'=>$http_request_id,
					];

					$http_request->insertData('player_ip_last_request', $last_request);
				}


			}//else

			//admin is 1
			$notes='import player '.$username.', balance: '.$balance.', level: '.$levelId.' at '.$this->utils->getNowForMysql();
			$player_model->addPlayerNote($playerId, 1, $notes);

		}

		$currency = $player_model->getActiveCurrencyCode();
		$wallet_model->syncAllWallet($playerId, $balance, $currency);
		$wallet_model->moveAllToRealOnMainWallet($playerId);

		$this->createPlayerInDB($playerId,$username,'',null,null,$assigned_game_apis_map);

		return $playerId;
	}


	public function createPlayerInDB($playerId,$playerName,$password='',$agent_id=null,$sma_id=null,$assigned_game_apis_map) {

		$source = Game_provider_auth::SOURCE_REGISTER;
		$is_demo_flag = false;
		$game_provider_auth_tbl = 'game_provider_auth';

		if(empty($assigned_game_apis_map)){
			$this->utils->debug_log('assigned_game_apis_map is empty ',$assigned_game_apis_map);
			return;
		}

		foreach ($assigned_game_apis_map as $apiId => $value) {

			$gameAccount=$playerName;
			if(!empty($value['prefix'])){
				$gameAccount = $value['prefix'].$playerName;
			}
			$player_game_acct = array(
				'login_name' => $gameAccount,
				"player_id" => $playerId,
				"password" => $password,
				"source" => $source,
				"is_demo_flag" => $is_demo_flag,
				"agent_id" => $agent_id,
				"sma_id" => $sma_id,
				'game_provider_id' => $apiId,
				'status' => self::STATUS_NORMAL,
			);

			$this->db->select('id')->from($game_provider_auth_tbl)->where('player_id', $playerId)
 			    ->where('game_provider_id', $apiId);
			$id=$this->runOneRowOneField('id');

			if(empty($id)){
				//double check login_name
				$this->db->select('id')->from($game_provider_auth_tbl)->where('login_name', $gameAccount)
				    ->where('game_provider_id', $apiId);
				$id=$this->runOneRowOneField('id');
			}

			if (!empty($id)) {
				$this->db->where('id', $id);
			    $this->db->update($game_provider_auth_tbl,$player_game_acct);
			} else {
				$this->db->insert($game_provider_auth_tbl,$player_game_acct);
			}
			$this->utils->info_log('Create update player game account  ','game_name', $playerName,'api', $apiId);
		}
	}


	public function importPlayerBank($external_id, $playerId, $bankTypeId, $dwBank,
		$bankAccountFullName, $bankAccountNumber, $province, $city, $branch, $bankAddress, $createdOn, $status, &$message){

		if (empty($playerId)) {
			$message = "Empty playerId: [$playerId]";
			return false;
		}

		if (empty($bankTypeId)) {
			$message = "Empty bankTypeId: [$bankTypeId]";
			return false;
		}

		$data = array(
			'playerId' => $playerId,
			'bankTypeId' => $bankTypeId,
			'bankAccountFullName' => $bankAccountFullName,
			'bankAccountNumber' => $bankAccountNumber,
			'bankAddress'=> $bankAddress,
			'city'=>$city,
			'province'=>$province,
			'branch'=>$branch,
			'isDefault'=>'1',
			'isRemember'=>'1',
			'dwBank' => $dwBank,
			'status' => $status,
			'createdOn' => $createdOn,
			'updatedOn' => $this->utils->getNowForMysql(),
			'external_id'=> $external_id,
		);

		$this->db->select('bankTypeId')->from('playerbankdetails')
		->where('playerId', $playerId)
		->where('dwBank', $dwBank)
		->where('bankAccountNumber', $bankAccountNumber);

		if(empty($external_id)){
			$this->db->where('external_id', $external_id);
		}else{
			$this->db->where('bankTypeId', $bankTypeId);
		}

		$bankTypeId= $this->runOneRowOneField('bankTypeId');
		$playerBankDetailsId = null;
		if (!empty($bankTypeId)) {
			$exists=true;
			$this->db->set($data)
			->where('bankTypeId', $bankTypeId)
			->where('playerId', $playerId)
			->where('dwBank', $dwBank)
			->where('bankAccountNumber', $bankAccountNumber)
			->update('playerbankdetails');
		} else {
			$exists=false;
			$this->db->set($data)->insert('playerbankdetails');
			$playerBankDetailsId = $this->db->insert_id();
		}

		return $playerBankDetailsId;
	}


		/**
	 * overview : import ole777 affiliate
	 *
	 * @param int		$externalId
	 * @param string	$username
	 * @param string	$password
	 * @param string	$trackingCode
	 * @param date		$createdOn
	 * @param string	$firstname
	 * @param string	$lastname
	 * @param int		$status
	 * @param null $extra
	 * @return null
	 */
		public function importAffiliate($externalId, $username, $password, $trackingCode, $createdOn,
			$firstname, $lastname, $status, $extra = null, $affShare=null) {

			$this->load->model(array('affiliatemodel'));

			if (empty($username)) {
				$message = "Empty username: [$username]";
				return false;
			}

			$this->load->library(array('salt'));
			$data = array(
				'affiliateId' => $externalId,
				'externalId' => $externalId,
				'username' => $username,
				'password' => empty($password) ? '' : $this->salt->encrypt($password, $this->getDeskeyOG()),
				'trackingCode' => $trackingCode,
				'createdOn' => $createdOn,
				'firstname' => $firstname,
				'lastname' => $lastname,
				'status' => $status,
				'registered_by' => self::REGISTERED_BY_IMPORTER,
			);


		//process extra
			if(empty($extra['lastLogin'])){
			// $this->utils->debug_log('unset lastLogin');
				unset($extra['lastLogin']);
			}
			if(empty($extra['lastLoginIp'])){
				unset($extra['lastLoginIp']);
			}

			if (!empty($extra)) {
				$data = array_merge($data, $extra);
			}

			$this->db->from('affiliates')->where('affiliateId', $externalId);
			$affId = $this->runOneRowOneField('affiliateId');

			$this->db->from('affiliates')->where('username', $username);
			$affId2 = $this->runOneRowOneField('affiliateId');

			if(empty($affId)){
				if(!empty($affId2)){
					$affId = $affId2;
				}
			}

			if (!empty($affId)) {
			//$data=[];
			//update
				if(!empty($firstname)){
					$data['firstname']=$firstname;
				}
				if(!empty($lastname)){
					$data['lastname']=$lastname;
				}
				if (!empty($extra)) {
					$data = array_merge($data, $extra);
				}
				if(!empty($data)){
					$this->utils->debug_log('importAffiliate update ', $data);
					$this->db->set($data)->where('affiliateId', $affId);
					$this->runAnyUpdate('affiliates');
				}
				if(!empty($affShare)){
				// $settings=$this->getAffTermsSettings($affId);
					$fldKV=['level_master'=>$affShare];
					$mode='operator_settings';
					$this->affiliatemodel->mergeToAffiliateSettings($fldKV, $affId, $mode);
				}

			// $this->utils->debug_log('ignore username', $username);
			//ignore
				return $affId;
			}

		// $this->db->set($data);
			return $this->insertData('affiliates', $data);
		}

		public function importAgent($externalId,$agent_name, $password,
			$tracking_code,$createdOn, $firstName, $lastName, $extra){

			$this->load->model(array('agency_model'));
			$this->load->library(array('salt'));

			$agent_id = $externalId;
			$data = array(
				    'agent_id' => $agent_id,
				    'agent_name' => $agent_name,
			     	'tracking_code' => $tracking_code,
			     	'password' => empty($password) ? '' : $this->salt->encrypt($password, $this->getDeskeyOG()),
					'can_have_sub_agent' => 1,
					'can_have_players' => 1,
					'can_do_settlement' => 1,
					'can_view_agents_list_and_players_list' => 1,
					'show_bet_limit_template' => 1,
					'show_rolling_commission' => 1,
				);


		 $data = array_merge($data, $extra);
			$this->utils->info_log('agent rowdata', $data);
			$this->updateAgentInfoByAgentId($data,$agent_id);
			return $agent_id;
		}

		public function updateAgentInfoByAgentId($data,$agent_id){

			$agency_agents_tbl = 'agency_agents';
		    $this->db->from($agency_agents_tbl)->where('agent_id', $agent_id);

			if ($this->runExistsResult()) {
				$this->db->where('agent_id', $agent_id);
			    $this->db->update($agency_agents_tbl,$data);
			} else {
				$this->db->insert($agency_agents_tbl,$data);
			}

		}

		public function addUpdateAgentInfoByAgentName($data,$agent_name){
			$table = 'agency_agents';
			$agent_id =null;
			$qry = $this->db->from($table)->where('agent_name', $agent_name);
			$agent_id = $this->runOneRowOneField('agent_id');
			if (!empty($agent_id)) {
				 $this->db->where('agent_id', $agent_id);
				 $this->db->update($table,$data);
			} else {
				$agent_id =$this->insertData($table, $data);
			}
			return $agent_id;
		}

		public function updateAllLevelOneParentId($root_agent_id){
			$table = 'agency_agents';
			$data = array('parent_id'=> $root_agent_id);
			$this->db->set($data)->where('agent_level', 1)->update($table);
		}

		public function addUpdateAgencyAgentGamePlatforms($data,$game_platform_id,$agent_id) {

			$table = 'agency_agent_game_platforms';
		    $this->db->from($table)->where('game_platform_id', $game_platform_id)->where('agent_id', $agent_id);

			if ($this->runExistsResult()) {
				$this->db->where('game_platform_id', $game_platform_id)->where('agent_id', $agent_id);
			    $this->db->update($table,$data);
			} else {
				$this->db->insert($table,$data);
			}

		}

		public function addUpdateAgencyAgentGameType($data,$game_platform_id,$game_type_id,$agent_id) {

			$table = 'agency_agent_game_types';
		    $this->db->from($table)
		    ->where('game_platform_id', $game_platform_id)
		    ->where('game_type_id', $game_type_id)
		    ->where('agent_id', $agent_id);

			if ($this->runExistsResult()) {
				$this->db->where('game_platform_id', $game_platform_id)
		                 ->where('game_type_id', $game_type_id)
		                 ->where('agent_id', $agent_id);
			    $this->db->update($table,$data);
			} else {
				$this->db->insert($table,$data);
			}

		}

		public function syncUser($rolesModel,$roleId, $userInfo){

			$userId = $this->syncAdminUser($rolesModel,$roleId, $userInfo);

			if(!empty($userId)){
				$genealogyTbl = 'genealogy';
				$this->db->select('genealogyId')->from($genealogyTbl)->where('roleId', $roleId);
				$genealogyId = $this->runOneRowOneField('genealogyId');

				$data=[
					'generation' => 1,
					'gene'=> $roleId,
					'roleId'=> $roleId
				];
				if(empty($genealogyId)){
					$this->insertData($genealogyTbl, $data);
				}else{
					$this->updateData('genealogyId', $genealogyId, $genealogyTbl, $data);
				}

				$userRolesTbl = 'userroles';
				$this->db->select('id')->from($userRolesTbl)->where('roleId', $roleId)->where('userId', $userId);
				$userRoleId=$this->runOneRowOneField('id');
				$data=[
					'userId'=> $userId,
					'roleId'=> $roleId
				];
				if(empty($userRoleId)){
					$this->insertData($userRolesTbl, $data);
				}else{
					$this->updateData('id', $userRoleId, $userRolesTbl, $data);
				}
			}
		}

		public function syncAdminUser($rolesModel,$roleId, $userInfo){

			if(!empty($userInfo)){
				$username=$userInfo['username'];
				$this->db->select('userId')->from('adminusers')->where('username', $username);
				$userId=$this->runOneRowOneField('userId');
				$adminusersTbl = 'adminusers';
				$password='';
				$password=$rolesModel->hashPassword($userInfo['password']);

				$data=[
					'username'=>$username,
					'password'=> $rolesModel->hashPassword($userInfo['password']),
					'realname'=> $userInfo['realname'],
					'department'=> $userInfo['department'],
					'position'=> $userInfo['position'],
					'email'=> $userInfo['email'],
					'position'=> $userInfo['position'],
					'createTime'=> $rolesModel->utils->getNowForMysql(),
					'createPerson'=>1,
					'status'=>self::DB_TRUE,
					'maxWidAmt'=> isset($userInfo['maxWidAmt']) ? $this->utils->extractFloatNumber($userInfo['maxWidAmt']): 0,
					'singleWidAmt' => isset($userInfo['singleWidAmt']) ? $this->utils->extractFloatNumber($userInfo['singleWidAmt']): 0,
					'tele_id' => isset($userInfo['tele_id']) ? $userInfo['tele_id'] : '',
					'note' => isset($userInfo['note']) ? $userInfo['note'] : '',
					'deleted'=>0,
				];

				if(empty($userId)){
					return $this->insertData('adminusers', $data);
				}else{
					$this->updateData('userId', $userId, $adminusersTbl, $data);
					return $userId;
				}
			}
		}



}


