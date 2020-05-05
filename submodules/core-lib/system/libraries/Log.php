<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once BASEPATH."/libraries/BetterJsonFormatter.php";
require_once BASEPATH."/libraries/BetterAmqpHandler.php";

use Psr\Log\LogLevel;

use Monolog\Logger;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\LogglyHandler;
// use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\AmqpHandler;
use PhpAmqpLib\Connection\AMQPStreamConnection;

// use Monolog\Formatter\JsonFormatter;

// use Monolog\Handler\NewRelicHandler;
// use Monolog\Handler\HipChatHandler;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\WebProcessor;
use Monolog\Processor\TagProcessor;

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package     CodeIgniter
 * @author      ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license     http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since       Version 1.0
 * @filesource
 */


// ------------------------------------------------------------------------

/**
 * Logging Class
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Logging
 * @author      ExpressionEngine Dev Team
 * @link        http://codeigniter.com/user_guide/general/errors.html
 */
class CI_Log {

    // CI log levels
    protected $_levels = array(
        'OFF' => '0',
        'ERROR' => '1',
        'INFO' => '2',
        'DEBUG' => '3',
        'VERBOSE' => '4',
        'ALL' => '5',
    );

    // config placeholder
    protected $config = array();

    private $log;
    private $jsonlog;
    // private $jsonlog_err;
    private $json_tags;
    private $enabled_verbose_log=false;
    private $file_logfile=null;
    private $min_level_config=Logger::DEBUG;

    private $argv;

    private $streamHanders=[];

    public $request_id=_REQUEST_ID;
    public $_external_request_id=null;

    const LOG_RECORDER_EXCHANGE_NAME='og-log-recorder';

    public function uniqueid(){

        return md5(uniqid());

    }

    private $hostname;

    public function getHostname(){

        if(!empty($this->hostname)){
            return $this->hostname;
        }

        $config=&get_config();

        $hostname=gethostname();

        $hostname=str_replace('_', '-', $hostname);

        $host_arr=explode('-', $hostname);
        //only keep 1-2
        if(count($host_arr)>=2){
            if(strpos($host_arr[0], 'staging')!==FALSE){
                $host_arr[0]=str_replace('staging','', $host_arr[0]);
                $host_arr[1].='-staging';
            }
            $hostname=$host_arr[0].'-'.$host_arr[1];
        }

        if(count($host_arr)>=3 && $host_arr[2]=='staging'){
            $hostname.='-staging';
        }

        if(count($host_arr)>=3 && $host_arr[2]=='sync'){
            $hostname.='-sync';
        }
        //<client>-shadow-og
        if(count($host_arr)>=3 && strpos($hostname, 'shadow')!==FALSE){
            $hostname=$host_arr[0].'-og-shadow';
            //to <client>-og-shadow
        }
        //<client>-live-bak-og
        if(count($host_arr)>=3 && strpos($hostname, 'live-bak')!==FALSE){
            $hostname=$host_arr[0].'-og';
            //to <client>-og
        }

        if(!empty($config['fake_hostname'])){
            $hostname=$config['fake_hostname'];
        }

        $this->hostname=$hostname;

        return $hostname;

    }

    public function is_cli_request() {
        return (php_sapi_name() === 'cli' OR defined('STDIN'));
    }

