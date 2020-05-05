<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

class Mglapis_game_logs extends BaseModel {

	function __construct() {
		parent::__construct();
	}

	protected $tableName = "mglapis_game_logs";

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	public function insertMGLapisGameLogs($data) {
		return $this->db->insert($this->tableName, $data);
	}

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	// public function insertMGLapisRawGameLogs($data) {
	// 	return $this->db->insert("mglapis_raw_game_logs", $data);
	// }

	// public function syncOriginal($data, $is_update_original_row){

	// 	//if($data['trans_id'] == '1497187186829238083' || $data['trans_id'] == '1497187186829238085'){

	// 	$external_uniqueid=$data['external_uniqueid'];
	// 	$originalRow=$this->getInfoByExternalUniqueId($external_uniqueid);

	// 	switch ($data["transType"]) {
	// 		case 'bet':

	// 			// $this->CI->mglapis_game_logs->syncOriginal($mgLapisGameData, $this->is_update_original_row);
	// 			if(empty($originalRow)){
	// 				//$this->CI->utils->debug_log('[---------MG LAPIS AYAW INSERTED--------]', $data['trans_id'],$data);
	// 				$data["record_update_flag"] = 0;
	// 				return $this->insertMGLapisGameLogs($data);
	// 			}else{
	// 				if($is_update_original_row){
	// 					//don't update result
	// 					// unset($data['result_amount']);
	// 					$this->updateBet($originalRow, $data['col_id'], $data['']);
	// 					return $this->updateOriginalGameLogs($originalRow['id'], $originalRow);
	// 				}
	// 			}

	// 			break;
	// 		case 'win':
	// 			//$returnData = $this->CI->mglapis_game_logs->getResultAmountByGmsGameId($playerName,$key["mgsGameId"],$gameKey);
	// 			// $trans_id=$this->getOriginalId($data);
	// 			// $returnData  = $this->getResultAmountByGmsGameId($playerName.'-'.$gameKey.'-'.$key["mgsGameId"]);
	// 			if(!empty($originalRow)){
	// 				// if(!$this->CI->mglapis_game_logs->isWinAmountExists($returnData['trans_id'])){
	// 				$updateData = [
	// 					// "trans_id" => $returnData['trans_id'],
	// 					"result_amount" => (float)$originalRow['result_amount'] + (float)$data['amnt'],
	// 					"trans_type" => "win",
	// 					"balance_after_bet" => $key["balance"],
	// 					"sync_datetime" => $this->CI->utils->getNowForMysql(),
	// 					"record_update_flag" => ((float)$originalRow['record_update_flag']) + 1  ,
	// 				];
	// 				//$this->CI->utils->debug_log('[---------MG LAPIS SYNC ORIGINAL UPDATE LOGS---------]',$returnData['id'], $updateData);
	// 				$this->updateOriginalGameLogs($originalRow['id'], $updateData);
	// 				// }
	// 			}else{
	// 				//try insert
	// 			}
	// 			break;
	// 		default:
	// 			# code...
	// 			break;
	// 	}

	// 	//}


	// 	return true;
	// }

	// function getInfoByExternalUniqueId($external_uniqueid) {

	// 	$this->db->from('mglapis_game_logs')->select('id')->where('external_uniqueid', $external_uniqueid);
	// 	return $this->runOneRow();

