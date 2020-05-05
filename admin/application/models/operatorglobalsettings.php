<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/base_model.php';

/**
 * Operatorglobalsettings
 *
 * This model represents global operator settings.
 *
 *
 */
class Operatorglobalsettings extends BaseModel {

	const CACHE_TTL = 3600; # 1 hour

	const NOTIF_NOT_FOUND_PLAYER = 1;
	const NOTIF_NO_ENOUGH_BALANCE = 2;
	const NOTIF_NETWORK_ERROR = 3;
	const NOTIF_GAME_ACCOUNT_IS_LOCKED = 4;
	const NOTIF_TRANSFER_AMOUNT_IS_TOO_HIGH = 5;
	const NOTIF_TRANSFER_AMOUNT_IS_TOO_LOW = 6;
	const NOTIF_LOGIN_PROBLEM = 7;
	const NOTIF_SESSION_TIMEOUT = 8;
	const NOTIF_NO_ENOUGH_CREDIT_IN_SYSTEM = 9;
	const NOTIF_GAME_PROVIDER_ACCOUNT_PROBLEM = 10;
	const NOTIF_API_MAINTAINING = 11;
	const NOTIF_API_FAILED = 12;
	const NOTIF_INVALID_TRANSFER_AMOUNT = 13;
	const NOTIF_INVALID_KEY = 14;
	const NOTIF_DUPLICATE_TRANSFER = 15;
	const NOTIF_DISABLED_DEPOSIT_BY_GAME_PROVIDER = 16;

	const NOTIF_CS_URL = 0;

	// Copied from Payment_management so other classes have access
	// OGP-13942
	const FINANCIAL_ACCOUNT_SETTING_OTHERS_LIST = array(
		'bank_number_validator_mode',
		'financial_account_enable_deposit_bank',
		'financial_account_require_deposit_bank_account',
		'financial_account_deposit_account_limit',
		'financial_account_max_deposit_account_number',
		'financial_account_can_be_withdraw_and_deposit',
		'financial_account_deposit_account_default_unverified',
		'financial_account_withdraw_account_limit',
		'financial_account_max_withdraw_account_number',
		'financial_account_allow_edit',
		'financial_account_one_account_per_institution',
		'financial_account_allow_delete',
		'financial_account_withdraw_account_default_unverified',
	);

	function __construct() {
		parent::__construct();
	}

	protected $tableName = 'operator_settings';