    /**
     * prepare logging environment with configuration variables
     */
    public function __construct()
    {

        // make $config from config/monolog.php accessible to $this->write_log()
        // $this->config = $config;
        $config=&get_config();

        $hostname=$this->getHostname();

        $this->argv=isset($_SERVER['argv']) ? @$_SERVER['argv'] : null;

        // error_log(var_export($config,true));
        $verbose_log=@$config['verbose_log'];
        $this->enabled_verbose_log=$verbose_log;
        $this->config = @$config['logger_settings'];

        $env=$config['RUNTIME_ENVIRONMENT'];

        $this->enabled_additional_error_log_file=$config['enabled_additional_error_log_file'];

        // if($json_exists){
        //init json log
        $this->jsonlog=new Logger($hostname);
        //change level to error
        $errorHandler=ErrorHandler::register($this->jsonlog, [], LogLevel::ERROR, LogLevel::ERROR);
        // $errorHandler->registerErrorHandler([], true);
        // $errorHandler->registerExceptionHandler();
        // $errorHandler->registerFatalHandler();

        if($verbose_log){
            // $json_tags=null;
            $this->addVerboseProcessor($this->jsonlog);
            // $this->json_tags=$json_tags;
        }

        $this->json_tags=new TagProcessor(['request_id'=>$this->request_id, 'env'=>$env, 'version'=>PRODUCTION_VERSION, 'hostname'=>$hostname]);
        $this->jsonlog->pushProcessor($this->json_tags);

        $file_logfile=$this->process_log_file($config['logger_file_list']);// $this->config['json_file'];
        $this->file_logfile=$file_logfile;
        $filePermission=0666;
        $level= Logger::DEBUG;
        if($this->config['json_file']=='debug'){
            $level= Logger::DEBUG;
        }elseif($this->config['json_file']=='error'){
            $level= Logger::ERROR;
        }elseif($this->config['json_file']=='info'){
            $level= Logger::INFO;
        }
        $this->min_level_config=$level;

        // $redis = new \Redis();
        $redis_server=isset($config['redis_server']) ? $config['redis_server'] : [];
        if(@$config['enabled_redis_logger'] && !empty($redis_server) && !empty($redis_server['server']) && !empty($redis_server['port'])){
            //use redis handler
            require_once BASEPATH."/libraries/RedisPublishHandler.php";

            $host=$redis_server['server'];
            $port=$redis_server['port'];
            $timeout=isset($redis_server['timeout']) ? $redis_server['timeout'] : 1;
            $retry_timeout=isset($redis_server['retry_timeout']) ? $redis_server['retry_timeout'] : 10;
            $password=isset($redis_server['password']) ? $redis_server['password'] : null;

            $publish_key=$hostname;

            $extra=['enabled_logger_file_channel'=>$config['enabled_logger_file_channel'],
                'enabled_debug_redis_logger'=>$config['enabled_debug_redis_logger']];
            $handler = new RedisPublishHandler($host, $port, $timeout, $retry_timeout,$password ,
                $publish_key, $extra, $level);
            $formatter=new BetterJsonFormatter();
            $handler->setFormatter($formatter);
            $this->streamHanders[]=$handler;
            $this->jsonlog->pushHandler($handler);

        }

        $enabled_json_file=isset($this->config['enabled_json_file']) ? $this->config['enabled_json_file'] : false;

        if(isset($config['enabled_logger_json_file'])){
            //overwrite
            $enabled_json_file=$config['enabled_logger_json_file'];
        }

        if($enabled_json_file){
            // raw_debug_log('json file: '.$file_logfile);
            // $max_files=3;
            $handler = new StreamHandler($file_logfile, $level, true, 0666);
            $formatter=new BetterJsonFormatter();
            $handler->setFormatter($formatter);
            $this->streamHanders[]=$handler;
            $this->jsonlog->pushHandler($handler);

            $errLogFile=$file_logfile;
            //add _err to log file
            if(substr($errLogFile, strlen($errLogFile)-4, 4)=='.log'){
                $errLogFile=substr($errLogFile, 0, strlen($errLogFile)-4).'_err.log';
            }else{
                $errLogFile=$errLogFile.'_err.log';
            }
            //only error
            $handler = new StreamHandler($errLogFile, Logger::ERROR, true, 0666);
            $formatter=new BetterJsonFormatter();
            $handler->setFormatter($formatter);
            $this->streamHanders[]=$handler;
            $this->jsonlog->pushHandler($handler);

        }

        if($this->is_cli_request()){
            //print error on console
            $handler = new StreamHandler('php://stdout', Logger::ERROR, true);
            $formatter=new BetterJsonFormatter();
            $handler->setFormatter($formatter);
            $this->streamHanders[]=$handler;
            $this->jsonlog->pushHandler($handler);
        }
        // }

        if(@$config['enabled_rabbitmq_logger']){

            $rabbitmq_server_config=@$config['rabbitmq_server'];
            if(!empty($rabbitmq_server_config)){

                $rabbitmq_host=isset($rabbitmq_server_config['host']) ? $rabbitmq_server_config['host'] : null;
                $rabbitmq_port=isset($rabbitmq_server_config['port']) ? $rabbitmq_server_config['port'] : null;
                $rabbitmq_username=isset($rabbitmq_server_config['username']) ? $rabbitmq_server_config['username'] : null;
                $rabbitmq_password=isset($rabbitmq_server_config['password']) ? $rabbitmq_server_config['password'] : null;

                if(!empty($rabbitmq_host)){

                    try{
                        $rabbitmq_connection = new AMQPStreamConnection($rabbitmq_host, $rabbitmq_port, $rabbitmq_username, $rabbitmq_password);
                        if(!empty($rabbitmq_connection)){
                            $rabbitmq_channel=$rabbitmq_connection->channel();
                            if(!empty($rabbitmq_channel)){
                                $exchangeName=self::LOG_RECORDER_EXCHANGE_NAME;
                                //build exchange first, 'fanout' type
                                $rabbitmq_channel->exchange_declare($exchangeName, 'fanout', false, false, false);

                                $handler=new BetterAmqpHandler($rabbitmq_channel, $exchangeName, $level);
                                $formatter=new BetterJsonFormatter();
                                $handler->setFormatter($formatter);
                                $this->streamHanders[]=$handler;
                                $this->jsonlog->pushHandler($handler);

                                // raw_debug_log('add rabbitmq', self::LOG_RECORDER_CHANNEL_NAME, $level);
                            }
                        }
                    }catch(Exception $e){
                        raw_error_log($e);
                    }
                }

            }

        }

    }

