<?php
require_once dirname(__FILE__) . "/base_cli.php";

require_once dirname(__FILE__).'/../modules/remote_queue_server_module.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Queue_server
 *
 * General behaviors include :
 *
 * * process queue job
 *
 * @category Command Line
 * @version 1.8.10
 * @copyright 2013-2019 TripleOneTech
 */
class Queue_server extends Base_cli {

	public $ogHome = null;

	private $redisClient;
	private $redis_host;
	private $redis_port;
	private $redis_timeout;
	private $redis_retry_timeout;
	private $redis_password;
	private $redis_database=null;

	private $redis_channel_key;

	private $dispatcher;
	private $subscriberList=[];

	use remote_queue_server_module;

	const CHANNEL_SUFFIX='-remote-queue';

	const MAX_RETRY_TIME=20;
	const FUNC_REMOTE_TRIGGER_EVENT='remote_trigger_event';

	const VALID_JOBS=[
		'remote_sms',
		'remote_sync_game_logs',
		'remote_email',
		'remote_debug_queue',
		'remote_import_players',
		'remote_rebuild_games_total',
		'remote_pay_cashback_daily',
		'remote_sync_t1_gamegateway',
		'remote_export_csv',
		'remote_stop_queue',
		'remote_manually_batch_add_cashback_bonus',
		'remote_batch_sync_balance_by',
		'remote_send_player_private_ip_mm_alert',
		'remote_regenerate_all_report',
		'remote_broadcast_message',
		'remote_batch_add_bonus',
	];

    private $online=false;
    // private $tried=false;