	// }

// 	function getOriginalId($row) {
// 		// if(isset($row)){
// 		// 	$this->db->where('trans_id', $row['trans_id']);
// 		// 	//$this->db->where('player_name', $row['player_name']);
// 		// 	$this->db->select('trans_id')->from($this->tableName);
// 		// 	return $this->runOneRowOneField('trans_id');
// 		// }
// //		sleep(.1);

// 		if(isset($row['trans_id'])){
// 			// $this->CI->utils->debug_log('[---------MG LAPIS checking--------]', $row['trans_id'],$row);
// 			$sql = "SELECT trans_id FROM mglapis_game_logs WHERE  trans_id = ? and player_name = ?";
// 			$query = $this->db->query($sql, array($row['trans_id'],$row['player_name']));
// 			if($query->num_rows() > 0){
// 				// $this->CI->utils->debug_log('[---------MG LAPIS AYAW CHECK--------]',$row['trans_id'],$row['player_name'], $row);
// 				$result =  $query->result_array()[0]['trans_id'];
// 				// $this->CI->utils->debug_log('[---------MG LAPIS AYAW CHECK result--------]',$result, $row);
// 				return $result;
// 			}
// 			return null;
// 		}
// 	}

	/**
	 * @param rowId int
	 *
	 * @return boolean
	 */
	// function isTransIdAlreadyExists($game_tran_id) {
	// 	$qry = $this->db->get_where($this->tableName, array('trans_id' => $game_tran_id));
	// 	if ($this->getOneRow($qry) == null) {
	// 		return false;
	// 	} else {
	// 		return true;
	// 	}
	// }

	// public function getLastInsertRecord() {
	// 	$this->db->select('id')->from($this->tableName);
	// 	$this->db->order_by('trans_time', 'desc');
	// 	$query = $this->db->get();
	// 	return $query->row_array();
	// }

	/**
	 * @param data array
	 *
	 * @return boolean
	 */
	// function updateMGLapisGameLogs($id, $data) {
	// 	//$this->CI->utils->debug_log('[---------MG LAPIS AYAW2--------]', $data['trans_id'],$data);
	// 	$this->db->where('trans_id', $id);
	// 	$res = $this->db->update($this->tableName, $data);
	// 	return $res;
	// }

	// function updateOriginalGameLogs($id, $data) {
	// 	$this->db->where('id', $id);
	// 	$res = $this->db->update($this->tableName, $data);
	// 	return $res;
	// }

	function getMGLapisGameLogStatistics($dateFrom, $dateTo) {

		$sql = <<<EOD
SELECT mglapis.id,
mglapis.player_name,
mglapis.game_id,
mglapis.trans_id,
mglapis.trans_type,
mglapis.bet_amount,
mglapis.result_amount,
mglapis.balance_after_bet,
mglapis.game_id as gameshortcode,
mglapis.external_uniqueid ,
mglapis.response_result_id,
mglapis.trans_time trans_time,
gd.id as game_description_id,
gd.game_name as game,
gd.game_code as game_code,
gd.game_type_id,
gp.player_id,
gt.game_type
FROM mglapis_game_logs as mglapis
LEFT JOIN game_description as gd ON mglapis.game_id = gd.external_game_id and gd.game_platform_id=?
LEFT JOIN game_type as gt ON gd.game_type_id = gt.id
LEFT JOIN game_provider_auth as gp ON mglapis.player_name = gp.login_name and game_provider_id=?
WHERE
mglapis.trans_time >= ? AND mglapis.trans_time <= ?
EOD;


		$query = $this->db->query($sql, array(
			LAPIS_API,
			LAPIS_API,
			$dateFrom,
			$dateTo,
		));
		//$this->CI->utils->debug_log('[---------MG LAPIS query---------]', $this->db->last_query());
		return $this->getMultipleRow($query);
	}

	function isRefundIdExist($transID,$dateFrom,$dateTo) {
		$refTransID = "BetTransaction:".$transID;
		$this->db->select('id')->from($this->tableName)->where('ref_trans_id', $refTransID);

		return $this->runExistsResult();
	}

    // public function getAvailableRows($rows) {

    //     if (!empty($rows)) {
    //         $arr = array();
    //         foreach ($rows as $row) {
    //             $uniqueId = $row['colId'];
    //             $arr[] = $uniqueId;
    //         }