    public function process_log_file($logger_file_list){
        // $this->argv;

        $log_file=$this->config['json_file'];

        $config=&get_config();
        if(!empty($config['default_logger_json_file'])){
            $hostname=$this->getHostname();
            $log_file=str_replace(['{hostname}'], [gethostname()], $config['default_logger_json_file']);
        }

        //search argv
        if(!empty($this->argv)){
            $found=false;
            foreach ($this->argv as $arg) {
                if(!empty($arg)){
                    //search it
                    foreach ($logger_file_list as $key => $value) {
                        if(strpos($arg, $key)!==FALSE){
                            //found
                            $log_file=$value;
                            $found=true;
                            break;
                        }
                    }
                }
                if($found){
                    break;
                }
            }

            //try check command
            // if(isset($this->argv[1]) && strpos($this->argv[1], 'command')!==false){
            if(isset($this->argv[1])){
                $arr=explode('/', $this->argv[1]);
                // raw_debug_log('argv arr', $arr);
                // if(isset($arr[1]) && ($arr[1]=='command' || substr($arr[1], 0, 4)=='sync') ){
                //     $log_file= BASEPATH.'/../application/logs/'.$arr[1].'_'.$arr[2].'.log';
                // }
                if(isset($arr[1]) && isset($arr[2]) ){
                    $log_file= BASEPATH.'/../application/logs/'.$arr[1].'-'.$arr[2].'.log';
                }
                // raw_debug_log('log_file', $log_file);
            }
        }

        $env_target_db=getenv('__OG_TARGET_DB');
        // raw_debug_log('env_target_db:'.$env_target_db);
        if(!empty($env_target_db)){
            //add db name
            if(substr($log_file, strlen($log_file)-4, 4)=='.log'){
                $log_file=substr($log_file, 0, strlen($log_file)-4).'_'.$env_target_db.'.log';
            }
        }
        // raw_debug_log('log_file:'.$log_file);

        return $log_file;

    }

