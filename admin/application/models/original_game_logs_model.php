<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}
require_once dirname(__FILE__) . '/base_model.php';

/**
 *
 * syncGameLogs, getAvailableRows, insertBatchGameLogs
 *
 * getGameLogStatistics, getGameLogStatisticsByIds
 *
 */
class Original_game_logs_model extends BaseModel {

    public function __construct(){
        parent::__construct();
    }

    /**
     * generate md5 field in $rows, and return uniqueid array
     *
     * @param  array &$rows
     * @param  array  $keys
     *
     */
    public function preprocessRows(&$rows, array $keys, $uniqueidFieldOnRows, $md5Field='md5_sum', $floatFields=[], &$originalStrArr=null){
        $uniqueidValues=[];
        if(!empty($rows)){
            foreach ($rows as &$row) {
                $originalStr=null;
                $row[$md5Field]=$this->generateMD5SumOneRow($row, $keys, $floatFields, $originalStr);
                if(!is_null($originalStrArr)){
                    $originalStrArr[]=$originalStr;
                }
                unset($originalStr);
                $uniqueidValues[]=$row[$uniqueidFieldOnRows];
            }
        }

        return $uniqueidValues;
    }

	/**
	 *
	 * process $rows from original api result
	 * return insert rows and update rows
	 *
	 *
	 * @param  array $rows
	 * @param  string $uniqueidFieldOnRows field name in $rows
	 * @param  string $uniqueidFieldOnDB   field name in db
	 * @param  array $floatFields
	 * @return array $insertRow, $updateRows
	 */
	public function getInsertAndUpdateRowsForOriginal($tableName, $apiRows, $uniqueidFieldOnRows, $uniqueidFieldOnDB,
		array $keysOfMd5, $md5Field='md5_sum', $primaryKeyFieldInDB='id', $floatFields=[]) {

		$insertRows=[]; $updateRows=[]; # default to empty array to fix php7 count() warning
		if(empty($apiRows)){
	        return [$insertRows, $updateRows];
		}

		$t=time();
		$this->utils->info_log('start process insert/update rows', $t, count($apiRows));

        //add md5 sum first
        $uniqueidValues=$this->preprocessRows($apiRows, $keysOfMd5, $uniqueidFieldOnRows, $md5Field, $floatFields);

        $limit=500;
        $arr = array_chunk($uniqueidValues, $limit);
        $existsRows =[];
        foreach ($arr as $data) {
            $this->db->select($uniqueidFieldOnDB.', '.$md5Field.', '.$primaryKeyFieldInDB)->from($tableName)
                ->where_in($uniqueidFieldOnDB, $data);
            $tmpRows = $this->runMultipleRowArray();
            $this->utils->info_log('getInsertAndUpdateRowsForOriginal query exists external_uniqueid', count($data), count($tmpRows), count($existsRows));
            if(!empty($tmpRows)){
                $existsRows=array_merge($existsRows, $tmpRows);
            }
            unset($tmpRows);
        }

        if (!empty($existsRows)) {
            $insertRows = array();
            $md5Map=[];

            foreach ($existsRows as $row) {
                $md5Map[$row[$uniqueidFieldOnDB]]=[$row[$md5Field], $row[$primaryKeyFieldInDB]];
            }
            unset($existsRows);

            foreach ($apiRows as &$row) {
                $snId = $row[$uniqueidFieldOnRows];

                if (!isset($md5Map[$snId])) {
                    //doesn't exist
                    $insertRows[] = $row;
                }else{
                	$existInfo=$md5Map[$snId];
                	if($existInfo[0]!=$row[$md5Field]){
	                    $this->utils->debug_log('----- dirty data, add it to updateRows '.$snId);
	                    //save id to update
	                    $row[$primaryKeyFieldInDB]=$existInfo[1];
	                    //diff status
	                    $updateRows[]=$row;
                	}else{
	                	//exists but same md5 , then ignore
                	}
                }
            }
            unset($md5Map);

        } else {
            //doesn't exist all
            $insertRows = $apiRows;
        }

		$this->utils->info_log('end process insert/update rows, cost:'.(time()-$t), 'insertRows: '.count($insertRows), 'updateRows: '.count($updateRows));

        return [$insertRows, $updateRows];
	}