	private function getCacheKey($name) {
		return PRODUCTION_VERSION."|$this->tableName|$name";
	}
	/**
	 * will get operator settings
	 *
	 * @param $name - str
	 *
	 * @return array
	 */
	public function getOperatorGlobalSetting($name) {
		$this->db->select('*')->from('operator_settings');
		$this->db->where('name', $name);
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * will set operator setting
	 *
	 * @param $data - array
	 *
	 * @return void
	 */
	public function setOperatorGlobalSetting($data) {
		$this->utils->deleteCache($this->getCacheKey($data['name'].'value'));
		$this->utils->deleteCache($this->getCacheKey($data['name'].'template'));
		$this->db->where('name', $data['name']);
		$this->db->update('operator_settings', $data);
		return true;
	}

	public function getSettingDoubleValue($name, $defaultValue=null) {
		$value = $this->getSettingValue($name, $defaultValue);
		return is_null($value) ? null : doubleval($value);
	}

	public function getSettingIntValue($name, $defaultValue=null) {
		$value = $this->getSettingValue($name, $defaultValue);
		return is_null($value) ? null : intval($value);
	}

	/**
	 * Get the setting Ignore cache from operator_settings by name
	 *
	 * @param string $name The setting name.
	 * @param string|integer|float $defaultValue The default value while can't get.
	 * @return string|integer|float $value The setting value.
	 */
	public function getSettingValueWithoutCache($name, $defaultValue=null) {
		$row = $this->getSetting($name);
		if ($row) {
			$value = $row->value;
		}else{
			$value = $defaultValue;
		}

		return $value;
	} // EOF getSettingValueWithoutCache

	/**
	 * Get the setting From cache from operator_settings by name
	 *
	 * @param string $name The setting name.
	 * @param string|integer|float $defaultValue The default value while can't get.
	 * @return string|integer|float $value The setting value.
	 */
	public function getSettingValue($name, $defaultValue=null) {
		$monitor_operator_settings=$this->utils->getConfig('monitor_operator_settings');
		$monitored= !empty($monitor_operator_settings) && in_array($name, $monitor_operator_settings);

		$value = $this->utils->getTextFromCache($this->getCacheKey($name.'value'));
		if($monitored){
			$this->utils->debug_log('load setting from cache', $name, $field, $value);
		}
		if($value === false) {
			$value=$this->getSettingValueWithoutCache($name, $defaultValue);
			$this->utils->saveTextToCache($this->getCacheKey($name.'value'), $value, self::CACHE_TTL);
		}

		return empty($value) ? $defaultValue : $value;
	}// EOF getSettingValue

	/**
	 *
	 *
	 * @param  [type] $name    [description]
	 * @param  string $field   [description]
	 * @param  mixin $default any object
	 * @return [type]          [description]
	 */
	public function getSettingJson($name, $field='value', $default=null) {
		$monitor_operator_settings=$this->utils->getConfig('monitor_operator_settings');
		$monitored= !empty($monitor_operator_settings) && in_array($name, $monitor_operator_settings);

		$json = $this->utils->getTextFromCache($this->getCacheKey($name.$field));
		if($monitored){
			$this->utils->debug_log('load setting from cache', $name, $field, $json);
		}
		if($json === false) {
			$row = $this->getSetting($name);
			if ($row) {
				$json = (isset($row->$field)) ? $row->$field : '';
			} else {
				$json = '';
			}
			$this->utils->saveTextToCache($this->getCacheKey($name.$field), $json, self::CACHE_TTL);
		}
		if(!empty($json)){
			return json_decode($json,true);
		}
		if($monitored){
			$this->utils->debug_log('final setting', $name, $field, $json);
		}

		return $default;
	}

	public function getSetting($name, $db=null) {
		if(empty($db)){
			$db=$this->db;
		}
		$db->from($this->tableName);
		$db->where('name', $name);
		return $this->runOneRow($db);
	}

	public function existsSetting($name, $db=null){
		$row=$this->getSetting($name, $db);
		return !empty($row);
	}

	public function putSetting($name, $val, $field = 'value', $db=null) {
		if(empty($db)){
			$db=$this->db;
		}
        $this->utils->deleteCache($this->getCacheKey($name.$field));
        $this->utils->deleteCache($this->getCacheKey($name));
		$db->where('name', $name);
		return $this->runUpdate(array($field => $val), $db);
	}

	public function putSettingJson($name, $val, $field='value', $db=null){
		return $this->putSetting($name, json_encode($val), $field, $db);
	}

	public function insertSettingJson($name, $val, $field='value', $db=null){
		return $this->insertSetting($name,  json_encode($val), $field, $db);
	}

	/**
	 *
	 * @param  string $name
	 * @param  mixin $val   any object
	 * @param  string $field
	 * @param  object $db
	 * @return bool
	 */
	public function syncSettingJson($name, $val, $field='value', $db=null){
		if($this->existsSetting($name, $db)){
			return $this->putSettingJson($name, $val, $field, $db);
		}else{
			return $this->insertSettingJson($name, $val, $field, $db);
		}
	}

	public function getFriendReferralSettings() {
		$this->db->from('friendreferralsettings')->where('status', self::OLD_STATUS_ACTIVE);
		return $this->runOneRow();
	}

	public function getAllOperatorSettings() {
		$sql = "SELECT * FROM " . $this->tableName;
		return $this->db->query($sql)->result_array();
	}

	public function addRecord($data) {
		return $this->db->insert($this->tableName, $data);
	}

	public function truncateTablesSync($secret_key) {
		if ($secret_key == 'Ch0wK1ing&M@ng!n@s@l') {
			$this->db->truncate($this->tableName);
			return array('success' => 1);
		}
		return array('success' => 0);
	}

	public function getSystemSettings($nameList) {
		$this->db->from($this->tableName)->where_in('name', $nameList);
		$rows = $this->runMultipleRow();
		$settings = array();
		//$settingMap = array();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$params = null;
				if (isset($row->description_json) && !empty($row->description_json)) {
					$params = json_decode($row->description_json, true);
				}

				if(isset($params['config'])){
				    $params['list'] = $this->utils->getConfig($params['config']);
                }

				//$settingMap[$row->name] = array('value' => $row->value, 'params' => $params, 'note' => $row->note);
				$settings[$row->name] = array('value' => $row->value, 'params' => $params, 'note' => $row->note);
			}
		}

