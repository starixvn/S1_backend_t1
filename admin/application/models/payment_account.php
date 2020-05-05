<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * http://ogdev.ddns.net:8090/display/OG/PaymentAccount
 *
 * banktype.bankTypeId=payment_account.payment_type_id
 *
 * General behaviors include
 * * Get payment account
 * * enable/disable payment account
 * * change the status of payment account
 * * add/update payment account
 * * replace applicable player level
 * * replace applicable affiliate
 * * replace applicable player
 * * delete player level limit for a certain payment account
 * * insert a player levels for a certain payment account
 * * get all payment account details
 * * get player level limit for a certain payment account
 * * get affiliate limit for a certain payment account
 * * get player limit for a certain payment account
 * * get payment account details for a certain payment account
 * * delete selected payment account
 * * get available account for a certain player
 * * sync record from payment gateway
 * * truncate tables
 *
 * @category Payment Model
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */

class Payment_account extends BaseModel {

	protected $tableName = 'payment_account';
	protected $levelTableName = 'payment_account_player_level';

	protected $statusField = 'status';
	protected $flagField = 'flag';
	protected $idField = 'id';

	# FLAG
	const FLAG_MANUAL_ONLINE_PAYMENT = MANUAL_ONLINE_PAYMENT;
	const FLAG_AUTO_ONLINE_PAYMENT = AUTO_ONLINE_PAYMENT;
	const FLAG_MANUAL_LOCAL_BANK = LOCAL_BANK_OFFLINE;

	const STATUS_ACTIVE = 1;
	const STATUS_DELETE = 3;

	public function __construct() {
		parent::__construct();
	}

    protected function applyVIPruleData($payment_account, $depositRule){
        if(empty($depositRule)){
            $payment_account->vip_rule_min_deposit_trans = $payment_account->min_deposit_trans;
            $payment_account->vip_rule_max_deposit_trans = $payment_account->max_deposit_trans;
            return $payment_account;
        }

        $depositRuleMinDeposit = isset($depositRule[0]['minDeposit']) ? $depositRule[0]['minDeposit'] : $this->utils->getConfig('defaultMinDepositDaily');
        $depositRuleMinDeposit = ($depositRuleMinDeposit === FALSE) ? 0 : $depositRuleMinDeposit;
        $depositRuleMaxDeposit = isset($depositRule[0]['maxDeposit']) ? $depositRule[0]['maxDeposit'] : $this->utils->getConfig('defaultMaxDepositDaily');

        // payment min
        $paymentAccountMinDeposit = $payment_account->min_deposit_trans;

        // payment max
        $paymentAccountMaxDeposit = $payment_account->max_deposit_daily;
        if ($payment_account->max_deposit_trans > 0 && $payment_account->max_deposit_trans < $paymentAccountMaxDeposit) {
            $paymentAccountMaxDeposit = $payment_account->max_deposit_trans;
        }

        // check vip rule min
        if ($paymentAccountMinDeposit > $depositRuleMinDeposit) {
            $minDeposit = $paymentAccountMinDeposit;
        } elseif ($depositRuleMinDeposit > 0) {
            $minDeposit = $depositRuleMinDeposit;
        } else {
            $minDeposit = 0;
        }

        // check vip rule max
        if ($depositRuleMaxDeposit > 0 && $depositRuleMaxDeposit < $paymentAccountMaxDeposit) {
            $maxDeposit = $depositRuleMaxDeposit;
        } elseif ($paymentAccountMaxDeposit > 0) {
            $maxDeposit = $paymentAccountMaxDeposit;
        } else {
            $maxDeposit = 0;
        }

        $payment_account->vip_rule_min_deposit_trans = $minDeposit;
        $payment_account->vip_rule_max_deposit_trans = $maxDeposit;

        return $payment_account;
    }

    protected function applyResponsibleGamingMaxDeposit($payment_account, $playerId){
        if(empty($payment_account)){
            return  $payment_account;
        }

        if($this->utils->isEnabledFeature('responsible_gaming')) {
            $this->load->library('player_responsible_gaming_library');
            $resDeposit= $this->player_responsible_gaming_library->getDepositLimit($playerId);
            if($resDeposit['status']){
                if($resDeposit['value'] < $payment_account->vip_rule_max_deposit_trans){
                    $payment_account->vip_rule_max_deposit_trans = $resDeposit['value'];
                }
            }
        }
        return $payment_account;
    }

    protected function appendAdditionalData($payment_account){
        if(empty($payment_account)){
            return null;
        }

        $payment_account->account_image_url = (empty($payment_account->account_image_filepath)) ? NULL : $this->utils->getSystemUrl('player', $this->utils->imageUrl('account/' . $payment_account->account_image_filepath));
        $payment_account->account_icon_url = (empty($payment_account->account_icon_filepath)) ? NULL : $this->utils->getSystemUrl('player', $this->utils->imageUrl('account/' . $payment_account->account_icon_filepath));
        $payment_account->bank_icon_url = (empty($payment_account->bankIcon)) ? NULL : Banktype::getBankIcon($payment_account->bankIcon);

        // $this->utils->debug_log('account_image_filepath', $payment_account->account_image_filepath, $payment_account->account_image_url);

        return $payment_account;
    }

	/**
	 * detail: get payment account details using id
	 *
	 * @param int $id payment account id
	 * @return int
	 */
	public function getPaymentAccount($id) {
		if (!empty($id)) {
			$payment_account = $this->getOneRowById($id);

            if(empty($payment_account)){
                return FALSE;
            }

            return $this->appendAdditionalData($payment_account);
		}
		return null;
	}

