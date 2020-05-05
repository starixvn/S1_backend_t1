<?php
/**
 * General behaviors include
 * * get player reports
 * * get agency player reports
 * * get agency agent reports
 * * get deposit withdrawal of a certain agents
 *
 * @category report_module_player
 * @version 1.8.10
 * @copyright 2013-2016 TripleOneTech
 */
trait report_module_player {

	/**
	 * detail: get player reports
	 *
	 * @param array $request
	 * @param Boolean $viewPlayerInfoPerm
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function player_reports($request, $viewPlayerInfoPerm, $is_export = false) {
		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(array('risk_score_model','player_kyc','kyc_status_model','agency_model'));
		$this->load->helper(['player_helper']);
		$game_apis_map = $this->utils->getAllSystemMap();


		$risk_score_model = $this->risk_score_model;
		$player_kyc = $this->player_kyc;
		$kyc_status_model = $this->kyc_status_model;


		$this->data_tables->is_export = $is_export;
		$risk_score_model = $this->risk_score_model;

		$this->load->model(array('transactions'));
		$i = 0;
		$input = $this->data_tables->extra_search($request);


    	$table = 'player';
		$joins = array();
		$where = array();
		$values = array();
		$group_by = array();
		$having = array();

		$joins['playerdetails'] = 'playerdetails.playerId = player.playerId';
		$joins['player_runtime'] = 'player_runtime.playerId = player.playerId';
		$joins['transactions'] = 'transactions.to_id = player.playerId';
		$joins['affiliates'] = 'affiliates.affiliateId = player.affiliateId';

		$date_from = null;
		$date_to = null;
		$playerlvl = null;
		$playerlvls = null;
		if (isset($input['date_from'], $input['date_to'])) {
			$date_from = $input['date_from'];
			$date_to = $input['date_to'];
		}

        $daterange = ' ';
		$daterange_hour = ' ';
		$secondndeposit_range = ' ';

		if (!empty($date_from) && !empty($date_to)) {
			#for transactions
			$daterange .= " AND transactions.created_at >=  '{$date_from}' AND transactions.created_at <= '{$date_to}'";
            #for total_player_game_hour
			$date_from_hour = (new DateTime($date_from))->format('YmdH');
			$date_to_hour = (new DateTime($date_to))->format('YmdH');
			$daterange_hour = " AND date_hour >=  {$date_from_hour} AND date_hour <=  {$date_to_hour}";
		}

		# FILTER ######################################################################################################################################################################################
		$where[] = 'transactions.to_type = ? AND transactions.status = ?';
		$values[] = Transactions::PLAYER;
		$values[] = Transactions::APPROVED;

		$show_username = true;
		$show_realname = true;
		$show_tag = true;
		$show_risk_level = true;
		$show_kyc_level = true;
		$show_player_level = true;
		$show_affiliate = true;
		$show_agent= true;
		$show_email =true;
		$show_phone = true;
		$show_registered_by = true;
		$show_registered_ip = true;
		$show_last_login_ip = true;
		$show_last_login_date = true;
		$show_last_logout_date = true;
		$show_register_date = true;
		$show_gender = true;
		$show_deposit_bonus=true;
		$show_total_bonus = true;
		$show_cashback_bonus = true;
		$show_referral_bonus = true;
		$show_manual_bonus = true;
		$show_subtract_bonus = true;
		$show_total_bonus = true;
		$show_first_deposit = true;
		$show_2nd_deposit = true;
		$show_total_deposit = true;
		$show_total_deposit_times = true;
		$show_total_withdrawal = true;
		$show_total_dw = true;
		$show_total_bets = true;
		$show_total_payout = true;
		$show_payout_rate = true;
		$show_total_revenue = true;

		if (isset($input['group_by'])) {
			switch ($input['group_by']) {
				case 'player_id':
					$group_by[] = 'player.playerId';
					break;

				case 'playerlevel':
					$group_by[] = 'player.levelId';
					$show_username = false;
					$show_realname = false;
					$show_tag = false;
					$show_risk_level = false;
					$show_kyc_level =false;
					$show_player_level = true;
					$show_affiliate = false;
					$show_agent= false;
					$show_email = false;
					$show_phone = false;
					$show_registered_by = false;
					$show_registered_ip = false;
					$show_last_login_ip = false;
					$show_last_login_date = false;
					$show_last_logout_date = false;
					$show_register_date = false;
					$show_gender =false;


					break;
				case 'affiliate_id':
					$group_by[] = 'player.affiliateId';
					$show_username = false;
					$show_realname = false;
					$show_tag = false;
					$show_risk_level = false;
					$show_kyc_level =false;
					$show_player_level = false;
					$show_affiliate = true;
					$show_agent= false;
					$show_email = false;
					$show_phone = false;
					$show_registered_by = false;
					$show_registered_ip = false;
					$show_last_login_ip = false;
					$show_last_login_date = false;
					$show_last_logout_date = false;
					$show_register_date = false;
					$show_gender = false;
					break;

			    case 'agent_id':
					$group_by[] = 'player.agent_id';
					$show_username = false;
					$show_realname = false;
					$show_tag = false;
					$show_risk_level = false;
					$show_kyc_level =false;
					$show_player_level = false;
					$show_affiliate = false;
					$show_agent= true;
					$show_email = false;
					$show_phone = false;
					$show_registered_by = false;
					$show_registered_ip = false;
					$show_last_login_ip = false;
					$show_last_login_date = false;
					$show_last_logout_date = false;
					$show_register_date = false;
					$show_gender = false;

					break;

				default:
					//
				break;
			}
		}

		if (isset($input['date_from'], $input['date_to'])) {
			$where[] = "transactions.created_at BETWEEN ? AND ?";
			$values[] = $input['date_from'];
			$values[] = $input['date_to'];
		}

		if (isset($input['username'])) {
			$where[] = "player.username LIKE ?";
			$values[] = '%' . $input['username'] . '%';
		}

		if (isset($input['referrer'])) {
			$refereePlayerId = $this->player_model->getPlayerIdByUsername($input['referrer']) ? : -1;
			$where[] = "player.refereePlayerId = ?";
			$values[] = $refereePlayerId;
		}

		if (isset($input['playerlevel'])) {
			$where[] = "player.levelId = ?";
			$values[] = $input['playerlevel'];
		}

		if (isset($input['depamt1'])) {
			$having['total_deposit <='] = $input['depamt1'];
		}

		if (isset($input['depamt2'])) {
			$having['total_deposit >='] = $input['depamt2'];
		}

		if (isset($input['widamt1'])) {
			$having['total_withdrawal <='] = $input['widamt1'];
		}

		if (isset($input['widamt2'])) {
			$having['total_withdrawal >='] = $input['widamt2'];
		}

		if (isset($input['only_under_agency']) && $input['only_under_agency'] != '') {
			$where[] = "player.agent_id IS NOT NULL";
			if (!isset($input['agent_name'])) {
				if (isset($input['current_agent_name']) && $input['current_agent_name'] != '') {
					$input['agent_name'] = $input['current_agent_name'];
				}
			}
		}

		if (isset($input['agent_name'])) {
			$this->load->model(array('agency_model'));
			$agent_detail = $this->agency_model->get_agent_by_name($input['agent_name']);
			$this->utils->debug_log('agent_detail', $agent_detail);

			if (isset($input['include_all_downlines']) && $input['include_all_downlines'] == true) {
				$joins['agency_agents'] = 'player.agent_id = agency_agents.agent_id';
				$parent_ids = array($agent_detail['agent_id']);
				$sub_ids = array();
				$all_ids = $parent_ids;
				while (!empty($sub_ids = $this->agency_model->get_sub_agent_ids_by_parent_id($parent_ids))) {
					$this->utils->debug_log('sub_ids', $sub_ids);
					$all_ids = array_merge($all_ids, $sub_ids);
					$parent_ids = $sub_ids;
					$sub_ids = array();
				}
				foreach ($all_ids as $i => $id) {
					if ($i == 0) {
						$w = "(player.agent_id = ?";
					} else {
						$w .= " OR player.agent_id = ?";
					}
					$values[] = $id;
				}
				$w .= ")";
				$where[] = $w;
			} else {
				$where[] = "player.agent_id = ?";
				$values[] = $agent_detail['agent_id'];
			}
		}

		if (isset($input['affiliate_name'])) {

			$this->load->model('affiliatemodel');
			$affiliateId = $this->affiliatemodel->getAffiliateIdByUsername($input['affiliate_name']);
			@$this->utils->debug_log('affiliateId', $$affiliateId);
			$affiliateIds = null;

			if (isset($input['aff_include_all_downlines']) && $input['aff_include_all_downlines'] && !empty($affiliateId)) {
				$affiliateIds = $this->affiliatemodel->includeAllDownlineAffiliateIds($affiliateId);
			}

			if (!empty($affiliateIds)) {
				$where[] = 'player.affiliateId IN(' . implode(',', $affiliateIds) . ')';
			} else {
				$where[] = "affiliates.username like ?";
				$values[] = '%' . $input['affiliate_name'] . '%';
			}
		}

		$affiliatesTagMap = [];

		if (isset($input['affiliate_tags'])) {
			$this->load->model(array('affiliatemodel','affiliate'));

			$affiliatesTagMap = $this->affiliate->getAffTagsMap();

			$this->utils->debug_log('affiliatesTagMap1', $affiliatesTagMap);

			$joins['affiliatetag'] = 'affiliatetag.affiliateId=affiliates.affiliateId';
			$joins['affiliatetaglist'] = 'affiliatetaglist.tagId=affiliatetag.tagId';
			$where[] = "affiliatetag.tagId = ?";
			$values[] = $input['affiliate_tags'];
		}

		if (isset($input['player_tag'])) {
			$joins['playertag'] = 'playertag.playerId = player.playerId';
			$where[] = "playertag.tagId = ?";
			$values[] = $input['player_tag'];
		}

		if (isset($input['affiliate_agent'])) {
			switch ($input['affiliate_agent']) {
				case '1': # Not under any Affiliate or Agent
					$where[] = '(player.agent_id IS NULL OR player.agent_id = 0) AND (player.affiliateId IS NULL OR player.affiliateId = 0)';
					break;

				case '2': # Under Affiliate Only
					$where[] = '(player.agent_id IS NULL OR player.agent_id = 0) AND player.affiliateId > 0';
					break;

				case '3': # Under Agent Only
					$where[] = 'player.agent_id > 0 AND (player.affiliateId IS NULL OR player.affiliateId = 0)';
					break;

				case '4': # Under Affiliate or Agent
					$where[] = '(player.agent_id > 0 OR player.affiliateId > 0)';
					break;
			}
		}

		$where[] = "player.deleted_at IS NULL";

    	$subtotals = ['subtotals_deposit_bonus'=> 0 , 'subtotals_cashback_bonus' => 0, 'subtotals_referral_bonus' => 0, 'subtotals_manual_bonus'=>0, 'subtotals_subtract_bonus' => 0, 'subtotals_total_bonus'=> 0, 'subtotals_first_deposit' => 0, 'subtotals_second_deposit' => 0,'subtotals_total_deposit' => 0, 'subtotals_total_deposit_times' => 0,'subtotals_total_withdrawal'=> 0,'subtotals_total_bets' => 0, 'subtotals_total_payout'=> 0, 'subtotals_total_dw'=> 0, 'subtotals_payout_rate' => 0];
    	$total = ['total_first_deposit' =>0, 'total_second_deposit'=> 0, 'total_bets_add' => 0, 'total_payout_add' => 0, 'total_payout_rate' => 0, 'total_player_id' => '', 'total_player_levelId' => ''];

		# DEFINE TABLE COLUMNS ######################################################################################
		$columns = array(
			array(
				'alias' => 'playerId',
				'name' => 'playerId',
				'select' => 'player.playerId',
			),
			array(
				'alias' => 'affiliateId',
	     		'select' => 'affiliates.affiliateId',
				'formatter' => 'defaultFormatter',
			),
			array(
				'alias' => 'levelId',
	     		'select' => 'player.levelId',
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'username',
				'name' => lang('report.pr01'),
				'select' => $show_username ? '( CASE WHEN affiliates.username IS NULL THEN player.username ELSE CONCAT(player.username, \' (\', affiliates.username,  \')\' ) END )' : '"N/A"',
				'formatter' => function ($d, $row) use ($is_export, $date_from, $date_to, $show_username) {

					if($show_username){
						if ($is_export) {
							return $d;
						} else {
							$date_qry = '';
							if (!empty($date_from) && !empty($date_to)) {
								$date = new DateTime($date_from);
								$date_qry = '&date_from=' . $date->format('Y-m-d') . '&hour_from=' . $date->format('H');

								$date = new DateTime($date_to);
								$date_qry .= '&date_to=' . $date->format('Y-m-d') . '&hour_to=' . $date->format('H');
							}
							return "<a href='/report_management/viewGamesReport?username={$d}{$date_qry}'>{$d}</a>";
						}
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'real_name',
				'name' => lang('report.pr02'),
				'select' => 'CONCAT(ifnull(playerdetails.firstName,""), \' \', ifnull(playerdetails.lastName,"") )',
				'formatter' => 'defaultFormatter',
				'formatter' => function ($d, $row) use ($is_export, $show_realname) {
					if($show_realname){
						return $d;
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
    			},
			),
			array(
				'dt' => $i++,
				'alias' => 'tagName',
				'select' => 'player.playerId',
				'name' => lang("player.41"),
				'formatter' => function ($d, $row) use ($is_export, $show_tag) {
					if($show_tag){
						return player_tagged_list($row['playerId'], $is_export);
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
    			},
     		),
			array(
				'dt' => $i++,
				'alias' => 'risk_level',
				'name' => lang("Risk Level/Score"),
				'select' => 'playerdetails.playerId',
				'formatter' => function ($d) use ($is_export,$risk_score_model,$show_risk_level) {
					if($show_risk_level){
						if ($is_export) {
							return $risk_score_model->getPlayerCurrentRiskLevel($d) && $risk_score_model->generate_total_risk_score($d) ? $risk_score_model->getPlayerCurrentRiskLevel($d) .' / '. $risk_score_model->generate_total_risk_score($d) : lang('lang.norecyet');
						}else{
							return $risk_score_model->getPlayerCurrentRiskLevel($d) && $risk_score_model->generate_total_risk_score($d) ? $risk_score_model->getPlayerCurrentRiskLevel($d) .' / '. $risk_score_model->generate_total_risk_score($d) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'kyc_level',
				'name' => lang("KYC Level/Rate Code"),
				'select' => 'playerdetails.playerId',
				'formatter' => function ($d) use ($is_export,$player_kyc,$kyc_status_model,$show_kyc_level) {
					if($show_kyc_level){
						if ($is_export) {
							return $player_kyc->getPlayerCurrentKycLevel($d) && $kyc_status_model->getPlayerCurrentStatus($d) ? $player_kyc->getPlayerCurrentKycLevel($d) .' / '. $kyc_status_model->getPlayerCurrentStatus($d): lang('lang.norecyet');
						}else{
							return $player_kyc->getPlayerCurrentKycLevel($d) && $kyc_status_model->getPlayerCurrentStatus($d) ? $player_kyc->getPlayerCurrentKycLevel($d) .' / '. $kyc_status_model->getPlayerCurrentStatus($d): '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			),
			array(
				'dt' => $i++,
				'name' => lang('report.pr03'),
				'alias' => 'member_level',
				'select' => 'player.levelName',
				'formatter' => function ($d, $row) use ($show_player_level, $is_export) {
					if(($show_player_level)){
			            $getUpdatedGroupAndLevel = $this->player_model->getPlayerCurrentLevel($row['playerId']);
			            if($getUpdatedGroupAndLevel){
			                $groupName = lang($getUpdatedGroupAndLevel[0]['groupName']);
			                $levelName = lang($getUpdatedGroupAndLevel[0]['vipLevelName']);
			            	return $groupName . ' - ' .$levelName;
			            }
			            else{
			            	return null;
			            }
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'affiliates_name',
				'name' => lang('Affiliate'),
				'select' => 'affiliates.username',
				'formatter' => function($d,$row) use ($is_export, $show_affiliate, $input, $affiliatesTagMap){
					if($show_affiliate){
						$url = site_url('/affiliate_management/userInformation/' . $row['affiliateId']);
						$name = '';
						if($is_export){
							$name = !empty($d) ? $d :  lang('lang.norecyet') ;
						}else{
							if(!empty($d)){
								if(isset($input['affiliate_tags'])){
									if(isset($affiliatesTagMap[$input['affiliate_tags']])){
										$name = '<span class="badge badge-info" style="float:right;">'.$affiliatesTagMap[$input['affiliate_tags']].'</span>';
									}else{
										$name ='';
									}
								}
								$name .= '<a href="' . $url . '">' . $d . '</a> ';

							}else{
								$name = '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
							}
						}
						return $name;
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'agent',
				'select' => 'player.agent_id',
				'name' => lang("Agent"),
				'formatter' => function ($d) use ($is_export,$show_agent) {
					if($show_agent){
						$name = '';
						if ($d != null) {
							$agent_details = $this->agency_model->get_agent_by_id($d);
							$name = $agent_details['agent_name'];
							$url = site_url('/agency_management/agent_information/' . $d);
							if($is_export){
								$name =  $name;
							}else{
								$name = '<a href="' . $url . '">' . $name . '</a>';
							}
						} else {
							if ($is_export) {
								return trim(trim($d), ',') ?: lang('lang.norecyet');
							} else {
								return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
							}
						}
						return $name;
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'email',
				'name' => lang('report.pr04'),
				'select' => 'player.email',
				'formatter' => function ($d) use ($viewPlayerInfoPerm, $is_export, $show_email) {
					if($show_email){
						$str = $d;
					//no permission then show *, hide real content
						if (!$viewPlayerInfoPerm) {
							$str = $this->data_tables->defaultFormatter(preg_replace('#.#', '*', $d));
							if (!$is_export) {
								$str = '<span title="' . lang('con.aff01') . '">' . $str . '</span>';
							}
						}
						return $str;
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'contactNumber',
				'select' => 'playerdetails.contactNumber',
				'name' => lang('aff.ai15'),
				'formatter' => function ($d) use ($viewPlayerInfoPerm, $is_export, $show_phone) {
					if($show_phone){
						$str = $d;
						if (!$str && !$is_export) {
							return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}
					//no permission then show *, hide real content
						if (!$viewPlayerInfoPerm) {
							$str = $this->data_tables->defaultFormatter(preg_replace('#.#', '*', $d));
							if (!$is_export) {
								$str = '<span title="' . lang('con.aff01') . '">' . $str . '</span>';
							}
						}
						return $str;
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'registered_by',
				'select' => 'player.registered_by',
				'name' => lang('report.pr05'),
				'formatter' => function ($d) use ($show_registered_by, $is_export){
					if($show_registered_by){
						$reg_by = lang("player.69");
						switch (strtolower($d)) {
							case 'mobile':		$reg_by = lang("sys.item4");		break;
							case 'website':		$reg_by = lang("player.68");		break;
							default:			$reg_by = lang("player.69");		break;
						}
						return $reg_by;
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
    		),
			array(
				'dt' => $i++,
				'name' => lang('report.pr06'),
				'alias' => 'registrationIP',
				'select' => 'playerdetails.registrationIP',
				'formatter' => 'defaultFormatter',
				'formatter' =>  function ($d) use ($show_registered_ip, $is_export){
					if($show_registered_ip){
						return $d;
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
			),
			array(
				'dt' => $i++,
				'alias' => 'lastLoginIp',
				'name' => lang('report.pr07'),
				'select' => 'player_runtime.lastLoginIp',
				'formatter' =>  function ($d) use ($show_last_login_ip, $is_export){
					if($show_last_login_ip){
						return $d;
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
    		),
			array(
				'dt' => $i++,
				'alias' => 'lastLoginTime',
				'name' => lang('report.pr08'),
				'select' => 'player_runtime.lastLoginTime',
				'formatter' =>  function ($d) use ($show_last_login_date, $is_export){
					if($show_last_login_date){
						return $d;
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
			),
			array(
				'dt' => $i++,
				'alias' => 'lastLogoutTime',
				'name' => lang('report.pr09'),
				'player_runtime.lastLogoutTime',
				'formatter' =>  function ($d) use ($show_last_logout_date, $is_export){
					if($show_last_logout_date){
						return $d;
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
			),
			array(
				'dt' => $i++,
				'alias' => 'createdOn',
				'name' => lang('report.pr10'),
				'select' => 'player.createdOn',
				'formatter' =>  function ($d) use ($show_register_date, $is_export){
					if($show_register_date){
						return $d;
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
			),
			array(
				'dt' => $i++,
				'alias' => 'gender',
				'name' => lang('report.pr11'),
				'select' => 'playerdetails.gender',
				'formatter' =>  function ($d) use ($show_gender, $is_export){
					if($show_gender){
						return $d;
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
			),
			array(
				'dt' => $i++,
				'alias' => 'cashback',
				'name' => lang('report.sum15'),
				'select' => 'SUM(CASE WHEN transactions.transaction_type = ' . Transactions::AUTO_ADD_CASHBACK_TO_BALANCE . ' THEN transactions.amount ELSE 0 END)',
				'formatter' =>  function ($d) use ($show_cashback_bonus, $is_export, &$subtotals){
					if($show_cashback_bonus){
						$subtotals['subtotals_cashback_bonus'] += $d;
						return $this->data_tables->currencyFormatter($d);
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
			),
			array(
				'dt' => $i++,
				'alias' => 'deposit_bonus',
				'name' => lang('report.pr15'),
				'select' => 'SUM(CASE WHEN transactions.transaction_type = ' . Transactions::MEMBER_GROUP_DEPOSIT_BONUS . ' THEN transactions.amount ELSE 0 END)',
				'formatter' =>  function ($d) use ($show_deposit_bonus, $is_export, &$subtotals){
					if($show_deposit_bonus){
						$subtotals['subtotals_deposit_bonus'] += $d;
						return $this->data_tables->currencyFormatter($d);
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
			),
			array(
				'dt' => $i++,
				'alias' => 'referral_bonus',
				'name' => lang('report.pr17'),
				'select' => 'SUM(CASE WHEN transactions.transaction_type = ' . Transactions::PLAYER_REFER_BONUS . ' THEN transactions.amount ELSE 0 END)',
				'formatter' =>  function ($d) use ($show_referral_bonus, &$subtotals){
					if($show_referral_bonus){
						$subtotals['subtotals_referral_bonus'] += $d;
						return $this->data_tables->currencyFormatter($d);
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
    		),
			array(
				'dt' => $i++,
				'alias' => 'manual_bonus',
				'name' => lang('transaction.manual_bonus'),
				'select' => 'SUM(CASE WHEN transactions.transaction_type = ' . Transactions::ADD_BONUS . ' THEN transactions.amount ELSE 0 END)',
				'formatter' =>  function ($d) use ($show_manual_bonus, $is_export, &$subtotals){
					if($show_manual_bonus){
						$subtotals['subtotals_manual_bonus'] += $d;
						return $this->data_tables->currencyFormatter($d);
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
     		),
     		array(
				'dt' => $i++,
				'alias' => 'subtract_bonus',
				'name' => lang('transaction.transaction.type.10'),
				'select' => 'SUM(CASE WHEN transactions.transaction_type = ' . Transactions::SUBTRACT_BONUS . ' THEN transactions.amount ELSE 0 END)',
				'formatter' =>  function ($d) use ($show_subtract_bonus, $is_export, &$subtotals){
					if($show_subtract_bonus){
						$subtotals['subtotals_subtract_bonus'] -= $d;
						return $this->data_tables->currencyFormatter($d);
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
     		),
			array(
				'dt' => $i++,
				'alias' => 'total_bonus',
				'name' => lang('report.pr18'),
				'select' => 'SUM(CASE WHEN transactions.transaction_type IN (' . implode(',', array(Transactions::MEMBER_GROUP_DEPOSIT_BONUS, Transactions::PLAYER_REFER_BONUS, Transactions::ADD_BONUS)) . ') THEN transactions.amount ELSE 0 END)',
				'formatter' =>  function ($d,$row) use ($show_total_bonus, $is_export, &$subtotals){
					if($show_total_bonus){
						$subtotals['subtotals_total_bonus'] += $d;
						return $this->data_tables->currencyFormatter($d - $row['subtract_bonus']);
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
			),
			array(
				'dt' => $i++,
				'alias' => 'first_deposit',
				'select' => 'transactions.id',
				'name' => lang('report.pr19'),
				'formatter' => function ($d, $row) use ($readOnlyDB,$daterange, $show_first_deposit, $is_export, &$subtotals) {
                    if($show_first_deposit){
                    	$query = $readOnlyDB->query("SELECT transactions.amount FROM transactions JOIN player ON player.playerId = transactions.to_id WHERE transactions.to_type = ?  AND transactions.transaction_type = ? AND transactions.status = ?  AND transactions.to_id  = ".$row['playerId']."  AND player.deleted_at IS NULL ORDER BY created_at ASC LIMIT ?,1", array(
						Transactions::PLAYER,
						//$d,
						Transactions::DEPOSIT,
						Transactions::APPROVED,
						0, # FIRST DEPOSIT
					));
					$row = $query->row_array();
					$subtotals['subtotals_first_deposit'] += isset($row['amount']) ? $row['amount'] : 0;
					return $this->data_tables->currencyFormatter(isset($row['amount']) ? $row['amount'] : 0);
                    }else{
                    	if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'second_deposit',
				'select' => 'player.playerId',
				'name' => lang('report.pr20'),
				'formatter' => function ($d, $row) use ($readOnlyDB,$daterange, $show_2nd_deposit, $is_export, &$subtotals) {
					if($show_2nd_deposit){
						$query = $readOnlyDB->query("SELECT transactions.amount FROM transactions JOIN player ON player.playerId = transactions.to_id WHERE transactions.to_type = ?  AND transactions.transaction_type = ? AND transactions.status = ?  AND transactions.to_id  = ".$row['playerId']."  AND player.deleted_at IS NULL ORDER BY created_at ASC LIMIT ?,1", array(
							Transactions::PLAYER,
						//$d,
							Transactions::DEPOSIT,
							Transactions::APPROVED,
						1, # FIRST DEPOSIT
					));

						$row = $query->row_array();
						$subtotals['subtotals_second_deposit'] += isset($row['amount']) ? $row['amount'] : 0;
						return $this->data_tables->currencyFormatter(isset($row['amount']) ? $row['amount'] : 0);

					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
    		),
			array(
				'dt' => $i++,
				'alias' => 'total_deposit',
				'name' => lang('report.pr21'),
				'select' => 'SUM(CASE WHEN transactions.transaction_type IN (' . Transactions::DEPOSIT . ') THEN transactions.amount ELSE 0 END)',
				'formatter' =>  function ($d) use ($show_total_deposit, $is_export, &$subtotals){
					if($show_total_deposit){
						$subtotals['subtotals_total_deposit'] += $d;
						return $this->data_tables->currencyFormatter($d);
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
			),
            array(
                'dt' => $i++,
                'alias' => 'total_deposit_times',
                'name' => lang('yuanbao.deposit.times'),
                'select' => 'COUNT(CASE WHEN transactions.transaction_type IN (' . Transactions::DEPOSIT . ') THEN 1 END)',
                'formatter' =>  function ($d) use ($show_total_deposit_times, $is_export, &$subtotals){
					if($show_total_deposit_times){
						$subtotals['subtotals_total_deposit_times'] += $d;
						return $d;
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
            ),
			array(
				'dt' => $i++,
				'alias' => 'total_withdrawal',
				'name' => lang('report.pr22'),
				'select' => 'SUM(CASE WHEN transactions.transaction_type IN (' . implode(',', array(Transactions::WITHDRAWAL)) . ') THEN transactions.amount ELSE 0 END)',
				'formatter' =>  function ($d) use ($show_total_withdrawal, $is_export, &$subtotals){
					if($show_total_withdrawal ){
						$subtotals['subtotals_total_withdrawal'] += $d;
						return $this->data_tables->currencyFormatter($d);
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
			),
			array(
				'dt' => $i++,
				'alias' => 'total_dw',
				'name' => lang('Deposit - Withdraw'),
			    'select' =>'SUM(CASE WHEN transactions.transaction_type IN (' . implode(',', array(Transactions::DEPOSIT)) . ') THEN transactions.amount ELSE 0 END) - SUM(CASE WHEN transactions.transaction_type IN (' . implode(',', array(Transactions::WITHDRAWAL)) . ') THEN transactions.amount ELSE 0 END)',
				'formatter' =>  function ($d) use ($show_total_dw, $is_export, &$subtotals){
					if($show_total_dw){
						$subtotals['subtotals_total_dw'] += $d;
						return $this->data_tables->currencyFormatter($d);
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}
			),
			array(
				'dt' => $i++,
				'alias' => 'total_bets',
				'name' => lang('cms.totalbets'),
				'select' => 'player.playerId',
				'formatter' => function ($d, $row) use ($readOnlyDB,$daterange_hour, $show_total_bets, $is_export, &$subtotals, $input) {
					if($show_total_bets){
							$queryString = "SELECT SUM(betting_amount) AS total_bets FROM total_player_game_hour WHERE player_id = ".$d."  ".$daterange_hour."";
							# overrive query if group by player level
							if (isset($input['group_by']) && strtolower($input['group_by']) == "playerlevel") {
								$queryString = "SELECT SUM(betting_amount) AS total_bets FROM total_player_game_hour WHERE player_id IN (SELECT playerId FROM player where levelId = ".$row['levelId'].")  ".$daterange_hour;
							}

							$query = $readOnlyDB->query($queryString);
							$row = $query->row_array();
							$subtotals['subtotals_total_bets'] += isset($row['total_bets']) ? $row['total_bets'] : 0 ;
							return $this->data_tables->currencyFormatter(isset($row['total_bets']) ? $row['total_bets'] : 0);

					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}

				},
			),
			array(
				'dt' => $i++,
				'alias' => 'total_payout',
				'name' => lang('Payout'),
				'select' => 'player.playerId',
				'formatter' => function ($d, $row) use ($readOnlyDB,$daterange_hour,$show_total_payout, $is_export, &$subtotals, $input) {
					if($show_total_payout){
						$queryString = "SELECT SUM(loss_amount - win_amount) AS total_payout FROM total_player_game_hour WHERE player_id = ".$d."  ".$daterange_hour."";
						# overrive query if group by player level
						if (isset($input['group_by']) && strtolower($input['group_by']) == "playerlevel") {
							$queryString = "SELECT SUM(loss_amount - win_amount) AS total_payout FROM total_player_game_hour WHERE player_id in (SELECT playerId FROM player where levelId = ".$row['levelId'].") ". $daterange_hour;
						}

						// return $queryString;
						$query = $readOnlyDB->query($queryString);
						$row = $query->row_array();
						$subtotals['subtotals_total_payout'] += isset($row['total_payout']) ? $row['total_payout'] : 0 ;
						return $this->data_tables->currencyFormatter(isset($row['total_payout']) ? $row['total_payout'] : 0);
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'payout_rate',
				'name' => lang('sys.payoutrate'),
				'select' => 'player.playerId',
				'formatter' => function ($d, $r) use ($readOnlyDB,$daterange_hour,$show_payout_rate, $is_export, $input) {
					if($show_payout_rate){
						$queryString = "SELECT SUM(loss_amount - win_amount)/SUM(betting_amount) AS payout_rate FROM total_player_game_hour WHERE player_id = ".$d."  ".$daterange_hour."     GROUP BY player_id ";
						# overrive query if group by player level
						if (isset($input['group_by']) && strtolower($input['group_by']) == "playerlevel") {
							$queryString = "SELECT SUM(loss_amount - win_amount)/SUM(betting_amount) AS payout_rate FROM total_player_game_hour WHERE player_id in (SELECT playerId FROM player where levelId = ".$r['levelId'].") ".$daterange_hour;
						}

						$query = $readOnlyDB->query($queryString);
						$row = $query->row_array();
						return $this->data_tables->percentageFormatter(isset($row['payout_rate']) ? $row['payout_rate'] : '0');
					}else{
						if($is_export){
							return lang('lang.norecyet');
						}
						return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'game_bet_details',
				'name' => lang('Bet Detail'),
				'select' => 'player.playerId',
				'formatter' => function ($d, $row) use ($readOnlyDB,$daterange_hour,$game_apis_map, $is_export, $input) {
					$queryString = "
						SELECT game_platform_id AS game,
							   SUM(betting_amount) AS bet,
							   SUM(loss_amount - win_amount) AS payout,
							   SUM(loss_amount - win_amount)/SUM(betting_amount) AS payout_rate
						FROM total_player_game_hour
						WHERE player_id = ".$d." ".$daterange_hour."
						GROUP BY game_platform_id";
					# overrive query if group by player level
					if (isset($input['group_by']) && strtolower($input['group_by']) == "playerlevel") {
						$queryString = "
							SELECT game_platform_id AS game,
							       SUM(betting_amount) AS bet,
							       SUM(loss_amount - win_amount) AS payout,
							       SUM(loss_amount - win_amount)/SUM(betting_amount) AS payout_rate
							FROM total_player_game_hour
							WHERE player_id IN (
								SELECT playerId
								FROM player
								WHERE levelId = ".$row['levelId'].") ".$daterange_hour."
								GROUP BY game_platform_id";
					}

					$query = $readOnlyDB->query($queryString);
					$bet_details=$query->result_array();

					$total_bets_ = 0;
					$total_payout_ = 0;
					$total_rate_ = 0;

					if(!empty($bet_details)){
						if(!$is_export){
							$bet_details_str = '<table  border="1" style="width:500px;">';
							$bet_details_str .=  '<tr style="padding:2px;">';
							$bet_details_str .=  '<th class="text-center" style="width:129px;"> '.lang('Game Platform').' </th><th class="text-center" style="width:105px;" > '.lang('Bets').' </th><th class="text-center" style="width:105px;"  > '.lang('report.Payout').' </th><th class="text-center" style="width:105px;"> '.lang('sys.payoutrate').'</th>';
							$bet_details_str .=  '</tr>';

							foreach ($bet_details as $v) {
								$total_bets_ += $v['bet'];
								$total_payout_ += $v['payout'];
								$bet_details_str .=  '<tr>';

								if(array_key_exists($v['game'], $game_apis_map)){
									$bet_details_str .= '<td style="text-align:right;padding:5px;width:50px;font-weight:bold;"><span class="text-success">'.$game_apis_map[$v['game']].'</span> </td>';
								}else{
									$bet_details_str .= '<td style="text-align:right;padding:5px;width:50px;"><span class="text-danger">'.$v['game'].'</span> </td>';
								}
								$bet_details_str .= '<td style="text-align:right;padding:5px;"> <span>'.$this->data_tables->currencyFormatter($v['bet']).'</span></td>';
								$bet_details_str .= '<td style="text-align:right;padding:5px;"> <span>'.$this->data_tables->currencyFormatter($v['payout']).'</span></td>';
								$bet_details_str .= '<td style="text-align:right;padding:5px;"> <span>'.$this->data_tables->currencyFormatter($v['payout_rate']*100).'%</span></td>';
								$bet_details_str .=  '</tr>';
							}

							$bet_details_str .=  '<tr>';
							$bet_details_str .=  '<td style="text-align:right;padding:5px;font-weight:bold;"><span class="text-primary">Total</span> </td> ';
							$bet_details_str .=  '<td style="text-align:right;padding:5px;font-weight:bold;"><span class="text-primary">'.$this->data_tables->currencyFormatter($total_bets_).'</span> </td> ';
							$bet_details_str .=  '<td style="text-align:right;padding:5px;font-weight:bold;"><span class="text-primary">'.$this->data_tables->currencyFormatter($total_payout_).'</span> </td> ';
							$bet_details_str .=  '<td style="text-align:right;padding:5px;font-weight:bold;"><span class="text-primary">'.@$this->data_tables->currencyFormatter(($total_payout_/$total_bets_)*100).'%</span> </td> ';
							$bet_details_str .=  '</tr>';

							return $bet_details_str;
						} else {
                            $details = [];
							foreach ($bet_details as $v) {
								$total_bets_ += $v['bet'];
								$total_payout_ += $v['payout'];
								$total_rate_ += $v['payout_rate'];

								$game =  (array_key_exists($v['game'], $game_apis_map)) ? $game_apis_map[$v['game']] : $v['game'];

								$game_row = array( 'Game Platform' => $game,
									'Bets' => $this->data_tables->currencyFormatter($v['bet']),
							    	'Payout'=> $this->data_tables->currencyFormatter($v['payout']),
		                        	'Payout_rate' => $this->data_tables->currencyFormatter($v['payout_rate']*100).'%'
		                        );

		                       $total_payout_rate_ = @$this->data_tables->currencyFormatter(($total_payout_/$total_bets_)*100);
		                        array_push($details, $game_row);
                            }
                            return json_encode(array('total_bets' => $total_bets_,'total_payout' => $total_payout_, 'total_payout_rate'=> $total_payout_rate_, 'details' => $details));
                        }
					}else{
						return "-";
					}
				},
			),
		);

		# OUTPUT #####################################################################################################################
		#SYSTEM FEATURES
		$columnsToDisplayOrNot = array(
			'risk_level' => array('field'=>'risk_level','showtThis' => 'yes',  'feature' => 'show_risk_score'),
			'kyc_level' => array('field'=>'kyc_level','showtThis' => 'yes', 'feature' => 'show_kyc_status')
		);

	    #CONTROL THIS WHEN USING SBE LOTTERY
		if(!$this->utils->isEnabledFeature('close_aff_and_agent')){
			$columnsToDisplayOrNot['affiliates_name'] = array('field'=>'affiliates_name','showtThis' => 'yes', 'feature' => 'show_search_affiliate');
			// $columnsToDisplayOrNot['agent'] = array('field'=>'agent','showtThis' => 'yes',  'feature' => 'agency');
		}else{
			$columnsToDisplayOrNot['affiliates_name'] = array('field'=>'affiliates_name','showtThis' => 'no', 'feature' => 'show_search_affiliate');
			// $columnsToDisplayOrNot['agent'] = array('field'=>'agent','showtThis' => 'no',  'feature' => 'agency');
		}

		$columns= $this->checkIfEnable($columnsToDisplayOrNot, $columns);

		#EXPORT TRIGGER
		if (isset($input['exportSelectedColumns'])) {
			$columns = $this->getSelectedColumns(explode(",", $input['exportSelectedColumns']), $columns);
		}
		if($is_export){
            $this->data_tables->options['is_export']=true;
			// $this->data_tables->options['only_sql']=true;
            if(empty($csv_filename)){
                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
            }
            $this->data_tables->options['csv_filename']=$csv_filename;
		}
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having);

		if($is_export){
		    //drop result if export
			return $csv_filename;
		}
		#EXPORT - FOR VIEWING FIELDS TO EXPORT ON VIEW
		$result['cols_names_aliases'] = $this->get_dt_column_names_and_aliases($columns);

  //       if($is_export){
  //           $this->data_tables->options['is_export']=true;
  //           if(empty($csv_filename)){
  //               $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
  //           }
  //           $this->data_tables->options['csv_filename']=$csv_filename;
		// }

		$subtotals['subtotals_payout_rate']  = @$this->data_tables->percentageFormatter($subtotals['subtotals_total_payout']/$subtotals['subtotals_total_bets']).'%';
		$result['subtotals'] = $subtotals;



		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(CASE WHEN transactions.transaction_type = ' . Transactions::AUTO_ADD_CASHBACK_TO_BALANCE . ' THEN transactions.amount ELSE 0 END) total_cashback', null, $columns, $where, $values);
		$result['summary'][0]['total_cashback'] = $this->utils->formatCurrencyNoSym($summary[0]['total_cashback']);

		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(CASE WHEN transactions.transaction_type = ' . Transactions::MEMBER_GROUP_DEPOSIT_BONUS . ' THEN transactions.amount ELSE 0 END) total_deposit_bonus', null, $columns, $where, $values);
		$result['summary'][0]['total_deposit_bonus'] = $this->utils->formatCurrencyNoSym($summary[0]['total_deposit_bonus']);

		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(CASE WHEN transactions.transaction_type = ' . Transactions::PLAYER_REFER_BONUS . ' THEN transactions.amount ELSE 0 END) total_bonus', null, $columns, $where, $values);
		$result['summary'][0]['total_bonus'] = $this->utils->formatCurrencyNoSym($summary[0]['total_bonus']);

		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(CASE WHEN transactions.transaction_type = ' . Transactions::ADD_BONUS . ' THEN transactions.amount ELSE 0 END) total_add_bonus', null, $columns, $where, $values);
		$result['summary'][0]['total_add_bonus'] = $this->utils->formatCurrencyNoSym($summary[0]['total_add_bonus']);

		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(CASE WHEN transactions.transaction_type = ' . Transactions::SUBTRACT_BONUS . ' THEN transactions.amount ELSE 0 END) total_sub_bonus', null, $columns, $where, $values);
		$result['summary'][0]['total_sub_bonus'] = $this->utils->formatCurrencyNoSym($summary[0]['total_sub_bonus']);

		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(CASE WHEN transactions.transaction_type IN (' . implode(',', array(Transactions::MEMBER_GROUP_DEPOSIT_BONUS, Transactions::PLAYER_REFER_BONUS, Transactions::ADD_BONUS)) . ') THEN transactions.amount ELSE 0 END) total_total_bonus', null, $columns, $where, $values);
		$result['summary'][0]['total_total_bonus'] = $this->utils->formatCurrencyNoSym($summary[0]['total_total_bonus']);

		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(CASE WHEN transactions.transaction_type IN (' . Transactions::DEPOSIT . ') THEN transactions.amount ELSE 0 END) total_deposit', null, $columns, $where, $values);
		$result['summary'][0]['total_deposit'] = $this->utils->formatCurrencyNoSym($summary[0]['total_deposit']);

		$summary = $this->data_tables->summary($request, $table, $joins, 'COUNT(CASE WHEN transactions.transaction_type IN (' . Transactions::DEPOSIT . ') THEN 1 END) total_deposit_times', null, $columns, $where, $values);
		$result['summary'][0]['total_deposit_times'] = $summary[0]['total_deposit_times'];

		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(CASE WHEN transactions.transaction_type IN (' . implode(',', array(Transactions::WITHDRAWAL)) . ') THEN transactions.amount ELSE 0 END) total_withdrawal', null, $columns, $where, $values);
		$result['summary'][0]['total_withdrawal'] = $this->utils->formatCurrencyNoSym($summary[0]['total_withdrawal']);

		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(CASE WHEN transactions.transaction_type IN (' . implode(',', array(Transactions::DEPOSIT)) . ') THEN transactions.amount ELSE 0 END) - SUM(CASE WHEN transactions.transaction_type IN (' . implode(',', array(Transactions::WITHDRAWAL)) . ') THEN transactions.amount ELSE 0 END) total_dw', null, $columns, $where, $values);
		$result['summary'][0]['total_dw'] = $this->utils->formatCurrencyNoSym($summary[0]['total_dw']);

		/**
		 * Manual Query for Total First Deposit, Total Second Deposit, Total Bets, Payout and Payout Rate
		 * @param string $date_from $date_to
		 * ©ivan.php.ph
		 * Owned by : Triple One Tech
		 * Date: 08-06-2018
		*/
		if (isset($input['date_from'], $input['date_to'])) {
			$date_from = $input['date_from'];
			$date_to = $input['date_to'];
		}

