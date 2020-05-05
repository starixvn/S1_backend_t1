<?php

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

require_once APPPATH."/libraries/lib_gearman.php";

/**
 * Sends errors to Loggly.
 *
 * @author Przemek Sobstel <przemek@sobstel.org>
 * @author Adam Pancutt <adam@pancutt.com>
 * @author Gregory Barchard <gregory@barchard.net>
 */
class QueuelogHandler extends AbstractProcessingHandler
{

    protected $job_name;

    // private $CI;
    private $lib_gearman;

    public function __construct($level = Logger::DEBUG, $job_name='publish_log_job', $bubble=true){

        $this->job_name=$job_name;
        parent::__construct($level, $bubble);

        // $this->CI = &get_instance();
        // $this->CI->load->library(['lib_queue']);

        $this->lib_gearman=new Lib_gearman();
    }

    const CALLER_TYPE_SYSTEM = 3;
    const SYSTEM_UNKNOWN = 0;

    protected function write(array $record)
    {


        // $this->CI = &get_instance();
        // $this->CI->load->library(['lib_queue']);
        // $this->CI->load->library(array('lib_gearman'));
        // $this->CI->load->helper('string');
        // $this->CI->load->model(array('queue_result'));

        $host=gethostname();
        $callerType=self::CALLER_TYPE_SYSTEM;
        $caller=0;
        $state='';
        $this->addLogJob($record['level'], $record["formatted"], $host, $callerType, $caller, $state);

    }

    public function addLogJob($level, $msg, $host, $callerType, $caller, $state) {

        $systemId = self::SYSTEM_UNKNOWN;
        $funcName = 'publish_log';
        $params = array(
            'level' => $level,
            'msg' => $msg,
            'host' => $host,
        );
        $token=null;
        // $token = $this->CI->queue_result->newResult($systemId,
        //     $funcName, $params, $callerType, $caller, $state);

        $this->lib_gearman->gearman_client();
        $data = array(
            'system_id' => $systemId,
            'func_name' => $funcName,
            'params' => $params,
            'token' => $token,
        );
        $this->lib_gearman->do_job_background($this->job_name, json_encode($data));

        raw_debug_log('add log job publish_log_job', $level, count($msg), $host, $callerType, $caller, $state);

        return $token;
    }

}
