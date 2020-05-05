<?php
trait transfer_request_model_module {


	/**
	 * detail: adding transfer request
	 * note: wallet id is game platform id
	 *
	 * @param int $playerId
	 * @param int $fromWalletTypeId
	 * @param int $toWalletTypeId
	 * @param double $amount
	 * @param int $userId
	 * @param int $secure_id wallet account
	 * @return boolean
	 */
	public function addTransferRequest($playerId, $fromWalletTypeId, $toWalletTypeId, $amount,
			$userId = null, &$secure_id = null, $external_system_id=null) {
		$secure_id = $this->getSecureId('transfer_request', 'secure_id', true, 'T');

		$data = array(
			'player_id' => $playerId,
			'from_wallet_type_id' => $fromWalletTypeId,
			'to_wallet_type_id' => $toWalletTypeId,
			'amount' => $amount,
			'user_id' => $userId,
			'secure_id' => $secure_id,
			'external_system_id' => $external_system_id,
			'status' => self::STATUS_TRANSFER_REQUEST,
			'created_at' => $this->utils->getNowForMysql(),
			'updated_at' => $this->utils->getNowForMysql(),
		);

		return $this->insertData('transfer_request', $data);
	}

	/**
	 * detail: set status success for transfer request
	 *
	 * @param int $requestId
	 * @param int $response_result_id
	 * @param int $external_transaction_id
	 * @return boolean
	 */
	public function setSuccessToTransferReqeust($requestId, $response_result_id = null, $external_transaction_id = null, $transfer_status=null, $reason_id=null) {
		$this->db->set('status', self::STATUS_TRANSFER_SUCCESS)->set('response_result_id', $response_result_id)
			->set('updated_at', $this->utils->getNowForMysql())
			->set('external_transaction_id', $external_transaction_id)
			->where('id', $requestId);

		if(!empty($transfer_status)){
			$this->db->set('transfer_status', $transfer_status);
		}

		if(!empty($reason_id)){
			$this->db->set('reason_id', $reason_id);
		}

		return $this->runAnyUpdateWithoutResult('transfer_request');
	}

	/**
	 * detail: set status failed for transfer request
	 *
	 * @param int $requestId
	 * @param int $response_result_id
	 * @param int $external_transaction_id external transactio
	 * @return boolean
	 */
	public function setFailedToTransferReqeust($requestId, $response_result_id = null, $external_transaction_id = null, $transfer_status=null, $reason_id=null) {
		$this->db->set('status', self::STATUS_TRANSFER_FAILED)->set('response_result_id', $response_result_id)
			->set('updated_at', $this->utils->getNowForMysql())
			->set('external_transaction_id', $external_transaction_id)
			->where('id', $requestId);

		if(!empty($transfer_status)){
			$this->db->set('transfer_status', $transfer_status);
		}

		if(!empty($reason_id)){
			$this->db->set('reason_id', $reason_id);
		}

		return $this->runAnyUpdateWithoutResult('transfer_request');
	}

	/**
	 * detail: get the last failed transfer request
	 *
	 * @param int $gamePlatfromId it must be the wallet type id
	 * @param int $playerId transfer_request field
	 * @param int $transferRequestType @deprecated
	 * @return array
	 */
	public function lastFailedTransferRequest($gamePlatformId, $playerId, $transferRequestType) {

		// switch ($transferRequestType) {

		// 	case Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET:
		// 		$transferRequestType = 'transfer_request.from_wallet_type_id';
		// 		break;

		// 	case Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET:
		// 		$transferRequestType = 'transfer_request.to_wallet_type_id';
		// 		break;

		// 	default:
		// 		return false;
		// 		break;
		// }

		$this->db->select('transfer_request.*');
		$this->db->select('response_results.request_params');
		$this->db->from('transfer_request');
		$this->db->join('response_results', 'response_results.id = transfer_request.response_result_id');
		// $this->db->where($transferRequestType, $gamePlatformId);
		$this->db->where('(transfer_request.from_wallet_type_id', $gamePlatformId);
		$this->db->or_where('transfer_request.to_wallet_type_id', $gamePlatformId . ')', false);
		$this->db->where('transfer_request.player_id', $playerId);
		$this->db->where('transfer_request.status', self::STATUS_TRANSFER_FAILED);
		$this->db->where('transfer_request.updated_at >=', date('Y-m-d H:i:s', strtotime('-6 hours'))); # only for past 6 hours
		$this->db->order_by('transfer_request.created_at', 'DESC');
		$this->db->limit(1);

		$query = $this->db->get();

		$row = $query->row_array();

		if ($row) {
			$row['request_params'] = json_decode($row['request_params'], true);
		}

		return $row;
	}

