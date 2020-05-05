<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_function_permission_export_201611011326 extends CI_Migration {

	const ROLE_DATA = array(
		array(
			'funcId' => '196',
			'funcName' => 'Export Payment API',
			'parentId' => '48',
			'funcCode' => 'export_payment_api',
		),
		array(
			'funcId' => '197',
			'funcName' => 'Export Game Type',
			'parentId' => '48',
			'funcCode' => 'export_game_type',
		),
		array(
			'funcId' => '198',
			'funcName' => 'Export User Logs',
			'parentId' => '72',
			'funcCode' => 'export_user_logs',
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