    public function getPaymentAccountWithVIPRule($id, $playerId){
		$this->load->model(array('banktype'));

        $payment_account = $this->getPaymentAccount($id);
        if(empty($payment_account)){
            return null;
        }

        $payment_account->payment_account_id = $payment_account->id;

		$banktype = $this->banktype->getBankTypeById($payment_account->payment_type_id);
        if(empty($banktype)){
            return null;
        }
        $payment_account->bankTypeId = $banktype->bankTypeId;
        $payment_account->bankIcon = $banktype->bankIcon;
        $payment_account->payment_type = $banktype->bankName;

        $depositRule = $this->CI->group_level->getPlayerDepositRule($playerId);
        $payment_account = $this->applyVIPruleData($payment_account, $depositRule);
        $payment_account = $this->applyResponsibleGamingMaxDeposit($payment_account, $playerId);
        return $payment_account;
    }

	/**
	 * detail: disable a certain payment account
	 *
	 * @param int $id payment account id
	 * @return boolean
	 */
	public function disablePaymentAccount($id) {
		$this->changeStatusPaymentAccount($id, self::STATUS_DISABLED);
	}

	/**
	 * detail: enable a certain payment account
	 *
	 * @param int $id payment account id
	 * @return boolean
	 */
	public function enablePaymentAccount($id) {
		$this->changeStatusPaymentAccount($id, self::STATUS_NORMAL);
	}

	/**
	 * detail: change the status of a certain payment account
	 *
	 * @param int $id payment account id
	 * @param string $status
	 * @return boolean
	 */
	public function changeStatusPaymentAccount($id, $status) {
		$this->db->where($this->idField, $id);
		$this->db->update($this->tableName, array($this->statusField => $status));
	}

	/**
	 * detail: check the disbled status of payment account
	 *
	 * @param int $secureId
	 * @return boolean
	 */
	public function checkPaymentAccountStatusDisabled($secureId) {
		$query = $this->db->query("SELECT pa.status , pa.id , so.payment_account_id FROM payment_account as pa LEFT JOIN sale_orders AS so ON pa.id LIKE so.payment_account_id WHERE so.secure_id = '$secureId' AND pa.status = 2"); // STATUS_DISABLED = 2

		if (!$query->row_array()) {
			return false;
		} else {
			return $query->result_array();
		}
	}

	/**
	 * detail: add new payment account
	 *
	 * @param array $paymentAccount
	 * @param string $player_levels
	 * @param string $affiliates
	 * @param string $players
	 * @return bolean
	 */
	public function addPaymentAccount($paymentAccount, $player_levels = null, $affiliates = null, $agents = null, $players = null) {
		$this->startTrans();
		$paymentAccountId = $this->insertPaymentAccount($paymentAccount);

		if ($player_levels !== null) {
			$this->replaceApplicablePlayerLevels($paymentAccountId, $player_levels);
		}

		if ($affiliates !== null) {
			$this->replaceApplicableAffiliates($paymentAccountId, $affiliates);
		}

		if ($agents !== null) {
			$this->replaceApplicableAgents($paymentAccountId, $agents);
		}

		if ($players !== null) {
			$this->replaceApplicablePlayers($paymentAccountId, $players);
		}

		$this->endTrans();

		return $this->succInTrans();

	}

	/**
	 *
	 * detail: edit a certain payment account
	 *
	 * @param int $paymentAccountId payment account id
	 * @param array $paymentAccount
	 * @param string $player_levels
	 * @param string $affiliates
	 * @param string $players
	 * @return bolean
	 */
	public function editPaymentAccount($paymentAccountId, $paymentAccount, $player_levels = null, $affiliates = null, $agents = null, $players = null) {
		$this->startTrans();
		$this->updatePaymentAccount($paymentAccountId, $paymentAccount);

		if ($player_levels !== null) {
			$this->replaceApplicablePlayerLevels($paymentAccountId, $player_levels);
		}

		if ($affiliates !== null) {
			$this->replaceApplicableAffiliates($paymentAccountId, $affiliates);
		}

		if ($agents !== null) {
			$this->replaceApplicableAgents($paymentAccountId, $agents);
		}

		if ($players !== null) {
			$this->replaceApplicablePlayers($paymentAccountId, $players);
		}

		$this->endTrans();

		return $this->succInTrans();
	}

	/**
	 * detail: replace applicable player levels for a certain payment account
	 *
	 * @param int $paymentAccountId payment account id
	 * @param string $player_levels
	 * @return bolean
	 */
	public function replaceApplicablePlayerLevels($paymentAccountId, $player_levels) {
		$this->db->delete('payment_account_player_level', array('payment_account_id' => $paymentAccountId));
		if (!empty($player_levels)) {
			array_walk($player_levels, function (&$player_level, $index, $paymentAccountId) {
				$player_level = array('payment_account_id' => $paymentAccountId, 'player_level_id' => $player_level);
			}, $paymentAccountId);
			$this->db->insert_batch('payment_account_player_level', $player_levels);
		}
	}

	/**
	 * detail: replace applicable affiliates for a certain payment account
	 *
	 * @param int $paymentAccountId payment account id
	 * @param string $affiliates
	 * @return boolean
	 */
	public function replaceApplicableAffiliates($paymentAccountId, $affiliates) {
		$this->db->delete('payment_account_affiliate', array('payment_account_id' => $paymentAccountId));
		if (!empty($affiliates)) {
			array_walk($affiliates, function (&$affiliate, $index, $paymentAccountId) {
				$affiliate = array('payment_account_id' => $paymentAccountId, 'affiliate_id' => $affiliate);
			}, $paymentAccountId);
			$this->db->insert_batch('payment_account_affiliate', $affiliates);
		}
	}

	public function replaceApplicableAgents($paymentAccountId, $agents) {
		$this->db->delete('payment_account_agent', array('payment_account_id' => $paymentAccountId));
		if (!empty($agents)) {
			array_walk($agents, function (&$agent, $index, $paymentAccountId) {
				$agent = array('payment_account_id' => $paymentAccountId, 'agent_id' => $agent);
			}, $paymentAccountId);
			$this->db->insert_batch('payment_account_agent', $agents);
		}
	}