	/**
	 * detail: count all transfer request
	 *
	 * @return array
	 */
	public function transfer_request_count() {
		$this->db->select('status');
		$this->db->select('count(1) as count', null, false);
		$this->db->from('transfer_request');
		$this->db->group_by('status');
		$this->db->order_by('status', 'asc');

		$result = $this->runMultipleRowArray();
		return @array_combine(array_column($result, 'status'), array_column($result, 'count'));
	}

	public function getTransferRequestById($id){

		if(!empty($id)){
			$this->db->from('transfer_request')->where('id', $id);
			$row= $this->runOneRowArray();

			//try fix external_system_id
			if(empty($row['external_system_id'])){
				if($row['from_wallet_type_id']==self::MAIN_WALLET_ID){

					$row['external_system_id']=$row['to_wallet_type_id'];

				}else{

					$row['external_system_id']=$row['from_wallet_type_id'];

				}
			}

			return $row;
		}

		return null;

	}

	public function updateTransferQueryStatus($id, $transfer_status){
		if(!empty($id)){
			$data=['transfer_status'=>$transfer_status, 'updated_at'=>$this->utils->getNowForMysql()];
			$this->db->set($data)->where('id', $id);

			return $this->runAnyUpdate('transfer_request');
		}

		return null;
	}

	public function getTransferStatusFromTransferRequest($id){
		if(!empty($id)){
			$this->db->from('transfer_request')->select('transfer_status')->where('id', $id);

			return $this->runOneRowOneField('transfer_status');
		}

		return null;
	}

	public function getFixFlagFromTransferRequest($id){
		if(!empty($id)){
			$this->db->from('transfer_request')->select('fix_flag')->where('id', $id);

			return $this->runOneRowOneField('fix_flag');
		}

		return null;
	}

	public function updateTransferQueryStatusAndFixFlag($id, $transfer_status, $fix_flag){
		if(!empty($id)){
			$data=['transfer_status'=>$transfer_status, 'fix_flag'=> $fix_flag, 'updated_at'=>$this->utils->getNowForMysql()];
			$this->db->set($data)->where('id', $id);

			return $this->runAnyUpdate('transfer_request');
		}

		return null;
	}