		if (isset($input['playerlevel'])) {
			$playerlvl = $input['playerlevel'];
			if (!empty($playerlvl)) {
				$playerlvls = " AND player.levelId = ".$playerlvl."";
			}
		}

		if (isset($input['group_by'])) {
			switch ($input['group_by']) {
				case 'player_id':
				$group_bys = 'player.playerId';
				break;
				case 'playerlevel':
				$group_bys = 'player.levelId';
				break;
				case 'affiliate_id':
				$group_bys = 'player.affiliateId';
				break;
				case 'agent_id':
				$group_bys = 'player.agent_id';
				break;
				default:
				//
				break;
			}
		}

		$query = $readOnlyDB->query("SELECT player.playerId playerId, affiliates.affiliateId affiliateId, player.levelId levelId FROM (`player`) LEFT JOIN `playerdetails` ON playerdetails.playerId = player.playerId LEFT JOIN `player_runtime` ON player_runtime.playerId = player.playerId LEFT JOIN `transactions` ON transactions.to_id = player.playerId LEFT JOIN `affiliates` ON affiliates.affiliateId = player.affiliateId WHERE `transactions`.`to_type` = 2 AND transactions.status = 1 AND transactions.created_at BETWEEN ? AND ?".$playerlvls." AND player.deleted_at IS NULL GROUP BY ".$group_bys."", array($date_from, $date_to));
		$rows = $query->result_array();
		foreach( $rows as $row )
		{
			$total['total_player_id'] = $row['playerId'];
			$total['total_player_levelId'] = $row['levelId'];

			# total first deposit
			$query = $readOnlyDB->query("SELECT transactions.amount FROM transactions JOIN player ON player.playerId = transactions.to_id WHERE transactions.to_type = ?  AND transactions.transaction_type = ? AND transactions.status = ?  AND transactions.to_id  = ".$total['total_player_id']."  AND player.deleted_at IS NULL ORDER BY created_at ASC LIMIT ?,1", array( Transactions::PLAYER, Transactions::DEPOSIT, Transactions::APPROVED, 0, ));
			$row = $query->row_array();
			$total['total_first_deposit'] += isset($row['amount']) ? $row['amount'] : 0;

			#total second deposit
			$query = $readOnlyDB->query("SELECT transactions.amount as amounts FROM transactions JOIN player ON player.playerId = transactions.to_id WHERE transactions.to_type = ?  AND transactions.transaction_type = ? AND transactions.status = ?  AND transactions.to_id  = ".$total['total_player_id']."  AND player.deleted_at IS NULL ORDER BY created_at ASC LIMIT ?,1", array( Transactions::PLAYER, Transactions::DEPOSIT, Transactions::APPROVED, 1, ));
			$row = $query->row_array();
			$total['total_second_deposit'] += isset($row['amounts']) ? $row['amounts'] : 0;

			#total bet add
			$queryTotalBet= "SELECT SUM(betting_amount) AS total_bets FROM total_player_game_hour WHERE player_id = ".$total['total_player_id']."  ".$daterange_hour." ";
			# overrive query if group by player level
			if (isset($input['group_by']) && strtolower($input['group_by']) == "playerlevel") {
				$queryTotalBet = "SELECT SUM(betting_amount) AS total_bets FROM total_player_game_hour WHERE player_id IN (SELECT playerId FROM player where levelId = ".$total['total_player_levelId'].")  ".$daterange_hour;
			}
			$query = $readOnlyDB->query($queryTotalBet);
			$row = $query->row_array();
			$total['total_bets_add'] += isset($row['total_bets']) ? $row['total_bets'] : 0 ;

			#total payout add
			$queryTotalPayout = "SELECT SUM(loss_amount - win_amount) AS total_payout FROM total_player_game_hour WHERE player_id = ".$total['total_player_id']."  ".$daterange_hour."";
			# overrive query if group by player level
			if (isset($input['group_by']) && strtolower($input['group_by']) == "playerlevel") {
				$queryTotalPayout = "SELECT SUM(loss_amount - win_amount) AS total_payout FROM total_player_game_hour WHERE player_id in (SELECT playerId FROM player where levelId = ".$total['total_player_levelId'].") ". $daterange_hour;
			}
			$query = $readOnlyDB->query($queryTotalPayout);
			$row = $query->row_array();
			$total['total_payout_add'] += isset($row['total_payout']) ? $row['total_payout'] : 0 ;
		}
		#total payout rate
		$total['total_payout_rate'] = @$this->data_tables->percentageFormatter($total['total_payout_add']/$total['total_bets_add']).'%';

		$result['total'] = $total;


