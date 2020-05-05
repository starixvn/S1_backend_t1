<?php
trait linked_account_module {
	private $linkedAccountList = array();
	public function linkedAccount(){
		if (!$this->permissions->checkPermissions('linked_account')) {
			$this->error_access();
		}

		if (($this->session->userdata('sidebar_status') == NULL)) {
			$this->session->set_userdata(array('sidebar_status' => 'active'));
		}

		$this->loadTemplate('Player Management', '', '', 'linked account');
		$this->load->model('linked_account_model');
		$this->session->set_userdata('searchType', Linked_account_model::SEARCH_TYPE_EXACT_USERNAME);

		$this->template->write_view('sidebar', 'player_management/sidebar');
		$this->template->write_view('main_content', 'player_management/linked_account/linked_account');
		$this->template->render();
	}

	public function searchLinkedPlayer(){
		if (!$this->permissions->checkPermissions('linked_account')) {
			$this->error_access();
		}

		if (($this->session->userdata('sidebar_status') == NULL)) {
			$this->session->set_userdata(array('sidebar_status' => 'active'));
		}

		$linkDatetime = $this->input->get_post('linkDatetime') ?: null;
		$this->session->set_userdata('linkDatetime', $linkDatetime);

		$username = $this->input->get_post('username') ?: null;
		$this->session->set_userdata('linkAccountUsername', $username);

		$searchType = $this->input->get_post('searchType') ?: null;
		$this->session->set_userdata('searchType', $searchType);

		$search = array(
			'link_datetime' => $linkDatetime,
			'username' => $username,
			'search_type' => $searchType,
		);

		if (!array_filter($search)) {
			$message = lang('Pls Enter Search Linked Account');
			$this->alertMessage(self::MESSAGE_TYPE_ERROR, $message);
			redirect('player_management/linkedAccount');
		} else {
			$this->load->model('linked_account_model');

			$data['players'] = $this->linked_account_model->getLinkedAccount($search);

			$this->loadTemplate('Player Management', '', '', 'linked account');
			$this->template->write_view('sidebar', 'player_management/sidebar');
			$this->template->write_view('main_content', 'player_management/linked_account/linked_account', $data);
			$this->template->render();
		}
	}

	/**
	 * detail: export linked accounts list
	 *
	 * @return json
	 */
	public function exportLinkedAccountsList($isFromLinkAccountDetailsPage=false) {

		//get post data
		$searchInputPost = $this->input->post();

		$this->load->model(array('linked_account_model'));
		$result = array();
		if($isFromLinkAccountDetailsPage){
			$result = $this->getLinkedAccountDetails($searchInputPost['username'],true);
			if(!empty($result)){
				$result = $result[Linked_account_model::ARRAY_FIRST_CHILD]['linked_accounts'];
				foreach ($result as $key => $value) {
					unset($result[$key]['id']);
					unset($result[$key]['playerId']);
					unset($result[$key]['action_edit_remarks']);
					unset($result[$key]['action_delete_remarks']);
					$result[$key]['blocked'] ? $result[$key]['blocked'] = "true" : $result[$key]['blocked'] = "false";
				}
			}
		}else{
			$result = $this->linked_account_model->getLinkedAccountCsvReport($searchInputPost);
		}

		$rlt = array();
		if(!empty($result)){
			$d = new DateTime();
			$csvFileName = 'linked_accounts_' . $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999);
			$link = $this->utils->create_excel($result,$csvFileName,null,null,true);

			//return file link
			$rlt = array('success' => true, 'link' => $link);
		}