	/**
	 * overview : queue server constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->load->model('queue_result');

		$this->config->set_item('print_log_to_console', true);

		$this->ogHome = realpath(dirname(__FILE__) . "/../../../");

		// $this->redisClient=new Redis();

		//never stop
		set_time_limit(0);

        // $this->redisClient=new Redis();

        // $this->readRedisConfig();

        // $this->redis_channel_key=$this->_app_prefix.self::CHANNEL_SUFFIX;

		$this->readRabbitMQConfig();

		$this->initEvent();

		// register_shutdown_function(array($this, 'clean_shutdown'));
	}

	public function readRabbitMQConfig(){
        $rabbitmq_server_config=$this->utils->getConfig('rabbitmq_server');
        $this->rabbitmq_host=isset($rabbitmq_server_config['host']) ? $rabbitmq_server_config['host'] : null;
        $this->rabbitmq_port=isset($rabbitmq_server_config['port']) ? $rabbitmq_server_config['port'] : null;
        $this->rabbitmq_username=isset($rabbitmq_server_config['username']) ? $rabbitmq_server_config['username'] : null;
        $this->rabbitmq_password=isset($rabbitmq_server_config['password']) ? $rabbitmq_server_config['password'] : null;
        $this->rabbitmq_queue_key=$this->_app_prefix.self::CHANNEL_SUFFIX;
	}

	public function activeRabbitMQ(){
        // $this->tried=true;
        if(!$this->online && !empty($this->rabbitmq_host)){
            try{

				$this->rabbitmq_connection = new AMQPStreamConnection($this->rabbitmq_host, $this->rabbitmq_port, $this->rabbitmq_username, $this->rabbitmq_password);
				$this->rabbitmq_channel = $this->rabbitmq_connection->channel();

                $this->online=!empty($this->rabbitmq_connection) && !empty($this->rabbitmq_channel);

            }catch(Exception $e){
                $this->CI->utils->error_log('active rabbit mq failed', $e);
                $this->online=false;
                $this->rabbitmq_connection=null;
	        	$this->rabbitmq_channel=null;
            }
        }

	}

	public function closeRabbitMQ(){
        if($this->online && !empty($this->rabbitmq_connection)){
        	try{
	        	$this->online=false;
	        	if(!empty($this->rabbitmq_channel)){
		        	$this->rabbitmq_channel->close();
		        	$this->rabbitmq_channel=null;
	        	}
	        	$this->rabbitmq_connection->close();
	        	$this->rabbitmq_connection=null;
	        }catch(Exception $e){
	        	$this->CI->utils->error_log('closeRabbitMQ failed', $this->CI->utils->getConfig('rabbitmq_server'), $e);
	        }
        }
	}

	// public function readRedisConfig(){

 //        $redis_server_config=$this->utils->getConfig('queue_redis_server');
 //        $this->redis_host = isset($redis_server_config['server']) ? $redis_server_config['server'] : null;
 //        $this->redis_port = isset($redis_server_config['port']) ? $redis_server_config['port'] : null;
 //        //always is 0, will hold until any message coming
 //        $this->redis_timeout = 0;// isset($redis_server_config['timeout']) ? $redis_server_config['timeout'] : 1;
 //        $this->redis_retry_timeout = isset($redis_server_config['retry_timeout']) ? $redis_server_config['retry_timeout'] : 10;
 //        $this->redis_password = isset($redis_server_config['password']) ? $redis_server_config['password'] : 10;

 //        if(isset($redis_server_config['database'])){
 //        	$this->redis_database=$redis_server_config['database'];
 //        }

 //        $this->queue_redis_pop_timeout= $this->utils->getConfig('queue_redis_pop_timeout');

	// }

	/**
	 * overview : sync service start
	 */
	public function start() {

		$cnt=0;
		// $connected=false;

		while(true){

			try{

				if(!$this->online){

					$this->readRabbitMQConfig();
					$this->activeRabbitMQ();

			        $this->utils->info_log('try connect rabbitmq queue server, success:', $this->online ,
			        	$this->utils->getConfig('rabbitmq_server'), $this->rabbitmq_queue_key);

	                // $connected = !!$this->redisClient->connect($this->redis_host, $this->redis_port, $this->redis_timeout,
	                // 	null, $this->redis_retry_timeout);
	                // if(!empty($this->redis_password)){
	                //     $this->redisClient->auth($this->redis_password);
	                // }
	                // if($this->redis_database!==null){
	                // 	$this->redisClient->select($this->redis_database);
	                // }

			        // $this->utils->info_log('try connect redis queue server, success:', $redisConnect ,$this->redis_host, $this->redis_port,
			        // 	$this->redis_timeout, $this->redis_retry_timeout, $this->redis_password, $this->redis_database,
			        // 	$this->utils->getConfig('queue_redis_server'), $this->redis_channel_key);

				}

				if($this->online && !empty($this->rabbitmq_channel)){
					$cnt=0;

					// $patterns=[$this->redis_channel_key];

					// $this->utils->debug_log("start", $this->redisClient->ping(), "brPop", $this->redis_channel_key);
					$this->utils->debug_log("start rabbitmq", $this->rabbitmq_queue_key);

					// $listContent=$this->redisClient->brPop([$this->redis_channel_key], $this->queue_redis_pop_timeout);
					$this->rabbitmq_channel->queue_declare($this->rabbitmq_queue_key, false, false, false, false);

					$this->rabbitmq_channel->basic_consume($this->rabbitmq_queue_key, '', false, true, false, false, function($msg){

					// if(!empty($listContent)){
					// 	$channel=$listContent[0];
					// 	$message=$listContent[1];
						//reset db
				        $this->db->_reset_select();
						$this->db->reconnect();
						$this->db->initialize();

						$channel=$this->rabbitmq_queue_key;
						$message=$msg->body;
						//$this->db = substr($channel, 0, strlen($channel)-strlen(self::CHANNEL_SUFFIX)); // str_replace(self::CHANNEL_SUFFIX, '', $channel);
						//$db = $this->db;

						$token=$message;
						$data = $this->initJobData($token);

						$this->utils->debug_log('process token:'.$token, $data);

						if (!empty($data)) {
							$func_name=$data['func_name'];

							if($func_name==self::FUNC_REMOTE_TRIGGER_EVENT){
								$params = json_decode($data['full_params'], true);
								$data['params']=$params;
								$this->utils->info_log('event start: '.$data['func_name'], $token, $params);
								$this->triggerEvent($data, $params);
								$this->utils->info_log('event end: '.$data['func_name'], $token);
							}else if(in_array($func_name, self::VALID_JOBS)){
								$params = json_decode($data['full_params'], true);
								$data['params']=$params;
								$this->utils->info_log('job start: '.$data['func_name'], $token, $params);
								$this->$func_name($data, $params);
								$this->utils->info_log('job end: '.$data['func_name'], $token);
							}else{
								$this->utils->error_log('Not in white list, Invalid function name:'.$func_name, $data);
							}

						} else {
							$this->utils->error_log('Got wrong message', $token);
					    }

					// }
					});

					while (count($this->rabbitmq_channel->callbacks)) {
					    $this->rabbitmq_channel->wait();
					}

					$this->closeRabbitMQ();

					// $this->redisClient->pSubscribe($patterns, function($redis, $pattern, $channel, $message)
					// 	use($patterns) {

					// 	$this->client_code = substr($channel, 0, strlen($channel)-strlen(self::CHANNEL_SUFFIX)); // str_replace(self::CHANNEL_SUFFIX, '', $channel);

					// 	$token=$message;
					// 	$data = $this->initJobData($token);

					// 	$this->utils->debug_log('process token:'.$token, $data);

					// 	if (!empty($data)) {
					// 		$func_name=$data['func_name'];

					// 		if(in_array($func_name, self::VALID_JOBS)){
					// 			$this->$func_name($this->client_code, $data);
					// 		}else{
					// 			$this->utils->error_log('Invalid function name:'.$func_name, $data);
					// 		}

					// 	} else {
					// 		$this->utils->error_log('Got wrong message', $token);
					//     }

					// });
				}else{
					$this->utils->error_log('connect rabbitmq failed', $cnt, $this->utils->getConfig('rabbitmq_server'), $this->rabbitmq_queue_key);
					// $this->utils->error_log('connect redis failed', $cnt, $this->redis_host, $this->redis_port);
					$cnt++;
					if($cnt>=self::MAX_RETRY_TIME){
						$this->utils->error_log('quit...');
						break;
					}
					// $redisConnect=false;
					$this->closeRabbitMQ();
				}

				sleep(1);

			} catch (Exception $e) {

				// if($e instanceof RedisException){

				// 	$this->utils->error_log($e);

				// }else{
					$this->utils->error_log($e);
				// }

				// $redisConnect=false;
				// if(!empty($this->redisClient)){
				// 	$this->redisClient->close();
				// }

		        $this->db->_reset_select();
		        //reconnect db
		        $this->db->reconnect();

				//try close
				$this->closeRabbitMQ();

			}
		}
	}

