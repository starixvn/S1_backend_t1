<?php
require_once dirname(__FILE__) . "/../cli/base_cli.php";
//unit test
require_once dirname(__FILE__) . '/unit_test_assertion_module.php';
require_once dirname(__FILE__) . '/unit_test_utils_module.php';
require_once dirname(__FILE__) . '/unit_test_sale_order_module.php';
require_once dirname(__FILE__) . '/unit_test_transactions_module.php';
require_once dirname(__FILE__) . '/unit_test_base_model_module.php';

/**
 * General behaviors include :
 *
 * unit test redis
 * unit test utils.php
 *
 * @category Unit test
 * @version 6.56
 * @copyright 2013-2019 TripleOneTech
 */
class Unit_test_cli extends Base_cli {

    use unit_test_assertion_module;
    use unit_test_utils_module;
    use unit_test_base_model_module;
    use unit_test_sale_order_module;
    use unit_test_transactions_module;

    private $print_debug_on_assert_function=true;
    private $assertion_summary='';
    private $unit_test_config=null;

    /**
     * overview : Command constructor. Initialize Data
     */
    public function __construct() {
        parent::__construct();
        //always print to console
        $this->config->set_item('print_log_to_console', $this->input->is_cli_request());

        $default_sync_game_logs_max_time_second = $this->utils->getConfig('default_sync_game_logs_max_time_second');
        set_time_limit($default_sync_game_logs_max_time_second);
        $this->print_debug_on_assert_function=$this->utils->getConfig('print_debug_on_assert_function');

        $file_path=dirname(__FILE__).'/unit_test_config.php';
        //load unit test config file
        require $file_path;

        // Does the $config array exist in the file?
        if (!isset($config) OR !is_array($config)) {
            $this->utils->error_log('Your config file does not appear to be formatted correctly.');
            exit(1);
        }

        $this->unit_test_config=$config;
        unset($config);
        // $this->utils->debug_log('unit_test_config', $this->unit_test_config);

        $this->oghome = realpath(dirname(__FILE__) . "/../../../");
    }

    //===base unit test=====================
    public function test_print_to_console(){
        $msg='test normal';
        $this->utils->printToConsole($msg);
        $msg='test error';
        $this->utils->printToConsole($msg, true);
    }

    public function test_assert_functions(){

        $result=$this->assertEquals('1234', '1234');
        $this->utils->info_log('test assertEquals result', $result);
        $this->assertEquals('string', 'string');
        $this->utils->info_log('test assertEquals result', $result);
        $this->assertEquals(false, false);
        $this->utils->info_log('test assertEquals result', $result);
        $this->assertTrue(true);
        $this->utils->info_log('test assertTrue result', $result);
        $this->assertFalse(false);
        $this->utils->info_log('test assertFalse result', $result);
        $this->assertNull(null);
        $this->utils->info_log('test assertNull result', $result);
        $val='string';
        $this->assertNotNull($val);
        $this->utils->info_log('test assertNotNull result', $result);

        // $this->assertNull($val);

        $this->printAssertionSummary();
    }

    public function test_uniqueid_on_redis($key){
        $maxId=4294967295;
        $success=$this->utils->resetUniqueIdOnRedis($key);
        $this->utils->info_log('reset key', $key, $success);
        $this->assertTrue($success);

        $id=$this->utils->generateUniqueIdFromRedis($key);
        $this->utils->info_log('generateUniqueIdFromRedis key', $key, $id);
        $this->assertEquals(2, $id);

        $this->utils->info_log('test max id', $maxId);
        $success=$this->utils->resetUniqueIdOnRedis($key, $maxId-2);
        $this->assertTrue($success);
        $id=$this->utils->generateUniqueIdFromRedis($key);
        $this->utils->info_log('next id', $id);
        $this->assertEquals($maxId-1, $id);
        $id=$this->utils->generateUniqueIdFromRedis($key);
        $this->utils->info_log('next id', $id);
        $this->assertEquals(1, $id);
        $id=$this->utils->generateUniqueIdFromRedis($key);
        $this->utils->info_log('next id', $id);
        $this->assertEquals(2, $id);
        $success=$this->utils->resetUniqueIdOnRedis($key);
        $this->utils->info_log('reset key', $key, $success);
        $this->assertTrue($success);
    }

    public function test_parallel_uniqueid_on_redis($key){
        usleep(1000+rand(100,999));
        $id=$this->utils->generateUniqueIdFromRedis($key);
        $this->utils->info_log('generateUniqueIdFromRedis key', $key, $id);
    }

    public function test_read_write_redis($k, $v){
        $rlt=$this->utils->writeRedis($k, $v);
        // $rlt=false;
        $this->assertTrue($rlt, 'write redis failed');
        $this->utils->info_log('writeRedis', $k, $v, 'result', $rlt);
        $rlt=$this->utils->readRedis($k);
        $this->assertEquals($v, $rlt, 'read redis failed');
        $this->utils->info_log('readRedis', $k, 'result', $rlt);
    }
    //===base unit test=====================

}