		$this->returnJsonResult($rlt);
	}

	/**
	 * detail: export linked accounts list
	 *
	 * @return json
	 */
	public function exportLinkedAccountsListBySearch() {
		//get post data
		$searchInputPost = $this->input->post();

		$this->load->model(array('linked_account_model'));
		$result = $this->linked_account_model->getLinkedAccountCsvReport($searchInputPost);

		$rlt = array();
		if(!empty($result)){
			$d = new DateTime();
			$csvFileName = 'linked_accounts_' . $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999);
			$forCsvData['data'] = $result;
			$link = $this->utils->create_csv($forCsvData,$csvFileName,null,null,true);

			//return file link
			$rlt = array('success' => true, 'link' => $link);
		}

		$this->returnJsonResult($rlt);
	}

	/**
	 * detail: export linked accounts list
	 *
	 * @return json
	 */
	public function exportLinkedAccountsListInPlayerInfo() {

		//get post data
		$searchInputPost = $this->input->post();

		$this->load->model(array('linked_account_model'));
		$result = array();
		$result = $this->getLinkedAccountDetails($searchInputPost['username'],true);
		if(!empty($result)){
			$result = $result[Linked_account_model::ARRAY_FIRST_CHILD]['linked_accounts'];
			foreach ($result as $key => $value) {
				unset($result[$key]['id']);
				unset($result[$key]['playerId']);
				unset($result[$key]['action_edit_remarks']);
				unset($result[$key]['action_delete_remarks']);
				$result[$key]['blocked'] ? $result[$key]['blocked'] = "true" : $result[$key]['blocked'] = "false";
			}
		}

		$rlt = array();
		if(!empty($result)){
			$d = new DateTime();
			$csvFileName = 'linked_accounts_' . $d->format('Y_m_d_H_i_s') . '_' . rand(1, 9999);
			$forCsvData['data'] = $result;
			$link =  $this->utils->create_csv($forCsvData,$csvFileName,null,null,true);

			//return file link
			$rlt = array('success' => true, 'link' => $link);
		}

		$this->returnJsonResult($rlt);
	}

	public function updatePlayerLinkedAccountRemarks(){
		$data = array(
					  "id"=>$this->input->post('id'),
					  "remarks"=>$this->input->post('remarks')
					 );
		$this->load->model('linked_account_model');
		$result = $this->linked_account_model->updatePlayerLinkedAccountRemarks($data);
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS,lang("Linked Account remarks has been successfully updated!"));
		echo json_encode(array("success"=>$data));
	}

	public function getPlayerLinkedAccountDetailsById($id){
		$this->load->model('linked_account_model');
		$result['success'] = false;
		$result = $this->linked_account_model->getPlayerLinkedAccountDetailsById($id);
		if($result){
			$result['success'] = true;
		}
		echo $this->utils->encodeJson($result);
	}

	public function deletePlayerLinkedAccountById($linkedAccountId){
		$this->load->model('linked_account_model');
		$result = $this->linked_account_model->deletePlayerLinkedAccountById($linkedAccountId);
		$result['success'] = true;
		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS,lang("Linked Account has been successfully deleted!"));
		echo $this->utils->encodeJson($result);
	}

	/**
	 * overview : get linked account details
	 *
	 * detail : @return array data
	 *
	 * @param string $playerUsername
	 *
	 */
	private function getLinkedAccountDetails($playerUsername){
		$this->load->model('linked_account_model');
		return $this->linked_account_model->getLinkedAccount(array("username"=>$playerUsername,
																   "search_type"=>Linked_account_model::SEARCH_TYPE_EXACT_USERNAME,
																   "link_datetime"=>null));
	}

	/**
	 * overview : get non linked account players
	 *
	 * detail : @return json data
	 *
	 * @param string $username
	 *
	 */
	public function getNonLinkedAccountPlayers($username) {
		$this->load->model(array('player_model','linked_account_model'));

		$q = $this->input->get('q');

		$linkId = $this->linked_account_model->getLinkedAccountByUsername($username);
		$playerIds = array();
		if(!empty($linkId['link_id'])) {
			$playerIds = $this->linked_account_model->getLinkAccountsPlayerIds($linkId['link_id']);
			$playerIds = array_column($playerIds,'playerId');
		}

		// -- Add current player ID from exceptions
		$playerIds[] = $this->player_model->getPlayerIdByUsername($username);


		$players = $this->linked_account_model->getAvailablePlayers($q,$playerIds);
		if(!empty($players)){
			array_walk($players, function(&$player) {
				$player = array(
					'id' 	=> $player->playerId,
					'text' 	=> $player->username,
				);
			});
		}

		$data = array(
			'items' => $players,
		);

		$this->returnJsonResult($data);
	}

	public function addPlayerLinkedAccount(){
		$linkAccountIds = $this->input->post('linkedAccountsId');
		$username = $this->input->post('username');
		$remarks = $this->input->post('remarks');

		if(!$linkAccountIds || !$username) redirect(current_url());

		$this->load->model(array('linked_account_model','player_model'));

		$linkId = null;
		$result = $this->linked_account_model->getPlayerLinkedAccountLinkIdByUsername($username);
		if(!empty($result)){
			$linkId = $result['link_id'];
		}

		# IF LINK ID DOES NOT EXISTS IT MEANS THE CURRENT PLAYER IS NOT IN THE LINKED ACCOUNT LIST YET
		# IN THIS CASE WE WILL GENERATE LINK ID AND APPLY TO ALL LINKED ACCOUNTS THAT IS LINKED TO THIS PLAYER
		if(!$linkId) {
			$linkId = $this->generateLinkId();

			$linkedAccount = array(
								   "link_id"=>$linkId,
								   "username"=>$username,
								   "remarks"=>$remarks,
								   "admin_user_id"=>$this->authentication->getUserId(),
								   "link_datetime"=>$this->utils->getNowForMySql()
								  );
			$this->addLinkedAccountsToDB($linkedAccount);
		}

		$this->linkAccountIdsFromUsername($linkAccountIds,$linkId,$remarks);

		$this->alertMessage(self::MESSAGE_TYPE_SUCCESS,lang("Linked Account has been successfully added!"));
		echo json_encode(array("success"=>true));
	}

	private function generateLinkId(){
		return random_string("numeric",6);
	}

	private function linkAccountIdsFromUsername($linkedAccountsId,$linkId,$remarks=null){

		foreach ($linkedAccountsId as $key) {
			$username = $this->player_model->getUsernameById($key);

			$isLinkAccountExists = $this->linked_account_model->isLinkAccountExists($username);
			if($isLinkAccountExists){
				#GET OLD LINKID OF THIS PLAYER THEN UPDATE LINK ID BASED ON NEW LINK ID OF THE PLAYERS CONNECTED BEFORE
				$oldLinkId = $this->linked_account_model->getPlayerLinkedAccountLinkIdByUsername($username)['link_id'];
				$this->updateOldLinkIdToNewLinkId($oldLinkId,$linkId);

			}else{

				$linkedAccounts = array("link_id"=>$linkId,
										"username"=>$username,
										"admin_user_id"=>$this->authentication->getUserId(),
										"remarks"=>$remarks,
										"link_datetime"=>$this->utils->getNowForMySql()
										);

				$this->addLinkedAccountsToDB($linkedAccounts);
			}
		}
	}

	private function updateOldLinkIdToNewLinkId($oldLinkId,$newLinkId){
		$this->load->model(array('linked_account_model'));
		$data = array("link_id"=>$newLinkId);

		$this->linked_account_model->updateOldLinkIdToNewLinkId($oldLinkId,$data);
	}

	private function addLinkedAccountsToDB($linkedAccounts){
		$this->load->model(array('linked_account_model'));
		$this->linked_account_model->addLinkedAccounts($linkedAccounts);
	}

	private function updateLinkedAccountsToDB($linkedAccounts){
		$this->load->model(array('linked_account_model'));
		$this->linked_account_model->updateLinkedAccounts($linkedAccounts);
	}

	public function linkAcctByBatch() {
		$retval = [ 'success' => false, 'message' => 'execution_incomplete', 'result' => null ];

		// $tagID = $this->input->post('tagId');
		$playerIDs = $this->input->post('linkAcctsplayerIDs');
		$linkAcctIds = explode(',', $playerIDs);

		$playerUserId = $this->input->post('linkAcctsplayerUserId');
		$remarks = $this->input->post('remarks');

		try {

			if (empty($playerIDs)) {
				throw new Exception('playerIDs empty');
			}

			$this->linkPlayerFromDuplicateAcctList($playerUserId,$linkAcctIds,$remarks);
			$retval = [ 'success' => true ];
		}
		catch (Exception $e) {
			$retval = [ 'success' => false, 'message' => $e->getMessage(), 'result' => null ];
		}
		finally {
			$this->returnJsonResult($retval);
		}
	}

	public function linkPlayerFromDuplicateAcctList($playerUserId,$linkAccountIds,$remarks){
		$this->load->model(array('linked_account_model','player_model'));

		$username = $this->player_model->getPlayerUsername($playerUserId)['username'];

		$linkId = null;
		// $remarks = "";
		$result = $this->linked_account_model->getPlayerLinkedAccountLinkIdByUsername($username);

		if(!empty($result)){
			$linkId = $result['link_id'];
		}

		# IF LINK ID DOES NOT EXISTS IT MEANS THE CURRENT PLAYER IS NOT IN THE LINKED ACCOUNT LIST YET
		# IN THIS CASE WE WILL GENERATE LINK ID AND APPLY TO ALL LINKED ACCOUNTS THAT IS LINKED TO THIS PLAYER
		if(!$linkId) {
			$linkId = $this->generateLinkId();

			$linkedAccount = array(
								   "link_id"=>$linkId,
								   "username"=>$username,
								   "remarks"=>$remarks,
								   "admin_user_id"=>$this->authentication->getUserId(),
								   "link_datetime"=>$this->utils->getNowForMySql()
								  );
			$this->addLinkedAccountsToDB($linkedAccount);
		}

		$this->linkAccountIdsFromUsername($linkAccountIds,$linkId);
	}
}

///END OF FILE
