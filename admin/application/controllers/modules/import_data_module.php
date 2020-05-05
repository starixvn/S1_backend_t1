<?php
trait import_data_module {

	public function import_dwusers() {
		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');

		set_time_limit($default_sync_game_logs_max_time_second);

		require_once dirname(__FILE__) . "/Crypt.php";
		$key = 'A#-3$%^6';

		// echo Crypt::decode('a4aaPsGKVQC4umikxz5GtabVRRZyS4BbpmBGMVXbRaIuIso', $key);
		//
		$this->load->model(array('player_model'));
		$defaultLevelId = 1;
		$bonusLevelId = 26;

		$sql = <<<EOD
select distinct dwusers.uid, dwusers.username,dwusers.email , dwusers.password, dwusers.regTime, dwusers.regIP,
dwusers.realname, dwusers.status, dwusers.tel, dwusers.qq, dwusers.integral, dwusers.level,
dwusers.api_pass, dwusers.ipaddress, dwusers_count.deposit, dwusers_count.withdrawals,
dwusers_count.money, dwusers_count.depositnum, dwusers_count.withdnum
from dwusers join dwusers_count on dwusers.uid=dwusers_count.uid
EOD;

		$qry = $this->db->query($sql);

		foreach ($qry->result() as $row) {
			$password = Crypt::decode($row->password, $key);
			$externalId = $row->uid;
			$username = $row->username;
			if (substr($username, 0, 2) == 'dw') {
				$username = substr($username, 2);
			}
			$levelId = $defaultLevelId;
			if ($row->level > 0) {
				$levelId = $bonusLevelId;
			}
			$balance = $row->money;
			$ip = '';
			if ($row->regIP) {
				$ip = long2ip($row->regIP);
			}
			$api_pass = Crypt::decode($row->api_pass, $key);

			$regDateTime = new DateTime();
			$regTime = $regDateTime->setTimestamp($row->regTime);

			$this->utils->debug_log('uid', $externalId, 'username', $username, 'password', $password, 'api_pass', $api_pass, 'regTime', $regTime->format('Y-m-d H:i:s'));

			$extra = array('email' => $row->email,
				'createdOn' => $regTime->format('Y-m-d H:i:s'),
				'point' => $row->integral,
				'approved_deposit_count' => $row->depositnum,
				'totalDepositAmount' => $row->deposit,
				'approvedWithdrawCount' => $row->withdnum,
				'approvedWithdrawAmount' => $row->withdrawals,
			);
			$details = array('firstName' => $row->realname,
				'language' => 'Chinese',
				'contactNumber' => $row->tel,
				'imAccount' => $row->qq,
				'city' => $row->ipaddress,
				'temppass' => $api_pass,
				'registrationIP' => $ip,
			);

			$this->player_model->startTrans();
			$this->player_model->importPlayer($externalId, $levelId, $username, $password, $balance,
				$extra, $details);
			$this->player_model->endTrans();
		}

	}

	public function import_lesbet($ouaccountTable, $afaccountTable) {
		$default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');

		set_time_limit($default_sync_game_logs_max_time_second);

		$import_player_limit = 0;

		// $ouaccountTable = 'ouaccount_0117';
		// $ouaccountTable='ouaccount_0117';
		// require_once dirname(__FILE__) . "/Ou_crypt.php";
		$key = 'commonex';
		// [ 0x12, 0x34, 0x56, 0x78, 0x90, 0xAB, 0xCD, 0xEF ];
		$iv = hex2bin('1234567890ABCDEF');

		$this->load->model(array('player_model', 'affiliatemodel'));
		$defaultLevelId = 1;
		$bonusLevelId = 26;

		//import afaccount
		$sql = <<<EOD
select id,currency,account, password,passkey,realname,sex,birthday, email,phone, qq, status,
website, spreadweb, spreadcode, remark, proportion, createtime, balance, ipaddress,lastipaddress,withdrawamount
from {$afaccountTable}
EOD;

		$qry = $this->db->query($sql);
		$rows = $qry->result();
		$cnt = 0;
		$failedAffiliate = array();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$password = $this->utils->decryptBase64DES($row->passkey, $key, $iv);
				$externalId = $row->id;
				$username = $row->account;
				$gender = $row->sex == '1' ? 'Female' : 'Male';
				$phone = $row->phone;
				$im1 = $row->qq;
				$imType1 = 'QQ';
				$status = $row->status == '1' ? Affiliatemodel::OLD_STATUS_ACTIVE : Affiliatemodel::OLD_STATUS_INACTIVE;
				$firstname = $row->realname;
				$lastname = "";
				$birthday = $row->birthday;
				$email = $row->email;
				$currency = $row->currency;
				$lastLoginIp = $row->lastipaddress;
				$ip_address = $row->ipaddress;
				$createdOn = $row->createtime;
				$trackingCode = $row->spreadcode;
				$notes = $row->remark;
				$language = 'Chinese';
				// $updatedOn = $this->utils->getNowForMysql();

				$this->affiliatemodel->startTrans();

				$affId = $this->affiliatemodel->importAffiliate($externalId, $username, $password, $trackingCode, $createdOn,
					$firstname, $lastname, $status,
					array(
						'gender' => $gender,
						'phone' => $phone,
						'im1' => $im1,
						'imType1' => $imType1,
						'birthday' => $birthday,
						'currency' => $currency,
						'lastLoginIp' => $lastLoginIp,
						'ip_address' => $ip_address,
						'notes' => $notes,
						'language' => $language,
						'email' => $email,
					));

				if ($this->affiliatemodel->endTransWithSucc()) {
					$cnt++;
				} else {
					$failedAffiliate[] = $this->utils->debug_log('failed affiliate', $externalId, 'username', $username);
				}

				// if ($affId) {
				//username => id map
				// $affMap[$username] = $affId;
				// $cnt++;
				// }
			}
		}
		$this->returnText('affiliate cnt:' . $cnt);
		$this->returnText('failedAffiliate:' . count($failedAffiliate));

		$affMap = $this->affiliatemodel->getUsernameMap();

		$sql = <<<EOD
select id, account,passkey,alias,password , email, realname, createtime, ipaddress,sex,
birthday,phone,qq,status,balance, affaccount, amount
from {$ouaccountTable}
EOD;

		$qry = $this->db->query($sql);
		$rows = $qry->result();
		$cnt = 0;
		$ignoreCnt = 0;
		$failedPlayer = array();
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$password = $this->utils->decryptBase64DES($row->passkey, $key, $iv);
				// $password = Ou_crypt::decode($row->password, $key);
				$externalId = $row->id;
				$username = $row->account;
				$balance = $row->amount;
				$frozen = $row->balance;
				$gender = $row->sex == '1' ? 'Female' : 'Male';
				$contactNumber = $row->phone;
				$imAccount = $row->qq;
				$status = $row->status == '1' ? '0' : '1';
				$ip = $row->ipaddress;

				// $password = $api_pass;
				//$api_pass = Crypt::decode($row->api_pass, $key);
				// echo "pass: " . $row->password . "<br/>";
				// echo $api_pass = Ou_crypt::decode($row->password, $key);
				// $this->utils->debug_log('api_pass', $api_pass);
				// exit();

				$affUsername = $row->affaccount;
				$affId = null;
				if (!empty($affUsername) && isset($affMap[$affUsername])) {
					$affId = $affMap[$affUsername];
				}

				if (!empty($affUsername) && empty($affId)) {
					$this->utils->debug_log('lost aff username', $affUsername);
				}

				if (!empty($row->createtime)) {
					$regTime = new DateTime(substr($row->createtime, 0, 19));
					// $regTime = $regDateTime->setTimestamp($row->createtime);
				} else {
					$regTime = new DateTime();
				}
				$levelId = $defaultLevelId;

				$this->utils->debug_log('uid', $externalId, 'username', $username, 'password', $password, 'regTime', $regTime->format('Y-m-d H:i:s'));

				$extra = array('email' => $row->email,
					'createdOn' => $regTime->format('Y-m-d H:i:s'),
					'affiliateId' => $affId,
					'frozen' => $frozen,
				);
				$details = array('firstName' => $row->realname,
					'language' => 'Chinese',
					'registrationIP' => $ip,
					'gender' => $gender,
					'contactNumber' => $contactNumber,
					'imAccount' => $imAccount,
				);

				$this->player_model->startTrans();
				$added = $this->player_model->importPlayer($externalId, $levelId, $username, $password, $balance,
					$extra, $details);
				if ($this->player_model->endTransWithSucc()) {
					if ($added) {
						$cnt++;
					} else {
						$ignoreCnt++;
					}
					//debug
					if ($import_player_limit > 0 && $cnt > $import_player_limit) {
						break;
					}
				} else {
					$failedPlayer[] = $this->utils->debug_log('failed player', $externalId, 'username', $username);
					// $failedPlayer[] = 'id:' . $externalId . ' ,username:' . $username;
				}
			}
		}

		$msg = $this->utils->debug_log('cnt', $cnt, 'ignoreCnt', $ignoreCnt, 'failedPlayer', $failedPlayer);
		$this->returnText($msg);
		// $this->returnText('cnt:' . $cnt);
		// $this->returnText('failedPlayer:' . count($failedPlayer));
	}

	public function test_password() {
		$this->load->library(array('salt'));
		$dbpass = "SjdJxWdoOZRjW637KIMfmw==";
		$realpass = trim($this->salt->decrypt($dbpass, $this->config->item('DESKEY_OG')));
		$pass = '123qwe';
		$msg = $this->utils->debug_log('compare pass', $realpass, $pass, strlen($realpass), strlen($pass), $realpass == $pass);
		$this->returnText($msg);

		$passkey = 'aWbZr4wyPpehCUP8Luo1fw==';
		$key = 'commonex';
		// [ 0x12, 0x34, 0x56, 0x78, 0x90, 0xAB, 0xCD, 0xEF ];
		$iv = hex2bin('1234567890ABCDEF');

		$str = $this->utils->decryptBase64DES($passkey, $key, $iv);

		$str = preg_replace('/[^[:print:]]/', '', $str);
		$msg = $this->utils->debug_log('decrypt pass', bin2hex($str), strlen($str));
		$this->returnText($msg);
	}

	public function clear_password() {
		$this->load->library(array('salt'));
		$this->load->model(array('player_model'));
		$players = $this->player_model->getAllImportPlayers();
		if (!empty($players)) {
			$key = $this->config->item('DESKEY_OG');
			foreach ($players as $player) {
				$dbpass = $player->password;
				$realpass = $this->salt->decrypt($dbpass, $key);
				$realpass = preg_replace('/[^[:print:]]/', '', $realpass);
				$dbpass = $this->salt->encrypt($realpass, $key);

				$codepass = $player->codepass;
				$codepass = preg_replace('/[^[:print:]]/', '', $codepass);

				$this->utils->debug_log('update player', $player->playerId, 'password', $dbpass,
					'codepass', $codepass
				);
				$this->db->where('playerId', $player->playerId)->update('player', array(
					'password' => $dbpass,
					'codepass' => $codepass,
				));
			}
		}
	}

	// public function import_affiliate_domain($tableName) {
	/*
	update affiliates set affdomain=concat(trackingCode,'.lesbet.com') where trackingCode is not null and affdomain is null and trackingCode!='failed' and trackingCode!='' and trackingCode!='fail';

*/

	// }

	public function import_win007_players($filename) {
		$cnt = 0;
		$levelId = 1;
		$file = fopen($filename, "r");
		$this->load->model(['player_model', 'wallet_model']);
		try {
			while (!feof($file)) {
				$tmpData = fgetcsv($file);
				if (empty($tmpData)) {
					continue;
				}

				$player['username'] = strval($tmpData[0]);
				$player['password'] = strval($tmpData[1]);

				$extra = array('email' => '',
					'createdOn' => $this->utils->getNowForMysql(),
					// 'point' => 0,
					// 'approved_deposit_count' => $row->depositnum,
					// 'totalDepositAmount' => $row->deposit,
					// 'approvedWithdrawCount' => $row->withdnum,
					// 'approvedWithdrawAmount' => $row->withdrawals,
				);

				$details = array(
					'firstName' => $player['username'],
					'language' => 'Chinese',
					// 'contactNumber' => $row->tel,
					// 'imAccount' => $row->qq,
					// 'city' => $row->ipaddress,
					'temppass' => $player['password'],
					// 'registrationIP' => $ip,
				);

				$this->startTrans();

				$player_id = $this->player_model->importPlayer($player['username'], $levelId,
					$player['username'], $player['password'], 0, $extra, $details, $message);

				$success = $this->endTransWithSucc();

				if (!$success) {
					$msg = $this->utils->error_log('database error', $player['username'], $message);
					return $this->returnText($msg);
				}

				if (!$player_id) {
					$msg = $this->utils->error_log('import failed', $player['username'], $message);
					return $this->returnText($msg);
				}

				// if($player_id){
				//     $this->wallet_model->import_win007_players($player_id);
				//     //$wallet = $this->wallet_model->getMainWalletBy($player_id);
				//     //if(!$wallet){
				//     //    $this->wallet_model->updateMainWallet($player_id, 0);
				//     //}
				//     $this->utils->debug_log('id',$player_id,' name:', $player['username'] ,' password:',$player['password'],
				//      ' balance:',($wallet)?$wallet['totalBalanceAmount']:'0');
				// }

			}
		} finally {
			fclose($file);
		}

		$msg = $this->utils->debug_log('import player', $cnt);
		$this->returnText($msg);
	}

	public function batch_fix_game_account($game_platform_id) {
		//compare
		$this->load->model(['game_provider_auth']);
		$accounts = $this->game_provider_auth->getAllAccountsByGamePlatform($game_platform_id);

		$msg = '';
		$changedCnt = 0;
		$noChangeCnt = 0;
		$failed = [];
		$api = $this->utils->loadExternalSystemLibObject($game_platform_id);
		if ($api && $api->isEnabled()) {

			//back up the records
			$playersBackUpBeforeChanged = json_encode($accounts);
			$backUpFile = fopen("players_backup.json", "w") or die("Unable to open file!");
			fwrite($backUpFile, $playersBackUpBeforeChanged);
			fclose($backUpFile);

			//compare game account with rebuild game account name
			foreach ($accounts as $row) {
				$rebuildGameAccount = $api->convertUsernameToGame($row['player_username']);
				if ($row['game_username'] != $rebuildGameAccount) {
					//should rename which means create and abandon old name
					if ($this->game_provider_auth->rebuildGameAccountName($row['id'], $rebuildGameAccount, $row['game_username'])) {
						$changedCnt++;
						$msg = $this->utils->debug_log('change game account', $row['game_username'], $rebuildGameAccount, $row['id']);

					} else {
						$failed[] = $row['game_username'];
						$msg = $this->utils->debug_log('change game account failed', $row['game_username'], $rebuildGameAccount, $row['id']);
					}

				} else {
					$noChangeCnt++;
					$msg = $this->utils->debug_log('not change game account', $row['game_username'], $rebuildGameAccount, $row['id']);
				}
			}
		} else {
			$msg = $this->utils->debug_log('api is disabled');
		}

		$this->returnText($msg);

		$msg = $this->utils->debug_log('batch fix game acount changed', $changedCnt, 'noChangeCnt', $noChangeCnt, 'failed', $failed);
		$this->returnText($msg);
	}

