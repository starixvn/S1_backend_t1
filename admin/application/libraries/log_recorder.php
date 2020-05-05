<?php

//require_once "vendor/autoload.php";

function raw_debug_log(){
	$args = func_get_args();

	$functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	$msg = buildDebugMessage($args, $functions);

	echo '[' . date('Y-m-d H:i:s') . '] ' .$msg."\n";
}

function formatDebugMessage($value) {

	if (is_object($value)) {
		if ($value instanceof \DateTime) {
			//print date time
			$str = $value->format(\DateTime::ISO8601);
		// } else if ($value instanceof \CI_DB_result) {
			// $str = $this->CI->db->last_query();
		} else if ($value instanceof \SimpleXMLElement) {
			$str = $value->asXML();
		} else if (method_exists($value, '__toString')) {
			$str = $value->__toString();
		} else if (method_exists($value, 'toString')) {
			$str = $value->toString();
		} else {
			$str = json_encode((array) $value, JSON_PRETTY_PRINT);
		}
	} else if (is_array($value)) {
		$str = json_encode($value, JSON_PRETTY_PRINT);
		if($str===false){
			$str=var_export($value, true);
		}
	} else if (is_null($value)) {
		$str = '(NULL)';
	} else if (is_bool($value)) {
		$str = $value ? 'true' : 'false';
	} else {
		$str = $value;
	}

	return $str;
}

function getSubTitleFromBacktrace($functions) {
	$subtitle = "";

	if (!empty($functions)) {
		$cnt = 0;
		foreach ($functions as $call) {
			//ignore CodeIgniter.php
			if (isset($call['file']) && strpos($call['file'], 'CodeIgniter.php') !== FALSE) {
				continue;
			}
			if (isset($call['function']) && strpos($call['function'], 'debug_log') !== FALSE) {
				continue;
			}
			if (isset($call['file']) && strpos($call['file'], 'public/index.php') !== FALSE) {
				continue;
			}
			if (empty($call['file']) && !empty($call['class'])) {
				$subtitle .= $call['class'] . "->" . $call['function'] . " > ";
			} else if (!empty($call['file'])) {
				$funcName = "";
				if (isset($call['function'])) {
					$funcName = '@' . @$call['function'];
				}
				$subtitle .= str_replace(array('/home/vagrant/Code/og', 'admin/application', 'player/application', 'aff/application'), array('..', '..', '..', '..'), $call['file']) . ":" . $call['line'] . $funcName . " > ";
			}
			if (!empty($call['file']) || !empty($call['class'])) {
				$cnt++;
			}
			if ($cnt > 1) {
				break;
			}
		}
	}
	return $subtitle;
}
function buildDebugMessage($args, $functions, $title = 'APP', $fullStack = false, $addHeader = true) {

	// $functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	// $functions = array_reverse($functions);
	if (empty($functions)) {
		$functions = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	}

	$msg = '';

	if ($addHeader) {
		$subtitle = getSubTitleFromBacktrace($functions);

		// $env = $this->getConfig('RUNTIME_ENVIRONMENT');
		// $host = $this->getCallHost();

		// $functions = debug_backtrace();
		// if (count($functions) > 1) {
		// 	$last = $functions[1];
		// 	$subtitle = @$last['class'] . '.' . @$last['function'];
		// 	if (isset($last['line'])) {
		// 		$subtitle .= ':' . @$last['line'];
		// 	}
		// }
		$msg .= "[" . $title . "] [" . getmypid() . "] [";
		if (!empty($subtitle)) {
			$msg = $msg . $subtitle . '] [';
		}
	}
	foreach ($args as $key => $value) {
		$str = formatDebugMessage($value);

		$msg .= $key . ": " . $str . ", ";
	}

	if ($addHeader) {
		$msg .= ' ]';
	}

	if ($fullStack) {
		$msg .= "\nStack:\n";
		foreach ($functions as $call) {
			if (empty($call['file']) && !empty($call['class'])) {
				$msg .= $call['class'] . "->" . $call['function'] . "\n";
			} else if (!empty($call['file'])) {
				$funcName = "";
				if (isset($call['function'])) {
					$funcName = '@' . @$call['function'];
				}
				$msg .= str_replace(array('/home/vagrant/Code/og', 'admin/application', 'player/application', 'aff/application'), array('..', '..', '..', '..'), $call['file']) . ":" . $call['line'] . $funcName . "\n";
			}
		}
	}

	return $msg;
}

function write_log($log_file, $message){

	return error_log($message."\n", 3, $log_file);

}

$host='35.187.153.12';
$port=6379;
$timeout=0;
$retry_timeout=5;

$redisClient=new Redis();
$rlt=$redisClient->connect($host, $port, $timeout, null, $retry_timeout);

//$redisClient=new Predis\Client(['scheme' => 'tcp',
//'host'   => $host,
//'port'   => $port,
//]);

//$rlt=$redisClient->connect($host, $port, $timeout, null, $retry_timeout);

if($rlt){

	raw_debug_log("start", $redisClient->ping());

	$patterns=["*_og", "*_og_sync", "*_og_staging"];
	$log_dir=dirname(__FILE__).'/logs/';

	while(true){
		// try{

			$redisClient->pSubscribe($patterns, function($redis, $pattern, $channel, $message)
				use($log_dir){
				//raw_debug_log($pattern, $channel, $message);

				write_log($log_dir.$channel.'-json.log', $message);

			});

		// }catch(Exception $e){
		// 	raw_debug_log($e);
		// }
		sleep(1);
	}

	$redisClient->close();

	raw_debug_log("end");

}else{
	raw_debug_log('connect redis failed');
}