    //         $this->db->select('trans_id')->from($this->tableName)->where_in('trans_id', $arr);
    //         $existsRow = $this->runMultipleRow();
    //         // $this->utils->printLastSQL();
    //         $availableRows = null;
    //         if (!empty($existsRow)) {
    //             $existsId = array();
    //             foreach ($existsRow as $row) {
    //                 $existsId[] = $row->trans_id;
    //             }
    //             $availableRows = array();
    //             foreach ($rows as $row) {
    //                 $uniqueId = $row['colId'];
    //                 if (!in_array($uniqueId, $existsId)) {
    //                     $availableRows[] = $row;
    //                 }
    //             }
    //         } else {
    //             //add all
    //             $availableRows = $rows;
    //         }
    //         return $availableRows;
    //     } else {
    //         return null;
    //     }

    // }

	// function isWinAmountExists($trans_id) {
	// 	$qry = $this->db->get_where($this->tableName, array('trans_id' => $trans_id, 'record_update_flag' => true,'trans_type' => 'win'));
	// 	if ($this->getOneRow($qry) == null) {
	// 		return false;
	// 	} else {
	// 		return true;
	// 	}
	// }

	// function getResultAmount($trans_id) {
	// 	$this->db->select('result_amount,bet_amount')->from($this->tableName);
	// 	$this->db->where('trans_id', $trans_id);
	// 	$query = $this->db->get();
	// 	return $query->row_array();
	// }

	// function getResultAmountByGmsGameId($playerName, $mgs_game_id,$game_id) {
	// 	$this->db->select('result_amount,bet_amount,trans_id,id')->from($this->tableName);
	// 	$this->db->where('mgs_game_id', $mgs_game_id)->where('game_id', $game_id)
	// 	    ->where('player_name', $playerName);
	// 	$query = $this->db->get();
	// 	return $query->row_array();
	// }

	// function getResultAmountByGmsGameId($relatedTransId) {
	// 	$this->db->where('external_uniqueid', $relatedTransId);
	// 	$this->db->select('id, result_amount, bet_amount,trans_id,record_update_flag')->from($this->tableName);
	// 	$query = $this->db->get();
	// 	return $query->row_array();
	// }

	// function isOrignalRowExist($pairedTransId) {
	// 	$this->db->where('external_uniqueid', $pairedTransId);
	// 	$this->db->where('trans_type', 'bet');
	// 	$this->db->select('id')->from($this->tableName);
	// 	return $this->runOneRowOneField('id');
	// }

	public function getGameTimeToServerTime() {
		return '+8 hours';
	}

	public function getServerTimeToGameTime() {
		return '-8 hours';
	}

	/**
	 *
	 * @param  string $dateTimeStr
	 * @return string
	 */
	public function gameTimeToServerTime($dateTimeStr) {
		if (is_object($dateTimeStr) && $dateTimeStr instanceof DateTime) {
			$dateTimeStr = $dateTimeStr->format('Y-m-d H:i:s');
		}
		$modify = $this->getGameTimeToServerTime();
		return $this->utils->modifyDateTime($dateTimeStr, $modify);
	}

	/**
	 *
	 * @param  string $dateTimeStr
	 * @return string
	 */
	public function serverTimeToGameTime($dateTimeStr) {
		if (is_object($dateTimeStr) && $dateTimeStr instanceof DateTime) {
			$dateTimeStr = $dateTimeStr->format('Y-m-d H:i:s');
		}
		$modify = $this->getServerTimeToGameTime();
		return $this->utils->modifyDateTime($dateTimeStr, $modify);
	}

