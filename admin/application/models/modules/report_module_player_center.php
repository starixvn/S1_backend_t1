<?php
/**
 * General behaviors include
 * * get player transfer request
 *
 * @category report_module_player_center
 * @author Elvis_Chen<elvis.chen@tripleonetech.net>
 * @since 1.0.0 Elvis_Chen: Initial File
 *
 * @version 1.0.0
 * @copyright 2013-2017 TripleOneTech
 */
trait report_module_player_center {
	/**
	 * get the player transfer request records
	 *
	 * @author Elvis_Chen<elvis.chen@tripleonetech.net>
	 * @since 1.0.0 Elvis_Chen: Initial function
	 *
	 * @param int $playerId
	 * @param array $request
	 * @param boolean $is_export
	 * @return void
	 */
	public function playerTransferRequest($playerId, $request, $is_export = false ){
		$this->load->library('data_tables');
		$this->load->model('wallet_model');

		// Wallet_model::STATUS_TRANSFER_REQUEST
		// Wallet_model::STATUS_TRANSFER_SUCCESS
		// Wallet_model::STATUS_TRANSFER_FAILED

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
				'dt' => $i++,
				'select' => 'transfer_request.secure_id',
				'name' => lang('ID'),
				'alias' => 'secure_id',
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.from_wallet_type_id',
				'name' => lang('From Wallet Type'),
				'alias' => 'from_wallet_type',
				'formatter' => function ($d, $row) use ($walletMap) {
					return $d == 0 ? lang('Main Wallet') : @$walletMap[$d];
				},
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.to_wallet_type_id',
				'name' => lang('To Wallet Type'),
				'alias' => 'to_wallet_type',
				'formatter' => function ($d, $row) use ($walletMap) {
					return $d == 0 ? lang('Main Wallet') : @$walletMap[$d];
				},
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.amount',
				'alias' => 'amount',
				'name' => lang('Transfer Amount'),
			),
			array(
				'dt' => $i++,
				'select' => 'transfer_request.status',
				'alias' => 'status',
				'name' => lang('Status'),
				'formatter' => function ($d, $row) use ($is_export) {

					if( ! $is_export ){

						switch ($d) {
						case Wallet_model::STATUS_TRANSFER_REQUEST:
							return '<strong class="text-info">' . lang('Request') . '</strong>';
						case Wallet_model::STATUS_TRANSFER_SUCCESS:
							return '<strong class="text-success">' . lang('Transfer Success') . '</strong>';
						case Wallet_model::STATUS_TRANSFER_FAILED:
							return '<strong class="text-danger">' . lang('Transfer Failed') . '</strong>';
						default:
							return '<i class="text-muted">N/A</i>';
						}

					}else{

						switch ($d) {
						case Wallet_model::STATUS_TRANSFER_REQUEST:
							return lang('Request');
						case Wallet_model::STATUS_TRANSFER_SUCCESS:
							return lang('Transfer Success');
						case Wallet_model::STATUS_TRANSFER_FAILED:
							return lang('Transfer Failed');
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
			)
		);

		$table = 'transfer_request';
		$joins = array(
			'player' => 'player.playerId = transfer_request.player_id',
			'response_results' => 'response_results.id = transfer_request.response_result_id',
		);

		$where[] = "transfer_request.player_id = ?";
		$values[] = $playerId;

		if (isset($input['status']) && ((int)$input['status'] !== 0)) {
			$where[] = "transfer_request.status = ?";
			$values[] = $input['status'];
		}

		if (isset($input['date_from'], $input['date_to'])) {
			$where[] = "transfer_request.created_at BETWEEN ? AND ?";
			$values[] = $input['date_from'];
			$values[] = $input['date_to'];
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

		return $result;
	}

	/**
	 * get the player promo report
	 *
	 * @param int $playerId
	 * @param array $request
	 * @param boolean $is_export
	 * @return void
	 */
	public function playerPromoReport($playerId, $request, $is_export = false){
		$this->load->model(array('player_promo'));
		$this->load->library('data_tables');

		$server_columns = [
            "promo_rule_id" => [
				'select' => 'promorules.promorulesId',
				'name' => "promo_rule_id",
				'alias' => 'promorulesId',
			],
            "promo_name" => [
				'select' => 'promorules.promoName',
				'name' => "promo_name",
				'alias' => 'promoName',
			],
            "promo_category" => [
				'select' => 'promorules.promoCategory',
				'name' => "promo_name",
				'alias' => 'promoCategory',
			],
            "promo_hide_date" => [
				'select' => 'promorules.hide_date',
				'name' => "promo_hide_date",
				'alias' => 'hide_date',
			],
            "promo_type_name" => [
				'select' => 'promotype.promoTypeName',
				'name' => "promo_type_name",
				'alias' => 'promoTypeName',
			],
            "promo_code" => [
				'select' => 'promocmssetting.promo_code',
				'name' => "promo_code",
				'alias' => 'promo_code',
			],
            "promo_started_at" => [
				'select' => 'withdraw_conditions.started_at',
				'name' => "promo_started_at",
				'alias' => 'started_at',
			],
            "promo_cms_id" => [
				'select' => 'promocmssetting.promoCmsSettingId',
				'name' => "promo_cms_id",
				'alias' => 'promoCmsSettingId',
			],
            "promo_cms_title" => [
				'select' => 'promocmssetting.promoName',
				'name' => "promo_cms_id",
				'alias' => 'promoCmsTitle',
			],
            "promo_cms_details" => [
				'select' => 'promocmssetting.promoDetails',
				'name' => "promo_cms_details",
				'alias' => 'promoDetails',
			],
            "promo_cms_tag_as_new_flag" => [
				'select' => 'promocmssetting.tag_as_new_flag',
				'name' => "promo_cms_tag_as_new_flag",
				'alias' => 'tag_as_new_flag',
			],
            "promo_cms_thumbnail" => [
				'select' => 'promocmssetting.promoThumbnail',
				'name' => "promo_cms_thumbnail",
				'alias' => 'promoThumbnail',
			],
            "promo_cms_is_default_banner_flag" => [
				'select' => 'promocmssetting.is_default_banner_flag',
				'name' => "promo_cms_is_default_banner_flag",
				'alias' => 'is_default_banner_flag',
			],
            "player_promo_id" => [
				'select' => 'playerpromo.playerpromoId',
				'name' => "player_promo_id",
				'alias' => 'playerpromoId',
			],
            "player_bonus_amount" => [
				'select' => 'playerpromo.bonusAmount',
				'name' => "player_bonus_amount",
				'alias' => 'bonusAmount',
			],
            "player_date_processed" => [
				'select' => 'playerpromo.dateProcessed',
				'name' => "player_date_processed",
				'alias' => 'dateProcessed',
			],
            "player_deposit_amount" => [
				'select' => 'playerpromo.depositAmount',
				'name' => "player_deposit_amount",
				'alias' => 'depositAmount',
			],
            "player_level_id" => [
				'select' => 'playerpromo.level_id',
				'name' => "player_level_id",
				'alias' => 'level_id',
			],
            "player_transaction_status" => [
				'select' => 'playerpromo.transactionStatus',
				'name' => "player_transaction_status",
				'alias' => 'transactionStatus',
			],
            "player_withdraw_condition_amount" => [
				'select' => 'playerpromo.withdrawConditionAmount',
				'name' => "player_withdraw_condition_amount",
				'alias' => 'withdrawConditionAmount',
			],
            "player_date_apply" => [
				'select' => 'playerpromo.dateApply',
				'name' => "player_date_apply",
				'alias' => 'dateApply',
			],
            "player_id" => [
				'select' => 'player.playerId',
				'name' => "player_id",
				'alias' => 'playerId',
			],
            "player_username" => [
				'select' => 'player.username',
				'name' => "player_username",
				'alias' => 'username',
			],
            "vip_level_name" => [
				'select' => 'vipsettingcashbackrule.vipLevelName',
				'name' => "vip_level_name",
				'alias' => 'vipLevelName',
			]
		];

		$columns = [];

		$sn = 0;
		foreach($request['columns'] as $column){
			if(!isset($server_columns[$column['name']])){
				continue;
			}

			$columns[] = $server_columns[$column['name']] + [
				'dt' => $sn
			];

			$sn++;
		}

		$table = 'playerpromo';
		$joins = array(
			'promorules' => 'promorules.promorulesId = playerpromo.promorulesId',
			'promotype' => 'promotype.promotypeId = promorules.promoCategory',
			'promocmssetting' => 'playerpromo.promoCmsSettingId = promocmssetting.promoCmsSettingId',
			'player' => 'player.playerId = playerpromo.playerId',
			'vipsettingcashbackrule' => 'vipsettingcashbackrule.vipsettingcashbackruleId = playerpromo.level_id',
			'transactions' => 'playerpromo.playerpromoId = transactions.player_promo_id',
			'withdraw_conditions' => 'withdraw_conditions.player_promo_id = playerpromo.playerpromoId'
		);

		// $this->db->order_by('playerpromo.dateProcessed', 'desc');
		$input = $this->data_tables->extra_search($request);

		$where[] = "playerpromo.transactionStatus not in (?, ?, ?)";
		$values[] = Player_promo::TRANS_STATUS_DECLINED_FOREVER;
		$values[] = Player_promo::TRANS_STATUS_DECLINED;
		$values[] = Player_promo::TRANS_STATUS_FINISHED_WITHDRAW_CONDITION;

		$where[] = "playerpromo.playerId = ?";
		$values[] = $playerId;

		if (isset($input['date_processed_from'], $input['date_processed_to'])) {
			$where[] = "dateProcessed BETWEEN ? AND ?";
			$values[] = $input['date_processed_from'];
			$values[] = $input['date_processed_to'];
		}

		$result = $this->data_tables->get_data($request, $columns, $table, $where, $values, $joins);

		return $result;
	}	
}
// vim:ft=php:fdm=marker
// end of report_module_player_center.php
////END OF FILE/////////