	/**
	 * detail: replace applicable players for a certain payment account
	 *
	 * @param int $paymentAccountId payment account id
	 * @param string $players
	 * @return boolean
	 */
	public function replaceApplicablePlayers($paymentAccountId, $players) {
		$this->db->delete('payment_account_player', array('payment_account_id' => $paymentAccountId));
		if (!empty($players)) {
			array_walk($players, function (&$player, $index, $paymentAccountId) {
				$player = array('payment_account_id' => $paymentAccountId, 'player_id' => $player);
			}, $paymentAccountId);
			$this->db->insert_batch('payment_account_player', $players);
		}
	}

	/**
	 * detail: delete the player level limit for a certain player of payment account
	 *
	 * @param int $paymentAccountId payment account id
	 * @return boolean
	 */
	public function deletePaymentAccountPlayerLevelLimit($paymentAccountId) {
		// $where = "payment_account_id = '" . $paymentAccountId . "'";
		$this->db->where('payment_account_id', $paymentAccountId);
		$this->db->delete($this->levelTableName);
	}

	/**
	 * detail: add new payment account player level
	 *
	 * @param int $paymentAccountId payment account id
	 * @param int $levelId level id
	 * @return boolean
	 */
	public function insertPayemntAccountPlayerLevels($paymentAccountId, $levelId) {
		$data['payment_account_id'] = $paymentAccountId;
		$data['player_level_id'] = $levelId; //link to vipsettingcashbackrule table (vipsettingcashbackruleId)
		// $this->db->insert($this->levelTableName, $data);
		return $this->insertData($this->levelTableName, $data);
	}

	/**
	 * @param array $data
	 * @return boolean
	 */
	public function addPayemntAccountPlayerLevels($data) {
		$this->db->insert($this->levelTableName, $data);
	}

	/**
	 * detail: insert payment account
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function insertPaymentAccount($data) {
		return $this->insertRow($data);
	}

	/**
	 * detail: update the certain payment account
	 *
	 * @param int $id payment account id
	 * @param array $data
	 * @return boolean
	 */
	public function updatePaymentAccount($id, $data) {
		return $this->updateRow($id, $data);
	}