    public function debug_sql_log($sql, $time=0){

        if(!empty($this->jsonlog)){

            $time=strval($time);
            if(!empty($this->json_tags)){
                $this->json_tags->addTags(['log_type'=>'sql', 'sql_time'=>$time]);
            }

            $this->jsonlog->addDebug($sql, [$time]);

        }

        return true;

    }

    public function closeAll(){
        if(!empty($this->streamHanders)){
            foreach ($this->streamHanders as $hander){
                if($hander){
                    try {
                        $hander->close();
                    }catch (Exception $e){
                        raw_error_log($e);
                    }
                }
            }
        }
    }

    /**
     * Write to defined logger. Is called from CodeIgniters native log_message()
     *
     * @param string $level
     * @param $msg
     * @return bool
     */
    public function write_log(
        $level = 'error',
        $msg ='', $context=array()
    ) {
        try{
            $level = strtoupper($level);

            $original_msg=$msg;

            if(is_array($msg)){
                $msg=json_encode($msg);
            }

            // filter out anything in $this->config['exclusion_list']
            if (!empty($this->config['exclusion_list'])) {
                foreach ($this->config['exclusion_list'] as $findme) {
                    $pos = strpos($msg, $findme);

                    if ($pos !== false) {
                        // just exit now - we don't want to log this error
                        return true;
                    }
                }
            }
            if(is_null($context)){
                $context=[];
            }
            if(!is_array($context)){
                $context=array($context);
            }
            //append _external_request_id, normally it's from ?_r_=xxxx
            if(!empty($this->_external_request_id)){
                $context['_r_']=$this->_external_request_id;//$this->request_id.rand();
            }

            //add subtitle
            $functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            //getSubTitleFromBacktrace in Common.php
            $subtitle = getSubTitleFromBacktrace($functions);
            $msg='{'.$subtitle.'} '.$msg;

            if(!empty($this->jsonlog)){
                // verify error level
                if (!isset($this->_levels[$level])) {
                    $this->jsonlog->addError('unknown error level: ' . $level);
                    $level = 'ALL';
                }
                $config_level=strtoupper($this->config['json_level']);
                if (!isset($this->_levels[$config_level])) {
                    $this->jsonlog->addError('unknown config level: ' . $config_level);
                    $config_level = 'ALL';
                }

                //exists jsonlog
                if ($this->_levels[$level] <= $this->_levels[$config_level] ) {

                    switch ($level) {
                        case 'ERROR':
                            $this->jsonlog->addError($original_msg, $context);
                            break;
                        case 'INFO':
                            $this->jsonlog->addInfo($original_msg, $context);
                            break;
                        default:
                            $this->jsonlog->addDebug($original_msg, $context);
                            break;
                    }

                }

            }

        }catch(Exception $e){
            // $err=$e->getTraceAsString();
            // error_log($e->getMessage()."\n".$err);
            raw_error_log($err);
        }
        return true;
    }

    public function addVerboseProcessor($logger){

        // $config=&get_config();
        // $env=$config['RUNTIME_ENVIRONMENT'];

        $logger->pushProcessor(new MemoryUsageProcessor());
        $logger->pushProcessor(new MemoryPeakUsageProcessor());
        $logger->pushProcessor(new ProcessIdProcessor());
        $processor=new WebProcessor(null, ['url', 'http_method', 'referrer', 'ip']);
        $processor->addExtraField('host','HTTP_HOST');
        $processor->addExtraField('real_ip','HTTP_X_FORWARDED_FOR');
        $processor->addExtraField('browser_ip','HTTP_X_SS_CLIENT_ADDR');
        $logger->pushProcessor($processor);
        $logger->pushProcessor(new IntrospectionProcessor(Logger::DEBUG, ['CI_Log','Utils'], 1));

    }

}
// END Log Class

/* End of file Log.php */
/* Location: ./system/libraries/Log.php */