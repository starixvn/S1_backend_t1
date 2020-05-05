<?php
/**
 * In-house bonus game API test service module
 *
 * @author		Rupert Chen <rupert.php.tw@tripleonetech.net>
 * @copyright	Tripleonetech 2018
 */
trait marketing_bonus_game_module {

	public function test_inspect($ver = 'new') {
		$this->load->library(['ip_manager']);
		if (!$this->ip_manager->checkIfIpAllowed()) {
			die('Sorry, not available.');
		}

		$dbop = $this->input->get('dbop');

		$pg_tables = [
			'tokens'			=> [ 'seq' => 1  , 'show' => 1 , 'id' => 'common_tokens' ] ,
			'deploy_channels'	=> [ 'seq' => 2  , 'show' => 0 , 'id' => 'promo_game_deploy_channels' ] ,
			'games'				=> [ 'seq' => 3  , 'show' => 1 , 'id' => 'promo_game_games' ] ,
			'game_to_channel'	=> [ 'seq' => 4  , 'show' => 0 , 'id' => 'promo_game_game_to_channel' ] ,
			'gametypes'			=> [ 'seq' => 5  , 'show' => 0 , 'id' => 'promo_game_gametypes' ] ,
			'player_history'	=> [ 'seq' => 6  , 'show' => 1 , 'id' => 'promo_game_player_game_history' ,
				'widths' => [ 'game_config' => 44 , 'external_request_id' => 8 , 'request_promotion_id' => 8, 'notes' => 8,
				'updated_at' => 4, 'created_at' => 4, 'realized_at' => 4, '_default' => 2.5 ]
			] ,
			'prizes'			=> [ 'seq' => 7  , 'show' => 0 , 'id' => 'promo_game_prizes' ] ,
			'promorule_to_games'=> [ 'seq' => 8  , 'show' => 1 , 'id' => 'promo_game_promorule_to_games' ] ,
			'resources'			=> [ 'seq' => 9  , 'show' => 0 , 'id' => 'promo_game_resources' ] ,
			'themes'			=> [ 'seq' => 10 , 'show' => 0 , 'id' => 'promo_game_themes' ] ,
			'player_to_games'	=> [ 'seq' => 11 , 'show' => 1 , 'id' => 'promo_game_player_to_games' ] ,
		];
		$this->load->model(['promo_games']);
		$dset = $this->promo_games->test_inspect();

		$dset['resources_ext'] = $this->promo_games->resources_add_extended_info($dset['resources']);

		$data = [];
		$data['tables'] = $pg_tables;
		$data['dset'] = $dset;
		$data['ops'] = $this->input->get('dbop');

		$view_files = [
			'old' => 'marketing_management/bonus_game_inspector' ,
			'new' => 'marketing_management/bonus_game_inspector2'
		];

		$view_file = $this->utils->safeGetArray($view_files, $ver, $view_files['new']);

		$this->load->view($view_file, $data);

	}

	public function test_bonus_game_ops() {
		$this->load->model(['promo_games']);
		$ret = [ 'success' => false, 'message' => 'exec_incomplete', 'result' => null ];

		try {
			$op = $this->input->post('op');
			$go = $this->input->post('go');
			$table = $this->input->post('table');
			$op_arg = json_decode($this->input->post('op_arg'), 'as_array');
			$op_res = null;
			if (empty($go)) {
				$op_res = print_r($op_arg, 1);
			}
			else {
				if (empty($table)) {
					throw new Exception('ops:table_missing');
				}
				switch ($op) {
					case 'insert' :
						if ($table == 'promo_game_player_to_games') {
							$op_res = $this->promo_games->create_player_to_game_entry($op_arg);
						}
						else if ($table == 'promo_game_resources') {
							$op_res = $this->promo_games->create_resources_entry($op_arg);
						}
						else {
							throw new Exception('ops-insert:table_unknkown');
						}

						if ($op_res === false) {
							throw new Exception($this->db->_error_message());
						}
						break;
					case 'delete' :
						if (!is_array($op_arg)) {
							throw new Exception('delete_requires_simple_array_of_ids');
						}

						switch ($table) {
							case 'promo_game_player_to_games' :
								$op_res = $this->promo_games->remove_player_to_game_entry($op_arg);
								break;

							case 'promo_game_resources' :
								$op_res = $this->promo_games->remove_resources_entry($op_arg);
								break;

							case 'promo_game_player_game_history' :
								if (!is_array($op_arg)) {
									throw new Exception('ops-remove:history:array_required');
								}
								if (count($op_arg) != 2) {
									throw new Exception('ops-remove:history:array_must_contain_begin_and_end');
								}
								$begin	= intval($op_arg[0]);
								$end	= intval($op_arg[1]);
								if ($begin < 0 || $end < 0) {
									throw new Exception('ops-remove:history:begin_end_malformed');
								}
								if (abs($begin - $end) > 20) {
									throw new Exception('ops-remove:history:begin_to_end_longer_than_20');
								}
								$op_res = $this->promo_games->remove_history_entry_between($begin, $end);
								break;

							default:
								throw new Exception('ops-remove:table_unknkown');
								break;
						}

						if ($op_res === false) {
							throw new Exception($this->db->_error_message());
						}
						break;
					case 'dump' :
						$query = $this->db->from($table)
							->get();
						$res = $query->result_array();
						$op_res = json_encode($res);
						break;
					case 'import' :
						if ($table != 'promo_game_resources') {
							throw new Exception('ops-import:not_supported_for_table');
						}
						$op_res = $this->promo_games->resources_import_json($op_arg);
						break;
					default :
						throw new Exception('unknown_op');
				}
			}

			$ret = [ 'success' => true, 'message' => null, 'result' => $op_res ];
		}
		catch (Exception $ex) {
			$ret['message'] = $ex->getMessage();
		}
		finally {
			$this->returnJsonResult($ret);
		}

	}