	/**
	 * generateInsertAndUpdateForGameLogs
     * it will set game_logs_id to $updateRows
     *
	 * @param  array $originalRows: unique id and md5_sum
	 * @param  string $uniqueidFieldOnRows
	 * @return array [$insertRows,$updateRows]
	 */
	public function generateInsertAndUpdateForGameLogs($originalRows, $uniqueidFieldOnRows, $reupdateMultipleOrigLogsInMerge = false, $gamePlatformId = null) {

		$insertRows=[]; $updateRows=[]; # default to empty array to fix php7 count() warning
		if(empty($originalRows)){
			return [$insertRows, $updateRows];
		}

		$t=time();
		// $this->utils->debug_log($originalRows, $uniqueidFieldOnRows);
		$this->utils->info_log('start process insert/update rows for game logs', $t, 'count of originalRows: '.count($originalRows), $uniqueidFieldOnRows);
		$uniqueidValues=array_column($originalRows, $uniqueidFieldOnRows);
		//check if really unique
        $afterUniqueArr=array_unique($uniqueidValues);
        if(count($afterUniqueArr)!=count($originalRows)){
            //means input data is wrong
            $this->utils->info_log('duplicate rows when merge', $originalRows, 'duplicate rows when merge');
            throw new Exception('$originalRows include duplicate rows');
            return;
        }
        //fixed column, can't change
        $limit=500;
        $arr = array_chunk($uniqueidValues, $limit);
        $existsRows =[];
        foreach ($arr as $data) {
            $this->db->select('md5_sum, id, external_uniqueid')
                ->from('game_logs')->where_in('external_uniqueid', $data);
            if($gamePlatformId != null) {
                $this->db->where('game_platform_id', $gamePlatformId);
            }
            $tmpRows = $this->runMultipleRowArray();
            $this->utils->info_log('generateInsertAndUpdateForGameLogs query exists external_uniqueid', $gamePlatformId, count($data), count($tmpRows), count($existsRows));
            if(!empty($tmpRows)){
                $existsRows=array_merge($existsRows, $tmpRows);
            }
            unset($tmpRows);
        }

		$this->utils->info_log('uniqueidValues', count($uniqueidValues), 'count of existsRows: '.count($existsRows));

        unset($uniqueidValues);

        if (!empty($existsRows)) {
            $insertRows = array();
            $md5Map=[];

            foreach ($existsRows as $existRow) {
                $md5Map[$existRow['external_uniqueid']]=[$existRow['md5_sum'], $existRow['id']];
            }
			$this->utils->info_log('existsRows', count($existsRows), ', md5Map: '.count($md5Map));

            unset($existsRows);

            foreach ($originalRows as &$row) {
                $snId = $row[$uniqueidFieldOnRows];

                if (!isset($md5Map[$snId])) {
                    //doesn't exist
                    $insertRows[] = $row;
                }else{
                	$existInfo=$md5Map[$snId];
                	if($existInfo[0]!=$row['md5_sum'] ||
                			(empty($existInfo[0]) && empty($row['md5_sum'])) ){
	                    $this->utils->debug_log('----- dirty data, add it to updateRows '.$snId, $existInfo);
	                    //save id to update
	                    $row['game_logs_id']=$existInfo[1];
	                    //diff status
	                    $updateRows[]=$row;
	                }else{
						if($reupdateMultipleOrigLogsInMerge) {
							$row['game_logs_id']=$existInfo[1];
							$updateRows[] = $row;
						}
	                	//exists but same md5 , then ignore
	                    // $this->utils->debug_log('----- same md5 '.$snId, $existInfo[0], $row['md5_sum']);
	                }
                }
            }

            unset($md5Map);

        } else {
            //doesn't exist all
            $insertRows = $originalRows;
        }
        if($this->utils->getConfig('print_insert_update_row_for_debug_merge_logs')){
            $this->utils->error_log('end process insert/update rows for game logs, cost:'.(time()-$t), 'insertRows: ', $insertRows, 'updateRows: ', $updateRows);
        }else{
            $this->utils->info_log('end process insert/update rows for game logs, cost:'.(time()-$t), 'insertRows: '.count($insertRows), 'updateRows: '.count($updateRows));
        }

		return [$insertRows, $updateRows];
	}

	public function commonGetOriginalGameLogs($sql, array $params){

		return $this->runRawSelectSQLArray($sql, $params);

	}

	public function insertRowsToOriginal($tableName, $data) {
		// return $this->db->insert($this->tableName, $data);
		return $this->insertData($tableName, $data);
	}

	/**
	 * always update by primary key
	 * @param  array $data
	 * @param  string $primaryKeyFieldInDB
	 * @return boolean
	 */
	public function updateRowsToOriginal($tableName, array $data, $primaryKeyFieldInDB='id') {
		$id=$data[$primaryKeyFieldInDB];
		unset($data[$primaryKeyFieldInDB]);
	    $this->db->where($primaryKeyFieldInDB, $id)->set($data);

	    return $this->runAnyUpdate($tableName);
	}