	/**
	 * detail: get all payment account details
	 *
	 * @param string $sort
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getAllPaymentAccountDetails($sort = 'payment_order', $limit = null, $offset = null) {
		$this->db->select("banktype.bankName as payment_type, admin1.username as created_by, admin2.username as updated_by,payment_account.id as payment_account_id, payment_account.*");
		$this->db->from($this->tableName);
		$this->db->join('banktype', 'banktype.bankTypeId=' . $this->tableName . '.payment_type_id', 'left');
		$this->db->join('adminusers AS admin1', 'admin1.userId = ' . $this->tableName . '.created_by_userid', 'left');
		$this->db->join('adminusers AS admin2', 'admin2.userId = ' . $this->tableName . '.updated_by_userid', 'left');
		$this->db->where('payment_account.status !=', self::STATUS_DELETE);
		if ($sort) {
			if (is_array($sort)) {
				foreach ($sort as $key => $value) {
					$this->db->order_by($key, $value);
				}
			} else {
				$this->db->order_by($sort);
			}
		}
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$qry = $this->db->get();
		$rows = $this->getMultipleRow($qry);
		if (!empty($rows)) {
			$paymentAccountFlags = $this->utils->getPaymentAccountAllFlagsKV();
			foreach ($rows as $list_item) {
				$list_item->flag_name = isset($paymentAccountFlags[$list_item->flag]) ? $paymentAccountFlags[$list_item->flag] : '';
				$list_item->player_levels = $this->getPaymentAccountPlayerLevelLimit($list_item->id);
			}
		}
		return $rows;
	}

	/** spencer.kuo */
	public function getAllPaymentAccountInfo($sort = 'payment_order', $limit = null, $offset = null) {
		$this->db->select("banktype.bankName as payment_type, payment_account.id as payment_account_id, payment_account.flag, payment_account.payment_account_name, payment_account.payment_account_number");
		$this->db->from($this->tableName);
		$this->db->join('banktype', 'banktype.bankTypeId=' . $this->tableName . '.payment_type_id', 'left');
		$this->db->where('payment_account.status !=', self::STATUS_DELETE);
		if ($sort) {
			if (is_array($sort)) {
				foreach ($sort as $key => $value) {
					$this->db->order_by($key, $value);
				}
			} else {
				$this->db->order_by($sort);
			}
		}
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$qry = $this->db->get();
		$rows = $this->getMultipleRow($qry);
		if (!empty($rows)) {
			$paymentAccountFlags = $this->utils->getPaymentAccountAllFlagsKV();
			foreach ($rows as $list_item) {
				$list_item->flag_name = isset($paymentAccountFlags[$list_item->flag]) ? $paymentAccountFlags[$list_item->flag] : '';
				$list_item->payment_type_name = lang($list_item->payment_type);
			}
		}
		return $rows;
	}
	/**
	 * detail: get a certain payment account player level limit
	 *
	 * @param int $paymentAccountId payment account id
	 * @return array
	 */
	public function getPaymentAccountPlayerLevelLimit($paymentAccountId) {
		$this->db->select('vipsettingcashbackrule.vipsettingcashbackruleId,
						   vipsettingcashbackrule.vipLevel,
						   vipsettingcashbackrule.vipLevelName,
						   vipsetting.groupName')
			->from($this->levelTableName)
			->join('vipsettingcashbackrule', 'vipsettingcashbackrule.vipsettingcashbackruleId = ' . $this->levelTableName . '.player_level_id', 'left')
			->join('vipsetting', 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId', 'left');
		$this->db->where($this->levelTableName . '.payment_account_id', $paymentAccountId);

		$query = $this->db->get();

		return $this->getMultipleRow($query);
	}

	/**
	 * detail: get a certain payment account affiliate limit
	 *
	 * @param int $paymentAccountId payment account id
	 * @return array
	 */
	public function getPaymentAccountAffiliateLimit($paymentAccountId) {
		$this->db->select('affiliates.affiliateId, affiliates.username')
			->from('payment_account_affiliate')
			->join('affiliates', 'affiliates.affiliateId = payment_account_affiliate.affiliate_id', 'left');
		$this->db->where('payment_account_affiliate.payment_account_id', $paymentAccountId);

		$query = $this->db->get();

		return $this->getMultipleRow($query);
	}

	public function getPaymentAccountAgentLimit($paymentAccountId) {
		$this->db->select('agency_agents.agent_id, agency_agents.agent_name')
			->from('payment_account_agent')
			->join('agency_agents', 'agency_agents.agent_id = payment_account_agent.agent_id', 'left');
		$this->db->where('payment_account_agent.payment_account_id', $paymentAccountId);

		$query = $this->db->get();

		return $this->getMultipleRow($query);
	}

	/**
	 * detail: get a certain payment account player limit
	 *
	 * @param int $paymentAccountId payment account id
	 * @return array
	 */
	public function getPaymentAccountPlayerLimit($paymentAccountId) {
		$this->db->select('player.playerId, player.username')
			->from('payment_account_player')
			->join('player', 'player.playerId = payment_account_player.player_id', 'left');
		$this->db->where('payment_account_player.payment_account_id', $paymentAccountId);

		$query = $this->db->get();

		return $this->getMultipleRow($query);
	}

	/**
	 * detail: get payment account details by id
	 *
	 * @param int $id payment account id
	 * @return array
	 */
	public function getPaymentAccountDetails($id) {

		$row = $this->getPaymentAccount($id);

		if ($row) {
			$row->player_levels = $this->getPaymentAccountPlayerLevelLimit($row->id);
			$row->affiliates = $this->getPaymentAccountAffiliateLimit($row->id);
			$row->agents = $this->getPaymentAccountAgentLimit($row->id);
			$row->players = $this->getPaymentAccountPlayerLimit($row->id);
		}

		return $row;
	}

	/**
	 * detail: delete selected payment accounts
	 *
	 * @param array $idArr
	 * @return boolean
	 */
	public function batchDeletePaymentAccount($idArr) {
		return $this->batchSoftDelete($idArr);
	}

	/**
	 * detail: delete a certain payment account
	 *
	 * @param array $id
	 * @return boolean
	 */
	public function deletePaymentAccount($id) {
		return $this->softDelete($id);
	}

	/**
	 * detail: get the next order in the lists
	 *
	 * @return int
	 */
	public function getNextOrder() {
		$lastOrder = $this->getLastOrder();
		return $lastOrder += 10;
	}

	/**
	 * detail: get the last order in the lists
	 *
	 * @return int
	 */
	public function getLastOrder() {
		$this->db->select('payment_order')->from($this->tableName)->order_by('payment_order', 'desc')->limit(1);
		$qry = $this->db->get();
		$ord = $this->getOneRowOneField($qry, 'payment_order');
		if ($ord) {
			return intval($ord);
		}
		return self::DEFAULT_START_ORDER;
	}

	/**
	 * detail: Get all payment account to be exported
	 *
	 * @return array
	 */
	public function getPaymentAccountListToExport() {

		$this->db->select("banktype.bankName as payment_type, admin1.username as created_by, admin2.username as updated_by,payment_account.id as payment_account_id, " .
			"payment_account.payment_account_name, payment_account.payment_account_number, payment_account.payment_branch_name," .
			"payment_account.max_deposit_daily , payment_account.total_deposit, payment_account.flag, payment_account.created_at, payment_account.updated_at, payment_account.status");
		$this->db->from($this->tableName);
		$this->db->join('banktype', 'banktype.bankTypeId=' . $this->tableName . '.payment_type_id', 'left');
		$this->db->join('adminusers AS admin1', 'admin1.userId = ' . $this->tableName . '.created_by_userid', 'left');
		$this->db->join('adminusers AS admin2', 'admin2.userId = ' . $this->tableName . '.updated_by_userid', 'left');
		$this->addDefaultStatusWhere();

		// $query = $this->db->get();
		return $this->runMultipleRowArray();
	}

	/**
	 * detail: get available account for a certain player
	 *
	 * @param int $playerId player id
	 * @param int $flag
	 * @param int $bankTypeId bank type id
	 * @param boolean $list
	 * @param int $payment_account_id payment account id
	 * @return array
	 */
	public function getAvailableAccount($playerId, $flag, $bankTypeId = null, $list = false, $payment_account_id= null) {

		$this->load->model(array('group_level', 'transactions'));

		$player = $this->player_model->getPlayerById($playerId);
        $filter_payment_accounts_by_player_dispatch_account_level=$this->utils->isEnabledFeature('filter_payment_accounts_by_player_dispatch_account_level');

		$this->db->distinct();
		$this->db->select('payment_account.*');
		$this->db->select('payment_account.id as payment_account_id');
		$this->db->select('banktype.bankTypeId as bankTypeId');
		$this->db->select('banktype.bankIcon as bankIcon');
		$this->db->select('banktype.bankName as payment_type');
		$this->db->from('payment_account');
		$this->db->join('banktype', 'banktype.bankTypeId=payment_account.payment_type_id', 'left');
		$this->db->join('payment_account_player_level', 'payment_account_player_level.payment_account_id=payment_account.id', 'left');
        $this->db->join('payment_account_player', 'payment_account_player.payment_account_id=payment_account.id', 'left');

		if($filter_payment_accounts_by_player_dispatch_account_level) {
			$this->db->join('dispatch_account_level_payment_account', 'payment_account.id=dispatch_account_level_payment_account.payment_account_id', 'left');
			$this->db->join('dispatch_account_level', 'dispatch_account_level_payment_account.dispatch_account_level_id=dispatch_account_level.id', 'left');
			$this->db->where('dispatch_account_level_payment_account.dispatch_account_level_id', $player->dispatch_account_level_id);
			$this->db->where('dispatch_account_level.status', '1');
		}

		$this->db->where('payment_account.status !=', self::STATUS_DELETE);

		# vip level and player using 'and' to check
		if($this->config->item('payment_account_only_dispaly_to_certain_player') == true) {
			# PLAYER GROUP LEVEL
			$this->db->where('payment_account_player_level.player_level_id = ' . $player->levelId);

			# PLAYER
			$this->db->where('(((SELECT count(id) FROM payment_account_player WHERE payment_account_id = payment_account.id) > 0 AND player_id = '. $playerId . ') or (SELECT count(id) FROM payment_account_player WHERE payment_account_id = payment_account.id) = 0)', NULL, FALSE);
		}
		# vip level and player using 'or' to check
		else {
			# PLAYER GROUP LEVEL
			$this->db->where('(payment_account_player_level.player_level_id = ' . $player->levelId, null, false);

			# PLAYER
			$this->db->or_where('payment_account_player.player_id = ' . $playerId . ')', null, false);
		}

		$this->addDefaultStatusWhere();

		if(!empty($payment_account_id)){
			$this->db->where('payment_account.id', $payment_account_id);
		}

		if ( ! empty($bankTypeId)) {
			$this->db->where('banktype.bankTypeId', $bankTypeId);
		}else{
			if ( ! empty($flag)) {
				$this->db->where('payment_account.flag', $flag);
				if ($flag == self::FLAG_AUTO_ONLINE_PAYMENT) {
					$this->db->where('payment_account.external_system_id is not null', null, false);
					$this->db->where('payment_account.external_system_id != \'0\'', null, false);
				}
			}
		}

		$this->db->order_by('payment_order');

		$rows = $this->runMultipleRow();
		$paymentAccountFlags = $this->utils->getPaymentAccountAllFlagsKV();

		if ( ! empty($rows)) {
		    $self = $this;
			$payment_accounts = array_filter($rows, function(&$row) use ($paymentAccountFlags, $self, $player) {
				$row->payment_flag = $paymentAccountFlags[$row->flag];
				$totalDepositDaily = $row->daily_deposit_amount;
				$totalDeposit = $row->total_deposit_amount;

                $agents = $self->getAgentByPaymentAccountId($row->id);
                if(!empty($agents)){
                    $in_agent = FALSE;
                    foreach($agents as $agent){
                        $in_agent = ($agent['agent_id'] == $player->agent_id) ? TRUE : $in_agent;
                    }

                    if($in_agent === FALSE) return FALSE;
                }

                $affiliates = $self->getAffiliateByPaymentAccountId($row->id);
                if(!empty($affiliates)){
                    $in_affiliate = FALSE;
                    foreach($affiliates as $affiliate){
                        $in_affiliate = ($affiliate['affiliate_id'] == $player->affiliateId) ? TRUE : $in_affiliate;
                    }

                    if($in_affiliate === FALSE) return FALSE;
                }

				// $this->utils->debug_log($row->payment_account_id.':'.$totalDeposit.' totalDepositDaily:'.$totalDepositDaily);
				return $row->total_deposit > $totalDeposit && $row->max_deposit_daily > $totalDepositDaily;
			});
            if(!empty($payment_accounts)){
                $depositRule = $this->CI->group_level->getPlayerDepositRule($playerId);
                foreach($payment_accounts as &$payment_account){
                    $payment_account = $this->applyVIPruleData($payment_account, $depositRule);
                    $payment_account = $this->applyResponsibleGamingMaxDeposit($payment_account, $playerId);
                    $payment_account = $this->appendAdditionalData($payment_account);
                }

				return $list ? $payment_accounts : array_shift($payment_accounts);
            }
		}

		return null;
	}

    public function getAgentByPaymentAccountId($payment_account_id){
        $this->db->from('payment_account_agent');
        $this->db->where('payment_account_id', $payment_account_id);
        $rows = $this->runMultipleRowArray();

        return (array)$rows;
    }

    public function getAffiliateByPaymentAccountId($payment_account_id){
        $this->db->from('payment_account_affiliate');
        $this->db->where('payment_account_id', $payment_account_id);
        $rows = $this->runMultipleRowArray();

        return (array)$rows;
    }

    public function getAvailableDefaultCollectionAccount($playerId, $flag = null, $bankTypeId = null, $list = true, $force_mobile = false){
		$this->CI->load->model(['operatorglobalsettings','playerbankdetails']);

        $payment_accounts = $this->getAvailableAccount($playerId, $flag, $bankTypeId, TRUE);
        if(empty($payment_accounts)){
            return null;
        }

        $total_deposits =  $this->playerbankdetails->PlayerApprovedDepositCount($playerId);
        $player_approved_deposit_count = $total_deposits[0]['approved_deposit_count'];


		$payment_account_types = $this->CI->operatorglobalsettings->getPaymentAccountTypes();
		$special_payment_list = ($this->CI->utils->is_mobile() || $force_mobile) ? $this->CI->operatorglobalsettings->getSpecialPaymentListMobile() : $this->CI->operatorglobalsettings->getSpecialPaymentList();

        if(empty($special_payment_list)){
            return $payment_accounts;
        }

        $filter_payment_accounts = [];
        foreach($payment_accounts as $payment_account){
            if(!in_array($payment_account->id, $special_payment_list)){
                continue;
            }

            if (!empty($payment_account->total_approved_deposit_count) &&
            	$player_approved_deposit_count < $payment_account->total_approved_deposit_count) {
            		continue;
            }

            switch($payment_account->flag){
                case self::FLAG_AUTO_ONLINE_PAYMENT:
                    if(empty($payment_account->external_system_id)){
                        continue;
                    }
                    break;
                default:
                    break;
            }

            $filter_payment_accounts[$payment_account->flag][] = $payment_account;
        }

        foreach($payment_account_types as $type => $payment_account_type){
            $payment_account_types[$type]['list'] = ($payment_account_type['enabled'] && !empty($filter_payment_accounts[$type])) ? $filter_payment_accounts[$type] : [];
        }

        return $payment_account_types;
    }

	/**
	 * detail: check payment account limit
	 *
	 * @param int $id payment account id
	 * @return boolean
	 */
	public function checkPaymentAccountLimit($id) {
		if (!empty($id)) {
			$paymentAccount = $this->getPaymentAccount($id);
			//calc total deposit in transactions by payment account
			$this->load->model(array('transactions'));
			$totalDeposit = $this->transactions->totalDepositByPaymentAccountId($id);
			if ($paymentAccount->total_deposit > 0 && $totalDeposit > $paymentAccount->total_deposit) {
				//disable payment account
				$this->disablePaymentAccount($id);
				$this->utils->debug_log(self::DEBUG_TAG, 'disabled payment account:' . $id);
				return true;
			}
		}
		return false;

		// foreach ($banks as $row) {
		// 	$localbankdata['otcPaymentMethodId'] = $row['otcPaymentMethodId'];
		// 	$localbankdata['updatedOn'] = date("Y-m-d H:i:s");
		// 	$localbankdata['updatedBy'] = $this->authentication->getUserId();

		// 	$totalBankDeposit = $row['totalDeposit'][0]['totalBankDeposit'];

		// 	if ($totalBankDeposit != null && $totalBankDeposit >= $row['dailyMaxDepositAmount']) {
		// 		$localbankdata['status'] = 'inactive';
		// 	} else if ($totalBankDeposit < $row['dailyMaxDepositAmount']) {
		// 		$localbankdata['status'] = 'active';
		// 	}
		// 	$this->bankaccount_manager->activateBankAccount($localbankdata);
		// }

	}

	/**
	 * detail: check payment account limit
	 *
	 * @param int $id payment account id
	 * @return boolean
	 */
	public function checkIfPaymentAccountOverLimitPercentage($id) {
		if (!empty($id)) {
			$paymentAccount = $this->getPaymentAccount($id);
			//calc total deposit in transactions by payment account
			$this->load->model(array('transactions'));
			$totalDeposit = $this->transactions->totalDepositByPaymentAccountId($id);
			$payment_account_over_limit_percentage = $this->config->item('payment_account_over_limit_percentage');
			$account_deposit_percentage = round( ($totalDeposit) / ($paymentAccount->total_deposit) *100, 1);
			$result_msg =
				"payment account id: " .$id. " | \n".
				"payment account name: " .$paymentAccount->payment_account_name. " | \n".
				"payment account total deposit so far: " .$totalDeposit. " | \n".
				"payment account total deposit limit: " .$paymentAccount->total_deposit. " | \n".
				"payment account deposit percentage: " .$account_deposit_percentage."%";
			if (
				$paymentAccount->total_deposit > 0
				&&
				$account_deposit_percentage > $payment_account_over_limit_percentage
			) {
				$this->utils->debug_log(
					self::DEBUG_TAG, '============checkIfPaymentAccountOverLimitPercentage true, '.$result_msg
				);

				$result_msg = "=============== Payment Account Total Amount Percentage Notification ===============\nThe total amount percentage of the payment_account is over ".$payment_account_over_limit_percentage."% now.\n".$result_msg;

				return array('success' => true, 'msg' => $result_msg);
			}

			$this->utils->debug_log(
				self::DEBUG_TAG, '============checkIfPaymentAccountOverLimitPercentage false, '.$result_msg
			);
		}
		return array('success' => false, 'msg' => 'Not over limit percentage yet.');
	}

	/**
	 * Based on payment account's transaction fee setting, calculate transaction fee
	 *
	 * @return double Calculated transaction fee
	 */
	public function getTransactionFee($id, $amount) {
		if (empty($id)) {
			return 0;
		}

		$paymentAccount = $this->getPaymentAccount($id);

		$min = $paymentAccount->min_deposit_fee;
		$max = $paymentAccount->max_deposit_fee;
		$percentage = $paymentAccount->deposit_fee_percentage;

		$transFee = $amount * $percentage / 100;

		if ($transFee < $min) {
			return $min;
		}
		if ($transFee > $max && $max >= 0.01) {
			return $max;
		}
		return $transFee;
	}

	/**
	 * detail: check exists payment gate way
	 * note: only for external system, if old , ignore old id
	 *
	 * @param int $paymentTypeId payment type id
	 * @param int $id
	 * @return boolean or array
	 */
	public function existsPaymentGateway($paymentTypeId, $id) {
		$this->load->model(array('banktype'));
		if ($this->banktype->existsSystemId($paymentTypeId)) {
			$this->db->from($this->tableName)->where('payment_type_id', $paymentTypeId);

			if (!empty($id)) {
				//it's not new
				$this->db->where('id !=', $id);
			}

			$this->addDefaultStatusWhere();
			return $this->runExistsResult();
		}

		return false;
	}

	/**
	 * detail: sync record from payment gateway
	 *
	 * @param int $systemId system id
	 * @param int $adminUserId admin user id
	 * @return boolean or array
	 */
	public function syncPaymentGateway($systemId, $adminUserId) {
		$this->load->model(array('banktype', 'external_system'));
		$banktype = $this->banktype->getBanktypeBySystemId($systemId);
		if ($banktype) {

			$systemCode = $this->external_system->getNameById($systemId);

			$defaultMaxDepositDaily = $this->config->item('defaultMaxDepositDaily');
			$defaultTotalDeposit = $this->config->item('defaultTotalDeposit');

			$this->db->from($this->tableName)->where('payment_type_id', $banktype->bankTypeId);

			if (!$this->runExistsResult()) {
				//insert
				$data = array(
					'payment_type_id' => $banktype->bankTypeId,
					'payment_account_name' => $systemCode,
					'max_deposit_daily' => $defaultMaxDepositDaily,
					'total_deposit' => $defaultTotalDeposit,
					'status' => self::STATUS_NORMAL,
					'created_at' => $this->utils->getNowForMysql(),
					'updated_at' => $this->utils->getNowForMysql(),
					'external_system_id' => $systemId,
					'created_by_userid' => $adminUserId,
					'updated_by_userid' => $adminUserId,
					'flag' => self::FLAG_AUTO_ONLINE_PAYMENT,
				);
				// $this->db->insert($this->tableName, $data);
				$paymentAccountId = $this->insertData($this->tableName, $data);
				if ($paymentAccountId) {
					//add group level
					$this->deletePaymentAccountPlayerLevelLimit($paymentAccountId);
					$this->appendAllLevelToPaymentAccount($paymentAccountId);
				} else {
					$this->utils->debug_log('insert payment account failed', $data);
				}
			}
		}

		return true;
	}

	/**
	 * detail: remove Qr code image
	 *
	 * @param int $account_id account id
	 * @return array
	 */
	public function removeQrCodeImage($account_id) {
		$sql = <<<EOD
		UPDATE
		  `payment_account`
		SET
		  `payment_account`.`account_image_filepath` = ''
		WHERE `payment_account`.`id` = {$account_id}
EOD;
		$this->runRawUpdateInsertSQL($sql);

		return array("success" => 1);
	}

	/**
	 * detail: append or insert level to a certain payment acoount
	 *
	 * @param int $paymentAccountId payment account id
	 * @return boolean
	 */
	public function appendAllLevelToPaymentAccount($paymentAccountId) {
		$this->load->model(array('group_level'));
		//load all group level
		$levels = $this->group_level->getAllLevels();
		if (!empty($levels)) {
			foreach ($levels as $lvl) {
				$this->insertPayemntAccountPlayerLevels($paymentAccountId, $lvl->vipsettingcashbackruleId);
			}
		}
	}

	/**
	 * detail: getting a certain payment account id using system id
	 *
	 * @param int $systemId system id
	 * @return string
	 */
	public function getPaymentAccountIdBySystemId($systemId) {
		$paymentAccount = $this->getPaymentAccountBySystemId($systemId);
		if ($paymentAccount) {
			return $paymentAccount->id;
		}
		return null;
	}

	/**
	 * detail: get payment account records using system id
	 *
	 * @param int $systemId system id
	 * @return string or array
	 */
	public function getPaymentAccountBySystemId($systemId) {
		$this->load->model(array('banktype'));
		$banktype = $this->banktype->getBanktypeBySystemId($systemId);
		if ($banktype) {
			$this->db->from($this->tableName)->where('payment_type_id', $banktype->bankTypeId);
			return $this->runOneRow();
		}
		return null;
	}

	/**
	 * detail: checking if the bank id is existing
	 *
	 * @param string $payment_type
	 * @return boolean
	 */
	public function isHideBankInfo($payment_type) {
		$payment_account_hide_bank_info = $this->utils->getConfig('payment_account_hide_bank_info');
		$this->utils->debug_log('payment_type', $payment_type, 'payment_account_hide_bank_info', $payment_account_hide_bank_info);
		return in_array($payment_type, $payment_account_hide_bank_info);
	}

	/**
	 * detail: checking if the bank type is existing
	 *
	 * @param string $payment_type
	 * @return boolean
	 */
	public function isHideBankType($payment_type) {
		$payment_account_hide_bank_type = $this->utils->getConfig('payment_account_hide_bank_type');
		$this->utils->debug_log('payment_type', $payment_type, 'payment_account_hide_bank_type', $payment_account_hide_bank_type);
		return in_array($payment_type, $payment_account_hide_bank_type);
	}

	/**
	 * detail: Get all records from payment table
	 *
	 * @return array
	 */
	public function getAllPaymentAccount() {
		$sql = "SELECT * FROM " . $this->tableName;
		return $this->db->query($sql)->result_array();
	}

	/**
	 * detail: Get all payment account player level from payment_account_player_level table
	 *
	 * @return array
	 */
	public function getAllPaymentAccountPlayerLevel() {
		$sql = "SELECT * FROM " . $this->levelTableName;
		return $this->db->query($sql)->result_array();
	}

	/**
	 * detail: add new record to payment_account table
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function addRecord($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * detail: add new record to payment_account_player_level table
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function addRecordPlayerLevel($data) {
		return $this->db->insert($this->levelTableName, $data);
	}

	/**
	 * detail: truncate table payment_account and payment_account_player_level
	 *
	 * @param string $secret_key
	 * @return array
	 */
	public function truncateTablesSync($secret_key) {
		if ($secret_key == 'Ch0wK1ing&M@ng!n@s@l') {
			$this->db->truncate($this->tableName);
			$this->db->truncate($this->levelTableName);
			return array('success' => 1);
		}
		return array('success' => 0);
	}

	const DEBUG_TAG = '[payment_account]';

	/**
	 * overview: get the payment account details
	 *
	 * @param int $id
	 * @return Array
	 */
	public function getPaymentAccountInfo( $id = '' ){

		$qobj = $this->db->where('payment_type_id', $id)
						 ->get($this->tableName);

		return $qobj->row();

	}

	/**
	 * overview: get the payment type ID
	 *
	 * @param int $id
	 * @return int
	 */
	public function getPaymentTypeId( $id = '' ){

		$qobj = $this->db->where('id', $id)
						 ->get($this->tableName)
						 ->row();

		return $qobj->payment_type_id;

	}

	public function softDeletePaymentAccount($id){
		// $totay = $this->utils->getTodayForMysql();
		$data = array(
               'status' => self::STATUS_DELETE,
               'deleted_at' => $this->utils->getNowForMysql(),
            );

		$this->db->where('id', $id);
		$this->db->update($this->tableName, $data);
	}

	public function getActveDepositPaymentAccount(){
		return $this->db->where('status', self::STATUS_ACTIVE)->where('flag', 1)->get($this->tableName)->row();
	}

	public function getActveDepositPaymentAccountWithBankTypeName($filter_manual_payment=true){
		$this->db->select('payment_account.*');
		$this->db->select('payment_account.id as payment_account_id');
		$this->db->select('banktype.bankName as payment_type');
		$this->db->from('payment_account');
		$this->db->join('banktype', 'banktype.bankTypeId=payment_account.payment_type_id', 'left');
		$this->db->where('payment_account.status', self::STATUS_ACTIVE);
		if($filter_manual_payment) {
			$this->db->where('payment_account.flag', 1);
		}
		$query = $this->db->get();
		$result = $query->result_array();

		$data = array();
		if ($query->num_rows() > 0) {
			foreach ($result as $row) {
				$row['payment_account_full_name'] = lang($row['payment_type']) . ' - '.$row['payment_account_name'];
				$row['ttt'] = 'kerker';
				$data[] = $row;
			}
		}

		return $data;
	}


    /**
	*OPG-1425
	* @param int $paymentAccountId from table payment_account_id
	*/
	public function updateDeposit($paymentAccountId){
		$dailyAmount=0;
		$totalAmount=0;
		$this->load->model(array('transactions'));
		$dataAry = array();
		//$paymentAccout = $this->getPaymentAccount($paymentAccountId);
		$dataAry['daily_deposit_amount']= $this->transactions->getTotalDailyDeposit($paymentAccountId);
		$dataAry['total_deposit_amount']= $this->transactions->getTotalDeposit($paymentAccountId);
		$this->db->where('id', $paymentAccountId);
		return $this->db->update($this->tableName, $dataAry);

	}

	public function getDeposit(){
		$rsAry = array();
		$sql = "SELECT id,daily_deposit_amount,total_deposit_amount  FROM " . $this->tableName;
		$dopsitAry = $this->db->query($sql)->result_array();

		foreach($dopsitAry as $k=>$valAry){
			$rsAry[$valAry['id']]['dailyDeposit']=$valAry['daily_deposit_amount'];
			$rsAry[$valAry['id']]['totalDeposit']=$valAry['total_deposit_amount'];

		}
		return $rsAry;

	}

	/**
	 * Determines if payment account is available for given player/payment flag/bank etc
	 * OGP-8278 ported from xcyl
	 * @param	int		$playerId
	 * @param	int		$flag
	 * @param	int		$bankTypeId
	 * @param	int		$paymentAccountId
	 * @param	bool	$onlyAllowAffiliate
	 * @used-by	t1t_comapi_module_third_party_deposit::get_thirdPartyPayments()
	 *
	 * @return	bool
	 */
	public function existsAvailableAccount($playerId, $flag, $bankTypeId = null, $paymentAccountId = null, $onlyAllowAffiliate = null) {

		$this->load->model(array('group_level', 'transactions'));

		$player = $this->player_model->getPlayerById($playerId);

		$this->db->distinct();
		$this->db->select('payment_account.*');
		$this->db->select('payment_account.id as payment_account_id');
		$this->db->select('banktype.bankName as payment_type');
		$this->db->select('payment_account_affiliate.affiliate_id');
		$this->db->from('payment_account');
		$this->db->join('banktype', 'banktype.bankTypeId=payment_account.payment_type_id', 'left');
		$this->db->join('payment_account_player_level', 'payment_account_player_level.payment_account_id=payment_account.id', 'left');
		# AFFILIATE
        $this->db->join('payment_account_affiliate', 'payment_account_affiliate.payment_account_id=payment_account.id', 'left');
        $this->db->join('payment_account_player', 'payment_account_player.payment_account_id=payment_account.id', 'left');

		$this->db->where('payment_account.status !=', self::STATUS_DELETE);

		# PLAYER GROUP LEVEL
		$this->db->where('(payment_account_player_level.player_level_id = ' . $player->levelId, null, false);

		# AFFILIATE
		if (!empty($player->affiliateId)) {
			if ($onlyAllowAffiliate) {
				$this->db->where('payment_account_affiliate.affiliate_id = ' . $player->affiliateId, null, false);
			} else {
				$this->db->or_where('payment_account_affiliate.affiliate_id = ' . $player->affiliateId, null, false);
			}
		}

		# PLAYER
		$this->db->or_where('payment_account_player.player_id = ' . $playerId . ')', null, false);

		$this->addDefaultStatusWhere();

		if ( ! empty($bankTypeId)) {
			$this->db->where('banktype.bankTypeId', $bankTypeId);
		}else{
			if ( ! empty($flag)) {
				$this->db->where('payment_account.flag', $flag);
				if ($flag == self::FLAG_AUTO_ONLINE_PAYMENT) {
					$this->db->where('payment_account.external_system_id is not null', null, false);
					$this->db->where('payment_account.external_system_id != \'0\'', null, false);
				}
			}
		}

		if(! empty($paymentAccountId) ) {
			$this->db->where('payment_account.id', $paymentAccountId);
		}

		// $this->db->order_by('payment_order');

		// $paymentAccountFlags = $this->utils->getPaymentAccountAllFlagsKV();

		$rows = $this->runMultipleRow();
		if ($onlyAllowAffiliate) {
			if ($rows && empty($player->affiliateId)) {
				return (empty($rows[0]->affiliate_id)) ? true : false ;
			}
		}

		return !empty($rows);

	}

	public function checkSecureidExists ($secure_Id) {
        $this->db->select('secure_id')->from('sale_orders')->where('secure_id', $secure_Id);
		if ($this->runExistsResult()) {
			$this->utils->debug_log('exists secure id', $secure_Id);
			return true;
		}
		return false;
	}

}

///END OF FILE/////////