	public function test_bonus_game_resource_ops() {
		$this->load->model(['promo_games']);
		$ret = [ 'success' => false, 'message' => 'exec_incomplete', 'result' => null ];

		try {
			$args = $this->input->post('arg');
			$action = $args['action'];
			unset($args['action']);

			switch ($action) {
				case 'remove' :
					if (!isset($args['id'])) {
						throw new Exception('rm__error_id_not_present');
					}
					$dbres = $this->promo_games->remove_resources_entry([ $args['id'] ]);
					if (!$dbres) {
						throw new Exception("rm__error_db__{$this->db->_error_message()}");
					}
					$ret = "rm__success__(aff_rows=$dbres)";
					break;
				case 'insert' :
					unset($args['id']);
					// print_r($args); die();
					$dbres = $this->promo_games->create_resources_entry($args);
					if (!$dbres) {
						throw new Exception("insert__error_db__{$this->db->_error_message()}");
					}
					$ret = "insert__success__(ins_id=$dbres)";
					break;
				case 'update' :
					if (!isset($args['id'])) {
						throw new Exception('update__error_id_not_present');
					}
					$id = $args['id'];
					unset($args['id']);
					$dbres = $this->promo_games->update_resources_entry($id, $args);
					if (!$dbres) {
						throw new Exception("update__error_db__{$this->db->_error_message()}");
					}
					$ret = "update_success__(aff_rows=$dbres)";
					break;

				default :
					throw new Exception('unknown_op');
					break;
			}

			$ret = [ 'success' => true, 'message' => null, 'result' => $ret ];
		}
		catch (Exception $ex) {
			$ret['message'] = $ex->getMessage();
		}
		finally {
			$this->returnJsonResult($ret);
		}
	}

	public function test_bonus_game_resource_export() {
		$this->load->model(['promo_games']);
		$ret = [ 'success' => false, 'message' => 'exec_incomplete', 'result' => null ];

		try {
			$ret = $this->promo_games->resources_export_json();

			$ret = [ 'success' => true, 'message' => null, 'result' => $ret ];
		}
		catch (Exception $ex) {
			$ret['message'] = $ex->getMessage();
		}
		finally {
			$this->returnJsonResult($ret);
		}
	}