		// foreach ($nameList as $name) {
		// 	$settings[$name] = isset($settingMap[$name]) ? $settingMap[$name] : null;
		// }
		return $settings;
	}

	public function getSystemSetting($name){
        $settings = $this->getSystemSettings([$name]);

        return (isset($settings[$name])) ? $settings[$name] : NULL;
    }

	public function saveSettings($settings) {
		foreach ($settings as $name => $value) {
		    $this->putSetting($name, (is_array($value)) ? json_encode($value) : $value);
		}
	}

	public function getSettingBooleanValue($name) {
		$row = $this->getSetting($name);
		if ($row) {
			$value = false;
			if ($row->value === 'true' || $row->value === '1') {
				//convert true and 1 to true
				$value = true;
			}
			return $value;
		}
		return null;
	}

	public function getOperatorNameListForAllCronJobs() {
		$cronjobs = $this->utils->getConfig('all_cron_jobs');
		$nameList = array();
		foreach ($cronjobs as $key => $row) {
			$nameList[] = $key;
		}
		return $nameList;
	}

	public function addAllCronJobs() {

		$cronjobs = $this->utils->getConfig('all_cron_jobs');

		$allJobs = $this->getAllCronJobs();
		$jobNames = array();
		foreach ($allJobs as $job) {
			$jobNames[] = $job['name'];
		}

		$rows = array();
		foreach ($cronjobs as $key => $row) {
			//ignore exists
			if (!in_array($key, $jobNames)) {
				$rows[] = array(
					'name' => $key,
					'value' => $row['default_enabled'],
					'note' => $row['note'],
					'description_json' => json_encode(array(
						"type" => "checkbox",
						"default_value" => $row['default_enabled'], "label_lang" => $row['note'],
					)),
				);
			}
		}

		$rlt=true;
		if(!empty($rows)){
			$rlt=$this->db->insert_batch('operator_settings', $rows);
		}

		foreach ($jobNames as $jobName) {
			if(!array_key_exists($jobName,$cronjobs)){
				//delete cron jobs
				$this->db->where('name',$jobName)->delete('operator_settings');
				$this->utils->debug_log('delete',$jobName);
			}
		}

		return $rlt;
	}

	public function deleteAllCronJobs() {
		$this->db->like('name', 'cronjob_', 'after')->delete('operator_settings');

		return true;
	}

	public function getAllCronJobs() {
		$this->db->from($this->tableName)->like('name', 'cronjob_', 'after');
		return $this->runMultipleRowArray();
	}

	# Import payment_account_types and special_payment_list from config to database
	public function importPaymentAccountSetting() {
		$paymentAccountTypes = $this->utils->getConfig('payment_account_types');
		# encode the config array into JSON and store in global settings
		$paymentAccountTypesJSON = json_encode($paymentAccountTypes);
		$this->utils->debug_log("==Importing payment account setting==");
		$this->utils->debug_log("payment_account_types in config: ", $paymentAccountTypes);
		$this->utils->debug_log("Translated to payment_account_types in db: ", $paymentAccountTypesJSON);
		$this->setPaymentAccountTypes($paymentAccountTypes);
		$this->utils->debug_log("Import is done.");

		$specialPaymentList = $this->utils->getConfig('special_payment_list');

		if (!empty($specialPaymentList)) {

			# specialPaymentList is a list of bank type names. it should be translated into an array of payment account ids
			## map special payment list to bank type
			$this->db->from('banktype')
				->where_in('bankName', $specialPaymentList)
				->where('status', 'active')->order_by('banktype_order');
			$bankTypeList = $this->runMultipleRow();

			## map bank type to payment account
			$bankTypeIds = array();
			foreach ($bankTypeList as $bankType) {
				$bankTypeIds[] = $bankType->bankTypeId;
			}
			if(!empty($bankTypeIds)){
				$this->db->from('payment_account')
					->where_in('payment_type_id', $bankTypeIds);

				$paymentTypeList = $this->runMultipleRow();
				$paymentTypeIds = array();

				if(!empty($paymentTypeList)){

					## filter the paymentTypeIds with ids in getAllPaymentAccountDetails so that no mismatch
					$this->load->model('payment_account');
					$allPaymentAccounts = $this->payment_account->getAllPaymentAccountDetails();
					$allPaymentAccountIds = array();
					foreach($allPaymentAccounts as $paymentAccount) {
						$allPaymentAccountIds[] = $paymentAccount->id;
					}

					foreach($paymentTypeList as $paymentType) {
						if(in_array($paymentType->id, $allPaymentAccountIds)) {
							$paymentTypeIds[] = $paymentType->id;
						}
					}

					$paymentTypeIdsString = implode(',', $paymentTypeIds);

					$this->utils->debug_log("==Importing payment account setting==");
					$this->utils->debug_log("payment_account_types in config: ", $paymentAccountTypes);
					$this->utils->debug_log("Translated to payment_account_types in db: ", $paymentAccountTypesJSON);
					$this->setPaymentAccountTypes($paymentAccountTypes);
					$this->utils->debug_log("Import is done.");

					$this->utils->debug_log("special_payment_list in config: ", $specialPaymentList);
					$this->utils->debug_log("Translated to special_payment_list in db: ", $paymentTypeIdsString);
					$this->setSpecialPaymentList($paymentTypeIds);
					$this->utils->debug_log("Import is done.");

				}else{
					$this->utils->error_log("empty paymentTypeList");
				}
			}else{
				$this->utils->error_log("empty paymentTypeList");
			}
		}
	}

	## Get and set methods for payment_account_types and special_payment_list
	public function getPaymentAccountTypes($raw = false) {
		$json = $this->utils->getTextFromCache($this->getCacheKey('payment_account_types'));
		if($json === false) {
			$paymentAccountTypes = $this->getSetting('payment_account_types');
			$paymentAccountTypesJSON = (empty($paymentAccountTypes->value)) ? $paymentAccountTypes->template : $paymentAccountTypes->value;
			$this->utils->saveTextToCache($this->getCacheKey('payment_account_types'), $paymentAccountTypesJSON, self::CACHE_TTL);
		} else {
			$paymentAccountTypesJSON = $json;
		}

		return $raw ? $paymentAccountTypesJSON : json_decode($paymentAccountTypesJSON, true);
	}

	public function setPaymentAccountTypes($paymentAccountTypes, $raw = false) {
		$paymentAccountTypesJSON = $raw ? $paymentAccountTypes : json_encode($paymentAccountTypes);
		$this->putSetting('payment_account_types', $paymentAccountTypesJSON);
	}

	public function getSpecialPaymentList() {
		$paymentTypeIdsString = $this->getSettingValue('special_payment_list');
		return explode(',', $paymentTypeIdsString);
	}

	public function setSpecialPaymentList($paymentTypeIds) {
		if(!empty($paymentTypeIds)){
			$paymentTypeIdsString = implode(',', $paymentTypeIds);
			$this->putSetting('special_payment_list', $paymentTypeIdsString);
		}
	}

	public function getSpecialPaymentListMobile() {
		$paymentTypeIdsString = $this->getSettingValue('special_payment_list_mobile');
		return explode(',', $paymentTypeIdsString);
	}

	public function isDefaultCollectionAccount($paymentAccountId) {
		$special_payment_list = $this->getSpecialPaymentList();
		$special_payment_list_mobile = $this->getSpecialPaymentListMobile();

		if(array_search($paymentAccountId, $special_payment_list) === false && array_search($paymentAccountId, $special_payment_list_mobile) === false){
			return false;
		}
		return true;
	}

	public function setSpecialPaymentListMobile($paymentTypeIds) {
		if(!empty($paymentTypeIds)){
			$paymentTypeIdsString = implode(',', $paymentTypeIds);
			$this->putSetting('special_payment_list_mobile', $paymentTypeIdsString);
		}
	}

    public function getPlayerCenterWithdrawalPageSetting(){
        $showMaxWithdrawalPerTransaction = (int)$this->getSettingValue('show_max_withdrawal_per_transaction');
        $showDailyMaxWithdrawalAmount = (int)$this->getSettingValue('show_daily_max_withdrawal_amount');

        $setting = ['showMaxWithdrawalPerTransaction' => $showMaxWithdrawalPerTransaction, 'showDailyMaxWithdrawalAmount' => $showDailyMaxWithdrawalAmount];
        return $setting;
    }

    public function getCustomWithdrawalProcessingStage() {
		$settingRow = $this->getSetting('custom_withdrawal_processing_stages');
		if($settingRow) {
			$setting = json_decode($settingRow->template, true);
		} else {
			$setting = array();
		}

		if(!isset($setting['payProc'])) {
			$setting['payProc']['name'] = lang('pay.processing');
			$setting['payProc']['enabled'] = false;
		}

		$this->load->model('external_system');
		$withdrawAPIs = $this->external_system->getWithdrawPaymentSystemsKV();
		if(count($withdrawAPIs) > 0) {
			$setting['payProc']['mustEnable'] = true;
			$setting['payProc']['enabled'] = true;
			if(empty($setting['payProc']['name'])) {
				$setting['payProc']['name'] = lang('pay.processing');
			}
		}

		for($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) {
			if(!array_key_exists($i, $setting)) {
				$setting[$i] = array('name'=>'', 'enabled'=>false);
			}
		}

		$maxCSIndex = -1;
		for($i = 0; $i < CUSTOM_WITHDRAWAL_PROCESSING_STAGES; $i++) {
			if(!array_key_exists('name', $setting[$i])) {
				$setting[$i]['name'] = '';
			}

			if($setting[$i]['enabled']) {
				$maxCSIndex = $i;
			}
		}

		$setting['maxCSIndex'] = $maxCSIndex;

		return $setting;
	}

	private $_customStageSetting;
	public function getCustomStageName($dwStatus) {
		if(!$_customStageSetting) {
			$_customStageSetting = $this->getCustomWithdrawalProcessingStage();
		}

		if(substr($dwStatus, 0, 2) == 'CS') {
			$index = intval(substr($dwStatus, 2, 3));
			return $_customStageSetting[$index]['name'];
		}

		if($dwStatus == Wallet_model::PAY_PROC_STATUS) {
			return $_customStageSetting['payProc']['name'];
		}

		return '';
	}

	public function setPlayerCenterWithdrawalPageSetting($withdrawal_setting){
        $this->putSetting('show_max_withdrawal_per_transaction', $withdrawal_setting['showMaxWithdrawalPerTransaction']);
        $this->putSetting('show_daily_max_withdrawal_amount', $withdrawal_setting['showDailyMaxWithdrawalAmount']);
    }

	public function setCustomWithdrawalProcessingStage($setting) {
		$settingJSON = json_encode($setting);
		$this->utils->debug_log("Saving settings: [$settingJSON]");
		$this->putSetting('custom_withdrawal_processing_stages', $settingJSON, 'template');
	}

	private function getStringFrom($val){
		$val=strval($val);

		return $val=='NULL' ? null : $val;
	}

	public function syncAllOperatorSettings(){
		if(!$this->db->table_exists('operator_settings')){
			return;
		}

		//load permissions.json
		//use xml, because extra info is json too
		$xmlFile=APPPATH.'config/operator_settings.xml';

		// $json=file_get_contents($jsonFile);
		$xml=simplexml_load_file($xmlFile);
		// $permissions=$this->utils->decodeJson($json);

		$now=$this->utils->getNowForMysql();

		if(empty($xml)){
			throw new Exception('wrong operator_settings.xml file');
		}
		foreach ($xml->ROW as $row) {
			$data=[
				'name'=>$this->getStringFrom($row->name),
				'value'=>$this->getStringFrom($row->value),
				'note'=>trim($this->getStringFrom($row->note)),
				'template'=>trim($this->getStringFrom($row->template)),
				'description_json'=>trim($this->getStringFrom($row->description_json)),
			];

			// $this->utils->debug_log('row', $data);

			$this->db->select('*')->from('operator_settings')->where('name', $data['name']);
            $operator_setting = $this->runOneRow();
			if(empty($operator_setting)){
				$this->insertData('operator_settings', $data);
			}else{
                $update_data = [];
                if(md5($operator_setting->note) != md5($data['note'])){
                    $update_data['note'] = $data['note'];
                }
//                if(md5($operator_setting->template) != md5($data['template'])){
//                    $update_data['template'] = $data['template'];
//                }
                if(md5($operator_setting->description_json) != md5($data['description_json'])){
                    $update_data['description_json'] = $data['description_json'];
                }
//                if(!empty($update_data)){
//                    $this->db->set($update_data);
//                    $this->db->where('name', $data['name']);
//                    $this->runAnyUpdate('operator_settings');
//                }

				if($data['name'] == 'cashier_notification_settings') {
//					$this->db->set($data);
//					$this->db->where('name', $data['name']);
//					$this->runAnyUpdate('operator_settings');
				} else {
					if(!empty($update_data)){
						$this->db->set($update_data);
						$this->db->where('name', $data['name']);
						$this->runAnyUpdate('operator_settings');
					}
				}


				//ignore
				// $this->db->set($data);
				// $this->db->where('name', $data['name']);
				// $this->runAnyUpdate('operator_settings');
			}

		}

		$this->addAllCronJobs();

	}

	public function insertSetting($name, $val, $field='value', $db=null){
		$data=[
			'name'=> $name,
			$field => $val,
		];

		$this->utils->deleteCache($this->getCacheKey($name.$field));
    	$this->utils->deleteCache($this->getCacheKey($name));

		return $this->insertData($this->tableName, $data, $db);
	}

	public function copyTemplateSettingToDB($db=null){
		//copy
		$view_template=$this->utils->getConfig('view_template');
		if(!empty($view_template)){
			$name='player_center_template';

			if($this->existsSetting($name, $db)){
				return $this->putSetting($name, $view_template, 'value', $db);
			}else{
				return $this->insertSetting($name, $view_template, 'value', $db);
			}
		}
	}

	/*
	*for message template only
	 * OGP-2154
	*/

	public function resestMsgTpl($rows){
        $this->db->delete($this->tableName, array('value' =>'msgTpl' ));
        if(!empty($rows)){
            return $this->db->insert_batch('operator_settings', $rows);

        }
    }

    public function getAllMsgTpl(){
        $this->db->select('*')->from('operator_settings');
        $this->db->where('value', 'msgTpl');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getPlayerCenterLogoutRedirectUrl(){
        $val = (int)$this->getSettingIntValue('player_center_logout_action', 1);

        $url = NULL;

        switch($val){
            case 2:
                if(!$this->utils->is_mobile()){
                    $url = $this->utils->getSystemUrl('www');
                }else{
                    $url = $this->utils->getSystemUrl($this->config->item('mobile_redirection_url_prefix'));
                }
                break;
            case 1:
            default:
                $url = $this->utils->getSystemUrl('player', '/iframe/auth/login');
                break;
        }

        return $url;
    }

    public function getGameWalletSettings(){
        $this->CI->load->model('external_system');

        $game_api_list = $this->CI->external_system->getAllActiveSystemApiByType(SYSTEM_GAME_API);

        $val = $this->getSettingJson('game_wallet_settings', 'template');
        if(empty($val)){
            $val = [];
        }

        $sort = 0;
        foreach($game_api_list as $game_api_entry){
            $sort++;

            $entry = [];
            $entry['wallet_name'] = lang($game_api_entry['system_code']);
            $entry['wallet_name'] = (empty($entry['wallet_name'])) ? $game_api_entry['system_code'] : $entry['wallet_name'];
            $entry['sort'] = (isset($val[$game_api_entry['id']])) ? $val[$game_api_entry['id']]['sort'] : $sort;
            $entry['enabled_on_desktop'] = (isset($val[$game_api_entry['id']])) ? $val[$game_api_entry['id']]['enabled_on_desktop'] : TRUE;
            $entry['enabled_on_mobile'] = (isset($val[$game_api_entry['id']])) ? $val[$game_api_entry['id']]['enabled_on_mobile'] : TRUE;

            $val[$game_api_entry['id']] = $entry;
        }

        uasort($val, function($entry1, $entry2){
            if ($entry1['sort'] == $entry2['sort']) {
                return 0;
            }
            return ($entry1['sort'] < $entry2['sort']) ? -1 : 1;
        });

        return $val;
    }

    public function getPlayerMessageRequestFormSettings(){
        $val = $this->getSettingJson('player_message_request_form_attributes', 'template');

        if(empty($val)){
            $val = [];
        }

        $val['enable_for_guest'] = (!isset($val['enable_for_guest'])) ? FALSE : !!$val['enable_for_guest'];
        $val['enable_for_player'] = (!isset($val['enable_for_player'])) ? FALSE : !!$val['enable_for_player'];
        $val['enable_floating_button'] = (!isset($val['enable_floating_button'])) ? TRUE : !!$val['enable_floating_button'];
        $val['real_name_enable'] = (!isset($val['real_name_enable'])) ? TRUE : !!$val['real_name_enable'];
        $val['real_name_required'] = (!isset($val['real_name_required'])) ? TRUE : !!$val['real_name_required'];
        $val['username_enable'] = (!isset($val['username_enable'])) ? TRUE : !!$val['username_enable'];
        $val['username_required'] = (!isset($val['username_required'])) ? TRUE : !!$val['username_required'];
        $val['contact_method_enable'] = (!isset($val['contact_method_enable'])) ? TRUE : !!$val['contact_method_enable'];
        $val['contact_method_required'] = (!isset($val['contact_method_required'])) ? TRUE : !!$val['contact_method_required'];
        $val['contact_method'] = (!isset($val['contact_method'])) ? 'mobile_phone' : $val['contact_method'];

        $val['window_title'] = (!isset($val['window_title'])) ? NULL : $val['window_title'];
        $val['footer_notice'] = (!isset($val['footer_notice'])) ? NULL : $val['footer_notice'];

        return $val;
    }

    public function getAfterDepositRedirectUrl(){
        $val = (int)$this->getSettingIntValue('deposit_successful_action', 1);
        $redirectUrl = '';

        switch($val){
            case 2:
                    $redirectUrl .= $this->utils->getSystemUrl('player', '/player_center/menu');
                break;
            case 1:
            default:
                    $redirectUrl .= $this->utils->getSystemUrl('player', '/player_center/menu');
                break;
        }

        return $redirectUrl;
    }

    public static function renderFormElement($setting_name, $setting_info){
        $value = false;
        if (isset($setting_info['value']) && $setting_info['value'] !== null && $setting_info['value'] !== '') {
            $value = $setting_info['value'];
        } elseif (isset($setting_info['params']['default_value']) && $setting_info['params']['default_value'] !== null && $setting_info['params']['default_value'] !== '') {
            $value = $setting_info['params']['default_value'];
        }

        $html = '';
        switch($setting_info['params']['type']){
            case 'checkbox':
                if(isset($setting_info['params']['list'])){
                    $value = json_decode($value, TRUE);
                    $value = (empty($value)) ? [] : $value;
                    foreach($setting_info['params']['list'] as $entry_key => $entry_value){
                        $html .= '<div class="checkbox"><label>';
                        $html .= '<input type="checkbox" name="value_' . $setting_name . '[]" value="' . $entry_key . '"' . ((in_array($entry_key, $value)) ? ' checked="checked"' : '') . ' >';
                        $html .= lang('operator_settings.' . $setting_name . '.' . $entry_key);
                        $html .= '</label></div>';
                    }
                }else{
                    if ($value === 'true' || $value === '1') {
                        $value = true;
                    }

                    $html .= "<label><input name='value_" . $setting_name . "' type='" . $setting_info['params']['type'] . "' " . ($value ? "checked='checked'" : "") . " value='true'></label>";
                }
            break;
            case 'radio':
                foreach($setting_info['params']['list'] as $entry_key => $entry_value){
                    $html .= '<div class="radio"><label>';
                    $html .= '<input type="radio" name="value_' . $setting_name . '" value="' . $entry_key . '"' . (($entry_key == $value) ? ' checked="checked"' : '') . ' >';
                    $html .= lang('operator_settings.' . $setting_name . '.' . $entry_key);
                    $html .= '</label></div>';
                }
            break;
            case 'select':
                $html .= '<div class="select"><select name="value_' . $setting_name . '">';
                foreach($setting_info['params']['list'] as $entry_key => $entry_value){
                    $html .= '<option value="' . $entry_key . '"' . (($entry_key == $value) ? ' selected="selected"' : '') . ' >';
                    $html .= lang('operator_settings.' . $setting_name . '.' . $entry_key);
                    $html .= '</option>';
                }
                $html .= '</select></div>';
            break;
            case 'number':
                $html .= "<input name='value_" . $setting_name . "' type='number' value='" . $value . "' class='form-control input-sm'>";
                break;
            case 'text':
            default:
                $html .= "<input name='value_" . $setting_name . "' type='text' value='" . $value . "' class='form-control input-sm'>";
                break;
        }

        return $html;
    }

}

/* End of file Operatorglobalsettings.php */
/* Location: ./application/models/operator_settings.php */
