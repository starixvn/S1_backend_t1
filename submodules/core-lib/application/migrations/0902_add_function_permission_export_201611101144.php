<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_function_permission_export_201611101144 extends CI_Migration {

	// const ROLE_DATA = array(
	// 	array(
	// 		'funcId' => '200',
	// 		'funcName' => 'Export Country Rules',
	// 		'parentId' => '1',
	// 		'funcCode' => 'export_country_rules',
	// 	)
	// );


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

	}

}

////END OF FILE////