	public function glops($method = null, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null) {
		// $this->load->model('gl_game_tokens');
		$this->load->library('gl_game_lib');
		try {
			$res = null;

			if ($this->utils->getConfig('marketing_glops_disabled') == true) {
				throw new Exception('glops disabled', 32754);
			}

			if (empty($method)) {
				throw new Exception('Test not specified', 32767);
			}

			$methods_player_id_optional = [ 'tokens', 'rev_query', 'find_id', 'gl_deact', 'maintain_del', 'gl_recharge', 'gl_withdraw' ];
			if (!in_array($method, $methods_player_id_optional)) {
				$player_id = $arg1;
				if (empty($player_id)) {
					throw new Exception('player_id missing', 0x01);
				}
			}

			switch($method) {
				case 'glrv_recharge' :
					$amount = floatval($arg2);
					$secure_id = $this->gl_game_lib->mock_secure_id('D');
					$tx_res = $this->gl_game_lib->create_token_recharge($player_id, $secure_id, $amount);
					$res = [ 'tx_res' => $tx_res ];
					break;

				case 'glrv_withdraw' :
					$amount = floatval($arg2);
					$secure_id = $this->gl_game_lib->mock_secure_id('W');
					$tx_res = $this->gl_game_lib->create_token_withdraw($player_id, $secure_id, $amount);
					$res = [ 'tx_res' => $tx_res ];
					break;

				case 'gl_deact' :
					$token = $arg1;
					if (empty($token)) {
						throw new Exception('token missing', 0x03);
					}
					$deact_res = $this->gl_game_lib->deactivate_token($token);
					$res = [ 'deact_res' => $deact_res ];
					break;

				// case 'gl_login' :
				// 	$gl_api = $this->utils->loadExternalSystemLibObject(GL_API);
				// 	$username = $this->player_model->getUsernameById($player_id);
				// 	$gl_login_res = $gl_api->login($username, null);
				// 	$res = [ 'gl_login_res' => $gl_login_res ];
				// 	break;

				case 'gl_recharge' : case 'gl_withdraw' :
					$player_id = intval($arg1);
					$amount = floatval($arg2);
					$gl_api = $this->utils->loadExternalSystemLibObject(GL_API);
					$username = $this->player_model->getUsernameById($player_id);
					if ($method == 'gl_recharge') {
						$gtx_res = $gl_api->depositToGame($username, $amount);
						$res = [ 'recharge_res' => $gtx_res ];
					}
					else {
						$gtx_res = $gl_api->withdrawFromGame($username, $amount);
						$res = [ 'withdraw_res' => $gtx_res ];
					}
					break;

				case 'gl_bal' :
					$player_id = intval($arg1);
					$gl_api = $this->utils->loadExternalSystemLibObject(GL_API);
					$username = $this->player_model->getUsernameById($player_id);
					$bal_res = $gl_api->queryPlayerBalance($username);
					$res = [ 'bal_res' => $bal_res ];
					break;

				case 'gl_rec' :
					$player_id	= intval($arg1);
					$date_from	= $arg2;
					$date_to	= $arg3;
					$gl_api = $this->utils->loadExternalSystemLibObject(GL_API);
					$username = $this->player_model->getUsernameById($player_id);
					$rec_res = $gl_api->queryGameRecords($date_from, $date_to, $username);
					$res = [ 'bal_res' => $rec_res ];
					break;

				case 'gl_game' :
					?>
					<div>
						<a target="_blank" href="/player_center/goto_glgame/demo">goto gl game</a>
					</div>
					<?php
					break;

				case 'login' :
					$res_login = $this->gl_game_lib->player_login($player_id);
					$res = [ 'result_login' => $res_login ];
					break;

				case 'logout' :
					$res_logout = $this->gl_game_lib->player_logout($player_id);
					$res = [ 'result_logout' => $res_logout ];
					break;

				case 'is_logged_in' :
					$result_is_logged_in = $this->gl_game_lib->is_player_logged_in($player_id);
					$res = [ 'result_is_logged_in' => $result_is_logged_in['result'] ];
					break;

				case 'maintain_del' :
					$id_min = intval($arg1);
					$id_max = intval($arg2);
					if (empty($id_min) || empty($id_max)) {
						throw new Exception('need both id_min and id_max', 0x04);
					}
					if ($id_min > $id_max) {
						$tmp = $id_min; $id_min = $id_max; $id_max = $tmp;
					}
					$result_del = $this->gl_game_tokens->del_tokens($id_min, $id_max);
					$res = [ 'num_deleted_rows' => $result_del ];
					break;

				case 'maintain_rename' :
					$player_id = intval($arg1);
					$new_game_username = trim($arg2);
					if (empty($new_game_username)) {
						throw new Exception('new_game_usernam required', 0x05);
					}
					$this->load->model([ 'game_provider_auth' ]);
					$this->game_provider_auth->updateUsernameForPlayer($player_id, $new_game_username, GL_API);
					$aff_rows = $this->db->affected_rows();
					$res = [ 'affected_rows' => $aff_rows ];
					break;

				case 'find_id' :
					$username = trim($arg1);
					if (empty($username)) {
						throw new Exception('username missing', 0x02);
					}
					$res_find_id = $this->gl_game_lib->find_player_id_by_username($username);
					$res = [ 'result_find_id' => $res_find_id , 'username' => $username ];
					break;

				case 'tokens' :
					$player_id = intval($arg1);
					$limit = intval($arg2);
					$res_tokens = $this->gl_game_lib->list_tokens($player_id, $limit);

					?>
					<style type="text/css">
						table.tok { font-family: iosevka, monospace; padding-bottom: 2em; min-width: 360px; }
						table.tok td { padding: 2px 8px; text-align: center; }
					</style>
					<?php if (!empty($res_tokens)) : ?>
						<table class="tok">
							<tr>
							<?php foreach ($res_tokens[0] as $key=>$cell) : ?>
								<th><?= $key ?></th>
							<?php endforeach; ?>
							</tr>
							<?php foreach ($res_tokens as $row) : ?>
								<tr>
								<?php foreach ($row as $key=>$cell) : ?>
									<td><?= $cell ?></td>
								<?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
						</table>
					<?php else : ?>
						<table class="tok">
							<caption>Query returned no row</caption>
						</table>
					<?php endif; ?>
					<?php
					$res = [ 'num_rows' => count($res_tokens) ];
					break;

				case 'token_insert_login' :
					$type = $arg2;
					if (empty($type)) {
						$type = Gl_game_tokens::TOKEN_TYPE_LOGIN;
					}
					$payload = [ 'player_id' => $player_id ];

					$res_ti = $this->gl_game_tokens->create_token($type, $payload);
					$res = [ 'result_token_insert' => $res_ti ];
					break;

				case 'rev_query' :
					$token = $arg1;
					if (empty($token)) {
						throw new Exception('token missing, 0x03');
					}
					$this->utils->debug_log(__METHOD__, [ 'token' => $token ]);
					$result = $this->gl_game_lib->get_login_creds_by_token($token);
					$res = [ 'result_rev_query' => $result ];
					break;

				case 'get_act_tokens' :
					$res_at = $this->gl_game_tokens->get_active_tokens_by_player_id($player_id);
					$res = [ 'active_tokens' => $res_at, 'player_id' => $player_id ];
					break;

				default :
					throw new Exception('Not implemented yet', 32766);
					break;

			} // End switch ($method)

			$ret = [
				'success'	=> true ,
				'code'		=> 0 ,
				'mesg'		=> 'Operation successful' ,
				'result'	=> $res ,
				'method'	=> $method
			];
		}
		catch (Exception $ex) {
			$ret = [
				'success'	=> false ,
				'code'		=> $ex->getCode() ,
				'mesg'		=> $ex->getMessage() ,
				'result'	=> $res ,
				'method'	=> $method
			];
		}
		finally {
			echo json_encode($ret);
		}
	} // End function glops()