		return $result;
	}

    public function player_reports_2($request, $viewPlayerInfoPerm, $is_export){
        $readOnlyDB = $this->getReadOnlyDB();

        $this->load->library('data_tables', array("DB" => $readOnlyDB));
        $this->load->model(array('risk_score_model','player_kyc','kyc_status_model','agency_model', 'transactions', 'affiliatemodel', 'affiliate'));
        $this->load->helper(['player_helper']);
        $game_apis_map= $this->utils->getAllSystemMap();

        $player_kyc = $this->player_kyc;
        $kyc_status_model = $this->kyc_status_model;
        $risk_score_model = $this->risk_score_model;

        $this->data_tables->is_export = $is_export;

        $table = 'player_report_hourly';
        $joins = array();
        $where = array();
        $values = array();
        $group_by = array();
        $having = array();
        $joins['vipsettingcashbackrule'] = 'vipsettingcashbackrule.vipsettingcashbackruleId = player_report_hourly.level_id';
        $joins['vipsetting'] = 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId';

        $i = 0;
        $input = $this->data_tables->extra_search($request);

        $date_from = null;
        $date_to = null;
        $playerlvl = null;
        $playerlvls = null;
        if (isset($input['date_from'], $input['date_to'])) {
            $date_from = $input['date_from'];
            $date_to = $input['date_to'];
        }

        # FILTER ######################################################################################################################################################################################

		$show_username            = true;
		$show_realname            = true;
		$show_tag                 = true;
		$show_risk_level          = true;
		$show_kyc_level           = true;
		$show_player_level        = true;
		$show_affiliate           = true;
		$show_agent               = true;
		$show_register_date       = true;
		$show_deposit_bonus       = true;
		$show_cashback_bonus      = true;
		$show_referral_bonus      = true;
		$show_manual_bonus        = true;
		$show_subtract_bonus      = true;
		$show_total_bonus         = true;
		$show_first_deposit       = true;
		$show_first_deposit_date  = true;
		$show_total_deposit       = true;
		$show_total_deposit_times = true;
		$show_total_withdrawal    = true;
		$show_total_dw            = true;
		$show_total_bets          = true;
		$show_total_win           = true;
		$show_total_loss          = true;
		$show_total_payout        = true;
		$show_payout_rate         = true;
		$show_total_revenue       = true;


        if (isset($input['group_by'])) {
            switch ($input['group_by']) {
                case 'player_id':
                    $group_by[] = 'player_id';
                    break;

                case 'playerlevel':
                    $group_by[] = 'level_id';
                    $show_username = false;
                    $show_realname = false;
                    $show_tag = false;
                    $show_risk_level = false;
                    $show_kyc_level =false;
                    $show_player_level = true;
                    $show_affiliate = false;
                    $show_agent= false;
                    $show_register_date = false;
                    break;
                case 'affiliate_id':
                    $group_by[] = 'affiliate_id';
                    $show_username = false;
                    $show_realname = false;
                    $show_tag = false;
                    $show_risk_level = false;
                    $show_kyc_level =false;
                    $show_player_level = false;
                    $show_affiliate = true;
                    $show_agent= false;
                    $show_register_date = false;
                    break;

                case 'agent_id':
                    $group_by[] = 'player_report_hourly.agent_id';
                    $show_username = false;
                    $show_realname = false;
                    $show_tag = false;
                    $show_risk_level = false;
                    $show_kyc_level =false;
                    $show_player_level = false;
                    $show_affiliate = false;
                    $show_agent= true;
                    $show_register_date = false;
                    break;

                default;
            }
        }

        $dateTimeFrom = null;
        $dateTimeTo = null;
        if (isset($input['date_from'], $input['date_to'])) {
            $dateTimeFrom = $input['date_from'];
            $dateTimeTo   = $input['date_to'];

            $where[]  = "player_report_hourly.date_hour >= ? AND player_report_hourly.date_hour <= ?";
            $values[] = $this->utils->formatDateHourForMysql(new DateTime($dateTimeFrom));
            $values[] = $this->utils->formatDateHourForMysql(new DateTime($dateTimeTo));
        }
        $date_range_hour = '';
        if (!empty($dateTimeFrom) && !empty($dateTimeTo)) {
            $date_from_hour = (new DateTime($date_from))->format('YmdH');
            $date_to_hour = (new DateTime($date_to))->format('YmdH');
            $date_range_hour = " AND date_hour >=  {$date_from_hour} AND date_hour <=  {$date_to_hour}";
        }

        if (isset($input['username'])) {
            $where[] = "player_username LIKE ?";
            $values[] = '%' . $input['username'] . '%';
        }

        if (isset($input['referrer'])) {

			$refereePlayerId = $this->player_model->getPlayerIdByUsername($input['referrer']) ? : -1;
			$joins['player'] = "player.playerId = player_report_hourly.player_id";
            $where[] = "player.refereePlayerId = ?";
            $values[] = $refereePlayerId;
        }


        if (isset($input['playerlevel'])) {
            $where[] = "level_id = ?";
            $values[] = $input['playerlevel'];
        }

        if (isset($input['depamt1'])) {
            $having['total_deposit <='] = $input['depamt1'];
        }

        if (isset($input['depamt2'])) {
            $having['total_deposit >='] = $input['depamt2'];
        }

        if (isset($input['widamt1'])) {
            $having['total_withdrawal <='] = $input['widamt1'];
        }

        if (isset($input['widamt2'])) {
            $having['total_withdrawal >='] = $input['widamt2'];
        }
        if (isset($input['only_under_agency']) && $input['only_under_agency'] != '') {
            $where[] = "player_report_hourly.agent_id IS NOT NULL";
            if (!isset($input['agent_name'])) {
                if (isset($input['current_agent_name']) && $input['current_agent_name'] != '') {
                    $input['agent_name'] = $input['current_agent_name'];
                }
            }
        }
        if (isset($input['agent_name'])) {
            $agent_detail = $this->agency_model->get_agent_by_name($input['agent_name']);

            if (isset($input['include_all_downlines']) && $input['include_all_downlines'] == true) {
                $joins['agency_agents'] = 'player_report_hourly.agent_id = agency_agents.agent_id';
                $parent_ids = array($agent_detail['agent_id']);
                $all_ids = $parent_ids;
                // $sub_ids = $this->agency_model->get_sub_agent_ids_by_parent_id($parent_ids);
                while (!empty($sub_ids = $this->agency_model->get_sub_agent_ids_by_parent_id($parent_ids))) {
                    $this->utils->debug_log('sub_ids', $sub_ids);
					$all_ids = array_merge($all_ids, $sub_ids);
					$parent_ids = $sub_ids;
                    $sub_ids = array();
                }
                $w = '';
                foreach ($all_ids as $i => $id) {
                    if ($i == 0) {
                        $w = "(player_report_hourly.agent_id = ?";
                    } else {
                        $w .= " OR player_report_hourly.agent_id = ?";
                    }
                    $values[] = $id;
                }
                $w .= ")";
                $where[] = $w;
            } else {
                $where[] = "player_report_hourly.agent_id = ?";
                $values[] = $agent_detail['agent_id'];
            }
        }

        if (isset($input['affiliate_name'])) {

            $affiliateId = $this->affiliatemodel->getAffiliateIdByUsername($input['affiliate_name']);
            @$this->utils->debug_log('affiliateId', $$affiliateId);
            $affiliateIds = null;

            if (isset($input['aff_include_all_downlines']) && $input['aff_include_all_downlines']
                && !empty($affiliateId)) {
                $affiliateIds = $this->affiliatemodel->includeAllDownlineAffiliateIds($affiliateId);
            }

            if (!empty($affiliateIds)) {
                $where[] = 'affiliate_id IN(' . implode(',', $affiliateIds) . ')';
            } else {
                $where[] = "affiliate_username = ?";
                $values[] = $input['affiliate_name'];
            }
        }

        $affiliatesTagMap = [];

        if (isset($input['affiliate_tags'])) {

            $affiliatesTagMap = $this->affiliate->getAffTagsMap();

            $this->utils->debug_log('affiliatesTagMap1', $affiliatesTagMap);

            $joins['affiliatetag'] = 'affiliatetag.affiliateId=affiliate_id';
            $joins['affiliatetaglist'] = 'affiliatetaglist.tagId=affiliatetag.tagId';
            $where[] = "affiliatetag.tagId = ?";
            $values[] = $input['affiliate_tags'];
        }

        if (isset($input['player_tag'])) {
            $joins['playertag'] = 'playertag.playerId = player_id';
            $where[] = "playertag.tagId = ?";
            $values[] = $input['player_tag'];
		}

		if (isset($input['tag_list'])) {
			$tagList = $input['tag_list'];

			if(is_array($tagList)) {
				$notag = array_search('notag',$tagList);
				if($notag !== false) {
					$where[] = 'player_id IN (SELECT DISTINCT playerId FROM playertag)';
					unset($tagList[$notag]);
				}

			} elseif ($tagList == 'notag') {
				$where[] = 'player_id IN (SELECT DISTINCT playerId FROM playertag)';
				$tagList = null;
			}
			if (!empty($tagList)) {
				$tagList = is_array($tagList) ? implode(',', $tagList) : $tagList;
				$where[] = 'player_id NOT IN (SELECT DISTINCT playerId FROM playertag WHERE playertag.tagId IN ('.$tagList.'))';
			}
		}


        if (isset($input['affiliate_agent'])) {
            switch ($input['affiliate_agent']) {

                case '1': # Not under any Affiliate or Agent
                    $where[] = '(player_report_hourly.agent_id IS NULL OR player_report_hourly.agent_id = 0) AND (affiliate_id IS NULL OR affiliate_id = 0)';
                    break;

                case '2': # Under Affiliate Only
                    $where[] = '(player_report_hourly.agent_id IS NULL OR player_report_hourly.agent_id = 0) AND affiliate_id > 0';
                    break;

                case '3': # Under Agent Only
                    $where[] = 'player_report_hourly.agent_id > 0 AND (affiliate_id IS NULL OR affiliate_id = 0)';
                    break;

                case '4': # Under Affiliate or Agent
                    $where[] = '(player_report_hourly.agent_id > 0 OR affiliate_id > 0)';
                    break;
            }

		}

        if (isset($input['username'], $input['search_by'])) {
            if ($input['search_by'] == 1) {
                $where[] = "player_username LIKE ?";
                $values[] = '%' . $input['username'] . '%';
            } else if ($input['search_by'] == 2) {
                $where[] = "player_username = ?";
                $values[] = $input['username'];
            }
        }

        $subtotals = ['subtotals_deposit_bonus'=> 0 , 'subtotals_cashback_bonus' => 0, 'subtotals_referral_bonus' => 0,
            'subtotals_manual_bonus'=>0, 'subtotals_subtract_bonus' => 0, 'subtotals_total_bonus'=> 0, 'subtotals_first_deposit' => 0,
            'subtotals_second_deposit' => 0,'subtotals_total_deposit' => 0, 'subtotals_total_deposit_times' => 0,'subtotals_total_withdrawal'=> 0,
            'subtotals_total_bets' => 0, 'subtotals_total_payout'=> 0, 'subtotals_total_win' => 0, 'subtotals_total_loss' => 0,
            'subtotals_total_dw'=> 0, 'subtotals_payout_rate' => 0,
            'subtotals_dnb' => 0, 'subtotals_bod' => 0, 'subtotals_wod' => 0, 'subtotals_tat' => 0, 'subtotals_win' => 0, 'subtotals_loss' => 0
        ];
        # DEFINE TABLE COLUMNS ########################################################################################################################################################################
        $columns = array(
            array(
                'select' => 'affiliate_id'
			),
			array(
                'select' => 'level_id'
            ),
            array(
                'select' => 'player_report_hourly.agent_id',
                'alias' => 'prh_agent_id'
            ),
            array(
                'select' => 'level_name'
            ),
            array(
                'select' => 'vipLevelName'
            ),
            array(
                'dt' => $i++,
                'alias' => 'username',
                'name' => lang('report.pr01'),
                'select' => 'player_username',
                'formatter' => function ($d, $row) use ($is_export, $date_from, $date_to, $show_username) {

                    if($show_username){
                        if ($is_export) {
                            return $d;
                        } else {
                            $date_qry = '';
                            if (!empty($date_from) && !empty($date_to)) {
                                $date = new DateTime($date_from);
                                $date_qry = '&date_from=' . $date->format('Y-m-d') . '&hour_from=' . $date->format('H');

                                $date = new DateTime($date_to);
                                $date_qry .= '&date_to=' . $date->format('Y-m-d') . '&hour_to=' . $date->format('H');
                            }
                            return "<a href='/report_management/viewGamesReport?username={$d}{$date_qry}'>{$d}</a>";
                        }
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'tagName',
                'select' => 'player_id',
                'name' => lang("player.41"),
                'formatter' => function ($d) use ($is_export, $show_tag) {
                    if($show_tag){
                        return player_tagged_list($d, $is_export);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'risk_level',
                'name' => lang("Risk Level/Score"),
                'select' => 'player_id',
                'formatter' => function ($d) use ($is_export,$risk_score_model,$show_risk_level) {
                    if($show_risk_level){
                        if ($is_export) {
                            return $risk_score_model->getPlayerCurrentRiskLevel($d) && $risk_score_model->generate_total_risk_score($d) ? $risk_score_model->getPlayerCurrentRiskLevel($d) .' / '. $risk_score_model->generate_total_risk_score($d) : lang('lang.norecyet');
                        }else{
                            return $risk_score_model->getPlayerCurrentRiskLevel($d) && $risk_score_model->generate_total_risk_score($d) ? $risk_score_model->getPlayerCurrentRiskLevel($d) .' / '. $risk_score_model->generate_total_risk_score($d) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                        }
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'kyc_level',
                'name' => lang("KYC Level/Rate Code"),
                'select' => 'player_id',
                'formatter' => function ($d) use ($is_export,$player_kyc,$kyc_status_model,$show_kyc_level) {
                    if($show_kyc_level){
                        if ($is_export) {
                            return $player_kyc->getPlayerCurrentKycLevel($d) && $kyc_status_model->getPlayerCurrentStatus($d) ? $player_kyc->getPlayerCurrentKycLevel($d) .' / '. $kyc_status_model->getPlayerCurrentStatus($d): lang('lang.norecyet');
                        }else{
                            return $player_kyc->getPlayerCurrentKycLevel($d) && $kyc_status_model->getPlayerCurrentStatus($d) ? $player_kyc->getPlayerCurrentKycLevel($d) .' / '. $kyc_status_model->getPlayerCurrentStatus($d): '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                        }
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'name' => lang('report.pr03'),
                'alias' => 'member_level',
                'select' => 'groupName',
                'formatter' => function ($d, $row) use ($show_player_level, $is_export) {
                    if(($show_player_level)){
                        return lang($d)." - ".lang($row['level_name']);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'affiliates_name',
                'name' => lang('Affiliate'),
                'select' => 'affiliate_username',
                'formatter' => function($d,$row) use ($is_export, $show_affiliate, $input, $affiliatesTagMap){
                    if($show_affiliate){
                        $url = site_url('/affiliate_management/userInformation/' . $row['affiliate_id']);
                        $name = '';
                        if($is_export){
                            $name = !empty($d) ? $d :  lang('lang.norecyet') ;
                        }else{
                            if(!empty($d)){
                                if(isset($input['affiliate_tags'])){
                                    if(isset($affiliatesTagMap[$input['affiliate_tags']])){
                                        $name = '<span class="badge badge-info" style="float:right;">'.$affiliatesTagMap[$input['affiliate_tags']].'</span>';
                                    }else{
                                        $name ='';
                                    }
                                }
                                $name .= '<a href="' . $url . '">' . $d . '</a> ';

                            }else{
                                $name = '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                            }
                        }
                        return $name;
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'agent',
                'select' => 'agent_username',
                'name' => lang("Agent"),
                'formatter' => function ($d, $row) use ($is_export,$show_agent) {
                    if($show_agent){
                        if ($d != null) {
                            $url = site_url('/agency_management/agent_information/' . $row['prh_agent_id']);
                            if($is_export){
                                return $d;
                            }else{
                                return '<a href="' . $url . '">' . $d . '</a>';
                            }
                        } else {
                            if ($is_export) {
                                return trim(trim($d), ',') ?: lang('lang.norecyet');
                            } else {
                                return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                            }
                        }
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'createdOn',
                'name' => lang('report.pr10'),
                'select' => 'registered_date',
                'formatter' =>  function ($d, $row) use ($show_register_date, $is_export){
                    if($show_register_date){
                        return $d;
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'first_deposit_datetime',
                'select' => 'first_deposit_datetime',
                'name' => lang('aff.ap06'),
                'formatter' => function ($d, $row) use ($show_first_deposit_date, $is_export, &$subtotals) {
                    if($show_first_deposit_date){
                        return $d;
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'cashback',
                'name' => lang('report.sum15'),
                'select' => 'SUM(player_report_hourly.total_cashback)',
                'formatter' =>  function ($d) use ($show_cashback_bonus, $is_export, &$subtotals){
                    if($show_cashback_bonus){
                        $subtotals['subtotals_cashback_bonus'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'deposit_bonus',
                'name' => lang('report.pr15'),
                'select' => 'SUM(deposit_bonus)',
                'formatter' =>  function ($d) use ($show_deposit_bonus, $is_export, &$subtotals){
                    if($show_deposit_bonus){
                        $subtotals['subtotals_deposit_bonus'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'referral_bonus',
                'name' => lang('report.pr17'),
                'select' => 'SUM(referral_bonus)',
                'formatter' =>  function ($d) use ($is_export, $show_referral_bonus, &$subtotals){
                    if($show_referral_bonus){
                        $subtotals['subtotals_referral_bonus'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'manual_bonus',
                'name' => lang('transaction.manual_bonus'),
                'select' => 'SUM(manual_bonus)',
                'formatter' =>  function ($d) use ($show_manual_bonus, $is_export, &$subtotals){
                    if($show_manual_bonus){
                        $subtotals['subtotals_manual_bonus'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'subtract_bonus',
                'name' => lang('transaction.transaction.type.10'),
                'select' => 'SUM(subtract_bonus)',
                'formatter' =>  function ($d) use ($show_subtract_bonus, $is_export, &$subtotals){
                    if($show_subtract_bonus){
                        $subtotals['subtotals_subtract_bonus'] -= $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'total_bonus',
                'name' => lang('report.pr18'),
                'select' => 'SUM(player_report_hourly.total_bonus)',
                'formatter' =>  function ($d,$row) use ($show_total_bonus, $is_export, &$subtotals){
                    if($show_total_bonus){
                        $subtotals['subtotals_total_bonus'] += $d;
                       // return $this->data_tables->currencyFormatter($d - $row['subtract_bonus']);
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'first_deposit',
                'select' => 'first_deposit_amount',
                'name' => lang('player.75'),
                'formatter' => function ($d, $row) use ($show_first_deposit, $is_export, &$subtotals) {
                    if($show_first_deposit){
                        $subtotals['subtotals_first_deposit'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'total_deposit',
                'name' => lang('report.pr21'),
                'select' => 'SUM(player_report_hourly.total_deposit)',
                'formatter' =>  function ($d) use ($show_total_deposit, $is_export, &$subtotals){
                    if($show_total_deposit){
                        $subtotals['subtotals_total_deposit'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'total_deposit_times',
                'name' => lang('yuanbao.deposit.times'),
                'select' => 'SUM(deposit_times)',
                'formatter' =>  function ($d) use ($show_total_deposit_times, $is_export, &$subtotals){
                    if($show_total_deposit_times){
                        $subtotals['subtotals_total_deposit_times'] += $d;
                        return $d;
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'total_deposit_and_bonus',
                'name' => lang('DNB'),
                'select' => 'SUM(player_report_hourly.total_deposit) + SUM(player_report_hourly.total_bonus)',
                'formatter' =>  function ($d) use ($show_total_deposit_times, $is_export, &$subtotals){
                    if($show_total_deposit_times){
                        $subtotals['subtotals_dnb'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'total_bonus_over_deposit',
                'name' => lang('BOD%'),
                'select' => 'SUM(player_report_hourly.total_bonus)/SUM(player_report_hourly.total_deposit)',
                'formatter' =>  function ($d) use ($show_total_deposit_times, $is_export, &$subtotals){
                    if($show_total_deposit_times){
                        return $this->data_tables->percentageFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'total_withdrawal',
                'name' => lang('report.pr22'),
                'select' => 'SUM(total_withdrawal)',
                'formatter' =>  function ($d) use ($show_total_withdrawal, $is_export, &$subtotals){
                    if($show_total_withdrawal ){
                        $subtotals['subtotals_total_withdrawal'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'total_dw',
                'name' => lang('Net Deposit'),
                'select' =>'SUM(total_gross)',
                'formatter' =>  function ($d) use ($show_total_dw, $is_export, &$subtotals){
                    if($show_total_dw){
                        $subtotals['subtotals_total_dw'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'total_withdrawal_over_deposit',
                'name' => lang('WOD%'),
                'select' =>'SUM(total_withdrawal)/SUM(total_deposit)',
                'formatter' =>  function ($d) use ($show_total_dw, $is_export, &$subtotals){
                    if($show_total_dw){
                        return $this->data_tables->percentageFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                }
            ),
            array(
                'dt' => $i++,
                'alias' => 'total_bets',
                'name' => lang('cms.totalbets'),
                'select' => 'SUM(total_bet)',
                'formatter' => function ($d) use ($show_total_bets, $is_export, &$subtotals, $input) {
                    if($show_total_bets){
                        $subtotals['subtotals_total_bets'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'turn_around_time',
                'name' => lang('TAT'),
                'select' => 'SUM(total_bet)/ (SUM(total_deposit)+SUM(player_report_hourly.total_bonus))',
                'formatter' => function ($d) use ($show_total_bets, $is_export, &$subtotals, $input) {
                    if($show_total_bets){
                        $subtotals['subtotals_tat'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'total_win',
                'name' => lang('Win'),
                'select' => 'SUM(total_win)',
                'formatter' => function ($d) use ($show_total_win, $is_export, &$subtotals, $input) {
                    if($show_total_win){
                        $subtotals['subtotals_total_win'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'total_loss',
                'name' => lang('Loss'),
                'select' => 'SUM(total_loss)',
                'formatter' => function ($d) use ($show_total_loss, $is_export, &$subtotals, $input) {
                    if($show_total_loss){
                        $subtotals['subtotals_total_loss'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'total_payout',
                'name' => lang('Payout'),
                'select' => 'SUM(total_bet) - ( SUM(total_loss) - SUM(total_win) )',
                'formatter' => function ($d) use ($show_total_payout, $is_export, &$subtotals, $input) {
                    if($show_total_payout){
                        $subtotals['subtotals_total_payout'] += $d;
                        return $this->data_tables->currencyFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
            ),
            array(
                'dt' => $i++,
                'alias' => 'payout_rate',
                'name' => lang('sys.payoutrate'),
                'select' => '(SUM(total_bet) - (SUM(total_loss)-SUM(total_win))) / SUM(total_bet)',
                'formatter' => function ($d) use ($show_payout_rate, $is_export, $input) {
                    if($show_payout_rate){
                        return $this->data_tables->percentageFormatter($d);
                    }else{
                        if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
                    }
                },
			),
			array(
				'dt' => $i++,
				'alias' => 'total_revenue',
				'name' => lang('Game Revenue'),
				'select' => 'SUM(total_loss)-SUM(total_win)',
				'formatter' =>  function($d) use ($show_total_revenue, $is_export, $input) {
					if($show_total_revenue){
						return $this->data_tables->currencyFormatter($d);
					} else {
						if($is_export){
                            return lang('lang.norecyet');
                        }
                        return  '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				}

			),
            array(
                'dt' => $i++,
                'alias' => 'game_bet_details',
                'name' => lang('Bet Detail'),
                'select' => 'player_id',
                'formatter' => function ($d, $row) use ($readOnlyDB, $date_range_hour, $game_apis_map, $is_export, $input) {
                    $queryString = "SELECT game_platform_id as game, SUM(betting_amount) as bet, SUM(loss_amount - win_amount) AS payout, SUM(loss_amount - win_amount)/SUM(betting_amount) as payout_rate FROM total_player_game_hour WHERE player_id = ".$d." ".$date_range_hour."  GROUP BY game_platform_id";
                    //  # overrive query if group by player level
                    if (isset($input['group_by']) && strtolower($input['group_by']) == "playerlevel") {
                        $queryString = "SELECT game_platform_id as game, SUM(betting_amount) as bet, SUM(loss_amount - win_amount) AS payout, SUM(loss_amount - win_amount)/SUM(betting_amount) as payout_rate FROM total_player_game_hour WHERE player_id IN (SELECT playerId FROM player where levelId = ".$row['level_id'].") ".$date_range_hour."  GROUP BY game_platform_id";
                    }

                    $query = $readOnlyDB->query($queryString);
                    $bet_details=$query->result_array();

                    $total_bets_ = 0;
                    $total_payout_ = 0;
                    $total_rate_ = 0;

                    if(!empty($bet_details)){

                        if(!$is_export){
                            $bet_details_str = '<table  border="1" style="width:500px;">';
                            $bet_details_str .=  '<tr style="padding:2px;">';
                            $bet_details_str .=  '<th class="text-center" style="width:129px;"> '.lang('Game Platform').' </th><th class="text-center" style="width:105px;" > '.lang('Bets').' </th><th class="text-center" style="width:105px;"  > '.lang('report.Payout').' </th><th class="text-center" style="width:105px;"> '.lang('sys.payoutrate').'</th>';
                            $bet_details_str .=  '</tr>';

                            foreach ($bet_details as $v) {

                                $total_bets_ += $v['bet'];
                                $total_payout_ += $v['payout'];
                                $bet_details_str .=  '<tr>';

                                if(array_key_exists($v['game'], $game_apis_map)){
                                    $bet_details_str .= '<td style="text-align:right;padding:5px;width:50px;font-weight:bold;"><span class="text-success">'.$game_apis_map[$v['game']].'</span> </td>';
                                }else{
                                    $bet_details_str .= '<td style="text-align:right;padding:5px;width:50px;"><span class="text-danger">'.$v['game'].'</span> </td>';
                                }
                                $bet_details_str .= '<td style="text-align:right;padding:5px;"> <span>'.$this->data_tables->currencyFormatter($v['bet']).'</span></td>';
                                $bet_details_str .= '<td style="text-align:right;padding:5px;"> <span>'.$this->data_tables->currencyFormatter($v['payout']).'</span></td>';
                                $bet_details_str .= '<td style="text-align:right;padding:5px;"> <span>'.$this->data_tables->currencyFormatter($v['payout_rate']*100).'%</span></td>';
                                $bet_details_str .=  '</tr>';

                            }

                            $bet_details_str .=  '<tr>';
                            $bet_details_str .=  '<td style="text-align:right;padding:5px;font-weight:bold;"><span class="text-primary">Total</span> </td> ';
                            $bet_details_str .=  '<td style="text-align:right;padding:5px;font-weight:bold;"><span class="text-primary">'.$this->data_tables->currencyFormatter($total_bets_).'</span> </td> ';
                            $bet_details_str .=  '<td style="text-align:right;padding:5px;font-weight:bold;"><span class="text-primary">'.$this->data_tables->currencyFormatter($total_payout_).'</span> </td> ';
                            $bet_details_str .=  '<td style="text-align:right;padding:5px;font-weight:bold;"><span class="text-primary">'.@$this->data_tables->currencyFormatter(($total_payout_/$total_bets_)*100).'%</span> </td> ';
                            $bet_details_str .=  '</tr>';

                            return $bet_details_str;

                        }else{
                            $details = [];
                            foreach ($bet_details as $v) {
                                $total_bets_ += $v['bet'];
                                $total_payout_ += $v['payout'];
                                $total_rate_ += $v['payout_rate'];

                                $game =  (array_key_exists($v['game'], $game_apis_map)) ? $game_apis_map[$v['game']] : $v['game'];

                                $game_row = array( 'Game Platform' => $game,
                                    'Bets' => $this->data_tables->currencyFormatter($v['bet']),
                                    'Payout'=> $this->data_tables->currencyFormatter($v['payout']),
                                    'Payout_rate' => $this->data_tables->currencyFormatter($v['payout_rate']*100).'%'
                                );

                                $total_payout_rate_ = @$this->data_tables->currencyFormatter(($total_payout_/$total_bets_)*100);
                                array_push($details, $game_row);
                            }
                            return json_encode(array('total_bets' => $total_bets_,'total_payout' => $total_payout_, 'total_payout_rate'=> $total_payout_rate_, 'details' => $details));
                        }
                    }else{
                        return "-";
                    }
                },
            ),

        );


        # OUTPUT ######################################################################################################################################################################################


        #SYSTEM FEATURES
        $columnsToDisplayOrNot = array(
            'risk_level' => array('field'=>'risk_level','showtThis' => 'yes',  'feature' => 'show_risk_score'),
            'kyc_level' => array('field'=>'kyc_level','showtThis' => 'yes', 'feature' => 'show_kyc_status')
        );

        #CONTROL THIS WHEN USING SBE LOTTERY
        if(!$this->utils->isEnabledFeature('close_aff_and_agent')){
            $columnsToDisplayOrNot['affiliates_name'] = array('field'=>'affiliates_name','showtThis' => 'yes', 'feature' => 'show_search_affiliate');
            // $columnsToDisplayOrNot['agent'] = array('field'=>'agent','showtThis' => 'yes',  'feature' => 'agency');
        }else{
            $columnsToDisplayOrNot['affiliates_name'] = array('field'=>'affiliates_name','showtThis' => 'no', 'feature' => 'show_search_affiliate');
            // $columnsToDisplayOrNot['agent'] = array('field'=>'agent','showtThis' => 'no',  'feature' => 'agency');
        }

        $columns= $this->checkIfEnable($columnsToDisplayOrNot, $columns);

        #EXPORT TRIGGER
        if (isset($input['exportSelectedColumns'])) {
            $columns = $this->getSelectedColumns(explode(",", $input['exportSelectedColumns']), $columns);
        }

        if($is_export){
            $this->data_tables->options['is_export']=true;
			// $this->data_tables->options['only_sql']=true;
            if(empty($csv_filename)){
                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
            }
            $this->data_tables->options['csv_filename']=$csv_filename;
		}

        $result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having);

        if($is_export){
		    //drop result if export
        	return $csv_filename;
        }

        #EXPORT - FOR VIEWING FIELDS TO EXPORT ON VIEW
        $result['cols_names_aliases'] = $this->get_dt_column_names_and_aliases($columns);

        $subtotals['subtotals_payout_rate']  = empty($subtotals['subtotals_total_bets']) ? $this->data_tables->percentageFormatter(0): $this->data_tables->percentageFormatter($subtotals['subtotals_total_payout']/$subtotals['subtotals_total_bets']);
        $subtotals['subtotals_bod']  = empty($subtotals['subtotals_total_deposit']) ? $this->data_tables->percentageFormatter(0): $this->data_tables->percentageFormatter($subtotals['subtotals_total_bonus']/$subtotals['subtotals_total_deposit']);
        $subtotals['subtotals_wod']  = empty($subtotals['subtotals_total_deposit']) ? $this->data_tables->percentageFormatter(0): $this->data_tables->percentageFormatter($subtotals['subtotals_total_withdrawal']/$subtotals['subtotals_total_deposit']);
        $subtotals['subtotals_tat']  = ($subtotals['subtotals_total_bets'] && $subtotals['subtotals_dnb']) ? ($subtotals['subtotals_total_bets']/$subtotals['subtotals_dnb']) : 0;
        $subtotals['subtotals_game_revenue']  = $subtotals['subtotals_total_loss'] - $subtotals['subtotals_total_win'];
        $result['subtotals'] = $subtotals;

		$query = $this->db->select('first_deposit_amount, second_deposit_amount, SUM(subtract_bonus) subtract_bonus')->from('player_report_hourly');
        if (count($where) > 0) {
            if (count($joins) > 0) {
                $joins = array_unique($joins);
                foreach ($joins as $key => $value) {
                    $joinMode='left';
                    if (!empty($this->innerJoins)) {
                        if (in_array($key, $this->innerJoins)) {
                            $joinMode='innder';
                        }
                    }
                    $query->join($key, $value, $joinMode);
                }
            }
            if ($search_string_1 = $this->_flatten($where)) {
                $search_string_1 = $this->db->compile_binds($search_string_1, $values);
                $query->where($search_string_1);
            }
        }

        $query->group_by($group_by);

        $rows = $this->runMultipleRowArray();
        $deposit = ['first' =>0, 'second'=> 0];
        $subtract_bonus = 0;
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $deposit['first'] += $row['first_deposit_amount'];
                $deposit['second'] += $row['second_deposit_amount'];
                $subtract_bonus -= $row['subtract_bonus'];
            }
        }

        $summary = $this->data_tables->summary($request, $table, $joins, 'SUM(player_report_hourly.total_cashback) cashback, SUM(deposit_bonus) deposit_bonus, SUM(referral_bonus) referral_bonus, SUM(manual_bonus) total_manual,
        SUM(player_report_hourly.total_bonus) total_bonus, SUM(total_deposit) total_deposit, SUM(deposit_times) deposit_times, SUM(total_withdrawal) total_withdrawal,
        SUM(total_gross) total_deposit_withdraw, SUM(total_bet) total_bet, SUM(payout) payout, SUM(total_win) total_win, SUM(total_loss) total_loss', null, $columns, $where, $values);

		$total['total_cashback']       = $this->utils->formatCurrencyNoSym($summary[0]['cashback']);
        $total['total_deposit_bonus']  = $this->utils->formatCurrencyNoSym($summary[0]['deposit_bonus']);
        $total['total_referral_bonus'] = $this->utils->formatCurrencyNoSym($summary[0]['referral_bonus']);
        $total['total_add_bonus']      = $this->utils->formatCurrencyNoSym($summary[0]['total_manual']);
        $total['total_subtract_bonus'] = $this->utils->formatCurrencyNoSym($subtract_bonus);
        $total['total_total_bonus']    = $this->utils->formatCurrencyNoSym($summary[0]['total_bonus']);
        $total['total_first_deposit']  = $this->utils->formatCurrencyNoSym($deposit['first']);
        $total['total_second_deposit'] = $this->utils->formatCurrencyNoSym($deposit['second']);
        $total['total_deposit']        = $this->utils->formatCurrencyNoSym($summary[0]['total_deposit']);
        $total['total_deposit_times']  = $summary[0]['deposit_times'] ?: 0;
        $total['total_withdrawal']     = $this->utils->formatCurrencyNoSym($summary[0]['total_withdrawal']);
        $total['total_dw']             = $this->utils->formatCurrencyNoSym($summary[0]['total_deposit_withdraw']);
		$total['total_bets']           = $this->utils->formatCurrencyNoSym($summary[0]['total_bet']);
		$total_payout = $summary[0]['total_bet']-($summary[0]['total_loss']-$summary[0]['total_win']);
        $total['total_payout']         = $this->utils->formatCurrencyNoSym($total_payout);
        $total['total_payout_rate']    = empty($summary[0]['total_bet']) ? $this->data_tables->percentageFormatter(0): $this->data_tables->percentageFormatter($total_payout/$summary[0]['total_bet']);
        $dnb = $summary[0]['total_deposit']+$summary[0]['total_bonus'];
        $total['total_dnb']            = $this->utils->formatCurrencyNoSym($dnb);
        $total['total_bod']            = empty($summary[0]['total_deposit']) ? $this->data_tables->percentageFormatter(0): $this->data_tables->percentageFormatter($summary[0]['total_bonus']/$summary[0]['total_deposit']);
        $total['total_wod']            = empty($summary[0]['total_deposit']) ? $this->data_tables->percentageFormatter(0): $this->data_tables->percentageFormatter($summary[0]['total_withdrawal']/$summary[0]['total_deposit']);
        $total['total_tat']            = empty($dnb) ? $this->utils->formatCurrencyNoSym(0): $this->utils->formatCurrencyNoSym($summary[0]['total_bet']/$dnb);
        $total['total_win']            = $this->utils->formatCurrencyNoSym($summary[0]['total_win']);
        $total['total_loss']           = $this->utils->formatCurrencyNoSym($summary[0]['total_loss']);
        $total['total_game_revenue']   = $this->utils->formatCurrencyNoSym($summary[0]['total_loss'] - $summary[0]['total_win']);
        $result['total'] = $total;

        return $result;
    }

    private function _flatten($a, $join = ' AND ') {
        if (!$a) {
            return '';
        } else if (is_array($a)) {
            return implode($join, $a);
        } else {
            return $a;
        }
    }

	private function checkIfEnable($columnsToDisplayOrNot, $columns){

		$fields =array_column($columnsToDisplayOrNot,'field' );
		$i = 0;
		foreach ($columns as $key => $column) {
			if(isset($column['alias'])){
				$alias = $column['alias'];
				if(in_array($alias, $fields ) && isset($columns[$key]['dt'])){

					if($this->utils->isEnabledFeature($columnsToDisplayOrNot[$alias]['feature'])){
						if($columnsToDisplayOrNot[$alias]['showtThis'] == 'no'){
							//$this->utils->debug_log('the  susi ---->', $columns[$key]);
							unset($columns[$key]);
						}
					}else{
						if($columnsToDisplayOrNot[$alias]['showtThis'] == 'yes'){
							//$this->utils->debug_log('the  susi ---->', $columns[$key]);
							unset($columns[$key]);
						}
					}
				}
				if(isset($columns[$key]['dt'])){
					$columns[$key]['dt'] = $i++;
				}
			}
		}
		return array_values($columns);
	}

	private function get_dt_column_names_and_aliases($columns){
		$cols_names_aliases = [];
		foreach ($columns as $key => $data) {
			if(isset( $data['dt'])){
				$arr = array();
				$arr['alias'] = $data['alias'];
				$arr['name'] = $data['name'];
				array_push($cols_names_aliases, $arr);
			}
		}
		return $cols_names_aliases;
	}

	private function getPlayerBetPayoutRate($playerId,$daterange_hour){
		$readOnlyDB = $this->getReadOnlyDB();
		$query = $readOnlyDB->query("SELECT game_platform_id as game, SUM(betting_amount) as bet, SUM(loss_amount - win_amount) AS payout, SUM(loss_amount - win_amount)/SUM(betting_amount) as payout_rate FROM total_player_game_hour WHERE player_id = ? ".$daterange_hour."  GROUP BY game_platform_id", array($playerId));
		return $query->result_array();
	}

	private function getSelectedColumns($selectedColumns, $columns){

		if(!empty($selectedColumns)){
			$i = 0;
			foreach ($columns as $key => $data) {
				if (!in_array($data['alias'], $selectedColumns) && isset($columns[$key]['dt'])) {
					unset($columns[$key]);
				}else{
					if(isset($columns[$key]['dt'])){
						$columns[$key]['dt'] = $i++;
					}
				}
			}
		}
		return array_values($columns);
	}

	/**
	 * detail: get player list
	 *
	 * @param array $request
	 * @param array $permissions For queue_results reference while executed.
	 * @param Boolean $is_export true while executed from queue or false means query from SBE.
	 * @param string $csv_filename The specified file name.
	 *
	 * @return array
	 */
	public function player_list_reports($request, $permissions, $is_export = false,$csv_filename=null) {
		$session_timeout =  $this->utils->getConfig('session_timeout');
		$dateTimeNow =  $this->utils->getNowDateTime();
		$dateTimeNow = $dateTimeNow->modify($session_timeout);
		$dateTimeNow = $dateTimeNow->getTimestamp();
		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->helper(['player_helper']);

		$viewVerifiedStatus = true;
		$this->load->model(array('player_model', 'agency_model', 'wallet_model', 'game_logs','risk_score_model','player_kyc','player_attached_proof_file_model','kyc_status_model','transactions'));

		$this->data_tables->is_export = $is_export;
		$wallet_model = $this->wallet_model;
		$risk_score_model = $this->risk_score_model;
		$player_attached_proof_file_model = $this->player_attached_proof_file_model;
		$player_kyc = $this->player_kyc;
		$kyc_status_model = $this->kyc_status_model;
		$utils = $this->utils;
		$model = $this;

		# START DEFINE COLUMNS #################################################################################################################################################
		//permission and enabled system
		$input = $this->data_tables->extra_search($request);

		$i = 0;
		$columns = array();
		if ( ! $is_export) { // for website
			$columns[] = array(
				'dt' => $i++, // #1
				'alias' => 'batch_message_action',
				'select' => 'player.playerId',

				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) {
						return '';
					} else {
						return '<input type="checkbox" class="batch-message-cb" title="' . lang('lang.select.send.message') . '" value="' . $row['playerId'] . '" username="' . $row['username'] . '"  id="cb-user-id-' . $row['username'] . '"/>';
					}
				},
				'name' => '',
			);
		}

			$columns[] = array(
				// 'dt' => $i++, // A1. Remove items from the table:ID
				'alias' => 'playerId',
				'select' => 'player.playerId',
				'name' => lang('ID'), // ID
			);
			$columns[] = array(
				'dt' => $i++, // #2
				'alias' => 'username',
				// 'select' => '( CASE WHEN affiliates.username IS NULL THEN player.username ELSE CONCAT(player.username, \' (\', affiliates.username,  \')\' ) END )',
				'select' => 'player.username',
				'formatter' => ($is_export) ? 'defaultFormatter' : function ($d, $row) {
					return '<a href="/player_management/userInformation/' . $row['playerId'] . '">' . $d . '</a>';
				},
				'name' => lang('player.01'),
			);
			// A2. Modify and arrange the sorting of the table: (the sequence as below)
			$columns[] = array(
				'dt' => $i++, // #3
				'alias' => 'online',
				// 'select' => 'player.playerId',
				'select' => 'player.online',
				'name' => lang("viewuser.03"),
				'formatter' => function ($d, $row) use ($is_export,$dateTimeNow) {
					if ($is_export) {
						return '';
					}else{
						// ci_player_sessions.player_id = player.playerId and ci_player_sessions.player_id is not null;
						// $isOnline=$this->player_model->existsOnlineSession($d);
						// return $isOnline ? '<i class="text-muted">' . lang('icon.online') . '</i>' : '<i class="text-muted">' . lang('icon.offline') . '</i>';
						return $d ? '<i class="text-muted">' . lang('icon.online') . '</i>' : '<i class="text-muted">' . lang('icon.offline') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++, // #4
				'alias' => 'lastLoginTime',
				'select' => 'player_runtime.lastLoginTime',
				'name' => lang("Last Login Date"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return (!$d || strtotime($d) < 0) ? lang('lang.norecyet') : date('Y-m-d H:i:s', strtotime($d));
					} else {
						return (!$d || strtotime($d) < 0) ? '<i class="text-muted">' . lang('lang.norecyet') . '</i>' : date('Y-m-d H:i:s', strtotime($d));
					}
				},
			);
			$columns[] = array(
				'dt' => $i++, // #5
				'alias' => 'lastLoginIp',
				'select' => 'player_runtime.lastLoginIp',
				'name' => lang("player_list.fields.last_login_ip"), // Patch for OGP-14715
				'formatter' => function ($d, $row) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?$d." ( ".$this->utils->getCountry($d)." )": lang('lang.norecyet');
					} else {
						return trim(trim($d), ',') ?$d." ( ".$this->utils->getCountry($d)." )": '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
                'dt' => $i++, // #6
                'alias' => 'blocked',
                'select' => 'player.blocked' ,
                'name' => lang("lang.accountstatus"),
                'formatter' => function ($d, $row) use ($is_export){
					$formatter = 1;
					// OGP-15172
					$lang = array();
					$lang['lang.active'] = lang('status.normal');
					$lang['Blocked'] = lang('player_list.options.blocked');
					$lang['Suspended'] = lang('player_list.options.suspended');
					$lang['Self Exclusion'] = lang('player_list.options.self_exclusion');
					$lang['Failed Login Attempt'] = lang('player_list.options.failed_login_attempt');
                    return  $this->utils->getPlayerStatus($row['playerId'],$formatter,$d,$is_export, $lang);
                },
			);
			$columns[] = array(
				'dt' => $i++, // #7
				'alias' => 'createdOn',
				'select' => 'player.createdOn',
				'name' => lang("player.38"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return (!$d || strtotime($d) < 0) ? lang('lang.norecyet') : date('Y-m-d H:i:s', strtotime($d));
					} else {
						return (!$d || strtotime($d) < 0) ? '<i class="text-muted">' . lang('lang.norecyet') . '</i>' : date('Y-m-d H:i:s', strtotime($d));
					}
				},
			);
			$columns[] = array(
				'dt' => $i++, // #8
				'alias' => 'registered_by',
				'select' => 'player.registered_by',
				'formatter' => function ($d) {
					switch ($d) {
						case Player_model::REGISTERED_BY_IMPORTER:
							$str = lang('Imported Account');
							break;
						case Player_model::REGISTERED_BY_WEBSITE:
							$str = lang('player_list.options.website');
							break;
						case Player_model::REGISTERED_BY_MASS_ACCOUNT:
							$str = lang('Batch Create');
							break;
						case Player_model::REGISTERED_BY_MOBILE:
							$str = lang('player_list.options.mobile');
							break;
						case Player_model::REGISTERED_BY_AGENCY_CREATED:
							$str = lang('Created by agency');
							break;
						default:
							$str = lang('Unknown');
							break;
					}
					return $str;
				},
				'name' => lang("Registered By"),
			);
			$columns[] = array(
				'dt' => $i++, // #9
				'alias' => 'registrationWebsite',
				'select' => 'playerdetails.registrationWebsite',
				'name' => lang("Registered Website"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					} else {
						return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++, // #10
				'alias' => 'ip_address',
				'select' => 'playerdetails.registrationIp',
				'name' => lang('Signup IP'), // Signup IP
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?$d." ( ".$this->utils->getCountry($d)." )": lang('lang.norecyet');
					} else {
						return trim(trim($d), ',') ?$d." ( ".$this->utils->getCountry($d)." )": '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++, // #11
				'alias' => 'referral_code',
				'select' => 'player.invitationCode',
				'name' => lang('player_list.fields.referral_code'),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					} else {
						return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++, // #12
				'alias' => 'refereePlayerId',
				'select' => 'player.refereePlayerId',
				'name' => lang("Referred By"),
				'formatter' => function ($d, $row) use ($readOnlyDB, $is_export) {
					$query = $readOnlyDB->query("SELECT username from player where playerId = ?", array($row['refereePlayerId'],));
					$rs = $query->row_array();
					if ($is_export) {
						return !empty($rs['username']) ?  trim($rs['username']):lang('lang.norecord');
					} else {
						return !empty($rs['username']) ?  trim($rs['username']): '<i class="text-muted">' . lang('lang.norecord') . '</i>';
					}
				},
			);
			if(!$this->utils->isEnabledFeature('close_aff_and_agent')){
				$columns[] = array(
					'dt' => $i++, // #13
					'alias' => 'affiliate',
					'select' => 'affiliates.username',
					'name' => lang('player.24'), // player.24
					'formatter' => function ($d) use ($is_export) {
						if ($is_export) {
							return trim(trim($d), ',') ?: lang('lang.norecyet');
						} else {
							return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}
					},

				);
			} // EOF if(!$this->utils->isEnabledFeature('close_aff_and_agent'))
			if(!$this->utils->isEnabledFeature('close_aff_and_agent')){
				$columns[] = array(
					'dt' => $i++, // #14
					'alias' => 'agent',
					'select' => 'player.agent_id',
					'name' => lang("Under Agency"),
					'formatter' => function ($d) use ($is_export) {
						if ($d != null) {
							$agent_details = $this->agency_model->get_agent_by_id($d);

							if(isset($agent_details['agent_name']) && !empty($agent_details['agent_name'])){
							 $name = $agent_details['agent_name'];
							 $url = site_url('/agency_management/agent_information/' . $d);

							 if($is_export){
								 return $name;
							 }else{
								 return '<a href="' . $url . '">' . $name . '</a>';
							 }
						 }else{
							 if($is_export){
								 return trim(trim($d), ',') ?: lang('lang.norecyet');
							 }else{
								 return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
							 }
						 }

						} else {
							if ($is_export) {
								return trim(trim($d), ',') ?: lang('lang.norecyet');
							} else {
								return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
							}
						}
					},
				);
			 } // EOF if(!$this->utils->isEnabledFeature('close_aff_and_agent'))
			$columns[] = array(
				'dt' => $i++, // #15
				'alias' => 'tagName',
				'select' => 'player.playerId',
				'name' => lang("Player Tag"), // Player Tag
				'formatter' => function ($d, $row) use ($is_export) {
					return player_tagged_list($row['playerId'], $is_export);
				},
			);
			$columns[] = array(
				'dt' => $i++, // #16
				'alias' => 'group_level',
				'select' => 'player.levelName',
				'formatter' => function ($d, $row) {
		            $getUpdatedGroupAndLevel = $this->player_model->getPlayerCurrentLevel($row['playerId']);
		            if($getUpdatedGroupAndLevel){
		                $groupName = lang($getUpdatedGroupAndLevel[0]['groupName']);
		                $levelName = lang($getUpdatedGroupAndLevel[0]['vipLevelName']);
		            	return $groupName . ' - ' .$levelName;
		            }
		            else{
		            	return null;
		            }
				},
				'name' => lang("player_list.fields.vip_level"), // player_list.fields.vip_level
			);
			$columns[] = array(
				'dt' => $i++, // #17
				'alias' => 'first_name',
				'select' => 'ifnull(playerdetails.firstName,"")',
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					} else {
						return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
				'name' => lang("First Name"),
			);
			$columns[] = array(
				'dt' => $i++, // #18
				'alias' => 'last_name',
				'select' => 'ifnull(playerdetails.lastName,"")',
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					} else {
						return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
				'name' => lang("Last Name"),
			);

			$columns[] = array(
				'alias' => 'verified_email',
				'select' => 'player.verified_email',
			);
			$columns[] = array(
				'dt' => $i++, // #19
				'alias' => 'email',
				'select' => 'player.email',
				'name' => lang("player_list.fields.email_address"),
				'formatter' => function ($d, $row) use ($permissions, $viewVerifiedStatus, $is_export) {
					$str = $d;

					if ( ! (isset($permissions['view_player_detail_contactinfo_em']) && $permissions['view_player_detail_contactinfo_em'] ) ) {
						$str = $this->utils->keepHeadString($str,3); /// Patch for OGP-15077
					}

					if ($is_export) { // for export
						if ($viewVerifiedStatus) {
							if( empty( trim($str) ) ){
								$str = lang('lang.norecyet');
							}else if( $row['verified_email'] == self::DB_TRUE ){
								$str = $str . ' ' . lang('player_list.options.verified');
							}else{
								$str = $str . ' ' . lang('player_list.options.not_verifiedked');
							}
						}
					}else{ // for HTML
						if( empty( trim($str) ) ){
							$str = '<i class="fa fa-envelope"></i> '. lang('lang.norecyet');
						}else if ($viewVerifiedStatus) {
							if ($row['verified_email'] == self::DB_TRUE) {
								$str = '<i class="fa fa-envelope"></i> <span class="text-success">' . $str . '</span>'; // OGP-15089
							} else {
								$str = '<i class="fa fa-envelope"></i> <span class="text-danger">' . $str . '</span>'; // OGP-15089
							}
						} else {
							$str = '<i class="fa fa-envelope"></i> <span class="text-default">' . $str . '</span>'; // OGP-15089
						}
					}


					return $str;
				},
			);
			$columns[] = array(
				'alias' => 'verified_phone',
				'select' => 'player.verified_phone',
			);
			$columns[] = array(
				'dt' => $i++, // #20
				'alias' => 'contactNumber',
				'select' => 'playerdetails.contactNumber',
				'name' => lang("player.63"),
				'formatter' => function ($d, $row) use ($permissions, $viewVerifiedStatus, $is_export) {
					$str = $d;
					if (!$permissions['view_player_detail_contactinfo_cn'] ) {
						$str = $this->utils->keepTailString($str,3); /// Patch for OGP-15078
					}
					if ($is_export) { // for is_export and display VerifiedStatus.
						if( empty( trim($str) ) ){
							$str = lang('lang.norecyet');
						}else if ($viewVerifiedStatus) {
							if($row['verified_phone'] == self::DB_TRUE ){
								$str = $str . ' ' . lang('player_list.options.verified');
							}else{
								$str = $str . '  ' . lang('player_list.options.not_verifiedked');
							}
						}
					}else{ // for HTML
						if( empty( trim($str) ) ){
							$str = '<i class="fa fa-phone"></i> '. lang('lang.norecyet');
						}else {
							if ($viewVerifiedStatus) {
								if ($row['verified_phone'] == self::DB_TRUE) {
									$str = '<i class="fa fa-phone"></i> <span class="text-success">' . $str . '</span>'; // OGP-15088
								} else {
									$str = '<i class="fa fa-phone"></i> <span class="text-danger">' . $str . '</span>'; // OGP-15088
								}
							} else {
								$str = '<i class="fa fa-phone"></i> <span class="text-default">' . $str . '</span>'; // OGP-15088
							}

							// for add call_player_tele uri in HTML
							if ($permissions['telesales_call']) {
								$str = '<span>'.$str.'</span>';
							}
						}
					}


					return $str;
				},
			);

			/**
			 * for imAccount1 ~ 3 formatter script for HTML.
			 * @param {string|int} $d
			 * @param {array} $permissions
			 * @param {class} $utils The utils class.
			 */
			$scriptListFormatterImAccount = function($d, $permissions, $utils){
				$no_record = '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
				$returnD = $no_record;
				$carry = '<span class="label label-primary">%s</span> ';
				if( ! empty($d) ){
					if ( ! $permissions['view_player_detail_contactinfo_im'] ){
						$d = $utils->keepTailString($d,0);
					}
					$returnD = sprintf($carry, $d);
				}
				return $returnD;
			};
			/**
			 * for imAccount1 ~ 3 formatter script for export.
			 * @param {string|int} $d
			 * @param {array} $permissions
			 * @param {class} $utils The utils class.
			 */
			$scriptListFormatterImAccount4export = function($d, $permissions, $utils){
				if( empty( trim($d) ) ){
					$d = lang('lang.norecyet');
				}else if (!$permissions['view_player_detail_contactinfo_im'] ){
					$d = $utils->keepTailString($d,0);
				}
				return $d;
			};

			/// patch for OGP-15079 Modify permission of "Search Player IM" and " View Player List Contact Information (IM)": SBE_System >View Roles
			// if ( $permissions['view_player_detail_contactinfo_im'] ){ // Show: All IM columns Marked: All IM account username replaced by *.
				# separate im 1 and and im 2 into two columns during export
				if ($is_export) { // for export
					$columns[] = array(
						'dt' => $i++, // #21
						'alias' => 'imAccount1',
						'name' => lang('reg.34') . ' 1',
						'select' => 'CONCAT_WS(" ", playerdetails.imAccountType, playerdetails.imAccount)',
						'formatter' => function ($d) use ($permissions, $utils, $scriptListFormatterImAccount4export) {
							return $scriptListFormatterImAccount4export($d,$permissions, $utils);
						},
					);
					$columns[] = array(
						'dt' => $i++, // #22
						'alias' => 'imAccount2',
						'name' => lang('reg.34') . ' 2',
						'select' => 'CONCAT_WS(" ", playerdetails.imAccountType2, playerdetails.imAccount2)',
						'formatter' => function ($d) use ($permissions, $utils, $scriptListFormatterImAccount4export) {
							return $scriptListFormatterImAccount4export($d,$permissions, $utils);
						},
					);
					$columns[] = array(
						'dt' => $i++, // #23
						'alias' => 'imAccount3',
						'name' => lang('reg.34') . ' 3',
						'select' => 'CONCAT_WS(" ", playerdetails.imAccountType3, playerdetails.imAccount3)',
						'formatter' => function ($d) use ($permissions, $utils, $scriptListFormatterImAccount4export) {
							return $scriptListFormatterImAccount4export($d,$permissions, $utils);
						},
					);
				} else { // for HTML
					$columns[] = array(
						'dt' => $i++, // #21
						'alias' => 'imAccount1',
						'name' => lang('reg.34') . ' 1',
						'select' => 'playerdetails.imAccount',
						'formatter' => function ($d) use ($permissions, $utils, $scriptListFormatterImAccount) {
							return $scriptListFormatterImAccount($d,$permissions, $utils);
						},
					);
					$columns[] = array(
						'dt' => $i++, // #22
						'alias' => 'imAccount2',
						'name' => lang('reg.34') . ' 2',
						'select' => 'playerdetails.imAccount2',
						'formatter' => function ($d) use ($permissions, $utils, $scriptListFormatterImAccount) {
							return $scriptListFormatterImAccount($d,$permissions, $utils);
						},
					);
					$columns[] = array(
						'dt' => $i++, // #23
						'alias' => 'imAccount3',
						'name' => lang('reg.34') . ' 3',
						'select' => 'playerdetails.imAccount3',
						'formatter' => function ($d) use ($permissions, $utils, $scriptListFormatterImAccount) {
							return $scriptListFormatterImAccount($d,$permissions, $utils);
						},
					);
				} // EOF if ($is_export)
			// } // EOF if ( $permissions['view_player_detail_contactinfo_im'] ) // Show: All IM columns Marked: All IM account username replaced by *.

			$columns[] = array(
				'dt' => $i++, // #24
				'alias' => 'city',
				'select' => 'playerdetails.city',
				'name' => lang('player_list.fields.city'),  // player_list.fields.country
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					} else {
						return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++, // #25
				'alias' => 'country',
				'select' => 'playerdetails.residentCountry',
				'name' => lang("player_list.fields.country"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					} else {
						return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++, // #26
				'alias' => 'birthdate',
				'select' => 'playerdetails.birthdate',
				'name' => lang("Date of Birth"),
				'formatter' => function ($d, $row) use ($is_export) {
					if($is_export){
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					}else{
						return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
				'dt' => $i++, // #27
				'alias' => 'id_card_number',
				'select' => 'playerdetails.id_card_number',
				'name' => lang("player_list.fields.id_card_number"),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					} else {
						return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			$columns[] = array(
                'dt' => $i++, // #28.1
                'alias' => 'total_deposit_times',
                'select' => 'player.approved_deposit_count', // Same as "The Counter form transactions where playerId, type = ransactions::PLAYER, transaction_type=Transactions::DEPOSIT and status=Transactions::APPROVED."
                'name' => lang('Has Deposit'), // Has Deposit
				'formatter' => function ($d, $row) use ($readOnlyDB, $is_export) {
					$str = '';
					if($d > 0){
						$str = lang('Have');
					}else{
						$str = lang('Have Not');
					}
					return $str;
				},
            );

			$columns[] = array(
				'dt' => $i++, // #29 // Reopen for OGP-15574
				'alias' => 'total_total_nofrozen',
				'select' => 'player.total_total_nofrozen',
				'name' => lang("Total Balance"), // Balance
				'formatter' => 'currencyFormatter',
				// 'formatter' => function ($d) use ($is_export, $wallet_model, $model){
				// 	// $str='0';
				// 	//decode json
				// 	// if(!empty($d)){
				// 		$totalBalance=$wallet_model->getTotalBalanceOnBigWallet($d);
				// 		$str=$model->utils->formatCurrencyNoSym($totalBalance);
				// 	// }
				// 	return $str;
				// }
			);

			$columns[] = array(
				'dt' => $i++, // #30 // Reopen for OGP-15574
				'alias' => 'total_deposit_amount',
				'select' => 'player.totalDepositAmount', // directly referenced from OGP-9124.
				'name' => lang('Total Deposit Amt'), // Total Deposit
				'formatter' => 'currencyFormatter',
			);
			$columns[] = array(
				'dt' => $i++, // #31 // Reopen for OGP-15574
				'alias' => 'total_withdrawal_amount',
				'select' => 'player.approvedWithdrawAmount',
				'name' => lang('Total Withdrawal Amt'), // Total Withdrawal
				'formatter' => function ($d, $row) use ($readOnlyDB, $is_export) {
					// Disable by OGP-9124
					/*$query = $readOnlyDB->query("SELECT sum(transactions.amount) as total_withdrawal FROM transactions WHERE transactions.to_id = ? AND transactions.to_type = ? AND transactions.transaction_type = ? AND transactions.status = ?  ", array(
						$row['playerId'],
						Transactions::PLAYER,
						Transactions::WITHDRAWAL,
						Transactions::APPROVED,
					));
					$r = $query->row_array();

					return $this->data_tables->currencyFormatter(isset($r['total_withdrawal']) ? $r['total_withdrawal'] : 0);*/

					return $this->data_tables->currencyFormatter(isset($d) ? $d : 0);

				},
			);
			$columns[] = array(
				'dt' => $i++, // #32 // Reopen for OGP-15574
				'alias' => 'total_betting_amount',
				'select' => 'player.totalBettingAmount',
				'name' => lang('Total Bet Amt'), // Total Bets
				'formatter' => function ($d, $row) use ($readOnlyDB, $is_export) {
					// Disable by OGP-9124
					/*$query = $readOnlyDB->query("SELECT  SUM(total_player_game_day.betting_amount) as total_bet FROM total_player_game_day WHERE player_id = ?", array(
						$row['playerId'],
					));
					$r = $query->row_array();

					return $this->data_tables->currencyFormatter(isset($r['total_bet']) ? $r['total_bet'] : 0);*/

					return $this->data_tables->currencyFormatter(isset($d) ? $d : 0);

				},
			);

			$columns[] = array(
				'dt' => $i++, // #33 for OGP-15575
				'alias' => 'kyc_level',
				'select' => 'player.playerId', // 'playerdetails.playerId',
				'formatter' => function ($d) use ($is_export,$player_kyc,$kyc_status_model) {
					if ($is_export) {
						return $player_kyc->getPlayerCurrentKycLevel($d) && $kyc_status_model->getPlayerCurrentStatus($d) ? $player_kyc->getPlayerCurrentKycLevel($d) .' / '. $kyc_status_model->getPlayerCurrentStatus($d): lang('lang.norecyet');
					}else{
						return $player_kyc->getPlayerCurrentKycLevel($d) && $kyc_status_model->getPlayerCurrentStatus($d) ? $player_kyc->getPlayerCurrentKycLevel($d) .' / '. $kyc_status_model->getPlayerCurrentStatus($d): '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
				'name' => lang("KYC Level/Rate Code"), // KYC Level/Rate Code
			);
			if ($this->utils->isEnabledFeature('show_risk_score')):
				$columns[] = array(
					'dt' => $i++, // #34 for OGP-15575
					'alias' => 'risk_level',
					'select' => 'player.playerId', // 'playerdetails.playerId',
					'formatter' => function ($d) use ($is_export,$risk_score_model) {
						if ($is_export) {
							return $risk_score_model->getPlayerCurrentRiskLevel($d) && $risk_score_model->generate_total_risk_score($d) ? $risk_score_model->getPlayerCurrentRiskLevel($d) .' / '. $risk_score_model->generate_total_risk_score($d) : lang('lang.norecyet');
						}else{
							return $risk_score_model->getPlayerCurrentRiskLevel($d) && $risk_score_model->generate_total_risk_score($d) ? $risk_score_model->getPlayerCurrentRiskLevel($d) .' / '. $risk_score_model->generate_total_risk_score($d) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}
					},
					'name' => lang("Risk Level/Score"), // Risk Level/Score
				);
			endif;


			$columns[] = array(
				// 'dt' => $i++, // A1. Remove items from the table: Total First Deposit
				'alias' => 'first_deposit',
				'select' => 'player.first_deposit',
				'name' => lang('report.pr19'), // Total First Deposit
				'formatter' => function ($d, $row) use ($readOnlyDB, $is_export) {
					/*$query = $readOnlyDB->query("SELECT transactions.amount FROM transactions JOIN player ON player.playerId = transactions.to_id WHERE transactions.to_id = ? AND  transactions.to_type = ?  AND transactions.transaction_type = ? AND transactions.status = ? ORDER BY created_at ASC LIMIT ?,1", array(
						$row['playerId'],
						Transactions::PLAYER,
						//$d,
						Transactions::DEPOSIT,
						Transactions::APPROVED,
						0, # FIRST DEPOSIT
					));
					$row = $query->row_array();
					return $this->data_tables->currencyFormatter(isset($row['amount']) ? $row['amount'] : 0);*/

					return $this->data_tables->currencyFormatter(isset($d) ? $d : 0);
				},
			);
			$columns[] = array(
				// 'dt' => $i++, // A1. Remove items from the table: Total Second Deposit
				'alias' => 'second_deposit',
				'select' => 'player.second_deposit',
				'name' => lang('report.pr20'), // Total Second Deposit
				'formatter' => function ($d, $row) use ($readOnlyDB, $is_export) {
					/*$query = $readOnlyDB->query("SELECT transactions.amount FROM transactions JOIN player ON player.playerId = transactions.to_id WHERE transactions.to_id = ? AND transactions.to_type = ? AND transactions.transaction_type = ? AND transactions.status = ? ORDER BY created_at ASC LIMIT ?,1", array(
						$row['playerId'],
						Transactions::PLAYER,
						//$d,
						Transactions::DEPOSIT,
						Transactions::APPROVED,
						1, # SECOND DEPOSIT
					));
					$row = $query->row_array();
					return $this->data_tables->currencyFormatter(isset($row['amount']) ? $row['amount'] : 0);*/

					return $this->data_tables->currencyFormatter(isset($d) ? $d : 0);

				},
			);

			if($this->utils->isEnabledFeature('show_allowed_withdrawal_status') && $this->utils->isEnabledFeature('show_risk_score') && $this->utils->isEnabledFeature('show_kyc_status')){
				$columns[] = array(
					// 'dt' => $i++, // A1. Remove items from the table: Allowed Withdrawal Status
					'alias' => 'allowedWithdrawalStatus',
					'select' => 'playerdetails.playerId',
					'name' => lang("Allowed Withdrawal Status"), // Allowed Withdrawal Status
					'formatter' => function ($d) use ($is_export,$risk_score_model) {
						return ($risk_score_model->generate_allowed_withdrawal_status($d)) ? lang('Yes') : lang('No');
					},

				);
			}
			if($this->utils->isEnabledFeature('verification_reference_for_player')){
				$columns[] = array(
					// 'dt' => $i++, // A1. Remove items from the table: Verified
					'alias' => 'verified',
					'select' => 'playerdetails.manual_verification',
					'name' => lang("Verified"), // Verified
					'formatter' => ($is_export) ?
					function ($d) use ($is_export){
						return ($d == 1) ? 'Yes' : 'No';
					} :
					function($d, $row){
						($d == 1) ?
						$output = '<i class="fa fa-check text-success" title="Yes"></i>'
						: $output = '<i class="fa fa-close text-danger" title="No"></i>';
						return $output;
					}
				);
			}
			$columns[] = array(
				// 'dt' => $i++, // A1. Remove items from the table: Device
				'alias' => 'device',
				'select' => 'player_device_last_request.device',
				'name' => lang('con.plm71'), // Device
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					} else {
						return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
			);
			if(!$this->utils->isEnabledFeature('show_zip_code_in_list')){
				$columns[] = array(
					// 'dt' => $i++, // A1. Remove items from the table: Zip Code
					'alias' => 'zipcode',
					'select' => 'playerdetails.zipcode',
					'name' => lang("player.60"), // Zip Code
					'formatter' => function ($d) use ($is_export) {
						if ($is_export) {
							return trim(trim($d), ',') ?: lang('lang.norecyet');
						} else {
							return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
						}
					},


				);
			}

        //update array content if EnabledFeature add_close_status
		if (!$is_export) {
			$columns[] = array(
				// 'dt' => $i++, // A1. Remove items from the table:Attached File
				'alias' => 'proof_filename',
				'select' => 'player.playerId',
				'name' => lang('attached_file'), // Attached File
				'formatter' => function ($d, $row) use ($readOnlyDB, $is_export, $player_attached_proof_file_model) {
					if($is_export){
						return '';
					}
                    $output = '<a onclick="modal(\'/player_management/player_attach_document/' . $row['playerId'] . '\',\'' . lang('Attached document') . '\')" href="javascript:void(0);" data-toggle="tooltip" title="View attached file"><i class="fa fa-search"></i></a>';
                    $imageInfo = $player_attached_proof_file_model->getAttachementRecordInfo($d);
                    if(!empty($imageInfo)){
                    	$output .= ' Y';
                    }else{
                        $output .= ' N';
                    }
					return $output;
				}
			);
		}

		# END DEFINE COLUMNS #################################################################################################################################################
		$this->benchmark->mark('data_gathering_start');
		$table = 'player';
		$joins = array(
			'playerdetails' => 'playerdetails.playerId = player.playerId',
			'player_runtime' => 'player_runtime.playerId = player.playerId',
			'affiliates' => 'affiliates.affiliateId = player.affiliateId',
			'agency_agents' => 'agency_agents.agent_id = player.agent_id',
			'player_device_last_request' => 'player_device_last_request.player_id = player.playerId'
		);
		$group_by = [];

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();
        $group_by = array();

		if($this->utils->isEnabledFeature('show_allowed_withdrawal_status') && $this->utils->isEnabledFeature('show_risk_score') && $this->utils->isEnabledFeature('show_kyc_status')){
			if(isset($input['allowed_withdrawal_status'])){
				$where[] = "player.allowed_withdrawal_status = ?";
				$values[] = $input['allowed_withdrawal_status'];
			}
		}

		if (isset($input['username'], $input['search_by'])) {
			if ($input['search_by'] == 1) {
				$where[] = "player.username LIKE ?";
				$values[] = '%' . $input['username'] . '%';
			} else if ($input['search_by'] == 2) {
				$where[] = "player.username = ?";
				$values[] = $input['username'];
			}
		}

		if (isset($input['email'])
			&& ( $permissions['view_player_detail_contactinfo_em']
			|| true // Patch for OGP-15540
		) ) {
			$where[] = "player.email LIKE ?";
			$values[] = '%' . $input['email'] . '%';
		}

		if (isset($input['registration_date_from'], $input['registration_date_to'])) {
			$where[] = "player.createdOn BETWEEN ? AND ?";
			$values[] = $input['registration_date_from'];
			$values[] = $input['registration_date_to'];
			$totalRegisteredPlayers['createdOn >='] = $input['registration_date_from'];
			$totalRegisteredPlayers['createdOn <='] = $input['registration_date_to'];
		} else {
			$totalRegisteredPlayers['createdOn >='] = date('Y-m-d 00:00:00');
			$totalRegisteredPlayers['createdOn <='] = date('Y-m-d 23:59:59');
		}

        if (isset($input['last_login_date_from'], $input['last_login_date_to'])) {
            $where[] = "player_runtime.lastLoginTime >= ?";
            $values[] = $input['last_login_date_from'];
            $where[] = "player_runtime.lastLoginTime <= ?";
            $values[] = $input['last_login_date_to'];
        }

        // OGP-11882, Date of Birth
        if (isset($input['dob_from'], $input['dob_to'])) {
			if( ! isset($input['with_year']) ){
				$with_year = true;
			}else{
				if( $input['with_year'] ){
					$with_year = true;
				}else{
					$with_year = false;
				}
			}

			if($with_year){
				// VARCHAR convert to DATE
				$where[] = "STR_TO_DATE(playerdetails.birthdate,'%Y-%m-%d') BETWEEN STR_TO_DATE(?,'%Y-%m-%d')  AND STR_TO_DATE(?,'%Y-%m-%d')";
				$values[] = $input['dob_from'];
				$values[] = $input['dob_to'];
			}else{ // without year

				// Get month and date of every year in the specified range.
				$currYear = date("Y");
				$dob_from = $currYear. '-'. $input['dob_from'];
				$dob_to = $currYear. '-'. $input['dob_to'];
				$period = new DatePeriod(
					new DateTime($dob_from),
					new DateInterval('P1D'),
					new DateTime($dob_to)
			   );
			   $mdRange = array();
			   foreach ($period as $key => $value) {
					$md = $value->format('m-d');
					$mdRange[$md] = $md;
				}
				// append endDate of period
				$endDate = $period->getEndDate();
				$md = $endDate->format('m-d');
				$mdRange[$md] = $md;
				// Detect 02-29 and append into $mdRange
				$lookup0228 = array_key_exists('02-28',$mdRange);
				$lookup0301 = array_key_exists('03-01',$mdRange);
				if($lookup0228 && $lookup0301){
					$md = '02-29';
					$mdRange[$md] = $md;
				}
				// combine dates of every year
				$subWhere = array();
				foreach ($mdRange as $key => $value) {
					$subWhere[] = "playerdetails.birthdate LIKE '%-$value'";
				}
				$where[] = '('. implode(' OR ', $subWhere). ')';

				/// Patch/Disable for OGP-15372 New player list DOB searching condition issue
				// $values[] = $input['dob_from'];
				// $values[] = $input['dob_to'];

			} // EOF if($with_year)
		} // EOF if (isset($input['dob_from'], $input['dob_to']))

        if (isset($input['registered_by'])) {
			if( $input['registered_by'] == Player_model::REGISTERED_BY_AGENCY_CREATED ){ // Patch for OGP-15085 Modify the logic of condition_Registered By: SBE_Player > All Player
				$where[] = "player.agent_id IS NOT NULL /* registered_by=created_on_agency */ ";
				$where[] = "(player.registered_by = ? OR player.registered_by = ? /* registered_by=created_on_agency */ )";
				$values[] = Player_model::REGISTERED_BY_MASS_ACCOUNT;
				$values[] = Player_model::REGISTERED_BY_AGENCY_CREATED;
			}else{
				$where[] = "player.registered_by = ?";
				$values[] = $input['registered_by'];
			}
		}
		// $input['created_on_agency']



		if (isset($input['ip_address'])) {
			$where[] = "playerdetails.registrationIp = ?";
			$values[] = $input['ip_address'];
		}

		if (isset($input['lastLoginIp'])) {
			$where[] = "player_runtime.lastLoginIp = ?";
			$values[] = $input['lastLoginIp'];
		}

		if (isset($input['device'])) {
			$where[] = "player_device_last_request.device = ?";
			$values[] = $input['device'];
		}


        if (isset($input['blocked'])) {
            switch($input['blocked']){
                case 0:
                    $where[] = "player.blockedUntil = 0";
                    $where[] = "player.blocked = ?";
                    if ($this->utils->isEnabledFeature('responsible_gaming')) {
                        $where[] = "player.playerId NOT IN (SELECT player_id FROM responsible_gaming WHERE (`type` = 1 AND `status` = 2) OR (`type` = 1 AND `status` = 7) OR (`type` = 2 AND `status` = 2) OR (`type` = 3 AND `status` = 2) )";
                    }
					$values[] = $input['blocked'];
					break;
                case Player_model::BLOCK_STATUS:
                case Player_model::SUSPENDED_STATUS:
                case Player_model::STATUS_BLOCKED_FAILED_LOGIN_ATTEMPT:
                    $where[] = "player.blocked = ?";
                    $values[] = $input['blocked'];
                    if ($this->utils->isEnabledFeature('responsible_gaming')) {
                        $where[] = "player.playerId NOT IN (SELECT player_id FROM responsible_gaming WHERE (`type` = 1 AND `status` = 2) OR (`type` = 1 AND `status` = 7) OR (`type` = 2 AND `status` = 2) OR (`type` = 3 AND `status` = 2) )";
                    }
                    break;
                case Player_model::SELFEXCLUSION_STATUS:
                    $where[] = "player.playerId IN (SELECT player_id FROM responsible_gaming WHERE (`type` = 1 AND `status` = 2) OR (`type` = 1 AND `status` = 7) OR (`type` = 2 AND `status` = 2) OR (`type` = 3 AND `status` = 2) )";
                    break;
            }
            $this->utils->debug_log("blocked in post");
        }else{
            $this->utils->debug_log("blocked not in post");
        }

		if (isset($input['player_level'])) {
			$where[] = "player.levelId = ?";
			$values[] = $input['player_level'];
		}

		if (isset($input['friend_referral_code'])) {
			$where[] = "player.invitationCode = ?";
			$values[] = $input['friend_referral_code'];
		}

		if (isset($input['deposit'])) {
			switch ($input['deposit']) {
			case 0:
				$where[] = "player.totalDepositAmount <= 0";
				break;

			case 1:
				$where[] = "player.totalDepositAmount > 0";
				break;
			}
		}

		if (isset($input['first_name'])) {
			$where[] = "playerdetails.firstName LIKE ?";
			$values[] = '%' . $input['first_name'] . '%';
		}

		if (isset($input['last_name'])) {
			$where[] = "playerdetails.lastName LIKE ?";
			$values[] = '%' . $input['last_name'] . '%';
		}

		if (isset($input['registration_website'], $input['reg_web_search_by'])) {
			if ($input['reg_web_search_by'] == 1) {
				$where[] = "playerdetails.registrationWebsite LIKE ?";
				$values[] = '%' . $input['registration_website'] . '%';
			} else if ($input['reg_web_search_by'] == 2) {
				$where[] = "playerdetails.registrationWebsite = ?";
				$values[] = $input['registration_website'];
			}
		}

		if (isset($input['city'])) {
			$where[] = "playerdetails.city = ?";
			$values[] = $input['city'];
		}

        if (isset($input['residentCountry'])) {
         $where[] = "playerdetails.residentCountry = ?";
         $values[] = $input['residentCountry'];
        }

		if (isset($input['im_account'])
			&& ( $permissions['view_player_detail_contactinfo_im']
			|| true // Patch for OGP-15540
		) ) {
			$where[] = "(playerdetails.imAccount = ? OR playerdetails.imAccount2 = ? OR playerdetails.imAccount3 = ?)";
			$values[] = $input['im_account'];
			$values[] = $input['im_account'];
			$values[] = $input['im_account'];
		}


		// $where[] = "playeraccount.type IN ('wallet', 'subwallet')";
		// $group_by[] = "playeraccount.playerId";

		/// total_balance_more_than / total_balance_less_than convert to wallet_order
		if ( isset($input['total_balance_more_than']) ) {
			$input['wallet_order'] = '-1';
			$input['wallet_amount_from'] = $input['total_balance_more_than'];
		}
		if( isset($input['total_balance_less_than']) ){
			$input['wallet_order'] = '-1';
			$input['wallet_amount_to'] = $input['total_balance_less_than'];
		}

        if (isset($input['wallet_order']) && (isset($input['wallet_amount_from']) || isset($input['wallet_amount_to']))) {
            if ($input['wallet_order'] == '-1') {
                if (isset($input['wallet_amount_from'])) {
                    $where[] = "player.total_total_nofrozen >= ?";
                    $values[] = $input['wallet_amount_from'];
                }

                if (isset($input['wallet_amount_to'])) {
                    $where[] = "player.total_total_nofrozen <= ?";
                    $values[] = $input['wallet_amount_to'];
                }
            } else {
                $joins['playeraccount'] = 'playeraccount.playerId = player.playerId';

                if ($input['wallet_order'] == 0) {
                    $where[] = "playeraccount.type = ?";
                    $values[] = 'wallet';
                } else {
                    $where[] = "playeraccount.type = ? AND playeraccount.typeId = ?";
                    $values[] = 'subwallet';
                    $values[] = $input['wallet_order'];
                }

                if (isset($input['wallet_amount_from'])) {
                    $where[] = "playeraccount.totalBalanceAmount >= ?";
                    $values[] = $input['wallet_amount_from'];
                }

                if (isset($input['wallet_amount_to'])) {
                    $where[] = "playeraccount.totalBalanceAmount <= ?";
                    $values[] = $input['wallet_amount_to'];
                }
            }
        }


        if (isset($input['blocked_gaming_networks'])) {
            $joins['game_provider_auth'] = 'game_provider_auth.player_id = player.playerId';
            $where[] = "game_provider_auth.game_provider_id = ? AND is_blocked = ?";
            $values[] = $input['blocked_gaming_networks'];
            $values[] = 1;
        }

        if (isset($input['promo']) || isset($input['promoCode'])) {
            $joins['playerpromo'] = 'playerpromo.playerId = player.playerId';

            if (isset($input['promo'])) {
                $where[] = "playerpromo.promorulesId = ?";
                $values[] = $input['promo'];
            }

            if (isset($input['promoCode'])) {
                $joins['promorules'] = 'promorules.promorulesId = playerpromo.promorulesId';
                $where[] = "promorules.promoCode = ?";
                $values[] = $input['promoCode'];
            }
        }


		if (isset($input['no_affiliate']) && $input['no_affiliate'] == true) {

			$where[] = "player.affiliateId IS NULL";

		} else {

			if (isset($input['affiliate'])) {

				$this->load->model('affiliatemodel');
				$affiliateId = $this->affiliatemodel->getAffiliateIdByUsername($input['affiliate']);
				$affiliateIds = null;

				if (isset($input['aff_include_all_downlines']) && $input['aff_include_all_downlines']
					&& !empty($affiliateId)) {
					$affiliateIds = $this->affiliatemodel->includeAllDownlineAffiliateIds($affiliateId);
					// $where[] = 'player.affiliateId IN('.implode(',', $affiliateIds).')';
				}

				if (!empty($affiliateIds)) {
					$where[] = 'player.affiliateId IN(' . implode(',', $affiliateIds) . ')';
				} else {
					$where[] = "affiliates.username like ?";
					$values[] = '%' . $input['affiliate'] . '%';
				}
			}

			if (isset($input['affiliate_tag_id'])) {
				$joins['affiliatetag'] = 'affiliatetag.affiliateId = player.affiliateId';
				$where[] = 'affiliatetag.tagId = ?';
				$values[] = $input['affiliate_tag_id'];
			}

		} // EOF if (isset($input['no_affiliate']) && $input['no_affiliate'] == true)

		if (isset($input['created_on_agency']) && $input['created_on_agency'] == true) {
			$where[] = "player.agent_id IS NOT NULL";
		}
		if (isset($input['agent'])) {
			if (isset($input['include_all_downlines']) && $input['include_all_downlines'] == true) {
				$joins['agency_agents'] = 'player.agent_id = agency_agents.agent_id';
				$parent_ids = array($input['agent']);
				$sub_ids = array();
				$all_ids = $parent_ids;
				while (!empty($sub_ids = $this->agency_model->get_sub_agent_ids_by_parent_id($parent_ids))) {
					$all_ids = array_merge($all_ids, $sub_ids);
					$parent_ids = $sub_ids;
					$sub_ids = array();
				}
				foreach ($all_ids as $i => $id) {
					if ($i == 0) {
						$w = "(player.agent_id = ?";
					} else {
						$w .= " OR player.agent_id = ?";
					}
					$values[] = $id;
				}
				$w .= ")";
				$where[] = $w;
			} else {
				$where[] = "player.agent_id = ?";
				$values[] = $input['agent'];
			}
		}
		if (!empty($input['referred_by'])) {
			$referred_player = $this->player_model->getPlayerByUsername($input['referred_by']);
			$where[] = "player.refereePlayerId = ? /* referred_by */";
			if($referred_player) {
				$values[] = $referred_player->playerId;
			} else {
				$values[] = NULL;
			}
		}
		if (isset($input['deposit_count'])) {
			$where[] = "player.approved_deposit_count = ? /* deposit_count */";
            $values[] = $input['deposit_count'];
		}

		if (isset($input['agent_name'])) {
			$hasAgent_id = false; // default
			if ( (	// orig
					isset($input['include_all_downlines'])
					&& $input['include_all_downlines'] == true
				) || ( // OGP-14711
					isset($input['own_downline_or_agency_line'])
					&& $input['own_downline_or_agency_line'] == 'own_downline'
				)
			) {
				/// eq. Own Downline 直属
				// 該代理下面的玩家及下一層代理的玩家"

                $agent = $this->agency_model->get_agent_by_name($input['agent_name']);
                $agent_id = $agent['agent_id'];

                if( ! empty($agent_id)){
                    $parent_ids = array($agent_id);
                    $sub_ids = $this->agency_model->get_sub_agent_ids_by_parent_id($parent_ids);
                    $all_ids = array_merge($parent_ids, $sub_ids);

                    $where[] = 'player.agent_id IN(' . implode(',', $all_ids) . ') /* own_downline_or_agency_line=direct_downline */';
                    $group_by[] = 'player.playerId';
					$hasAgent_id = true;
                }
			}else if( isset($input['own_downline_or_agency_line'])
				&& $input['own_downline_or_agency_line'] == 'agency_line'
			){ // OGP-14711
				$agent = $this->agency_model->get_agent_by_name($input['agent_name']);
				$agent_id = $agent['agent_id'];
				if(!empty($agent_id)){
					// 代理下面的整條玩家
					$idArr=$this->agency_model->get_all_downline_arr($agent_id);
					$where[] = 'player.agent_id IN(' . implode(',', $idArr) . ') /* own_downline_or_agency_line=agency_line */';
					$group_by[] = 'player.playerId';
					$hasAgent_id = true;
				}
			}else if (! empty($input['agent_name']) ) {
				$hasAgent_id = false;
			}

			if( ! $hasAgent_id ){
				$where[] = "agency_agents.agent_name like ?";
                $values[] = '%' . $input['agent_name'] . '%';
			}
		}

		if (isset($input['account_type'])) {
			$joins['playeraccount playertype'] = 'playertype.playerId = player.playerId';
			$where[] = "playertype.typeOfPlayer = ?";
			$values[] = $input['account_type'];
		}

		# deprecated
		if (isset($input['tagged'])) {

			$joins['playertag'] = 'playertag.playerId = player.playerId';

			if($input['tagged'] == 'no_tag'){
				$where[] = "playertag.tagId IS NULL";
			} else {
				$where[] = "playertag.tagId = ?";
            	$values[] = $input['tagged'];
			}
		}

		if (isset($input['tag_list'])) {

			$tag_list = $input['tag_list'];

			if(is_array($tag_list)) {
				$notag = array_search('notag',$tag_list);
				if($notag !== false) {
					$where[] = 'player.playerId IN (SELECT DISTINCT playerId FROM playertag)';
					unset($tag_list[$notag]);
				}

			} elseif ($tag_list == 'notag') {
				$where[] = 'player.playerId IN (SELECT DISTINCT playerId FROM playertag)';
				$tag_list = null;
			}

			if (!empty($tag_list)) {
				$tagList = is_array($tag_list) ? implode(',', $tag_list) : $tag_list;
				$where[] = 'player.playerId NOT IN (SELECT DISTINCT playerId FROM playertag WHERE playertag.tagId IN ('.$tagList.'))';
			}

		}

		if (isset($input['contactNumber'])
			&& ( $permissions['view_player_detail_contactinfo_cn']
			|| true // Patch for OGP-15540
		) ) {
			$where[] = "playerdetails.contactNumber LIKE ?";
			$values[] = '%' . $input['contactNumber'] . '%';
		}

		if (isset($input['phone_status'])) {
			$where[] = "player.verified_phone = ?";
			$values[] = $input['phone_status'];
		}

		if (isset($input['email_status'])) {
			$where[] = "player.verified_email = ?";
			$values[] = $input['email_status'];
		}

		if (isset($input['game_username'])) {
			$joins['game_provider_auth'] = 'game_provider_auth.player_id = player.playerId';
			$where[] = "game_provider_auth.login_name = ?";
			$values[] = $input['game_username'];
		}

		if (isset($input['player_bank_account_number'])) {
			$where[] = "player.playerId in (SELECT playerId FROM `playerbankdetails` WHERE `bankAccountNumber` = ?)";
			$values[] = $input['player_bank_account_number'];
		}

        if (isset($input['id_card_number'])) {
            $where[] = "playerdetails.id_card_number LIKE ?";
            $values[] = '%' . $input['id_card_number'] . '%';
        }

		if(!$this->utils->isEnabledFeature('show_game_history_of_deleted_player')){
        	$where[] = "player.deleted_at IS NULL";
        }

		# END PROCESS SEARCH FORM #################################################################################################################################################
		if($is_export){
            $this->data_tables->options['is_export']=true;
            if(empty($csv_filename)){
                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
            }
            $this->data_tables->options['csv_filename']=$csv_filename;
		}

		$having=[];
		$distinct=true;
		$external_order=[];
		$not_datatable='';
		$countOnlyField='player.playerId';
		$this->benchmark->mark('data_gathering_end');

		$this->benchmark->mark('playerList_result_start');

		$columns = $this->checkIfEnabled($this->utils->isEnabledFeature('show_risk_score'), array('risk_level'), $columns);
		$columns = $this->checkIfEnabled($this->utils->isEnabledFeature('show_kyc_status'), array('kyc_level'), $columns);
		$columns = $this->checkIfEnabled(!$this->utils->isEnabledFeature('close_aff_and_agent'), array('affiliate','agent'), $columns);

		$this->utils->debug_log('playerlist column ======>', $columns);
		$this->config->set_item('debug_data_table_sql', true);
		$result = $this->data_tables->get_data(	$request // #1
												, $columns // #2
												, $table // #3
												, $where // #4
												, $values // #5
												, $joins // #6
												, $group_by // #7
												, $having // #8
												, $distinct // #9
												, $external_order // #10
												, $not_datatable // #11
												, $countOnlyField // #12
											);
		$sqls = $this->data_tables->last_query;
		$this->utils->debug_log('result done');
		$this->utils->debug_log('result data  ======>', count($result));
		if($is_export){
		    //drop result if export
			return $csv_filename;
		}
		$this->benchmark->mark('playerList_result_end');

		$this->benchmark->mark('playerList_summary_start');
		$summary = $this->data_tables->summary($request, $table, $joins, 'SUM(player.total_total_nofrozen) total_balance, SUM(player.totalBettingAmount) total_bets_amount, SUM(player.approvedWithdrawAmount) total_withdraw, SUM(player.totalDepositAmount) total_deposit', null, $columns, $where, $values);
		$result['summary'] = $summary;
		$this->benchmark->mark('playerList_summary_end');

		$result['benchmarks']['data_gathering'] = $this->utils->getMarkProfiler('data_gathering');
		$result['benchmarks']['playerList_result'] = $this->utils->getMarkProfiler('playerList_result');
		$result['benchmarks']['playerList_summary'] = $this->utils->getMarkProfiler('playerList_summary');
		$result['sqls'] = $sqls;
		$result['dbg_request'] = $where;
		$result['dbg_input'] =$input;// ['registered_by'];
		$result['dbg_request'] = $request;
		$this->utils->debug_log(array(
			'playerList data gathering================================' => $this->benchmark->elapsed_time('data_gathering_start', 'data_gathering_end'),
		 	'playerList result=============================' => $this->benchmark->elapsed_time('playerList_result_start', 'playerList_result_end'),
		 	'playerList Summary============================' => $this->benchmark->elapsed_time('playerList_summary_start', 'playerList_summary_end'),
		));
		return $result;
	}// EOF player_list_reports

	/**
	 * detail: get agency player reports
	 *
	 * @param array $request
	 * @param Boolean $viewPlayerInfoPerm
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function get_agency_player_reports($request, $viewPlayerInfoPerm, $is_export = false) {
		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->data_tables->is_export = $is_export;

		$this->load->model(array('transactions', 'game_logs', 'total_player_game_minute'));
		$i = 0;
		$input = $this->data_tables->extra_search($request);
		$table = 'player';
		$joins = array();
		$where = array();
		$values = array();
		$group_by = array();
		$having = array();

		$joins['playerdetails'] = 'playerdetails.playerId = player.playerId';
		$joins['player_runtime'] = 'player_runtime.playerId = player.playerId';

		# DEFINE TABLE COLUMNS ########################################################################################################################################################################
		$columns = array(
			array(
				'alias' => 'playerId',
				'select' => 'player.playerId',
			),
			array(
				'alias' => 'group_by',
				'select' => isset($input['group_by']) ? '\'' . $input['group_by'] . '\'' : 'NULL',
			),
			array(
				'dt' => $i++,
				'alias' => 'username',
            	'select' => isset($input['group_by']) ? $input['group_by'] == 'player.playerId' ? 'player.username' : "''" : 'NULL',
            	'formatter' => 'defaultFormatter',
				'name' => lang('report.pr01'),
			),
			array(
				'alias' => 'levelName',
				'select' => 'player.levelName',
			),
			array(
				'dt' => $i++,
				'alias' => 'member_level',
				'select' => 'player.groupName',
				'formatter' => function ($d, $row) use ($is_export) {
					return lang($d)." - ".lang($row['levelName']);
				},
				'name' => lang('report.pr03'),
			),
			array(
				'dt' => $i++,
				'alias' => 'total_deposit',
				'select' => 'player.totalDepositAmount',
				'formatter' => function($d, $row) {
					return $this->data_tables->currencyFormatter($d);
				},
				'name' => lang('report.pr21'),
			),
			array(
				'dt' => $i++,
				'alias' => 'total_withdrawal',
				'select' => 'player.approvedWithdrawAmount',
				'formatter' => function($d, $row){
					return $this->data_tables->currencyFormatter($d);
				},
				'name' => lang('report.pr22'),
			),
			array(
				'dt' => $i++,
				'alias' => 'total_bets',
				'select' => $this->utils->getConfig('use_total_minute') ? 'SUM(total_player_game_minute.betting_amount)' : 'SUM(game_logs.bet_amount)',
				'formatter' => function($d, $row)  {
					return $this->utils->formatCurrencyNoSym($d);
				},
				'name' => lang('Total Bets'),
			),
			array(
				'dt' => $i++,
				'alias' => 'payout',
				'select' => $this->utils->getConfig('use_total_minute') ? 'SUM(total_player_game_minute.betting_amount) - (SUM(total_player_game_minute.loss_amount) - SUM(total_player_game_minute.win_amount))' : 'SUM(game_logs.bet_amount) - (SUM(game_logs.loss_amount) - SUM(game_logs.win_amount))',
				'formatter' => function($d, $row)  {
					return $this->utils->formatCurrencyNoSym($d);
				},
				'name' => lang('Payout'),
			),
			array(
				'dt' => $i++,
				'alias' => 'total_wins',
				'select' => $this->utils->getConfig('use_total_minute') ? 'SUM(total_player_game_minute.win_amount)' : 'SUM(game_logs.win_amount)',
				'formatter' => function($d, $row) {
					return $this->utils->formatCurrencyNoSym($d);
				},
				'name' => lang('Total Wins'),
			),
			array(
				'dt' => $i++,
				'alias' => 'total_loss',
				'select' => $this->utils->getConfig('use_total_minute') ? 'SUM(total_player_game_minute.loss_amount)' : 'SUM(game_logs.loss_amount)',
				'formatter' => function($d, $row) {
					return $this->utils->formatCurrencyNoSym($d);
				},
				'name' => lang('Total Loss'),
			),
			array(
				'dt' => $i++,
				'alias' => 'net_gaming',
				'select' => $this->utils->getConfig('use_total_minute') ? 'SUM(total_player_game_minute.loss_amount) - SUM(total_player_game_minute.win_amount)' : 'SUM(game_logs.loss_amount) - SUM(game_logs.win_amount)',
				'formatter' => function($d, $row) {
					return $d < 0 ? '<span class="text-danger">' . $this->utils->formatCurrencyNoSym($d) . '</span>' : $this->utils->formatCurrencyNoSym($d);
				},
				'name' => lang('Net Gaming'),
			),
			array(
				'dt' => $i++,
				'alias' => 'createdOn',
				'select' => 'player.createdOn',
				'formatter' => 'dateTimeFormatter',
				'name' => lang('report.pr10'),
			),
		);

		# FILTER ######################################################################################################################################################################################

		$dateTimeFrom = null;
		$dateTimeTo = null;

		if (isset($input['search_on_date']) && $input['search_on_date']) {
			if (isset($input['date_from'], $input['date_to'])) {
				$dateTimeFrom = $input['date_from'];
				$dateTimeTo = $input['date_to'];

				$where[] = "player.createdOn BETWEEN ? AND ?";
				$values[] = $dateTimeFrom;
				$values[] = $dateTimeTo;
			}
		}

		if($this->utils->getConfig('use_total_minute')){
			$total_player_game_minute_join = 'total_player_game_minute.player_id = player.playerId ';

			$joins['total_player_game_minute'] = $total_player_game_minute_join;
		}
		else{

			$game_logs_join = 'game_logs.flag = '.Game_logs::FLAG_GAME.' AND game_logs.player_id = player.playerId ';

			$joins['game_logs'] = $game_logs_join;
		}

		if (isset($input['group_by'])) {
			$group_by[] = $input['group_by'];
		}

		if (isset($input['username'])) {
			$where[] = "player.username LIKE ?";
			$values[] = '%' . $input['username'] . '%';
		}

		if (isset($input['playerlevel'])) {
			$where[] = "player.levelId = ?";
			$values[] = $input['playerlevel'];
		}

		if (isset($input['depamt1'])) {
			$having['total_deposit <='] = $input['depamt1'];
		}

		if (isset($input['depamt2'])) {
			$having['total_deposit >='] = $input['depamt2'];
		}

		if (isset($input['widamt1'])) {
			$having['total_withdrawal <='] = $input['widamt1'];
		}

		if (isset($input['widamt2'])) {
			$having['total_withdrawal >='] = $input['widamt2'];
		}
		if (isset($input['only_under_agency']) && $input['only_under_agency'] != '') {
			$where[] = "player.agent_id IS NOT NULL";
			if (!isset($input['agent_name'])) {
				if (isset($input['current_agent_name']) && $input['current_agent_name'] != '') {
					$input['agent_name'] = $input['current_agent_name'];
				}
			}
		}
		if (isset($input['agent_name'])) {
			$this->load->model(array('agency_model'));
			$agent_detail = $this->agency_model->get_agent_by_name($input['agent_name']);
			$this->utils->debug_log('agent_detail', $agent_detail);

			if (isset($input['include_all_downlines']) && $input['include_all_downlines'] == true) {
				$joins['agency_agents'] = 'player.agent_id = agency_agents.agent_id';
				$parent_ids = array($agent_detail['agent_id']);
				$sub_ids = array();
				$all_ids = $parent_ids;
				while (!empty($sub_ids = $this->agency_model->get_sub_agent_ids_by_parent_id($parent_ids))) {
					$this->utils->debug_log('sub_ids', $sub_ids);
					$all_ids = array_merge($all_ids, $sub_ids);
					$parent_ids = $sub_ids;
					$sub_ids = array();
				}
				foreach ($all_ids as $i => $id) {
					if ($i == 0) {
						$w = "(player.agent_id = ?";
					} else {
						$w .= " OR player.agent_id = ?";
					}
					$values[] = $id;
				}
				$w .= ")";
				$where[] = $w;
			} else {
				$where[] = "player.agent_id = ?";
				$values[] = $agent_detail['agent_id'];
			}
		}
		$this->utils->debug_log('GET_AGENCY_PLAYER_REPORTSS where values', $where, $values);
		# OUTPUT ######################################################################################################################################################################################
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having);
		if($this->utils->getConfig('use_total_minute')) {

			$summary = $this->data_tables->summary(
				$request,
				$table,
				$joins,
				'SUM(total_player_game_minute.betting_amount) total_bets, '.
				'SUM(total_player_game_minute.betting_amount) - (SUM(total_player_game_minute.loss_amount) - SUM(total_player_game_minute.win_amount)) payout,'.
				'SUM(total_player_game_minute.win_amount) total_wins,'.
				'SUM(total_player_game_minute.loss_amount) total_loss,'.
				'SUM(total_player_game_minute.loss_amount) - SUM(total_player_game_minute.win_amount) net_gaming',
				null,
				$columns,
				$where,
				$values
			);
			unset($joins['total_player_game_minute']);
		} else {

			$summary = $this->data_tables->summary(
				$request,
				$table,
				$joins,
				'SUM(game_logs.bet_amount) total_bets,'.
				'SUM(game_logs.bet_amount) - (SUM(game_logs.loss_amount) - SUM(game_logs.win_amount)) payout,'.
				'SUM(game_logs.win_amount) total_wins, SUM(game_logs.loss_amount) total_loss,'.
				'SUM(game_logs.loss_amount) - SUM(game_logs.win_amount) net_gaming',
				null,
				$columns,
				$where,
				$values
			);
			unset($joins['game_logs']);
		}
		$summary_player = $this->data_tables->summary(
			$request,
			$table,
			$joins,
			'SUM(player.totalDepositAmount) total_deposit,'.
			'SUM(player.approvedWithdrawAmount) total_withdrawal',
			null,
			$columns,
			$where,
			$values
		);

		$result['totals'] = array_merge($summary_player[0],$summary[0]);

		return $result;
	}

	public function get_agency_player_reports_hourly($request, $viewPlayerInfoPerm, $is_export = false) {

        $readOnlyDB = $this->getReadOnlyDB();

        $this->load->library('data_tables', array('DB' => $readOnlyDB));

        $this->data_tables->is_export = $is_export;

        $i          = 0;
        $input      = $this->data_tables->extra_search($request);
        $joins      = array();
        $where      = array();
        $values     = array();
        $group_by   = array();
        $having     = array();
		$start      = $request['start'] + 1;

        $only_show_non_zero_player = isset($input['only_show_non_zero_player']) ? $input['only_show_non_zero_player'] == 'true' : false;

        # DEFINE TABLE COLUMNS ########################################################################################################################################################################
        $columns = array();

        $columns[] = array(
            'alias' => 'group_by',
            'select' => $input['group_by'],
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'username',
            'select' => $input['group_by'] == 'player.playerId' ? 'player.username' : "''",
            'formatter' => 'defaultFormatter',
            'name' => lang('report.pr01'),
        );

        $columns[] = array(
			'alias' => 'levelName',
			'select' => 'player.levelName',
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'member_level',
			'select' => 'player.groupName',
            'formatter' => function ($d, $row) {
            	return lang($d)." - ".lang($row['levelName']);
            },
            'name' => lang('report.pr03'),
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'total_deposit',
            'select' => 'SUM(player_report_hourly.total_deposit)',
			'formatter' => function($d, $row){
				return $this->data_tables->currencyFormatter($d);
			},
            'name' => lang('report.pr21'),
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'total_withdrawal',
            'select' => 'SUM(player_report_hourly.total_withdrawal)',
			'formatter' => function($d, $row){
				return $this->data_tables->currencyFormatter($d);
			},
            'name' => lang('report.pr22'),
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'total_bets',
            'select' => 'SUM(player_report_hourly.total_bet)',
			'formatter' => function($d, $row) {
				return $this->data_tables->currencyFormatter($d);
			},
            'name' => lang('Total Bets'),
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'total_payout',
            'select' => 'SUM(player_report_hourly.total_bet + player_report_hourly.total_result)',
			'formatter' => function($d, $row) {
				return $this->data_tables->currencyFormatter($d);
			},
            'name' => lang('Payout'),
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'total_wins',
            'select' => 'SUM(player_report_hourly.total_win)',
			'formatter' => function($d, $row){
				return $this->data_tables->currencyFormatter($d);
			},
            'name' => lang('Total Wins'),
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'total_loss',
            'select' => 'SUM(player_report_hourly.total_loss)',
            'formatter' => function($d, $row){
				return $this->data_tables->currencyFormatter($d);
			},
            'name' => lang('Total Loss'),
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'net_gaming',
            'select' => 'SUM(-player_report_hourly.total_result)',
            'formatter' => function($d, $row){
				return $this->data_tables->currencyFormatter($d);
			},
            'name' => lang('Net Gaming'),
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'createdOn',
            'select' => 'player.createdOn',
            'formatter' => 'dateTimeFormatter',
            'name' => lang('report.pr10'),
        );

        # FILTER ######################################################################################################################################################################################
        $dateTimeFrom = null; $dateTimeTo = null;

        if ($only_show_non_zero_player) {

            $table = 'player_report_hourly';

            $joins['player'] = 'player.playerId = player_report_hourly.player_id';
            $joins['agency_agents'] = 'agency_agents.agent_id = player.agent_id';

            if (isset($input['search_on_date']) && $input['search_on_date']) {
                if (isset($input['date_from'], $input['date_to'])) {
                    $dateTimeFrom = $input['date_from'];
                    $dateTimeTo   = $input['date_to'];

                    $where[]  = "player_report_hourly.date_hour >= ? AND player_report_hourly.date_hour <= ?";
                    $values[] = $this->utils->formatDateHourForMysql(new DateTime($dateTimeFrom));
                    $values[] = $this->utils->formatDateHourForMysql(new DateTime($dateTimeTo));
                }
            }

        } else {

            $table = 'player';

            $joins['agency_agents'] = 'agency_agents.agent_id = player.agent_id';

            $transactions_sql = 'player_report_hourly.player_id = player.playerId ';

            if (isset($input['search_on_date']) && $input['search_on_date']) {
                if (isset($input['date_from'], $input['date_to'])) {
                    $dateTimeFrom = $input['date_from'];
                    $dateTimeTo   = $input['date_to'];

                    $transactions_sql .= " AND player_report_hourly.date_hour >= '" . $this->utils->formatDateHourForMysql(new DateTime($dateTimeFrom)) . "'";
                    $transactions_sql .= " AND player_report_hourly.date_hour <= '" . $this->utils->formatDateHourForMysql(new DateTime($dateTimeTo)) . "'";
                }
            }

            $joins['player_report_hourly'] = $transactions_sql;

        }

        if (isset($input['username'])) {
            $where[] = "player.username LIKE ?";
            $values[] = '%' . $input['username'] . '%';
        }

        if (isset($input['playerlevel'])) {
            $where[] = "player.levelId = ?";
            $values[] = $input['playerlevel'];
        }

        if (isset($input['group_by'])) {
            $group_by[] = $input['group_by'];
        }

        if (isset($input['depamt1'])) {
            $having['total_deposit <='] = $input['depamt1'];
        }

        if (isset($input['depamt2'])) {
            $having['total_deposit >='] = $input['depamt2'];
        }

        if (isset($input['widamt1'])) {
            $having['total_withdrawal <='] = $input['widamt1'];
        }

        if (isset($input['widamt2'])) {
            $having['total_withdrawal >='] = $input['widamt2'];
        }

        if (isset($input['only_under_agency']) && $input['only_under_agency'] != '') {
            if ( ! isset($input['agent_name'])) {
                if (isset($input['current_agent_name']) && $input['current_agent_name'] != '') {
                    $input['agent_name'] = $input['current_agent_name'];
                }
            }
        }

        if (isset($input['agent_name'])) {

            $this->load->model(array('agency_model'));

            $agent_detail = $this->agency_model->get_agent_by_name($input['agent_name']);

            if (isset($input['include_all_downlines']) && $input['include_all_downlines'] == true) {

                $parent_ids = array($agent_detail['agent_id']);

                $sub_ids = array();
                $all_ids = $parent_ids;

                while ( ! empty($sub_ids = $this->agency_model->get_sub_agent_ids_by_parent_id($parent_ids))) {
                    $all_ids = array_merge($all_ids, $sub_ids);
                    $parent_ids = $sub_ids;
                    $sub_ids = array();
                }

                foreach ($all_ids as $i => $id) {
                    if ($i == 0) {
                        $w = "(player.agent_id = ?";
                    } else {
                        $w .= " OR player.agent_id = ?";
                    }
                    $values[] = $id;
                }

                $w .= ")";

                $where[] = $w;

            } else {
                $where[] = "player.agent_id = ?";
                $values[] = $agent_detail['agent_id'];
            }

        }
        # OUTPUT ######################################################################################################################################################################################
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having);

		$summary = $this->data_tables->summary(
			$request,
			$table,
			$joins,
			'SUM(player_report_hourly.total_deposit) total_deposit,'.
			'SUM(player_report_hourly.total_withdrawal) total_withdrawal,'.
			'SUM(player_report_hourly.total_bet) total_bets, '.
			'SUM(player_report_hourly.total_bet + player_report_hourly.total_result) payout,'.
			'SUM(player_report_hourly.total_win) total_wins,'.
			'SUM(player_report_hourly.total_loss) total_loss,'.
			'SUM(-player_report_hourly.total_result) net_gaming',
			null,
			$columns,
			$where,
			$values
		);

		$result['totals'] = $summary[0];
        return $result;
    }

	/**
	 * detail: get agency agent reports
	 *
	 * @param array $request
	 * @param Boolean $viewagentInfoPerm
	 * @param Boolean $is_export
	 *
	 * @return array
	 */
	public function get_agency_agent_reports($request, $viewagentInfoPerm, $is_export = false) {

		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->data_tables->is_export = $is_export;

		$this->load->model(array('transactions', 'game_logs', 'agency_model','agency_agent_report'));
		$i = 0;
		$input = $this->data_tables->extra_search($request);
		$table = 'agency_agents';
		$joins = array();
		$where = array();
		$values = array();
		$group_by = array('agency_agents.agent_id');
		$having = array();

		$joins['player'] = 'player.agent_id = agency_agents.agent_id';

		# DEFINE TABLE COLUMNS ######################################################################################################################################
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'agent_name',
				'select' => 'agency_agents.agent_name',
				'formatter' => function ($d, $row) {
					return '<ul class="list-inline">' .
					'<li><a href="#" onclick="credit_transactions(\'' . $d . '\')" title="' . lang('Credit Transaction') . '"><i class="fa fa-money"></i></a></li>' .
					'<li><a href="#" onclick="agency_player_report(\'' . $d . '\')" title="' . lang('Player List') . '"><i class="fa fa-users"></i></a></li>' .
						'</ul>';
				},
				'name' => lang('Actions'),
			),
			array(
				'dt' => $i++,
				'alias' => 'agent_name',
				'select' => 'agency_agents.agent_name',
				'formatter' => function ($d, $row) {
					return '<a href="#" onclick="agency_agent_report(' . $row['agent_id'] . ')">' . $d . '</a>';
				},
				'name' => lang('Agent Username'),
			),
			array(
				'dt' => $i++,
				'alias' => 'available_credit',
				'select' => 'agency_agents.available_credit',
				'formatter' => function ($d) {return $d;},
				'name' => lang('Available Credit'),
			),
			array(
				'dt' => $i++,
				'alias' => 'agent_id',
				'select' => 'agency_agents.agent_id',
				'formatter' => function ($d) {return $d;},
				'name' => lang('Total Deposit'),
			),
			array(
				'dt' => $i++,
				'alias' => 'agent_id',
				'select' => 'agency_agents.agent_id',
				'formatter' => function ($d) {return $d;},
				'name' => lang('Total Withdrawal'),
			),
			array(
				'dt' => $i++,
				'alias' => 'agent_id',
				'select' => 'agency_agents.agent_id',
				'formatter' => function ($d) {return $d;},
				'name' => lang('Total Bet'),
			),
			array(
				'dt' => $i++,
				'alias' => 'agent_id',
				'select' => 'agency_agents.agent_id',
				'formatter' => function ($d) {return $d;},
				'name' => lang('Total Win'),
			),
			array(
				'dt' => $i++,
				'alias' => 'agent_id',
				'select' => 'agency_agents.agent_id',
				'formatter' => function ($d) {return $d;},
				'name' => lang('Total Loss'),
			),
			array(
				'dt' => $i++,
				'alias' => 'agent_id',
				'select' => 'agency_agents.agent_id',
				'formatter' => function ($d) {return $d;},
				'name' => lang('Net Gaming'),
			),
			array(
				'dt' => $i++,
				'alias' => 'agent_id',
				'select' => 'agency_agents.agent_id',
				'formatter' => function ($d) {return $d;},
				'name' => lang('report.pr21'),
			),
		);

		# FILTER ####################################################################################################################################################

		$dateTimeFrom = isset($input['date_from']) ? $input['date_from'] : NULL;
		$dateTimeTo = isset($input['date_to']) ? $input['date_to'] : NULL;

		if (isset($input['agent_id'])) {
			$where[] = 'agency_agents.parent_id = ?';
			$values[] = $input['agent_id'];
			$agent_username = $this->agency_model->get_agent_by_id($input['agent_id'])['agent_name'];
		}

		# OUTPUT ####################################################################################################################################################

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having);

		$agent_summary[0] = '';
		$agent_summary[1] = lang('Agent Summary');
		$agent_summary[2] = 0;
		$agent_summary[3] = 0;
		$agent_summary[4] = 0;
		$agent_summary[5] = 0;
		$agent_summary[6] = 0;
		$agent_summary[7] = 0;
		$agent_summary[8] = 0;

		$player_summary[0] = '';
		if ($agent_username) {
			$player_summary[0] .= '<ul class="list-inline">';
			$player_summary[0] .= '<li><a href="javascript:void()" onclick="credit_transactions(\'' . $agent_username . '\')"<i class="fa fa-money"></i></a></li>';
			$player_summary[0] .= '<li><a href="javascript:void()" onclick="agency_player_report(\'' . $agent_username . '\')"<i class="fa fa-users"></i></a></li>';
			$player_summary[0] .= '</ul>';
		}
		$player_summary[1] = lang('Player Summary');
		$player_summary[2] = 0;
		$player_summary[3] = 0;
		$player_summary[4] = 0;
		$player_summary[5] = 0;
		$player_summary[6] = 0;
		$player_summary[7] = 0;
		$player_summary[8] = 0;

		$total_summary[0] = '';
		$total_summary[1] = lang('Total Summary');
		$total_summary[2] = 0;
		$total_summary[3] = 0;
		$total_summary[4] = 0;
		$total_summary[5] = 0;
		$total_summary[6] = 0;
		$total_summary[7] = 0;
		$total_summary[8] = 0;

		# OGP-13822
		$use_agency_agent_reports = $this->utils->getConfig('use_agency_agent_reports');

		if (isset($result['data']) && !empty($result['data'])) {

			foreach ($result['data'] as &$row) {

				$agentId = $row[3];

				if (!empty($agentId)) {

					$agentIdArr = $this->agency_model->get_all_downline_arr($agentId);
					$this->utils->debug_log('Agency Agents Report Agent IDS: ', $agentIdArr);

					if($use_agency_agent_reports){
						list($totalBet, $totalWin, $totalLoss, $totalDeposits, $totalWithdrawals) = $this->agency_agent_report->getAgentsSummaryReport($agentIdArr, $dateTimeFrom, $dateTimeTo);
					}else{
						list($totalBet, $totalWin, $totalLoss) = $this->game_logs->sumBetsWinsLossByDatetimeAgents($agentIdArr, $dateTimeFrom, $dateTimeTo);

						list($totalDeposits, $totalWithdrawals) = $this->sumDepositsWithdrawalsByDatetimeAgents($agentIdArr, $dateTimeFrom, $dateTimeTo);
					}

					$row[3] = $totalDeposits;
					$row[4] = $totalWithdrawals;
					$row[5] = $totalBet;
					$row[6] = $totalWin;
					$row[7] = $totalLoss;
					$row[8] = $totalLoss - $totalWin;

					$agent_summary[2] += $row[2];
					$agent_summary[3] += $row[3];
					$agent_summary[4] += $row[4];
					$agent_summary[5] += $row[5];
					$agent_summary[6] += $row[6];
					$agent_summary[7] += $row[7];
					$agent_summary[8] += $row[8];

				}

			}

		}

		if($use_agency_agent_reports){
			list($totalBet, $totalWin, $totalLoss, $totalDeposits, $totalWithdrawals) = $this->agency_agent_report->getAgentsSummaryReport(array($input['agent_id']), $dateTimeFrom, $dateTimeTo);
		}else{
			list($totalBet, $totalWin, $totalLoss) = $this->game_logs->sumBetsWinsLossByDatetimeAgents(array($input['agent_id']), $dateTimeFrom, $dateTimeTo);

			list($totalDeposits, $totalWithdrawals) = $this->sumDepositsWithdrawalsByDatetimeAgents(array($input['agent_id']), $dateTimeFrom, $dateTimeTo);
		}

		$player_summary[3] = $totalDeposits;
		$player_summary[4] = $totalWithdrawals;
		$player_summary[5] = $totalBet;
		$player_summary[6] = $totalWin;
		$player_summary[7] = $totalLoss;
		$player_summary[8] = $totalLoss - $totalWin;

		$total_summary[2] = $agent_summary[2] + $player_summary[2];
		$total_summary[3] = $agent_summary[3] + $player_summary[3];
		$total_summary[4] = $agent_summary[4] + $player_summary[4];
		$total_summary[5] = $agent_summary[5] + $player_summary[5];
		$total_summary[6] = $agent_summary[6] + $player_summary[6];
		$total_summary[7] = $agent_summary[7] + $player_summary[7];
		$total_summary[8] = $agent_summary[8] + $player_summary[8];

		foreach ($result['data'] as &$row) {
			$row[2] = $this->formatCurrency($row[2]);
			$row[3] = $this->formatCurrency($row[3]);
			$row[4] = $this->formatCurrency($row[4]);
			$row[5] = $this->formatCurrency($row[5]);
			$row[6] = $this->formatCurrency($row[6]);
			$row[7] = $this->formatCurrency($row[7]);
			$row[8] = $this->formatCurrency($row[8]);
		}

		$agent_summary[2] = $this->formatCurrency($agent_summary[2]);
		$agent_summary[3] = $this->formatCurrency($agent_summary[3]);
		$agent_summary[4] = $this->formatCurrency($agent_summary[4]);
		$agent_summary[5] = $this->formatCurrency($agent_summary[5]);
		$agent_summary[6] = $this->formatCurrency($agent_summary[6]);
		$agent_summary[7] = $this->formatCurrency($agent_summary[7]);
		$agent_summary[8] = $this->formatCurrency($agent_summary[8]);

		$result['agent_summary'] = $agent_summary;

		$player_summary[2] = $this->formatCurrency($player_summary[2]);
		$player_summary[3] = $this->formatCurrency($player_summary[3]);
		$player_summary[4] = $this->formatCurrency($player_summary[4]);
		$player_summary[5] = $this->formatCurrency($player_summary[5]);
		$player_summary[6] = $this->formatCurrency($player_summary[6]);
		$player_summary[7] = $this->formatCurrency($player_summary[7]);
		$player_summary[8] = $this->formatCurrency($player_summary[8]);

		$result['player_summary'] = $player_summary;

		$total_summary[2] = $this->formatCurrency($total_summary[2]);
		$total_summary[3] = $this->formatCurrency($total_summary[3]);
		$total_summary[4] = $this->formatCurrency($total_summary[4]);
		$total_summary[5] = $this->formatCurrency($total_summary[5]);
		$total_summary[6] = $this->formatCurrency($total_summary[6]);
		$total_summary[7] = $this->formatCurrency($total_summary[7]);
		$total_summary[8] = $this->formatCurrency($total_summary[8]);

		$result['total_summary'] = $total_summary;

		return $result;
	}

	/**
	 * detail: format currency
	 *
	 * @param string $value
	 * @return string
	 */
	private function formatCurrency($value) {
		return $value > 0 ? $this->utils->formatCurrencyNoSym($value) : ($value < 0 ? '<span class="text-danger">' . $this->utils->formatCurrencyNoSym($value) . '</span>' : '<span class="text-muted">0.00</span>');
	}

	/**
	 * detail: get deposit withdrawal of a certain agents
	 *
	 * @param array $agentIdArr
	 * @param string $dateTimeFrom
	 * @param string $dateTimeTo
	 *
	 * @return array
	 */
	private function sumDepositsWithdrawalsByDatetimeAgents($agentIdArr, $dateTimeFrom = null, $dateTimeTo = null) {

		$db=$this->getReadOnlyDB();

		$db->select_sum(
			sprintf('(CASE WHEN transactions.transaction_type = %s THEN transactions.amount ELSE 0 END)', Transactions::DEPOSIT),
			'total_deposits'
		);

		$db->select_sum(
			sprintf('(CASE WHEN transactions.transaction_type = %s THEN transactions.amount ELSE 0 END)', Transactions::WITHDRAWAL),
			'total_withdrawals'
		);

		// $this->db->from('agency_agents');
		// $this->db->join('player', 'player.agent_id = agency_agents.agent_id', 'left');
		// $this->db->join('transactions', "transactions.to_id = player.playerId", 'left');
		$db->from('agency_agents');
		$db->join('player', 'player.agent_id = agency_agents.agent_id', 'left');
		$db->join('transactions', "transactions.to_id = player.playerId", 'left');
		$db->where_in('agency_agents.agent_id', $agentIdArr);
		$db->where('transactions.to_type', Transactions::PLAYER);
		$db->where('transactions.status', Transactions::APPROVED);

		if (isset($dateTimeFrom, $dateTimeTo)) {
			$db->where('transactions.created_at >=', $dateTimeFrom);
			$db->where('transactions.created_at <=', $dateTimeTo);
		}

		$db->limit(1);
		$query = $db->get();
		return array_values($query->row_array());
	}

	/**
	 * detail: get promo applications
	 * @param array $request
	 * @param Boolean $is_export
	 * @return array
	 */
	public function promoApplicationList($request, $is_export = false) {

		$this->load->model(array('promorules', 'player_promo'));
		$this->load->library(array('data_tables'));
		$this->load->library(array('permissions'));
		$this->permissions->setPermissions();

		$promotion_rules = $this->utils->getConfig('promotion_rules');
		//default is true
		// $release_on_admin_approve = $promotion_rules['release_on_admin_approve'];
		$allow_decline_on_approved_and_without_release = $promotion_rules['allow_decline_on_approved_and_without_release'];
		$disable_pre_application_on_release_bonus_first = $promotion_rules['disable_pre_application_on_release_bonus_first'];

		$this->utils->debug_log('allow_decline_on_approved_and_without_release', $allow_decline_on_approved_and_without_release, 'disable_pre_application_on_release_bonus_first', $disable_pre_application_on_release_bonus_first);

		$i = 0;
		//	$request = $this->input->post();
		$input = $this->data_tables->extra_search($request);

        $controller = $this;

		$where = array();
		$values = array();

		$allow_to_delete_declined_promotion = $this->permissions->checkPermissions('allow_to_delete_declined_promotion');
//		$declined_forever_promotion = $this->utils->isEnabledFeature('declined_forever_promotion');
//		$manaully_update_promotion_bonus = $this->permissions->checkPermissions('manaully_update_promotion_bonus');
//		$manually_decline_promo = $this->permissions->checkPermissions('promocancel_list');

		$columns = array(
			array(
				'select' => 'player.playerId',
				'alias' => 'playerId',
			),
			array(
				'select' => 'promocmssetting.promoCmsSettingId',
				'alias' => 'promoCmsSettingId',
			),
			array(
				'select' => 'promorules.promorulesId',
				'alias' => 'promorulesId',
			),
			array(
				'select' => 'promorules.disabled_pre_application',
				'alias' => 'disabled_pre_application',
			),
            array(
                'select' => '(CASE WHEN playerpromo.requestAdminId IS NOT NULL THEN adminUsersRequestId.username ELSE null END)',
                'alias' => 'requestAdmin',
            ),
            array(
                'select' => '(CASE WHEN playerpromo.requestPlayerId IS NOT NULL THEN player.username ELSE null END)',
                'alias' => 'requestPlayer',
            ),
			array(
				'dt' => (!$is_export) ? $i++ : NULL,
				'select' => 'player.playerId',
				'alias' => 'memberLevel',
				'formatter' => function ($d, $row) use ($is_export) {
					if (!$is_export) {
						if ($this->utils->isEnabledFeature('batch_decline_promo') || $this->utils->isEnabledFeature('batch_finish_promo') || $this->utils->isEnabledFeature('batch_release_promo')) {
							$action = '<div class="clearfix">';
							$action .= '	<div class="col-md-3" style="padding:5px 1px 0 2px"><input type="checkbox" name="sales_promo_id" class="chk-promo-id" value="'.$row['playerpromoId'].'"></div>';
							// OGP - 11385 Remove Details button and Action column
							// $action .= '	<div class="col-md-9" style="padding:0 2px 0 2px"><span class="btn btn-xs btn-info review-btn" data-toggle="modal" onclick="getDupLicateAccountList(' . $row["playerId"] . ')" data-target="#detailModal">' . lang("lang.details") . '</span></div>';
							$action .= "</div>";
							return $action;
						} else {
							// OGP - 11385 Remove Details button and Action column
							// return '<span class="btn btn-xs btn-info review-btn" data-toggle="modal" onclick="getDupLicateAccountList(' . $row["playerId"] . ')" data-target="#detailModal">' . lang("lang.details") . '</span>';
						}
					} else {
						return '';
					}
				},
			),
            array(
                'select' => 'playerpromo.playerpromoId',
                'alias' => 'playerpromoId',
                'name' => lang('column.id'),
                'formatter' => 'defaultFormatter'
            ),
			array(
				'dt' => $i++,
				'select' => '( CASE WHEN affiliates.username IS NULL THEN player.username ELSE CONCAT(player.username, \' (\', affiliates.username,  \')\' ) END )',
				'alias' => 'username',
				'name' => lang('player.01'),
				'formatter' => function ($d, $row) use ($is_export) {

					if (!$is_export) {
						return '<a href="/player_management/userInformation/' . $row['playerId'] . '" data-toggle="tooltip" data-original-title="' . lang("cms.checkPlayerDetails") . '" data-placement="right">' . $d . '</a>';
					} else {
						return $d;
					}

				},
			),
			array(
				'dt' => $i++,
				'select' => 'CONCAT(vipsetting.groupName, \'|\', vipsettingcashbackrule.vipLevelName )',
				'formatter' => function ($d, $row) {
					$d = (explode("|",$d));
					if(count($d) > 1){
						$d = lang($d[0]).' - '.lang($d[1]);
					}
					return $d;
				},
				'alias' => 'memberLevel',
				'name' => lang('player.07'),
			),
			array(
				'dt' => $i++,
				'select' => 'promocmssetting.promoName',
				'alias' => 'promoTitle',
				'name' => lang('cms.promotitle'),
				'formatter' => function ($d, $row) use ($is_export) {

					if (!$is_export) {

						return '<span data-toggle="tooltip" title="' . lang('cms.checkCmsPromo') . '" data-placement="right">' . anchor_popup('/cms_management/viewPromoDetails/' . $row['promoCmsSettingId'], $d, array(
							'width' => '1030',
							'height' => '600',
							'scrollbars' => 'yes',
							'status' => 'yes',
							'resizable' => 'no',
							'screenx' => '0',
							'screeny' => '0')) .
							'</span>';

					} else {
						return $d;
					}
				},
			),
			// array(
			// 	'dt' => $i++,
			// 	'select' => 'promorules.promoName',
			// 	'alias' => 'promoName',
			// 	'formatter' => function ($d, $row) {
			// 		return '<a href="#" data-toggle="modal" data-target="#promoDetails" onclick="return viewPromoRuleDetails(' . $row['promorulesId'] . ')">' .
			// 		'<span data-toggle="tooltip" data-original-title="' . lang("cms.showPromoRuleDetails") . '" data-placement="right">' . $row['promoName'] . '</span></a>';
			// 	},
			// ),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.depositAmount',
				'alias' => 'depositAmount',
				'name' => lang('Deposit'),
				// 'formatter' => function ($d, $row) {
				// 	return $d ?: lang('N/A');
				// },
				'formatter' => function ($d) use ($is_export) {
					if (!$is_export) {
						return '<strong>' . $d . '</strong>';
					} else {
						return $d;
					}
				},
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.bonusAmount',
				'alias' => 'bonusAmount',
				'name' => lang('cms.bonusAmount'),
				// 'formatter' => function ($d, $row) {
				// 	// if ($row['transactionStatus'] == Player_promo::TRANS_STATUS_MANUAL_REQUEST_PENDING) {
				// 	// 	return lang('Will generate result once approved');
				// 	// } else {
				// 	return $d = $d ?: lang('N/A');
				// 	// }
				// },
				'formatter' => function ($d) use ($is_export) {
					if (!$is_export) {
						return '<strong>' . $d . '</strong>';
					} else {
						return $d;
					}
				},
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.withdrawConditionAmount',
				'alias' => 'withdrawConditionAmount',
				'name' => lang('Withdraw Condition'),
				// 'formatter' => function ($d, $row) {
				// 	// if ($row['transactionStatus'] == Player_promo::TRANS_STATUS_MANUAL_REQUEST_PENDING) {
				// 	// 	return lang('Will generate result once approved');
				// 	// } else {
				// 	return $d = $d ?: lang('N/A');
				// 	// }
				// },

				'formatter' => function ($d) use ($is_export) {
					if (!$is_export) {
						return '<strong>' . $d . '</strong>';
					} else {
						return $d;
					}
				},
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.dateApply',
				'alias' => 'dateApply',
				'name' => lang('cms.dateApplyRequest'),
				'formatter' => 'dateTimeFormatter',
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.dateProcessed',
				'alias' => 'dateProcessed',
				'name' => lang('cms.dateProcessed'),
				'formatter' => function ($d, $row) {
					return $d ?: lang('N/A');
				},
			),
            array(
                'dt' => $i++,
                'alias' => 'requestBy',
                'name' => lang('cms.requestBy'),
                'formatter' => function ($d, $row) {
                    if(isset($row['requestAdmin'])){
                        $d = $row['requestAdmin'];
                    }else if(isset($row['requestPlayer'])){
                        $d = $row['requestPlayer'];
                    }else{
                        $d = NULL;
                    }

                    return $d;
                },
            ),
			array(
				'dt' => $i++,
				'alias' => 'processed_by_name',
				'select' => 'adminusers.username',
				'name' => lang('pay.procssby'),
				'formatter' => function ($d) use ($is_export) {
					if (!$is_export) {
						return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					} else {
						return $d ?: lang('lang.norecyet');
					}
				},
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.login_ip',
				'alias' => 'loginIp',
				'name' => lang('sys.vu44'),
				'formatter' => function ($d, $row) {
					return $d ? : lang('N/A');
				},
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.transactionStatus',
				'alias' => 'status',
				'name' => lang('Status'),
				'formatter' => function ($d, $row) use ($controller) {

					return $controller->player_promo->statusToName($d);

					// if ($row['transactionStatus'] == Player_promo::TRANS_STATUS_MANUAL_REQUEST_PENDING) {
					// return lang('REQUEST');
					// } else
					// if ($row['transactionStatus'] == Player_promo::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS) {
					// 	return lang('(Approved) Pending Bonus Release');
					// } else {
					// 	return $d == 0 ? lang('cms.verificationStatus.none') : lang('cms.verificationStatus.ok');
					// }
				},
			),
			array(
				'dt' => $i++,
				'select' => 'promorules.bonusReleaseToPlayer',
				'alias' => 'bonusReleaseToPlayer',
				'name' => lang('cms.bonusRelease'),
				'formatter' => function ($d, $row) {
					return $d == Promorules::BONUS_RELEASE_TO_PLAYER_AUTO ? lang('cms.auto') : lang('cms.manual');
				},
			),
			array(
				'select' => 'promorules.disabled_pre_application',
				'alias' => 'disabled_pre_application',
			),
			array(
                'dt' => (!$is_export) ? $i++ : NULL,
				'select' => 'playerpromo.transactionStatus',
				'alias' => 'transactionStatus',
				'name' => lang('lang.action'),
				'formatter' => function ($d, $row) use ($allow_to_delete_declined_promotion, $allow_decline_on_approved_and_without_release, $disable_pre_application_on_release_bonus_first, $is_export){

					if (!$is_export) {
						$ret = '';

						if ($d == Player_promo::TRANS_STATUS_REQUEST) {
							/**
							 * OGP-11240:
							 * a) Remove old perm guards $manaully_update_promotion_bonus, $manually_decline_promo
							 * b) Add new perm guards
							 * 	manaully_update_promotion_bonus -> button 'edit and release'
							 *  promocancel_list -> button release
							 *  promocancel_list -> button decline
							 */
                            // if($manaully_update_promotion_bonus){
                        	if ($this->permissions->checkPermissions('manaully_update_promotion_bonus')) {
                                $ret = '<a href="#" class="btn btn-xs btn-success" onclick="releasePromoWithZero(' . $row['playerpromoId'] . ')" >' . lang('Edit And Release') . '</a>';
                            }
                            //only bonusAmount>0 to allow release now
                            if(@$row['bonusAmount'] > 0 && $this->permissions->checkPermissions('promocancel_list')) {
                                $ret .= '<a href="#" class="btn btn-xs btn-success" onclick="releasePromo(' . $row['playerpromoId'] . ')" >' . lang('Release') . '</a>';
                            }
                            // }
							// if($disable_pre_application_on_release_bonus_first){
							// 	$ret = '<a href="#" class="btn btn-xs btn-success" onclick="approveManualRequestPromo(' . $row['promoCmsSettingId'] . ',' . $row['playerId'] . ')" >' . lang('lang.approve') . '</a>';
							// }
							// }

							// if($manually_decline_promo){
							if ($this->permissions->checkPermissions('promocancel_list')){
								$ret .= '<a href="#" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#promoCancel" onclick="viewPromoDeclineForm(' . $row['playerpromoId'] . ')">' . lang('lang.decline') . '</a>';
							}
							return $ret;
							// } else if ($d == Player_promo::TRANS_STATUS_MANUAL_REQUEST_PENDING) {
							// 	$ret = '<a href="javascript:void()" class="btn btn-xs btn-success" onclick="approveManualRequestPromo(' . $row['promoCmsSettingId'] . ',' . $row['playerId'] . ')" >' . lang('lang.approve') . '</a>';
							// 	$ret .= '<a href="javascript:void()" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#promoCancel" onclick="viewPromoDeclineForm(' . $row['playerpromoId'] . ')">' . lang('lang.decline') . '</a>';
							// 	return $ret;
						//} elseif ($d == Player_promo::TRANS_STATUS_DECLINED && $allow_to_delete_declined_promotion && $declined_forever_promotion) {
						//	$ret = '<a href="#" class="btn btn-xs btn-success" data-toggle="modal" data-target="#promoDeclineForever" onclick="viewPromoDeclineForeverForm(' . $row['playerpromoId'] . ')">' . lang('Declined Forever') . '</a>';
						//	return $ret;
						//} elseif ($d == Player_promo::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS) {
							//active
						//	$ret = '<a href="#" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#promoExpireModal" onclick="viewPromoExpireForm(' . $row['playerpromoId'] . ')">' . lang('Set Expire') . '</a>';
						//	if($manually_decline_promo){
						//		$ret .= '<a href="#" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#promoCancel" onclick="viewPromoDeclineForm(' . $row['playerpromoId'] . ')">' . lang('lang.decline') . '</a>';
						//	}
						//	return $ret;
						//} elseif ($d == Player_promo::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS) {
							// if($allow_decline_on_approved_and_without_release){
						//	$ret = '<a href="#" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#promoExpireModal" onclick="viewPromoExpireForm(' . $row['playerpromoId'] . ')">' . lang('Set Expire') . '</a>';
						//	if($manually_decline_promo){
						//		$ret .= '<a href="#" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#promoCancel" onclick="viewPromoDeclineForm(' . $row['playerpromoId'] . ')">' . lang('lang.decline') . '</a>';
						//	}
							// }
						//	if($manaully_update_promotion_bonus){
						//		$ret .= '<a href="#" class="btn btn-xs btn-success" onclick="releasePromo(' . $row['playerpromoId'] . ')" >' . lang('Release') . '</a>';
						//	}
						//	return $ret;
							// } elseif ($d == Player_promo::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION) {
							// 	$ret = '<a href="#" class="btn btn-xs btn-warning" onclick="setFinished(' . $row['playerpromoId'] . ')">' . lang('Set Finished') . '</a>';
							// 	return $ret;
						} elseif ($d == Player_promo::TRANS_STATUS_APPROVED) {
							$ret = '<a href="#" class="btn btn-xs btn-warning" onclick="setFinished(' . $row['playerpromoId'] . ')">' . lang('Set to Finished') . '</a>';
							return $ret;
						} else {
							return '<i>' . lang('cms.noAvailableAction') . '</i>';
						}

					} else {
						return '';
						// $ret = '';

						// if ($d == Player_promo::TRANS_STATUS_REQUEST) {

						// 	return lang('pay.req');

						// } elseif ($d == Player_promo::TRANS_STATUS_DECLINED && $allow_to_delete_declined_promotion) {

						// 	return lang('report.p13');

						//} elseif ($d == Player_promo::TRANS_STATUS_MANUAL_REQUEST_APPROVED_WITHOUT_RELEASE_BONUS) {

						//	return lang('Manual request approved without release bonus.');

						//} elseif ($d == Player_promo::TRANS_STATUS_APPROVED_WITHOUT_RELEASE_BONUS) {

						//	return lang('Approved without release bonus.');

						// } elseif ($d == Player_promo::TRANS_STATUS_APPROVED) {

						// 	return lang('transaction.status.1');

						// } else {
						// 	return lang('lang.norecyet');
						// }
					}

				},
			),
			array(
				'dt' => $i++,
				'select' => 'playerpromo.note',
				'alias' => 'note',
				'name' => lang('Note'),
				'formatter' => function($d){
					$test = explode(' | ', $d);
					if(count($test) > 1){
						$result = array_unique($test);
						$d = '';
						foreach($result as $res){
							$d .= $res.' | ';
						}
					}
					return $d;
				}
			),
		);

		$table = 'playerpromo';
		$joins = array(
			'promorules' => 'promorules.promorulesId = playerpromo.promorulesId',
			'player' => 'player.playerId = playerpromo.playerId',
			// 'playerlevel' => 'playerlevel.playerId = player.playerId',
			'vipsettingcashbackrule' => 'vipsettingcashbackrule.vipsettingcashbackruleId = player.levelId',
			'vipsetting' => 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId',
			'promocmssetting' => 'promocmssetting.promoCmsSettingId = playerpromo.promoCmsSettingId',
			'adminusers' => 'adminusers.userId = playerpromo.processedBy',
			'adminusers adminUsersRequestId' => 'adminUsersRequestId.userId = playerpromo.requestAdminId',
			'affiliates' => 'affiliates.affiliateId = player.affiliateId',
		);

        if($input['status'] == Player_promo::TRANS_STATUS_REQUEST){
            //status request's request time and prodessed on are the same
            $input['transactionDateType'] = Player_promo::TRANSACTION_DATE_TYPE_REQUEST_TIME;
        }
        if($input['transactionDateType'] == Player_promo::TRANSACTION_DATE_TYPE_REQUEST_TIME){
            if(isset($input['request_date_from'], $input['request_date_to']) && !empty($input['search_by_date']) ){
                $where[] = "playerpromo.dateApply BETWEEN ? AND ?";
                $values[] = $input['request_date_from'];
                $values[] = $input['request_date_to'];
            }

        }
        if($input['transactionDateType'] == Player_promo::TRANSACTION_DATE_TYPE_PROCESSED_TIME){
            if(isset($input['request_date_from'], $input['request_date_to']) && !empty($input['search_by_date'])){
                $where[] = "playerpromo.dateProcessed BETWEEN ? AND ?";
                $values[] = $input['request_date_from'];
                $values[] = $input['request_date_to'];
            }
        }
        if($input['status'] != $input['player_promo_status']){ //dropdown status
            if($input['player_promo_status'] != 'all'){ //normal status
                $where[] = "playerpromo.transactionStatus = ?";
                $values[] = $input['player_promo_status'];
            }
        }else{ //portlet status
            $where[] = "playerpromo.transactionStatus = ?";
            $values[] = $input['status'];
        }
		if (isset($input['username'])) {
			$where[] = "player.username = ?";
			$values[] = $input['username'];
		}
		if (isset($input['vipsettingcashbackruleId'])) {
			$where[] = "vipsettingcashbackrule.vipsettingcashbackruleId = ?";
			$values[] = $input['vipsettingcashbackruleId'];
		}
		if (isset($input['promoCmsSettingId'])) {
			$where[] = "playerpromo.promoCmsSettingId = ?";
			$values[] = $input['promoCmsSettingId'];
		}
 		//if (!empty($input['processed_by'])) {
		//	$where[] = "processedBy = ?";
		//	$values[] = $input['processed_by'];
		//}

        $where[] = "player.deleted_at IS NULL";

        if($is_export){
            $this->data_tables->options['is_export']=true;
			// $this->data_tables->options['only_sql']=true;
            if(empty($csv_filename)){
                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
            }
            $this->data_tables->options['csv_filename']=$csv_filename;
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		if($is_export){
		    //drop result if export
			return $csv_filename;
		}

		return $result;
	}

	/**
	 * detail: get shopping item claim list
	 * @param array $request
	 * @param Boolean $is_export
	 * @return array
	 */
	public function shoppingItemClaimList($request, $is_export = false) {

		$this->load->model(array('shopper_list', 'shopping_center'));
		$this->load->library(array('permissions'));

		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->permissions->setPermissions();

		$i = 0;
		$input = $this->data_tables->extra_search($request);
		$controller = $this;

		$where = array();
		$values = array();

		$playerTotalPoints = 0;

		$allow_to_declined_shopper_request = $this->permissions->checkPermissions('allow_to_declined_shopper_request');
		$allow_to_approve_shopper_request = $this->permissions->checkPermissions('allow_to_approve_shopper_request');
		$columns = array(
			array(
				'select' => 'player.playerId',
				'alias' => 'playerId',
			),

			array(
				'dt' => $i++,
				'select' => 'shopper_list.status',
				'alias' => 'status',
				'name' => lang('lang.action'),
				'formatter' => function ($d, $row) use ($allow_to_declined_shopper_request, $allow_to_approve_shopper_request, $is_export) {

					if (!$is_export) {
						$ret = '';

						if ($d == Shopper_list::REQUEST) {
							if ($allow_to_approve_shopper_request) {
								$ret = '<a href="#" class="btn btn-xs btn-success" onclick="approveOrDeclinedShopItemClaimRequest(' . $row['playerId'] . ',' . $row['shopping_item_unique_id'] . ',' . Shopper_list::APPROVED . ')" >' . lang('Approve') . '</a>';
							}

							if ($allow_to_declined_shopper_request) {
								// $ret .= ' <a href="#" class="btn btn-xs btn-danger" onclick="approveOrDeclinedShopItemClaimRequest(' . $row['playerId'] . ',' . $row['shopping_item_unique_id'] . ',' . Shopper_list::DECLINED . ')" >' . lang('Decline') . '</a>';

								$ret .= ' <button type="button" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#declineShoppingRequestModal" onclick="showShoppinItemDeclineReasonModal(' . $row['playerId'] . ',' . $row['shopping_item_unique_id'] . ',' . Shopper_list::DECLINED . ')">' . lang('Decline') . '</button> ';
							}
							return $ret;

						} else {
							return '<i>' . lang('cms.noAvailableAction') . '</i>';
						}

					}

				},
			),

			array(
				'dt' => $i++,
				'select' => '( CASE WHEN affiliates.username IS NULL THEN player.username ELSE CONCAT(player.username, \' (\', affiliates.username,  \')\' ) END )',
				'alias' => 'username',
				'name' => lang('player.01'),
				'formatter' => function ($d, $row) use ($is_export) {

					if (!$is_export) {
						return '<a href="/player_management/userInformation/' . $row['playerId'] . '" data-toggle="tooltip" data-original-title="' . lang("cms.checkPlayerDetails") . '" data-placement="right">' . $d . '</a>';
					} else {
						return $d;
					}

				},
			),
			array(
				'dt' => $i++,
				'select' => 'CONCAT(vipsetting.groupName,\' \',vipsettingcashbackrule.vipLevelName)',
				'alias' => 'memberLevel',
				'name' => lang('player.07'),
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'select' => 'shopping_center.title',
				'alias' => 'itemTitle',
				'name' => lang('Item Title'),
				'formatter' => function ($d, $row) use ($is_export) {
					return ucwords($d);
				},
			),

			array(
				'dt' => $i++,
				'alias' => 'playerTotalPoints',
				'name' => lang('Player Available Points'),
				'formatter' => function ($d, $row) use ($controller) {
					$playerPoints = $controller->point_transactions->getPlayerTotalPoints($row['playerId']);
					$playerTotalPoints = array_sum(array_column($playerPoints, 'points'));
					$playerDeductedPoints = $controller->point_transactions->getPlayerTotalDeductedPoints($row['playerId'])['points'];
					$total = (int) $playerTotalPoints - (int) $playerDeductedPoints;
					return "<b>" . $total . "</b>";
				},
			),

			array(
				'dt' => $i++,
				'select' => 'shopping_center.requirements',
				'alias' => 'shoppingItemRequiredPoints',
				'name' => lang('cms.bonusAmount'),
				'formatter' => function ($d) use ($is_export) {
					return json_decode($d, true)['required_points'];
				},
			),

			array(
				'dt' => $i++,
				// 'select' => 'shopping_center.requirements',
				'alias' => 'totalPointsAfterConverted',
				'name' => lang('Total Points After Converted'),
				'formatter' => function ($d, $row) use ($controller) {
					$playerPoints = $controller->point_transactions->getPlayerTotalPoints($row['playerId']);
					$playerTotalPoints = array_sum(array_column($playerPoints, 'points'));
					$playerDeductedPoints = $controller->point_transactions->getPlayerTotalDeductedPoints($row['playerId'])['points'];
					$total = (int) $playerTotalPoints - (int) $playerDeductedPoints;
					return $total - json_decode($row['shoppingItemRequiredPoints'], true)['required_points'];
				},
			),

			array(
				'dt' => $i++,
				'select' => 'shopping_center.how_many_available',
				'alias' => 'how_many_available',
				'formatter' => function ($d, $row) use ($readOnlyDB) {
					$query = $readOnlyDB->query("SELECT count(shopper_list.shopping_item_id) as usedItem FROM shopper_list WHERE shopper_list.status = ? AND shopper_list.shopping_item_id = ?", array(
						Shopper_list::APPROVED,
						$row['shopping_item_id'],
					));
					$row = $query->row_array();
					$availableItemCnt = (int) $d - (int) $row['usedItem'];
					return $availableItemCnt ?: 0;
				},
				'name' => lang('report.pr19'),
			),

			// array(
			// 	'dt' => $i++,
			// 	'select' => 'shopper_list.application_datetime',
			// 	'alias' => 'dateApply',
			// 	'name' => lang('cms.dateApplyRequest'),
			// 	'formatter' => 'dateTimeFormatter',
			// ),
			// array(
			// 	'dt' => $i++,
			// 	'select' => 'shopper_list.processed_datetime',
			// 	'alias' => 'dateProcessed',
			// 	'name' => lang('cms.dateProcessed'),
			// 	'formatter' => function ($d, $row) {
			// 		return $d ?: lang('N/A');
			// 	},
			// ),
			// array(
			// 	'dt' => $i++,
			// 	'alias' => 'processed_by_name',
			// 	'select' => 'adminusers.username',
			// 	'name' => lang('pay.procssby'),
			// 	'formatter' => function ($d) use ($is_export) {
			// 		if (!$is_export) {
			// 			return $d ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
			// 		} else {
			// 			return $d ?: lang('lang.norecyet');
			// 		}
			// 	},
			// ),
			// array(
			// 	'dt' => $i++,
			// 	'select' => 'shopper_list.status',
			// 	'alias' => 'status',
			// 	'name' => lang('Status'),
			// 	'formatter' => function ($d, $row) use ($controller) {
			// 		return $controller->shopper_list->statusToName($d);
			// 	},
			// ),

			array(
				'dt' => $i++,
				'select' => 'shopper_list.notes',
				'alias' => 'note',
				'name' => lang('Note'),
				'formatter' => function ($d, $row) {
					return $d ?: lang('N/A');
				},
			),

			array(
				'dt' => $i++,
				'alias' => 'transactionHistory',
				'name' => lang('Transaction History'),
				'formatter' => function ($d, $row) use ($controller) {
					$ret = ' <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#shoppingTransactionHistoryModal" onclick="showShoppingTransactionHistoryModal(' . $row['playerId'] . ')">' . lang('History') . '</button> ';
					return $ret;
				},
			),

			array(
				'alias' => 'shopping_item_id',
				'select' => 'shopper_list.shopping_item_id',
			),
			array(
				'alias' => 'shopping_item_unique_id',
				'select' => 'shopper_list.id',
			),

		);

		$table = 'shopper_list';
		$joins = array(
			'player' => 'player.playerId = shopper_list.player_id',
			'vipsettingcashbackrule' => 'vipsettingcashbackrule.vipsettingcashbackruleId = player.levelId',
			'vipsetting' => 'vipsetting.vipSettingId = vipsettingcashbackrule.vipSettingId',
			'shopping_center' => 'shopping_center.id = shopper_list.shopping_item_id',
			'adminusers' => 'adminusers.userId = shopper_list.processed_by',
			'affiliates' => 'affiliates.affiliateId = player.affiliateId',
		);

		// $where[] = 'playerpromo.promoStatus = ?';
		// $values[] = Shopper_list::PROMO_STATUS_ACTIVE;

		if (isset($input['status'])) {
			if ($input['status'] == Shopper_list::REQUEST) {
				$where[] = "shopper_list.status in (" . Shopper_list::REQUEST . ")";
			} else {
				$where[] = "shopper_list.status = ?";
				$values[] = $input['status'];
			}
		}

		if ($input['status'] == Shopper_list::REQUEST) {
			if (isset($input['request_date_from'], $input['request_date_to'])) {
				$where[] = "shopper_list.application_datetime BETWEEN ? AND ?";
				$values[] = $input['request_date_from'];
				$values[] = $input['request_date_to'];
			}
		} else {
			if (isset($input['request_date_from'], $input['request_date_to'])) {
				$where[] = "shopper_list.processed_datetime BETWEEN ? AND ?";
				$values[] = $input['request_date_from'];
				$values[] = $input['request_date_to'];
			}
		}

		if (isset($input['username'])) {
			$where[] = "player.username = ?";
			$values[] = $input['username'];
		}
		if (isset($input['vipsettingcashbackruleId'])) {
			$where[] = "vipsettingcashbackrule.vipsettingcashbackruleId = ?";
			$values[] = $input['vipsettingcashbackruleId'];
		}
		if (isset($input['itemId'])) {
			$where[] = "shopping_center.id = ?";
			$values[] = $input['itemId'];
		}
		if (!empty($input['processed_by'])) {
			$where[] = "processed_by = ?";
			$values[] = $input['processed_by'];
		}
		// var_dump($request, $columns, $table, $where, $values, $joins);
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		// var_dump($result);exit();
		return $result;
	}

	/**
	 * detail: get friend referrals
	 * @param array $request
	 * @param Boolean $is_export
	 * @return array
	 */
	public function friendReferral($request, $is_export = false) {

		$this->load->library('data_tables');

		# START DEFINE COLUMNS #################################################################################################################################################
		$i = 0;
		$columns = array(
			array(
				'alias' => 'inviterPlayerId',
				'select' => 'playerfriendreferral.playerId',
			),
			array(
				'alias' => 'inviterPlayerId',
				'select' => 'playerfriendreferral.playerId',
			),
			array(
				'alias' => 'invitedPlayerId',
				'select' => 'playerfriendreferral.invitedPlayerId',
			),
			array(
				'dt' => $i++,
				'alias' => 'invited',
				'select' => 'referred.username',
				'name' => lang('player.fr03'),
				'formatter' => function ($d, $row) use ($is_export) {
					if (!$is_export) {
						return '<a href="/player_management/userInformation/' . $row['invitedPlayerId'] . '" target="_blank">' . $d . '</a>';
					} else {
						return $d;
					}

				},
			),
			array(
				'dt' => $i++,
				'alias' => 'inviterCode',
				'select' => 'referred.invitationCode',
				'name' => lang('player.fr05'),
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'inviter',
				'select' => 'referrer.username',
				'name' => lang('player.fr04'),
				'formatter' => function ($d, $row) use ($is_export) {

					if (!$is_export) {
						return '<a href="/player_management/userInformation/' . $row['inviterPlayerId'] . '" target="_blank">' . $d . '</a>';
					} else {
						return $d;
					}

				},
			),
			array(
				'dt' => $i++,
				'alias' => 'createdOn',
				'select' => 'referred.createdOn',
				'name' => lang('player.fr02'),
				'formatter' => 'defaultFormatter',
			),
			array(
				'dt' => $i++,
				'alias' => 'transactionId',
				'select' => 'playerfriendreferral.transactionId',
				'name' => lang('pay.transact'),
				'formatter' => function ($d, $row) use ($is_export) {
					if (!$is_export) {
						return $d ? '<a href="/payment_management/viewtransactionList?transaction_id=' . $d . '" target="_blank">' . $d . '</a>' : '<i>' . lang('lang.norecyet') . '</i>';
					} else {
						return $d ? $d : lang('lang.norecyet');
					}

				},

			),
			array(
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'playerfriendreferral.status',
				'name' => lang('player.ut09'),
				'formatter' => function ($d, $row) use ($is_export) {
					if (!$is_export) {
						switch ($d) {
						    case 1:
						        return lang('player.tl08');
						        break;
						    case 3:
						        return lang('lang.paid');
						        break;
						    case 4:
						        return lang('lang.decline');
						        break;
						}
					} else {
						return $d ? $d : lang('lang.norecyet');
					}

				},

			),
			array(
				'dt' => $i++,
				'alias' => 'referralId',
				'select' => 'playerfriendreferral.referralId',
				'name' => lang('lang.action'),
				'formatter' => function ($d, $row) use ($is_export) {
					if (!$is_export) {
						return  $row['status'] == 1 ? '<a href="javascript:void(0)" class="btn btn-primary btn-xs decline_referral" data-id="'.$d.'" data-invited="'.$row['invitedPlayerId'].'" data-inviter="'.$row['inviterPlayerId'].'" data-singleton="true" data-toggle="confirmation" data-popout="true" data-btn-ok-label="'.lang('cms.yes').'" data-btn-ok-class="btn-success btn-xs" data-btn-cancel-label="'.lang('cms.no').'" data-btn-cancel-class="btn-danger btn-xs" data-title="'.lang('sys.sure').'" ><i class="fa fa-ban">'.lang('lang.decline').'</i></a>' : '<i>' . lang('lang.norecyet') . '</i>';
					} else {
						return $d ? $d : lang('lang.norecyet');
					}

				},

			),
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'playerfriendreferral';
		$joins = array(
			'player referrer' => 'referrer.playerId = playerfriendreferral.playerId',
			'player referred' => 'referred.playerId = playerfriendreferral.invitedPlayerId',
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();

		$input = $this->data_tables->extra_search($request);

		if (isset($input['enabled_date'])) {
			if (isset($input['date_from'], $input['date_to'])) {
				$where[] = "referred.createdOn BETWEEN ? AND ?";
				$values[] = $input['date_from'];
				$values[] = $input['date_to'];
			}
		}

		if (isset($input['inviter'])) {
			$where[] = "referrer.username = ?";
			$values[] = $input['inviter'];
		}

		if (isset($input['invited'])) {
			$where[] = "referred.username = ?";
			$values[] = $input['invited'];
		}

		# END PROCESS SEARCH FORM #################################################################################################################################################
		$this->config->set_item('debug_data_table_sql', true);
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		// $result['sqls'] = $this->data_tables->last_query;
		return $result;
	} // EOF friendReferral

	/**
	 * detail: get transfer request
	 * @param array $request
	 * @param Boolean $is_export
	 * @return array
	 */
	public function transferRequest($playerId = null, $request, $permissions, $is_export = false) {

		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array("DB" => $readOnlyDB));

		$this->load->model('wallet_model');
		$this->utils->loadAnyGameApiObject();
		$abstractApi=$this->utils->loadAnyGameApiObject();
		$walletMap = $this->utils->getGameSystemMap();

		$i = 0;
		$input = $this->data_tables->extra_search($request);

		$where = array();
		$values = array();

		$columns = array(
			array(
				'select' => 'transfer_request.id',
				'alias' => 'id',
			),
			array(
				'select' => 'transfer_request.flag',
				'alias' => 'flag',
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.id',
				'alias' => 'action',
				'name' => lang('Action'),
				'formatter' => function ($d, $row) use($is_export, $permissions) {
					if(!$is_export){
						$str='';
						$str.=" <input type='button' class='btn btn-success btn-xs m-b-5' onclick='queryStatus(" . $d . ")' value='".lang('Query Status')."'>";
						if($permissions['make_up_transfer_record']){
							$str.=" <input type='button' class='btn btn-danger btn-xs m-b-5' onclick='autoFixTransfer(" . $d . ")' value='".lang('Auto Fix')."'>";
						}
						return $str;
					}
				},
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.secure_id',
				'name' => lang('ID'),
				'alias' => 'secure_id',
			),
			array(
				'select' => 'transfer_request.player_id',
				'alias' => 'player_id',
			),
			array(
				'dt' => $i++,
				'select' => 'player.username',
				'name' => lang('Player Username'),
				'alias' => 'player_username',
				'formatter' => function ($d, $row) use ($is_export) {
					if($is_export){
						return $d;
					}else{
						return "<a href='".site_url('/player_management/userInformation/'.$row['player_id'])."' target='_blank'>".$d."</a>";
					}
				},
			),
			array(
				'select' => 'transfer_request.from_wallet_type_id',
				'alias' => 'from_wallet_type',
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.to_wallet_type_id',
				'name' => lang('Transfer'),
				'alias' => 'to_wallet_type',
				'formatter' => function ($d, $row) use ($walletMap) {

					$to = $d == 0 ? lang('Main Wallet') : @$walletMap[$d];
					$from = $row['from_wallet_type'] == 0 ? lang('Main Wallet') : @$walletMap[$row['from_wallet_type']];

					return $from.' => '.$to;
				},
			),
			array(
				'dt' => $i++,
				'select' => 'adminusers.username',
				'alias' => 'admin_username',
				'name' => lang('Admin Username'),
				'formatter' => function ($d, $row) {
					return $d = $d ?: lang('N/A');
				},
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.amount',
				'alias' => 'amount',
				'name' => lang('Amount'),
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.status',
				'alias' => 'status',
				'name' => lang('Status'),
				'formatter' => function ($d, $row) use ($is_export) {

					if (!$is_export) {

						switch ($d) {
						case Wallet_model::STATUS_TRANSFER_REQUEST:
							return '<strong class="text-info">' . lang('Request') . '</strong>';
						case Wallet_model::STATUS_TRANSFER_SUCCESS:
							return '<strong class="text-success">' . lang('Successful') . '</strong>';
						case Wallet_model::STATUS_TRANSFER_FAILED:
							return '<strong class="text-danger">' . lang('Failed') . '</strong>';
						default:
							return '<i class="text-muted">N/A</i>';
						}

					} else {

						switch ($d) {
						case Wallet_model::STATUS_TRANSFER_REQUEST:
							return lang('Request');
						case Wallet_model::STATUS_TRANSFER_SUCCESS:
							return lang('Successful');
						case Wallet_model::STATUS_TRANSFER_FAILED:
							return lang('Failed');
						default:
							return 'N/A';
						}

					}

				},
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.created_at',
				'alias' => 'created_at',
				'name' => lang('Created At'),
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.updated_at',
				'alias' => 'updated_at',
				'name' => lang('Updated At'),
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.external_transaction_id',
				'alias' => 'external_transaction_id',
				'name' => lang('External ID'),
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.external_system_id',
				'alias' => 'external_system_id',
				'name' => lang('API ID'),
			),
			array(
				'select' => 'transfer_request.response_result_id',
				'alias' => 'response_result_id',
			),
			array(
				'select' => 'response_results.status_text',
				'alias' => 'status_text',
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.reason_id',
				'name' => lang('aff.ai59'),
				'alias' => 'reason_id',
				'formatter' => function ($d, $row) use($abstractApi) {
					return $abstractApi->translateReasonId($d, $row['status_text']);
				},
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.transfer_status',
				'name' => lang('Query Status'),
				'alias' => 'transfer_status',
				'formatter' => function ($d, $row) use($abstractApi) {

					return $abstractApi->translateTransferStatus($d);

				},
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.fix_flag',
				'name' => lang('Fix Flag'),
				'alias' => 'fix_flag',
				'formatter' => function ($d, $row) {

					return $d==Wallet_model::DB_TRUE ? lang('Yes') : lang('No');

				},
			),
			array(
				'dt' => $i++,
				'select' => 'response_results.filepath',
				'alias' => 'filepath',
				'name' => lang('con.pym27'),
				'formatter' => function ($d, $row) use($is_export) {
					if($is_export){
						return '';
					}

					return empty($d) ? '' : '<a href="'.site_url('/system_management/download_response_result/'.$d).'" target="_blank">'.lang('Download').'</a>';
				},
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.response_result_id',
				'alias' => 'response_result_id',
				'name' => lang('Resp ID'),
				'formatter' => function ($d, $row) use($is_export) {
					if($is_export){
						return $d;
					}

					return empty($d) ? '' : '<a href="'.site_url('/system_management/view_resp_result?result_id='.$d).'" target="_blank">'.$d.'</a>';
				},
			),
		);

		$table = 'transfer_request';
		$joins = array(
			'adminusers' => 'adminusers.userId = transfer_request.user_id',
			'player' => 'player.playerId = transfer_request.player_id',
			'response_results' => 'response_results.id = transfer_request.response_result_id',
		);

		if (isset($input['status']) && !empty($input['status'])) {
			$where[] = "transfer_request.status = ?";
			$values[] = $input['status'];
		}

		if (!empty($playerId) && !is_array($playerId)) {
			$where[] = "transfer_request.player_id = ?";
			$values[] = $playerId;
		}

		if (isset($input['player_username']) && !empty($input['player_username'])) {
			$where[] = "player.username LIKE ?";
			$values[] = '%' . $input['player_username'] . '%';
		}

		if (isset($input['admin_username']) && !empty($input['admin_username'])) {
			$where[] = "adminusers.username LIKE ?";
			$values[] = '%' . $input['admin_username'] . '%';
		}

		if (isset($input['secure_id']) && !empty($input['secure_id'])) {
			$where[] = "transfer_request.secure_id = ?";
			$values[] = $input['secure_id'];
		}

		if ( ! empty($input['search_reg_date'])) {
            if (isset($input['date_from'], $input['date_to'])) {
                if (isset($input['timezone']) && $input['timezone']!='8') {
                    $hours= -( intval($input['timezone'])-8 );

                    $where[] = "transfer_request.created_at BETWEEN ? AND ?";

                    $date_from_str = $input['date_from'];
                    $date_to_str = $input['date_to'];

                    $by_date_from = new DateTime($date_from_str);
                    $by_date_to = new DateTime($date_to_str);

                    $this->utils->debug_log('date_from_str', $date_from_str, 'date_to_str', $date_to_str, 'hours', $hours);

                    if($hours!=0){
                        if($hours>0){
                            $hours='+'.$hours;
                        }
                        $by_date_from->modify("".$hours." hours");
                        $by_date_to->modify("".$hours." hours");
                    }


                    $values[] = $this->utils->formatDateTimeForMysql($by_date_from);
                    $values[] = $this->utils->formatDateTimeForMysql($by_date_to);
                } else {
                    $where[] = "transfer_request.created_at >= ? AND transfer_request.created_at <= ?";
                    $values[] = $input['date_from'];
                    $values[] = $input['date_to'];
                }
            }
        }

		if (isset($input['amount_from'])) {
			$where[] = "transfer_request.amount >= ?";
			$values[] = $input['amount_from'];
		}

		if (isset($input['amount_to'])) {
			$where[] = "transfer_request.amount <= ?";
			$values[] = $input['amount_to'];
		}

		if (isset($input['by_game_platform_id'])) {
			$where[] = "transfer_request.external_system_id = ?";
			$values[] = $input['by_game_platform_id'];
		}

		if (isset($input['result_id'])) {
			$where[] = "transfer_request.response_result_id = ?";
			$values[] = $input['result_id'];
		}

		if (isset($input['suspicious_trans'])) {
			if($input['suspicious_trans']==Wallet_model::SUSPICIOUS_ALL){
				//status is success and query status is declined
				//or status is failed but query status is approved
				$where[] = "((transfer_request.status=".Wallet_model::STATUS_TRANSFER_SUCCESS." and transfer_request.transfer_status = '".
					Abstract_game_api::COMMON_TRANSACTION_STATUS_DECLINED."' ) or (transfer_request.status=".Wallet_model::STATUS_TRANSFER_FAILED.
					" and transfer_request.transfer_status ='".Abstract_game_api::COMMON_TRANSACTION_STATUS_APPROVED."'))";

			}elseif($input['suspicious_trans']==Wallet_model::SUSPICIOUS_TRANSFER_IN_ONLY){
				$where[] = "transfer_request.status = ?";
				$where[] = "transfer_request.transfer_status = ?";
				$values[]=Wallet_model::STATUS_TRANSFER_SUCCESS;
				$values[]=Abstract_game_api::COMMON_TRANSACTION_STATUS_DECLINED;

			}elseif($input['suspicious_trans']==Wallet_model::SUSPICIOUS_TRANSFER_OUT_ONLY){
				$where[] = "transfer_request.status = ?";
				$where[] = "transfer_request.transfer_status = ?";
				$values[]=Wallet_model::STATUS_TRANSFER_FAILED;
				$values[]=Abstract_game_api::COMMON_TRANSACTION_STATUS_APPROVED;

			}
		}

		// to see test player transfer request  on their user information page
        if(empty($playerId)){
        	$where[] = "player.deleted_at IS NULL";
        }

        if($is_export){
        	$this->data_tables->options['is_export']=true;
        	if(empty($csv_filename)){
        		$csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
        	}
        	$this->data_tables->options['csv_filename']=$csv_filename;
        }

        $group_by=[];
        $having=[];
        $distinct=true;
        $external_order=[];
        $not_datatable='';
        $countOnlyField='transfer_request.id';
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having,
			$distinct, $external_order, $not_datatable, $countOnlyField);

		if($is_export){
		    //drop result if export
			return $csv_filename;
		}


		return $result;
	}

	/**
	 * add by spencer.kuo
	 */
	public function friend_referrial_monthly_earnings($request, $is_export) {
		$readOnlyDB = $this->getReadOnlyDB();

		$this->load->library('data_tables', array('DB' => $readOnlyDB));
		$this->load->model('player_earning');

		$this->data_tables->is_export = $is_export;

		$input = $this->data_tables->extra_search($request);

		//$min_amount = $this->affiliate_earnings->getMinimumMonthlyPayAmountSetting();
		$min_amount = 0;
		$i = 0;
		$columns = array(
			array(
				'select' => 'friend_referrial_monthly_earnings.player_id',
				'alias' => 'player_id',
			),
			array(
				'dt' => $i++,
				'select' => 'friend_referrial_monthly_earnings.id',
				'alias' => 'id',
				'formatter' => function ($d, $row) use ($min_amount) {
					return $row['paid_flag'] == 0 && $row['total_commission'] > 0 ? '<input type="checkbox" class="batch-selected-cb user-success" id="selected_earnings_id" onClick="selectionValidate();" value="' . $d . '">' : '';
				},
			),
			array(
				'dt' => $i++,
				'select' => 'friend_referrial_monthly_earnings.id',
				'alias' => 'id',
				'formatter' => function ($d, $row) use ($min_amount) {
					return $row['paid_flag'] == 0 && $row['total_commission'] > 0 ? '<a class="btn btn-xs btn-primary pay" href="javascript:void(0)" onclick="payOne(this)" data-earningid="' . $d . '"><i class="fa fa-paper-plane-o"></i> Transfer to wallet</a>' : '';
				},
			),
			array(
				'dt' => $i++,
				'select' => 'friend_referrial_monthly_earnings.year_month',
				'alias' => 'yearmonth',
			),
			array(
				'dt' => isset($input['player_id']) ? NULL : $i++,
				'select' => 'player.username',
				'alias' => 'player_username',
				'formatter' => function ($d, $row) {
					$url = site_url('/player_management/userInformation/' . $row['player_id']);
					return $d ? "<a href=\"{$url}\" target=\"_blank\">{$d}</a>" : ('<i class="text-muted">' . lang('N/A') . '</i>');
				},
			),
			array(
				'dt' => $i++,
				'select' => 'friend_referrial_monthly_earnings.active_players',
				'alias' => 'active_players',
			),
			array(
				'dt' => $i++,
				'select' => 'friend_referrial_monthly_earnings.total_players',
				'alias' => 'total_players',
			),
			array(
				'dt' => $i++,
				'select' => 'friend_referrial_monthly_earnings.total_bets',
				'alias' => 'total_bets',
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'select' => 'friend_referrial_monthly_earnings.total_commission',
				'alias' => 'total_commission',
				'formatter' => 'currencyFormatter',
			),
			array(
				'dt' => $i++,
				'select' => 'friend_referrial_monthly_earnings.paid_flag',
				'alias' => 'paid_flag',
				'formatter' => function ($d, $row) {
					return $d == 0 ? lang('Unpaid') : lang('Paid');
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'manual_adjustment',
				'formatter' => function ($d, $row) {
					$output = '';
					if ($row['paid_flag'] == 0) {
						$output .= '<a onclick="modal(\'/player_management/friend_referrial_commision_manual_adjustment/' . $row['id'] . '/' . $row['total_commission'] . '\',\'' . lang('Manual Adjustment') . '\')" href="javascript:void(0);" data-toggle="tooltip" title="' . lang('Manual Adjustment') . '"><span class="glyphicon glyphicon-edit"></a> ';
					}
					return $output;
				},
			),
			array(
				'dt' => $i++,
				'select' => 'friend_referrial_monthly_earnings.player_id',
				'alias' => 'player_id',
			),
			array(
				'dt' => $i++,
				'select' => 'friend_referrial_monthly_earnings.total_bets',
				'alias' => 'total_bets',
			),
		);
		# END DEFINE COLUMNS #################################################################################################################################################

		$table = 'friend_referrial_monthly_earnings';
		$joins = array(
			'player' => 'player.playerId = friend_referrial_monthly_earnings.player_id',
		);

		# START PROCESS SEARCH FORM #################################################################################################################################################
		$where = array();
		$values = array();

		if (isset($input['player_id'])) {
			$where[] = 'friend_referrial_monthly_earnings.player_id = ?';
			$values[] = $input['player_id'];
		}

		if (isset($input['year_month'])) {
			$where[] = 'friend_referrial_monthly_earnings.year_month = ?';
			$values[] = $input['year_month'];
		}

		if (isset($input['player_username'])) {
			$where[] = 'player.username = ?';
			$values[] = $input['player_username'];
		}

		if (isset($input['paid_flag'])) {
			$where[] = 'friend_referrial_monthly_earnings.paid_flag = ?';
			$values[] = $input['paid_flag'];
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		$total_bets_limit = 100000;
		//print_r($result);
		for ($c = 0; $c < count($result['data']); $c++) {
			$total_bets = $result['data'][$c][11]; //total_bets
			$yearmonth = $result['data'][$c][2]; //yearmonth
			$re = '';
			for ($i = 1; $i <= 3; $i++) {
				$re .= $yearmonth . ',';
				$previousReport = $this->player_earning->getPreviousEarning($result['data'][$c][10], $yearmonth);
				if (!$previousReport) {
					break;
				}

				$activeplayers += (int) $previousReport->active_players;
				$yearmonth = $previousReport->year_month;
			}
			if ($total_bets < $total_bets_limit) {
				$result['data'][$c][0] = '';
				$result['data'][$c][1] = '';
			}
		}
		return $result;
	}

	public function friend_referral_daily_report($request, $is_export) {
		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array('DB' => $readOnlyDB));
		$this->load->model('player_earning');

		$this->data_tables->is_export = $is_export;

		$input = $this->data_tables->extra_search($request);
		$i = 0;
		$columns = array(
			array(
				'select' => 'player_friend_referrial_logs.id',
				'alias' => 'id',
			),
			array(
				'dt' => $i++,
				'select' => 'player_friend_referrial_logs.year_month_date',
				'alias' => 'yearmonthdate',
				'formatter' => function ($d, $row) {
					$year = substr($d, 0, 4);
					$month = substr($d, 4, 2);
					$date = substr($d, 6, 2);
					return $year . '-' . $month . '-' . $date;
				},
			),
			array(
				'dt' => $i++,
				'select' => 'player_friend_referrial_logs.referred_count',
				'alias' => 'referred_count',
			),
			array(
				'dt' => $i++,
				'select' => 'player_friend_referrial_logs.total_bets',
				'alias' => 'total_bets',
				'formatter' => 'currencyFormatter',
			),
		);
		$table = 'player_friend_referrial_logs';
		// $joins = array(
		// 	'player' => 'player.playerId = player_friend_referrial_logs.player_id',
		// );
		$joins = array();

		if (isset($input['player_id'])) {
			$where[] = 'player_friend_referrial_logs.player_id = ?';
			$values[] = $input['player_id'];
		}
		if (isset($input['from'])) {
			$where[] = 'player_friend_referrial_logs.year_month_date >= ?';
			$values[] = $input['from'];
		}
		if (isset($input['to'])) {
			$where[] = 'player_friend_referrial_logs.year_month_date <= ?';
			$values[] = $input['to'];
		}
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		return $result;
	}

	/**
	 * detail: get player grade request
	 * @param array $request
	 * @param Boolean $is_export
	 * @return array
	 */
	public function playerGradeReports($request, $is_export = false)
	{
		$this->load->model('group_level');
		$this->load->library('data_tables');

		$i = 0;
		$input = $this->data_tables->extra_search($request);
		$allGroupLevels = $this->group_level->getGroupLevelList();

		# DEFINE TABLE COLUMNS ########################################################################################################################################################################
		$columns = array(
			array(
				'dt' => $i++,
				'alias' => 'id',
				'select' => 'vip_grade_report.id',
				'name' => lang('No.'),
			),
			array(
				'alias' => 'newvipId',
				'select' => 'vip_grade_report.newvipId',
			),
			array(
				'alias' => 'vipsettingcashbackruleId',
				'select' => 'vip_grade_report.vipsettingcashbackruleId',
			),
			array(
				'alias' => 'period_start_time',
				'select' => 'vip_grade_report.period_start_time',
			),
			array(
				'alias' => 'period_end_time',
				'select' => 'vip_grade_report.period_end_time',
			),
			array(
				'dt' => $i++,
				'alias' => 'username',
				'select' => 'player.username',
				'name' => lang('User Name'),
			),
			array(
				'dt' => $i++,
				'alias' => 'request_type',
				'select' => 'vip_grade_report.request_type',
				'formatter' => function ($d) {
					switch ($d) {
						case Group_level::REQUEST_TYPE_AUTO_GRADE:
							return lang('report.gr.auto_grade');
						case Group_level::REQUEST_TYPE_MANUAL_GRADE:
							return lang('report.gr.manual_grade');
						case Group_level::REQUEST_TYPE_SPECIFIC_GRADE:
							return lang('report.gr.specific_grade');
						default:
							return 'N/A';
					}
				},
				'name' => lang('Request Type'),
			),
			array(
				'dt' => $i++,
				'alias' => 'request_time',
				'select' => 'vip_grade_report.request_time',
				'name' => lang('Request Time'),
			),
			array(
				'dt' => $i++,
				'alias' => 'request_grade',
				'select' => 'vip_grade_report.request_grade',
				'formatter' => function ($d) {
					switch ($d) {
						case Group_level::RECORD_UPGRADE:
							return lang('report.gr.upgrade');
						case Group_level::RECORD_DOWNGRADE:
							return lang('report.gr.downgrade');
						case Group_level::RECORD_SPECIFICGRADE:
							return lang('report.gr.specificgrade');
						default:
							return 'N/A';
					}
				},
				'name' => lang('Behavior'),
			),
			array(
				'dt' => $i++,
				'alias' => 'group_name',
				'select' => 'vipsetting.groupName',
				'formatter' => function ($d) {
					if (empty($d)) {
						return 'N/A';
					} else {
						return lang($d);
					}
				},
				'name' => lang('Group Name'),
			),
			array(
				'dt' => $i++,
				'alias' => 'level_from',
				'select' => 'vip_grade_report.level_from',
				'formatter' => function ($d, $row) use ($allGroupLevels) {
					$oldLevelId = $row['vipsettingcashbackruleId'];
					if (!empty($oldLevelId) && isset($allGroupLevels[$oldLevelId])) {
						return lang($allGroupLevels[$oldLevelId]['vipLevelName']);
					} else {
						return 'N/A';
					}
				},
				'name' => lang('Origin Level'),
			),
			array(
				'dt' => $i++,
				'alias' => 'level_to',
				'select' => 'vip_grade_report.level_to',
				'formatter' => function ($d, $row) use ($allGroupLevels) {
					$newLevelId = $row['newvipId'];
					if(!empty($newLevelId) && !empty($allGroupLevels)){
						if (isset($allGroupLevels[$newLevelId])) {
							return lang($allGroupLevels[$newLevelId]['vipLevelName']);
						}
					}

					return 'N/A';
				},
				'name' => lang('New Level'),
			),
			array(
				'dt' => $i++,
				'alias' => 'period_time',
				'select' => 'vip_grade_report.period_start_time',
				'formatter' => function ($d, $row) {
					if ($row['period_start_time'] && $row['period_end_time']) {
						return $row['period_start_time'] . ' ~ ' . $row['period_end_time'];
					} else {
						return 'N/A';
					}
				},
				'name' => lang('Grace Period'),
			),
			array(
				'dt' => $i++,
				'alias' => 'pgrm_start_time',
				'select' => 'vip_grade_report.pgrm_start_time',
				'name' => lang('Process Start Time'),
			),
			array(
				'dt' => $i++,
				'alias' => 'pgrm_end_time',
				'select' => 'vip_grade_report.pgrm_end_time',
				'name' => lang('Process End Time'),
			),
			array(
				'dt' => $i++,
				'alias' => 'pgrm_time_elapsed',
				'formatter' => function ($d, $row) {
					return (strtotime($row['pgrm_start_time']) - strtotime($row['pgrm_start_time']));
				},
				'name' => lang('Time Elapsed'),
			),
			array(
				'dt' => $i++,
				'alias' => 'updated_by',
				'select' => 'adminusers.username',
				'formatter' => function ($d) {
					return ($d) ? $d : 'N/A';
				},
				'name' => lang('Updated By'),
			),
			array(
				'dt' => $i++,
				'alias' => 'remark',
				'select' => 'vip_grade_report.remark',
				'formatter' => function ($d, $row) {
					if (empty($d) || $row['status']) {
						return 'N/A';
					}
					$desc = json_decode($d, true);

					return "rule: " . $desc['rule'] . "<br/>" . "result: " . $desc['rlt'];
				},
				'name' => lang('Remark'),
			),
			array(
				'dt' => $i++,
				'alias' => 'status',
				'select' => 'vip_grade_report.status',
				'formatter' => function ($d) {
					return ($d) ? lang('success') : lang('Failed');
				},
				'name' => lang('Status')
			),
		);

		# FILTER ######################################################################################################################################################################################
		$table = 'vip_grade_report';
		$where = [];
		$values = [];
		$joins = [
			'player'     => 'player.playerId = vip_grade_report.player_id',
			'adminusers' => 'adminusers.userId = vip_grade_report.updated_by',
			'vipsetting' => 'vipsetting.vipSettingId = vip_grade_report.vipsettingId'
		];

		if (isset($input['date_from'], $input['date_to'])) {
			$where[] = "vip_grade_report.request_time BETWEEN ? AND ?";
			$values[] = $input['date_from'];
			$values[] = $input['date_to'];
		}

		if (isset($input['username'])) {
			$where[]  = "player.username LIKE ?";
			$values[] = '%' . $input['username'] . '%';
		}

		if (isset($input['request_type'])) {
			$where[]  = 'vip_grade_report.request_type = ?';
			$values[] = $input['request_type'];
		}

		if (isset($input['request_grade'])) {
			$where[]  = 'vip_grade_report.request_grade = ?';
			$values[] = $input['request_grade'];
		}

		if (isset($input['level_from'])) {
			$where[]  = 'vip_grade_report.vipsettingcashbackruleId = ?';
			$values[] = $input['level_from'];
		}

		if (isset($input['level_to'])) {
			$where[]  = 'vip_grade_report.newvipId = ?';
			$values[] = $input['level_to'];
		}

		if (isset($input['status'])) {
			$where[]  = 'vip_grade_report.status = ?';
			$values[] = $input['status'];
		}
		if($is_export){
            $this->data_tables->options['is_export']=true;
			// $this->data_tables->options['only_sql']=true;
            if(empty($csv_filename)){
                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
            }
            $this->data_tables->options['csv_filename']=$csv_filename;
		}
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);
		if($is_export){
		    //drop result if export
			return $csv_filename;
		}
		return $result;
	}


	/**
	 * detail: get communicatio preference request
	 * @param array $request
	 * @param Boolean $is_export
	 * @return array
	 */
	public function communicationPreferenceReports($request, $is_export = false)
	{
		$this->load->model(array('group_level','communication_preference_model'));
		$this->load->library('data_tables');

		$i = 0;
		$input = $this->data_tables->extra_search($request);
		$allPlayerLevels = $this->group_level->getAllPlayerLevels();
		$config_comm_pref = $this->utils->getConfig('communication_preferences');
		# DEFINE TABLE COLUMNS ########################################################################################################################################################################
		$columns = array(
			/*array(
				//'dt' => $i++,
				'alias' => 'id',
				'select' => 'cp.id',
			),*/
			array(
				'alias' => 'player_id',
				'select' => 'cp.player_id',
			),
			array(
				'dt' => $i++,
				'alias' => 'requested_at',
				'select' => 'cp.requested_at',
				'name'	=> lang('Date of Request'),
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return (!$d || strtotime($d) < 0) ? lang('lang.norecyet') : date('Y-m-d H:i:s', strtotime($d));
					} else {
						return (!$d || strtotime($d) < 0) ? '<i class="text-muted">' . lang('lang.norecyet') . '</i>' : date('Y-m-d H:i:s', strtotime($d));
					}
				},
			),
			array(
				'dt' => $i++,
				'alias' => 'username',
				'select' => 'p.username',
				'formatter' => ($is_export) ? 'defaultFormatter' : function ($d, $row) {
					return '<a href="/player_management/userInformation/' . $row['player_id'] . '">' . $d . '</a>';
				},
				'name'	=> lang('Username')

			),
			array(
				'dt' => $i++,
				'alias' => 'real_name',
				'select' => 'CONCAT(ifnull(pd.firstName,""), \' \', ifnull(pd.lastName,"") )',
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					} else {
						return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
				'name' => lang("Real Name"),
			),
			array(
				'dt' => $i++,
				'alias' => 'aff_username',
				'select' => 'aff.username',
				'formatter' => function ($d) use ($is_export) {
					if ($is_export) {
						return trim(trim($d), ',') ?: lang('lang.norecyet');
					} else {
						return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
				"name" => lang('Affiliate Username')
			),
			array(
				'dt' => $i++,
				'alias' => 'levelId',
				'select' => 'p.levelId',
				'formatter' => function ($d, $row) use ($allPlayerLevels, $is_export) {

					foreach ($allPlayerLevels as $key => $allPlayerLevel) {
						if($allPlayerLevel['vipsettingcashbackruleId'] == $d && $row['levelId'] == $d)
							return lang($allPlayerLevel['groupName']) . ' - ' . lang($allPlayerLevel['vipLevelName']);
					}

					if (isset($is_export)) {
						return lang('lang.norecyet');
					} else {
						return '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
					}
				},
				'name' => lang("Player Level"),
			),

		);

		foreach ($config_comm_pref as $comm_pref_key => $comm_pref_value) {
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'preferences',
				'select' => 'cp.preferences',
				'formatter' => function ($d) use ($is_export, $comm_pref_key) {

					$d = json_decode($d);

					if(isset($d->$comm_pref_key) && $d->$comm_pref_key == "true")
						return lang('Yes');

					return lang('No');

				},
				'name' => lang($comm_pref_value),
			);
		}

		# FILTER ######################################################################################################################################################################################
		$table = 'player_communication_preference_history cp';
		$where = [];
		$values = [];
		$joins = [
			'player p'     => 'p.playerId = cp.player_id',
			'playerdetails pd' => 'pd.playerId = cp.player_id',
			'affiliates aff' => 'aff.affiliateId = p.affiliateId'
		];

		$group_by = ['cp.player_id','cp.requested_at','cp.preferences'];

		if (isset($input['date_from'], $input['date_to'])) {
			//$where[] = "cp.requested_at IN (SELECT max(requested_at) FROM player_communication_preference_history)";
			$where[] = "cp.requested_at IN (SELECT max(requested_at) FROM player_communication_preference_history WHERE player_id = cp.player_id AND requested_at BETWEEN ? AND ?) ";
			$values[] = $input['date_from'];
			$values[] = $input['date_to'];
		}
		else
		{
			$where[] = "cp.requested_at IN (SELECT max(requested_at) FROM player_communication_preference_history WHERE player_id = cp.player_id) ";
		}

		if (isset($input['player_username'])) {
			$where[]  = "p.username LIKE ?";
			$values[] = '%' . $input['player_username'] . '%';
		}

		if (isset($input['group_level'])) {
			$where[]  = 'p.levelId = ?';
			$values[] = $input['group_level'];
		}

		if (isset($input['aff_username'])) {
			$where[]  = "aff.username LIKE ?";
			$values[] = '%' . $input['aff_username'] . '%';
		}

		// -- Apply strict filtering if enabled
		$preferences = array();
		if(isset($input['strict_filtering']))
		{
			foreach ($config_comm_pref as $key => $value) {
				$preferences[$key] = "false";
			}
		}

		// -- Generate filter based on provided options
		if (isset($input['comm_pref_options'])) {

			if(!is_array($input['comm_pref_options'])){
				$preferences[$input['comm_pref_options']] = "true";
			}
			else{
				foreach ($input['comm_pref_options'] as $key => $value) {
					$preferences[$value] = "true";
				}
			}
		}

		// -- If strict, query with an "WHERE AND", else, use "WHERE OR"
		if(isset($input['strict_filtering'])){
			foreach ($preferences as $key => $value) {
				$where[]  = "cp.preferences LIKE ? ";
				$values[] = '%"'.$key.'":"'.$value.'"%';
			}
		}
		elseif(!empty($preferences)){
			$numItems = count($preferences);
			$i = 0;
			$where_or = "(";
			foreach ($preferences as $key => $value) {
				$where_or .= "cp.preferences LIKE ? ";
				if(++$i !== $numItems)
			    	$where_or .= "AND ";

				$values[] = '%"'.$key.'":"'.$value.'"%';
			}
			$where_or .= ")";

			$where[] = $where_or;
		}


		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by);

		return $result;
	}


	/**
	 * Return Income Access Signup Report
	 *
	 * @param void
	 * @return json
	 */
	public function incomeAccessSignupReports($request, $is_export = false) {
		$i = 0;
		$columns = array();
		$this->load->library('data_tables');

		$input = $this->data_tables->extra_search($request);

		$signup_csv_headers = $this->utils->getConfig('ia_daily_signup_csv_headers');

		foreach ($signup_csv_headers as $key => $header) {
			$data = array(
				'dt' => $i++,
				'alias' => $header,
				'select' => $header,
				'name' => lang($header)
			);

			array_push($columns, $data);
		}

		$input = $this->data_tables->extra_search($request);

		$from = isset($input['date_from']) ? $input['date_from'] : null;
		$to = isset($input['date_to']) ? $input['date_to'] : null;
		$username = isset($input['username']) ? $input['username'] : null;

		$data = $this->player_model->getDailySignupWithBtag($from, $to, $username);

		if ( $data > 0){
			$result = $this->data_tables->_prepareDataForLists($columns, $data);
		} else {
			$result = $this->data_tables->empty_data($request);
		}

		$result['header_data'] = $signup_csv_headers;

		return $result;

	}

	/**
	 * Return Income Access Sales Report
	 *
	 * @param void
	 * @return json
	 */
	public function incomeAccessSalesReports($request, $is_export = false) {
		$i = 0;
		$columns = array();
		$this->load->library('data_tables');

		$input = $this->data_tables->extra_search($request);

		$sales_csv_headers = $this->utils->getConfig('ia_daily_sales_csv_headers');

		foreach ($sales_csv_headers as $key => $header) {
			$data = array(
				'dt' => $i++,
				'alias' => $header,
				'select' => $header,
				'name' => lang($header)
			);

			array_push($columns, $data);
		}

		$input = $this->data_tables->extra_search($request);

		$from = isset($input['date_from']) ? $input['date_from'] : null;
		$to = isset($input['date_to']) ? $input['date_to'] : null;
		$username = isset($input['username']) ? $input['username'] : null;

		$data = $this->player_model->getDailySalesWithBtag($from, $to, $username);

		if ( $data > 0){
			$result = $this->data_tables->_prepareDataForLists($columns, $data);
		} else {
			$result = $this->data_tables->empty_data($request);
		}

		$result['header_data'] = $sales_csv_headers;

		return $result;

	}


	/**
	 * detail: get playertaggedlist
	 * @param array $request
	 * @param Boolean $is_export
	 * @return array
	 */
	public function playertaggedlist($request, $permissions, $is_export = false) {
		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(array('risk_score_model', 'player_kyc', 'kyc_status_model'));
		$this->load->helper(['player_helper']);

		$abstractApi=$this->utils->loadAnyGameApiObject();
		$walletMap = $this->utils->getGameSystemMap();
		$risk_score_model = $this->risk_score_model;
		$player_kyc = $this->player_kyc;
		$kyc_status_model = $this->kyc_status_model;

		$i = 0;
		$input = $this->data_tables->extra_search($request);

		$where = array();
		$values = array();

		$columns = array(
			array(
				'select' => 'player.playerId',
				'alias' => 'playerId',
			),
			array(
				'select' => 'vipsettingcashbackrule.vipLevel',
				'alias' => 'vipLevel',
			),
			array(
				'alias' => 'verified_email',
				'select' => 'player.verified_email',
			),
			array(
				'alias' => 'verified_phone',
				'select' => 'player.verified_phone',
			),
		);

		$columns[] = array(
			'dt' => $i++, # 1
			'alias' => 'username',
			'select' => 'player.username',
			'formatter' => ($is_export) ? 'defaultFormatter' : function ($d, $row) {
				return '<a href="/player_management/userInformation/' . $row['playerId'] . '">' . $d . '</a>';
			},
			'name' => lang('player.01'),
		);

		$columns[] = array(
			'dt' => $i++, # 2
			'alias' => 'real_name',
			'select' => 'CONCAT(ifnull(playerdetails.firstName,""), \' \', ifnull(playerdetails.lastName,"") )',
			'formatter' => function ($d) use ($is_export) {
				if ($is_export) {
					return trim(trim($d), ',') ?: lang('lang.norecyet');
				} else {
					return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
				}
			},
			'name' => lang("sys.vu19"),
		);

		$columns[] = array(
			'dt' => $i++, # 3
			'select' => 'CONCAT(vipsetting.groupName, \'|\', vipsettingcashbackrule.vipLevelName )',
			'formatter' => function ($d, $row) {
				$d = (explode("|",$d));
				if(count($d) > 1){
					$d = lang($d[0]).' - '.lang($d[1]);
				}
				return $d;
			},
			'alias' => 'memberLevel',
			'name' => lang('VIP Level'),
		);

		// if ( $permissions['view_player_contactinfo_cn'] /// Combo permissions, view_player_contactinfo_cn and player_contact_info.
		// ){
		// 	$columns[] = array(
		// 		'dt' => $i++, # 4
		// 		'alias' => 'contactNumber',
		// 		'select' => 'playerdetails.contactNumber',
		// 		'name' => lang("player.63"),
		// 		'formatter' => function ($d, $row) use ($permissions, $is_export) {
		// 			$str = $d;
		// 			if ($permissions['view_player_detail_contactinfo_cn'] ) {
		// 				if ($is_export) {
		// 					$str = $row['verified_phone'] == self::DB_TRUE ? $str . '   ' . lang('Verified') : $str . '  ' . lang('Not verified');
		// 				} else {
		// 					if ($row['verified_phone'] == self::DB_TRUE) {
		// 						$str = '<span class="text-success"><i class="fa fa-phone"></i> ' . $str . '</a></span>';
		// 					} else {
		// 						$str = '<span class="text-danger"><i class="fa fa-phone"></i> ' . $str . '</a></span>';
		// 					}
		// 				}
		//
		// 			} else {
		// 				$str = $this->utils->maskMiddleString($str,0,strlen($str)-4, 4);
		// 				if (!$is_export) {
		// 					if ($row['verified_phone'] == self::DB_TRUE) {
		// 						$str = '<span class="text-success"><i class="fa fa-phone"></i><span title="' . lang('con.aff01') . '">' . $str . '</span>';
		// 					} else {
		// 						$str = '<span class="text-danger"><i class="fa fa-phone"></i><span title="' . lang('con.aff01') . '">' . $str . '</span>';
		// 					}
		// 				}
		//
		// 			}
		// 			if (!$is_export && $permissions['tele_marketing_call']) {
		// 				$str = '<a href="' . site_url('/api/call_player_tele/' . $row['playerId']) . '" target="_blank">' . $str . '</a>';
		// 			}
		// 			return $str;
		// 		},
		// 	);
		// }

		// if ( $permissions['view_player_contactinfo_em'] /// Combo permissions, view_player_contactinfo_em and player_contact_info.
		// 	&& (!$this->utils->isEnabledFeature('hide_taggedlist_email_column'))
		// ) {
		// 	$columns[] = array(
		// 		'dt' => $i++, # 5
		// 		'alias' => 'email',
		// 		'select' => 'player.email',
		// 		'name' => lang("player.06"),
		// 		'formatter' => function ($d, $row) use ($permissions, $is_export) {
		// 			$str = $d;
		// 			if ($permissions['view_player_detail_contactinfo_em']) {
		// 				if ($is_export) {
		// 					$str = $row['verified_email'] == self::DB_TRUE ? $str . '  ' . lang('Verified') : $str . '  ' . lang('Not verified');
		// 				} else {
		// 					if ($row['verified_email'] == self::DB_TRUE) {
		// 						$str = '<span class="text-success"><i class="fa fa-envelope"></i> ' . $str . '</a></span>';
		// 					} else {
		// 						$str = '<span class="text-danger"><i class="fa fa-envelope"></i> ' . $str . '</a></span>';
		// 					}
		// 				}
		// 			}else{
		// 				$str = $this->utils->maskMiddleStringLite($str, 4);
		// 				if (!$is_export) {
		// 					$str = '<span title="' . lang('con.aff01') . '">' . $str . '</span>';
		// 				}
		// 			}
		// 			return $str;
		// 		},
		// 	);
		// } // EOF if ($this->permissions->checkPermissions('view_player_contactinfo_em') && !$this->utils->isEnabledFeature('hide_taggedlist_email_column'))

		// $columns[] = array(
		// 	'dt' => $i++, # 6
		// 	'alias' => 'country',
		// 	'select' => 'playerdetails.residentCountry',
		// 	'name' => lang("player.20"),
		// 	'formatter' => function ($d) use ($is_export) {
		// 		if ($is_export) {
		// 			return trim(trim($d), ',') ?: lang('lang.norecyet');
		// 		} else {
		// 			return trim(trim($d), ',') ?: '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
		// 		}
		// 	},
		// );

		$columns[] = array(
			'dt' => $i++, # 7
			'alias' => 'tagName',
			'select' => 'playertag.tagid',
			'name' => lang("player.41"),
			'formatter' => function ($d, $row) use ($is_export) {
				if (!$is_export) {
					return tag_formatted($d);
				}
				return strip_tags(tag_formatted($d));
			},
		);

		$columns[] = array(
			'dt' => $i++, # 8
			'alias' => 'tagged_at',
			'select' => 'playertag.createdOn',
			'name' => lang("tagged_players.tagged_at"),
			// 'formatter' => function ($d, $row) use ($is_export) {
				// return 'aaa';
				// return player_tagged_list($row['playerId'], $is_export);
			// },
		);

		// if($this->utils->isEnabledFeature('show_risk_score')){
		// 	$columns[] = array(
		// 		'dt' => $i++, # 9
		// 		'alias' => 'risk_level',
		// 		'select' => 'playerdetails.playerId',
		// 		'formatter' => function ($d) use ($is_export,$risk_score_model) {
		// 			if ($is_export) {
		// 				return $risk_score_model->getPlayerCurrentRiskLevel($d) && $risk_score_model->generate_total_risk_score($d) ? $risk_score_model->getPlayerCurrentRiskLevel($d) .' / '. $risk_score_model->generate_total_risk_score($d) : lang('lang.norecyet');
		// 			}else{
		// 				return $risk_score_model->getPlayerCurrentRiskLevel($d) && $risk_score_model->generate_total_risk_score($d) ? $risk_score_model->getPlayerCurrentRiskLevel($d) .' / '. $risk_score_model->generate_total_risk_score($d) : '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
		// 			}
		// 		},
		// 		'name' => lang("tagged_players.risk_level_score"),
		// 	);
		// }

		// if($this->utils->isEnabledFeature('show_kyc_status')){
		// 	$columns[] = array(
		// 		'dt' => $i++,
		// 		'alias' => 'kyc_level',
		// 		'select' => 'playerdetails.playerId',
		// 		'formatter' => function ($d) use ($is_export,$player_kyc,$kyc_status_model) {
		// 			if ($is_export) {
		// 				return $player_kyc->getPlayerCurrentKycLevel($d) && $kyc_status_model->getPlayerCurrentStatus($d) ? $player_kyc->getPlayerCurrentKycLevel($d) .' / '. $kyc_status_model->getPlayerCurrentStatus($d): lang('lang.norecyet');
		// 			}else{
		// 				return $player_kyc->getPlayerCurrentKycLevel($d) && $kyc_status_model->getPlayerCurrentStatus($d) ? $player_kyc->getPlayerCurrentKycLevel($d) .' / '. $kyc_status_model->getPlayerCurrentStatus($d): '<i class="text-muted">' . lang('lang.norecyet') . '</i>';
		// 			}
		// 		},
		// 		'name' => lang("KYC Level/Rate Code"),
		// 	);
		// }

		$columns[] = array(
			'dt' => $i++, # 11
			'alias' => 'lastLoginTime',
			'select' => 'player.lastLoginTime',
			'name' => lang("player.102"),
		);

		$columns[] = array(
			'dt' => $i++, # 12
			'alias' => 'createdOn',
			'select' => 'player.createdOn',
			'name' => lang("player.43"),
			'formatter' => function ($d) use ($is_export) {
				if ($is_export) {
					return (!$d || strtotime($d) < 0) ? lang('lang.norecyet') : date('Y-m-d H:i:s', strtotime($d));
				} else {
					return (!$d || strtotime($d) < 0) ? '<i class="text-muted">' . lang('lang.norecyet') . '</i>' : date('Y-m-d H:i:s', strtotime($d));
				}
			},

		);

		$columns[] = array(
            'dt' => $i++, # 13
            'alias' => 'blocked',
            'select' => 'player.blocked' ,
            'name' => lang("lang.status"),
            'formatter' => function ($d, $row) use ($is_export){
                $formatter = 1;
                return  $this->utils->getPlayerStatus($row['playerId'],$formatter,$d,$is_export);
            },
        );

        $columns[] = array(
            'dt' => $i++, # 14
            'alias' => 'blocked_status_last_update',
            'select' => 'player.blocked_status_last_update' ,
            'name' => lang("tagged_players.account_status_last_update"),
            'formatter' => function ($d, $row) use ($is_export){
                if ($is_export) {
					return (!$d || strtotime($d) < 0) ? lang('lang.norecyet') : date('Y-m-d H:i:s', strtotime($d));
				} else {
					return (!$d || strtotime($d) < 0) ? '<i class="text-muted">' . lang('lang.norecyet') . '</i>' : date('Y-m-d H:i:s', strtotime($d));
				}
            },
        );

		$table = 'player';
		$joins = array(
			'playeraccount' => 'player.playerid = playeraccount.playerid',
			'playerdetails' => 'player.playerid = playerdetails.playerid',
			'playerlevel' => 'player.playerid = playerlevel.playerid',
			'playertag' => 'player.playerid = playertag.playerid',
			'vipsettingcashbackrule' => 'playerlevel.playergroupid = vipsettingcashbackrule.vipsettingcashbackruleid',
			'vipsetting' => 'vipsettingcashbackrule.vipsettingid = vipsetting.vipsettingid',
			'tag' => 'playertag.tagid = tag.tagid',
		);

		$where[] = "playeraccount.type = 'wallet' AND playertag.tagId != '0'";

		$this->utils->debug_log(__METHOD__, [ 'selected_tags' => isset($input['selected_tags']) ? $input['selected_tags'] : '(not set)' ]);

		if (isset($input['selected_tags']) && !empty($input['selected_tags'])) {
			$selected_tags =  is_array($input['selected_tags']) ? $input['selected_tags'] : [ $input['selected_tags'] ];
			$bind_marks = implode(',', array_fill(0, count($selected_tags), '?'));
			$where[] = "playertag.tagId IN ( $bind_marks )";
			foreach ($selected_tags as $tag) {
				$values[] = $tag;
			}
		}

		if (isset($input['username']) && !empty($input['username'])) {
			$where[] = "player.username like ?";
			$values[] = "%{$input['username']}%";
		}

		if ( ! empty($input['search_reg_date'])) {
            if (isset($input['date_from'], $input['date_to'])) {
                $where[] = "playertag.createdon BETWEEN ? AND ?";
                $values[] = $input['date_from'];
                $values[] = $input['date_to'];
            }
        }

        if (!empty($input['search_last_update_date'])) {
        	if (isset($input['last_update_from'], $input['last_update_to'])) {
        		$where[] = "player.blocked_status_last_update BETWEEN ? AND ?";
        		$values[] = $input['last_update_from'];
        		$values[] = $input['last_update_to'];
        	}
        }

        $group_by=[];
        $having=[];
        $distinct=true;
        $external_order=[];
        $not_datatable='';
		$countOnlyField='player.playerId';

		// $this->config->set_item('debug_data_table_sql', true);
		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having,	$distinct, $external_order, $not_datatable, $countOnlyField);
		// $result['sql'] = $this->data_tables->last_query;

		return $result;
	}

	public function player_realtime_balance($request, $is_export = false, $csv_filename = null) {
        $readOnlyDB = $this->getReadOnlyDB();
        $this->load->library('data_tables', array('DB' => $readOnlyDB));
        $this->load->model(array('player_model'));
        $this->load->helper(['player_helper']);
        $this->data_tables->is_export = $is_export;
        $input = $this->data_tables->extra_search($request);

        $i          = 0;
        $input      = $this->data_tables->extra_search($request);
        $joins      = array();
        $where      = array();
        $values     = array();
        $group_by   = array();
        $having     = array();

        # DEFINE TABLE COLUMNS ########################################################################################################################################################################
        $columns = array();

        $columns[] = array(
            'alias' => 'playerId',
            'select' => 'playerId',
        );
        $columns[] = array(
            'dt' => $i++,
            'alias' => 'username',
            'select' => 'username',
            'name' => lang('Player Username'),
            'formatter' => function($d, $row) use ($is_export) {
            	if (!$is_export) {
            		return '<a href=/player_management/userInformation/'.$row['playerId'].'>'.$d.'</a>';
            	} else {
            		return $d;
            	}
            }
        );
        $columns[] = array(
            'dt' => $i++,
            'alias' => 'registration_date',
            'select' => 'player.createdOn',
    		'formatter' => 'dateTimeFormatter',
            'name' => lang('player.38'),
        );
        $columns[] = array(
            'dt' => $i++,
            'alias' => 'tag',
            'select' => 'playerId',
            'name' => lang('Player Tag'),
            'formatter' => function($d, $row) use ($is_export) {
            	return player_tagged_list($d, $is_export);
            }
        );
        $columns[] = array(
            'dt' => $i++,
            'alias' => 'total_balance',
            'select' => "total_total",
            'name' => lang('report.balance.player.balance'),
            'formatter' => function($d, $row) use ($is_export) {
            	return number_format($d, 2, '.', ',');
            }
        );

        # FILTER ######################################################################################################################################################################################

        $table = 'player';

        if (!empty($input["username"])) {
        	$where[] = 'username = "'.$input["username"].'"';
        }

        if (!empty($input["total_balance"])) {
        	$where[] = "total_total <= ".$input["total_balance"];
        }

		if (isset($input['tag_list'])) {
			$tag_list = $input['tag_list'];

			if(is_array($tag_list)) {
				$notag = array_search('notag', $tag_list);
				if($notag !== false) {
					$where[] = 'playerId IN (SELECT DISTINCT playerId FROM playertag)';
					unset($tag_list[$notag]);
				}
			} elseif ($tag_list == 'notag') {
				$where[] = 'playerId IN (SELECT DISTINCT playerId FROM playertag)';
				$tag_list = null;
			}

			if (!empty($tag_list)) {
				$tagList = is_array($tag_list) ? implode(',', $tag_list) : $tag_list;
				$where[] = 'playerId NOT IN (SELECT DISTINCT playerId FROM playertag WHERE playertag.tagId IN ('.$tagList.'))';
			}
		}

		if (!empty($input['search_reg_date'])) {
            if (isset($input['registration_date_from'], $input['registration_date_to'])) {
                $where[] = "createdOn BETWEEN ? AND ?";
                $values[] = $input['registration_date_from'];
                $values[] = $input['registration_date_to'];
            }
        }

        if($is_export){
            $this->data_tables->options['is_export'] = true;
            if(empty($csv_filename)){
                $csv_filename = $this->utils->create_csv_filename(__FUNCTION__);
            }
            $this->data_tables->options['csv_filename'] = $csv_filename;
		}


        # OUTPUT ######################################################################################################################################################################################
        $result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having, false);

        if($is_export){
			return $csv_filename;
		}


        $summary = $this->data_tables->summary($request, $table, $joins, 'SUM(total_total) sum_balance', null, $columns, $where, $values);
		$result['sum_balance'] = $this->utils->formatCurrencyNoSym($summary[0]['sum_balance']);
        return $result;
	}

	public function player_daily_balance($request, $is_export = false, $csv_filename = null) {
        $readOnlyDB = $this->getReadOnlyDB();
        $this->load->library('data_tables', array('DB' => $readOnlyDB));
        $this->load->model(array('player_model'));
        $this->data_tables->is_export = $is_export;
        $input = $this->data_tables->extra_search($request);

        $i          = 0;
        $input      = $this->data_tables->extra_search($request);
        $joins      = array();
        $where      = array();
        $values     = array();
        $group_by   = array();
        $having     = array();

        $playerWalletBalances = array();
        $dateFilter = $this->utils->getYesterdayForMysql();
        if (!empty($input["date_filter"])) {
        	$dateFilter = $input["date_filter"];
        }

        $playerWalletBalances = $this->player_model->getDailyPlayerWalletBalanceByDate($dateFilter);

        # DEFINE TABLE COLUMNS ########################################################################################################################################################################
        $columns = array();

        $columns[] = array(
            'alias' => 'playerId',
            'select' => 'player.playerId',
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'game_date',
            'select' => 'daily_balance.game_date',
    		'formatter' => 'dateFormatter',
            'name' => lang('Date'),
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'username',
            'select' => 'player.username',
            'formatter' => 'defaultFormatter',
            'name' => lang('Player Username'),
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'total_balance',
            'select' => 'balance',
            'formatter' => 'currencyFormatter',
            'name' => lang('Total Balance')
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'main_wallet',
            'select' => 'player.playerId',
            'name' => lang('Main Wallet Balance'),
            'formatter' => function($d, $row) use (&$playerWalletBalances) {
            	# main wallet id is zero
            	if (isset($playerWalletBalances[$row['playerId']][0]) && !empty($playerWalletBalances[$row['playerId']][0])) {
            		return $this->utils->formatCurrencyNoSym($playerWalletBalances[$row['playerId']][0]);
            	} else {
            		return "0.00";
            	}
            }
        );

        // $gamePlatforms = $this->utils->getActiveGameSystemList();
        $gamePlatforms = $this->utils->getAllGameSystemList();

        if (!empty($gamePlatforms)) {
        	foreach ($gamePlatforms as $key => $gamePlatform) {
        		$gamePlatformId = $gamePlatform['id'];
        		$columns[] = array(
		            'dt' => $i++,
		            'alias' => 'sub_wallet_' . $gamePlatformId,
		            'select' => 'player.playerId',
		            'name' => $gamePlatform['system_code'],
		            'formatter' => function($d, $row) use (&$playerWalletBalances, $gamePlatformId) {
		            	if (isset($playerWalletBalances[$row['playerId']][$gamePlatformId]) && !empty($playerWalletBalances[$row['playerId']][$gamePlatformId])) {
		            		return $this->utils->formatCurrencyNoSym($playerWalletBalances[$row['playerId']][$gamePlatformId]);
		            	} else {
		            		return "0.00";
		            	}
		            }
		        );
        	}
        }

        # FILTER ######################################################################################################################################################################################

        $table = 'player';
        $joins['daily_balance USE INDEX(idx_game_date)'] = 'player.playerId = daily_balance.player_id';
        $where[] = 'player.deleted_at IS NULL';
        $where[] = 'daily_balance.type = 1';
        $where[] = 'daily_balance.game_date = "'.$dateFilter.'"';

        if (!empty($input["username"])) {
        	$where[] = 'player.username = "'.$input["username"].'"';
        }

        if (!empty($input["total_balance"])) {
        	$where[] = 'daily_balance.balance >= '.$input["total_balance"];
        }

        if (!empty($input['tag_list'])) {
        	$tagList = is_array($input['tag_list']) ? implode(',', $input["tag_list"]) : $input["tag_list"];
        	$where[] = 'player.playerId NOT IN (SELECT DISTINCT playerId FROM playertag WHERE playertag.tagId IN ('.$tagList.'))';
        }

        if($is_export){
            $this->data_tables->options['is_export'] = true;
            if(empty($csv_filename)){
                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
            }
            $this->data_tables->options['csv_filename'] = $csv_filename;
		}


        # OUTPUT ######################################################################################################################################################################################
        $result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having);

        if($is_export){
			return $csv_filename;
		}

        return $result;
    }

    public function playerAttachmentFileList($request, $is_export = false){
		$readOnlyDB = $this->getReadOnlyDB();
		$this->load->library('data_tables', array("DB" => $readOnlyDB));
		$this->load->model(['risk_score_model','kyc_status_model']);
		$this->load->helper(['player_helper']);

		$input = $this->data_tables->extra_search($request);
		$kyc_status_model = $this->kyc_status_model;
		$risk_score_model = $this->risk_score_model;
		$player_kyc       = $this->player_kyc;

		$i=0;
		$where = array();
		$values = array();
		$joins = array();
		$joins['playerdetails'] = 'player.playerId = playerdetails.playerId';
		$joins['player_attached_proof_file'] = 'player.playerId = player_attached_proof_file.player_id';
		$joins['playertag'] = 'playertag.playerId = player.playerId';
		$joins['attached_file_status afs'] = 'afs.player_id = player.playerId';

		$group_by = array('player.playerId');

		$columns[] = array(
			'select' => 'playerdetails.proof_filename',
			'alias' => 'proof_filename',
		);

		$columns[] = array(
			'dt' => $i++,
			'alias' => 'playerId',
			'select' => 'player.playerId',
			'formatter' => 'defaultFormatter',
			'name' => lang('ID'),
		);

		if(!$is_export){

			$columns[] = array(
				'dt' => $i++,
				'alias' => 'action',
				'select' => '1',
				'formatter' => function ($d, $row){

	                $output = '<a onclick="modal(\'/player_management/player_attach_document/' . $row['playerId'] . '/attached_file_list\',\'' . lang('Attached document') . '\')" href="javascript:void(0);" data-toggle="tooltip" title="View attached file"><i class="fa fa-search"></i></a>';

					return $output;
				},
				'name' => lang('lang.action'),
			);
		}

		$columns[] = array(
			'dt' => $i++,
			'alias' => 'username',
			'select' => 'player.username',
			'formatter' => function ($d, $row) use ($is_export) {

				if($is_export) return $d;

				return '<a href="/player_management/userInformation/' . $row['playerId'] . '">' . $d . '</a>';
			},
			'name' => lang('player.01'),
		);


		if(!isset($input['show_logs'])){

			$columns[] = array(
				'dt' => $i++,
				'alias' => 'last_update_date',
				'select' => 'MAX(afs.updated_at)',
				'formatter' => function ($d, $row) {
					return !empty($d) ? $d : lang('lang.norecyet');
				},
				'name' => lang('last_update_date'),
			);

			$columns[] = array(
				'select' => 'MAX(CONCAT_WS("|",afs.id, afs.status))',
				'alias' => 'latest_status',
			);
		}
		else{
			$columns[] = array(
				'dt' => $i++,
				'alias' => 'last_update_date',
				'select' => 'afs.updated_at',
				'formatter' => function ($d, $row) {
					return !empty($d) ? $d : lang('lang.norecyet');
				},
				'name' => lang('last_update_date'),
			);

			$columns[] = array(
				'select' => 'CONCAT_WS("|",afs.id, afs.status)',
				'alias' => 'latest_status',
			);

			$group_by = array('afs.id');
		}

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'id_card_number',
            'select' => 'playerdetails.id_card_number',
            'name' => lang('ID Card Number'),
        );

		$proof_attachment_types = $this->utils->getconfig('proof_attachment_type');

		if(!empty($proof_attachment_types) && is_array($proof_attachment_types)){
			foreach ($proof_attachment_types as $attachment_type_key => $attachment_type) {
				$columns[] = array(
					'dt' => $i++,
					'alias' => $attachment_type_key,
					'select' => 'afs.id',
					'formatter' => function ($d, $row) use($attachment_type, $is_export){

						$status_array = explode('|', $row['latest_status']);

						// -- get all available verification status from config
						$verification_list = $this->utils->getConfig('verification');

						$remarks = [
							self::Remark_No_Attach      => 'text-secondary',
							self::Remark_Wrong_attach   => 'text-danger',
							self::Remark_Verified 		=> 'text-success',
							self::Remark_Not_Verified   => 'text-warning',
						];

						$default = "<span class='".$remarks[self::Remark_No_Attach]."'>" . (isset($verification_list[self::Remark_No_Attach]['description']) ? $verification_list[self::Remark_No_Attach]['description'] : 'No Attachment'). "</span>";

						if(empty($status_array) || !isset($status_array[1])) return $default;

						$status = json_decode($status_array[1]);

						foreach ($verification_list as $key => $value) {

							if($status->{$attachment_type['tag']} == $key) {

								if($is_export)
									$default = $value['description'];
								else
									$default = "<span class='".$remarks[$key]."'>" . $value['description']. "</span>";

								break;
							}
						}

						return $default;
					},
					'name' => lang($attachment_type['description']),
				);
			}
		}


		$columns[] = array(
			'dt' => $i++,
			'alias' => 'risk_level',
			'select' => 'playerdetails.risk_score_level',
			'formatter' => function ($d, $row) use($risk_score_model){

				if(empty($d)) return lang('N/A');

				$risk_score_info = json_decode($d, true);

				if(!isset($risk_score_info['risk_score'], $risk_score_info['risk_level'])) return lang('N/A');

				return $risk_score_info['risk_score'] . ' / ' . $risk_score_info['risk_level'];
			},
			'name' => lang('Risk Level'),
		);

		$columns[] = array(
			'dt' => $i++,
			'alias' => 'kyc_level',
			'select' => 'playerdetails.kyc_status_id',
			'formatter' => function ($d, $row) use($kyc_status_model){

				if(empty($d)) return lang('N/A');

				$kyc_info = $kyc_status_model->getKycStatusInfo($d);

				if(empty($kyc_info) || !is_array($kyc_info)) return lang('N/A');

				return $kyc_info['kyc_lvl'].' / '.$kyc_info['rate_code'];
			},
			'name' => lang('KYC Level'),
		);

		$columns[] = array(
			'dt' => $i++,
			'alias' => 'tag',
			'select' => 'playertag.tagId',
			'formatter' => function ($d, $row) use($is_export){
				return player_tagged_list($row['playerId'], $is_export);
			},
			'name' => lang('Tag'),
		);

		$table = 'player';

		###### QUERY FILTER #####
		if(!empty($input['username'])){
			$where[] = 'player.username = ?';
			$values[] = $input['username'];
		}

		if (!empty($input['tag'])) {
			$where[] = "playertag.tagId = ?";
        	$values[] = $input['tag'];
		}

		if (!empty($input['kyc_level_id'])) {
			$where[] = "playerdetails.kyc_status_id = ?";
        	$values[] = $input['kyc_level_id'];
		}

        if (!empty($input['id_card_number'])) {
            $where[] = "playerdetails.id_card_number LIKE ?";
            $values[] = '%' . $input['id_card_number'] . '%';
        }

		if (isset($input['risk_score_level'])) {
			$where[] = "playerdetails.risk_score_level LIKE ?";
			$values[] = '%"' . $input['risk_score_level'] . '"%';
		}


        $where[] = "playerdetails.proof_filename IS NOT NULL";


		if(isset($input['can_attachment'])){
            $where_verification = "'".self::Verification_Income."',
                                    '".self::Verification_Adress."',
                                    '".self::Verification_Photo_ID."',
                                    '".self::Verification_Deposit_Withrawal."'";

            $where_verification_foreach = '';

            foreach ($input['can_attachment'] as $input_attachment_key => $input_attachment) {
                if(isset($input[$input_attachment])){
                    $where[] = "afs.status LIKE ?";
                    $values[] = '%"' . $input_attachment . '":"'.$input[$input_attachment].'"%';
                    $where_verification_foreach .= "'".array_shift($input['can_attachment'])."',";
                }
            }

            if(!empty($where_verification_foreach)){
                $where_verification_foreach = rtrim($where_verification_foreach, ',');
                $where_verification = $where_verification_foreach;
            }

            $where[] = "player_attached_proof_file.tag IN(" . $where_verification .")";

        }else{
            $where[] = "player_attached_proof_file.tag = ?";
            $values[] = '';
        }

		if (!empty($input['can_search_last_update_date']) && isset($input['last_update_date_from']) && isset($input['last_update_date_to'])) {
			$where[] = 'afs.updated_at >= ? AND afs.updated_at <= ?';
			$values[] = $input['last_update_date_from'];
			$values[] = $input['last_update_date_to'];
		}

		##### END OF QUERY FILTER #########

		return $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by);
		// return $data['attachment_info'] = $this->player_attached_proof_file_model->getPlayerAttachmentInfoList(56967);
	}

	function show_active_players($request, $is_export = false, $csv_filename = null)
    {
        $readOnlyDB = $this->getReadOnlyDB();
        $this->load->library('data_tables', array('DB' => $readOnlyDB));
        $this->load->model(array('player_model'));
        $this->data_tables->is_export = $is_export;
        $input = $this->data_tables->extra_search($request);

        $i          = 0;
        $input      = $this->data_tables->extra_search($request);
        $joins      = [];
        $where      = [];
        $values     = [];
        $group_by   = [];
        $having     = [];
        $columns        = [];

        # DEFINE TABLE COLUMNS ########################################################################################################################################################################
         $columns[] = array(
            'alias' => 'player_id',
            'select' => 'total_player_game_day.player_id',
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'game_date',
            'select' => 'date',
            'name' => lang('Date'),
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'username',
            'select' => 'player.username',
            'formatter' => 'defaultFormatter',
            'name' => lang('Player Username'),
            'formatter' => function ($d, $row) {
				return '<a href="' . site_url('player_management/userInformation/' . $row['player_id']) . '">' . $d . '</a>';
            }
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'affiliate_username',
            'select' => 'affiliates.username',
            'name' => lang('Affiliate Username'),
            'formatter' => function ($d, $row) {
                if ($row['affiliate_username'] != '') {
                        return sprintf('%s', $row['affiliate_username'], $d);
                } else {
                        return lang('lang.norecyet');
                }
            }
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'agent_username',
            'select' => 'agency_agents.agent_name',
            'name' => lang('Agent Username'),
            'formatter' => function ($d, $row) {
                if (!empty($d)) {
                	return $d;
                } else {
                	return lang('lang.norecyet');
                }
            }
        );

        $columns[] = array(
            'dt' => $i++,
            'alias' => 'referrer',
            'select' => 'referrer.username',
            'name' => lang('Referrer'),
            'formatter' => function ($d, $row) {
                if (!empty($d)) {
                    return $d;
                } else {
                    return lang('lang.norecyet');
                }
            }
        );

        # FILTER ######################################################################################################################################################################################
        $table = 'total_player_game_day';
        $joins = [
            'player' => 'player.playerId = total_player_game_day.player_id',
            'affiliates' => 'affiliates.affiliateId = player.affiliateId',
            'agency_agents' => 'player.agent_id = agency_agents.agent_id',
            'player referrer' => 'player.refereePlayerId = referrer.playerId'
        ];

        $where[] = 'player.deleted_at IS NULL';
        $where[] = 'player.username IS NOT NULL';

        if (isset($input['date_start'], $input['date_end'])) {
            $where[] = 'total_player_game_day.date >= "'.$input['date_start'].'" AND total_player_game_day.date  <= "'.$input['date_end'].'"';
        }

        if(!empty($input['username'])){
			$where[] = 'player.username = ?';
			$values[] = $input['username'];
		}

        $group_by[] = 'total_player_game_day.player_id';

        if($is_export){
            $this->data_tables->options['is_export']=true;
            if(empty($csv_filename)){
                $csv_filename=$this->utils->create_csv_filename(__FUNCTION__);
            }
            $this->data_tables->options['csv_filename'] = $csv_filename;
        }


        # OUTPUT ######################################################################################################################################################################################
        $result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins, $group_by, $having);
        $summary = $this->data_tables->summary($request, $table, $joins, 'affiliates.username affiliate_total, agency_agents.agent_name agency_total, referrer.username referrer_total', $group_by, $columns, $where, $values);

        $newSummary = [];

        if (!empty($summary)) {
            $newSummary['affiliate_total'] 		= ['total_count' => 0, 'description' => lang('affiliate_total'), ];
            $newSummary['agency_total'] 		= ['total_count' => 0, 'description' => lang('agency_total'), ];
            $newSummary['referrer_total'] 		= ['total_count' => 0, 'description' => lang('referrer_total'), ];
            $newSummary['direct_players_total'] = ['total_count' => 0, 'description' => lang('direct_players_total'), ];

            foreach ($summary as $summaryKey => $items) {
            	$lastKey = key(array_slice( $items, -1, 1, TRUE ) );
            	$isDirectPlayer = true;

	            foreach ($items as $itemsKey => $value) {
	            	# add to direct player if no aff, agent or reffer
	            	if (($itemsKey == $lastKey) && $isDirectPlayer && empty($value)) {
	            		$newSummary['direct_players_total']['total_count'] += 1;
	            	}

	            	if (!in_array($itemsKey, ['affiliate_total', 'agency_total', 'referrer_total']) || empty($value)) {
	            		continue;
	            	}

	            	# if pass in the above condition mean is the player is direct
	            	$isDirectPlayer = false;
                    if (!isset($newSummary[$itemsKey][$value])) {
                        $newSummary[$itemsKey][$value] = 1;
                    } else {
                        $newSummary[$itemsKey][$value] += 1;
                    }
	            }
            }

            foreach ($newSummary as $key => $value) {
                # get total
                $newSummary[$key]['total_count'] = array_sum($newSummary[$key]);
            }
        }

        $result['summary'] = $newSummary;

        if($is_export){
            return $csv_filename;
        }

        return $result;
	}
}
////END OF FILE/////////