	public function syncRecords($gameRecords, $responseResultId){

        if (!empty($gameRecords)) {
            $map = array();
            $externalUniqueIdArr=[];
            foreach ($gameRecords as $row) {

            	//transactionTimestampDate
            	$trans_time = null;
            	if(isset($row["transTime"])){
					$trans_time = $this->utils->convertTimestampToDateTime($row["transTime"]);
            	}else{
					$trans_time = $this->utils->convertTimestampToDateTime($row["transactionTimestampDate"]);
            	}
				$trans_time_converted = $this->gameTimeToServerTime($trans_time);

				$playerName = explode(":",$row["mbrCode"])[1];
				$gameKey = isset($row['gameKey']) ? explode(":",$row["gameKey"])[1] : $row['gameId'];
				$mgsGameId=$row["mgsGameId"];
                $external_uniqueid = $playerName.'-'.$gameKey.'-'.$mgsGameId;

                $trans_type='bet';
                if(isset($row["transType"])){
                	$trans_type=$row["transType"];
                }elseif(isset($row["type"])){
                	$trans_type=$row["type"];
                }
                //translate
                if($trans_type=='mgsaspibet'){
                	$trans_type='bet';
                }elseif($trans_type=='mgsapiwin'){
                	$trans_type='win';
                }

                //afterTxWalletAmount
                $after_balance=null;
                if(isset($row["balance"])){
                	$after_balance=$row["balance"];
                }elseif(isset($row["afterTxWalletAmount"])){
                	$after_balance=$row["afterTxWalletAmount"];
                }

                $amount=null;
                if(isset($row['amnt'])){
                	$amount=$row['amnt'];
                }else{
                	$amount=$row['amount'];
                }

                $data=[
					"key" => $row["key"],
					"col_id" => $row["colId"],
					"mbr_id" => isset($row["mbrId"]) ? $row["mbrId"] : $row["mbrNeKey"] ,
					"mbr_code" => $row["mbrCode"],
					"trans_type" => $trans_type,
					"trans_id" => $row["colId"],
					"mgs_game_id" => $mgsGameId,
					"mgs_action_id" => $row["mgsActionId"],
					"clearing_amount" => $row["clrngAmnt"],
					"balance_after_bet" => $after_balance,
					"ref_trans_id" => isset($row["refTransId"]) ? $row["refTransId"] : $row["refKey"] ,
					"ref_trans_type" => isset($row["refTransType"]) ? $row["refTransType"] : $row["refType"],
					"sync_datetime" => $this->utils->getNowForMysql(),

                	"player_name" => $playerName,
                	"game_id" => $gameKey,
					"trans_time" => $trans_time_converted,
					"external_uniqueid" => $external_uniqueid,
					"response_result_id" => $responseResultId,
				];

				if($trans_type=='bet'){
					$data['bet_amount']=$amount;
					$data['result_amount']=0;
				}elseif($trans_type=='win'){
					$data['bet_amount']=0;
					$data['result_amount']=$amount;
				}

				if(isset($map[$external_uniqueid])){
	                $map[$external_uniqueid][] = $data;
				}else{
					//first one
                	$map[$external_uniqueid] = [$data];
				}
                $externalUniqueIdArr[]=$external_uniqueid;
	            // $this->utils->debug_log('-------------processed-------------------', $data);
            }

            $this->db->select('id, col_id, external_uniqueid, extra')->from('mglapis_game_logs')
                ->where_in('external_uniqueid', $externalUniqueIdArr);
            $rows=$this->runMultipleRowArray();

            if(!empty($rows)){

	            foreach ($rows as $row) {
		            $extra=$this->utils->decodeJson($row['extra']);
	            	//anyone exists, should update all
	            	$records=$map[$row['external_uniqueid']];
	            	foreach ($records as $rec) {
		            	//save current info to extra
		            	if($rec['trans_type']=='bet'){

		            		$extra[$rec['col_id']]=[
		            			'trans_type'=>$rec['trans_type'],
		            			'bet_amount'=>$rec['bet_amount'],
		            			'balance_after_bet'=>$rec["balance_after_bet"],
		            			"trans_time"=>$rec['trans_time'],
		            		];

		            	}else if($rec['trans_type']=='win'){

		            		$extra[$rec['col_id']]=[
		            			'trans_type'=>$rec['trans_type'],
		            			'result_amount'=>$rec['result_amount'],
		            			'balance_after_bet'=>$rec["balance_after_bet"],
		            			"trans_time"=>$rec['trans_time'],
		            		];

		            	}
	            	}

	            	list($bet_amount, $result_amount, $balance_after_bet)=$this->recalc($extra);

	            	//update
	            	$updateData=[
	            		'bet_amount'=>$bet_amount,
	            		'result_amount'=>$result_amount,
	            		'balance_after_bet'=>$balance_after_bet,
	            		'extra'=> $this->utils->encodeJson($extra),
						"sync_datetime" => $this->utils->getNowForMysql(),
	            	];
	            	$this->db->set($updateData)->where('id', $row['id']);
	            	$this->runAnyUpdate('mglapis_game_logs');

	            	// $this->utils->debug_log('update mglapis_game_logs external_uniqueid:'.$row['external_uniqueid'].', id:'.$row['id'].', col id:'.$row['col_id'].', bet_amount:'.$bet_amount.', result_amount:'.$result_amount);
	            	//remove
	            	unset($map[$row['external_uniqueid']]);
	            }
            }

            if(!empty($map)){
            	foreach ($map as $external_uniqueid => $records) {
            		$insertRec=null;
            		$extra=[];
            		//merge all
	            	foreach ($records as $rec) {
		            	if($rec['trans_type']=='bet'){

		            		$extra[$rec['col_id']]=[
		            			'trans_type'=>$rec['trans_type'],
		            			'bet_amount'=>$rec['bet_amount'],
		            			'balance_after_bet'=>$rec["balance_after_bet"],
		            			"trans_time"=>$rec['trans_time'],
		            		];

		            	}else if($rec['trans_type']=='win'){

		            		$extra[$rec['col_id']]=[
		            			'trans_type'=>$rec['trans_type'],
		            			'result_amount'=>$rec['result_amount'],
		            			'balance_after_bet'=>$rec["balance_after_bet"],
		            			"trans_time"=>$rec['trans_time'],
		            		];

		            	}
	            		if(empty($insertRec)){
	            			//get first one
	            			$insertRec=$rec;
	            		}
					}
					//setup last
	            	list($bet_amount, $result_amount, $balance_after_bet)=$this->recalc($extra);
	            	$insertRec['extra']= $this->utils->encodeJson($extra);
	            	$insertRec['bet_amount']=$bet_amount;
	            	$insertRec['result_amount']=$result_amount;
	            	$insertRec['balance_after_bet']=$balance_after_bet;
					$insertRec["sync_datetime"] = $this->utils->getNowForMysql();
	            	// $this->utils->debug_log('insert mglapis_game_logs external_uniqueid:'.$insertRec['external_uniqueid'].', col id:'.$insertRec['col_id'].', bet_amount:'.$bet_amount.', result_amount:'.$result_amount);
            		//insert row
					$this->db->insert('mglapis_game_logs', $insertRec);
            	}
            }


        }


	}

	public function recalc($extra){
		//load from extra
		$bet_amount=0;
		$result_amount=0;
		//always max balance
		$balance_after_bet=0;
		// $extra=$originalRow['extra'];
		// $extra=$this->utils->encodeJson($extra);
		if(!empty($extra)){
			foreach ($extra as $item) {
				if($item['balance_after_bet']>$balance_after_bet){
					$balance_after_bet=$item['balance_after_bet'];
				}
				if($item['trans_type']=='bet'){
					$bet_amount+=$item['bet_amount'];
				}elseif($item['trans_type']=='win'){
					$result_amount+=$item['result_amount'];
				}
			}
		}
		// $originalRow['result_amount']=$result_amount;
		// $originalRow['bet_amount']=$bet_amount;

		// return $originalRow;
		return [$bet_amount, $result_amount, $balance_after_bet];
	}

}

///END OF FILE///////