	public function ole777ops($method = null, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null) {

		$this->load->library([ 'ole_reward_lib' ]);
		$this->load->model([ 'ole_reward_model' ]);
		$tm_start = microtime(1);
		try {
			$res = null;

			if ($this->utils->getConfig('marketing_gl777_disabled')) {
				throw new Exception('glops disabled', 32754);
			}

			$methods = json_encode([ 'discard', 'calc', 'list', 'remove' ]);

			if ($this->utils->getConfig('marketing_ole777_disabled') == true) {
				throw new Exception("ole777ops disabled", 32754);
			}

			if (empty($method)) {
				throw new Exception("Method not specified.  Supported methods: {$methods}", 32767);
			}

			switch ($method) {
				case 'discard' :
					$dw_from	= date('Ymd', strtotime($arg1));
					$dw_to		= date('Ymd', strtotime($arg2));
					$offset		= intval($arg3);
					if (empty($dw_from) || empty($dw_to)) {
						throw new Exception('Requires date_from and date_to', 0x11);
					}
					if (empty($offset)) {
						throw new Exception('Requires offset', 0x12);
					}

					$sres = $this->ole_reward_lib->wager_interval_shift($dw_from, $dw_to, $offset);

					$res = $sres;

					break;

				case 'remove' :
					$dw_from	= date('Ymd', strtotime($arg1));
					$dw_to		= date('Ymd', strtotime($arg2));
					$live		= ($arg3 == 'live');

					if (empty($dw_from) || empty($dw_to)) {
						throw new Exception('Requires date_from and date_to', 0x21);
					}

					$res = $this->ole_reward_lib->wager_interval_remove($dw_from, $dw_to, $live);

					break;

				case 'calc' :
					$dt_from	= strtotime($arg1);
					$dt_to		= strtotime($arg2);
					if (empty($dt_from) || empty($dt_to)) {
						throw new Exception('Requires date_from and date_to', 0x31);
					}

					for ($d = $dt_from; $d <= $dt_to; $d += 86400) {
						$calc_date = date('Y-m-d', $d);
						$cres = $this->ole_reward_lib->build_daily_wagerdata($calc_date);
						$num_wagers = count($cres[0]);
						$num_summaries = count($cres[1]);
						if ($num_wagers <= 0) { continue; }
						$this->utils->debug_log(__METHOD__, "Calculation complete", ['calc_date' => $calc_date, 'num_wager_records' => $num_wagers, 'num_summary_items' => $num_summaries]);
						$res[] =  [ 'calc_date' => $calc_date, 'num_wager_records' => $num_wagers, 'num_summary_items' => $num_summaries ];
					}

					break;

				case 'remote_list' :
					$raw = $this->ole_reward_model->remote_wager_list();
					$res = [];
					foreach ($raw as $key=>$row) {
						$res[] = sprintf("%-8s %3d", $row['Date'], $row['count']);
					}
					break;

				case 'remote_full' :
					$raw = $this->ole_reward_model->remote_wager_full();
						$res = [];
						foreach ($raw as $key=>$row) {
							$rst = '';
							foreach ($row as $col) {
								$rst .= sprintf("%12s", $col);
							}
							$res[] = $rst;
						}
					// $res = $raw;
					break;

				case 'wagers_sync' :
					$date_start	= date('Y-m-d', strtotime($arg1));
					$date_end	= date('Y-m-d', strtotime($arg2));
					$live		= ($arg3 == 'live');

					if (!$live) {
						$res = [
							"sync_date_from" => $date_start ,
							"sync_date_to" => $date_end ,
							"mesg" => 'Not in live mode, debug date settings only.'
						];
					}

					if ($live) {
						$res = $this->ole_reward_lib->wager_daily_update($date_start, $date_end);
					}

					break;

				case 'userinfo_sync' :
					$raw = $this->ole_reward_lib->userinfo_daily_update();
					$this->utils->debug_log(__METHOD__, "userinfo update raw output", $raw);
					if (isset($raw['mesg'])) {
						$res = [ 'mesg' => $raw['mesg']];
					}
					else {
						$num_ins = count($raw['inserted']);
						$num_upd = count($raw['updated']);
						// $json_ins = json_encode($raw['inserted']);
						// $json_upd = json_encode($raw['updated']);
						$this->utils->debug_log(__METHOD__, "Userinfo daily sync complete", ['num_inserted' => $num_ins, 'num_updated' => $num_upd]);
						$res = [
							'count_inserted' => $num_ins ,
							'count_updated' => $num_upd ,
							'inserted' => $raw['inserted'] ,
							'updated' => $raw['updated'] ,
						];
					}

					break;


				case 'list' : default :
					$res = $this->ole_reward_lib->wagers_sync_overview();
					foreach ($res as $key=>$grp) {
						$res["list_$key"] = [];
						foreach ($grp as $row) {
							if ($key == 'syncs') {
								$res["list_$key"][] = sprintf("%3d %-8s %3d %1d-%1d",
									$row['id'], $row['Date'], $row['ProductID'],
									// $row['count'],
									$row['confirmed'], $row['synced']);
							}
							else {
								$res["list_$key"][] = sprintf("%-8s %3d", $row['Date'], $row['count']);
							}
						}
						unset($res[$key]);
					}

					break;

			}

			$ret = [
				'success'	=> true ,
				'code'		=> 0 ,
				'mesg'		=> 'Operation successful' ,
				'result'	=> $res ,
				'method'	=> $method
			];
		}
		catch (Exception $ex) {
			$ret = [
				'success'	=> false ,
				'code'		=> $ex->getCode() ,
				'mesg'		=> $ex->getMessage() ,
				'result'	=> $res ,
				'method'	=> $method
			];
		}
		finally {
			$tm_elapsed = microtime(1) - $tm_start;
			$ret['time_used'] = sprintf('%.3f ms', $tm_elapsed * 1000);
			$retj = json_encode($ret, JSON_PRETTY_PRINT);
			echo "<pre>{$retj}</pre>";
		}
	}

} // end of marketing_bonus_game_module.php