	/**
	 * searchSuspiciousTransferRequest
	 *
	 * 2 rules:
	 * search all deposit and status is success and transfer status is unknown or declined
	 * withdraw and status is failed and transfer status is unknown or approved
	 *
	 * @param  string $from from date time
	 * @param  string $to   to date time
	 * @return array rows of result or empty
	 */
	public function searchSuspiciousTransferRequest($from, $to){

		$this->load->model(['wallet_model', 'external_system', 'transactions', 'player_model']);

		$this->utils->loadAnyGameApiObject();

		$secondReadDB = $this->getSecondReadDB();

		// $secondReadDB->from('transfer_request')->where('created_at >=', $from)
		// 	->where('created_at <=', $to)->where();

		$sql=<<<EOD
select *
from transfer_request
where created_at >= ? and created_at <= ?
and (
  (from_wallet_type_id=? and status=? and transfer_status in (?, ?)) or
  (to_wallet_type_id=? and status=? and transfer_status in (?, ?))
 )
and fix_flag= ?
EOD;
		// $this->db->from('game_logs');

		//deposit and success and transfer status is unknown or declined
		//withdrawal and failed and status is failed and transfer status is unknown or approved
		$qry=$secondReadDB->query_unbuffered_mysql($sql, [$from, $to,
			self::MAIN_WALLET_ID, self::STATUS_TRANSFER_FAILED,
				Abstract_game_api::COMMON_TRANSACTION_STATUS_APPROVED, Abstract_game_api::COMMON_TRANSACTION_STATUS_UNKNOWN,
			self::MAIN_WALLET_ID, self::STATUS_TRANSFER_SUCCESS,
				Abstract_game_api::COMMON_TRANSACTION_STATUS_DECLINED, Abstract_game_api::COMMON_TRANSACTION_STATUS_UNKNOWN,
			self::DB_FALSE]);

		// $success=!empty($qry);
		$action_of_search_suspicious_transfer_request=$this->utils->getConfig('action_of_search_suspicious_transfer_request');
		$cnt=0;

		$this->utils->debug_log('action_of_search_suspicious_transfer_request', $action_of_search_suspicious_transfer_request);

		//set $row , if row is null, just quit
		while (!empty($qry) && $row=$qry->nextRowArray()) {

		// $this->loopRowsFromReadySql(function($row){

			// $stop=false;

			$m1=memory_get_usage();

			// $this->utils->debug_log($row['id'].' '.$row['bet_amount'].', '.round(memory_get_usage()/(1024*1024), 2));

			//process
			$this->utils->debug_log($row['id'].' '.$row['amount'].', '.round(memory_get_usage()/(1024*1024), 2));

			if($action_of_search_suspicious_transfer_request=='update_status'){

				if($row['transfer_status']==Abstract_game_api::COMMON_TRANSACTION_STATUS_UNKNOWN){
					//try query status first
					list($success, $status, $error_message, $message)=$this->utils->queryAndUpdateTransferRequestStatus($row);
					if($success){
						$this->appendNoteToTransferRequest($row['id'], 'query api status:'.$status.', status:'.$row['status']);
					}
					$this->utils->debug_log('queryAndUpdateTransferRequestStatus:'.$row['id'],$success, $status, $error_message, $message);
				}else{

					$this->utils->debug_log('ignore queryAndUpdateTransferRequestStatus:'.$row['id'],$success, $status, $error_message, $message);
				}

			}else if($action_of_search_suspicious_transfer_request=='auto_fix'){

				list($success, $status, $error_message, $message)=$this->utils->fixTransferRequest($row);
				$this->utils->debug_log('fixTransferRequest:'.$row['id'],$success, $status, $error_message, $message);

			}else{
				//none
				$this->utils->debug_log('ignore action');
			}


			$m2=memory_get_usage();

			$cnt++;
			unset($row);

			$m3=memory_get_usage();

			$this->utils->debug_log($m1.' > '.$m2.' > '.$m3.', '.($m2-$m1).', '.($m3-$m2));

			// return $stop;

		// });
		}

		$qry->free_result();

		$this->utils->debug_log('count', $cnt);
	}

	public function appendNoteToTransferRequest($id, $note){

		$sql = "update transfer_request set notes=concat(ifnull(notes,''),' | ',?) where id=?";
		return $this->runRawUpdateInsertSQL($sql, array($note, $id));
	}

	public function getSecureIdByExternalTransactionId($external_transaction_id){
		$this->db->select('secure_id');
		$this->db->where('external_transaction_id', $external_transaction_id);
		$this->db->from('transfer_request');
		return $this->runOneRowOneField('secure_id');
	}

	public function getAllSuspiciousBy($from, $to){

		$sql=" select id, secure_id, created_at from transfer_request ".
			" where ((transfer_request.status=".Wallet_model::STATUS_TRANSFER_SUCCESS." and transfer_request.transfer_status = '".
			Abstract_game_api::COMMON_TRANSACTION_STATUS_DECLINED."' ) or (transfer_request.status=".Wallet_model::STATUS_TRANSFER_FAILED.
			" and transfer_request.transfer_status ='".Abstract_game_api::COMMON_TRANSACTION_STATUS_APPROVED."'))".
			" and created_at>=? and created_at<=?";

		return $this->runRawSelectSQLArray($sql, [$from, $to]);

	}

	public function getTransferRequestByExternalTransactionIdAndExternalSystemId($external_transaction_id, $external_system_id){
		$this->db->where('external_transaction_id', $external_transaction_id)->where('external_system_id', $external_system_id);
		$this->db->from('transfer_request');
		$row= $this->runOneRowArray();

		//try fix external_system_id
		if(empty($row['external_system_id'])){
			if($row['from_wallet_type_id']==self::MAIN_WALLET_ID){

				$row['external_system_id']=$row['to_wallet_type_id'];

			}else{

				$row['external_system_id']=$row['from_wallet_type_id'];

			}
		}

		return $row;
	}

}

