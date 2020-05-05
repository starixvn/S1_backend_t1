<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


trait log_recorder_module {

	public function log_recorder(){
		//never stop
		set_time_limit(0);

		$log_recorder_path=$this->utils->getConfig('log_recorder_path');
		if(!empty($log_recorder_path) && !file_exists($log_recorder_path)){
			@mkdir($log_recorder_path, 0777, true);
			@chmod($log_recorder_path, 0777);
		}
		if(empty($log_recorder_path) || !is_writable($log_recorder_path)){
			$this->utils->error_log('empty or cannot write log_recorder_path', $log_recorder_path);
			return;
		}

		$this->readRabbitMQConfig();

		$cnt=0;

		$log_dir=rtrim($log_recorder_path,'/').'/';

		raw_debug_log('log_recorder_path:'.$log_recorder_path);
		// $connected=false;

		while(true){

			try{

				if(!$this->rabbitmq_online){

					$this->readRabbitMQConfig();
					$this->activeRabbitMQ();

			        raw_debug_log('try connect rabbitmq queue server, success:', $this->rabbitmq_online ,
			        	$this->utils->getConfig('rabbitmq_server'), $this->rabbitmq_exchange_name);

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

				if($this->rabbitmq_online && !empty($this->rabbitmq_channel)){
					$cnt=0;

					// $exch=$this->rabbitmq_exchange_name;

					// $patterns=[$this->redis_channel_key];

					// $this->utils->debug_log("start", $this->redisClient->ping(), "brPop", $this->redis_channel_key);
					// $this->utils->debug_log("start rabbitmq", $this->rabbitmq_exchange_name);

					// $listContent=$this->redisClient->brPop([$this->redis_channel_key], $this->queue_redis_pop_timeout);
					// $this->rabbitmq_channel->queue_declare($queue_key, false, false, false, false);
					$this->rabbitmq_channel->exchange_declare($this->rabbitmq_exchange_name, 'fanout', false, false, false);
					//random queue
					list($queue_name, ,) = $this->rabbitmq_channel->queue_declare("");
					$this->rabbitmq_channel->queue_bind($queue_name, $this->rabbitmq_exchange_name);

					raw_debug_log("start rabbitmq", $this->rabbitmq_exchange_name, $queue_name);

					$this->rabbitmq_channel->basic_consume($queue_name, '', false, true, false, false, function($msg)
							use($log_dir){

						// $channel=$this->rabbitmq_queue_key;
						$message=$msg->body;

						// raw_debug_log($message);

						//decode json
						$json=$this->utils->decodeJson($message);
						if(!empty($json)){
							$channel=$json['channel'];
							// raw_debug_log('get message from ', $channel, $log_dir.$channel.'-json.log');
							// write_log($log_dir.$channel.'-json.log', $message);
							error_log($message, 3, $log_dir.$channel.'-json.log');

							//try write to db

						}else{
							raw_error_log('process log failed', $message);
						}

					});

					while (count($this->rabbitmq_channel->callbacks)) {
					    $this->rabbitmq_channel->wait();
					}

					$this->closeRabbitMQ();

				}else{
					raw_error_log('connect rabbitmq failed', $cnt, $this->utils->getConfig('rabbitmq_server'), $this->rabbitmq_exchange_name);
					// $this->utils->error_log('connect redis failed', $cnt, $this->redis_host, $this->redis_port);
					$cnt++;
					if($cnt>=self::MAX_RETRY_RABBITMQ_TIME){
						raw_error_log('quit...');
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
					raw_error_log($e);
				// }

				// $redisConnect=false;
				// if(!empty($this->redisClient)){
				// 	$this->redisClient->close();
				// }

				//try close
				$this->closeRabbitMQ();

			}
		}

	}

	public function activeRabbitMQ(){
        // $this->tried=true;
        if(!$this->rabbitmq_online && !empty($this->rabbitmq_host)){
            try{

				$this->rabbitmq_connection = new AMQPStreamConnection($this->rabbitmq_host, $this->rabbitmq_port, $this->rabbitmq_username, $this->rabbitmq_password);
				$this->rabbitmq_channel = $this->rabbitmq_connection->channel();

                $this->rabbitmq_online=!empty($this->rabbitmq_connection) && !empty($this->rabbitmq_channel);

            }catch(Exception $e){
                raw_error_log('active rabbit mq failed', $e);
                $this->rabbitmq_online=false;
                $this->rabbitmq_connection=null;
	        	$this->rabbitmq_channel=null;
            }
        }

	}

	public function closeRabbitMQ(){
        if($this->rabbitmq_online && !empty($this->rabbitmq_connection)){
        	try{
	        	$this->rabbitmq_online=false;
	        	if(!empty($this->rabbitmq_channel)){
		        	$this->rabbitmq_channel->close();
		        	unset($this->rabbitmq_channel);
		        	$this->rabbitmq_channel=null;
	        	}
	        	$this->rabbitmq_connection->close();
	        	unset($this->rabbitmq_connection);
	        	$this->rabbitmq_connection=null;
	        }catch(Exception $e){
	        	raw_error_log('closeRabbitMQ failed', $this->CI->utils->getConfig('rabbitmq_server'), $e);
	        }
        }
	}

	public function readRabbitMQConfig(){
        $rabbitmq_server_config=$this->utils->getConfig('rabbitmq_server');
        $this->rabbitmq_host=isset($rabbitmq_server_config['host']) ? $rabbitmq_server_config['host'] : null;
        $this->rabbitmq_port=isset($rabbitmq_server_config['port']) ? $rabbitmq_server_config['port'] : null;
        $this->rabbitmq_username=isset($rabbitmq_server_config['username']) ? $rabbitmq_server_config['username'] : null;
        $this->rabbitmq_password=isset($rabbitmq_server_config['password']) ? $rabbitmq_server_config['password'] : null;
        $this->rabbitmq_exchange_name=CI_Log::LOG_RECORDER_EXCHANGE_NAME;
	}

}