	public function runCmd($cmd){

		// $str = exec($cmd, $output, $return_var);

		// $this->utils->debug_log($cmd, $return_var, $output);
		// unset($output);
		// unset($str);

        $return_var=pclose(popen($cmd, 'r'));
		return $return_var;
	}

	private function initJobData($token) {
		$row = $this->queue_result->getResult($token);
		return $row;
	}

    public function initEvent(){

        $this->dispatcher = new EventDispatcher();
        //register all subscribers
        //load files
        $path=dirname(__FILE__) . "/../../models/subscribers/*.php";
        $this->utils->info_log('init event start', $path);
        $list=glob($path);
        if(!empty($list)){
        	foreach ($list as $filename) {
        		$this->utils->debug_log('try load subscriber: '.$filename);
        		$className=pathinfo($filename, PATHINFO_FILENAME);
        		//ignore abstract subscriber
        		if($className=='AbstractSubscriber' || $className=='AutoFinishSubscriber'){
        			continue;
        		}
        		// require_once $filename;
        		$this->load->model('subscribers/'.$className, $className, false, true);
        		// $subObj=new $className();
        		$subObj=$this->$className;
        		$this->utils->debug_log('class is', get_class($subObj));
        		$this->subscriberList[]=$subObj;
        		$this->dispatcher->addSubscriber($subObj);
        	}
            //last one is AutoFinishSubscriber
            $this->load->model('subscribers/AutoFinishSubscriber', 'AutoFinishSubscriber', false, true);
            $subObj=$this->AutoFinishSubscriber;
            $this->subscriberList[]=$subObj;
            $this->dispatcher->addSubscriber($subObj);
        }
        $this->utils->info_log('init event end');
    }

    public function triggerEvent($data, $params){
        $token = $data['token'];
        $eventInfo=$params['event'];
        $eventClass=$eventInfo['class'];
        require_once dirname(__FILE__) . "/../../models/events/".$eventClass.".php";
        $event=new $eventClass($token, $eventInfo['name'], $eventInfo['data']);
        $this->dispatcher->dispatch($event->getEventName(), $event);
    }

}

/// END OF FILE//////////////
