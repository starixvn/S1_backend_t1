<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_function_permission_export_201610311341 extends CI_Migration {

	const ROLE_DATA = array(
		array(
			'funcId' => '183',
			'funcName' => 'Export Affiliate Tag',
			'parentId' => '48',
			'funcCode' => 'export_affiliate_tag',
		),
		array(
			'funcId' => '184',
			'funcName' => 'Export Affiliate Payment',
			'parentId' => '48',
			'funcCode' => 'export_affiliate_payment',
		),
		array(
			'funcId' => '185',
			'funcName' => 'Export Game Logs',
			'parentId' => '72',
			'funcCode' => 'export_game_logs',
		),
		array(
			'funcId' => '186',
			'funcName' => 'Export Tagged Players',
			'parentId' => '15',
			'funcCode' => 'export_tagged_players',
		),
		array(
			'funcId' => '187',
			'funcName' => 'Export Tag Management List',
			'parentId' => '15',
			'funcCode' => 'export_tag_list',
		),
		array(
			'funcId' => '188',
			'funcName' => 'Export Batch Create List',
			'parentId' => '15',
			'funcCode' => 'export_batch_create',
		),
		array(
			'funcId' => '189',
			'funcName' => 'Export Friend Referral',
			'parentId' => '15',
			'funcCode' => 'export_friend_referral',
		),
		array(
			'funcId' => '190',
			'funcName' => 'Export Bank Payment',
			'parentId' => '72',
			'funcCode' => 'export_bank_payment',
		),
		array(
			'funcId' => '191',
			'funcName' => 'Export Transfer Request',
			'parentId' => '40',
			'funcCode' => 'export_transfer_request',
		),
		array(
			'funcId' => '192',
			'funcName' => 'Export Agency Logs',
			'parentId' => '116',
			'funcCode' => 'export_agency_logs',
		),
		array(
			'funcId' => '193',
			'funcName' => 'Export Credit Transaction',
			'parentId' => '116',
			'funcCode' => 'export_credit_transaction',
		),
		array(
			'funcId' => '194',
			'funcName' => 'Export Agent List',
			'parentId' => '116',
			'funcCode' => 'export_agent_list',
		),
		array(
			'funcId' => '195',
			'funcName' => 'Export Agent Template List',
			'parentId' => '116',
			'funcCode' => 'export_agent_template_list',
		)
	);


	public function up() {

		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// foreach (self::ROLE_DATA as $key => $value) {

		// 	$funcId = $value['funcId'];
		// 	$funcName = $value['funcName'];
		// 	$parentId = $value['parentId'];
		// 	$funcCode = $value['funcCode'];

		// 	$this->roles->initFunction( $funcCode, $funcName, $funcId, $parentId, true);
		// }

		// $succ = $this->roles->endTransWithSucc();
		// //process result

		// if ( !$succ ) {
		// 	throw new Exception('migrate failed ');
		// }
	}

	public function down() {

		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// foreach (self::ROLE_DATA as $key => $value){

		// 	$funcId = $value['funcId'];
		// 	$this->roles->deleteFunction($funcId);

		// }

		// $succ = $this->roles->endTransWithSucc();
		//process result


	}

}

////END OF FILE////