/**
 *

create table tmp_import_hxh(
id int NOT NULL AUTO_INCREMENT,
`帐号` varchar(500),
`入会时间` varchar(500),
`大股东` varchar(500),
`股东` varchar(500),
`总代理` varchar(500),
`代理` varchar(500),
`余额` varchar(500),
`状态` varchar(500),
`会员等级` varchar(500),
`反水设定` varchar(500),
`真实姓名` varchar(500),
`手机` varchar(500),
`性别` varchar(500),
`email` varchar(500),
`生日` varchar(500),
`微信` varchar(500),
`QQ` varchar(500),
`银行名称` varchar(500),
`省份` varchar(500),
`县市` varchar(500),
`银行帐户` varchar(500),
`存款次数` varchar(500),
`存款金额` varchar(500),
`提款次数` varchar(500),
`提款金额` varchar(500),
`BBIN帐号` varchar(500),
`Saba帐号` varchar(500),
`XTD帐号` varchar(500),
`MG帐号` varchar(500),
`AG帐号` varchar(500),
`HG帐号` varchar(500),
`MG2帐号` varchar(500),
`PT帐号` varchar(500),
`PT2帐号` varchar(500),
`GPI帐号` varchar(500),
`GNS帐号` varchar(500),
`最后登入IP` varchar(500),

primary key(id)
)
 */
	public function import_hxh_player_csv() {
		// $default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');

		set_time_limit(0);

		//import csv
		$ignoreHeader = true;
		$cnt = 0;
		$filename = '/home/vagrant/Code/import_data/hxh/player.csv';

		$file = fopen($filename, "r");

		$this->utils->debug_log('start hxh', $filename);

		$this->load->model(['player_model', 'wallet_model']);
		if ($file !== false) {

			try {
				while (!feof($file)) {
					$tmpData = fgetcsv($file);
					if (empty($tmpData)) {
						continue;
					}

					if ($ignoreHeader && $cnt == 0) {
						$cnt++;
						continue;
					}

					$insert_sql = <<<EOD
INSERT INTO `tmp_import_hxh`
(`帐号`,`入会时间`,`大股东`,`股东`,`总代理`,`代理`,`余额`,`状态`,`会员等级`,`反水设定`,
`真实姓名`,`手机`,`性别`,`email`,`生日`,`微信`,`QQ`,`银行名称`,`省份`,`县市`,
`银行帐户`,`存款次数`,`存款金额`,`提款次数`,`提款金额`,`BBIN帐号`,`Saba帐号`,`XTD帐号`,`MG帐号`,`AG帐号`,
`HG帐号`,`MG2帐号`,`PT帐号`,`PT2帐号`,`GPI帐号`,`GNS帐号`,`最后登入IP`)
VALUES
(?,?,?,?,?,?,?,?,?,?,
?,?,?,?,?,?,?,?,?,?,
?,?,?,?,?,?,?,?,?,?,
?,?,?,?,?,?,?)

EOD;

					$this->player_model->runRawUpdateInsertSQL($insert_sql, $tmpData);

					$cnt++;

				}
			} finally {
				fclose($file);
			}
		} else {
			$this->utils->error_log('open failed');
		}

		$msg = $this->utils->debug_log('import_hxh_player_csv', $cnt);
		$this->returnText($msg);
	}

	public function import_hxh_player($max_count = 10) {
		// $default_sync_game_logs_max_time_second = $this->config->item('default_sync_game_logs_max_time_second');

		set_time_limit(0);

		//import csv
		$ignoreHeader = true;
		$cnt = 0;
		$levelMap = ['一般会员' => 1,
			'关注' => 1,
			'TTA50 台_代理50%' => 1,
			'黑名单' => 1,
		];
		$tagMap = [
			'关注' => 2,
			'黑名单' => 1,
		];
		$bankMap = [
			'工商银行' => 1,
			'农业银行' => 4,
			'招商银行' => 2,
			'中国银行' => 6,
			'交通银行' => 5,
			'建设银行' => 3,
			'中国邮政' => 12,
			'中信银行' => 10,
			'光大银行' => 20,
			'兴业银行' => 13,
			'民生银行' => 11,
			'广州银行' => 17,
			'平安银行' => 15,
			'上海浦东发展银行' => 24,
			'广东发展银行' => 8,
			'华夏银行' => 14,
			'深圳发展银行' => 7,
		];
		$lang = 'Chinese';

		// $file = fopen($filename, "r");
		$this->load->model(['player_model', 'wallet_model', 'affiliatemodel']);
		// try {
		$this->db->from('tmp_import_hxh');
		$rows = $this->player_model->runMultipleRowArray();
		// while (!feof($file)) {

		foreach ($rows as $row) {

			foreach ($row as &$val) {
				if ($val == '帐号') {
					$val = '';
				}
			}

			//ignore empty row
			$username = $row['帐号'];
			if (empty($username) || $username == '帐号') {
				$this->utils->debug_log('ignore username', $username);
				continue;
			}

			$player['username'] = $row['帐号'];
			$player['password'] = random_string('alnum', 6); //random password
			$email = $row['email'];

			$extra = array('email' => $email,
				'createdOn' => $this->utils->getNowForMysql(),
				'lastLoginIp' => $row['最后登入IP'],
				'status' => ($row['状态'] == '启用' ? 0 : 1),
				// 'point' => 0,
				// 'approved_deposit_count' => $row->depositnum,
				// 'totalDepositAmount' => $row->deposit,
				// 'approvedWithdrawCount' => $row->withdnum,
				// 'approvedWithdrawAmount' => $row->withdrawals,
			);

			$details = array(
				'lastName' => $row['真实姓名'],
				'language' => $lang,
				'contactNumber' => $row['手机'],
				'imAccount' => $row['微信'],
				'imAccount2' => $row['QQ'],
				'qq' => $row['QQ'],
				'birthdate' => $row['生日'],
				// 'city' => $row->ipaddress,
				// 'temppass' => $player['password'],
				'registrationIP' => $row['最后登入IP'],
			);

			$this->startTrans();

			$affId = $this->sync_hxh_aff($row);

			$balance = $row['余额'];

			$extra['affiliateId'] = $affId;
			$levelId = isset($levelMap[$row['会员等级']]) ? $levelMap[$row['会员等级']] : 1;

			$player_id = $this->player_model->importPlayer($row['id'], $levelId,
				$player['username'], $player['password'], $balance, $extra, $details, $message);

			$this->utils->debug_log('insert player id', $player_id, $player['username']);

			if (!empty($player_id)) {

				//search bankname
				$bankName = $row['银行名称'];

				$bankTypeId = isset($bankMap[$bankName]) ? $bankMap[$bankName] : null;

				//add bank detail
				$this->db->insert('playerbankdetails', [
					'playerId' => $player_id,
					'province' => $row['省份'],
					'city' => $row['县市'],
					'bankAccountFullName' => $row['真实姓名'],
					'bankAccountNumber' => $row['银行帐户'],
					'isDefault' => 0,
					'isRemember' => 0,
					'dwBank' => 1,
					'status' => 0,
					'bankTypeId' => $bankTypeId,
					'createdOn' => $this->utils->getNowForMysql(),
					'updatedOn' => $this->utils->getNowForMysql(),
				]);

				//add tag
				if (isset($tagMap[$row['会员等级']])) {
					$tagId = $tagMap[$row['会员等级']];

					$this->utils->debug_log($player_id, 'to tagId', $tagId);

					$this->db->insert('playertag', [
						'playerId' => $player_id,
						'taggerId' => 1,
						'tagId' => $tagId,
						'createdOn' => $this->utils->getNowForMysql(),
						'updatedOn' => $this->utils->getNowForMysql(),
						'status' => 1,
					]);
				}

			}

			$success = $this->endTransWithSucc();

			if (!$success) {
				$msg = $this->utils->error_log('database error', $player['username'], $row['id'], $message);
				return $this->returnText($msg);
			}

			if (!$player_id) {
				$msg = $this->utils->error_log('import failed', $player['username'], $row['id'], $message);
				return $this->returnText($msg);
			}

			$cnt++;

			// if($player_id){
			//     $this->wallet_model->import_win007_players($player_id);
			//     //$wallet = $this->wallet_model->getMainWalletBy($player_id);
			//     //if(!$wallet){
			//     //    $this->wallet_model->updateMainWallet($player_id, 0);
			//     //}
			//     $this->utils->debug_log('id',$player_id,' name:', $player['username'] ,' password:',$player['password'],
			//      ' balance:',($wallet)?$wallet['totalBalanceAmount']:'0');
			// }

			if ($cnt > $max_count) {
				break;
			}

		}
		// } finally {
		// fclose($file);
		// }

		$msg = $this->utils->debug_log('import player', $cnt);
		$this->returnText($msg);

	}

	public function sync_hxh_aff($row) {

		$aff1Username = $row['大股东'];
		$aff2Username = $row['股东'];
		$aff3Username = $row['总代理'];
		$aff4Username = $row['代理'];

		$aff1Id = $this->affiliatemodel->getAffiliateIdByUsername($aff1Username);
		$aff2Id = $this->affiliatemodel->getAffiliateIdByUsername($aff2Username);
		$aff3Id = $this->affiliatemodel->getAffiliateIdByUsername($aff3Username);
		$aff4Id = $this->affiliatemodel->getAffiliateIdByUsername($aff4Username);

		$this->utils->debug_log('aff1Id', $aff1Id, 'aff1Username', $aff1Username,
			'aff2Id', $aff2Id, 'aff2Username', $aff2Username,
			'aff3Id', $aff3Id, 'aff3Username', $aff3Username,
			'aff4Id', $aff4Id, 'aff4Username', $aff4Username);

		$status = Affiliatemodel::OLD_STATUS_ACTIVE;
		$lastname = "";
		$trackingCode = '';
		$notes = 'import';
		$language = 'Chinese';

		if (empty($aff1Id)) {
			$username = $aff1Username;
			$externalId = $aff1Username;
			$parentId = 0;
			$firstname = $aff1Username;
			$createdOn = $this->utils->getNowForMysql();
			$password = random_string('alnum', 6);

			$aff1Id = $this->affiliatemodel->importAffiliate($externalId, $username, $password, $trackingCode, $createdOn,
				$firstname, $lastname, $status,
				array(
					'notes' => $notes,
					'language' => $language,
					'parentId' => $parentId,
				));

			$this->utils->debug_log('create aff', $aff1Username, 'aff1Id', $aff1Id);

		}

		if (empty($aff2Id)) {
			$username = $aff2Username;
			$externalId = $username;
			$parentId = $aff1Id;
			$firstname = $username;
			$createdOn = $this->utils->getNowForMysql();
			$password = random_string('alnum', 6);

			$aff2Id = $this->affiliatemodel->importAffiliate($externalId, $username, $password, $trackingCode, $createdOn,
				$firstname, $lastname, $status,
				array(
					'notes' => $notes,
					'language' => $language,
					'parentId' => $parentId,
				));
			$this->utils->debug_log('create aff', $aff2Username, 'aff2Id', $aff2Id);

		}

		if (empty($aff3Id)) {
			$username = $aff3Username;
			$externalId = $username;
			$parentId = $aff2Id;
			$firstname = $username;
			$createdOn = $this->utils->getNowForMysql();
			$password = random_string('alnum', 6);

			$aff3Id = $this->affiliatemodel->importAffiliate($externalId, $username, $password, $trackingCode, $createdOn,
				$firstname, $lastname, $status,
				array(
					'notes' => $notes,
					'language' => $language,
					'parentId' => $parentId,
				));

			$this->utils->debug_log('create aff', $aff3Username, 'aff3Id', $aff3Id);

		}

		if (empty($aff4Id)) {
			$username = $aff4Username;
			$externalId = $username;
			$parentId = $aff3Id;
			$firstname = $username;
			$createdOn = $this->utils->getNowForMysql();
			$password = random_string('alnum', 6);

			$aff4Id = $this->affiliatemodel->importAffiliate($externalId, $username, $password, $trackingCode, $createdOn,
				$firstname, $lastname, $status,
				array(
					'notes' => $notes,
					'language' => $language,
					'parentId' => $parentId,
				));
			$this->utils->debug_log('create aff', $aff4Username, 'aff4Id', $aff4Id);

		}

		return $aff4Id;

	}

	public function import_v8_csv() {

		set_time_limit(0);

		//import csv
		$ignoreHeader = true;
		$cnt = 0;
		$filename = '/home/vagrant/Code/import_data/v8/player.csv';

		$file = fopen($filename, "r");

		$this->utils->debug_log('start hxh', $filename);

		$this->load->model(['player_model', 'wallet_model']);
		if ($file !== false) {

			try {
				while (!feof($file)) {
					$tmpData = fgetcsv($file);
					if (empty($tmpData)) {
						continue;
					}

					if ($ignoreHeader && $cnt == 0) {
						$cnt++;
						continue;
					}

					$insert_sql = <<<EOD
INSERT INTO `tmp_import_hxh`
(`帐号`,`入会时间`,`大股东`,`股东`,`总代理`,`代理`,`余额`,`状态`,`会员等级`,`反水设定`,
`真实姓名`,`手机`,`性别`,`email`,`生日`,`微信`,`QQ`,`银行名称`,`省份`,`县市`,
`银行帐户`,`存款次数`,`存款金额`,`提款次数`,`提款金额`,`BBIN帐号`,`Saba帐号`,`XTD帐号`,`MG帐号`,`AG帐号`,
`HG帐号`,`MG2帐号`,`PT帐号`,`PT2帐号`,`GPI帐号`,`GNS帐号`,`最后登入IP`)
VALUES
(?,?,?,?,?,?,?,?,?,?,
?,?,?,?,?,?,?,?,?,?,
?,?,?,?,?,?,?,?,?,?,
?,?,?,?,?,?,?)

EOD;

					$this->player_model->runRawUpdateInsertSQL($insert_sql, $tmpData);

					$cnt++;

				}
			} finally {
				fclose($file);
			}
		} else {
			$this->utils->error_log('open failed');
		}

		$msg = $this->utils->debug_log('import_hxh_player_csv', $cnt);
		$this->returnText($msg);
	}

	# Imports csv data for v8
	# Input data files are:
	# /home/vagrant/Code/import_data/v8/users.csv
	# /home/vagrant/Code/import_data/v8/players.csv
	# /home/vagrant/Code/import_data/v8/userbanks.csv
	# All files are assumed to have a header row
	public function import_v8($maxCount = 10) {
		set_time_limit(0);

		$this->load->model(array('player_model', 'affiliatemodel', 'game_provider_auth'));

		$this->import_v8_players($maxCount);
		$this->import_v8_gameaccount($maxCount);
		$this->import_v8_userbanks($maxCount);
	}

	private function import_v8_players($maxCount) {
		# Import users
		$this->utils->debug_log("Start importing v8 players...");
		if ($maxCount) {
			$this->utils->debug_log("Only processing [$maxCount] records.");
		}

		$usersFile = fopen('/home/vagrant/Code/import_data/v8/players.csv', "r");

		if (!$usersFile) {
			$this->utils->error_log("Unable to open file players.csv for reading");
		}

		$totalCount = -1;
		$failCount = 0;

		$affMap = $this->affiliatemodel->getUsernameMap();
		$levelIdMap = [
			'1' => '1',
		];
		$levelId = 1;

		while ($usersFile && ($row = fgetcsv($usersFile)) !== false) {
			if ($totalCount == -1) {
				# skip header row
				$totalCount++;
				continue;
			}

			if ($maxCount && $totalCount >= $maxCount) {
				$this->utils->debug_log("Processed [$maxCount] records, stopping here.");
				break;
			}

			# Build up the user data
			$externalId = $row[0];
			$username = $row[0]; //$this->trimPrefix($row[1], 'V8');
			$apiPass = $row[4];
			$blocked = $row[5];
			// $levelId = $row[3]; # pending usage
			$password = $row[3];
			$balance = $row[6];
			$name = $row[7];
			$phone = $row[8];
			$email = $row[9];
			$qq = $row[10];
			$dob = date('Y-m-d', $row[11] + 0); # format: unix time
			$lastLoginTime = date('Y-m-d H:i:s', $row[13] + 0); # format: unix time
			$lastLoginIp = $row[14];
			$regTime = date('Y-m-d H:i:s', $row[15] + 0); # format: unix time
			$regIp = $row[16];
			$regDomain = $row[17];
			$initDeposit = $row[18]; # not used
			$initDepositTime = date('Y-m-d H:i:s', $row[19] + 0); # not used
			$aff = $row[20];
			if (array_key_exists($aff, $affMap)) {
				$affId = $affMap[$aff];
			}
			$wechat = $row[21];

			$this->utils->debug_log('uid', $externalId, 'username', $username, 'password', $password, 'balance', $balance,
				'api_pass', $apiPass, 'regTime', $regTime, 'levelId', $levelId, 'blocked', $blocked);

			$extra = array('email' => $email,
				'createdOn' => $regTime,
				'withdraw_password_md5' => $apiPass,
				'blocked' => $blocked,
			);
			if (isset($affId)) {
				$extra['affiliateId'] = $affId;
			}
			$details = array('firstName' => $name,
				'language' => 'Chinese',
				'birthdate' => $dob,
				'phone' => $phone,
				'contactNumber' => $phone,
				'imAccount' => $qq,
				'imAccountType' => 'QQ',
				'imAccount2' => $wechat,
				'imAccountType2' => 'WeChat',
				'qq' => $qq,
				'registrationWebsite' => 'http://' . $regDomain,
				'registrationIP' => $regIp,
			);

			$this->player_model->startTrans();
			$failMessage = '';
			$importPlayerId = $this->player_model->importPlayer($externalId, $levelId, $username, $password, $balance, $extra, $details, $failMessage);
			if (!$importPlayerId) {
				$failCount++;
				$this->utils->error_log("Import failed: [$failMessage]");
			}
			$this->player_model->endTrans();
			$totalCount++;
		}

		if ($usersFile) {
			fclose($usersFile);
		}

		$successCount = $totalCount - $failCount;
		$this->utils->debug_log("Import v8 users done, [$successCount] out of [$totalCount] succeed.");
	}

	private function import_v8_gameaccount($maxCount) {
		# Update platform usernames
		$this->utils->debug_log("Updating game platform usernames...");
		$platformMap = array(
			'PT' => 1,
			'AGIN' => 72,
			'MG' => 6,
			'NT' => 7,
			'BBIN' => 8,
			'LB' => 10,
			'ONE88' => 11,
			'GPI' => 13,
			'WIN9777' => 14,
			'INTEPLAY' => 19,
			'OPUS_API' => 20,
			'ONESGAME_API' => 21,
			'GSPT' => 22,
			'GSAG' => 23,
			'GAMEPLAY_API' => 24,
			'KG' => 26, # KENOGAME
			'ALLBET' => 29, # AB
			'BS' => 30,
			'IBC' => 31,
			'GD_API' => 32,
			'WFT' => 36,
			'HB' => 38,
			'IMPT' => 39,
			'GAMESOS' => 50,
			'EBET' => 53,
			'HG' => 67, # AGHG
			'SEVEN' => 70, # SEVEN77
			'SHABA' => 69, # AGSHABA
		);

		$playersFile = fopen('/home/vagrant/Code/import_data/v8/gameaccount.csv', "r");

		if (!$playersFile) {
			$this->utils->error_log("Unable to open file gameaccount.csv for reading");
		}

		$failCount = 0;
		$skipCount = 0;
		$totalCount = 0;

		while ($playersFile && ($row = fgetcsv($playersFile)) !== false) {
			if ($totalCount == -1) {
				# skip header row
				$totalCount++;
				continue;
			}

			if ($maxCount && $totalCount >= $maxCount) {
				$this->utils->debug_log("Processed [$maxCount] records, stopping here.");
				break;
			}

			$totalCount++;

			$username = $row[0]; //$this->trimPrefix($row[0], 'V8');
			$playerId = $this->player_model->getPlayerIdByUsername($username);
			if (empty($playerId)) {
				$this->utils->debug_log("Unable to find player ID from player user name [$username], skip this record");
				$skipCount++;
				continue;
			}

			$password = $this->player_model->getPasswordById($playerId);

			$platformId = $platformMap[$row[1]];
			$platformUsername = $row[2]; //$this->trimPrefix($row[2], 'V8');

			// function syncGameAccount($playerId, $gameUsername, $password, $gamePlatformId, $register, $source = self::SOURCE_BATCH)
			$updated = $this->game_provider_auth->syncGameAccount($playerId, $platformUsername,
				$password, $platformId, 1);

			if (!$updated) {
				$this->utils->debug_log("Not updated: playerId: [$playerId], platform: [$platformId], platformUsername: [$platformUsername]");
				$failCount++;
			}
		}

		$processedTotal = $totalCount - $skipCount;
		$successCount = $processedTotal - $failCount;
		$this->utils->debug_log("Import v8 players done, [$processedTotal] of [$totalCount] processed, [$successCount] succeed. failed: [$failCount]");
	}

	public function import_v8_userbanks($maxCount) {

		$this->load->model(array('player_model', 'affiliatemodel', 'game_provider_auth'));

		# Import userbanks
		$this->utils->debug_log("Start importing v8 user banks...");
		# Find unique banks from the csv and get this mapping
		$bankMap = [
			'ICBC' => 1,
			'ABC' => 4,
			'CMB' => 2,
			'BOC' => 6,
			'BOCO' => 5,
			'BCCB' => 33,
			'CCB' => 3,
			'CITIC' => 10,
			'CMBC' => 11,
			'PSBC' => 12,
			'CEB' => 20,
			'CIB' => 13,
			'PAB' => 15,
			'GDB' => 8,
			'HXB' => 14,
			'SPDB' => 24,
		];

		$userBanksFile = fopen('/home/vagrant/Code/import_data/v8/userbanks.csv', "r");

		if (!$userBanksFile) {
			$this->utils->error_log("Unable to open file userbanks.csv for reading");
		}

		$failCount = 0;
		$skipCount = 0;
		$totalCount = -1;

		while ($userBanksFile && ($row = fgetcsv($userBanksFile)) !== false) {
			if ($totalCount == -1) {
				# skip header row
				$totalCount++;
				continue;
			}

			if ($maxCount && $totalCount >= $maxCount) {
				$this->utils->debug_log("Processed [$maxCount] records, stopping here.");
				break;
			}

			$totalCount++;

			$username = $row[0]; //$this->trimPrefix($row[0], 'V8');
			$userRealname = $row[1]; # Not used
			$bankId = $bankMap[$row[2]];
			$bankName = $row[3];
			$cardNum = $row[4];
			$province = $row[5];
			$city = $row[6];
			$branch = $row[7];
			$bankRealname = $row[8];

			# Look up player ID using user name
			$playerId = $this->player_model->getPlayerIdByUsername($username);
			if (empty($playerId)) {
				#$this->utils->debug_log("Unable to find player ID from player user name [$username], skip this record");
				$skipCount++;
				continue;
			}

			if (empty($bankId) || empty($cardNum)) {
				$this->utils->error_log("Import failed: [$username] bank ID [$bankId] or bank card number [$cardNum] is empty.");
				$failCount++;
				continue;
			}

			$data = array(
				'playerId' => $playerId,
				'province' => $province,
				'city' => $city,
				'branch' => $branch,
				'bankAddress' => $branch,
				'bankAccountFullName' => $bankRealname,
				'bankAccountNumber' => $cardNum,
				'isDefault' => 1,
				'isRemember' => 0,
				'dwBank' => 1, //withdraw bank
				'status' => 0,
				'bankTypeId' => $bankId,
				'createdOn' => $this->utils->getNowForMysql(),
				'updatedOn' => $this->utils->getNowForMysql(),
			);

			$this->db->select('playerBankDetailsId')->from('playerbankdetails')
				->where('playerId', $playerId)
				->where('bankAccountNumber', $cardNum);
			$playerBankDetailsIdQuery = $this->db->get();
			if ($playerBankDetailsIdQuery && $playerBankDetailsIdQuery->num_rows() > 0) {
				$playerBankDetailsIdResult = $playerBankDetailsIdQuery->result_array();
				$playerBankDetailsId = $playerBankDetailsIdResult[0]['playerBankDetailsId'];
				$this->db->set($data)->where('playerBankDetailsId', $playerBankDetailsId)->update('playerbankdetails');
			} else {
				$this->db->set($data)->insert('playerbankdetails');
			}
		}

		$processedTotal = $totalCount - $skipCount;
		$successCount = $processedTotal - $failCount;
		$this->utils->debug_log("Import v8 user banks done, [$processedTotal] of [$totalCount] processed, [$successCount] succeed. failed: [$failCount]");
	}

	private function trimPrefix($input, $prefix) {
		if (strcasecmp(substr($input, 0, 2), $prefix) == 0) {
			$input = substr($input, 2);
		}
		return $input;
	}

	public function import_v8_aff_with_info($debug = 'true', $max_count = 10) {
		set_time_limit(0);

		$is_debug = $debug == 'true';

		//import csv
		$ignoreHeader = true;
		$cnt = 0;
		$filename = '/home/vagrant/Code/import_data/v8/aff_info.csv';

		$file = fopen($filename, "r");

		$this->utils->debug_log('start importing affiliate', $filename);

		$this->load->model(['player_model', 'wallet_model', 'affiliatemodel']);
		$cnt = 0;
		$failedCnt = 0;
		if ($file !== false) {

			try {
				// $first_char=['v','w','x','y','z','a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u'];
				// $second_char=['1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y','z'];
				// $first_index=0;
				// $second_index=0;
				while (!feof($file)) {
					$tmpData = fgetcsv($file);
					if (empty($tmpData)) {
						$this->utils->debug_log('ignore empty row');
						continue;
					}
					if ($max_count > 0 && $cnt >= $max_count) {
						$this->utils->error_log('stopped for debug');
						break;
					}

					// if($first_index>count($first_char)){
					//  $this->utils->error_log('wrong prefix last row',$tmpData);
					//  break;
					// }
					$this->utils->debug_log('process row', $tmpData);

					$affUsername = $tmpData[1];
					$affName = $tmpData[4];
					$affBalance = $tmpData[5];
					$affPhone = $tmpData[6];
					$affEmail = $tmpData[7];
					$affQQ = $tmpData[8];
					$affRegTime = $tmpData[10];
					$affRegIP = $tmpData[11];
					$affDomain = $tmpData[14];
					$affShare = 100 * $tmpData[15];

					$affStatus = $tmpData[3];
					$affPassword = random_string('numeric', 8);
					// $trackingCode from domain
					$domainList = explode('.', $affDomain);
					$trackingCode = $domainList[0];
					if (empty($trackingCode)) {
						$trackingCode = $affUsername;
					}
					$createdOn = $this->utils->getNowForMysql();
					$lastname = '';
					$status = $affStatus == '1' ? Affiliatemodel::OLD_STATUS_INACTIVE : Affiliatemodel::OLD_STATUS_ACTIVE;
					//create prefix
					// $prefix=$first_char[$first_index].$second_char[$second_index];
					// $extra=['affdomain'=> $affDomain, 'prefix_of_player'=>$prefix];
					$extra = ['mobile' => $affPhone, 'email' => $affEmail, 'im1' => $affQQ, 'wallet_balance' => $affBalance,
						'imType1' => 'QQ', 'ip_address' => $affRegIP, 'affdomain' => $affDomain];

					$context_info = [
						'affUsername' => $affUsername,
						'createdOn' => $createdOn, 'affName' => $affName,
						'lastname' => $lastname, 'trackingCode' => $trackingCode,
						'status' => $status, 'extra' => $extra, 'share' => $affShare];

					if (!$is_debug) {
						//import to affiliates
						$affId = $this->affiliatemodel->importAffiliate($affUsername, $affUsername,
							$affPassword, $trackingCode, $createdOn, $affName, $lastname, $status, $extra, $affShare);

						$success = !!$affId;

						// if($affBalance>0){
						//  $this->affiliatemodel->incMainWallet($affId, $affBalance);
						// }

						if (!$success) {
							$this->utils->error_log('import affiliate failed', $context_info);
							$failedCnt++;
						} else {
							$cnt++;
						}
					} else {
						$this->utils->debug_log('only debug ' . $affBalance, $context_info);
					}

					// $second_index++;
					// if($second_index>=count($second_char)){
					//  //move next
					//  $first_index++;
					//  $second_index=0;
					// }

				}
			} finally {
				fclose($file);
			}

		} else {
			$this->utils->error_log('open failed');
		}

		$msg = $this->utils->debug_log('import affiliate', $cnt, 'failed', $failedCnt);
		$this->returnText($msg);
	}

	public function import_v8_aff($debug = 'true', $max_count = 10) {
		set_time_limit(0);

		$is_debug = $debug == 'true';

		//import csv
		$ignoreHeader = true;
		$cnt = 0;
		$filename = '/home/vagrant/Code/import_data/v8/affiliates.csv';

		$file = fopen($filename, "r");

		$this->utils->debug_log('start importing affiliate', $filename);

		$this->load->model(['player_model', 'wallet_model', 'affiliatemodel']);
		$cnt = 0;
		$failedCnt = 0;
		if ($file !== false) {

			try {
				$first_char = ['v', 'w', 'x', 'y', 'z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u'];
				$second_char = ['1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
				$first_index = 0;
				$second_index = 0;
				while (!feof($file)) {
					$tmpData = fgetcsv($file);
					if (empty($tmpData)) {
						$this->utils->debug_log('ignore empty row');
						continue;
					}
					if ($max_count > 0 && $cnt >= $max_count) {
						$this->utils->error_log('stopped for debug');
						break;
					}

					if ($first_index > count($first_char)) {
						$this->utils->error_log('wrong prefix last row', $tmpData);
						break;
					}
					$this->utils->debug_log('process row', $tmpData);

					$affUsername = $tmpData[0];
					$affName = $tmpData[1];
					$affDomain = $tmpData[2];
					$affPassword = random_string('numeric', 8);
					// $trackingCode from domain
					$domainList = explode('.', $affDomain);
					$trackingCode = $domainList[0];
					if (empty($trackingCode)) {
						$trackingCode = $affUsername;
					}
					$createdOn = $this->utils->getNowForMysql();
					$lastname = '';
					$status = Affiliatemodel::OLD_STATUS_ACTIVE;
					//create prefix
					$prefix = $first_char[$first_index] . $second_char[$second_index];
					$extra = ['affdomain' => $affDomain, 'prefix_of_player' => $prefix];

					$context_info = ['affUsername' => $affUsername,
						'affUsername' => $affUsername, 'affPassword' => $affPassword, 'trackingCode' => $trackingCode,
						'createdOn' => $createdOn, 'affName' => $affName, 'lastname' => $lastname, 'status' => $status,
						'extra' => $extra];

					if (!$is_debug) {
						//import to affiliates
						$success = $this->affiliatemodel->importAffiliate($affUsername, $affUsername, $affPassword, $trackingCode,
							$createdOn, $affName, $lastname, $status, $extra);

						if (!$success) {
							$this->utils->error_log('import affiliate failed', $context_info);
							$failedCnt++;
						} else {
							$cnt++;
						}
					} else {
						$this->utils->debug_log('only debug', $context_info);
					}

					$second_index++;
					if ($second_index >= count($second_char)) {
						//move next
						$first_index++;
						$second_index = 0;
					}

				}
			} finally {
				fclose($file);
			}

		} else {
			$this->utils->error_log('open failed');
		}

		$msg = $this->utils->debug_log('import affiliate', $cnt, 'failed', $failedCnt);
		$this->returnText($msg);
	}

	public function export_affiliate_with_password() {
		$this->load->model(['affiliatemodel']);
		$this->load->library(array('salt'));
		$rows = $this->affiliatemodel->getAllAffiliates();
		$cnt = 0;
		$failed = [];
		if (!empty($rows)) {

			$fp = fopen('/home/vagrant/Code/aff.csv', 'w');

			foreach ($rows as $row) {
				$username = $row['username'];
				$mobile = $row['mobile'];
				$password = $row['password'];
				if (!empty($password)) {
					$password = $this->salt->decrypt($password, $this->getDeskeyOG());
				} else {
					$failed[] = $username;
				}
				$fields = [$username, $mobile, $password];
				fputcsv($fp, $fields);
				$cnt++;
			}

			fclose($fp);

		}
		$this->utils->debug_log('export ', $cnt, 'failed', $failed);

	}

	public function export_game_password($gamePlatformId) {

		$this->load->model(['game_provider_auth']);

		$rows = $this->game_provider_auth->exportPassword($gamePlatformId);

		$fp = fopen('/home/vagrant/Code/game_account.csv', 'w');

		$cnt = 0;
		if (!empty($rows)) {
			foreach ($rows as $row) {
				$fields = [$row['login_name'], $row['password']];
				fputcsv($fp, $fields);
				$cnt++;
			}
		}

		fclose($fp);

		$this->utils->debug_log('exporting', $cnt);

	}

	public function create_ebet_account() {
		$apiId = EBET_API;
		$api = $this->utils->loadExternalSystemLibObject($apiId);

		if (!empty($api)) {
			$this->load->model(['player_model', 'game_provider_auth']);
			$players = $this->player_model->getAllUnlimitPlayers();
			foreach ($players as $player) {
				if (!$this->game_provider_auth->isRegisterd($player->playerId, $apiId)) {
					$password = $this->utils->decodePassword($player->password);
					$api->createPlayer($player->username, $player->playerId, $password);
					$this->utils->debug_log('add to ebet', $player->username, $player->playerId);
				}
			}
		}

	}

	protected function import_affiliate($aff_csv_file, $config, $callback_process){
		// $success=false;
		// $affMap=[];
		// $file = fopen($aff_csv_file, "r");
		$this->utils->debug_log('start importing affiliate', $aff_csv_file, $config, empty($callback_process));
		$this->load->model(['player_model', 'wallet_model', 'affiliatemodel']);
		$cnt = 0;
		$message='';
		// $failedCnt = 0;

		$result_info=[
			'success'=>false,
			'cnt'=>0,
			'failedCnt'=>0,
			'successCnt'=>0,
			'failedAffiliate'=>[], // username=>failed message
			'affMap'=>[],
		];

		$controller=$this;
		$result_info['success']=$this->utils->loopCSV($aff_csv_file, @$config['ignore_first_row'], $cnt, $message,
			function($cnt, $tmpData, &$stop_flag=false) use($controller, $callback_process, $config, &$result_info){

			$debug_info=$config['debug_info'];

			if (@$debug_info['max_count'] && $cnt >= $debug_info['max_count']) {
				$this->utils->debug_log('stopped for debug');
				$stop_flag=true;
				return false;
			}

			if(@$debug_info['print_each_row']){
				$controller->utils->debug_log('processing', $tmpData);
			}

			$affInfo=$callback_process($tmpData);

			if (!@$debug_info['dry_run']) {

				if(empty($affInfo['external_id']) || empty($affInfo['username']) ){
					$affId=null;
				}else{
					//import to affiliates
					$affId = $controller->affiliatemodel->importAffiliate($affInfo['external_id'],
						$affInfo['username'], $affInfo['password'], $affInfo['tracking_code'],
						$affInfo['created_on'], $affInfo['first_name'], $affInfo['last_name'],
						$affInfo['status'], $affInfo['extra']);
				}

				$success=!empty($affId);
				if (!$success) {
					$controller->utils->error_log('import affiliate failed', $affInfo);
					$result_info['failedCnt']++;
					$result_info['failedAffiliate'][]=$affInfo['username'];
				} else {
					$result_info['affMap'][$affInfo['username']]=$affId;
					$result_info['cnt']++;
				}
			} else {
				$controller->utils->debug_log('only debug', $affInfo);
				$result_info['cnt']++;
			}

			return true;
		});

		// $this->utils->debug_log('import affiliate', $result_info);

		return $result_info;
	}

	protected function import_player($player_csv_file, $affMap, $config, $callback_process){
		$success=false;
		$file = fopen($player_csv_file, "r");
		$this->utils->debug_log('start importing player', $player_csv_file);
		$this->load->model(['player_model', 'wallet_model', 'affiliatemodel']);
		$cnt = 0;
		$failedCnt = 0;
		$controller=$this;

		$result_info=[
			'success'=>false,
			'cnt'=>0,
			'failedCnt'=>0,
			'successCnt'=>0,
			'failedPlayer'=>[], // username=>failed message
			'playerMap'=>[],
		];

		$result_info['success']=$this->utils->loopCSV($player_csv_file, @$config['ignore_first_row'], $cnt, $message,
			function($cnt, $tmpData, &$stop_flag=false) use($controller, $callback_process, $config, $affMap, &$result_info){

			$debug_info=$config['debug_info'];

			if (@$debug_info['max_count'] && $cnt >= $debug_info['max_count']) {
				$this->utils->debug_log('stopped for debug');
				$stop_flag=true;
				return false;
			}

			if(@$debug_info['print_each_row']){
				$this->utils->debug_log('processing', $tmpData);
			}

			$playerInfo=$callback_process($tmpData);

			$message=null;

			//process aff id
			$affId=isset($playerInfo['extra']['affiliateId']) ? $playerInfo['extra']['affiliateId'] : null;
			if(!empty($playerInfo['affiliate_username'])){
				if(isset($affMap[$playerInfo['affiliate_username']])){
					$affId=$affMap[$playerInfo['affiliate_username']];
				}
			}

			$this->utils->debug_log('search aff', $affId, $playerInfo['affiliate_username']);
			$playerInfo['extra']['affiliateId']=$affId;

			if (!@$debug_info['dry_run']) {

				if(empty($playerInfo['external_id']) || empty($playerInfo['username']) ){

					$playerId=null;

				}else{

					//import to player
					//extra is for player table, details is for playerdetails table
					$playerId = $this->player_model->importPlayer($playerInfo['external_id'],
						$playerInfo['level_id'], $playerInfo['username'],
						$playerInfo['password'], $playerInfo['balance'],
						$playerInfo['extra'], $playerInfo['details'], $message);

				}
				$success=!empty($playerId);

				if (!$success) {
					$this->utils->error_log('import player failed', $message, $playerInfo);
					$result_info['failedCnt']++;
					$result_info['failedPlayer'][]=$playerInfo['username'];
				} else {
					$result_info['cnt']++;
					// $result_info['playerMap'][$playerInfo['username']]=$playerId;
				}
			} else {
				$this->utils->debug_log('only debug', $playerInfo);
				$result_info['cnt']++;
			}

			return true;
		});

		// $this->utils->debug_log('import player', $result_info);

		return $result_info;
	}

	public function import_player_bank($bank_csv_file, $bankNameMap, $config, $callback_process){
		$this->load->model(['player_model', 'playerbankdetails', 'banktype', 'wallet_model', 'affiliatemodel']);
		$playerMap=$this->player_model->getFirstNamePlayerMap();
		$bankTypeMap=$this->banktype->getBankTypeMap();

		//playerbankdetails
		$success=false;
		$file = fopen($bank_csv_file, "r");
		$this->utils->debug_log('start importing player bank', $bank_csv_file);
		$cnt = 0;
		$failedCnt = 0;
		$controller=$this;

		$result_info=[
			'success'=>false,
			'cnt'=>0,
			'failedCnt'=>0,
			'successCnt'=>0,
			'failedPlayerBank'=>[], // username=>failed message
			'playerBankMap'=>[],
		];

		$result_info['success']=$this->utils->loopCSV($bank_csv_file, @$config['ignore_first_row'], $cnt, $message,
			function($cnt, $tmpData, &$stop_flag=false)
			use($controller, $playerMap, $bankNameMap, $bankTypeMap, $callback_process, $config, &$result_info){

			$debug_info=$config['debug_info'];

			if (@$debug_info['max_count'] && $cnt >= $debug_info['max_count']) {
				$this->utils->debug_log('stopped for debug');
				$stop_flag=true;
				return false;
			}

			if(@$debug_info['print_each_row']){
				$this->utils->debug_log('processing', $tmpData);
			}

			$playerBankInfo=$callback_process($tmpData);

			if(empty($playerBankInfo)){
				//ignore
				$this->utils->debug_log('ignore player bank because empty');
				return true;
			}

			$extra=$playerBankInfo['extra'];

			$playerId=null;
			$realname=null;
			if(isset($extra['playerId']) && !empty($extra['playerId'])){
				$playerId=$extra['playerId'];
				$realname=$playerInfo['first_name'];
			}else{
				//convert
				$playerInfo=isset($playerMap[$extra['first_name']]) ? $playerMap[$extra['first_name']] : null;
				if(!empty($playerInfo)){
					$playerId=$playerInfo['playerId'];
					$realname=$playerInfo['firstName'];
				}
			}

			$bank_type= @$extra['bank_type']=='deposit' ? Playerbankdetails::DEPOSIT_BANK : Playerbankdetails::WITHDRAWAL_BANK;
			$bank_name= @$extra['bank_name'];
			$bank_code=isset($bankNameMap[$bank_name]) ? $bankNameMap[$bank_name] : null;
			//serach bank type id
			$bankTypeId=null;
			if(!empty($bank_code)){
				$bankTypeId=@$bankTypeMap[$bank_code];
			}

			$bankAccountFullName= $playerBankInfo['bankAccountFullName'];
			if(empty($bankAccountFullName)){
				//load from player map
				$bankAccountFullName=$realname;
			}

			$message=null;

			if (!@$debug_info['dry_run']) {

				if(empty($playerId) || empty($bankTypeId) || empty($playerBankInfo['external_id'])
						|| empty($playerBankInfo['bankAccountNumber']) ){

					$this->utils->error_log('ignore player bank because empty external_id or bankTypeId or player id', $playerBankInfo, $bankTypeId, $playerId);
					$playerBankId=null;

				}else{

					if(!isset($playerBankInfo['status'])){
						$playerBankInfo['status']='0';
					}

					//import to playerbankdetails
					$playerBankId = $this->playerbankdetails->importPlayerBank($playerBankInfo['external_id'],
						$playerId, $bankTypeId, $bank_type, $bankAccountFullName, $playerBankInfo['bankAccountNumber'],
						$playerBankInfo['province'], $playerBankInfo['city'],
						$playerBankInfo['branch'], $playerBankInfo['bankAddress'], $playerBankInfo['createdOn'],
						$playerBankInfo['status'],
						$message);

				}
				$success=!empty($playerBankId);

				if (!$success) {
					$this->utils->error_log('import player bank failed', $message, $playerBankInfo);
					$result_info['failedCnt']++;
					$result_info['failedPlayerBank'][]=$playerBankInfo['external_id'].$playerBankInfo['bankAccountNumber'];
				} else {
					$result_info['cnt']++;
					// $result_info['playerBankMap'][$playerBankInfo['external_id']]=$playerBankId;
				}
			} else {
				$this->utils->debug_log('only debug', $playerBankInfo, 'playerId', $playerId, 'bankTypeId', $bankTypeId, 'bank_type', $bank_type, 'bankAccountFullName', $bankAccountFullName);
				$result_info['cnt']++;
			}

			return true;
		});

		// $this->utils->debug_log('import player', $result_info);

		return $result_info;
	}

	public function import_ag_client($player_csv_file, $aff_csv_file, $print_each_row="true", $dry_run="true", $max_count="10", $ignore_first_row="true"){

		$debug_info=[
			'ignore_first_row'=>$ignore_first_row=='true',
			'print_each_row'=>$print_each_row=='true',
			'max_count'=>$max_count, 'dry_run'=>$dry_run=='true'];

		//always load csv file from /home/vagrant/Code

		$player_csv_file='/home/vagrant/Code/'.$player_csv_file;
		$aff_csv_file='/home/vagrant/Code/'.$aff_csv_file;

		$this->utils->debug_log('player_csv_file', $player_csv_file, 'aff_csv_file', $aff_csv_file, 'debug_info', $debug_info);
		$this->load->model(['player_model', 'wallet_model', 'affiliatemodel']);

		$controller=$this;
		// $balanace_to_main_wallet=true;
		$config=['debug_info'=>$debug_info, 'ignore_first_row'=>true];
		$result_info=$this->import_affiliate($aff_csv_file, $config, function($csv_row)
				use($controller){

			$loginIp=$controller->utils->onlyGetLastIP($csv_row[7]);
			// $dateTimeFmt='Ymd,H:i:s';

			$aff=[
				'external_id'=> $csv_row[0],
				'username'=> $csv_row[0],
				'password'=> '',
				'tracking_code'=> $csv_row[0],
				'created_on'=>$csv_row[5], //$controller->utils->convertDateTimeFormat($csv_row[5], $dateTimeFmt),
				'first_name'=>$csv_row[3],
				'last_name'=>'',
				'status'=>Affiliatemodel::OLD_STATUS_ACTIVE,
				'extra'=>['email'=>$csv_row[1], 'phone'=>$csv_row[2],
					'im1'=>$csv_row[4],
					'lastLogin'=>!empty($csv_row[6]) ? $csv_row[6] : '',
						// $controller->utils->convertDateTimeFormat($csv_row[6], $dateTimeFmt) : '',
					'lastLoginIp'=> $loginIp,
				],
			];
			return $aff;
		});

		$this->utils->debug_log('affiliate result',$result_info);
		$affMap=$this->affiliatemodel->getAffiliateMap(); //$result_info['affMap'];

		$defaultLevelId=1;
		//=====player====================
		$result_info=$this->import_player($player_csv_file, $affMap, $config, function($csv_row)
				use ($defaultLevelId){

			// $dateTimeFmt='Ymd,H:i:s';
			// $dateTimeFmt='Ymd,H:i:s';
			$col_login_name=0;
			$col_email=2;
			$col_phone=3;
			$col_realname=4;
			$col_im=5;
			$col_withdraw_password=6;
			$col_created_date=7;
			$col_last_login_date=8;
			$col_register_ip=9;
			$col_last_login_ip=10;
			$col_aff_username=11;
			$col_credit=12;
			$col_rebat_amount=13;

			//only use last ip
			$regIp=$this->utils->onlyGetLastIP($csv_row[$col_register_ip]);
			$loginIp=$this->utils->onlyGetLastIP($col_last_login_ip);

			$player=[
				'external_id'=> $csv_row[$col_login_name],
				'username'=> trim($csv_row[$col_login_name]),
				'password'=> '',
				'balance'=> $csv_row[$col_credit],
				'level_id'=> $defaultLevelId,
				'affiliate_username'=>trim($csv_row[$col_aff_username]),
				'extra'=>[
					'email'=> trim($csv_row[$col_email]),
					'createdOn'=>$csv_row[$col_created_date], // $this->utils->convertDateTimeFormat($csv_row[7], $dateTimeFmt),
					'lastLoginTime'=>$csv_row[$col_last_login_date], // $this->utils->convertDateTimeFormat($csv_row[8], $dateTimeFmt),
					'lastLoginIp'=>$loginIp,
					'total_cashback'=>$csv_row[$col_rebat_amount],
				],
				'details'=>[
					'contactNumber'=>trim($csv_row[$col_phone]),
					'imAccount'=>trim($csv_row[$col_im]),
					'firstName'=>trim($csv_row[$col_realname]),
					'registrationIP'=>$regIp,
				],
			];
			return $player;
		});

		$this->utils->debug_log('player result',$result_info);

	}

	public function import_ag_bank($withdraw_bank_csv_file, $print_each_row="true", $dry_run="true", $max_count="10", $ignore_first_row="true"){
		$debug_info=[
			'ignore_first_row'=>$ignore_first_row=='true',
			'print_each_row'=>$print_each_row=='true',
			'max_count'=>$max_count, 'dry_run'=>$dry_run=='true'];

		//name to code
		$bankNameMap=[
			"中国农业银行"=>"AGB","中国建设银行"=>"CCB","工商银行"=>"ICBC","ICBC"=>"ICBC","PSBC"=>"PSBC","中国工商银行"=>"ICBC",
			"黎都农商银行"=>"LIDUBANK","RCU"=>"RCU","OTHER"=>"OTHER","CCB"=>"CCB","中信银行"=>"CITIC",
			"中国邮政储蓄银行"=>"PSBC","ABC"=>"AGB","CIB"=>"CIB","中国民生银行"=>"CMBC", "民生银行"=>"CMBC", "BC"=>"BOC",
			"中国银行"=>"BOC","BCM"=>"BCOMM","农村信用社"=>"RCU","交通银行"=>"BCOMM", "交通银行, 交通银行"=>"BCOMM", "上海浦东发展银行"=>"SPDB",
			"中国光大银行"=>"CEB", "光大银行"=>"CEB","招商银行"=>"CMB","CEB"=>"CEB","SPDB"=>"SPDB","成都农商银行"=>"CDRCB",
			"CMBC"=>"CMBC","兴业银行"=>"CIB","CITIC"=>"CITIC","华夏银行"=>"HXB", "HXB"=>"HXB", "CMB"=>"CMB",
			"桂林银行"=>"GLBANK","广发银行"=>"GDB", "CGB"=>"GDB", "平安银行"=>"PAB","PAB"=>"PAB",

			"BOB"=>"BOB", "北京银行"=>"BOB", "渤海银行"=>"CBHB", "东莞农村商业银行"=>"DRCBANK", "东莞银行"=>"DGBANK",
			"东莞银行支行"=>"DGBANK", "福建省农村信用社"=>"FJNX", "广州农村商业银行"=>"GRCB", "广州农商银行"=>"GRCB",
			"广州银行"=>"GZCB", "贵阳银行"=>"GYCCB", "贵州银行"=>"BGZC", "河北省农村信用社"=>"HEBNX", "恒丰银行"=>"HFBANK",
			"华融湘江银行"=>"HRXJBANK", "建设银行"=>"CCB", "江苏银行"=>"JSBC", "九江银行"=>"JJCCB", "农业"=>"AGB", "农业银行"=>"AGB",
			"兰州银行"=>"LZBANK", "柳州银行"=>"LZCCB", "南充市商业银行"=>"SCTFBANK", "南充银行"=>"SCTFBANK",

			"农村商业银行"=>"NCSYYH", "浦发银行"=>"SPDB", "上海浦发银行"=>"SPDB", "其他"=> "OTHER", "青岛银行"=> "QDCCB",
			"深圳发展银行"=>"PAB", "顺德农商银行"=>"SDEBANK", "网商银行"=>"WCYH", "微信"=>"WECHAT", "武汉农村商业银行"=>"WHRCB",
			"邮政储蓄"=>"PSBC", "邮政"=>"PSBC", "邮政银行"=>"PSBC", "浙江网商银行"=>"WCYH", "支付宝"=> "ALIPAY", "中国农商银行"=>"ZGNCBANK",
			"农商银行"=>"ZGNCBANK", "重庆银行"=>"CQCBANK", "上海银行"=> "BOSH", "中国农业银行, 农业银行"=>"AGB", "中国邮政储蓄所"=>"PSBC",
			"中国农业银行, 中国农业银行"=>"AGB", "中国邮政"=>"PSBC", "重庆农村商业银行"=>"CQRCB", "重庆市农村商业银行"=>"CQRCB",

			"安徽合肥肥东撮镇农村信用社"=>"AHRCU", "RCU"=>"RCU", "安徽农村信用社"=>"AHRCU", "安徽省农村食用社"=>"AHRCU",
			"包商银行"=>"BSB", "成都"=>"CDRCB", "成都银行"=>"BOCD", "大连银行"=>"BANKOFDL", "德阳银行"=>"DYYH",
			"广东南粤银行"=>"GDNYB", "广东农信"=>"GDRCU", "广西农村信用社"=>"GXRCU", "哈尔滨银行"=>"HRBCB",
			"黑龙江省哈尔滨银行"=>"HRBCB", "徽商银行"=>"HSBANK", "江苏农村信用社"=>"JSRCU", "江西银行"=>"JXBANK",
			"晋商银行"=>"JSHBANK", "昆山农商银行"=>"KSRCB", "南京银行"=>"NJCB", "宁波银行"=>"NBCB", "农村信用社湖北省鄂州市大弯支行"=>"RCU",
			"山东农村信用社"=>"SDNXS", "上海农商银行"=>"SRCB", "深圳农村商业银行"=>"SZRCB",
			"深圳商业银行"=>"SZRCB", "四川农村信用社"=>"SCRCU", "云南农村信用社"=>"YNRCC", "云南省农村信用社"=>"YNRCC",
			"长安银行"=>"CCABCHINA", "长沙银行"=>"CSCB", "郑州银行"=>"ZZBANK", "珠海华润银行"=>"CRBANK",
			];

		//always load csv file from /home/vagrant/Code
		$withdraw_bank_csv_file='/home/vagrant/Code/'.$withdraw_bank_csv_file;

		$this->utils->debug_log('withdraw_bank_csv_file', $withdraw_bank_csv_file, 'debug_info', $debug_info);
		$this->load->model(['player_model', 'wallet_model', 'affiliatemodel']);

		$controller=$this;
		// $balanace_to_main_wallet=true;
		$config=['debug_info'=>$debug_info, 'ignore_first_row'=>true];
		$result_info=$this->import_player_bank($withdraw_bank_csv_file, $bankNameMap, $config, function($csv_row)
				use($controller){

			if(empty($csv_row[1])){
				return null;
			}

			$bank=[
				'external_id'=> $csv_row[0],
				'bankAccountNumber'=> $csv_row[1],
				'bankAccountFullName'=>null,
				'province'=> $csv_row[5],
				'city'=> $csv_row[6],
				'branch'=> $csv_row[8],
				'bankAddress'=> $csv_row[5].$csv_row[6],
				'createdOn'=> $csv_row[4],
				'extra'=>[
					// 'player_username'=>$csv_row[0],
					'first_name'=>$csv_row[0],
					'bank_name'=>trim($csv_row[7]," ,'\t\n\r\0\x0B\""),
					'bank_type'=>'withdraw',
				]
			];
			return $bank;
		});


		$this->utils->debug_log('import bank result',$result_info);

	}

	public function print_bank_code(){
		$bankNameMap=[
			"中国农业银行"=>"AGB","中国建设银行"=>"CCB","工商银行"=>"ICBC","ICBC"=>"ICBC","PSBC"=>"PSBC","中国工商银行"=>"ICBC",
			"黎都农商银行"=>"LIDUBANK","RCU"=>"RCU","OTHER"=>"OTHER","CCB"=>"CCB","中信银行"=>"CITIC",
			"中国邮政储蓄银行"=>"PSBC","ABC"=>"AGB","CIB"=>"CIB","中国民生银行"=>"CMBC", "民生银行"=>"CMBC", "BC"=>"BOC",
			"中国银行"=>"BOC","BCM"=>"BCOMM","农村信用社"=>"RCU","交通银行"=>"BCOMM", "交通银行, 交通银行"=>"BCOMM", "上海浦东发展银行"=>"SPDB",
			"中国光大银行"=>"CEB", "光大银行"=>"CEB","招商银行"=>"CMB","CEB"=>"CEB","SPDB"=>"SPDB","成都农商银行"=>"CDRCB",
			"CMBC"=>"CMBC","兴业银行"=>"CIB","CITIC"=>"CITIC","华夏银行"=>"HXB", "HXB"=>"HXB", "CMB"=>"CMB",
			"桂林银行"=>"GLBANK","广发银行"=>"GDB", "CGB"=>"GDB", "平安银行"=>"PAB","PAB"=>"PAB",

			"BOB"=>"BOB", "北京银行"=>"BOB", "渤海银行"=>"CBHB", "东莞农村商业银行"=>"DRCBANK", "东莞银行"=>"DGBANK",
			"东莞银行支行"=>"DGBANK", "福建省农村信用社"=>"FJNX", "广州农村商业银行"=>"GRCB", "广州农商银行"=>"GRCB",
			"广州银行"=>"GZCB", "贵阳银行"=>"GYCCB", "贵州银行"=>"BGZC", "河北省农村信用社"=>"HEBNX", "恒丰银行"=>"HFBANK",
			"华融湘江银行"=>"HRXJBANK", "建设银行"=>"CCB", "江苏银行"=>"JSBC", "九江银行"=>"JJCCB", "农业"=>"AGB", "农业银行"=>"AGB",
			"兰州银行"=>"LZBANK", "柳州银行"=>"LZCCB", "南充市商业银行"=>"SCTFBANK", "南充银行"=>"SCTFBANK",

			"农村商业银行"=>"NCSYYH", "浦发银行"=>"SPDB", "上海浦发银行"=>"SPDB", "其他"=> "OTHER", "青岛银行"=> "QDCCB",
			"深圳发展银行"=>"PAB", "顺德农商银行"=>"SDEBANK", "网商银行"=>"WCYH", "微信"=>"WECHAT", "武汉农村商业银行"=>"WHRCB",
			"邮政储蓄"=>"PSBC", "邮政"=>"PSBC", "邮政银行"=>"PSBC", "浙江网商银行"=>"WCYH", "支付宝"=> "ALIPAY", "中国农商银行"=>"ZGNCBANK",
			"农商银行"=>"ZGNCBANK", "重庆银行"=>"CQCBANK", "上海银行"=> "BOSH", "中国农业银行, 农业银行"=>"AGB", "中国邮政储蓄所"=>"PSBC",
			"中国农业银行, 中国农业银行"=>"AGB", "中国邮政"=>"PSBC", "重庆农村商业银行"=>"CQRCB", "重庆市农村商业银行"=>"CQRCB",

			"安徽合肥肥东撮镇农村信用社"=>"AHRCU", "RCU"=>"RCU", "安徽农村信用社"=>"AHRCU", "安徽省农村食用社"=>"AHRCU",
			"包商银行"=>"BSB", "成都"=>"CDRCB", "成都银行"=>"BOCD", "大连银行"=>"BANKOFDL", "德阳银行"=>"DYYH",
			"广东南粤银行"=>"GDNYB", "广东农信"=>"GDRCU", "广西农村信用社"=>"GXRCU", "哈尔滨银行"=>"HRBCB",
			"黑龙江省哈尔滨银行"=>"HRBCB", "徽商银行"=>"HSBANK", "江苏农村信用社"=>"JSRCU", "江西银行"=>"JXBANK",
			"晋商银行"=>"JSHBANK", "昆山农商银行"=>"KSRCB", "南京银行"=>"NJCB", "宁波银行"=>"NBCB", "农村信用社湖北省鄂州市大弯支行"=>"RCU",
			"山东农村信用社"=>"SDNXS", "上海农商银行"=>"SRCB", "深圳农村商业银行"=>"SZRCB",
			"深圳商业银行"=>"SZRCB", "四川农村信用社"=>"SCRCU", "云南农村信用社"=>"YNRCC", "云南省农村信用社"=>"YNRCC",
			"长安银行"=>"CCABCHINA", "长沙银行"=>"CSCB", "郑州银行"=>"ZZBANK", "珠海华润银行"=>"CRBANK",
			];

		$arr=array_unique( array_values($bankNameMap));
		sort($arr);
		$this->utils->debug_log(implode('","',  $arr));

	}

	// public function import_rebate($rebate_csv_file, $print_each_row="true", $dry_run="true", $max_count="10", $ignore_first_row="true"){

	// 	$this->load->model(['player_model', 'transactions']);

	// 	$debug_info=[
	// 		'ignore_first_row'=>$ignore_first_row=='true',
	// 		'print_each_row'=>$print_each_row=='true',
	// 		'max_count'=>$max_count, 'dry_run'=>$dry_run=='true'];
	// 	$config=['debug_info'=>$debug_info, 'ignore_first_row'=>$ignore_first_row=='true'];

	// 	$result_info=[
	// 		'success'=>false,
	// 		'cnt'=>0,
	// 		'failedCnt'=>0,
	// 		'successCnt'=>0,
	// 		'failed_username'=>[], // username=>failed message
	// 		'username'=>[],
	// 	];

	// 	$rebate_csv_file='/home/vagrant/Code/'.$rebate_csv_file;

	// 	$controller=$this;
	// 	$cnt=0;
	// 	$message=null;
	// 	$result_info['success']=$this->utils->loopCSV($rebate_csv_file, @$config['ignore_first_row'], $cnt, $message,
	// 		function($cnt, $tmpData, &$stop_flag=false)
	// 		use($controller, $config, &$result_info){

	// 		$debug_info=$config['debug_info'];

	// 		if (@$debug_info['max_count'] && $cnt >= $debug_info['max_count']) {
	// 			$this->utils->debug_log('stopped for debug');
	// 			$stop_flag=true;
	// 			return false;
	// 		}

	// 		if(@$debug_info['print_each_row']){
	// 			$this->utils->debug_log('processing', $tmpData);
	// 		}

	// 		$next=true;

	// 		$username=$tmpData[0];
	// 		$rebate_amount=round(doubleval($tmpData[1]), 2);

	// 		if(empty($username) || $rebate_amount<0){

	// 			$result_info['failedCnt']++;
	// 			$result_info['failed_username'][]=$username;
	// 			return $next;
	// 		}

	// 		$playerId=$this->player_model->getPlayerIdByUsername($username);

	// 		$action_name='Import rebate';
	// 		$wallet_name='';
	// 		$adminUserId=1;
	// 		$adjustment_type=Transactions::AUTO_ADD_CASHBACK_TO_BALANCE;
	// 		$note = sprintf('%s <b>%s</b> balance to <b>%s</b>',
	// 					$action_name, number_format($rebate_amount, 2), $username);

	// 		if (!@$debug_info['dry_run']) {

	// 			$success=$this->transactions->createAdjustmentTransaction($adjustment_type, $adminUserId, $playerId,
	// 				$rebate_amount, null, $note);

	// 		}else{

	// 			$this->utils->debug_log('dry run...');
	// 			$success=true;
	// 		}

	// 		$result_info['cnt']++;
	// 		if($success){

	// 			$result_info['successCnt']++;
	// 			$result_info['username'][]=$username;
	// 		}else{
	// 			$result_info['failedCnt']++;
	// 			$result_info['failed_username'][]=$username;
	// 		}

	// 		return $next;

	// 	});

	// 	$this->utils->debug_log('rebate result',$result_info);

	// }

	public function import_vip($vip_csv_file, $print_each_row="true", $dry_run="true", $max_count="10", $ignore_first_row="true"){

		$this->load->model(['player_model', 'group_level']);

		$debug_info=[
			'ignore_first_row'=>$ignore_first_row=='true',
			'print_each_row'=>$print_each_row=='true',
			'max_count'=>$max_count, 'dry_run'=>$dry_run=='true'];
		$config=['debug_info'=>$debug_info, 'ignore_first_row'=>$ignore_first_row=='true'];

		$result_info=[
			'success'=>false,
			'cnt'=>0,
			'failedCnt'=>0,
			'successCnt'=>0,
			'failed_username'=>[], // username=>failed message
			'username'=>[],
		];

		$vip_csv_file='/home/vagrant/Code/'.$vip_csv_file;

		$vipLevelMap=[
			'VIP1'=>1,
			'VIP2'=>21,
			'VIP3'=>22,
			'VIP4'=>23,
			'VIP5'=>24,
			'VIP6'=>25,
		];

		$controller=$this;
		$cnt=0;
		$message=null;
		$result_info['success']=$this->utils->loopCSV($vip_csv_file, @$config['ignore_first_row'], $cnt, $message,
			function($cnt, $tmpData, &$stop_flag=false)
			use($controller, $config, $vipLevelMap, &$result_info){

			$debug_info=$config['debug_info'];

			if (@$debug_info['max_count'] && $cnt >= $debug_info['max_count']) {
				$this->utils->debug_log('stopped for debug');
				$stop_flag=true;
				return false;
			}

			if(@$debug_info['print_each_row']){
				$this->utils->debug_log('processing', $tmpData);
			}

			$next=true;

			$username=$tmpData[0];
			$vip_level_id=@$vipLevelMap[$tmpData[1]];

			$playerId=$this->player_model->getPlayerIdByUsername($username);

			if(empty($username) || empty($playerId) || empty($vip_level_id)){

				$result_info['failedCnt']++;
				$result_info['failed_username'][]=$username;
				return $next;
			}

			$this->utils->debug_log('adjust player level',$username, $playerId, $vip_level_id);

			if (!@$debug_info['dry_run']) {

				$this->group_level->adjustPlayerLevel($playerId, $vip_level_id);
				$success=true;

			}else{

				$this->utils->debug_log('dry run...');
				$success=true;
			}

			$result_info['cnt']++;
			if($success){

				$result_info['successCnt']++;
				$result_info['username'][]=$username;
			}else{
				$result_info['failedCnt']++;
				$result_info['failed_username'][]=$username;
			}

			return $next;

		});

		$this->utils->debug_log('import vip result',$result_info);

	}

	public function import_single_vip_level($vip_csv_file, $singleVipLevelId, $print_each_row="true", $dry_run="true", $max_count="10", $ignore_first_row="true"){

		$this->load->model(['player_model', 'group_level']);

		$debug_info=[
			'ignore_first_row'=>$ignore_first_row=='true',
			'print_each_row'=>$print_each_row=='true',
			'max_count'=>$max_count, 'dry_run'=>$dry_run=='true'];
		$config=['debug_info'=>$debug_info, 'ignore_first_row'=>$ignore_first_row=='true'];

		$result_info=[
			'success'=>false,
			'cnt'=>0,
			'failedCnt'=>0,
			'successCnt'=>0,
			'failed_username'=>[], // username=>failed message
			'username'=>[],
		];

		$vip_csv_file='/home/vagrant/Code/'.$vip_csv_file;

		$controller=$this;
		$cnt=0;
		$message=null;
		$result_info['success']=$this->utils->loopCSV($vip_csv_file, @$config['ignore_first_row'], $cnt, $message,
			function($cnt, $tmpData, &$stop_flag=false)
			use($controller, $config, $singleVipLevelId, &$result_info){

			$debug_info=$config['debug_info'];

			if (@$debug_info['max_count'] && $cnt >= $debug_info['max_count']) {
				$this->utils->debug_log('stopped for debug');
				$stop_flag=true;
				return false;
			}

			if(@$debug_info['print_each_row']){
				$this->utils->debug_log('processing', $tmpData);
			}

			$next=true;

			$username=$tmpData[0];
			$vip_level_id=$singleVipLevelId; //@$vipLevelMap[$tmpData[1]];

			$playerId=$this->player_model->getPlayerIdByUsername($username);

			if(empty($username) || empty($playerId) || empty($vip_level_id)){

				$result_info['failedCnt']++;
				$result_info['failed_username'][]=$username;
				return $next;
			}

			$this->utils->debug_log('adjust player level',$username, $playerId, $vip_level_id);

			if (!@$debug_info['dry_run']) {

				$this->group_level->adjustPlayerLevel($playerId, $vip_level_id);
				$success=true;

			}else{

				$this->utils->debug_log('dry run...');
				$success=true;
			}

			$result_info['cnt']++;
			if($success){

				$result_info['successCnt']++;
				$result_info['username'][]=$username;
			}else{
				$result_info['failedCnt']++;
				$result_info['failed_username'][]=$username;
			}

			return $next;

		});

		$this->utils->debug_log('rebate result',$result_info);

	}

	// public function import_csv_bonus($csv_file, $promoCmsSettingId, $dry_run='true', $status='approved', $reason='import from csv'){

	//  // $csv_file
	//  $csv_file='/home/vagrant/Code/'.$csv_file;
	//  $success=true;
	//  $dry_run=$dry_run=='true';

	//  $this->load->model(['player_model', 'promorules', 'player_promo', 'transactions', 'withdraw_condition']);

	//  $message=lang('Add Bonus Failed');
	//  $ignore_first_row = true;
	//  $cnt = 0;
	//  $success_amount = 0;
	//  $success_usernames = [];
	//  $failed_usernames = [];
	//  $controller=$this;

	//  if($status=='request'){
	//      $status=Player_promo::TRANS_STATUS_REQUEST;
	//  }else{
	//      $status=Player_promo::TRANS_STATUS_APPROVED;
	//  }
	//  $show_in_front_end=null;

	//  $this->utils->loopCSV($csv_file, $ignore_first_row, $cnt, $message,
	//      function($cnt, $tmpData, $stop_flag) use($controller, &$message, &$success_amount, &$success_usernames, &$failed_usernames,
	//          $status, $reason, $dry_run, $show_in_front_end, $promoCmsSettingId, $promoRuleId, $release_date, $promo_category, $promorule){
	//      //process one row
	//      $success=false;

	//      $this->utils->debug_log('==============post_manually_batch_bonus->tmpData', $tmpData);

	//      // git issue #1420
	//      // sanitizing data items: trim first, then force data type
	//      for ($i = 0; $i <= 4; ++$i) {
	//          $tmpData[$i] = trim($tmpData[$i]);
	//          $tmpData[$i] = ($i == 0) ? strval($tmpData[$i]) : round(doubleval($tmpData[$i]), 2);
	//      }

	//      $player_name    = $tmpData[0];
	//      $amount         = $tmpData[1];
	//      $betTimes       = $tmpData[2];
	//      $condition      = $tmpData[3];
	//      $deposit_amt_condition = $tmpData[4];

	//      $player_id=$this->player_model->getPlayerIdByUsername($player_name);

	//      if(empty($player_id)){
	//          $this->utils->debug_log('==============player not exist', $player_name);
	//          $failed_usernames[$player_name] = sprintf(lang('gen.error.not_exist'), $player_name);
	//          return false;
	//      }

	//      $promoRuleId  = $controller->promorules->getPromorulesIdByPromoCmsId($promoCmsSettingId);
	//      $release_date=$this->utils->getNowForMysql();
	//      $promo_category = null;
	//      if (!empty($promoRuleId)) {
	//          $promorule = $controller->promorules->getPromoRuleRow($promoRuleId);
	//          $promo_category = $promorule['promoCategory'];
	//      }

	//      $success=$this->lockAndTransForPlayerBalance($player_id, function()
	//      use($controller, $player_id, $player_name, $amount, $betTimes, $condition, $deposit_amt_condition, &$message,
	//              $status, $reason, $dry_run, $show_in_front_end, $promoCmsSettingId, $promoRuleId, $release_date, $promo_category, $promorule) {

	//          $current_timestamp = $controller->utils->getNowForMysql();

	//          //get logged user
	//          $adminUserId=1;
	//          $adminUsername='admin';

	//          // $promoCmsSettingId = $controller->input->post('promo_cms_id');
	//          // $promoRuleId  = $controller->promorules->getPromorulesIdByPromoCmsId($promoCmsSettingId);
	//          // $release_date=$controller->input->post('release_date');

	//          //set promo category

	//          $action_name = 'Add';
	//          $adjustment_type = Transactions::ADD_BONUS;

	//          // $status = $controller->input->post('status');
	//          // $reason=$controller->input->post('reason');
	//          // $show_in_front_end=$controller->input->post('show_in_front_end');

	//          $note = 'add bonus '.number_format($amount, 2).' to '.$player_name.' by '.$adminUsername.', with deposit condition of ' . $condition;

	//          if($this->player_model->isDisabledPromotion($player_id)){
	//              $message = lang('Add Bonus Failed') . '. ' . lang('Disable Promotion');

	//              return false;
	//          }

	//          #if want pending, don't create transaction, only create player promo
	//          if($status == Player_promo::TRANS_STATUS_REQUEST ){
	//              // request promo
	//              $success=!!$controller->player_promo->requestPromoToPlayer($player_id, $promoRuleId, $amount, $promoCmsSettingId, $adminUserId, $deposit_amt_condition,
	//                  $condition , Player_promo::TRANS_STATUS_REQUEST, $betTimes, $reason );
	//              if(!$success){
	//                  $message=lang('Request Promotion Failed');
	//              }else{
	//                  $message=lang('Request Promotion Successfully');
	//              }
	//          }else{

	//              $this->utils->debug_log('==============post_manually_batch_bonus->before_transaction', 'test');

	//              $transaction = $this->transactions->createAdjustmentTransaction($adjustment_type,
	//                  $adminUserId, $player_id, $amount, null, $note, null,
	//                  $promo_category, $show_in_front_end, $reason, $promoRuleId);

	//              $this->utils->debug_log('==============post_manually_batch_bonus->transaction', $transaction);

	//              $success=!!$transaction;
	//              if($success){
	//                  $adjustData=array(
	//                      'playerId' => $transaction['to_id'],
	//                      'adjustmentType' => $transaction['transaction_type'],
	//                      'walletType' => 0, # 0 - MAIN WALLET
	//                      'amountChanged' => $transaction['amount'],
	//                      'oldBalance' => $transaction['before_balance'],
	//                      'newBalance' => $transaction['after_balance'],
	//                      'reason' => $reason,
	//                      'adjustedOn' => $transaction['created_at'],
	//                      'adjustedBy' => $transaction['from_id'],
	//                      // 'show_flag' => $show_in_front_end == '1',
	//                  );

	//                  $this->db->insert('balanceadjustmenthistory', $adjustData);

	//                  //move to withdraw_condition
	//                  $promorule=$this->promorules->getPromoruleById($promoRuleId);
	//                  $bonusTransId=$transaction['id'];
	//                  $controller->withdraw_condition->createWithdrawConditionForManual($player_id, $bonusTransId,
	//                      $condition, $deposit_amt_condition, $amount, $betTimes, $promorule,$reason);

	//                  //save to player promo
	//                  $playerBonusAmount = $amount;
	//                  $extra_info=[];
	//                  $player_promo_id = $controller->player_promo->approvePromoToPlayer($player_id, $promoRuleId, $playerBonusAmount,
	//                      $promoCmsSettingId, $adminUserId, null, $condition, $extra_info, $deposit_amt_condition, $betTimes,$reason );
	//                  //update player promo id of transaction
	//                  $controller->transactions->updatePlayerPromoId($transaction['id'], $player_promo_id, $promo_category);
	//                  // }
	//                  $success=true;
	//                  $message=lang('Add Bonus Successfully');
	//              }else{
	//                  $message=lang('Add Bonus Failed');
	//              }
	//          }

	//          return $success;

	//      });

	//      if($success){
	//          $success_amount += $amount;
	//          // $success_usernames[$player_name] = lang('Transfer Success');
	//          $success_usernames[$player_name] = $message;
	//      }else{
	//          // $failed_usernames[$player_name] = lang('Transfer Failed');
	//          $failed_usernames[$player_name] = $message;
	//      }

	//      return $success;
	//  });

	// }

	public function import_lebo_client($player_csv_file, $aff_csv_file, $print_each_row="true", $dry_run="true", $max_count="10", $ignore_first_row="true"){

		$debug_info=[
			'ignore_first_row'=>$ignore_first_row=='true',
			'print_each_row'=>$print_each_row=='true',
			'max_count'=>$max_count, 'dry_run'=>$dry_run=='true'];

		//always load csv file from /home/vagrant/Code

		$player_csv_file='/home/vagrant/Code/'.$player_csv_file;
		$aff_csv_file='/home/vagrant/Code/'.$aff_csv_file;

		$this->utils->debug_log('player_csv_file', $player_csv_file, 'aff_csv_file', $aff_csv_file, 'debug_info', $debug_info);
		$this->load->model(['player_model', 'wallet_model', 'affiliatemodel']);

		$controller=$this;
		// $balanace_to_main_wallet=true;
		$config=['debug_info'=>$debug_info, 'ignore_first_row'=>true];
		$result_info=$this->import_affiliate($aff_csv_file, $config, function($csv_row)
				use($controller){
			// $dateTimeFmt='Ymd,H:i:s';

			$username=$csv_row[0];
			$tracking_code=$csv_row[3];
			$real_name=$csv_row[1];
			$registration_date=$csv_row[4];
			$loginIp=$controller->utils->onlyGetLastIP($csv_row[5]);

			$aff=[
				'external_id'=> $username,
				'username'=> $username,
				'password'=> $username,
				'tracking_code'=> $tracking_code,
				'created_on'=>$registration_date,
				'first_name'=>$real_name,
				'last_name'=>'',
				'status'=>Affiliatemodel::OLD_STATUS_ACTIVE,
				'extra'=>[
					'lastLoginIp'=> $loginIp,
				],
			];
			return $aff;
		});

		$this->utils->debug_log('affiliate result',$result_info);
		$affMap=$this->affiliatemodel->getAffiliateMap(); //$result_info['affMap'];

		$defaultLevelId=1;
		$vipMap=[
			'小新手【琪琪】'=>$defaultLevelId,
			'新手上路【暖暖】'=>$defaultLevelId,
			'黑名单'=>$defaultLevelId,
			'测试层级'=>$defaultLevelId,
			'特殊2-东北高返'=>$defaultLevelId,
			'老用户【小薇】'=>$defaultLevelId,
			'特殊-占6成'=>$defaultLevelId,
			'V3会员'=>36,
			'V2会员'=>35,
			'V1会员'=>34,
		];

		//=====player====================
		$result_info=$this->import_player($player_csv_file, $affMap, $config, function($csv_row)
				use ($defaultLevelId, $vipMap){

			// $dateTimeFmt='Ymd,H:i:s';
			// $dateTimeFmt='Ymd,H:i:s';
			$col_login_name=1;
			$col_email=6;
			$col_phone=5;
			$col_realname=2;
			$col_im=7;
			$col_withdraw_password=6;
			$col_created_date=8;
			// $col_last_login_date=9;
			// $col_register_ip=9;
			$col_last_login_ip=9;
			// $col_aff_username=11;
			// $col_credit=null;
			// $col_rebat_amount=13;
			$col_vip=4;

			$vip_name=trim($csv_row[$col_vip]);
			$vip_level_id=isset($vipMap[$vip_name]) ? $vipMap[$vip_name] : $defaultLevelId;
			//only use last ip
			// $regIp=$this->utils->onlyGetLastIP($csv_row[$col_register_ip]);
			$loginIp=$this->utils->onlyGetLastIP($col_last_login_ip);

			$username=trim($csv_row[$col_login_name]);
			$password=$username;
			$balance=0;
			$aff_username='';
			$player=[
				'external_id'=> $username,
				'username'=> $username,
				'password'=> $password,
				'balance'=> $balance,
				'level_id'=> $vip_level_id,
				'affiliate_username'=>$aff_username, // trim($csv_row[$col_aff_username]),
				'extra'=>[
					'email'=> trim($csv_row[$col_email]),
					'createdOn'=>$csv_row[$col_created_date], // $this->utils->convertDateTimeFormat($csv_row[7], $dateTimeFmt),
					// 'lastLoginTime'=>$csv_row[$col_last_login_date], // $this->utils->convertDateTimeFormat($csv_row[8], $dateTimeFmt),
					'lastLoginIp'=>$loginIp,
					// 'total_cashback'=>$csv_row[$col_rebat_amount],
				],
				'details'=>[
					'contactNumber'=>trim($csv_row[$col_phone]),
					'imAccount'=>trim($csv_row[$col_im]),
					'firstName'=>trim($csv_row[$col_realname]),
					// 'registrationIP'=>$regIp,
				],
			];
			return $player;
		});

		$this->utils->debug_log('player result',$result_info);

	}

	public function import_player_aff_map($aff_player_csv_file, $print_each_row="true", $dry_run="true", $max_count="10", $ignore_first_row="true"){

		$debug_info=[
			'ignore_first_row'=>$ignore_first_row=='true',
			'print_each_row'=>$print_each_row=='true',
			'max_count'=>$max_count, 'dry_run'=>$dry_run=='true'];

		//always load csv file from /home/vagrant/Code

		$aff_player_csv_file='/home/vagrant/Code/'.$aff_player_csv_file;

		$this->utils->debug_log('aff_player_csv_file', $aff_player_csv_file, 'debug_info', $debug_info);
		$this->load->model(['player_model', 'wallet_model', 'affiliatemodel']);

		$affMap=$this->affiliatemodel->getAffiliateMap(); //$result_info['affMap'];
		$controller=$this;
		$config=['debug_info'=>$debug_info, 'ignore_first_row'=>true];
		$result_info=[
			'success'=>false,
			'cnt'=>0,
			'failedCnt'=>0,
			'successCnt'=>0,
			'failed_username'=>[], // username=>failed message
			// 'username'=>[],
		];

		$result_info['success']=$this->utils->loopCSV($aff_player_csv_file, @$config['ignore_first_row'], $cnt, $message,
			function($cnt, $tmpData, &$stop_flag=false) use($controller, $config, $affMap, &$result_info){

			$debug_info=$config['debug_info'];

			if (@$debug_info['max_count'] && $cnt >= $debug_info['max_count']) {
				$this->utils->debug_log('stopped for debug');
				$stop_flag=true;
				return false;
			}

			if(@$debug_info['print_each_row']){
				$this->utils->debug_log('processing', $tmpData);
			}

			// $playerInfo=$callback_process($tmpData);

			$message=null;

			$playerUsername=trim($tmpData[0]);
			$affUsername=trim($tmpData[2]);

			$this->utils->debug_log('playerUsername', $playerUsername, 'affUsername', $affUsername);

			//process aff id
			// $affId=isset($playerInfo['extra']['affiliateId']) ? $playerInfo['extra']['affiliateId'] : null;
			$affId;
			if(!empty($affUsername)){
				// if(isset($affMap[$playerInfo['affiliate_username']])){
					$affId=isset($affMap[$affUsername]) ? $affMap[$affUsername] : null;
				// }
			}

			$this->utils->debug_log('search aff', $affId, $affUsername);

			if (!@$debug_info['dry_run']) {

				$success=false;

				if(!empty($affId)){
					$success=$this->player_model->importAffiliateIdToUsername($playerUsername, $affId)>=1;
				}

				if (!$success) {
					$this->utils->error_log('import player failed', $message);
					$result_info['failedCnt']++;
					$result_info['failed_username'][]=$playerUsername;
				} else {
					$result_info['cnt']++;
					// $result_info['username']=$playerUsername;
				}
			} else {
				$this->utils->debug_log('only debug');
				$result_info['cnt']++;
			}

			return true;
		});

		$this->utils->debug_log($result_info);

	}

	protected function import_balance($balance_csv_file, $config, $callback_process){
		$success=false;
		$file = fopen($balance_csv_file, "r");
		$this->utils->debug_log('start importing balance', $balance_csv_file);
		$this->load->model(['player_model', 'wallet_model', 'transactions']);
		$cnt = 0;
		$failedCnt = 0;
		$controller=$this;

		$result_info=[
			'success'=>false,
			'cnt'=>0,
			'failedCnt'=>0,
			'successCnt'=>0,
			'failedPlayer'=>[], // username=>failed message
			'playerMap'=>[],
		];

		$result_info['success']=$this->utils->loopCSV($balance_csv_file, @$config['ignore_first_row'], $cnt, $message,
			function($cnt, $tmpData, &$stop_flag=false) use($controller, $callback_process, $config, &$result_info){

			$debug_info=$config['debug_info'];

			if (@$debug_info['max_count'] && $cnt >= $debug_info['max_count']) {
				$this->utils->debug_log('stopped for debug');
				$stop_flag=true;
				return false;
			}

			if(@$debug_info['print_each_row']){
				$this->utils->debug_log('processing', $tmpData);
			}

			$balanceInfo=$callback_process($tmpData);

			$message=null;
			$success=false;

			if (!@$debug_info['dry_run']) {

				if(empty($balanceInfo['playerId'])){

					$success=false;

				}else{

					if(!$this->transactions->existsDeposit($balanceInfo['playerId'])){
						//import balance to player
						$adjust_time=$this->utils->formatDateTimeForMysql(new DateTime('-1 year'));
						//extra is for player table, details is for playerdetails table
						$success = $this->wallet_model->importBalanceAsDeposit($balanceInfo['playerId'],
							$balanceInfo['amount'], $balanceInfo['withdraw_condition_times'], $adjust_time,
							$message);
					}else{
						$this->utils->debug_log('ignore player', $balanceInfo['playerId']);
					}
				}

				if (!$success) {
					$this->utils->error_log('import balance failed', $message, $balanceInfo);
					$result_info['failedCnt']++;
					$result_info['failedPlayer'][]=$balanceInfo['playerId'];

				} else {
					$result_info['cnt']++;
					// $result_info['playerMap'][$playerInfo['username']]=$playerId;
				}
			} else {
				$this->utils->debug_log('only debug', $tmpData, $balanceInfo);
				$result_info['cnt']++;
			}

			return true;
		});

		// $this->utils->debug_log('import player', $result_info);

		return $result_info;
	}

	public function import_xc_balance($balance_csv_file, $print_each_row="true", $dry_run="true", $max_count="10", $ignore_first_row="true"){

		$debug_info=[
			'ignore_first_row'=>$ignore_first_row=='true',
			'print_each_row'=>$print_each_row=='true',
			'max_count'=>$max_count, 'dry_run'=>$dry_run=='true'];

		//always load csv file from /home/vagrant/Code
		$balance_csv_file='/home/vagrant/Code/'.$balance_csv_file;

		$this->utils->debug_log('balance_csv_file', $balance_csv_file, 'debug_info', $debug_info);
		$this->load->model(['player_model', 'wallet_model', 'affiliatemodel']);

		$externalIdMap=$this->player_model->getExternalIdMap();
		$withdraw_condition_times=null;

		$config=['debug_info'=>$debug_info, 'ignore_first_row'=>true];
		$result_info=$this->import_balance($balance_csv_file, $config, function($csv_row)
				use ($externalIdMap, $withdraw_condition_times){

			$playerId=$externalIdMap[$csv_row[0]];
			$balance=$csv_row[6];

			$balanceInfo=[
				'playerId'=>$playerId,
				'amount'=>$balance,
				'withdraw_condition_times'=>$withdraw_condition_times,
			];

			return $balanceInfo;
		});

		$this->utils->debug_log('balance result', $result_info);

	}

	public function import_dup_promo($promo_csv_file, $promo_category, $print_each_row="true", $dry_run="true", $max_count="10", $ignore_first_row="true"){

		$this->load->model(['player_model', 'transactions']);

		$debug_info=[
			'ignore_first_row'=>$ignore_first_row=='true',
			'print_each_row'=>$print_each_row=='true',
			'max_count'=>$max_count, 'dry_run'=>$dry_run=='true'];
		$config=['debug_info'=>$debug_info, 'ignore_first_row'=>$ignore_first_row=='true'];

		$result_info=[
			'success'=>false,
			'cnt'=>0,
			'failedCnt'=>0,
			'successCnt'=>0,
			'failed_username'=>[], // username=>failed message
			'username'=>[],
		];

		$promo_csv_file='/home/vagrant/Code/'.$promo_csv_file;

		$controller=$this;
		$cnt=0;
		$message=null;
		$result_info['success']=$this->utils->loopCSV($promo_csv_file, @$config['ignore_first_row'], $cnt, $message,
			function($cnt, $tmpData, &$stop_flag=false)
			use($controller, $config, &$result_info){

			$debug_info=$config['debug_info'];

			if (@$debug_info['max_count'] && $cnt >= $debug_info['max_count']) {
				$this->utils->debug_log('stopped for debug');
				$stop_flag=true;
				return false;
			}

			if(@$debug_info['print_each_row']){
				$this->utils->debug_log('processing', $tmpData);
			}

			$next=true;
			//0=username, 1=amount
			$username=$tmpData[0];
			$minus_amount=round(doubleval($tmpData[1]), 2);

			if(empty($username) || $minus_amount<=0){

				$result_info['failedCnt']++;
				$result_info['failed_username'][]=$username;
				return $next;
			}

			$playerId=$this->player_model->getPlayerIdByUsername($username);

			if(!empty($playerId)){
				if (!@$debug_info['dry_run']) {

					$success=$controller->lockAndTransForPlayerBalance($playerId, function()
							use($minus_amount, $playerId, $controller, $username, $promo_category){

						$action_name='Minus promo';
						$wallet_name='';
						$adminUserId=1;
						$adjustment_type=Transactions::SUBTRACT_BONUS;
						$note = sprintf('%s <b>%s</b> balance to <b>%s</b>',
									$action_name, number_format($minus_amount, 2), $username);

						$success=$controller->transactions->createAdjustmentTransaction($adjustment_type, $adminUserId, $playerId,
							$minus_amount, null, $note, null,
							$promo_category, null, null, null, null);

						return $success;
					});


				}else{

					$this->utils->debug_log('dry run...');
					$success=true;
				}
			}else{

				$success=false;

			}

			$result_info['cnt']++;
			if($success){

				$result_info['successCnt']++;
				// $result_info['username'][]=$username;
			}else{
				$result_info['failedCnt']++;
				$result_info['failed_username'][]=['username'=>$username, 'amount'=>$minus_amount];
			}

			return $next;

		});

		$this->utils->debug_log('minus promo result',$result_info);

	}

	/**
	 * everyone in $source_vip_group_id  group will be updated to $target_vip_level_id
	 *
	 * @param  int $source_vip_group_id
	 * @param  int $target_vip_level_id
	 * @param  string $dry_run
	 * @param  string $max_count
	 */
	public function batch_update_single_vip_level($source_vip_group_id, $source_vip_level_id, $target_vip_level_id, $dry_run="true", $max_count="10"){

		$this->load->model(['player_model', 'group_level']);

		$debug_info=[
			'max_count'=>$max_count, 'dry_run'=>$dry_run=='true'];
		$config=['debug_info'=>$debug_info];

		$result_info=[
			'debug_info'=>$debug_info,
			'success'=>false,
			'cnt'=>0,
			'message'=>null,
			'failedCnt'=>0,
			'successCnt'=>0,
			'failed_username'=>[], // username=>failed message
			'username'=>[],
		];

		$backup_file='/home/vagrant/Code/backup_batch_update_single_vip_level-'.date('Y-m-d-H-i-s').'.json';

		// $vip_csv_file='/home/vagrant/Code/'.$vip_csv_file;

		$controller=$this;

		$this->dbtransOnly(function() use($controller, $backup_file, $config,
			$source_vip_group_id, $source_vip_level_id, $target_vip_level_id, &$result_info){

			$success=true;
			$updated_username=[];
			//get player list
			$playerList=null;
			if(!empty($source_vip_level_id) && $source_vip_level_id!='_null'){
				$playerList=$this->player_model->getPlayerListByVipLevelId($source_vip_level_id);
			}else{
				//try group id
				$playerList=$this->player_model->getPlayerListByVipGroupId($source_vip_group_id, [$target_vip_level_id]);
			}

			$this->utils->debug_log('getPlayerListByVipGroupId count', count($playerList));

			if(!empty($playerList)){

				$result_info['message']='query total: '.count($playerList);

				foreach ($playerList as $playerInfo) {

					$debug_info=$config['debug_info'];
					if (@$debug_info['max_count'] && $result_info['cnt'] >= $debug_info['max_count']) {
						$this->utils->debug_log('stopped for debug');
						$result_info['message']='stopped for debug';
						break;
					}

					$username=$playerInfo['username'];

					$rlt=true;
					if (!@$debug_info['dry_run']) {
						$rlt=$this->group_level->adjustPlayerLevel($playerInfo['playerId'], $target_vip_level_id);
						$this->utils->debug_log('update '.$username.' to '.$target_vip_level_id);
					}else{
						$this->utils->debug_log('dry run...   '.$username.' to '.$target_vip_level_id);
					}

					$result_info['cnt']++;
					if($rlt){

						$result_info['successCnt']++;
						$updated_username[]=$username;
						// $result_info['username'][]=$username;
					}else{
						$result_info['failedCnt']++;
						$result_info['failed_username'][]=$username;
					}

				}
			}

			if(!empty($updated_username)){
				file_put_contents($backup_file, json_encode($updated_username, JSON_PRETTY_PRINT));
				$this->utils->debug_log('write to backup file', $backup_file);
			}

			return $success;

		});

		$this->utils->debug_log('batch_update_single_vip_level result',$result_info);

	}

	public function batch_block_player($player_csv_file, $print_each_row="true", $dry_run="true", $max_count="10", $ignore_first_row="true"){

		$this->load->model(['player_model', 'group_level']);

		$debug_info=[
			'ignore_first_row'=>$ignore_first_row=='true',
			'print_each_row'=>$print_each_row=='true',
			'max_count'=>$max_count, 'dry_run'=>$dry_run=='true'];
		$config=['debug_info'=>$debug_info, 'ignore_first_row'=>$ignore_first_row=='true'];

		$result_info=[
			'success'=>false,
			'cnt'=>0,
			'failedCnt'=>0,
			'successCnt'=>0,
			'failed_username'=>[], // username=>failed message
			'username'=>[],
		];

		$player_csv_file='/home/vagrant/Code/'.$player_csv_file;

		$controller=$this;
		$cnt=0;
		$message=null;
		$result_info['success']=$this->utils->loopCSV($player_csv_file, @$config['ignore_first_row'], $cnt, $message,
			function($cnt, $tmpData, &$stop_flag=false)
			use($controller, $config, &$result_info){

			$debug_info=$config['debug_info'];

			if (@$debug_info['max_count'] && $cnt >= $debug_info['max_count']) {
				$this->utils->debug_log('stopped for debug');
				$stop_flag=true;
				return false;
			}

			if(@$debug_info['print_each_row']){
				$this->utils->debug_log('processing', $tmpData);
			}

			$next=true;

			$username=$tmpData[0];

			$playerId=$this->player_model->getPlayerIdByUsername($username);

			if(empty($username) || empty($playerId)){

				$result_info['failedCnt']++;
				$result_info['failed_username'][]=$username;
				return $next;
			}

			$this->utils->debug_log('block player',$username, $playerId);

			if (!@$debug_info['dry_run']) {

				$success=$this->player_model->blockPlayerWithGame($playerId);

			}else{

				$this->utils->debug_log('dry run...');
				$success=true;
			}

			$result_info['cnt']++;
			if($success){

				$result_info['successCnt']++;
				$result_info['username'][]=$username;
			}else{
				$result_info['failedCnt']++;
				$result_info['failed_username'][]=$username;
			}

			return $next;

		});

		$this->utils->debug_log('batch_block_player result',$result_info);

	}

	public function import_ole777_players($player_csv_file){

		$this->utils->debug_log('start importing player', $player_csv_file);

		$country = unserialize(COUNTRY_ISO2);
		$country_map = [];

		foreach ($country as $key => $value) {
			$country_map[$value] = $key;
		}

		$this->load->model(array('affiliatemodel', 'player_model'));
		$filename = '/home/vagrant/Code/'.$player_csv_file;

		if(!file_exists($filename)){
			$this->utils->error_log("File not exist!");
			return;
		}

		$file = fopen($filename , "r");
		$header_keys =null;
		$count = 0;
		$failCount = 0;
		$totalCount = 0;

		while (($csv_row = fgetcsv($file)) !== FALSE){

			$count++;
			//get the title to be used as field keys
			if($count == 1){
				$header_keys = $csv_row;
			}else{
				$row = array_combine($header_keys, $csv_row);
				$externalId = null;
				$levelId = 1;
				$username = $row['user_code'];
				$password = empty($row['password']) ? '' : $row['password'] ;
				$balance =  $row['available_balance'];

				$createdOn= $row['create_date1'] == 'NULL' ? NULL : $row['create_date1'];
				$createdOn = empty($createdOn) ? $this->utils->getNowForMysql() : $this->utils->formatDateTimeForMysql(new DateTime($createdOn));
				$affId = $this->affiliatemodel->getAffiliateIdByUsername($row['affliliate_code']);

				$real_name_arr = explode(" ", $row['real_name']);
				$real_name_arr_count = count($real_name_arr);

				$firstName = null;
				$lastName = null;

				if($real_name_arr_count == 1){
					$firstName = $real_name_arr[0];
				}elseif($real_name_arr_count == 2){
					$firstName = $real_name_arr[0];
					$lastName = $real_name_arr[1];
				}elseif($real_name_arr_count == 3 ){
					$firstName = $real_name_arr[0].' '.$real_name_arr[1];
					$lastName = $real_name_arr[2];
				}elseif($real_name_arr_count == 4 ){
					$firstName = $real_name_arr[0].' '.$real_name_arr[1];
					$lastName = $real_name_arr[2];
					if(isset($real_name_arr[3])){
						$lastName .= ' '.$real_name_arr[3];
					}
				}

				$gender = $row['gender_id'];

				if($gender == 'NULL' || empty($gender)){
					$gender = 'Female';
				}elseif($gender == '1'){
					$gender = 'Male';
				}else{
					$gender = 'Female';
				}

				$extra = array(
					'email' =>  $row['email'] == 'NULL' ? 'noemail@test.com' : $row['email'],
					'createdOn' => $createdOn,
					'affiliateId' => $affId,
					'lastLoginTime' => $row['last_login_time'],
		//  'frozen' => $frozen,
				);

				$details = array(
					'firstName' =>  $firstName,
					'lastName' => $lastName,
					'gender' => $gender,
					'country' => $country_map[$row['country_id_prefix']],
					'birthdate' => $row['birthday'],
					'contactNumber' => $row['mobile_no'],

				);

				$this->player_model->startTrans();
				$failMessage = '';
				$importPlayerId = $this->player_model->importPlayer($externalId, $levelId, $username, $password, $balance, $extra, $details, $failMessage);
				if (!$importPlayerId) {
					$failCount++;
					$this->utils->error_log("Import failed: [$failMessage]" . $username);
				}
				$this->player_model->endTrans();
				$totalCount++;

			}
		}

		fclose($file);
		$successCount = $totalCount - $failCount;
		$this->utils->debug_log("Import ole777 players done, [$successCount] out of [$totalCount] succeed.");
	}


	public function import_ole777_affiliates($aff_csv_file){

		$this->utils->debug_log('start importing affiliate', $aff_csv_file);

		$country = unserialize(COUNTRY_ISO2);
		$country_map = [];

		foreach ($country as $key => $value) {
			$country_map[$value] = $key;
		}

		$this->load->model(array('affiliatemodel'));
		$filename = '/home/vagrant/Code/'.$aff_csv_file;

		if(!file_exists($filename)){
			$this->utils->error_log("File not exist!");
			return;
		}

		$file = fopen($filename , "r");
		$header_keys =null;
		$count = 0;
		$failCount = 0;
		$totalCount = 0;

		while (($csv_row = fgetcsv($file)) !== FALSE){

			$count++;
			//get the title to be used as field keys
			if($count == 1){
				$header_keys = $csv_row;
			}else{

				$row = array_combine($header_keys, $csv_row);
				$externalId = null;
				$username = $row['affiliate_code'];
				$password = empty($row['password']) ? '' : $row['password'] ;

				$createdOn= $row['create_date'] == 'NULL' ? NULL : $row['create_date'];
				$createdOn = empty($createdOn) ? $this->utils->getNowForMysql() : $this->utils->formatDateTimeForMysql(new DateTime($createdOn));
				$affId = $this->affiliatemodel->getAffiliateIdByUsername($row['affiliate_code']);

				$real_name_arr = explode(" ", $row['real_name']);
				$real_name_arr_count = count($real_name_arr);

				$firstName = null;
				$lastName = null;


				if($real_name_arr_count == 1){
					$firstName = $real_name_arr[0];
				}elseif($real_name_arr_count == 2){
					$firstName = $real_name_arr[0];
					$lastName = $real_name_arr[1];
				}elseif($real_name_arr_count == 3 ){
					$firstName = $real_name_arr[0].' '.$real_name_arr[1];
					$lastName = $real_name_arr[2];
				}elseif($real_name_arr_count == 4 ){
					$firstName = $real_name_arr[0].' '.$real_name_arr[1];
					$lastName = $real_name_arr[2];
					if(isset($real_name_arr[3])){
						$lastName .= ' '.$real_name_arr[3];
					}
				}

				$gender = $row['gender'];
				$trackingCode = $this->affiliatemodel->randomizer('trackingCode');
				$status = $row['status'] == 'Active' ? 0 : 1;

				while ($this->affiliatemodel->checkTrackingCode($trackingCode)) {
					$trackingCode =  $this->affiliatemodel->randomizer('trackingCode');
				}

				$extra  = array(

					'notes' => $row['notes'] == 'NULL' ? NULL : $row['notes'],
					'gender' => $gender,
					'birthday' =>  $row['birthday'] == 'NULL' ? NULL : $row['birthday'],
					'currency' => $row['currency'],
					'country' => $country_map[$row['country_id']],
					'mobile' => $row['mobile'] == 'NULL' ? NULL : $row['mobile'],
					'affiliatePayoutId' => '0',
					'createdOn' => $createdOn,
					'website' => ($row['promotion_website'] == 'NULL' || empty($row['promotion_website'])) ? null : $row['promotion_website']

				);
				if(empty($username) ){
					$affId=null;
				}else{
					 //import to affiliates
					$affId = $this->affiliatemodel->importAffiliate($externalId,
						$username, $password, $trackingCode,
						$createdOn, $firstName, $lastName,
						$status, $extra);
				}
				$this->affiliatemodel->startTrans();
				$failMessage = '';
				$affId = $this->affiliatemodel->importAffiliate($externalId,$username, $password,
					$trackingCode,$createdOn, $firstName, $lastName,$status, $extra);
				if (!$affId ) {
					$failCount++;
					$this->utils->error_log("Import failed: [$failMessage]" . @$username);
				}
				$this->affiliatemodel->endTrans();
				$totalCount++;
			}
		}

		fclose($file);
		$successCount = $totalCount - $failCount;
		$this->utils->debug_log("Import ole777 affiliate done, [$successCount] out of [$totalCount] succeed.");
	}


	public function import_ole777_player_contacts($player_contacts_csv_file){

		$this->utils->debug_log('start importing player contacts',$player_contacts_csv_file);
		$this->load->model(array('player'));
		$filename = '/home/vagrant/Code/'.$player_contacts_csv_file;

		if(!file_exists($filename)){
			$this->utils->error_log("File not exist!");
			return;
		}
		$file = fopen($filename , "r");
		$header_keys =null;
		$count = 0;
		$failCount = 0;
		$totalCount = 0;

		while (($csv_row = fgetcsv($file)) !== FALSE){

			$count++;
			//get the title to be used as field keys
			if($count == 1){
				$header_keys = $csv_row;
			}else{
				$row = array_combine($header_keys, $csv_row);
				//print_r($row);
				$imAccount = null;
				$imAccountType = null;
				$imAccount2 = null;
				$imAccountType2 = null;
				$imAccount3 = null;
				$imAccountType3 = null;

				$player_id = $this->player->getPlayerIdByUsername($row['user_code']);
				$country = $this->player->getPlayerCountryByPlayerId($player_id);

				switch ($country) {
					case 'China':
					if(strtolower($row['contact_type']) == 'wechat') {
						$imAccount  = $row['contact_account'];
						$imAccountType = 'WeChat';
					}elseif(strtolower($row['contact_type']) == 'qq') {
						$imAccount2  = $row['contact_account'];
						$imAccountType2 = 'QQ';
					}elseif(strtolower($row['contact_type']) == 'skype') {
						$imAccount3  = $row['contact_account'];
						$imAccountType3 = 'Skype';
					}else{
						$imAccount3  = strtoupper($row['contact_type']).'-'.$row['contact_account'];
						$imAccountType3 = strtoupper($row['contact_type']);
					}
					break;

					case 'Thailand':
					case 'Indonesia':
					if(strtolower($row['contact_type']) == 'skype') {
						$imAccount  = $row['contact_account'];
						$imAccountType = 'Skype';
					}elseif(strtolower($row['contact_type']) == 'facebook') {
						$imAccount2 = $row['contact_account'];
						$imAccountType2 = 'Facebook';
					}else{
						$imAccount3  = strtoupper($row['contact_type']).'-'.$row['contact_account'];
						$imAccountType3 = strtoupper($row['contact_type']);
					}
					break;

					default:
					if(strtolower($row['contact_type']) == 'skype') {
						$imAccount  = $row['contact_account'];
						$imAccountType = 'Skype';
					}elseif(strtolower($row['contact_type']) == 'facebook') {
						$imAccount2 = $row['contact_account'];
						$imAccountType2 = 'Facebook';
					}else{
						$imAccount3  = strtoupper($row['contact_type']).'-'.$row['contact_account'];
						$imAccountType3 = strtoupper($row['contact_type']);
					}
					break;
				}

			$player_contacts = array(
					'imAccount' => $imAccount ,
					'imAccountType' => $imAccountType,
					'imAccount2' =>  $imAccount2,
					'imAccountType2' => $imAccountType2,
					'imAccount3' =>  $imAccount3,
					'imAccountType3' => $imAccountType3
				);
				//print_r($player_contacts);
				if(!empty($player_id)){
					$this->player->startTrans();
					$failMessage = '';
					$this->player->editPlayerDetails($player_contacts, $player_id);
					if ($this->player->isErrorInTrans()){
						$failCount++;
						$this->utils->error_log("Import failed: [$failMessage]" . @$row['user_code']);
					}
					$this->player->endTrans();
					$totalCount++;
				}
			}
		}
		fclose($file);
		$successCount = $totalCount - $failCount;
		$this->utils->debug_log("Import ole777 players contacts done, [$successCount] out of [$totalCount] succeed.");
	}

	public function import_ole777_affiliate_contacts($affiliate_contacts_csv_file){

		$this->utils->debug_log('start importing affiliate contacts',$affiliate_contacts_csv_file);
		$this->load->model(array('affiliatemodel'));
		$filename = '/home/vagrant/Code/'.$affiliate_contacts_csv_file;

		if(!file_exists($filename)){
			$this->utils->error_log("File not exist!");
			return;
		}
		$file = fopen($filename , "r");
		$header_keys =null;
		$count = 0;
		$failCount = 0;
		$totalCount = 0;

		while (($csv_row = fgetcsv($file)) !== FALSE){

			$count++;
			//get the title to be used as field keys
			if($count == 1){
				$header_keys = $csv_row;
			}else{
				$row = array_combine($header_keys, $csv_row);
				//print_r($row);
				$imAccount = null;
				$imAccountType = null;
				$imAccount2 = null;
				$imAccountType2 = null;
				if(strtolower($row['contact_type']) == 'qq') {
					$imAccount  = $row['contact_acct'];
					$imAccountType = 'QQ';
				}else{
					$imAccount2  = $row['contact_acct'];
					$imAccountType2 = strtoupper($row['contact_type']);
				}
				$affiliateId = $this->affiliatemodel->getAffiliateIdByUsername($row['affiliate_code']);
				$affiliate_contacts = array(
					'im1' => $imAccount ,
					'imType1' => $imAccountType,
					'im2' =>  $imAccount2,
					'imType2' => $imAccountType2
				);
				//print_r($affiliate_contacts);
				if(!empty($affiliateId)){
					$this->player->startTrans();
					$failMessage = '';
					$this->affiliatemodel->editAffiliate($affiliate_contacts, $affiliateId);
					if ($this->affiliatemodel->isErrorInTrans()){
						$failCount++;
						$this->utils->error_log("Import failed: [$failMessage]" .@$row['affiliate_code']);
					}
					$this->affiliatemodel->endTrans();
					$totalCount++;
				}
			}
		}
		fclose($file);
		$successCount = $totalCount - $failCount;
		$this->utils->debug_log("Import ole777 affiliates contacts done, [$successCount] out of [$totalCount] succeed.");
	}

	public function call_login_external($playerId, $username, $password){

		$success=$this->utils->login_external($playerId, $username, $password, $message);

		$this->utils->debug_log($success, $playerId, $username, $password, $message);

	}

	public function import_ole777_player_bankdetails($player_bankdetails_csv_file){

		$this->utils->debug_log('start importing player bank info',$player_bankdetails_csv_file);
		$this->load->model(array('player_model','playerbankdetails'));
		$filename = '/home/vagrant/Code/'.$player_bankdetails_csv_file;

		if(!file_exists($filename)){
			$this->utils->error_log("File not exist!");
			return;
		}
		$file = fopen($filename , "r");
		$header_keys =null;
		$count = 0;
		$failCount = 0;
		$totalCount = 0;

		while (($csv_row = fgetcsv($file)) !== FALSE){

			$count++;
			//get the title to be used as field keys
			if($count == 1){
				$header_keys = $csv_row;
			}else{
				$row = array_combine($header_keys, $csv_row);
				$playerId =  $this->player_model->getPlayerIdByUsername($row['user_code']);
				$external_id = null;
				$bankTypeId = $this->banktype->getBankTypeIdByBankcode($row['bank_code'],$playerId);
				$dwBank = '1';
				$bankAccountFullName = $row['user_bank_account_name'];
				$bankAccountNumber = $row['bank_account_no'];
				$province = null;
				$city =  null;
				$branch = $row['branch_bank_name'];
				$bankAddress = null; //included in branch we cant separate due to language;
				$createdOn = $this->utils->getNowForMysql();
				$status = '0';
				$message = null;
				$this->utils->debug_log("bankTypeId",$bankTypeId);
				if(!empty($playerId )  && !empty($bankTypeId)){

					$this->playerbankdetails->startTrans();
					$failMessage = '';
					$this->playerbankdetails->importPlayerBank($external_id, $playerId, $bankTypeId, $dwBank,
						  $bankAccountFullName, $bankAccountNumber, $province, $city, $branch, $bankAddress, $createdOn, $status, $message);
					if ($this->playerbankdetails->isErrorInTrans()){
						$failCount++;
						$this->utils->error_log("Import failed: [$failMessage]" .@$row['user_code']);
					}
					$this->playerbankdetails->endTrans();
					$totalCount++;
				}
			}
		}
		fclose($file);
		$successCount = $totalCount - $failCount;
		$this->utils->debug_log("Import ole777 player bankdetails  done, [$successCount] out of [$totalCount] succeed.");

	}

	public function run_importer_by_queue_token($token){

		$this->load->model(['player_model', 'queue_result']);
		if(empty($token)){
			$this->utils->error_log("[ERROR] [run_importer_by_queue_token], token is empty");
			return;
		}
		$row=$this->queue_result->getResult($token);
		if(empty($token)){
			$this->utils->error_log("[ERROR] [run_importer_by_queue_token], cannot find token ".$token);
			return;
		}

		$params=$this->utils->decodeJson($row['full_params']);

		$rlt=[];

		$files=$params['files'];
		$importer_formatter=$params['importer_formatter'];
		$summary=[];
		$message=null;

		$success=$this->player_model->importFromCSV($importer_formatter, $files, $summary, $message);
		$rlt['summary']=$summary;
		$rlt['message']=$message;
		$rlt['importer_formatter']=$importer_formatter;

		if ($success) {
			if (!$this->queue_result->appendResult($token, $rlt, true)) {
				$this->utils->error_log("[ERROR] [run_importer_by_queue_token], append result failed token:" . $token . " failed: ", $params);
			}
		} else {
			$this->queue_result->appendResult($token, $rlt, false, true);
			$this->utils->error_log("[ERROR] [run_importer_by_queue_token], token:" . $token . " failed: " , $params);
		}

	}

	public function preprocess_csv_file($filename, $split_number, $print_each_row='false', $max_count=9999999999, $max_col_length=200){

		$filename='/home/vagrant/Code/'.$filename;

		$number=intval($split_number);

		if(file_exists($filename)){

			$debug_info=[
				'ignore_first_row'=>true,
				'print_each_row'=>$print_each_row=='true',
				'max_count'=>$max_count, 'dry_run'=>false];

			$config=['debug_info'=>$debug_info, 'ignore_first_row'=>true];

			//get header
			$f=fopen($filename, 'r');
			$header=fgetcsv($f);
			fclose($f);
			$cntOfHeader=count($header);

			$this->utils->info_log('get header', $header, $cntOfHeader);

			$result_info=[
				'success'=>false,
				'cnt'=>0,
				'failedCnt'=>0,
				'successCnt'=>0,
				'successCntPerFile'=>[],
				'failedIndex'=>[],
				'warning'=>[],
				'cntOfHeader'=>$cntOfHeader,
			];

			//create how many files
			$cnt=0;
			$message=null;
			$file_handlers=[];
			for($i=0;$i<$number;$i++){
				$file_index=$i;
				$write_csv=$filename.'.'.$file_index;
				touch($write_csv);
				$file_handlers[$i]=fopen($write_csv, "w");
				$this->utils->info_log('open file to write', $write_csv);
			}

			$result_info['success']=$this->utils->loopCSV($filename, @$config['ignore_first_row'], $cnt, $message,
				function($cnt, $tmpData, &$stop_flag=false)
				use($file_handlers, $cntOfHeader, $max_col_length, $header, $filename, $number, $config, &$result_info){

				$debug_info=$config['debug_info'];

				if (@$debug_info['max_count'] && $cnt >= $debug_info['max_count']) {
					$this->utils->debug_log('stopped for debug');
					$stop_flag=true;
					return false;
				}

				if(@$debug_info['print_each_row']){
					$this->utils->debug_log('processing', $tmpData);
				}

				$message=null;

				//write line
				$file_index=$cnt % $number;

				if (!@$debug_info['dry_run']) {

					if($cnt<=$number){
						//write header
						fputcsv($file_handlers[$file_index], $header);
						$this->utils->debug_log('write header to '.$file_index, $header);
					}

					$success=fputcsv($file_handlers[$file_index], $tmpData)!==false;

					if($cntOfHeader!=count($tmpData)){
						$result_info['warning'][]=$cnt;
						$this->utils->info_log('warning header not match content', count($tmpData), $cntOfHeader, $cnt, $tmpData);
					}

					foreach ($tmpData as $d) {
						if(strlen($d)>$max_col_length){
							$result_info['warning'][]=$cnt;
							$this->utils->info_log('warning content too big', $cnt, $tmpData);
							break;
						}
					}

					if (!$success) {
						$this->utils->error_log('write to csv failed', $tmpData);
						$result_info['failedCnt']++;
						$result_info['failedIndex'][]=$cnt;
					} else {
						$result_info['cnt']++;
						if(!isset($result_info['successCntPerFile'][$file_index])){
							$result_info['successCntPerFile'][$file_index]=0;
						}
						$result_info['successCntPerFile'][$file_index]++;
						// $result_info['playerMap'][$playerInfo['username']]=$playerId;
					}
				} else {
					$this->utils->debug_log('only debug', $file_index);
					$result_info['cnt']++;
				}

				return true;
			});

			foreach ($file_handlers as $handler) {
				fclose($handler);
			}

			$this->utils->info_log('split_csv_file:', $result_info);
		}else{
			$this->utils->error_log('filename doesnot exist', $filename, $number);
		}

	}

	public function import_ole777_adminusers($adminusers_csv_file){

		$this->utils->debug_log('start importing adminusers info',$adminusers_csv_file);
		$this->load->model(array('roles','ole777_model'));
		$filename = '/home/vagrant/Code/'.$adminusers_csv_file;
		$rolesModel = $this->roles;

		if(!file_exists($filename)){
			$this->utils->error_log("File not exist!");
			return;
		}
		$file = fopen($filename , "r");
		$header_keys =null;
		$count = 0;
		$failCount = 0;
		$totalCount = 0;
		$roleMap = $this->roles->getRoleMap();
		$failedList = [] ;
		while (($csv_row = fgetcsv($file)) !== FALSE){
			$count++;
			//get the title to be used as field keys
			if($count == 1){
				$header_keys = $csv_row;
				//print_r($csv_row);exit;
			}else{
				$row = array_combine($header_keys, $csv_row);

				// Username RealName    Department  Position    Daily Maximum Approval for Withdrawal   Max Amount for Every Single Withdrawal  Role    Phone Extension Password    Notes
				$roleId = isset($roleMap[$row['Role']]) ? $roleMap[$row['Role']] : null;
				$userInfo = array(
					"username" => $row['Username'],
					"password" => $row['Password'],
					"realname" => $row['RealName'],
					"department" => $row['Department'],
					"position" => $row['Position'],
					"singleWidAmt"=> $row['Max Amount for Every Single Withdrawal'],
					"maxWidAmt" => $row["Daily Maximum Approval for Withdrawal"],
					"tele_id" => $row['Phone Extension'],
					"note" => $row['Notes'],
					"email" => '',
				);
				$this->utils->info_log("RoleId" ,$roleId, "userInfo", $userInfo);
				if(!empty($roleId)){
					$this->ole777_model->startTrans();
					$failMessage = '';
					$this->ole777_model->syncUser($rolesModel,$roleId,$userInfo);
					if ($this->ole777_model->isErrorInTrans()){
						$failCount++;
						$csv_row['reason'] = 'Trans Error';
						array_push($failedList, $csv_row);
						$this->utils->error_log("Import failed: [$failMessage]" , $csv_row);
					}
					$this->ole777_model->endTrans();
					$totalCount++;
				}else{
					$csv_row['reason'] = 'No roleId';
					array_push($failedList, $csv_row);
				}
			}
		}
		fclose($file);
		$successCount = $totalCount - $failCount;
		$this->utils->debug_log("Import ole777 adminusers  done, [$successCount] out of [$totalCount] succeed.", "failedList", $failedList);

	}

	public function import_ole777_player_agent_relation($player_agent_csv_file){

		$this->utils->debug_log('start importing player_agent info',$player_agent_csv_file);
		$this->load->model(array('player_model','agency_model'));
		$filename = '/home/vagrant/Code/'.$player_agent_csv_file;
		$playerModel = $this->player_model;
		$agencyModel = $this->agency_model;

		if(!file_exists($filename)){
			$this->utils->error_log("File not exist!");
			return;
		}
		$file = fopen($filename , "r");
		$header_keys =null;
		$count = 0;
		$failCount = 0;
		$totalCount = 0;
		$playerMap = $this->player_model->getPlayerUsernameIdMap();
		$agentMap = $this->agency_model->getAgentNameIdMap();
		$failedList = [] ;
		while (($csv_row = fgetcsv($file)) !== FALSE){
			$count++;
			//get the title to be used as field keys
			if($count == 1){
				$header_keys = $csv_row;
				//print_r($csv_row);exit;
			}else{
				$row = array_combine($header_keys, $csv_row);
				//member_user_code  ParentAgent
				$playerInfo = array(
					"agent_id" => $agentMap[$row['ParentAgent']],
				);

				$playerId = isset($playerMap[$row['member_user_code']]) ? $playerMap[$row['member_user_code']] : null;

				$this->utils->info_log("PlayerId" ,$playerId, "userInfo", $playerInfo);

				if(!empty($playerId)){
					$this->player_model->startTrans();
					$failMessage = '';
					$this->utils->info_log('Updating player agent_id', "PlayerId" ,$playerId, "userInfo", $playerInfo);
					$this->player_model->updatePlayer($playerId, $playerInfo);
					if ($this->player_model->isErrorInTrans()){
						$failCount++;
						$csv_row['reason'] = 'Trans Error';
						array_push($failedList, $csv_row);
						$this->utils->error_log("Import failed: [$failMessage]" , $csv_row);
					}
					$this->player_model->endTrans();
					$totalCount++;
				}else{
					$csv_row['reason'] = 'No playeId -username not exist!';
					array_push($failedList, $csv_row);
				}
			}
		}
		fclose($file);
		$successCount = $totalCount - $failCount;
		$this->utils->debug_log("Import ole777 player_agent info done, [$successCount] out of [$totalCount] succeed.", "failedList", $failedList);

	}


	public function generate_importer_queue_job($importer, $import_player_csv_file, $import_aff_csv_file,
		$import_aff_contact_csv_file, $import_player_contact_csv_file, $import_player_bank_csv_file,
		$import_agency_csv_file, $import_agency_contact_csv_file){

		$uploadCsvFilepath=$this->utils->getSharingUploadPath('/upload_temp_csv');

		$importer_formatter=$importer;
		$files=[
			'import_player_csv_file'=>$import_player_csv_file,
			'import_aff_csv_file'=>$import_aff_csv_file,
			'import_aff_contact_csv_file'=>$import_aff_contact_csv_file,
			'import_player_contact_csv_file'=>$import_player_contact_csv_file,
			'import_player_bank_csv_file'=>$import_player_bank_csv_file,
			'import_agency_csv_file'=>$import_agency_csv_file,
			'import_agency_contact_csv_file'=>$import_agency_contact_csv_file,
		];
		$this->load->library(['lib_queue']);
		$callerType=Queue_result::CALLER_TYPE_ADMIN;
		$caller=1;
		$state=null;
		$lang=null;
		$this->lib_queue->addRemoteImportPlayers($files, $importer_formatter, $callerType, $caller, $state, $lang);

	}

	public function import_9win_agency_player($filename, $dry_run='true', $max_count=10, $print_each_row='false'){

		$filename='/home/vagrant/Code/'.$filename;
		if(!file_exists($filename)){
			return $this->utils->error_log('not found file');
		}
		$dry_run=$dry_run=='true';
		$debug_info=[
			'ignore_first_row'=>true,
			'print_each_row'=>$print_each_row=='true',
			'max_count'=>$max_count, 'dry_run'=>$dry_run];

		$config=['debug_info'=>$debug_info, 'ignore_first_row'=>true];

		$result_info=[
			'success'=>false,
			'cnt'=>0,
			'failedCnt'=>0,
			'successCnt'=>0,
			'failed_username'=>[],
		];

		$this->load->model(['player_model', 'agency_model']);

		$result_info['success']=$this->utils->loopCSV($filename, @$config['ignore_first_row'], $cnt, $message,
			function($cnt, $tmpData, &$stop_flag=false)
			use(&$result_info, $config){

			$debug_info=$config['debug_info'];

			if (@$debug_info['max_count'] && $cnt >= $debug_info['max_count']) {
				$this->utils->debug_log('stopped for debug');
				$stop_flag=true;
				return false;
			}

			if(@$debug_info['print_each_row']){
				$this->utils->debug_log('processing', $tmpData);
			}

			$next=true;

			$agent_name=$tmpData[0];
			$playerUsername=$tmpData[1];

			$playerInfo=$this->player_model->getPlayerArrayByUsername($playerUsername);
			$playerId=$playerInfo['playerId'];
			if(empty($playerUsername) || empty($playerId)){
				$result_info['failedCnt']++;
				$result_info['failed_username'][]=$playerUsername;
				$this->utils->info_log('cannot find player', $playerUsername, $playerId, $playerInfo);
				return $next;
			}
			//exists agent
			if(!empty($playerInfo['agent_id'])){
				$result_info['failedCnt']++;
				$result_info['failed_username'][]=$playerUsername;
				$this->utils->debug_log('ignore exist', $playerUsername, $playerId, $playerInfo);
				return $next;
			}
			$agentId=$this->agency_model->getAgentIdByUsername($agent_name);
			if(empty($agent_name) || empty($agentId)){
				$result_info['failedCnt']++;
				$result_info['failed_username'][]=$playerUsername;
				$this->utils->info_log('cannot find agent', $playerUsername, $agent_name, $agentId);
				return $next;
			}

			if($debug_info['dry_run']){
				$result_info['cnt']++;
				$this->utils->debug_log('!!!!dry run!!!! update agent id by player id', $playerId, $agentId);
			}else{
				//update player
				$succ=$this->player_model->updateAgentIdByPlayerId($playerId, $agentId);
				if($succ){
					$result_info['cnt']++;
				}else{
					$result_info['failedCnt']++;
					$result_info['failed_username'][]=$playerUsername;
				}
			}

			return $next;

		});

		$this->utils->debug_log('import_9win_agency_player',$result_info);

	}

	public function import_csv_bonus_by_queue($token){
		//load from token
		$data=$this->initJobData($token);

		$this->load->model(['queue_result', 'player_promo']);
		// $rlt=$this->queue_result->getResult($token);
		// $params=$this->utils->decodeJson($rlt['full_params']);

		$token = $data['token'];
		$params = $data['params'];

		$this->utils->debug_log('load from queue', $token, $params);

		$adminUserId=$data['caller'];
		$show_in_front_end=$params['show_in_front_end'];
		$reason=$params['reason'];

		$status=$params['status']==Player_promo::TRANS_STATUS_APPROVED ? 'approved' : 'request';

		$csv_file = $params['csv_full_path'];
		$csv_file = rtrim($this->utils->getConfig('SHARING_PRIVATE_PATH').'/'.$csv_file);

		$result_info=$this->import_csv_bonus_anyfile($csv_file, $params['promo_cms_setting_id'], 'false', 'true', $status,
			$adminUserId, $show_in_front_end, $reason, $token);

		//$this->update_csv_bonus_queue_result($token, $result_info);

		return $result_info;
	}

	public function import_csv_bonus($csv_file, $promoCmsSettingId, $dry_run='true', $ignore_first_row='true', $status='approved',
			$adminUserId='1', $show_in_front_end='0', $reason='import from csv'){
		// $csv_file
		$csv_file='/home/vagrant/Code/'.$csv_file;

		$this->import_csv_bonus_anyfile($csv_file, $promoCmsSettingId, $dry_run, $ignore_first_row, $status,
			$adminUserId, $show_in_front_end, $reason);
	}

	public function import_csv_bonus_anyfile($csv_file, $promoCmsSettingId, $dry_run='true', $ignore_first_row='true', $status='approved',
		$adminUserId='1', $show_in_front_end='0', $reason='import from csv', $token=null){
		// $csv_file
		// $csv_file='/home/vagrant/Code/'.$csv_file;
		$success=true;
		$dry_run=$dry_run=='true';

		$this->load->model(['queue_result','player_model', 'users', 'promorules', 'player_promo', 'transactions', 'withdraw_condition']);

		$message=lang('Add Bonus Failed');
		$ignore_first_row = $ignore_first_row=='true';
		$cnt = 0;
		$playerCnt=0;
		$success_amount = 0;
		$successCnt =0;
		$failedCnt =0;
		$success_usernames = [];
		$failed_usernames = [];
		$controller=$this;
		$queue_result_model = $this->queue_result;

		$adminUsername=$this->users->getUsernameById($adminUserId);

		if($status=='request'){
			$status=Player_promo::TRANS_STATUS_REQUEST;
		}else{
			$status=Player_promo::TRANS_STATUS_APPROVED;
		}
		//1 or 0
		// $show_in_front_end=$show_in_front_end=='false';
		$log_filepath = null;

		if(!empty($token)){
			$csv_headers = [lang('username'), lang('cms.bonusAmount'), lang('status')];
			$message_log =[];
			$this->utils-> _appendSaveDetailedResultToRemoteLog($token, 'batch_add_bonus_result', $message_log, $log_filepath, true, $csv_headers);
		}

		//debug
		// $csv_file = rtrim($this->utils->getConfig('SHARING_PRIVATE_PATH').'/'.$csv_file);

		if(!file_exists($csv_file)){
			$rlt=['success'=>false, 'failCount'=>0, 'errorDetail'=>'CSV file is not exist', 'failedList' =>0,  'successCount'=>0,  'processedRows' => 0, 'progress' => 0];
			$queue_result_model->failedResult($token, $rlt);
			return $controller->utils->error_log("File not exist!");
		}

		$fp = file($csv_file);// this one works
		$totalCount =  count($fp) - 1;
		$count_loop=0;

		$percentage_steps = [];

		for ($i=.1; $i <= 10 ; $i +=.1) {
			array_push($percentage_steps, ceil($i/10 * $totalCount));
		};

		$success=$this->utils->loopCSV($csv_file, $ignore_first_row, $cnt, $message,
			function($cnt, $tmpData, $stop_flag) use($controller,$queue_result_model, $adminUserId, $adminUsername, $promoCmsSettingId,
				$show_in_front_end, $reason, $status, $dry_run, $token,&$message, &$success_amount, &$success_usernames, &$failed_usernames,
				&$successCnt, &$failedCnt, &$playerCnt,&$totalCount,&$log_filepath,$percentage_steps,&$count_loop){
				//process one row
			 	$success=false;

				$this->utils->debug_log('==============post_manually_batch_bonus->tmpData', $tmpData);

				// git issue #1420
				// sanitizing data items: trim first, then force data type
				for ($i = 0; $i <= 4; ++$i) {
					$tmpData[$i] = trim($tmpData[$i]);
					$tmpData[$i] = ($i == 0) ? strval($tmpData[$i]) : floatval($tmpData[$i]);
				}

				$player_name    = $tmpData[0];
				$amount         = $tmpData[1];
				$betTimes       = $tmpData[2];
				$condition      = $tmpData[3];
				$deposit_amt_condition = $tmpData[4];

				$playerCnt++;

				$player_id=$this->player_model->getPlayerIdByUsername($player_name);

				if(empty($player_id)){
					$this->utils->debug_log('==============player not exist', $player_name);
					$failed_usernames[$player_name] = sprintf(lang('gen.error.not_exist'), $player_name).' '.lang('Amount').': '.$amount;
					$message_log = [$player_name, $amount, lang('Player does not exist')];
					$controller->utils-> _appendSaveDetailedResultToRemoteLog($token,'batch_add_bonus_result', $message_log, $log_filepath, true, []);
					$failedCnt++;
					$count_loop++;
					if(!empty($token)){
					$rlt=['success'=>false, 'failedCnt'=>$failedCnt, 'successCnt'=> $successCnt, 'success_amount'=>$success_amount,
					'cnt'=>$cnt, 'playerCnt'=>$playerCnt, 'processedRows' => $count_loop, 'totalCount' => $totalCount, 'progress' => ceil($count_loop/$totalCount * 100),'log_filepath' => site_url().'remote_logs/'.basename($log_filepath)];
					$queue_result_model->updateResultRunning($token, $rlt);
				    }
					return false;
				}

				$message = lang('Add Bonus Failed');
				$transaction = false;
				$success=$this->lockAndTransForPlayerBalance($player_id, function()
					use($controller,$queue_result_model, $adminUserId, $adminUsername, $promoCmsSettingId, $show_in_front_end, $player_id, $player_name,
						$status, $reason, $amount, $betTimes, $condition, $deposit_amt_condition, $dry_run, $token, &$message,&$log_filepath,&$count_loop) {

						// $current_timestamp = $controller->utils->getNowForMysql();

						$promoRuleId  = $controller->promorules->getPromorulesIdByPromoCmsId($promoCmsSettingId);
						// $release_date=$controller->input->post('release_date');

						//set promo category
						$promo_category = null;
						if (!empty($promoRuleId)) {
							$promorule = $controller->promorules->getPromoRuleRow($promoRuleId);
							$promo_category = $promorule['promoCategory'];
						}

						$action_name = 'Add';
						$adjustment_type = Transactions::ADD_BONUS;

						$note = 'add bonus '.number_format($amount, 2).' to '.$player_name.' by '.$adminUsername.', with deposit condition of ' . $condition;

						if($this->player_model->isDisabledPromotion($player_id)){
							$message = lang('Add Bonus Failed') . '. ' . lang('Disable Promotion');

							return false;
						}

				        #if want pending, don't create transaction, only create player promo
						if($status == Player_promo::TRANS_STATUS_REQUEST ){
					    // request promo
							$success=!!$controller->player_promo->requestPromoToPlayer($player_id, $promoRuleId, $amount, $promoCmsSettingId, $adminUserId, $deposit_amt_condition,
								$condition , Player_promo::TRANS_STATUS_REQUEST, $betTimes, $reason );
							if(!$success){
								$message=lang('Request Promotion Failed');
							}else{
								$message=lang('Request Promotion Successfully');
							}
						}else{

							$controller->utils->debug_log('==============post_manually_batch_bonus->before_transaction', 'test');

							$transaction = $controller->transactions->createAdjustmentTransaction($adjustment_type,
								$adminUserId, $player_id, $amount, null, $note, null,
								$promo_category, $show_in_front_end, $reason, $promoRuleId);

							$controller->utils->debug_log('==============post_manually_batch_bonus->transaction', $transaction);

							$success=!!$transaction;

							if($success){
								$controller->transactions->addPlayerBalAdjustmentHistory(array(
									'playerId' => $transaction['to_id'],
									'adjustmentType' => $transaction['transaction_type'],
									'walletType' => 0, # 0 - MAIN WALLET
									'amountChanged' => $transaction['amount'],
									'oldBalance' => $transaction['before_balance'],
									'newBalance' => $transaction['after_balance'],
									'reason' => $reason,
									'adjustedOn' => $transaction['created_at'],
									'adjustedBy' => $transaction['from_id'],
									// 'show_flag' => $show_in_front_end == '1',
						        ));

								//move to withdraw_condition
								$promorule=$controller->promorules->getPromoruleById($promoRuleId);
								$bonusTransId=$transaction['id'];
								$controller->withdraw_condition->createWithdrawConditionForManual($player_id, $bonusTransId,
									$condition, $deposit_amt_condition, $amount, $betTimes, $promorule,$reason);

								//save to player promo
								$playerBonusAmount = $amount;
								$extra_info=[];
								$player_promo_id = $controller->player_promo->approvePromoToPlayer($player_id, $promoRuleId, $playerBonusAmount,
								$promoCmsSettingId, $adminUserId, null, $condition, $extra_info, $deposit_amt_condition, $betTimes,$reason );
								//update player promo id of transaction
								$controller->transactions->updatePlayerPromoId($bonusTransId, $player_promo_id, $promo_category);
								$success=true;
								$message=lang('Add Bonus Successfully');

							}else{
								$message=lang('Add Bonus Failed');
							}
						}

						if($dry_run){
							$success=false;
							$controller->utils->debug_log('dry run, so rollback');
						}

						return $success;

					});//lockAndTransForPlayerBalance

				 /*if successfully transferred to main wallet then check if promorule has  a releaseToSubWallet ID*/
				if($success){
				 	$promoRuleId  = $controller->promorules->getPromorulesIdByPromoCmsId($promoCmsSettingId);
				 	if (!empty($promoRuleId)) {
				 		$gameAPI = $controller->promorules->getPromoRuleRow($promoRuleId)['releaseToSubWallet'];
				 		$main_wallet_id = 0;

				 		/** check if promo rule release bonus to sub-wallet */
				 		if (!empty($gameAPI)) {
				 			if (!$this->external_system->isGameApiActive($gameAPI)) {
				 				$this->utils->debug_log('============== API is not active. Cant transfer to subwallet', 'warning');
				 			} else {
				 				/** Transfer bonus from main wallet sub wallet */
				 				$transferResult = $this->utils->transferWallet($player_id, $player_name, $main_wallet_id, $gameAPI, $amount);
				 				$this->utils->debug_log('result of transfer wallet', $transferResult);
				 			}
				 		}
				 	}
				}

				if($success){
					$success_amount += $amount;
					// $success_usernames[$player_name] = lang('Transfer Success');
					$success_usernames[$player_name] = $message;
					$successCnt++;
				}else{
		            // $failed_usernames[$player_name] = lang('Transfer Failed');
					$failed_usernames[$player_name] = $message;
					$failedCnt++;
				}

				if(!empty($token)){
					$result=[
						lang('Username').' '.$player_name.' '.lang('Add Bonus').' '.$amount.' '.($success ? lang('success') : lang('Failed'))
					];
					$done=false;
	                //update result
					$message_log = [$player_name, $amount, $success ? lang('success') : lang('Failed')];
					$controller->utils-> _appendSaveDetailedResultToRemoteLog($token,'batch_add_bonus_result', $message_log, $log_filepath, true, []);
					$controller->utils->info_log('count_loop',$count_loop);
					$count_loop++;
	                //update front end progress
					$rlt=['success'=>false, 'failedCnt'=>$failedCnt, 'successCnt'=> $successCnt, 'success_amount'=>$success_amount,
					'cnt'=>$cnt, 'playerCnt'=>$playerCnt, 'processedRows' => $count_loop, 'totalCount' => $totalCount, 'progress' => ceil($count_loop/$totalCount * 100),'log_filepath' => site_url().'remote_logs/'.basename($log_filepath)];
					$queue_result_model->updateResultRunning($token, $rlt);

				}

	        return $success;
	});

	$failed_json_file='/home/vagrant/Code/import-bonus-failed_usernames-'.random_string('unique').'.json';
	file_put_contents($failed_json_file, json_encode($failed_usernames));
	$result_info=[
	   //'success'=>$success,
		'success'=>true,
		'cnt'=>$cnt,
		'playerCnt'=>$playerCnt,
		'failedCnt'=>$failedCnt,
		'successCnt'=>$successCnt,
		'successAmt'=>$success_amount,
		'failed_json_file'=>$failed_json_file,
		//'failed_username'=>$failed_usernames, // username=>failed message
		//'username'=>[], // $success_usernames too long
		'log_filepath' => site_url().'remote_logs/'.basename($log_filepath),
	];

	$this->utils->debug_log('add bonus promo result',$result_info);

	if($count_loop == $totalCount){
		$controller->utils->info_log('count_loop == totalCount',$count_loop == $totalCount);
		//update last - Done
		$rlt=['success'=>true,'failedCnt'=>$failedCnt, 'successCnt'=> $successCnt, 'successAmt'=>$success_amount,
		'playerCnt'=>$playerCnt, 'processedRows' => $count_loop, 'totalCount' => $totalCount, 'progress' => 100,'log_filepath' => site_url().'remote_logs/'.basename($log_filepath)];
		$queue_result_model->updateResult($token, $rlt);
		//end adjustnment-------------------------------------------------------------------
	}

	return $result_info;

	}

}

///END OF FILE
