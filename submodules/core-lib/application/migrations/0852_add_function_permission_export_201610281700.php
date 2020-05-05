<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_function_permission_export_201610281700 extends CI_Migration {

	private $roleData = array(
		array(
			'funcId' => '173',
			'funcName' => 'Export All Player',
			'parentId' => '15',
			'funcCode' => 'export_player_lists',
		),
		array(
			'funcId' => '174',
			'funcName' => 'Export Deposit List',
			'parentId' => '72',
			'funcCode' => 'export_deposit_lists',
		),
		array(
			'funcId' => '175',
			'funcName' => 'Export Payment Transactions',
			'parentId' => '72',
			'funcCode' => 'export_payment_transactions',
		),
		array(
			'funcId' => '176',
			'funcName' => 'Export Withdrawal List',
			'parentId' => '72',
			'funcCode' => 'export_withdrawal_lists',
		),
		array(
			'funcId' => '177',
			'funcName' => 'Export Promo Request List',
			'parentId' => '59',
			'funcCode' => 'export_promo_request_list',
		),
		array(
			'funcId' => '178',
			'funcName' => 'Export Player Report',
			'parentId' => '40',
			'funcCode' => 'export_player_report',
		),
		array(
			'funcId' => '179',
			'funcName' => 'Export Game Description',
			'parentId' => '1',
			'funcCode' => 'export_game_description',
		),
		array(
			'funcId' => '180',
			'funcName' => 'Export Report Transactions',
			'parentId' => '40',
			'funcCode' => 'export_report_transactions',
		),
	);


	public function up() {

		// $this->load->model(array('roles'));

		// $this->roles->startTrans();

		// foreach ($this->roleData as $key => $value) {

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

		// foreach ($this->roleData as $key => $value){

		// 	$funcId = $value['funcId'];
		// 	$this->roles->deleteFunction($funcId);

		// }

		// $succ = $this->roles->endTransWithSucc();
		//process result


	}

}

////END OF FILE////