	public function commonGenerateOriginalMD5($tableName, $qryStr,
			$keysOfMd5, $floatFieldsForMd5, $md5Field, $primaryKeyFieldInDB){

		$success=true;

		$flds=implode(',', $keysOfMd5).', '.$primaryKeyFieldInDB.', '.$md5Field;

		$sql=<<<EOD
select {$flds}
from {$tableName}
where
{$md5Field} is null

{$qryStr}

EOD;

		$rows=$this->runRawSelectSQLArray($sql);
		if(!empty($rows)){

			foreach ($rows as $row) {
				$md5_sum=$this->generateMD5SumOneRow($row, $keysOfMd5, $floatFieldsForMd5);
				//run update
				$this->db->where($primaryKeyFieldInDB, $row[$primaryKeyFieldInDB])
				    ->set($md5Field, $md5_sum);
				$success=$this->runAnyUpdate($tableName);
				if(!$success){
					$this->utils->debug_log('update failed', $tableName, $row, $md5_sum);
					break;
				}else{
					$this->utils->debug_log('update success '.$tableName.', '.
						$primaryKeyFieldInDB.'='.$row[$primaryKeyFieldInDB].', '.$md5Field.'='.$md5_sum);
				}

			}

			unset($rows);
		}

		return $success;
	}

    public function removeDuplicateUniqueid(&$rows, $uniqueidField, callable $keepRow){

        $deleteList=[];
        $idMap=[];
        //create index array
        for ($i=0; $i < count($rows); $i++) {
            $key=$rows[$i][$uniqueidField];
            if(!isset($idMap[$key])){
                $idMap[$key]=[$i];
            }else{
                $idMap[$key][]=$i;
            }
        }
        foreach ($idMap as $key => $indexArr) {
            if(count($indexArr)>1){
                if(count($indexArr)>2){
                    //keep last one
                    unset($indexArr[count($indexArr)-1]);
                    foreach ($indexArr as $idx) {
                        $deleteList[]=$idx;
                    }
                }else{
                    //add it to delete
                    $keep=$keepRow($rows[$indexArr[0]], $rows[$indexArr[1]]);
                    if($keep==1){
                        $deleteList[]=$indexArr[1];
                    }elseif($keep==2){
                        $deleteList[]=$indexArr[0];
                    }
                }
            }
        }
        if(!empty($deleteList)){
            foreach ($deleteList as $delIndex) {
                $this->utils->debug_log('delete row', $rows[$delIndex]);
                unset($rows[$delIndex]);
            }
        }

        unset($idMap);
        unset($deleteList);
    }

    public function queryHBIncompleteLastGame(array $usernameKeys, $db=null){
        if(empty($db)){
            $db=$this->db;
        }
        $db->from('hb_incomplete_games')->where_in('username_key', $usernameKeys)
            ->order_by('dt_started desc')->limit(1);
        return $this->runOneRowArray($db);
    }

    public function queryHBIncompleteGameList($username, $db=null){
        if(empty($db)){
            $db=$this->db;
        }
        $usernameKeys=[];
        if(!empty($username)){
            $this->utils->debug_log('search game username by '.$username);
            $this->load->model(['game_provider_auth']);
            $hb_common_apis=$this->utils->getConfig('hb_common_apis');
            $gameUsernameList = $this->game_provider_auth->getMultipleGameUsernameBy($username, $hb_common_apis);
            if(!empty($gameUsernameList)){
                foreach ($gameUsernameList as $row) {
                    $usernameKeys[]=$row['game_provider_id'].'-'.$row['login_name'];
                }
            }else{
                $this->utils->error_log('didnot find any game username', $username);
                return null;
            }
        }
        $db->select('username, game_instance_id, friendly_id, game_name, game_key_name, provider, brand_game_id, dt_started, stake, payout, game_state_id, game_state_name, game_platform_id')
          ->from('hb_incomplete_games');
        if(!empty($usernameKeys)){
            $db->where_in('username_key', $usernameKeys);
        }

        $rows=$this->runMultipleRowArray($db);
        $this->utils->printLastSQL();
        return $rows;
    }

    public function calcOriginalMD5ByApi($tableName, $api, $externalUniqueId, $externalUniqueIdFldName='external_uniqueid'){
        $originalStr=null;
        $md5=null;

        $fields=$api->getMD5Fields();
        $md5_fields_for_original=$fields['md5_fields_for_original'];
        $md5_float_fields_for_original=$fields['md5_float_fields_for_original'];
        if(!empty($md5_fields_for_original)){
            $this->db->from($tableName)->where($externalUniqueIdFldName, $externalUniqueId);
            $row=$this->runOneRowArray();
            $md5=$this->generateMD5SumOneRow($row, $md5_fields_for_original,
                $md5_float_fields_for_original, $originalStr);
        }
        return ['original'=>$originalStr, 'md5'=>$md5];
    }

}
